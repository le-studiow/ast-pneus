<?php

class Smartcrawl_Check_Title_Keywords extends Smartcrawl_Check_Post_Abstract {

	private $_state;

	public function get_status_msg() {
		if ( - 1 === $this->_state ) {
			return __( 'We couldn\'t find a title to check for keywords', 'wds' );
		}

		return false === $this->_state
			? __( 'Title has no keywords', 'wds' )
			: __( 'Title contains keywords', 'wds' );
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

			$subject = Smartcrawl_Meta_Value_Helper::get()->get_title();
		}

		if ( $resolver ) {
			$resolver->stop_simulation();
		}

		$this->_state = $this->has_focus( $subject );

		return ! ! $this->_state;
	}

	public function apply_html() {
		$titles = Smartcrawl_Html::find_content( 'title', $this->get_markup() );
		if ( empty( $titles ) ) {
			$this->_state = - 1;

			return false;
		}

		$title = reset( $titles );
		if ( empty( $title ) ) {
			$this->_state = - 1;

			return false;
		}

		$this->_state = $this->has_focus( $title );

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		if ( $this->_state ) {
			$message = __( "You've managed to get your focus keywords in your SEO title which has the best chance of matching what users are searching for first up - nice work.", 'wds' );
		} else {
			$message = __( "The focus keyword for this article doesn't appear in the SEO title which means it has less of a chance of matching what your visitors will search for.", 'wds' );
		}

		return $message;
	}

	public function get_more_info() {
		return __( "It's considered good practice to try to include your focus keyword(s) in the SEO title of a page because this is what people looking for the article are likely searching for. The higher chance of a keyword match, the greater the chance that your article will be found higher up in search results. Whilst it's recommended to try and get these words in, don't sacrifice readability and the quality of the SEO title just to rank higher - people may not want to click on it if it doesn't read well.", 'wds' );
	}
}
