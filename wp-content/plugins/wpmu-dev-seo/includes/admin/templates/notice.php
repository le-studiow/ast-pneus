<?php
$message = empty( $message ) ? '' : $message;
$class = empty( $class ) ? 'sui-notice-warning' : $class;
?>
<div class="wds-notice sui-notice <?php echo esc_attr( $class ); ?>">
	<p><?php echo wp_kses_post( $message ); ?></p>
</div>
