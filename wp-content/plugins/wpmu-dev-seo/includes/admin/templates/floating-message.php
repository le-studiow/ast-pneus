<?php
$class = empty( $class ) ? 'sui-notice-info' : $class;
$message = empty( $message ) ? '' : $message;
?>
<div class="sui-wrap wds-notice-floating">
	<div class="sui-notice-top sui-can-dismiss <?php echo esc_attr( $class ); ?>">
		<div class="sui-notice-content">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<span class="sui-notice-dismiss">
			<a role="button" href="#" aria-label="<?php esc_html_e( 'Dismiss', 'wds' ); ?>"
			   class="sui-icon-check"></a>
		</span>
	</div>
</div>
