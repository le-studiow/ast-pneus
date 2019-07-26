<?php
$this->_render( 'modal', array(
	'id'              => 'wds-post-exclusion-selector',
	'title'           => esc_html__( 'Add Exclusion', 'wds' ),
	'description'     => esc_html__( 'Choose which post you want to exclude.', 'wds' ),
	'body_template'   => 'advanced-tools/underscore-postlist-selector-body',
	'footer_template' => 'advanced-tools/underscore-postlist-selector-footer',
	'small'           => true,
) );
