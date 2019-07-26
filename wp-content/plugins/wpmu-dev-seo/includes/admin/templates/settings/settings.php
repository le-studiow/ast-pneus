<?php $this->_render( 'before-page-container' ); ?>
<div id="container" class="sui-wrap wrap wrap-wds wds-page wds-page-settings">

	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'SmartCrawl Settings', 'wds' ),
		'documentation_chapter' => 'settings',
	) ); ?>

	<div class="wds-vertical-tabs-container sui-row-with-sidenav">
		<?php $this->_render( 'vertical-tabs-side-nav', array(
			'active_tab' => $active_tab,
			'tabs'       => array(
				array(
					'id'   => 'tab_general_settings',
					'name' => esc_html__( 'General Settings', 'wds' ),
				),
				array(
					'id'   => 'tab_user_roles',
					'name' => esc_html__( 'User Roles', 'wds' ),
				),
				array(
					'id'   => 'tab_import_export',
					'name' => esc_html__( 'Import / Export', 'wds' ),
				),
			),
		) ); ?>

		<form action='<?php echo esc_attr( $_view['action_url'] ); ?>' method='post' class="wds-form">
			<?php $this->settings_fields( $_view['option_name'] ); ?>

			<input type="hidden"
			       name='<?php echo esc_attr( $_view['option_name'] ); ?>[<?php echo esc_attr( $_view['slug'] ); ?>-setup]'
			       value="1"/>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_general_settings',
				'tab_name'     => __( 'General Settings', 'wds' ),
				'is_active'    => 'tab_general_settings' === $active_tab,
				'tab_sections' => array(
					array(
						'section_template' => 'settings/settings-section-general',
						'section_args'     => array(
							'verification_pages'  => $verification_pages,
							'sitemap_option_name' => $sitemap_option_name,
							'slugs'               => $slugs,
							'wds_sitewide_mode'   => $wds_sitewide_mode,
							'blog_tabs'           => $blog_tabs,
							'plugin_modules'      => $plugin_modules,
						),
					),
				),
			) );

			$this->_render( 'vertical-tab', array(
				'tab_id'        => 'tab_user_roles',
				'tab_name'      => __( 'User Roles', 'wds' ),
				'is_active'     => 'tab_user_roles' === $active_tab,
				'before_output' => $this->_load( '_forms/settings' ),
				'after_output'  => '</form>',
				'tab_sections'  => array(
					array(
						'section_template' => 'settings/settings-section-user-roles',
						'section_args'     => array(
							'seo_metabox_permission_level'        => $seo_metabox_permission_level,
							'seo_metabox_301_permission_level'    => $seo_metabox_301_permission_level,
							'urlmetrics_metabox_permission_level' => $urlmetrics_metabox_permission_level,
						),
					),
				),
			) );
			?>
		</form>

		<form method='post' enctype="multipart/form-data" class="wds-form">
			<?php $this->settings_fields( $_view['option_name'] ); ?>

			<input type="hidden"
			       name='<?php echo esc_attr( $_view['option_name'] ); ?>[<?php echo esc_attr( $_view['slug'] ); ?>-setup]'
			       value="1"/>
			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_import_export',
				'tab_name'     => __( 'Import / Export', 'wds' ),
				'is_active'    => 'tab_import_export' === $active_tab,
				'button_text'  => false,
				'tab_sections' => array(
					array(
						'section_template' => 'settings/settings-section-import-export',
					),
				),
			) );
			?>
		</form>
	</div>

	<?php $this->_render( 'footer' ); ?>
</div>
