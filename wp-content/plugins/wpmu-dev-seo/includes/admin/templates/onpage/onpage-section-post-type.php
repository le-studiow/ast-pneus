<?php
$post_type = empty( $post_type ) ? '' : $post_type;
$post_type_object = empty( $post_type_object ) ? '' : $post_type_object;
$post_type_robots = empty( $post_type_robots ) ? array() : $post_type_robots;
$singular_name = empty( $post_type_object->labels->singular_name )
	? 'post' : strtolower( $post_type_object->labels->singular_name );

$og_description = esc_html__( 'OpenGraph support enhances how your content appears when shared on social networks such as Facebook. You can set default values here but also customize this per %s via the post editor.', 'wds' );
$og_description = sprintf( $og_description, $singular_name );

$twitter_description = esc_html__( 'Twitter Cards support enhances how your content appears when shared on Twitter. You can set default values here but also customize this per %s via the post editor.', 'wds' );
$twitter_description = sprintf( $twitter_description, $singular_name );

$this->_render( 'onpage/onpage-preview' );

$this->_render( 'onpage/onpage-general-settings', array(
	'title_key'       => 'title-' . $post_type,
	'description_key' => 'metadesc-' . $post_type,
) );

$this->_render( 'onpage/onpage-og-twitter', array(
	'for_type'            => $post_type,
	'social_label_desc'   => esc_html__( 'Enable or disable support for social platforms when this post type is shared on them.', 'wds' ),
	'og_description'      => $og_description,
	'twitter_description' => $twitter_description,
) );

$this->_render( 'onpage/onpage-meta-robots', array(
	'items' => $post_type_robots,
) );
