<?php
/**
 * Shipper checks body copy templates: password protection
 *
 * @since v1.0.3
 * @package shipper
 */

?>
<div>
	<h4><?php esc_html_e( 'Overview', 'shipper' ); ?></h4>
	<p>
		<?php
			esc_html_e( 'Password protection is an additional layer of security requiring visitors to provide a password to visit your site. Unfortunately, Shipper can’t run with password protection enabled.', 'shipper' );
		?>
	</p>

	<h4><?php esc_html_e( 'Status', 'shipper' ); ?></h4>
	<div class="sui-notice sui-notice-error">
		<p>
			<?php
				echo wp_kses_post( sprintf(
					__( 'Password protection is enabled on <b>%s</b>.', 'shipper' ),
					$domain
				) );
			?>
		</p>
	</div>

	<h4><?php esc_html_e( 'How To Fix', 'shipper' ); ?></h4>
	<p>
		<?php
			esc_html_e( 'Temporarily disable password protection so that Shipper can run. Below are some options for doing this.', 'shipper' );
		?>
	</p>
	<p>
		<?php
			esc_html_e( '1. If you are using a plugin to password protect your website, disable this setting within the plugin or disable the plugin altogether during the migration. Enabled password protection again once the migration is complete.', 'shipper' );
		?>
	</p>
	<p>
		<?php
			esc_html_e( '2. Some managed hosts provide password protection. If your site is password protected in this way, you can disable in your hosting settings.', 'shipper' );
		?>
	</p>
</div>
<div class="sui-notice-top sui-notice-error sui-can-dismiss shipper-recheck-unsuccessful" style="display:none">
	<div class="sui-notice-content">
		<p>
			<?php echo wp_kses_post( sprintf(
				__( 'Password protection enabled on %1$s. Please fix this and check again.', 'shipper' ),
				$domain
			) ); ?>
		</p>
	</div>
	<span class="sui-notice-dismiss">
		<a role="button" href="#" aria-label="Dismiss" class="sui-icon-check"></a>
	</span>
</div>
