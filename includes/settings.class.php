<?php
/**
 * IP Geolocation Settings Class
 * 
 * @package      WordPress
 * @sub-package  ipgeo 
 * @since        1.0.0
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
						'abstractapi' => 'Abstract API - abstractapi.com',
						'apiip' => 'apiip - apiip.net',
						'freeipapi' => 'Free IP API - freeipapi.com',
						'ip-api'    => 'IP-API - ip-api.com',
						'ipapi'    => 'ipapi - ipapi.co',
						'ipdata'    => 'IP Data - ipdata.co',
						'ip2location' => 'IP2location API - ip2location.io',
						'ipbase'    => 'ipbase - ipbase.com',
						'ipgeolocation' => 'IPGeolocation API - ipgeolocation.io',
						'ipify' => 'ipify API - geo.ipify.org',
						'ipinfo'    => 'IPinfo - ipinfo.io',
						'ipstack' => 'ipstack - ipstack.com',
						'ipwhois'    => 'IPWhois - ipwhois.io'
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
				$current_section = sanitize_text_field( $_POST['tab'] );
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = sanitize_text_field( $_GET['tab'] );
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'ipgeo_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( 'ipgeo_settings', $option_name, $validation );

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
	 * Settings section description field.
	 *
	 * @param array $section Array of section IDs.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
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
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" size="'.esc_attr( $field['length'] ).'" value="' . esc_attr( $data ) . '" '.disabled( $disabled, true, false ).'/>' . "\n";
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
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '" '. disabled( $disabled, true, false ) .'>';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

		}

		switch ( $field['type'] ) { // for description

			case 'text':
			case 'select':
				if( !empty( $field['description'] ) ) 
					$html .= '<p class="description">' . $field['description'] . '</p>';
				break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}
				
				if( !empty( $field['description'] ) ) 
					$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
				break;
		}
		
		// Define allowed HTML tags and attributes for form elements
        $allowed_tags = array(
            'input' => array(
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
				$data = esc_attr( $data );
				break;
			case 'url':
				$data = esc_url( $data );
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
		$html  = '<div class="wrap" id="ipgeo_settings">' . "\n";
    		$html .= '<h2>' . __( 'IP Geo Location Settings', 'ip-geolocation' ) . '</h2>' . "\n";
    
    		$tab = '';
    		//phpcs:disable
    		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
    			$tab .= sanitize_text_field( $_GET['tab'] );
    		}
    		//phpcs:enable
    
    		// Show page tabs.
    		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {
    
    			$html .= '<h2 class="nav-tab-wrapper">' . "\n";
    
    			$c = 0;
    			foreach ( $this->settings as $section => $data ) {
    
    				// Set tab class.
    				$class = 'nav-tab';
    				if ( ! isset( $_GET['tab'] ) ) {
    					if ( 0 === $c ) {
    						$class .= ' nav-tab-active';
    					}
    				} else {
    					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
    						$class .= ' nav-tab-active';
    					}
    				}
    
    				// Set tab link.
    				$tab_link = add_query_arg( array( 'tab' => $section ) );
    				if ( isset( $_GET['settings-updated'] ) ) {
    					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
    				}
    
    				// Output tab.
    				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";
    
    				++$c;
    			}
    
    			$html .= '</h2>' . "\n";
    		}
    
    		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
    
    			// Get settings fields.
    			ob_start();
    			settings_fields( 'ipgeo_settings' );
    			do_settings_sections( 'ipgeo_settings' );
    			$html .= ob_get_clean();
    
    			$html     .= '<p class="submit">' . "\n";
    				$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
    				$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'ip-geolocation' ) ) . '" />' . "\n";
    			$html .= '</p>' . "\n";
    		$html .= '</form>' . "\n";
	    $html .= '</div>' . "\n";
		
		// Define allowed HTML tags and attributes for form elements
        $allowed_tags = array(
            'div' => array(
                'class' => array(),
                'id' => array(),
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
            ),
            'td' => array(),
        );
    
        $html = apply_filters('ipgeo_settings_page', $html);
    
        // Escape output with wp_kses and custom allowed tags
        echo wp_kses( $html, $allowed_tags );
	}

	/**
	 * Add admin footer scripts.
	 *
	 * @return void
	 */
	public function admin_header_scripts()
	{
		global $pagenow;

		//Check if current admin page is Option Tree settings
		if ( $pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == 'ipgeo-settings' ) :
		?>
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
				jQuery("input[name=ipgeo_api_token]").parent().parent().slideUp();
			else
				jQuery("input[name=ipgeo_api_token]").parent().parent().slideDown();
		}

		function map_api_token_toggle()
		{
			var api_selector = jQuery("select[name=ipgeo_map_service]").val();
			if(api_selector=="leaflet")
				jQuery("input[name=ipgeo_map_api_token]").parent().parent().slideUp();
			else
				jQuery("input[name=ipgeo_map_api_token]").parent().parent().slideDown();
		}
		</script>
		<?php
		endif;
	}

}

new IP_Geo_Location_Settings();
?>