<?php
$archive_post_type = empty( $archive_post_type ) ? '' : $archive_post_type;
$archive_post_type_robots = empty( $archive_post_type_robots ) ? '' : $archive_post_type_robots;

$this->_render( 'onpage/onpage-preview' );

$this->_render( 'onpage/onpage-general-settings', array(
	'title_key'       => 'title-' . $archive_post_type,
	'description_key' => 'metadesc-' . $archive_post_type,
) );

$this->_render( 'onpage/onpage-og-twitter', array(
	'for_type' => $archive_post_type,
) );

$this->_render( 'onpage/onpage-meta-robots', array(
	'items' => $archive_post_type_robots,
) );
