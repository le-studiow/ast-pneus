<?php
$options = empty( $options ) ? $_view['options'] : $options;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$item = empty( $item ) ? '' : $item;
$value = isset( $options[ $item ] ) ? $options[ $item ] : '';
?>
<div class="wds-media-url" data-name="<?php echo esc_attr( $item ); ?>">
	<input class="wds-media-url-field sui-form-control" id="<?php echo esc_attr( $item ); ?>" type="text"
	       name="<?php echo esc_attr( $option_name ); ?>[<?php echo esc_attr( $item ); ?>]"
	       value="<?php echo esc_attr( $value ); ?>"/>
	<p>
		<button type="button" class="wds-media-url-button sui-button sui-button-ghost">
			<?php esc_html_e( 'Select', 'wds' ); ?>
		</button>
	</p>
</div>
