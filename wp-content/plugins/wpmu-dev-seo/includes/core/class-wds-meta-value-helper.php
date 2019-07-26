<?php

class Smartcrawl_Meta_Value_Helper extends Smartcrawl_Type_Traverser {
	private $title = '';
	private $fallback_title = '';
	private $description = '';
	private $fallback_description = '';
	private $keywords = array();
	private $news_keywords = array();

	private function __construct() {
		$this->init();
	}

	private function init() {
		$this->title = '';
		$this->fallback_title = '';
		$this->description = '';
		$this->fallback_description = '';
		$this->keywords = array();
		$this->news_keywords = array();
	}

	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * Static instance getter
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		self::$_instance->init();
		self::$_instance->traverse();

		return self::$_instance;
	}

	public function handle_bp_groups() {
		$this->from_options( 'bp_groups' );
	}

	public function handle_bp_profile() {
		$this->from_options( 'bp_profile' );
	}

	public function handle_woo_shop() {
		$this->handle_singular( wc_get_page_id( 'shop' ) );
	}

	public function handle_blog_home() {
		$this->from_options( 'home' );
	}

	public function handle_static_home() {
		$this->handle_singular( get_option( 'page_for_posts' ) );
	}

	public function handle_search() {
		$this->from_options( 'search' );
	}

	public function handle_404() {
		$this->from_options( '404' );
	}

	public function handle_date_archive() {
		$this->from_options_archive( 'date' );
	}

	public function handle_pt_archive() {
		$post_type = $this->get_queried_object();
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$location = Smartcrawl_Onpage_Settings::PT_ARCHIVE_PREFIX . $post_type->name;
			$this->from_options_archive( $location );
		}
	}

	public function handle_tax_archive() {
		/**
		 * @var $term WP_Term
		 */
		$term = $this->get_queried_object();
		if ( is_a( $term, 'WP_Term' ) ) {
			$this->fallback_title = wp_strip_all_tags( get_the_archive_title() );
			$this->fallback_description = wp_strip_all_tags( get_term_field( 'description', $term ) );

			$this->from_options( $term->taxonomy );
			$this->from_term_meta( $term );
		}
	}

	public function handle_author_archive() {
		/**
		 * @var $author WP_User
		 */
		$author = $this->get_queried_object();
		if ( is_a( $author, 'WP_User' ) ) {
			$this->from_options_archive( 'author' );

			$this->from_author_meta( $author );
		}
	}

	public function handle_archive() {
		// TODO: Implement handle_archive() method.
	}

	public function handle_singular( $post_id = 0 ) {
		$post = $post_id ? get_post( $post_id ) : null;
		if ( ! $post ) {
			$post = $this->get_context();
		}
		if ( empty( $post->ID ) ) {
			$query = $this->get_resolver()->get_query_context();
			$post = $query->get_queried_object();
		}
		if ( is_a( $post, 'WP_Post' ) ) {
			$this->fallback_title = get_the_title( $post );
			$this->fallback_description = smartcrawl_get_trimmed_excerpt( $post->post_excerpt, $post->post_content );

			$this->from_options( $this->get_post_type( $post ) );

			// Now apply any overrides from the individual post's meta
			$this->from_post_meta( $post );
		}
	}

	private function from_options( $location ) {
		$options = $this->get_options();

		$title = smartcrawl_get_array_value( $options, 'title-' . $location );
		$description = smartcrawl_get_array_value( $options, 'metadesc-' . $location );
		$keywords = smartcrawl_get_array_value( $options, 'keywords-' . $location );

		$title = $this->prepare_value( $title );
		$description = $this->prepare_value( $description );
		$keywords = $this->keywords_string_to_array( wp_strip_all_tags( strval( $keywords ) ) );

		$this->title = empty( $title ) ? $this->title : $title;
		$this->description = empty( $description ) ? $this->description : $description;
		$this->keywords = empty( $keywords ) ? $this->keywords : $keywords;
	}

	private function from_term_meta( $term ) {
		$raw_title = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_title' );
		$raw_desc = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_desc' );

		$title = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_desc );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
	}

	private function get_options() {
		return Smartcrawl_Settings::get_options();
	}

	/**
	 * @param $author WP_User
	 */
	private function from_author_meta( $author ) {
		$raw_title = get_the_author_meta( 'wds_title', $author->ID );
		$raw_description = get_the_author_meta( 'wds_metadesc', $author->ID );

		$title = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_description );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
	}

	/**
	 * @param $post WP_Post
	 */
	private function from_post_meta( $post ) {
		$post_id = $post->ID;

		$raw_title = smartcrawl_get_value( 'title', $post_id );
		$raw_desc = smartcrawl_get_value( 'metadesc', $post_id );

		$title = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_desc );
		$news_keywords = $this->keywords_string_to_array( smartcrawl_get_value( 'news_keywords', $post_id ) );

		$tags_to_keywords = smartcrawl_get_value( 'tags_to_keywords', $post_id );
		$keywords = $this->keywords_string_to_array( smartcrawl_get_value( 'keywords', $post_id ) );
		$focus_keywords = $this->keywords_string_to_array( smartcrawl_get_value( 'focus-keywords', $post_id ) );
		$tag_keywords = $tags_to_keywords
			? $this->get_tag_keywords( $post )
			: array();
		$all_keywords = array_filter( array_unique( array_merge(
			$keywords,
			$focus_keywords,
			$tag_keywords
		) ) );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
		if ( ! empty( $all_keywords ) ) {
			$this->keywords = $all_keywords;
		}
		if ( ! empty( $news_keywords ) ) {
			$this->news_keywords = $news_keywords;
		}
	}

	/**
	 * @param $post WP_Post
	 *
	 * @return array
	 */
	private function get_tag_keywords( $post ) {
		$target_taxonomy = $this->get_tag_taxonomy( $post );
		if ( empty( $target_taxonomy ) ) {
			return array();
		}

		$tags = array();
		$raw_tags = get_the_terms( $post->ID, $target_taxonomy );
		if ( $raw_tags ) {
			foreach ( $raw_tags as $tag ) {
				$tags[] = $tag->name;
			}
		}

		return $tags;
	}

	/**
	 * @param $post WP_Post
	 *
	 * @return string
	 */
	private function get_tag_taxonomy( $post ) {
		if ( $this->get_post_type( $post ) === 'post' ) {
			return 'post_tag';
		}

		$taxonomies = get_object_taxonomies( $this->get_post_type( $post ), 'objects' );
		$target_taxonomy = '';
		foreach ( $taxonomies as $taxonomy => $taxonomy_obj ) {
			if ( ! $taxonomy_obj->hierarchical && $taxonomy_obj->public ) {
				// We're going to use the first public, non-hierarchical taxonomy we find
				$target_taxonomy = $taxonomy;
				break;
			}
		}
		return $target_taxonomy;
	}

	/**
	 * Converts a comma-separated string of keywords into an array
	 *
	 * @param string $string Keywords string.
	 *
	 * @return array List of keywords
	 */
	private function keywords_string_to_array( $string ) {
		$string = trim( strval( $string ) );
		$array = $string ? explode( ',', $string ) : array();
		$array = array_map( 'trim', $array );

		return array_filter( array_unique( $array ) );
	}

	private function from_options_archive( $location ) {
		$this->fallback_title = wp_strip_all_tags( get_the_archive_title() );

		$this->from_options( $location );
	}

	/**
	 * Returns a title string.
	 *
	 * - Value from options/meta is returned
	 * - if it's empty, the passed default argument is returned
	 * - if the passed default is also empty, the fallback value is returned
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_title( $default = '' ) {
		$fallback = ! empty( $default )
			? $default
			: $this->fallback_title;

		$title = ! empty( $this->title )
			? $this->title
			: $fallback; // We tried but didn't get anywhere so let's use fallback value

		return apply_filters( 'wds_title', $title );
	}

	/**
	 * Returns description.
	 *
	 * - Value from options/meta is returned
	 * - if it's empty, the passed default argument is returned
	 * - if the passed default is also empty, the fallback value is returned
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_description( $default = '' ) {
		$fallback = ! empty( $default )
			? $default
			: $this->fallback_description;

		$description = ! empty( $this->description )
			? $this->description
			: $fallback; // We tried but didn't get anywhere so let's use default value

		return apply_filters( 'wds_metadesc', $description );
	}

	/**
	 * @return array
	 */
	public function get_keywords() {
		return apply_filters( 'wds_keywords', $this->keywords );
	}

	public function get_focus_keywords( $post = false ) {
		$result = array();
		if ( empty( $post ) ) {
			$post = $this->get_context();
		}

		if ( is_a( $post, 'WP_Post' ) ) {
			$string = smartcrawl_get_value( 'focus-keywords', $post->ID );
			$result = $this->keywords_string_to_array( $string );
		}

		return apply_filters( 'wds_focus_keywords', $result );
	}

	/**
	 * @return array
	 */
	public function get_news_keywords() {
		return apply_filters( 'wds_news_keywords', $this->news_keywords );
	}

	/**
	 * @param $raw
	 *
	 * @return string
	 */
	private function prepare_value( $raw ) {
		$raw = wp_strip_all_tags( trim( strval( $raw ) ) );

		return Smartcrawl_Replacement_Helper::replace( $raw );
	}

	/**
	 * When the argument is a revision, returns the post type of the parent.
	 *
	 * @param $post WP_Post
	 *
	 * @return string
	 */
	private function get_post_type( $post ) {
		$post_parent = wp_is_post_revision( $post->ID );
		if ( ! empty( $post_parent ) ) {
			$post = get_post( $post_parent );
		}

		return $post->post_type;
	}
}
