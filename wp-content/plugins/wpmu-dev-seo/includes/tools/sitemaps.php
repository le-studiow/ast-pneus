<?php
/**
 * SmartCrawl pages optimization classes
 *
 * Smartcrawl_Xml_Sitemap::generate_sitemap()
 * inspired by WordPress SEO by Joost de Valk (http://yoast.com/wordpress/seo/).
 *
 * @package wpmu-dev-seo
 * @since 1.3
 */

/**
 * Sitemap handling class
 *
 * phpcs:ignoreFile -- Since the class has a lot of file operations
 */
class Smartcrawl_Xml_Sitemap {

	const EXTRAS_STORAGE = 'wds-sitemap-extras';
	const IGNORE_URLS_STORAGE = 'wds-sitemap-ignore_urls';
	const IGNORE_IDS_STORAGE = 'wds-sitemap-ignore_post_ids';
	const SITEMAP_PRISTINE_OPTION = 'wds_sitemap_cache_pristine';
	/**
	 * Static instance
	 *
	 * @var Smartcrawl_Xml_Sitemap
	 */
	private static $_instance;
	/**
	 * Raw sitemap items
	 *
	 * @var array
	 */
	protected $_items;
	/**
	 * Raw options
	 *
	 * @var array
	 */
	private $_data;
	/**
	 * Database handle
	 *
	 * @var object WPDB instance
	 */
	private $_db;

	/**
	 * Constructor
	 */
	private function __construct() {
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

	/**
	 * Check whether the sitemap output file is writable.
	 *
	 * @return bool
	 */
	public static function is_sitemap_path_writable() {
		$file = smartcrawl_get_sitemap_path();

		if ( file_exists( $file ) ) {
			$fp = @fopen( $file, 'a' );
			if ( $fp ) {
				fclose( $fp );

				return true;
			}

			return false;
		}

		return is_writable( dirname( $file ) );
	}

	/**
	 * Extra URLs storage setter
	 *
	 * @param array $extras New extra URLs.
	 *
	 * @return bool
	 */
	public static function set_extra_urls( $extras = array() ) {
		if ( ! is_array( $extras ) ) {
			return false;
		}

		return update_option( self::EXTRAS_STORAGE, array_filter( array_unique( $extras ) ) );
	}

	/**
	 * Ignore URLs storage setter
	 *
	 * @param array $extras New ignore URLs.
	 *
	 * @return bool
	 */
	public static function set_ignore_urls( $extras = array() ) {
		if ( ! is_array( $extras ) ) {
			return false;
		}

		return update_option( self::IGNORE_URLS_STORAGE, array_filter( array_unique( $extras ) ) );
	}

	/**
	 * Ignore post IDs storage setter
	 *
	 * @param array $extras New ignore post IDs.
	 *
	 * @return bool
	 */
	public static function set_ignore_ids( $extras = array() ) {
		if ( ! is_array( $extras ) ) {
			return false;
		}

		return update_option( self::IGNORE_IDS_STORAGE, array_filter( array_unique( $extras ) ) );
	}

	/**
	 * Sitemap generation wrapper
	 *
	 * @return bool
	 */
	public function generate_sitemap() {
		global $wpdb;

		$data = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SITEMAP );
		if ( empty( $data['sitemappath'] ) ) {
			return false;
		}

		$this->_data = $data;
		$this->_db = $wpdb;

		$this->_init_items();
		$smartcrawl_options = Smartcrawl_Settings::get_options();

		Smartcrawl_Logger::info( '(Re)generating sitemap' );

		if ( is_admin() && defined( 'SMARTCRAWL_SITEMAP_SKIP_ADMIN_UPDATE' ) && SMARTCRAWL_SITEMAP_SKIP_ADMIN_UPDATE ) {
			Smartcrawl_Logger::debug( 'Skipping sitemap generation in admin context' );

			return false;
		}

		// this can take a whole lot of time on big blogs.
		$this->_set_time_limit( 120 );

		if ( ! $this->_items ) {
			$this->_load_all_items();
		}

		$map = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$hide_branding = Smartcrawl_White_Label::get()->is_hide_wpmudev_branding();

		if ( ! empty( $smartcrawl_options['sitemap-stylesheet'] ) ) {
			$map .= $hide_branding
				? $this->_get_stylesheet( 'xml-sitemap-whitelabel' )
				: $this->_get_stylesheet( 'xml-sitemap' );
		}

		$image_schema_url = 'http://www.google.com/schemas/sitemap-image/1.1';
		$image_schema = ! empty( $smartcrawl_options['sitemap-images'] ) ? "xmlns:image='{$image_schema_url}'" : '';
		$map .= "<urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd' xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' {$image_schema}>\n";

		foreach ( $this->_items as $item ) {
			$map .= "<url>\n";
			foreach ( $item as $key => $val ) {
				if ( 'images' === $key ) {
					if ( ! $val ) {
						continue;
					}
					if ( empty( $smartcrawl_options['sitemap-images'] ) ) {
						continue;
					}
					foreach ( $item['images'] as $image ) {
						$text = $image['title'] ? $image['title'] : $image['alt'];
						$map .= '<image:image>';
						$map .= '<image:loc>' . esc_url( $image['src'] ) . '</image:loc>';
						$map .= '<image:title>' . ent2ncr( esc_attr( $text ) ) . '</image:title>';
						$map .= "</image:image>\n";
					}
				} else {
					$map .= "<{$key}>{$val}</{$key}>\n";
				}
			}
			$map .= "</url>\n\n";
		}
		$map .= '</urlset>';

		$this->_write_sitemap( $map );
		$this->_postprocess_sitemap();

		Smartcrawl_Logger::info( 'Sitemap regenerated' );

		return true;
	}

	/**
	 * Initialize internal storage
	 *
	 * @return void
	 */
	private function _init_items() {
		$this->_items = array();
	}

	/**
	 * Attempt to set time limit
	 *
	 * @param int $amount Optional extension amount.
	 *
	 * @return bool
	 */
	protected function _set_time_limit( $amount = 120 ) {
		$amount = empty( $amount ) || ! is_numeric( $amount )
			? 120
			: (int) $amount;
		// Check manual override.
		if ( defined( 'SMARTCRAWL_SITEMAP_SKIP_TIME_LIMIT_SETTING' ) && SMARTCRAWL_SITEMAP_SKIP_TIME_LIMIT_SETTING ) {
			return false;
		}

		// Check safe mode.
		$is_safe_mode = strtolower( ini_get( 'safe_mode' ) );
		if ( ! empty( $is_safe_mode ) && 'off' !== $is_safe_mode ) {
			Smartcrawl_Logger::debug( 'Safe mode on, skipping time limit set.' );

			return false;
		}

		// Check disabled state.
		$disabled = array_map( 'trim', explode( ',', ini_get( 'disable_functions' ) ) );
		if ( in_array( 'set_time_limit', $disabled, true ) ) {
			Smartcrawl_Logger::debug( 'Time limit setting disabled, skipping.' );

			return false;
		}

		return set_time_limit( $amount );
	}

	/**
	 * Loads all items that will get into a sitemap.
	 */
	private function _load_all_items() {
		$this->_add_item( home_url( '/' ), 1, 'daily' ); // Home URL.
		$this->_load_post_items();
		$this->_load_taxonomy_items();
		// Load BuddyPress-specific items.
		if ( defined( 'BP_VERSION' ) && smartcrawl_is_main_bp_site() ) {
			$this->_load_buddypress_group_items();
			$this->_load_buddypress_profile_items();
		}

		$this->_load_extra_items();
	}

	/**
	 * Adds a single item into the sitemap queue
	 *
	 * @param string $url URL to add.
	 * @param float $priority Sitemap item priority.
	 * @param string $freq Optional item frequency.
	 * @param int $time Optional item creation time, defaults to now.
	 * @param string $content Raw item content, for images extraction, optional.
	 *
	 * @return bool
	 */
	protected function _add_item( $url, $priority, $freq = 'weekly', $time = false, $content = '' ) {
		if ( ! $this->_items ) {
			$this->_init_items();
		}
		$time = intval( $time ) > 0
			? $time
			: time();
		$offset = date( 'O', $time );

		$ignore_urls = self::get_ignore_urls();
		if ( ! empty( $ignore_urls ) ) {
			if ( preg_match( smartcrawl_get_relative_urls_regex( $ignore_urls ), $url ) ) {
				return false;
			}
		}

		$item = array(
			'loc'        => esc_url( $url ),
			'lastmod'    => date( 'Y-m-d\TH:i:s', $time ) . substr( $offset, 0, 3 ) . ':' . substr( $offset, - 2 ),
			'changefreq' => strtolower( apply_filters( 'wds_sitemap_changefreq', $freq, $url, $priority, $time, $content ) ),
			'priority'   => sprintf( '%.1f', $priority ),
		);

		$item['images'] = $content ? $this->_extract_images( $content ) : array();

		$this->_items[] = $item;

		return true;
	}

	/**
	 * Ignore URLs storage getter
	 *
	 * @return array Ignore sitemap URLs.
	 */
	public static function get_ignore_urls() {
		$extras = get_option( self::IGNORE_URLS_STORAGE );
		$ignores = empty( $extras ) || ! is_array( $extras )
			? array()
			: array_filter( array_unique( $extras ) );

		return apply_filters( 'wds-sitemaps-ignore_urls', $ignores );
	}

	/**
	 * Extracts images from content
	 *
	 * @param string $content Content to process.
	 *
	 * @return array
	 */
	private function _extract_images( $content ) {
		if ( smartcrawl_is_switch_active( 'SMARTCRAWL_SITEMAP_SKIP_IMAGES' ) ) {
			return array();
		}

		preg_match_all( '|(<img [^>]+?>)|', $content, $matches, PREG_SET_ORDER );
		if ( ! $matches ) {
			return false;
		}

		$images = array();
		foreach ( $matches as $tmp ) {
			$img = $tmp[0];

			$res = preg_match( '/src=("|\')([^"\']+)("|\')/', $img, $match );
			$src = $res ? $match[2] : '';
			if ( strpos( $src, 'http' ) !== 0 ) {
				$src = site_url( $src );
			}

			$res = preg_match( '/title=("|\')([^"\']+)("|\')/', $img, $match );
			$title = $res ? str_replace( '-', ' ', str_replace( '_', ' ', $match[2] ) ) : '';

			$res = preg_match( '/alt=("|\')([^"\']+)("|\')/', $img, $match );
			$alt = $res ? str_replace( '-', ' ', str_replace( '_', ' ', $match[2] ) ) : '';

			$images[] = array(
				'src'   => $src,
				'title' => $title,
				'alt'   => $alt,
			);
		}

		return $images;
	}

	/**
	 * Loads posts into the sitemap.
	 */
	private function _load_post_items() {
		$smartcrawl_options = Smartcrawl_Settings::get_options();

		$get_content = ! empty( $smartcrawl_options['sitemap-images'] ) ? 'post_content,' : '';

		// Cache the static front page state.
		$front_page = 'page' === get_option( 'show_on_front' )
			? get_option( 'page_on_front' )
			: false;

		$types_query = '';
		$types = array();
		$type_placeholders = array();
		$raw = get_post_types( array(
			'public'  => true,
			'show_ui' => true,
		) );
		foreach ( $raw as $type ) {
			if ( ! empty( $smartcrawl_options[ 'post_types-' . $type . '-not_in_sitemap' ] ) ) {
				continue;
			}
			$types[] = $type;
			$type_placeholders[] = '%s';
		}
		if ( ! empty( $types ) && ! empty( $type_placeholders ) ) {
			$format = join( ',', $type_placeholders );
			$types_query = $this->_db->prepare( "AND post_type IN ({$format})", $types );
		}

		$id_query = '';
		$ignore_ids = array_values( array_filter( array_map( 'intval', self::get_ignore_ids() ) ) );
		if ( ! empty( $ignore_ids ) ) {
			$format = join( ',', array_fill( 0, count( $ignore_ids ), '%d' ) );
			$id_query = 'AND ID NOT IN (' . join( ',', $ignore_ids ) . ')';
			$id_query = $this->_db->prepare( "AND ID NOT IN ({$format})", $ignore_ids );
		}

		$query = "SELECT ID, {$get_content} post_parent, post_type, post_modified FROM {$this->_db->posts} " .
		         "WHERE post_status = 'publish' " .
		         "AND post_password = '' " .
		         "{$types_query} " .
		         "{$id_query} " .
		         'ORDER BY post_parent ASC, post_modified DESC LIMIT ' . intval( SMARTCRAWL_SITEMAP_POST_LIMIT );
		$posts = $this->_db->get_results( $query );
		$posts = $posts ? $posts : array();

		foreach ( $posts as $post ) {
			if ( smartcrawl_get_value( 'meta-robots-noindex', $post->ID ) ) {
				continue;
			} // Not adding no-index files.
			if ( smartcrawl_get_value( 'redirect', $post->ID ) ) {
				continue;
			} // Don't add redirected URLs.

			// Check for inclusion.
			if ( ! apply_filters( 'wds-sitemaps-include_post', true, $post ) ) {
				continue;
			}

			// If this is a page and it's actually the one set as static front, pass.
			if ( 'page' === $post->post_type && $front_page === $post->ID ) {
				continue;
			}

			$link = get_permalink( $post->ID );

			$canonical = smartcrawl_get_value( 'canonical', $post->ID );
			$link = $canonical ? $canonical : $link;

			$priority = smartcrawl_get_value( 'sitemap-priority', $post->ID );
			$priority = apply_filters( 'wds-post-priority', (
			$priority
				? $priority
				: ( $post->post_parent ? 0.6 : 0.8 )
			), $post );

			$content = isset( $post->post_content ) ? $post->post_content : '';
			$modified_ts = ! empty( $post->post_modified ) ? strtotime( $post->post_modified ) : time();
			$modified_ts = $modified_ts > 0 ? $modified_ts : time();

			$this->_add_item(
				$link,
				$priority,
				'weekly',
				$modified_ts,
				$content
			);
		}
	}

	/**
	 * Ignore post IDs storage getter
	 *
	 * @return array Ignore sitemap post IDs
	 */
	public static function get_ignore_ids() {
		$extras = get_option( self::IGNORE_IDS_STORAGE );

		return empty( $extras ) || ! is_array( $extras )
			? array()
			: array_filter( array_unique( $extras ) );
	}

	/**
	 * Loads taxonomies into the sitemap.
	 */
	private function _load_taxonomy_items() {
		if ( smartcrawl_is_switch_active( 'SMARTCRAWL_SITEMAP_SKIP_TAXONOMIES' ) ) {
			return false;
		}

		$smartcrawl_options = Smartcrawl_Settings::get_options();

		$tax = array();
		$raw = get_taxonomies( array(
			'public'  => true,
			'show_ui' => true,
		), 'objects' );
		foreach ( $raw as $tid => $taxonomy ) {
			if ( ! empty( $smartcrawl_options[ 'taxonomies-' . $taxonomy->name . '-not_in_sitemap' ] ) ) {
				continue;
			}
			$tax[] = $taxonomy->name;
		}
		if ( empty( $tax ) ) {
			return true;
		} // All done here.

		$terms = get_terms( $tax, array( 'hide_empty' => true ) );

		foreach ( $terms as $term ) {
			if ( smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_noindex' ) ) {
				continue;
			}

			$canonical = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_canonical' );
			$link = $canonical ? $canonical : get_term_link( $term, $term->taxonomy );

			$priority = apply_filters( 'wds-term-priority', (
			$term->count > 10
				? 0.6
				: ( $term->count > 3 ? 0.4 : 0.2 )
			), $term );

			// -------------------------------------- Potential kludge
			$q = new WP_Query( array(
				'tax_query'      => array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'id',
					'terms'    => $term->term_id,
				),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => 1,
			) );
			$time = $q->posts ? strtotime( $q->posts[0]->post_date ) : time();
			// -------------------------------------- Potential kludge
			$this->_add_item(
				$link,
				$priority,
				'weekly',
				$time
			);
		}
	}

	/**
	 * Loads BuddyPress Group items.
	 */
	private function _load_buddypress_group_items() {
		if ( ! function_exists( 'groups_get_groups' ) ) {
			return false;
		} // No BuddyPress Groups, bail out.

		$smartcrawl_options = Smartcrawl_Settings::get_options();
		if ( ! defined( 'BP_VERSION' ) ) {
			return false;
		} // Nothing to do.
		if ( empty( $smartcrawl_options['sitemap-buddypress-groups'] ) ) {
			return false;
		} // Nothing to do.

		$groups = groups_get_groups( array( 'per_page' => SMARTCRAWL_BP_GROUPS_LIMIT ) );
		$groups = ! empty( $groups['groups'] ) ? $groups['groups'] : array();

		foreach ( $groups as $group ) {
			if ( ! empty( $smartcrawl_options["sitemap-buddypress-exclude-buddypress-group-{$group->slug}"] ) ) {
				continue;
			}

			$link = bp_get_group_permalink( $group );
			$this->_add_item(
				$link,
				0.2, // $priority.
				'weekly',
				strtotime( $group->last_activity ),
				$group->description
			);
		}

		return true;
	}

	/**
	 * Loads BuddyPress profile items.
	 */
	private function _load_buddypress_profile_items() {
		$smartcrawl_options = Smartcrawl_Settings::get_options();
		if ( ! defined( 'BP_VERSION' ) ) {
			return false;
		} // Nothing to do.
		if ( ! function_exists( 'bp_core_get_users' ) ) {
			return false;
		}
		if ( empty( $smartcrawl_options['sitemap-buddypress-profiles'] ) ) {
			return false;
		} // Nothing to do.

		$users = bp_core_get_users( array( 'per_page' => SMARTCRAWL_BP_PROFILES_LIMIT ) );
		$users = ! empty( $users['users'] ) ? $users['users'] : array();

		foreach ( $users as $user ) {
			$wp_user = new WP_User( $user->id );
			$role = ! empty( $wp_user->roles[0] ) ? $wp_user->roles[0] : false;
			if ( ! empty( $smartcrawl_options["sitemap-buddypress-roles-exclude-profile-role-{$role}"] ) ) {
				continue;
			}

			$link = bp_core_get_user_domain( $user->id );
			$this->_add_item(
				$link,
				0.2,
				'weekly',
				strtotime( $user->last_activity ),
				$user->display_name
			);
		}
	}

	/**
	 * Loads extra items
	 *
	 * @return bool Status
	 */
	private function _load_extra_items() {
		$extras = self::get_extra_urls();
		foreach ( $extras as $url ) {
			$this->_add_item(
				$url,
				0.5,
				'weekly',
				time()
			);
		}

		return true;
	}

	/**
	 * Extra URLs storage getter
	 *
	 * @return array Extra sitemap URLs
	 */
	public static function get_extra_urls() {
		$extras = get_option( self::EXTRAS_STORAGE );

		return empty( $extras ) || ! is_array( $extras )
			? array()
			: array_filter( array_unique( $extras ) );
	}

	/**
	 * Gets XSL stylesheet XML instruction
	 *
	 * @param string $xsl XSL stylesheet to fetch.
	 *
	 * @return string
	 */
	private function _get_stylesheet( $xsl ) {
		$plugin_dir_url = SMARTCRAWL_PLUGIN_URL;

		return "<?xml-stylesheet type='text/xml' href='{$plugin_dir_url}admin/templates/xsl/{$xsl}.xsl'?>\n";
	}

	/**
	 * Writes sitemap source to a file
	 *
	 * @param string $map Generated sitemap as string.
	 *
	 * @return bool
	 */
	protected function _write_sitemap( $map ) {
		$file = smartcrawl_get_sitemap_path();
		$status = ! ! @file_put_contents( $file, $map );
		if ( ! $status ) {
			Smartcrawl_Logger::error( "Failed writing sitemap file to [{$file}]" );
		}

		$f = @fopen( "{$file}.gz", 'w' );
		if ( ! $f ) {
			Smartcrawl_Logger::error( "Failed writing compressed sitemap file to [{$file}.gz]" );

			return false;
		}

		@fwrite( $f, gzencode( $map, 9 ) );
		@fclose( $f );

		return true;
	}

	/**
	 * Postprocesses the sitemap
	 *
	 * @return void
	 */
	private function _postprocess_sitemap() {
		// Throw a hook.
		do_action( 'wds_sitemap_created' );

		$this->notify_engines();

		// Update sitemap meta data.
		update_option( 'wds_sitemap_dashboard', array(
			'items' => count( $this->_items ),
			'time'  => time(),
		) );
	}

	/**
	 * Notifies search engines of the latest sitemap update
	 *
	 * @param bool $forced Whether to forcefully do engine notification.
	 *
	 * @return bool
	 */
	public static function notify_engines( $forced = false ) {
		if ( smartcrawl_is_switch_active( 'SMARTCRAWL_SITEMAP_SKIP_SE_NOTIFICATION' ) ) {
			Smartcrawl_Logger::debug( 'Skipping SE update notification.' );

			return false;
		}

		$smartcrawl_options = Smartcrawl_Settings::get_options();
		if ( empty( $smartcrawl_options['sitemapurl'] ) ) {
			return false;
		}

		$result = array();
		$now = time();

		if ( $forced || ! empty( $smartcrawl_options['ping-google'] ) ) {
			do_action( 'wds_before_search_engine_update', 'google' );
			$resp = wp_remote_get( 'http://www.google.com/webmasters/tools/ping?sitemap=' . esc_url( smartcrawl_get_sitemap_url() ) );
			$result['google'] = array(
				'response' => $resp,
				'time'     => $now,
			);
			if ( is_wp_error( $resp ) ) {
				do_action( 'wds_after_search_engine_update', 'google', false, $resp );
			} else {
				do_action( 'wds_after_search_engine_update', 'google', (bool) ( 200 === (int) wp_remote_retrieve_response_code( $resp ) ), $resp );
			}
		}

		if ( $forced || ! empty( $smartcrawl_options['ping-bing'] ) ) {
			do_action( 'wds_before_search_engine_update', 'bing' );
			$resp = wp_remote_get( 'http://www.bing.com/webmaster/ping.aspx?sitemap=' . esc_url( smartcrawl_get_sitemap_url() ) );
			$result['bing'] = array(
				'response' => $resp,
				'time'     => $now,
			);
			if ( is_wp_error( $resp ) ) {
				do_action( 'wds_after_search_engine_update', 'bing', false, $resp );
			} else {
				do_action( 'wds_after_search_engine_update', 'bing', (bool) ( 200 === (int) wp_remote_retrieve_response_code( $resp ) ), $resp );
			}
		}

		update_option( 'wds_engine_notification', $result );

		return true;
	}

	/**
	 * Check whether we have domain mapped admin
	 *
	 * @return bool
	 */
	private function _is_admin_mapped() {
		return (bool) ( is_multisite() && ( is_admin() || is_network_admin() ) && class_exists( 'domain_map' ) );
	}

	public function set_sitemap_pristine( $value ) {
		$pristine = $this->get_sitemap_pristine_option();
		$current_site_id = get_current_blog_id();

		if ( $value ) {
			if ( ! in_array( $current_site_id, $pristine ) ) {
				$pristine[] = $current_site_id;
				$this->update_sitemap_pristine_option( $pristine );
			}
		} else {
			if ( ! is_multisite() || smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) ) {
				// The whole network (or single site) is out of date now so drop everything
				$this->delete_sitemap_pristine_option();
			} else {
				$this->update_sitemap_pristine_option(
					array_diff( $pristine, array( $current_site_id ) )
				);
			}
		}
	}

	public function is_sitemap_pristine() {
		return in_array(
			get_current_blog_id(),
			$this->get_sitemap_pristine_option()
		);
	}

	private function get_sitemap_pristine_option() {
		$value = get_site_option( self::SITEMAP_PRISTINE_OPTION, array() );
		return is_array( $value )
			? $value
			: array();
	}

	private function update_sitemap_pristine_option( $value ) {
		return update_site_option( self::SITEMAP_PRISTINE_OPTION, $value );
	}

	private function delete_sitemap_pristine_option() {
		return delete_site_option( self::SITEMAP_PRISTINE_OPTION );
	}
}
