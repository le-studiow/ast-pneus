<?php

class Smartcrawl_Controller_Hub { // phpcs:ignore -- We have two versions of this class


	private static $_instance;

	private $_is_running = false;

	private function __construct() {
	}

	/**
	 * Boot controller listeners
	 *
	 * Do it only once, if they're already up do nothing
	 *
	 * @return bool Status
	 */
	public static function serve() {
		$me = self::get();
		if ( $me->is_running() ) {
			return false;
		}

		$me->_add_hooks();

		return true;
	}

	/**
	 * Obtain instance without booting up
	 *
	 * @return Smartcrawl_Controller_Hub instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Check if we already have the actions bound
	 *
	 * @return bool Status
	 */
	public function is_running() {
		return $this->_is_running;
	}

	/**
	 * Bind listening actions
	 */
	private function _add_hooks() {
		add_filter( 'wdp_register_hub_action', array( $this, 'register_hub_actions' ) );

		$this->_is_running = true;
	}

	/**
	 * Registers Hub action listeners
	 *
	 * @param array $actions All the Hub actions registered this far
	 *
	 * @return array Augmented actions
	 */
	public function register_hub_actions( $actions ) {
		if ( ! is_array( $actions ) ) {
			return $actions;
		}

		$actions['wds-sync-ignores'] = array( $this, 'json_sync_ignores_list' );
		$actions['wds-purge-ignores'] = array( $this, 'json_purge_ignores_list' );

		$actions['wds-sync-extras'] = array( $this, 'json_sync_extras_list' );
		$actions['wds-purge-extras'] = array( $this, 'json_purge_extras_list' );

		$actions['wds-audit-data'] = array(
			$this,
			'json_receive_audit_data'
		);

		return $actions;
	}

	public function obj_to_array( $data ) {
		return json_decode(
			json_encode( $data ),
			true
		);
	}

	/**
	 * Receives the SEO Audit data pushes from the Hub
	 *
	 * Updates the crawl state.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 */
	public function json_receive_audit_data( $params = array(), $action = '' ) {
		$status = true;

		$service = Smartcrawl_Service::get(
			Smartcrawl_Service::SERVICE_SEO
		);
		$data = $this->obj_to_array( $params );
		$service->set_result( $data );
		$service->set_progress_flag( empty( $data['end'] ) );
		$service->set_last_run_timestamp();
		Smartcrawl_Logger::debug('Received sitemap crawl data from remote');

		return ! empty( $status )
			? wp_send_json_success()
			: wp_send_json_error();
	}

	/**
	 * Fresh ignores from the Hub action handler
	 *
	 * Updates local ignores list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 */
	public function json_sync_ignores_list( $params = array(), $action = '' ) {
		Smartcrawl_Logger::info( 'Received ignores syncing request' );
		$status = $this->sync_ignores_list( $params, $action );

		return ! empty( $status )
			? wp_send_json_success()
			: wp_send_json_error();
	}

	/**
	 * Fresh ignores from the Hub action handler
	 *
	 * Updates local ignores list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 *
	 * @return bool Status
	 */
	public function sync_ignores_list( $params = array(), $action = '' ) {
		$ignores = new Smartcrawl_Model_Ignores();

		$data = stripslashes_deep( (array) $params );
		if ( empty( $data['issue_ids'] ) || ! is_array( $data['issue_ids'] ) ) {
			return false;
		}

		$status = true;
		foreach ( $data['issue_ids'] as $issue_id ) {
			$tmp = $ignores->set_ignore( $issue_id );
			if ( ! $tmp ) {
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * Purge ignores from the Hub action handler
	 *
	 * Purges local ignores list when the Hub storage is purged.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 */
	public function json_purge_ignores_list( $params = array(), $action = '' ) {
		Smartcrawl_Logger::info( 'Received ignores purging request' );
		$status = $this->purge_ignores_list( $params, $action );

		return ! empty( $status )
			? wp_send_json_success()
			: wp_send_json_error();
	}

	/**
	 * Purge ignores from the Hub action handler
	 *
	 * Purges local ignores list when the Hub storage is purged.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 *
	 * @return bool Status
	 */
	public function purge_ignores_list( $params = array(), $action = '' ) {
		$ignores = new Smartcrawl_Model_Ignores();

		return $ignores->clear();
	}

	/**
	 * Fresh extras from the Hub action handler
	 *
	 * Updates local extra URLs list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 */
	public function json_sync_extras_list( $params = array(), $action = '' ) {
		Smartcrawl_Logger::info( 'Received extras syncing request' );
		$status = $this->sync_extras_list( $params, $action );

		return ! empty( $status )
			? wp_send_json_success()
			: wp_send_json_error();
	}

	/**
	 * Fresh extras from the Hub action handler
	 *
	 * Updates local extra URLs list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 *
	 * @return bool Status
	 */
	public function sync_extras_list( $params = array(), $action = '' ) {
		$data = stripslashes_deep( (array) $params );
		if ( empty( $data['urls'] ) || ! is_array( $data['urls'] ) ) {
			return false;
		}

		$existing = Smartcrawl_Xml_Sitemap::get_extra_urls();
		foreach ( $data['urls'] as $url ) {
			$existing[] = esc_url( $url );
		}

		return Smartcrawl_Xml_Sitemap::set_extra_urls( $existing );
	}

	/**
	 * Purge extras from the Hub action handler
	 *
	 * Purges local extra URLs list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 */
	public function json_purge_extras_list( $params = array(), $action = '' ) {
		$status = $this->purge_extras_list( $params, $action );

		return ! empty( $status )
			? wp_send_json_success()
			: wp_send_json_error();
	}

	/**
	 * Purge extras from the Hub action handler
	 *
	 * Purges local extra URLs list when the Hub storage is updated.
	 *
	 * @param object $params Hub-provided parameters
	 * @param string $action Action called
	 *
	 * @return bool Status
	 */
	public function purge_extras_list( $params = array(), $action = '' ) {
		return Smartcrawl_Xml_Sitemap::set_extra_urls( array() );
	}


}
