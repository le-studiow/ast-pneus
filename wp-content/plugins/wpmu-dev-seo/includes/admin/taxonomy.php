<?php

class Smartcrawl_Taxonomy extends Smartcrawl_Base_Controller {
	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $_instance;

	protected function init() {
		$taxonomy = smartcrawl_get_array_value( $_GET, 'taxonomy' ); // phpcs:ignore -- Can't add nonce to the request
		if ( is_admin() && ! empty( $taxonomy ) ) {
			add_action( sanitize_key( $taxonomy ) . '_edit_form', array(
				&$this,
				'term_additions_form',
			), 10, 2 );
		}

		add_action( 'edit_term', array( &$this, 'update_term' ), 10, 3 );
		add_action( 'wp_ajax_wds-term-form-preview', array( $this, 'json_create_preview' ) );
	}

	public function json_create_preview() {
		$data = $this->get_request_data();
		$term_id = (int) smartcrawl_get_array_value( $data, 'term_id' );
		$result = array( 'success' => false );

		if ( empty( $term_id ) ) {
			wp_send_json( $result );

			return;
		}

		$result['success'] = true;
		$result['markup'] = Smartcrawl_Simple_Renderer::load( 'term/term-google-preview', array(
			'term' => get_term( $term_id ),
		) );

		wp_send_json( $result );
	}

	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( $_POST['_wds_nonce'], 'wds-metabox-nonce' ) ? stripslashes_deep( $_POST ) : array();
	}

	/**
	 * Static instance getter
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function term_additions_form( $term, $taxonomy ) {
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( ! $taxonomy_object->public ) {
			return;
		}

		$smartcrawl_options = Smartcrawl_Settings::get_options();
		$tax_meta = get_option( 'wds_taxonomy_meta' );

		if ( isset( $tax_meta[ $taxonomy ][ $term->term_id ] ) ) {
			$tax_meta = $tax_meta[ $taxonomy ][ $term->term_id ];
		}

		$taxonomy_labels = $taxonomy_object->labels;

		$global_noindex = ! empty( $smartcrawl_options[ 'meta_robots-noindex-' . $term->taxonomy ] )
			? $smartcrawl_options[ 'meta_robots-noindex-' . $term->taxonomy ]
			: false;
		$global_nofollow = ! empty( $smartcrawl_options[ 'meta_robots-nofollow-' . $term->taxonomy ] )
			? $smartcrawl_options[ 'meta_robots-nofollow-' . $term->taxonomy ]
			: false;

		wp_enqueue_style( Smartcrawl_Controller_Assets::APP_CSS );
		wp_enqueue_script( Smartcrawl_Controller_Assets::TERM_FORM_JS );
		wp_enqueue_media();

		Smartcrawl_Simple_Renderer::render( 'term/term-form', array(
			'taxonomy_object' => $taxonomy_object,
			'taxonomy_labels' => $taxonomy_labels,
			'term'            => $term,
			'global_noindex'  => $global_noindex,
			'global_nofollow' => $global_nofollow,
			'tax_meta'        => $tax_meta,
		) );
	}

	public function update_term( $term_id, $tt_id, $taxonomy ) {
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( ! $taxonomy_object->public ) {
			return;
		}

		$smartcrawl_options = Smartcrawl_Settings::get_options();

		$tax_meta = get_option( 'wds_taxonomy_meta' );
		$post_data = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'update-tag_' . $term_id )
			? stripslashes_deep( $_POST )
			: array();

		foreach ( array( 'title', 'desc', 'bctitle', 'canonical' ) as $key ) {
			$value = isset( $post_data["wds_{$key}"] )
				? $post_data["wds_{$key}"]
				: '';
			if ( 'canonical' === $key ) {
				$value = esc_url_raw( $value );
			} else {
				$value = smartcrawl_sanitize_preserve_macros( $value );
			}
			$tax_meta[ $taxonomy ][ $term_id ]["wds_{$key}"] = $value;
		}

		foreach ( array( 'noindex', 'nofollow' ) as $key ) {
			$global = ! empty( $smartcrawl_options["meta_robots-{$key}-{$taxonomy}"] ) ? (bool) $smartcrawl_options["meta_robots-{$key}-{$taxonomy}"] : false;

			if ( ! $global ) {
				$tax_meta[ $taxonomy ][ $term_id ][ 'wds_' . $key ] = isset( $post_data["wds_{$key}"] )
					? (bool) $post_data["wds_{$key}"]
					: false;
			} else {
				$tax_meta[ $taxonomy ][ $term_id ]["wds_override_{$key}"] = isset( $post_data["wds_override_{$key}"] )
					? (bool) $post_data["wds_override_{$key}"]
					: false;
			}
		}

		if ( ! empty( $post_data['wds-opengraph'] ) ) {
			$data = is_array( $post_data['wds-opengraph'] ) ? stripslashes_deep( $post_data['wds-opengraph'] ) : array();
			$tax_meta[ $taxonomy ][ $term_id ]['opengraph'] = array();
			if ( ! empty( $data['title'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['opengraph']['title'] = smartcrawl_sanitize_preserve_macros( $data['title'] );
			}
			if ( ! empty( $data['description'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['opengraph']['description'] = smartcrawl_sanitize_preserve_macros( $data['description'] );
			}
			if ( ! empty( $data['images'] ) && is_array( $data['images'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['opengraph']['images'] = array();
				foreach ( $data['images'] as $img ) {
					$img = esc_url_raw( $img );
					$tax_meta[ $taxonomy ][ $term_id ]['opengraph']['images'][] = $img;
				}
			}
			$tax_meta[ $taxonomy ][ $term_id ]['opengraph']['disabled'] = ! empty( $data['disabled'] );
		}

		if ( ! empty( $post_data['wds-twitter'] ) ) {
			$data = is_array( $post_data['wds-twitter'] ) ? stripslashes_deep( $post_data['wds-twitter'] ) : array();
			$tax_meta[ $taxonomy ][ $term_id ]['twitter'] = array();
			if ( ! empty( $data['title'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['twitter']['title'] = smartcrawl_sanitize_preserve_macros( $data['title'] );
			}
			if ( ! empty( $data['description'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['twitter']['description'] = smartcrawl_sanitize_preserve_macros( $data['description'] );
			}
			if ( ! empty( $data['images'] ) && is_array( $data['images'] ) ) {
				$tax_meta[ $taxonomy ][ $term_id ]['twitter']['images'] = array();
				foreach ( $data['images'] as $img ) {
					$img = esc_url_raw( $img );
					$tax_meta[ $taxonomy ][ $term_id ]['twitter']['images'][] = $img;
				}
			}
			$tax_meta[ $taxonomy ][ $term_id ]['twitter']['disabled'] = ! empty( $data['disabled'] );
		}

		update_option( 'wds_taxonomy_meta', $tax_meta );

		if ( function_exists( 'w3tc_flush_all' ) ) {
			// Use W3TC API v0.9.5+
			w3tc_flush_all();
		} elseif ( defined( 'W3TC_DIR' ) && is_readable( W3TC_DIR . '/lib/W3/ObjectCache.php' ) ) {
			// Old (very old) API
			require_once W3TC_DIR . '/lib/W3/ObjectCache.php';
			$w3_objectcache = &W3_ObjectCache::instance();

			$w3_objectcache->flush();
		}

	}
}
