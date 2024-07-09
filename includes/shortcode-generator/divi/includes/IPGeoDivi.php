<?php

/**
 * IPGeoDivi : A Divi extension that displays IP geolocation information.
 *
 * @category   Wordpress
 * @since      4.21 (Divi Version)
 */

class IPGeoDivi extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'ip-geolocation';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'ipgeo-divi';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $required_divi_core_version = '4.9.0';

	/**
	 * IPGeoDivi constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct( $name = 'ipgeo-divi', $args = array() ) {

		//compare divi version with required version
		if (!function_exists('_et_core_find_latest')) return;
		$divi_core_version = _et_core_find_latest('version');
		if (version_compare($divi_core_version, $this->required_divi_core_version) < 0) {
			return;
		}
		
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );
		$this->_builder_js_data = array(
			'i10n' => array(
				'ip_geolocation_module' => array(
					'title'			=> __( 'IP-Geolocation', 'ip-geolocation' ),
					'description'	=> __( 'Showing IP Info & Location', 'ip-geolocation' )
				),
			),
		);

		parent::__construct( $name, $args );

		if( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;

	}
}

new IPGeoDivi;