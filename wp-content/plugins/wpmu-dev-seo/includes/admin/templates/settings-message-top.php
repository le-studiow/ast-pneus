<?php
/**
 * Messages handling template for settings pages
 *
 * @package wpmu-dev-seo
 *
 * phpcs:ignoreFile -- We need to use $_GET without nonces
 */

$errors = ! empty( $_view['errors'] ) && is_array( $_view['errors'] )
	? $_view['errors']
	: array();
$type = ! empty( $errors )
	? 'warning'
	: 'success';
?>
<?php if ( ! empty( $_view['msg'] ) ): ?>
	<div class="wds-notice-floating wds-notice wds-notice-<?php echo esc_attr( $type ); ?>">
		<p><?php echo wp_kses_post( $_view['msg'] ); ?></p>
	</div>
<?php endif; ?>

<?php if ( ! empty( $errors ) ): ?>
	<?php foreach ( $errors as $error ): ?>
		<?php
		$msg = ! empty( $error['message'] ) ? $error['message'] : false;
		if ( empty( $msg ) ) {
			continue;
		}
		?>
		<div class="wds-notice-floating wds-notice wds-notice-error">
			<p><?php echo wp_kses_post( $msg ); ?></p>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php
/**
 * Import/Export error messages display
 */
$io_errors = Smartcrawl_Controller_IO::get()->get_errors();
?>

<?php if ( ! empty( $io_errors ) ): ?>
	<?php foreach ( $io_errors as $io_type => $io_error ): ?>
		<div class="wds-notice-floating wds-notice wds-notice-error <?php esc_attr( $io_type ); ?>">
			<p><?php echo wp_kses( $io_error, array( 'br' => array() ) ); ?></p>
		</div>
	<?php endforeach; ?>
<?php elseif ( ! empty( $_GET['import'] ) && 'success' === $_GET['import'] ): ?>
	<div class="wds-notice-floating wds-notice wds-notice-success">
		<p><?php esc_html_e( 'Settings successfully imported', 'wds' ); ?></p>
	</div>
<?php endif; ?>

