<?php
if ( ! smartcrawl_subsite_setting_page_enabled( 'wds_social' ) ) {
	return;
}

$options = $_view['options'];
$og_enabled = smartcrawl_get_array_value( $options, 'og-enable' );
$twitter_card_enabled = smartcrawl_get_array_value( $options, 'twitter-card-enable' );
$twitter_card_type = smartcrawl_get_array_value( $options, 'twitter-card-type' );
$twitter_card_status_text = Smartcrawl_Twitter_Printer::CARD_IMAGE === $twitter_card_type ? esc_html__( 'Summary Card with Large Image', 'wds' ) : esc_html__( 'Summary Card', 'wds' );
$pinterest_verification_status = smartcrawl_get_array_value( $options, 'pinterest-verification-status' );
$pinterest_tag = smartcrawl_get_array_value( $options, 'pinterest-verify' );

$social_page_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_SOCIAL );
$social_option_name = Smartcrawl_Settings::TAB_SOCIAL . '_options';
$settings_option_name = Smartcrawl_Settings::TAB_SETTINGS . '_options';
$social_enabled = Smartcrawl_Settings::get_setting( 'social' );
?>
<section id="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_SOCIAL ); ?>" class="sui-box wds-dashboard-widget">
	<div class="sui-box-header">
		<h3 class="sui-box-title">
			<i class="sui-icon-social-twitter"></i> <?php esc_html_e( 'Social', 'wds' ); ?>
		</h3>
	</div>

	<div class="sui-box-body">
		<p><?php esc_html_e( 'Control and optimize how your website appears when shared on social platforms like Facebook and Twitter.', 'wds' ); ?></p>

		<?php if ( $social_enabled ) : ?>
			<div class="wds-separator-top wds-draw-left-padded">
				<small><strong><?php esc_html_e( 'OpenGraph', 'wds' ); ?></strong></small>
				<?php if ( ! $og_enabled ) : ?>
					<p>
						<small><?php esc_html_e( 'Add meta data to your pages to make them look great when shared platforms such as Facebook and other popular social networks.', 'wds' ); ?></small>
					</p>
					<button
							type="button"
							data-option-id="<?php echo esc_attr( $social_option_name ); ?>"
							data-flag="<?php echo 'og-enable'; ?>"
							class="wds-activate-component wds-disabled-during-request sui-button sui-button-blue">

						<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>
				<?php else : ?>
					<div class="wds-right">
						<small><?php esc_html_e( 'Active', 'wds' ); ?></small>
					</div>
				<?php endif; ?>
			</div>

			<div class="wds-separator-top wds-draw-left-padded">
				<small><strong><?php esc_html_e( 'Twitter Cards', 'wds' ); ?></strong></small>
				<?php if ( ! $twitter_card_enabled ) : ?>
					<p>
						<small><?php esc_attr_e( 'With Twitter Cards, you can attach rich photos, videos and media experiences to Tweets, helping to drive traffic to your website.', 'wds' ); ?></small>
					</p>
					<button type="button"
					        data-option-id="<?php echo esc_attr( $social_option_name ); ?>"
					        data-flag="<?php echo 'twitter-card-enable'; ?>"
					        class="wds-activate-component wds-disabled-during-request sui-button sui-button-blue">

						<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>
				<?php else : ?>
					<div class="wds-right">
						<small><?php echo esc_html( $twitter_card_status_text ); ?></small>
					</div>
				<?php endif; ?>
			</div>

			<div class="wds-separator-top wds-draw-left-padded">
				<small>
					<strong><?php esc_html_e( 'Pinterest Verification', 'wds' ); ?></strong>
				</small>
				<?php if ( ! $pinterest_tag || 'fail' === $pinterest_verification_status ) : ?>
					<p>
						<small><?php esc_html_e( 'Verify your website with Pinterest to attribute your website when your website content is pinned to the platform.', 'wds' ); ?></small>
					</p>
					<a href="<?php echo esc_attr( $social_page_url ); ?>&tab=tab_pinterest_verification"
					   class="sui-button sui-button-blue">
						<i class="sui-icon-plus" aria-hidden="true"></i>

						<?php esc_html_e( 'Connect', 'wds' ); ?>
					</a>
				<?php else : ?>
					<div class="wds-right">
						<small><?php esc_html_e( 'Verification tag added' ); ?></small>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="sui-box-footer">
		<?php if ( $social_enabled ) : ?>
			<a href="<?php echo esc_attr( $social_page_url ); ?>"
			   class="sui-button sui-button-ghost">
				<i class="sui-icon-wrench-tool"
				   aria-hidden="true"></i> <?php esc_html_e( 'Configure', 'wds' ); ?>
			</a>
		<?php else : ?>
			<button type="button"
			        data-option-id="<?php echo esc_attr( $settings_option_name ); ?>"
			        data-flag="<?php echo esc_attr( 'social' ); ?>"
			        class="wds-activate-component wds-disabled-during-request sui-button sui-button-blue">

				<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			</button>
		<?php endif; ?>
	</div>
</section>
