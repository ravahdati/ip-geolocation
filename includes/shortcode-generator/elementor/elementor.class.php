<?php
/**
 * IP Geolocation element
 *
 * Create a IP Geolocation in Elementor
 *
 * @category   Wordpress
 * @since      3.10.1 (Elementor Version)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

if ( ! class_exists( 'IPGeoElementor' ) )
{
	class IPGeoElementor
	{ 
		/**
		 * Initialize elementor element class
		 */
		public static function init()
		{
			$min_php_version = '7.0';
			$min_elementor_version = '2.0.0';

			// Check if Elementor installed and activated
			if(!did_action('elementor/loaded')) return;
			
			// Check for required Elementor version
			if( ! version_compare ( ELEMENTOR_VERSION, $min_elementor_version, '>=' ) ) return;
			
			// Check for required PHP version
			if( version_compare( PHP_VERSION, $min_php_version, '<' ) ) return;
			
			// Add Plugin actions
			add_action('elementor/widgets/widgets_registered', array('IPGeoElementor', 'init_elementor_widgets') );	
			
		}
		
		/**
		 * Initialize elementor widgets and register widget
		 */
		public static function init_elementor_widgets() 
		{
			require_once( 'elementor-widget.class.php' );
	
			// Register widget
			$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
			if( version_compare ( ELEMENTOR_VERSION, '3.1.0', '<=' ) )
			{
				$widgets_manager->register_widget_type( new IPGeoElementorWidgetPreVer() );
			}
			else
			{
				$widgets_manager->register_widget_type( new IPGeoElementorWidget() );
			}
	
		}
		
	}
}