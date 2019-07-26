<div class="sui-dialog sui-fade-in" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>" aria-hidden="true">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide=""></div>
	<div class="sui-dialog-content sui-bounce-in" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-block-content-center">
				<h3 class="sui-box-title">
					<span class="branda-new"><?php esc_html_e( 'Add Help Item', 'ub' ); ?></span>
					<span class="branda-edit"><?php esc_html_e( 'Edit Help Item', 'ub' ); ?></span>
				</h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide="" type="button" class="sui-dialog-close branda-admin-help-content-add-cancel" aria-label=""></button>
				</div>
			</div>
			<div class="sui-box-body">
				<div class="sui-form-field simple-option simple-option-text" >
					<label for="<?php echo esc_attr( $dialog_id ); ?>-title" class="sui-label"><?php esc_html_e( 'Title', 'ub' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $dialog_id ); ?>-title" name="branda[title]" class="sui-form-control" placeholder="<?php esc_attr_e( 'Enter help item title here…', 'ub' ); ?>" />
				</div>
				<div class="sui-form-field simple-option simple-option-wp_editor" >
				<label for="<?php echo esc_attr( $dialog_id ); ?>-content" class="sui-label"><?php esc_html_e( 'Content', 'ub' ); ?></label>
					<div class="branda-editor-placeholder hidden" aria-hidden="true"><?php esc_attr_e( 'Enter help item content here…', 'ub' ); ?></div>
<?php
$id = $dialog_id.'-content';
$args = array(
	'textarea_name' => 'branda[content]',
	'textarea_rows' => 9,
	// 'textarea_placeholder' => esc_attr_e( 'Add your help item content here…', 'ub' ),
	'teeny' => true,
);
wp_editor( '', $id, $args );
?>
				</div>
				<input type="hidden" name="branda[id]" value="new" />
			</div>
			<div class="sui-box-footer sui-space-between">
				<button class="sui-button sui-button-ghost branda-cancel" type="button" data-a11y-dialog-hide=""><?php esc_html_e( 'Cancel', 'ub' ); ?></button>
				<button class="sui-button branda-admin-help-content-save branda-save" type="button">
					<span class="sui-loading-text">
						<span class="branda-new"><i class="sui-icon-check"></i><?php esc_html_e( 'Add', 'ub' ); ?></span>
						<span class="branda-edit"><?php esc_html_e( 'Update', 'ub' ); ?></span>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
</div>

