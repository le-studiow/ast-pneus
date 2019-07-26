<?php

class Smartcrawl_Replacement_Helper extends Smartcrawl_Type_Traverser {

	private $specific_replacements;
	private $bp_data;
	private $subject;

	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Replacement_Helper
	 */
	private static $_instance;

	/**
	 * Constructor
	 */
	private function __construct() {
	}

	/**
	 * Singleton instance getter
	 *
	 * @return Smartcrawl_Replacement_Helper instance
	 */
	private static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function replace( $subject, $context = null ) {
		if ( ! is_string( $subject ) ) {
			return $subject;
		}

		$me = self::get();
		$me->subject = $subject;
		$me->specific_replacements = array();

		if ( $context ) {
			$me->dispatch_handler( $context );
		} else {
			$me->traverse();
		}

		$replacements = array_merge(
			$me->get_general_replacements(),
			$me->get_specific_replacements()
		);

		$replacements = apply_filters(
			'wds-known_macros',
			array_combine(
				apply_filters( 'wds-known_macros-keys', array_keys( $replacements ) ),
				apply_filters( 'wds-known_macros-values', array_values( $replacements ) )
			)
		);

		foreach ( $replacements as $macro => $replacement ) {
			$replacement = apply_filters( 'wds-macro-variable_replacement', $replacement, $macro );

			$subject = str_replace( $macro, $me->process_replacement_value( $replacement ), $subject );
		}

		return preg_replace( '/%%[a-zA-Z_]*%%/', '', $subject );
	}

	public static function get_dynamic_replacements( $subject, $context_object ) {
		if ( ! is_string( $subject ) ) {
			return array();
		}

		return self::get()->find_dynamic_replacements( $subject, $context_object );
	}

	private function dispatch_handler( $context ) {
		if ( $this->is_term( $context ) ) {
			$this->handle_tax_archive( $context );
		} elseif ( $this->is_pt( $context ) ) {
			$this->handle_pt_archive( $context );
		} elseif ( $this->is_post( $context ) ) {
			$this->handle_singular( $context );
		} elseif ( $this->is_bp_group( $context ) ) {
			$this->handle_bp_groups( $context );
		} elseif ( $this->is_bp_profile( $context ) ) {
			$this->handle_bp_profile( $context );
		}
	}

	private function is_pt( $context ) {
		return is_a( $context, 'WP_Post_Type' );
	}

	private function is_term( $context ) {
		return is_a( $context, 'WP_Term' );
	}

	private function is_post( $context ) {
		return is_a( $context, 'WP_Post' );
	}

	private function is_bp_group( $context ) {
		return is_a( $context, 'BP_Groups_Group' );
	}

	private function process_replacement_value( $replacement ) {
		if ( ! is_scalar( $replacement ) ) {
			return '';
		}

		return wp_strip_all_tags( $replacement );
	}

	public function get_bp_data() {
		if ( empty( $this->bp_data ) && function_exists( 'buddypress' ) ) {
			$this->bp_data = buddypress();
		}

		return $this->bp_data;
	}

	public function set_bp_data( $bp_data ) {
		$this->bp_data = $bp_data;
	}

	private function get_specific_replacements() {
		return empty( $this->specific_replacements ) ? array() : $this->specific_replacements;
	}

	private function get_general_replacements() {
		$query = $this->get_query_context();
		$paged = intval( $query->get( 'paged' ) );
		$max_num_pages = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
		$page_x_of_y = esc_html__( 'Page %1$s of %2$s' );
		$smartcrawl_options = Smartcrawl_Settings::get_options();
		$preset_sep = ! empty( $smartcrawl_options['preset-separator'] ) ? $smartcrawl_options['preset-separator'] : 'pipe';
		$separator = ! empty( $smartcrawl_options['separator'] ) ? $smartcrawl_options['separator'] : smartcrawl_get_separators( $preset_sep );
		$pagenum = $paged;
		if ( 0 === $pagenum ) {
			$pagenum = $max_num_pages > 1 ? 1 : '';
		}

		return array(
			'%%sitename%%'         => get_bloginfo( 'name' ),
			'%%sitedesc%%'         => get_bloginfo( 'description' ),
			'%%page%%'             => 0 !== $paged ? sprintf( $page_x_of_y, $paged, $max_num_pages ) : '',
			'%%spell_page%%'       => 0 !== $paged ? sprintf( $page_x_of_y, smartcrawl_spell_number( $paged ), smartcrawl_spell_number( $max_num_pages ) ) : '',
			'%%pagetotal%%'        => $max_num_pages > 1 ? $max_num_pages : '',
			'%%spell_pagetotal%%'  => $max_num_pages > 1 ? smartcrawl_spell_number( $max_num_pages ) : '',
			'%%pagenumber%%'       => empty( $pagenum ) ? '' : $pagenum,
			'%%spell_pagenumber%%' => empty( $pagenum ) ? '' : smartcrawl_spell_number( $pagenum ),
			'%%currenttime%%'      => date_i18n( get_option( 'time_format' ) ),
			'%%currentdate%%'      => date_i18n( get_option( 'date_format' ) ),
			'%%currentmonth%%'     => date_i18n( 'F' ),
			'%%currentyear%%'      => date_i18n( 'Y' ),
			'%%sep%%'              => $separator,
		);
	}

	public function handle_bp_groups( $current_group = null ) {
		if ( ! $this->is_bp_group( $current_group ) ) {
			$bp = $this->get_bp_data();
			$current_group = empty( $bp->groups->current_group ) ? null : $bp->groups->current_group;
		}

		$this->specific_replacements = array(
			'%%bp_group_name%%'        => $current_group ? $current_group->name : '',
			'%%bp_group_description%%' => $current_group ? $current_group->description : '',
		);
	}

	public function handle_bp_profile( $profile = array() ) {
		if ( ! $this->is_bp_profile( $profile ) ) {
			$profile = $this->get_current_bp_profile_data();
		}

		$this->specific_replacements = array(
			'%%bp_user_username%%'  => (string) smartcrawl_get_array_value( $profile, 'bp_user_username' ),
			'%%bp_user_full_name%%' => (string) smartcrawl_get_array_value( $profile, 'bp_user_full_name' ),
		);
	}

	public function handle_woo_shop() {
		$this->handle_singular( get_post( wc_get_page_id( 'shop' ) ) );
	}

	public function handle_blog_home() {
		// No context specific values available on blog index page
	}

	public function handle_static_home() {
		$this->handle_singular( get_post( get_option( 'page_for_posts' ) ) );
	}

	public function handle_search() {
		$query = $this->get_query_context();

		$this->specific_replacements = array(
			'%%searchphrase%%' => esc_html( $query->get( 's' ) ),
		);
	}

	public function handle_404() {
		// No context specific values available on the 404 page
	}

	public function handle_date_archive() {
		$this->specific_replacements = array(
			'%%date%%' => $this->get_date_for_archive(),
		);
	}

	public function handle_pt_archive( $post_type = null ) {
		if ( ! $this->is_pt( $post_type ) ) {
			$post_type = $this->get_queried_object();
		}
		$is_pt_archive = $this->is_pt( $post_type );

		$this->specific_replacements = array(
			'%%pt_plural%%' => $is_pt_archive ? $post_type->labels->name : '',
			'%%pt_single%%' => $is_pt_archive ? $post_type->labels->singular_name : '',
		);
	}

	public function handle_tax_archive( $term = null ) {
		if ( ! $this->is_term( $term ) ) {
			$term = $this->get_queried_object();
		}
		$term_data = wp_parse_args(
			$this->is_term( $term ) ? (array) $term : array(),
			$this->get_term_defaults()
		);

		$this->specific_replacements = array(
			'%%id%%'               => $term_data['term_id'],
			'%%term_title%%'       => $term_data['name'],
			'%%term_description%%' => $term_data['description'],
		);

		$custom_replacements = $this->find_dynamic_replacements( $this->subject, $term );
		$this->specific_replacements = wp_parse_args( $this->specific_replacements, $custom_replacements );

		if ( 'category' === $term_data['taxonomy'] ) {
			$this->specific_replacements['%%category%%'] = $term_data['name'];
			$this->specific_replacements['%%category_description%%'] = $term_data['description'];
		} elseif ( 'post_tag' === $term_data['taxonomy'] ) {
			$this->specific_replacements['%%tag%%'] = $term_data['name'];
			$this->specific_replacements['%%tag_description%%'] = $term_data['description'];
		}
	}

	public function handle_author_archive() {
		$query = $this->get_query_context();
		$user_id = $query->get( 'author' );

		$this->specific_replacements = array(
			'%%name%%'   => empty( $user_id ) ? '' : get_the_author_meta( 'display_name', $user_id ),
			'%%userid%%' => (string) $user_id,
		);
	}

	public function handle_archive() {
		// No context specific values available on the archive page
	}

	public function handle_singular( $post = null ) {
		if ( ! $this->is_post( $post ) ) {
			$post = $this->get_context();
		}
		if ( empty( $post->ID ) ) {
			$query = $this->get_resolver()->get_query_context();
			$post = $query->get_queried_object();
		}
		$post_data = wp_parse_args(
			$this->is_post( $post ) ? (array) $post : array(),
			$this->get_post_defaults()
		);

		$this->specific_replacements = array(
			'%%date%%'         => mysql2date( get_option( 'date_format' ), $post_data['post_date'] ),
			'%%excerpt%%'      => smartcrawl_get_trimmed_excerpt( $post_data['post_excerpt'], $post_data['post_content'] ),
			'%%excerpt_only%%' => $post_data['post_excerpt'],
			'%%id%%'           => $post_data['ID'],
			'%%modified%%'     => $post_data['post_modified'],
			'%%name%%'         => empty( $post_data['post_author'] ) ? '' : get_the_author_meta( 'display_name', $post_data['post_author'] ),
			'%%title%%'        => $post_data['post_title'],
			'%%userid%%'       => $post_data['post_author'],
		);

		$custom_replacements = $this->find_dynamic_replacements( $this->subject, $post );
		$this->specific_replacements = wp_parse_args( $this->specific_replacements, $custom_replacements );

		if ( 'attachment' === $post_data['post_type'] ) {
			$this->specific_replacements['%%caption%%'] = $post_data['post_excerpt'];
		} elseif ( 'post' === $post_data['post_type'] ) {
			$this->specific_replacements['%%category%%'] = get_the_category_list( ', ', '', $post_data['ID'] );
		}
	}

	private function get_hierarchical_terms_for_post_type( $post ) {
		$custom_taxonomy = $this->get_post_type_taxonomy( $post );
		if ( $custom_taxonomy ) {
			$terms = wp_get_post_terms( $post->ID, $custom_taxonomy, array( 'fields' => 'names' ) );
			if ( ! is_wp_error( $terms ) ) {
				return implode( ', ', $terms );
			}
		}

		return '';
	}

	private function get_post_type_taxonomy( $post ) {
		$taxonomies = get_object_taxonomies( $post );

		return empty( $taxonomies ) ? '' : array_shift( $taxonomies );
	}

	private function get_post_defaults() {
		return array(
			'ID'                => '',
			'post_author'       => '',
			'post_name'         => '',
			'post_type'         => '',
			'post_title'        => '',
			'post_date'         => '',
			'post_date_gmt'     => '',
			'post_content'      => '',
			'post_excerpt'      => '',
			'post_status'       => '',
			'comment_status'    => '',
			'ping_status'       => '',
			'post_password'     => '',
			'post_parent'       => '',
			'post_modified'     => '',
			'post_modified_gmt' => '',
			'comment_count'     => '',
			'menu_order'        => '',
		);
	}

	private function get_term_defaults() {
		return array(
			'name'             => '',
			'term_taxonomy_id' => '',
			'count'            => '',
			'description'      => '',
			'term_id'          => '',
			'taxonomy'         => '',
			'term_group'       => '',
			'slug'             => '',
		);
	}

	private function get_date_for_archive() {
		$query = $this->get_query_context();
		$day = $query->get( 'day' );
		$month = $query->get( 'monthnum' );
		$year = $query->get( 'year' );
		$format = '';
		if ( empty( $year ) ) {
			// At the very least we need an year
			return '';
		}
		$timestamp = mktime( 0, 0, 0,
			empty( $month ) ? 1 : $month,
			empty( $day ) ? 1 : $day,
			$year
		);

		if ( ! empty( $day ) ) {
			$format = get_option( 'date_format' );
		} elseif ( ! empty( $month ) ) {
			$format = 'F Y';
		} elseif ( ! empty( $year ) ) {
			$format = 'Y';
		}

		$date = date_i18n( $format, $timestamp );

		return $date;
	}

	private function find_dynamic_replacements( $subject, $context ) {
		$term_desc_replacements = $this->find_term_field_replacements( $subject, $context, 'ct_desc_', 'description' );
		$subject = str_replace( array_keys( $term_desc_replacements ), '', $subject );

		$term_name_replacements = $this->find_term_field_replacements( $subject, $context, 'ct_', 'name' );
		$subject = str_replace( array_keys( $term_name_replacements ), '', $subject );

		$meta_replacements = $this->find_meta_replacements( $subject, $context );

		return array_merge( $term_desc_replacements, $term_name_replacements, $meta_replacements );
	}

	private function find_term_field_replacements( $subject, $context, $prefix, $term_field ) {
		$pattern = "/(%%{$prefix}[a-z_]+%%)/";
		$matches = array();
		$replacements = array();
		$match_result = preg_match_all( $pattern, $subject, $matches, PREG_PATTERN_ORDER );
		if ( ! empty( $match_result ) ) {
			$placeholders = array_shift( $matches );
			foreach ( array_unique( $placeholders ) as $placeholder ) {
				$taxonomy_name = str_replace( array( "%%$prefix", '%%' ), '', $placeholder );

				$taxonomy = get_taxonomy( $taxonomy_name );
				if ( empty( $taxonomy ) ) {
					continue;
				}

				$terms = $this->get_linked_terms( $context, $taxonomy_name );
				if ( ! empty( $terms ) ) {
					$term = array_shift( $terms );
					$replacements[ $placeholder ] = wp_strip_all_tags( get_term_field( $term_field, $term, $taxonomy_name ) );
				}
			}
		}

		return $replacements;
	}

	private function find_meta_replacements( $subject, $context ) {
		$prefix = 'cf_';
		$pattern = "/(%%{$prefix}[a-z_]+%%)/";
		$matches = array();
		$replacements = array();
		$match_result = preg_match_all( $pattern, $subject, $matches, PREG_PATTERN_ORDER );
		if ( ! empty( $match_result ) ) {
			$placeholders = array_shift( $matches );
			foreach ( array_unique( $placeholders ) as $placeholder ) {
				$meta_key = str_replace( array( "%%$prefix", '%%' ), '', $placeholder );

				$meta_value = $this->get_meta( $context, $meta_key );
				if ( ! empty( $meta_value ) && ! is_array( $meta_value ) && ! is_object( $meta_value ) ) {
					$replacements[ $placeholder ] = wp_strip_all_tags( $meta_value );
				}
			}
		}

		return $replacements;
	}

	private function get_meta( $context, $meta_key ) {
		if ( $this->is_post( $context ) ) {
			return get_post_meta( $context->ID, $meta_key, true );
		} elseif ( $this->is_term( $context ) ) {
			return get_term_meta( $context->term_id, $meta_key, true );
		}

		return array();
	}

	private function get_linked_terms( $context, $taxonomy_name ) {
		if ( $this->is_post( $context ) ) {
			return get_the_terms( $context->ID, $taxonomy_name );
		} elseif ( $this->is_term( $context ) && $context->taxonomy === $taxonomy_name ) {
			return array( $context );
		}

		return array();
	}

	private function get_current_bp_profile_data() {
		$bp_active = function_exists( 'buddypress' );

		return array(
			'bp_user_username'  => $bp_active ? bp_get_displayed_user_username() : '',
			'bp_user_full_name' => $bp_active ? bp_get_displayed_user_fullname() : '',
		);
	}

	private function is_bp_profile( $context ) {
		return is_array( $context ) && ! empty( $context['bp_user_username'] );
	}
}
