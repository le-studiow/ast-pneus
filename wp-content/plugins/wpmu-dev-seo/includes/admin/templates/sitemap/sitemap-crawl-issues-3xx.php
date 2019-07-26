<?php
$type = empty( $type ) ? '' : $type;
$report = empty( $report ) ? null : $report;
$open = empty( $open ) ? false : $open;
$ignored_tab_open = empty( $ignored_tab_open ) ? false : $ignored_tab_open;

$this->_render( 'sitemap/sitemap-crawl-issues-group', array(
	'type'             => $type,
	'report'           => $report,
	'open'             => $open,
	'ignored_tab_open' => $ignored_tab_open,
	'singular_title'   => esc_html__( '%s URL has multiple redirections', 'wds' ),
	'plural_title'     => esc_html__( '%s URLs have multiple redirections', 'wds' ),
	'description'      => esc_html__( 'Some of your URLs have multiple redirections. In the options menu you can List occurrences to see where these links can be found, and also set up and 301 redirects to a newer version of these pages.', 'wds' ),
) );
