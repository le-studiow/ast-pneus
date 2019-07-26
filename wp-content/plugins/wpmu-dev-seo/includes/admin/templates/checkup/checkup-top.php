<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$is_member = $service->is_member();
if ( $service->in_progress() ) {
	return;
}

$results = $service->result();
$counts = smartcrawl_get_array_value( $results, 'counts' );
$score = smartcrawl_get_array_value( $results, 'score' );

if ( null === $counts || null === $score || false === $score ) {
	return;
}

$issue_count = intval( smartcrawl_get_array_value( $counts, 'warning' ) ) + intval( smartcrawl_get_array_value( $counts, 'critical' ) );
$score_class = $issue_count > 0 ? 'sui-icon-info sui-warning' : 'sui-icon-check-tick sui-success';
$opts = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_CHECKUP );
$reporting_enabled = ! empty( $opts['checkup-cron-enable'] );
$cron = Smartcrawl_Controller_Cron::get();
$frequencies = $cron->get_frequencies();
$whitelabel_class = Smartcrawl_White_Label::get()->summary_class();
?>
<div class="sui-box sui-summary <?php echo esc_attr( $whitelabel_class ); ?>"
     data-issue-count="<?php echo esc_attr( $issue_count ); ?>">

	<div class="sui-summary-image-space">
	</div>

	<div class="sui-summary-segment">
		<div class="sui-summary-details">
			<div class="wds-checkup-summary">
				<span class="sui-summary-large"><?php echo esc_html( intval( $score ) ); ?></span>
				<i class="<?php echo esc_attr( $score_class ); ?>"></i>
				<span class="sui-summary-percent">/100</span>
				<span class="sui-summary-sub"><?php esc_html_e( 'Current SEO Score', 'wds' ); ?></span>
			</div>
		</div>
	</div>

	<div class="sui-summary-segment">
		<ul class="sui-list">
			<li>
				<span class="sui-list-label"><?php esc_html_e( 'Last SEO Checkup', 'wds' ); ?></span>
				<span class="sui-list-detail"><?php echo esc_html( $service->get_last_checked( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></span>
			</li>

			<li>
				<span class="sui-list-label"><?php esc_html_e( 'SEO Issues', 'wds' ); ?></span>
				<span class="sui-list-detail">
					<?php if ( $issue_count > 0 ): ?>
						<span class="sui-tag sui-tag-warning"><?php echo esc_html( $issue_count ); ?></span>
					<?php else: ?>
						<i class="sui-icon-check-tick sui-success" aria-hidden="true"></i>
						<small><?php esc_html_e( 'No issues', 'wds' ); ?></small>
					<?php endif; ?>
				</span>
			</li>

			<li>
				<span class="sui-list-label">
					<?php esc_html_e( 'Scheduled Reports', 'wds' ); ?>
					<?php if ( ! $is_member ) : ?>
						<a href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_seocheckup_top_reports_pro_tag"
						   target="_blank">
							<span class="sui-tag sui-tag-pro sui-tooltip sui-tooltip-constrained"
							      style="--tooltip-width: 200px;"
							      data-tooltip="<?php esc_attr_e( 'Upgrade to Pro to schedule automated checkups and email reports', 'wds' ); ?>"> <?php esc_html_e( 'Pro', 'wds' ); ?></span>
						</a>
					<?php endif; ?>
				</span>
				<span class="sui-list-detail">
					<?php if ( $is_member ) : ?>
						<?php if ( $reporting_enabled ) : ?>

							<?php
							$monday = strtotime( 'this Monday' );
							$midnight = strtotime( 'today' );
							$checkup_frequency = smartcrawl_get_array_value( $opts, 'checkup-frequency' );
							$checkup_dow = smartcrawl_get_array_value( $opts, 'checkup-dow' );
							$checkup_tod = smartcrawl_get_array_value( $opts, 'checkup-tod' );
							?>

							<?php
							if ( 'daily' === $checkup_frequency ) {
								printf(
									esc_html__( '%1$s at %2$s' ),
									esc_html( smartcrawl_get_array_value( $frequencies, $checkup_frequency ) ),
									esc_html( date_i18n( get_option( 'time_format' ), $midnight + ( $checkup_tod * HOUR_IN_SECONDS ) ) )
								);
							} else {
								printf(
									esc_html__( '%1$s on %2$ss at %3$s' ),
									esc_html( smartcrawl_get_array_value( $frequencies, $checkup_frequency ) ),
									esc_html( date_i18n( 'l', $monday + ( $checkup_dow * DAY_IN_SECONDS ) ) ),
									esc_html( date_i18n( get_option( 'time_format' ), $midnight + ( $checkup_tod * HOUR_IN_SECONDS ) ) )
								);
							}
							?>

						<?php else : ?>
							<button class="sui-button sui-button-blue wds-enable-reporting">
								<?php esc_html_e( 'Enable', 'wds' ); ?>
							</button>
							<button class="sui-button sui-button-blue wds-disable-reporting"
							        style="display: none;">
								<?php esc_html_e( 'Disable', 'wds' ); ?>
							</button>
						<?php endif; ?>
					<?php else : /* Not a member, this is a pro feature */ ?>
						<span class="sui-tag sui-tag-inactive"><?php esc_html_e( 'Inactive', 'wds' ); ?></span>
					<?php endif; ?>
				</span>
			</li>
		</ul>
	</div>
</div>
