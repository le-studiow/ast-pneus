<?php
$index = empty( $index ) ? 0 : $index;
$email_recipient = empty( $email_recipient ) ? array() : $email_recipient;
$field_name = empty( $field_name ) ? '' : $field_name;

if ( ! is_array( $email_recipient ) || empty( $email_recipient ) ) {
	return;
}

$name = smartcrawl_get_array_value( $email_recipient, 'name' );
$email = smartcrawl_get_array_value( $email_recipient, 'email' );
?>
<div class="wds-recipient sui-recipient">
	<span class="sui-recipient-name">
		<?php echo get_avatar( $email, 20 ); ?>
		<?php echo esc_html( $name ); ?>
	</span>
	<span class="sui-recipient-email"><?php echo esc_html( $email ); ?></span>
	<span>
	<?php if ( $field_name ): ?>
		<a type="button" class="sui-button-icon wds-remove-email-recipient">
			<i class="sui-icon-trash" aria-hidden="true"></i>
		</a>
	<?php endif; ?>
	</span>

	<?php if ( $field_name ): ?>
		<input type="hidden"
		       name="<?php echo esc_attr( $field_name ); ?>[<?php echo esc_attr( $index ); ?>][name]"
		       value="<?php echo esc_attr( $name ); ?>"/>
		<input type="hidden"
		       name="<?php echo esc_attr( $field_name ); ?>[<?php echo esc_attr( $index ); ?>][email]"
		       value="<?php echo esc_attr( $email ); ?>"/>
	<?php endif; ?>
</div>
