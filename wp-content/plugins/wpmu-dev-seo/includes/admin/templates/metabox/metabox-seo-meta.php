<?php $post = empty( $post ) ? null : $post; ?>
<div class="wds-metabox-section">
	<?php
	$this->_render( 'metabox/metabox-dummy-preview' );
	?>

	<?php $this->_render( 'metabox/metabox-meta-edit-form', array(
		'post' => $post,
	) ); ?>
</div>
