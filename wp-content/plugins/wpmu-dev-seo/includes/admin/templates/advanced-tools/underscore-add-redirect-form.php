<?php
$this->_render( 'modal', array(
	'id'              => 'wds-add-redirect-form',
	'title'           => esc_html__( 'Add Redirect', 'wds' ),
	'description'     => sprintf(
		esc_html__( 'Allowed formats include relative URLs like %1$s or absolute URLs such as %2$s.', 'wds' ),
		sprintf( '<strong>%s</strong>', esc_html__( '/cats', 'wds' ) ),
		sprintf( '<strong>%s</strong>', esc_html__( 'https://website.com/cats', 'wds' ) )
	),
	'body_template'   => 'advanced-tools/underscore-add-redirect-form-body',
	'footer_template' => 'advanced-tools/underscore-add-redirect-form-footer',
	'small'           => true,
) );
