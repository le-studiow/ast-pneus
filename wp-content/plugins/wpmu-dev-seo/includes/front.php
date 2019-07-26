<?php
/**
 * Initializes plugin front-end behavior
 *
 * @package wpmu-dev-seo
 */

/**
 * Frontend init class
 */
class Smartcrawl_Front extends Smartcrawl_Base_Controller {
	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * Static instance getter
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializing method
	 */
	protected function init() {
		if ( defined( 'SMARTCRAWL_EXPERIMENTAL_FEATURES_ON' ) && SMARTCRAWL_EXPERIMENTAL_FEATURES_ON ) {
			if ( file_exists( SMARTCRAWL_PLUGIN_DIR . 'tools/video-sitemaps.php' ) ) {
				require_once SMARTCRAWL_PLUGIN_DIR . 'tools/video-sitemaps.php';
			}
		}

		add_filter( 'the_content', array( $this, 'process_frontend_rendering' ), 999 );

	}

	public function process_frontend_rendering( $content ) {
		if ( ! isset( $_GET['wds-frontend-check'] ) ) { // phpcs:ignore -- Nonce not necessary
			return $content;
		}

		return '<div class="wds-frontend-content-check">' . $content . '</div>';
	}

}
