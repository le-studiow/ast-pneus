<?php
$status = __( 'Inactive', 'ub' );
if ( 'active' === $module['status'] ) {
	$status = __( 'Active', 'ub' );
}
$url = add_query_arg(
	array(
		'page' => sprintf( 'branding_group_%s', $module['group'] ),
		'module' => $module['module'],
	),
	is_network_admin()? network_admin_url( 'admin.php' ):admin_url( 'admin.php' )
);
?>
<tr data-id="<?php echo esc_attr( $module['module'] ); ?>">
    <td class="sui-table--name sui-table-item-title"><?php echo esc_attr( $module['name'] ); ?></td>
    <td class="sui-table--status">
        <span class="branda-module-status sui-tooltip module-status-<?php echo esc_attr( $module['status'] ); ?>" data-tooltip="<?php echo esc_attr( $status ); ?>"></span>
        <a href="<?php echo esc_url( $url ); ?>" class="sui-button-icon sui-tooltip sui-tooltip-top-right-mobile" data-tooltip="<?php esc_attr_e( 'Edit Module', 'ub' ); ?>">
        <i class="sui-icon-pencil" aria-hidden="true"></i>
        </a>
    </td>
</tr>

