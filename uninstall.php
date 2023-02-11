<?php
/*
*
* Uninstall IP Geolocation Plugin
*
*/

if(!defined('WP_UNINSTALL_PLUGIN'))
	exit();

// delete general options
delete_option('ipgeo_input_class');
delete_option('ipgeo_button_class');

// delete api options
delete_option('ipgeo_api_service');
delete_option('ipgeo_api_token');

// delete map options
delete_option('ipgeo_enable_map');
delete_option('ipgeo_map_service');
delete_option('ipgeo_map_api_token');

?>