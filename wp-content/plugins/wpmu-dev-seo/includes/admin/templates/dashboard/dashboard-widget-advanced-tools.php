<?php
if ( ! smartcrawl_subsite_setting_page_enabled( 'wds_autolinks' ) ) {
	return;
}

$page_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_AUTOLINKS );

$redirection_model = new Smartcrawl_Model_Redirection();
$redirection_count = count( $redirection_model->get_all_redirections() );

$option_name = Smartcrawl_Settings::TAB_SETTINGS . '_options';
$options = $_view['options'];
$autolinking_enabled = Smartcrawl_Settings::get_setting( 'autolinks' );
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$is_member = $service->is_member();
$moz_connected = Smartcrawl_Settings::get_setting( 'access-id' )
                 && Smartcrawl_Settings::get_setting( 'secret-key' );
$footer_class = $is_member ? 'sui-box-footer' : 'sui-box-body'; // Because the mascot message needs to be inside box body
?>

<section id="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_ADVANCED_TOOLS ); ?>"
         class="sui-box wds-dashboard-widget">
	<div class="sui-box-header">
		<h3 class="sui-box-title">
			<i class="sui-icon-wand-magic" aria-hidden="true"></i> <?php esc_html_e( 'Advanced Tools', 'wds' ); ?>
		</h3>
	</div>

	<div class="sui-box-body">
		<p><?php esc_html_e( 'Advanced tools focus on the finer details of SEO including internal linking, redirections and Moz analysis.', 'wds' ); ?></p>

		<div class="wds-separator-top wds-draw-left-padded">
			<small><strong><?php esc_html_e( 'URL Redirects', 'wds' ); ?></strong></small>
			<?php if ( empty( $redirection_count ) ): ?>
				<p>
					<small><?php esc_html_e( 'Automatically redirect traffic from one URL to another.', 'wds' ); ?></small>
				</p>
				<a href="<?php echo esc_attr( $page_url ); ?>&tab=tab_url_redirection"
				   class="sui-button sui-button-blue">
					<?php esc_html_e( 'Add Redirect', 'wds' ); ?>
				</a>
			<?php else: ?>
				<span class="wds-right"><small><?php echo esc_html( $redirection_count ); ?></small></span>
			<?php endif; ?>
		</div>

		<div class="wds-separator-top wds-draw-left-padded <?php echo $moz_connected ? 'wds-space-between' : ''; ?>">
			<small><strong><?php esc_html_e( 'Moz Integration', 'wds' ); ?></strong></small>

			<?php if ( $moz_connected ) : ?>
				<a href="<?php echo esc_attr( $page_url ); ?>&tab=tab_moz"
				   class="sui-button sui-button-ghost">

					<i class="sui-icon-eye" aria-hidden="true"></i> <?php esc_html_e( 'View Report', 'wds' ); ?>
				</a>
			<?php else : ?>
				<p>
					<small><?php esc_html_e( 'Moz provides reports that tell you how your site stacks up against the competition with all of the important SEO measurement tools.', 'wds' ); ?></small>
				</p>
				<a href="<?php echo esc_attr( $page_url ); ?>&tab=tab_moz"
				   class="sui-button sui-button-blue">

					<?php esc_html_e( 'Connect', 'wds' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="wds-separator-top <?php echo ! $is_member ? 'wds-box-blocked-area wds-draw-down wds-draw-left' : 'wds-draw-left-padded'; ?>">
			<small><strong><?php esc_html_e( 'Automatic Linking', 'wds' ); ?></strong></small>
			<?php if ( ! $is_member ) : ?>
				<a href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_dash_autolinking_pro_tag"
				   target="_blank">
					<span class="sui-tag sui-tag-pro sui-tooltip"
					      data-tooltip="<?php esc_attr_e( 'Get SmartCrawl Pro today Free', 'wds' ); ?>">
						<?php esc_html_e( 'Pro', 'wds' ); ?>
					</span>
				</a>
			<?php endif; ?>
			<?php if ( $autolinking_enabled && $is_member ) : ?>
				<div class="wds-right">
					<small><?php esc_html_e( 'Active', 'wds' ); ?></small>
				</div>
			<?php else : ?>
				<p>
					<small><?php esc_html_e( 'Configure SmartCrawl to automatically link certain key words to a page on your blog or even a whole new site all together.', 'wds' ); ?></small>
				</p>
				<button type="button"
				        data-option-id="<?php echo esc_attr( $option_name ); ?>"
				        data-flag="<?php echo 'autolinks'; ?>"
				        class="wds-activate-component wds-disabled-during-request sui-button sui-button-blue">

					<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<div class="<?php echo esc_attr( $footer_class ); ?>">
		<div>
			<a href="<?php echo esc_attr( $page_url ); ?>"
			   class="sui-button sui-button-ghost">

				<i class="sui-icon-wrench-tool"
				   aria-hidden="true"></i> <?php esc_html_e( 'Configure', 'wds' ); ?>
			</a>
		</div>

		<?php
		if ( ! $is_member ) {

			$this->_render( 'mascot-message', array(
				'key'         => 'seo-checkup-upsell',
				'dismissible' => false,
				'image_name'  => 'mascot-message-advanced-tools',
				'message'     => sprintf(
					'%s <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_dash_reports_upsell_notice">%s</a>',
					esc_html__( 'Upgrade to Pro and automatically link your articles both internally and externally with automatic linking - a favourite among SEO pros.', 'wds' ),
					esc_html__( '- Try SmartCrawl Pro FREE today!', 'wds' )
				),
			) );
		}
		?>
	</div>
</section>
