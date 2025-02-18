<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://iqonic.design
 * @since      1.2.4
 *
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.2.4
 * @package    Graphina_Pro_Charts_For_Elementor
 * @subpackage Graphina_Pro_Charts_For_Elementor/includes
 * @author     Iqonic Design < hello@iqonic.design>
 */
class Graphina_Pro_Charts_For_Elementor_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.2.4
     */
    public static function deactivate()
    {
        delete_option('graphina_is_activate');
        delete_option('graphina_pro_is_install');
    }
}