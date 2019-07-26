<?php
/**
 * Entity resolving stuff.
 *
 * Interface for resolving/simulating varions WP resources,
 * virtual or otherwise.
 *
 * @package wpmu-dev-seo
 */

/**
 * Entity resolving class
 */
class Smartcrawl_Endpoint_Resolver {

	const L_BLOG_HOME = 'front_home_posts';
	const L_STATIC_HOME = 'static_home';
	const L_SEARCH = 'search_page';
	const L_404 = '404_page';
	const L_ARCHIVE = 'archive';
	const L_DATE_ARCHIVE = 'date';
	const L_PT_ARCHIVE = 'post_type_archive';
	const L_TAX_ARCHIVE = 'taxonomy_archive';
	const L_AUTHOR_ARCHIVE = 'author_archive';
	const L_SINGULAR = 'singular';
	const L_BP_GROUPS = 'bp_groups';
	const L_BP_PROFILE = 'bp_profile';
	const L_WOO_SHOP = 'woo_shop';
	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Endpoint_Resolver
	 */
	private static $_instance;
	/**
	 * Current resolved location
	 *
	 * One of the known constants, or false-ish.
	 *
	 * @var string
	 */
	private $_location;
	/**
	 * Overridden environment holder
	 *
	 * @var array
	 */
	private $_env = array();
	/**
	 * Used to store resolution data before simulation
	 *
	 * Used for recovering from simulation
	 *
	 * @var array
	 */
	private $_presimulation_data = array();

	/**
	 * Gets object instance ready for item resolution
	 *
	 * @return Smartcrawl_Endpoint_Resolver instance
	 */
	public static function resolve() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->resolve_location();
		}

		return self::$_instance;
	}

	/**
	 * Resolves current location to one of known constants
	 */
	public function resolve_location() {
		if ( is_front_page() && 'posts' === get_option( 'show_on_front' ) ) {
			$this->set_location( self::L_BLOG_HOME );
        } elseif ( is_home() && 'posts' !== get_option( 'show_on_front' ) ) {
			$this->set_location( self::L_STATIC_HOME );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$this->set_location( self::L_TAX_ARCHIVE );
		} elseif ( is_search() ) {
			$this->set_location( self::L_SEARCH );
		} elseif ( is_author() ) {
			$this->set_location( self::L_AUTHOR_ARCHIVE );
		} elseif ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_id' ) ) { // WooCommerce shop page.
			$this->set_location( self::L_WOO_SHOP );
		} elseif ( is_post_type_archive() ) {
			$this->set_location( self::L_PT_ARCHIVE );
		} elseif ( is_date() ) {
			$this->set_location( self::L_DATE_ARCHIVE );
		} elseif ( is_archive() ) {
			$this->set_location( self::L_ARCHIVE );
		} elseif ( is_404() ) {
			$this->set_location( self::L_404 );
		} elseif ( function_exists( 'groups_get_current_group' ) && 'groups' === bp_current_component() && groups_get_current_group() ) {
			$this->set_location( self::L_BP_GROUPS );
		} elseif ( function_exists( 'bp_current_component' ) && 'profile' === bp_current_component() ) {
			$this->set_location( self::L_BP_PROFILE );
		} elseif ( is_singular() ) {
			$this->set_location( self::L_SINGULAR );
		}
	}

	/**
	 * Sets query context
	 *
	 * @param WP_Query|false $qobj Optional overriding query object.
	 *
	 * @return bool
	 */
	public function set_query_context( $qobj ) {
		$this->_env['query'] = $qobj;

		return ! ! $this->_env['query'];
	}

	/**
	 * Gets query context
	 *
	 * @return WP_Query instance
	 */
	public function get_query_context() {
		if ( isset( $this->_env['query'] ) ) {
			return $this->_env['query'];
		}
		global $wp_query;

		return $wp_query;
	}

	/**
	 * Simulates endpoint post
	 *
	 * Used of onpage getters and checks
	 *
	 * @param WP_Post|int $pid Post or post ID to simulate.
	 *
	 * @return bool
	 */
	public function simulate_post( $pid ) {
		$post = get_post( $pid );
		$query = new WP_Query();
		$query->queried_object = $post;
		$query->queried_object_id = $post->ID;

		return $this->simulate( self::L_SINGULAR, $post, $query );
	}

	public function simulate_taxonomy_term( $term_id ) {
		$term = get_term( $term_id );
		$query = new WP_Query();
		$query->queried_object = $term;
		$query->queried_object_id = $term->term_id;

		return $this->simulate( self::L_TAX_ARCHIVE, null, $query );
	}

	public function simulate_post_type( $post_type ) {
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$post_type = get_post_type_object( $post_type );
		}
		$query = new WP_Query();
		$query->queried_object = $post_type;

		$this->simulate( self::L_PT_ARCHIVE, null, $query );
	}

	public function simulate( $location, $context, $query_context = null ) {
		$this->_presimulation_data[] = array(
			'location'      => $this->get_location(),
			'context'       => $this->get_context(),
			'query_context' => $this->get_query_context(),
		);

		$this->set_context( $context );
		$this->set_location( $location );
		$this->set_query_context( $query_context );

		return true;
	}

	/**
	 * Gets resolved or simulated location
	 *
	 * @return string Location
	 */
	public function get_location() {
		return $this->_location;
	}

	/**
	 * Sets resolved location
	 *
	 * @param string $loc One of the defined location constants.
	 *
	 * @return bool
	 */
	public function set_location( $loc ) {
		$this->_location = $loc;

		return ! ! $this->_location;
	}

	/**
	 * Gets post context
	 *
	 * @return WP_Post Post
	 */
	public function get_context() {
		if ( isset( $this->_env['context'] ) ) {
			return $this->_env['context'];
		}

		return get_post();
	}

	/**
	 * Sets post context
	 *
	 * @param WP_Post|false $pobj Optional overriding post object.
	 *
	 * @return bool
	 */
	public function set_context( $pobj ) {
		$this->_env['context'] = $pobj;

		return ! ! $this->_env['context'];
	}

	/**
	 * Stops endpoint simulation
	 *
	 * @return bool Status
	 */
	public function stop_simulation() {
		$data = array_pop( $this->_presimulation_data );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}

		$status = true;
		foreach ( $data as $key => $val ) {
			$method = "set_{$key}";
			if ( ! is_callable( array( $this, $method ) ) ) {
				continue;
			}

			if ( ! call_user_func( array( $this, $method ), $val ) ) {
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * Resets environment overrides
	 */
	public function reset_env() {
		$this->_env = array();
	}

	/**
	 * Check if resolved endpoint is singular
	 *
	 * @param string $location Optional location to check.
	 *
	 * @return bool
	 */
	public function is_singular( $location = false ) {
		$location = ! empty( $location ) ? $location : $this->get_location();

		return self::L_SINGULAR === $location;
	}

}
