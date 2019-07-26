<?php
$this->_render( 'modal', array(
	'id'              => 'wds-custom-keywords',
	'title'           => esc_html__( 'Add Custom Keywords', 'wds' ),
	'description'     => esc_html__( 'Choose your keywords, and then specify the URL to auto-link to.', 'wds' ),
	'body_template'   => 'advanced-tools/underscore-keywords-form-body',
	'footer_template' => 'advanced-tools/underscore-keywords-form-footer',
) );
