<?php
/**
 * Divi integration bootstrap.
 *
 * @package IP_Geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ipgeo_initialize_extension' ) ):
/**
 * Register the legacy Divi script-library handle when Divi does not provide it.
 *
 * DiviExtension adds this handle as a dependency of the generated frontend
 * bundle. Some builder contexts (notably Divi 5) no longer register it, which
 * triggers a missing-dependency notice in WordPress 6.9.1 and later. Registering
 * it as an alias preserves compatibility without loading a non-existent file.
 *
 * @since 3.0.1
 *
 * @return void
 */
function ipgeo_register_divi_script_library_alias() {
	if ( ! wp_script_is( 'divi-script-library-frontend-scripts', 'registered' ) ) {
		wp_register_script(
			'divi-script-library-frontend-scripts',
			false,
			array(),
			null,
			true
		);
	}
}

/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function ipgeo_initialize_extension() {
	// Run after Divi's hooks are registered but before DiviExtension enqueues
	// this extension's generated frontend bundle.
	add_action( 'wp_enqueue_scripts', 'ipgeo_register_divi_script_library_alias' );

	require_once plugin_dir_path( __FILE__ ) . 'includes/IPGeoDivi.php';
}
add_action( 'divi_extensions_init', 'ipgeo_initialize_extension' );
endif;