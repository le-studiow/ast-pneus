<?php
$items = empty( $items ) ? array() : $items;

if ( ! $items ) {
	return;
}
?>

<?php
$this->_render( 'toggle-group', array(
	'label'       => esc_html__( 'Indexing', 'wds' ),
	'description' => esc_html__( 'Choose whether you want your website to appear in search results.', 'wds' ),
	'separator'   => true,
	'items'       => $items,
) );

