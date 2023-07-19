<?php
/**
 * Plugin Name:	IP Geolocation
 * Description:	This plugin is showing IP Geolocation using api service
 * Version: 	2.0
 * Author: 		Rasool Vahdati
 * Author URI: 	https://tidaweb.com
 * License:		GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ipgeo
 */


define( 'IP_GEOLOCATION_VERSION', '1.4' );
define( 'IP_GEOLOCATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( IP_GEOLOCATION_PLUGIN_DIR . 'ipgeo.class.php' );
require_once( IP_GEOLOCATION_PLUGIN_DIR . 'includes/settings.class.php' );

register_activation_hook( __FILE__, array( 'IP_Geo_Location', 'ipgeo_activate' ) );
register_deactivation_hook( __FILE__, array( 'IP_Geo_Location', 'ipgeo_deactivate' ) );