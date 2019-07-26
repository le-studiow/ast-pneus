<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$counts = smartcrawl_get_array_value( $service->result(), 'counts' );
$issue_count = intval( smartcrawl_get_array_value( $counts, 'warning' ) ) + intval( smartcrawl_get_array_value( $counts, 'critical' ) );
$active_tab = empty( $active_tab ) ? '' : $active_tab;
$is_member = empty( $_view['is_member'] ) ? false : true;
$checkup_cron_enabled = ! empty( $_view['options']['checkup-cron-enable'] ) && $is_member;
$email_recipients = empty( $email_recipients ) ? array() : $email_recipients;
?>

<div class="wds-seo-checkup-stats-container">
	<?php $this->_render( 'checkup/checkup-top' ); ?>
</div>

<form action='<?php echo esc_attr( $_view['action_url'] ); ?>' method='post' class="wds-form">
	<?php $this->settings_fields( $_view['option_name'] ); ?>

	<input type="hidden"
	       name='<?php echo esc_attr( $_view['option_name'] ); ?>[<?php echo esc_attr( $_view['slug'] ); ?>-setup]'
	       value="1">

	<div class="wds-vertical-tabs-container sui-row-with-sidenav">

		<?php
		$this->_render( 'vertical-tabs-side-nav', array(
			'active_tab' => $active_tab,
			'tabs'       => array(
				array(
					'id'        => 'tab_checkup',
					'name'      => esc_html__( 'Checkup', 'wds' ),
					'tag_value' => $issue_count > 0 ? $issue_count : false,
					'tag_class' => 'sui-tag-warning',
				),
				array(
					'id'   => 'tab_settings',
					'name' => esc_html__( 'Reporting', 'wds' ),
					'tick' => $checkup_cron_enabled,
				),
			),
		) );
		?>

		<?php
		$this->_render( 'vertical-tab', array(
			'tab_id'       => 'tab_checkup',
			'tab_name'     => esc_html__( 'Checkup', 'wds' ),
			'button_text'  => false,
			'is_active'    => 'tab_checkup' === $active_tab,
			'tab_sections' => array(
				array(
					'section_template' => 'checkup/checkup-checkup',
				),
			),
		) );
		?>
		<?php
		$this->_render(
			'vertical-tab-upsell',
			array(
				'tab_id'             => 'tab_settings',
				'tab_name'           => esc_html__( 'Reporting', 'wds' ),
				'is_active'          => 'tab_settings' === $active_tab,
				'button_text'        => $is_member ? esc_html__( 'Save Settings', 'wds' ) : '',
				'title_actions_left' => 'checkup/checkup-reporting-title-pro-tag',
				'tab_sections'       => array(
					array(
						'section_template' => 'checkup/checkup-reporting',
						'section_args'     => array(
							'checkup_cron_enabled' => $checkup_cron_enabled,
							'email_recipients'     => $email_recipients,
						),
					),
				),
			)
		);
		?>

	</div>
</form>
