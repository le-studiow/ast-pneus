<?php
$ready_template = empty( $ready_template ) ? '' : $ready_template;
$ready_args = empty( $ready_args ) ? array() : $ready_args;

$no_data_template = empty( $no_data_template ) ? '' : $no_data_template;
$no_data_args = empty( $no_data_args ) ? array() : $no_data_args;

$progress_template = empty( $progress_template ) ? '' : $progress_template;
$progress_args = empty( $progress_args ) ? array() : $progress_args;

/**
 * @var Smartcrawl_Seo_Service $service
 */
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SEO );
$crawl_report = $service->get_report();

if ( $crawl_report->has_data() ) {
	if ( $ready_template ) {
		$this->_render( $ready_template, array_merge(
			array( 'report' => $crawl_report ),
			$ready_args
		) );
	}
} elseif ( $crawl_report->is_in_progress() ) {
	if ( $progress_template ) {
		$this->_render( $progress_template, array_merge(
			array( 'progress' => $crawl_report->get_progress() ),
			$progress_args
		) );
	}
} else {
	if ( $no_data_template ) {
		$this->_render( $no_data_template, $no_data_args );
	}
}
