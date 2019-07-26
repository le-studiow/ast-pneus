<?php

class Smartcrawl_Readability_Analysis_UI extends Smartcrawl_Base_Controller {
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
		return Smartcrawl_Settings::get_setting( 'analysis-readability' );
	}

	protected function init() {
		add_filter( 'wds-sections-metabox-readability', array( $this, 'add_analysis_section' ), 10, 2 );
		add_filter( 'wds-metabox-nav-item', array( $this, 'add_issue_count' ), 10, 2 );
	}

	public function add_analysis_section( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		wp_enqueue_script( Smartcrawl_Controller_Assets::METABOX_ANALYSIS_JS );

		$sections['metabox/metabox-readability'] = array(
			'post' => $post,
		);

		return $sections;
	}

	public function add_issue_count( $tab_name, $tab_id ) {
		return $tab_id === 'wds_readability'
			? $tab_name . '<span class="wds-issues"><span></span></span>'
			: $tab_name;
	}
}
