<div class="sui-dialog sui-fade-in" tabindex="-1" id="<?php echo esc_attr( $dialog_id ); ?>" aria-hidden="true">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide=""></div>
	<div class="sui-dialog-content sui-bounce-in" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-block-content-center">
				<h3 class="sui-box-title">
					<span class="branda-new"><?php esc_html_e( 'Add Custom Menu Item', 'ub' ); ?></span>
					<span class="branda-edit"><?php esc_html_e( 'Edit Custom Menu Item', 'ub' ); ?></span>
				</h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide="" type="button" class="sui-dialog-close branda-admin-bar-add-cancel" aria-label=""></button>
				</div>
			</div>
			<div class="sui-box-body">
				<div class="sui-tabs sui-tabs-flushed">
					<div data-tabs="">
						<div data-tab="general" class="active branda-first-tab"><?php esc_html_e( 'General', 'ub' ); ?></div>
						<div data-tab="submenu"><?php esc_html_e( 'Submenu', 'ub' ); ?></div>
						<div data-tab="visibility"><?php esc_html_e( 'Visibility', 'ub' ); ?></div>
					</div>
					<div data-panes="">
						<div data-tab="general" class="active <?php echo esc_attr( $dialog_id ); ?>-pane-general"></div>
						<div data-tab="submenu" class="<?php echo esc_attr( $dialog_id ); ?>-pane-submenu"></div>
						<div data-tab="visibility" class="<?php echo esc_attr( $dialog_id ); ?>-pane-visibility"></div>
					</div>
				</div>
				<input type="hidden" name="branda[id]" value="new" />
				<input type="hidden" name="branda[nonce]" value="new" />
			</div>
			<div class="sui-box-footer sui-space-between">
				<button class="sui-button sui-button-ghost branda-cancel" type="button" data-a11y-dialog-hide=""><?php esc_html_e( 'Cancel', 'ub' ); ?></button>
				<button class="sui-button branda-admin-bar-save branda-save" data-nonce="<?php echo esc_attr( $nonce_edit ); ?>" type="button">
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

