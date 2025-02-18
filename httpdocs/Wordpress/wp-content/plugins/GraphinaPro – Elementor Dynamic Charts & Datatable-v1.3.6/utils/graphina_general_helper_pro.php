<?php

function graphina_pro_if_failed_load()
{
    $lite_latest_version = "1.7.4";
    if (!current_user_can('activate_plugins')) {
        return;
    }

    if (!function_exists('get_plugins')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    // Get Graphina animation lite version basename
    $basename = '';
    $plugins = get_plugins();

    foreach ($plugins as $key => $data) {
        if ($data['TextDomain'] === "graphina-charts-for-elementor") {
            $basename = $key;
        }
    }

    $folder_name = explode("/", $basename);

//    if (is_graphina_pro_plugin_installed($basename) && is_plugin_active($basename)) {
//        return;
//    }

    if (is_graphina_pro_plugin_installed($basename)) {
        if(!is_plugin_active($basename)){
            $url = wp_nonce_url('plugins.php?action=activate&plugin=' . $basename . '&plugin_status=all&paged=1&s', 'activate-plugin_' . $basename);
            $message = sprintf(__('<strong>GraphinaPro – Elementor Dynamic Charts & Datatable</strong> requires <strong>Graphina - Elementor Charts and Graphs</strong> plugin to be active. Please activate Graphina - Elementor Charts and Graphs for GraphinaPro – Elementor Dynamic Charts & Datatable to continue.', 'graphina-pro-charts-for-elementor'));
            $button_text = __('Activate Graphina – Elementor Charts and Graphs',  'graphina-pro-charts-for-elementor');
            $button = '<p><a href="' . $url . '" class="button-primary">' . $button_text . '</a></p>';
        }
        else if(version_compare(graphina_pro_get_lite_plugin_version($basename), $lite_latest_version, '>=')){
            return;
        }else{
            $url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $basename), 'upgrade-plugin_' . $basename);
            $message = sprintf(__('<strong>GraphinaPro – Elementor Dynamic Charts & Datatable</strong> requires <strong>Graphina – Elementor Charts and Graphs v-' . $lite_latest_version . '</strong> plugin. Please update Graphina – Elementor Charts and Graphs to continue.',  'graphina-pro-charts-for-elementor'), '<strong>', '</strong>');
            $button_text = __('Update Graphina – Elementor Charts and Graphs',  'graphina-pro-charts-for-elementor');
            $button = '<p><a href="' . $url . '" class="button-primary">' . $button_text . '</a></p>';
        }
    } else {
        $url = wp_nonce_url(self_admin_url('plugin-install.php?s=Graphina%20–%20Elementor%20Charts%20and%20Graphs&tab=search&type=term'), 'install-plugin_graphina-elementor-charts-and-graphs');
        $message = sprintf(__('<strong>GraphinaPro – Elementor Dynamic Charts & Datatable</strong> requires <strong>Graphina – Elementor Charts and Graphs</strong> plugin to be installed and activated. Please install Graphina – Elementor Charts and Graphs for GraphinaPro – Elementor Dynamic Charts & Datatable to continue.',  'graphina-pro-charts-for-elementor'), '<strong>', '</strong>');
        $button_text = __('Install Graphina – Elementor Charts and Graphs',  'graphina-pro-charts-for-elementor');
        $button = '<p><a href="' . $url . '" class="button-primary">' . $button_text . '</a></p>';
    }
    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
    printf('<div class="error"><p>%1$s</p>%2$s</div>', __($message), $button);
    deactivate_plugins(  GRAPHINA_PRO_BASE_PATH);

}

function graphina_if_license_deactivate()
{
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $url = wp_nonce_url(self_admin_url('admin.php?page=graphina-pro'));
    $message = sprintf(__('<strong>GraphinaPro – Elementor Dynamic Charts & Datatable</strong> requires <strong>License Code</strong> for use pro features. Please enter license code for GraphinaPro.',  'graphina-pro-charts-for-elementor'), '<strong>', '</strong>');
    $button_text = __('Add License Code for GraphinaPro',  'graphina-pro-charts-for-elementor');

    $button = '<p><a href="' . $url . '" class="button-primary">' . $button_text . '</a></p>';

    printf('<div class="notice notice-warning"><p>%1$s</p>%2$s</div>', __($message), $button);
}

function is_graphina_pro_plugin_installed($basename)
{
    if (!function_exists('get_plugins')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    return isset($plugins[$basename]);
}

function graphina_pro_get_lite_plugin_version($basename)
{
    if (!function_exists('get_plugins')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    return !empty($plugins[$basename]) && !empty($plugins[$basename]['Version']) ? $plugins[$basename]['Version'] : '0';
}

function graphina_pro_make_lite_version()
{
    // Get Graphina animation lite version basename
    $basename = '';
    $plugins = get_plugins();
    foreach ($plugins as $key => $data) {
        if ($data['TextDomain'] === "graphina-charts-for-elementor") {
            $basename = $key;
        }
    }

    $plugin_data = graphina_pro_get_plugin_data('graphina-charts-for-elementor');

    if (is_graphina_pro_plugin_installed($basename)) {
        // upgrade plugin
        if (isset($plugin_data->version) && graphina_pro_get_lite_plugin_version($basename) != $plugin_data->version) {
            graphina_pro_upgrade_plugin($basename);
        }

        if (!is_plugin_active($basename)) {
            activate_plugin(graphina_pro_call_path(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $basename), '', false, false);
            return true;
        }
    } else {
        if (isset($plugin_data->download_link)) {
            graphina_pro_install_plugin($plugin_data->download_link);
            return true;
        }
    }
    return false;
}

function graphina_pro_install_plugin($plugin_url)
{
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

    $skin = new \Automatic_Upgrader_Skin;
    $upgrade = new \Plugin_Upgrader($skin);
    $upgrade->install($plugin_url);

    // activate plugin
    activate_plugin($upgrade->plugin_info(), '', false, false);

    return $skin->result;
}

function graphina_pro_upgrade_plugin($basename)
{
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

    $skin = new \Automatic_Upgrader_Skin;
    $upgrader = new \Plugin_Upgrader($skin);
    $upgrader->upgrade($basename);

    return $skin->result;
}

function graphina_pro_call_path($path)
{
    $path = str_replace(['//', '\\\\'], ['/', '\\'], $path);
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function graphina_pro_get_plugin_data($slug = '')
{
    $args = array(
        'slug' => $slug,
        'fields' => array(
            'version' => false,
        ),
    );

    $response = wp_remote_post(
        'http://api.wordpress.org/plugins/info/1.0/',
        array(
            'body' => array(
                'action' => 'plugin_information',
                'request' => serialize((object)$args),
            ),
        )
    );

    if (is_wp_error($response)) {
        return false;
    } else {
        $response = unserialize(wp_remote_retrieve_body($response));
        if ($response) {
            return $response;
        } else {
            return false;
        }
    }
}

function graphina_pro_get_alphabet()
{
    return [
        '1' => 'A',
        '2' => 'B',
        '3' => 'C',
        '4' => 'D',
        '5' => 'E',
        '6' => 'F',
        '7' => 'G',
        '8' => 'H',
        '9' => 'I',
        '10' => 'J',
        '11' => 'K',
        '12' => 'L',
        '13' => 'M',
        '14' => 'N',
        '15' => 'O',
        '16' => 'P',
        '17' => 'Q',
        '18' => 'R',
        '19' => 'S',
        '20' => 'T',
        '21' => 'U',
        '22' => 'V',
        '23' => 'W',
        '24' => 'X',
        '25' => 'Y',
        '26' => 'Z'
    ];
}

function graphina_replace_dynamic_key($type,$dataType,$settings,$data){

    if(!empty($settings['iq_' . $type . '_element_import_from_'.$dataType.'_dynamic_key']) && $settings['iq_' . $type . '_element_import_from_'.$dataType.'_dynamic_key'] === 'yes'){
        $data = str_replace("{{CURRENT_USER_ID}}",get_current_user_id(),$data);
        $userObj = new WP_User(get_current_user_id());
        $data = str_replace("{{CURRENT_USER_EMAIL}}",!empty($userObj->user_email) ? $userObj->user_email : 'demographina@gmail.com' ,$data);
        $data = str_replace("{{CURRENT_USER_ROLE}}",!empty($userObj->roles) && $userObj->roles[0] ? $userObj->roles[0] : 'demoadmin' ,$data);
        $data = str_replace("{{CURRENT_DATE}}",current_time('Y-m-d'),$data);
        $data = str_replace("{{CURRENT_DATE_TIME}}",current_time('Y-m-d H:i:s'),$data);
        $data = str_replace("{{CURRENT_TIME}}",current_time('H:i:s'),$data);

        $data = apply_filters("after_graphina_replace_dynamic_key",$data);
    }

    return $data;
}
