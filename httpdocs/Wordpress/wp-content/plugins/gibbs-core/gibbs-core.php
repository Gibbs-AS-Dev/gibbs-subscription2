<?php
/*
Plugin Name: Gibbs Core
Description: This plugin contains common code used by other Gibbs plugins.
Version: 1.0
Author: Geir Vidar Kristensen
*/

// Prevent direct access to the plugin. It has to appear on a page.
if (!defined('ABSPATH'))
{
    exit;
}

class GibbsCore
{
    public function __construct()
    {
        add_action('init', array($this, 'initialise_component'));
    }

    public function initialise_component()
    {
        define('PLUGIN_PATH', dirname(__FILE__) . '/');
        require_once PLUGIN_PATH . 'users/user_groups.php';
        require_once PLUGIN_PATH . 'custom_fields/custom_fields_renderer.php';
    }
}

new GibbsCore;
?>