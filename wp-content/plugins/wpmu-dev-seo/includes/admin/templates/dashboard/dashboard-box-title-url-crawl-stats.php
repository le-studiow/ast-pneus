<?php
$report = empty( $report ) ? null : $report;

if ( is_null( $report ) ) {
	return;
}

$active_issues = intval( $report->get_issues_count() );
$sitemap_issues_tooltip = _n(
	'You have %s sitemap issue',
	'You have %s sitemap issues',
	$active_issues,
	'wds'
);
?>

<?php if ( $active_issues > 0 ) : ?>
	<div class="sui-actions-left">
		<span class="sui-tag sui-tag-warning sui-tooltip"
		      data-tooltip="<?php echo esc_attr( sprintf( $sitemap_issues_tooltip, $active_issues ) ); ?>">
	
			<?php echo intval( $active_issues ); ?>
		</span>
	</div>
<?php endif; ?>
