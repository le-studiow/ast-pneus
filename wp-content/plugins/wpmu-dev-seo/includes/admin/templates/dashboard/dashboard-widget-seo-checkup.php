<?php
$checkup_available = is_main_site() && smartcrawl_subsite_setting_page_enabled( 'wds_checkup' );
if ( ! $checkup_available ) {
	return;
}

$page_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_CHECKUP );
$checkup_url = Smartcrawl_Settings_Dashboard::checkup_url();
/**
 * @var $service Smartcrawl_Checkup_Service
 */
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$options = $_view['options'];
$reporting_enabled = smartcrawl_get_array_value( $options, 'checkup-cron-enable' );
$last_checked = (boolean) $service->get_last_checked_timestamp();
$in_progress = $service->in_progress();
$option_name = Smartcrawl_Settings::TAB_SETTINGS . '_options';
$checkup_enabled = smartcrawl_get_array_value( $options, 'checkup' );
$checkup_text = esc_html__( 'Get a comprehensive report on how optimized your website is for search engines and social media. We recommend running this checkup first to see what needs improving.', 'wds' );
$results = ! $in_progress && $last_checked ? $service->result() : array();
$counts = smartcrawl_get_array_value( $results, 'counts' );
$issue_count = intval( smartcrawl_get_array_value( $counts, 'warning' ) ) + intval( smartcrawl_get_array_value( $counts, 'critical' ) );
$checkup_issues_tooltip = _n(
	'You have %d outstanding SEO issue to fix up',
	'You have %d outstanding SEO issues to fix up',
	$issue_count,
	'wds'
);
$checkup_issues_tooltip = sprintf( $checkup_issues_tooltip, $issue_count );
?>
<section id="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_SEO_CHECKUP ); ?>"
         data-dependent="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_TOP_STATS ); ?>"
         class="sui-box wds-dashboard-widget">
	<div class="sui-box-header">
		<h3 class="sui-box-title">
			<i class="sui-icon-smart-crawl" aria-hidden="true"></i><?php esc_html_e( 'SEO Checkup', 'wds' ); ?>
		</h3>
		<?php if ( $checkup_enabled ): ?>
			<?php if ( $issue_count > 0 && $checkup_enabled ) : ?>
				<div class="sui-actions-left">
					<span class="sui-tag sui-tag-warning sui-tooltip"
					      data-tooltip="<?php echo esc_attr( $checkup_issues_tooltip ); ?>">
						<?php echo intval( $issue_count ); ?>
					</span>
				</div>
			<?php elseif ( $in_progress ): ?>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			<?php endif; ?>

			<?php if ( $results ): ?>
				<div class="sui-actions-right">
					<a href="<?php echo esc_attr( $checkup_url ); ?>"
					   class="sui-button sui-button-blue">
						<i class="sui-icon-plus" aria-hidden="true"></i>

						<?php esc_html_e( 'Run checkup', 'wds' ); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="sui-box-body">
		<?php
		if (
			! $checkup_enabled
			|| ( ! $last_checked && ! $in_progress )
		) {
			printf( '<p>%s</p>', esc_html( $checkup_text ) );
		} elseif ( $in_progress ) {
			$this->_render( 'dashboard/dashboard-checkup-progress' );
		} else {
			$this->_render( 'dashboard/dashboard-mini-checkup-report', array(
				'results'           => $results,
				'issue_count'       => $issue_count,
				'reporting_enabled' => $reporting_enabled,
			) );
		}
		?>
	</div>
	<?php if ( ! $checkup_enabled ) : ?>
		<div class="sui-box-footer">
			<button type="button"
			        data-option-id="<?php echo esc_attr( $option_name ); ?>"
			        data-flag="<?php echo esc_attr( 'checkup' ); ?>"
			        class="wds-activate-component sui-button sui-button-blue wds-disabled-during-request">

				<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			</button>
		</div>
	<?php elseif ( ! $last_checked && ! $in_progress ): ?>
		<div class="sui-box-footer">
			<a href="<?php echo esc_attr( $checkup_url ); ?>"
			   class="sui-button sui-button-blue">
				<i class="sui-icon-plus" aria-hidden="true"></i>

				<?php esc_html_e( 'Run checkup', 'wds' ); ?>
			</a>

			<span>
				<small>
					<?php echo empty( $reporting_enabled )
						? esc_html__( 'Automatic checkups are disabled', 'wds' )
						: esc_html__( 'Automatic checkups are enabled', 'wds' ); ?>
				</small>
			</span>
		</div>
	<?php endif; ?>
</section>
