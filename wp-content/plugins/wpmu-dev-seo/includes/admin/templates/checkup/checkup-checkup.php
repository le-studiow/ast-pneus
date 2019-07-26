<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
?>

<div class="wds-report">
	<?php $this->_render( 'checkup/checkup-checkup-results' ); ?>
	<?php if ( ! $service->is_member() ) { ?>
		<?php
		$this->_render( 'mascot-message', array(
			'key'         => 'seo-checkup-upsell',
			'dismissible' => false,
			'message'     => sprintf(
				'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_seocheckup_checkup_upsell_notice">%s</a>',
				esc_html__( 'Grab the Pro version of SmartCrawl to unlock unlimited SEO Checkups plus automated scheduled reports to always stay on top of any issues. These features are included in a WPMU DEV membership along with 100+ plugins, 24/7 support and lots of handy site management tools.', 'wds' ),
				esc_html__( '- Try it all FREE today', 'wds' )
			),
		) );
		?>
	<?php } ?>
</div>
