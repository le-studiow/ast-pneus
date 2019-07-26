<?php
$active_tab = empty( $active_tab ) ? '' : $active_tab;
$buddypress_active = defined( 'BP_VERSION' );
$tabs = array(
	array(
		'id'   => 'tab_homepage',
		'name' => esc_html__( 'Homepage', 'wds' ),
	),
	array(
		'id'   => 'tab_post_types',
		'name' => esc_html__( 'Post Types', 'wds' ),
	),
	array(
		'id'   => 'tab_taxonomies',
		'name' => esc_html__( 'Taxonomies', 'wds' ),
	),
	array(
		'id'   => 'tab_archives',
		'name' => esc_html__( 'Archives', 'wds' ),
	),
	array(
		'id'   => 'tab_settings',
		'name' => esc_html__( 'Settings', 'wds' ),
	),
);
if ( $buddypress_active ) {
	$tabs[] = array(
		'id'   => 'tab_buddypress',
		'name' => esc_html__( 'BuddyPress', 'wds' ),
	);
}
$this->_render( 'vertical-tabs-side-nav', array(
	'active_tab' => $active_tab,
	'tabs'       => $tabs,
) );
