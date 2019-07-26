<?php
$meta_robots_bp_groups = empty( $meta_robots_bp_groups ) ? array() : $meta_robots_bp_groups;
$this->_render( 'onpage/onpage-preview' );

$this->_render( 'onpage/onpage-general-settings', array(
	'title_key'       => 'title-bp_groups',
	'description_key' => 'metadesc-bp_groups',
) );

$this->_render( 'onpage/onpage-og-twitter', array(
	'for_type'            => 'bp_groups',
	'social_label_desc'   => esc_html__( 'Enable or disable support for social platforms when a BuddyPress group is shared on them.', 'wds' ),
	'og_description'      => esc_html__( 'OpenGraph support enhances how your content appears when shared on social networks such as Facebook.', 'wds' ),
	'twitter_description' => esc_html__( 'Twitter Cards support enhances how your content appears when shared on Twitter.', 'wds' ),
) );

$this->_render( 'onpage/onpage-meta-robots', array(
	'items' => $meta_robots_bp_groups,
) );
