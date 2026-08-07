<?php
/**
 * IP Geolocation element
 *
 * Create a IP Geolocation in Elementor
 *
 * @category   Wordpress
 * @since      3.10.1 (Elementor Version)
 *
 */

if(!defined('ABSPATH')) exit();

class IPGeoElementorWidget extends \Elementor\Widget_Base
{
	/**
	 * Get widget name.
	 *
	 * Retrieve IPGeo widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'IP-Geolocation';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve IPGeo widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return 'IP Geolocation';	
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve IPGeo widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-map-pin';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the IPGeo widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return array('general');
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the IPGeo widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'ipgeo', 'IP Geo', 'ip-geo', 'ip-geolocation' ];
	}

	/**
	 * Register IPGeo widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{
		$this->ipgeo_register_controls();
	}

	public function ipgeo_register_controls()
	{
		$this->start_controls_section (
			'content_section',
			array(
				'label' => 'IP Geolocation',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'ipgeo_wrapper_id',
			array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Wrapper ID', 'ip-geolocation' ),
				//'dynamic' => ['active' => true ],
				'placeholder' => '',
				'default' => '',
			)
		);

		$this->add_control(
			'ipgeo_wrapper_class',
			array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Wrapper Class', 'ip-geolocation' ),
				//'dynamic' => ['active' => true ],
				'placeholder' => '',
				'default' => '',
			)
		);

		$this->end_controls_section();	
	}

	/**
	 * Render IPGeo widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		// wrapper id - sanitize_html_class() strips anything that isn't
		// valid in an HTML id/class token, which also closes off any
		// attribute-breakout attempt (quotes, spaces, angle brackets, etc).
		$wrapper_id = $this->get_settings_for_display( 'ipgeo_wrapper_id' );
		$wrapper_id = sanitize_html_class( $wrapper_id );

		// wrapper class - supports a space-separated list of classes.
		$wrapper_class_raw = $this->get_settings_for_display( 'ipgeo_wrapper_class' );
		$wrapper_classes    = array_filter( array_map( 'sanitize_html_class', explode( ' ', (string) $wrapper_class_raw ) ) );
		$wrapper_class      = implode( ' ', $wrapper_classes );

		// generate IPGeo shortcode
		$shortcode = do_shortcode('[ipgeo]');

		?>

		<div<?php if ( ! empty( $wrapper_id ) ) : ?> id="<?php echo esc_attr( $wrapper_id ); ?>"<?php endif; ?><?php if ( ! empty( $wrapper_class ) ) : ?> class="<?php echo esc_attr( $wrapper_class ); ?>"<?php endif; ?>>
			<?php echo $shortcode; ?>
		</div>

		<?php
	}
	
}

/**
 * Elementor class for previous elementor version
 * function _register_controls() is deprecated since 3.1.0 of Elementor
 */
class IPGeoElementorWidgetPreVer extends IPGeoElementorWidget {
	protected function _register_controls() {
		$this->ipgeo_register_controls();
	}
}