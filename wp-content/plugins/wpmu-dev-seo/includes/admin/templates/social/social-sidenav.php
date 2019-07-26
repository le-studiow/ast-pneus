<?php
$active_tab = empty( $active_tab ) ? '' : $active_tab;

$this->_render( 'vertical-tabs-side-nav', array(
	'active_tab' => $active_tab,
	'tabs'       => array(
		array(
			'id'   => 'tab_accounts',
			'name' => esc_html__( 'Accounts', 'wds' ),
		),
		array(
			'id'   => 'tab_open_graph',
			'name' => esc_html__( 'OpenGraph', 'wds' ),
		),
		array(
			'id'   => 'tab_twitter_cards',
			'name' => esc_html__( 'Twitter Cards', 'wds' ),
		),
		array(
			'id'   => 'tab_pinterest_verification',
			'name' => esc_html__( 'Pinterest Verification', 'wds' ),
		),
	),
) );
