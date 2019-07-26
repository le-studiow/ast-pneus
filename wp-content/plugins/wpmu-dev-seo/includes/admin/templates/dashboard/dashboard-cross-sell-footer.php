<?php
if ( Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SITE )->is_member() ) {
	return;
}
?>

<div class="sui-row" id="sui-cross-sell-footer">
	<div><span class="sui-icon-plugin-2"></span></div>
	<h3><?php esc_html_e( 'Check out our other free wordpress.org plugins!', 'wds' ); ?></h3>
</div>

<div class="sui-row sui-cross-sell-modules">
	<div class="sui-col-md-4">
		<div class="sui-cross-1"><span></span></div>
		<div class="sui-box">
			<div class="sui-box-body">
				<h3><?php esc_html_e( 'Smush Image Compression and Optimization', 'wds' ); ?></h3>
				<p><?php esc_html_e( 'Resize, optimize and compress all of your images with the incredibly powerful and award-winning, 100% free WordPress image optimizer.', 'wds' ); ?></p>
				<a href="https://wordpress.org/plugins/wp-smushit/" class="sui-button sui-button-ghost"
				   target="_blank">
					<?php esc_html_e( 'View features', 'wds' ); ?> <i class="sui-icon-arrow-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="sui-col-md-4">
		<div class="sui-cross-2"><span></span></div>
		<div class="sui-box">
			<div class="sui-box-body">
				<h3><?php esc_html_e( 'Defender Security, Monitoring, and Hack Protection', 'wds' ); ?></h3>
				<p><?php esc_html_e( 'Security Tweaks & Recommendations, File & Malware Scanning, Login & 404 Lockout Protection, Two-Factor Authentication & more.', 'wds' ); ?></p>
				<a href="https://wordpress.org/plugins/defender-security/"
				   class="sui-button sui-button-ghost" target="_blank">
					<?php esc_html_e( 'View features', 'wds' ); ?> <i class="sui-icon-arrow-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="sui-col-md-4">
		<div class="sui-cross-3"><span></span></div>
		<div class="sui-box">
			<div class="sui-box-body">
				<h3><?php esc_html_e( 'Hummingbird Page Speed Optimization', 'wds' ); ?></h3>
				<p><?php esc_html_e( 'Performance Tests, File Optimization & Compression, Page, Browser & Gravatar Caching, GZIP Compression, CloudFlare Integration & more.', 'wds' ); ?></p>
				<a href="https://wordpress.org/plugins/hummingbird-performance/"
				   class="sui-button sui-button-ghost" target="_blank">
					<?php esc_html_e( 'View features', 'wds' ); ?> <i class="sui-icon-arrow-right"></i>
				</a>
			</div>
		</div>
	</div>
</div>

<div class="sui-cross-sell-bottom">
	<h3><?php esc_html_e( 'WPMU DEV - Your WordPress Toolkit', 'wds' ); ?></h3>
	<p><?php esc_html_e( 'Pretty much everything you need for developing and managing WordPress based websites, and then some.', 'wds' ); ?></p>

	<a class="sui-button sui-button-green"
	   target="_blank"
	   href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_dash_footer_upsell_notice"
	   id="dash-uptime-update-membership" rel="dialog">
		<?php esc_html_e( 'Learn more', 'wds' ); ?>
	</a>

	<img class="sui-image"
	     src="<?php echo esc_attr( SMARTCRAWL_PLUGIN_URL ); ?>/assets/shared-ui/images/dev-team.png"
	     srcset="<?php echo esc_attr( SMARTCRAWL_PLUGIN_URL ); ?>/assets/shared-ui/images/dev-team@2x.png 2x"
	     alt="<?php esc_html_e( 'Try pro features for free!', 'wds' ); ?>">
</div>
