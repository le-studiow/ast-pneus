<?php
$content = empty( $content ) ? '' : $content;
$image = empty( $image ) ? '' : $image;
$component = empty( $component ) ? '' : $component;
$button_text = empty( $button_text ) ? '' : $button_text;
$is_member = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SITE )->is_member();
$upgrade_tag = empty( $upgrade_tag ) ? '' : $upgrade_tag;
$premium_feature = empty( $premium_feature ) ? false : $premium_feature;
$notice = empty( $notice ) ? '' : $notice;
$button_url = empty( $button_url ) ? '' : $button_url;
$button_icon = empty( $button_icon ) ? '' : $button_icon;
$button_disabled = empty( $button_disabled ) ? false : $button_disabled;

$image_url = sprintf( '%s/assets/images/%s', SMARTCRAWL_PLUGIN_URL, $image );
$image_url = Smartcrawl_White_Label::get()->get_wpmudev_hero_image( $image_url );

$upgrade_url = 'https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=' . $upgrade_tag;
?>
<div class="wds-disabled-component">
	<?php if ( $image_url ): ?>
		<p>
			<img src="<?php echo esc_attr( $image_url ); ?>"
			     alt="<?php esc_attr_e( 'Disabled', 'wds' ); ?>" class="wds-disabled-image"/>
		</p>
	<?php endif; ?>

	<p><?php echo wp_kses_post( $content ); ?></p>

	<?php if ( $notice ) : ?>
		<div class="wds-notice wds-notice-warning">
			<p><?php echo esc_html( $notice ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $component ) : ?>
		<input type="hidden" name="wds-activate-component" value="<?php echo esc_attr( $component ); ?>"/>
		<?php wp_nonce_field( 'wds-settings-nonce', '_wds_nonce' ); ?>
	<?php endif; ?>

	<?php if ( $premium_feature && ! $is_member ) : ?>
		<a class="sui-button sui-button-purple"
		   target="_blank"
		   href="<?php echo esc_attr( $upgrade_url ); ?>">
			<?php esc_html_e( 'Upgrade to Pro', 'wds' ); ?>
		</a>
	<?php else : ?>
		<?php if ( $button_url ) : ?>
			<a class="sui-button sui-button-blue <?php echo $button_disabled ? 'disabled' : ''; ?>"
			   href="<?php echo esc_attr( $button_url ); ?>">
				<?php if ( $button_icon ): ?>
					<i class="<?php echo esc_attr( $button_icon ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
				<?php echo esc_html( $button_text ); ?>
			</a>
		<?php else : ?>
			<input name="submit" class="sui-button sui-button-blue"
			       value="<?php echo esc_attr( $button_text ); ?>"
			       type="submit"/>
		<?php endif; ?>
	<?php endif; ?>
</div>
