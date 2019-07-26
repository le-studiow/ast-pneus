<?php
$is_member = empty( $_view['is_member'] ) ? false : true;
if ( ! $is_member ) {
	return;
}
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SEO );
/**
 * @var Smartcrawl_SeoReport $crawl_report
 */
$crawl_report = empty( $_view['crawl_report'] ) ? null : $_view['crawl_report'];
if ( ! $crawl_report ) {
	return;
}
$crawl_url = Smartcrawl_Sitemap_Settings::crawl_url();
$sitemap_enabled = Smartcrawl_Settings::get_setting( 'sitemap' );
if ( ! $sitemap_enabled ) {
	return;
}
$end = $service->get_last_run_timestamp();
$end = ! empty( $end ) && is_numeric( $end )
	? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $end )
	: __( 'Never', 'wds' );
?>

<span>
	<?php printf(
		esc_html__( 'Last crawl: %s', 'wds' ),
		esc_html( $end )
	); ?>
</span>

<a href="<?php echo esc_attr( $crawl_url ); ?>" class="sui-button sui-button-blue wds-new-crawl-button"
   style="<?php echo $crawl_report->is_in_progress() ? 'display:none;' : ''; ?>">
	<?php esc_html_e( 'New crawl', 'wds' ); ?>
</a>
