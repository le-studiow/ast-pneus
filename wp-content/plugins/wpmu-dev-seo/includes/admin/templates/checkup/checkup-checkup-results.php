<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$results = $service->result();
$counts = smartcrawl_get_array_value( $results, 'counts' );
$issue_count = intval( smartcrawl_get_array_value( $counts, 'warning' ) ) + intval( smartcrawl_get_array_value( $counts, 'critical' ) );
$has_errors = smartcrawl_get_array_value( $results, 'error' );
$has_items = smartcrawl_get_array_value( $results, 'items' );
?>

<?php if ( $has_items && ! $has_errors ): ?>
	<p><?php esc_html_e( 'Here are your outstanding SEO issues. We recommend actioning as many as possible to ensure your site is as search engine and social media friendly as possible.', 'wds' ); ?></p>

	<?php
	if ( $issue_count > 0 ) {
		$this->_render( 'notice', array(
			'message' => sprintf(
				_n( 'You have %d SEO recommendation.', 'You have %d SEO recommendations.', $issue_count, 'wds' ),
				$issue_count
			),
		) );
	} else {
		$this->_render( 'notice', array(
			'message' => esc_html__( "You don't have any SEO checkup recommendations – Google is loving it.", 'wds' ),
			'class'   => 'sui-notice-success',
		) );
	}
	?>
<?php endif; ?>

<?php $this->_render( 'checkup/checkup-results-inner', array( 'results' => $results ) ); ?>

<?php if ( $has_items && ! $has_errors ): ?>
	<p class="wds-centre">
		<small><?php esc_html_e( 'Remember, these are recommendations only to help Google index your content effectively. SEO requires constant tweaking and improvement alongside good quality content on your website.', 'wds' ); ?></small>
	</p>
<?php endif; ?>
