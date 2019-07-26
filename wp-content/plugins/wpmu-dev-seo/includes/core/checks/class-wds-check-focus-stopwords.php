<?php

class Smartcrawl_Check_Focus_Stopwords extends Smartcrawl_Check_Post_Abstract {

	private $_state;

	public function get_status_msg() {
		return false === $this->_state
			? __( 'There are stop words in focus keywords', 'wds' )
			: __( 'Focus to the point', 'wds' );
	}

	public function apply() {
		$focus = $this->get_raw_focus();
		$state = true;
		foreach ( $focus as $phrase ) {
			if ( ! Smartcrawl_String::has_stopwords( $phrase ) ) {
				continue;
			}
			$state = false;
			break;
		}

		$this->_state = $state;

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		$focus = $this->get_raw_focus();
		$keyphrase = __( 'keywords', 'wds' );
		if ( count( $focus ) > 1 ) {
			$keyphrase = __( 'keywords or key phrases', 'wds' );
		} else {
			$subj = end( $focus );
			$keyphrase = false === strpos( $subj, ' ' )
				? __( 'keywords', 'wds' )
				: __( 'key phrase', 'wds' );
		}
		$message = $this->_state
			? __( 'You kept the focus %s of your article to the point, way to go!', 'wds' )
			: __( 'Your focus %s contains some words that might be considered insignificant in a search query.', 'wds' );

		return sprintf( $message, $keyphrase );
	}

	public function get_more_info() {
		return __( 'Stop words are words which can be considered insignificant in a search query, either because they are way too common, or because they do not convey much information. Such words are often filtered out from a search query. Ideally, you will want such words to not be a part of your article focus.', 'wds' );
	}
}
