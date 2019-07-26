<?php
/**
 * @var string $for_type
 */
$section_enabled_field_id = 'og-active-' . $for_type;
$section_enabled = ! empty( $_view['options'][ $section_enabled_field_id ] ) ? $_view['options'][ $section_enabled_field_id ] : false;
$section_title = __( 'Enable OpenGraph', 'wds' );
$section_description = empty( $section_description )
	? esc_html__( 'OpenGraph support enhances how your content appears when shared on social networks such as Facebook.', 'wds' )
	: $section_description;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$title_field_id = 'og-title-' . $for_type;
$current_title = ! empty( $_view['options']["og-title-{$for_type}"] )
	? $_view['options']["og-title-{$for_type}"]
	: '';

$description_field_id = 'og-description-' . $for_type;
$current_description = ! empty( $_view['options']["og-description-{$for_type}"] )
	? $_view['options']["og-description-{$for_type}"]
	: '';

$images_field_id = 'og-images-' . $for_type;
$current_images = ! empty( $_view['options']["og-images-{$for_type}"] ) && is_array( $_view['options']["og-images-{$for_type}"] )
	? $_view['options']["og-images-{$for_type}"]
	: array();

$this->_render( 'onpage/onpage-social-meta-tags', array(
	'for_type'                 => $for_type,
	'section_enabled_field_id' => $section_enabled_field_id,
	'section_enabled'          => $section_enabled,
	'section_title'            => $section_title,
	'section_description'      => $section_description,
	'option_name'              => $option_name,
	'title_field_id'           => $title_field_id,
	'current_title'            => $current_title,
	'description_field_id'     => $description_field_id,
	'current_description'      => $current_description,
	'images_field_id'          => $images_field_id,
	'current_images'           => $current_images,
) );
