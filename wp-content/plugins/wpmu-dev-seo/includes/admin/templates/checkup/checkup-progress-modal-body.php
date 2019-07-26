<?php
$is_member = empty( $_view['is_member'] ) ? false : true;

$this->_render( 'progress-bar', array(
	'progress' => 0,
) );

if ( ! $is_member ) {
	$this->_render( 'mascot-message', array(
		'dismissible' => false,
		'image_name'  => 'graphic-seocheckup-modal',
		'message'     => sprintf(
			'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_seocheckup_modal_upsell_notice">%s</a>',
			esc_html__( 'Did you know with SmartCrawl Pro you can schedule automated SEO checkups and send whitelabel email reports direct to yours and your clients inboxes?', 'wds' ),
			esc_html__( '- Try SmartCrawl Pro FREE today!', 'wds' )
		),
	) );
}
