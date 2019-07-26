<?php
/**
 * Checkup service hub
 *
 * @package wpmu-dev-seo
 */

/**
 * Checkup service implementation dispatcher class
 */
class Smartcrawl_Checkup_Service extends Smartcrawl_Service {

	const IMPL_AJAX = 'implementation::ajax';
	const IMPL_REST = 'implementation::rest';

	/**
	 * @var Smartcrawl_Checkup_Service_Implementation
	 */
	private $_implementation;

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( self::IMPL_AJAX === $this->get_implementation_type() ) {
			$this->_implementation = new Smartcrawl_Checkup_Ajax_Service();
		} else {
			$this->_implementation = new Smartcrawl_Checkup_Rest_Service();
			if ( ! has_filter( $this->get_filter( 'api-namespace' ), array( $this, 'fix_namespace' ) ) ) {
				add_filter( $this->get_filter( 'api-namespace' ), array( $this, 'fix_namespace' ) );
			}
		}
	}

	/**
	 * Gets currently set implementation technique
	 *
	 * @return string
	 */
	public function get_implementation_type() {
		return self::IMPL_REST;
	}

	/**
	 * Namespace filtering temporary fix
	 *
	 * @since 2.1.1
	 *
	 * @param string $namespace Service namespace.
	 *
	 * @return string
	 */
	public function fix_namespace( $namespace ) {
		return 'seo-checkup/v1' === $namespace
			? 'seo-checkup/v1.1'
			: $namespace;
	}

	/**
	 * Gets implementation object instance
	 *
	 * @return object
	 */
	public function get_implementation() {
		return $this->_implementation;
	}

	/**
	 * Gets all verbs known by the service
	 *
	 * @return array
	 */
	public function get_known_verbs() {
		return $this->_implementation->get_known_verbs();
	}

	/**
	 * Checks if service verb supports results caching
	 *
	 * @param string $verb Service verb.
	 *
	 * @return bool
	 */
	public function is_cacheable_verb( $verb ) {
		return $this->_implementation->is_cacheable_verb( $verb );
	}

	/**
	 * Gets service base URL
	 *
	 * @return string
	 */
	public function get_service_base_url() {
		return $this->_implementation->get_service_base_url();
	}

	/**
	 * Gets full request URL for a service verb
	 *
	 * @param string $verb Service verb.
	 *
	 * @return string|bool
	 */
	public function get_request_url( $verb ) {
		return $this->_implementation->get_request_url( $verb );
	}

	/**
	 * Gets request arguments array for a verb
	 *
	 * @param string $verb Service verb.
	 *
	 * @return array
	 */
	public function get_request_arguments( $verb ) {
		return $this->_implementation->get_request_arguments( $verb );
	}

	/**
	 * Handles service error response
	 *
	 * @param object $response WP HTTP API response.
	 */
	public function handle_error_response( $response, $verb ) {
		return $this->_implementation->handle_error_response( $response, $verb );
	}

	/**
	 * Gets last checked string
	 *
	 * @param string $format Optional timestamp format string.
	 *
	 * @return string Last checkup updated time
	 */
	public function get_last_checked( $format = false ) {
		$format = ! empty( $format )
			? $format
			: get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$time = $this->get_last_checked_timestamp();
		if ( empty( $time ) ) {
			return __( 'Never', 'wds' );
		}

		return date_i18n( $format, $time );
	}

	/**
	 * Gets last checked timestamp
	 *
	 * @return mixed
	 */
	public function get_last_checked_timestamp() {
		return get_option( $this->get_filter( 'checkup-last' ), false );
	}

	/**
	 * Issues a start request, if not already issued
	 *
	 * @return  bool
	 */
	public function start() {
		if ( $this->in_progress() ) {
			Smartcrawl_Logger::debug( 'Checkup already in progress. Doing nothing.' );
			return true;
		}

		$cached_error = $this->get_cached_error( 'checkup' );
		if ( $cached_error ) {
			Smartcrawl_Logger::debug( 'Checkup could not be started because a cached error was found: [$cached_error]' );
			return false;
		}

		$this->set_progress_flag( true );
		$this->set_cached_error( 'checkup', false );

		$verb = $this->_implementation->get_result_verb();
		$this->set_cached( "checkup-{$verb}", false );
		delete_option( $this->get_filter( "checkup-{$verb}" ) );

		Smartcrawl_Logger::debug( 'Sending start request to remote checkup service' );
		$result = $this->request( $this->_implementation->get_start_verb() );

		$data = ! empty( $result['seo'] ) ? $result['seo'] : array();
		if ( empty( $data ) ) {
			Smartcrawl_Logger::error( 'Response from remote checkup service has no data' );
			return false;
		}

		$overall = ! empty( $data['overall'] ) ? $data['overall'] : array();
		$pcnt = ! empty( $overall['pcnt_complete'] ) ? (int) $overall['pcnt_complete'] : 0;

		if ( $pcnt && $pcnt >= 99.9 ) {
			$this->done( $data );
		}

		return true;
	}

	/**
	 * Checks whether a call is currently being processed
	 *
	 * @return bool
	 */
	public function in_progress() {
		$flag = $this->get_progress_flag();

		$expected_timeout = intval( $flag ) + 360;
		if ( ! empty( $flag ) && is_numeric( $flag ) && time() > $expected_timeout ) {
			// Over 6 minutes, clear flag forcefully.
			$this->stop();
		}

		return ! ! $flag;
	}

	/**
	 * Gets progress flag state
	 *
	 * @return bool
	 */
	public function get_progress_flag() {
		return get_option( $this->get_filter( 'checkup-progress' ), false );
	}

	/**
	 * Stops expecting response
	 *
	 * @return bool
	 */
	public function stop() {
		$this->set_progress_flag( false );

		return true;
	}

	/**
	 * Sets progress flag state
	 *
	 * @param bool $flag Whether the service check is in progress.
	 *
	 * @return bool
	 */
	public function set_progress_flag( $flag ) {
		if ( ! empty( $flag ) ) {
			$flag = time();
		}

		return ! ! update_option( $this->get_filter( 'checkup-progress' ), $flag );
	}

	/**
	 * Called when the request is actually done
	 *
	 * @param array $data Response data.
	 *
	 * @return void
	 */
	public function done( $data ) {
		$verb = $this->_implementation->get_result_verb();

		$this->set_cached( "checkup-{$verb}", $data );
		update_option( $this->get_filter( "checkup-{$verb}" ), $data );

		$this->stop();
		$this->set_last_checked();

		if ( is_callable( array( $this->_implementation, 'after_done' ) ) ) {
			if ( ! $this->get_cached_error( 'checkup' ) ) {
				$this->_implementation->after_done();
			}
		}

		do_action( $this->get_filter( 'checkup_done' ), $data, $this );
	}

	/**
	 * Sets last checked time
	 *
	 * @param int $time Optional timestamp, defaults to now.
	 *
	 * @return bool
	 */
	public function set_last_checked( $time = false ) {
		$time = ! empty( $time ) && is_numeric( $time )
			? (int) $time
			: current_time( 'timestamp' );

		return ! ! update_option( $this->get_filter( 'checkup-last' ), $time );
	}

	/**
	 * Checks current status
	 *
	 * Issues status request, or serves cached update
	 *
	 * @return int Percentage
	 */
	public function status() {
		$res = $this->in_progress()
			? $this->request( $this->_implementation->get_result_verb() )
			: $this->get_cached( $this->_implementation->get_result_verb() );
		if ( ! is_array( $res ) ) {
			$res = array();
		}
		if ( isset( $res['success'] ) && empty( $res['success'] ) ) {
			$this->done( $res );

			return 100;
		}
		$status = isset( $res['success'] ) && ! empty( $res['success'] ) ? 1 : 0;
		if ( ! empty( $res['data'] ) ) {
			$data = $res['data'];
		} else {
			$data = $res;
		}

		if ( ! empty( $data['overall']['pcnt_complete'] ) ) {
			$status = (int) $data['overall']['pcnt_complete'];
			if ( $status >= 100 ) {
				$this->done( $data );

				return 100;
			}
		} elseif ( empty( $res ) && empty( $status ) ) {
			$this->done( $res );

			return 100;
		}

		return $status;
	}

	/**
	 * Checks result
	 *
	 * Issues result request, or serves cached update
	 *
	 * @return array
	 */
	public function result() {
		if ( $this->in_progress() ) {
			return array();
		}

		$error = $this->get_cached_error( 'checkup' );
		if ( ! empty( $error ) ) {
			return array( 'error' => $error );
		}

		$verb = $this->_implementation->get_result_verb();
		$res = get_option( $this->get_filter( "checkup-{$verb}" ), array() );
		if ( empty( $res ) ) {
			$res = $this->request( $verb );
			$this->done( $res );
		}

		if ( ! is_array( $res ) ) {
			// JSON string.
			$res = json_decode( $res, true );
			if ( ! empty( $res['data'] ) ) {
				$res = $res['data']; // @TODO This should probably be refactored.
			}
		} elseif ( ! empty( $res['data'] ) ) {
			$res = $res['data'];
		}

		if ( is_array( $res ) && ! empty( $res['seo'] ) ) {
			return $res['seo'];
		}

		$error = $this->get_cached_error( 'checkup' );
		if ( empty( $error ) && is_string( $res ) ) {
			$error = $res;
		}

		return ! empty( $error )
			? array( 'error' => $error )
			: array( 'error' => __( 'Your request timed out', 'wds' ) );
	}

}
