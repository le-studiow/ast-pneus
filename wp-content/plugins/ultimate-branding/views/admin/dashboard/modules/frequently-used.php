<div id="branda-dashboard-widget-frequently-used" class="sui-box sui-box-close">
	<div class="sui-box-header">
		<h3 class="sui-box-title"><i class="sui-icon-clock" aria-hidden="true"></i><?php esc_html_e( 'Frequently Used', 'ub' ); ?></h3>
	</div>
	<div class="sui-box-body">
		<p><?php esc_attr_e( 'You can find your top 5 frequently used modules here.', 'ub' ); ?></p>
	</div>
<?php
if ( empty( $modules ) ) {
?>
	<div class="sui-box-body">
		<div class="sui-notice">
			<p><?php esc_html_e( 'We don\'t have enough data at the moment. As you begin interacting with the plugin, we\'ll start collecting data and show your frequently used modules here.', 'ub' ); ?></p>
		</div>
	</div>
<?php
} else { ?>
	<table class="sui-table sui-table-flushed">
		<thead>
			<tr>
				<th class="sui-table--name"><?php esc_attr_e( 'Module', 'ub' ); ?></th>
				<th class="sui-table--status"><?php esc_attr_e( 'Status', 'ub' ); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
    foreach ( $modules as $id => $module ) {
        if ( ! isset( $module['name'] ) ) {
            continue;
        }
        $this->render( 'admin/dashboard/modules/one-row', array( 'id' => $id, 'module' => $module ) );
    }
?>
		</tbody>
	</table>
<?php } ?>
</div>
