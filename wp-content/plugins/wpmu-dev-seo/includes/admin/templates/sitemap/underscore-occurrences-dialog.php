<?php
$this->_render( 'modal', array(
	'id'            => 'wds-issue-occurrences',
	'title'         => esc_html__( 'Broken URL Locations' ),
	'small'         => true,
	'body_template' => 'sitemap/underscore-occurrences-dialog-body',
) );
