<?php
/**
 * Periodical execution module
 *
 * @package wpmu-dev-seo
 */

/**
 * Cron controller
 */
class Smartcrawl_Controller_Cron { // phpcs:ignore -- We have two versions of this class

	const ACTION_CRAWL = 'wds-cron-start_service';
	const ACTION_CHECKUP = 'wds-cron-start_checkup';
	const ACTION_CHECKUP_RESULT = 'wds-cron-checkup_result';

	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Controller_Cron
	 */
	private static $_instance;

	/**
	 * Controller actively running flag
	 *
	 * @var bool
	 */
	private $_is_running = false;

	/**
	 * Constructor
	 */
	private function __construct() {
	}

	/**
	 * Singleton instance getter
	 *
	 * @return object Smartcrawl_Controller_Cron instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Boots controller interface
	 *
	 * @return bool
	 */
	public function run() {
		if ( ! $this->is_running() ) {
			$this->_add_hooks();
		}

		return $this->is_running();
	}

	/**
	 * Check whether controller interface is active
	 *
	 * @return bool
	 */
	public function is_running() {
		return ! ! $this->_is_running;
	}

	/**
	 * Sets up controller listening interface
	 *
	 * Also sets up controller running flag approprietly.
	 *
	 * @return void
	 */
	private function _add_hooks() {
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule_intervals' ) );

		$copts = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SITEMAP );
		if ( ! empty( $copts['crawler-cron-enable'] ) ) {
			add_action( $this->get_filter( self::ACTION_CRAWL ), array( $this, 'start_crawl' ) );
			$this->fix_legacy_round_runtimes( self::ACTION_CRAWL, $copts );
		}

		$chopts = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_CHECKUP );
		if ( ! empty( $chopts['checkup-cron-enable'] ) ) {
			$this->fix_legacy_round_runtimes( self::ACTION_CHECKUP, $chopts );
			add_action( $this->get_filter( self::ACTION_CHECKUP ), array( $this, 'start_checkup' ) );
			add_action( $this->get_filter( self::ACTION_CHECKUP_RESULT ), array( $this, 'check_checkup_result' ) );
		}

		$this->_is_running = true;
	}

	/**
	 * Gets prefixed filter action
	 *
	 * @param string $what Filter action suffix.
	 *
	 * @return string Full filter action
	 */
	public function get_filter( $what ) {
		return 'wds-controller-cron-' . $what;
	}

	/**
	 * Checks for round time schedules and adjusts
	 * schedule accordingly
	 *
	 * @param string $type Action type to check.
	 * @param array $opts Action options.
	 *
	 * @return bool
	 */
	public function fix_legacy_round_runtimes( $type, $opts ) {
		$prefix = self::ACTION_CRAWL === $type
			? 'crawler'
			: 'checkup';
		$dow = ! empty( $opts["{$prefix}-dow"] ) && in_array( (int) $opts["{$prefix}-dow"], range( 0, 6 ), true )
			? (int) $opts["{$prefix}-dow"]
			: 0;
		$tod = ! empty( $opts["{$prefix}-tod"] ) && in_array( (int) $opts["{$prefix}-tod"], range( 0, 23 ), true )
			? (int) $opts["{$prefix}-tod"]
			: 0;

		if ( 0 === $dow && 0 === $tod && $this->is_round_next_runtime( $type ) ) {
			// Reschedule.
			Smartcrawl_Logger::warning( "Rescheduling detected rounded event {$prefix}" );
			$comp_key = self::ACTION_CRAWL === $type
				? Smartcrawl_Settings::COMP_SITEMAP
				: Smartcrawl_Settings::COMP_CHECKUP;
			$opts["{$prefix}-dow"] = rand( 0, 6 );
			$opts["{$prefix}-tod"] = rand( 0, 24 );

			Smartcrawl_Settings::update_component_options( $comp_key, $opts );
			$this->unschedule( $type );
			call_user_func( array( $this, "set_up_{$prefix}_schedule" ) );

			return true;
		}

		return false;
	}

	/**
	 * Checks if next runtime event schedule is round
	 *
	 * @param string $type Action type to check.
	 *
	 * @return bool
	 */
	public function is_round_next_runtime( $type ) {
		$current = $this->get_next_event( $type );
		if ( empty( $current ) ) {
			return false;
		}

		return '00:00' === date( 'H:i', $current );
	}

	/**
	 * Gets next scheduled event time
	 *
	 * @param string $event Optional event name, defaults to service start.
	 *
	 * @return int|bool UNIX timestamp or false if no next event
	 */
	public function get_next_event( $event = false ) {
		$event = ! empty( $event ) ? $event : self::ACTION_CRAWL;

		return wp_next_scheduled( $this->get_filter( $event ) );
	}

	/**
	 * Unschedules a particular event
	 *
	 * @param string $event Optional event name, defaults to service start.
	 *
	 * @return bool
	 */
	public function unschedule( $event = false ) {
		$event = ! empty( $event ) ? $event : self::ACTION_CRAWL;
		Smartcrawl_Logger::info( "Unscheduling event {$event}" );
		$tstamp = $this->get_next_event( $event );
		if ( $tstamp ) {
			Smartcrawl_Logger::debug( "Found next event {$event} at {$tstamp}" );
			wp_unschedule_event( $tstamp, $this->get_filter( $event ) );
		}

		wp_clear_scheduled_hook( $this->get_filter( $event ) );

		return true;
	}

	/**
	 * Controller interface stop
	 *
	 * @return bool
	 */
	public function stop() {
		if ( $this->is_running() ) {
			$this->_remove_hooks();
		}

		return $this->is_running();
	}

	/**
	 * Tears down controller listening interface
	 *
	 * Also sets up controller running flag approprietly.
	 *
	 * @return void
	 */
	private function _remove_hooks() {

		remove_action( $this->get_filter( self::ACTION_CRAWL ), array( $this, 'start_crawl' ) );
		remove_action( $this->get_filter( self::ACTION_CHECKUP ), array( $this, 'start_checkup' ) );
		remove_filter( 'cron_schedules', array( $this, 'add_cron_schedule_intervals' ) );

		$this->_is_running = false;
	}

	/**
	 * Checks whether we have a next event scheduled
	 *
	 * @param string $event Optional event name, defaults to service start.
	 *
	 * @return bool
	 */
	public function has_next_event( $event = false ) {
		return ! ! $this->get_next_event( $event );
	}

	/**
	 * Sets up overall schedules
	 *
	 * @uses Smartcrawl_Controller_Cron::set_up_checkup_schedule()
	 * @uses Smartcrawl_Controller_Cron::set_up_crawler_schedule()
	 *
	 * @return void
	 */
	public function set_up_schedule() {
		Smartcrawl_Logger::debug( 'Setting up schedules' );
		$this->set_up_crawler_schedule();
		$this->set_up_checkup_schedule();
	}

	/**
	 * Sets up crawl service schedule
	 *
	 * @return bool
	 */
	public function set_up_crawler_schedule() {
		Smartcrawl_Logger::debug( 'Setting up cralwer schedule' );

		$options = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SITEMAP );

		if ( empty( $options['crawler-cron-enable'] ) ) {
			Smartcrawl_Logger::debug( 'Disabling crawler cron' );
			$this->unschedule( self::ACTION_CRAWL );

			return false;
		}

		$current = $this->get_next_event( self::ACTION_CRAWL );
		$now = time();
		$frequency = $this->get_valid_frequency(
			( ! empty( $options['crawler-frequency'] ) ? $options['crawler-frequency'] : array() )
		);
		$dow = ! empty( $options['crawler-dow'] ) && in_array( (int) $options['crawler-dow'], range( 0, 6 ), true )
			? (int) $options['crawler-dow']
			: 0;
		$tod = ! empty( $options['crawler-tod'] ) && in_array( (int) $options['crawler-tod'], range( 0, 23 ), true )
			? (int) $options['crawler-tod']
			: 0;
		$next = $this->get_estimated_next_event( $now, $frequency, $dow, $tod );

		$msg = sprintf( "Attempt rescheduling crawl start ({$frequency},{$dow},{$tod}): {$next} (%s)", date( 'Y-m-d@H:i', $next ) );
		if ( ! empty( $current ) ) {
			$msg .= sprintf( " by replacing {$current} (%s)", date( 'Y-m-d@H:i', $current ) );
		}
		Smartcrawl_Logger::debug( $msg );

		$diff = abs( $current - $next );
		if ( $diff > 59 * 60 ) {
			Smartcrawl_Logger::info( sprintf(
				"Rescheduling crawl start from {$current} (%s) to {$next} (%s)",
				date( 'Y-m-d@H:i', $current ),
				date( 'Y-m-d@H:i', $next )
			) );
			$this->schedule( self::ACTION_CRAWL, $next, $frequency );
		} else {
			Smartcrawl_Logger::info( 'Currently scheduled crawl matches our next sync estimate, leaving it alone' );
		}

		return true;
	}

	/**
	 * Gets estimated next event time based on parameters
	 *
	 * @param int $pivot Pivot time - base estimation relative to this (UNIX timestamp).
	 * @param strng $frequency Valid frequency interval.
	 * @param int $dow Day of the week (0-6).
	 * @param int $tod Time of day (0-23).
	 *
	 * @return int Estimated next event time as UNIX timestamp
	 */
	public function get_estimated_next_event( $pivot, $frequency, $dow, $tod ) {
		$start = $this->get_initial_pivot_time( $pivot, $frequency );
		$offset = $start + ( $dow * DAY_IN_SECONDS );
		$time = strtotime( date( "Y-m-d {$tod}:00", $offset ) );
		$freqs = array(
			'daily'   => DAY_IN_SECONDS,
			'weekly'  => 7 * DAY_IN_SECONDS,
			'monthly' => 30 * DAY_IN_SECONDS,
		);
		if ( $time > $pivot ) {
			return $time;
		}

		$freq = $freqs[ $this->get_valid_frequency( $frequency ) ];

		return $time + $freq;
	}

	/**
	 * Gets primed pivot time for a given frequency value
	 *
	 * @param int $pivot Raw pivot UNIX timestamp.
	 * @param string $frequency Frequency interval.
	 *
	 * @return int Zeroed pivot time for given frequency interval
	 */
	public function get_initial_pivot_time( $pivot, $frequency ) {
		$frequency = $this->get_valid_frequency( $frequency );

		if ( 'daily' === $frequency ) {
			return strtotime( date( 'Y-m-d 00:00', $pivot ) );
		}

		if ( 'weekly' === $frequency ) {
			$monday = strtotime( 'this monday', $pivot );
			if ( $monday > $pivot ) {
				return $monday - ( 7 * DAY_IN_SECONDS );
			}

			return $monday;
		}

		if ( 'monthly' === $frequency ) {
			$day = (int) date( 'd', $pivot );
			$today = strtotime( date( 'Y-m-d H:i', $pivot ) );

			return $today - ( $day * DAY_IN_SECONDS );
		}

		return $pivot;
	}

	/**
	 * Gets validated frequency interval
	 *
	 * @param string $freq Raw frequency string.
	 *
	 * @return string
	 */
	public function get_valid_frequency( $freq ) {
		if ( in_array( $freq, array_keys( $this->get_frequencies() ), true ) ) {
			return $freq;
		}

		return $this->get_default_frequency();
	}

	/**
	 * Gets a list of frequency intervals
	 *
	 * @return array
	 */
	public function get_frequencies() {
		return array(
			'daily'   => __( 'Daily', 'wds' ),
			'weekly'  => __( 'Weekly', 'wds' ),
			'monthly' => __( 'Monthly', 'wds' ),
		);
	}

	/**
	 * Gets default frequency interval (fallback)
	 *
	 * @return string
	 */
	public function get_default_frequency() {
		return 'weekly';
	}

	/**
	 * Schedules a particular event
	 *
	 * @param string $event Event name.
	 * @param int $time UNIX timestamp.
	 * @param string $recurrence Event recurrence.
	 *
	 * @return bool
	 */
	public function schedule( $event, $time, $recurrence = false ) {
		Smartcrawl_Logger::info( "Start scheduling new {$recurrence} event {$event}" );

		$this->unschedule( $event );
		$recurrence = $this->get_valid_frequency( $recurrence );
		$now = time();
		while ( $time < $now ) {
			Smartcrawl_Logger::debug( "Time in the past, applying offset for {$recurrence} recurrence" );
			$offset = DAY_IN_SECONDS;
			if ( 'weekly' === $recurrence ) {
				$offset *= 7;
			}
			if ( 'monthly' === $recurrence ) {
				$offset *= 30;
			}
			$time += $offset;
		}

		// Make the time not round.
		$time += rand( 0, 59 ) * 60;

		Smartcrawl_Logger::debug( sprintf( "Adding new {$recurrence} event {$event} at {$time} (%s)", date( 'Y-m-d@H:i', $time ) ) );

		$result = wp_schedule_event(
			          $time,
			          $recurrence,
			          $this->get_filter( $event )
		          ) !== false;

		if ( $result ) {
			Smartcrawl_Logger::info( "New {$recurrence} event {$event} added at {$time}" );
		} else {
			Smartcrawl_Logger::warning( "Failed adding new {$recurrence} event {$event} at {$time}" );
		}

		return $result;
	}

	/**
	 * Sets up checkup service schedule
	 *
	 * @return bool
	 */
	public function set_up_checkup_schedule() {
		Smartcrawl_Logger::debug( 'Setting up checkup schedule' );

		$options = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_CHECKUP );

		if ( empty( $options['checkup-cron-enable'] ) ) {
			Smartcrawl_Logger::debug( 'Disabling checkup cron' );
			$this->unschedule( self::ACTION_CHECKUP );

			return false;
		}

		$current = $this->get_next_event( self::ACTION_CHECKUP );
		$now = time();
		$frequency = $this->get_valid_frequency(
			( ! empty( $options['checkup-frequency'] ) ? $options['checkup-frequency'] : array() )
		);
		$dow = ! empty( $options['checkup-dow'] ) && in_array( (int) $options['checkup-dow'], range( 0, 6 ), true )
			? (int) $options['checkup-dow']
			: 0;
		$tod = ! empty( $options['checkup-tod'] ) && in_array( (int) $options['checkup-tod'], range( 0, 23 ), true )
			? (int) $options['checkup-tod']
			: 0;
		$next = $this->get_estimated_next_event( $now, $frequency, $dow, $tod );

		$msg = sprintf( "Attempt rescheduling checkup start ({$frequency},{$dow},{$tod}): {$next} (%s)", date( 'Y-m-d@H:i', $next ) );
		if ( ! empty( $current ) ) {
			$msg .= sprintf( " by replacing {$current} (%s)", date( 'Y-m-d@H:i', $current ) );
		}
		Smartcrawl_Logger::debug( $msg );

		$diff = abs( $current - $next );
		if ( $diff > 59 * 60 ) {
			Smartcrawl_Logger::info( sprintf(
				"Rescheduling checkup start from {$current} (%s) to {$next} (%s)",
				date( 'Y-m-d@H:i', $current ),
				date( 'Y-m-d@H:i', $next )
			) );
			$this->schedule( self::ACTION_CHECKUP, $next, $frequency );
		} else {
			Smartcrawl_Logger::info( 'Currently scheduled checkup matches our next sync estimate, leaving it alone' );
		}

		return true;
	}

	/**
	 * Starts crawl
	 *
	 * @return bool
	 */
	public function start_crawl() {
		Smartcrawl_Logger::debug( 'Triggered automated crawl start action' );

		$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SEO );
		$result = $service->start();

		if ( $result ) {
			Smartcrawl_Logger::debug( 'Successfully started a crawl' );
		} else {
			Smartcrawl_Logger::warning( 'Automated crawl start action failed' );
		}

		return $result;
	}

	/**
	 * Starts checkup
	 *
	 * @return bool
	 */
	public function start_checkup() {
		Smartcrawl_Logger::debug( 'Triggered automated checkup start action' );

		$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
		if ( $service->in_progress() ) {
			return false;
		} // Already running.
		$result = $service->start();

		if ( $result ) {
			Smartcrawl_Logger::debug( 'Successfully started a checkup' );
			// Check result immediately.
			$this->check_checkup_result();
		} else {
			Smartcrawl_Logger::warning( 'Automated checkup start action failed' );
		}

		return $result;
	}

	/**
	 * Checks checkup result for cron action
	 *
	 * Schedules another singular check after timeout if not ready yet.
	 *
	 * @return bool
	 */
	public function check_checkup_result() {
		Smartcrawl_Logger::debug( 'Triggered checkup results check' );

		$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
		$status = $service->status();
		Smartcrawl_Logger::debug( "Checkup status: {$status}%" );

		if ( (int) $status < 100 ) {
			Smartcrawl_Logger::debug( 'Re-scheduling checkup status event' );
			wp_schedule_single_event( time() + $this->get_checkup_ping_delay(), $this->get_filter( self::ACTION_CHECKUP_RESULT ), array( 'test' => rand() ) );
		}

		return $status >= 100;
	}

	/**
	 * Gets time delay for checkup ping individual requests
	 *
	 * @return int
	 */
	public function get_checkup_ping_delay() {
		return 600;
	}

	/**
	 * Set up cron schedule intervals
	 *
	 * @param array $intervals Intervals known this far.
	 *
	 * @return array
	 */
	public function add_cron_schedule_intervals( $intervals ) {
		if ( ! is_array( $intervals ) ) {
			return $intervals;
		}

		if ( ! isset( $intervals['daily'] ) ) {
			$intervals['daily'] = array(
				'display'  => __( 'SmartCrawl Daily', 'wds' ),
				'interval' => DAY_IN_SECONDS,
			);
		}

		if ( ! isset( $intervals['weekly'] ) ) {
			$intervals['weekly'] = array(
				'display'  => __( 'SmartCrawl Weekly', 'wds' ),
				'interval' => 7 * DAY_IN_SECONDS,
			);
		}

		if ( ! isset( $intervals['monthly'] ) ) {
			$intervals['monthly'] = array(
				'display'  => __( 'SmartCrawl Monthly', 'wds' ),
				'interval' => 30 * DAY_IN_SECONDS,
			);
		}

		return $intervals;
	}

	/**
	 * Clone
	 */
	private function __clone() {
	}
}
