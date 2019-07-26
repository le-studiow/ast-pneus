<?php
/**
 * Autolinking action module
 *
 * Autolinks module contains code from SEO Smart Links plugin
 * (http://wordpress.org/extend/plugins/seo-automatic-links/ and http://www.prelovac.com/products/seo-smart-links/)
 * by Vladimir Prelovac (http://www.prelovac.com/).
 *
 * @package wpmu-dev-seo
 */

/**
 * SmartCrawl Auto Links class
 *
 * @package SmartCrawl
 * @since 0.1
 */
class Smartcrawl_Autolinks extends Smartcrawl_Base_Controller { // phpcs:ignore -- We have two versions of this class

	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Autolinks
	 */
	private static $_instance;
	/**
	 * Component settings
	 *
	 * @var array
	 */
	private $settings = array();

	private $backup_links = array();

	public function should_run() {
		return Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SITE )->is_member()
		       && Smartcrawl_Settings::get_setting( 'autolinks' )
		       && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_AUTOLINKS );
	}

	/**
	 * Initialization method
	 */
	protected function init() {
		$smartcrawl_options = Smartcrawl_Settings::get_options();

		$this->settings = $smartcrawl_options;
		// Set autolinks filter ordering to *after* shortcode processing.
		$content_filter_order = defined( 'SMARTCRAWL_AUTOLINKS_CONTENT_FILTER_ORDER' ) && intval( SMARTCRAWL_AUTOLINKS_CONTENT_FILTER_ORDER, 10 )
			? intval( SMARTCRAWL_AUTOLINKS_CONTENT_FILTER_ORDER, 10 )
			: 99;

		add_filter( 'the_content', array( $this, 'the_content_filter' ), $content_filter_order );
		if ( ! empty( $smartcrawl_options['comment'] ) ) {
			add_filter( 'comment_text', array( $this, 'comment_text_filter' ), 10 );
		}

		add_action( 'create_category', array( $this, 'delete_cache' ) );
		add_action( 'edit_category', array( $this, 'delete_cache' ) );
		add_action( 'edit_post', array( $this, 'delete_cache' ) );
		add_action( 'save_post', array( $this, 'delete_cache' ) );
	}

	public static function get() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cache deleting
	 *
	 * @param int $id Deprecated.
	 *
	 * @return void
	 */
	public function delete_cache( $id ) {
		$options = $this->settings;

		if ( ! empty( $options['ltaxonomies'] ) && is_array( $options['ltaxonomies'] ) ) {
			foreach ( $options['ltaxonomies'] as $taxonomy ) {
				wp_cache_delete( "wds-autolinks-$taxonomy", 'wds-autolinks' );
			}
		}
		wp_cache_delete( 'wds-autolinks-posts', 'wds-autolinks' );
	}

	/**
	 * Insert character delimiters in text
	 *
	 * @param string $str String to process.
	 *
	 * @return string
	 */
	public function insert_special_case_delimiters( $str ) {
		if ( ! $str ) {
			return $str;
		}

		return defined( 'SMARTCRAWL_CORE_FLAG_ASCII_SPECIAL_CHARS' ) && SMARTCRAWL_CORE_FLAG_ASCII_SPECIAL_CHARS
			? join( '<!---->', str_split( $str ) )
			: join( '<!---->', preg_split( '/(.)/u', $str, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY ) );
	}

	/**
	 * Remove special character delimiters from text
	 *
	 * @param string $str String to process.
	 *
	 * @return string
	 */
	public function remove_special_case_delimiters( $str ) {
		$strarr = explode( '<!---->', $str );
		$str = implode( '', $strarr );
		$str = stripslashes( $str );

		return $str;
	}

	/**
	 * Comment text filter handler
	 *
	 * @param string $text Comment text.
	 *
	 * @return string
	 */
	public function comment_text_filter( $text ) {
		return $this->the_content_filter( $text );
	}

	private function is_utf8_matching_enabled() {
		$utf8_variations = array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' );
		$is_utf8_site = ( ! defined( 'DB_CHARSET' ) || strpos( DB_CHARSET, 'utf8' ) !== false )
		                && in_array( get_option( 'blog_charset', '' ), $utf8_variations, true );

		return apply_filters( 'wds-autolinks-utf8-matching-enabled', $is_utf8_site );
	}

	/**
	 * Post content filter handler
	 *
	 * @param string $text Content.
	 *
	 * @return string
	 */
	public function the_content_filter( $text ) {
		$post_type = get_post_type();
		if ( empty( $this->settings[ $post_type ] ) ) {
			return $text;
		}

		$exclude = smartcrawl_get_value( 'autolinks-exclude' );
		if ( ! empty( $exclude ) ) {
			return $text;
		} // Explicitly excluded from autollink processing
		$result = $this->process_text( $text, 0 );

		$options = $this->settings;
		$link = wp_parse_url( get_bloginfo( 'wpurl' ) );
		$host = 'http://' . $link['host'];

		if ( ! empty( $options['blanko'] ) ) {
			$result = preg_replace( '%<a(\s+.*?href=\S(?!' . $host . '))%i', '<a target="_blank"\\1', $result );
		}

		if ( ! empty( $options['nofolo'] ) ) {
			$result = preg_replace( '%<a(\s+.*?href=\S(?!' . $host . '))%i', '<a rel="nofollow"\\1', $result );
		}

		return $result;
	}

	private function backup_links( $text ) {
		$this->backup_links = array();
		$utf = $this->is_utf8_matching_enabled() ? 'u' : '';

		return preg_replace_callback(
			"/<\s*a[^>]*>.*?<\s*\/a\s*>/is$utf",
			array( $this, 'replace_link_with_placeholder' ),
			$text
		);
	}

	private function restore_links( $text ) {
		return str_replace(
			array_keys( $this->backup_links ),
			array_values( $this->backup_links ),
			$text
		);
	}

	private function replace_link_with_placeholder( $matches ) {
		$link = $matches[0];
		$link_hash = md5( $link );
		$placeholder = "WDS_LINK_PLACEHOLDER_$link_hash";

		$this->backup_links[ $placeholder ] = $link;

		return $placeholder;
	}

	/**
	 * Text processing method
	 *
	 * @param string $text Text to process.
	 * @param bool $mode Mode switch - deprecated.
	 *
	 * @return string
	 */
	private function process_text( $text, $mode ) {
		global $wpdb, $post;

		$options = $this->settings;
		$options['casesens'] = ! empty( $options['casesens'] ) ? $options['casesens'] : false;
		$options['lpages'] = ! empty( $options['lpages'] ) ? $options['lpages'] : false;

		$links = 0;

		$allow_feed = (boolean) smartcrawl_get_array_value( $options, 'allowfeed' );
		if ( is_feed() && ! $allow_feed ) {
			return $text;
		} elseif ( isset( $options['onlysingle'] ) && ! ( is_single() || is_page() ) ) {
			return $text;
		}

		$ignore_post = smartcrawl_get_array_value( $options, 'ignorepost' );
		$arrignorepost = $this->explode_trim( ',', $ignore_post );

		if ( is_page( $arrignorepost ) || is_single( $arrignorepost ) ) {
			return $text;
		}

		if ( ! $mode ) {
			if ( 'post' === $post->post_type && empty( $options['post'] ) ) {
				return $text;
			} elseif ( 'page' === $post->post_type && empty( $options['page'] ) ) {
				return $text;
			}

			if ( ( 'page' === $post->post_type && empty( $options['pageself'] ) ) || ( 'post' === $post->post_type && empty( $options['pageself'] ) ) ) {
				$thistitle = isset( $options['casesens'] ) ? $post->post_title : strtolower( $post->post_title );
				$thisurl = trailingslashit( get_permalink( $post->ID ) );
			} else {
				$thistitle = '';
				$thisurl = '';
			}
		}

		$maxlinks = ! empty( $options['link_limit'] ) ? $options['link_limit'] : 0;
		$maxsingle = ! empty( $options['single_link_limit'] ) ? $options['single_link_limit'] : ( $maxlinks ? $maxlinks : - 1 );
		$maxsingleurl = ! empty( $options['maxsingleurl'] ) ? $options['maxsingleurl'] : 0;
		$minusage = ! empty( $options['minusage'] ) ? $options['minusage'] : 1;

		$urls = array();

		$ignores = (string) smartcrawl_get_array_value( $options, 'ignore' );
		$arrignore = $this->explode_trim( ',', $ignores );
		if ( empty( $options['casesens'] ) ) {
			$arrignore = array_map( 'strtolower', $arrignore );
		}
		if ( $minusage && ! empty( $options['excludeheading'] ) ) {
			$text = preg_replace_callback( '%(<h.*?>)(.*?)(</h.*?>)%si', array(
				$this,
				'inject_special_chars_callback',
			), $text );
		}

		// Backup any existing links because we don't want to mess those up
		$text = $this->backup_links( $text );

		// Don't match ...
		$lookahead_parts = array(
			'[^<]+[>]+',        // ... name of HTML tags e.g. block in <blockquote>
			'[^>]+<\/a>',       // ... text that is already linked e.g. hello in <a>hello</a>
			'[^>]+<\/script>',  // ... anything in script tags e.g. hello in <script>alert('hello');</script>
			'[^>]+<\/style>',   // ... anything in style tags e.g. hello in <style>.hello {display:none}</style>
			'[\[\]]+',          // ... TODO see what this one does
		);
		$negative_lookahead = join( '|', $lookahead_parts );
		$negative_lookahead = "(?!(?:$negative_lookahead))";

		$reg_post = "/$negative_lookahead(^|\b|[^<\p{L}\/>])(KEYWORD)([^\p{L}\/>]|\b|$)/msU";
		if ( empty( $options['casesens'] ) ) {
			$reg_post .= 'i';
		}
		if ( $this->is_utf8_matching_enabled() ) {
			// Enable UTF-8 flag in the regex
			$reg_post .= 'u';
		}

		$strpos_fnc = ! empty( $options['casesens'] ) ? 'strpos' : 'stripos';

		$text = " {$text} ";

		// insert custom keywords.
		if ( ! empty( $options['customkey'] ) ) {
			$kw_array = array();

			foreach ( explode( "\n", $options['customkey'] ) as $line ) {
				if ( ! empty( $options['customkey_preventduplicatelink'] ) ) {
					$line = trim( $line );
					$last_delimiter_pos = strrpos( $line, ',' );
					$url = substr( $line, $last_delimiter_pos + 1 );
					$keywords = substr( $line, 0, $last_delimiter_pos );

					if ( ! empty( $keywords ) && ! empty( $url ) ) {
						$kw_array[ $keywords ] = trim( $url );
					}

					$keywords = '';
					$url = '';
				} else {
					$chunks = array_map( 'trim', explode( ',', $line ) );
					$total_chuncks = count( $chunks );
					if ( $total_chuncks > 2 ) {

						$i = 0;
						$url = $chunks[ $total_chuncks - 1 ];

						while ( $i < $total_chuncks - 1 ) {
							if ( ! empty( $chunks[ $i ] ) ) {
								$kw_array[ $chunks[ $i ] ] = $url;
							}

							$i ++;
						}
					} else {

						if ( false !== stristr( $line, ',' ) ) {
							list( $keyword, $url ) = array_map( 'trim', explode( ',', $line, 2 ) );
							if ( ! empty( $keyword ) ) {
								$kw_array[ $keyword ] = $url;
							}
						}
					}
				}
			}

			// Add htmlemtities and WordPress texturizer alternations for keywords.
			$kw_array_tmp = $kw_array;
			foreach ( $kw_array_tmp as $kw => $url ) {
				$kw_entity = htmlspecialchars( $kw, ENT_QUOTES );
				if ( ! isset( $kw_array[ $kw_entity ] ) ) {
					$kw_array[ $kw_entity ] = $url;
				}

				$kw_entity = wptexturize( $kw );
				if ( ! isset( $kw_array[ $kw_entity ] ) ) {
					$kw_array[ $kw_entity ] = $url;
				}
			}

			// prevent duplicate links.
			foreach ( $kw_array as $name => $url ) {

				if ( ( ! $maxlinks || ( $links < $maxlinks ) ) && ( $this->get_absolute_url( $url ) !== $thisurl ) && ! in_array( ! empty( $options['casesens'] ) ? $name : strtolower( $name ), $arrignore, true ) && ( ! $maxsingleurl || $urls[ $url ] < $maxsingleurl ) ) {

					if ( ! empty( $options['customkey_preventduplicatelink'] ) || $strpos_fnc( $text, $name ) !== false ) {
						$name = preg_quote( $name, '/' );
					}

					if ( ! empty( $options['customkey_preventduplicatelink'] ) ) {
						$name = str_replace( ',', '|', $name );
					}

					$maxsingle = ( ! empty( $options['customkey_preventduplicatelink'] ) ) ? 1 : (int) $maxsingle;
					$arguments = array(
						'target' => empty( $options['target_blank'] ) ? '' : '_blank',
						'rel'    => empty( $options['rel_nofollow'] ) ? '' : 'nofollow',
					);
					$replace = '$1<a title="$2" ' . smartcrawl_autolinks_construct_attributes( $arguments ) . ' href="' . $url . '">$2</a>$3';
					$regexp = str_replace( 'KEYWORD', $name, $reg_post );

					if ( ( defined( 'SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK' ) && SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK ) && ! preg_match( $regexp, strip_shortcodes( $text ) ) ) {
						continue;
					}
					$newtext = preg_replace( $regexp, $replace, $text, $maxsingle );

					if ( $newtext !== $text ) {
						$replacement_count = count( preg_split( $regexp, $text ) ) - 1;
						$replacement_count = $replacement_count > 0 ? $replacement_count : 1;
						$links += $replacement_count > $maxsingle ? $maxsingle : $replacement_count;
						$text = $newtext;
						if ( ! isset( $urls[ $url ] ) ) {
							$urls[ $url ] = 1;
						} else {
							$urls[ $url ] ++;
						}
					}
				}
			} // end foreach.
		}

		// process posts.
		if ( ( ! empty( $post->post_type ) && ! empty( $options["{$post->post_type}"] ) ) ) {
			$cpt_char_limit = ! empty( $options['cpt_char_limit'] ) ? (int) $options['cpt_char_limit'] : false;
			$cpt_char_limit = (int) $cpt_char_limit ? (int) $cpt_char_limit : intval( SMARTCRAWL_AUTOLINKS_DEFAULT_CHAR_LIMIT );
			$posts = wp_cache_get( 'wds-autolinks-posts', 'wds-autolinks' );
			if ( ! $posts ) {
				$posts = $wpdb->get_results( $wpdb->prepare(
					"SELECT post_title, ID, post_type FROM {$wpdb->posts} WHERE post_status = 'publish' AND LENGTH(post_title)>=%d ORDER BY LENGTH(post_title) DESC LIMIT 2000",
					$cpt_char_limit
				) );

				wp_cache_add( 'wds-autolinks-posts', $posts, 'wds-autolinks', 86400 );
			}

			foreach ( $posts as $postitem ) {
				if ( $postitem->ID === $post->ID ) {
					continue;
				}
				if (
					! empty( $options["l{$postitem->post_type}"] ) &&
					( ! $maxlinks || ( $links < $maxlinks ) ) &&
					( ( $options['casesens'] ? $postitem->post_title : strtolower( $postitem->post_title ) ) !== $thistitle ) &&
					( ! in_array( ( $options['casesens'] ? $postitem->post_title : strtolower( $postitem->post_title ) ), $arrignore, true ) )
				) {
					if ( $strpos_fnc( $text, $postitem->post_title ) !== false ) {
						$name = preg_quote( $postitem->post_title, '/' );

						$regexp = str_replace( 'KEYWORD', $name, $reg_post );

						if ( ! empty( $options['customkey_preventduplicatelink'] ) ) {
							$maxsingle = 1;
						} elseif ( ! empty( $maxlinks ) ) {
							$maxsingle = ( $links + $maxsingle >= $maxlinks ) ? $maxlinks - $links : $maxsingle;
						}

						$arguments = array(
							'target' => empty( $options['target_blank'] ) ? '' : '_blank',
							'rel'    => empty( $options['rel_nofollow'] ) ? '' : 'nofollow',
						);
						$replace = '$1<a title="$2" ' . smartcrawl_autolinks_construct_attributes( $arguments ) . ' href="$$$url$$$">$2</a>$3';

						if ( ( defined( 'SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK' ) && SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK ) && ! preg_match( $regexp, strip_shortcodes( $text ) ) ) {
							continue;
						}
						$newtext = preg_replace( $regexp, $replace, $text, $maxsingle );

						if ( $newtext !== $text ) {
							$url = get_permalink( $postitem->ID );
							if ( ! $maxsingleurl || $urls[ $url ] < $maxsingleurl ) {
								$replacement_count = count( preg_split( $regexp, $text ) ) - 1;
								$replacement_count = $replacement_count > 0 ? $replacement_count : 1;
								$links += $replacement_count > $maxsingle ? $maxsingle : $replacement_count;
								$text = str_replace( '$$$url$$$', $url, $newtext );

								if ( ! isset( $urls[ $url ] ) ) {
									$urls[ $url ] = 1;
								} else {
									$urls[ $url ] ++;
								}
							}
						}
					}
				}
			} // end foreach.

		}

		// process taxonomies.
		$_tax = array();
		foreach ( get_taxonomies( false, 'object' ) as $taxonomy ) {
			if ( in_array( $taxonomy->name, array(
				'nav_menu',
				'link_category',
				'post_format',
			), true ) ) {
				continue;
			}
			$key = strtolower( $taxonomy->labels->name );
			if ( ! empty( $options["l{$key}"] ) ) {
				$_tax[] = $taxonomy->name;
			}
		}
		$tax_char_limit = ! empty( $options['tax_char_limit'] ) ? (int) $options['tax_char_limit'] : false;
		$tax_char_limit = (int) $tax_char_limit ? (int) $tax_char_limit : intval( SMARTCRAWL_AUTOLINKS_DEFAULT_CHAR_LIMIT );
		$minimum_count = ! empty( $options['allow_empty_tax'] ) ? 0 : $minusage;
		foreach ( $_tax as $taxonomy ) {
			$terms = wp_cache_get( "wds-autolinks-{$taxonomy}", 'wds-autolinks' );
			if ( ! $terms ) {
				$terms = $wpdb->get_results( $wpdb->prepare(
					"SELECT {$wpdb->terms}.name, {$wpdb->terms}.term_id FROM {$wpdb->terms} LEFT JOIN {$wpdb->term_taxonomy} " .
					"ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id " .
					"WHERE {$wpdb->term_taxonomy}.taxonomy = %s " .
					"AND LENGTH({$wpdb->terms}.name) >= %d " .
					"AND {$wpdb->term_taxonomy}.count >= %d " .
					"ORDER BY LENGTH({$wpdb->terms}.name) DESC LIMIT 2000",
					$taxonomy,
					$tax_char_limit,
					$minimum_count
				) );

				wp_cache_add( "wds-autolinks-{$taxonomy}", $terms, 'wds-autolinks', 86400 );
			}

			foreach ( $terms as $term ) {
				if (
					( ! $maxlinks || ( $links < $maxlinks ) ) && ! in_array( $options['casesens'] ? $term->name : strtolower( $term->name ), $arrignore, true )
				) {
					if ( false === $strpos_fnc( $text, $term->name ) ) {
						continue;
					}

					$name = preg_quote( $term->name, '/' );
					$regexp = str_replace( 'KEYWORD', $name, $reg_post );
					$arguments = array(
						'target' => empty( $options['target_blank'] ) ? '' : '_blank',
						'rel'    => empty( $options['rel_nofollow'] ) ? '' : 'nofollow',
					);
					$replace = '$1<a title="$2" ' . smartcrawl_autolinks_construct_attributes( $arguments ) . ' href="$$$url$$$">$2</a>$3';

					if ( ( defined( 'SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK' ) && SMARTCRAWL_AUTOLINKS_ON_THE_FLY_CHECK ) && ! preg_match( $regexp, strip_shortcodes( $text ) ) ) {
						continue;
					}
					$newtext = preg_replace( $regexp, $replace, $text, $maxsingle );
					if ( $newtext !== $text ) {
						$url = get_term_link( get_term( $term->term_id, $taxonomy ) );
						if ( is_wp_error( $url ) ) {
							continue;
						}
						if ( ! $maxsingleurl || $urls[ $url ] < $maxsingleurl ) {
							$links ++;
							$text = str_replace( '$$$url$$$', $url, $newtext );
							if ( ! isset( $urls[ $url ] ) ) {
								$urls[ $url ] = 1;
							} else {
								$urls[ $url ] ++;
							}
						}
					}
				}
			}
		}

		// exclude headers.
		if ( ! empty( $options['excludeheading'] ) ) {
			$text = preg_replace_callback( '%(<h.*?>)(.*?)(</h.*?>)%si', array(
				$this,
				'remove_special_chars_callback',
			), $text );
			$text = stripslashes( $text );
		}

		// Restore the links that we backed up before getting started
		$text = $this->restore_links( $text );

		return trim( $text );
	}

	private function get_absolute_url( $url ) {
		$is_relative = strpos( $url, '/' ) === 0;

		return $is_relative
			? trailingslashit( home_url( $url ) )
			: $url;
	}

	/**
	 * Text to trimmed array of strings
	 *
	 * @param string $separator Separator to break the text on.
	 * @param string $text Text to break.
	 *
	 * @return array
	 */
	private function explode_trim( $separator, $text ) {
		$arr = explode( $separator, $text );

		$ret = array();
		foreach ( $arr as $e ) {
			$ret[] = trim( $e );
		}

		return $ret;
	}

	/**
	 * Special characters insertion preg callback
	 *
	 * @param array $matches List of matches to work with.
	 *
	 * @return string
	 */
	private function inject_special_chars_callback( $matches ) {
		return $this->_special_chars_callback( $matches, 'insert_special_case_delimiters' );
	}

	/**
	 * Special characters dispatcher method
	 *
	 * @param array $matches List of matches to work with.
	 * @param string $callback Our method to call.
	 *
	 * @return string
	 */
	private function _special_chars_callback( $matches, $callback ) {
		$default = ! empty( $matches[0] ) ? $matches[0] : false;
		$open_tag = ! empty( $matches[1] ) ? $matches[1] : false;
		$tag_text = ! empty( $matches[2] ) ? $matches[2] : false;
		$close_tag = ! empty( $matches[3] ) ? $matches[3] : false;

		if ( ! $open_tag || ! $tag_text || ! $close_tag ) {
			return $default;
		}
		if ( ! is_callable( array( $this, $callback ) ) ) {
			return $default;
		}

		$tag_text = call_user_func( array( $this, $callback ), $tag_text );
		if ( ! $tag_text ) {
			return $default;
		}

		return "{$open_tag}{$tag_text}{$close_tag}";
	}

	/**
	 * Special characters removal preg callback
	 *
	 * @param array $matches List of matches to work with.
	 *
	 * @return string
	 */
	private function remove_special_chars_callback( $matches ) {
		return $this->_special_chars_callback( $matches, 'remove_special_case_delimiters' );
	}

	public function get_settings() {
		return $this->settings;
	}

	public function set_settings( $settings ) {
		$this->settings = $settings;
	}
}
