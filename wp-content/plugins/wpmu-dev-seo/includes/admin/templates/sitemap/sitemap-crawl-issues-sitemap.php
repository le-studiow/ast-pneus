<?php
$type = empty( $type ) ? '' : $type;
$report = empty( $report ) ? null : $report;
$open = empty( $open ) ? false : $open;
$ignored_tab_open = empty( $ignored_tab_open ) ? false : $ignored_tab_open;

$this->_render( 'sitemap/sitemap-crawl-issues-group', array(
	'type'              => $type,
	'report'            => $report,
	'open'              => $open,
	'ignored_tab_open'  => $ignored_tab_open,
	'singular_title'    => esc_html__( '%s URL is missing from the sitemap', 'wds' ),
	'plural_title'      => esc_html__( '%s URLs are missing from the sitemap', 'wds' ),
	'description'       => esc_html__( 'SmartCrawl couldn’t find these URLs in your Sitemap. You can choose to add them to your Sitemap, or ignore the warning if you don’t want them included.', 'wds' ),
	'issue_template'    => 'sitemap/sitemap-crawl-issue-sitemap',
	'controls_template' => 'sitemap/sitemap-crawl-issues-sitemap-controls',
	'warning_class'     => 'wds-check-invalid',
) );
