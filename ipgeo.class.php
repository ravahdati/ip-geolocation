<?php
/**
 * IP Geolocation Class
 * 
 * @package      WordPress
 * @sub-package  ipgeo 
 * @since        1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

if(!class_exists('IP_Geo_Location'))
{
	class IP_Geo_Location
	{
		protected static $_instance = null;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Construct the plugin object
		 */
    	public function __construct()
    	{
			// Initialize Settings
            add_action('wp_enqueue_scripts', array(&$this, 'ipgeo_enqueue_scripts'));
			
			// ipgeo shortcode
            add_shortcode('ipgeo', array(&$this, 'ipgeo_shortcode'));
			
			// WPbakery Functionality
            require_once('includes/shortcode-generator/wpbakery/wpbakery.class.php');
			add_action('vc_before_init', array('IPGeoWpbakeryShortcode','create_shortcode')); // VC functionality
			
			// Gutenberg Functionality
			require_once('includes/shortcode-generator/gutenberg/gutenberg-block.php');

			// Elementor Functionality
			require_once('includes/shortcode-generator/elementor/elementor.class.php');
			add_action('init', array('IPGeoElementor', 'init'));

			do_action('ipgeo_plugin_hooks');
			
		} // END public function __construct()

		public function ipgeo_enqueue_scripts()
		{
			wp_enqueue_style('ipgeo', plugins_url( '/assets/css/ipgeo.css', __FILE__ ) );
		}
        
		/**
		 * Get client IP
		 * 
		 * @return string client_ip
		 */
		public function get_client_ip()
		{
			$client_ip = '';

			if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
				$ip = $_SERVER['REMOTE_ADDR'];

			return explode(",", $ip)[0];
		}
		/**
		 * IP Geo Shortcode: show IP Geo Location form and result
		 */
        public function ipgeo_shortcode()
        {
			ob_start();
			$ipgeo_input_class = get_option('ipgeo_input_class');
			$ipgeo_button_class = get_option('ipgeo_button_class');
            ?>
            <form class="ipgeo-form" method="post" action="">
				<?php wp_nonce_field('ipgeo_location_nonce_action', 'ipgeo_location_nonce'); ?>
                <input type="text" <?php if(!empty($ipgeo_input_class)) echo 'class="'.esc_attr( $ipgeo_input_class ).'"'; ?> name="ip" value="<?php if(isset($_POST['ip'])) echo esc_attr( $_POST['ip'] ); ?>" placeholder="<?php echo __('Enter IP Address here', 'ipgeo'); ?>" />
                <input type="submit" <?php if(!empty($ipgeo_button_class)) echo 'class="'.esc_attr( $ipgeo_button_class ).'"'; ?> name="check" value="<?php echo __('Search', 'ipgeo'); ?>" />
            </form>
			<?php
			$ip = '';
			if(isset($_POST['check']) && wp_verify_nonce($_REQUEST['ipgeo_location_nonce'], 'ipgeo_location_nonce_action'))
            {
                $ip = sanitize_text_field( $_POST['ip'] );
				$this->get_ip_info($ip);
			}
			else
			{
			    $default_ip_type = get_option('ipgeo_default_ip_type');
                if(!empty($default_ip_type))
                {
                    if($default_ip_type=='client')
                    {
						$ip = $this->get_client_ip();

						if(!empty($ip))
							$this->get_ip_info($ip);
						else
							$this->get_ip_info();
                    }
                    else
                    {
                        $this->get_ip_info();
                    }
                }
                else
                {
					$ip = $this->get_client_ip();
                    $this->get_ip_info($ip);
                }
			}

            $output = ob_get_contents();
            ob_end_clean();
            return apply_filters('ipgeo_shortcode_filter', $output);
		}
		
		/**
		 * Get IP Info
		 * @param inp_ip (optional)
		 * @return void
		 */
		public function get_ip_info( $inp_ip = null )
		{
			$ip = sanitize_text_field( $inp_ip );
			$error = "";

			// get api service
			if(!empty($ip) && !WP_Http::is_ip_address($ip))
			{
				$result['error'] = __('IP Address is invalid.', 'ipgeo');
			}
			else
			{
				$api_service = get_option('ipgeo_api_service');
				if(!empty($api_service))
				{
					if($api_service=="ip-api")
					{
						$result = $this->get_result_data_api( $ip, 'ip-api' );	
					}
					else
					{
						$api_token = get_option('ipgeo_api_token');
						
						// check api token is empty or no
						if(!empty($api_token))
						{
							$result = $this->get_result_data_api( $ip, $api_service, $api_token );
						}
					}
				}
			}
			

			// return error
			if(isset($result['error']))
			{
				if(is_array($result['error']))
					$error = $result['error']['title'];
				else
					$error = $result['error'];
			}

			// show data
			if(empty($error))
			{
				$sanitized_result = $this->get_sanitize_result( $api_service, $result );
				if(!empty($sanitized_result))
				{
					echo '<div id="ipw_main_area" class="home-ip-details">';
					
						// show info
						foreach($sanitized_result as $index => $res_value)
						{
							if($index=="status") continue; // skip 'status' item

							if(!empty($res_value) && !is_array($res_value))
							{
								echo '<div class="json-widget-entry">';
									echo '<div class="indent-0 String">';
										echo '<i></i> ';
										$final_index = ( $index == "ip" || $index == "isp" ) ? esc_attr( strtoupper( $index ) ) : esc_attr( ucwords( $index ) );
										echo '<span class="key">'.esc_attr( $final_index ).':</span> ';
										if( wp_http_validate_url ( $res_value ) )
											echo '<span class="value"><img src="'.esc_url( $res_value ).'" /></span>';
										else
											echo '<span class="value">'.esc_attr( $res_value ).'</span>';
									echo '</div>';
								echo '</div>';
							}
						}

						// show latitude
						echo '<div class="json-widget-entry">';
							echo '<div class="indent-0 String">';
								echo '<i></i> ';
								echo '<span class="key">Latitude:</span> ';
								echo '<span class="value">'.esc_attr( $sanitized_result['location']['lat'] ).'</span>';
							echo '</div>';
						echo '</div>';

						// show longtitude
						echo '<div class="json-widget-entry">';
							echo '<div class="indent-0 String">';
								echo '<i></i> ';
								echo '<span class="key">Longitude:</span> ';
								echo '<span class="value">'.esc_attr( $sanitized_result['location']['lng'] ).'</span>';
							echo '</div>';
						echo '</div>';

					echo '</div>';
					// embed maps
					$location = $this->get_api_location( $sanitized_result );
					if(!empty($location))
						$this->load_maps($location[0], $location[1]);
				}
			}
			else
			{
				echo '<p class="alert alert-danger">'.esc_attr( $error ).'</p>';
			}
		}

		/**
		 * Get result data from API
		 * @param ip
		 * @param api_service
		 * @param api_key (optional)
		 * @return result array
		 */
		protected function get_result_data_api( $ip, $api_service, $api_key = '' )
		{
			$result = [];
			$error  = '';
			$api_key = esc_attr( $api_key );
			// check ans sanitize ip address
			if(!empty($ip) && !WP_Http::is_ip_address($ip))
				$result['error'] = __('IP Address is invalid.', 'ipgeo');
			
			// api service condition
			if(empty($error))
			{
				switch($api_service)
				{
					case "ip-api":
						if(!empty($ip))
							$api_url = 'http://ip-api.com/json/'.$ip;
						else
							$api_url = 'http://ip-api.com/json/';
						break;
					case "ipbase":
						if(!empty($ip))
							$api_url = 'https://api.ipbase.com/v2/info?apikey='.esc_attr($api_key).'&ip='.$ip;
						else
							$api_url = 'https://api.ipbase.com/v2/info?apikey='.esc_attr($api_key);
						break;
					case "ipgeolocation":
						if(!empty($ip))
							$api_url = 'https://api.ipgeolocation.io/ipgeo?apikey='.esc_attr($api_key).'&ip='.$ip;
						else
							$api_url = 'https://api.ipgeolocation.io/ipgeo?apikey='.esc_attr($api_key);
						break;
					case "ip2location":
						if(!empty($ip))
							$api_url = 'https://api.ip2location.io/?format=json&key='.esc_attr($api_key).'&ip='.$ip;
						else
							$api_url = 'https://api.ip2location.io/?format=json&key='.esc_attr($api_key);
						break;
					case "ipwhois":
						if(!empty($ip))
							$api_url = 'http://ipwho.is/'.$ip;
						else
							$api_url = 'http://ipwho.is/';
						break;
					case "abstractapi":
						if(!empty($ip))
							$api_url = 'https://ipgeolocation.abstractapi.com/v1/?api_key='.esc_attr($api_key).'&ip_address='.$ip;
						else
							$api_url = 'https://ipgeolocation.abstractapi.com/v1/?api_key='.esc_attr($api_key);
						break;
					case "ipify":
						if(!empty($ip))
							$api_url = 'https://geo.ipify.org/api/v2/country,city,vpn?apiKey='.esc_attr($api_key).'&ipAddress='.$ip;
						else
							$api_url = 'https://geo.ipify.org/api/v2/country,city,vpn?apiKey='.esc_attr($api_key);
						break;
					case "ipinfo":
						if(!empty($ip))
							$api_url = 'https://ipinfo.io/'.$ip.'?token='.esc_attr($api_key);
						else
							$api_url = 'https://ipinfo.io/?token='.esc_attr($api_key);
						break;
				}
				$response = wp_remote_get( wp_http_validate_url( $api_url ) );
				$result = json_decode($response['body'], true);
			}
			
			if(isset($result['status']) && $result['status']=="fail")
				$result['error'] = __('API doesn\'t get a valid data.', 'ipgeo');

			return apply_filters('result_data_api_filter', $result);
		}

		/**
		 * Get raw api result and return sanitize result
		 * @param api_service
		 * @param raw_api_result
		 * @return sanitized_result
		 */
		protected function get_sanitize_result( $api_service, $raw_api_result )
		{
			$sanitized_result = [];
			if(!empty($raw_api_result))
			{
                switch($api_service)
                {
    				case "ip-api":
    				    if(is_array($raw_api_result))
    				    {
    				        foreach($raw_api_result as $key => $val)
        					{
        						if(!is_array($val) && !is_null($val))
        						{
        							if($key!="lat" && $key!="lon" && $key!="success")
        							{
        								$sanitized_result[$key] = $val;
        							}
        						}
        					}
        					$sanitized_result['location'] = [ 'lat' => $raw_api_result['lat'] , 'lng' => $raw_api_result['lon'] ];
    				    }
    					break;
    				case "ipbase":
    				    if(is_array($raw_api_result['data']))
    				    {
                            foreach($raw_api_result['data'] as $key => $val)
        					{
        						if(is_array($val))
        						{
        							if(!is_null($val))
        							{
        							    // get timezone
        							    if($key=="timezone")
        							        $sanitized_result['timezone'] = $val['id'];    
        							    
        							    // get isp info
        							    if($key=="connection")
        							    {
        							        $sanitized_result['ASN'] = $val['asn'];
            								$sanitized_result['organization'] = $val['organization'];
            								$sanitized_result['ISP'] = $val['isp'];
        							    }
        							    
        							    // get location info
        							    if($key=="location")
        							    {
        							        $sanitized_result['continent'] = $val['continent'];
            								$sanitized_result['country'] = $val['country']['alpha2'];
            								$sanitized_result['city'] = $val['city']['name'];
            								$sanitized_result['location'] = [ 'lat' => $val['latitude'] , 'lng' => $val['longitude'] ];
        							    }
        							    
        							}
        						}
        						else
        						{
        							$sanitized_result[$key] = $val;
        						}
        					}   
    				    }
    					break;
					case "ipgeolocation":
						if(is_array($raw_api_result))
    				    {
    				        foreach($raw_api_result as $key => $val)
        					{
        						if(!is_array($val) && !is_null($val))
        						{
        							if($key!="latitude" && $key!="longitude")
        							{
										$sanitized_result[$key] = $val;
        							}
        						}
        						else
        						{
        							if($key == 'time_zone')
        							{
        								$sanitized_result[$key] = $val['name'];
        							}

									if($key == 'currency')
        							{
        								$sanitized_result[$key] = "{$val['code']} / {$val['name']} / {$val['symbol']}";
        							}
        						}
        					}
        					$sanitized_result['location'] = [ 'lat' => $raw_api_result['latitude'] , 'lng' => $raw_api_result['longitude'] ];
    				    }
						break;
					case "ip2location":
						if(is_array($raw_api_result))
						{
							foreach($raw_api_result as $key => $val)
							{
								if($key!="latitude" && $key!="longitude")
								{
									$sanitized_result[$key] = $val;
								}
							}
							$sanitized_result['location'] = [ 'lat' => $raw_api_result['latitude'] , 'lng' => $raw_api_result['longitude'] ];
						}
						break;
    				case "ipwhois":
    				    if(is_array($raw_api_result))
    				    {
    				        foreach($raw_api_result as $key => $val)
        					{
        						if(!is_array($val) && !is_null($val))
        						{
        							if($key!="latitude" && $key!="longitude" && $key!="success")
        							{
        								$sanitized_result[$key] = $val;
        							}
        						}
        						else
        						{
        							if($key == 'timezone')
        							{
        								$sanitized_result['timezone'] = $val['id'];
        							}
        						}
        					}
        					$sanitized_result['location'] = [ 'lat' => $raw_api_result['latitude'] , 'lng' => $raw_api_result['longitude'] ];
    				    }
    					break;
    				case "abstractapi":
    				    if(is_array($raw_api_result))
    				    {
    				        foreach($raw_api_result as $key => $val)
        					{
        						if(!is_array($val) && !is_null($val))
        						{
        							if($key!="latitude" && $key!="longitude" && $key!="success")
        							{
        								$sanitized_result[$key] = $val;
        							}
        						}
        						else
        						{
        							if($key=="timezone")
        							{
        								$sanitized_result['timezone'] = $val['name'];
        							}
        						}
        					}
        					$sanitized_result['location'] = [ 'lat' => $raw_api_result['latitude'] , 'lng' => $raw_api_result['longitude'] ];
    				    }
    					break;
    				case "ipify":
    				    if(is_array($raw_api_result))
    				    {
    				        foreach($raw_api_result as $key => $val)
        					{
        						if(is_array($val) && $key=="location")
        						{
        							if(!is_null($val) && !is_null($val))
        							{
        								foreach($val as $index => $child_val)
        								{
        									if($index=="country" || $index=="city" || $index=="region" || $index=="timezone")
        										$sanitized_result[$index] = $child_val;
        								}
        								$sanitized_result['location'] = [ 'lat' => $val['lat'] , 'lng' => $val['lng'] ];
        							}
        						}
        						else
        						{
        							$sanitized_result[$key] = $val;
        						}
        					}
    				    }
    					break;
				    case "ipinfo":
				        if(is_array($raw_api_result))
				        {
				            foreach($raw_api_result as $key => $val)
        					{
        						if(!is_array($val) && !is_null($val))
        						{
        							if($key=="loc")
        							{
        								$location = explode(",", $val);
        								$sanitized_result['location'] = [ 'lat' => $location[0], 'lng' => $location[1] ];
        							}
        							else
        							{
        								$sanitized_result[$key] = $val;
        							}
        						}
        					}
				        }
                        break; 
			        }
		    }
			
			return $sanitized_result;
		}
		
		/**
		 * Get API location for map
		 * @param api_result
		 * @return loc (lat and lng)
		 */
		protected function get_api_location( $api_result )
		{
			$loc = [];
			if(!is_null($api_result))
			{
				if(!is_null($api_result['location']))
				{
					return [
						$api_result['location']['lat'],
						$api_result['location']['lng']
					];
				}
			}

			return $loc;
		}

		/**
		 * Load Maps in site
		 * @param lat
		 * @param lng
		 * @return void
		 */
		protected function load_maps($lat, $lng)
		{
			$latitude = esc_attr( $lat );
			$longitude = esc_attr( $lng );
			$enable_map_token  = get_option('ipgeo_enable_map');
			if($enable_map_token)
			{
				$map_api_token = get_option('ipgeo_map_api_token');
				if($map_api_token)
				{
					$map_service = get_option('ipgeo_map_service');
					switch($map_service)
					{
						case "google":
							?>
							<div id="ipgeo_map" style="width:100%;height:250px;"></div>
							<script>
							// Initialize and add the map
							function initMap() {
								// The location of Uluru
								var uluru = {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?>};
								// The map, centered at Uluru
								var map = new google.maps.Map(
									document.getElementById('ipgeo_map'), {zoom: 18, center: uluru}
								);
								// The marker, positioned at Uluru
								var marker = new google.maps.Marker({position: uluru, map: map});
							}
							</script>
							<script defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $map_api_token; ?>&callback=initMap"></script>
							<?php
							break;
						case "leaflet":
								?>
								<!-- Get the leaflet JavaScript file -->
								<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
								<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
								<div id="ipgeo_map" style="width:100%;height:250px;"></div>
								<script>
								// Initialize the map
								const map = L.map('ipgeo_map')
							
								// Get the tile layer from OpenStreetMaps
								L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
							
								// Specify the maximum zoom of the map
								maxZoom: 18,
							
								// Set the attribution for OpenStreetMaps
								attribution: 'IP Geo Location | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
								}).addTo(map);
							
								// Set the view of the map
								// with the latitude, longitude and the zoom value
								map.setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 16);
							
								// Show a market at the position of the Eiffel Tower
								L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map);
								</script>
								<?php
								break;
						case "parsimap":
							?>
							<script src="https://cdn.parsimap.ir/third-party/mapbox-gl-js/v1.13.0/mapbox-gl.js"></script>
							<link href="https://cdn.parsimap.ir/third-party/mapbox-gl-js/v1.13.0/mapbox-gl.css" rel="stylesheet"/>
							<div id="ipgeo_map" style="width: 100%; height: 250px"></div>
							<script>
							mapboxgl.setRTLTextPlugin(
								'https://cdn.parsimap.ir/third-party/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
								null,
							)

							const map = new mapboxgl.Map({
								container: 'ipgeo_map',
								style: 'https://api.parsimap.ir/styles/parsimap-streets-v11?key=<?php echo $map_api_token; ?>',
								center: [<?php echo $latitude; ?>, <?php echo $longitude; ?>],
								zoom: 8,
							})
							</script>
							<?php
							break;
					}
				}
			}
		}
		
		/**
		* Activate the plugin
		*/
		public static function ipgeo_activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		* Deactivate the plugin
		*/
		public static function ipgeo_deactivate()
		{
			// Do nothing
		} // END public static function deactivate	
	
	} // END class IPInfo
} // END if(!class_exists('IPInfo'))

if(class_exists('IP_Geo_Location'))
{
	// instantiate the plugin class
	$ipGeoObj = new IP_Geo_Location();
}
?>