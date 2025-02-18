<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://iqonic.design
 * @since      1.2.4
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/public
 * @author     Iqonic Design < hello@iqonic.design>
 */
class Graphina_Pro_Charts_For_Elementor_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.2.4
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.2.4
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.2.4
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.2.4
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Graphina_Pro_Charts_For_Elementor_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Graphina_Pro_Charts_For_Elementor_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style('graphina-pro-charts-for-elementor-public', plugin_dir_url(__FILE__) . 'css/graphina-pro-charts-for-elementor-public.css', array(), $this->version, 'all');
    }

    public function include_widgets()
    {
        if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
            require plugin_dir_path(__FILE__) . '/charts/nested-column/widget/nested_column_chart.php';
            require plugin_dir_path(__FILE__) . '/charts/mixed/widget/mixed_chart.php';
            require plugin_dir_path(__FILE__) . '/tables/advance-datatable/widget/advance-datatable.php';
            require plugin_dir_path(__FILE__) . '/counters/counter/widget/counter.php';
            require plugin_dir_path(__FILE__) . '/charts/brush-chart/widget/brush_chart.php';
            require plugin_dir_path(__FILE__) . '/google_charts/geo/widget/geo_google_chart.php';
            require plugin_dir_path(__FILE__) . '/google_charts/gauge/widget/gauge_google_chart.php';
            require plugin_dir_path(__FILE__) . '/google_charts/org/widget/org_google_chart.php';
            require plugin_dir_path(__FILE__) . '/google_charts/gantt/widget/gantt_google_chart.php';

        }
    }

}