<?php

class Smartcrawl_Core_Request {

	/**
	 * Gets post from front-end, via HTTP API
	 *
	 * @param int $post_id ID of the post to fetch content.
	 *
	 * @return string|WP_Error
	 */
	public function get_rendered_post( $post_id ) {
		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return new WP_Error( __CLASS__, 'Unknown post ID' );
		}

		$post_parent = wp_is_post_revision( $post_id );
		$is_post_revision = ! empty( $post_parent );
		$permalink = $is_post_revision
			? get_permalink( $post_parent )
			: get_permalink( $post_id );
		if ( empty( $permalink ) ) {
			return new WP_Error( __CLASS__, 'Error figuring out post permalink' );
		}

		$url = add_query_arg( 'preview', 'true', $permalink );
		if ( $is_post_revision ) {
			$url = add_query_arg( 'preview_id', $post_id, $url );
		}
		$url = add_query_arg( 'preview_nonce', wp_create_nonce( 'post_preview_' . $post_id ), $url );
		$url = add_query_arg( 'wds-frontend-check', md5( microtime() ), $url );

		$post_status = get_post_status( $post_id );
		if ( 'auto-draft' === $post_status ) {
			return '';
		}

		$params = array();

		// Let's copy over the current cookies to apply to the request.
		$cookies = array();
		$source = ! empty( $_COOKIE )
			? $_COOKIE
			: array();
		foreach ( $source as $cname => $cvalue ) {
			if ( ! preg_match( '/^(wp-|wordpress_)/', $cname ) ) {
				continue;
			} // Only WP cookies, pl0x.
			$cookies[] = new WP_Http_Cookie( array(
				'name'  => $cname,
				'value' => $cvalue,
			) );
		}

		// Post password cookie.
		$post = $is_post_revision
			? get_post( $post_parent )
			: get_post( $post_id );
		if ( ! empty( $post->post_password ) ) {
			if ( ! class_exists( 'PasswordHash' ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
			}
			$hasher = new PasswordHash( 8, true );
			$cookies[] = new WP_Http_Cookie( array(
				'name'  => 'wp-postpass_' . COOKIEHASH,
				'value' => $hasher->HashPassword( $post->post_password ),
			) );
		}

		if ( ! empty( $cookies ) ) {
			$params['cookies'] = $cookies;
		}
		$params['timeout'] = $this->get_timeout();

		$response = wp_remote_get( $url, $params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( __CLASS__, 'Non-200 response' );
		}

		$content = wp_remote_retrieve_body( $response );

		$bits = Smartcrawl_Html::find( '.wds-frontend-content-check', $content );

		return apply_filters(
			'wds-analysis-content',
			(string) trim( join( "\n", $bits ) ),
			$post_id
		);
	}

	private function get_timeout() {
		return defined( 'SMARTCRAWL_ANALYSIS_REQUEST_TIMEOUT' )
			? SMARTCRAWL_ANALYSIS_REQUEST_TIMEOUT
			: 5;
	}
}
