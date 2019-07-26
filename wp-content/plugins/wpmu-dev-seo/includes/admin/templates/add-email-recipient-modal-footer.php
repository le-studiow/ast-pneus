<?php
$id = empty( $id ) ? '' : $id;
?>

<button type="button"
        class="sui-button sui-button-ghost wds-cancel-button"
        data-a11y-dialog-hide="<?php echo esc_attr( $id ); ?>">
	<?php esc_html_e( 'Cancel', 'wds' ); ?>
</button>

<div class="sui-actions-right">
	<button type="button"
	        class="sui-button wds-add-email-recipient">
		<?php esc_html_e( 'Add', 'wds' ); ?>
	</button>
</div>
