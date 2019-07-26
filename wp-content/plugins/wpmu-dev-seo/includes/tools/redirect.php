<?php

class Smartcrawl_Redirection_Front extends Smartcrawl_Base_Controller {

	private static $_instance;

	/**
	 * @var Smartcrawl_Model_Redirection
	 */
	private $_model;

	public function should_run() {
		return smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_AUTOLINKS );
	}

	protected function init() {
		$this->_model = new Smartcrawl_Model_Redirection();

		add_action( 'wp', array( $this, 'intercept' ) );

		$opts = Smartcrawl_Settings::get_options();
		if ( ! empty( $opts['redirect-attachments'] ) ) {
			add_action( 'template_redirect', array( $this, 'redirect_attachments' ) );
		}
	}

	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Intercept the page and redirect if needs be
	 */
	public function intercept() {
		$source = $this->_model->get_current_url();
		$redirection = $this->_model->get_redirection( $source );
		if ( empty( $redirection ) ) {
			return false;
		}

		// We're here, so redirect
		wp_redirect(
			$this->_to_safe_redirection( $redirection, $source ),
			$this->_get_redirection_status( $source )
		);
		die;
	}

	/**
	 * Converts the redirection to a safe one
	 *
	 * @param string $redirection Raw URL
	 * @param string $source Source URL (optional)
	 *
	 * @return string Safe redirection URL
	 */
	private function _to_safe_redirection( $redirection, $source = false ) {
		$status = $this->_get_redirection_status( $source );
		$fallback = apply_filters( 'wp_safe_redirect_fallback', home_url(), $status );

		add_filter( 'allowed_redirect_hosts', array( $this, 'allow_external_host' ), 10, 2 );

		$redirection = wp_sanitize_redirect( $redirection );
		$redirection = wp_validate_redirect( $redirection, $fallback );

		remove_filter( 'allowed_redirect_hosts', array( $this, 'allow_external_host' ) );

		return $redirection;
	}

	public function allow_external_host( $allowed_hosts, $host_being_checked ) {
		$source = $this->_model->get_current_url();
		$destination = $this->_model->get_redirection( $source );
		$destination_parts = wp_parse_url( $destination );

		if (
			empty( $destination_parts['host'] )
			|| $host_being_checked !== $destination_parts['host']
		) {
			return $allowed_hosts;
		}

		if ( ! is_array( $allowed_hosts ) ) {
			$allowed_hosts = array();
		}

		return array_unique( array_merge(
			$allowed_hosts,
			array( $destination_parts['host'] )
		) );
	}

	/**
	 * Gets redirection status header code
	 *
	 * @param string $source Raw URL (optional)
	 *
	 * @return int
	 */
	private function _get_redirection_status( $source = false ) {
		$status_code = $this->_model->get_default_redirection_status_type();
		if ( ! empty( $source ) ) {
			$item_status = $this->_model->get_redirection_type( $source );
			if ( ! empty( $item_status ) && is_numeric( $item_status ) ) {
				$status_code = (int) $item_status;
			}
		}
		if ( $status_code > 399 || $status_code < 300 ) {
			$status_code = Smartcrawl_Model_Redirection::DEFAULT_STATUS_TYPE;
		}

		return (int) $status_code;
	}

	/**
	 * Redirects attachments to parent post
	 *
	 * If we can't determine parent post type,
	 * we at least throw the noindex header.
	 *
	 * Respects the `redirect-attachment-images_only` sub-option,
	 *
	 * @return void
	 */
	public function redirect_attachments() {
		if ( ! is_attachment() ) {
			return;
		}

		$opts = Smartcrawl_Settings::get_options();
		if ( ! empty( $opts['redirect-attachments-images_only'] ) ) {
			$type = get_post_mime_type();
			if ( ! preg_match( '/^image\//', $type ) ) {
				return;
			}
		}

		$post = get_post();
		$parent_id = is_object( $post ) && ! empty( $post->post_parent )
			? $post->post_parent
			: false;

		if ( ! empty( $parent_id ) ) {
			wp_safe_redirect( get_permalink( $parent_id ), 301 );
			die;
		}

		// No parent post, let's noidex
		header( 'X-Robots-Tag: noindex', true );
	}

	private function __clone() {
	}

}
