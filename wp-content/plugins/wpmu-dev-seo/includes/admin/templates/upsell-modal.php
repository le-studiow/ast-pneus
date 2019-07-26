<?php
$this->_render( 'modal', array(
	'id'            => 'wds-upsell-modal',
	'title'         => esc_html__( 'Upgrade to Pro', 'wds' ),
	'description'   => esc_html__( "Here's what you’ll get by upgrading to SmartCrawl Pro", 'wds' ),
	'body_template' => 'upsell-modal-body',
) );
