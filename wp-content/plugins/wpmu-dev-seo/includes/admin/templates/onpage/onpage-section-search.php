<?php
$meta_robots_search = empty( $meta_robots_search ) ? array() : $meta_robots_search;

$this->_render( 'onpage/onpage-preview' );

$this->_render( 'onpage/onpage-general-settings', array(
	'title_key'       => 'title-search',
	'description_key' => 'metadesc-search',
) );

$this->_render( 'onpage/onpage-og-twitter', array(
	'for_type' => 'search',
) );

$this->_render( 'onpage/onpage-meta-robots', array(
	'items' => $meta_robots_search,
) );
