<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://iqonic.design
 * @since      1.2.4
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.2.4
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 * @author     Iqonic Design < hello@iqonic.design>
 */
class Graphina_Pro_Charts_For_Elementor_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.2.4
     */
    public function load_plugin_textdomain()
    {

        load_plugin_textdomain(
            'graphina-pro-charts-for-elemento',
            false,
            dirname( GRAPHINA_PRO_BASE_PATH ). '/languages/'
        );

    }
}