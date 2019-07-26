<?php
$redirections = empty( $redirections ) ? array() : $redirections;
$types = empty( $types ) ? array() : $types;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$plugin_settings = Smartcrawl_Settings::get_specific_options( 'wds_settings_options' );
$redirection_index = 0;
?>

<input type="hidden" value="1" name="<?php echo esc_attr( $option_name ); ?>[save_redirects]"/>
<div class="wds-redirects-container">
	<div id="wds-bulk-update-redirects"></div>
	<div id="wds-add-redirect-form"></div>
	<div id="wds-edit-redirect-form"></div>

	<div class="sui-box-builder">
		<div class="sui-box-builder-header">
			<button class="wds-add-redirect sui-button sui-button-purple">
				<i class="sui-icon-plus" aria-hidden="true"></i>
				<?php esc_html_e( 'Add Redirect', 'wds' ); ?>
			</button>
		</div>

		<div class="sui-box-builder-body <?php echo empty( $redirections ) ? 'wds-no-redirects' : ''; ?>">
			<div class="wds-redirect-controls">
				<label class="sui-checkbox">
					<input type="checkbox"/>
					<span aria-hidden="true"></span>
				</label>
				<button class="wds-bulk-update sui-button" disabled>
					<?php esc_html_e( 'Bulk Update', 'wds' ); ?>
				</button>
				<button class="wds-bulk-remove sui-button" disabled>
					<?php esc_html_e( 'Remove Redirects', 'wds' ); ?>
				</button>
			</div>

			<div class="wds-redirects sui-builder-fields">
				<?php foreach ( $redirections as $source => $destination ) {
					$type = ! empty( $types[ $source ] ) ? $types[ $source ] : '';
					$this->_render( 'advanced-tools/advanced-tools-redirect-item', array(
						'source'        => esc_attr( $source ),
						'destination'   => esc_attr( $destination ),
						'index'         => $redirection_index,
						'selected_type' => intval( $type ),
					) );
					$redirection_index ++;
				} ?>
			</div>

			<button class="wds-add-redirect sui-button sui-button-dashed">
				<i class="sui-icon-plus" aria-hidden="true"></i>
				<?php esc_html_e( 'Add Redirect', 'wds' ); ?>
			</button>

			<p class="wds-no-redirects-message">
				<small><?php esc_html_e( 'You can add as many redirects as you like. Add your first above!', 'wds' ); ?></small>
			</p>
		</div>
	</div>
</div>
