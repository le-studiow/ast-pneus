<?php

/**
 * Class Smartcrawl_Compatibility
 *
 * Fixes third-party compatibility issues
 */
class Smartcrawl_Compatibility extends Smartcrawl_Base_Controller {
	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Compatibility
	 */
	private static $_instance;

	/**
	 * Obtain instance without booting up
	 *
	 * @return Smartcrawl_Compatibility instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected function init() {
		add_action( 'init', array( $this, 'load_divi_in_ajax' ), - 10 );
		add_action( 'init', array( $this, 'load_wp_bakery_shortcodes' ), - 10 );
		add_filter( 'wds-omitted-shortcodes', array( $this, 'avada_omitted_shortcodes' ) );
		add_filter( 'wds-omitted-shortcodes', array( $this, 'divi_omitted_shortcodes' ) );
		add_filter( 'wds-omitted-shortcodes', array( $this, 'wpbakery_omitted_shortcodes' ) );
		add_filter( 'wds-omitted-shortcodes', array( $this, 'swift_omitted_shortcodes' ) );

		return true;
	}

	public function avada_omitted_shortcodes( $omitted ) {
		return array_merge( $omitted, array(
			'fusion_code',
			'fusion_imageframe',
			'fusion_slide',
			'fusion_syntax_highlighter',
		) );
	}

	public function divi_omitted_shortcodes( $omitted ) {
		return array_merge( $omitted, array(
			'et_pb_code',
			'et_pb_fullwidth_code',
		) );
	}

	public function wpbakery_omitted_shortcodes( $omitted ) {
		return array_merge( $omitted, array(
			'vc_raw_js',
			'vc_raw_html',
		) );
	}

	public function swift_omitted_shortcodes( $omitted ) {
		return array_merge( $omitted, array(
			'spb_raw_js',
			'spb_raw_html',
		) );
	}

	/**
	 * Divi doesn't usually load its shortcodes during ajax requests but we need these shortcodes in order to
	 * render an accurate preview.
	 *
	 * Force Divi to load during our requests.
	 */
	public function load_divi_in_ajax() {
		if ( $this->is_preview_request() ) {
			$_POST['et_load_builder_modules'] = '1';
		}
	}

	/**
	 * Force WPBakery to load its shortcodes so we can render an accurate preview
	 */
	public function load_wp_bakery_shortcodes() {
		if ( $this->is_preview_request() ) {
			$load_shortcodes_callback = array(
				'WPBMap',
				'addAllMappedShortcodes',
			);

			if ( is_callable( $load_shortcodes_callback ) ) {
				add_action( 'init', $load_shortcodes_callback );
			}
		}
	}

	private function is_preview_request() {
		return is_admin()
		       && smartcrawl_is_switch_active( 'DOING_AJAX' )
		       && isset( $_POST['_wds_nonce'] )
		       && (
			       wp_verify_nonce( $_POST['_wds_nonce'], 'wds-metabox-nonce' )
			       || wp_verify_nonce( $_POST['_wds_nonce'], 'wds-onpage-nonce' )
		       );
	}
}
