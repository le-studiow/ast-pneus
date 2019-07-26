<?php

class Smartcrawl_Logger {

	const L_DEBUG = 10;
	const L_INFO = 20;
	const L_NOTICE = 30;
	const L_WARNING = 40;
	const L_ERROR = 50;

	/**
	 * Default logging level
	 *
	 * @var int
	 */
	const L_DEFAULT = 30;

	private static $_instance;

	private function __construct() {
	}

	public static function debug( $message ) {
		return self::get()->log( self::L_DEBUG, $message );
	}

	/**
	 * Logging function
	 *
	 * Checks for log level active and takes
	 * an appropriate action - logs the message
	 * or not.
	 *
	 * @param int $level Log level that the message belongs to (see constants)
	 * @param string $message Message to log
	 *
	 * @return bool|int Operation status, or (int)log level if we're above listening
	 */
	public function log( $level, $message ) {
		if ( ! smartcrawl_is_switch_active( 'SMARTCRAWL_ENABLE_LOGGING' ) ) {
			return false; // Require explicit logging
		}
		if ( ! is_numeric( $level ) ) {
			return false;
		}

		$level = (int) $level;
		if ( ! in_array( $level, array_keys( self::get_known_levels() ), true ) ) {
			return false;
		}

		if ( ! $this->is_logging( $level ) ) {
			return $level;
		}

		$file = $this->get_log_file();
		if ( empty( $file ) ) {
			return false;
		}

		$timestamp = date( 'Y-m-d H:i:s' );

		$line = "[{$timestamp}][{$level}] {$message}\n";

		return ! ! smartcrawl_file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
	}

	/**
	 * Gets a list of known levels and their brief descriptions
	 *
	 * @return array List of levels and descriptions hash
	 */
	public static function get_known_levels() {
		return array(
			self::L_DEBUG   => __( 'Debug (verbose procedural data)', 'wds' ),
			self::L_INFO    => __( 'Info (verbose informal data)', 'wds' ),
			self::L_NOTICE  => __( 'Notice (attention might be needed)', 'wds' ),
			self::L_WARNING => __( 'Warning (something non-critical went wrong)', 'wds' ),
			self::L_ERROR   => __( 'Error (critical issue)', 'wds' ),
		);
	}

	/**
	 * Check if we're logging this level
	 *
	 * @param int $level Level to check
	 *
	 * @return bool Logging or not
	 */
	public function is_logging( $level ) {
		$current = self::get_log_level();

		return (int) $level >= $current;
	}

	/**
	 * Gets current logging level
	 *
	 * @return int Current level
	 */
	public static function get_log_level() {
		$level = defined( 'SMARTCRAWL_DEBUG_LOG_LEVEL' ) && is_numeric( SMARTCRAWL_DEBUG_LOG_LEVEL )
			? SMARTCRAWL_DEBUG_LOG_LEVEL
			: self::L_DEFAULT;

		return (int) apply_filters(
			'wds-log-level',
			$level
		);
	}

	/**
	 * Gets initialized log file path
	 *
	 * @return string Log file path
	 */
	public function get_log_file() {
		$file = $this->get_log_file_path();

		if ( ! file_exists( $file ) ) {
			$this->create_log();
		}

		return file_exists( $file ) && is_readable( $file )
			? $file
			: false;
	}

	/**
	 * Gets full path to log file
	 *
	 * @return string Log file path
	 */
	public function get_log_file_path() {
		$data = wp_upload_dir();
		if ( empty( $data['basedir'] ) ) {
			return false;
		}

		$file = trailingslashit( $data['basedir'] ) . 'wds-seo-log.php';

		return wp_normalize_path( $file );
	}

	/**
	 * Creates and primes the log file
	 *
	 * @return bool Operation status
	 */
	public function create_log() {
		$file = $this->get_log_file_path();

		if ( file_exists( $file ) && is_writable( $file ) ) {
			return true;
		}

		return ! ! smartcrawl_file_put_contents( $file, "<?php die(); ?>\n" );
	}

	/**
	 * Singleton instance getter
	 *
	 * @return Smartcrawl_Logger instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function info( $message ) {
		return self::get()->log( self::L_INFO, $message );
	}

	public static function notice( $message ) {
		return self::get()->log( self::L_NOTICE, $message );
	}

	public static function warning( $message ) {
		return self::get()->log( self::L_WARNING, $message );
	}

	public static function error( $message ) {
		return self::get()->log( self::L_ERROR, $message );
	}

	/**
	 * Resets log file by re-creating it
	 *
	 * @return bool Operation status
	 */
	public function reset_log() {
		return $this->remove_log() && $this->create_log();
	}

	/**
	 * Clear log file by removing it
	 *
	 * @return bool Operation status
	 */
	public function remove_log() {
		$file = $this->get_log_file_path();

		if ( file_exists( $file ) && is_writable( $file ) ) {
			return @unlink( $file );  // phpcs:ignore -- We want logger to not make any noise
		}

		return true;
	}

	private function __clone() {
	}
}
