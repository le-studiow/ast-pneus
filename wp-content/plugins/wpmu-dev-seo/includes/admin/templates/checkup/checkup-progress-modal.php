<?php $this->_render( 'modal', array(
	'id'                      => 'wds-checkup-progress-modal',
	'title'                   => esc_html__( 'Checkup in progress', 'wds' ),
	'description'             => esc_html__( "We're performing a full SEO checkup of your website, please be patient …", 'wds' ),
	'header_actions_template' => 'checkup/checkup-progress-modal-header',
	'body_template'           => 'checkup/checkup-progress-modal-body',
) );
