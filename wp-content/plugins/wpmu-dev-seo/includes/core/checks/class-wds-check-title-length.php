<?php

class Smartcrawl_Check_Title_Length extends Smartcrawl_Check_Post_Abstract {

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;
	private $_length = null;

	public function get_status_msg() {
		if ( ! is_numeric( $this->_state ) ) {
			return __( 'Title has a good length', 'wds' );
		}

		return 0 === $this->_state
			? __( 'There is no title set', 'wds' )
			: ( $this->_state > 0
				? sprintf( __( 'Title is longer than %d characters', 'wds' ), $this->get_max() )
				: sprintf( __( 'Title is shorter than %d characters', 'wds' ), $this->get_min() )
			);
	}

	public function get_max() {
		return 70;
	}

	public function get_min() {
		return 50;
	}

	public function apply() {
		$post = $this->get_subject();
		$subject = false;
		$resolver = false;

		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			$subject = $this->get_markup();
		} elseif ( wp_is_post_revision( $post->ID ) && ! empty( $post->post_title ) ) {
			$parent_post_id = wp_is_post_revision( $post->ID );
			$resolver = Smartcrawl_Endpoint_Resolver::resolve();
			$resolver->simulate_post( $parent_post_id );
			$parent_title = get_the_title( $parent_post_id );
			$parent_subject = Smartcrawl_Meta_Value_Helper::get()->get_title( $post->post_title );
			$subject = preg_replace( '/' . preg_quote( $parent_title, '/' ) . '/', $post->post_title, $parent_subject );
		} else {
			$resolver = Smartcrawl_Endpoint_Resolver::resolve();
			$resolver->simulate_post( $post->ID );

			$subject = Smartcrawl_Meta_Value_Helper::get()->get_title();
		}

		if ( $resolver ) {
			$resolver->stop_simulation();
		}
		$this->_state = $this->is_within_char_length( $subject, $this->get_min(), $this->get_max() );
		$this->_length = Smartcrawl_String::len( $subject );

		return ! is_numeric( $this->_state );
	}

	public function apply_html() {
		$titles = Smartcrawl_Html::find_content( 'title', $this->get_markup() );
		if ( empty( $titles ) ) {
			$this->_state = 0;

			return false;
		}

		$title = reset( $titles );
		$this->_state = $this->is_within_char_length( $title, $this->get_min(), $this->get_max() );
		$this->_length = Smartcrawl_String::len( $title );

		return ! is_numeric( $this->_state );
	}

	public function get_recommendation() {
		$message = '';
		if ( ! is_numeric( $this->_length ) ) {
			return $message;
		}

		$title_length = $this->_length;

		if (
			$title_length >= $this->get_min()
			&& $title_length <= $this->get_max()
		) {
			$message = __( 'Your SEO title is %1$d characters which is between the recommended best practice of %2$d-%3$d characters.', 'wds' );
		} elseif ( $title_length > $this->get_max() ) {
			$message = __( 'Your SEO title is %1$d characters which is greater than the recommended %3$d characters. Best practice is between %2$d and %3$d characters with 60 being the sweetspot.', 'wds' );
		} elseif ( $title_length < $this->get_min() ) {
			$message = __( 'Your SEO title is %1$d characters which is less than the recommended %2$d characters. Best practice is between %2$d and %3$d characters with 60 being the sweetspot.', 'wds' );
		} elseif ( 0 === intval( $title_length ) ) {
			$message = __( 'You have NOT written an SEO specific title for this article. We recommend an SEO specific title between %2$d and %3$d characters, optimized with your focus keywords.', 'wds' );
		}

		return sprintf(
			$message,
			$title_length,
			$this->get_min(),
			$this->get_max()
		);
	}

	public function get_more_info() {
		$message = '';
		if ( ! is_numeric( $this->_length ) ) {
			return $message;
		}

		$title_length = $this->_length;

		return sprintf(
			__( 'Your SEO title is the most important element because it is what users will see in search engine results. You\'ll want to make sure that you have your focus keywords in there, that it\'s a nice length, and that people will want to click on it. Best practices suggest keeping your titles between %2$d and %3$d characters including spaces, though in some cases 60 is the sweetspot. The length is important both for SEO ranking but also how your title will show up in search engines - long titles will be cut off visually and look bad. Unfortunately there isn\'t a rule book for SEO titles, just remember to make your title great for SEO but also (most importantly) readable and enticing for potential visitors to click on.', 'wds' ),
			$title_length,
			$this->get_min(),
			$this->get_max()
		);
	}
}
