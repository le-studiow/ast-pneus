<?php
$macros = empty( $macros ) ? array() : $macros;
$active_tab = empty( $active_tab ) ? '' : $active_tab;
$buddypress_active = defined( 'BP_VERSION' );
?>

<?php $this->_render( 'before-page-container' ); ?>

<div id="container" class="sui-wrap wrap wrap-wds wds-page wds-page-onpage">
	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'Title & Meta', 'wds' ),
		'documentation_chapter' => 'title-meta',
		'extra_actions'         => 'onpage/onpage-header-actions',
	) ); ?>

	<?php
	$this->_render( 'modal', array(
		'id'                 => 'wds-supported-macros-modal',
		'title'              => esc_html__( 'Supported Macros', 'wds' ),
		'body_template'      => 'onpage/onpage-macros-modal',
		'body_template_args' => array(
			'macros' => $macros,
		),
		'small'              => true,
	) );
	?>

	<form action='<?php echo esc_attr( $_view['action_url'] ); ?>' method='post' class="wds-form">
		<?php $this->settings_fields( $_view['option_name'] ); ?>

		<input type="hidden"
		       name='<?php echo esc_attr( $_view['option_name'] ); ?>[<?php echo esc_attr( $_view['slug'] ); ?>-setup]'
		       value="1">

		<div class="wds-vertical-tabs-container sui-row-with-sidenav" id="page-title-meta-tabs">
			<?php $this->_render( 'onpage/onpage-sidenav', array( 'active_tab' => $active_tab ) ); ?>

			<?php
			/*
			 * Homepage tab
			 */
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_homepage',
				'tab_name'     => esc_html__( 'Homepage', 'wds' ),
				'is_active'    => 'tab_homepage' === $active_tab,
				'button_text'  => $show_homepage_options ? esc_html__( 'Save Settings', 'wds' ) : false,
				'tab_sections' => array(
					array(
						'section_description' => esc_html__( 'Customize your homepage title, description and meta options.', 'wds' ),
						'section_type'        => $show_homepage_options ? 'homepage' : 'static-homepage',
						'section_template'    => 'onpage/onpage-section-homepage',
						'section_args'        => array(
							'show_homepage_options'         => $show_homepage_options,
							'meta_robots_main_blog_archive' => $meta_robots_main_blog_archive,
						),
					),
				),
			) );

			/*
			 * Post types tab
			 */
			$post_type_sections = array();
			foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
				if ( in_array( $post_type, array( 'revision', 'nav_menu_item' ), true ) ) {
					continue;
				}

				$post_type_object = get_post_type_object( $post_type );

				$post_type_sections[] = array(
					'section_title'       => $post_type_object->labels->name,
					'section_description' => sprintf( esc_html__( 'Customize your %s title, description and meta options.', 'wds' ), strtolower( $post_type_object->labels->singular_name ) ),
					'section_type'        => $post_type,
					'section_template'    => 'onpage/onpage-section-post-type',
					'section_args'        => array(
						'post_type'        => $post_type,
						'post_type_object' => $post_type_object,
						'post_type_robots' => ( ! empty( $post_robots[ $post_type ] ) ? $post_robots[ $post_type ] : array() ),
					),
				);
			}

			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_post_types',
				'tab_name'     => esc_html__( 'Post Types', 'wds' ),
				'is_active'    => 'tab_post_types' === $active_tab,
				'tab_sections' => $post_type_sections,
			) );

			/*
			 * Other taxonomies
			 */
			$taxonomy_sections = array();
			$taxonomies = array_merge(
				array( 'post_tag', 'category' ),
				get_taxonomies( array( '_builtin' => false, 'public' => true ) )
			);
			foreach ( $taxonomies as $taxonomy_name ) {
				$taxonomy = get_taxonomy( $taxonomy_name );
				$meta_robots_taxonomy_name = 'meta_robots_' . str_replace( '-', '_', $taxonomy->name );
				$taxonomy_sections[] = array(
					'section_title'       => $taxonomy->label,
					'section_description' => sprintf( esc_html__( 'Customize the title, description and meta options for %s.', 'wds' ), strtolower( $taxonomy->label ) ),
					'section_type'        => $taxonomy->name,
					'section_template'    => 'onpage/onpage-section-taxonomy',
					'section_args'        => array(
						'taxonomy'    => $taxonomy,
						'meta_robots' => $$meta_robots_taxonomy_name,
					),
				);
			}

			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_taxonomies',
				'tab_name'     => esc_html__( 'Taxonomies', 'wds' ),
				'is_active'    => 'tab_taxonomies' === $active_tab,
				'tab_sections' => $taxonomy_sections,
			) );

			$archive_sections = array(
				// Author archive
				array(
					'section_title'          => esc_html__( 'Author Archive', 'wds' ),
					'section_description'    => esc_html__( 'If you are the only author of your website content Google may see your author archives as duplicate content to your Blog Homepage. If this is the case we recommend disabling author archives.', 'wds' ),
					'section_type'           => 'author-archive',
					'section_template'       => 'onpage/onpage-section-author-archive',
					'section_enabled_option' => 'enable-author-archive',
					'section_toggle_tooltip' => esc_html__( 'Enable/Disable author archives depending on whether you require them or not' ),
					'section_args'           => array(
						'meta_robots_author' => $meta_robots_author,
					),
				),
				// Date archive
				array(
					'section_title'          => esc_html__( 'Date Archive', 'wds' ),
					'section_description'    => esc_html__( 'Google may see your date archives as duplicate content to your Blog Homepage. For this reason we recommend disabling date archives.', 'wds' ),
					'section_type'           => 'date-archive',
					'section_template'       => 'onpage/onpage-section-date-archive',
					'section_enabled_option' => 'enable-date-archive',
					'section_toggle_tooltip' => esc_html__( 'Enable/Disable date archives depending on whether you require them or not' ),
					'section_args'           => array(
						'meta_robots_date' => $meta_robots_date,
					),
				),
				// Search page
				array(
					'section_title'       => esc_html__( 'Search Page', 'wds' ),
					'section_description' => esc_html__( 'Customize your search page title, description and meta options.', 'wds' ),
					'section_type'        => 'search-page',
					'section_template'    => 'onpage/onpage-section-search',
					'section_args'        => array(
						'meta_robots_search' => $meta_robots_search,
					),
				),
				// 404 page
				array(
					'section_title'       => esc_html__( '404 Page', 'wds' ),
					'section_description' => esc_html__( 'Customize your 404 page title, description and meta options.', 'wds' ),
					'section_type'        => '404-page',
					'section_template'    => 'onpage/onpage-section-404',
					'section_args'        => array(),
				),
			);

			/**
			 * @var $archive_post_types array
			 */
			foreach ( $archive_post_types as $archive_post_type => $archive_post_type_label ) {

				$archive_sections[] = array(
					'section_title'       => $archive_post_type_label . esc_html__( ' Archive', 'wds' ),
					'section_description' => sprintf(
						esc_html__( 'Customize title, description and meta for the archive page of custom post type %s.', 'wds' ),
						strtolower( $archive_post_type_label )
					),
					'section_type'        => $archive_post_type,
					'section_template'    => 'onpage/onpage-section-post-type-archive',
					'section_args'        => array(
						'archive_post_type'        => $archive_post_type,
						'archive_post_type_label'  => $archive_post_type_label,
						'archive_post_type_robots' => ( ! empty( $archive_post_type_robots[ $archive_post_type ] ) ? $archive_post_type_robots[ $archive_post_type ] : array() ),
					),
				);
			}

			/*
			 * Archives
			 */
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_archives',
				'tab_name'     => esc_html__( 'Archives', 'wds' ),
				'is_active'    => 'tab_archives' === $active_tab,
				'tab_sections' => $archive_sections,
			) );

			$buddypress_sections = array();

			if ( function_exists( 'groups_get_groups' ) && ( is_network_admin() || is_main_site() ) ) {
				$buddypress_sections[] = array(
					'section_title'       => esc_html__( 'BuddyPress Groups', 'wds' ),
					'section_description' => esc_html__( 'Customize your BuddyPress group title, description and meta options.', 'wds' ),
					'section_type'        => 'bp-group',
					'section_template'    => 'onpage/onpage-section-buddypress-groups',
					'section_args'        => array(
						'meta_robots_bp_groups' => $meta_robots_bp_groups,
					),
				);
			}

			if ( $buddypress_active && ( is_network_admin() || is_main_site() ) ) {
				$buddypress_sections[] = array(
					'section_title'       => esc_html__( 'BuddyPress Profile', 'wds' ),
					'section_description' => esc_html__( 'Customize your BuddyPress profile title, description and meta options.', 'wds' ),
					'section_type'        => 'bp-profile',
					'section_template'    => 'onpage/onpage-section-buddypress-profile',
					'section_args'        => array(
						'meta_robots_bp_profile' => $meta_robots_bp_profile,
					),
				);
			}

			if ( $buddypress_sections ) {
				$this->_render( 'vertical-tab', array(
					'tab_id'       => 'tab_buddypress',
					'tab_name'     => esc_html__( 'BuddyPress', 'wds' ),
					'is_active'    => 'tab_buddypress' === $active_tab,
					'tab_sections' => $buddypress_sections,
				) );
			}
			?>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_settings',
				'tab_name'     => esc_html__( 'Settings', 'wds' ),
				'is_active'    => 'tab_settings' === $active_tab,
				'tab_sections' => array(
					array(
						'section_type'     => '',
						'section_template' => 'onpage/onpage-section-settings',
						'section_args'     => array(
							'separators' => $separators,
						),
					),
				),
			) );
			?>

		</div><!-- end page-title-meta-tabs -->

	</form>

	<?php $this->_render( 'footer' ); ?>
</div><!-- end wds-page-onpage -->
