<?php

class Smartcrawl_Check_Focus extends Smartcrawl_Check_Post_Abstract {

	/**
	 * Holds check state
	 *
	 * @var int
	 */
	private $_state;

	public function get_status_msg() {
		return false === $this->_state
			? __( 'There are no focus keywords', 'wds' )
			: __( 'There are some focus keywords', 'wds' );
	}

	public function apply() {
		$focus = $this->get_focus();
		$this->_state = ! empty( $focus );

		return ! ! $this->_state;
	}

	public function get_recommendation() {
		return false === $this->_state
			? __( 'In order to give your content the best possible chance to be discovered, it is best to select some focus keywords or key phrases, to give it some context.', 'wds' )
			: __( 'Nice work, now that we know what your article is about we can be more specific in analysis.', 'wds' );
	}

	public function get_more_info() {
		return __( 'Selecting focus keywords helps describe what your content is about.', 'wds' );
	}
}
