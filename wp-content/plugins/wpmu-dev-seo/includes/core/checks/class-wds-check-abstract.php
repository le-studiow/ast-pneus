<?php
/**
 * Abstractions related to checks
 *
 * @package wpmu-dev-seo
 */

/**
 * Check abstraction class
 */
abstract class Smartcrawl_Check_Abstract {

	/**
	 * Holds subject reference
	 *
	 * @var string|WP_Post
	 */
	private $_subject = '';
	/**
	 * Holds a list of expected keywords (as strings)
	 *
	 * This is an internal normalized focus keywords representation,
	 * where the key phrases are also normalized to words.
	 *
	 * @var array
	 */
	private $_focus = array();
	/**
	 * Holds a list of raw keywords
	 *
	 * As opposed to $_focus, this one holds raw,
	 * denormalized version of focus key words|phrases.
	 *
	 * @var array
	 */
	private $_raw_focus_keywords = array();

	/**
	 * Constructor
	 *
	 * Accepts optional current working markup parameter
	 *
	 * @param string $markup Current working markup (optional).
	 */
	public function __construct( $markup = '' ) {
		$this->set_subject( $markup );
	}

	/**
	 * Sets working markup
	 *
	 * @param string|WP_Post $subject Markup to work with.
	 *
	 * @return bool
	 */
	public function set_subject( $subject = '' ) {
		if ( is_string( $subject ) || ( is_object( $subject ) && $subject instanceof WP_Post ) ) {
			$this->_subject = $subject;

			return true;
		}

		return false;
	}

	/**
	 * Applies the check
	 *
	 * @return bool
	 */
	abstract public function apply();

	/**
	 * Gets status message
	 *
	 * @return string
	 */
	abstract public function get_status_msg();

	/**
	 * Recommendation getter
	 *
	 * To be overridden in concrete implementations
	 *
	 * @return string
	 */
	public function get_recommendation() {
		return '';
	}

	/**
	 * More info getter
	 *
	 * To be overridden in concrete implementations
	 *
	 * @return string
	 */
	public function get_more_info() {
		return '';
	}

	/**
	 * Gets current working markup
	 *
	 * @return string Working markup
	 */
	public function get_markup() {
		return $this->_subject;
	}

	/**
	 * Returns raw, non-internal focus keywords
	 *
	 * @return array
	 */
	public function get_raw_focus() {
		return (array) $this->_raw_focus_keywords;
	}

	/**
	 * Checks if subject string length is within constraints
	 *
	 * @param string $str Subject string.
	 * @param int $min Optional minimum length.
	 * @param int $max Optional maximum length.
	 *
	 * @return bool|int (bool)true if within constraints
	 *                  negative integer if shorter than $min
	 *                  positive integer if longer than $max
	 */
	public function is_within_char_length( $str, $min, $max ) {
		$str = '' . $str;
		$min = (int) $min;
		$max = (int) $max;

		if ( $min && Smartcrawl_String::len( $str ) < $min ) {
			return - 1;
		}
		if ( $max && Smartcrawl_String::len( $str ) > $max ) {
			return 1;
		}

		return true;
	}

	/**
	 * Checks whether we have some keywords in place
	 *
	 * @param string $str Subject string.
	 *
	 * @return bool
	 */
	public function has_focus( $str ) {
		$kws = Smartcrawl_String::keywords( $str );
		$expected = $this->get_focus();

		if ( empty( $expected ) ) {
			return true;
		} // We don't seem to have any focus keywords, so... yeah.
		$diff = array_diff( $expected, array_keys( $kws ) );

		return count( $expected ) !== count( $diff );
	}

	/**
	 * Returns list of expected keywords
	 *
	 * @return array
	 */
	public function get_focus() {
		return (array) $this->_focus;
	}

	/**
	 * Sets expected keywords
	 *
	 * Converts keywords collection to internal representation,
	 * abstracting away key phrases and normalizing everything
	 * to a list of words which can be checked.
	 *
	 * @param array $keywords List of expected keywords.
	 *
	 * @return bool
	 */
	public function set_focus( $keywords = array() ) {
		$kwds = array();
		foreach ( $keywords as $k ) {
			$kwds = array_merge( $kwds, Smartcrawl_String::keywords( $k ) );
		}
		$this->_raw_focus_keywords = $keywords;
		$this->_focus = array_unique( array_keys( $kwds ) );

		return ! ! $this->_focus;
	}

}
