<?php

class Smartcrawl_Html {

	const NODE_CONTENT = 'innertext';

	const NODE_MARKUP = 'outertext';

	const NODE_TEXT = 'plaintext';

	/**
	 * Holds de-texturizing charmap pairs
	 *
	 * @var array
	 */
	private static $_charmap = array(
		'2026' => '...',
		'201C' => '"', // LEFT DOUBLE QUOTATION MARK
		'201D' => '"', // RIGHT DOUBLE QUOTATION MARK
		'2018' => "'", // LEFT SINGLE QUOTATION MARK
		'2019' => "'", // RIGHT SINGLE QUOTATION MARK
		'2014' => '-', // Emdash
		'2013' => '-', // Endash
	);

	/**
	 * Holds cached DOM object instances
	 *
	 * @var array
	 */
	private static $_doms_cache = array();

	/**
	 * Retrieves plain text representation of HTML fragment
	 *
	 * @param string $html Markup fragment
	 *
	 * @return string Plaintext
	 */
	public static function plaintext( $html ) {
		$html = preg_replace( '/.*<body[^>]*?>/s', '', $html );
		$html = preg_replace( '/<\/body>.*/', '', $html );
		$html = self::decorate( $html );
		$text = self::decode( wp_strip_all_tags( $html ) );

		return $text;
	}

	/**
	 * Does *standard* text decoration according to WP rules
	 *
	 * This is done instead of `the_content` filtering to prevent
	 * triggering additional output for broken implementations.
	 *
	 * @param string $html Markup fragment to decorate
	 *
	 * @return string Decorated markup
	 */
	public static function decorate( $html ) {
		// $html = do_shortcode($html);   // Do NOT!!! do shortcode expansion.
		// It will break shortcode layout themes (e.g. Divi).
		$html = strip_shortcodes( $html ); // Let's strip them instead.

		$html = capital_P_dangit( $html );
		$html = convert_smilies( $html );
		$html = wptexturize( $html );
		$html = wpautop( $html );
		$html = shortcode_unautop( $html );

		return $html;
	}

	/**
	 * Decodes HTML entities in a controlled fashion
	 *
	 * @param string $html Markup fragment
	 *
	 * @return string Decoded fragment
	 */
	public static function decode( $html ) {
		$str = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );

		foreach ( self::get_decode_charmap() as $utf8 => $rpl ) {
			$str = preg_replace( '/\x{' . $utf8 . '}/u', $rpl, $str );
		}

		return $str;
	}

	/**
	 * Returns internal charmap
	 *
	 * @return array List of charmap replacements as utf8 code => replacement pairs
	 */
	public static function get_decode_charmap() {
		return self::$_charmap;
	}

	/**
	 * Finds DOM nodes matching selector and extracts their content
	 *
	 * @param string $selector Expression to select DOM nodes
	 * @param string $html Markup to select nodes from
	 *
	 * @return array A map of node => content pairs
	 */
	public static function find_content( $selector, $html ) {
		return self::find_attributes( $selector, self::NODE_CONTENT, $html );
	}

	/**
	 * Finds DOM nodes matching selector and extracts their requested attribute
	 *
	 * @param string $selector Expression to select DOM nodes
	 * @param string $attr Attribute to extract
	 * @param string $html Markup to select nodes from
	 *
	 * @return array A map of node => attribute pairs
	 */
	public static function find_attributes( $selector, $attr, $html ) {
		$nodes = self::find( $selector, $html );
		if ( empty( $nodes ) ) {
			return array();
		}

		return array_combine(
			$nodes,
			wp_list_pluck( $nodes, $attr )
		);
	}

	/**
	 * Finds DOM nodes matching the selector
	 *
	 * @param string $selector Expression to select DOM nodes
	 * @param string $html Markup to select nodes from
	 *
	 * @return array Selected DOM nodes
	 */
	public static function find( $selector, $html ) {
		if ( empty( $html ) || empty( $selector ) ) {
			return array();
		}

		$ret = self::get_dom( $html )->find( $selector );

		return ! empty( $ret )
			? $ret
			: array();
	}

	/**
	 * Gets a DOM object instance
	 *
	 * @param string $html Optional HTML string
	 *
	 * @return object simple_html_dom object instance
	 */
	public static function get_dom( $html = '' ) {
		if ( ! class_exists( 'simple_html_dom' ) ) {
			require_once SMARTCRAWL_PLUGIN_DIR . '/external/simple_html_dom.php';
		}
		if ( empty( $html ) ) {
			return new simple_html_dom();
		}

		$key = md5( $html );
		if ( ! empty( self::$_doms_cache[ $key ] ) ) {
			return self::$_doms_cache[ $key ];
		}

		self::$_doms_cache[ $key ] = new simple_html_dom();
		self::$_doms_cache[ $key ]->load( $html );

		return self::$_doms_cache[ $key ];
	}

	/**
	 * Finds DOM nodes matching selector and extracts their markup
	 *
	 * @param string $selector Expression to select DOM nodes
	 * @param string $html Markup to select nodes from
	 *
	 * @return array A map of node => markup pairs
	 */
	public static function find_markup( $selector, $html ) {
		return self::find_attributes( $selector, self::NODE_MARKUP, $html );
	}

	/**
	 * Plucks attribute value from node
	 *
	 * @param object $node simple_html_dom_node object
	 * @param string $attr Attribute to extract
	 *
	 * @return string Attribute value
	 */
	public static function get_attribute( $node, $attr ) {
		if ( ! is_object( $node ) ) {
			return '';
		}
		if ( empty( $attr ) ) {
			return '';
		}

		return '' . $node->$attr;
	}
}
