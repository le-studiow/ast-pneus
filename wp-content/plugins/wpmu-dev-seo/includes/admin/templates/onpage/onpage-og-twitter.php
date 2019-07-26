<?php
/**
 * @var string $for_type
 */
$social_options = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
$onpage_options = ! empty( $_view['options'] ) ? $_view['options'] : array();
$social_label_desc = empty( $social_label_desc )
	? esc_html__( 'Enable or disable support for social platforms when this content is shared on them.' ) : $social_label_desc;

$og_enabled_field_id = 'og-active-' . esc_attr( $for_type );
$og_enabled_globally = smartcrawl_get_array_value( $social_options, 'og-enable' );
$og_enabled_locally = ! empty( $onpage_options[ $og_enabled_field_id ] ) ? $onpage_options[ $og_enabled_field_id ] : false;
$og_description = empty( $og_description ) ? '' : $og_description;

$twitter_enabled_field_id = 'twitter-active-' . esc_attr( $for_type );
$twitter_enabled_globally = smartcrawl_get_array_value( $social_options, 'twitter-card-enable' );
$twitter_enabled_locally = ! empty( $onpage_options[ $twitter_enabled_field_id ] ) ? $onpage_options[ $twitter_enabled_field_id ] : false;
$twitter_description = empty( $twitter_description ) ? '' : $twitter_description;
?>
<div class="sui-box-settings-row wds-social-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label">
			<?php esc_html_e( 'Social', 'wds' ); ?>
		</label>
		<span class="sui-description"><?php echo esc_html( $social_label_desc ); ?></span>
	</div>
	<div class="sui-box-settings-col-2">
		<?php
		if ( ! $og_enabled_globally ) {
			$this->_render( 'onpage/onpage-og-disabled' );
		} else {
			$this->_render( 'onpage/onpage-og-settings', array(
				'for_type'            => $for_type,
				'section_description' => $og_description,
			) );
		}
		?>
		<p></p>

		<?php
		if ( ! $twitter_enabled_globally ) {
			$this->_render( 'onpage/onpage-twitter-disabled' );
		} else {
			$this->_render( 'onpage/onpage-twitter-settings', array(
				'for_type'            => $for_type,
				'section_description' => $twitter_description,
			) );
		}
		?>
	</div>
</div>
