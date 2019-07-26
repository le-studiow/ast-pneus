<?php
/**
 * @var Smartcrawl_SeoReport $crawl_report
 */
$crawl_report = empty( $_view['crawl_report'] ) ? null : $_view['crawl_report'];
$this->_render( 'sitemap/sitemap-crawl-content', array(
	'crawl_report' => $crawl_report,
) );
