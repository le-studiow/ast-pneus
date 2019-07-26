<?php

abstract class Smartcrawl_Model {

	/**
	 * Gets the filter/action name based on supplied suffix
	 *
	 * @param string $suffix Action suffix
	 *
	 * @return string
	 */
	public function get_filter( $suffix ) {
		return 'wds-model-' . $this->get_type() . '-' . $suffix;
	}

	/**
	 * Returns model type.
	 *
	 * Used for filtering and other places where model type distinction is needed.
	 *
	 * @return string
	 */
	abstract public function get_type();

}
