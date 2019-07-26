<?php
/**
 * Handles imports
 *
 * @package wpmu-dev-seo
 */

/**
 * Imports handling class
 */
class Smartcrawl_Import extends Smartcrawl_WorkUnit {

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
		parent::__construct();
		$this->_model = new Smartcrawl_Model_IO();
	}

	/**
	 * Loads all options
	 *
	 * @param string $json JSON model to load from.
	 *
	 * @return Smartcrawl_Import instance
	 */
	public static function load( $json ) {
		$me = new self();

		$me->load_all( $json );

		return $me;
	}

	/**
	 * Loads everything
	 *
	 * @param string $json JSON model to load from.
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_all( $json ) {
		$data = json_decode( $json, true );
		if ( empty( $data ) ) {
			return $this->_model;
		}

		foreach ( $this->_model->get_sections() as $section ) {
			if ( ! isset( $data[ $section ] ) || ! is_array( $data[ $section ] ) ) {
				continue;
			}
			$this->_model->set( $section, $data[ $section ] );
		}

		return $this->_model;
	}

	/**
	 * Handles staging area saves
	 *
	 * @return bool
	 */
	public function save() {
		$overall_status = true;

		foreach ( $this->_model->get_sections() as $section ) {
			$method = array( $this, "save_{$section}" );
			if ( ! is_callable( $method ) ) {
				continue;
			}
			$status = call_user_func( $method );

			if ( ! $status ) {
				$this->add_error( $section, __( 'Import process failed, aborting', 'wds' ) );
				$overall_status = false;
			}
			if ( ! $overall_status ) {
				break;
			}
		}

		return $overall_status;
	}

	/**
	 * Save options
	 *
	 * @return bool
	 */
	public function save_options() {
		$overall_status = true;
		foreach ( $this->_model->get( Smartcrawl_Model_IO::OPTIONS ) as $key => $value ) {
			if ( false === $value ) {
				continue;
			} // Do not force-add false values.
			if ( 'wds_blog_tabs' === $key ) {
				$old = get_site_option( $key );
				$status = update_site_option( $key, $value );
			} else {
				$status = smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
					? update_site_option( $key, $value )
					: update_option( $key, $value );
			}
//			if ( false ) {
//				$this->add_error( $key, sprintf( __( 'Failed importing options: %s', 'wds' ), $key ) );
//				$overall_status = false;
//			}
			if ( ! $overall_status ) {
				break;
			}
		}

		return $overall_status;
	}

	/**
	 * Saves ignores
	 *
	 * @return bool
	 */
	public function save_ignores() {
		$data = $this->_model->get( Smartcrawl_Model_IO::IGNORES );
		$ignores = new Smartcrawl_Model_Ignores();

		$overall_status = true;
		foreach ( $data as $key ) {
			$status = $ignores->set_ignore( $key );

			/*
			// Now, since update_(site_)option can return non-true for success, we do not do any error checking.
			if (!$status) {
				$this->add_error($key, sprintf(__('Failed importing ignores: %s', 'wds'), $key));
				$overall_status = false;
			}
			if (!$overall_status) break;
			*/
		}

		return $overall_status;
	}

	/**
	 * Saves extra URLs
	 *
	 * @return bool
	 */
	public function save_extra_urls() {
		$data = $this->_model->get( Smartcrawl_Model_IO::EXTRA_URLS );
		$result = Smartcrawl_Xml_Sitemap::set_extra_urls( $data );

		return Smartcrawl_Xml_Sitemap::get_extra_urls() === $data;
	}

	/**
	 * Saves ignored URLs
	 *
	 * @return bool
	 */
	public function save_ignore_urls() {
		$data = $this->_model->get( Smartcrawl_Model_IO::IGNORE_URLS );
		$result = Smartcrawl_Xml_Sitemap::set_ignore_urls( $data );

		return Smartcrawl_Xml_Sitemap::get_ignore_urls() === $data;
	}

	/**
	 * Saves ignored post IDs
	 *
	 * @return bool
	 */
	public function save_ignore_post_ids() {
		$data = $this->_model->get( Smartcrawl_Model_IO::IGNORE_POST_IDS );
		$result = Smartcrawl_Xml_Sitemap::set_ignore_ids( $data );

		return Smartcrawl_Xml_Sitemap::get_ignore_ids() === $data;
	}

	/**
	 * Saves postmeta entries
	 *
	 * @TODO: actually implement this.
	 *
	 * @return bool
	 */
	public function save_postmeta() {
		return true;
	}

	/**
	 * Saves taxmeta entries
	 *
	 * @TODO: actually implement this
	 *
	 * @return bool
	 */
	public function save_taxmeta() {
		return true;
	}

	/**
	 * Saves redirects
	 *
	 * @return bool
	 */
	public function save_redirects() {
		$model = new Smartcrawl_Model_Redirection();
		$redirects = $this->_model->get( Smartcrawl_Model_IO::REDIRECTS );
		$types = $this->_model->get( Smartcrawl_Model_IO::REDIRECT_TYPES );

		$result = $model->set_all_redirections( $redirects );
		$result = $model->set_all_redirection_types( $types );

		return $redirects === $model->get_all_redirections() && $types === $model->get_all_redirection_types();
	}

	/**
	 * Gets filtering prefix
	 *
	 * @return string
	 */
	public function get_filter_prefix() {
		return 'wds-import';
	}

}
