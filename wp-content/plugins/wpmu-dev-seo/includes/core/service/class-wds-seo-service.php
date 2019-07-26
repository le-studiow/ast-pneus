<?php

class Smartcrawl_Seo_Service extends Smartcrawl_Service { // phpcs:ignore -- We have two versions of this class

	const ERR_BASE_API_ISSUE = 40;
	const ERR_BASE_CRAWL_RUN = 51;
	const ERR_BASE_COOLDOWN = 52;
	const ERR_BASE_CRAWL_ERR = 53;
	const ERR_BASE_GENERIC = 59;

	public function get_known_verbs() {
		return array( 'start', 'status', 'result', 'sync' );
	}

	public function is_cacheable_verb( $verb ) {
		return false;
	}

	public function get_request_url( $verb ) {
		if ( empty( $verb ) ) {
			return false;
		}

		$domain = apply_filters(
			$this->get_filter( 'domain' ),
			network_site_url()
		);
		if ( empty( $domain ) ) {
			return false;
		}

		$query_url = http_build_query( array(
			'domain' => $domain,
		) );
		$query_url = $query_url && preg_match( '/^\?/', $query_url ) ? $query_url : "?{$query_url}";

		return trailingslashit( $this->get_service_base_url() ) .
		       $verb .
		       $query_url;
	}

	public function get_service_base_url() {
		$base_url = 'https://premium.wpmudev.org/';

		$api = apply_filters(
			$this->get_filter( 'api-endpoint' ),
			'api'
		);

		$namespace = apply_filters(
			$this->get_filter( 'api-namespace' ),
			'seo-audit/v1'
		);

		if ( defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER ) {
			$base_url = trailingslashit( WPMUDEV_CUSTOM_API_SERVER );
		}

		return trailingslashit( $base_url ) . trailingslashit( $api ) . trailingslashit( $namespace );
	}

	public function get_request_arguments( $verb ) {
		$domain = apply_filters(
			$this->get_filter( 'domain' ),
			network_site_url()
		);
		if ( empty( $domain ) ) {
			return false;
		}

		$key = $this->get_dashboard_api_key();
		if ( empty( $key ) ) {
			return false;
		}

		$args = array(
			'method'    => 'GET',
			'timeout'   => 40,
			'sslverify' => false,
			'headers'   => array(
				'Authorization' => "Basic {$key}",
			),
		);

		if ( 'sync' === $verb ) {
			$ignores = new Smartcrawl_Model_Ignores();

			$args['method'] = 'POST';
			$args['body'] = array(
				'ignored_issue_ids' => wp_json_encode( $ignores->get_all() ),
			);
		}

		return $args;
	}

	/**
	 * Local ignores list sync handler
	 *
	 * @return bool Status
	 */
	public function sync_ignores() {
		Smartcrawl_Logger::debug( 'Start syncing the ignore list' );

		return $this->request( 'sync' );
	}

	/**
	 * Public wrapper for start service method call
	 *
	 * @return mixed Service response hash on success, (bool) on failure
	 */
	public function start() {
		if ( $this->in_progress() ) {
			Smartcrawl_Logger::debug( 'Crawl already in progress. Doing nothing.' );
			return true; // Already in progress
		}
		Smartcrawl_Logger::debug( 'Starting a new crawl' );
		$result = $this->request( 'start' );
		if ( $result ) {
			// Let's check if we're all good here first!
			if ( ! empty( $result['data']['status'] ) && (int) $result['data']['status'] > 399 ) {
				// So we had an error API side that's been handled. We're not progressing anymore.
				// Also, let's preserve previous results.
				$this->stop();
				Smartcrawl_Logger::debug( 'API-side isssue, properly handled API side: ' . $result['data']['status'] );
			} else {
				// Also, preserve last crawl time if there isn't one
				$this->set_last_run_timestamp();

				// So crawl start successfully sent.
				// Clear previous results in anticipation
				// and mark ourselves as ready to receive status updates
				$this->_clear_result();
				$this->set_progress_flag( true );
				update_option( $this->get_filter( 'seo-service-start' ), true );
				Smartcrawl_Logger::debug( 'Crawl started' );
			}
		} else {
			$this->stop();
		}

		return $result;
	}

	public function is_started() {
		return (bool) get_option( $this->get_filter( 'seo-service-start' ), false );
	}

	/**
	 * Checks whether a call is currently being processed
	 *
	 * @return bool
	 */
	public function in_progress() {
		if ( ! $this->is_started() ) {
			return false;
		}

		$flag = $this->get_progress_flag();
		$update_timeout = HOUR_IN_SECONDS;

		$expected_timeout = intval( $flag ) + $update_timeout;
		if ( ! empty( $flag ) && is_numeric( $flag ) && time() > $expected_timeout ) {
			// Over timeout threshold, clear flag forcefully
			$this->set_progress_flag( false );
		}

		return ! ! $flag;
	}

	/**
	 * Gets progress flag state
	 *
	 * @return bool
	 */
	public function get_progress_flag() {
		return get_option( $this->get_filter( 'seo-progress' ), false );
	}

	/**
	 * Stops expecting response
	 *
	 * @return bool
	 */
	public function stop() {
		delete_option( $this->get_filter( 'seo-service-start' ) );
		$this->set_progress_flag( false );

		return true;
	}

	/**
	 * Sets progress flag state
	 *
	 * param bool $flag Whether the service check is in progress
	 *
	 * @return bool
	 */
	public function set_progress_flag( $flag ) {
		if ( ! empty( $flag ) ) {
			$flag = time();
		}

		return ! ! update_option( $this->get_filter( 'seo-progress' ), $flag );
	}

	/**
	 * Sets service last run time
	 *
	 * Attempts to use embedded result, and falls back
	 * to current timestamp
	 *
	 * @return bool
	 */
	public function set_last_run_timestamp() {
		$raw = $this->get_result();
		$timestamp = ! empty( $raw['end'] ) ? (int) $raw['end'] : 0;
		if ( empty( $timestamp ) && ! empty( $raw['issues']['previous']['timestamp'] ) ) {
			$timestamp = (int) $raw['issues']['previous']['timestamp'];
		}

		if ( empty( $timestamp ) ) {
			$timestamp = time();
		}

		return ! ! update_option( $this->get_filter( 'seo-service-last_runtime' ), $timestamp );
	}

	/**
	 * Public result getter
	 *
	 * @return mixed result
	 */
	public function get_result() {
		$result = get_option( $this->get_filter( 'seo-service-result' ), false );

		return $result;
	}

	/**
	 * @return Smartcrawl_SeoReport
	 */
	public function get_report() {
		// Start with an empty report
		$report = new Smartcrawl_SeoReport();
		if ( ! $this->is_member() ) {
			return $report;
		}

		// Call result first so it can perform cleanup in case of timeout
		$result = $this->result();
		if ( $this->in_progress() ) {
			$report->set_in_progress( true );
			$report->set_progress(
				empty( $result['percentage'] ) ? 0 : $result['percentage']
			);
		} else {
			$report->build( $result );
		}

		return $report;
	}

	private function _clear_result() {
		return ! ! delete_option( $this->get_filter( 'seo-service-result' ) );
	}

	/**
	 * Public wrapper for status service method call
	 *
	 * @return mixed Service response hash on success, (bool)false on failure
	 */
	public function status() {
		return $this->result();
	}

	/**
	 * Public wrapper for result service method call
	 *
	 * @return mixed Service response hash on success, (bool)false on failure
	 */
	public function result() {
		$result = $this->get_result();

		if ( $this->in_progress() ) {
			$percentage = empty( $result['percentage'] )
				? 0
				: $result['percentage']
			;
			$result['percentage'] = $percentage;
		} else if ( $this->is_started() && empty( $result['end'] ) ) {
			// Force timeout.
			$result = array(
				'issues' => array(
					'messages' => array(
						__( 'The crawl timed out', 'wds' ),
					),
				),
				'end' => time(),
			);
			$this->set_result( $result );
			$this->stop();
			$this->set_last_run_timestamp();
			Smartcrawl_Logger::debug( 'Forced timeout on sitemap crawl' );
		}

		return $result;
	}

	/**
	 * Sets result to new value
	 *
	 * Sets both cache and permanent result
	 *
	 * @return bool
	 */
	public function set_result( $result ) {
		return ! ! update_option( $this->get_filter( 'seo-service-result' ), $result );
	}

	/**
	 * Returns last service run time
	 *
	 * Returns either time embedded in results, or the timestamp
	 * from the results service, whichever is greater.
	 *
	 * @return int UNIX timestamp
	 */
	public function get_last_run_timestamp() {
		$recorded = (int) get_option( $this->get_filter( 'seo-service-last_runtime' ), 0 );

		$raw = $this->get_result();
		$embedded = ! empty( $raw['end'] ) ? (int) $raw['end'] : 0;
		if ( empty( $embedded ) && ! empty( $raw['issues']['previous']['timestamp'] ) ) {
			$embedded = (int) $raw['issues']['previous']['timestamp'];
		}

		return max( $recorded, $embedded );
	}

	public function handle_error_response( $response, $verb ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( empty( $body ) || empty( $data ) ) {
			$this->_set_error( __( 'Unspecified error', 'wds' ) );

			return true;
		}

		$msg = '';
		if ( ! empty( $data['message'] ) ) {
			$msg = $data['message'];
		}

		if ( ! empty( $data['data']['manage_link'] ) ) {
			$url = esc_url( $data['data']['manage_link'] );
			$msg .= ' <a href="' . $url . '">' . __( 'Manage', 'wds' ) . '</a>';
		}

		if ( ! empty( $msg ) ) {
			$this->_set_error( $msg );
		}

		return true;
	}


}
