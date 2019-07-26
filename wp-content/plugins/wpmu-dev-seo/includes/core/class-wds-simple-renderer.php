<?php

class Smartcrawl_Simple_Renderer extends Smartcrawl_Renderable {
	/**
	 * @var Smartcrawl_Simple_Renderer
	 */
	private static $_instance;

	public static function get_instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function render( $view, $args = array() ) {
		$instance = self::get_instance();
		$instance->_render( $view, $args );
	}

	public static function load( $view, $args = array() ) {
		$instance = self::get_instance();

		return $instance->_load( $view, $args );
	}

	protected function _get_view_defaults() {
		return array();
	}
}
