<?php

class Smartcrawl_Controller_Checkup_Progress extends Smartcrawl_Base_Controller {
	const FAKE_PROGRESS_OPTION = 'wds-fake-checkup-progress';
	const CHECKUP_PROGRESS_OPTION = 'wds-checkup-progress-locked';

	/**
	 * Singleton instance
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * Singleton instance getter
	 *
	 * @return self instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Child controllers can use this method to initialize.
	 *
	 * @return mixed
	 */
	protected function init() {
		add_action( 'wp_ajax_wds-checkup-status', array( $this, 'ajax_checkup_status' ) );
		add_filter( 'wds-model-service-request-checkup_start', array( $this, 'clear' ) );
		add_filter( 'wds-model-service-request-start', array( $this, 'clear' ) );

		return true;
	}

	/**
	 * Checks checkup service status and sends back percentage.
	 */
	public function ajax_checkup_status() {
		$fake_progress = $this->get_fake_progress();
		$progress = $fake_progress < 99
			? $fake_progress + 1
			: 99;

		// Some other thread is taking care of the calculation
		if ( $this->is_progress_calculation_locked() ) {
			return $this->send_checkup_progress_response( $progress );
		}

		// Try to acquire the lock
		$lock_acquired = $this->acquire_progress_calculation_lock();
		if ( ! $lock_acquired ) {
			// no dice, someone else beat us to it
			return $this->send_checkup_progress_response( $progress );
		}

		$this->set_fake_progress( $fake_progress + 1 );

		// Check real progress every tenth time
		if ( $fake_progress && $fake_progress % 20 === 0 ) {
			$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
			$is_complete = $service->status() >= 100;

			if ( $is_complete ) {
				$progress = 100;
			}
		}

		$this->release_progress_calculation_lock();

		return $this->send_checkup_progress_response( $progress );
	}

	private function send_checkup_progress_response( $percentage ) {
		wp_send_json_success( array(
			'percentage' => $percentage,
		) );
		return true;
	}

	private function get_fake_progress() {
		return (int) Smartcrawl_Settings::get_specific_options( self::FAKE_PROGRESS_OPTION );
	}

	private function set_fake_progress( $new ) {
		Smartcrawl_Settings::update_specific_options( self::FAKE_PROGRESS_OPTION, $new );
	}

	private function clear_fake_progress() {
		Smartcrawl_Settings::delete_specific_options( self::FAKE_PROGRESS_OPTION );
	}

	private function is_progress_calculation_locked() {
		return (boolean) Smartcrawl_Settings::get_specific_options( self::CHECKUP_PROGRESS_OPTION );
	}

	private function acquire_progress_calculation_lock() {
		return (boolean) Smartcrawl_Settings::update_specific_options( self::CHECKUP_PROGRESS_OPTION, 1 );
	}

	private function release_progress_calculation_lock() {
		return (boolean) Smartcrawl_Settings::delete_specific_options( self::CHECKUP_PROGRESS_OPTION );
	}

	public function clear( $response = array() ) {
		$this->clear_fake_progress();
		$this->release_progress_calculation_lock();

		return $response;
	}
}
