<?php

/**
 * Abstract implementation class
 */
abstract class Smartcrawl_Checkup_Service_Implementation extends Smartcrawl_Service {

	/**
	 * Gets crawl starting verb
	 *
	 * @return string
	 */
	abstract public function get_start_verb();

	/**
	 * Gets results fetching verb
	 *
	 * @return string
	 */
	abstract public function get_result_verb();
}
