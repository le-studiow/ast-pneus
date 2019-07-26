<?php
/*
Plugin Name: Branda
Plugin URI: https://premium.wpmudev.org/project/ultimate-branding/
Description: A complete white-label and branding solution for multisite. Login images, favicons, remove WordPress links and branding, and much more.
Author: WPMU DEV
Version: 3.1.2
Author URI: http://premium.wpmudev.org/
Requires PHP: 5.6
Text_domain: ub
WDP ID: 9135

Copyright 2009-2019 Incsub (http://incsub.com)

Lead Developer - Marcin Pietrzak (Incsub)

Contributors - Sam Najian (Incsub), Ve Bailovity (Incsub), Barry (Incsub), Andrew Billits, Ulrich Sossou, Marko Miljus, Joseph Fusco (Incsub), Calum Brash (Incsub), Joel James ( Incsub)

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License (Version 2 - GPLv2) as published
by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc., 51 Franklin
St, Fifth Floor, Boston, MA 02110-1301 USA

 */

/**
 * Branda Version
 */
$ub_version = null;

// Include the configuration library
require_once( dirname( __FILE__ ) . '/etc/config.php' );
// Include the functions library
require_once( 'inc/functions.php' );
require_once( 'inc/class-branda-helper.php' );

// Set up my location
set_ultimate_branding( __FILE__ );

/**
 * set ub Version
 */
function ub_set_ub_version() {
	global $ub_version;
	$data = get_plugin_data( __FILE__ );
	$ub_version = $data['Version'];
}

if ( ! defined( 'BRANDA_SUI_VERSION' ) ) {
	define( 'BRANDA_SUI_VERSION', '2.3.29' );
}

include_once( 'external/dash-notice/wpmudev-dash-notification.php' );

register_activation_hook( __FILE__, 'ub_register_activation_hook' );
register_deactivation_hook( __FILE__, 'ub_register_deactivation_hook' );
register_uninstall_hook( __FILE__, 'ub_register_uninstall_hook' );

