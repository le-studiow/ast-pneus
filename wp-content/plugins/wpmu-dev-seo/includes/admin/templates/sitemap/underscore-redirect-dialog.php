<?php
$this->_render( 'modal', array(
	'id'              => 'wds-issue-redirect',
	'title'           => esc_html__( 'Redirect URL' ),
	'small'           => true,
	'body_template'   => 'sitemap/underscore-redirect-dialog-body',
	'footer_template' => 'sitemap/underscore-redirect-dialog-footer',
) );
