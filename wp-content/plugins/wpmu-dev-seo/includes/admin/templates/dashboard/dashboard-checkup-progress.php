<?php
$progress = 0;
?>
<p><?php esc_html_e( 'SmartCrawl is performing a full SEO checkup which will take a few moments. You can close this page if you need to, we’ll let you know when it’s complete.', 'wds' ); ?></p>
<?php
$this->_render( 'progress-bar', array(
	'progress' => $progress,
) );
?>

<?php
if ( ! Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SITE )->is_member() ) {
	$this->_render( 'mascot-message', array(
		'key'         => 'dash-seo-checkup-upsell',
		'dismissible' => false,
		'message'     => sprintf(
			'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_dash_seocheckup_upsell_notice">%s</a>',
			esc_html__( 'Did you know with SmartCrawl Pro you can schedule automated SEO checkups and send whitelabel email reports direct to yours and your clients inboxes?', 'wds' ),
			esc_html__( '- Try it all FREE today', 'wds' )
		),
	) );
}
?>
