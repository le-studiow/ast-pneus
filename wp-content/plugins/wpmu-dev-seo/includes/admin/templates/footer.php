<?php
$is_member = (boolean) smartcrawl_get_array_value( $_view, 'is_member' );
$footer_text = sprintf( esc_html__( 'Made with %s by WPMU DEV', 'wds' ), '<i class="sui-icon-heart"></i>' );
$footer_text = Smartcrawl_White_Label::get()->get_wpmudev_footer_text( $footer_text );
?>

<div class="sui-footer">
	<?php echo wp_kses_post( $footer_text ); ?>
</div>

<?php if ( $is_member ): ?>
	<!-- PRO Navigation -->
	<ul class="sui-footer-nav">
		<li><a href="https://premium.wpmudev.org/hub/" target="_blank"><?php esc_html_e( 'The Hub', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/projects/category/plugins/"
		       target="_blank"><?php esc_html_e( 'Plugins', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'wds' ); ?></a>
		</li>
		<li><a href="https://premium.wpmudev.org/hub/support/"
		       target="_blank"><?php esc_html_e( 'Support', 'wds' ); ?></a>
		</li>
		<li><a href="https://premium.wpmudev.org/docs/" target="_blank"><?php esc_html_e( 'Docs', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/hub/community/"
		       target="_blank"><?php esc_html_e( 'Community', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/terms-of-service/"
		       target="_blank"><?php esc_html_e( 'Terms of Service', 'wds' ); ?></a></li>
		<li><a href="https://incsub.com/privacy-policy/"
		       target="_blank"><?php esc_html_e( 'Privacy Policy', 'wds' ); ?></a>
		</li>
	</ul>
<?php else: ?>
	<ul class="sui-footer-nav">
		<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins"
		       target="_blank"><?php esc_html_e( 'Free Plugins', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/features/"
		       target="_blank"><?php esc_html_e( 'Membership', 'wds' ); ?></a>
		</li>
		<li><a href="https://premium.wpmudev.org/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'wds' ); ?></a>
		</li>
		<li><a href="https://wordpress.org/support/plugin/smartcrawl-seo"
		       target="_blank"><?php esc_html_e( 'Support', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/docs/" target="_blank"><?php esc_html_e( 'Docs', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/hub/" target="_blank"><?php esc_html_e( 'The Hub', 'wds' ); ?></a></li>
		<li><a href="https://premium.wpmudev.org/terms-of-service/"
		       target="_blank"><?php esc_html_e( 'Terms of Service', 'wds' ); ?></a></li>
		<li><a href="https://incsub.com/privacy-policy/"
		       target="_blank"><?php esc_html_e( 'Privacy Policy', 'wds' ); ?></a>
		</li>
	</ul>
<?php endif; ?>

<ul class="sui-footer-social">
	<li><a href="https://www.facebook.com/wpmudev" target="_blank">
			<i class="sui-icon-social-facebook" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Facebook', 'wds' ); ?></span>
		</a></li>
	<li><a href="https://twitter.com/wpmudev" target="_blank">
			<i class="sui-icon-social-twitter" aria-hidden="true"></i></a>
		<span class="sui-screen-reader-text"><?php esc_html_e( 'Twitter', 'wds' ); ?></span>
	</li>
	<li><a href="https://www.instagram.com/wpmu_dev/" target="_blank">
			<i class="sui-icon-instagram" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Instagram', 'wds' ); ?></span>
		</a></li>
</ul>
