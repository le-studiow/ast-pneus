<?php

class Smartcrawl_Autolinks_UI extends Smartcrawl_Base_Controller {

	/**
	 * Static instance
	 *
	 * @var Smartcrawl_OnPage
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
		return smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_AUTOLINKS );
	}

	/**
	 * Child controllers can use this method to initialize.
	 *
	 * @return mixed
	 */
	protected function init() {
		add_filter( 'wds-sections-metabox-advanced', array( $this, 'add_advanced_metabox_redirects_section' ), 20, 2 );

		if ( Smartcrawl_Settings::get_setting( 'autolinks' ) ) {
			add_filter( 'wds-sections-metabox-advanced', array(
				$this,
				'add_advanced_metabox_autolinks_section',
			), 40, 2 );
		}

		return true;
	}

	/**
	 * @param $sections
	 * @param WP_Post $post
	 */
	public function add_advanced_metabox_redirects_section( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$post_id = $post->ID;
		$sections['metabox/metabox-advanced-redirect'] = array(
			'redirect_url'   => smartcrawl_get_value( 'redirect', $post_id ),
			'has_permission' => user_can_see_seo_metabox_301_redirect(),
		);

		return $sections;
	}

	/**
	 * @param $sections
	 * @param WP_Post $post
	 */
	public function add_advanced_metabox_autolinks_section( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$post_id = $post->ID;
		$sections['metabox/metabox-advanced-autolinks'] = array(
			'autolinks_exclude' => smartcrawl_get_value( 'autolinks-exclude', $post_id ),
		);

		return $sections;
	}
}
