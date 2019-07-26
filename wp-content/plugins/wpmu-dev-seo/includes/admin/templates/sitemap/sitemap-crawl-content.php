<?php
/**
 * @var Smartcrawl_SeoReport $crawl_report
 */
$crawl_report = empty( $crawl_report ) ? null : $crawl_report;
$open_type = empty( $open_type ) ? null : $open_type;
$ignored_tab_open = empty( $ignored_tab_open ) ? false : $ignored_tab_open;
if ( ! $crawl_report ) {
	return;
}

if ( $crawl_report->has_data() ) {

	$this->_render( 'sitemap/sitemap-crawl-results', array(
		'report'           => $crawl_report,
		'open_type'        => $open_type,
		'ignored_tab_open' => $ignored_tab_open,
	) );

} elseif ( $crawl_report->is_in_progress() ) {

	// The URL crawl was started and is in progress at the moment.
	$this->_render( 'sitemap/sitemap-progress-bar', array(
		'progress' => $crawl_report->get_progress(),
	) );

} else {

	// The URL crawl was never started so there is nothing to do.
	$this->_render( 'sitemap/sitemap-no-crawler-data' );

}
