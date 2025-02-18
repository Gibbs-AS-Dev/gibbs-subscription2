<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin line.
 *
 * @link       https://iqonic.design
 * @since      1.2.4
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.2.4
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 * @author     Iqonic Design < hello@iqonic.design>
 */
class Graphina_Pro_Charts_For_Elementor
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.2.4
     * @access   protected
     * @var      Graphina_Pro_Charts_For_Elementor_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.2.4
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.2.4
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin line and
     * the public-facing side of the site.
     *
     * @since    1.2.4
     */
    public function __construct()
    {
        if (defined('GRAPHINA_PRO_CHARTS_FOR_ELEMENTOR_VERSION')) {
            $this->version = GRAPHINA_PRO_CHARTS_FOR_ELEMENTOR_VERSION;
        } else {
            $this->version = '1.2.5';
        }
        $this->plugin_name = 'graphina-pro-charts-for-elementor';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_elementor_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Graphina_Pro_Charts_For_Elementor_Loader. Orchestrates the hooks of the plugin.
     * - Graphina_Pro_Charts_For_Elementor_i18n. Defines internationalization functionality.
     * - Graphina_Pro_Charts_For_Elementor_Admin. Defines all hooks for the admin line.
     * - Graphina_Pro_Charts_For_Elementor_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.2.4
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-graphina-pro-charts-for-elementor-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-graphina-pro-charts-for-elementor-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'elementor/class-graphina-pro-charts-for-elementor-public.php';

        $this->loader = new Graphina_Pro_Charts_For_Elementor_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Graphina_Pro_Charts_For_Elementor_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.2.4
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Graphina_Pro_Charts_For_Elementor_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.2.4
     * @access   private
     */
    private function define_elementor_hooks()
    {
        $plugin_public = new Graphina_Pro_Charts_For_Elementor_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('elementor/widgets/register', $plugin_public, 'include_widgets');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.2.4
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.2.4
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Graphina_Pro_Charts_For_Elementor_Loader    Orchestrates the hooks of the plugin.
     * @since     1.2.4
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.2.4
     */
    public function get_version()
    {
        return $this->version;
    }
}