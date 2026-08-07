<?php
/**
 * IP Geolocation Settings Class
 * 
 * @package      WordPress
 * @sub-package  ipgeo 
 * @since        1.0.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class IP_Geo_Location_Settings {

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct() {
		// base of option
		$this->base = 'ipgeo_';

		// Initialise settings
		add_action( 'admin_init', array( &$this, 'init' ) );

		// Register plugin settings
		add_action( 'admin_init' , array( &$this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( &$this, 'add_menu_item' ) );

		// admin footer
		add_action('admin_head', array(&$this, 'admin_header_scripts'));

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( IP_GEOLOCATION_PLUGIN_DIR . 'ip-geolocation.php' ) , array( &$this, 'add_settings_link' ) );
	}

	/**
	 * Initialize settings.
	 *
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'IP Geo Location Settings', 'ip-geolocation' ) , __( 'IP Geo Location Settings', 'ip-geolocation' ) , 'manage_options' , 'ipgeo-settings' ,  array( &$this, 'ipgeo_settings_page' ) );
	}

	/**
	 * Add settings link to the plugin list table.
	 *
	 * @param  array $links Existing links
	 * @return array       Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link[] = '<a href="'.esc_url( add_query_arg( array( 'page' => 'ipgeo-settings' ) , admin_url( '/options-general.php' ) ) ).'">' . __( 'Settings', 'ip-geolocation' ) . '</a>';
		$settings_link = array_merge( $settings_link, $links );
  		return $settings_link;
	}

	/**
	 * Return a dashicon slug for a given settings section, used to give
	 * every tab / header a small piece of visual identity.
	 *
	 * @param  string $section Section key (general|api|map|...)
	 * @return string          Dashicon slug (without the `dashicons-` prefix)
	 */
	private function get_section_icon( $section ) {
		$icons = array(
			'general' => 'admin-generic',
			'api'     => 'rest-api',
			'map'     => 'location-alt',
		);

		$icons = apply_filters( 'ipgeo_settings_section_icons', $icons );

		return isset( $icons[ $section ] ) ? $icons[ $section ] : 'admin-settings';
	}

	/**
	 * Build settings fields.
	 *
	 * @return array Fields to be displayed on the settings page
	 */
	private function settings_fields() {

		$settings['general'] = array(
			'title'       => __( 'General', 'ip-geolocation' ),
			'description' => __( 'This section is the appearance settings.', 'ip-geolocation' ),
			'fields'				=> array(
				array(
					'id' 			=> 'input_class',
					'label'			=> __( 'Input Class' , 'ipgo' ),
					'description'	=> __( 'You can enter name of input class for custom style.', 'ip-geolocation' ),
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 20,
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'button_class',
					'label'			=> __( 'Button Class' , 'ipgo' ),
					'description'	=> __( 'You can enter name of button class for custom style.', 'ip-geolocation' ),
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 20,
					'placeholder'	=> ''
				),
			)
		);

		$settings['api'] = array(
			'title'       => __( 'API', 'ip-geolocation' ),
			'description' => __( 'This section is the settings of API service.', 'ip-geolocation' ),
			'fields'				=> array(
			    array(
					'id'          => 'default_ip_type',
					'label'       => __( 'Default IP Type', 'ip-geolocation' ),
					'type'        => 'select',
					'options'     => array(
						'client'    => 'Client IP',
						'server'    => 'Server IP'
					),
					'default'     => 'client',
				),
				array(
					'id'          => 'api_service',
					'label'       => __( 'API Service', 'ip-geolocation' ),
					'description' => __( 'Please select the service for showing ip information', 'ip-geolocation' ),
					'type'        => 'select',
					'options'     => array(
						'abstractapi'   => 'Abstract API - abstractapi.com',
						'apiip'         => 'apiip - apiip.net',
						'freeipapi'     => 'Free IP API - freeipapi.com',
						'geoplugin'     => 'geoPlugin - geoplugin.com',
						'ipdata'        => 'IP Data - ipdata.co',
						'ip-api'        => 'IP-API - ip-api.com',
						'ip2location'   => 'IP2location API - ip2location.io',
						'ipapi'         => 'ipapi - ipapi.co',
						'ipbase'        => 'ipbase - ipbase.com',
						'ipgeolocation' => 'IPGeolocation API - ipgeolocation.io',
						'ipify'         => 'ipify API - geo.ipify.org',
						'ipinfo'        => 'IPinfo - ipinfo.io',
						'ipstack'       => 'ipstack - ipstack.com',
						'ipwhoorg'      => 'IPWho.org - ipwho.org',
						'ipwhois'       => 'IPWhois - ipwhois.io'
					),
					'default'     => 'ipapi',
				),
				array(
					'id' 			=> 'api_token',
					'label'			=> __( 'API Token' , 'ipgo' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 70,
					'placeholder'	=> ''
				),
			)
		);

		$settings['map'] = array(
			'title'       => __( 'Map', 'ip-geolocation' ),
			'description' => __( 'This section is the settings of displaying the user\'s location on the map.', 'ip-geolocation' ),
			'fields'      => array(
				array(
					'id'          => 'enable_map',
					'label'       => __( 'Enable/Disable', 'ip-geolocation' ),
					'description' => __( 'Enable - if you save this option as checked then it will be shown location on the map.', 'ip-geolocation' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'map_service',
					'label'       => __( 'Map Service', 'ip-geolocation' ),
					'description' => '',
					'type'        => 'select',
					'options'     => array(
						'cedarmaps' =>	'Cedarmaps (cedarmaps.com)',
						'google'    =>	'Google (maps.google.com)',
						'leaflet' 	=>	'Leaflet (leafletjs.com)',
						'mapbox'  =>	'Mapbox (mapbox.com)',
						'mapir'  	=>	'Mapir (corp.map.ir)',
						'parsimap'  =>	'Parsimap (parsimap.ir)',
					),
					'default'     => 'google'
				),
				array(
					'id' 			=> 'map_api_token',
					'label'			=> __( 'Map API Key' , 'ip-geolocation' ),
					'description'	=> __( 'Please enter the map api key to show the map.', 'ip-geolocation' ),
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 55,
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'map_width_section',
					'label'			=> __( 'The width of map section' , 'ip-geolocation' ),
					'description'	=> __( 'Please enter the width of map section. Example: 80%, 100px', 'ip-geolocation' ),
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 5,
					'placeholder'	=> '',
					'default'     => '100%'
				),
				array(
					'id' 			=> 'map_height_section',
					'label'			=> __( 'The height of map section' , 'ip-geolocation' ),
					'description'	=> __( 'Please enter the height of map section. Example: 80%, 100px', 'ip-geolocation' ),
					'type'			=> 'text',
					'default'		=> '',
					'length'		=> 5,
					'placeholder'	=> '',
					'default'     => '250px'
				),
			),
		);

		$settings = apply_filters( 'ipgeo_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = sanitize_text_field( wp_unslash( $_POST['tab'] ) );
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'ipgeo_settings' );

				foreach ( $data['fields'] as $field ) {

					// Allow a field to declare its own callback (kept for
					// backwards-compatibility with any filter-added fields),
					// otherwise fall back to a type-aware sanitizer so every
					// option is ALWAYS sanitized, never registered with an
					// empty callback.
					if ( isset( $field['callback'] ) && is_callable( $field['callback'] ) ) {
						$sanitize_callback = $field['callback'];
					} else {
						$field_copy = $field; // capture by value for the closure
						$sanitize_callback = function( $value ) use ( $field_copy ) {
							return $this->sanitize_field_value( $value, $field_copy );
						};
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting(
						'ipgeo_settings',
						$option_name,
						array(
							'sanitize_callback' => $sanitize_callback,
						)
					);

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( &$this, 'display_field' ),
						'ipgeo_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Type-aware sanitizer used as the sanitize_callback for every
	 * registered setting. Keeps invalid/unexpected input from ever
	 * reaching the database.
	 *
	 * @param  mixed $value The raw submitted value.
	 * @param  array $field The field definition (from settings_fields()).
	 * @return mixed        The sanitized value.
	 */
	public function sanitize_field_value( $value, $field ) {

		switch ( $field['type'] ) {

			case 'checkbox':
				// Checkboxes only ever legitimately submit 'on' (or are
				// absent, which register_setting() won't call us for).
				return ( 'on' === $value ) ? 'on' : '';

			case 'select':
				// Only allow one of the field's own declared option keys;
				// anything else silently falls back to the field default.
				$allowed = isset( $field['options'] ) && is_array( $field['options'] )
					? array_keys( $field['options'] )
					: array();

				$value = is_scalar( $value ) ? (string) $value : '';

				if ( in_array( $value, $allowed, true ) ) {
					return $value;
				}

				return isset( $field['default'] ) ? $field['default'] : '';

			case 'url':
				return esc_url_raw( trim( (string) $value ) );

			case 'hidden':
				// Used for numeric fields elsewhere in the codebase.
				return is_numeric( $value ) ? $value + 0 : '';

			case 'text':
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Settings section description field.
	 *
	 * @param array $section Array of section IDs.
	 * @return void
	 */
	public function settings_section( $section ) {
		$description = $this->settings[ $section['id'] ]['description'];
		$icon        = $this->get_section_icon( $section['id'] );

		$html  = '<div class="ipgeo-section-intro">';
		$html .=	'<span class="dashicons dashicons-' . esc_attr( $icon ) . ' ipgeo-section-intro-icon"></span>';
		$html .=	'<p class="ipgeo-section-intro-text">' . $description . '</p>';
		$html .= '</div>' . "\n";

		$html = apply_filters('ipgeo_settings_section', $html );
		echo wp_kses_post( $html );
	}

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param  array   $data Data array.
	 * @param  object  $post Post object.
	 * @param  boolean $echo Whether to echo the field HTML or return it.
	 * @return string
	 */
	public function display_field( $data = array(), $post = null, $echo = true ) {

		// Get field info.
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name.
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data.
		$data = '';
		if ( $post ) {

			// Get saved field data.
			$option_name .= $field['id'];
			$option       = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		} else {

			// Get saved option.
			$option_name .= $field['id'];
			$option       = get_option( $option_name );

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		}

		// Show default data if no option saved and default is supplied.
		if ( false === $data && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( false === $data ) {
			$data = '';
		}

		// Disable field for map service and map token
		$disabled = false;
		if($field['id'] == "map_service" || $field['id'] == "map_api_token")
		{
			$check_enable_map = get_option('ipgeo_enable_map');
			if($check_enable_map == "on")
				$disabled = false;
			else
				$disabled = true;
		}

		$html = '';

		switch ( $field['type'] ) {

			case 'text':
			case 'url':
				$html .= '<input class="ipgeo-input" id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" size="'.esc_attr( $field['length'] ).'" value="' . esc_attr( $data ) . '" '.disabled( $disabled, true, false ).'/>' . "\n";
				break;

			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' === $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<label class="ipgeo-toggle-switch">';
				$html .=	'<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>';
				$html .=	'<span class="ipgeo-toggle-slider"></span>';
				$html .= '</label>' . "\n";
				break;

			case 'select':
				$html .= '<div class="ipgeo-select-wrap">';
				$html .= '<select class="ipgeo-select" name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '" '. disabled( $disabled, true, false ) .'>';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . esc_html( $v ) . '</option>';
				}
				$html .= '</select>';
				$html .= '<span class="dashicons dashicons-arrow-down-alt2 ipgeo-select-arrow"></span>';
				$html .= '</div> ';
				break;

		}

		switch ( $field['type'] ) { // for description

			case 'text':
			case 'select':
				if( !empty( $field['description'] ) ) 
					$html .= '<p class="description">' . esc_html( $field['description'] ) . '</p>';
				break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}
				
				if( !empty( $field['description'] ) ) 
					$html .= '<span class="description">' . esc_html( $field['description'] ) . '</span>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
				break;
		}
		
		// Define allowed HTML tags and attributes for form elements
        $allowed_tags = array(
            'input' => array(
                'id' => array(),
                'class' => array(),
                'type' => array(),
                'name' => array(),
                'placeholder' => array(),
                'size' => array(),
                'value' => array(),
                'checked' => array(),
                'min' => array(),
                'max' => array(),
                'disabled' => array(),
            ),
            'select' => array(
                'class' => array(),
                'name' => array(),
                'id' => array(),
                'disabled' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
            ),
            'div' => array(
                'class' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'span' => array(
                'class' => array(),
            ),
            'label' => array(
                'for' => array(),
                'class' => array(),
            ),
        );
    
        $html = apply_filters('ipgeo_settings_display_field', $html);
    
        // Escape output with wp_kses and custom allowed tags
        if ( ! $echo ) {
            return wp_kses( $html, $allowed_tags );
        }
    
        echo wp_kses( $html, $allowed_tags );

	}

	/**
	 * Validate form field
	 *
	 * @param  string $data Submitted value.
	 * @param  string $type Type of field to validate.
	 * @return string       Validated value
	 */
	public function validate_field( $data = '', $type = 'text' ) {

		switch ( $type ) {
			case 'text':
				$data = sanitize_text_field( $data );
				break;
			case 'url':
				$data = esc_url_raw( $data );
				break;
			case 'email':
				$data = is_email( $data );
				break;
		}

		return $data;
	}

	/**
	 * Display settings page.
	 *
	 * @return void
	 */
	public function ipgeo_settings_page() {

		// Build page HTML.
		$html  = '<div class="wrap ipgeo-wrap" id="ipgeo_settings">' . "\n";

			// Hero header.
			$html .= '<div class="ipgeo-header">' . "\n";
				$html .= '<div class="ipgeo-header-icon"><span class="dashicons dashicons-location-alt"></span></div>' . "\n";
				$html .= '<div class="ipgeo-header-text">' . "\n";
					$html .= '<h1>' . __( 'IP Geo Location', 'ip-geolocation' ) . ' <span class="ipgeo-version-badge">' . esc_html( defined( 'IP_GEOLOCATION_VERSION' ) ? 'v' . IP_GEOLOCATION_VERSION : '' ) . '</span></h1>' . "\n";
					$html .= '<p>' . __( 'Configure how IP information & location are detected and displayed on your website.', 'ip-geolocation' ) . '</p>' . "\n";
				$html .= '</div>' . "\n";
			$html .= '</div>' . "\n";
    
    		$tab = '';
    		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
    			$tab .= sanitize_text_field( wp_unslash( $_GET['tab'] ) );
    		}
    
    		// Show page tabs.
    		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {
    
    			$html .= '<div class="ipgeo-tabs-wrapper">' . "\n";
    
    			$c = 0;
    			foreach ( $this->settings as $section => $data ) {
    
    				// Set tab class.
    				$class = 'ipgeo-tab';
    				if ( ! isset( $_GET['tab'] ) ) {
    					if ( 0 === $c ) {
    						$class .= ' ipgeo-tab-active';
    					}
    				} else {
    					if ( isset( $_GET['tab'] ) && $section == $tab ) {
    						$class .= ' ipgeo-tab-active';
    					}
    				}
    
    				// Set tab link.
    				$tab_link = add_query_arg( array( 'tab' => $section ) );
    				if ( isset( $_GET['settings-updated'] ) ) {
    					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
    				}
    
    				$icon = $this->get_section_icon( $section );
    
    				// Output tab.
    				$html .= '<a href="' . esc_url( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . "\n";
    				$html .=	'<span class="dashicons dashicons-' . esc_attr( $icon ) . '"></span>' . "\n";
    				$html .=	'<span class="ipgeo-tab-label">' . esc_html( $data['title'] ) . '</span>' . "\n";
    				$html .= '</a>' . "\n";
    
    				++$c;
    			}
    
    			$html .= '</div>' . "\n";
    		}

    		$html .= '<div class="ipgeo-card">' . "\n";
    		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
    
    			// Get settings fields.
    			ob_start();
    			settings_fields( 'ipgeo_settings' );
    			do_settings_sections( 'ipgeo_settings' );
    			$html .= ob_get_clean();
    
    			$html     .= '<p class="submit ipgeo-submit-row">' . "\n";
    				$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
    				$html .= '<input name="Submit" type="submit" class="button-primary ipgeo-save-btn" value="' . esc_attr( __( 'Save Settings', 'ip-geolocation' ) ) . '" />' . "\n";
    			$html .= '</p>' . "\n";
    		$html .= '</form>' . "\n";
    		$html .= '</div>' . "\n"; // .ipgeo-card
	    $html .= '</div>' . "\n"; // .wrap
		
		// Define allowed HTML tags and attributes for form elements
        $allowed_tags = array(
            'div' => array(
                'class' => array(),
                'id' => array(),
            ),
            'h1' => array(
                'class' => array(),
            ),
            'h2' => array(
                'class' => array()
            ),
            'a' => array(
                'href' => array(),
                'class' => array(),
            ),
            'form' => array(
                'method' => array(),
                'action' => array(),
                'enctype' => array(),
            ),
            'input' => array(
                'class' => array(),
                'id' => array(),
                'type' => array(),
                'name' => array(),
                'placeholder' => array(),
                'size' => array(),
                'value' => array(),
                'checked' => array(),
                'min' => array(),
                'max' => array(),
                'disabled' => array(),
            ),
            'select' => array(
                'class' => array(),
                'name' => array(),
                'id' => array(),
                'disabled' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'span' => array(
                'class' => array(),
            ),
            'label' => array(
                'for' => array(),
                'class' => array(),
            ),
            'table' => array(
                'class' => array(),
                'id' => array(),
            ),
            'thead' => array(),
            'tbody' => array(),
            'tr' => array(),
            'th' => array(
                'scope' => array(),
                'class' => array(),
            ),
            'td' => array(
                'class' => array(),
            ),
        );
    
        $html = apply_filters('ipgeo_settings_page', $html);
    
        // Escape output with wp_kses and custom allowed tags
        echo wp_kses( $html, $allowed_tags );
	}

	/**
	 * Add admin footer scripts & styles for the settings page.
	 *
	 * @return void
	 */
	public function admin_header_scripts()
	{
		global $pagenow;

		//Check if current admin page is Option Tree settings
		if ( $pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == 'ipgeo-settings' ) :
		?>
		<style>
		#ipgeo_settings {
			--ipgeo-primary: #6d5ef8;
			--ipgeo-primary-dark: #5a49e0;
			--ipgeo-accent: #22c1a1;
			--ipgeo-bg: #f3f2fb;
			--ipgeo-border: #e6e3f7;
			--ipgeo-text: #2b2650;
			--ipgeo-text-light: #6b6690;
			max-width: 980px;
			margin: 24px auto 60px;
		}

		#ipgeo_settings * { box-sizing: border-box; }

		/* ---------- Hero header ---------- */
		#ipgeo_settings .ipgeo-header {
			display: flex;
			align-items: center;
			gap: 20px;
			background: linear-gradient(135deg, var(--ipgeo-primary) 0%, #9c6bf0 45%, var(--ipgeo-accent) 100%);
			border-radius: 16px;
			padding: 28px 32px;
			margin-bottom: 22px;
			box-shadow: 0 10px 30px -12px rgba(109, 94, 248, .55);
			position: relative;
			overflow: hidden;
		}
		#ipgeo_settings .ipgeo-header:before {
			content: "";
			position: absolute;
			inset: 0;
			background-image: radial-gradient(circle at 85% 20%, rgba(255,255,255,.18), transparent 45%);
			pointer-events: none;
		}
		#ipgeo_settings .ipgeo-header-icon {
			flex: 0 0 auto;
			width: 58px;
			height: 58px;
			border-radius: 14px;
			background: rgba(255,255,255,.18);
			backdrop-filter: blur(4px);
			display: flex;
			align-items: center;
			justify-content: center;
			border: 1px solid rgba(255,255,255,.35);
		}
		#ipgeo_settings .ipgeo-header-icon .dashicons {
			color: #fff;
			font-size: 30px;
			width: 30px;
			height: 30px;
		}
		#ipgeo_settings .ipgeo-header-text h1 {
			margin: 0 0 4px;
			padding: 0;
			font-size: 22px;
			font-weight: 700;
			color: #fff;
			line-height: 1.3;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		#ipgeo_settings .ipgeo-header-text {
			flex: 1 1 auto;
			min-width: 0;
		}
		#ipgeo_settings .ipgeo-header .notice {
			margin: 10px 0 8px;
			padding: 9px 38px 9px 12px;
			background: rgba(255,255,255,.16);
			border: 1px solid rgba(255,255,255,.38);
			border-inline-start: 4px solid #a7f3d0;
			border-radius: 9px;
			box-shadow: none;
			color: #fff;
		}
		#ipgeo_settings .ipgeo-header .notice p {
			margin: 0;
			color: #fff;
		}
		#ipgeo_settings .ipgeo-header .notice-dismiss:before {
			color: rgba(255,255,255,.92);
		}
		#ipgeo_settings .ipgeo-header-text p {
			margin: 0;
			color: rgba(255,255,255,.9);
			font-size: 13.5px;
		}
		#ipgeo_settings .ipgeo-version-badge {
			font-size: 11px;
			font-weight: 600;
			background: rgba(255,255,255,.22);
			border: 1px solid rgba(255,255,255,.4);
			padding: 2px 9px;
			border-radius: 20px;
			color: #fff;
			vertical-align: middle;
		}

		/* ---------- Tabs ---------- */
		#ipgeo_settings .ipgeo-tabs-wrapper {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			margin: 0 0 20px;
			padding: 6px;
			background: #fff;
			border: 1px solid var(--ipgeo-border);
			border-radius: 14px;
			box-shadow: 0 2px 8px rgba(90, 73, 224, .06);
		}
		#ipgeo_settings .ipgeo-tab {
			display: inline-flex;
			align-items: center;
			gap: 7px;
			padding: 9px 16px;
			border-radius: 10px;
			font-size: 13px;
			font-weight: 600;
			color: var(--ipgeo-text-light);
			text-decoration: none;
			transition: all .18s ease;
			box-shadow: none;
		}
		#ipgeo_settings .ipgeo-tab .dashicons {
			font-size: 16px;
			width: 16px;
			height: 16px;
		}
		#ipgeo_settings .ipgeo-tab:hover {
			background: var(--ipgeo-bg);
			color: var(--ipgeo-primary-dark);
		}
		#ipgeo_settings .ipgeo-tab.ipgeo-tab-active {
			background: linear-gradient(135deg, var(--ipgeo-primary), #9c6bf0);
			color: #fff;
			box-shadow: 0 6px 14px -6px rgba(109, 94, 248, .65);
		}

		/* ---------- Card ---------- */
		#ipgeo_settings .ipgeo-card {
			background: #fff;
			border: 1px solid var(--ipgeo-border);
			border-radius: 16px;
			padding: 8px 30px 24px;
			box-shadow: 0 4px 18px rgba(90, 73, 224, .06);
		}

		/* ---------- Section intro ---------- */
		#ipgeo_settings .ipgeo-section-intro {
			display: flex;
			align-items: flex-start;
			gap: 12px;
			margin: 22px 0 6px;
			padding-bottom: 14px;
			border-bottom: 1px solid var(--ipgeo-border);
		}
		#ipgeo_settings .ipgeo-section-intro-icon {
			flex: 0 0 auto;
			width: 30px;
			height: 30px;
			border-radius: 9px;
			background: var(--ipgeo-bg);
			color: var(--ipgeo-primary);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 17px;
		}
		#ipgeo_settings .ipgeo-section-intro-text {
			margin: 4px 0 0;
			color: var(--ipgeo-text-light);
			font-size: 13px;
		}

		/* ---------- Form table ---------- */
		#ipgeo_settings .form-table {
			margin-top: 0;
		}
		#ipgeo_settings .form-table > tbody > tr {
			transition: background-color .15s ease;
			border-radius: 10px;
		}
		#ipgeo_settings .form-table > tbody > tr:hover {
			background-color: var(--ipgeo-bg);
		}
		#ipgeo_settings .form-table th {
			padding: 18px 16px 18px 8px;
			font-weight: 600;
			color: var(--ipgeo-text);
			font-size: 13.5px;
			width: 220px;
		}
		#ipgeo_settings .form-table td {
			padding: 14px 8px;
			vertical-align: middle;
		}
		#ipgeo_settings .form-table .description {
			color: var(--ipgeo-text-light);
			font-size: 12.5px;
			margin-top: 6px;
		}

		/* ---------- Inputs ---------- */
		#ipgeo_settings .ipgeo-input,
		#ipgeo_settings input[type="text"] {
			border: 1.5px solid var(--ipgeo-border);
			border-radius: 9px;
			padding: 8px 12px;
			font-size: 13.5px;
			min-width: 260px;
			transition: border-color .15s ease, box-shadow .15s ease;
			box-shadow: none;
		}
		#ipgeo_settings .ipgeo-input:focus,
		#ipgeo_settings input[type="text"]:focus {
			border-color: var(--ipgeo-primary);
			box-shadow: 0 0 0 3px rgba(109, 94, 248, .15);
			outline: none;
		}
		#ipgeo_settings .ipgeo-input:disabled {
			background: #f3f2fb;
			color: #b3aee0;
		}

		/* ---------- Select ---------- */
		#ipgeo_settings .ipgeo-select-wrap {
			position: relative;
			display: inline-block;
		}
		#ipgeo_settings .ipgeo-select {
			appearance: none;
			-webkit-appearance: none;
			border: 1.5px solid var(--ipgeo-border);
			border-radius: 9px;
			padding: 8px 34px 8px 12px;
			font-size: 13.5px;
			min-width: 300px;
			background: #fff;
			transition: border-color .15s ease, box-shadow .15s ease;
		}
		#ipgeo_settings .ipgeo-select:focus {
			border-color: var(--ipgeo-primary);
			box-shadow: 0 0 0 3px rgba(109, 94, 248, .15);
			outline: none;
		}
		#ipgeo_settings .ipgeo-select:disabled {
			background: #f3f2fb;
			color: #b3aee0;
		}
		#ipgeo_settings .ipgeo-select-arrow {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			font-size: 15px;
			color: var(--ipgeo-text-light);
			pointer-events: none;
		}

		/* ---------- Toggle switch ---------- */
		#ipgeo_settings .ipgeo-toggle-switch {
			position: relative;
			display: inline-block;
			width: 46px;
			height: 25px;
			vertical-align: middle;
		}
		#ipgeo_settings .ipgeo-toggle-switch input {
			opacity: 0;
			width: 0;
			height: 0;
		}
		#ipgeo_settings .ipgeo-toggle-slider {
			position: absolute;
			cursor: pointer;
			inset: 0;
			background-color: #d9d6f2;
			transition: .25s;
			border-radius: 30px;
		}
		#ipgeo_settings .ipgeo-toggle-slider:before {
			position: absolute;
			content: "";
			height: 19px;
			width: 19px;
			left: 3px;
			top: 3px;
			background-color: #fff;
			transition: .25s;
			border-radius: 50%;
			box-shadow: 0 2px 5px rgba(0,0,0,.2);
		}
		#ipgeo_settings .ipgeo-toggle-switch input:checked + .ipgeo-toggle-slider {
			background: linear-gradient(135deg, var(--ipgeo-primary), var(--ipgeo-accent));
		}
		#ipgeo_settings .ipgeo-toggle-switch input:checked + .ipgeo-toggle-slider:before {
			transform: translateX(21px);
		}
		#ipgeo_settings .ipgeo-toggle-switch + .description,
		#ipgeo_settings label > .ipgeo-toggle-switch ~ .description {
			display: inline-block;
			margin-left: 12px;
			vertical-align: middle;
		}

		/* ---------- Submit ---------- */
		#ipgeo_settings .ipgeo-submit-row {
			margin-top: 20px;
			padding-top: 18px;
			border-top: 1px solid var(--ipgeo-border);
		}
		#ipgeo_settings .ipgeo-save-btn {
			background: linear-gradient(135deg, var(--ipgeo-primary), #9c6bf0) !important;
			border: none !important;
			border-radius: 10px !important;
			padding: 9px 26px !important;
			height: auto !important;
			font-size: 13.5px !important;
			font-weight: 600 !important;
			box-shadow: 0 6px 16px -6px rgba(109, 94, 248, .65) !important;
			transition: transform .15s ease, box-shadow .15s ease !important;
			text-shadow: none !important;
		}
		#ipgeo_settings .ipgeo-save-btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 10px 22px -6px rgba(109, 94, 248, .75) !important;
		}

		/* ---------- Responsive ---------- */
		@media (max-width: 782px) {
			#ipgeo_settings .ipgeo-header { flex-direction: column; align-items: flex-start; }
			#ipgeo_settings .form-table th { width: auto; padding-bottom: 6px; }
			#ipgeo_settings .ipgeo-input,
			#ipgeo_settings .ipgeo-select { min-width: 100%; width: 100%; }
		}
		</style>
		<script>
		jQuery(document).ready(function(){
			jQuery("#enable_map").click(function(){
                if( jQuery(this).is(':checked') )
                    jQuery("input[type=text], select").prop('disabled', false );
                else
					jQuery("input[type=text], select").prop('disabled', true );
            });

			// event for api token
			api_token_field_toggle();
			jQuery("select[name=ipgeo_api_service]").on('change', function(){ api_token_field_toggle(); });
			// event for map api token
			map_api_token_toggle();
			jQuery("select[name=ipgeo_map_service]").on('change', function(){ map_api_token_toggle(); });
		});

		function api_token_field_toggle()
		{
			var api_selector = jQuery("select[name=ipgeo_api_service]").val();
			if( api_selector == "ip-api" || api_selector == "ipwhois" || api_selector == "freeipapi" || api_selector == 'ipapi' )
				jQuery("input[name=ipgeo_api_token]").closest('tr').slideUp();
			else
				jQuery("input[name=ipgeo_api_token]").closest('tr').slideDown();
		}

		function map_api_token_toggle()
		{
			var api_selector = jQuery("select[name=ipgeo_map_service]").val();
			if(api_selector=="leaflet")
				jQuery("input[name=ipgeo_map_api_token]").closest('tr').slideUp();
			else
				jQuery("input[name=ipgeo_map_api_token]").closest('tr').slideDown();
		}
		</script>
		<?php
		endif;
	}

}

new IP_Geo_Location_Settings();