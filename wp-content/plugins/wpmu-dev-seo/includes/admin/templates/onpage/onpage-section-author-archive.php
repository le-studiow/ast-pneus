<?php
$meta_robots_author = empty( $meta_robots_author ) ? '' : $meta_robots_author;

$this->_render( 'onpage/onpage-preview' );

$this->_render( 'onpage/onpage-general-settings', array(
	'title_key'       => 'title-author',
	'description_key' => 'metadesc-author',
) );

$this->_render( 'onpage/onpage-og-twitter', array(
	'for_type' => 'author',
) );

$this->_render( 'onpage/onpage-meta-robots', array(
	'items' => $meta_robots_author,
) );
