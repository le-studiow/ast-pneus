<?php
$active_tab = empty( $active_tab ) ? '' : $active_tab;

$this->_render( 'vertical-tabs-side-nav', array(
	'active_tab' => $active_tab,
	'tabs'       => array(
		array(
			'id'   => 'tab_automatic_linking',
			'name' => esc_html__( 'Automatic Links', 'wds' ),
		),
		array(
			'id'   => 'tab_url_redirection',
			'name' => esc_html__( 'URL Redirection', 'wds' ),
		),
		array(
			'id'   => 'tab_moz',
			'name' => esc_html__( 'Moz', 'wds' ),
		),
	),
) );
