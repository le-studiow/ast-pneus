<?php

class Smartcrawl_Checkup_Ajax_Service extends Smartcrawl_Checkup_Service_Implementation {

	public function get_known_verbs() {
		return array( 'checkup_start', 'checkup_results' );
	}

	public function is_cacheable_verb( $verb ) {
		return false;
	}

	public function get_request_url( $verb ) {
		return $this->get_service_base_url();
	}

	public function get_service_base_url() {
		$base_url = 'https://premium.wpmudev.org/';

		if ( defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER ) {
			$base_url = trailingslashit( WPMUDEV_CUSTOM_API_SERVER );
		}

		return trailingslashit( $base_url ) . 'wp-admin/admin-ajax.php';
	}

	public function get_request_arguments( $verb ) {
		$request = array(
			'method'    => 'POST',
			'sslverify' => false,
			'body'      => array(
				'action' => $verb,
			),
		);
		if ( 'checkup_start' === $verb ) {
			$domain = apply_filters(
				$this->get_filter( 'domain' ),
				network_site_url()
			);
			$request['body']['checkup-url'] = $domain;
		}

		return $request;
	}

	public function handle_error_response( $response, $verb ) {
	}

	public function get_start_verb() {
		return 'checkup_start';
	}

	public function get_result_verb() {
		return 'checkup_results';
	}

}
