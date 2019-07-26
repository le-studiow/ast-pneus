<?php
$advanced_sections = empty( $advanced_sections ) ? array() : $advanced_sections;
if ( empty( $advanced_sections ) ) {
	return;
}
?>

<div class="wds-metabox-section wds-advanced-metabox-section sui-box-body">
	<p><?php esc_html_e( 'Configure the advanced settings for this post.', 'wds' ); ?></p>

	<?php foreach ( $advanced_sections as $template => $args ) {
		$this->_render( $template, $args );
	} ?>
</div>
