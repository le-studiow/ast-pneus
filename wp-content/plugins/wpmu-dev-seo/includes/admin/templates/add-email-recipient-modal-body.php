<?php
$id = empty( $id ) ? '' : $id;
?>

<div class="sui-form-field">
	<label class="sui-label"><?php esc_html_e( 'First name', 'wds' ); ?></label>
	<input class="sui-form-control wds-recipient-name" placeholder="<?php esc_html_e( 'E.g. John', 'wds' ); ?>">
</div>

<div class="sui-form-field">
	<label class="sui-label"><?php esc_html_e( 'Email address', 'wds' ); ?></label>
	<input class="sui-form-control wds-recipient-email" placeholder="<?php esc_html_e( 'E.g. john@doe.com', 'wds' ); ?>">
</div>
