<?php
$key = empty( $key ) ? '' : $key;
$message = empty( $message ) ? '' : $message;
$dismissible = isset( $dismissible ) ? $dismissible : true;
$image_name = empty( $image_name ) ? 'mascot-message' : $image_name;

if ( ! $message ) {
	return;
}

$dismissed_messages = get_user_meta( get_current_user_id(), 'wds_dismissed_messages', true );
$is_message_dismissed = smartcrawl_get_array_value( $dismissed_messages, $key ) === true;
?>
<?php if ( ! $is_message_dismissed ) : ?>
	<div class="wds-mascot-message sui-box-settings-row sui-upsell-row" data-key="<?php echo esc_attr( $key ); ?>">
		<img class="sui-image sui-upsell-image"
		     src="<?php echo esc_attr( SMARTCRAWL_PLUGIN_URL ); ?>/assets/images/<?php echo esc_attr( $image_name ); ?>.svg"/>
		<div class="sui-upsell-notice">
			<p>
				<?php if ( $dismissible ) : ?>
					<span class="wds-mascot-bubble-dismiss">
						<i class="sui-icon-check" aria-hidden="true"></i>
					</span>
				<?php endif; ?>
				<?php echo wp_kses_post( $message ); ?>
			</p>
		</div>
	</div>
<?php endif; ?>
