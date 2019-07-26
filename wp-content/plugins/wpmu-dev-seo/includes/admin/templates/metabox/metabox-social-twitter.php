<?php
$post = empty( $post ) ? null : $post;

$resolver = Smartcrawl_Endpoint_Resolver::resolve();
$resolver->simulate_post( $post );

$twitter_printer = Smartcrawl_Twitter_Printer::get();
$twitter = smartcrawl_get_value( 'twitter', $post->ID );
if ( ! is_array( $twitter ) ) {
	$twitter = array();
}
$twitter = wp_parse_args( $twitter, array(
	'title'       => false,
	'description' => false,
	'images'      => false,
	'disabled'    => false,
) );

$this->_render( 'metabox/metabox-social-meta-tags', array(
	'main_title'              => __( 'Twitter', 'wds' ),
	'main_description'        => __( 'These details will be used in Twitter cards.', 'wds' ),
	'field_name'              => 'wds-twitter',
	'disabled'                => (bool) smartcrawl_get_array_value( $twitter, 'disabled' ),
	'current_title'           => $twitter['title'],
	'title_placeholder'       => $twitter_printer->get_title_content(),
	'current_description'     => $twitter['description'],
	'description_placeholder' => $twitter_printer->get_description_content(),
	'images'                  => $twitter['images'],
	'single_image'            => true,
) );

$resolver->stop_simulation();
