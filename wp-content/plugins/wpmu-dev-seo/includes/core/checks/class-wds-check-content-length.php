<?php

class Smartcrawl_Check_Content_Length extends Smartcrawl_Check_Abstract {

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;

	private $_wordcount = null;

	public function get_status_msg() {
		if ( - 1 === $this->_state ) {
			return __( 'There is no content', 'wds' );
		}

		return false === $this->_state
			? sprintf( __( 'Content is shorter than %d words', 'wds' ), $this->get_min() )
			: sprintf( __( 'Content is greater than %d words', 'wds' ), $this->get_min() );
	}

	public function get_min() {
		return 300;
	}

	public function apply() {
		$markup = $this->get_markup();
		if ( empty( $markup ) ) {
			$this->_state = - 1;

			return false;
		}

		$words = Smartcrawl_String::words( Smartcrawl_Html::plaintext( $markup ) );

		$count = count( $words );
		$this->_wordcount = $count;

		$this->_state = $count > $this->get_min();

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		$word_count = $this->_wordcount ? $this->_wordcount : 0;

		if ( 0 === $word_count ) {
			$message = __( 'Unless your website is a photography blog it\'s generally a good idea to include content for your visitors to read, and also Google to help with indexing. Something, anything, is better than nothing.', 'wds' );
		} elseif ( $this->_state ) {
			$message = __( 'Your content is longer than the recommend minimum of %1$d words, excellent!', 'wds' );
		} else {
			$message = __( 'The best practice minimum content length for the web is %1$d words so we recommend you aim for at least this amount - the more the merrier.', 'wds' );
		}

		return sprintf(
			$message,
			$this->get_min(),
			$word_count
		);
	}

	public function get_more_info() {
		$message = __( 'Content is ultimately the bread and butter of your SEO. Without words, your pages and posts will have a hard time ranking for the keywords you want them to. As a base for any article best practice suggests a minimum of %1$d words, with 1000 being a good benchmark and 1600 being the optimal. Numerous studies have uncovered that longer content tends to perform better than shorter content, with pages having 1000 words or more performing best. Whilst optimizing your content for search engines is what we\'re going for here, a proven bi-product is that high quality long form articles also tend to get shared more on social platforms. With the increasing power of social media as a tool for traffic it\'s a nice flow on effect of writing those juicy high quality articles your readers are waiting for.', 'wds' );

		return sprintf(
			$message,
			$this->get_min()
		);
	}
}
