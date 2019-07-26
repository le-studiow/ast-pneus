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

$rmodel = new Smartcrawl_Model_Redirection();
$redirections = $rmodel->get_all_redirections();
$redirection_target = ! empty( $redirections[ $path ] ) ? esc_url( $redirections[ $path ] ) : '';
?>

<tr data-issue-id="<?php echo esc_attr( $issue_id ); ?>"
    data-path="<?php echo esc_attr( $path ); ?>"
    data-redirect-path="<?php echo esc_attr( $redirection_target ); ?>">

	<td>
		<i aria-hidden="true" class="sui-warning sui-icon-warning-alert"></i>
		<small>
			<strong><?php echo esc_html( $path ); ?></strong>
		</small>
	</td>
	<td>
		<span class="sui-tag sui-tag-warning">
			<?php echo count( $issue['origin'] ); ?>
		</span>
	</td>
	<td>
		<?php
		$this->_render( 'links-dropdown', array(
			'label' => esc_html__( 'Options', 'wds' ),
			'links' => array(
				'#occurrences' => '<i class="sui-icon-list-bullet" aria-hidden="true"></i> ' . esc_html__( 'List Occurrences', 'wds' ),
				'#redirect'    => '<i class="sui-icon-arrow-right" aria-hidden="true"></i> ' . esc_html__( 'Redirect', 'wds' ),
				'#ignore'      => '<i class="sui-icon-eye-hide" aria-hidden="true"></i> ' . esc_html__( 'Ignore', 'wds' ),
			),
		) );
		?>
	</td>
</tr>
