<?php
/**
 * Main plugin entry point
 *
 * @package shipper
 */

/**
 * Plugin Name: Shipper
 * Plugin URI: http://premium.wpmudev.org/project/shipper/
 * Description: WPMU DEV Shipper plugin
 * Version: 1.0.3
 * Network: true
 * Text Domain: shipper
 * Author: WPMU DEV
 * Author URI: http://premium.wpmudev.org
 * WDP ID: 2175128
 */

/*
* Copyright 2010-2011 Incsub (http://incsub.com/)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'SHIPPER_VERSION', '1.0.3' );
define( 'SHIPPER_PLUGIN_FILE', __FILE__ );

if ( ! defined( 'SHIPPER_IS_TEST_ENV' ) ) {
	define( 'SHIPPER_IS_TEST_ENV', false );
}

require_once( dirname( __FILE__ ) . '/lib/functions.php' );
require_once( dirname( __FILE__ ) . '/lib/loader.php' );

register_activation_hook(
	__FILE__,
	array( 'Shipper_Controller_Setup_Activate', 'activate' )
);
register_deactivation_hook(
	__FILE__,
	array( 'Shipper_Controller_Setup_Deactivate', 'deactivate' )
);
register_uninstall_hook(
	__FILE__,
	array( 'Shipper_Controller_Setup_Uninstall', 'uninstall' )
);

Shipper_Main::get()->boot();

