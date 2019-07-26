<div class="sui-dialog sui-dialog-sm branda-welcome-step1" aria-hidden="true" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				 <h3 class="sui-box-title"><?php esc_html_e( 'Welcome to Branda', 'ub' ); ?></h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_attr_e( 'Close this dialog window', 'ub' ); ?>"></button>
				</div>
			</div>
			<div class="sui-box-body">
				<p><?php esc_html_e( 'Let’s get you started by activating the modules you want to use. You can always activate/deactivate modules later.', 'ub' ); ?></p>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step1">
				<div class="sui-form-field sui-actions-center">
					<button class="sui-button sui-button-blue branda-welcome-all-modules" type="button" data-nonce="<?php echo wp_create_nonce( 'branda-welcome-all-modules' ); ?>"><?php echo esc_html_x( 'Activate Modules', 'button', 'ub' ); ?></button>
				</div>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step1 branda-welcome-footer-close">
				<div class="sui-form-field sui-actions-center">
					<a href="#" class="sui-modal-close" data-a11y-dialog-hide="<?php echo esc_attr( $dialog_id ); ?>"><?php echo esc_html_x( 'Skip for now', 'button', 'ub' ); ?></a>
				</div>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step2">
				<div class="sui-form-field sui-actions-center">
					<button type="button" class="sui-modal-close sui-button sui-button-ghost" data-a11y-dialog-hide="<?php echo esc_attr( $dialog_id ); ?>"><?php echo esc_html_x( 'Skip', 'button', 'ub' ); ?></button>
					<button class="sui-button sui-button-blue branda-welcome-activate" type="button" data-nonce="<?php echo wp_create_nonce( 'branda-welcome-activate' ); ?>"><?php echo esc_html_x( 'Activate', 'button', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
