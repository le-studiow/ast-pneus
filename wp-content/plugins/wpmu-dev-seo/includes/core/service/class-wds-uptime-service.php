<?php

class Smartcrawl_Uptime_Service extends Smartcrawl_Service {

	public function get_known_verbs() {
		return array( 'day' );
	}

	public function is_cacheable_verb( $verb ) {
		return true;
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
		       'api/uptime/v1/stats/' .
		       $verb .
		       $query_url;
	}

	public function get_service_base_url() {
		return defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER
			? WPMUDEV_CUSTOM_API_SERVER
			: 'https://premium.wpmudev.org/';
	}

	public function get_request_arguments( $verb ) {
		$key = $this->get_dashboard_api_key();
		if ( empty( $key ) ) {
			return false;
		}

		return array(
			'method'    => 'GET',
			'timeout'   => 15,
			'sslverify' => false,
			'headers'   => array(
				'Authorization' => "Basic {$key}",
			),
		);
	}

	/**
	 * Overridden to use longer timeouts
	 */
	public function get_cache_expiry( $expiry = false ) {
		$expiry = Smartcrawl_Service::ERR_CACHE_EXPIRY === $expiry
			? $expiry
			: DAY_IN_SECONDS;

		return $expiry;
	}

	public function handle_error_response( $response, $verb ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( empty( $body ) || empty( $data ) ) {
			$msg = __( 'Unspecified error', 'wds' );
			$this->_set_error( $msg );
			$this->set_cached_error( 'day', $msg );

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
			$this->set_cached_error( 'day', $msg );
		}

		return true;
	}

}
