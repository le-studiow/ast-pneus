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

<tr data-issue-id="<?php echo esc_attr( $issue_id ); ?>" data-path="<?php echo esc_url( $url ); ?>">
	<td>
		<i aria-hidden="true" class="sui-icon-warning-alert"></i>
		<small>
			<strong><?php echo esc_html( $path ); ?></strong>
		</small>
	</td>
	<td>
		<?php
		$this->_render( 'links-dropdown', array(
			'label' => esc_html__( 'Options', 'wds' ),
			'links' => array(
				'#add-to-sitemap' => '<i class="sui-icon-plus" aria-hidden="true"></i> ' . esc_html__( 'Add to Sitemap', 'wds' ),
				'#ignore'         => '<i class="sui-icon-eye-hide" aria-hidden="true"></i> ' . esc_html__( 'Ignore', 'wds' ),
			),
		) );
		?>
	</td>
</tr>
