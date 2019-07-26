<?php

abstract class Smartcrawl_WorkUnit {

	/**
	 * Holds reference to WP error instance
	 */
	protected $_error;

	public function __construct() {
		$this->_error = new WP_Error();
	}

	/**
	 * Adds error to the queue
	 *
	 * @param string $code Error code
	 * @param string $msg Error message
	 *
	 * @return bool Status
	 */
	public function add_error( $code, $msg ) {
		$prefix = $this->get_filter_prefix();
		$errcode = "{$prefix}::{$code}";

		Smartcrawl_Logger::debug( "({$errcode}) $msg" );

		$this->_error->add( $errcode, $msg );

		return true;
	}

	abstract public function get_filter_prefix();

	/**
	 * Gets a list of detected errors
	 *
	 * @return array List of detected errors, as check => msg pairs
	 */
	public function get_errors() {
		$error_codes = $this->_error->get_error_codes();
		$errors = array();

		if ( empty( $error_codes ) ) {
			return $errors;
		}

		foreach ( $error_codes as $code ) {
			$errors[ $code ] = $this->_error->get_error_message( $code );
		}

		return $errors;
	}

	/**
	 * Wrapper for `apply_filters` core call
	 *
	 * Accepts variable length of parameters, the first one
	 * being a filter suffix, the rest are passed as parameters.
	 *
	 * @return mixed Whatever the filter returns
	 */
	public function apply_filters() {
		$args = func_get_args();
		if ( empty( $args ) ) {
			return false;
		}

		$filter = array_splice( $args, 0, 1 );
		array_unshift( $args, $this->get_filter( reset( $filter ) ) );

		return call_user_func_array( 'apply_filters', $args );
	}

	/**
	 * Expands filter suffix name
	 *
	 * @param string $suffix Filter suffix
	 *
	 * @return string Full filter name
	 */
	public function get_filter( $suffix ) {
		$prefix = $this->get_filter_prefix();

		return "{$prefix}-{$suffix}";
	}

}
