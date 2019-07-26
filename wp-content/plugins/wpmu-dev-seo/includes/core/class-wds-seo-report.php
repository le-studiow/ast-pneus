<?php

class Smartcrawl_SeoReport {

	private $_in_progress = false;
	private $_progress = 0;
	private $_items = array();
	private $_by_type = array();

	private $_state_messages = array();
	private $_meta = array();

	private $_ignores;

	private $_sitemap_issues = 0;

	public function __construct() {
	}

	/**
	 * Builds report instance
	 *
	 * @param array $raw Raw crawl report, as returned by service
	 *
	 * @return Smartcrawl_SeoReport instance
	 */
	public function build( $raw ) {
		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		$issues = ! empty( $raw['issues'] )
			? $raw['issues']
			: array();
		if ( isset( $issues['issues'] ) && is_array( $issues['issues'] ) ) {
			$issues = $issues['issues'];
		}

		$this->build_meta( $raw );
		$this->build_issues( $issues );

		return $this;
	}

	/**
	 * Builds report meta list
	 *
	 * @param array $raw Raw crawl report, as returned by service
	 *
	 * @return object Smartcrawl_SeoReport instance
	 */
	public function build_meta( $raw ) {
		$sitemap_total = ! empty( $raw['sitemap_total'] )
			? $raw['sitemap_total']
			: ( ! empty( $raw['issues']['sitemap_total'] ) ? $raw['issues']['sitemap_total'] : 0 );
		$discovered = ! empty( $raw['discovered'] )
			? $raw['discovered']
			: ( ! empty( $raw['issues']['discovered'] ) ? $raw['issues']['discovered'] : 0 );

		if ( ! empty( $raw['issues']['messages'] ) ) {
			foreach ( $raw['issues']['messages'] as $msg ) {
				$this->_state_messages[] = $msg;
			}
		}

		$total = isset( $raw['total'] )
			? (int) $raw['total']
			: 0;

		$this->_meta = array(
			'sitemap_total' => $sitemap_total,
			'discovered'    => $discovered,
			'total'         => $total,
		);
	}

	/**
	 * Builds report instance issues
	 *
	 * @param array $raw Raw issues list, as returned by service
	 *
	 * @return object Smartcrawl_SeoReport instance
	 */
	public function build_issues( $raw ) {
		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		foreach ( $raw as $type => $items ) {
			if ( ! is_array( $items ) || empty( $items ) ) {
				continue;
			}
			if ( ! in_array( $type, array_keys( $this->_by_type ), true ) ) {
				$this->_by_type[ $type ] = array();
			}
			foreach ( $items as $item ) {
				$key = $this->get_item_key( $item, $type );
				if ( empty( $key ) ) {
					continue; // Invalid key
				}
				$item['type'] = $type;

				$this->_items[ $key ] = $item;
				$this->_by_type[ $type ][] = $key;
			}
		}

		// Special case sitemap issues reporting
		if ( ! empty( $raw['sitemap'] ) && is_numeric( $raw['sitemap'] ) ) {
			$this->_sitemap_issues = (int) $raw['sitemap'];
		}

		if ( empty( $this->_state_messages ) && ! empty( $raw['messages'] ) ) {
			foreach ( $raw['messages'] as $msg ) {
				$this->_state_messages[] = $msg;
			}
		}

		$this->_ignores = new Smartcrawl_Model_Ignores();

		return $this;
	}

	/**
	 * Creates an unique key for a corresponding item
	 *
	 * @param array $item Item to create the key for
	 * @param string $type Optional item type
	 *
	 * @return string Unique key
	 */
	public function get_item_key( $item, $type = false ) {
		if ( ! is_array( $item ) ) {
			return false;
		}
		if ( empty( $item['path'] ) ) {
			return false;
		}

		if ( empty( $type ) ) {
			$type = 'generic';
		}

		return md5( "{$type}-{$item['path']}" );
	}

	/**
	 * Returns known issue types
	 *
	 * @return array List of known issue types identifiers
	 */
	public function get_issue_types() {
		return array_keys( $this->_by_type );
	}

	/**
	 * Gets a list of ignored items
	 *
	 * @return array List of ignored items unique IDs
	 */
	public function get_ignored_issues() {
		return $this->_ignores->get_all();
	}

	/**
	 * Boolean accessor to check issues existence by type
	 *
	 * @param string $type Optional issue type
	 *                     - if omitted, all issues are considered
	 *
	 * @return bool
	 */
	public function has_issues( $type = false ) {
		return $this->get_issues_count( $type ) > 0;
	}

	/**
	 * Gets issues count, for all issues or by type
	 *
	 * @param string $type Optional issue type
	 *                     - if omitted, all issues are counted
	 * @param bool $include_ignored Whether to include ignored items (default: no)
	 *
	 * @return int Issues count
	 */
	public function get_issues_count( $type = false, $include_ignored = false ) {
		$issues = empty( $type )
			? $this->get_all_issues( $include_ignored )
			: $this->get_issues_by_type( $type, $include_ignored );

		return (int) count( $issues );
	}

	/**
	 * Gets unique IDs of all issues
	 *
	 * @param bool $include_ignored Whether to include ignored items (default: no)
	 *
	 * @return array List of all known issues
	 */
	public function get_all_issues( $include_ignored = false ) {
		$all = array_keys( $this->_items );
		if ( ! empty( $include_ignored ) ) {
			return $all;
		}

		$result = array();
		foreach ( $all as $issue ) {
			if ( ! $this->is_ignored_issue( $issue ) ) {
				$result[] = $issue;
			}
		}

		return $result;
	}

	/**
	 * Checks if an issue is to be ignored
	 *
	 * @return bool
	 */
	public function is_ignored_issue( $key ) {
		return (bool) $this->_ignores->is_ignored( $key );
	}

	/**
	 * Gets issues for a specific issue type
	 *
	 * @param string $type Type identifier
	 * @param bool $include_ignored Whether to include ignored items (default: no)
	 *
	 * @return array List of issues for this type
	 */
	public function get_issues_by_type( $type, $include_ignored = false ) {
		$issues = ! empty( $this->_by_type[ $type ] ) && is_array( $this->_by_type[ $type ] )
			? $this->_by_type[ $type ]
			: array();

		if ( ! empty( $include_ignored ) ) {
			return $issues;
		}

		$result = array();
		foreach ( $issues as $issue ) {
			if ( ! $this->is_ignored_issue( $issue ) ) {
				$result[] = $issue;
			}
		}

		return array_unique( $result );
	}

	/**
	 * Checks if we have any ignored issues going on
	 *
	 * @return bool
	 */
	public function has_ignored_issues() {
		return $this->get_ignored_issues_count() > 0;
	}

	/**
	 * Gets ignored issues count, all or by type
	 *
	 * @param string $type Optional issue type to count ignores for
	 *                     - if omitted, all ignores are counted
	 *
	 * @return int Ignored issues count
	 */
	public function get_ignored_issues_count( $type = false ) {
		$issues = empty( $type )
			? $this->get_all_issues( true )
			: $this->get_issues_by_type( $type, true );
		$count = 0;

		foreach ( $issues as $key ) {
			if ( $this->is_ignored_issue( $key ) ) {
				$count ++;
			}
		}

		return (int) $count;
	}

	/**
	 * Gets count of URLs not in sitemaps
	 *
	 * @return int Count
	 */
	public function get_sitemap_misses() {
		$count = (int) $this->_sitemap_issues;

		return 0 === $count
			? (int) $this->get_issues_count( 'sitemap' )
			: $count;
	}

	/**
	 * Gets a meta key value
	 *
	 * @param string $key Meta key to check
	 * @param mixed $fallback What to return instead if there's no such key
	 *
	 * @return mixed Meta value
	 */
	public function get_meta( $key, $fallback = false ) {
		if ( $this->has_meta( $key ) ) {
			return $this->_meta[ $key ];
		}

		return $fallback;
	}

	/**
	 * Check whether a meta key has been set
	 *
	 * @param string $key Meta key to check
	 *
	 * @return bool
	 */
	public function has_meta( $key ) {
		return isset( $this->_meta[ $key ] );
	}

	/**
	 * Gets a specific issue by its key
	 *
	 * @param string $key Issue's unique key
	 *
	 * @return array Issue info hash
	 */
	public function get_issue( $key ) {
		return ! empty( $this->_items[ $key ] ) && is_array( $this->_items[ $key ] )
			? $this->_items[ $key ]
			: array();
	}

	/**
	 * Gets all known state messages
	 *
	 * @return array
	 */
	public function get_state_messages() {
		return ! empty( $this->_state_messages ) && is_array( $this->_state_messages )
			? $this->_state_messages
			: array();
	}

	/**
	 * Checks whether we have any state messages
	 *
	 * @return bool
	 */
	public function has_state_messages() {
		return ! empty( $this->_state_messages );
	}

	private function __clone() {
	}

	public function is_in_progress() {
		return $this->_in_progress;
	}

	public function set_in_progress( $in_progress ) {
		$this->_in_progress = $in_progress;
	}

	public function get_progress() {
		return $this->_progress;
	}

	public function set_progress( $progress ) {
		$this->_progress = $progress;
	}

	public function has_data() {
		// Check if the meta has been set already or we have some error messages to show
		return (boolean) (
			array_filter( $this->_meta )
			|| array_filter( $this->_state_messages )
		);
	}

}
