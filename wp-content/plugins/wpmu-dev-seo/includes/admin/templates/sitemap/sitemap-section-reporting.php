<?php
$cron = Smartcrawl_Controller_Cron::get();
$option_name = $_view['option_name'];

// This does the actual rescheduling
$cron->set_up_schedule();
$is_member = empty( $_view['is_member'] ) ? false : true;
$crawler_cron_enabled = ! empty( $_view['options']['crawler-cron-enable'] ) && $is_member;
$toggle_field_name = $option_name . '[crawler-cron-enable]';
$dash_profile_data = smartcrawl_get_dash_profile_data();
$crawler_freq = empty( $_view['options']['crawler-frequency'] ) ? false : $_view['options']['crawler-frequency'];
$frequencies = $cron->get_frequencies();
?>

<div class="wds-upsell-tab-description">
	<div>
		<p><?php esc_html_e( 'Set up SmartCrawl to automatically crawl your URLs daily, weekly or monthly and send an email report to your inbox.', 'wds' ); ?></p>
	</div>

	<?php if ( $crawler_cron_enabled && $dash_profile_data && $crawler_freq ): ?>
		<?php $this->_render( 'notice', array(
			'message' => sprintf(
				'Automatic crawls are enabled and sending %s to 1 recipient.',
				smartcrawl_get_array_value( $frequencies, $crawler_freq )
			),
			'class'   => 'sui-notice-info',
		) ); ?>
	<?php endif; ?>
</div>
<div class="sui-box-settings-row <?php echo $is_member ? '' : 'sui-disabled'; ?>">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"
		       for="<?php echo esc_attr( $toggle_field_name ); ?>">

			<?php esc_html_e( 'Schedule automatic crawls', 'wds' ); ?>
		</label>

		<span class="sui-description">
			<?php esc_html_e( 'Enable automated sitemap crawl reports for this website.', 'wds' ); ?>
		</span>
	</div>
	<div class="sui-box-settings-col-2 wds-toggleable <?php echo $crawler_cron_enabled ? '' : 'inactive'; ?>">
		<?php
		$this->_render( 'toggle-item', array(
			'field_name' => $toggle_field_name,
			'field_id'   => $toggle_field_name,
			'checked'    => checked( $crawler_cron_enabled, true, false ),
			'item_label' => esc_html__( 'Run regular URL crawls', 'wds' ),
		) );
		?>
		<div class="wds-toggleable-inside sui-border-frame sui-toggle-content">
			<?php if ( $dash_profile_data ): ?>
				<small><strong><?php esc_html_e( 'Recipient', 'wds' ); ?></strong></small>
				<?php $this->_render( 'email-recipients', array(
					'id'               => 'wds-sitemap-email-recipients',
					'disable_addition' => true,
					'email_recipients' => array(
						array(
							'name'  => $dash_profile_data->user_login,
							'email' => $dash_profile_data->user_email,
						),
					),
				) ); ?>
				<p></p>
			<?php endif; ?>

			<small><strong><?php esc_html_e( 'Schedule', 'wds' ); ?></strong></small>

			<?php $this->_render( 'sitemap/sitemap-reporting-schedule' ); ?>
		</div>
	</div>

</div>

<?php if ( ! $is_member ): ?>
	<?php $this->_render( 'mascot-message', array(
		'key'         => 'seo-checkup-upsell',
		'dismissible' => false,
		'message'     => sprintf(
			'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_sitemap_reporting_upsell_notice">%s</a>',
			esc_html__( 'Unlock automated crawls of your URLs to always stay on top of any issues with SmartCrawl Pro. Get Sitemap Reports as part of a WPMU DEV membership along with other pro plugins and services, 24/7 support and much more', 'wds' ),
			esc_html__( '- Try it all FREE today', 'wds' )
		),
	) ); ?>
<?php endif; ?>
