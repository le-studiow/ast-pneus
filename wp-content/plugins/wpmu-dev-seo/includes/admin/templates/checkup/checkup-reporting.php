<?php
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$toggle_field_name = $option_name . '[checkup-cron-enable]';
$is_member = empty( $_view['is_member'] ) ? false : true;
$checkup_cron_enabled = empty( $checkup_cron_enabled ) ? false : true;
$checkup_freq = isset( $_view['options']['checkup-frequency'] ) ? $_view['options']['checkup-frequency'] : false;
$email_recipients = empty( $email_recipients ) ? array() : $email_recipients;
$cron = Smartcrawl_Controller_Cron::get();
$frequencies = $cron->get_frequencies();
?>

<div class="wds-upsell-tab-description">
	<div>
		<p><?php esc_html_e( 'Set up SmartCrawl to automatically run a comprehensive SEO Checkup daily, weekly or monthly and receive an email report to as many recipients as you like.', 'wds' ); ?></p>
	</div>

	<?php if ( $checkup_cron_enabled && ! empty( $email_recipients ) ): ?>
		<?php $this->_render( 'notice', array(
			'message' => sprintf(
				_n(
					'Automatic checkups are enabled and sending %1$s to %2$d recipient.',
					'Automatic checkups are enabled and sending %1$s to %2$d recipients.',
					count( $email_recipients ),
					'wds'
				),
				smartcrawl_get_array_value( $frequencies, $checkup_freq ),
				count( $email_recipients )
			),
			'class'   => 'sui-notice-info',
		) ); ?>
	<?php endif; ?>
</div>
<div class="sui-box-settings-row <?php echo $is_member ? '' : 'sui-disabled'; ?>">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"
		       for="<?php echo esc_attr( $toggle_field_name ); ?>">

			<?php esc_html_e( 'Schedule automatic checkups', 'wds' ); ?>
		</label>
		<span class="sui-description">
			<?php esc_html_e( 'Enable automated SEO reports for this website.', 'wds' ); ?>
		</span>
	</div>
	<div class="sui-box-settings-col-2 wds-toggleable <?php echo $checkup_cron_enabled ? '' : 'inactive'; ?>">
		<?php
		$this->_render( 'toggle-item', array(
			'field_name' => $toggle_field_name,
			'field_id'   => $toggle_field_name,
			'checked'    => checked( $checkup_cron_enabled, true, false ),
			'item_label' => esc_html__( 'Enable regular checkups', 'wds' ),
		) );
		?>
		<div class="wds-toggleable-inside sui-border-frame sui-toggle-content">
			<small><strong><?php esc_html_e( 'Recipients', 'wds' ); ?></strong></small>

			<div class="wds-recipients-notice <?php echo empty( $email_recipients ) ? '' : 'hidden'; ?>">
				<?php $this->_render( 'notice', array(
					'message' => esc_html__( "You've removed all recipients. If you save without a recipient, we'll automatically turn off reports.", 'wsd' ),
				) ); ?>
			</div>

			<?php
			$this->_render( 'email-recipients', array(
				'id'               => 'wds-seo-checkup-email-recipients',
				'email_recipients' => $email_recipients,
				'field_name'       => $option_name . '[checkup-email-recipients]',
			) );
			?>

			<p></p>
			<small><strong><?php esc_html_e( 'Schedule', 'wds' ); ?></strong></small>
			<?php $this->_render( 'checkup/checkup-reporting-schedule', array(
				'checkup_freq' => $checkup_freq,
			) ); ?>
		</div>
	</div>
</div>

<?php if ( ! $is_member ): ?>
	<?php $this->_render( 'mascot-message', array(
		'key'         => 'seo-checkup-upsell',
		'dismissible' => false,
		'message'     => sprintf(
			'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_seocheckup_reporting_upsell_notice">%s</a>',
			esc_html__( 'Grab the Pro version of SmartCrawl to unlock unlimited SEO Checkups plus automated scheduled reports to always stay on top of any issues. These features are included in a WPMU DEV membership along with 100+ plugins, 24/7 support and lots of handy site management tools.', 'wds' ),
			esc_html__( '- Try it all FREE today', 'wds' )
		),
	) ); ?>
<?php endif; ?>
