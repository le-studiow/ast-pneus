<?php
$dialog_id = sprintf( 'branda-copy-settings-%s', $module['module'] );
$data_name = '_'.preg_replace( '/-/', '_', $dialog_id );
?>
<div class="sui-actions-left">
	<button type="button" class="sui-button sui-button-ghost" data-a11y-dialog-show="<?php echo esc_attr( $dialog_id ); ?>" data-data-name="<?php echo esc_attr( $data_name ); ?>" ><?php echo esc_html_x( 'Copy Settings', 'button', 'ub' ); ?></button>
</div>
<div class="sui-dialog sui-dialog-sm" aria-hidden="true" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Copy Settings', 'ub' ); ?></h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_attr_e( 'Close this dialog window', 'ub' ); ?>"></button>
				</div>
			</div>
			<div class="sui-box-body">
				<p id="dialogDescription"><?php esc_html_e( 'Choose the module you want to copy settings from and specify the sections to copy the setting of.', 'ub' ); ?></p>
				<div class="sui-form-field">
					<label for="dialog-text-5" class="sui-label"><?php esc_html_e( 'Copy settings from', 'ub' ); ?></label>
					<select class="branda-copy-settings-select">
						<option value=""><?php esc_html_e( 'Choose module', 'ub' ); ?></option>
<?php
asort( $related );
foreach ( $related as $module_key => $data ) {
	printf(
		'<option value="%s">%s</option>',
		esc_attr( $module_key ),
		esc_html( $data['title'] )
	);
}
					?></select>
				</div>
<?php
foreach ( $related as $module_key => $data ) {
	printf(
		'<div class="branda-copy-settings-options branda-copy-settings-%s hidden">',
		esc_attr( $module_key )
	);
	foreach ( $data['options'] as $value => $label ) {
?>
<label class="sui-checkbox sui-checkbox-stacked">
<input type="checkbox" class="branda-copy-settings-section" value="<?php echo esc_attr( $value ); ?>" />
	<span aria-hidden="true"></span>
	<span><?php echo esc_html( $label ); ?></span>
</label>
<?php
	}
	echo '</div>';
}
?>
			</div>
			<div class="sui-box-footer sui-space-between">
				<button type="button" class="sui-button sui-button-ghost" data-a11y-dialog-hide="<?php echo esc_attr( $dialog_id ); ?>"><?php echo esc_html_x( 'Cancel', 'Dialog "Copy Settings" button', 'ub' ); ?></button>
				<button type="button" class="sui-modal-close sui-button sui-button-blue branda-copy-settings-copy-button" data-module="<?php echo esc_attr( $module['module'] ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( $dialog_id ) ); ?>" disabled="disabled"><?php echo esc_html_x( 'Copy', 'Dialog "Copy Settings" button', 'ub' ); ?></button>
			</div>
		</div>
	</div>
</div>
