<?php

class Smartcrawl_Check_Subheadings_Keywords extends Smartcrawl_Check_Abstract {

	private $_state;

	public function get_status_msg() {
		return false === $this->_state
			? __( 'Subheadings have no keywords', 'wds' )
			: __( 'Subheadings contain keywords', 'wds' );
	}

	public function apply() {
		$subjects = Smartcrawl_Html::find_content( 'h1,h2,h3,h4,h5,h6', $this->get_markup() );
		if ( empty( $subjects ) ) {
			return true;
		} // No subheadings, nothing to check.

		$status = false;
		foreach ( $subjects as $subject ) {
			$status = $this->has_focus( $subject );
			if ( $status ) {
				break;
			}
		}

		$this->_state = $status;

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		if ( $this->_state ) {
			$message = __( "You've used keywords in your subheadings which will help both the user and search engines quickly figure out what your article is about, good work!", 'wds' );
		} else {
			$message = __( "Using keywords in any of your subheadings (such as H2's or H3's) will help both the user and search engines quickly figure out what your article is about. It's best practice to include your focus keywords in at least one subheading if you can.", 'wds' );
		}

		return $message;
	}

	public function get_more_info() {
		return __( "When trying to rank for certain keywords, those keywords should be found in as many key places as possible. Given that you're writing about the topic it only makes sense that you mention it in at least one of your subheadings. Headings are important to users as they break up your content, sectioning is by subtopics to help readers figure out what the text is about - and the same goes for search engines. With that said, don't force keywords into all your titles - keep it natural, readable and use moderation!", 'wds' );
	}
}
