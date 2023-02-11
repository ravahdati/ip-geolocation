<?php
/**
 * IP Geolocation element
 *
 * Create a IP Geolocation block in Gutenberg
 * @package      WordPress
 * @sub-package  ipgeo 
 * @since        14.4 (Gutenberg Version)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function ipgeo_gutenberg_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'ipgeo_gutenberg_block_render_callback',
		)
	);
}
add_action( 'init', 'ipgeo_gutenberg_block_init' );

/**
 * Render callback function.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered output.
 */
function ipgeo_gutenberg_block_render_callback( $attributes, $content, $block ) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'build/template.php';
	return ob_get_clean();
}
