<?php

class Gibbs_Season_Calendar_Utility
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

   
    public static function get_user_list($cal_type)
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

        $results = $wpdb->get_results(
            "SELECT * FROM $users_table WHERE ID IN (select users_id from $users_and_users_groups_table where users_groups_id IN
                (Select id from ptn_users_groups where ID IN
                    (
                        SELECT id FROM ptn_users_groups WHERE id IN (
                            SELECT users_groups_id FROM ptn_users_and_users_groups WHERE users_id = $cuser_id
                        )
                    )
                )
            ) OR ID IN(select bookings_author from $booking_table WHERE owner_id= $cuser_id ) OR  ID IN(select id from $users_table WHERE user_login = 'stengt' )"
        );

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
                        'payment_url'   => $payment_url
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
}
