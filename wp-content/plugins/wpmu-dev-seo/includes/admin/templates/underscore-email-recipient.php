<?php
$this->_render( 'email-recipient', array(
	'index'           => '{{- index }}',
	'email_recipient' => array(
		'name'  => '{{- name }}',
		'email' => '{{- email }}',
	),
	'field_name'      => '{{- field_name }}',
) );
