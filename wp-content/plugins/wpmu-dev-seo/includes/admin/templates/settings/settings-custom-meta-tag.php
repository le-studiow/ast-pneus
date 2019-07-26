<?php $value = empty( $value ) ? '' : $value; ?>
<div class="wds-custom-meta-tag">
	<input type="text"
	       class="sui-form-control"
	       value="<?php echo esc_attr( $value ); ?>"
	       placeholder="<?php esc_html_e( 'Paste your meta tag here', 'wds' ); ?>"
	       name="<?php echo esc_attr( $_view['option_name'] ); ?>[additional-metas][]"/>
</div>
