<?php
$inverted = empty( $inverted ) ? false : $inverted;
$field_name = empty( $field_name ) ? '' : $field_name;
$field_id = empty( $field_id ) ? $field_name : $field_id;
$checked = empty( $checked ) ? '' : $checked;
$item_label = empty( $item_label ) ? '' : $item_label;
$item_value = empty( $item_value ) ? '1' : $item_value;
$item_description = empty( $item_description ) ? '' : $item_description;
$html_description = empty( $html_description ) ? '' : $html_description;
$attributes = empty( $attributes ) ? array() : $attributes;

$attr_string = '';
foreach ( $attributes as $attribute => $attribute_value ) {
	$attr_string .= sprintf( '%s="%s" ', esc_attr( $attribute ), esc_attr( $attribute_value ) );
}
?>
<div class="wds-toggle-table">
	<div class="wds-toggle">
		<label class="sui-toggle <?php echo $inverted ? esc_attr( 'wds-inverted-toggle' ) : ''; ?>">
			<input type="checkbox"
			       value='<?php echo esc_attr( $item_value ); ?>'
			       name="<?php echo esc_attr( $field_name ); ?>"
			       id="<?php echo esc_attr( $field_id ); ?>"
				<?php echo esc_html( $checked ); ?>
				<?php echo $attr_string; // phpcs:ignore -- Built escaped. ?>>
			<span class="sui-toggle-slider"></span>
		</label>
	</div>

	<div class="wds-toggle-description">
		<label
				for="<?php echo esc_attr( $field_id ); ?>"
				class="sui-toggle-label">
			<?php echo esc_html( $item_label ); ?>
		</label>

		<?php if ( $item_description ) : ?>
			<span class="sui-description">
				<?php echo esc_html( $item_description ); ?>
			</span>
		<?php endif; ?>

		<?php if ( $html_description ) : ?>
			<?php echo wp_kses_post( $html_description ); ?>
		<?php endif; ?>
	</div>
</div>
