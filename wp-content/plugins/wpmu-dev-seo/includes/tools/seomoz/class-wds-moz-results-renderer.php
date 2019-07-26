<?php

class Smartcrawl_Moz_Results_Renderer extends Smartcrawl_Renderable {

	public function render( $target_url, $view ) {
		$access_id = Smartcrawl_Settings::get_setting( 'access-id' );
		$secret_key = Smartcrawl_Settings::get_setting( 'secret-key' );

		if ( empty( $access_id ) || empty( $secret_key ) ) {
			return;
		}

		$target_url = preg_replace( '!http(s)?:\/\/!', '', $target_url );
		$api = new Smartcrawl_Moz_API( $access_id, $secret_key );
		$urlmetrics = $api->urlmetrics( $target_url );

		$attribution = str_replace( '/', '%252F', untrailingslashit( $target_url ) );
		$attribution = "https://moz.com/researchtools/ose/links?site={$attribution}";

		if ( is_object( $urlmetrics ) && $api->is_response_valid( $urlmetrics ) ) {
			$this->_render( $view, array(
				'attribution' => $attribution,
				'urlmetrics'  => $urlmetrics,
			) );
		} else {
			$error = $this->get_specific_error( $urlmetrics );
			$message = sprintf(
				"%s %s",
				esc_html__( 'We were unable to retrieve data from the Moz API.', 'wds' ),
				$error
			);

			$this->_render( 'notice', array(
				'class'   => 'sui-notice-error',
				'message' => $message,
			) );
		}
	}

	private function get_specific_error( $response ) {
		switch ( Smartcrawl_Moz_API::get_error_type( $response ) ) {
			case 400:
				return esc_html__( "If you've recently created an account, allow 24 hours for your first data to arrive. If you are an existing user, please reset your Moz API credentials to fix this issue.", 'wds' );

			default:
				return isset( $response->error_message ) ? $response->error_message : '';
		}
	}

	protected function _get_view_defaults() {
		return array();
	}
}
