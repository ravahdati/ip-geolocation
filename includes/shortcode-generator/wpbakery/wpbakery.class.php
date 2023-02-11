<?php
/**
 * IP Geolocation element
 *
 * Create a IP Geolocation in WPBakery
 *
 * @category   Wordpress
 * @since      6.9 (WPBakery Version)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.


if ( ! class_exists( 'IPGeoWpbakeryShortcode' ) )
{

    class IPGeoWpbakeryShortcode
    {
        function __construct()
        {
            add_action( 'init', array( $this, 'create_shortcode' ), 999 );
        }
        
        public function create_shortcode()
        {
            // Stop all if VC is not enabled
            if ( !defined( 'WPB_VC_VERSION' ) || !function_exists('vc_map') ) {
                return;
            }        

            // Map IP Geolocation with vc_map()
            vc_map( array(
                'name'          => __('IP-Geolocation', 'ipgeo'),
                'base'          => 'ipgeo',
                'description'  	=> __( 'Show IP Geolocation on your custom page', 'ipgeo' ),
                'category'      => __( 'IP Geolocation', 'ipgeo'),                
                'params' => array(
                    array(
                		'type' => 'el_id',
                		'heading' => esc_html__( 'Element ID', 'js_composer' ),
                		'param_name' => 'el_id',
                		'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
                	),
                	array(
                		'type' => 'textfield',
                		'heading' => esc_html__( 'Extra class name', 'js_composer' ),
                		'param_name' => 'el_class',
                		'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
                	)
                ),
            ));
        }
    }
}