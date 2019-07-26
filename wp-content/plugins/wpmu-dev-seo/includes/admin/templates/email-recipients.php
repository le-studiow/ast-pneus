<?php
$id = empty( $id ) ? '' : $id;
$modal_id = $id . '-modal';
$email_recipients = empty( $email_recipients ) || ! is_array( $email_recipients )
	? array()
	: $email_recipients;
$field_name = empty( $field_name ) ? '' : $field_name;
$disable_addition = empty( $disable_addition ) ? false : true;
?>

<div class="wds-recipients sui-recipients"
     data-field-name="<?php echo esc_attr( $field_name ); ?>">

	<?php foreach ( $email_recipients as $index => $email_recipient ) {
		$this->_render( 'email-recipient', array(
			'index'           => $index,
			'email_recipient' => $email_recipient,
			'field_name'      => $field_name,
		) );
	} ?>

	<?php if ( ! $disable_addition ): ?>
		<a data-a11y-dialog-show="<?php echo esc_attr( $modal_id ); ?>"
		   class="sui-button sui-button-ghost">

			<i class="sui-icon-plus"
			   aria-hidden="true"></i> <?php esc_html_e( 'Add Recipient', 'wds' ); ?>
		</a>

		<?php $this->_render( 'modal', array(
			'id'              => $modal_id,
			'title'           => esc_html__( 'Add Recipient', 'wds' ),
			'description'     => esc_html__( 'Add as many recipients as you like, they will receive email reports as per the schedule you set.', 'wds' ),
			'body_template'   => 'add-email-recipient-modal-body',
			'footer_template' => 'add-email-recipient-modal-footer',
			'small'           => true,
		) ); ?>
	<?php endif; ?>
</div>
