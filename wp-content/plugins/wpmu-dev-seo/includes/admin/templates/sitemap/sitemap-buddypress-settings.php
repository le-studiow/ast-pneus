<?php
$groups_enabled = ! empty( $_view['options']['sitemap-buddypress-groups'] );
$profiles_enabled = ! empty( $_view['options']['sitemap-buddypress-profiles'] );

$this->_render( 'sitemap/sitemap-part', array(
	'item'        => 'sitemap-buddypress-groups',
	'item_name'   => '',
	'item_label'  => esc_html__( 'BuddyPress Groups', 'wds' ),
	'option_name' => $_view['option_name'] . '[sitemap-buddypress-groups]',
	'class'       => 'wds-sitemap-toggleable wds-sitemap-buddypress-groups',
) ); ?>

<tr>
	<td colspan="3" class="wds-nested-sitemap-parts">
		<table class="sui-table wds-sitemap-parts">
			<?php if ( ! empty( $exclude_groups ) ) {
				foreach ( $exclude_groups as $exclude_bp_group => $exclude_bp_group_label ) {
					$this->_render( 'sitemap/sitemap-part', array(
						'item'        => 'sitemap-buddypress-' . $exclude_bp_group,
						'item_name'   => '',
						'item_label'  => $exclude_bp_group_label,
						'option_name' => $_view['option_name'] . '[exclude_bp_groups][]',
						'inverted'    => true,
					) );
				}
			} ?>
		</table>
	</td>
</tr>

<?php $this->_render( 'sitemap/sitemap-part', array(
	'item'        => 'sitemap-buddypress-profiles',
	'item_name'   => '',
	'item_label'  => esc_html__( 'BuddyPress Profiles', 'wds' ),
	'option_name' => $_view['option_name'] . '[sitemap-buddypress-profiles]',
	'class'       => 'wds-sitemap-toggleable wds-sitemap-buddypress-profiles',
) ); ?>

<tr>
	<td colspan="3" class="wds-nested-sitemap-parts">
		<table class="sui-table wds-sitemap-parts">
			<?php if ( ! empty( $exclude_roles ) ) {
				foreach ( $exclude_roles as $exclude_bp_role => $exclude_bp_role_label ) {
					$this->_render( 'sitemap/sitemap-part', array(
						'item'        => 'sitemap-buddypress-roles-' . $exclude_bp_role,
						'item_name'   => '',
						'item_label'  => $exclude_bp_role_label,
						'option_name' => $_view['option_name'] . '[exclude_bp_roles][]',
						'inverted'    => true,
					) );
				}
			} ?>
		</table>
	</td>
</tr>
