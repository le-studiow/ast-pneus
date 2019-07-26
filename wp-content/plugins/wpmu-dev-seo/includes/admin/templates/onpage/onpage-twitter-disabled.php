<?php
$message = esc_html__( 'Twitter Cards are globally disabled.', 'wds' );
if ( smartcrawl_subsite_setting_page_enabled( 'wds_social' ) ) {
	$social_page = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_SOCIAL );
	$message = sprintf(
		esc_html__( '%1$s You can enable them %2$s.', 'wds' ),
		$message,
		sprintf(
			'<a href="%s">%s</a>',
			esc_url_raw( add_query_arg( 'tab', 'tab_twitter_cards', $social_page ) ),
			esc_html__( 'here', 'wds' )
		)
	);
}

$this->_render( 'notice', array(
	'class'   => 'sui-notice-info',
	'message' => $message,
) );
