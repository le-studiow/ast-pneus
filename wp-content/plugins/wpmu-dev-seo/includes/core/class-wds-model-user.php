<?php

class Smartcrawl_Model_User extends Smartcrawl_Model {

	/**
	 * Holds the user ID reference used in the constructor
	 *
	 * @var int
	 */
	private $_user_id;

	/**
	 * Holds the user object cache
	 *
	 * @var WP_User object
	 */
	private $_user;

	public function __construct( $user_id = false ) {
		if ( ! empty( $user_id ) && is_numeric( $user_id ) ) {
			$this->_user_id = (int) $user_id;
			$this->_user = new WP_User( $user_id );
		} elseif ( ! empty( $user_id ) ) {
			$user = new WP_User( $user_id );
			if ( ! empty( $user->ID ) && is_numeric( $user->ID ) ) {
				$this->_user_id = $user->ID;
				$this->_user = $user;
			}
		} else {
			$this->_user_id = false;
			$this->_user = new WP_User();
		}
	}

	/**
	 * Current user convenience factory method
	 *
	 * @return Smartcrawl_Model_User Current user instance
	 */
	public static function current() {
		return self::get( get_current_user_id() );
	}

	/**
	 * Particular user convenience factory method
	 *
	 * @param int|string $user_id User ID, or login|email
	 *
	 * @return Smartcrawl_Model_User Particular user instance
	 */
	public static function get( $user_id = false ) {
		return new self( $user_id );
	}

	/**
	 * Fetches the site owner user (one of)
	 *
	 * @return Smartcrawl_Model_User Owner user reference
	 */
	public static function owner() {
		$admins = get_users( array(
			'role'   => 'administrator',
			'fields' => 'ID',
		) );

		return self::get( reset( $admins ) );
	}

	/**
	 * Returns user first name
	 *
	 * @return string First name, or display name
	 */
	public function get_first_name() {
		$name = $this->_user->user_firstname;
		$name = ! empty( $name )
			? $name
			: $this->get_display_name();

		return apply_filters(
			$this->get_filter( 'first_name' ),
			$name,
			$this->_user_id
		);
	}

	/**
	 * Returns user display name
	 *
	 * @return string Display name, or fallback
	 */
	public function get_display_name() {
		$name = $this->_user->display_name;
		$name = ! empty( $name )
			? $name
			: $this->get_fallback_name();

		return apply_filters(
			$this->get_filter( 'display_name' ),
			$name,
			$this->_user_id
		);
	}

	/**
	 * Returns the fallback name, for when other methods fail
	 *
	 * @return string
	 */
	public function get_fallback_name() {
		$name = $this->_user->user_nicename;
		$name = ! empty( $name )
			? $name
			: __( 'Anonymous', 'wds' );

		return apply_filters(
			$this->get_filter( 'fallback_name' ),
			$name,
			$this->_user_id
		);
	}

	/**
	 * Gets user full name
	 *
	 * Falls back to display name
	 *
	 * @return string Full name
	 */
	public function get_full_name() {
		$name = '';

		// Try full first
		$first = get_user_meta( $this->get_id(), 'first_name', true );
		$last = get_user_meta( $this->get_id(), 'last_name', true );
		if ( ! empty( $first ) && ! empty( $last ) ) {
			$name = "{$first} {$last}";
		}

		// Fall back to display name
		if ( empty( $name ) ) {
			$name = $this->_user->display_name;
		}

		return apply_filters(
			$this->get_filter( 'full_name' ),
			$name,
			$first, $last
		);
	}

	/**
	 * Returns the user ID
	 *
	 * @return int
	 */
	public function get_id() {
		return (int) $this->_user_id;
	}

	/**
	 * Gets user URL
	 *
	 * Falls back to posts URL
	 *
	 * @return string User URL
	 */
	public function get_user_url() {
		$url = get_user_meta( $this->get_id(), 'url', true );
		if ( empty( $url ) ) {
			$url = get_author_posts_url( $this->get_id() );
		}

		return apply_filters( $this->get_filter( 'user_url' ), $url, $this->get_id() );
	}

	public function get_user_urls() {
		$urls = array();

		// TODO: fetch user URLs
		return $urls;
	}

	public function get_type() {
		return 'user';
	}

	public function get_email() {
		return $this->_user->user_email;
	}
}
