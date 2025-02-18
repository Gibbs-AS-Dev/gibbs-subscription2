<?php
/**
 * Widgets class.
 *
 * @category   Class
 * @package    ElementorListeo
 * @subpackage WordPress
 * @author     Purethemes.net
 * @copyright  Purethemes.net
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorListeo;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || die();

/**
 * Class Plugin
 *
 * Main Plugin class
 *
 * @since 1.0.0
 */
class Widgets {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers the widget scripts.
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		//wp_register_script( 'listeo_elementor', plugins_url( '/assets/js/elementorlisteo.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	}

	public function backend_preview_scripts() {
		wp_enqueue_script( 'elementor-preview-listeo', plugins_url( '/assets/js/elementor_preview_listeo.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files() {

		require_once 'widgets/class-headline.php';
		require_once 'widgets/class-tax-carousel.php';
		require_once 'widgets/class-tax-grid.php';
		require_once 'widgets/class-tax-list.php';
		require_once 'widgets/class-homecustom-grid.php';
		require_once 'widgets/class-tax-gallery.php';
		require_once 'widgets/class-iconbox.php';
		require_once 'widgets/class-imagebox.php';
		require_once 'widgets/class-post-grid.php';
		require_once 'widgets/class-listings-carousel.php';
		require_once 'widgets/class-listings.php';
		require_once 'widgets/class-flip-banner.php';
		require_once 'widgets/class-testimonials.php';
		require_once 'widgets/class-pricing-table.php';
		require_once 'widgets/class-pricing-table-woo.php';
		
		//require_once 'widgets/class-home-banner.php';
		require_once 'widgets/class-home-search-slider.php';
		
		require_once 'widgets/class-logo-slider.php';
		require_once 'widgets/class-address-box.php';
		require_once 'widgets/class-alertbox.php';
		// home search boxes


		
		//require_once 'widgets/class-widget2.php';
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {
		// It's now safe to include Widgets files.
		$this->include_widgets_files();
			
            // 'imagebox',
            // 'posts-carousel',
            // 'listings-carousel',
            // 'flip-banner',
            // 'testimonials',
            // 'pricing-table',
            // 'pricingwrapper',
            // 'logo-slider',
           
            // 'address-box',
            // 'button',
            // 'alertbox',
            // 'list',
            // 'pricing-tables-wc',
            // 'masonry'
		// Register the plugin widget classes.
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Headline() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TaxonomyCarousel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TaxonomyGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\HomeCustomGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TaxonomyList() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TaxonomyGallery() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\IconBox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\ImageBox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PostGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\ListingsCarousel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listings() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\FlipBanner() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Testimonials() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PricingTable() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PricingTableWoo() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\LogoSlider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Addresbox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Alertbox() );
		//\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\HomeBanner() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\HomeSearchSlider() );


	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'elementor/elements/categories_registered', array( $this, 'create_custom_categories') );

		// Register the widget scripts.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts' ) );

		add_action('elementor/preview/enqueue_styles', array($this, 'backend_preview_scripts'), 10);
        
        //add_action('elementor/frontend/after_register_scripts', array($this, 'cocobasic_frontend_enqueue_script'));

		// Register the widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

		
	}


	function create_custom_categories( $elements_manager ) {

	    $elements_manager->add_category(
	        'listeo',
	        [
	         'title' => __( 'Listeo', 'plugin-name' ),
	         'icon' => 'fa fa-clipboard',
	        ]
	    );
	}
}

// Instantiate the Widgets class.
Widgets::instance();