<?php
/**
 * Checks hub
 *
 * @package wpmu-dev-seo
 */

/**
 * Checks dispatcher class
 */
class Smartcrawl_Checks extends Smartcrawl_WorkUnit {

	const ENDPOINT = 'endpoint';
	const POST = 'post';

	/**
	 * Remote handler for frontend post fetching
	 *
	 * @var Smartcrawl_Core_Request
	 */
	private $_endpoint_remote_handler;

	/**
	 * Holds reference to checks that deal with final rendered content
	 *
	 * @var array
	 */
	private $_endpoint_checks = array(
		'imgalts_keywords',
		'content_length',
		'keyword_density',
		'links_count',
		'para_keywords',
		'subheadings_keywords',
	);

	/**
	 * Holds reference to checks that deal with raw post data
	 *
	 * @var array
	 */
	private $_post_checks = array(
		'focus',
		'focus_stopwords',
		'title_keywords',
		'title_length',
		'metadesc_keywords',
		'metadesc_length',
		'slug_keywords',
		'keywords_used',
	);

	/**
	 * Holds a reference to all checks that have been
	 * applied in this checks run
	 *
	 * @var array
	 */
	private $_applied_checks = array();

	/**
	 * Post ID
	 *
	 * @var int
	 */
	private $_post_id;

	/**
	 * Static entry point, can be used instead of constructor
	 *
	 * Applies all queued checks to the subject post
	 *
	 * @param int $post_id ID of the post to check.
	 * @param object $request Optional Smartcrawl_Core_Request instance to use - used in testing.
	 *
	 * @return object Smartcrawl_Checks instance
	 */
	public static function apply( $post_id, $request = false ) {
		$me = new self();
		$me->set_post_id( $post_id );

		if ( ! empty( $request ) ) {
			$me->set_remote_handler( $request );
		}

		$post_status = $me->apply_post_checks();
		$endpoint_status = $me->apply_endpoint_checks();

		return $me;
	}

	/**
	 * Sets internal post ID
	 *
	 * @param int $post_id Post ID to check.
	 *
	 * @return bool Status
	 */
	public function set_post_id( $post_id ) {
		$this->_post_id = (int) $post_id;

		return ! ! $this->_post_id;
	}

	public function set_remote_handler( $request ) {
		$this->_endpoint_remote_handler = $request;
	}

	/**
	 * Applies check tests to post-specific queue
	 *
	 * @return bool Overall status
	 */
	public function apply_post_checks() {
		$subject = $this->apply_filters(
			'subject-post',
			false
		);
		if ( empty( $subject ) ) {
			$subject = get_post( $this->_post_id );
		}

		return $this->apply_checks( $this->_post_checks, $subject );
	}

	/**
	 * Applies the checks in queue
	 *
	 * @param array $checks A list of checks to apply.
	 * @param mixed $subject Subject to apply the checks to.
	 *
	 * @return bool Overall status
	 */
	public function apply_checks( $checks, $subject ) {
		$overall_result = true;

		$keywords = $this->get_focus();
		foreach ( $checks as $check_id ) {
			/**
			 * @var $check Smartcrawl_Check_Abstract
			 */
			$check = $this->get_check( $check_id );
			$check->set_subject( $subject );
			$check->set_focus( $keywords );

			$is_ignored = $this->is_ignored_check( $check_id );
			$result = true;
			if ( ! $is_ignored ) {
				$result = $check->apply();
				if ( ! $result ) {
					$overall_result = false;
					$this->add_error( $check_id, $check->get_status_msg() );
				}
			}
			$this->_applied_checks[ $check_id ] = array(
				'status'         => $result,
				'ignored'        => $is_ignored,
				'recommendation' => $check->get_recommendation(),
				'more_info'      => $check->get_more_info(),
				'status_msg'     => $check->get_status_msg(),
			);
		}

		return $overall_result;
	}

	/**
	 * Gets a list of keywords
	 *
	 * @return array A list of expected keywords
	 */
	public function get_focus() {
		$post = get_post( $this->_post_id );
		$keywords = Smartcrawl_Meta_Value_Helper::get()->get_focus_keywords( $post );

		return (array) $this->apply_filters(
			'focus',
			$keywords, $this->_post_id
		);
	}

	/**
	 * Instantiates check according to check ID
	 *
	 * @param string $check_id Check to be instantiated.
	 *
	 * @return bool|object Smartcrawl_Check_abstract object instance on success,
	 *                     (bool)false on failure
	 */
	public function get_check( $check_id ) {
		$cname = $this->get_check_class_name( $check_id );
		if ( ! class_exists( $cname ) ) {
			return false;
		}

		return new $cname();
	}

	private function get_check_class_name( $check_id ) {
		$cname = sprintf( "Smartcrawl Check %s", str_replace( '_', ' ', $check_id ) );

		return str_replace( ' ', '_', ucwords( $cname ) );
	}

	/**
	 * Checks whether a check is to be ignored for the current post
	 *
	 * @param string $check_id ID of the check.
	 *
	 * @return bool
	 */
	public function is_ignored_check( $check_id ) {
		$ignored = self::get_ignored_checks( $this->_post_id );

		return in_array( $check_id, $ignored, true );
	}

	/**
	 * Gets a list of post-specific ignored checks
	 *
	 * Ignored checks are skipped in analysis
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array A list of check IDs
	 */
	public static function get_ignored_checks( $post_id ) {
		// Make sure meta is fetched from the post, not a revision. The same thing is done in WP function update_post_meta
		if ( $the_post = wp_is_post_revision( $post_id ) ) {
			$post_id = $the_post;
		}
		$ignored = get_post_meta( $post_id, '_wds_ignored_checks', true );

		return is_array( $ignored ) && ! empty( $ignored )
			? $ignored
			: array();
	}

	/**
	 * Applies check tests to endpoint-specific queue
	 *
	 * @return bool Overall status
	 */
	public function apply_endpoint_checks() {
		if ( empty( $this->_endpoint_checks ) ) {
			return true;
		}

		$subject = $this->apply_filters(
			'subject-endpoint',
			false
		);
		if ( empty( $subject ) ) {
			$subject = $this->get_endpoint_content();
		}
		if ( false === $subject ) {
			$this->add_error( 'checks', __( 'We encountered an error fetching your content', 'wds' ) );

			return false;
		}

		return $this->apply_checks( $this->_endpoint_checks, $subject );
	}

	/**
	 * Fetches local endpoint via HTTP API
	 *
	 * @return bool|string Endpoint content as string, or
	 *                     (bool)false if something went wrong
	 */
	public function get_endpoint_content() {
		$content = false;

		if ( empty( $this->_endpoint_remote_handler ) || ! ( $this->_endpoint_remote_handler instanceof Smartcrawl_Core_Request ) ) {
			$this->set_remote_handler( new Smartcrawl_Core_Request() );
		}
		$content = $this->_endpoint_remote_handler->get_rendered_post( $this->_post_id );
		if ( is_wp_error( $content ) ) {
			return false;
		}

		return (string) $content;
	}

	/**
	 * Whether the readability check for this post is ignored
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	public static function is_readability_ignored( $post_id ) {
		$ignored = self::get_ignored_checks( $post_id );

		return in_array( 'readability', $ignored, true );

	}

	/**
	 * Adds a single check to the ignored checks stack
	 *
	 * @param int $post_id ID of the post.
	 * @param string $check_id ID of the check.
	 *
	 * @return bool
	 */
	public static function add_ignored_check( $post_id, $check_id ) {
		$ignored = self::get_ignored_checks( $post_id );
		$ignored[] = $check_id;

		return self::set_ignored_checks( $post_id, $ignored );
	}

	/**
	 * Updates a list of ignored checks
	 *
	 * @param int $post_id ID of the post.
	 * @param array $checks Full list of checks.
	 *
	 * @return bool
	 */
	public static function set_ignored_checks( $post_id, $checks ) {
		if ( ! is_array( $checks ) ) {
			return false;
		}
		$checks = array_filter( array_map( 'trim', array_unique( $checks ) ) );

		return update_post_meta( $post_id, '_wds_ignored_checks', $checks );
	}

	/**
	 * Removes a single check from the ignored checks stack
	 *
	 * @param int $post_id ID of the post.
	 * @param string $check_id ID of the check.
	 *
	 * @return bool
	 */
	public static function remove_ignored_check( $post_id, $check_id ) {
		$ignored = self::get_ignored_checks( $post_id );
		$key = array_search( $check_id, $ignored, true );

		if ( false === $key ) {
			return false;
		}
		unset( $ignored[ $key ] );

		return self::set_ignored_checks( $post_id, $ignored );
	}

	/**
	 * Gets checks that have been applied in this run
	 *
	 * @return array
	 */
	public function get_applied_checks() {
		return $this->_applied_checks;
	}

	/**
	 * Calculates approximate checks success percentage
	 *
	 * Approximate because the result is rounded to integer
	 *
	 * @return int Success percentage
	 */
	public function get_percentage() {
		if ( $this->get_status() ) {
			return 100;
		}

		$cnum = count( $this->get_checks() );
		$enum = count( $this->get_errors() );
		$err = (int) ( ( $enum / $cnum ) * 100 );

		return 100 - $err;
	}

	/**
	 * Checks whether we're all good and without issues
	 *
	 * @return bool
	 */
	public function get_status() {
		$errors = $this->get_errors();

		return empty( $errors );
	}

	/**
	 * Gets the list of checks to be performed
	 *
	 * @param string $which Which checks to perform.
	 *
	 * @return array List of check IDs
	 */
	public function get_checks( $which = false ) {
		if ( self::ENDPOINT === $which ) {
			return $this->_endpoint_checks;
		}
		if ( self::POST === $which ) {
			return $this->_post_checks;
		}

		return array_merge( $this->_endpoint_checks, $this->_post_checks );
	}

	/**
	 * Gets filtering prefix
	 *
	 * @return string
	 */
	public function get_filter_prefix() {
		return 'wds-checks';
	}
}
