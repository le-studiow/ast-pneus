<?php
$section_description = empty( $section_description ) ? '' : $section_description;
$section_template = empty( $section_template ) ? '' : $section_template;
$section_args = empty( $section_args ) ? array() : $section_args;
$section_type = empty( $section_type ) ? '' : $section_type;
?>

<div data-type="<?php echo esc_attr( $section_type ); ?>">
	<?php if ( $section_description ) : ?>
		<p><?php echo esc_html( $section_description ); ?></p>
	<?php endif; ?>

	<?php $this->_render( $section_template, $section_args ); ?>
</div>
