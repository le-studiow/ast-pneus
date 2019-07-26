<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$last_checked_timestamp = $service->get_last_checked_timestamp();
$active_tab = empty( $active_tab ) ? '' : $active_tab;
$this->_render( 'before-page-container' );
$email_recipients = empty( $email_recipients ) ? array() : $email_recipients;
$in_progress = $service->in_progress();
?>

<div id="container"
     class="sui-wrap wrap wrap-wds wds-page wds-checkup-settings">

	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'SEO Checkup', 'wds' ),
		'documentation_chapter' => 'seo-checkup',
		'extra_actions'         => 'checkup/checkup-header-actions',
		'extra_actions_args'    => array(
			'in_progress' => $in_progress,
		),
	) ); ?>

	<?php if ( empty( $last_checked_timestamp ) || $in_progress ): ?>
		<?php if ( $in_progress ): ?>
			<?php $this->_render( 'checkup/checkup-progress-modal' ); ?>
		<?php endif; ?>

		<?php $this->_render( 'checkup/checkup-get-started', array(
			'in_progress' => $in_progress,
		) ); ?>
	<?php else: ?>
		<?php $this->_render( 'checkup/checkup-settings-inner', array(
			'active_tab'       => $active_tab,
			'email_recipients' => $email_recipients,
		) ); ?>
	<?php endif; ?>

	<?php $this->_render( 'footer' ); ?>
	<?php $this->_render( 'upsell-modal' ); ?>
</div>
