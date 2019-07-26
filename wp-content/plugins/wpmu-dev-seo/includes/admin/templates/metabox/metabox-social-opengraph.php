<?php
$post = empty( $post ) ? null : $post;

$resolver = Smartcrawl_Endpoint_Resolver::resolve();
$resolver->simulate_post( $post );

$og_helper = new Smartcrawl_OpenGraph_Value_Helper();
$og = smartcrawl_get_value( 'opengraph', $post->ID );
if ( ! is_array( $og ) ) {
	$og = array();
}
$og = wp_parse_args( $og, array(
	'title'       => false,
	'description' => false,
	'images'      => false,
	'disabled'    => false,
) );

$this->_render( 'metabox/metabox-social-meta-tags', array(
	'main_title'              => __( 'OpenGraph', 'wds' ),
	'main_description'        => __( 'OpenGraph is used on many social networks such as Facebook.', 'wds' ),
	'field_name'              => 'wds-opengraph',
	'disabled'                => (bool) smartcrawl_get_array_value( $og, 'disabled' ),
	'current_title'           => $og['title'],
	'title_placeholder'       => $og_helper->get_title(),
	'current_description'     => $og['description'],
	'description_placeholder' => $og_helper->get_description(),
	'images'                  => $og['images'],
	'single_image'            => false,
) );

$resolver->stop_simulation();
