<?php

/**
 * Outputs OG tags to the page
 */
class Smartcrawl_OpenGraph_Printer {

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

	private function _add_hooks() {
		// Do not double-bind
		if ( apply_filters( 'wds-opengraph-is_running', $this->_is_running ) ) {
			return true;
		}

		add_action( 'wp_head', array( $this, 'dispatch_og_tags_injection' ), 50 );
		add_action( 'wds_head-after_output', array( $this, 'dispatch_og_tags_injection' ) );

		$this->_is_running = true;
	}

	/**
	 * Singleton instance getter
	 *
	 * @return Smartcrawl_OpenGraph_Printer instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * First-line dispatching of OG tags injection
	 */
	public function dispatch_og_tags_injection() {
		if ( ! ! $this->_is_done ) {
			return false;
		}

		$settings = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		if ( empty( $settings['og-enable'] ) ) {
			return false;
		}
		$this->inject_global_tags();

		$this->_is_done = true;

		return $this->inject_og_tags();
	}

	/**
	 * Injects globally valid tags - regardless of context
	 */
	public function inject_global_tags() {
		$settings = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SOCIAL );
		if ( ! empty( $settings['fb-app-id'] ) ) {
			$this->print_og_tag( 'fb:app_id', $settings['fb-app-id'] );
		}
	}

	/**
	 * Actually prints the OG tag
	 *
	 * @param string $tag Tagname or tagname-like string to print
	 * @param mixed $value Tag value as string, or list of string tag values
	 *
	 * @return bool
	 */
	public function print_og_tag( $tag, $value ) {
		if ( empty( $tag ) || empty( $value ) ) {
			return false;
		}

		$og_tag = $this->get_og_tag( $tag, $value );
		if ( empty( $og_tag ) ) {
			return false;
		}

		echo wp_kses( $og_tag, $this->get_allowed_tags() );

		return true;
	}

	/**
	 * Gets the markup for an OG tag
	 *
	 * @param string $tag Tagname or tagname-like string to print
	 * @param mixed $value Tag value as string, or list of string tag values
	 *
	 * @return string
	 */
	public function get_og_tag( $tag, $value ) {
		if ( empty( $tag ) || empty( $value ) ) {
			return false;
		}

		return '<meta property="' . esc_attr( $tag ) . '" content="' . esc_attr( $value ) . '" />' . "\n";
	}

	private function inject_type() {
		if ( is_front_page() || is_home() ) {
			$type = 'website';
		} elseif ( is_singular() ) {
			$type = 'article';
		} else {
			$type = 'object';
		}
		$this->print_og_tag( 'og:type', $type );
	}

	private function inject_url() {
		$helper = new Smartcrawl_Canonical_Value_Helper();
		$canonical_url = $helper->get_canonical();

		if ( ! empty( $canonical_url ) && ! is_wp_error( $canonical_url ) ) {
			$this->print_og_tag( 'og:url', $canonical_url );
		}
	}

	/**
	 * Attempt to use post-specific meta setup to resolve tag values
	 *
	 * Fallback to generic, global values
	 *
	 * @return bool
	 */
	public function inject_og_tags() {
		$value_helper = new Smartcrawl_OpenGraph_Value_Helper();
		if ( ! $value_helper->is_enabled() ) {
			return false;
		}

		$this->inject_type();
		$this->inject_url();
		$this->print_og_tag( 'og:title', $value_helper->get_title() );
		$this->print_og_tag( 'og:description', $value_helper->get_description() );
		$this->print_og_images( $value_helper->get_images() );

		if ( is_singular() ) {
			$post = get_post();
			$date = get_the_date( 'Y-m-d\TH:i:s', $post );
			$this->print_og_tag( 'article:published_time', $date );

			$user_id = $post->post_author;
			if ( ! empty( $user_id ) ) {
				$user = Smartcrawl_Model_User::get( $user_id );
				$this->print_og_tag( 'article:author', $user->get_full_name() );
			}
		}

		return true;
	}

	public function print_og_images( $values ) {
		if ( empty( $values ) ) {
			return;
		}

		$values = is_string( $values ) ? array( $values ) : $values;

		$image_tags = array();
		foreach ( $values as $value ) {
			$image_tags[] = $this->get_og_tag( 'og:image', $value );

			// Try to inject image width and height
			$attachment = smartcrawl_get_attachment_by_url( trim( $value ) );
			if ( $attachment ) {
				$image_tags[] = $this->get_og_tag( 'og:image:width', $attachment['width'] );
				$image_tags[] = $this->get_og_tag( 'og:image:height', $attachment['height'] );
			}
		}
		$markup = join( "", array_filter( $image_tags ) );
		echo wp_kses( $markup, $this->get_allowed_tags() );
	}

	/**
	 * @return array
	 */
	private function get_allowed_tags() {
		$allowed_tags = array(
			'meta' => array(
				'property' => array(),
				'content'  => array(),
			),
		);

		return $allowed_tags;
	}
}
