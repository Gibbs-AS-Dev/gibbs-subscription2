<?php

class Gibbs_Common_Calendar
{

    public static function get_language()
    {
        $current_language = get_locale();
        $current_language = str_replace("_", "-", $current_language);

        return $current_language;
    }
    public static function is_group_active()
    {

        $active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

        if($active_group_id != ""){
            return true;
        }else{
            return false;
        }

    }

    public static function get_current_user_id_custom()
    {
        global $wpdb;
        $users_table = $wpdb->prefix . 'users_and_users_groups';
        if ($cuser_id) {
            $group_id_data = $wpdb->get_results("SELECT users_groups_id FROM $users_table WHERE `users_id`=$cuser_id");
            $group_ids = array();
            foreach ($group_id_data as $key => $group_id) {
                $group_ids[] = $group_id->users_groups_id;
            }
            return $group_ids;
        } else {
            return 5;
        }
    }
    public static function get_current_admin_user()
    {
        $cr_user = get_current_user_id();

        $group_admin = get_group_admin();

        if($group_admin != ""){

            $cr_user = $group_admin;
        }
        return $cr_user;
    }

    /**
     * Loading Gyms for current user depending on its muncipality_id
     * 
     * Required things 
     *      1) municipality_id in the ptn_users table 
     *      2) gym and gym_section table
     * 
     * @return array $gym_resources
     * 
     */
    public static function get_gym_resources($listings = [])
    {
        global $wpdb;

        $gym_table   = 'gym';
        $gym_section_table = 'gym_section';
        $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';
        $listing_posts_table = $wpdb->prefix . 'posts';

        $admin_user_id = self::get_current_admin_user();

        $author_id = $admin_user_id;

        $cuser_id = 0;
        if (is_user_logged_in() == true) {
            $cuser_id = $admin_user_id;

            $ajax_data["is_user_login"] = true;
        } else {
            $ajax_data["is_user_login"] = false;
            //Temp for development
            $cuser_id = 1;
        }

        $sqlll = "select * from $users_and_users_groups_table where users_id =" . $admin_user_id;

        $roless_data = $wpdb->get_results($sqlll);

        $roles = array();

        foreach ($roless_data as $key => $roless_da) {
            $roles[] = $roless_da->role;
        }

        $listing_query = "";


        if(isset($_GET["listings"]) && $_GET["listings"] != ""){

            $post_author = "";
            $listingss = $_GET["listings"];
            $listing_query = " AND p.ID in ($listingss)";
                
        }else{
            $post_author = " AND ( `post_author` = $author_id )";
        }


        

        

        if (in_array("2", $roles) || in_array("3", $roles)) {
            $user_listings = $wpdb->get_results("SELECT id,post_title,post_parent,post_author FROM $listing_posts_table as p WHERE `post_type`='listing' AND post_status != 'trash' $post_author AND NOT EXISTS (SELECT * FROM $listing_posts_table as p_r WHERE p.id = p_r.post_parent AND `post_type`='listing') $listing_query order by post_title ASC");
        } else {
            

            $user_listings = $wpdb->get_results("SELECT id,post_title,post_parent,post_author FROM $listing_posts_table as p WHERE `post_type`='listing'  AND post_status != 'trash' $post_author AND NOT EXISTS (SELECT * FROM $listing_posts_table as p_r WHERE p.id = p_r.post_parent AND `post_type`='listing') $listing_query order by post_title ASC");
        }
        //$user_listings = array();


        //1) Getting Muncipality Id of Current User
        $gym_resources = [];
        $gym_sections_resources = [];

        if ($cuser_id != 0) {
            //|| No longer need to load the Gym Sections as the Resources||//
            $group_ids = self::get_current_user_id_custom(get_current_user_id());
            $group_ids = is_array($group_ids) ? implode(",", $group_ids) : '';
            if($group_ids != ""){
                $gyms      = $wpdb->get_results("SELECT * from $gym_table WHERE `users_groups_id` in ($group_ids)");
            }else{
                $gyms = array();
            }
            
            //$gyms_sections   = $wpdb->get_results("SELECT * from $gym_section_table");
            $gyms_sections   = array();

            foreach ($gyms as $gym) {
                $color = "orange";
                $gym_resources[] = array(
                    'text'    => $gym->name,
                    'value'   => $gym->id,
                    'id'   => $gym->id,
                    'color'   => $color,
                );
            }

            foreach ($gyms_sections as $gym_section) {
                foreach ($gyms as $gym) {
                    if ($gym_section->gym_id == $gym->id) {
                        $gym_sections_resources[] = array(
                            'text' => $gym_section->name,
                            'value' => $gym_section->id,
                            'gym_id' => $gym_section->gym_id,
                        );
                    }
                }
            }

            $user_listing_resources = [];

            foreach ($user_listings as $listing) {
                if (!empty($listings) && !in_array($listing->id, $listings)) continue;
                $post_meta_table = $wpdb->prefix . "postmeta";
                $week_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $working_hours_arr1 = [
                    'monday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'tuesday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'wednesday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'thursday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'friday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'saturday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                    'sunday' => [
                        'start' => null,
                        'end' => null,
                        'startBreak' => null,
                        'endBreak' => null,
                    ],
                ];

                $whr_slots = $wpdb->get_results("SELECT * FROM $post_meta_table WHERE post_id = $listing->id AND (meta_key = '_slots')");

                if (!empty($whr_slots)) {

                    foreach ($whr_slots as $key_slot => $item_slots) {
                        $slotsss = json_decode($item_slots->meta_value);
                        if (!empty($slotsss)) {
                            if (isset($slotsss[0]) && !empty($slotsss[0])) {

                                if (isset($slotsss[0][0])) {
                                    $explode_monday = explode("-", $slotsss[0][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['monday']['start'] = $start_hour;
                                    $working_hours_arr1['monday']['end'] = $end_hour;
                                }
                            }
                        }
                        if (!empty($slotsss)) {
                            if (isset($slotsss[1]) && !empty($slotsss[1])) {

                                if (isset($slotsss[1][0])) {
                                    $explode_monday = explode("-", $slotsss[1][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['tuesday']['start'] = $start_hour;
                                    $working_hours_arr1['tuesday']['end'] = $end_hour;
                                }
                            }
                        }

                        if (!empty($slotsss)) {
                            if (isset($slotsss[2]) && !empty($slotsss[2])) {

                                if (isset($slotsss[2][0])) {
                                    $explode_monday = explode("-", $slotsss[2][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['wednesday']['start'] = $start_hour;
                                    $working_hours_arr1['wednesday']['end'] = $end_hour;
                                }
                            }
                        }

                        if (!empty($slotsss)) {
                            if (isset($slotsss[3]) && !empty($slotsss[3])) {

                                if (isset($slotsss[3][0])) {
                                    $explode_monday = explode("-", $slotsss[3][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['thursday']['start'] = $start_hour;
                                    $working_hours_arr1['thursday']['end'] = $end_hour;
                                }
                            }
                        }

                        if (!empty($slotsss)) {
                            if (isset($slotsss[4]) && !empty($slotsss[4])) {

                                if (isset($slotsss[4][0])) {
                                    $explode_monday = explode("-", $slotsss[4][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['friday']['start'] = $start_hour;
                                    $working_hours_arr1['friday']['end'] = $end_hour;
                                }
                            }
                        }

                        if (!empty($slotsss)) {
                            if (isset($slotsss[5]) && !empty($slotsss[5])) {

                                if (isset($slotsss[5][0])) {
                                    $explode_monday = explode("-", $slotsss[5][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['saturday']['start'] = $start_hour;
                                    $working_hours_arr1['saturday']['end'] = $end_hour;
                                }
                            }
                        }

                        if (!empty($slotsss)) {
                            if (isset($slotsss[6]) && !empty($slotsss[6])) {

                                if (isset($slotsss[6][0])) {
                                    $explode_monday = explode("-", $slotsss[6][0]);
                                    $start_hour = "";
                                    $end_hour = "";
                                    if (isset($explode_monday[0])) {
                                        $start_hour = trim(str_replace(" ", "", $explode_monday[0]));
                                    }
                                    if (isset($explode_monday[1])) {
                                        $end_hoursss = explode("|", $explode_monday[1]);

                                        $end_hour = trim(str_replace(" ", "", $end_hoursss[0]));
                                    }
                                    $working_hours_arr1['sunday']['start'] = $start_hour;
                                    $working_hours_arr1['sunday']['end'] = $end_hour;
                                }
                            }
                        }
                    }

                    $hourss = $working_hours_arr1;
                } else {
                    $hourss = $working_hours_arr1;
                }
                $listing_sports_data = get_post_meta($listing->id, '_listing_sports');
                // write_log(array('sports_data' => $listing_sports_data));
                $sports = array();
                if (!empty($listing_sports_data)) {

                    foreach ($listing_sports_data as $key => $sp) {

                        $sport_table = 'sport';

                        $query = "SELECT id,name FROM $sport_table WHERE id = $sp";
                        $sport_id_data = $wpdb->get_row($query);
                        if (isset($sport_id_data->name)) {
                            $sports[] = $sport_id_data->name;
                        }
                    }
                }
                if (!empty($sports)) {
                    sort($sports);
                    $sports = "\t(" . implode(", ", $sports) . ")";
                } else {
                    $sports = "";
                }

                $post_title_full = $listing->post_title;
                $post_title = mb_strimwidth($listing->post_title, 0, 30, '...');
                if ($post_title != '')
                    $user_listing_resources[] = array(
                        'name' => $post_title,
                        'text' => $post_title,
                        'full_text' => $post_title_full,
                        'value' => $listing->id,
                        'id' => $listing->id,
                        'owner_id' => $listing->post_author,
                        'gym_id' => 2,
                        'workingHours' => $hourss,
                        'sports' => $sports,
                    );
            }
        }

        $listings_working_hours_raw = array();

        return array(
            'gyms'          => $gym_resources,
            'gym_sections'  => $gym_sections_resources,
            "listings"      => $user_listing_resources,
            'workingHours'  => $listings_working_hours_raw,
            'sample_data_next_id'   => 0
        );
    }

    public static function get_user_list($cal_type, $listings = array())
    {
        global $wpdb;
        $group_ids = self::get_current_user_id_custom(get_current_user_id());
        $cuser_id = get_current_user_id();
        $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';
        $users_table = $wpdb->prefix . 'users';
        $group_id = $value;

        if ($cal_type == "view_only") {
            $cal_view = get_user_meta(get_current_user_ID(), "cal_view", true);
            if (!$cal_view) {
                $cal_view = "furespurte";
            }
            wp_localize_script('custom-script', 'cal_view', $cal_view);
            if ($cal_view == "algoritme") {
                $booking_table = $wpdb->prefix . 'bookings_calendar_raw_algorithm';
            } elseif ($cal_view == "manuelle") {
                $booking_table = $wpdb->prefix . 'bookings_calendar_raw_approved';
            } else {
                $booking_table = $wpdb->prefix . 'bookings_calendar_raw';
            }
        } else {
            $booking_table = $wpdb->prefix . 'bookings_calendar';
        }

        $listing_ids = array();

        if(!empty($listings)){
            foreach ($listings as $listingssss2) {
                $listing_ids[] = $listingssss2["id"];
            }
        }else{
            $listing_ids[] = 840989584958045845; 
        }
        $listing_ids = implode(',', $listing_ids);




        $admin_user_id = self::get_current_admin_user(); 

        //echo get_current_user_id(); die;
        
         $group_datas =  $wpdb->get_results(
             "select users_groups_id from $users_and_users_groups_table where role in (2,3) AND users_id = ".get_current_user_id() 
         );
 
         //echo "<pre>"; print_r($group_datas); die;
 
 
         // $active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );
 
         if(!empty($group_datas)){
            $group_ids = array();
    
            foreach($group_datas as $group_data){
                $group_ids[] = $group_data->users_groups_id;
            }
    
            $group_ids = implode(",",$group_ids);
            // echo "<pre>"; print_r($group_ids); die;
            $group_sql = "select users_id from $users_and_users_groups_table where users_groups_id IN (".$group_ids.")";
         }else{
            $group_sql = "select users_id from $users_and_users_groups_table where users_groups_id IN
            (Select id from ptn_users_groups where ID IN
                (
                    SELECT id FROM ptn_users_groups WHERE id IN (
                        SELECT users_groups_id FROM ptn_users_and_users_groups WHERE users_id = $cuser_id
                    )
                )
            )";
         }
 
 
 
 
         $results = $wpdb->get_results(
            "SELECT * FROM $users_table WHERE ID IN ($group_sql) OR ID IN(select bookings_author from $booking_table WHERE owner_id= $cuser_id ) OR ID IN(select bookings_author from $booking_table WHERE listing_id IN ($listing_ids) ) OR  ID IN(select id from $users_table WHERE user_login = 'stengt' )"
         );

       //echo "<pre>";print_r($wpdb); die;

        return $results;
    }

    public static function send_mail_booking($booking_table, $booking_id, $status)
    {
        global $wpdb;
        $order_id = "";
        $booking_data = $wpdb->get_row('SELECT * FROM `'  . $booking_table . '` WHERE `id`=' . esc_sql($booking_id), 'ARRAY_A');
        if ($booking_data) {

            $user_id = $booking_data['bookings_author'];
            $owner_id = $booking_data['owner_id'];
            $startDate = $booking_data['date_start'];
            $current_user_id = get_current_user_id();

            $user_info = get_userdata($user_id);

            $owner_info = get_userdata($owner_id);
            $comment = json_decode($booking_data['comment']);
            switch ($status) {
                case 'waiting':

                    $mail_to_user_args = array(
                        'email' => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_waiting_approval', $mail_to_user_args);

                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                    );

                    do_action('listeo_mail_to_owner_new_reservation', $mail_to_owner_args);

                    break;
                case 'confirmed':

                    $product_id = get_post_meta($booking_data['listing_id'], 'product_id', true);

                    $expired_after = get_post_meta($booking_data['listing_id'], '_expired_after', true);
                    if (empty($expired_after)) {
                        $expired_after = 48;
                    }
                    if (!empty($expired_after) && $expired_after > 0) {
                        $expiring_date = date("Y-m-d H:i:s", strtotime('+' . $expired_after . ' hours'));
                    }

                    $instant_booking = get_post_meta($booking_data['listing_id'], '_instant_booking', true);

                    if ($instant_booking) {

                        $mail_to_user_args = array(
                            'email' => $user_info->user_email,
                            'booking'  => $booking_data,
                        );
                        do_action('listeo_mail_to_user_instant_approval', $mail_to_user_args);
                        $mail_to_owner_args = array(
                            'email'     => $owner_info->user_email,
                            'booking'  => $booking_data,
                        );
                        do_action('listeo_mail_to_owner_new_intant_reservation', $mail_to_owner_args);
                    }
                    if ($booking_data['price'] == 0 || $booking_data['price'] == "") {
                        $mail_args = array(
                            'email'     => $user_info->user_email,
                            'booking'  => $booking_data,
                        );
                        do_action('listeo_mail_to_user_free_confirmed', $mail_args);

                        break;
                    }
                    $first_name = (isset($comment->first_name) && !empty($comment->first_name)) ? $comment->first_name : get_user_meta($user_id, "billing_first_name", true);

                    $last_name = (isset($comment->last_name) && !empty($comment->last_name)) ? $comment->last_name : get_user_meta($user_id, "billing_last_name", true);

                    $phone = (isset($comment->phone) && !empty($comment->phone)) ? $comment->phone : get_user_meta($user_id, "billing_phone", true);

                    $email = (isset($comment->email) && !empty($comment->email)) ? $comment->email : get_user_meta($user_id, "user_email", true);

                    $billing_address_1 = (isset($comment->billing_address_1) && !empty($comment->billing_address_1)) ? $comment->billing_address_1 : '';

                    $billing_city = (isset($comment->billing_city) && !empty($comment->billing_city)) ? $comment->billing_city : '';

                    $billing_postcode = (isset($comment->billing_postcode) && !empty($comment->billing_postcode)) ? $comment->billing_postcode : '';

                    $billing_country = (isset($comment->billing_country) && !empty($comment->billing_country)) ? $comment->billing_country : '';

                    $address = array(
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'address_1' => $billing_address_1,
                        'city' => $billing_city,
                        'postcode'  => $billing_postcode,
                        'country'   => $billing_country,
                    );

                    $order = wc_create_order();

                    $args['totals']['subtotal'] = $booking_data['price'];
                    $args['totals']['total'] = $booking_data['price'];

                    $comment = json_decode($booking_data['comment']);

                    $order->add_product(wc_get_product($product_id), 1, $args);
                    $order->set_address($address, 'billing');
                    $order->set_address($address, 'shipping');
                    $order->set_customer_id($user_id);
                    $order->set_billing_email($email);
                    if (isset($expiring_date)) {
                        $order->set_date_paid(strtotime($expiring_date));
                    }

                    $payment_url = $order->get_checkout_payment_url();

                    $order->calculate_totals();
                    $order->save();

                    $order->update_meta_data('booking_id', $booking_id);
                    $order->update_meta_data('owner_id', $owner_id);
                    $order->update_meta_data('listing_id', $booking_data['listing_id']);
                    if (isset($comment->service)) {

                        $order->update_meta_data('listeo_services', $comment->service);
                    }

                    $order->save_meta_data();

                    $mail_args = array(
                        'email'         => $user_info->user_email,
                        'booking'       => $booking_data,
                        'expiration'    => $expiring_date,
                        'payment_url'   => $payment_url,
                        'order_id'   => $order->id,
                    );

                    do_action('listeo_mail_to_user_pay', $mail_args);
                    $order_id =  $order->id;
                    break;
                case 'paid':
                    $mail_to_owner_args = array(
                        'email'     => $owner_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_owner_paid', $mail_to_owner_args);

                    break;
                /* case 'canceled': */
                case 'cancelled':
                    $mail_to_user_args = array(
                        'email'     => $user_info->user_email,
                        'booking'  => $booking_data,
                    );
                    do_action('listeo_mail_to_user_canceled', $mail_to_user_args);

                    break;
            }
        }

        return $order_id;
    }
    public function get_selected_template_data($template_selected = ""){

        global $wpdb;



        $data = array();

        $data["cal_start_day"] = "1";
        $data["cal_end_day"] = "0";
        $data["cal_starttime"] = "06:00";
        $data["cal_endtime"] = "23:00";
        $data["cal_time_cell_step"] = "60";
        $data["cal_time_label_step"] = "60";
        $data["cal_show_week_nos"] = "true";
        $data["show_bk_payment_failed"] = "false";
        $data["show_bk_pay_to_confirm"] = "false";
        $data["cal_show_daily_summery_weak"] = "false";
        $data["filter_location"] = null;
        $data["change_location_single_booking"] = "";
        $data["change_location_grouped_booking"] = "";
        $data["algo_time"] = "";
        $data["algo_move_booking"] = "";
        $data["algo_optimalization"] = "";
        $data["additional_info"] = "";
        $data["show_admin_icons"] = "";
        $data["show_fields_info"] = "";
        $data["show_rejected"] = "";



        if($template_selected && $template_selected != ""){
            $filter_template_table = "filter_template";

            $selected_filter_template_sql = "SELECT * from $filter_template_table where id = $template_selected";
            $selected_filter_template_data = $wpdb->get_row($selected_filter_template_sql);

            if(isset($selected_filter_template_data->id)){

                if($selected_filter_template_data->json_data != "" && $selected_filter_template_data->json_data != null){
                    $selected_temp_data = json_decode($selected_filter_template_data->json_data);


                    $data["cal_start_day"] = $selected_temp_data->cal_start_day;
                    $data["cal_end_day"] = $selected_temp_data->cal_end_day;
                    $data["cal_starttime"] = $selected_temp_data->cal_starttime;
                    $data["cal_endtime"] = $selected_temp_data->cal_endtime;
                    $data["cal_time_cell_step"] = $selected_temp_data->cal_time_cell_step;
                    $data["cal_time_label_step"] = $selected_temp_data->cal_time_label_step;
                    $data["cal_show_week_nos"] = $selected_temp_data->cal_show_week_nos;
                    // $data["show_bk_payment_failed"] = $selected_temp_data->show_bk_payment_failed;
                    // $data["show_bk_pay_to_confirm"] = $selected_temp_data->show_bk_pay_to_confirm;
                    $data["cal_show_daily_summery_weak"] = $selected_temp_data->cal_show_daily_summery_weak;
                    $data["filter_location"] = $selected_temp_data->filter_location;
                    $data["calendar_view"] = $selected_temp_data->calendar_view;

                    if(isset($selected_temp_data->show_bk_payment_failed)){
                        $data["show_bk_payment_failed"] = $selected_temp_data->show_bk_payment_failed;
                    }
                    if(isset($selected_temp_data->show_bk_pay_to_confirm)){
                        $data["show_bk_pay_to_confirm"] = $selected_temp_data->show_bk_pay_to_confirm;
                    }

                    if(isset($selected_temp_data->change_location_single_booking)){
                        $data["change_location_single_booking"] = $selected_temp_data->change_location_single_booking;
                    }
                    if(isset($selected_temp_data->change_location_grouped_booking)){
                        $data["change_location_grouped_booking"] = $selected_temp_data->change_location_grouped_booking;
                    }
                    if(isset($selected_temp_data->algo_time)){
                        $data["algo_time"] = $selected_temp_data->algo_time;
                    }
                    if(isset($selected_temp_data->algo_move_booking)){
                        $data["algo_move_booking"] = $selected_temp_data->algo_move_booking;
                    }
                    if(isset($selected_temp_data->change_location_single_booking)){
                        $data["algo_optimalization"] = $selected_temp_data->algo_optimalization;
                    }
                    if(isset($selected_temp_data->additional_info)){
                        $data["additional_info"] = $selected_temp_data->additional_info;
                    }
                    if(isset($selected_temp_data->show_admin_icons)){
                        $data["show_admin_icons"] = $selected_temp_data->show_admin_icons;
                    }
                    if(isset($selected_temp_data->show_fields_info)){
                        $data["show_fields_info"] = $selected_temp_data->show_fields_info;
                    }
                    if(isset($selected_temp_data->show_rejected)){
                        $data["show_rejected"] = $selected_temp_data->show_rejected;
                    }

                }

            }

        }

       // echo "<pre>"; print_r($data); die;

        return $data;

    }
    public static function get_translations($language)
    {

        $gibbs = [
            "week" => __("Week- ", "Gibbs"),
            "Location" => __("Location", "Gibbs"),
            "Passer for" => __("Passer for", "Gibbs"),
            "USE" => __("USE", "Gibbs"),
            "Club/Client" => __("Club/Client", "Gibbs"),
            "No teams found for selected user" => __("No teams found for selected user", "Gibbs"),
            "No Reservation Found!" => __("No Reservation Found!", "Gibbs"),
            "Username" => __("Username", "Gibbs"),
            "FirstName" => __("FirstName", "Gibbs"),
            "LastName" => __("LastName", "Gibbs"),
            "Description" => __("Description", "Gibbs"),
            "Email" => __("Email", "Gibbs"),
            "Phone" => __("Phone", "Gibbs"),
            "Filter" => __("Filter", "Gibbs"),
            "Select Location" => __("Select Location", "Gibbs"),
            "Setting" => __("Settings", "Gibbs"),
            "Calendar Setting" => __("Calendar Setting", "Gibbs"),
            "View calendar from" => __("Vis kalender fra ", "Gibbs"),
            "View calendar to" => __("Vis kalender til ", "Gibbs"),
            "View calendar from 00:00 - 23:59" => __("Vis kalender fra 00:00-23:59 ", "Gibbs"),
            "Start" => __("Start", "Gibbs"),
            "Select" => __("Select", "Gibbs"),
            "End" => __("End", "Gibbs"),
            "Show full day" => __("Show full day", "Gibbs"),
            "Export to PDF" => __("Export to PDF", "Gibbs"),
            "Search" => __("Search", "Gibbs"),
            "Client" => __("Client", "Gibbs"),
            "Team" => __("Team", "Gibbs"),
            "Comment" => __("Comment", "Gibbs"),
            "Repeat" => __("Repeat", "Gibbs"),
            "Related booking" => __("Related booking", "Gibbs"),
            "Message" => __("Message", "Gibbs"),
            "Booking details" => __("Booking details", "Gibbs"),
            "Customer info" => __("Customer info", "Gibbs"),
            "Client Name" => __("Client Name", "Gibbs"),
            "Date" => __("Date", "Gibbs"),
            "Time" => __("Time", "Gibbs"),
            "ALL RESERVATION" => __("ALL RESERVATION", "Gibbs"),
            "CLIENT INFO" => __("CLIENT INFO", "Gibbs"),
            "Client Name" => __("Client Name", "Gibbs"),
            "Date" => __("Date", "Gibbs"),
            "Time" => __("Time", "Gibbs"),
            "Select Group" => __("Select Group", "Select Group"),
            "Listing" => __("Listing", "Gibbs"),
            "Status" => __("Status", "Gibbs"),
            "Action" => __("Action", "Gibbs"),
            "No Data" => __("No Data", "Gibbs"),
            "No Data Found in Team" => __("No Data Found in Team", "Gibbs"),
            "loading" => __("Loading", "Gibbs"),
            "No Title" => __("No Title", "Gibbs"),
            "Fra" => __("Fra", "Gibbs"),
            "Till" => __("Till", "Gibbs"),
            "Group" => __("Group", "Group"),
            "Teams" => __("Teams", "Gibbs"),
            "export_in_csv" => __("Eksporter til csv", "Gibbs"),
            "export_in_database" => __("Til admin kalender", "Gibbs"),
            "select_export_option" => __("Eksporter", "Gibbs"),
            "Velg" => __("Velg", "Gibbs"),
            "cell_width" => __("Cellebredde ", "Gibbs"),
            "timeline_cell_width" => __("Timeline Cell width", "Gibbs"),
            "timeline_week_cell_width" => __("Timeline week Cell width", "Gibbs"),
            "small" => __("Liten", "Gibbs"),
            "medium" => __("Medium", "Gibbs"),
            "big" => __("Stor", "Gibbs"),
            "show_extra_info" => __("Nøkkelinfo ", "Gibbs"),
            "age_group" => __("Age group", "Gibbs"),
            "level" => __("Level", "Gibbs"),
            "type" => __("Type", "Gibbs"),
            "sport" => __("Sport", "Gibbs"),
            "members" => __("Members", "Gibbs"),
            "team_name" => __("Team name", "Gibbs"),
        ];

        return $gibbs;
    }

}
