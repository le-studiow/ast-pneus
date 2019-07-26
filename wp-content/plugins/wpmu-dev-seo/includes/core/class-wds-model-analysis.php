<?php
/**
 * This is what's used to calculate SEO and readability analysis score.
 *
 * @package wpmu-dev-seo
 */

/**
 * Analysis model class
 */
class Smartcrawl_Model_Analysis extends Smartcrawl_Model {

	const DATA_ANALYSIS = 'analysis';
	const DATA_READABILITY = 'readability';

	const META_KEY_ANALYSIS = '_wds_analysis';
	const META_KEY_READABILITY = '_wds_readability';

	/**
	 * Keeps track of analyzed post ID
	 *
	 * @var int
	 */
	private $_post_id = false;

	/**
	 * Constructor
	 *
	 * @param int $post_id Optional post ID to analyze.
	 */
	public function __construct( $post_id = false ) {
		$this->set_post_id( $post_id );
	}

	/**
	 * Post ID setter
	 *
	 * @param int $post_id Post ID to set.
	 *
	 * @return bool
	 */
	public function set_post_id( $post_id ) {
		$this->_post_id = (int) $post_id;

		return ! ! $this->_post_id;
	}

	/**
	 * Gets model type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'analysis';
	}

	/**
	 * Clears cached post data
	 *
	 * @return bool
	 */
	public function clear_cached_data() {
		foreach ( $this->get_known_data_types() as $type ) {
			$this->set_post_data( $type, false );
		}

		return true;
	}

	/**
	 * Returns a list of known data types
	 *
	 * @return array List of known data types
	 */
	public function get_known_data_types() {
		return array(
			self::DATA_ANALYSIS,
			self::DATA_READABILITY,
		);
	}

	/**
	 * General data setter
	 *
	 * @param string $data_type Data type to check.
	 * @param array $data Data to set.
	 *
	 * @return bool
	 */
	public function set_post_data( $data_type, $data ) {
		if ( ! in_array( $data_type, $this->get_known_data_types(), true ) ) {
			return false;
		}

		return update_post_meta( $this->_post_id, "_wds_{$data_type}", $data );
	}

	/**
	 * Gets readability level label for the current post
	 *
	 * @param bool $update Force update if there's no data.
	 *
	 * @return string
	 */
	public function get_readability_level( $update = true ) {
		$data = $this->get_post_data( self::DATA_READABILITY );
		if ( empty( $data ) && ! empty( $update ) ) {
			$this->update_readability_data();
			$data = $this->get_post_data( self::DATA_READABILITY );
		}

		if ( empty( $data['raw_score'] ) && ! empty( $data['error'] ) ) {
			return $data['error'];
		}

		$map = $this->get_readability_levels_map();

		$score = $this->normalize_readability_score( $data['score'] );
		$level = '';
		foreach ( $map as $label => $lvl ) {
			if ( ! is_array( $lvl ) || ! isset( $lvl['min'] ) || ! isset( $lvl['max'] ) ) {
				continue;
			}

			$min = $lvl['min'];
			$max = $lvl['max'];

			if ( $score < $min || $score > $max ) {
				continue;
			}

			$level = $label;
			break;
		}

		return $level;
	}

	/**
	 * Data getter
	 *
	 * @param string $data_type Data type to check.
	 *
	 * @return array|false Post data, or (bool)false on failure
	 */
	public function get_post_data( $data_type ) {
		$data = array();
		if ( ! in_array( $data_type, $this->get_known_data_types(), true ) ) {
			return false;
		}

		$data = smartcrawl_get_value( $data_type, $this->_post_id );

		return $data;
	}

	/**
	 * Updates the post readability data
	 *
	 * @return bool Readability status
	 */
	public function update_readability_data() {
		if ( ! Smartcrawl_Settings::get_setting( 'analysis-readability' ) ) {
			return false;
		}

		$this->set_default_remote_handler();
		$request = new Smartcrawl_Core_Request();
		$content = $this->_endpoint_remote_handler->get_rendered_post( $this->_post_id );
		$str = is_wp_error( $content )
			? ''
			: Smartcrawl_Html::plaintext( $content );

		$error = empty( $str )
			? __( 'No content to check', 'wds' )
			: false;
		$result = 0;
		$raw_result = Smartcrawl_String::readability_score( $str );

		if ( false === $raw_result ) {
			$error = ! empty( $error ) ? $error : __( 'Error calculating readability', 'wds' );
		} else {
			$result = $this->normalize_readability_score( $raw_result );
		}

		$is_readable = Smartcrawl_String::get_readability_strategy() === Smartcrawl_String::READABILITY_FLESCH
			? $result < $this->get_readability_threshold()
			: $result > $this->get_readability_threshold();

		$this->set_post_data( self::DATA_READABILITY, array(
			'score'       => $result,
			'raw_score'   => $raw_result,
			'is_readable' => $is_readable,
			'error'       => $error,
		) );

		return ! empty( $result ) && $is_readable;
	}

	/**
	 * Sets default handler, if it hasn't been set already
	 *
	 * @return bool
	 */
	public function set_default_remote_handler() {
		if ( ! empty( $this->_endpoint_remote_handler ) ) {
			return true;
		}

		$this->_endpoint_remote_handler = new Smartcrawl_Core_Request();

		return true;
	}

	/**
	 * Ensures readability sore is within mappable range
	 *
	 * @param float $score Raw score.
	 *
	 * @return float Normalized score, within mappable range
	 */
	public function normalize_readability_score( $score ) {
		$map = $this->get_readability_levels_map();

		$minimum = 999;
		$maximum = 0;
		foreach ( $map as $label => $lvl ) {
			if ( ! is_array( $lvl ) || ! isset( $lvl['min'] ) || ! isset( $lvl['max'] ) ) {
				continue;
			}

			$min = $lvl['min'];
			if ( $min < $minimum ) {
				$minimum = $min;
			}

			$max = $lvl['max'];
			if ( $max > $maximum ) {
				$maximum = $max;
			}
		}
		if ( $score < $minimum ) {
			$score = $minimum;
		}
		if ( $score > $maximum ) {
			$score = $maximum;
		}

		return $score;
	}

	/**
	 * Gets mapped readability levels
	 *
	 * @return array
	 */
	public function get_readability_levels_map() {
		$very_easy = __( 'Very easy to read', 'wds' );
		$easy = __( 'Easy to read', 'wds' );
		$fairly_easy = __( 'Fairly easy to read', 'wds' );
		$plain_english = __( 'Plain English', 'wds' );
		$fairly_difficult = __( 'Fairly difficult to read', 'wds' );
		$difficult = __( 'Difficult to read', 'wds' );
		$confusing = __( 'Very difficult to read', 'wds' );

		$map_flesch = array(
			$very_easy        => array(
				'min' => - 10,
				'max' => 0.9,
			),
			$easy             => array(
				'min' => 1,
				'max' => 1.9,
			),
			$fairly_easy      => array(
				'min' => 2,
				'max' => 2.9,
			),
			$plain_english    => array(
				'min' => 3,
				'max' => 4.9,
			),
			$fairly_difficult => array(
				'min' => 5,
				'max' => 5.9,
			),
			$difficult        => array(
				'min' => 6,
				'max' => 6.9,
			),
			$confusing        => array(
				'min' => 7,
				'max' => 15,
			),
		);

		$map_kincaid = array(
			$very_easy        => array(
				'min' => 90,
				'max' => 100,
			),
			$easy             => array(
				'min' => 80,
				'max' => 89.9,
			),
			$fairly_easy      => array(
				'min' => 70,
				'max' => 79.9,
			),
			$plain_english    => array(
				'min' => 60,
				'max' => 69.9,
			),
			$fairly_difficult => array(
				'min' => 50,
				'max' => 59.9,
			),
			$difficult        => array(
				'min' => 30,
				'max' => 49.9,
			),
			$confusing        => array(
				'min' => 0,
				'max' => 29.9,
			),
		);

		return Smartcrawl_String::READABILITY_FLESCH === Smartcrawl_String::get_readability_strategy()
			? $map_flesch
			: $map_kincaid;
	}

	/**
	 * Gets maximum readability level
	 *
	 * Content over this value is considered complex.
	 *
	 * @return float
	 */
	public function get_readability_threshold() {
		$threshold_flesch = 5.0;
		$threshold_kincaid = 60.0;

		return Smartcrawl_String::READABILITY_FLESCH === Smartcrawl_String::get_readability_strategy()
			? $threshold_flesch
			: $threshold_kincaid;
	}

	/**
	 * Checks readability status of post versus our threshold
	 *
	 * As a side-effect, will update readability meta info if it's
	 * not readily available.
	 *
	 * @return bool Readability status
	 */
	public function is_readable() {
		if ( Smartcrawl_Checks::is_readability_ignored( $this->_post_id ) ) {
			return true;
		}

		$data = $this->get_post_data( self::DATA_READABILITY );
		if ( empty( $data ) || ! is_array( $data ) || empty( $data['is_readable'] ) ) {
			return $this->update_readability_data();
		}

		return ! ! $data['is_readable'];
	}

	/**
	 * Updates post analysis data
	 *
	 * Runs all registered checks against the internal post instance
	 *
	 * @return bool Checks status
	 */
	public function update_analysis_data() {
		if ( ! Smartcrawl_Settings::get_setting( 'analysis-seo' ) ) {
			return false;
		}

		$this->set_default_remote_handler();

		/**
		 * @var $checks Smartcrawl_Checks
		 */
		$checks = Smartcrawl_Checks::apply( $this->_post_id, $this->_endpoint_remote_handler );

		$this->set_post_data( self::DATA_ANALYSIS, array(
			'errors'     => $checks->get_errors(),
			'percentage' => $checks->get_percentage(),
			'checks'     => $checks->get_applied_checks(),
		) );

		return $checks->get_status();
	}

	/**
	 * Sets endpoint remote handler.
	 *
	 * The handler will be used for readability data computation.
	 * Used in tests.
	 *
	 * @param object $request A Smartcrawl_Core_Request instance.
	 */
	public function set_remote_handler( $request ) {
		$this->_endpoint_remote_handler = $request;
	}

	/**
	 * Takes a string key representing readability level and returns readability description.
	 *
	 * @param string $level Readability level key.
	 *
	 * @return string Human readable description.
	 */
	public function get_readability_level_description( $level ) {
		$very_easy = __( 'Very easy to read', 'wds' );
		$easy = __( 'Easy to read', 'wds' );
		$fairly_easy = __( 'Fairly easy to read', 'wds' );
		$plain_english = __( 'Plain English', 'wds' );
		$fairly_difficult = __( 'Fairly difficult to read', 'wds' );
		$difficult = __( 'Difficult to read', 'wds' );
		$confusing = __( 'Very difficult to read', 'wds' );

		$content = sprintf(
			esc_html__( 'Your content is %s.' ),
			'<strong>%s</strong>'
		);

		$map = array(
			$very_easy        => sprintf( $content, strtolower( $very_easy ) ),
			$easy             => sprintf( $content, strtolower( $easy ) ),
			$fairly_easy      => sprintf( $content, strtolower( $fairly_easy ) ),
			$plain_english    => sprintf( $content, esc_html__( 'in plain english' ) ),
			$fairly_difficult => sprintf( $content, strtolower( $fairly_difficult ) ),
			$difficult        => sprintf( $content, strtolower( $difficult ) ),
			$confusing        => sprintf( $content, strtolower( $confusing ) ),
		);

		return isset( $map[ $level ] ) ? $map[ $level ] : '';
	}

	/**
	 * Gets the overall SEO analysis for site posts
	 *
	 * @return array
	 */
	public function get_overall_seo_analysis() {
		$overall_analysis = array();
		$cutoff = 100;
		$checked = 0;
		$passed = 0;
		$post_types = $this->_get_post_types_for_overview();

		foreach ( $post_types as $type ) {

			$checked_in_type = 0;
			$passed_in_type = 0;
			$posts = $this->_get_posts_for_overview( $type, self::META_KEY_ANALYSIS );

			foreach ( $posts as $post ) {
				$this->set_post_id( $post->ID );
				$has_seo_data = $this->has_post_data( self::DATA_ANALYSIS );
				if ( $has_seo_data ) {

					$checked ++;
					$checked_in_type ++;

					$seo_data = $this->get_post_data( self::DATA_ANALYSIS );
					$percentage = intval( smartcrawl_get_array_value( $seo_data, 'percentage' ) );

					if ( $percentage >= $cutoff ) {
						$passed_in_type ++;
						$passed ++;
					}
				}
			}

			$overall_analysis['post-types'][ $type ] = array(
				'total'  => $checked_in_type,
				'passed' => $passed_in_type,
			);
		}

		$overall_analysis['total'] = $checked;
		$overall_analysis['passed'] = $passed;

		return $overall_analysis;
	}

	/**
	 * Gets a list of post types to be used in overview
	 *
	 * @return array
	 */
	private function _get_post_types_for_overview() {
		return get_post_types( array( 'public' => true ) );
	}

	/**
	 * Gets posts sample for the overview calculations
	 *
	 * @param string $type Post type to query.
	 * @param string $meta_key Meta key to be used in the query (overview analysis part).
	 *
	 * @return array
	 */
	private function _get_posts_for_overview( $type, $meta_key ) {
		$posts = get_posts( array(
			'posts_per_page' => 100,
			'post_type'      => $type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future' ),
			'meta_key'       => $meta_key,
		) );

		return $posts;
	}

	/**
	 * Data presence checker
	 *
	 * @param string $data_type Data type to check.
	 *
	 * @return bool
	 */
	public function has_post_data( $data_type ) {
		$data = $this->get_post_data( $data_type );

		return false !== $data;
	}

	/**
	 * Gets the overall readability analysis for site posts
	 *
	 * @return array
	 */
	public function get_overall_readability_analysis() {
		$overall_analysis = array();
		$checked = 0;
		$passed = 0;
		$post_types = $this->_get_post_types_for_overview();

		foreach ( $post_types as $type ) {

			$type_overview = array();
			$posts = $this->_get_posts_for_overview( $type, self::META_KEY_READABILITY );

			foreach ( $posts as $post ) {
				$this->set_post_id( $post->ID );
				$data_available = $this->has_post_data( self::DATA_READABILITY );
				if ( $data_available ) {

					$data = $this->get_post_data( self::DATA_READABILITY );
					$post_score = intval( smartcrawl_get_array_value( $data, 'score' ) );

					$readability_state = $this->get_kincaid_readability_state( $post_score, false );

					$type_overview[ $readability_state ] = intval( smartcrawl_get_array_value( $type_overview, $readability_state ) ) + 1;

					if ( 'warning' === $readability_state || 'success' === $readability_state ) {
						$passed ++;
					}

					if ( 'invalid' !== $readability_state ) {
						$checked ++;
					}
				}
			}

			$overall_analysis['post-types'][ $type ] = $type_overview;
		}

		$overall_analysis['total'] = $checked;
		$overall_analysis['passed'] = $passed;

		return $overall_analysis;
	}

	/**
	 * Takes readability score and returns a string representing readability status.
	 *
	 * @param float $readability_score Score for which range needs to be found.
	 * @param bool $readability_ignored Whether readability is marked as ignored by the user.
	 *
	 * @return string A string representing readability status.
	 */
	public function get_kincaid_readability_state( $readability_score, $readability_ignored ) {
		$readability_state = '';

		if ( Smartcrawl_String::get_readability_strategy() === Smartcrawl_String::READABILITY_KINCAID ) {
			if ( 0 === intval( $readability_score ) || $readability_ignored ) {
				$readability_state = 'invalid';
			} elseif ( $readability_score < 60 ) {
				$readability_state = 'error';
			} elseif ( $readability_score < 70 ) {
				$readability_state = 'warning';
			} elseif ( $readability_score <= 100 ) {
				$readability_state = 'success';
			}
		}

		return $readability_state;
	}
}
