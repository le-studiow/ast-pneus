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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not allowed' );
}

function LRMTarteaucitron()
{
    wp_enqueue_script( 'lrmt-tarteaucitron', plugins_dir_url() . 'tarteaucitron/js/tarteaucitron.js');
}
add_action('wp_enqueue_scripts', 'LRMTarteaucitron');
