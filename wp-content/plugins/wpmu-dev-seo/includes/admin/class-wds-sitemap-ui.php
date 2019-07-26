<?php

class Smartcrawl_Sitemap_UI extends Smartcrawl_Base_Controller {
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
		return Smartcrawl_Settings::get_setting( 'sitemap' )
		       && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_SITEMAP );
	}

	/**
	 * Child controllers can use this method to initialize.
	 *
	 * @return mixed
	 */
	protected function init() {
		if ( Smartcrawl_Settings::get_setting( 'sitemap' ) ) {
			add_filter( 'wds-sections-metabox-advanced', array(
				$this,
				'add_advanced_metabox_settings_section',
			), 30, 2 );
		}

		return true;
	}

	/**
	 * @param $sections
	 * @param WP_Post $post
	 */
	public function add_advanced_metabox_settings_section( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$post_id = $post->ID;
		$sections['metabox/metabox-advanced-sitemap-priority'] = array(
			'sitemap_priority_options' => array(
				''    => __( 'Automatic prioritization', 'wds' ),
				'1'   => __( '1 - Highest priority', 'wds' ),
				'0.9' => '0.9',
				'0.8' => '0.8 - ' . __( 'High priority (root pages default)', 'wds' ),
				'0.7' => '0.7',
				'0.6' => '0.6 - ' . __( 'Secondary priority (subpages default)', 'wds' ),
				'0.5' => '0.5 - ' . __( 'Medium priority', 'wds' ),
				'0.4' => '0.4',
				'0.3' => '0.3',
				'0.2' => '0.2',
				'0.1' => '0.1 - ' . __( 'Lowest priority', 'wds' ),
			),
			'sitemap_priority'         => smartcrawl_get_value( 'sitemap-priority', $post_id ),
		);

		return $sections;
	}
}
