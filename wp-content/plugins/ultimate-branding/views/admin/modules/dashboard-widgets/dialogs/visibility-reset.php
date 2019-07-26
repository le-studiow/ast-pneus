<?php
/**
 * reset Widget Visibility list
 *
 * @since 3.0.7
 */
?>
<div class="sui-dialog sui-dialog-sm" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>" aria-hidden="true">
	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide=""></div>
	<div class="sui-dialog-content sui-bounce-out" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
			<h3 class="sui-box-title"><?php esc_html_e( 'Reset Widget Visibility List', 'ub' ); ?></h3>
				<button data-a11y-dialog-hide="" class="sui-dialog-close" aria-label="<?php esc_attr_e( 'Close this dialog window', 'ub' ); ?>"></button>
			</div>
			<div class="sui-box-body">
				<p><?php esc_html_e( 'Are you sure you wish to reset list of registered widgets?', 'ub' ); ?></p>
			</div>
			<div class="sui-box-footer">
				<div class="sui-form-field sui-actions-center">
					<button type="button" class="sui-modal-close sui-button sui-button-ghost" data-a11y-dialog-hide><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></button>
					<button type="button" class="sui-button sui-button-ghost sui-button-red sui-button-icon branda-data-reset-confirm" data-nonce="<?php echo esc_attr( $nonce ); ?>"><i class="sui-icon-undo" aria-hidden="true"></i><?php echo esc_html_x( 'Reset', 'button', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
