<div class="sui-dialog" aria-hidden="true" tabindex="-1" id="<?php echo esc_attr( $id ); ?>">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<h3 class="sui-box-title"><?php esc_html_e( 'Preview', 'ub' ); ?></h3>
				<div class="sui-actions-right">
				<button data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_attr_e( 'Close this dialog window', 'ub' ); ?>"></button>
				</div>
			</div>
			<div class="sui-box-body"></div>
			<div class="sui-box-footer">
				<div class="sui-actions-right">
					<button class="sui-button" data-a11y-dialog-hide><?php esc_html_e( 'Close', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
