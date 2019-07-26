<?php
if ( ! empty( $footer_text ) ) {
	printf(
		'<div class="sui-footer">%s</div>',
		$footer_text
	);
}
if ( Branda_Helper::is_member() ) {
	if ( ! $hide_footer ) { ?>
<ul class="sui-footer-nav">
	<li><a href="https://premium.wpmudev.org/hub/" target="_blank"><?php esc_html_e( 'The Hub', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/projects/category/plugins/" target="_blank"><?php esc_html_e( 'Plugins', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/hub/support/" target="_blank"><?php esc_html_e( 'Support', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/docs/" target="_blank"><?php esc_html_e( 'Docs', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/hub/community/" target="_blank"><?php esc_html_e( 'Community', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/academy/" target="_blank"><?php esc_html_e( 'Academy', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/terms-of-service/" target="_blank"><?php esc_html_e( 'Terms of Service', 'ub' ); ?></a></li>
	<li><a href="https://incsub.com/privacy-policy/" target="_blank"><?php esc_html_e( 'Privacy Policy', 'ub' ); ?></a></li>
</ul>
<?php
	}
} else {
?>
<ul class="sui-footer-nav">
	<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins" target="_blank"><?php esc_html_e( 'Free Plugins', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/features/" target="_blank"><?php esc_html_e( 'Membership', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/docs/" target="_blank"><?php esc_html_e( 'Docs', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/hub-welcome/" target="_blank"><?php esc_html_e( 'The Hub', 'ub' ); ?></a></li>
	<li><a href="https://premium.wpmudev.org/terms-of-service/" target="_blank"><?php esc_html_e( 'Terms of Service', 'ub' ); ?></a></li>
	<li><a href="https://incsub.com/privacy-policy/" target="_blank"><?php esc_html_e( 'Privacy Policy', 'ub' ); ?></a></li>
</ul>
<?php
}
if ( ! $hide_footer ) {
?>
<ul class="sui-footer-social">
	<li><a href="https://www.facebook.com/wpmudev" target="_blank"><i class="sui-icon-social-facebook" aria-hidden="true"></i><span class="sui-screen-reader-text">Facebook</span></a></li>
	<li><a href="https://twitter.com/wpmudev" target="_blank"><i class="sui-icon-social-twitter" aria-hidden="true"></i></a><span class="sui-screen-reader-text">Twitter</span></li>
	<li><a href="https://www.instagram.com/wpmu_dev/" target="_blank"><i class="sui-icon-instagram" aria-hidden="true"></i><span class="sui-screen-reader-text">Instagram</span></a></li>
</ul>
<?php
}
$this->render('admin/common/dialogs/reset');
