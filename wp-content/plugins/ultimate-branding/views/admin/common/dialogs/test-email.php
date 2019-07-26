<?php
$current_user = wp_get_current_user();
?>
<div class="sui-dialog sui-dialog-sm branda-test-email" aria-hidden="true" tabindex="-1" id="<?php echo esc_attr( $id ); ?>">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-block-content-center">
				<h3 class="sui-box-title"><?php esc_html_e( 'Test Email', 'ub' ); ?></h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide type="button" class="sui-dialog-close" aria-label=""></button>
				</div>
			</div>
			<div class="sui-box-body">
				<p class="sui-description"><?php echo esc_html( $description ); ?></p>
				<div class="sui-form-field">
					<label for="branda-smtp-test-email" class="sui-label"><?php esc_html_e( 'Email address', 'ub' ); ?></label>
					<input type="email" class="sui-form-control" placeholder="<?php echo esc_attr( $current_user->user_email ); ?>" required="required" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
					<span class="hidden"><?php esc_html_e( 'Test email can not be empty!', 'ub' ); ?></span>
				</div>
			</div>
			<div class="sui-box-footer">
				<div class="sui-form-field sui-actions-right">
					<button class="sui-button" type="button"
						data-nonce="<?php echo esc_attr( $nonce ); ?>"
						data-action="<?php echo esc_attr( $action ); ?>"
					>
						<span class="sui-loading-text"><?php esc_html_e( 'Send', 'ub' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

