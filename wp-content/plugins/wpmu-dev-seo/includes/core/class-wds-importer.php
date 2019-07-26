<?php

abstract class Smartcrawl_Importer {
	private $status = null;

	abstract public function data_exists();

	abstract protected function get_source_plugins();

	public function get_active_source_plugin() {
		$source_plugin = $this->get_source_plugins();
		foreach ( $source_plugin as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				return $plugin;
			}
		}

		return false;
	}

	public function get_deactivation_link() {
		$active_plugin = $this->get_active_source_plugin();
		if ( ! $active_plugin ) {
			return false;
		}

		return wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $active_plugin . '&amp;plugin_status=all', 'deactivate-plugin_' . $active_plugin );
	}

	public function import_for_all_sites( $options = array() ) {
		$processed_sites = $this->get_processed_sites_count();
		$this->update_status( array(
			'total_sites'     => get_sites( array(
				'count'  => true,
				'number' => PHP_INT_MAX,
			) ),
			'completed_sites' => $processed_sites,
		) );
		$next_site = $this->get_next_site_to_process();
		if ( ! $next_site ) {
			$this->reset_network_import_flag();

			return;
		}

		switch_to_blog( $next_site );
		$site_import_complete = $this->import( $options );
		switch_to_blog( get_main_site_id() );

		if ( $site_import_complete ) {
			$processed_sites ++;
		}
		$this->update_processed_site_count( $processed_sites );
		$this->turn_sitewide_mode_off();
	}

	public function get_status() {
		return empty( $this->status ) ? array() : $this->status;
	}

	protected function set_status( $status ) {
		$this->status = $status;
	}

	protected function update_status( $status ) {
		$this->status = wp_parse_args( $status, $this->get_status() );
	}

	private function get_processed_sites_count() {
		return (int) get_site_option( $this->get_next_network_site_option_id(), 0 );
	}

	abstract protected function get_next_network_site_option_id();

	private function get_next_site_to_process() {
		$processed_sites_count = $this->get_processed_sites_count();
		$next_site = get_sites( array(
			'fields' => 'ids',
			'number' => 1,
			'offset' => $processed_sites_count,
		) );

		return empty( $next_site ) ? false : $next_site[0];
	}

	private function reset_network_import_flag() {
		delete_site_option( $this->get_next_network_site_option_id() );
	}

	public function import( $options = array() ) {
		$options = wp_parse_args( $options, array(
			'import-options'          => true,
			'import-term-meta'        => true,
			'import-post-meta'        => true,
			'force-restart'           => false,
			'keep-existing-post-meta' => false,
		) );
		$import_options = (boolean) smartcrawl_get_array_value( $options, 'import-options' );
		$import_term_meta = (boolean) smartcrawl_get_array_value( $options, 'import-term-meta' );
		$import_post_meta = (boolean) smartcrawl_get_array_value( $options, 'import-post-meta' );
		$force_restart = (boolean) smartcrawl_get_array_value( $options, 'force-restart' );
		$keep_post_meta = (boolean) smartcrawl_get_array_value( $options, 'keep-existing-post-meta' );

		if ( ! $this->is_import_in_progress() || $force_restart ) {
			$this->set_import_flag();
			if ( $import_options ) {
				$this->remove_existing_wds_options();
				$this->import_options();
			}
			if ( $import_term_meta ) {
				$this->remove_existing_wds_taxonomy_meta();
				$this->import_taxonomy_meta();
			}
			if ( $import_post_meta && ! $keep_post_meta ) {
				$this->remove_existing_wds_post_meta();
			}
		}

		// If post meta doesn't need to be imported then we're done.
		$complete = $import_post_meta ? $this->import_post_meta() : true;
		if ( $complete ) {
			$this->reset_import_flag();
		}

		return $complete;
	}

	public function is_import_in_progress() {
		return (boolean) get_option( $this->get_import_in_progress_option_id() );
	}

	abstract protected function get_import_in_progress_option_id();

	private function set_import_flag() {
		update_option( $this->get_import_in_progress_option_id(), true );
	}

	private function remove_existing_wds_options() {
		Smartcrawl_Settings::reset_options();

		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%wds-sitemap%'" );
	}

	private function remove_existing_wds_taxonomy_meta() {
		delete_option( 'wds_taxonomy_meta' );
	}

	abstract public function import_options();

	abstract public function import_taxonomy_meta();

	private function remove_existing_wds_post_meta() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wds_%'" );
	}

	abstract public function import_post_meta();

	private function reset_import_flag() {
		delete_option( $this->get_import_in_progress_option_id() );
	}

	private function update_processed_site_count( $count ) {
		update_site_option( $this->get_next_network_site_option_id(), $count );
	}

	private function turn_sitewide_mode_off() {
		// Because Yoast and AIOSEO don't have sitewide modes
		update_site_option( 'wds_sitewide_mode', '0' );
		// When sitewide mode is off settings must always be on
		$blog_tabs = get_site_option( 'wds_blog_tabs', array() );
		$blog_tabs['wds_settings'] = true;
		update_site_option( 'wds_blog_tabs', $blog_tabs );
	}

	public function is_network_import_in_progress() {
		return get_site_option( $this->get_next_network_site_option_id(), false ) !== false;
	}

	protected function get_posts_with_source_metas( $prefix ) {
		global $wpdb;
		$posts_with_target_meta = implode( ',', $this->get_posts_with_target_metas() );
		$not_in = $posts_with_target_meta ? $posts_with_target_meta : '-1';
		$meta_query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE '{$prefix}%' AND post_id NOT IN ({$not_in}) GROUP BY post_id";

		return $wpdb->get_col( $meta_query ); // phpcs:ignore -- Preparation difficult due to % escaping and complex IN clause
	}

	protected function get_posts_with_target_metas() {
		global $wpdb;

		return $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wds_%' AND meta_key NOT IN ('_wds_analysis','_wds_readability') GROUP BY post_id" );
	}

	protected function load_mapping_file( $file ) {
		return include SMARTCRAWL_PLUGIN_DIR . 'core/resources/' . $file;
	}

	protected function save_options( $options ) {
		foreach ( $options as $option_key => $values ) {
			remove_all_filters( 'sanitize_option_' . $option_key );
			update_option( $option_key, $values );
		}
	}

	protected function try_custom_handlers( $source_key, $source_value, $target_options ) {
		$custom_handler = null;
		foreach ( $this->expand_mappings( $this->get_custom_handlers() ) as $pattern => $callback ) {
			if ( preg_match( '#' . $pattern . '#', $source_key ) ) {
				$custom_handler = $callback;
			}
		}

		if ( ! $custom_handler ) {
			return $target_options;
		}

		$target_options = call_user_func_array(
			array( $this, $custom_handler ),
			array( $source_key, $source_value, $target_options )
		);

		return $target_options;
	}

	protected function expand_mappings( $mappings ) {
		$post_types = $this->get_post_types();
		$taxonomies = $this->get_taxonomies();

		foreach ( $mappings as $source_key => $target_key ) {
			if ( ! $this->is_custom_type_option( $source_key ) ) {
				continue;
			}

			unset( $mappings[ $source_key ] );

			if ( $this->is_post_type_option( $source_key ) ) {
				foreach ( $post_types as $post_type ) {
					$new_source_key = str_replace( 'POSTTYPE', $post_type, $source_key );
					$new_target_key = false === $target_key ? false : str_replace( 'POSTTYPE', $post_type, $target_key );
					$mappings[ $new_source_key ] = $new_target_key;
				}
			} elseif ( $this->is_taxonomy_option( $source_key ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$new_source_key = str_replace( 'TAXONOMY', $taxonomy, $source_key );
					$new_target_key = false === $target_key ? false : str_replace( 'TAXONOMY', $taxonomy, $target_key );
					$mappings[ $new_source_key ] = $new_target_key;
				}
			}
		}

		return $mappings;
	}

	protected function get_post_types() {
		return get_post_types( array( 'public' => true ) );
	}

	/**
	 * @return array
	 */
	protected function get_taxonomies() {
		return array_merge(
			array( 'post_tag', 'category' ),
			get_taxonomies( array( '_builtin' => false, 'public' => true ) )
		);
	}

	private function is_custom_type_option( $key ) {
		return $this->is_post_type_option( $key ) || $this->is_taxonomy_option( $key );
	}

	private function is_post_type_option( $key ) {
		return $key && strpos( $key, 'POSTTYPE' ) !== false;
	}

	private function is_taxonomy_option( $key ) {
		return $key && strpos( $key, 'TAXONOMY' ) !== false;
	}

	protected function get_custom_handlers() {
		return array();
	}

	protected function pre_process_value( $target_key, $source_value ) {
		if ( $this->requires_array_wrapping( $target_key ) ) {
			return array( $source_value );
		}

		if ( $this->requires_boolean_casting( $target_key ) ) {
			return $this->is_value_truthy( $source_value );
		}

		if ( $this->requires_boolean_inversion( $target_key ) ) {
			return ! $this->is_value_truthy( $source_value );
		}

		$all_arguments = func_get_args();

		return $this->try_custom_pre_processor( $target_key, $source_value, $all_arguments );
	}

	private function requires_array_wrapping( $key ) {
		return $key && strpos( $key, '[]' ) !== false;
	}

	private function requires_boolean_casting( $key ) {
		return $key && strpos( $key, '!!' ) !== false;
	}

	private function is_value_truthy( $value ) {
		return 'on' === $value
		       || '1' === $value
		       || true === $value
		       || ( is_int( $value ) && $value > 0 )
			? true
			: false;
	}

	private function requires_boolean_inversion( $key ) {
		return $key && strpos( $key, '!' ) !== false;
	}

	private function try_custom_pre_processor( $target_key, $source_value, $all_arguments ) {
		$pre_processor = null;
		foreach ( $this->get_pre_processors() as $pattern => $callback ) {
			if ( preg_match( '#' . $pattern . '#', $target_key ) ) {
				$pre_processor = $callback;
			}
		}

		if ( ! $pre_processor ) {
			return $source_value;
		}

		$source_value = call_user_func_array(
			array( $this, $pre_processor ),
			$all_arguments
		);

		return $source_value;
	}

	protected function get_pre_processors() {
		return array();
	}

	protected function pre_process_key( $key ) {
		if ( $this->requires_array_wrapping( $key ) ) {
			$key = $this->remove_array_wrapping_indicators( $key );
		}

		if ( $this->requires_boolean_casting( $key ) ) {
			$key = $this->remove_boolean_casting_indicators( $key );
		}

		if ( $this->requires_boolean_inversion( $key ) ) {
			$key = $this->remove_boolean_inversion_indicators( $key );
		}

		if ( $this->is_multipart_key( $key ) ) {
			$key = $this->get_key_parts( $key );
		}

		return $key;
	}

	private function remove_array_wrapping_indicators( $key ) {
		$parts = explode( '[]', $key );

		return empty( $parts[0] ) ? '' : $parts[0];
	}

	private function remove_boolean_casting_indicators( $key ) {
		$parts = explode( '!!', $key );

		return empty( $parts[1] ) ? '' : $parts[1];
	}

	private function remove_boolean_inversion_indicators( $key ) {
		$parts = explode( '!', $key );

		return empty( $parts[1] ) ? '' : $parts[1];
	}

	private function is_multipart_key( $key ) {
		return $key && strpos( $key, '/' ) !== false;
	}

	private function get_key_parts( $key ) {
		return explode( '/', $key );
	}
}
