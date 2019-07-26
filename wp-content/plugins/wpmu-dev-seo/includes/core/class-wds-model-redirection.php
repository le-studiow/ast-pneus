<?php

class Smartcrawl_Model_Redirection extends Smartcrawl_Model {

	const OPTIONS_KEY = 'wds-redirections';
	const OPTIONS_KEY_TYPES = 'wds-redirections-types';

	const TYPE_301 = 301;
	const TYPE_302 = 302;

	const DEFAULT_STATUS_TYPE = 302;

	/**
	 * Gets individual redirection value
	 *
	 * @param string $source Source URL
	 * @param mixed $fallback Optional fallback value
	 *
	 * @return mixed (string)Redirection URL, or fallback value (defaults to (bool)false)
	 */
	public function get_redirection( $source, $fallback = false ) {
		$redirections = $this->get_all_redirections();

		$source = in_array( trailingslashit( $source ), array_keys( $redirections ), true )
			? trailingslashit( $source )
			: ( in_array( untrailingslashit( $source ), array_keys( $redirections ), true )
				? untrailingslashit( $source )
				: $source
			);

		return ! empty( $redirections[ $source ] )
			? $redirections[ $source ]
			: $fallback;
	}

	/**
	 * Get all defined redirections for current execution context
	 *
	 * @return array
	 */
	public function get_all_redirections() {
		$is_sitewide = is_multisite() && defined( 'SMARTCRAWL_SITEWIDE' ) && SMARTCRAWL_SITEWIDE;
		$redirections = $is_sitewide
			? get_site_option( self::OPTIONS_KEY )
			: get_option( self::OPTIONS_KEY );
		if ( ! is_array( $redirections ) ) {
			$redirections = array();
		}

		return (array) apply_filters(
			$this->get_filter( 'get-all' ),
			array_filter( $redirections )
		);
	}

	/**
	 * Gets individual redirection type value
	 *
	 * @param string $source Source URL
	 *
	 * @return mixed (int)Redirection status type, or false for default
	 */
	public function get_redirection_type( $source ) {
		$types = $this->get_all_redirection_types();

		return ! empty( $types[ $source ] )
			? $types[ $source ]
			: false;
	}

	/**
	 * Get all defined redirection types for current execution context
	 *
	 * @return array
	 */
	public function get_all_redirection_types() {
		$is_sitewide = is_multisite() && defined( 'SMARTCRAWL_SITEWIDE' ) && SMARTCRAWL_SITEWIDE;
		$types = $is_sitewide
			? get_site_option( self::OPTIONS_KEY_TYPES )
			: get_option( self::OPTIONS_KEY_TYPES );
		if ( ! is_array( $types ) ) {
			$types = array();
		}

		return (array) apply_filters(
			$this->get_filter( 'get-all-types' ),
			array_filter( $types )
		);
	}

	/**
	 * Updates a redirection for source URL in the list
	 *
	 * @param string $source Source URL
	 * @param string $redirection Redirection URL
	 *
	 * @return bool
	 */
	public function set_redirection( $source, $redirection ) {
		$redirections = $this->get_all_redirections();
		$redirections[ $source ] = $redirection;

		return $this->set_all_redirections( $redirections );
	}

	/**
	 * Batch-sets all redirections
	 *
	 * @param bool
	 */
	public function set_all_redirections( $redirections ) {
		$is_sitewide = is_multisite() && defined( 'SMARTCRAWL_SITEWIDE' ) && SMARTCRAWL_SITEWIDE;
		if ( ! is_array( $redirections ) ) {
			$redirections = array();
		}

		$redirections = (array) apply_filters(
			$this->get_filter( 'set-all' ),
			array_filter( $redirections )
		);

		return $is_sitewide
			? update_site_option( self::OPTIONS_KEY, $redirections )
			: update_option( self::OPTIONS_KEY, $redirections );
	}

	/**
	 * Updates a redirection type for source URL in the list
	 *
	 * @param string $source Source URL
	 * @param int $status Redirection status code
	 *
	 * @return bool
	 */
	public function set_redirection_type( $source, $status ) {
		$status = $this->get_valid_redirection_status_type( $status );
		$types = $this->get_all_redirection_types();
		$types[ $source ] = $status;

		return $this->set_all_redirection_types( $types );
	}

	/**
	 * Returnds a valid status redirection type
	 *
	 * @param int $status Status to validate
	 *
	 * @return mixed (int)Redirection status, or (bool)false for passthrough
	 */
	public function get_valid_redirection_status_type( $status ) {
		return is_numeric( $status ) && in_array( (int) $status, array( self::TYPE_301, self::TYPE_302 ), true )
			? (int) $status
			: false;
	}

	/**
	 * Batch-sets all redirection types
	 *
	 * @param bool
	 */
	public function set_all_redirection_types( $types ) {
		$is_sitewide = is_multisite() && defined( 'SMARTCRAWL_SITEWIDE' ) && SMARTCRAWL_SITEWIDE;
		if ( ! is_array( $types ) ) {
			$types = array();
		}

		$types = (array) apply_filters(
			$this->get_filter( 'set-all-types' ),
			array_filter( $types )
		);

		return $is_sitewide
			? update_site_option( self::OPTIONS_KEY_TYPES, $types )
			: update_option( self::OPTIONS_KEY_TYPES, $types );
	}

	/**
	 * Check if we have any redirections set
	 *
	 * @return bool
	 */
	public function has_redirections() {
		return ! ! count( $this->get_all_redirections() );
	}

	/**
	 * Default status code getter
	 *
	 * @return int Default status code
	 */
	public function get_default_redirection_status_type() {
		$settings = class_exists( 'Smartcrawl_Settings' ) && is_callable( array(
			'Smartcrawl_Settings',
			'get_specific_options',
		) )
			? Smartcrawl_Settings::get_specific_options( 'wds_settings_options' )
			: array();
		$status_code = ! empty( $settings['redirections-code'] ) && is_numeric( $settings['redirections-code'] )
			? (int) $settings['redirections-code']
			: self::DEFAULT_STATUS_TYPE;
		$status_code = $this->get_valid_redirection_status_type( $status_code );

		return ! empty( $status_code )
			? (int) $status_code
			: self::DEFAULT_STATUS_TYPE;
	}

	/**
	 * Build current URL string
	 *
	 * Omits query strings
	 *
	 * @return string Current URL
	 */
	public function get_current_url() {
		$protocol = is_ssl() ? 'https:' : 'http:';
		$domain = $_SERVER['HTTP_HOST'];

		$port = smartcrawl_is_switch_active( 'SMARTCRAWL_OMIT_PORT_MATCHES' )
			? ''
			: $this->get_current_request_port();

		$request = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		$source = $protocol . '//' . $domain . $port . $request;

		return (string) apply_filters(
			$this->get_filter( 'current_url' ),
			$source
		);
	}

	/**
	 * Fetches the current request port
	 *
	 * @return int|string Port number or empty string
	 */
	public function get_current_request_port() {
		$port = (int) $_SERVER['SERVER_PORT'] ? ':' . (int) $_SERVER['SERVER_PORT'] : '';
		if ( is_ssl() && 443 === (int) $_SERVER['SERVER_PORT'] ) {
			$port = '';
		}
		if ( ! is_ssl() && 80 === (int) $_SERVER['SERVER_PORT'] ) {
			$port = '';
		}
		return $port;
	}

	public function get_type() {
		return 'redirection';
	}
}
