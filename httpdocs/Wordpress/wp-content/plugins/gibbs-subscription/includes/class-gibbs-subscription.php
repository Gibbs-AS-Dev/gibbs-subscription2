<?php

class Class_Gibbs_Subscription 
{
    private $stripe;
    private $publishableKey;
    private $secretKey;
    private $stripe_webhook;
    private $stripe_custom_plan_product_id;

    public function action_init() {

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
         // Replace with your secret key
        $this->register_post_type();
        // Add actions
        add_action('wp_ajax_create_checkout_session', [$this, 'create_checkout_session']);
        add_action('wp_ajax_nopriv_create_checkout_session', [$this, 'create_checkout_session']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('rest_api_init', [$this, 'register_webhook']);
        add_action('wp_ajax_update_subscription', [$this, 'update_subscription']);
        add_action('wp_ajax_cancel_subscription', [$this, 'cancel_subscription']);
        add_shortcode('package_view', [$this, 'render_package_view']);
        add_shortcode('user_stripe_subscription', [$this, 'render_subscription_management']);

        add_shortcode('subscription_register', [$this, 'subscription_register']);
        add_shortcode('user-dashboard', [$this, 'user_dashboard_func']);

        add_action('wp_ajax_stripe_dashboard', [$this, 'stripe_dashboard']);
        add_action('wp_ajax_nopriv_stripe_dashboard', [$this, 'stripe_dashboard']);

        add_action('admin_menu', [new Class_Gibbs_Subscription_Admin, 'add_stripe_packages_submenu']);

        add_action( 'rest_api_init', function () {
            register_rest_route( 'v1', '/add_licence_id', array(
                'methods' => 'GET',
                'callback' => array( $this, 'add_licence_id' ),
            ) );
        } );
    }

    public function get_super_admin() {

        $current_user = wp_get_current_user();

        $active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

        global $wpdb;
        $users_groups = $wpdb->prefix . 'users_groups';  // table name
        $sql_user_group = "select * from `$users_groups` where id = ".$active_group_id; 
        $user_group_data = $wpdb->get_row($sql_user_group);

        if(isset($user_group_data->superadmin) && $user_group_data->superadmin > 0){
            return $user_group_data->superadmin;
        }
        return null;
       
    }

    public function update_group_licence($user_id,$status) {

        global $wpdb;
        $sql = "SELECT id, group_admin FROM ". $wpdb->prefix . "users_groups WHERE superadmin  = ".$user_id."";
        $groups = $wpdb->get_results($sql);

        $table_name = $wpdb->prefix."users_and_users_groups_licence";

        foreach($groups as $group){

            $sql2 = "SELECT id FROM ". $table_name . " where licence_id = 10 AND users_groups_id  = ".$group->id;
            $group_exist = $wpdb->get_row($sql2);
            if(isset($group_exist->id)){

                $where = array('id' => $group_exist->id);
                $data = array(
                    'licence_is_active' => $status,
                );
                $wpdb->update($table_name, $data, $where);

            }else{
                $data = array(
                    'users_groups_id' => $group->id,
                    'licence_id' => 10,
                    'licence_is_active' => $status,
                );
                $wpdb->insert($table_name, $data);
            }
            
            
        }
        $myfile = fopen(ABSPATH."/update_group_licence.txt", "w");

        $text2 = json_encode($wpdb);

        fwrite($myfile, $text2);

        fclose($myfile);
        return true;
       
    }

    public function add_licence_id(){
        // global $wpdb;
        // $data = array(
        //     'licence_id' => 10,
        //     'licence_is_active' => 0,
        // );
        // $sql = "
        //     UPDATE {$wpdb->prefix}users_and_users_groups_licence
        //     SET licence_id = %d, licence_is_active = %d
        // ";
        
        // $updated = $wpdb->query( $wpdb->prepare( $sql, $data['licence_id'], $data['licence_is_active'] ) );
    
        // echo "<pre>"; print_r($wpdb); die;
    }
    


    public function getLocks($user_id){
        global $wpdb;
        $sql = "SELECT count(*) FROM ". $wpdb->prefix . "access_management_match WHERE `owner_id` = ".$user_id." AND `provider`in ('igloohome','locky','unloc')";
        return $count_lock = $wpdb->get_var($sql);
    }
    public function getShally($user_id){
        global $wpdb;
        $sql = "SELECT count(*) FROM ". $wpdb->prefix . "access_management_match WHERE `owner_id` = ".$user_id." AND `provider`in ('shelly')";
        return $count_lock = $wpdb->get_var($sql);
    }

    public function update_price($user_id) {

        $package_id = get_user_meta($user_id, 'package_id', true);

        if($package_id != ""){

            $listing_count = $this->get_listing_count($user_id);

            $locks = $this->getLocks($user_id);
            $shelly = $this->getShally($user_id);
            
            $price_id = $this->getPriceId($package_id,$listing_count,$locks,$shelly);

            $license_status = get_user_meta($user_id, 'license_status', true);

            if($price_id != "" && $license_status == "active"){
                    $stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);

                    $subscriptions = $this->stripe->subscriptions->all(['customer' => $stripe_customer_id]);
                    if (count($subscriptions->data) > 0) {
                        // Update existing subscription
                        $subscription = $subscriptions->data[0]; // Get the first subscription (modify as needed)

                        $price_amount = $subscription->items->data[0]->price->unit_amount;

                        if($price_amount > 0){

                            $updated_subscription = $this->stripe->subscriptions->update($subscription->id, [
                                'items' => [[
                                    'id' => $subscription->items->data[0]->id,
                                    'price' => $price_id,
                                ]],
                            ]);

                        }


                    }

            }
        }    
    }
  
    
    public function register_post_type() {
        $args = [
            'labels'      => [
                'name'          => __('Stripe Packages', 'textdomain'),
                'singular_name' => __('Stripe Package', 'textdomain'),
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'rewrite' => ['slug' => 'stripe-packages'],
            'menu_position' => 6,
            'menu_icon' => 'dashicons-list-view',
        ];
        register_post_type('stripe-packages', $args);

        // Debug output
        error_log('Stripe Packages post type registered');
    }

    public function stripe_dashboard(){
        $user_id = $this->get_super_admin();

        $stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);

        $customerId = $stripe_customer_id;

        try {
            // Create a session for the customer portal
            $session = $this->stripe->billingPortal->sessions->create([
                'customer' => $customerId,
                'return_url' => home_url(), // URL to redirect to after the portal
            ]);
           if(isset($session->url)){
            wp_redirect($session->url);
            exit;
           }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle error from Stripe API
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        wp_redirect(home_url());
        exit;
    }
    public function get_listing_count($user_id) {

        $count_listing = 0;

        global $wpdb;
        $sql = "SELECT id, group_admin FROM ". $wpdb->prefix . "users_groups WHERE superadmin  = ".$user_id."";
        $groups = $wpdb->get_results($sql);

        foreach($groups as $group){
            $cr_cuser = $group->group_admin;
            $sql = "SELECT count(*) FROM ". $wpdb->prefix . "posts WHERE post_type = 'listing' AND `post_author` = ".$cr_cuser." AND `post_status` = 'publish'";
            $count_listing += $wpdb->get_var($sql);
        }
        return $count_listing;
       
    }
    

    public function user_dashboard_func(){
        

    
        ob_start();

        require GIBBS_STRIPE_PATH . 'user-dashboard.php'; 

        return ob_get_clean();

    }
    public function subscription_register($atts){

        $redirect = "";
        if(isset($atts["redirect"])){
            $redirect = $atts["redirect"];
        }

        ob_start();

        require GIBBS_STRIPE_PATH . 'get-started.php'; 

        return ob_get_clean();

    }

    



    public function remove_active_package($user_id){

        $stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);
        $active_subscription_price_id = null;

        $has_active = false;

        if ($stripe_customer_id) {
            try {
                $subscriptions = $this->stripe->subscriptions->all(['customer' => $stripe_customer_id]);
                if (count($subscriptions->data) > 0) {
                }else{
                    update_user_meta($user_id, 'license_status', "inactive");
                }
            } catch (Exception $e) {
                error_log('Error fetching subscriptions: ' . $e->getMessage());
            }
        }

        return $has_active;

    }

    public function render_package_view() {
        if(!$this->stripe){
         return;
        }
        if(!is_user_logged_in()){
            wp_redirect(home_url());
        }
        ob_start();
        require GIBBS_STRIPE_PATH . 'packages.php'; 
        return ob_get_clean();
        exit;
        
    }



    public function enqueue_scripts() {
        wp_enqueue_style('stripe-plugin-css', GIBBS_STRIPE_URL . '/css/styles.css');
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/');
        wp_enqueue_script('stripe-plugin-js', GIBBS_STRIPE_URL . '/js/stripe-plugin.js', ['jquery'], time(), true);

        wp_localize_script('stripe-plugin-js', 'stripePlugin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'publishableKey' => $this->publishableKey,
        ]);
    }

    public function create_checkout_session() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $package_id = $data['package_id']; 
            $user_id = $this->get_super_admin();
            $user_data = get_userdata($user_id);
            $stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);

            if(!$package_id){
                echo json_encode(['error' => "package id not found"]);
                wp_die();
            }

            $stripe_product_id = get_post_meta($package_id, 'stripe_product_id', true);
            $price = get_post_meta($package_id, 'start_price_id', true);

            if(!$price){
                echo json_encode(['error' => "price not found"]);
                wp_die();
            }
            $listing_count = $this->get_listing_count($user_id);

            $locks = $this->getLocks($user_id);
            $shelly = $this->getShally($user_id);

            $price_id = $this->getPriceId($package_id,$listing_count,$locks,$shelly);

            if(!$price_id){
                echo json_encode(['error' => "price_id not found"]);
                wp_die();
            }

            // Create Stripe customer if it doesn't exist
            if (!$stripe_customer_id) {
                try {
                    $customer = $this->stripe->customers->create([
                        'name' => $user_data->display_name,
                        'email' => $user_data->user_email,
                    ]);
                    update_user_meta($user_id, 'stripe_customer_id', $customer->id);
                    $stripe_customer_id = $customer->id;
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                    wp_die();
                }
            }

            try {

                $subscriptions = $this->stripe->subscriptions->all(['customer' => $stripe_customer_id]);
                if (count($subscriptions->data) > 0) {
                    // Update existing subscription
                    $subscription = $subscriptions->data[0]; // Get the first subscription (modify as needed)
                    $updated_subscription = $this->stripe->subscriptions->update($subscription->id, [
                        'items' => [[
                            'id' => $subscription->items->data[0]->id,
                            'price' => $price_id,
                        ]],
                    ]);

                    update_user_meta($user_id, 'package_id', $package_id);
                    // Store the updated subscription ID in the user meta
                    update_user_meta($user_id, 'subscription_id', $updated_subscription->id);
                    echo json_encode(['status' => 'success', 'subscription_id' => $updated_subscription->id]);
                } else {

                    $trail = get_user_meta($user_id, 'stripe_trail', true);

                    if($trail == "true"){
                      $sub_dataa = array();
                    }else{
                      $sub_dataa = [
                            'trial_settings' => ['end_behavior' => ['missing_payment_method' => 'cancel']],
                            'trial_period_days' => 30,
                      ];
                    }
                    // Create a new subscription
                    $session = $this->stripe->checkout->sessions->create([
                        'payment_method_types' => ['card'],
                        'mode' => 'subscription',
                        'customer' => $stripe_customer_id,
                        'line_items' => [[
                            'price' => $price_id,
                            'quantity' => 1,
                        ]],
                        'subscription_data' => $sub_dataa,
                        'payment_method_collection' => 'if_required',
                        'success_url' => home_url('/dashbord?success=true'), // URL to redirect on success
                        'cancel_url' => home_url('/dashboard'),   // URL to redirect on cancel
                        'locale' => 'nb',
                    ]);

                    update_user_meta($user_id, 'package_id', $package_id);
                    // No need to store the subscription ID here, it'll be done in the webhook
                    echo json_encode(['id' => $session->id]);
                }
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            wp_die();
        }
    }

    public function getPriceId($package_id,$listing_count,$locks,$shelly){
        
        

        $price = 0;

        $lock_price = get_post_meta($package_id,"lock_price",true);
        $shally_price = get_post_meta($package_id,"shally_price",true);
        $stripe_product_id = get_post_meta($package_id, 'stripe_product_id', true);

        if($listing_count >= 2 && $listing_count <= 5){
            $price = get_post_meta($package_id,"listing_2_to_5_price_id",true);
        }elseif($listing_count >= 6 && $listing_count <= 20){
            $price = get_post_meta($package_id,"listing_6_to_20_price_id",true);
        }elseif($listing_count >= 20){
            $price = get_post_meta($package_id,"listing_20_plus_price_id",true);
        }else{
            $price = get_post_meta($package_id,"start_price_id",true);
        }
       
        if($lock_price != ""){
            $lock_price = $lock_price * $locks;
            $price = $price + $lock_price;
        }
    
        if($shally_price != ""){
            $shally_price = $shally_price * $shelly;
            $price = $price + $shally_price;
        }
        $price_id = $this->getStripePriceId($price,$stripe_product_id); 

        if($price_id == ""){
            $price_id = $this->createStripePriceId($price,$stripe_product_id); 
        }
        return $price_id;

    }

    public function createStripePriceId($amount,$stripe_product_id){
        $price_id = "";
        try {
            $amount = $amount * 100;

            $priceData = [
                'unit_amount' => $amount,
                'currency' => "NOK",
                'product' => $stripe_product_id,
            ];

            $priceData['recurring'] = [
                'interval' => "month"
            ];

            // Create the price
            $price = $this->stripe->prices->create($priceData);

            if(isset($price->id)){
                $price_id = $price->id;
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
        }
        return $price_id;
        
    }
    public function getStripePriceId($amount,$stripe_product_id){
        try {
            $amount = $amount * 100;
            $prices = $this->stripe->prices->all(["product"=>$stripe_product_id]);

            $filteredPrices = array_filter($prices->data, function($price) use ($amount) {
                return $price->unit_amount == $amount;
            });

            $price_id = "";

            foreach($filteredPrices as $pricee){
                $price_id = $pricee->id;
            }

            return $price_id;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return "";
        }
        
    }

    public function register_webhook() {
        register_rest_route('stripe/v1', '/webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_stripe_webhook'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_stripe_webhook(WP_REST_Request $request) {
        $payload = $request->get_body();
        $sig_header = $request->get_header('Stripe-Signature');
        $endpoint_secret = $this->stripe_webhook; // Your webhook secret from Stripe

        try {
            $event =  \Stripe\Webhook::constructEvent(
                            $payload, $sig_header, $endpoint_secret
                        );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object; // Contains the session details

                    $myfile = fopen(ABSPATH."/customer_session_completed.txt", "w");

                    $text2 = json_encode($session);

                    fwrite($myfile, $text2);

                    fclose($myfile);

                    


                    // Check if the session has a subscription
                    if (isset($session->subscription)) {
                        $subscription_id = $session->subscription;
                        $customer_id = $session->customer;

                        // Retrieve the user by Stripe customer ID
                        $user = $this->get_user_by_stripe_customer_id($customer_id);
                        if ($user) {
                            
                            update_user_meta($user->ID, 'license_status', "active");
                            update_user_meta($user->ID, 'stripe_trail', "true");
                            update_user_meta($user->ID, 'subscription_id', $subscription_id);
                            $this->update_group_licence($user->ID,1);

                            
                        }
                    }
                    break;

                case 'customer.subscription.created':

                    $session = $event->data->object; 

                    $myfile = fopen(ABSPATH."/customer_subscription_created.txt", "w");

                    $text2 = json_encode($session);

                    fwrite($myfile, $text2);

                    fclose($myfile);


                    if (isset($session->customer)) {
                        $customer_id = $session->customer;

                        $user = $this->get_user_by_stripe_customer_id($customer_id);
                        if ($user) {

                            $subscriptions = $this->stripe->subscriptions->all(['customer' => $customer_id]);
                            if (count($subscriptions->data) > 0) {

                                    $subscription = $subscriptions->data[0]; 
                                    update_user_meta($user->ID, 'license_status', "active");
                                    update_user_meta($user->ID, 'stripe_trail', "true");
                                    update_user_meta($user->ID, 'subscription_id', $subscription->id);
                                    $this->update_group_licence($user->ID,1);

                                
                            }else{
                                update_user_meta($user->ID, 'license_status', "inactive");
                                update_user_meta($user->ID, 'subscription_id', "");
                                $this->update_group_licence($user->ID,0);
                            }
                        }
                        
                    }
                    break;
                case 'customer.subscription.updated':
                        $session = $event->data->object; 

                        $myfile = fopen(ABSPATH."/customer_subscription_updated.txt", "w");

                        $text2 = json_encode($session);

                        fwrite($myfile, $text2);

                        fclose($myfile);
    
    
                        if (isset($session->customer)) {
                            $customer_id = $session->customer;
    
                            $user = $this->get_user_by_stripe_customer_id($customer_id);
                            if ($user) {
    
                                $subscriptions = $this->stripe->subscriptions->all(['customer' => $customer_id]);
                                if (count($subscriptions->data) > 0) {
    
                                        $subscription = $subscriptions->data[0]; 
                                        update_user_meta($user->ID, 'license_status', "active");
                                        update_user_meta($user->ID, 'stripe_trail', "true");
                                        update_user_meta($user->ID, 'subscription_id', $subscription->id);
                                        $this->update_group_licence($user->ID,1);
    
                                    
                                }else{
                                    update_user_meta($user->ID, 'license_status', "inactive");
                                    update_user_meta($user->ID, 'subscription_id', "");
                                    $this->update_group_licence($user->ID,0);
                                }
                            }
                            
                        }
                        break;    

                case 'customer.subscription.deleted':
                    $session = $event->data->object; // Contains the session details

                    $myfile = fopen(ABSPATH."/customer_subscription_deleted.txt", "w");

                    $text2 = json_encode($session);

                    fwrite($myfile, $text2);

                    fclose($myfile);
                    


                    if (isset($session->customer)) {
                        $customer_id = $session->customer;

                        $user = $this->get_user_by_stripe_customer_id($customer_id);
                        if ($user) {

                            $subscriptions = $this->stripe->subscriptions->all(['customer' => $customer_id]);
                            if (count($subscriptions->data) > 0) {

                                    $subscription = $subscriptions->data[0]; 
                                    update_user_meta($user->ID, 'license_status', "active");
                                    update_user_meta($user->ID, 'stripe_trail', "true");
                                    update_user_meta($user->ID, 'subscription_id', $subscription->id);
                                    $this->update_group_licence($user->ID,1);

                                
                            }else{
                                update_user_meta($user->ID, 'license_status', "inactive");
                                update_user_meta($user->ID, 'subscription_id', "");
                                $this->update_group_licence($user->ID,0);
                            }
                        }
                        
                    }
                    break;

                // Add more event types as needed
            }
        } catch (Exception $e) {
            error_log('Webhook error: ' . $e->getMessage());
            return new WP_Error('invalid_webhook', 'Invalid webhook signature', ['status' => 400]);
        }

        return new WP_REST_Response(['status' => 'success'], 200);
    }

    // Helper function to get user by Stripe customer ID
    private function get_user_by_stripe_customer_id($stripe_customer_id) {
        $args = [
            'meta_key' => 'stripe_customer_id',
            'meta_value' => $stripe_customer_id,
            'number' => 1,
        ];
        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();
        
        return !empty($users) ? $users[0] : null;
    }



    public function cancel_subscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $this->get_super_admin();
            $data = json_decode(file_get_contents('php://input'), true);
            $subscription_id = $data['subscription_id'];

            try {
                // Retrieve the subscription and cancel it
                $subscription = $this->stripe->subscriptions->retrieve($subscription_id);
                $subscription->cancel();
                update_user_meta($user_id, 'subscription_id', "");
                update_user_meta($user_id, 'license_status', "inactive");

                echo json_encode(['status' => 'success', 'message' => 'Subscription canceled']);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            wp_die();
        }
    }

    public function render_subscription_management() {
        ob_start();
        require GIBBS_STRIPE_PATH . 'active-packages.php'; 
        return ob_get_clean();
        exit;
    }
}
