<?php
$seo_sections = empty( $seo_sections ) ? array() : $seo_sections;
if ( empty( $seo_sections ) ) {
	return;
}
?>

<div class="wds-form">
	<?php foreach ( $seo_sections as $template => $args ) {
		$this->_render( $template, $args );
	} ?>
</div>
