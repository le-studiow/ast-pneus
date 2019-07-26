<?php
/**
 * Import/export model
 *
 * @package wpmu-dev-seo
 */

/**
 * IO model class
 */
class Smartcrawl_Model_IO {

	const OPTIONS = 'options';
	const IGNORES = 'ignores';
	const EXTRA_URLS = 'extra_urls';
	const POSTMETA = 'postmeta';
	const TAXMETA = 'taxmeta';
	const REDIRECTS = 'redirects';
	const REDIRECT_TYPES = 'redirect_types';
	const IGNORE_URLS = 'ignore_urls';
	const IGNORE_POST_IDS = 'ignore_post_ids';

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_options = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_ignores = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_extra_urls = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_postmeta = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_taxmeta = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_redirects = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_redirect_types = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_ignore_urls = array();

	/**
	 * Intermediate staging area
	 *
	 * @var array
	 */
	private $_ignore_post_ids = array();

	/**
	 * Sets the property value
	 *
	 * @param string $what IO section to set.
	 * @param array $value Value to set.
	 *
	 * @return bool Status
	 */
	public function set( $what, $value ) {
		if ( ! in_array( $what, $this->get_sections(), true ) ) {
			return false;
		}
		$prop = "_{$what}";
		$this->$prop = $value;

		return ! ! $this->$prop;
	}

	/**
	 * Returns a list of known sections
	 *
	 * @return array List of IO sections
	 */
	public function get_sections() {
		return array(
			self::OPTIONS,
			self::IGNORES,
			self::EXTRA_URLS,
			self::POSTMETA,
			self::TAXMETA,
			self::REDIRECTS,
			self::REDIRECT_TYPES,
			self::IGNORE_URLS,
			self::IGNORE_POST_IDS,
		);
	}

	/**
	 * Encodes all loaded parameters into a JSON string
	 *
	 * @return string JSON
	 */
	public function get_json() {
		return wp_json_encode( $this->get_all() );
	}

	/**
	 * Gets all loaded parameters
	 *
	 * @return array Everything
	 */
	public function get_all() {
		$ret = array();
		foreach ( $this->get_sections() as $sect ) {
			$ret[ $sect ] = $this->get( $sect );
		}

		return $ret;
	}

	/**
	 * Gets loaded options
	 *
	 * @param string $what Which part to get.
	 *
	 * @return array
	 */
	public function get( $what ) {
		$ret = array();
		if ( ! in_array( $what, $this->get_sections(), true ) ) {
			return $ret;
		}
		$prop = "_{$what}";

		$ret = $this->$prop;

		return (array) $ret;
	}

}
