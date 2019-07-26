<?php
$groups = branda_get_groups_list();
?>
<div id="branda-dashboard-widget-modules" class="sui-row">
	<div class="sui-col-sm-12 sui-col-md-6">
<?php
$this->render( 'admin/dashboard/modules/frequently-used', $stats );
$set = array( 'widgets', 'front-end' );
foreach ( $set as $one ) {
	$args = array(
		'group' => $one,
		'data' => $modules[ $one ],
		'info' => $groups[ $one ],
	);
	$this->render( 'admin/dashboard/modules/group', $args );
}
$args = array(
	'group' => 'data',
	'data' => $modules['data'],
	'info' => $groups['data'],
	'stats' => $stats,
);

$this->render( 'admin/dashboard/modules/import-export', $args );
?>
	</div>
	<div class="sui-col-sm-12 sui-col-md-6">
<?php
$set = array( 'admin', 'emails', 'utilities' );
foreach ( $set as $one ) {
	$args = array(
		'group' => $one,
		'data' => $modules[ $one ],
		'info' => $groups[ $one ],
	);
	$this->render( 'admin/dashboard/modules/group', $args );
}
?>
	</div>
</div><?php // #sui-dashboard-widget-modules ?>
<div class="sui-box hidden" id="branda-dashboard-search-no-results">
	<div class="branda-empty">
		<h2 class="branda-image branda-image-confused">
	<?php _ex( 'No results for "<span></span>"', '&lt;span&gt; tag is the placeholder for searching string.', 'ub' ); ?></h2>
		<p><?php _e( 'We couldn\'t find any modules matching your search. Perhaps try again?', 'ub' ); ?></p>
	</div>
</div>
<?php
if ( ! Branda_Helper::is_member() ) {
	$this->render( 'admin/dashboard/footer-free' );
}
?>
