<?php
$smartcrawl_options = empty( $_view['options'] ) ? array() : $_view['options'];
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$section_title = empty( $section_title ) ? '' : $section_title;
$section_description = empty( $section_description ) ? '' : $section_description;
$section_template = empty( $section_template ) ? '' : $section_template;
$section_args = empty( $section_args ) ? array() : $section_args;
$section_type = empty( $section_type ) ? '' : $section_type;

$section_enabled_option = empty( $section_enabled_option ) ? '' : $section_enabled_option;
$section_enabled = empty( $section_enabled_option ) || ! empty( $smartcrawl_options[ $section_enabled_option ] );
$section_enabled_option_name = $option_name . '[' . $section_enabled_option . ']';
$section_toggle_tooltip = empty( $section_toggle_tooltip )
	? sprintf( esc_html__( 'Enable/Disable %s' ), strtolower( $section_title ) )
	: $section_toggle_tooltip;
$accordion_section_open = empty( $accordion_section_open ) ? false : $accordion_section_open;

$classes = array();
$classes[] = $section_enabled ? '' : 'sui-accordion-item--disabled';
$classes[] = $section_enabled && $accordion_section_open ? 'sui-accordion-item--open' : '';
?>

<div data-type="<?php echo esc_attr( $section_type ); ?>"
     class="sui-accordion-item <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="sui-accordion-item-header">
		<div class="sui-accordion-item-title sui-accordion-col-2">
			<?php echo esc_html( $section_title ); ?>
		</div>

		<div class="sui-accordion-col-2">
			<?php if ( $section_enabled_option ): ?>
				<span class="sui-tooltip sui-tooltip-top-right"
				      data-tooltip="<?php echo esc_html( $section_toggle_tooltip ); ?>">
					<label for="<?php echo esc_attr( $section_enabled_option_name ); ?>"
					       class="sui-toggle sui-accordion-item-action">
						<input type="checkbox" <?php checked( $section_enabled ); ?>
						       id="<?php echo esc_attr( $section_enabled_option_name ); ?>"
						       name="<?php echo esc_attr( $section_enabled_option_name ); ?>">
						<span aria-hidden="true" class="sui-toggle-slider"></span>
						<span class="sui-screen-reader-text">Toggle</span>
					</label>
				</span>
			<?php endif; ?>
			<button type="button" class="sui-button-icon sui-accordion-open-indicator"
			        aria-label="<?php esc_html_e( 'Open item', 'wds' ); ?>">
				<i class="sui-icon-chevron-down" aria-hidden="true"></i>
			</button>
		</div>
	</div>

	<div class="sui-accordion-item-body">
		<div class="sui-box">
			<div class="sui-box-body">
				<?php if ( $section_description ) : ?>
					<p><?php echo esc_html( $section_description ); ?></p>
				<?php endif; ?>
				<?php $this->_render( $section_template, $section_args ); ?>
			</div>
		</div>
	</div>
</div>
