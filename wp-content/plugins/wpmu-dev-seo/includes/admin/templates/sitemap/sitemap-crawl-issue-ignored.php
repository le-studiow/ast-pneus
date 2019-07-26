<?php
$type = empty( $type ) ? '' : $type;
$report = empty( $report ) ? null : $report;
$issue_id = empty( $issue_id ) ? null : $issue_id;

if ( ! $report || ! $type || ! $issue_id ) {
	return;
}

$issue = $report->get_issue( $issue_id );
$url = ! empty( $issue['path'] ) ? $issue['path'] : '';
$path = preg_replace( '/' . preg_quote( home_url(), '/' ) . '/', '', $url );
$path = empty( $path ) ? $url : $path;
?>

<tr data-issue-id="<?php echo esc_attr( $issue_id ); ?>">
	<td>
		<small>
			<strong><?php echo esc_html( $path ); ?></strong>
		</small>
	</td>
	<td>
		<button class="wds-unignore wds-disabled-during-request sui-button sui-button-ghost">
			<span class="sui-loading-text">
				<i class="sui-icon-plus"
				   aria-hidden="true"></i> <?php esc_html_e( 'Restore', 'wds' ); ?>
			</span>

			<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
		</button>
	</td>
</tr>
