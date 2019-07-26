<?php

class Smartcrawl_Controller_Sitemap extends Smartcrawl_Base_Controller {

	private static $_instance;

	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function should_run() {
		return Smartcrawl_Settings::get_setting( 'sitemap' )
		       && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_SITEMAP );
	}

	protected function init() {
		add_action( 'init', array( $this, 'serve_sitemap' ), 999 );

		add_action( 'wp_ajax_wds_update_sitemap', array( $this, 'json_update_sitemap' ) );
		add_action( 'wp_ajax_wds_update_engines', array( $this, 'json_update_engines' ) );

		add_action( 'wp_ajax_wds-sitemap-add_extra', array( $this, 'json_add_sitemap_extra' ) );
		add_action( 'wp_ajax_wds-sitemap-remove_extra', array( $this, 'json_remove_sitemap_extra' ) );
		add_action( 'wp_ajax_wds-get-sitemap-report', array( $this, 'json_get_sitemap_report' ) );

		add_action( 'admin_init', array( $this, 'rebuild_when_sitemap_page_loaded' ) );

		$smartcrawl_options = Smartcrawl_Settings::get_options();
		if ( isset( $smartcrawl_options['sitemap-disable-automatic-regeneration'] ) && empty( $smartcrawl_options['sitemap-disable-automatic-regeneration'] ) ) {
			add_action( 'delete_post', array( $this, 'drop_sitemap_cache' ) );
			add_action( 'publish_post', array( $this, 'drop_sitemap_cache' ) );

			add_action( 'delete_page', array( $this, 'drop_sitemap_cache' ) );
			add_action( 'publish_page', array( $this, 'drop_sitemap_cache' ) );
		}
	}

	public function rebuild_when_sitemap_page_loaded() {
		global $plugin_page;

		if ( isset( $plugin_page ) && Smartcrawl_Settings::TAB_SITEMAP === $plugin_page ) {
			$this->mark_sitemap_as_dirty();
		}
	}

	public function json_get_sitemap_report() {
		$result = array(
			'success' => false,
		);
		$data = $this->get_request_data();
		$open_type = isset( $data['open_type'] ) ? sanitize_text_field( $data['open_type'] ) : null;
		$ignored_tab_open = empty( $data['ignored_tab_open'] ) ? false : $data['ignored_tab_open'];

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( $result );

			return;
		}

		$seo_service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SEO );
		$crawl_report = $seo_service->get_report();
		$result['summary_markup'] = Smartcrawl_Simple_Renderer::load( 'sitemap/sitemap-crawl-stats', array(
			'crawl_report' => $crawl_report,
		) );
		$result['markup'] = Smartcrawl_Simple_Renderer::load( 'sitemap/sitemap-crawl-content', array(
			'crawl_report'     => $crawl_report,
			'open_type'        => $open_type,
			'ignored_tab_open' => $ignored_tab_open,
		) );
		$result['success'] = true;

		wp_send_json( $result );
	}

	/**
	 * Adds extra item to sitemap processing
	 */
	public function json_add_sitemap_extra() {
		$result = array( 'status' => 0 );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( $result );

			return;
		}

		$data = $this->get_request_data();
		if ( empty( $data['path'] ) ) {
			wp_send_json( $result );

			return;
		}

		$path = $data['path'];
		$paths = is_array( $path )
			? array_map( 'sanitize_text_field', (array) $path )
			: array( sanitize_text_field( $path ) );
		if ( ! is_array( $paths ) ) {
			$paths = array();
		}

		$extras = Smartcrawl_Xml_Sitemap::get_extra_urls();
		foreach ( $paths as $current_path ) {
			$index = array_search( $current_path, $extras, true );
			if ( false === $index ) {
				$extras[] = esc_url( $current_path );
			}
		}
		Smartcrawl_Xml_Sitemap::set_extra_urls( $extras );

		// Update sitemap
		$this->mark_sitemap_as_dirty();

		$result['status'] = 1;
		$result['add_all_message'] = Smartcrawl_Simple_Renderer::load( 'dismissable-notice', array(
			'message' => __( 'The missing items have been added to your sitemap as extra URLs.', 'wds' ),
			'class'   => 'sui-notice-info',
		) );

		wp_send_json( $result );
	}

	private function update_sitemap() {
		Smartcrawl_Xml_Sitemap::get()->generate_sitemap();
		Smartcrawl_Xml_Sitemap::get()->set_sitemap_pristine( true );
	}

	/**
	 * Removes extra item to sitemap processing
	 */
	public function json_remove_sitemap_extra() {
		$result = array( 'status' => 0 );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( $result );
			return;
		}

		$data = $this->get_request_data();
		if ( empty( $data['path'] ) ) {
			wp_send_json( $result );
			return;
		}

		$extras = Smartcrawl_Xml_Sitemap::get_extra_urls();
		$idx = array_search( sanitize_text_field( $data['path'] ), $extras, true );
		if ( false === $idx ) {
			wp_send_json( $result );
			return;
		}

		unset( $extras[ $idx ] );
		Smartcrawl_Xml_Sitemap::set_extra_urls( $extras );

		// Update sitemap
		$this->mark_sitemap_as_dirty();

		$result['status'] = 1;

		wp_send_json( $result );
	}

	/**
	 * Gets sitemap stat options
	 *
	 * @return array
	 */
	public function get_sitemap_stats() {
		$opts = get_option( 'wds_sitemap_dashboard' );

		return is_array( $opts ) ? $opts : array();
	}

	/**
	 * Serves the sitemap, if requested via the URL
	 *
	 * @return void
	 */
	public function serve_sitemap() {
		if ( ! function_exists( 'smartcrawl_get_sitemap_path' ) ) {
			return;
		}
		if ( ! Smartcrawl_Settings::get_setting( 'sitemap' ) ) {
			return;
		}

		$url_path = $this->get_url_part( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		$path = smartcrawl_get_sitemap_path();

		$is_gzip = preg_match( '~\.gz$~i', $url_path );
		$path = $is_gzip ? "{$path}.gz" : $path;

		if ( preg_match( '~' . preg_quote( '/sitemap.xml' ) . '(\.gz)?$~i', $url_path ) ) {
			// Check if any updates are required
			if ( ! file_exists( $path ) || $this->is_sitemap_dirty() ) {
				$this->update_sitemap();
			}

			// Serve the file if it exists
			if ( file_exists( $path ) ) {
				if ( $is_gzip ) {
					header( 'Content-Encoding: gzip' );
				}
				header( 'Content-Type: text/xml' );
				die( smartcrawl_file_get_contents( $path ) ); // phpcs:ignore -- Can't escape XML
			} else {
				wp_die( esc_html__( 'The sitemap file was not found.', 'wds' ) );
			}
		}
	}

	/**
	 * Extracts the URL part
	 *
	 * Falls back to the original passed argument
	 *
	 * @param string $raw Raw URL to extract from
	 * @param int|string $part Part flag (one of the PHP `parse_url()` flags, OR string key value)
	 *
	 * @return string
	 */
	public function get_url_part( $raw, $part ) {
		if ( empty( $part ) ) {
			return $raw;
		}

		if ( is_numeric( $part ) ) {
			$clean = wp_parse_url( $raw, $part );

			return false !== $clean
				? $clean
				: $raw;
		}
		$parts = wp_parse_url( $raw );

		return ! empty( $parts[ $part ] )
			? $parts[ $part ]
			: $raw;
	}

	/**
	 * Drops generated sitemap cache files
	 *
	 * This is so the next sitemap request re-generates the caches.
	 * Serves as performance improvement for post-based action listeners.
	 *
	 * On setups with large posts table, fully regenerating sitemap can take a
	 * while. So instead, we just drop the cache and potentially ping the
	 * search engines to notify them about the change.
	 */
	public function drop_sitemap_cache() {
		$this->mark_sitemap_as_dirty();

		// Also notify engines of changes.
		// Do *not* forcefully do so, respect settings.
		Smartcrawl_Xml_Sitemap::notify_engines();
	}

	public function json_update_sitemap() {
		$this->mark_sitemap_as_dirty();
		die( 1 );
	}

	public function json_update_engines() {
		Smartcrawl_Xml_Sitemap::notify_engines( 1 );
		die( 1 );
	}

	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( $_POST['_wds_nonce'], 'wds-nonce' ) ? stripslashes_deep( $_POST ) : array();
	}

	private function mark_sitemap_as_dirty() {
		Smartcrawl_Xml_Sitemap::get()->set_sitemap_pristine( false );
	}

	private function is_sitemap_dirty() {
		return ! Smartcrawl_Xml_Sitemap::get()->is_sitemap_pristine();
	}
}
