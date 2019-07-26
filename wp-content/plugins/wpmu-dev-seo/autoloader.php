<?php

if ( ! class_exists( 'Smartcrawl_Autoloader' ) ) {
	class Smartcrawl_Autoloader {
		private static $class_mappings = array();

		private static function load_from_file() {
			$mappings_file = dirname( __FILE__ ) . '/class-mappings.php';
			if ( file_exists( $mappings_file ) ) {
				return include $mappings_file;
			}

			return array();
		}

		private static function get_class_mappings() {
			if ( empty( self::$class_mappings ) ) {
				self::$class_mappings = self::load_from_file();
			}

			return self::$class_mappings;
		}

		public static function autoload( $class ) {
			$class_mappings = self::get_class_mappings();
			if ( ! isset( $class_mappings[ $class ] ) ) {
				return;
			}

			$class_path = untrailingslashit( SMARTCRAWL_PLUGIN_DIR ) . $class_mappings[ $class ];
			if ( ! file_exists( $class_path ) ) {
				return;
			}

			include $class_path;
		}
	}

	spl_autoload_register( array( 'Smartcrawl_Autoloader', 'autoload' ) );
}
