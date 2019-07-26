<?php
$term = empty( $term ) ? null : $term;
if ( ! $term ) {
	return;
}

$link = get_term_link( $term );
$resolver = Smartcrawl_Endpoint_Resolver::resolve();
$resolver->simulate_taxonomy_term( $term );
$title = Smartcrawl_Meta_Value_Helper::get()->get_title();
$description = Smartcrawl_Meta_Value_Helper::get()->get_description();
$resolver->stop_simulation();
?>
<div class="wds-metabox-preview">
	<label class="sui-label"><?php esc_html_e( 'Google Preview' ); ?></label>

	<?php
	$this->_render( 'onpage/onpage-preview', array(
		'link'        => esc_url( $link ),
		'title'       => esc_html( $title ),
		'description' => esc_html( $description ),
	) );
	?>
</div>
