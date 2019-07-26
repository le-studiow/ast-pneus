<?php
$post = empty( $post ) ? null : $post;

if ( ! $post ) {
	return false;
}
?>
<div class="wds-readability-analysis-container">
	<?php do_action( 'wds-editor-metabox-readability-analysis', $post ); ?>
</div>
