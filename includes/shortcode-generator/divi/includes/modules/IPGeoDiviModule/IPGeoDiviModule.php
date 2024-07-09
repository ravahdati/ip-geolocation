<?php
/**
 * IPGeoDiviModule : A Divi module for running [ipgeo] shortcode
 *
 * @category   Wordpress
 * @since      4.21 (Divi Version)
 */

class IPGeoDiviModule extends ET_Builder_Module {

	public $slug       = 'ip_geolocation_module';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => '',
		'author_uri' => '',
	);

	public function init() {
		$this->name = esc_html__( 'IP-Geolocation', 'ip-geolocation' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'images/icon.svg';
	}

	public function get_advanced_fields_config() {
        return array(
            'main_content' => false,
            'link_options' => false,
            'background' => false,
            'borders' => false,
            'box_shadow' => false,
            'button' => false,
            'filters' => false,
            'fonts' => false,
            'margin_padding' => false,
            'max_width' => false,
        );
    }

	public function render( $attrs, $content = null, $render_slug = '' ) {
		return do_shortcode( '[ipgeo]' );
	}
}

new IPGeoDiviModule;
