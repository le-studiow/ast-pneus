<?php

class Smartcrawl_Social_UI extends Smartcrawl_Base_Controller {
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

	public function should_run() {
		return Smartcrawl_Settings::get_setting( 'social' )
		       && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_SOCIAL );
	}

	protected function init() {
		add_filter( 'wds-sections-metabox-social', array( $this, 'add_social_sections' ), 10, 2 );
	}

	public function add_social_sections( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$all_options = Smartcrawl_Settings::get_options();
		$og_setting_enabled = (bool) smartcrawl_get_array_value( $all_options, 'og-enable' );
		$og_post_type_enabled = (bool) smartcrawl_get_array_value( $all_options, 'og-active-' . get_post_type( $post ) );
		if ( $og_setting_enabled && $og_post_type_enabled ) {
			$sections['metabox/metabox-social-opengraph'] = array(
				'post' => $post,
			);
		}

		$twitter_post_type_enabled = (bool) smartcrawl_get_array_value( $all_options, 'twitter-active-' . get_post_type( $post ) );
		$twitter_setting_enabled = (bool) smartcrawl_get_array_value( $all_options, 'twitter-card-enable' );
		if ( $twitter_post_type_enabled && $twitter_setting_enabled ) {
			$sections['metabox/metabox-social-twitter'] = array(
				'post' => $post,
			);
		}

		return $sections;
	}
}
