<div class="wds-metabox-preview">
	<label class="sui-label"><?php esc_html_e( 'Google Preview' ); ?></label>

	<?php
	if ( apply_filters( 'wds-metabox-visible_parts-preview_area', true ) ) {
		$this->_render( 'onpage/onpage-preview' );
	}
	?>
</div>
