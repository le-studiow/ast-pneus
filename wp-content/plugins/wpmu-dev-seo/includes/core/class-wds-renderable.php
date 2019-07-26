<?php

abstract class Smartcrawl_Renderable {

	/**
	 * Renders the view by calling `_load`
	 *
	 * @param string $view View file to load
	 * @param array $args Optional array of arguments to pass to view
	 *
	 * @return bool
	 */
	protected function _render( $view, $args = array() ) {
		$view = $this->_load( $view, $args );
		if ( ! empty( $view ) ) {
			echo $view;  // phpcs:ignore -- The template being rendered could contain anything
		}

		return ! empty( $view );
	}

	/**
	 * Loads the view file and returns the output as string
	 *
	 * @param string $view View file to load
	 * @param array $args Optional array of arguments to pass to view
	 *
	 * @return mixed (string)View output on success, (bool)false on failure
	 */
	protected function _load( $view, $args = array() ) {
		$view = preg_replace( '/[^\-_a-z0-9\/]/i', '', $view );
		if ( empty( $view ) ) {
			return false;
		}

		$_path = wp_normalize_path( SMARTCRAWL_PLUGIN_DIR . 'admin/templates/' . $view . '.php' );
		if ( ! file_exists( $_path ) || ! is_readable( $_path ) ) {
			return false;
		}

		if ( empty( $args ) || ! is_array( $args ) ) {
			$args = array();
		}
		$args = wp_parse_args( $args, $this->_get_view_defaults() );

		if ( ! empty( $args ) ) {
			extract( $args );  // phpcs:ignore -- extract method is necessary here for making variables available to template
		}

		ob_start();
		include $_path;

		return ob_get_clean();
	}

	abstract protected function _get_view_defaults();
}
