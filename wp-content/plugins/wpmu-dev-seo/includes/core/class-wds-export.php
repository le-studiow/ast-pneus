<?php
/**
 * Handles export
 *
 * @package wpmu-dev-seo
 */

/**
 * Settings export class
 */
class Smartcrawl_Export {

	/**
	 * Model instance
	 *
	 * @var Smartcrawl_Model_IO
	 */
	private $_model;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->_model = new Smartcrawl_Model_IO();
	}

	/**
	 * Loads all options
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public static function load() {
		$me = new self();

		$me->load_all();

		return $me->_model;
	}

	/**
	 * Loads everything
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_all() {
		foreach ( $this->_model->get_sections() as $section ) {
			$method = array( $this, "load_{$section}" );
			if ( ! is_callable( $method ) ) {
				continue;
			}

			call_user_func( $method );
		}

		return $this->_model;
	}

	/**
	 * Loads options
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_options() {
		$options = array();

		$components = Smartcrawl_Settings::get_all_components();
		foreach ( $components as $component ) {
			$options[ $this->get_option_name( $component ) ] = Smartcrawl_Settings::get_component_options( $component );
		}

		$options['wds_settings_options'] = smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
			? Smartcrawl_Settings::get_sitewide_settings()
			: Smartcrawl_Settings::get_local_settings();

		$options['wds_blog_tabs'] = get_site_option( 'wds_blog_tabs' );

		$this->_model->set( Smartcrawl_Model_IO::OPTIONS, $options );

		return $this->_model;
	}

	/**
	 * Gets option name
	 *
	 * @param string $comp Partial.
	 *
	 * @return string Options key
	 */
	public function get_option_name( $comp ) {
		if ( in_array( $comp, Smartcrawl_Settings::get_all_components(), true ) ) {
			return "wds_{$comp}_options";
		}
	}

	/**
	 * Loads ignores
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignores() {
		$model = new Smartcrawl_Model_Ignores();
		$this->_model->set( Smartcrawl_Model_IO::IGNORES, $model->get_all() );

		return $this->_model;
	}

	/**
	 * Loads extra sitemap URLs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_extra_urls() {
		$this->_model->set( Smartcrawl_Model_IO::EXTRA_URLS, Smartcrawl_Xml_Sitemap::get_extra_urls() );

		return $this->_model;
	}

	/**
	 * Loads ignore sitemap URLs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignore_urls() {
		$this->_model->set( Smartcrawl_Model_IO::IGNORE_URLS, Smartcrawl_Xml_Sitemap::get_ignore_urls() );

		return $this->_model;
	}

	/**
	 * Loads extra sitemap post IDs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignore_post_ids() {
		$this->_model->set( Smartcrawl_Model_IO::IGNORE_POST_IDS, Smartcrawl_Xml_Sitemap::get_ignore_ids() );

		return $this->_model;
	}

	/**
	 * Loads all stored postmeta
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_postmeta() {
		global $wpdb;
		$res = $wpdb->get_results(
			"SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wds%'",
			ARRAY_A
		);
		$this->_model->set( Smartcrawl_Model_IO::POSTMETA, $res );

		return $this->_model;
	}

	/**
	 * Loads all stored taxmeta for the current site
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_taxmeta() {
		$taxmeta = get_option( 'wds_taxonomy_meta' );
		if ( ! is_array( $taxmeta ) ) {
			$taxmeta = array();
		}
		$this->_model->set( Smartcrawl_Model_IO::TAXMETA, $taxmeta );

		return $this->_model;
	}

	/**
	 * Loads all stored redirects for the current site
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_redirects() {
		$model = new Smartcrawl_Model_Redirection();
		$this->_model->set( Smartcrawl_Model_IO::REDIRECTS, $model->get_all_redirections() );

		return $this->_model;
	}
}
