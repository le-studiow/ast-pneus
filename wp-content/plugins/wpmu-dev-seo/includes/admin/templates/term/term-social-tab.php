<?php
$og_setting_enabled = empty( $og_setting_enabled ) ? false : $og_setting_enabled;
$og_taxonomy_enabled = empty( $og_taxonomy_enabled ) ? false : $og_taxonomy_enabled;

$twitter_setting_enabled = empty( $twitter_setting_enabled ) ? false : $twitter_setting_enabled;
$twitter_taxonomy_enabled = empty( $twitter_taxonomy_enabled ) ? false : $twitter_taxonomy_enabled;

$tax_meta = empty( $tax_meta ) ? array() : $tax_meta;
$term = empty( $term ) ? null : $term;
$default_settings_message = smartcrawl_format_link(
	esc_html__( "Customize this term's title, description and featured images for social shares. You can also configure the default settings for this taxonomy in SmartCrawl's %s area.", 'wds' ),
	Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_ONPAGE ),
	esc_html__( 'Titles & Meta', 'wds' )
);
$is_active = empty( $is_active ) ? false : $is_active;
?>
<div class="<?php echo $is_active ? 'active' : ''; ?>">
	<div class="wds-metabox-section sui-box-body">
		<p>
			<?php echo wp_kses_post( $default_settings_message ); ?>
		</p>

		<?php if ( $og_setting_enabled && $og_taxonomy_enabled ) :
			$og = smartcrawl_get_array_value( $tax_meta, 'opengraph' );
			$og = wp_parse_args( ! is_array( $og ) ? array() : $og, array(
				'title'       => false,
				'description' => false,
				'images'      => false,
				'disabled'    => false,
			) );
			$og_meta_disabled = (bool) smartcrawl_get_array_value( $og, 'disabled' );
			$og_helper = new Smartcrawl_OpenGraph_Value_Helper();

			$this->_render( 'metabox/metabox-social-meta-tags', array(
				'toggle_label'            => esc_html__( 'Enable for this term', 'wds' ),
				'main_title'              => esc_html__( 'OpenGraph', 'wds' ),
				'main_description'        => esc_html__( 'OpenGraph is used on many social networks such as Facebook.', 'wds' ),
				'field_name'              => 'wds-opengraph',
				'disabled'                => $og_meta_disabled,
				'current_title'           => $og['title'],
				'title_placeholder'       => $og_helper->get_title(),
				'current_description'     => $og['description'],
				'description_placeholder' => $og_helper->get_description(),
				'images'                  => $og['images'],
				'single_image'            => false,
				'images_description'      => esc_html__( 'Each of these images will be available to use as the featured image when the term archive is shared.', 'wds' ),
			) ); ?>
		<?php endif; ?>

		<?php if ( $twitter_setting_enabled && $twitter_taxonomy_enabled ) :
			$twitter = smartcrawl_get_array_value( $tax_meta, 'twitter' );
			$twitter = wp_parse_args( ! is_array( $twitter ) ? array() : $twitter, array(
				'title'       => false,
				'description' => false,
				'images'      => false,
				'disabled'    => false,
			) );
			$twitter_printer = Smartcrawl_Twitter_Printer::get();
			$twitter_meta_disabled = smartcrawl_get_array_value( $twitter, 'disabled' );

			$this->_render( 'metabox/metabox-social-meta-tags', array(
				'toggle_label'            => esc_html__( 'Enable for this term', 'wds' ),
				'main_title'              => esc_html__( 'Twitter', 'wds' ),
				'main_description'        => esc_html__( 'These details will be used in Twitter cards.', 'wds' ),
				'field_name'              => 'wds-twitter',
				'disabled'                => $twitter_meta_disabled,
				'current_title'           => $twitter['title'],
				'title_placeholder'       => $twitter_printer->get_title_content(),
				'current_description'     => $twitter['description'],
				'description_placeholder' => $twitter_printer->get_description_content(),
				'images'                  => $twitter['images'],
				'single_image'            => true,
				'images_description'      => esc_html__( 'This image will be used as the featured image when the term archive is shared.', 'wds' ),
			) );
			?>
		<?php endif; ?>
	</div>
</div>
