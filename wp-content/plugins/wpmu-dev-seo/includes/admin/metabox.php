<?php
/**
 * Metabox main class
 *
 * @package wpmu-dev-seo
 */

/**
 * Metabox rendering / handling class
 */
class Smartcrawl_Metabox extends Smartcrawl_Base_Controller {

	/**
	 * Static instance
	 *
	 * @var Smartcrawl_Metabox
	 */
	private static $_instance;

	protected function init() {
		// WPSC integration.
		add_action( 'wpsc_edit_product', array( $this, 'rebuild_sitemap' ) );
		add_action( 'wpsc_rate_product', array( $this, 'rebuild_sitemap' ) );

		add_action( 'admin_menu', array( $this, 'smartcrawl_create_meta_box' ) );

		add_action( 'save_post', array( $this, 'smartcrawl_save_postdata' ) );
		add_filter( 'attachment_fields_to_save', array( $this, 'smartcrawl_save_attachment_postdata' ) );

		add_filter( 'manage_pages_columns', array( $this, 'smartcrawl_page_title_column_heading' ), 10, 1 );
		add_filter( 'manage_posts_columns', array( $this, 'smartcrawl_page_title_column_heading' ), 10, 1 );

		add_action( 'manage_pages_custom_column', array( $this, 'smartcrawl_page_title_column_content' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $this, 'smartcrawl_page_title_column_content' ), 10, 2 );

		add_action( 'quick_edit_custom_box', array( $this, 'smartcrawl_quick_edit_dispatch' ), 10, 2 );
		add_action( 'admin_footer-edit.php', array( $this, 'smartcrawl_quick_edit_javascript' ) );
		add_action( 'wp_ajax_wds_get_meta_fields', array( $this, 'json_wds_postmeta' ) );
		add_action( 'wp_ajax_wds_metabox_update', array( $this, 'smartcrawl_metabox_live_update' ) );

		add_action( 'admin_print_scripts-post.php', array( $this, 'js_load_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'js_load_scripts' ) );
		add_action( 'wp_ajax_wds-metabox-preview', array( $this, 'json_create_preview' ) );
		/**
		 * TODO perhaps we can combine wds-analysis-get-editor-analysis wds-metabox-preview and wds_metabox_update
		 * since they are used together so frequently
		 */

		/*
		 * When running analysis in metabox, or rendering metabox preview, 
		 * always use overriding values passed in the request before values saved in the DB.
		 * 
		 * This is done by filtering the metadata.
		 */
		add_filter( 'get_post_metadata', array( $this, 'filter_meta_title' ), 10, 3 );
		add_filter( 'get_post_metadata', array( $this, 'filter_meta_desc' ), 10, 3 );
		add_filter( 'get_post_metadata', array( $this, 'filter_focus_keyword' ), 10, 3 );

		/**
		 * Similar for taxonomy meta
		 */
		add_filter( 'wds-taxonomy-meta-wds_title', array( $this, 'filter_term_meta_title' ) );
		add_filter( 'wds-taxonomy-meta-wds_desc', array( $this, 'filter_term_meta_desc' ) );
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
	 * Handles preview asking requests
	 */
	public function json_create_preview() {
		$data = $this->get_request_data();
		$post_id = (int) smartcrawl_get_array_value( $data, 'post_id' );
		$result = array( 'success' => false );

		if ( empty( $post_id ) ) {
			wp_send_json( $result );

			return;
		}

		$is_dirty = (boolean) smartcrawl_get_array_value( $data, 'is_dirty' );
		/**
		 * If there is_dirty flag is set i.e. are unsaved changes in the editor then we
		 * will fetch the latest post revision and preview that.
		 */
		$post_to_preview = $is_dirty
			? smartcrawl_get_latest_post_version( $post_id )
			: get_post( $post_id );
		$result['success'] = true;
		$result['markup'] = Smartcrawl_Simple_Renderer::load( 'metabox/metabox-preview', array(
			'post' => $post_to_preview,
		) );

		wp_send_json( $result );
	}

	/**
	 * Enqueues frontend dependencies
	 */
	public function js_load_scripts() {
		if ( $this->is_editing_private_post_type() ) {
			return;
		}

		wp_enqueue_script( Smartcrawl_Controller_Assets::METABOX_JS );

		wp_enqueue_media();

		wp_enqueue_style( Smartcrawl_Controller_Assets::APP_CSS );
	}

	/**
	 * Handles page body class
	 *
	 * @param string $string Body classes this far.
	 *
	 * @return string
	 */
	public function admin_body_class( $string ) {
		return str_replace( 'wpmud', '', $string );
	}

	/**
	 * Handles actual metabox rendering
	 *
	 * @param $post
	 */
	public function smartcrawl_meta_boxes( $post ) {
		Smartcrawl_Simple_Renderer::render( 'metabox/metabox-main', array(
			'post' => $post,
		) );
	}

	/**
	 * Adds the metabox to the queue
	 */
	public function smartcrawl_create_meta_box() {
		$show = user_can_see_seo_metabox();
		if ( function_exists( 'add_meta_box' ) ) {
			// Show branding for singular installs.
			$metabox_title = is_multisite() ? __( 'SmartCrawl', 'wds' ) : 'SmartCrawl';
			$post_types = get_post_types( array(
				'show_ui' => true, // Only if it actually supports WP UI.
				'public'  => true, // ... and is public
			) );
			foreach ( $post_types as $posttype ) {
				if ( $this->is_private_post_type( $posttype ) ) {
					continue;
				}
				if ( $show ) {
					add_meta_box( 'wds-wds-meta-box', $metabox_title, array(
						&$this,
						'smartcrawl_meta_boxes',
					), $posttype, 'normal', 'high' );
				}
			}
		}
	}

	/**
	 * Handles attachment metadata saving
	 *
	 * @param array $data Data to save.
	 *
	 * @return array
	 */
	public function smartcrawl_save_attachment_postdata( $data ) {
		$request_data = $this->get_request_data();
		if ( empty( $request_data ) || empty( $data['post_ID'] ) || ! is_numeric( $data['post_ID'] ) ) {
			return $data;
		}
		$this->smartcrawl_save_postdata( (int) $data['post_ID'] );

		return $data;
	}

	private function get_post() {
		global $post;

		return $post;
	}

	/**
	 * Saves submitted metabox POST data
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	public function smartcrawl_save_postdata( $post_id ) {
		$request_data = $this->get_request_data();
		if ( ! $post_id || empty( $request_data ) ) {
			return;
		}

		$post = $this->get_post();
		if ( empty( $post ) ) {
			$post = get_post( $post_id );
		}

		$all_options = Smartcrawl_Settings::get_options();
		$post_type_noindexed = (bool) smartcrawl_get_array_value( $all_options, sprintf( 'meta_robots-noindex-%s', get_post_type( $post ) ) );
		$post_type_nofollowed = (bool) smartcrawl_get_array_value( $all_options, sprintf( 'meta_robots-nofollow-%s', get_post_type( $post ) ) );

		// Determine posted type.
		$post_type_rq = ! empty( $request_data['post_type'] ) ? sanitize_key( $request_data['post_type'] ) : false;
		if ( 'page' === $post_type_rq && ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$ptype = ! empty( $post_type_rq )
			? $post_type_rq
			: ( ! empty( $post->post_type ) ? $post->post_type : false );
		// Do not process post stuff for non-public post types.
		if ( ! in_array( $ptype, get_post_types( array( 'public' => true ) ), true ) ) {
			return $post_id;
		}

		if ( ! empty( $request_data['wds-opengraph'] ) ) {
			$input = stripslashes_deep( $request_data['wds-opengraph'] );
			$result = array();

			$og_disabled = ! empty( $input['disabled'] );
			if ( $og_disabled ) {
				$result['disabled'] = true;
			}
			if ( ! empty( $input['title'] ) ) {
				$result['title'] = smartcrawl_sanitize_preserve_macros( $input['title'] );
			}
			if ( ! empty( $input['description'] ) ) {
				$result['description'] = smartcrawl_sanitize_preserve_macros( $input['description'] );
			}
			if ( ! empty( $input['images'] ) && is_array( $input['images'] ) ) {
				$result['images'] = array();
				foreach ( $input['images'] as $img ) {
					$img = esc_url_raw( $img );
					$result['images'][] = $img;
				}
			}

			if ( empty( $result ) ) {
				delete_post_meta( $post_id, '_wds_opengraph' );
			} else {
				update_post_meta( $post_id, '_wds_opengraph', $result );
			}
		}

		if ( ! empty( $request_data['wds-twitter'] ) ) {
			$input = stripslashes_deep( $request_data['wds-twitter'] );
			$twitter = array();

			$twitter_disabled = ! empty( $input['disabled'] );
			if ( $twitter_disabled ) {
				$twitter['disabled'] = true;
			}
			if ( ! empty( $input['title'] ) ) {
				$twitter['title'] = smartcrawl_sanitize_preserve_macros( $input['title'] );
			}
			if ( ! empty( $input['description'] ) ) {
				$twitter['description'] = smartcrawl_sanitize_preserve_macros( $input['description'] );
			}
			if ( ! empty( $input['images'] ) && is_array( $input['images'] ) ) {
				$twitter['images'] = array();
				foreach ( $input['images'] as $img ) {
					$img = esc_url_raw( $img );
					$twitter['images'][] = $img;
				}
			}

			if ( empty( $twitter ) ) {
				delete_post_meta( $post_id, '_wds_twitter' );
			} else {
				update_post_meta( $post_id, '_wds_twitter', $twitter );
			}
		}

		if ( isset( $request_data['wds_focus'] ) ) {
			$focus = stripslashes_deep( $request_data['wds_focus'] );
			if ( trim( $focus ) === '' ) {
				delete_post_meta( $post_id, '_wds_focus-keywords' );
			} else {
				update_post_meta( $post_id, '_wds_focus-keywords', smartcrawl_sanitize_preserve_macros( $focus ) );
			}
		}

		foreach ( $request_data as $key => $value ) {
			if ( in_array( $key, array( 'wds-opengraph', 'wds_focus', 'wds-twitter' ), true ) ) {
				continue;
			} // We already handled those.
			if ( ! preg_match( '/^wds_/', $key ) ) {
				continue;
			}

			$id = "_{$key}";
			$data = $value;
			if ( is_array( $value ) ) {
				$data = join( ',', $value );
			}

			if ( $data ) {
				$value = in_array( $key, array( 'wds_canonical', 'wds_redirect' ), true )
					? esc_url_raw( $data )
					: smartcrawl_sanitize_preserve_macros( $data );
				update_post_meta( $post_id, $id, $value );
			} else {
				delete_post_meta( $post_id, $id );
			}
		}

		/**
		 * If the user un-checks a checkbox and saves the post, the value for that checkbox will not be included inside $_POST array
		 * so we may have to delete the corresponding meta value manually.
		 */
		$checkbox_meta_items = array(
			'wds_tags_to_keywords',
			'wds_meta-robots-adv',
			'wds_autolinks-exclude',
		);
		$checkbox_meta_items[] = $post_type_nofollowed ? 'wds_meta-robots-follow' : 'wds_meta-robots-nofollow';
		$checkbox_meta_items[] = $post_type_noindexed ? 'wds_meta-robots-index' : 'wds_meta-robots-noindex';
		foreach ( $checkbox_meta_items as $item ) {
			if ( ! isset( $request_data[ $item ] ) ) {
				delete_post_meta( $post_id, "_{$item}" );
			}
		}

		do_action( 'wds_saved_postdata' );
	}

	/**
	 * Handles sitemap rebuilding
	 */
	public function rebuild_sitemap() {
		Smartcrawl_Xml_Sitemap::get()->generate_sitemap();
	}

	/**
	 * Adds title and robots columns to post listing page
	 *
	 * @param array $columns Post list columns.
	 *
	 * @return array
	 */
	public function smartcrawl_page_title_column_heading( $columns ) {
		$title_idx = array_search( 'title', array_keys( $columns ), true );
		$title_idx = ! empty( $title_idx ) ? $title_idx + 1 : 2;
		return array_merge(
			array_slice( $columns, 0, $title_idx ),
			array( 'page-title' => __( 'Title Tag', 'wds' ) ),
			array_slice( $columns, $title_idx, count( $columns ) ),
			array( 'page-meta-robots' => __( 'Robots Meta', 'wds' ) )
		);
	}

	/**
	 * Puts out actual column bodies
	 *
	 * @param string $column_name Column ID.
	 * @param int $id Post ID.
	 *
	 * @return void
	 */
	public function smartcrawl_page_title_column_content( $column_name, $id ) {
		if ( 'page-title' === $column_name ) {
			echo esc_html( $this->smartcrawl_page_title( $id ) );

			// Show any 301 redirects.
			$redirect = smartcrawl_get_value( 'redirect', $id );
			if ( ! empty( $redirect ) ) {
				$href = $redirect;
				$link = "<a href='{$href}' target='_blank'>{$href}</a>";
				echo '<br /><em>' . sprintf( esc_html( __( 'Redirects to %s', 'wds' ) ), esc_url( $href ) ) . '</em>';
			}
		}

		if ( 'page-meta-robots' === $column_name ) {
			$meta_robots_arr = array(
				( smartcrawl_get_value( 'meta-robots-noindex', $id ) ? 'noindex' : 'index' ),
				( smartcrawl_get_value( 'meta-robots-nofollow', $id ) ? 'nofollow' : 'follow' ),
			);
			$meta_robots = join( ',', $meta_robots_arr );
			if ( empty( $meta_robots ) ) {
				$meta_robots = 'index,follow';
			}
			echo esc_html( ucwords( str_replace( ',', ', ', $meta_robots ) ) );

			// Show additional robots data.
			$advanced = array_filter( array_map( 'trim', explode( ',', smartcrawl_get_value( 'meta-robots-adv', $id ) ) ) );
			if ( ! empty( $advanced ) && 'none' !== $advanced ) {
				$adv_map = array(
					'noodp'     => __( 'No ODP', 'wds' ),
					'noydir'    => __( 'No YDIR', 'wds' ),
					'noarchive' => __( 'No Archive', 'wds' ),
					'nosnippet' => __( 'No Snippet', 'wds' ),
				);
				$additional = array();
				foreach ( $advanced as $key ) {
					if ( ! empty( $adv_map[ $key ] ) ) {
						$additional[] = $adv_map[ $key ];
					}
				}
				if ( ! empty( $additional ) ) {
					echo '<br /><small>' . esc_html( join( ', ', $additional ) ) . '</small>';
				}
			}
		}
	}

	/**
	 * Gets SEO title (with expanded macro replacements)
	 *
	 * @param int $postid Post ID.
	 *
	 * @return string
	 */
	public function smartcrawl_page_title( $postid ) {
		$post = get_post( $postid );
		$resolver = Smartcrawl_Endpoint_Resolver::resolve();

		$resolver->simulate_post( $post );
		$title = Smartcrawl_Meta_Value_Helper::get()->get_title();
		$resolver->stop_simulation();

		return $title;
	}

	/**
	 * Dispatch quick edit areas
	 *
	 * @param string $column Column ID.
	 * @param string $type Passthrough.
	 */
	public function smartcrawl_quick_edit_dispatch( $column, $type ) {
		switch ( $column ) {
			case 'page-title':
				return $this->_title_qe_box( $type );
			case 'page-meta-robots':
				return $this->_robots_qe_box();
			default:
				break;
		}
	}

	/**
	 * Renders title quick edit box
	 */
	private function _title_qe_box() {
		global $post;
		Smartcrawl_Simple_Renderer::render( 'quick-edit-title', array(
			'post' => $post,
		) );
	}

	/**
	 * Renders robots quick edit box
	 */
	private function _robots_qe_box() {
		global $post;
		Smartcrawl_Simple_Renderer::render( 'quick-edit-robots', array(
			'post' => $post,
		) );
	}

	/**
	 * Inject the quick editing javascript
	 */
	public function smartcrawl_quick_edit_javascript() {
		Smartcrawl_Simple_Renderer::render( 'quick-edit-javascript' );
	}

	/**
	 * Handle postmeta getting requests
	 */
	public function json_wds_postmeta() {
		$data = $this->get_request_data();
		$id = (int) $data['id'];
		$post = get_post( $id );
		die( wp_json_encode( array(
			'title'       => smartcrawl_get_value( 'title', $id ),
			'description' => smartcrawl_get_value( 'metadesc', $id ),
			'focus'       => smartcrawl_get_value( 'focus-keywords', $id ),
			'keywords'    => smartcrawl_get_value( 'keywords', $id ),
		) ) );
	}

	/**
	 * Handle metabox live update requests
	 */
	public function smartcrawl_metabox_live_update() {
		$response = array();
		$data = $this->get_request_data();
		$id = (int) smartcrawl_get_array_value( $data, 'id' );
		if ( $id ) {
			$post = get_post( $id );

			$post_data = sanitize_post( $data['post'] );

			/* Merge live post data with currently saved post data */
			$post->post_author = $post_data['post_author'];
			$post->post_title = $post_data['post_title'];
			$post->post_excerpt = $post_data['excerpt'];
			$post->post_content = $post_data['content'];
			$post->post_type = $post_data['post_type'];

			$title = smartcrawl_get_seo_title( $post );
			$description = smartcrawl_get_seo_desc( $post );
			$response = array(
				'title'       => $title,
				'description' => $description,
				'focus'       => smartcrawl_get_value( 'focus-keywords', $id ),
				'keywords'    => smartcrawl_get_value( 'keywords', $id ),
			);
		}

		wp_send_json( $response );
		die();
	}

	/**
	 * When we are rendering a preview, or refreshing analysis,
	 * we want to use the latest values from the request.
	 */
	public function filter_meta_title( $original, $post_id, $meta_key ) {
		if ( $meta_key !== '_wds_title' ) {
			return $original;
		}

		return $this->use_request_param_value( 'wds_title', $original );
	}

	public function filter_meta_desc( $original, $post_id, $meta_key ) {
		if ( $meta_key !== '_wds_metadesc' ) {
			return $original;
		}

		return $this->use_request_param_value( 'wds_description', $original );
	}

	public function filter_focus_keyword( $original, $post_id, $meta_key ) {
		if ( $meta_key !== '_wds_focus-keywords' ) {
			return $original;
		}

		return $this->use_request_param_value( 'wds_focus_keywords', $original );
	}

	public function filter_term_meta_title( $original ) {
		return $this->use_request_param_value( 'wds_title', $original );
	}

	public function filter_term_meta_desc( $original ) {
		return $this->use_request_param_value( 'wds_description', $original );
	}

	private function use_request_param_value( $request_param, $default ) {
		$overridden = smartcrawl_get_array_value( $this->get_request_data(), $request_param );
		if ( is_null( $overridden ) ) {
			return $default;
		}

		return Smartcrawl_Replacement_Helper::replace( $overridden );
	}

	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( $_POST['_wds_nonce'], 'wds-metabox-nonce' ) ? stripslashes_deep( $_POST ) : array();
	}

	private function is_private_post_type( $post_type_name ) {
		$post_type = get_post_type_object( $post_type_name );

		return $post_type->name === 'attachment'
		       || ! $post_type->show_ui
		       || ! $post_type->public;
	}

	private function is_editing_private_post_type() {
		$current_screen = get_current_screen();
		if ( empty( $current_screen->post_type ) ) {
			return false;
		}

		return $this->is_private_post_type( $current_screen->post_type );
	}
}
