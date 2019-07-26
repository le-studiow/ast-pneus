<?php
$sitemap_available = smartcrawl_subsite_setting_page_enabled( 'wds_sitemap' );
$sitemap_crawler_available = is_main_site();
if ( ! $sitemap_available ) {
	return;
}

$page_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_SITEMAP );
$options = $_view['options'];
$sitemap_enabled = Smartcrawl_Settings::get_setting( 'sitemap' );
$option_name = Smartcrawl_Settings::TAB_SETTINGS . '_options';
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$is_member = $service->is_member();
?>
<section id="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_SITEMAP ); ?>"
         class="sui-box wds-dashboard-widget"
         data-dependent="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_TOP_STATS ); ?>">

	<div class="sui-box-header">
		<h3 class="sui-box-title">
			<i class="sui-icon-web-globe-world"></i> <?php esc_html_e( 'Sitemap', 'wds' ); ?>
		</h3>
		<?php
		if ( $sitemap_enabled && $is_member && $sitemap_crawler_available ) {
			$this->_render( 'url-crawl-master', array(
				'progress_template' => 'dashboard/dashboard-box-title-url-crawl-in-progress',
				'ready_template'    => 'dashboard/dashboard-box-title-url-crawl-stats',
			) );
		}
		?>
	</div>
	<div class="sui-box-body">
		<p><?php esc_html_e( 'Automatically generate detailed sitemaps to tell search engines what content you want them to crawl and index.', 'wds' ); ?></p>

		<div class="wds-separator-top wds-draw-left-padded">
			<small><strong><?php esc_html_e( 'XML Sitemap', 'wds' ); ?></strong></small>
			<?php if ( $sitemap_enabled ) : ?>

				<?php
				$this->_render( 'notice', array(
					'class'   => 'sui-notice-info',
					'message' => sprintf(
						__( 'Your sitemap is available at %s', 'wds' ),
						sprintf( '<a target="_blank" href="%s">/sitemap.xml</a>', esc_attr( smartcrawl_get_sitemap_url() ) )
					),
				) );
				?>

			<?php else : ?>
				<p>
					<small><?php esc_html_e( 'Enables an XML page that search engines will use to crawl and index your website pages.', 'wds' ); ?></small>
				</p>

				<?php
				$this->_render( 'dismissable-notice', array(
					'key'     => 'dashboard-sitemap-disabled-warning',
					'message' => __( 'Your sitemap is currently disabled. We highly recommend you enable this feature if you don’t already have a sitemap.', 'wds' ),
					'class'   => 'sui-notice-warning',
				) );
				?>
				<button type="button"
				        data-option-id="<?php echo esc_attr( $option_name ); ?>"
				        data-flag="<?php echo 'sitemap'; ?>"
				        class="wds-activate-component sui-button sui-button-blue wds-disabled-during-request">

					<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $sitemap_crawler_available ): ?>
			<div class="wds-separator-top cf <?php echo $is_member ? 'wds-draw-left-padded' : 'wds-box-blocked-area wds-draw-down wds-draw-left'; ?>">
				<small><strong><?php esc_html_e( 'URL Crawler', 'wds' ); ?></strong></small>
				<?php if ( $is_member ) : ?>
					<?php if ( $sitemap_enabled ) : ?>
						<?php
						$this->_render( 'url-crawl-master', array(
							'ready_template'    => 'dashboard/dashboard-url-crawl-stats',
							'progress_template' => 'dashboard/dashboard-url-crawl-in-progress',
							'no_data_template'  => 'dashboard/dashboard-url-crawl-no-data-small',
						) );
						?>
					<?php else : ?>
						<p>
							<small><?php esc_html_e( 'Automatically schedule SmartCrawl to run check for URLs that are missing from your Sitemap.', 'wds' ); ?></small>
						</p>
						<div><span class="sui-tag sui-tag-inactive">
							<?php esc_html_e( 'Sitemaps must be activated', 'wds' ); ?>
						</span></div>
					<?php endif; ?>
				<?php else : ?>
					<a href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_dash_crawl_pro_tag"
					   target="_blank">
						<span class="sui-tag sui-tag-pro sui-tooltip"
						      data-tooltip="<?php esc_attr_e( 'Get SmartCrawl Pro today Free', 'wds' ); ?>">
							<?php esc_html_e( 'Pro', 'wds' ); ?>
						</span>
					</a>
					<p>
						<small><?php esc_html_e( 'Automatically schedule SmartCrawl to run check for URLs that are missing from your Sitemap.', 'wds' ); ?></small>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="sui-box-footer">
		<a href="<?php echo esc_attr( $page_url ); ?>"
		   class="sui-button sui-button-ghost">

			<i class="sui-icon-wrench-tool"
			   aria-hidden="true"></i> <?php esc_html_e( 'Configure', 'wds' ); ?>
		</a>
	</div>
</section>
