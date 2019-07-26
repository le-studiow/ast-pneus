<?php

abstract class Smartcrawl_Service {


	const INTERMEDIATE_CACHE_EXPIRY = 300;
	const ERR_CACHE_EXPIRY = 120;
	const SERVICE_UPTIME = 'uptime';
	const SERVICE_SEO = 'seo';
	const SERVICE_CHECKUP = 'checkup';
	const SERVICE_SITE = 'site';
	private $_errors = array();

	/**
	 * Service factory method
	 *
	 * @param string $type Requested service type
	 *
	 * @return Smartcrawl_Uptime_Service|Smartcrawl_Checkup_Service|Smartcrawl_Site_Service|Smartcrawl_Seo_Service Smartcrawl_Service Service instance
	 */
	public static function get( $type ) {
		$type = ! empty( $type ) && in_array( $type, array(
			self::SERVICE_SEO,
			self::SERVICE_UPTIME,
			self::SERVICE_CHECKUP,
			self::SERVICE_SITE,
		), true )
			? $type
			: self::SERVICE_SEO;
		if ( self::SERVICE_UPTIME === $type ) {
			$class_name = 'Smartcrawl_Uptime_Service';
		} elseif ( self::SERVICE_CHECKUP === $type ) {
			$class_name = 'Smartcrawl_Checkup_Service';
		} elseif ( self::SERVICE_SITE === $type ) {
			$class_name = 'Smartcrawl_Site_Service';
		} else {
			$class_name = 'Smartcrawl_Seo_Service';
		}

		return new $class_name();
	}

	/**
	 * Check if status code is within radix
	 *
	 * @param int $code Code to check
	 * @param int $base Base to check
	 * @param int $radix Optional increment
	 *
	 * @return bool
	 */
	public static function is_code_within( $code, $base, $radix = 10 ) {
		$code = (int) $code;
		$base = (int) $base;
		$radix = (int) $radix;
		if ( ! $code || ! $base || ! $radix ) {
			return false;
		}

		$min = $base * $radix;
		$max = ( ( $base + 1 ) * $radix ) - 1;

		return $code >= $min && $code <= $max;
	}

	/**
	 * Service URL implementation
	 *
	 * @return string Remote service URL
	 */
	abstract public function get_service_base_url();

	/**
	 * Check if the user can access service functionality
	 *
	 * @return bool
	 */
	public function can_access() {
		$can_access = false;
		if ( ! $this->has_dashboard() ) {
			$can_access = $this->can_install();
		} elseif ( class_exists( 'WPMUDEV_Dashboard' ) && ! empty( WPMUDEV_Dashboard::$site ) && is_callable( array(
				WPMUDEV_Dashboard::$site,
				'allowed_user',
			) )
		) {
			$can_access = WPMUDEV_Dashboard::$site->allowed_user();
		}

		return (bool) apply_filters(
			$this->get_filter( 'can_access' ),
			$can_access
		);
	}

	/**
	 * Check if we have dashboard installed
	 *
	 * @return bool
	 */
	public function has_dashboard() {
		return (bool) apply_filters(
			$this->get_filter( 'has_dashboard' ),
			$this->is_dahsboard_active() && $this->has_dashboard_key()
		);
	}

	/**
	 * Filter/action name getter
	 *
	 * @param string $filter Filter name to convert
	 *
	 * @return string Full filter name
	 */
	public function get_filter( $filter = false ) {
		if ( empty( $filter ) ) {
			return false;
		}
		if ( ! is_string( $filter ) ) {
			return false;
		}

		return 'wds-model-service-' . $filter;
	}

	/**
	 * Check if we have WPMU DEV Dashboard plugin installed and activated
	 *
	 * @return bool
	 */
	public function is_dahsboard_active() {
		$installed = is_admin() ? class_exists( 'WPMUDEV_Dashboard' ) : true;

		return (bool) apply_filters(
			$this->get_filter( 'is_dahsboard_active' ),
			$installed
		);
	}

	/**
	 * Check if we have our API key
	 *
	 * If we do, this means the user has logged into the dashboard
	 *
	 * @return bool
	 */
	public function has_dashboard_key() {
		$key = $this->get_dashboard_api_key();

		return (bool) apply_filters(
			$this->get_filter( 'has_dashboard_key' ),
			! empty( $key )
		);
	}

	/**
	 * Actual dashborad API key getter.
	 *
	 * @return string Dashboard API key
	 */
	public function get_dashboard_api_key() {
		$api_key = defined( 'WPMUDEV_APIKEY' ) && WPMUDEV_APIKEY
			? WPMUDEV_APIKEY
			: get_site_option( 'wpmudev_apikey', false );

		return apply_filters(
			$this->get_filter( 'api_key' ),
			$api_key
		);
	}

	/**
	 * Check if the user can install dashboard
	 *
	 * @return bool
	 */
	public function can_install() {
		$can_install = is_multisite()
			? current_user_can( 'manage_network_options' )
			: current_user_can( 'manage_options' );

		return (bool) apply_filters(
			$this->get_filter( 'can_install' ),
			$can_install
		);
	}

	/**
	 * Checks whether the account has current paid plan with us
	 *
	 * @return bool
	 */
	public function is_member() {
		if ( ! $this->is_dahsboard_active() || ! $this->has_dashboard_key() ) {
			return false; // No sense in checking further
		}
		if ( is_admin() && ! function_exists( 'is_wpmudev_member' ) ) {
			return false;
		}

		return is_admin() ? is_wpmudev_member() : true;
	}

	/**
	 * Clears the value from cache
	 *
	 * @param string $key Key for the value to clear
	 *
	 * @return bool
	 */
	public function clear_cached( $key ) {
		$key = $this->get_cache_key( $key );
		if ( empty( $key ) ) {
			return false;
		}

		return delete_transient( $key );
	}

	/**
	 * Get the key used for caching
	 *
	 * @param string $key Key suffix
	 *
	 * @return mixed Full cache key as string, or (bool)false on failure
	 */
	public function get_cache_key( $key ) {
		if ( empty( $key ) ) {
			return false;
		}

		return $this->get_filter( $key );
	}

	/**
	 * Actually perform a request on behalf of the implementing service
	 *
	 * @param string $verb Action string
	 *
	 * @return mixed Service response hash on success, (bool)false on failure
	 */
	public function request( $verb ) {
		$response = $this->_remote_call( $verb );

		return apply_filters(
			$this->get_filter( "request-{$verb}" ),
			apply_filters(
				$this->get_filter( 'request' ),
				$response, $verb
			)
		);
	}

	/**
	 * Actually send out remote request
	 *
	 * @param string $verb Service endpoint to call
	 *
	 * @return mixed Service response hash on success, (bool)false on failure
	 */
	protected function _remote_call( $verb ) {
		if ( empty( $verb ) || ! in_array( $verb, $this->get_known_verbs(), true ) ) {
			return false;
		}

		$cacheable = $this->is_cacheable_verb( $verb );

		if ( $cacheable ) {
			$cached = $this->get_cached( $verb );
			if ( false !== $cached ) {
				Smartcrawl_Logger::debug( "Fetching [{$verb}] result from cache." );

				return $cached;
			}
		}

		// Check to see if we have a valid error cache still
		$error = $this->get_cached_error( $verb );
		if ( false !== $error && ! empty( $error ) ) {
			Smartcrawl_Logger::debug( "Error cache still in effect for [{$verb}]" );
			$errors = is_array( $error ) ? $error : array( $error );
			foreach ( $errors as $err ) {
				$this->_set_error( $err );
			}

			return false;
		}

		$remote_url = $this->get_request_url( $verb );
		if ( empty( $remote_url ) ) {
			Smartcrawl_Logger::warning( "Unable to construct endpoint URL for [{$verb}]." );

			return false;
		}

		$request_arguments = $this->get_request_arguments( $verb );
		if ( empty( $request_arguments ) ) {
			Smartcrawl_Logger::warning( "Unable to obtain request arguments for [{$verb}]." );

			return false;
		}

		Smartcrawl_Logger::debug( "Sending a remote request to [{$remote_url}] ({$verb})" );
		$response = wp_remote_request( $remote_url, $request_arguments );
		if ( is_wp_error( $response ) ) {
			Smartcrawl_Logger::error( "We were not able to communicate with [{$remote_url}] ({$verb})." );
			if ( is_callable( array( $response, 'get_error_messages' ) ) ) {
				$msgs = $response->get_error_messages();
				foreach ( $msgs as $msg ) {
					$this->_set_error( $msg );
				}
				$this->set_cached_error( $verb, $msgs );
			}

			return false;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			Smartcrawl_Logger::error( "We had an error communicating with [{$remote_url}]:[{$response_code}] ({$verb})." );
			$this->handle_error_response( $response, $verb );

			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$result = $this->_postprocess_response( $body );

		if ( $cacheable ) {
			Smartcrawl_Logger::debug( "Setting cache for [{$verb}]" );
			$this->set_cached( $verb, $result );
		}

		return $result;
	}

	/**
	 * Returns a flat list of known verbs as strings
	 *
	 * @return array
	 */
	abstract public function get_known_verbs();

	/**
	 * Determine if the action verb is able to be locally cached
	 *
	 * @param string $verb Action string
	 *
	 * @return bool
	 */
	abstract public function is_cacheable_verb( $verb );

	/**
	 * Get cached value corresponding to internal key
	 *
	 * @param string $key Key to check
	 *
	 * @return mixed Cached value, or (bool)false on failure
	 */
	public function get_cached( $key ) {
		$key = $this->get_cache_key( $key );
		if ( empty( $key ) ) {
			return false;
		}

		return get_transient( $key );
	}

	/**
	 * Special case error cache getter
	 *
	 * @param string $verb Verb to check cached errors for
	 *
	 * @return mixed Cached error or (bool) false
	 */
	public function get_cached_error( $verb ) {
		if ( empty( $verb ) ) {
			return false;
		}

		return $this->get_cached( "{$verb}-error" );
	}

	/**
	 * Adds error message to the errors queue
	 *
	 * @param string $msg Error message
	 */
	protected function _set_error( $msg ) {
		Smartcrawl_Logger::error( $msg );
		$this->_errors[] = $msg;
	}

	/**
	 * Get the full URL to perform the service request
	 *
	 * @param string $verb Action string
	 *
	 * @return mixed Full URL as string or (bool)false on failure
	 */
	abstract public function get_request_url( $verb );

	/**
	 * Spawn the arguments for WP HTTP API request call
	 *
	 * @param string $verb Action string
	 *
	 * @return mixed Array of WP HTTP API arguments on success, or (bool)false on failure
	 */
	abstract public function get_request_arguments( $verb );

	/**
	 * Special case error cache setter
	 *
	 * @param string $verb Verb to set error cache for
	 * @param mixed $error Error to set
	 *
	 * @return bool
	 */
	public function set_cached_error( $verb, $error ) {
		if ( empty( $verb ) ) {
			return false;
		}

		return $this->set_cached( "{$verb}-error", $error, self::ERR_CACHE_EXPIRY );
	}

	/**
	 * Sets cached value to the corresponding key
	 *
	 * @param string $key Key for the value to set
	 * @param mixed $value Value to set
	 * @param int $expiry Optional expiry time, in secs (one of the class expiry constants)
	 *
	 * @return bool
	 */
	public function set_cached( $key, $value, $expiry = false ) {
		$key = $this->get_cache_key( $key );
		if ( empty( $key ) ) {
			return false;
		}

		return set_transient( $key, $value, $this->get_cache_expiry( $expiry ) );
	}

	/**
	 * Get cache expiry, in seconds
	 *
	 * @param int $expiry Expiry time to approximate
	 *
	 * @return int Cache expiry time, in seconds
	 */
	public function get_cache_expiry( $expiry = false ) {
		$expiry = ! empty( $expiry ) && is_numeric( $expiry )
			? (int) $expiry
			: self::INTERMEDIATE_CACHE_EXPIRY;

		return (int) apply_filters(
			$this->get_filter( 'cache_expiry' ),
			$expiry
		);
	}

	/**
	 * Handles error response (non-200) from service
	 *
	 * @param object $response WP HTTP API response.
	 * @param string $verb Request verb.
	 */
	abstract public function handle_error_response( $response, $verb );

	/**
	 * Post-process the response body
	 *
	 * Passthrough as default implementation
	 *
	 * @param string $body Response body
	 *
	 * @return mixed
	 */
	protected function _postprocess_response( $body ) {
		return json_decode( $body, true );
	}

	/**
	 * Gets all error message strings
	 *
	 * @return array
	 */
	public function get_errors() {
		return (array) $this->_errors;
	}

	/**
	 * Checks if we have any errors this far
	 *
	 * @return bool
	 */
	public function has_errors() {
		return ! empty( $this->_errors );
	}

	/**
	 * Silently Sets all errors
	 *
	 * @param array $errs Errors to set
	 */
	protected function _set_all_errors( $errs ) {
		if ( ! is_array( $errs ) ) {
			return false;
		}
		$this->_errors = $errs;
	}

}
