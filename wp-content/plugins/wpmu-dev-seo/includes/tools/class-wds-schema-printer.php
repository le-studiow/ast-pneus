<?php

/**
 * Outputs JSON+LD schema.org data to the page
 */
class Smartcrawl_Schema_Printer extends Smartcrawl_WorkUnit {

	const PERSON = 'Person';
	const WEBSITE = 'WebSite';
	const ARTICLE = 'Article';
	const ORGANIZATION = 'Organization';
	const IMAGE = 'ImageObject';

	/**
	 * Singleton instance holder
	 */
	private static $_instance;

	private $_is_running = false;
	private $_is_done = false;

	public function __construct() {
	}

	/**
	 * Boot the hooking part
	 */
	public static function run() {
		self::get()->_add_hooks();
	}

	/**
	 * Singleton instance getter
	 *
	 * @return object Smartcrawl_Schema_Printer instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * First-line dispatching of schema tags injection
	 */
	public function dispatch_schema_injection() {
		if ( ! ! $this->_is_done ) {
			return false;
		}

		$social = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		if ( ! empty( $social['disable-schema'] ) ) {
			$this->_is_done = true;

			return false; // Disabled
		}

		$data = array();
		$data = apply_filters( 'wds-schema-data', $data );

		if ( empty( $data ) ) {
			return false;
		}

		if ( empty( $data ) ) {
			return false;
		}

		$this->_is_done = true;

		echo '<script type="application/ld+json">' . wp_json_encode( $data ) . "</script>\n";
	}

	/**
	 * Determine schema data to inject
	 *
	 * Hooks up to `wds-schema-data` filter
	 *
	 * @param array $data Data gathered this far
	 *
	 * @return array Schema data
	 */
	public function dispatch_schema_data( $data = array() ) {
		// First up, global stuff
		$site = $this->get_site_schema_data();
		if ( ! empty( $site ) ) {
			$data[] = $site;
		}

		// Next up, individual (if applicable)
		if ( $this->apply_filters( 'is_singular', is_singular() ) ) {
			$post = $this->get_post_schema_data( $this->apply_filters( 'post', get_post() ) );
			if ( ! empty( $post ) ) {
				$data[] = $post;
			}
		}

		return $data;
	}

	/**
	 * Gets schema data for the whole web site
	 *
	 * @return array Site schema data
	 */
	public function get_site_schema_data() {
		$data = array();
		$social = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		$social = is_array( $social ) ? $social : array();

		$description = get_bloginfo( 'description' );
		$data['about'] = (string) $this->apply_filters( 'site-data-about', $description );
		$data['description'] = (string) $this->apply_filters( 'site-data-description', $description );

		$data['dateModified'] = get_lastpostdate();

		$data['encoding'] = get_bloginfo( 'charset' );

		$headline = get_bloginfo( 'name' );
		$name = ! empty( $social['sitename'] ) ? $social['sitename'] : $headline;
		$data['name'] = (string) $this->apply_filters( 'site-data-name', $name );
		$data['headline'] = (string) $this->apply_filters( 'site-data-headline', $headline );

		$data['inLanguage'] = get_bloginfo( 'language' );

		$data['url'] = site_url();

		$data['publisher'] = $this->get_owner_schema_data( false );

		$keywords = $this->get_keywords( Smartcrawl_Endpoint_Resolver::L_BLOG_HOME );
		if ( ! empty( $keywords ) ) {
			$data['keywords'] = join( ',', $keywords );
		}

		// thumbnailUrl | image
		// potentialAction
		return (array) $this->create_schema( self::WEBSITE, $this->apply_filters( 'site-data', $data ) );
	}

	/**
	 * Gets owner schema data
	 *
	 * @param bool $context Include schema context, defaults to true
	 *
	 * @return array Owner schema data
	 */
	public function get_owner_schema_data( $context = true ) {
		$data = array();
		$social = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		$social = is_array( $social ) ? $social : array();

		// address
		// brand
		// $data['email'] = get_option('admin_email');
		// logo
		// subOrganization
		// description
		$type = ! empty( $social['schema_type'] ) && self::PERSON === $social['schema_type']
			? self::PERSON
			: self::ORGANIZATION;

		if ( self::PERSON === $type ) {
			$data['name'] = ! empty( $social['override_name'] )
				? $social['override_name']
				: $this->get_full_name( Smartcrawl_Model_User::owner() );
		} else {
			$data['name'] = ! empty( $social['organization_name'] )
				? $social['organization_name']
				: get_bloginfo( 'name' );
		}

		$urls = array();
		foreach ( $social as $key => $value ) {
			if ( preg_match( '/_url$/', $key ) && ! empty( $value ) ) {
				$urls[] = $value;
			}
		}
		if ( ! empty( $urls ) ) {
			$data['sameAs'] = $urls;
		}

		return (array) $this->create_schema( $type, $this->apply_filters( 'owner-data', $data ), $context );
	}

	/**
	 * Gets user full name
	 *
	 * Falls back to display name
	 *
	 * @param WP_User|Smartcrawl_Model_User User object
	 *
	 * @return string Full name
	 */
	public function get_full_name( $user ) {
		$name = '';
		if ( ! is_object( $user ) || (
				! ( $user instanceof WP_User )
				&&
				! ( $user instanceof Smartcrawl_Model_User )
			)
		) {
			return $name;
		}

		if ( ! ( $user instanceof Smartcrawl_Model_User ) ) {
			$model = Smartcrawl_Model_User::get( $user->ID );
		} else {
			$model = $user;
		}

		$name = $model->get_full_name();

		return $this->apply_filters( 'user-full_name', $name, $user );
	}

	/**
	 * Wraps up generic schema data
	 *
	 * @param string $type Schema type
	 * @param array $data Raw schema point data
	 * @param bool $context Include context - defaults to true
	 *
	 * @return array Augumeted schema data
	 */
	public function create_schema( $type, $data, $context = true ) {
		if ( ! is_array( $data ) ) {
			return array();
		}
		if ( empty( $data ) ) {
			return $data;
		}

		if ( ! in_array( $type, $this->get_schema_types(), true ) ) {
			return $data;
		}

		if ( ! empty( $context ) ) {
			$data['@context'] = 'http://schema.org';
		}
		$data['@type'] = $type;

		ksort( $data );

		return $data;
	}

	/**
	 * Gets a list of known schema types
	 *
	 * @return array List of schema types
	 */
	public function get_schema_types() {
		return array(
			self::PERSON,
			self::WEBSITE,
			self::ARTICLE,
			self::ORGANIZATION,
			self::IMAGE,
		);
	}

	/**
	 * Creates schema data for a post
	 *
	 * @param WP_Post $post Post object to create chema for
	 *
	 * @return array Post schema data
	 */
	public function get_post_schema_data( $post ) {
		$data = array();
		if ( ! is_object( $post ) || ! ( $post instanceof WP_Post ) ) {
			return $data;
		}

		// pageStart/pageEnd
		// wordCount
		$data['author'] = $this->get_author_schema_data( $post, false );

		$data['commentCount'] = get_comments_number( $post->ID );

		$data['dateModified'] = get_the_modified_date( 'Y-m-d\TH:i:s', $post );
		$data['datePublished'] = get_the_date( 'Y-m-d\TH:i:s', $post );

		$post_title = get_the_title( $post );
		$data['name'] = (string) $this->apply_filters( 'post-data-name', $post_title, $post );

		$title = Smartcrawl_Meta_Value_Helper::get()->get_title( $post_title );
		$data['headline'] = (string) $this->apply_filters( 'post-data-headline', $title, $post );

		$keywords = $this->get_keywords( Smartcrawl_Endpoint_Resolver::L_SINGULAR, $post );
		if ( ! empty( $keywords ) ) {
			$data['keywords'] = join( ',', $keywords );
		}

		$data['publisher'] = $this->get_article_publisher( false );
		$data['mainEntityOfPage'] = get_permalink( $post->ID );

		if ( has_post_thumbnail( $post ) ) {
			$thumb = '';
			$height = 0;
			$width = 0;
			$tid = get_post_thumbnail_id( $post );
			if ( ! empty( $tid ) ) {
				list( $thumb, $width, $height ) = wp_get_attachment_image_src( $tid, array( 700, 700 ) );
				$data['thumbnailUrl'] = (string) $this->apply_filters( 'post-data-thumbnailUrl', $thumb );
				$data['image'] = (array) $this->apply_filters(
					'post-data-image',
					$this->create_schema( self::IMAGE, array(
						'url'    => $thumb,
						'height' => $height,
						'width'  => $width,
					) ),
					$post
				);
			}
		}

		$fallback = smartcrawl_get_trimmed_excerpt( $post->post_excerpt, $post->post_content );
		$description = Smartcrawl_Meta_Value_Helper::get()->get_description( $fallback );
		$data['description'] = ! empty( $description ) ? $description : $fallback;

		$data['url'] = get_the_permalink( $post->ID );

		return (array) $this->create_schema( self::ARTICLE, $this->apply_filters( 'post-data', $data, $post ) );
	}

	private function get_keywords( $location, $context = null ) {
		$resolver = Smartcrawl_Endpoint_Resolver::resolve();
		$resolver->simulate( $location, $context );
		$keywords = Smartcrawl_Meta_Value_Helper::get()->get_keywords();
		$resolver->stop_simulation();

		return $keywords;
	}

	/**
	 * Gets schema data for post author
	 *
	 * @param WP_Post $post Post to get the data for
	 * @param bool $context Include schema context, defaults to true
	 *
	 * @return array User schema data
	 */
	public function get_author_schema_data( $post, $context = true ) {
		$data = array();
		if ( ! is_object( $post ) || ! ( $post instanceof WP_Post ) ) {
			return $data;
		}

		$user_id = $post->post_author;
		if ( empty( $user_id ) ) {
			return $data;
		}

		$user = new WP_User( $user_id );

		return (array) $this->get_user_schema_data( $user, $context );
	}

	/**
	 * Gets schema data for user
	 *
	 * @param WP_User $user User to create schema data for
	 * @param bool $context Include schema context, defaults to true
	 *
	 * @return array User schema data
	 */
	public function get_user_schema_data( $user, $context = true ) {
		if ( ! is_object( $user ) || ! ( $user instanceof WP_User ) ) {
			return array();
		}
		$data['name'] = $this->get_full_name( $user );
		$data['url'] = $this->get_user_url( $user );
		// $data['email'] = $user->user_email;
		$urls = $this->get_user_urls( $user );
		if ( ! empty( $urls ) ) {
			$data['sameAs'] = $urls;
		}

		return (array) $this->create_schema( self::PERSON, $this->apply_filters( 'user-data', $data, $user ), $context );
	}

	/**
	 * Gets user URL
	 *
	 * Falls back to posts URL
	 *
	 * @param WP_User User object
	 *
	 * @return string User URL
	 */
	public function get_user_url( $user ) {
		$url = '';
		if ( ! is_object( $user ) || ! ( $user instanceof WP_User ) ) {
			return $url;
		}
		$model = Smartcrawl_Model_User::get( $user->ID );

		$url = $model->get_user_url();

		return $this->apply_filters( 'user-url', $url, $user );
	}

	/**
	 * Gets a list of known user URLs
	 *
	 * @param WP_User user object
	 *
	 * @return array Known user ULRs
	 */
	public function get_user_urls( $user ) {
		$urls = array();
		if ( ! is_object( $user ) || ! ( $user instanceof WP_User ) ) {
			return $urls;
		}
		$model = Smartcrawl_Model_User::get( $user->ID );

		$url = $model->get_user_urls();

		return $this->apply_filters( 'user-urls', $urls, $user );
	}

	public function get_article_publisher() {
		$social = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		$social = is_array( $social ) ? $social : array();

		$data = $this->get_owner_schema_data( false );

		if ( ! empty( $social['schema_type'] ) && self::PERSON === $social['schema_type'] ) {
			$data['@type'] = self::ORGANIZATION;
		}
		$data['logo'] = $this->get_site_logo();

		return $data;
	}

	/**
	 * Returns site logo array
	 *
	 * Used mainly for article publisher
	 *
	 * @return array
	 */
	public function get_site_logo() {
		$admin = Smartcrawl_Model_User::owner();
		$aid = ! empty( $admin->ID ) ? $admin->ID : '';
		$url = get_avatar_url( $aid, array( 60, 60 ) );

		$social = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		$social = is_array( $social ) ? $social : array();
		if ( ! empty( $social['schema_type'] ) && self::PERSON !== $social['schema_type'] ) {
			if ( ! empty( $social['organization_logo'] ) ) {
				$url = esc_url( $social['organization_logo'] );
			}
		}

		return $this->apply_filters( 'site-logo', array(
			'@type'  => self::IMAGE,
			'url'    => $url,
			'height' => 60,
			'width'  => 60,
		) );
	}

	public function get_filter_prefix() {
		return 'wds-schema';
	}

	private function _add_hooks() {
		// Do not double-bind
		if ( $this->apply_filters( 'is_running', $this->_is_running ) ) {
			return true;
		}

		add_action( 'wp_head', array( $this, 'dispatch_schema_injection' ), 50 );
		add_action( 'wds_head-after_output', array( $this, 'dispatch_schema_injection' ) );

		add_filter( 'wds-schema-data', array( $this, 'dispatch_schema_data' ) );

		$this->_is_running = true;
	}

}
