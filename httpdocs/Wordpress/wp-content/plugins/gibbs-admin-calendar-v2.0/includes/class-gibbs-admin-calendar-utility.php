<?php

class Gibbs_Admin_Calendar_Utility
{

    public static function get_language()
    {
        $current_language = get_locale();
        $current_language = str_replace("_", "-", $current_language);

        return $current_language;
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

        

        if (in_array("2", $roles) || in_array("3", $roles)) {
            $user_listings = $wpdb->get_results("SELECT id,post_title,post_parent FROM $listing_posts_table as p WHERE `post_type`='listing' AND ( `post_author` = $author_id $inUserGroupsSQL ) AND NOT EXISTS (SELECT * FROM $listing_posts_table as p_r WHERE p.id = p_r.post_parent AND `post_type`='listing')  order by post_title ASC");
        } else {
            $user_listings = $wpdb->get_results("SELECT id,post_title,post_parent FROM $listing_posts_table as p WHERE `post_type`='listing' AND ( `post_author` = $author_id ) AND NOT EXISTS (SELECT * FROM $listing_posts_table as p_r WHERE p.id = p_r.post_parent AND `post_type`='listing')  order by post_title ASC");
        }

        //1) Getting Muncipality Id of Current User
        $gym_resources = [];
        $gym_sections_resources = [];

        if ($cuser_id != 0) {
            //|| No longer need to load the Gym Sections as the Resources||//
            $group_ids = self::get_current_user_id_custom(get_current_user_id());
            $group_ids = is_array($group_ids) ? implode(",", $group_ids) : '';
            $gyms      = $wpdb->get_results("SELECT * from $gym_table WHERE `users_groups_id` in ($group_ids)");
            $gyms_sections   = $wpdb->get_results("SELECT * from $gym_section_table");

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

    public static function get_user_list($cal_type = "", $booking_data = array())
    {
        global $wpdb;
        $group_ids = self::get_current_user_id_custom(get_current_user_id());
        $cuser_id = get_current_user_id();
        $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';
        $users_table = $wpdb->prefix . 'users';
        $group_id = $value;

        $bookings_authorss = array();

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
            $booking_data = Gibbs_Admin_Calendar_API::get_booking_data("customer");

            

            foreach ($booking_data as $key => $booking_d) {
                $bookings_authorss[] = $booking_d->bookings_author;
            }

            $booking_table = $wpdb->prefix . 'bookings_calendar';
        }
        $bookings_authors_where = "";

        if(!empty($bookings_authorss)){
            $bookings_authorss = array_unique($bookings_authorss);

            $bookings_authors = implode(",",$bookings_authorss);

            $bookings_authors_where = " OR ID IN(".$bookings_authors.") ";
        }

        $sqll = "SELECT * FROM $users_table WHERE ID IN (select users_id from $users_and_users_groups_table where users_groups_id IN
                (Select id from ptn_users_groups where ID IN
                    (
                        SELECT id FROM ptn_users_groups WHERE id IN (
                            SELECT users_groups_id FROM ptn_users_and_users_groups WHERE users_id = $cuser_id
                        )
                    )
                )
            ) OR ID IN(select bookings_author from $booking_table WHERE owner_id= $cuser_id ) ".$bookings_authors_where." OR  ID IN(select id from $users_table WHERE user_login = 'stengt' )";

        

        $results = $wpdb->get_results($sqll);
      //  echo "<pre>"; print_r($results); die("sdjskd");

        return $results;
    }

    public static function send_mail_booking($booking_table, $booking_id, $status, $mail_status = false)
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
                    if($mail_status == false){

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
                    }

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
                        if($mail_status == false){

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
                    }
                    if ($booking_data['price'] == 0 || $booking_data['price'] == "") {
                        if($mail_status == false){
                            $mail_args = array(
                                'email'     => $user_info->user_email,
                                'booking'  => $booking_data,
                            );
                            do_action('listeo_mail_to_user_free_confirmed', $mail_args);
                        }    

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
                    if($mail_status == false){

                        $mail_args = array(
                            'email'         => $user_info->user_email,
                            'booking'       => $booking_data,
                            'expiration'    => $expiring_date,
                            'payment_url'   => $payment_url,
                            'order_id'   => $order->id,
                        );

                        do_action('listeo_mail_to_user_pay', $mail_args);
                    }
                    $order_id =  $order->id;
                    break;
                case 'paid':
                    if($mail_status == false){
                        $mail_to_owner_args = array(
                            'email'     => $owner_info->user_email,
                            'booking'  => $booking_data,
                        );
                        do_action('listeo_mail_to_owner_paid', $mail_to_owner_args);
                    }

                    break;
                /* case 'cancelled': */
                case 'cancelled':
                    if($mail_status == false){
                        $mail_to_user_args = array(
                            'email'     => $user_info->user_email,
                            'booking'  => $booking_data,
                        );
                        do_action('listeo_mail_to_user_canceled', $mail_to_user_args);
                    }

                    break;
            }
        }

        return $order_id;
    }
}
