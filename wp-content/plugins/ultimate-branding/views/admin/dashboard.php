<?php
$link = add_query_arg(
	array(
		'page' => 'branding',
		'branda' => 'manage-all-modules',
	)
);
$this->render( 'admin/common/header', array( 'title' => __( 'Dashboard', 'ub' ) ) );
echo '<section id="sui-branda-content" class="sui-container">';
$args = array(
	'stats' => $stats,
	'modules' => $modules,
	'sui' => $sui,
);
$this->render( 'admin/dashboard/widget-summary', $args );
$this->render( 'admin/dashboard/widget-modules', $args );
echo '</section>';

