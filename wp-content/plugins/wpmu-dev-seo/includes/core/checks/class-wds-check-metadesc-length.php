<?php

class Smartcrawl_Check_Metadesc_Length extends Smartcrawl_Check_Post_Abstract {

	/**
	 * Holds metadesc length
	 *
	 * @var int
	 */
	private $_length;

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;

	public function get_status_msg() {
		if ( ! is_numeric( $this->_state ) ) {
			return __( 'Description has a good length', 'wds' );
		}

		return 0 === $this->_state
			? __( 'There is no description set', 'wds' )
			: ( $this->_state > 0
				? sprintf( __( 'Description is longer than %d characters', 'wds' ), $this->get_max() )
				: sprintf( __( 'Description is shorter than %d characters', 'wds' ), $this->get_min() )
			);
	}

	public function get_max() {
		if ( defined( 'SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT' ) && SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT ) {
			return SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT;
		}

		return 160;
	}

	public function get_min() {
		return 135;
	}

	public function apply() {
		$post = $this->get_subject();
		$subject = false;
		$resolver = false;

		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			$subject = $this->get_markup();
		} else {
			$resolver = Smartcrawl_Endpoint_Resolver::resolve();
			$resolver->simulate_post( $post->ID );

			$subject = Smartcrawl_Meta_Value_Helper::get()->get_description();
		}

		$this->_state = $this->is_within_char_length( $subject, $this->get_min(), $this->get_max() );
		$this->_length = Smartcrawl_String::len( $subject );

		if ( $resolver ) {
			$resolver->stop_simulation();
		}

		return ! is_numeric( $this->_state );
	}

	public function apply_html() {
		$subjects = Smartcrawl_Html::find_attributes( 'meta[name="description"]', 'content', $this->get_markup() );
		if ( empty( $subjects ) ) {
			$this->_length = 0;
			$this->_state = 0;

			return false;
		}

		$subject = reset( $subjects );
		$this->_state = $this->is_within_char_length( $subject, $this->get_min(), $this->get_max() );
		$this->_length = Smartcrawl_String::len( $subject );

		return ! is_numeric( $this->_state );
	}

	public function get_recommendation() {
		$msg = is_numeric( $this->_length )
			? sprintf( __( 'Your current meta description is %d characters in length. ', 'wds' ), $this->_length )
			: '';
		$msg .= sprintf(
			__( 'Best practice suggests this should be around %1$d to %1$d characters.', 'wds' ),
			$this->get_min(), $this->get_max()
		);

		return $msg;
	}

	public function get_more_info() {
		return __( 'There is no \'this number is right\' in this. It depends on what Google adds to your search result and how much they want to show. Google might, for instance, add the date to an article, and that will reduce the number of characters. We wrote about going back to 10 blue links. Bearing that in mind, the rule of thumb is that 135 characters is usually fine. Lately, we have even seen meta descriptions that contain over 250 characters.', 'wds' );
	}
}
