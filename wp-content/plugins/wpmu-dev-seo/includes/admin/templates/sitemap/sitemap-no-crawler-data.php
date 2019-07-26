<?php
$action_url = Smartcrawl_Sitemap_Settings::crawl_url();

$this->_render( 'disabled-component-inner', array(
	'content'         => sprintf(
		'%s<br/>%s',
		esc_html__( 'Have SmartCrawl check for broken URLs, 404s, multiple redirections and other harmful', 'wds' ),
		esc_html__( 'issues that can reduce your ability to rank in search engines.', 'wds' )
	),
	'image'           => 'url-crawler-disabled.svg',
	'button_text'     => esc_html__( 'Begin Crawl', 'wds' ),
	'button_url'      => $action_url,
	'upgrade_tag'     => 'smartcrawl_sitemap_crawler_upgrade_button',
	'premium_feature' => true,
) );
