<?php
/**
 * Automatic links settings template
 *
 * @package wpmu-dev-seo
 */

$additional_settings = empty( $additional_settings ) ? array() : $additional_settings;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Min lengths', 'wds' ); ?></label>
		<p class="sui-description"><?php esc_html_e( 'Define the shortest title and taxonomy length to autolink. Smaller titles will be ignored.', 'wds' ); ?></p>
	</div>

	<div class="sui-box-settings-col-2">
		<div class="sui-row">
			<div class="sui-form-field sui-col">
				<label for="cpt_char_limit" class="sui-label"><?php esc_html_e( 'Posts & pages', 'wds' ); ?></label>
				<input id='cpt_char_limit' name='<?php echo esc_attr( $option_name ); ?>[cpt_char_limit]'
				       type='text' class='sui-form-control sui-input-sm'
				       value='<?php echo esc_attr( $_view['options']['cpt_char_limit'] ); ?>'>
			</div>

			<div class="sui-form-field sui-col">
				<label for="tax_char_limit"
				       class="sui-label"><?php esc_html_e( 'Archives & taxonomies', 'wds' ); ?></label>
				<input id='tax_char_limit' name='<?php echo esc_attr( $option_name ); ?>[tax_char_limit]'
				       type='text' class='sui-form-control sui-input-sm'
				       value='<?php echo esc_attr( $_view['options']['tax_char_limit'] ); ?>'>
			</div>
		</div>
		<p class="sui-description"><?php esc_html_e( 'We recommend a minimum of 10 chars for each type.', 'wds' ); ?></p>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Max limits', 'wds' ); ?></label>
		<p class="sui-description"><?php esc_html_e( 'Set the max amount of links you want to appear per post.', 'wds' ); ?></p>
	</div>

	<div class="sui-box-settings-col-2">
		<div class="sui-row">
			<div class="sui-form-field sui-col">
				<label for="link_limit" class="sui-label"><?php esc_html_e( 'Per post total', 'wds' ); ?></label>
				<input id='link_limit' name='<?php echo esc_attr( $option_name ); ?>[link_limit]' type='text'
				       class='sui-form-control sui-input-sm'
				       value='<?php echo esc_attr( $_view['options']['link_limit'] ); ?>'>
			</div>

			<div class="sui-form-field sui-col">
				<label for="single_link_limit"
				       class="sui-label"><?php esc_html_e( 'Per keyword group', 'wds' ); ?></label>
				<input id='single_link_limit' name='<?php echo esc_attr( $option_name ); ?>[single_link_limit]'
				       type='text' class='sui-form-control sui-input-sm'
				       value='<?php echo esc_attr( $_view['options']['single_link_limit'] ); ?>'>
			</div>
		</div>
		<p class="sui-description"><?php esc_html_e( 'Use 0 to allow unlimited automatic links.', 'wds' ); ?></p>
	</div>
</div>

<?php
$this->_render( 'toggle-group', array(
	'label'       => __( 'Optional Settings', 'wds' ),
	'description' => __( 'Configure extra settings for absolute control over autolinking.', 'wds' ),
	'items'       => $additional_settings,
	'separator'   => true,
) );
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label">
			<?php esc_html_e( 'Deactivate', 'wds' ); ?>
		</label>
		<p class="sui-description">
			<?php esc_html_e( 'No longer need keyword linking? This will deactivate this feature and remove existing links.', 'wds' ); ?>
		</p>
	</div>
	<div class="sui-box-settings-col-2">
		<button type="submit" name="deactivate-autolinks-component"
		        class="sui-button sui-button-ghost">
			<i class="sui-icon-power-on-off" aria-hidden="true"></i>
			<?php esc_html_e( 'Deactivate', 'wds' ); ?>
		</button>
	</div>
</div>
