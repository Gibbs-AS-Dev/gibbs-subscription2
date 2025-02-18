<?php
class Class_Gibbs_Subscription_Admin 
{
    private $stripe;
    private $publishableKey;
    private $secretKey;
    private $stripe_webhook;
    private $stripe_custom_plan_product_id;

    public function __construct() {
        //error_log('Class_Gibbs_Subscription_Admin instantiated');

        $mode = get_option('stripe_mode');

        if ($mode === 'test') {
            $this->publishableKey = get_option('stripe_test_publish_key');
            $this->secretKey = get_option('stripe_test_secret_key');
            $this->stripe_webhook = get_option('stripe_test_webhook');
            $this->stripe_custom_plan_product_id = get_option('stripe_test_custom_plan_product_id');
        } else {
            $this->publishableKey = get_option('stripe_live_publish_key');
            $this->secretKey = get_option('stripe_live_secret_key');
            $this->stripe_webhook = get_option('stripe_live_webhook');
            $this->stripe_custom_plan_product_id = get_option('stripe_live_custom_plan_product_id');
        }

        // Load Stripe PHP Library
        require_once GIBBS_STRIPE_PATH . 'library/stripe/vendor/autoload.php'; // Adjust the path if necessary
        if($this->secretKey){
            $this->stripe = new \Stripe\StripeClient($this->secretKey);
        }else{
            $this->stripe = "";
        }
        
        add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);
        add_action('save_post', [$this, 'save_custom_fields']);

        // Add custom columns
        //add_filter('manage_stripe-packages_posts_columns', [$this, 'add_custom_columns']);
        //add_action('manage_stripe-packages_posts_custom_column', [$this, 'populate_custom_columns'], 10, 2);

        // Enqueue custom styles
        add_action('admin_enqueue_scripts', function() {
            wp_enqueue_style('custom-package-styles', plugin_dir_url(__FILE__) . 'css/admin-style.css');
        });
        register_setting('stripe_options_group', 'stripe_mode');
        register_setting('stripe_options_group', 'stripe_test_publish_key');
        register_setting('stripe_options_group', 'stripe_test_secret_key');
        register_setting('stripe_options_group', 'stripe_live_publish_key');
        register_setting('stripe_options_group', 'stripe_live_secret_key');
        register_setting('stripe_options_group', 'stripe_test_webhook');
        register_setting('stripe_options_group', 'stripe_live_webhook');
        register_setting('stripe_options_group', 'stripe_test_custom_plan_product_id');
        register_setting('stripe_options_group', 'stripe_live_custom_plan_product_id');


    }
    public function add_stripe_packages_submenu() {
        add_submenu_page(
            'edit.php?post_type=stripe-packages', // Parent slug
            __('Settings', 'textdomain'), // Page title
            __('Settings', 'textdomain'), // Menu title
            'manage_options', // Capability
            'stripe-packages-settings', // Menu slug
            [$this, 'render_settings_page'] // Callback function
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Stripe Package Settings', 'textdomain'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('stripe_options_group');
                do_settings_sections('stripe-packages-settings');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Stripe Mode', 'textdomain'); ?></th>
                        <td>
                            <select name="stripe_mode">
                                <option value="test" <?php selected(get_option('stripe_mode'), 'test'); ?>><?php _e('Test', 'textdomain'); ?></option>
                                <option value="live" <?php selected(get_option('stripe_mode'), 'live'); ?>><?php _e('Live', 'textdomain'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Test Publish Key', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_test_publish_key" value="<?php echo esc_attr(get_option('stripe_test_publish_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Test Secret Key', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_test_secret_key" value="<?php echo esc_attr(get_option('stripe_test_secret_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Stripe Test webhook', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_test_webhook" value="<?php echo esc_attr(get_option('stripe_test_webhook')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Stripe Test Custom plan product id', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_test_custom_plan_product_id" value="<?php echo esc_attr(get_option('stripe_test_custom_plan_product_id')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Live Publish Key', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_live_publish_key" value="<?php echo esc_attr(get_option('stripe_live_publish_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Live Secret Key', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_live_secret_key" value="<?php echo esc_attr(get_option('stripe_live_secret_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Stripe Live webhook', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_live_webhook" value="<?php echo esc_attr(get_option('stripe_live_webhook')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Stripe Live Custom plan product id', 'textdomain'); ?></th>
                        <td>
                            <input type="text" name="stripe_live_custom_plan_product_id" value="<?php echo esc_attr(get_option('stripe_live_custom_plan_product_id')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    
   
    

    public function add_custom_meta_boxes() {
        add_meta_box('stripe_packages_meta', 'Package Details', [$this, 'render_meta_box'], 'stripe-packages', 'normal', 'high');
    }

    public function render_meta_box($post) {
        wp_nonce_field('stripe_packages_meta_box', 'stripe_packages_meta_box_nonce');

        // Retrieve existing values
        $price = get_post_meta($post->ID, '_package_price', true);
        $stripe_product_id = get_post_meta($post->ID, 'stripe_product_id', true);
        $start_price_id = get_post_meta($post->ID, 'start_price_id', true);
        $listing_2_to_5_price_id = get_post_meta($post->ID, 'listing_2_to_5_price_id', true);
        $listing_6_to_20_price_id = get_post_meta($post->ID, 'listing_6_to_20_price_id', true);
        $listing_20_plus_price_id = get_post_meta($post->ID, 'listing_20_plus_price_id', true);
        $lock_price = get_post_meta($post->ID, 'lock_price', true);
        $shally_price = get_post_meta($post->ID, 'shally_price', true);
        $package_type = get_post_meta($post->ID, '_package_type', true);

        // Design layout with fieldset
        echo '<fieldset style="border: 1px solid #ddd; padding: 10px; margin-bottom: 15px;">';
        echo '<legend style="font-weight: bold; padding: 0 5px;">Package Details</legend>';


        // Price ID field
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="stripe_product_id" style="display: block; margin-bottom: 5px;">Stripe Product Id:</label>';
        echo '<input type="text" id="stripe_product_id" name="stripe_product_id" value="' . esc_attr($stripe_product_id) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="start_price_id" style="display: block; margin-bottom: 5px;">Start Price:</label>';
        echo '<input type="text" id="start_price_id" name="start_price_id" value="' . esc_attr($start_price_id) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        // Price ID field
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="listing_2_to_5_price_id" style="display: block; margin-bottom: 5px;">Listing 2 to 5 Price:</label>';
        echo '<input type="text" id="listing_2_to_5_price_id" name="listing_2_to_5_price_id" value="' . esc_attr($listing_2_to_5_price_id) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        // Price ID field
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="listing_6_to_20_price_id" style="display: block; margin-bottom: 5px;">Listing 6 to 20 Price:</label>';
        echo '<input type="text" id="listing_6_to_20_price_id" name="listing_6_to_20_price_id" value="' . esc_attr($listing_6_to_20_price_id) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        // Price ID field
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="listing_20_plus_price_id" style="display: block; margin-bottom: 5px;">Listing 20+ Price:</label>';
        echo '<input type="text" id="listing_20_plus_price_id" name="listing_20_plus_price_id" value="' . esc_attr($listing_20_plus_price_id) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="lock_price" style="display: block; margin-bottom: 5px;">Lock Price:</label>';
        echo '<input type="text" id="lock_price" name="lock_price" value="' . esc_attr($lock_price) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';
        
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="shally_price" style="display: block; margin-bottom: 5px;">Shally Price:</label>';
        echo '<input type="text" id="shally_price" name="shally_price" value="' . esc_attr($shally_price) . '" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />';
        echo '</div>';

        // Package Type field
        echo '<div style="margin-bottom: 10px;">';
        echo '<label for="package_type" style="display: block; margin-bottom: 5px;">Package Type:</label>';
        echo '<select id="package_type" name="package_type" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="stripe"' . selected($package_type, 'stripe', false) . '>Stripe</option>
              </select>';
        echo '</div>';

        echo '</fieldset>';
    }

    public function save_custom_fields($post_id) {
        if (!isset($_POST['stripe_packages_meta_box_nonce']) || !wp_verify_nonce($_POST['stripe_packages_meta_box_nonce'], 'stripe_packages_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['stripe_product_id'])) {
            update_post_meta($post_id, 'stripe_product_id', sanitize_text_field($_POST['stripe_product_id']));
        }
        if (isset($_POST['start_price_id'])) {
            update_post_meta($post_id, 'start_price_id', sanitize_text_field($_POST['start_price_id']));
        }

        if (isset($_POST['listing_2_to_5_price_id'])) {
            update_post_meta($post_id, 'listing_2_to_5_price_id', sanitize_text_field($_POST['listing_2_to_5_price_id']));
        }

        if (isset($_POST['listing_6_to_20_price_id'])) {
            update_post_meta($post_id, 'listing_6_to_20_price_id', sanitize_text_field($_POST['listing_6_to_20_price_id']));
        }

        if (isset($_POST['listing_20_plus_price_id'])) {
            update_post_meta($post_id, 'listing_20_plus_price_id', sanitize_text_field($_POST['listing_20_plus_price_id']));
        }
        if (isset($_POST['lock_price'])) {
            update_post_meta($post_id, 'lock_price', sanitize_text_field($_POST['lock_price']));
        }
        if (isset($_POST['shally_price'])) {
            update_post_meta($post_id, 'shally_price', sanitize_text_field($_POST['shally_price']));
        }

        // Save Package Type
        if (isset($_POST['package_type'])) {
            update_post_meta($post_id, '_package_type', sanitize_text_field($_POST['package_type']));
        }
    }

    public function add_custom_columns($columns) {
        $columns['package_price'] = __('Price', 'textdomain');
        return $columns;
    }

    public function populate_custom_columns($column, $post_id) {
        switch ($column) {
            case 'package_price':
                echo $price = get_post_meta($post_id, '_package_price', true);
                break;
        }
    }
}
