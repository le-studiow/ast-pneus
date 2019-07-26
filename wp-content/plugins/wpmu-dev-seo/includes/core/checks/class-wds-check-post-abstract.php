<?php

/**
 * Post-specific check abstraction class
 */
abstract class Smartcrawl_Check_Post_Abstract extends Smartcrawl_Check_Abstract {

	/**
	 * Gets post content markup
	 *
	 * @return string Decorated markup
	 */
	public function get_markup() {
		$subject = parent::get_markup();

		if ( is_object( $subject ) ) {
			return Smartcrawl_Html::decorate( $subject->post_content );
		}

		return $subject;
	}

	/**
	 * Gets subject directly
	 */
	public function get_subject() {
		$subject = parent::get_markup();

		return $subject;
	}
}
