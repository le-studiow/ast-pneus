<?php
$admin_email = false;
$dash_email = false;
if ( class_exists( 'WPMUDEV_Dashboard' ) && ! empty( WPMUDEV_Dashboard::$site ) ) {
	if ( is_callable( array( WPMUDEV_Dashboard::$site, 'get_option' ) ) ) {
		$dash_email = WPMUDEV_Dashboard::$site->get_option( 'auth_user' );
		if ( false !== strpos( $dash_email, '@' ) ) {
			$admin_email = $dash_email;
		}
	}
}
$message = empty( $message ) ? '' : $message;
if ( ! $message ) {
	return;
}
?>
<p>
	<small>
		<?php if ( ! empty( $dash_email ) && ! empty( $admin_email ) ) { ?>
			<?php $admin_email = sprintf( '<a href="mailto: %1$s"><strong>%1$s</strong></a>', $admin_email ); ?>
		<?php } else { ?>
			<?php $admin_email = sprintf( '<strong>%1$s</strong>', esc_html__( 'your DEV account email', 'wds' ) ); ?>
		<?php } ?>

		<?php printf(
			wp_kses_post( $message ),
			wp_kses_post( $admin_email )
		); ?>
	</small>
</p>
