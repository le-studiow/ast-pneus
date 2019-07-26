<?php
$report = empty( $report ) ? null : $report;

if ( is_null( $report ) ) {
	return;
}

$active_issues = $report->get_issues_count();
$missing_urls = $report->get_issues_count( 'sitemap' );
$active_issues_tooltip = _n( 'You have %s sitemap issue', 'You have %s sitemap issues', $active_issues, 'wds' );
$active_issues_tooltip = sprintf( $active_issues_tooltip, $active_issues );

$active_issues_tag = _n( '%d issue', '%d issues', $active_issues, 'wds' );
$active_issues_tag = sprintf( $active_issues_tag, $active_issues );

$missing_urls_tag = _n( '%d missing URL', '%d missing URLs', $missing_urls, 'wds' );
$missing_urls_tag = sprintf( $missing_urls_tag, $missing_urls );
?>

<?php if ( $active_issues > 0 ) : ?>
	<div class="wds-right">
		<span class="sui-tag sui-tag-warning sui-tooltip"
		      data-tooltip="<?php echo esc_attr( $active_issues_tooltip ); ?>">

			<?php echo esc_html( $active_issues_tag ); ?>
		</span>
		<span class="sui-tag sui-tag-inactive"><?php echo esc_html( $missing_urls_tag ); ?></span>
	</div>
<?php else: ?>
	<div class="wds-right">
		<small><?php esc_html_e( 'No issues', 'wds' ); ?></small>
	</div>
<?php endif; ?>
