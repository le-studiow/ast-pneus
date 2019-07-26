<div class="wds-separator-top">
	<?php
	$this->_render( 'toggle-group', array(
		'label'       => __( 'OpenGraph Support', 'wds' ),
		'description' => __( 'This will add a few extra meta tags to the head section of your pages.', 'wds' ),
		'items'       => array(
			'og-enable' => array(
				'label'       => __( 'Enable OpenGraph', 'wds' ),
				'description' => __( 'By default OpenGraph will use your default titles, descriptions and feature images. You can override the default on a per post basis inside the post editor, as well as under Titles & Meta for specific post types.', 'wds' ),
			),
		),
	) );
	?>
</div>
