<?php
/**
 * Core helper functions
 *
 * Procedures smartcrawl_get_value(), smartcrawl_replace_vars(), smartcrawl_get_term_meta()
 * inspired by WordPress SEO by Joost de Valk (http://yoast.com/wordpress/seo/).
 *
 * @package wpmu-dev-seo
 */

/**
 * Gets post meta value
 *
 * @param string $val Key root to check.
 * @param int $post_id Optional post ID.
 *
 * @return mixed
 */
function smartcrawl_get_value( $val, $post_id = false ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = isset( $post ) ? $post->ID : false;
	}
	if ( ! $post_id ) {
		return false;
	}

	$meta_value = get_post_meta( $post_id, '_wds_' . $val, true );
	return empty( $meta_value ) ? false : $meta_value;
}

/**
 * Sets post meta value
 *
 * @param string $meta Key root to check.
 * @param mixed $val Value to set.
 * @param int $post_id Optional post ID.
 *
 * @return void
 */
function smartcrawl_set_value( $meta, $val, $post_id ) {
	update_post_meta( $post_id, "_wds_{$meta}", $val );
}

/**
 * Gets separator, or separators list
 *
 * @param string $key Optional separator key.
 *
 * @return string|array
 */
function smartcrawl_get_separators( $key = null ) {
	$separators = array(
		'dot'           => '·',
		'dot-l'         => '•',
		'dash'          => '-',
		'dash-l'        => '—',
		'pipe'          => '|',
		'forward-slash' => '/',
		'back-slash'    => '\\',
		'tilde'         => '~',
		'greater-than'  => '>',
		'less-than'     => '<',
		'caret-right'   => '›',
		'caret-left'    => '‹',
		'arrow-right'   => '→',
		'arrow-left'    => '←',
	);

	if ( null === $key || empty( $separators[ $key ] ) ) {
		return $separators;
	} else {
		return $separators[ $key ];
	}
}

/**
 * Returns the post title meta using options set by user on smartcrawl_onpage
 *
 * @param WP_Post $post Optional post.
 *
 * @return string|false
 */
function smartcrawl_get_seo_title( $post = false ) {
	$smartcrawl_options = Smartcrawl_Settings::get_options();

	if ( ! $post ) {
		global $post;
	}
	if ( ! $post ) {
		return false;
	}

	if ( ! empty( $post->post_type ) && isset( $smartcrawl_options[ 'title-' . $post->post_type ] ) && ! empty( $smartcrawl_options[ 'title-' . $post->post_type ] ) ) {
		return Smartcrawl_Replacement_Helper::replace( $smartcrawl_options[ 'title-' . $post->post_type ], $post );
	}

	return false;
}

/**
 * Returns the post desc meta using options set by user on smartcrawl_onpage
 *
 * @param WP_Post $post Optional post.
 *
 * @return string|false
 */
function smartcrawl_get_seo_desc( $post = false ) {
	$smartcrawl_options = Smartcrawl_Settings::get_options();

	if ( ! $post ) {
		global $post;
	}
	if ( ! $post ) {
		return false;
	}

	if ( ! empty( $post->post_type ) && isset( $smartcrawl_options[ 'metadesc-' . $post->post_type ] ) && ! empty( $smartcrawl_options[ 'metadesc-' . $post->post_type ] ) ) {
		return Smartcrawl_Replacement_Helper::replace( $smartcrawl_options[ 'metadesc-' . $post->post_type ], $post );
	}

	return false;
}


/**
 * Returns the number as an anglicized string
 *
 * Adapted from original code by Hugh Bothwell (hugh_bothwell@hotmail.com)
 *
 * @param int $num Number to convert.
 *
 * @return string
 */
function smartcrawl_spell_number( $num ) {
	$num = (int) $num;    // make sure it's an integer.

	if ( $num < 0 ) {
		return 'negative' . _wds_hb_convert_tri( - $num, 0 );
	}
	if ( 0 === $num ) {
		return 'zero';
	}

	return _wds_hb_convert_tri( $num, 0 );
}

/**
 * Recursive fn, converts three digits per pass
 *
 * Adapted from original code by Hugh Bothwell (hugh_bothwell@hotmail.com)
 *
 * @param int $num Number to convert.
 * @param int $tri Triplet to check.
 *
 * @return string
 */
function _wds_hb_convert_tri( $num, $tri ) {
	$ones = array(
		'',
		' one',
		' two',
		' three',
		' four',
		' five',
		' six',
		' seven',
		' eight',
		' nine',
		' ten',
		' eleven',
		' twelve',
		' thirteen',
		' fourteen',
		' fifteen',
		' sixteen',
		' seventeen',
		' eighteen',
		' nineteen',
	);

	$tens = array(
		'',
		'',
		' twenty',
		' thirty',
		' forty',
		' fifty',
		' sixty',
		' seventy',
		' eighty',
		' ninety',
	);

	$triplets = array(
		'',
		' thousand',
		' million',
		' billion',
		' trillion',
		' quadrillion',
		' quintillion',
		' sextillion',
		' septillion',
		' octillion',
		' nonillion',
	);

	// chunk the number, ...rxyy.
	$r = (int) ( $num / 1000 );
	$x = ( $num / 100 ) % 10;
	$y = $num % 100;

	// init the output string.
	$str = '';

	// do hundreds.
	if ( $x > 0 ) {
		$str = $ones[ $x ] . ' hundred';
	}

	// do ones and tens.
	if ( $y < 20 ) {
		$str .= $ones[ $y ];
	} else {
		$str .= $tens[ (int) ( $y / 10 ) ] . $ones[ $y % 10 ];
	}

	// add triplet modifier only if there is some output to be modified...
	if ( '' !== $str ) {
		$str .= $triplets[ $tri ];
	}

	// continue recursing?
	if ( $r > 0 ) {
		return _wds_hb_convert_tri( $r, $tri + 1 ) . $str;
	} else {
		return $str;
	}
}

/**
 * Gets excerpt trimmed to length
 *
 * @param string $excerpt Optional excerpt.
 * @param string $contents Contents.
 *
 * @return string
 */
function smartcrawl_get_trimmed_excerpt( $excerpt, $contents ) {
	$string = $excerpt ? $excerpt : $contents;
	// Remove shortcodes but keep the content
	$string = smartcrawl_remove_shortcodes( $string );
	// Strip all HTML tags
	$string = wp_strip_all_tags( $string );
	// Encode any HTML entities like > and <
	$string = esc_attr( $string );
	// Normalize whitespace
	$string = smartcrawl_normalize_whitespace( $string );

	return smartcrawl_truncate_meta_description( $string );
}

function smartcrawl_normalize_whitespace( $string ) {
	// Replace whitespace characters with simple spaces
	$string = str_replace( array( "\r", "\n", "\t" ), ' ', $string );
	// Replace each set of multiple consecutive spaces with a single space
	$string = preg_replace( '/[ ]+/', ' ', $string );

	return trim( $string );
}

/**
 * Removes the shortcode tags but keeps the content within them. Will convert [shortcode attr="val"]Some text![/shortcode] to: Some text!
 * @see get_shortcode_regex()
 */
function smartcrawl_remove_shortcodes( $content ) {
	$pattern = get_shortcode_regex();
	return preg_replace_callback( "/$pattern/s", 'smartcrawl_extract_shortcode_contents', $content );
}

/**
 * Our callback function for making get_shortcode_regex() replacements.
 *
 * @see get_shortcode_regex()
 *
 * @param $matches array This array contains data in the following format:
 * array(
 *      0 => full matched string
 *      1 => the character [ for escaped shortcodes e.g. [[foo]]
 *      2 => the actual shortcode tag
 *      3 => shortcode attributes
 *      4 => ?
 *      5 => The content nested inside the opening and closing shortcode tags
 *      6 => the character ] for escaped shortcodes
 * );
 *
 * @return bool|mixed|string
 */
function smartcrawl_extract_shortcode_contents( $matches ) {
	if ( empty( $matches ) || count( $matches ) < 7 ) {
		// Not the expected regex for some reason. Try returning the full match or fall back to empty string.
		return isset( $matches[0] ) ? $matches[0] : '';
	}

	// Allow [[foo]] syntax for escaping a tag.
	if ( '[' === $matches[1] && ']' === $matches[6] ) {
		// Return the whole matched string without the surrounding square brackets that were there for escaping
		return substr( $matches[0], 1, - 1 );
	}

	$omitted = apply_filters( 'wds-omitted-shortcodes', array() );
	if (
		! empty( $matches[5] )
		&& ! in_array( $matches[2], $omitted, true )
	) {
		// Call the removal method on the content nested in the current shortcode
		// This will continue recursively until we have removed all shortcodes
		return smartcrawl_remove_shortcodes( trim( $matches[5] ) . ' ' );
	}

	// Just remove the content-less, non-escaped shortcodes
	return '';
}

function smartcrawl_truncate_meta_title( $string ) {
	return smartcrawl_truncare_meta( $string, SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT );
}

function smartcrawl_truncate_meta_description( $string ) {
	return smartcrawl_truncare_meta( $string, SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT );
}

function smartcrawl_truncare_meta( $string, $limit ) {
	$pattern = sprintf( '/.{%d,}/um', $limit + 1 );
	$replacement_pattern = sprintf( '/(.{0,%d}).*/um', $limit - 4 ); // -4 for 1 space plus ...

	return ( preg_match( $pattern, $string ) )
		? preg_replace( $replacement_pattern, '$1', $string ) . ' ...'
		: $string;
}

/**
 * Gets taxonomy term meta value
 *
 * @param object $term Term object.
 * @param string $taxonomy Taxonomy the term belongs to.
 * @param string $meta Meta key to check.
 *
 * @return mixed
 */
function smartcrawl_get_term_meta( $term, $taxonomy, $meta ) {
	$term = ( is_object( $term ) ) ? $term->term_id : get_term_by( 'slug', $term, $taxonomy );
	$tax_meta = get_option( 'wds_taxonomy_meta' );
	$meta_value = ( isset( $tax_meta[ $taxonomy ][ $term ][ $meta ] ) ) ? $tax_meta[ $taxonomy ][ $term ][ $meta ] : false;

	return apply_filters( "wds-taxonomy-meta-{$meta}", $meta_value, $term, $taxonomy );
}

/**
 * Blog template settings handler
 *
 * @param string $and Query gathered this far.
 *
 * @return string
 */
function smartcrawl_blog_template_settings( $and ) {
	// $and .= " AND `option_name` != 'wds_sitemaps_options'"; // Removed plural
	$and .= " AND `option_name` != 'wds_sitemap_options'"; // Added singular

	return $and;
}

add_filter( 'blog_template_exclude_settings', 'smartcrawl_blog_template_settings' );


/**
 * Checks user persmission level against minumum requirement
 * for displaying SEO metabox.
 *
 * @return bool
 */
function user_can_see_seo_metabox() {
	$smartcrawl_options = Smartcrawl_Settings::get_options();
	$capability = ( defined( 'SMARTCRAWL_SEO_METABOX_ROLE' ) && SMARTCRAWL_SEO_METABOX_ROLE )
		? SMARTCRAWL_SEO_METABOX_ROLE
		: ( ! empty( $smartcrawl_options['seo_metabox_permission_level'] ) ? $smartcrawl_options['seo_metabox_permission_level'] : false );
	$capability = apply_filters( 'wds-capabilities-seo_metabox', $capability );
	$able = false;

	if ( is_array( $capability ) ) {
		foreach ( $capability as $cap ) {
			$able = current_user_can( $cap );
			if ( $able ) {
				break;
			}
		}
	} else {
		$able = current_user_can( $capability );
	}

	return $able;
}

/**
 * Checks user persmission level against minumum requirement
 * for displaying Moz urlmetrics metabox.
 *
 * @return bool
 */
function user_can_see_urlmetrics_metabox() {
	$smartcrawl_options = Smartcrawl_Settings::get_options();
	$capability = ( defined( 'SMARTCRAWL_URLMETRICS_METABOX_ROLE' ) && SMARTCRAWL_URLMETRICS_METABOX_ROLE )
		? SMARTCRAWL_URLMETRICS_METABOX_ROLE
		: ( ! empty( $smartcrawl_options['urlmetrics_metabox_permission_level'] ) ? $smartcrawl_options['urlmetrics_metabox_permission_level'] : false );
	$capability = apply_filters( 'wds-capabilities-urlmetrics_metabox', $capability );
	$able = false;

	if ( is_array( $capability ) ) {
		foreach ( $capability as $cap ) {
			$able = current_user_can( $cap );
			if ( $able ) {
				break;
			}
		}
	} else {
		$able = current_user_can( $capability );
	}

	return $able;
}

/**
 * Checks user persmission level against minumum requirement
 * for displaying the 301 redirection field within SEO metabox.
 *
 * @return bool
 */
function user_can_see_seo_metabox_301_redirect() {
	$smartcrawl_options = Smartcrawl_Settings::get_options();
	$capability = ( defined( 'SMARTCRAWL_SEO_METABOX_301_ROLE' ) && SMARTCRAWL_SEO_METABOX_301_ROLE )
		? SMARTCRAWL_SEO_METABOX_301_ROLE
		: ( ! empty( $smartcrawl_options['seo_metabox_301_permission_level'] ) ? $smartcrawl_options['seo_metabox_301_permission_level'] : false );
	$capability = apply_filters( 'wds-capabilities-seo_metabox_301_redirect', $capability );
	$able = false;

	if ( is_array( $capability ) ) {
		foreach ( $capability as $cap ) {
			$able = current_user_can( $cap );
			if ( $able ) {
				break;
			}
		}
	} else {
		$able = current_user_can( $capability );
	}

	return $able;
}

/**
 * Attempt to hide metaboxes by default by adding them to "hidden" array.
 *
 * Metaboxes are still added to "Screen Options".
 * If user chooses to show/hide them, respect her decision.
 *
 * @deprecated as of version 1.0.9
 *
 * @param array $arg Whatever's been already hidden.
 *
 * @return array
 */
function smartcrawl_process_default_hidden_meta_boxes( $arg ) {
	$smartcrawl_options = Smartcrawl_Settings::get_options();
	$arg[] = 'wds-wds-meta-box';
	$arg[] = 'wds_seomoz_urlmetrics';

	return $arg;
}

/**
 * Hide ALL wds metaboxes.
 *
 * Respect wishes for other metaboxes.
 * Still accessble from "Screen Options".
 *
 * @param array $arg Whatever's been already hidden.
 *
 * @return array
 */
function smartcrawl_hide_metaboxes( $arg ) {
	// Hide WP defaults, if nothing else.
	if ( empty( $arg ) ) {
		$arg = array(
			'slugdiv',
			'trackbacksdiv',
			'postcustom',
			'postexcerpt',
			'commentstatusdiv',
			'commentsdiv',
			'authordiv',
			'revisionsdiv',
		);
	}
	$arg[] = 'wds-wds-meta-box';
	$arg[] = 'wds_seomoz_urlmetrics';

	return $arg;
}

/**
 * Register metabox hiding for other boxes.
 *
 * @deprecated
 */
function smartcrawl_register_metabox_hiding() {
	$post_types = get_post_types();
	foreach ( $post_types as $type ) {
		add_filter( 'get_user_option_metaboxhidden_' . $type, 'smartcrawl_hide_metaboxes' );
	}

}

/**
 * Forces metaboxes to start collapsed.
 *
 * It properly merges the WDS boxes with the rest of the users collapsed boxes.
 * For info on registering, see `register_metabox_collapsed_state`.
 *
 * @param array $closed Whatever's been closed this far.
 *
 * @return array
 */
function force_metabox_collapsed_state( $closed ) {
	$closed = is_array( $closed ) ? $closed : array();

	return array_merge( $closed, array(
		'wds-wds-meta-box',
		'wds_seomoz_urlmetrics',
	) );
}

/**
 * Registers WDS boxes state.
 * Collapsed state is tracked per post type.
 * This is why we have this separate hook to register state change processing.
 */
function register_metabox_collapsed_state() {
	global $post;
	if ( $post && $post->post_type ) {
		add_filter( 'get_user_option_closedpostboxes_' . $post->post_type, 'force_metabox_collapsed_state' );
	}
}

add_filter( 'post_edit_form_tag', 'register_metabox_collapsed_state' );

/**
 * Checks the page tab slug against permitted ones.
 *
 * This applies only for multisite, non-sitewide setups.
 *
 * @param string $slug Slug to check.
 *
 * @TODO: This function is a duplicate of Smartcrawl_Settings_Admin.is_tab_allowed, make sure only one remains
 *
 * @return bool
 */
function smartcrawl_is_allowed_tab( $slug ) {
	$blog_tabs = get_site_option( 'wds_blog_tabs' );
	$blog_tabs = is_array( $blog_tabs ) ? $blog_tabs : array();
	$allowed = true;
	if ( is_multisite() && ! SMARTCRAWL_SITEWIDE ) {
		$allowed = in_array( $slug, array_keys( $blog_tabs ), true ) ? true : false;
	}

	return $allowed;
}

/**
 * Checks if transient is stuck
 *
 * Stuck transient has no expiry time.
 * If so found, removes it.
 *
 * @param string $key Transient key.
 *
 * @return bool
 */
function smartcrawl_kill_stuck_transient( $key ) {
	global $_wp_using_ext_object_cache;
	if ( $_wp_using_ext_object_cache ) {
		return true;
	} // In object cache, nothing to do.

	$key = "_transient_{$key}";
	$alloptions = wp_load_alloptions();
	// If option is in alloptions, it is autoloaded and thus has no timeout - kill it.
	if ( isset( $alloptions[ $key ] ) ) {
		return delete_option( $key );
	}

	return true;
}

/**
 * Check for boolean define switches and their values.
 *
 * @param string $switch Define name to check.
 *
 * @return bool
 */
function smartcrawl_is_switch_active( $switch ) {
	$result = defined( $switch ) ? constant( $switch ) : false;

	/**
	 * Checks if a define switch is toggled on
	 *
	 * Used in tests.
	 *
	 * @param bool $result Whether the switch is turned on.
	 * @param string $switch Switch name.
	 *
	 * @return bool
	 */
	return (bool) apply_filters( 'smartcrawl_switch_active', $result, $switch );
}

/**
 * Check if we're on main BuddyPress site - BuddyPress root blog check.
 *
 * @return bool Are we on the main BuddyPress site.
 */
function smartcrawl_is_main_bp_site() {
	if ( is_multisite() && defined( 'BP_VERSION' ) && ( defined( 'BP_ROOT_BLOG' ) && BP_ROOT_BLOG ) ) {
		global $blog_id;

		return intval( BP_ROOT_BLOG ) === intval( $blog_id );
	}

	return is_main_site();
}

/**
 * Converts an argument map to HTML attributes string.
 *
 * @param array $args A hash of arguments.
 *
 * @return string Constructed attributes string
 */
function smartcrawl_autolinks_construct_attributes( $args = array() ) {
	$ret = array();
	if ( empty( $args ) ) {
		return '';
	}
	foreach ( $args as $key => $value ) {
		if ( empty( $key ) || empty( $value ) ) {
			continue; // Only accept properly formatted members.
		}
		$ret[] = esc_html( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return apply_filters( 'wds_autolinks_attributes', trim( join( ' ', $ret ) ) );
}

/**
 * Get a value from an array. If nothing is found for the provided keys, returns null.
 *
 * @param array $array The array to search (haystack).
 * @param array|string $key The key to use for the search.
 *
 * @return null|mixed The array value found or null if nothing found.
 */
function smartcrawl_get_array_value( $array, $key ) {
	if ( ! is_array( $key ) ) {
		$key = array( $key );
	}

	if ( ! is_array( $array ) ) {
		return null;
	}

	$value = $array;
	foreach ( $key as $key_part ) {
		$value = isset( $value[ $key_part ] ) ? $value[ $key_part ] : null;
	}

	return $value;
}

/**
 * Inserts a value in the given array.
 *
 * @param mixed $value The value to insert.
 * @param array $array The array in which the value is to be inserted. Passed by reference.
 * @param array|string $keys Key specifying the place where the new value is to be inserted.
 *
 * @return void
 */
function smartcrawl_put_array_value( $value, &$array, $keys ) {
	if ( ! is_array( $keys ) ) {
		$keys = array( $keys );
	}

	$pointer = &$array;
	foreach ( $keys as $key ) {
		if ( ! isset( $pointer[ $key ] ) ) {
			$pointer[ $key ] = array();
		}
		$pointer = &$pointer[ $key ];
	}
	$pointer = $value;
}

/**
 * Checks if a dashboard widget mode is renderable
 *
 * Used on dashboard root page, for scenarios when we're
 * outside the network-wide mode and not all tabs are active
 * for site admins, in order to prevent showing broken "configure" links
 * and such. Re: https://app.asana.com/0/46496453944769/509480319187557/f
 *
 * @param string $tab Tab to check.
 *
 * @return bool
 */
function smartcrawl_can_show_dash_widget_for( $tab ) {
	if ( ! ! smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) ) {
		return true;
	}

	if ( ! is_network_admin() ) {
		return true;
	} // Whatever, let site admin deal with it.

	// Not in sitewide mode, let's check if site admins can access it.
	$allowed_blog_tabs = Smartcrawl_Settings_Settings::get_blog_tabs();
	$allowed = in_array( $tab, array_keys( $allowed_blog_tabs ), true ) && ! empty( $allowed_blog_tabs[ $tab ] );

	return $allowed;
}

/**
 * Sanitizes a string into relative URL
 *
 * @param string $raw Raw string to process.
 *
 * @return string Root-relative string
 */
function smartcrawl_sanitize_relative_url( $raw ) {
	$raw = preg_match( '/^https?:\/\//', $raw ) || preg_match( '/^\//', $raw )
		? esc_url( $raw )
		: esc_url( "/{$raw}" );

	$parsed = wp_parse_url( $raw );
	$result = '';

	if ( empty( $parsed ) ) {
		$domain = preg_replace( '/^https?:\/\//', '', home_url() );
		$raw = preg_replace( '/^https?:\/\//', '', $raw );
		$result = preg_replace( '/^' . preg_quote( $domain, '/' ) . '/', '', $raw );
	} else {
		$result = ! empty( $parsed['path'] ) ? $parsed['path'] : '/';
		if ( ! empty( $parsed['query'] ) ) {
			$result .= '?' . $parsed['query'];
		}
	}

	return '/' . ltrim( $result, '/' );
}

/**
 * Gets regex for matching against a list of relative URLs
 *
 * @param string $urls A list of relative URLs.
 *
 * @return string
 */
function smartcrawl_get_relative_urls_regex( $urls ) {
	$regex = '';
	if ( ! is_array( $urls ) ) {
		return $regex;
	}

	$processed = array();
	foreach ( $urls as $url ) {
		if ( empty( $url ) ) {
			continue;
		}
		$processed[] = preg_quote( $url, '/' );
	}
	$regex = '/https?:\/\/.*?(' . join( '|', $processed ) . ')\/?$/';

	return $regex;
}

function smartcrawl_subsite_setting_page_enabled( $key ) {
	if ( ! is_multisite() || smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) ) {
		return true;
	}

	return (boolean) smartcrawl_get_array_value( get_site_option( 'wds_blog_tabs', array() ), $key );
}

function smartcrawl_get_attachment_id_by_url( $url ) {
	global $wpdb;

	return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid=%s", $url ) );
}

function smartcrawl_get_attachment_by_url( $url ) {
	$attachment_id = smartcrawl_get_attachment_id_by_url( $url );
	if ( $attachment_id ) {
		$attachment_image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

		return $attachment_image_src
			? array(
				'url'    => $attachment_image_src[0],
				'width'  => $attachment_image_src[1],
				'height' => $attachment_image_src[2],
			)
			: null;
	}

	return null;
}

function smartcrawl_get_archive_post_types() {
	return array_keys( smartcrawl_get_archive_post_type_labels() );
}

function smartcrawl_get_archive_post_type_labels() {
	$archive_post_types = array();
	$post_type_args = array(
		'public'      => true,
		'has_archive' => true,
	);
	foreach ( get_post_types( $post_type_args ) as $post_type ) {
		if ( in_array( $post_type, array( 'revision', 'nav_menu_item' ), true ) ) {
			continue;
		}

		$post_type_object = get_post_type_object( $post_type );
		$archive_post_types[ 'pt-archive-' . $post_type ] = $post_type_object->labels->name;
	}

	return $archive_post_types;
}

/**
 * Fetch the actual sitemap path, to the best of our abilities.
 *
 * @return string Sitemap path
 */
function smartcrawl_get_sitemap_path() {
	$smartcrawl_options = Smartcrawl_Settings::get_options();

	$dir = wp_upload_dir();
	$path = ! empty( $smartcrawl_options['sitemappath'] ) ? $smartcrawl_options['sitemappath'] : false; // First thing first, try the sitewide option.

	// Not in sitewide mode, check per-blog options.
	if ( ! SMARTCRAWL_SITEWIDE ) {
		$_data = get_option( 'wds_sitemap_options' );
		$path = ! empty( $_data['sitemappath'] ) ? $_data['sitemappath'] : false;
	}

	// If there isn't a dir we need to write to, or we're on a network child in sitewide mode, go for the uploads dir.
	if ( ! is_dir( dirname( $path ) ) || ( SMARTCRAWL_SITEWIDE && ! is_main_site() ) ) {
		$path = trailingslashit( $dir['basedir'] );
		$path = "{$path}sitemap.xml";
	}

	return wp_normalize_path( $path );
}

/**
 * Fetch sitemap URL in an uniform fashion.
 *
 * @return string Sitemap URL
 */
function smartcrawl_get_sitemap_url() {
	$smartcrawl_options = Smartcrawl_Settings::get_options();
	$sitemap_options = ( is_multisite() && is_main_site() ) ? $smartcrawl_options : get_option( 'wds_sitemap_options' );
	$sitemap_url = ! empty( $sitemap_options['sitemapurl'] ) ? $sitemap_options['sitemapurl'] : false;

	if ( empty( $sitemap_url ) ) {
		$sitemap_url = trailingslashit( home_url( false ) ) . 'sitemap.xml';
	}

	if ( is_multisite() && class_exists( 'domain_map' ) ) {
		$sitemap_url = home_url( false ) . '/sitemap.xml';

		if ( defined( 'SMARTCRAWL_SITEMAP_DM_SIMPLE_DISCOVERY_FALLBACK' ) && SMARTCRAWL_SITEMAP_DM_SIMPLE_DISCOVERY_FALLBACK ) {
			$sitemap_url = ( is_network_admin() ? '../../' : ( is_admin() ? '../' : '/' ) ) . 'sitemap.xml'; // Simplest possible logic.
		}
	}

	return apply_filters( 'wds-sitemaps-sitemap_url', $sitemap_url );
}

function smartcrawl_get_allowed_html_for_forms() {
	return array(
		'form'  => array(
			'class'   => array(),
			'id'      => array(),
			'action'  => array(),
			'method'  => array(),
			'enctype' => array(),
		),
		'input' => array(
			'class' => array(),
			'id'    => array(),
			'type'  => array(),
			'name'  => array(),
			'value' => array(),
		),
	);
}

function smartcrawl_file_get_contents( $file ) {
	return file_get_contents( $file ); // phpcs:ignore -- WP_Filesystem doesn't work properly
}

function smartcrawl_file_put_contents( $file, $contents, $flags = 0 ) {
	return file_put_contents( $file, $contents, $flags ); // phpcs:ignore -- WP_Filesystem doesn't work properly
}

/**
 * Gets whatever's latest of a post
 *
 * @param int $post_id Post ID.
 *
 * @return WP_Post
 */
function smartcrawl_get_latest_post_version( $post_id ) {
	$post = get_post( $post_id );
	$post_revisions = wp_get_post_revisions( $post_id, array(
		'orderby'       => 'modified',
		'order'         => 'DESC',
		'check_enabled' => false,
	) );
	if ( count( $post_revisions ) ) {
		$revision = array_shift( $post_revisions );
		if ( strtotime( $revision->post_modified ) > strtotime( $post->post_modified ) ) {
			return $revision;
		}
	}

	return $post;
}

/**
 * Checks whether the supplied string is a valid meta tag
 *
 * @param string $string String to check.
 *
 * @return bool
 */
function smartcrawl_is_valid_meta_tag( $string ) {
	$string = trim( $string );
	if ( ! preg_match( '/^\<meta/i', $string ) ) {
		return false;
	}
	if ( ! preg_match( '/\>$/', $string ) ) {
		return false;
	}

	return true;
}

function smartcrawl_get_dash_profile_data( $default = null ) {
	if (
		class_exists( 'WPMUDEV_Dashboard' )
		&& isset( WPMUDEV_Dashboard::$site )
		&& is_callable( array( WPMUDEV_Dashboard::$site, 'get_option' ) )
	) {
		$profile_data = WPMUDEV_Dashboard::$site->get_option( 'profile_data' );
		$name = empty( $profile_data['profile']['name'] )
			? ''
			: $profile_data['profile']['name'];
		$email = empty( $profile_data['profile']['user_name'] )
			? ''
			: sanitize_email( $profile_data['profile']['user_name'] );

		if ( $name && $email ) {
			return (object) array(
				'user_login' => $name,
				'user_email' => $email,
			);
		}
	}

	return $default;
}

function smartcrawl_sui_class() {
	$classes[] = defined( 'SMARTCRAWL_SUI_VERSION' ) && SMARTCRAWL_SUI_VERSION
		? 'sui-' . str_replace( '.', '-', SMARTCRAWL_SUI_VERSION )
		: '';

	$hide_branding = Smartcrawl_White_Label::get()->is_hide_wpmudev_branding();
	if ( $hide_branding ) {
		$classes[] = 'wds-no-branding';
	}

	return implode( ' ', $classes );
}

function smartcrawl_format_link( $text, $url, $anchor = '' ) {
	if ( empty( $anchor ) ) {
		$anchor = esc_html__( 'click here' );
	}

	return sprintf(
		$text,
		sprintf(
			'<a href="%s">%s</a>',
			esc_url_raw( $url ),
			$anchor
		)
	);
}

function smartcrawl_sanitize_preserve_macros( $str ) {
	if ( empty( $str ) ) {
		return $str;
	}

	$rpl = '__SMARTCRAWL_MACRO_QUOTES_REPLACEMENT__';
	$str = preg_replace( '/%%/', $rpl, $str );

	$str = sanitize_text_field( $str );

	$str = preg_replace( '/' . preg_quote( $rpl, '/' ) . '/', '%%', $str );

	return $str;
}

/**
 * @deprecated 2.3.0 Use the method Smartcrawl_Replacement_Helper::replace
 */
function smartcrawl_replace_vars( $string, $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.3.0', 'Smartcrawl_Replacement_Helper::replace()' );
	return Smartcrawl_Replacement_Helper::replace( $string, $args );
}
