<div class="sui-dialog sui-dialog-sm" aria-hidden="true" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				 <h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Delete Subsites Data', 'ub' ); ?></h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php echo esc_attr_x( 'Close this dialog window', 'button', 'ub' ); ?>"></button>
				</div>
			</div>
			<div class="sui-box-body">
				<p><?php esc_html_e( 'Are you sure you want to delete Branda’s data stored in all the subsites?', 'ub' ); ?></p>
			</div>
			<div class="sui-box-footer">
				<div class="sui-form-field sui-actions-center">
					<button type="button" class="sui-modal-close sui-button sui-button-ghost" data-a11y-dialog-hide="<?php echo esc_attr( $dialog_id ); ?>"><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></button>
					<button type="button" class="sui-button sui-button-ghost sui-button-red sui-button-icon branda-data-delete-subsites-confirm" data-nonce="<?php echo esc_attr( $button_nonce ); ?>"><?php echo esc_html_x( 'Delete', 'button', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
