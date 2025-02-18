<!-- <?php
define('WP_USE_THEMES', false);
require('./wp-load.php');

$new_version = '3.0.01'; // Define the new version here

// Update version for themes
$themes = wp_get_themes();
foreach ($themes as $theme) {
    $theme_slug = $theme->get_stylesheet();
    $theme_file = get_theme_root($theme_slug) . '/' . $theme_slug . '/style.css';
    if (file_exists($theme_file)) {
        $content = file_get_contents($theme_file);
        $content = preg_replace('/Version:\s*\d+\.\d+\.\d+/', 'Version: ' . $new_version, $content);
        file_put_contents($theme_file, $content);
    }
}

// Update version for plugins
$plugins = get_plugins();
foreach ($plugins as $plugin_file => $plugin_data) {
    $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
    if (file_exists($plugin_path)) {
        $content = file_get_contents($plugin_path);
        $content = preg_replace('/Version:\s*\d+\.\d+\.\d+/', 'Version: ' . $new_version, $content);
        file_put_contents($plugin_path, $content);
    }
}

echo "Version updated to $new_version for all themes and plugins.";
 -->

 // after version change, go to this url and run script. Then comment again.  https://staging5.dev.gibbs.no/update_versions.php