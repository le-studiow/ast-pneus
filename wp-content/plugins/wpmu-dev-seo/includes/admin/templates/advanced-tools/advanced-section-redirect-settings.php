<?php
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$plugin_settings = Smartcrawl_Settings::get_specific_options( 'wds_settings_options' );
$current_redirection_code = smartcrawl_get_array_value( $plugin_settings, 'redirections-code' );
$redirection_types = array(
	301 => __( 'Permanent (301)', 'wds' ),
	302 => __( 'Temporary (302)', 'wds' ),
);

$this->_render( 'toggle-group', array(
	'label'       => __( 'Redirect attachments', 'wds' ),
	'description' => __( 'Redirect attachments to their parent post, preventing them from appearing in SERPs.', 'wds' ),
	'items'       => array(
		'redirect-attachments'             => esc_html__( 'Redirect attachments', 'wds' ),
		'redirect-attachments-images_only' => esc_html__( '... but only if the attachment is image', 'wds' ),
	),
) );
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label" for="wds-default-redirection-type">
			<?php esc_html_e( 'Default Redirection Type', 'wds' ); ?>
		</label>
		<p class="sui-description">
			<?php esc_html_e( 'Select the redirection type that you would like to be used as default.', 'wds' ); ?>
		</p>
	</div>

	<div class="sui-box-settings-col-2">
		<select id="wds-default-redirection-type"
		        name="<?php echo esc_attr( $option_name ); ?>[redirections-code]"
		        autocomplete="off"
		        data-minimum-results-for-search="-1"
		        class="sui-select">
			<?php foreach ( $redirection_types as $redirection_type => $redirection_type_label ): ?>
				<option value="<?php echo esc_attr( $redirection_type ); ?>"
					<?php echo selected( $redirection_type, $current_redirection_code, false ); ?>>
					<?php echo esc_html( $redirection_type_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
