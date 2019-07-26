<?php
/**
 * Uninstall file
 *
 * @package wpmu-dev-seo
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

define( 'SMARTCRAWL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once dirname( __FILE__ ) . '/config.php';
require_once dirname( __FILE__ ) . '/autoloader.php';
require_once SMARTCRAWL_PLUGIN_DIR . 'init.php';

Smartcrawl_Reset::reset();
