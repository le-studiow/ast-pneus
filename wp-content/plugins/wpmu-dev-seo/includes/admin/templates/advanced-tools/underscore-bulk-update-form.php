<?php
$this->_render( 'modal', array(
	'id'              => 'wds-bulk-update-redirects',
	'title'           => esc_html__( 'Bulk Update', 'wds' ),
	'description'     => esc_html__( 'Choose which bulk update actions you wish to apply. This will override the existing values for the selected items.', 'wds' ),
	'body_template'   => 'advanced-tools/underscore-bulk-update-form-body',
	'footer_template' => 'advanced-tools/underscore-bulk-update-form-footer',
	'small'           => true,
) );
