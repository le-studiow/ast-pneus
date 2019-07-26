<?php
$in_progress = empty( $in_progress ) ? false : $in_progress;

$this->_render( 'disabled-component-inner', array(
	'content'         => sprintf(
		'%s<br/>%s',
		esc_html__( 'SmartCrawl will look at your website from the perspective of a search engine (like Google)', 'wds' ),
		esc_html__( 'and then give you a detailed SEO report with recommendations for improvements.', 'wds' )
	),
	'image'           => 'seocheckup-disabled.svg',
	'button_icon'     => 'sui-icon-plus',
	'button_text'     => esc_html__( 'Run Checkup', 'wds' ),
	'button_url'      => Smartcrawl_Checkup_Settings::checkup_url(),
	'button_disabled' => $in_progress,
	'premium_feature' => false,
) );
