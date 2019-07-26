<?php

class Smartcrawl_Macro {

	const OPEN_MACRO_DELIM = '%%';
	const CLOSE_MACRO_DELIM = '%%';

	/**
	 * Holds defaults definitions for the current replacement
	 *
	 * @var array
	 */
	private $_defaults = array();

	/**
	 * Holds temporary overrides definitions for the current replacement
	 *
	 * @var array
	 */
	private $_overrides = array();

	/**
	 * Post factory method
	 *
	 * @param int|WP_Post $p Post to boot from
	 *
	 * @return Smartcrawl_Macro instance
	 */
	public static function post( $p ) {
		$post = ! is_object( $p )
			? get_post( $p )
			: new WP_Post( $p );

		return self::with(
			(array) $post
		);
	}

	/**
	 * Generic data booting method
	 *
	 * @param array $data Data to pass as defaults
	 *
	 * @return Smartcrawl_Macro instance
	 */
	public static function with( $data ) {
		$me = new self();
		if ( ! is_array( $data ) ) {
			return $me;
		}
		$me->set_defaults( $data );

		return $me;
	}

	/**
	 * Merges in a batch of overrides
	 *
	 * @param array $values A list of values to merge in
	 *
	 * @return Smartcrawl_Macro instance
	 */
	public function merge( $values = array() ) {
		if ( ! is_array( $values ) ) {
			return $this;
		}
		$this->_overrides = array_merge( $this->_overrides, $values );

		return $this;
	}

	/**
	 * Expands known macro definitions
	 *
	 * @param string $str String to process
	 *
	 * @return string Processed input
	 */
	public function expand( $str ) {
		$known = $this->get_values();
		$replacements = $this->get_replacements();

		$ret = '' . $str;

		foreach ( $replacements as $subject => $expansion ) {
			$value = '';

			if ( '@' === substr( $expansion, 0, 1 ) ) {
				$key = substr( $expansion, 1 );
				$value = ! empty( $known[ $key ] ) ? $known[ $key ] : '';
			} elseif ( is_callable( array( $this, $expansion ) ) ) {
				$value = call_user_func( array( $this, $expansion ) );
			}

			$macro = $this->get_macro( $subject );
			$ret = str_replace( $macro, $value, $ret );
		}

		return $ret;
	}

	/**
	 * Gets a full list of values ready for replacement
	 *
	 * Includes both defaults and the merged in overrides
	 *
	 * @return array All known values
	 */
	public function get_values() {
		return array_merge(
			$this->get_defaults(),
			$this->_overrides
		);
	}

	/**
	 * Defaults getter
	 *
	 * @return array Defaults
	 */
	public function get_defaults() {
		return (array) $this->_defaults;
	}

	/**
	 * Defaults setter
	 *
	 * @param array $defaults Defaults to be used
	 *
	 * @return Smartcrawl_Macro instance
	 */
	public function set_defaults( $defaults = array() ) {
		if ( ! is_array( $defaults ) ) {
			return $this;
		}
		$this->_defaults = $defaults;

		return $this;
	}

	/**
	 * Replacements map getter
	 *
	 * @return array Hash of replacements mappings
	 */
	public function get_replacements() {
		$rpl = array(
			'date'              => '@post_date',
			'title'             => '@post_title',
			'term_title'        => '@name',
			'tag'               => '@name',
			'modified'          => '@post_modified',
			'id'                => '@ID',
			'caption'           => '@post_excerpt',
			'bp_group_name'     => '@name',
			'bp_user_username'  => '@username',
			'bp_user_full_name' => '@full_name',
			'excerpt_only'      => '@post_excerpt',

			'excerpt'              => 'get_excerpt',
			'sitename'             => 'get_site_name',
			'sitedesc'             => 'get_site_description',
			'userid'               => 'get_post_author',
			'name'                 => 'get_author_name',
			'bp_group_description' => 'get_bp_group_description',
			'searchphrase'         => 'get_search_phrase',
			'currenttime'          => 'get_current_time',
			'currentdate'          => 'get_current_date',
			'currentmonth'         => 'get_current_month',
			'currentyear'          => 'get_current_year',
			'page'                 => 'get_page',
			'spell_page'           => 'get_page_spelled',
			'pagetotal'            => 'get_page_total',
			'spell_pagetotal'      => 'get_page_total_spelled',
			'pagenumber'           => 'get_pagenum',
			'spell_pagenumber'     => 'get_pagenum_spelled',
			'category'             => 'get_category',
			'category_description' => 'get_taxonomy_description',
			'tag_description'      => 'get_taxonomy_description',
			'term_description'     => 'get_taxonomy_description',
		);

		return $rpl;
	}

	/**
	 * Gets fully qualified macro
	 *
	 * @param string $bare Tag to convert to macro
	 *
	 * @return string Macro
	 */
	public function get_macro( $bare ) {
		return $this->get_macro_open() . "{$bare}" . $this->get_macro_close();
	}

	/**
	 * Gets macro opening sequence
	 *
	 * @return string Opening
	 */
	public function get_macro_open() {
		return self::OPEN_MACRO_DELIM;
	}

	/**
	 * Gets macro closing sequence
	 *
	 * @return string Closing
	 */
	public function get_macro_close() {
		return self::CLOSE_MACRO_DELIM;
	}

	public function get_site_name() {
		return get_bloginfo( 'name' );
	}

	// --- Value getters

	public function get_site_description() {
		return get_bloginfo( 'description' );
	}

	public function get_excerpt() {
		return smartcrawl_get_trimmed_excerpt( $this->get_value( 'post_excerpt' ), $this->get_value( 'post_content' ) );
	}

	/**
	 * Gets single value
	 *
	 * @param string $key Which value to get
	 * @param string $fallback Fallback value
	 *
	 * @return string Value or fallback
	 */
	public function get_value( $key, $fallback = '' ) {
		$values = $this->get_values();

		return isset( $values[ $key ] )
			? $values[ $key ]
			: $fallback;
	}

	public function get_author_name() {
		return get_the_author_meta( 'display_name', $this->get_post_author() );
	}

	public function get_post_author() {
		return $this->get_value( 'post_author', get_query_var( 'author' ) );
	}

	public function get_bp_group_description() {
		return smartcrawl_get_trimmed_excerpt( '', $this->get_value( 'description' ) );
	}

	public function get_search_phrase() {
		return esc_html( get_query_var( 's' ) );
	}

	public function get_current_time() {
		return date_i18n( get_option( 'time_format' ) );
	}

	public function get_current_date() {
		return date_i18n( get_option( 'date_format' ) );
	}

	public function get_current_month() {
		return date( 'F' );
	}

	public function get_current_year() {
		return date( 'Y' );
	}

	public function get_page() {
		if ( 0 === intval( get_query_var( 'paged' ) ) ) {
			return '';
		}

		return sprintf(
			__( 'Page %1$d of %1$d', 'wds' ),
			get_query_var( 'paged' ),
			$this->get_page_total( 1 )
		);
	}

	public function get_page_total( $fallback = '' ) {
		global $wp_query;

		return $wp_query->max_num_pages > 1
			? $wp_query->max_num_pages
			: $fallback;
	}

	public function get_page_spelled() {
		if ( 0 === intval( get_query_var( 'paged' ) ) ) {
			return '';
		}

		return sprintf(
			__( 'Page %1$s of %1$s', 'wds' ),
			smartcrawl_spell_number( get_query_var( 'paged' ) ),
			smartcrawl_spell_number( $this->get_page_total( 1 ) )
		);
	}

	public function get_page_total_spelled() {
		return smartcrawl_spell_number( $this->get_page_total() );
	}

	public function get_pagenum_spelled() {
		return smartcrawl_spell_number( $this->get_pagenum() );
	}

	public function get_pagenum() {
		global $pagenum;

		return $pagenum;
	}

	public function get_category() {
		$list = get_the_category_list( '', '', $this->get_value( 'ID' ) );

		return ! empty( $list )
			? trim( strip_tags( $list ) )
			: $this->get_value( $name );
	}

	public function get_taxonomy_description() {
		$tax = $this->get_value( 'taxonomy' );

		return ! empty( $tax )
			? trim( strip_tags( get_term_field( 'description', $this->get_value( 'term_id' ), $this->get_value( 'taxonomy' ) ) ) )
			: '';
	}
}
