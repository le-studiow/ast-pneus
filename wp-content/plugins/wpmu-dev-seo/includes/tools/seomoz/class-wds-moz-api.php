<?php

class Smartcrawl_Moz_API {

	private $access_id = false;
	private $secret_key = false;

	public function __construct( $access_id, $secret_key ) {
		$this->access_id = $access_id;
		$this->secret_key = $secret_key;
	}

	/**
	 * mozRank - Returns the Moz 'mozRank' for the given URL.
	 *
	 * @param string $target_url
	 * @param bool $raw
	 *
	 * @returns mixed if $raw == true: returns "raw rank" ( float in exponential notation )
	 *                   if $raw == false: returns "pretty rank" ( float between 0 and 10 inclusive )
	 */
	public function urlmetrics( $target_url ) {
		$transient_key = $this->transient_key( $target_url );
		smartcrawl_kill_stuck_transient( $transient_key );
		$response = get_transient( $transient_key );
		if ( empty( $response ) ) {
			$response = $this->query( 'url-metrics', $target_url );
			set_transient( $transient_key, $response, SMARTCRAWL_EXPIRE_TRANSIENT_TIMEOUT ); // Pre-defined expiration
		}

		return $response;
	}

	private function transient_key( $target_url ) {
		return sprintf(
			"seomoz_urlmetrics_%s",
			md5( sprintf( '%s-%s-%s', $target_url, $this->access_id, $this->secret_key ) )
		);
	}

	/**
	 * query - Queries the SEOMoz API
	 *
	 * @param string $apiName
	 * @param string $target_url
	 *
	 * @returns mixed URL contents on success, false on failure
	 */
	private function query( $api_call, $argument ) {
		$timestamp = time() + 600; // 10 minutes into the future
		$argument = urlencode( $argument ); // phpcs:ignore -- The api call may fail with rawurlencode
		$request_url = "http://lsapi.seomoz.com/linkscape/{$api_call}/{$argument}?Cols=103079266340&AccessID={$this->access_id}&Expires={$timestamp}&Signature=" . $this->generate_signature( $timestamp );
		$response = wp_remote_get( $request_url );

		return ! is_wp_error( $response ) ? json_decode( wp_remote_retrieve_body( $response ) ) : false;
	}

	/**
	 * generate_signature - Builds the signature var needed to authenticate
	 *
	 * @param int $timestamp
	 *
	 * @returns string URL encoded Signature key/value pair
	 */
	private function generate_signature( $timestamp ) {
		$timestamp = isset( $timestamp ) ? $timestamp : time() + 300; // one minute into the future
		$hash = hash_hmac( 'sha1', $this->access_id . "\n" . $timestamp, $this->secret_key, true );

		return urlencode( base64_encode( $hash ) ); // phpcs:ignore -- The api call may fail with rawurlencode and base64_encode is required
	}

	public function is_response_valid( $response ) {
		return isset( $response->uu );
	}

	public static function get_error_type( $response ) {
		$response = (array) $response;
		$status = (int) smartcrawl_get_array_value( $response, 'status' );

		if ( $status >= 400 && $status <= 499 ) {
			return 400;
		}

		if ( $status >= 500 && $status <= 599 ) {
			return 500;
		}

		return 0;
	}
}
