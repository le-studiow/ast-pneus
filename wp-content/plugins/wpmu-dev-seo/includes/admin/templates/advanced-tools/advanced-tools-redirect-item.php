<?php
// All values passed to this template are expected to be escaped already so phpcs is disabled
// phpcs:ignoreFile
$source = empty( $source ) ? '' : $source;
$destination = empty( $destination ) ? '' : $destination;
$selected_type = empty( $selected_type ) ? '' : $selected_type;
$index = empty( $index ) ? 0 : $index;

$option_name = 'wds_autolinks_options';
?>

<div data-index="<?php echo esc_attr( $index ); ?>"
     class="sui-builder-field wds-redirect-item">

	<label class="sui-checkbox">
		<input type="checkbox"
		       name="<?php echo esc_attr( $option_name ); ?>[bulk][]"
		       value="<?php echo esc_attr( $index ); ?>"/>
		<span aria-hidden="true"></span>
	</label>

	<div class="sui-builder-field-label">
		<span><?php echo esc_html( $source ); ?></span>
	</div>

	<small><?php echo esc_html( $destination ); ?></small>
	<span class="wds-redirect-type-label wds-redirect-type-label-<?php echo esc_attr( $selected_type ); ?>">
        <small><?php esc_html_e( 'Permanent', 'wds' ); ?></small>
        <small><?php esc_html_e( 'Temporary', 'wds' ); ?></small>
    </span>

	<?php $this->_render( 'links-dropdown', array(
		'label' => esc_html__( 'Options', 'wds' ),
		'links' => array(
			'#edit'   => '<i class="sui-icon-pencil" aria-hidden="true"></i> ' . esc_html__( 'Edit', 'wds' ),
			'#remove' => '<i class="sui-icon-trash" aria-hidden="true"></i> ' . esc_html__( 'Remove', 'wds' ),
		),
	) ); ?>

	<input value="<?php echo esc_attr( $source ); ?>"
	       type="hidden"
	       class="wds-source-url"
	       name="<?php echo esc_attr( $option_name ); ?>[urls][<?php echo esc_attr( $index ); ?>][source]"/>

	<input value="<?php echo esc_attr( $destination ); ?>"
	       type="hidden"
	       class="wds-destination-url"
	       name="<?php echo esc_attr( $option_name ); ?>[urls][<?php echo esc_attr( $index ); ?>][destination]"/>

	<input value="<?php echo esc_attr( $selected_type ); ?>"
	       type="hidden"
	       class="wds-redirect-type"
	       name="<?php echo esc_attr( $option_name ); ?>[urls][<?php echo esc_attr( $index ); ?>][type]"/>
</div>
