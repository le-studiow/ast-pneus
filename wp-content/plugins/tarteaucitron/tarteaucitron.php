<?php
/**
 * Plugin Name:     Tarteaucitron
 * Plugin URI:      https://loren.zone
 * Description:     Plugin qui permet d'ajouter tarteaucitron sur un site wordpress
 * Author:          Lorenzo Milesi <lorenzo.milesi@live.fr>
 * Author URI:      https://loren.zone
 * Text Domain:     tarteaucitron
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tarteaucitron
 */

if ( ! class_exists( 'LRMTarteauCitron' ) ) {

    class LRMTarteauCitron  {

        private $plugin_path;

        public function __construct()
        {
			$this->plugin_path = plugin_dir_path( __FILE__ );
            add_action('wp_footer', [ $this, 'loadScripts' ]);
        }

        private function loadScripts(): void
        {
            wp_enqueue_script( 'lrmt-tarteaucitron', $this->plugin_path . '/js/tarteaucitron.js');
        }

    }

}

$LRMTarteauCitron = new LRMTarteauCitron();
