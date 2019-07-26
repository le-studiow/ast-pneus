<?php
// Required
$type = empty( $type ) ? '' : $type;
/**
 * @var $report Smartcrawl_SeoReport
 */
$report = empty( $report ) ? null : $report;
// Optional
$description = empty( $description ) ? '' : $description;
$controls_template = empty( $controls_template ) ? 'sitemap/sitemap-crawl-issues-generic-controls' : $controls_template;
$issue_template = empty( $issue_template ) ? 'sitemap/sitemap-crawl-issue-generic' : $issue_template;
$success_class = empty( $success_class ) ? 'sui-success' : $success_class;
$warning_class = empty( $warning_class ) ? 'sui-warning' : $warning_class;
$open = empty( $open ) ? false : $open;
$ignored_tab_open = empty( $ignored_tab_open ) ? false : $ignored_tab_open;
$ignored_tab_open = $open && $ignored_tab_open;

if ( ! $report || ! $type ) {
	return;
}
$all_issues = $report->get_issues_by_type( $type, true );
$ignored_issues = array();
$active_issues = array();

foreach ( $all_issues as $issue_id ) {
	if ( $report->is_ignored_issue( $issue_id ) ) {
		$ignored_issues[] = $issue_id;
	} else {
		$active_issues[] = $issue_id;
	}
}

$issue_count = count( $active_issues );
$class = $issue_count > 0 ? $warning_class : $success_class;
$icon_class = $issue_count > 0 ? 'sui-icon-warning-alert' : 'sui-icon-check-tick';

$singular_title = empty( $singular_title ) ? '' : $singular_title;
$plural_title = empty( $plural_title ) ? '' : $plural_title;
$title = 1 === $issue_count ? $singular_title : $plural_title;
?>
<div class="wds-issues-type-<?php echo esc_attr( $type ); ?> sui-accordion-item <?php echo esc_attr( $class ); ?> <?php echo $open ? 'sui-accordion-item--open' : ''; ?> <?php echo $all_issues ? '' : 'disabled'; ?>"
     data-type="<?php echo esc_attr( $type ); ?>">

	<div class="sui-accordion-item-header">
		<div class="sui-accordion-item-title">
			<i aria-hidden="true" class="<?php echo esc_attr( $class ); ?> <?php echo esc_attr( $icon_class ); ?>"></i>

			<?php printf( esc_html( $title ), $issue_count > 0 ? intval( $issue_count ) : esc_html__( 'No', 'wds' ) ); ?>
		</div>
		<?php if ( $all_issues ): ?>
			<div>
				<span class="sui-accordion-open-indicator">
					<i aria-hidden="true" class="sui-icon-chevron-down"></i>
					<button type="button" class="sui-screen-reader-text"><?php esc_html__( 'Expand' ); ?></button>
				</span>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $all_issues ) : ?>
		<div class="sui-accordion-item-body">
			<div class="sui-box">
				<div class="sui-box-body">
					<small><strong><?php esc_html_e( 'Overview', 'wds' ); ?></strong></small>
					<p>
						<small><?php echo esc_html( $description ); ?></small>
					</p>

					<div class="sui-tabs">
						<div data-tabs>
							<div class="issues <?php echo $ignored_tab_open ? '' : 'active'; ?>">
								<?php esc_html_e( 'Issues', 'wds' ); ?>
							</div>

							<div class="ignored <?php echo $ignored_tab_open ? 'active' : ''; ?>">
								<?php esc_html_e( 'Ignored', 'wds' ); ?>
							</div>
						</div>
						<div data-panes>
							<div class="<?php echo $ignored_tab_open ? '' : 'active'; ?>">
								<table class="wds-crawl-issues-table">
									<tbody>
									<?php if ( $active_issues ) : ?>
										<?php
										foreach ( $active_issues as $active_issue_id ) {
											$this->_render( $issue_template, array(
												'type'     => $type,
												'report'   => $report,
												'issue_id' => $active_issue_id,
											) );
										}

										$this->_render( $controls_template );
										?>
									<?php else: ?>
										<tr class="wds-no-results-row">
											<td colspan="2">
												<small><?php esc_html_e( 'No active issues.' ); ?></small>
											</td>
										</tr>
									<?php endif; ?>
									</tbody>
								</table>
							</div>

							<div class="<?php echo $ignored_tab_open ? 'active' : ''; ?>">
								<table class="wds-ignored-items-table">
									<tbody>
									<?php if ( $ignored_issues && is_array( $ignored_issues ) ) : ?>
										<?php
										foreach ( $ignored_issues as $ignored_issue_id ) {
											$this->_render( 'sitemap/sitemap-crawl-issue-ignored', array(
												'type'     => $type,
												'report'   => $report,
												'issue_id' => $ignored_issue_id,
											) );
										}
										?>
									<?php else: ?>
										<tr class="wds-no-results-row">
											<td colspan="2">
												<small><?php esc_html_e( 'No ignored issues.' ); ?></small>
											</td>
										</tr>
									<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
