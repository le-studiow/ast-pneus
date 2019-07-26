<?php
$this->_render( 'modal', array(
	'id'                 => 'wds-edit-redirect-form',
	'title'              => esc_html__( 'Edit Redirect', 'wds' ),
	'description'        => sprintf(
		esc_html__( 'Allowed formats include relative URLs like %1$s or absolute URLs such as %2$s.', 'wds' ),
		sprintf( '<strong>%s</strong>', esc_html__( '/cats', 'wds' ) ),
		sprintf( '<strong>%s</strong>', esc_html__( 'https://website.com/cats', 'wds' ) )
	),
	'body_template'      => 'advanced-tools/underscore-add-redirect-form-body',
	'body_template_args' => array(
		'source'             => '{{- source }}',
		'destination'        => '{{- destination }}',
		'index'              => '{{- index }}',
		'temporary_selected' => "{{- selected_type == 302 ? 'selected' : '' }}",
		'permanent_selected' => "{{- selected_type == 301 ? 'selected' : '' }}",
	),
	'footer_template'    => 'advanced-tools/underscore-add-redirect-form-footer',
	'small'              => true,
) );
