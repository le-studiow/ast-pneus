<?php

class Smartcrawl_AIOSEOP_Importer extends Smartcrawl_Importer {
	const IMPORT_IN_PROGRESS_FLAG = 'wds-aioseop-import-in-progress';
	const NETWORK_IMPORT_SITES_PROCESSED_COUNT = 'wds-aioseop-network-sites-processed';
	const AIOSEOP_OPTIONS_ID = 'aioseop_options';
	// phpcs:disable -- PHPCS complains about whitespaces here
	private $conditions = array(
		'_aioseop_custom_link'                                                        => 'canonical_links_enabled',
		'_aioseop_keywords'                                                           => 'keywords_enabled',
		'aioseop_options/aiosp_([a-z0-9_]+)_tax_title_format'                         => 'rewrite_titles_enabled',
		'aioseop_options/aiosp_([a-z0-9_]+)_title_format'                             => 'rewrite_titles_enabled',
		'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_hometitle'   => 'home_og_fields_enabled',
		'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_description' => 'home_og_fields_enabled',
	);

	// phpcs:enable

	public function data_exists() {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$version = smartcrawl_get_array_value( $options, 'last_active_version' );

		if ( ! $version ) {
			return false;
		}

		return apply_filters(
			'wds-import-aioseop-data-exists',
			strpos( $version, '2.12' ) === 0
		);
	}

	public function import_options() {
		$mappings = $this->expand_mappings( $this->load_option_mappings() );
		$source_options = $this->get_source_options();
		$target_options = array();

		foreach ( $source_options as $source_key => $source_value ) {
			$use_mapped_value = true;
			if ( ! $this->meets_condition( $source_key ) ) {
				$use_mapped_value = false;
			}

			$target_key = $this->get_target_key( $mappings, $source_key );
			if ( ! $target_key ) {
				$use_mapped_value = false;
			}

			$processed_target_key = $this->pre_process_key( $target_key );
			$processed_target_value = $this->pre_process_value( $target_key, $source_value );

			if ( ! $processed_target_key ) {
				$use_mapped_value = false;
			}

			if ( $use_mapped_value ) {
				smartcrawl_put_array_value(
					$processed_target_value,
					$target_options,
					$processed_target_key
				);
			} else {
				$target_options = $this->try_custom_handlers( $source_key, $source_value, $target_options );
			}
		}

		$target_options = $this->save_sitemap_posttypes( $target_options );
		$target_options = $this->save_sitemap_taxonomies( $target_options );
		$target_options = $this->save_social_types( $target_options );

		$this->save_options( $target_options );
	}

	private function load_option_mappings() {
		return $this->load_mapping_file( 'aioseop-mappings.php' );
	}

	private function get_source_options() {
		$processed_options = array();

		return $this->populate_option_array(
			get_option( self::AIOSEOP_OPTIONS_ID, array() ),
			$processed_options,
			self::AIOSEOP_OPTIONS_ID
		);
	}

	private function populate_option_array( $array_value, $array, $array_key, $level = 0 ) {
		if ( is_array( $array_value ) && ! $this->is_numeric_array( $array_value ) && $level < 3 ) {
			$level ++;
			foreach ( $array_value as $key => $value ) {
				$array = $this->populate_option_array( $value, $array, $array_key . '/' . $key, $level );
			}
		} else {
			smartcrawl_put_array_value( $array_value, $array, $array_key );
		}

		return $array;
	}

	private function is_numeric_array( $array ) {
		return array_keys( $array ) === range( 0, count( $array ) - 1 );
	}

	private function meets_condition( $key ) {
		$condition = null;
		$matches = array();
		foreach ( $this->conditions as $condition_pattern => $callback ) {
			if ( preg_match( '#' . $condition_pattern . '#', $key, $matches ) ) {
				$condition = $callback;
				break;
			}
		}
		if ( ! $condition ) {
			return true;
		}

		return call_user_func_array( array( $this, $condition ), array( $key, $matches ) );
	}

	private function get_target_key( $mappings, $source_key ) {
		$target_key = smartcrawl_get_array_value( $mappings, $source_key );
		if ( null !== $target_key ) {
			return $target_key;
		}

		foreach ( $mappings as $pattern => $target_key ) {
			if ( preg_match( '#^' . $pattern . '$#', $source_key ) ) {
				return $target_key;
			}
		}

		return null;
	}

	public function save_sitemap_posttypes( $target_options ) {
		$source_options = get_option( self::AIOSEOP_OPTIONS_ID );
		$all_post_types = $this->get_post_types();
		$source_post_types = smartcrawl_get_array_value( $source_options, array(
			'modules',
			'aiosp_sitemap_options',
			'aiosp_sitemap_posttypes',
		) );
		$source_post_types = null === $source_post_types ? $all_post_types : $source_post_types;

		$excluded_post_types = array_diff( $all_post_types, $source_post_types );

		foreach ( $excluded_post_types as $excluded_post_type ) {
			smartcrawl_put_array_value( true, $target_options, array(
				'wds_sitemap_options',
				sprintf( 'post_types-%s-not_in_sitemap', $excluded_post_type ),
			) );
		}

		return $target_options;
	}

	public function save_sitemap_taxonomies( $target_options ) {
		$source_options = get_option( self::AIOSEOP_OPTIONS_ID );
		$all_taxonomies = $this->get_taxonomies();
		$source_taxonomies = smartcrawl_get_array_value( $source_options, array(
			'modules',
			'aiosp_sitemap_options',
			'aiosp_sitemap_taxonomies',
		) );
		$source_taxonomies = null === $source_taxonomies ? $all_taxonomies : $source_taxonomies;

		$excluded_post_types = array_diff( $all_taxonomies, $source_taxonomies );

		foreach ( $excluded_post_types as $excluded_post_type ) {
			smartcrawl_put_array_value( true, $target_options, array(
				'wds_sitemap_options',
				sprintf( 'taxonomies-%s-not_in_sitemap', $excluded_post_type ),
			) );
		}

		return $target_options;
	}

	private function save_social_types( $target_options ) {
		$default_post_types = array( 'post', 'page' );
		$source_post_types = smartcrawl_get_array_value( get_option( self::AIOSEOP_OPTIONS_ID, array() ), array(
			'modules',
			'aiosp_opengraph_options',
			'aiosp_opengraph_types',
		) );
		$source_post_types = null === $source_post_types ? $default_post_types : $source_post_types;

		// Activate twitter
		smartcrawl_put_array_value( true, $target_options, array( 'wds_social_options', 'twitter-card-enable' ) );

		// Activate for post types
		foreach ( $source_post_types as $og_post_type ) {
			$target_options = $this->enable_social_for( $og_post_type, $target_options );
		}

		foreach ( $this->get_taxonomies() as $taxonomy ) {
			if ( $this->enabled_for_taxonomy( $taxonomy ) ) {
				$target_options = $this->enable_social_for( $taxonomy, $target_options );
			}
		}

		$target_options = $this->enable_social_for( 'home', $target_options );

		return $target_options;
	}

	private function enable_social_for( $type, $target_options ) {
		smartcrawl_put_array_value( true, $target_options, array(
			'wds_onpage_options',
			sprintf( 'og-active-%s', $type ),
		) );
		smartcrawl_put_array_value( true, $target_options, array(
			'wds_onpage_options',
			sprintf( 'twitter-active-%s', $type ),
		) );

		return $target_options;
	}

	private function enabled_for_taxonomy( $taxonomy ) {
		$default_types = array( 'category', 'post_tag', 'tag' );
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$seo_for_custom_types = (bool) smartcrawl_get_array_value( $options, 'aiosp_enablecpost' );
		$active_types = smartcrawl_get_array_value( $options, 'aiosp_taxactive' );

		if ( $seo_for_custom_types ) {
			return is_array( $active_types ) && in_array( $taxonomy, $active_types, true );
		} else {
			return in_array( $taxonomy, $default_types, true );
		}
	}

	public function import_taxonomy_meta() {
		$term_ids = $this->get_terms_with_aioseop_metas();
		$wds_meta = array();
		foreach ( $term_ids as $term_id ) {
			if ( ! $this->enabled_for_term( $term_id ) ) {
				continue;
			}

			$term_object = get_term( $term_id );
			$taxonomy_meta = array();

			$taxonomy_meta = $this->import_term_meta_text( '_aioseop_title', 'wds_title', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_text( '_aioseop_description', 'wds_desc', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_text( '_aioseop_custom_link', 'wds_canonical', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_text( '_aioseop_keywords', 'wds_keywords', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_boolean( '_aioseop_noindex', 'wds_noindex', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_boolean( '_aioseop_nofollow', 'wds_nofollow', $term_id, $taxonomy_meta );
			$taxonomy_meta = $this->import_term_meta_opengraph( $term_id, $taxonomy_meta );

			smartcrawl_put_array_value( $taxonomy_meta, $wds_meta, array( $term_object->taxonomy, $term_id ) );
		}
		update_option( 'wds_taxonomy_meta', $wds_meta );
	}

	private function get_terms_with_aioseop_metas() {
		global $wpdb;

		return $wpdb->get_col( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key LIKE '_aioseop_%' GROUP BY term_id" );
	}

	private function enabled_for_term( $term_id ) {
		$term = get_term( $term_id );
		$enabled_for_term = 'on' !== get_term_meta( $term_id, '_aioseop_disable', true );
		$enabled_for_taxonomy = $this->enabled_for_taxonomy( $term->taxonomy );

		return $enabled_for_term && $enabled_for_taxonomy;
	}

	private function import_term_meta_text( $source_key, $target_key, $term_id, $taxonomy_meta ) {
		if ( $this->meets_condition( $source_key ) ) {
			$meta_value = get_term_meta( $term_id, $source_key, true );
			$taxonomy_meta[ $target_key ] = $meta_value;
		}

		return $taxonomy_meta;
	}

	private function import_term_meta_boolean( $source_key, $target_key, $term_id, $taxonomy_meta ) {
		if ( $this->meets_condition( $source_key ) ) {
			$meta_value = get_term_meta( $term_id, $source_key, true );
			$meta_value = 'on' === $meta_value ? true : false;
			$taxonomy_meta[ $target_key ] = $meta_value;
		}

		return $taxonomy_meta;
	}

	private function import_term_meta_opengraph( $term_id, $taxonomy_meta ) {
		$source_values = get_term_meta( $term_id, '_aioseop_opengraph_settings', true );
		if ( empty( $source_values ) ) {
			return $taxonomy_meta;
		}

		$wds_values = $this->populate_opengraph_values( $source_values, 'opengraph', 'twitter' );
		foreach ( $wds_values as $meta_key => $meta_value ) {
			$taxonomy_meta[ $meta_key ] = $meta_value;
		}

		return $taxonomy_meta;
	}

	private function populate_opengraph_values( $meta_values, $opengraph_key = '_wds_opengraph', $twitter_key = '_wds_twitter' ) {
		$wds_values = array();
		$title = smartcrawl_get_array_value( $meta_values, 'aioseop_opengraph_settings_title' );
		$description = smartcrawl_get_array_value( $meta_values, 'aioseop_opengraph_settings_desc' );
		$image = smartcrawl_get_array_value( $meta_values, 'aioseop_opengraph_settings_customimg' );
		$twitter_image = smartcrawl_get_array_value( $meta_values, 'aioseop_opengraph_settings_customimg_twitter' );

		smartcrawl_put_array_value( $title, $wds_values, array( $opengraph_key, 'title' ) );
		smartcrawl_put_array_value( $description, $wds_values, array( $opengraph_key, 'description' ) );
		if ( $image ) {
			smartcrawl_put_array_value( array( $image ), $wds_values, array( $opengraph_key, 'images' ) );
		}

		smartcrawl_put_array_value( $title, $wds_values, array( $twitter_key, 'title' ) );
		smartcrawl_put_array_value( $description, $wds_values, array( $twitter_key, 'description' ) );
		if ( $twitter_image ) {
			smartcrawl_put_array_value( array( $twitter_image ), $wds_values, array( $twitter_key, 'images' ) );
		}

		return $wds_values;
	}

	public function import_post_meta() {
		$batch_size = apply_filters( 'wds_post_meta_import_batch_size', 300 );
		$all_posts = $this->get_posts_with_aioseop_metas();
		$batch_posts = array_slice( $all_posts, 0, $batch_size );

		foreach ( $batch_posts as $post_id ) {
			if ( ! $this->enabled_for_post( $post_id ) ) {
				continue;
			}

			$this->import_post_meta_text( '_aioseop_title', '_wds_title', $post_id );
			$this->import_post_meta_text( '_aioseop_description', '_wds_metadesc', $post_id );
			$this->import_post_meta_text( '_aioseop_custom_link', '_wds_canonical', $post_id );
			$this->import_post_meta_text( '_aioseop_keywords', '_wds_keywords', $post_id );
			$this->import_post_meta_no_index( $post_id );
			$this->import_post_meta_no_follow( $post_id );
			$this->import_post_meta_opengraph( $post_id );
		}

		$this->update_status( array(
			'remaining_posts' => count( $this->get_posts_with_aioseop_metas() ),
			'completed_posts' => count( $this->get_posts_with_target_metas() ),
		) );

		return count( $all_posts ) === count( $batch_posts );
	}

	private function get_posts_with_aioseop_metas() {
		return $this->get_posts_with_source_metas( '_aioseop_' );
	}

	private function enabled_for_post( $post_id ) {
		$enabled_for_post = 'on' !== get_post_meta( $post_id, '_aioseop_disable', true );
		$enabled_for_post_type = $this->enabled_for_post_type( get_post_type( $post_id ) );

		return $enabled_for_post && $enabled_for_post_type;
	}

	private function enabled_for_post_type( $post_type ) {
		$default_types = array( 'post', 'page' );
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$seo_for_custom_types = (bool) smartcrawl_get_array_value( $options, 'aiosp_enablecpost' );
		$active_types = smartcrawl_get_array_value( $options, 'aiosp_cpostactive' );

		if ( $seo_for_custom_types ) {
			return is_array( $active_types ) && in_array( $post_type, $active_types, true );
		} else {
			return in_array( $post_type, $default_types, true );
		}
	}

	private function import_post_meta_text( $source_key, $target_key, $post_id ) {
		if ( ! $this->meets_condition( $source_key ) ) {
			return;
		}

		$meta_value = get_post_meta( $post_id, $source_key, true );
		update_post_meta( $post_id, $target_key, $meta_value );
	}

	private function import_post_meta_no_index( $post_id ) {
		$source_meta_value = get_post_meta( $post_id, '_aioseop_noindex', true );
		if ( 'on' === $source_meta_value ) {
			update_post_meta( $post_id, '_wds_meta-robots-noindex', true );
		} elseif ( 'off' === $source_meta_value ) {
			update_post_meta( $post_id, '_wds_meta-robots-index', true );
		}
	}

	private function import_post_meta_no_follow( $post_id ) {
		$source_meta_value = get_post_meta( $post_id, '_aioseop_nofollow', true );
		if ( 'on' === $source_meta_value ) {
			update_post_meta( $post_id, '_wds_meta-robots-nofollow', true );
		} elseif ( 'off' === $source_meta_value ) {
			update_post_meta( $post_id, '_wds_meta-robots-follow', true );
		}
	}

	private function import_post_meta_opengraph( $post_id ) {
		$source_values = get_post_meta( $post_id, '_aioseop_opengraph_settings', true );
		if ( empty( $source_values ) || ! $this->opengraph_enabled_for_post_type( get_post_type( $post_id ) ) ) {
			return;
		}

		$wds_values = $this->populate_opengraph_values( $source_values );
		foreach ( $wds_values as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	private function opengraph_enabled_for_post_type( $post_type ) {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$og_types = smartcrawl_get_array_value( $options, array(
			'modules',
			'aiosp_opengraph_options',
			'aiosp_opengraph_types',
		) );
		$default_types = array( 'post', 'page' );

		if ( null !== $og_types ) {
			return is_array( $og_types ) && in_array( $post_type, $og_types, true );
		} else {
			return in_array( $post_type, $default_types, true );
		}
	}

	public function get_custom_handlers() {
		// phpcs:disable -- PHPCS complains about whitespaces here
		return array(
			'aioseop_options/modules/aiosp_sitemap_options/aiosp_sitemap_excl_pages'        => 'save_excluded_pages',
			'aioseop_options/modules/aiosp_sitemap_options/aiosp_sitemap_addl_pages'        => 'save_extra_urls',
			'aioseop_options/aiosp_tax_noindex'                                             => 'save_tax_noindex_values',
			'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_social_name'   => 'save_person_or_organization_name',
			'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_profile_links' => 'save_social_profile_links',
			'aioseop_options/aiosp_cpostnoindex'                                            => 'save_post_type_noindex',
			'aioseop_options/aiosp_cpostnofollow'                                           => 'save_post_type_nofollow',
		);
		// phpcs:enable
	}

	public function save_post_type_noindex( $source_key, $source_value, $target_options ) {
		$post_types = ! empty( $source_value ) && is_array( $source_value ) ? $source_value : array();
		foreach ( $post_types as $post_type ) {
			$noindex_key = array( 'wds_onpage_options', sprintf( 'meta_robots-noindex-%s', $post_type ) );
			smartcrawl_put_array_value( true, $target_options, $noindex_key );
		}

		return $target_options;
	}

	public function save_post_type_nofollow( $source_key, $source_value, $target_options ) {
		$post_types = ! empty( $source_value ) && is_array( $source_value ) ? $source_value : array();
		foreach ( $post_types as $post_type ) {
			$nofollow_key = array( 'wds_onpage_options', sprintf( 'meta_robots-nofollow-%s', $post_type ) );
			smartcrawl_put_array_value( true, $target_options, $nofollow_key );
		}

		return $target_options;
	}

	public function save_social_profile_links( $source_key, $source_value, $target_options ) {
		$mappings = array(
			'facebook.com'  => 'facebook_url',
			'fb.com'        => 'facebook_url',
			'instagram.com' => 'instagram_url',
			'linkedin.com'  => 'linkedin_url',
			'pinterest.com' => 'pinterest_url',
			'youtu.be'      => 'youtube_url',
			'youtube.com'   => 'youtube_url',
		);
		$social_links = empty( $source_value ) ? array() : explode( "\n", $source_value );

		foreach ( $social_links as $social_link ) {
			foreach ( $mappings as $domain => $target ) {
				if ( strpos( $social_link, $domain ) !== false ) {
					smartcrawl_put_array_value( trim( $social_link ), $target_options, array(
						'wds_social_options',
						$target,
					) );
				}
			}
		}

		return $target_options;
	}

	public function save_tax_noindex_values( $source_key, $source_value, $target_options ) {
		if ( ! is_array( $source_value ) ) {
			return $target_options;
		}

		foreach ( $source_value as $taxonomy ) {
			smartcrawl_put_array_value(
				true,
				$target_options,
				array(
					'wds_onpage_options',
					sprintf( 'meta_robots-noindex-%s', $taxonomy ),
				)
			);
		}

		return $target_options;
	}

	public function save_person_or_organization_name( $source_key, $source_value, $target_options ) {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$person_or_org = smartcrawl_get_array_value( $options, array(
			'modules',
			'aiosp_opengraph_options',
			'aiosp_opengraph_person_or_org',
		) );

		if ( 'person' === $person_or_org ) {
			smartcrawl_put_array_value( $source_value, $target_options, array(
				'wds_social_options',
				'override_name',
			) );
		}

		if ( 'org' === $person_or_org ) {
			smartcrawl_put_array_value( $source_value, $target_options, array(
				'wds_social_options',
				'organization_name',
			) );
		}

		return $target_options;
	}

	public function get_pre_processors() {
		return array(
			'wds_onpage_options/title-[a-z0-9_]+'          => 'replace_placeholders',
			'wds_social_options/schema_type'               => 'convert_person_or_org_setting',
			'wds_sitemap_options/verification-google-meta' => 'wrap_google_meta_in_markup',
			'wds_sitemap_options/verification-bing-meta'   => 'wrap_bing_meta_in_markup',
		);
	}

	public function wrap_google_meta_in_markup( $target_key, $source_value ) {
		if ( strpos( trim( $source_value ), '<meta' ) === 0 ) {
			return $source_value;
		}

		return sprintf( '<meta name="google-site-verification" content="%s" />', $source_value );
	}

	public function wrap_bing_meta_in_markup( $target_key, $source_value ) {
		if ( strpos( trim( $source_value ), '<meta' ) === 0 ) {
			return $source_value;
		}

		return sprintf( '<meta name="msvalidate.01" content="%s" />', $source_value );
	}

	public function save_excluded_pages( $source_key, $source_value, $target_options ) {
		$source_posts = empty( $source_value ) ? array() : explode( ',', $source_value );
		$target_posts = array();

		foreach ( $source_posts as $post ) {
			$post = trim( $post );
			if ( is_numeric( $post ) ) {
				$target_posts[] = intval( $post );
			} else {
				$id_from_slug = (int) $this->get_post_id_by_slug( $post );
				if ( $id_from_slug ) {
					$target_posts[] = $id_from_slug;
				}
			}
		}

		smartcrawl_put_array_value(
			$target_posts,
			$target_options,
			'wds-sitemap-ignore_post_ids'
		);

		return $target_options;
	}

	private function get_post_id_by_slug( $slug ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s", $slug ) );
	}

	public function save_extra_urls( $source_key, $source_value, $target_options ) {
		$source_excluded_urls = empty( $source_value ) ? array() : $source_value;
		$target_excluded_urls = array();
		foreach ( $source_excluded_urls as $url => $setting ) {
			$target_excluded_urls[] = smartcrawl_sanitize_relative_url( $url );
		}

		smartcrawl_put_array_value(
			$target_excluded_urls,
			$target_options,
			'wds-sitemap-extras'
		);

		return $target_options;
	}

	public function convert_person_or_org_setting( $target_key, $source_value ) {
		return 'person' === $source_value ? Smartcrawl_Schema_Printer::PERSON : Smartcrawl_Schema_Printer::ORGANIZATION;
	}

	public function replace_placeholders( $target_key, $source_value ) {
		$placeholders = $this->load_mapping_file( 'aioseop-macros.php' );
		if ( ! is_array( $placeholders ) ) {
			return $source_value;
		}

		foreach ( $placeholders as $source => $target ) {
			$source_value = str_replace( $source, $target, $source_value );
		}

		return $source_value;
	}

	protected function get_import_in_progress_option_id() {
		return self::IMPORT_IN_PROGRESS_FLAG;
	}

	protected function get_next_network_site_option_id() {
		return self::NETWORK_IMPORT_SITES_PROCESSED_COUNT;
	}

	private function canonical_links_enabled() {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );

		return 'on' === smartcrawl_get_array_value( $options, 'aiosp_can' )
		       && 'on' === smartcrawl_get_array_value( $options, 'aiosp_customize_canonical_links' );
	}

	private function keywords_enabled() {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );

		return '0' === (string) smartcrawl_get_array_value( $options, 'aiosp_togglekeywords' );
	}

	private function rewrite_titles_enabled( $key, $matches ) {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$rewrite_titles = (bool) smartcrawl_get_array_value( $options, 'aiosp_rewrite_titles' );
		$seo_for_custom_types = (bool) smartcrawl_get_array_value( $options, 'aiosp_enablecpost' );
		$advanced_settings_for_custom_types = (bool) smartcrawl_get_array_value( $options, 'aiosp_cpostadvanced' );
		$rewrite_titles_for_custom_types = (bool) smartcrawl_get_array_value( $options, 'aiosp_cposttitles' );
		$type = smartcrawl_get_array_value( $matches, 1 );
		$basic_types = array(
			'post',
			'page',
			'category',
			'tag',
			'home_page',
			'archive',
			'date',
			'author',
			'search',
			'404',
		);

		if ( in_array( $type, $basic_types, true ) ) {
			return $rewrite_titles;
		} else {
			return $rewrite_titles
			       && $seo_for_custom_types
			       && $advanced_settings_for_custom_types
			       && $rewrite_titles_for_custom_types
			       && ( $this->enabled_for_post_type( $type ) || $this->enabled_for_taxonomy( $type ) );
		}
	}

	private function home_og_fields_enabled() {
		$options = get_option( self::AIOSEOP_OPTIONS_ID );
		$use_home_meta_as_social = (bool) smartcrawl_get_array_value( $options, 'aiosp_opengraph_setmeta' );

		return ! $use_home_meta_as_social;
	}

	protected function get_source_plugins() {
		return array(
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
		);
	}
}
