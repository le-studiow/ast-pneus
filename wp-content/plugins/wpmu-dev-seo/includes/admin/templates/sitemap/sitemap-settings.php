<?php
$crawl_url = Smartcrawl_Sitemap_Settings::crawl_url();
$is_member = empty( $_view['is_member'] ) ? false : true;
$active_tab = empty( $active_tab ) ? '' : $active_tab;
$crawl_report = empty( $_view['crawl_report'] ) ? null : $_view['crawl_report'];
$smartcrawl_buddypress = empty( $smartcrawl_buddypress ) ? array() : $smartcrawl_buddypress;
$sitemaps_enabled = Smartcrawl_Settings::get_setting( 'sitemap' );
$sitemap_crawler_available = is_main_site();
?>

<?php $this->_render( 'before-page-container' ); ?>
<div id="container" class="sui-wrap wrap wrap-wds wds-page wds-sitemap-settings">

	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'Sitemap', 'wds' ),
		'documentation_chapter' => 'sitemap',
		'extra_actions'         => $sitemap_crawler_available ? 'sitemap/sitemap-extra-actions' : '',
	) ); ?>

	<?php
	if ( $sitemaps_enabled ) {

		?>
		<form action='<?php echo esc_attr( $_view['action_url'] ); ?>' method='post' class="wds-form">
			<?php $this->settings_fields( $_view['option_name'] ); ?>

			<input type="hidden"
			       name='<?php echo esc_attr( $_view['option_name'] ); ?>[<?php echo esc_attr( $_view['slug'] ); ?>-setup]'
			       value="1">

			<?php if ( $sitemap_crawler_available ): ?>
				<div id="wds-crawl-summary-container">
					<?php $this->_render( 'sitemap/sitemap-crawl-stats', array(
						'crawl_report' => $crawl_report,
					) ); ?>
				</div>
			<?php endif; ?>

			<div class="wds-vertical-tabs-container sui-row-with-sidenav" id="sitemap-settings-tabs">

				<?php $this->_render( 'sitemap/sitemap-side-nav', array(
					'active_tab'                => $active_tab,
					'sitemap_crawler_available' => $sitemap_crawler_available,
				) ); ?>

				<?php
				$this->_render( 'vertical-tab', array(
					'tab_id'       => 'tab_sitemap',
					'tab_name'     => esc_html__( 'Sitemap', 'wds' ),
					'is_active'    => 'tab_sitemap' === $active_tab,
					'tab_sections' => array(
						array(
							'section_description' => esc_html__( 'Automatically generate a sitemap and regularly send updates to Google.', 'wds' ),
							'section_template'    => 'sitemap/sitemap-section-settings',
							'section_args'        => array(
								'post_types'            => $post_types,
								'taxonomies'            => $taxonomies,
								'smartcrawl_buddypress' => $smartcrawl_buddypress,
								'extra_urls'            => ! empty( $extra_urls ) ? $extra_urls : '',
								'ignore_urls'           => ! empty( $ignore_urls ) ? $ignore_urls : '',
								'ignore_post_ids'       => ! empty( $ignore_post_ids ) ? $ignore_post_ids : '',
							),
						),
					),
				) );
				?>

				<?php if ( $sitemap_crawler_available ) {
					$this->_render(
						'vertical-tab',
						array(
							'tab_id'              => 'tab_url_crawler',
							'tab_name'            => esc_html__( 'Crawler', 'wds' ),
							'title_actions_left'  => 'sitemap/sitemap-url-crawler-tab-title-left',
							'title_actions_right' => 'sitemap/sitemap-url-crawler-tab-title-right',
							'is_active'           => 'tab_url_crawler' === $active_tab,
							'button_text'         => false,
							'title_button'        => 'upgrade',
							'tab_sections'        => array(
								array(
									'section_template' => 'sitemap/sitemap-section-url-crawler',
								),
							),
						)
					);

					$this->_render(
						'vertical-tab-upsell',
						array(
							'tab_id'             => 'tab_url_crawler_reporting',
							'tab_name'           => esc_html__( 'Reporting', 'wds' ),
							'is_active'          => 'tab_url_crawler_reporting' === $active_tab,
							'title_actions_left' => 'sitemap/sitemap-reporting-title-pro-tag',
							'button_text'        => $is_member ? esc_html__( 'Save Settings', 'wds' ) : '',
							'tab_sections'       => array(
								array(
									'section_template' => 'sitemap/sitemap-section-reporting',
								),
							),
						)
					);
				} ?>

				<?php
				$this->_render( 'vertical-tab', array(
					'tab_id'       => 'tab_settings',
					'tab_name'     => esc_html__( 'Settings', 'wds' ),
					'is_active'    => 'tab_settings' === $active_tab,
					'tab_sections' => array(
						array(
							'section_template' => 'sitemap/sitemap-section-advanced',
							'section_args'     => array(
								'engines' => $engines,
							),
						),
					),
				) );
				?>
			</div>
		</form>
	<?php } else {
		$this->_render( 'sitemap/sitemap-disabled' );
	} ?>
	<?php $this->_render( 'footer' ); ?>
	<?php $this->_render( 'upsell-modal' ); ?>

</div><!-- end wds-sitemap-settings -->
