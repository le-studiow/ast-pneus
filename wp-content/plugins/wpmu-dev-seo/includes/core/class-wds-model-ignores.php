<?php

class Smartcrawl_Model_Ignores extends Smartcrawl_Model {

	const IGNORES_STORAGE = 'wds-ignores';

	private $_ignores = array();

	public function __construct() {
		$this->load();
	}

	/**
	 * Loads the ignores list
	 *
	 * @return bool Status
	 */
	public function load() {
		$this->_ignores = array();

		$ignores = smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
			? get_site_option( self::IGNORES_STORAGE )
			: get_option( self::IGNORES_STORAGE );

		if ( ! empty( $ignores ) && is_array( $ignores ) ) {
			$this->_ignores = array_filter( array_unique( $ignores ) );

			return true;
		}

		return false;
	}

	public function get_type() {
		return 'ignores';
	}

	/**
	 * Clears the persisted ignores list
	 *
	 * @return bool Status
	 */
	public function clear() {
		return smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
			? update_site_option( self::IGNORES_STORAGE, array() )
			: update_option( self::IGNORES_STORAGE, array() );
	}

	/**
	 * Adds ignored item to ignores list
	 *
	 * @param string $key Item key to ignore
	 *
	 * @return bool Status
	 */
	public function set_ignore( $key ) {
		if ( empty( $key ) ) {
			return false;
		}
		if ( ! $this->is_valid_ignore_key( $key ) ) {
			return false;
		}

		$this->_ignores[] = $key;
		$this->_ignores = array_filter( array_unique( $this->_ignores ) );

		$status = smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
			? update_site_option( self::IGNORES_STORAGE, $this->_ignores )
			: update_option( self::IGNORES_STORAGE, $this->_ignores );

		return $status;
	}

	/**
	 * Check if a string is valid ignored issue identifier
	 *
	 * @param string $key String to check
	 *
	 * @return bool Valid state
	 */
	public function is_valid_ignore_key( $key ) {
		if ( ! is_string( $key ) ) {
			return false;
		}

		return ! ! preg_match( '/^[a-f0-9]+$/i', $key );
	}

	/**
	 * Removes ignored item from ignores list
	 *
	 * @param string $key Item key to remove from ignores
	 *
	 * @return bool Status
	 */
	public function unset_ignore( $key ) {
		if ( empty( $key ) ) {
			return false;
		}
		if ( ! $this->is_valid_ignore_key( $key ) ) {
			return false;
		}

		$index = array_search( $key, $this->_ignores, true );
		if ( false !== $index ) {
			unset( $this->_ignores[ $index ] );
		}

		$this->_ignores = array_filter( array_unique( $this->_ignores ) );

		$status = smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' )
			? update_site_option( self::IGNORES_STORAGE, $this->_ignores )
			: update_option( self::IGNORES_STORAGE, $this->_ignores );

		return $status;
	}

	/**
	 * Checks if an issue is to be ignored
	 *
	 * @return bool
	 */
	public function is_ignored( $key ) {
		return (bool) in_array( $key, $this->get_all(), true );
	}

	/**
	 * Gets a list of ignored items
	 *
	 * @return array List of ignored items unique IDs
	 */
	public function get_all() {
		return $this->_ignores;
	}
}
