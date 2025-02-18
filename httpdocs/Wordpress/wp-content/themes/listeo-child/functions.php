<?php
define('GIBBS_VERSION', '4.3.59');
require get_stylesheet_directory() . '/vendor/autoload.php';
require get_stylesheet_directory() . '/scripts.php';
use Jumbojett\OpenIDConnectClient;
session_start();
if(!class_exists('wc_create_order')){
    include_once(ABSPATH."/wp-content/plugins/woocommerce/woocommerce.php");
}
//Alen
function halfHourTimes($format)
{
    $formatter = function ($time) use ($format) {
        return date($format, $time);
    };
    $halfHourSteps = range(0, 47 * 1800, 1800);
    return array_map($formatter, $halfHourSteps);
}

add_action('wp_enqueue_scripts', 'listeo_enqueue_styles');
function listeo_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array('bootstrap', 'listeo-iconsmind', 'listeo-woocommerce'));
    wp_enqueue_style('listeocore-child-style', get_stylesheet_directory_uri() . '/assets/css/custom.css?l1d1', array('parent-style'));
    wp_enqueue_script('listeocore-child-script', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('listeo-custom'));
    wp_enqueue_script('timer-script', get_stylesheet_directory_uri() . '/assets/js/timer.js', array(),time(),true);

    wp_localize_script('timer-script', 'myAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}

add_action('wp_enqueue_scripts', 'listeo_enqueue_scripts',101);
function listeo_enqueue_scripts()
{
    //wp_dequeue_script('listeo-custom');
    wp_enqueue_script( 'chosen-min', get_template_directory_uri() . '/js/chosen.min.js', array( 'jquery' ));
    /* wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js', array( 'jquery' ));
    wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js', array( 'jquery' )); */
    wp_enqueue_script('listeo-custom', get_stylesheet_directory_uri() . '/assets/js/parent-custom.js', array('jquery'), '20170821', true);
    wp_enqueue_script('listeocore-child-script', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('listeo-custom'));
  //  wp_enqueue_script('listeocore-custom-script', get_template_directory_uri() . '/js/custom.js', array('listeo-custom'));
    wp_enqueue_script('listeocore-group-custom-fe-script', get_stylesheet_directory_uri() . '/assets/js/group-custom-fe.js', array('listeo-custom'));
    global $wp_scripts;
    $version = GIBBS_VERSION;
    
    /** @var _WP_Dependency $regScript */
    foreach ( $wp_scripts->registered as &$regScript ) {
        $regScript->ver = $version;
    }
    global $wp_styles;
    
    /** @var _WP_Dependency $regScript */
    foreach ( $wp_styles->registered as &$wp_style ) {
        $wp_style->ver = $version;
    }

}


function remove_parent_theme_features()
{

}
add_action('after_setup_theme', 'remove_parent_theme_features', 10);

/**
 * Return custom field for categories, datetime, capactiy and price.
 */
add_filter('listeo_core_search_fields_half', function ($fields) {
    $fields = array_map(function ($field) {
        if (false === strpos($field['css_class'], 'mygibb')) {
            return $field;
        }

        if ('tax-listing_category' === $field['name']) {
            $field['type'] = 'mygibb-categories';
        } elseif ('_event_date' === $field['name']) {
            $field['type'] = 'mygibb-datetime';
        } elseif ('_rooms' === $field['name']) {
            $field['type'] = 'mygibb-capacity';
        } elseif ('_price' === $field['name']) {
            $field['type'] = 'mygibb-price';
        } elseif ('tax-region' === $field['name']) {
            $field['type'] = 'mygibb-location';
        }

        return $field;

    }, $fields);

    return $fields;
});

/**
 * Modify the WP_Query to include the capacity.
 */
add_filter('realto_get_listings', function ($query_args, $args) {
    foreach ($query_args as $key => $query_arg) {
        if ('meta_query' === $key) {
            foreach ($query_arg as $index => $meta) {
                if ('_rooms' === $meta['key']) {
                    $value = explode(',', $meta['value']);
                    $value = array_map(function ($v) {
                        return absint($v);
                    }, $value);

                    $room_query = array(
                        'relation' => 'OR',
                        array(
                            'key' => '_rooms',
                            'value' => $value,
                            'compare' => 'BETWEEN',
                            'type' => 'NUMERIC',
                        ),
                    );

                    $query_args['meta_query'][$index] = $room_query;
                    $query_args['meta_query']['relation'] = 'AND';
                }
            }
        }
    }

    return $query_args;
}, 10, 2);

/**
 * Modify the default WP sender name.
 */
add_filter('wp_mail_from_name', function ($from_name) {
    $from_name = 'gibbs.no';
    return $from_name;
});

/**
 * Modify the default WP sender email.
 */
add_filter('wp_mail_from', function ($from_email) {
    $from_email = 'no_reply@gibbs.no';
    return $from_email;
});

/**
 * Modify the registration class to enforce 8 characters password length.
 */
$custom_listeo_core = Listeo_Core::instance();
// add_action( 'wp_ajax_nopriv_listeoajaxregister', function() {
//     if ( get_option('listeo_display_password_field') ) {
//         $password = sanitize_text_field(trim($_POST['password']));
//         if ( strlen( $password ) < 8 ) {
//             remove_action( 'wp_ajax_nopriv_listeoajaxregister', array( $custom_listeo_core->users, 'ajax_register' ) );

//             echo json_encode(
//                 array(
//                     'registered'=>false,
//                     'message'=> esc_html__( 'Password must be 8 characters in length', 'listeo_core' )
//                 )
//             );
//             die();
//         }
//     }
// }, 9 );

/**
 * Modify the registration class to enforce 8 characters password length while changing password.
 */
remove_action('init', array($custom_listeo_core->users, 'submit_change_password_form'));
add_action('init', function () {
    $error = false;
    if (isset($_POST['listeo_core-password-change']) && '1' == $_POST['listeo_core-password-change']) {
        $current_user = wp_get_current_user();
        if (!empty($_POST['current_pass']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {

            if (!wp_check_password($_POST['current_pass'], $current_user->user_pass, $current_user->ID)) {
                /*$error = 'Your current password does not match. Please retry.';*/
                $error = 'error_1';
            } elseif ($_POST['pass1'] != $_POST['pass2']) {
                /*$error = 'The passwords do not match. Please retry.';*/
                $error = 'error_2';
            } elseif (strlen($_POST['pass1']) < 8) {
                /*$error = 'A bit short as a password, don\'t you think?';*/
                $error = 'error_3';
            } elseif (false !== strpos(wp_unslash($_POST['pass1']), "\\")) {
                /*$error = 'Password may not contain the character "\\" (backslash).';*/
                $error = 'error_4';
            } else {
                $user_id = wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['pass1'])));

                if (is_wp_error($user_id)) {
                    /*$error = 'An error occurred while updating your profile. Please retry.';*/
                    $error = 'error_5';
                } else {
                    $error = 'ok';
                    do_action('edit_user_profile_update', $current_user->ID);
                    wp_redirect(get_permalink() . '?updated_pass=true');
                    exit;
                }
            }

            if ($error == 'ok') {
                do_action('edit_user_profile_update', $current_user->ID);
                wp_redirect(get_permalink() . '?updated_pass=true');
                exit;
            } else {
                wp_redirect(get_permalink() . '?err_pass=' . $error);
                exit;

            }

        }
    } // end if
});

/* Custom Menu */
function wpb_main_nav_menu()
{
    register_nav_menu('main-nav', __('Main Nav'));
    register_nav_menu('outlogged-nav', __('outlogged Nav'));
    register_nav_menu('editor-dashboard', __('Editor Nav'));
}
add_action('init', 'wpb_main_nav_menu');

function custom_my_listings($atts)
{

    if (isset($atts['status']) && !empty($atts['status'])) {
        $_REQUEST['status'] = $atts['status'];
    }
    return do_shortcode('[listeo_my_listings]');
}

add_shortcode('my_listings', 'custom_my_listings');

function showAccountDetailsWithoutExternalLinks($atts)
{

    // Get absolute path to file.
    $file = locate_template('my-account.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';

}

add_shortcode('my_account_no_external_links', 'showAccountDetailsWithoutExternalLinks');

function taxonomyGridOrderbyInclude($atts)
{

    // Get absolute path to file.
    $file = locate_template('taxonomy-grid.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';

}

add_shortcode('taxonomy-grid-orderby-include', 'taxonomyGridOrderbyInclude');

// Register child widgets.
include_once 'listeo-child-widgets.php';
function register_owner_widget_child()
{
    register_widget('custom_Listeo_Core_Owner_Widget');
}
add_action('widgets_init', 'register_owner_widget_child');

include_once 'child-classes/extended_listeo_core_listing.php';
include_once 'child-classes/extended_listeo_core_search.php';

add_action('after_setup_theme', 'remove_parent_theme_features', 10);

function change_date()
{
    $_POST['data_id'];
    $_POST['date_start'];
    $_POST['date_end'];

    $data = json_encode($_POST['comment']);

    Bookings_Admin_List::update_booking([
        'ID' => $_POST['data_id'],
        'date_start' => $_POST['date_start'],
        'date_end' => $_POST['date_end'],
        'comment' => $data,

    ]);
    wp_die();
}
add_action('wp_ajax_change_date', 'change_date', 10);

function repeat_booking_end_date()
{

    Listeo_Core_Bookings_Calendar::listeo_check_repeat_booking_availability($_POST['repeat_end_date']);
    wp_die();
}
add_action('wp_ajax_repeat_booking_end_date', 'repeat_booking_end_date', 10);

function db_insert($reservation_id, $status, $date_start, $date_end, $hour_start, $hour_end, $listing_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'r';
    $_status = $status;
    $_date_start = $date_start;
    $_date_end = $date_end;
    $_hour_start = $hour_start;
    $_hour_end = $hour_end;
    $_listing_id = $listing_id;
    $_reservation_id = $reservation_id;
    $data = array(
        'reservation_id' => $_reservation_id,
        'status' => $_status,
        'date_start' => $_date_start,
        'date_end' => $_date_end,
        'hour_start' => $_hour_start,
        'hour_end' => $_hour_end,
        'listing_id' => $_listing_id,
    );
    $wpdb->insert($table_name, $data, $format = null);
}

function new_offer()
{
    $_POST['data_id'];
    $_POST['price'];
    $_POST['date_start'];
    $data = json_encode($_POST['comment']);

    Bookings_Admin_List::update_booking([
        'ID' => $_POST['data_id'],
        'comment' => $data,
        'price' => $_POST['price'],

    ]);
    wp_die();
}
add_action('wp_ajax_new_offer', 'new_offer', 10);

function change_status()
{
    $_POST['booking_id'];
    $_POST['status'];

    Listeo_Core_Bookings_Calendar::set_booking_status($_POST['booking_id'], $_POST['status']);

    wp_die();
}
add_action('wp_ajax_change_status', 'change_status', 10);

function ba_bookings()
{
    $date = $_SESSION['date'];
    $time = $_SESSION['time'];
    $id = $_SESSION['id'];
    return $id;
}

function db_update_test()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'r';
    $status = $_POST['status'];
    $reservation_id = $_POST['reservation_id'];
    $where = array('reservation_id' => $reservation_id);
    $data = array(
        'status' => $status,
    );
    $wpdb->update($table_name, $data, $where);
    wp_die();
}
add_action('wp_ajax_db_update_test', 'db_update_test', 10);

//invoice person/company 
function invoice_radio_button()
{
    $user_id = wp_get_current_user();

    $name = $_POST['name'];
    $address = $_POST['address'];
    $number = $_POST['number'];
    $type = $_POST['type'];
    $booking_id = $_POST['booking_id'];

    if($type == 'person'){
        update_user_meta( intval($user_id), 'personal_number', $number );
        update_user_meta( intval($user_id), 'billing_first_name', $name );
    }else{
        update_user_meta( intval($user_id), 'company_number', $number );
        update_user_meta( intval($user_id), 'billing_company', $name );
    }
    update_user_meta( intval($user_id), 'billing_address_1', $address );
    update_user_meta( intval($user_id), 'cptype', $type );


    add_post_meta( intval($booking_id), 'invoice_payment', 'true', true );
    update_post_meta ( intval($booking_id), 'invoice_payment', 'true' );
    Listeo_Core_Bookings_Calendar::set_booking_status(intval($booking_id), 'paid');

    wp_send_json(['reservations' => intval($booking_id)]);


    wp_die();
}

add_action('wp_ajax_invoice_radio_button', 'invoice_radio_button', 10);

//download invoice 
function download_invoice()
{
    global $wpdb;
    $user_id = wp_get_current_user();

    $from = $_POST['from'];
    $to = $_POST['to'];

    //generate invoice
    $xmlFile = array();
    $results = $wpdb->get_results("SELECT `order_id` FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `date_start` >= '{$from}' AND `date_end` <= '{$to}'");
    foreach ($results as $key => $reservation) {
        if(isset($reservation->order_id)){
            $order = wc_get_order($reservation->order_id);
            $user_id = $order -> get_user_id();
            $booking_id = $reservation->order_id;
            $price = $order->get_total();
            $name = $order -> get_billing_first_name();
            $company = $order -> get_billing_company();
            $address = $order -> get_billing_address_1();
            $personal_number =  get_the_author_meta( 'personal_number', $user_id );
            $company_number = get_the_author_meta( 'company_number', $user_id );

            $arr = array (
                'booking_id' => $booking_id,
                'price' => $price,
                'name' => $name,
                'company' => $company,
                'address' => $address,
                'personal_number' => $personal_number,
                'company_number' => $company_number,
                'payment_method' => 'Faktura'
            );
            $doc = new DomDocument('1.0');
            // create root node
            $root = $doc->createElement('xml');
            $root = $doc->appendChild($root);

            foreach ($arr as $key => $val) {
                // add node for each row
                $occ = $doc->createElement('reservation');
                $occ = $root->appendChild($occ);
                // add a child node for each field
                foreach ($arr as $fieldname => $fieldvalue) {
                    $child = $doc->createElement($fieldname);
                    $child = $occ->appendChild($child);
                    $value = $doc->createTextNode($fieldvalue);
                    $value = $child->appendChild($value);
                }
            }
            $xml_string = $doc->saveXML() ;
            // array_push($xmlFile,array_flip($arr));
            // $xml = new SimpleXMLElement('<xml/>');
            // array_walk_recursive($xmlFile, array ($xml, 'addChild'));
        }


    }
    //write invoice
    $fp = get_stylesheet_directory();
    $file_path = $fp . '/invoices.xml';
    $myfile = fopen($file_path, "w") or die("Unable to open file!");
    $txt = $xml_string;
    fwrite($myfile, $txt);
    fclose($myfile);
    wp_send_json(['reservations' => $txt]);

    //set status to paid
    wp_die();
}
add_action('wp_ajax_download_invoice', 'download_invoice', 10);


add_filter('upload_mimes', 'custom_upload_xml');

function custom_upload_xml($mimes) {
    $mimes = array_merge($mimes, array('xml' => 'application/xml'));
    return $mimes;
}



add_action('wp_head', 'some_js');
function some_js()
{
    
    ?>

    <script type='text/javascript'>
        var hour = jQuery('.opening-hours #booking-confirmation-summary-time span').text();
        setTimeout(() => {
            //jQuery('.single-slot-right input').val(99);
            //jQuery('.single-slot-right ').css('pointer-events','none');
            //jQuery('.single-slot-right ').css('opacity','0.5');
        }, 1000);
        jQuery(this).on('click', function(){
          //  jQuery('.single-slot-right input').val(99);

        });

    </script>
    <?php
if(isset($_GET['header']) && $_GET['header'] == "no"){
    ?>
        <style type="text/css">
            #header-container, .copyrights{
               display: none !important;
            }
        </style>
        <?php
    } 
    if(isset($_GET['filter']) && $_GET['filter'] == "no"){
    ?>
        <style type="text/css">
            .search{
               display: none !important;
            }
        </style>
        <?php
    }  
}

add_action('wp_head', 'clear_trash_button');
function clear_trash_button()
{
    ?>
    <script>
        function loading(seconds){
            jQuery('.timer-loader').show();
            jQuery('.tabela').css('opacity','0.1');
            setTimeout(function(){
                jQuery('.timer-loader').hide();
                jQuery('.tabela').css('opacity','1');
            }, seconds);
        }
        jQuery(document).ready(function(){
            jQuery('span:contains(Nullstill)').on('click',function(){
                loading(1500);
                if(jQuery('#divtoshow').is(':visible')){
                    jQuery('.startDate').parent()[0].click();
                    jQuery('.nextbtn').click();

                    setTimeout(() => {
                        jQuery('.previousbtn').click();
                    }, 500);
                }else{
                    jQuery('.nextbtn').click();
                    setTimeout(() => {
                        jQuery('.previousbtn').click();
                    }, 500);
                }
//                 jQuery('.booking-sticky-footer .timenotifi').hide();
//                 jQuery('.booking-sticky-footer .timeSpan').hide();


                jQuery('.booking-sticky-footer .bsf-left h4').show();
//                 jQuery('.booking-estimated-cost').hide();
//                 jQuery('.bsf-leftDropdown').hide();
                jQuery('.bsf-left').show();
                jQuery('.booking-sticky-footer .button').text("Velg tid");
//                 jQuery('.poraka').hide();
            });
        });
    </script>
    <?php
}

add_action('wp_head', 'show_time_block');
function show_time_block()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            window.mobileCheck = function() {
                let check = false;
                (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                return check;
            };

            jQuery('.time-price #listing-pricing-list h3').removeClass('margin-top-70');
            jQuery('.time-price #listing-pricing-list h3').css('margin-top','0');
            jQuery('.timenotifi').hide();
            jQuery('.fratil').hide();
            if(!mobileCheck()) {
                jQuery('table tr td label').on('click', function () {
                    jQuery('.timenotifi').show();
                    jQuery('.fratil').show();
                });
            }

        });


    </script>
    <?php
}

add_action('wp_head', 'from_dropdown');
function from_dropdown()
{
    ?>
    <script>
        jQuery(document).ready(function(){

            window.mobileCheck = function() {
                let check = false;
                (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                return check;
            };

            var fromId = '';
            var toId = '';
            if(mobileCheck()){
                fromId = 'mobFromHours';
                toId = 'mobToHours';
            }else{
                fromId = 'fromHours';
                toId = 'toHours';
            }

            jQuery(`#${fromId}`).on('click').change(function () {
                if (jQuery('.endDate').length > 0) {
                    var cl = jQuery(`#${fromId} option:selected`).attr('data-cl');
                    cl = cl.replace(" ", ".");
                    cl = '.' + cl;
                    cl = cl.substring(0, cl.indexOf(' '));
                    jQuery(`#${toId} option:selected`).removeAttr('selected');
                    let x = jQuery('.endDate').parent()[0];
                    jQuery(`${cl} label`)[0].click();
                    setTimeout(() => {
                        x.click();
                    }, 200);
                }
                else {
                    var cl = jQuery(`#${fromId} option:selected`).attr('data-cl');
                    cl = cl.replace(" ", ".");
                    cl = '.' + cl;
                    cl = cl.substring(0, cl.indexOf(' '));
                    jQuery(`${cl} label`)[0].click();
                }
            });

            jQuery(`#${toId}`).on('click').change(function () {
                if (jQuery('.endDate').length > 0) {
                    jQuery('.endDate').removeClass('endDate');
                    var cl = jQuery(`#${toId} option:selected`).attr('data-cl');
                    cl = cl.replace(" ", ".");
                    cl = '.' + cl;
                    cl = cl.substring(0, cl.indexOf(' '));
                    jQuery('.startDate').parent()[0].click();
                    setTimeout(() => {
                        jQuery(`${cl} label`)[0].click();
                    }, 200);

                }
                else {
                    var cl = jQuery(`#${toId} option:selected`).attr('data-cl');
                    cl = cl.replace(" ", ".");
                    cl = '.' + cl;
                    cl = cl.substring(0, cl.indexOf(' '));
                    jQuery(`${cl} label`)[0].click();
                }
            });

        });
    </script>
    <?php
}

add_action('wp_head', 'hide_pay_now');
function hide_pay_now()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery('body').is('.page-id-52') &&  localStorage.noBooking == 'true'){
                jQuery('a:contains(Pay now)').remove();
                localStorage.removeItem("noBooking");
            }
            //hide footer in booking-summary
            if(jQuery('body').is('.page-id-52')){
                jQuery('#footer').hide();
            }
        });
    </script>
    <?php
}

add_action('wp_head', 'checkout_without_booking');
function checkout_without_booking()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('#dashboard-page-content').on('click','.newoffer', function () {
                jQuery('.popup').show();
                element = jQuery(this).parent().parent();
            });

            jQuery('.pay').on('click',function(){
                let x = jQuery(this).parent().parent().attr('id');
                if( jQuery(`#${x} #billing_address_1`).text() == 'true ' ){
                    localStorage.setItem('checkout_disable', 'true');
                }else{
                    localStorage.setItem('checkout_disable', 'false');
                }
            });

            if( jQuery('body').is('.page-id-82') ){
                var checkout_disable = localStorage.getItem('checkout_disable');
                if (checkout_disable == 'true'){
                    jQuery('.shop_table thead tr .product-name').text('Custom request');
                    let z = jQuery('.shop_table tbody tr .product-name').children()[0];
                    z.style.display = 'none';
                    localStorage.setItem('checkout_disable', 'false');
                }
            }
        });
    </script>
    <?php
}

// SESSION PROBLEM HERE
function send_days()
{
    $_SESSION['totalDays'] = $_POST['totalDays'];
    $_SESSION['totalPrice'] = $_POST['totalPrice'];

    $data = array(
        'totalDays' => $_POST['totalDays'],
        'totalPrice' => $_POST['totalPrice'],
    );

    wp_send_json_success($data);
}
add_action('wp_ajax_send_days', 'send_days', 10);
add_action('wp_ajax_nopriv_send_days', 'send_days', 10);


function send_listingid()
{
    $_SESSION['_listingid'] = $_POST['_listingid'];

}
add_action('wp_ajax_send_listingid', 'send_listingid', 10);
add_action('wp_ajax_nopriv_send_listingid', 'send_listingid', 10);

add_action('wp_head', 'change_footer_mobile');
function change_footer_mobile()
{
    ?>
    <script>
        jQuery(document).ready(function(){

            jQuery('.time-slot').on('click',function(){
                if(jQuery('select#mobToHours option:selected').text() == "velg slutt-tid"){
                    setTimeout(() => {
                        jQuery('.booking-sticky-footer .button').text("Velg slutttid");
                    },100);
                }else{
                    setTimeout(() => {
                        jQuery('.booking-sticky-footer .button').text("Reserver");
                    },100);
                }
            });

            setTimeout(() => {
                window.mobileCheck = function() {
                    let check = false;
                    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                    return check;
                };

                if(mobileCheck()) {
                  //  jQuery('.booking-sticky-footer .bsf-left h4').html("<a style='text-decoration: none;color: black;'href='#booking-widget-anchor'>Trykk inni kalenderen for å velge starttid</a>");
                    //jQuery('.booking-sticky-footer .bsf-left h4').css('font-size','small');
                    jQuery('.booking-sticky-footer .bsf-right').on('click', function(){
                        if(jQuery('#divtoshow').is(':visible') == true){
                            jQuery('.tabela').parent().removeClass('borderFunction1');
                            jQuery('.tabela').parent().addClass('borderFunction');
                            setTimeout(() => {
                                jQuery('.tabela').parent().removeClass('borderFunction');
                                jQuery('.tabela').parent().addClass('borderFunction1');
                            }, 800);

                        }else if(jQuery('.endDate').length > 0){
                            jQuery('html,body').animate({
                                scrollTop: jQuery('.book-now').offset().top-250
                            }, 'slow');
                            jQuery('.book-now').removeClass('booknowanimation1');
                            jQuery('.book-now').addClass('booknowanimation');
                            setTimeout(() => {
                                jQuery('.book-now').removeClass('booknowanimation');
                                jQuery('.book-now').addClass('booknowanimation1');
                            }, 3000);
                        }
                    });

                    // whole footer click
                    jQuery('.booking-sticky-footer').on('click', function(){
                        if(jQuery('.bsf-left h4').is(':visible')){
                            jQuery('.bsf-right a').removeAttr('href');
                            jQuery('html,body').animate({
                                scrollTop: jQuery('#booking-widget-anchor').offset().top
                            }, 'slow');
                            jQuery('.tabela').parent().removeClass('borderFunction1');
                            jQuery('.tabela').parent().addClass('borderFunction');
                            setTimeout(() => {
                                jQuery('.tabela').parent().removeClass('borderFunction');
                                jQuery('.tabela').parent().addClass('borderFunction1');
                            }, 800);
                        }else{
                            jQuery('.bsf-right a').attr('href','#booking-widget-anchor');
                        }

                    });
                }

            }, 300);
        });
    </script>
    <?php
}

add_action('wp_head', 'pdfFiles');
function pdfFiles()
{
    ?>
    <script>
        jQuery(document).ready(function() {
            let y = 1;
            let x = 0;

            jQuery('.remove-uploaded-file').on('click',function(){
                console.log(jQuery(this).parent().parent().remove());
            });

            if(jQuery('body').is('.page-id-73')){
               // jQuery('.details').hide();
                
                let addButtonPdf = "<input id='addStuffPdf' type='button' value='Legg til flere PDF'>";
                jQuery('.remove-uploaded-file').text('Fjern fil');
                //jQuery('.pdf_documents').append(addButton);
                jQuery('.new_pdf_upload_for_stuff').append(addButtonPdf);
                let counter = 0;
                let counter1 = 0;
                jQuery('.pdf_documents').children().each(function(){
                    counter++;
                    if(jQuery(this).children().length == 2 && counter > 4){
                        jQuery(this).hide();
                    }else if(jQuery(this).children().length == 3){
                        y += 1;
                    }
                })
                jQuery('.new_pdf_upload_for_stuff').children().each(function(){
                    counter1++;
                    if(jQuery(this).children().length == 2 && counter1 > 2){
                        jQuery(this).hide();
                    }else if(jQuery(this).children().length == 3){
                        x += 1;
                    }
                })
            }

            jQuery('#addPdf').on('click',function(){
                if(y < 10){
                    y += 1;
                    jQuery('.pdf_documents').children()[y].style.display = "block";
                }else{
                    jQuery('#addPdf').css('pointer-events','none');
                }
            });
            jQuery('#addStuffPdf').on('click',function(){
                if(x < 5){
                    x += 1;
                    jQuery('.new_pdf_upload_for_stuff').children()[x].style.display = "block";
                }else{
                    jQuery('#addStuffPdf').css('pointer-events','none');
                }
            });

        });

    </script>
    <?php
}

add_action('wp_head', 'append_nobooking');
function append_nobooking()
{
    ?>
    <script type='text/javascript'>
        jQuery(document).ready(function(){
            //jQuery('.clicktologin .book-now-text').text('Logg inn for å sende forespørsel');
            var btn = '<input class="<?php if(is_user_logged_in()){echo'sendreq';} else{ echo 'xoo-el-login-tgr';}?>" type="button" value="Send forespørsel">';
            var minMax = '';
            var minPrice = "<?php //echo get_post_custom_values($key = '_price_min')[0]; ?>";
            var maxPrice = "<?php //echo get_post_custom_values($key = '_price_max')[0]; ?>";
            if((minPrice != "" && minPrice != "0") && (maxPrice == "" || maxPrice == "0")){
                minMax = `<div class='minMaxSpan' style='font-size:18px;text-align:center;font-weight:bolder;'><span>Pris fra: </span><span class='js-min-price'>${minPrice} kr</span></div>`;
            }else if((minPrice != "" && minPrice != "0") && (maxPrice != "" && maxPrice != "0")){
                minMax = `<div class='minMaxSpan' style='font-size:18px;text-align:center;font-weight:bolder;'><span>Pris: </span><span class='js-min-price'>${minPrice} kr - </span><span class='js-max-price'>${maxPrice} kr</span></div>`;
            }else{
                minMax = "<div class='minMaxSpan'></div>";
            }
           /* if(jQuery('#widget_booking_listings-2').length == 1){
                jQuery('.sendreq').remove();
                jQuery('.minMaxSpan').remove();
            }else{
                let sidebar = '<div id="widget_listing_owner-2" class="listing-widget widget listeo_core widget_listing_owner boxed-widget margin-bottom-35"></div>';
                jQuery('.sticky-wrapper .sticky').append(sidebar);
                jQuery('.boxed-widget').append(btn);
                jQuery('.boxed-widget').append(minMax);
                jQuery('#listing-pricing-list').hide();
                if(jQuery('.clicktologin').css('display') == 'none' || jQuery('.clicktologin').length == '0'){
                    jQuery('.sendreq').show();
                }else{
                    jQuery('.sendreq').hide();
                }
                jQuery('.hosted-by-title').hide();
                jQuery('.hosted-by-bio').hide();
                jQuery('.send-message-to-owner').hide();
                jQuery('#widget_listing_owner-2').prepend('<h3><i class="fa fa-calendar-check-o "></i>Booking</h3>');
            }*/

            var _listingid = jQuery('body').attr('class');
            _listingid = _listingid.substr(_listingid.indexOf('postid'));
            _listingid =  _listingid.substr(_listingid.indexOf('-')+1);
            _listingid =  _listingid.substr(0,_listingid.indexOf(' '));
            var _ajax_data1;
            jQuery('.sendreq').on('click', function(e){

                <?php
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $url = "https://";
    } else {
        $url = "http://";
    }

    // Append the host(domain name, ip) to the URL.
    $url .= $_SERVER['HTTP_HOST'];

    // Append the requested resource location to the URL
    // $url.= $_SERVER['REQUEST_URI'];
    ?>

                var url ="<?php echo $url ?>";
                e.preventDefault();

                _ajax_data1 = {
                    'action': 'send_listingid',
                    '_listingid': _listingid,

                };

                jQuery.ajax({
                    type: "POST",
                    url: listeo.ajaxurl,
                    data: _ajax_data1,
                    dataType: 'json',
                    success: function (data) {
                        console.log('success',data);
                    },
                    fail: function(data){
                        console.log(data);
                    }
                });
                setTimeout(() => {
                    window.open(`${url}/booking-bekreftelse/`,"_self");
                }, 1000);


            });

            if(jQuery('body').is('.page-id-52')){
                jQuery('.sendreq').remove();
                jQuery('.minMaxSpan').remove();
            }
        });

    </script>
    <?php
}

add_action('wp_head', 'make_slots_available');
function make_slots_available()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            //check slot status
            if(jQuery("#_booking_status").is(':checked') == true){
                if(jQuery("#_slots_status").is(':checked') == false){
                    jQuery("#_slots_status").click();
                }
            }

            jQuery("#_booking_status").on('click',function(){
                if(jQuery("#_booking_status").is(':checked') == true){
                    if(jQuery("#_slots_status").is(':checked') == false){
                        jQuery("#_slots_status").click();
                    }
                }
            });
            jQuery('.form-field-_slots_status-container').hide();

            jQuery('select.chosen-select').change(function(){
                jQuery('.fm-input.pricing-bookable-options select option:selected').each(function(){
                    if(jQuery(this).text() == 'Multipliser med personer '){
                        jQuery(this).parent().parent().parent().children()[3].style.display = 'none';
                    }else{
                        jQuery(this).parent().parent().parent().children()[3].style.display = 'block';
                    }
                });
            });

            jQuery('.checkboxHours').change(function() {
                if(jQuery('.checkboxHours').is(':checked')){
                    jQuery(this).parent().parent().find('.time-slot-start').attr('placeholder','00:00');
                    jQuery(this).parent().parent().find('.time-slot-start').val('00:00');
                    jQuery(this).parent().parent().find('.time-slot-end').attr('placeholder','24:00');
                    jQuery(this).parent().parent().find('.time-slot-end').val('24:00');
                }else{
                    jQuery(this).parent().parent().find('.time-slot-start').attr('placeholder','--|--');
                    jQuery(this).parent().parent().find('.time-slot-start').val('');
                    jQuery(this).parent().parent().find('.time-slot-end').attr('placeholder','--|--');
                    jQuery(this).parent().parent().find('.time-slot-end').val('');
                }
            });

            jQuery('.slots .day-slots .slots-container .single-slot-left').each(function(){
                if(jQuery(this).find('.single-slot-time').text() == '00:00 - 23:00'){
                    jQuery(this).find('.single-slot-time').text('00:00 - 24:00');
                }
            })

            jQuery("#submit-listing-form button:contains('Lagre')").on('click',function(){
                jQuery('.add-listing-section.row.menu.switcher-on .fm-input.pricing-ingredients input').each(function(){
                    if(jQuery(this).val() == ""){
                        jQuery(this).val(' ');
                    }
                });

                if(jQuery("#_booking_status").is(':checked') == true){
                  /*  let x = jQuery('.basic_prices #_hour_price').val();
                    if(x != ''){
                        jQuery('.details #_price_min').val(x);
                    }else {
                        jQuery('.details #_price_min').val(0);
                    }
                    let z = parseInt(jQuery('.basic_prices #_weekday_price').val());
                    if(z > 0){
                        jQuery('#_price_max').val(z);
                    }else{
                        jQuery('#_price_max').val(parseInt(jQuery('.basic_prices #_normal_price').val()));
                    }*/
                }

                jQuery('.slots .day-slots').each(function(){
                    let start = jQuery(this).find('.time-slot-start').val();
                    let end = jQuery(this).find('.time-slot-end').val();

                    if(start != "" && end != ""){
                        jQuery(this).find('.slots-container .single-slot').remove();
                        jQuery(this).find('.add-slot-btn').click();
                    }

                    if(jQuery(this).find('.checkboxHours').is(':checked')){
                        jQuery(this).find('.slots-container .single-slot').remove();
                        jQuery(this).find('.time-slot-start').val("00:00");
                        jQuery(this).find('.time-slot-end').val("23:00");
                        jQuery(this).find('.add-slot-btn').click();
                    }

                });
            });
        });
    </script>
    <?php
}

add_action('wp_head', 'hide_price');
function hide_price()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery('body').is('.single-listing')){
                let x = 0;
                jQuery('#listing-pricing-list .pricing-list-container ul li span').each(function(){
                    if(jQuery(this).text() == 'kr' || jQuery(this).text() == '0kr'){
                        jQuery(this).parent().hide();
                        x++;
                    }
                });

                if(x == 3){
                    jQuery('.time-price').hide();
                }
            }
        });
    </script>
    <?php
}
//changes if listing has specific category

add_action('wp_head', 'if_category');
function if_category()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery('body').is('.page-id-73')){
                //booking checkbox value min max
                jQuery('#_booking_status').change(function(){
                    if(jQuery('#_booking_status').is(':checked')){
                        jQuery('.details').hide();
                    }else{
                        jQuery('.details').show();
                    }
                })

                //Change red notification for Fra pris
                if(jQuery('.notification.closeable.error.listing-manager-error p').text() == 'Fra pris er et obligatorisk felt'){
                    jQuery('.notification.closeable.error.listing-manager-error p').text('Timepris er obligatorisk felt. Hvis du ikke operer med timepris, legg inn dagsprisen i det Timepris feltet. Der etter skriv i feltet «Minimum timer som kan leies» 24')
                }

                jQuery('.form-field-_category-container').hide();
                jQuery('.form-field-listing_category-container #firstLv option').each(function(){
                    var val = jQuery(this).val();
                    //category is lokaler
                    if(val == 352 || val == 314 || val == 324 || val == 82 || val == 297 || val == 164 || val == 165 || val == -1 || val == 244 || val == 245 || val == 246 || val == 247 || val == 249 || val == 250 || val == 251 || val == 253 || val == 254 || val == 158){
                        jQuery('#_category').val('lokaler');
                        jQuery('.kapasitet').show();
                        //Add listing (only venue)
                        let url = window.location.protocol+'//'+ window.location.hostname+'/my-listings/add-listings/';
                        let currUrl = window.location.href;
                        if(url === currUrl){
                            setTimeout(function(){
                                if(jQuery('#firstLvRegion :selected').val() != -1){
                                    jQuery('#firstLvRegion option:eq(0)').prop('selected', true);
                                }
                            }, 200);
                        }
                    }else{
                        jQuery('.kapasitet').hide();
                        jQuery('.label-_friendly_address').html('Hvor leverer du til?<i class="tip" data-tip-content="Skriv hvor du leverer dine tjenester. Eksempel 1: Leverer til Hele Norge. Eksempel 2: Leverer kun til Østfold."><div class="tip-content">Skriv hvor du leverer dine tjenester. Eksempel 1: Leverer til Hele Norge. Eksempel 2: Leverer kun til Østfold.</div></i>')
                    }
                    //category is lokaler or utstyr
                    if(val == 352 || val == 314 || val == 324 || val == 82 || val == 297 || val == 164 || val == 165 || val == 166 || val == 305 || val == 169 || val == 173 || val == 168 || val == 171 || val == 172 || val == 223 || val == -1 || val == 244 || val == 245 || val == 246 || val == 247 || val == 249 || val == 250 || val == 251 || val == 253 || val == 254 || val == 158 || val == 343){
                        if(jQuery('.booking').css('display') == 'none'){
                            jQuery('.details').show();
                        }else if(jQuery("#_booking_status").is(':checked') == false){
                            jQuery('.details').show();
                        }else{
                            jQuery('.details').hide();
                        }
                    }else{
                        jQuery('.details').show();
                        jQuery('.booking').hide();
                        jQuery('.menu').hide();
                    }
                    //category utstyr
                    if(val == 166 || val == 305 || val == 169 || val == 173 || val == 168 || val == 171 || val == 172 || val == 223 || val == 343 || val == -1){
                        jQuery('#_category').val('utstr');
                        if(jQuery("#_booking_status").is(':checked')){
                            jQuery('#_count_per_guest').click();
                            jQuery('#_count_per_guest').hide();
                        }
                        jQuery('.form-field-_count_per_guest-container').hide();
                    }else{
                        jQuery('.form-field-_max_guests-container').hide();
                        jQuery('.form-field-_count_per_guest-container').hide();
                    }
                });
            }

        });
    </script>
    <?php
}

add_action('wp_head', 'change_guests_label');
function change_guests_label()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            //change span in header
            jQuery('#header .main-nav li span').css('left','0px');
            //change <i> in search-bar
            jQuery('.search #listeo_core-search-form #tax-listing_category i').first().attr('class','fa fa-binoculars');
            var cat = jQuery('.categoryName span').attr('data-cat');
            if(cat == 'lokaler'){
                if(jQuery('a:contains(Antall)').contents()[0]){
                    jQuery('a:contains(Antall)').contents()[0].textContent = 'Antall';
                }
            }
        });
    </script>
    <?php
}

// Open in new tab (booking enabled button)
add_action('wp_head', 'submit_newtab_bookingcal');
function submit_newtab_bookingcal()
{
    ?>

 <script>
        // jQuery(document).ready(function(){
            // var btn;
            // if (window.location.pathname !== 'min-gibbs/my-profile/'){
            //     localStorage.setItem('url', window.location.href);
            // }
            // console.log('btn asdasdasdasd');

            // jQuery('#sign-in-dialog a[href="#tab2"]').on('click', function(){
            //     console.log('btn registered');
            //    var btn = jQuery('.register .button');

            // });
            // btn.on('click', function(){
            //    console.log('btn cliciked');
            //    window.open('https://www.gibbs.no/','_blank');


            // });

        // });
    </script>
    <?php
}

//move last hour on calendar
add_action('wp_head', 'change_lasthour_css');
function change_lasthour_css()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            var firstHour = jQuery('.halfright1').first();
            var lastHour = jQuery('.halfright1').last();
            var firstHourOffset = firstHour.offset();
            var lastHourOffset = lastHour.offset();

            if(firstHourOffset != lastHourOffset){
                console.log('lasthour postition not equal');
                lastHour.css('left','9px');
            }

        });
    </script>
    <?php
}

//capacity disable filter

add_action('wp_head', 'capacity_disable');
function capacity_disable()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('.capacityClass').on('change', function(){
                var cap = jQuery('.capacityDisable');
                if(cap.css('visibility') == 'hidden'){
                    cap.css('visibility','visible');
                }
                for (let u = 0; u < 8; u++) {
                    var a = jQuery(`.capacityClass input:eq(${u})`);
                    a.prop('disabled', false);
                }

            });

            jQuery('.capacityDisable').on('click', function(){
                clearFiltersFor('#_standing-panel');

                // for (let u = 0; u < 8; u++) {
                //     var min = jQuery(`.capacityClass input:eq(${u})`).attr('data-slider-min');
                //     var max = jQuery(`.capacityClass input:eq(${u})`).attr('data-slider-max');
                //     jQuery(`.capacityClass input:eq(${u})`).attr('value',`${min},${max}`);
                //     console.log('val changed');
                //     jQuery('.capacityDisable').css('visibility','hidden');
                // }
            })

        });
    </script>
    <?php
}

add_action('wp_head', 'repeated_booking');
function repeated_booking()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery(".repeat").change(function () {
                if (this.checked && this.value === "option1") {
                    jQuery(".selectDates").show();
                } else if (this.checked && this.value === "option2") {
                    jQuery(".selectDates").show();
                } else if (this.checked && this.value === "option3") {
                    jQuery(".selectDates").show();
                } else{
                    jQuery(".selectDates").hide();
                }
            });

            let currUrl = window.location.href;
            let url = window.location.protocol+"//"+ window.location.hostname+"/";
            window.mobileCheck = function() {
                let check = false;
                (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                return check;
            };
            if ((currUrl === url) || (jQuery('body').is(".single-listing"))){
                //Change browser message
                // Firefox 1.0+
                let isFirefox = typeof InstallTrigger !== 'undefined';

                // Safari 3.0+ "[object HTMLElementConstructor]"
                let isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
                    return p.toString() === "[object SafariRemoteNotification]";
                })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

                // Chrome 1 - 79
                let isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);

                // Opera 8.0+
                let isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

                function isIpadOS() {
                    return navigator.maxTouchPoints &&
                        navigator.maxTouchPoints > 2 &&
                        /MacIntel/.test(navigator.platform);
                }

                if(mobileCheck()) {
                    let ua = navigator.userAgent;

                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(ua)) {
                        jQuery('#userAgentMessage').hide();
                    } else if (/Chrome/i.test(ua)) {
                        jQuery('#userAgentMessage').hide();
                    }
                }else {
                    if (!isChrome) {
                        jQuery('#userAgentMessage').show();
                    } else if (!isFirefox && !isChrome && !isSafari) {
                        if(!isIpadOS) {
                            jQuery('#userAgentMessage').show();
                        }
                    }
                }
            }
        });
    </script>
    <?php
}

add_action('wp_ajax_listeo_check_repeat_booking_availability', 'listeo_check_repeat_booking_availability');
function listeo_check_repeat_booking_availability()
{

    global $wpdb;
    $listing_id = $_POST['listing_id'];
    $repeatDate = $_POST['repeat_end_date'];

    $start_date = $_POST['date_from'];
    $end_date = $_POST['date_to'];
    $first_hour = intval($_POST['hour_from']);
    $second_hour = intval($_POST['hour_to']);

    $newRepeatedDate = strtotime($repeatDate);
    $newFormattedRepeatedDate = date("m/d/Y", $newRepeatedDate);

    $reservations = [];

    $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "r` WHERE `listing_id` = '$listing_id' AND `status` = 'approved' AND `date_start` > '$end_date' AND `date_end` < '$newFormattedRepeatedDate'");

    foreach ($results as $result) {

        $db_first_hour = intval($result->hour_start);
        $db_second_hour = intval($result->hour_end);

        if ($result->date_start == $result->date_end) {
            if (!(($first_hour <= $db_first_hour && $second_hour <= $db_first_hour) || ($first_hour >= $db_second_hour && $second_hour >= $db_second_hour))) {
                array_push($reservations, $result->date_start);
            }
        } else {
            $period = new DatePeriod(
                new DateTime($result->date_start),
                new DateInterval('P1D'),
                new DateTime($result->date_end)
            );

            foreach ($period as $key => $value) {
                if ($key = 0) {
                    if (!(($first_hour <= $db_first_hour && $second_hour <= $db_first_hour))) {
                        array_push($reservations, $value->format('m/d/Y'));
                    }
                } else {
                    array_push($reservations, $value->format('m/d/Y'));
                }
            }

            if (!(($first_hour >= $db_second_hour && $second_hour >= $db_second_hour))) {
                array_push($reservations, $result->date_end);
            }
        }
    }

    wp_send_json(['reservations' => $reservations]);
}

add_action('wp_ajax_listeo_check_repeat_booking_availability_multiply_days', 'listeo_check_repeat_booking_availability_multiply_days');
function listeo_check_repeat_booking_availability_multiply_days()
{

    global $wpdb;
    $listing_id = $_POST['listing_id'];
    $repeatDate = $_POST['repeat_end_date'];

    $start_date = $_POST['date_from'];
    $end_date = $_POST['date_to'];
    $first_hour = intval($_POST['hour_from']);
    $second_hour = intval($_POST['hour_to']);
    $availableDates = $_POST['availableDates'];

    $newRepeatedDate = strtotime($repeatDate);
    $newFormattedRepeatedDate = date("m/d/Y", $newRepeatedDate);

    $reservations = [];
    $availableReservations = $availableDates;

    $timestampFirstDay = strtotime($start_date);
    $dayStart = date('N', $timestampFirstDay);
    $timestampSecondDay = strtotime($end_date);
    $dayEnd = date('N', $timestampSecondDay);

    $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "r` WHERE `listing_id` = '$listing_id' AND `status` = 'approved' AND `date_start` > '$end_date' AND `date_end` < '$newFormattedRepeatedDate'");

    foreach ($results as $result) {
//        $reservationFirstDay = strtotime($result->date_start);
        //        $resStartDay = date('N',$reservationFirstDay);
        //        $reservationSecondDay = strtotime($result->date_end);
        //        $resEndDay = date('N',$reservationSecondDay);

//        if(($resStartDay >= $dayStart && $resStartDay <= $dayEnd) || ($resEndDay >= $dayStart && $resEndDay <= $dayEnd)){
        array_push($reservations, [$result->date_start, $result->hour_start, $result->date_end, $result->hour_end]);
//        }
    }

    foreach ($reservations as $reservation) {
        $res_first_hour = intval($reservation[1]);
        $res_second_hour = intval($reservation[3]);

        if ($reservation[0] == $reservation[2]) {
            $arrayCounter = 0;
            foreach ($availableDates as $dates) {
                if (in_array($reservation[0], $dates)) {
                    if (!(($first_hour <= $res_first_hour && $second_hour <= $res_first_hour) || ($first_hour >= $res_second_hour && $second_hour >= $res_second_hour))) {
                        unset($availableDates[$arrayCounter]);
                    }
                }
                $arrayCounter++;
            }
        } else {

            $arrayCounter = 0;
            foreach ($availableDates as $dates) {

                if ($reservation[0] <= $dates[0] && $reservation[2] > $dates[0]) {
                    if ($reservation[2] == $dates[0]) {
                        if ($res_second_hour >= $second_hour) {
                            unset($availableDates[$arrayCounter]);
                        }
                    } else {
                        unset($availableDates[$arrayCounter]);
                    }
                } elseif ($reservation[0] == $dates[count($dates)]) {
                    if ($res_first_hour <= $second_hour) {
                        unset($availableDates[$arrayCounter]);
                    }
                }
                $arrayCounter++;
            }
        }
    }

    wp_send_json(['reservations' => $reservations, 'finalDates' => $availableDates]);
}

//if lokaler show capacity in search
add_action('wp_head', 'show_capacity');
function show_capacity()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            /*if(jQuery('.greenThenWhite').text() === 'Lokaler & uteområder'){
                jQuery('#_standing-panel').show();
            }else{
                jQuery('#_standing-panel').hide();

            }*/

        });
    </script>
    <?php
}
//get url before
add_action('wp_head', 'get_url');
function get_url()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            if(window.location.pathname == '/gibbs/min-gibbs/my-profile/'){

            }else{
                localStorage.setItem('redirect',`${window.location.pathname}`);
            }
            //Close button in header
            jQuery("#headerCloseButton").on('click',function (){
                jQuery('#userAgentMessage').remove();
            })
        });


    </script>
    <?php
}

//show capacity only on lokaler
add_action('wp_head', 'show_cap');
function show_cap()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            var url = window.location.href;
            let a = url.split('/');
            a.pop();
            var last = a[a.length - 1]
            if(last != 'lokaler-uteomrader'){
                //jQuery('#_standing-panel').remove();
            }
        });


    </script>
    <?php
}

// add icons to add-listing
add_action('wp_head', 'add_icons');
function add_icons()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('.form-field-_captest-container').append('<img class="sitting-icon" src="/wp-content/uploads/2020/10/sitting.svg" alt="sitting">');
            jQuery('.form-field-_standing-container').append('<img class="standing-icon" src="/wp-content/uploads/2020/10/standing.svg" alt="standing">');
            jQuery('.form-field-_banquet-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/banquet.svg" alt="standing">');
            jQuery('.form-field-_theatre-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/theatre.svg" alt="theatre">');
            jQuery('.form-field-_classroom-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/classroom.svg" alt="classroom">');
            jQuery('.form-field-_horseshoe-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/horseshoe.svg" alt="horseshoe">');
            jQuery('.form-field-_coronares-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/coronares5.svg" alt="coronares5">');
            jQuery('.form-field-_squarefeet-container').append('<img class="allicons" src="/wp-content/uploads/2020/10/kvm.svg" alt="squarefeet">');

        });


    </script>
    <?php
}

add_action('wp_ajax_insert_repeat_booking', 'insert_repeat_booking');
function insert_repeat_booking()
{

    $_user_id = get_current_user_id();

    $data = $_POST['value'];

    $error = false;

    $services = (isset($data['services'])) ? $data['services'] : false;
    $comment_services = false;
    if (!empty($services)) {
        $currency_abbr = get_option('listeo_currency');
        $currency_postion = get_option('listeo_currency_postion');
        $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
        //$comment_services = '<ul>';
        $comment_services = array();
        $bookable_services = listeo_get_bookable_services($data['listing_id']);

        $firstDay = new DateTime($data['date_start']);
        $lastDay = new DateTime($data['date_start'] . '23:59:59');

        $days_between = $lastDay->diff($firstDay)->format("%a");
        $days_count = ($days_between == 0) ? 1 : $days_between;

        //since 1.3 change comment_service to json
        $countable = array_column($services, 'value');
        if (isset($data['adults'])) {
            $guests = $data['adults'];
        } else if (isset($data['tickets'])) {
            $guests = $data['tickets'];
        } else {
            $guests = 1;
        }
        $i = 0;
        foreach ($bookable_services as $key => $service) {

            if (in_array(sanitize_title($service['name']), array_column($services, 'service'))) {
                //$services_price += (float) preg_replace("/[^0-9\.]/", '', $service['price']);
                $comment_services[] = array(
                    'service' => $service,
                    'guests' => $guests,
                    'days' => $days_count,
                    'countable' => $countable[$i],
                    'price' => listeo_calculate_service_price($service, $data['adults'], $totalDays, $countable[$i]),
                );

                $i++;
            }

        }

    }
    $listing_meta = get_post_meta($data['listing_id'], '', true);
    // detect if website was refreshed
    $instant_booking = get_post_meta($data['listing_id'], '_instant_booking', true);

    set_transient('listeo_last_booking' . $_user_id, $data['listing_id'] . ' ' . $data['date_start'] . ' ' . $data['date_end'], 60 * 15);

    // because we have to be sure about listing type
    $listing_meta = get_post_meta($data['listing_id'], '', true);

    $listing_owner = get_post_field('post_author', $data['listing_id']);

    $billing_address_1 = (isset($_POST['billing_address_1'])) ? $_POST['billing_address_1'] : false;
    $billing_postcode = (isset($_POST['billing_postcode'])) ? $_POST['billing_postcode'] : false;
    $billing_city = (isset($_POST['billing_city'])) ? $_POST['billing_city'] : false;
    $billing_country = (isset($_POST['billing_country'])) ? $_POST['billing_country'] : false;

    switch ($listing_meta['_listing_type'][0]) {
        case 'service':

            $status = apply_filters('listeo_service_default_status', 'waiting');
            if ($instant_booking == 'check_on' || $instant_booking == 'on') {$status = 'confirmed';}
            // time picker booking
            if (!isset($data['slot'])) {
                $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                //check count_per_guest

                if ($count_per_guest) {

                    $multiply = 1;
                    if (isset($data['adults'])) {
                        $multiply = $data['adults'];
                    }

                    $price = Listeo_Core_Bookings_Calendar::calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);
                } else {
                    $price = Listeo_Core_Bookings_Calendar::calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], 1, $services);
                }

                $hour_end = (isset($data['_hour_end']) && !empty($data['_hour_end'])) ? $data['_hour_end'] : $data['_hour'];

                $booking_id = Listeo_Core_Bookings_Calendar::insert_booking(array(
                    'owner_id' => $listing_owner,
                    'listing_id' => $data['listing_id'],
                    'date_start' => $data['date_start'] . ' ' . $data['_hour'] . ':00',
                    'date_end' => $data['date_end'] . ' ' . $hour_end . ':00',
                    'comment' => json_encode(array('first_name' => $_POST['firstname'],
                        'last_name' => $_POST['lastname'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'],
                        'adults' => $data['adults'],
                        'message' => $_POST['message'],
                        'service' => $comment_services,
                        'billing_address_1' => $billing_address_1,
                        'billing_postcode' => $billing_postcode,
                        'billing_city' => $billing_city,
                        'billing_country' => $billing_country,

                    )),
                    'type' => 'reservation',
                    'price' => $price,
                ));

                $changed_status = Listeo_Core_Bookings_Calendar::set_booking_status($booking_id, $status);

            } else {

                // here when we have enabled slots

                $free_places = Listeo_Core_Bookings_Calendar::count_free_places($data['listing_id'], $data['date_start'], $data['date_end'], $data['slot']);

                if ($free_places > 0) {

                    $slot = json_decode(wp_unslash($data['slot']));

                    // converent hours to mysql format
                    $hours = explode(' - ', $slot[0]);
                    $hour_start = date("H:i:s", strtotime($hours[0]));
                    $hour_end = date("H:i:s", strtotime($hours[1]));

                    $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                    //check count_per_guest
                    $services = (isset($data['services'])) ? $data['services'] : false;
                    if ($count_per_guest) {

                        $multiply = 1;
                        if (isset($data['adults'])) {
                            $multiply = $data['adults'];
                        }

                        $price = Listeo_Core_Bookings_Calendar::calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);
                    } else {
                        $price = Listeo_Core_Bookings_Calendar::calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], 1, $services);
                    }

                    $booking_id = Listeo_Core_Bookings_Calendar::insert_booking(array(
                        'owner_id' => $listing_owner,
                        'listing_id' => $data['listing_id'],
                        'date_start' => $data['date_start'] . ' ' . $hour_start,
                        'date_end' => $data['date_end'] . ' ' . $hour_end,
                        'comment' => json_encode(array('first_name' => $_POST['firstname'],
                            'last_name' => $_POST['lastname'],
                            'email' => $_POST['email'],
                            'phone' => $_POST['phone'],
                            //'childrens' => $data['childrens'],
                            'adults' => $data['adults'],
                            'message' => $_POST['message'],
                            'service' => $comment_services,
                            'billing_address_1' => $billing_address_1,
                            'billing_postcode' => $billing_postcode,
                            'billing_city' => $billing_city,
                            'billing_country' => $billing_country,

                        )),
                        'type' => 'reservation',
                        'price' => $price,
                    ));

                    $status = apply_filters('listeo_service_slots_default_status', 'waiting');
                    if ($instant_booking == 'check_on' || $instant_booking == 'on') {$status = 'confirmed';}

                    $changed_status = Listeo_Core_Bookings_Calendar::set_booking_status($booking_id, $status);

                } else {

                    $error = true;
                    $message = __('Those dates are not available.', 'listeo_core');

                }

            }

            break;
    }

    // when we have database problem with statuses
    if (!isset($changed_status)) {
        $message = __('We have some technical problem, please try again later or contact administrator.', 'listeo_core');
        $error = true;
    }

    switch ($status) {

        case 'waiting':

            $message = esc_html__('Your booking is waiting for confirmation.', 'listeo_core');

            break;

        case 'confirmed':

            $message = esc_html__('We are waiting for your payment.', 'listeo_core');

            break;

        case 'cancelled':

            $message = esc_html__('Your booking was cancelled', 'listeo_core');

            break;
    }

    if (isset($booking_id)) {
        $booking_data = Listeo_Core_Bookings_Calendar::get_booking($booking_id);
        $order_id = $booking_data['order_id'];
        $order_id = (isset($booking_data['order_id'])) ? $booking_data['order_id'] : false;
    }

}

function get_the_term_list_ids_string_format($post_id, $taxonomy, $before = '', $sep = '', $after = '')
{
    $terms = get_the_terms($post_id, $taxonomy);

    if (is_wp_error($terms)) {
        return $terms;
    }

    if (empty($terms)) {
        return false;
    }

    $termsIds = "";
    foreach ($terms as $term) {
        $termsIds .= $term->term_id . ",";
    }

    return $termsIds;
}

add_action('wp_head', 'features_bug');
function features_bug()
{
    if(isset($_GET['listing_id'])){
    ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery('.dashboard-content').is('#post-73')){
                let features_ids = [];
                let features = "<?php echo get_the_term_list_ids_string_format($_GET['listing_id'], 'listing_feature') ?>";
                features_ids = features.split(",");
                window.onload = function() {
                    setTimeout(function(){
                        if (jQuery('.listeo_core-term-checklist-listing_feature input:checked').length != (features_ids.length - 1)) {
                            for (let i = 0; i < features_ids.length - 1; i++) {
                                if(!jQuery(`.listeo_core-term-checklist-listing_feature input[value='${features_ids[i]}']`).is(':checked')) {
                                    jQuery(`.listeo_core-term-checklist-listing_feature input[value='${features_ids[i]}']`).prop('checked', 'checked');
                                }
                            }
                        }
                    }, 3000);
                }
            }
        });
    </script>
    <?php
    }
}

add_action('wp_head', 'capacity_filter');
function capacity_filter()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            let value;
            let value1;
            let firstVal;
            let secondVal;
            let firstVal1;
            let secondVal1;
            jQuery("#_standing").change(function(){
                value = jQuery(this).val();
                value = value.split(",");
                firstVal = value[0];
                secondVal = value[1];
                jQuery('#capacitySpan').text('345 - 345')
            });
            jQuery("#_coronares").change(function(){
                value1 = jQuery(this).val();
                value1 = value1.split(",");
                firstVal1 = value1[0];
                secondVal1 = value1[1];
            });
            jQuery('.brukFilterKnapp.button:eq(4)').on('click',function(){
                let text;
                if(firstVal !== undefined && firstVal1 !== undefined){
                    text = `${firstVal}`+' - '+`${secondVal}`+' '+`${firstVal1}`+' - '+`${secondVal1}`;
                }else if(firstVal !== undefined){
                    text = `${firstVal}`+' - '+`${secondVal}`;
                }else if (firstVal1 !== undefined){
                    text = `${firstVal1}`+' - '+`${secondVal1}`;
                }
                if(text !== undefined){
                    jQuery('#capacitySpan').text(text);
                }
            });
        });
    </script>
    <?php
}

/* //embed redirect button
add_action('wp_head', 'embed_redirect_button');
function embed_redirect_button()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            var url = window.location.hostname;

            if(url == 'www.staging44.gibbs.no'){
                jQuery('.back-button-embed').show();
                jQuery('.back-button-embed-results').show();
                jQuery('.main-nav').hide();
                jQuery('#menu-new-main-menu-1').parent().hide()
                jQuery('.book-now').hide();
                jQuery('.book-now-notloggedin').hide();
                var ur = window.location.pathname;
                if(ur == '/listings/'){
                    localStorage.setItem('url',`${ur}`);
                }
                
                jQuery('#logo a').removeAttr('href');
                jQuery('#logo').on('click',function(){
                    window.open(`${localStorage.url}`,'_self');
                });
                jQuery('#booking-widget-anchor').append('<a onclick="window.open(\'https://gibbs.no${window.location.pathname}\')" class="button fullwidth margin-top-5"><span>Book på gibbs.no</span></a>');
            }    
        });

    </script>
    <?php
} */

add_action('wp_ajax_check_reservation_availability', 'check_reservation_availability');
function check_reservation_availability()
{

    global $wpdb;
    $listing_id = $_POST['listingId'];
    $date = $_POST['date'];
    $hour = explode(':',$_POST['hour']);
    $hourInteger = intval($hour[0]);
    $datesArray = [];

    $results = $wpdb->get_results("SELECT `ID`, `date_start`, `date_end`, `bookings_author` FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `listing_id` =  '$listing_id' AND DATE_FORMAT(`date_start`,'%m/%d/%Y') <= '$date' AND DATE_FORMAT(`date_end`,'%m/%d/%Y') >= '$date'");

    foreach ($results as $result){

        $start = explode(" ",$result->date_start);
        $end = explode(" ",$result->date_end);
        $firstHour = explode(":",$start[1]);
        $secondHour = explode(":",$end[1]);
        $authorName = get_userdata($result->bookings_author);
        $result->bookings_author = $authorName->display_name;
        if($start == $end){
            if($firstHour[0] <= $hourInteger && $secondHour[0] > $hourInteger){

                array_push($datesArray,$result);
            }
        }else if($firstHour[0] <= $hourInteger){
            array_push($datesArray,$result);
        }
    }

    wp_send_json(['reservations' => $datesArray]);
}

add_action('wp_head', 'change_check_availability_listing');
function change_check_availability_listing(){
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('select#selectPost').on('change',function(){
                let url = jQuery(this).children("option:selected").attr('data-address');
                window.open(url,"_self");
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_get_html_single_reservation', 'get_html_single_reservation');
function get_html_single_reservation()
{
    $template_loader = new Listeo_Core_Template_Loader;
    $booking_id = $_POST['bookingId'];
    $data = Listeo_Core_Bookings_Calendar::get_booking($booking_id);
    $html = "";
    ob_start();
    $template_loader->set_template_data( $data )->get_template_part( 'single-reservation' );
    $html = ob_get_clean();
    wp_send_json(['data' => $html]);
}

//add aditional field in user profile
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Personal/Company number", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="personal_number"><?php _e("Personal number"); ?></label></th>
        <td>
            <input type="text" name="personal_number" id="personal_number" value="<?php echo esc_attr( get_the_author_meta( 'personal_number', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your personal number."); ?></span>
        </td>
    </tr>
    <tr>    
        <th><label for="company_number"><?php _e("Company number"); ?></label></th>
        <td>
            <input type="text" name="company_number" id="company_number" value="<?php echo esc_attr( get_the_author_meta( 'company_number', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your company number."); ?></span>
        </td>
    </tr>
    <tr>
        <td style="pointer-events:none;">
            <input style="display: none;" type="text" name="cptype" id="cptype" value="<?php echo esc_attr( get_the_author_meta( 'cptype', $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
        <td style="pointer-events:none;">
            <input style="display: none;" type="text" name="discount" id="discount" value="<?php echo esc_attr( get_the_author_meta( 'discount', $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
    </table>
<?php }

//save aditional field in DB
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
function save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    update_user_meta( $user_id, 'personal_number', $_POST['personal_number'] );
    update_user_meta( $user_id, 'company_number', $_POST['company_number'] );
    update_user_meta( $user_id, 'cptype', $_POST['cptype'] );
    update_user_meta( $user_id, 'discount', $_POST['discount'] );

    if ( isset( $_POST['profile_type'] ) ){

        update_user_meta($user_id, 'profile_type', $_POST['profile_type']  );
    }


}

//download invoice
add_action('wp_head', 'download_invoice_ajax');
function download_invoice_ajax(){
    ?>
    <script>
        jQuery(document).ready(function(e){
            //download in motatte
            
            jQuery('.download-invoice').on('click', function(){
                var id = jQuery(this).attr('data-id');
                var url = jQuery(`.download-shortcode${id} p a`).attr('href');
                window.open(url);
            });
            jQuery(document).on('click','.downloadInvoice', function(){
                var both =  jQuery('.drp-selected').text();
                var from = both.split('-')[0];
                var to = both.split('-')[1];
                from = from.slice(0, -1);
                to = to.substring(1);
                var urlFromFile = '<?php $file_path = get_stylesheet_directory_uri().'/invoices.xml';
                                        echo $file_path;
                                    ?>';
                toArr = to.split('/');
                _to = `${toArr[2]}-${toArr[1]}-${toArr[0]}`;
                fromArr = from.split('/');
                _from = `${fromArr[2]}-${fromArr[1]}-${fromArr[0]}`;
                var ajax_data = {
                    'action': 'download_invoice',
                    'from': _from,
                    'to': _to
                };
                jQuery.ajax({
                    type: "POST",
                    url: listeo.ajaxurl,
                    data: ajax_data,
                    success: function () {
                        setTimeout(() => {
                            window.open(`${urlFromFile}`,'_blank');
                        }, 2000);
                    }
                }); 
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_check_availability_new_offer', 'check_availability_new_offer');
function check_availability_new_offer()
{
    Bookings_Admin_List::update_booking([
        'ID' => $_POST['data_id'],
        'date_start' =>$_POST['start_date'],
        'date_end' =>$_POST['end_date'],
        'comment' => json_encode($_POST['comment']),
        'price' => $_POST['price']
    ]);
    wp_die();
}

add_action('wp_ajax_send_unavailability', 'send_unavailability');
function send_unavailability()
{
    $listingId = $_POST['listingId'];
    $dateStart = $_POST['dateStart'];
    $newDate = date_create($dateStart);
    $dateStart = date_format($newDate,'m/d/Y');
    $dateEnd = $_POST['dateEnd'];
    $newDate = date_create($dateEnd);
    $dateEnd = date_format($newDate,'m/d/Y');
    $startHour = $_POST['hourStart'];
    $endHour = $_POST['hourEnd'];
    db_insert(null,"unavailable",$dateStart,$dateEnd,$startHour,$endHour,$listingId);
}

add_action('wp_ajax_check_reservation_unavailability', 'check_reservation_unavailability');
function check_reservation_unavailability()
{

    global $wpdb;
    $listing_id = $_POST['listingId'];
    $date = date_create($_POST['date']);
    $hour = explode(':',$_POST['hour']);
    $hourInteger = intval($hour[0]);
    $booking_id = null;

    $results = $wpdb->get_results("SELECT `id`, `date_start`, `date_end`,`hour_start`,`hour_end` FROM `" . $wpdb->prefix . "r` WHERE `listing_id` =  '$listing_id' AND `status`='unavailable'");

    foreach ($results as $result){

        $start = date_create($result->date_start);
        $end = date_create($result->date_end);
        $firstHour = intval($result->hour_start);
        $secondHour = intval($result->hour_end);
        if($start <= $date && $end >= $date){
            if($start == $end){
                if($firstHour <= $hourInteger && $secondHour > $hourInteger){
                    $booking_id = $result->id;
                }
            }else if($firstHour[0] <= $hourInteger){
                $booking_id = $result->id;
            }
        }
    }

    if (isset($booking_id)){
        return $wpdb -> delete( $wpdb->prefix . 'r', array( 'id' => $booking_id ));
    }
}


// criipto verify *hide menu items
add_action('wp_head', 'criipto_hide_item');
function criipto_hide_item()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            var str = window.location.href;
            var n = str.includes("my-bookings");
            if(n){jQuery('.pay-invoice').hide();};


        });


    </script>
    <?php
}


add_action('wp_ajax_get_user_for_discount', 'get_user_for_discount');
function get_user_for_discount()
{
    $user = $_POST['user'];
    $id = $_POST['id'];
    $discount = $_POST['discount'];

    if ( ! add_post_meta( intval($id),  $user,  $discount, true ) ) { 
        update_post_meta ( intval($id),  $user,  $discount);
    }

  
    // var_dump(get_post_meta(intval($id),  $user));
    wp_die();
}

// call check for availability for discount


// remove discount from edit listing
add_action('wp_ajax_remove_discount_edit_listing', 'remove_discount_edit_listing');
function remove_discount_edit_listing()
{
    $user = $_POST['user'];
    $id = $_POST['id'];

    if ( ! add_post_meta( intval($id),  $user,  $discount, true ) ) { 
        update_post_meta ( intval($id),  $user,  "");
    }

  
    // var_dump(get_post_meta(intval($id),  $user));
    wp_die();
}

// discount in edit listing 
add_action('wp_head', 'discount_in_edit_listing');
function discount_in_edit_listing()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('.form-field-_discount-container').removeClass('col-md-4');
            jQuery('.form-field-_discount-container').addClass('col-md-12');
            jQuery('.form-field-_discount-container .switch_box').hide();
            jQuery('.form-field-_discount-container .label-_discount').hide();
            if(jQuery('#_discount').attr('checked')){
                jQuery('.discounts .add-listing-headline').append('<div style="position: absolute; right: 93px; top: 34px; z-index: 100;" class="switch_box box_1 new-discount-switch"> <input type="checkbox" class="input-checkbox switch_1" name="_discount" id="_discount1" placeholder="discount" value="on" checked="checked" maxlength=""></div>');
                jQuery('.discounts .form-field-_discount-container').removeClass('discount-opacity');

            }else{
                jQuery('.discounts .add-listing-headline').append('<div style="position: absolute; right: 93px; top: 34px; z-index: 100;" class="switch_box box_1 new-discount-switch"> <input type="checkbox" class="input-checkbox switch_1" name="_discount" id="_discount1" placeholder="discount" value="on" maxlength=""></div>');
                jQuery('.discounts .form-field-_discount-container').addClass('discount-opacity');
            }


            jQuery('#_discount1').on('click',function(){
                jQuery('#_discount').click();
                if(jQuery('.discounts .form-field-_discount-container').hasClass('discount-opacity')){
                    jQuery('.discounts .form-field-_discount-container').removeClass('discount-opacity');

                }else{
                    jQuery('.discounts .form-field-_discount-container').addClass('discount-opacity');

                }
            });
        });


    </script>
    <?php
}


// discount in single listing -  extend discount radio btn :????

add_action('wp_head', 'extend_radio_btn');
function extend_radio_btn()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('.discount-dropdown label').on('click', function(){
                var a = jQuery(this).attr('for');
                jQuery(`.discount-dropdown input[data-id=${a}]`).click();
            });
        });
    </script>
    <?php
}

function pre_get_posts_custom_filter($query) {
    if( isset($_GET['authorid']) ) {
        $query->set('author', $_GET['authorid']);
    }
}
add_action('pre_get_posts', 'pre_get_posts_custom_filter');

add_action('wp_head', 'equipment_default_val');
function equipment_default_val()
{
    ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery(".form-field-_max_guests-container input").val() == ""){
                jQuery(".form-field-_max_guests-container input").val(1);
            }
        });
    </script>
    <?php
}

add_action('wp_head','pdf_error');
function pdf_error(){
    ?>
    <script>
        jQuery('.new_pdf_upload_for_stuff .col-md-4 input').on('change', function(){
            file = jQuery(this);
            fileSize = jQuery(this)[0].files[0].size;
            fileSize = (fileSize / 1024) /1024;
            if(fileSize > 10){
                alert('File need to be less then 10MB');
                jQuery(this).replaceWith(file.val('').clone(true));
            }else if(fileSize > 1){
                alert('Filen du har valgt er litt stor, og den trenger litt ekstra tid så den rekker å laste seg opp.');
            }
        });

        jQuery('.pdf_documents .col-md-4 input').on('change', function(){
            file = jQuery(this);
            fileSize = jQuery(this)[0].files[0].size;
            fileSize = (fileSize / 1024) /1024;
            if(fileSize > 10){
                alert('File need to be less then 10MB');
                jQuery(this).replaceWith(file.val('').clone(true));
            }else if(fileSize > 1){
                alert('Filen du har valgt er litt stor, og den trenger litt ekstra tid så den rekker å laste seg opp.');
            }
        });
    </script>
<?php
}

add_action('wp_ajax_nets_user_form', 'nets_user_form');
function nets_user_form()
{
    $user_id = wp_get_current_user();
    $user_id = $user_id->id;

    $order_id = $_POST['order_id'];
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $personOrCompany = $_POST['personOrCompany'];

    if($personOrCompany == 'dibs-company') {
        update_user_meta( $user_id, '_billing_company', $name );
        update_post_meta( $order_id, '_billing_company', $name );
        update_user_meta( $user_id, '_company_number', $last_name );
        update_post_meta( $order_id, '_company_number', $last_name );
        update_user_meta( $user_id, '_shipping_company', $name );
        update_post_meta( $order_id, '_shipping_company', $name );
    }else {
        update_post_meta($order_id, '_billing_first_name', $name);
        update_post_meta($order_id, '_billing_last_name', $last_name);
        update_post_meta($order_id, '_shipping_first_name', $name);
        update_post_meta($order_id, '_shipping_last_name', $last_name);
    }

    update_post_meta( $order_id, '_billing_email', $email );
    update_post_meta( $order_id, '_billing_phone', $phone );
    update_post_meta( $order_id, '_billing_address_1', $address );
    update_post_meta( $order_id, '_billing_postcode', $postcode );
    update_post_meta( $order_id, '_billing_city', $city );
    update_post_meta( $order_id, '_billing_country', $country );

    update_post_meta( $order_id, '_shipping_email', $email );
    update_post_meta( $order_id, '_shipping_phone', $phone );
    update_post_meta( $order_id, '_shipping_address_1', $address );
    update_post_meta( $order_id, '_shipping_postcode', $postcode );
    update_post_meta( $order_id, '_shipping_city', $city );
    update_post_meta( $order_id, '_shipping_country', $country );
}


add_action("wp_footer", "validate_edit_form_fields");
function validate_edit_form_fields(){ ?>
<script>
jQuery(document).ready(function(){
    jQuery(".menu-item").each(function(){
        var title = jQuery(this).find(".tooltip-menu").attr("title");
        if(title){
            jQuery(this).attr("title",title);
            jQuery(this).tipTip({
                content: title,
                activation: 'click', // Show tooltip on click
                keepAlive: true // Keep tooltip open until clicked again
            });
            jQuery(this).tipTip('show');
        }
    })
    // jQuery(".menu-item").on("click",function(){
    //     var title = jQuery(this).find(".tooltip-menu").attr("title");
    //     if(title){
    //         debugger;
    //         jQuery(this).attr("title",title);
    //         jQuery(this).tipTip({
    //             content: title,
    //             activation: 'click', // Show tooltip on click
    //             keepAlive: true // Keep tooltip open until clicked again
    //         });
    //         jQuery(this).tipTip('show');
    //     }
        
    // })
     var inputs = document.querySelectorAll(".phonenumber");

                inputs.forEach(function(input){
                    var iti = window.intlTelInput(input, {
                            initialCountry: "no",
                            allowExtensions: true,
                            formatOnDisplay: true,
                            autoFormat: true,
                            numberType: "MOBILE",
                            preventInvalidNumbers: true,
                            separateDialCode: true,
                            utilsScript: "<?php echo home_url();?>/wp-content/themes/listeo-child/assets/js/utils.js?ver=5.7.2",
                    });
                    input.addEventListener("countrychange", function(e) {
                        var pnumber = iti.getSelectedCountryData().dialCode;
                        jQuery(".country_code").val(pnumber);
                    });
                })
    jQuery(document).on("click",".pw-icon", function(){
        
        if(jQuery(this).hasClass("fa-eye-slash")){
            jQuery(this).addClass("fa-eye").removeClass("fa-eye-slash");
            jQuery(this).parent().parent().find("input[name=password]").attr("type","text");
        }else{
            jQuery(this).addClass("fa-eye-slash").removeClass("fa-eye");
            jQuery(this).parent().parent().find("input[name=password]").attr("type","password");
        }
    })


   
    jQuery("input#phone").attr("maxlength", "8");
    jQuery("input#phone").attr("required", "required");
    jQuery("button[form='edit_user']").attr("onclick", "return confirm('Er du sikker på at du vil lagre?')");
    
    jQuery("#booking-confirmation").on('submit', function(e){
        if(jQuery("#booking_message").val() === ""){
            jQuery("#booking_message").closest("#label_message> span").addClass("show-inline");
            e.preventDefault();
        }else{
            jQuery("#booking_message").closest("#label_message> span").removeClass("show-inline");
        };
        
        var windowsize = jQuery(window).width();
        jQuery(window).resize(function() {
            windowsize = jQuery(window).width();
            if (windowsize < 480) {
                jQuery(".main-nav").addClass("main-nav-small");
            }else{
                jQuery(".main-nav").removeClass("main-nav-small");
            }
        });
        windowsize = jQuery(window).width();
        if (windowsize < 480) {
            jQuery(".main-nav").addClass("main-nav-small");
        }else{
            jQuery(".main-nav").removeClass("main-nav-small");
        }
    })

  
})
</script>
<?php }

function listeo_count_my_bookings_with_status($user_id,$status){
    global $wpdb;
    if($status == 'waiting') {
        $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation') AND (`status` = '$status')", "ARRAY_A");
    }elseif($status == 'attention'){
        $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation') AND (`status` = '$status')", "ARRAY_A");
    }elseif($status == 'confirmed'){
        $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation') AND (`status` = '$status') AND (`expiring` > NOW())", "ARRAY_A");
    }else
        $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation') AND (`status` = '$status')", "ARRAY_A");
    return $wpdb->num_rows;
}


function my_login_logo() { ?>
<style type=”text/css”>
#login h1 a, .login h1 a {
background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/Frame-966.svg);
height:65px;
width:320px;
background-size: 320px 65px;
background-repeat: no-repeat;
padding-bottom: 30px;
}
</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );



add_action('wp_ajax_listeo_get_sub_region_category', 'listeo_get_sub_region_category');
add_action('wp_ajax_nopriv_listeo_get_sub_region_category', 'listeo_get_sub_region_category');


function listeo_get_sub_region_category(){


    //echo "<pre>"; print_r($_POST); die;
   
    $all_cat = array();
    $multi_select = "";
    /*foreach ($_POST["cat_ids"] as $key => $cats_id) {
        $multi_select = get_term_meta( $cats_id, 'mutiselect_category', true );
        if($cats_id != ""){
            $cats = get_terms($_POST['taxonomy'],array(
                'hide_empty'    => 0,
                'parent'        => $cats_id,
            ));

            if(!empty($cats)){
               foreach ($cats as $key => $cat) {
                   $all_cat[] = $cat;
               }
            }
        }
        
    }*/
    $cats = get_terms($_POST['taxonomy'],array(
                'hide_empty'    => 0,
            ));

    if(!empty($cats)){
       foreach ($cats as $key => $cat) {
            if($cat->parent != 0){
               $all_cat[] = $cat;
            }
       }
    }

    $all_selected_categores = array();
    if($_POST["list_id"] != ""){
        $listing_top_categories = wp_get_object_terms($_POST["list_id"], $_POST['taxonomy']);
        foreach ($listing_top_categories as $key => $listing_top_cat) {
            $all_selected_categores[] = $listing_top_cat->term_id;
            
        }
    }
    $html = "";

 

    /*if($multi_select == "on"){
         $multiple = "multiple";
    }else{
        $multiple = "";
    }*/
  /*  if($_POST["multiselected"] == "1"){
        $multiple = "multiple";
    }else{
        $multiple = "";
    }*/
    if(!empty($all_cat)){
        $html =  "<label>By</label><select ".$_POST["multiselected"]." class='chosen-select-no-single' name='subregion[]'";
        $html .= "id=\"subregionselect\">";
        $html .= "<option parent_id='' value=''>".__("Select","gibbs")."</option>";

        foreach($all_cat as $category){

            $term_children = get_term_children($category->term_id, $_POST['taxonomy']);

            $html .= "<option parent_id='".$category->parent."' value=\"".$category->term_id."\"";
            if(!empty($term_children) && !is_wp_error($term_children)){
                $html .= " class=\"hasChildCategories\" ";  
            }
            if(!empty($all_selected_categores)){
                if(in_array($category->term_id, $all_selected_categores)){
                    $html .= " selected "; 
                }
            }
            $html .= ">". $category->name ."</option>";
            $childrenOfFirstLv = get_terms($_POST['taxonomy'], array(
                'hide_empty'    => 0,
                'parent'        => $category->term_id,
            ));
            $firstLvMap[$category->term_id] = array();
            foreach($childrenOfFirstLv as $subCat){
                array_push($firstLvMap[$category->term_id], $subCat->term_id);
            }
        }

        $html .= "</select>";

        $json_data = array("error"=>0,"output"=>$html,"cat_ids"=>array());
    }else{
        $json_data = array("error"=>0,"output"=>0,"cat_ids"=>$_POST["cat_ids"]);

    }



    echo json_encode($json_data);


    die;
        
}

add_action('wp_ajax_listeo_get_sub_category', 'listeo_get_sub_category');
add_action('wp_ajax_nopriv_listeo_get_sub_category', 'listeo_get_sub_category');


function listeo_get_sub_category(){
   
    $all_cat = array();
    $multi_select = "";
    foreach ($_POST["cat_ids"] as $key => $cats_id) {
        $multi_select = get_term_meta( $cats_id, 'mutiselect_category', true );
        if($cats_id != ""){
            $cats = get_terms($_POST['taxonomy'],array(
                'hide_empty'    => 0,
                'parent'        => $cats_id,
            ));

            if(!empty($cats)){
               foreach ($cats as $key => $cat) {
                   $all_cat[] = $cat;
               }
            }
        }
        
    }
    $all_selected_categores = array();
    if($_POST["list_id"] != ""){
        $listing_top_categories = wp_get_object_terms($_POST["list_id"], $_POST['taxonomy']);
        foreach ($listing_top_categories as $key => $listing_top_cat) {
            $all_selected_categores[] = $listing_top_cat->term_id;
            
        }
    }
    $html = "";

    if($_POST['taxonomy'] == "service_category"){
        $typpee = "service";
    }elseif($_POST['taxonomy'] == "event_category"){
        $typpee = "event";
    }elseif($_POST['taxonomy'] == "rental_category"){
        $typpee = "rental";
    }else{
        $typpee = "classifieds";
    }






    $listing_form_multisubcategory_value = get_option("listing_form_".$typpee."_multisubcategory"); 



    if($multi_select == "on"){
         $multiple = "multiple";
    }else{
        $multiple = "";
    }
   /* if($_POST["multiselected"] == "1"){
        $multiple = "multiple";
    }else{
        $multiple = "";
    }*/
    if(!empty($all_cat)){
        $html =  "<label>Underkategori</label><select ".$multiple." class='chosen-select-no-single' name='subcats[]'";
        $html .= "id=\"subcategories\">";

        foreach($all_cat as $category){

            $term_children = get_term_children($category->term_id, $_POST['taxonomy']);

            $html .= "<option value=\"".$category->term_id."\"";
            if(!empty($term_children) && !is_wp_error($term_children)){
                $html .= " class=\"hasChildCategories\" ";  
            }
            if(!empty($all_selected_categores)){
                if(in_array($category->term_id, $all_selected_categores)){
                    $html .= " selected "; 
                }
            }
            $html .= ">". $category->name ."</option>";
            $childrenOfFirstLv = get_terms($_POST['taxonomy'], array(
                'hide_empty'    => 0,
                'parent'        => $category->term_id,
            ));
            $firstLvMap[$category->term_id] = array();
            foreach($childrenOfFirstLv as $subCat){
                array_push($firstLvMap[$category->term_id], $subCat->term_id);
            }
        }

        $html .= "</select>";

        $json_data = array("error"=>0,"output"=>$html,"cat_ids"=>array());
    }else{
        $json_data = array("error"=>0,"output"=>0,"cat_ids"=>$_POST["cat_ids"]);

    }



    echo json_encode($json_data);


    die;
        
}

add_action('wp_ajax_listeo_get_sub_sub_category', 'listeo_get_sub_sub_category');
add_action('wp_ajax_nopriv_listeo_get_sub_sub_category', 'listeo_get_sub_sub_category');


function listeo_get_sub_sub_category(){
   
    $all_cat = array();
    $multi_select = "";
    foreach ($_POST["cat_ids"] as $key => $cats_id) {
        $multi_select = get_term_meta( $cats_id, 'mutiselect_category', true );
        if($cats_id != ""){
            $cats = get_terms($_POST['taxonomy'],array(
                'hide_empty'    => 0,
                'parent'        => $cats_id,
            ));

            if(!empty($cats)){
               foreach ($cats as $key => $cat) {
                   $all_cat[] = $cat;
               }
            }
        }
        
    }
    $all_selected_categores = array();
    if($_POST["list_id"] != ""){
        $listing_top_categories = wp_get_object_terms($_POST["list_id"], $_POST['taxonomy']);
        foreach ($listing_top_categories as $key => $listing_top_cat) {
            $all_selected_categores[] = $listing_top_cat->term_id;
            
        }
    }
    $html = "";

    if($_POST['taxonomy'] == "service_category"){
        $typpee = "service";
    }elseif($_POST['taxonomy'] == "event_category"){
        $typpee = "event";
    }elseif($_POST['taxonomy'] == "rental_category"){
        $typpee = "rental";
    }else{
        $typpee = "classifieds";
    }






    $listing_form_multisubcategory_value = get_option("listing_form_".$typpee."_multisubcategory"); 



    if($multi_select == "on"){
         $multiple = "multiple";
    }else{
        $multiple = "";
    }
   /* if($_POST["multiselected"] == "1"){
        $multiple = "multiple";
    }else{
        $multiple = "";
    }*/
    if(!empty($all_cat)){
        $html =  "<label>Subkategori</label><select ".$multiple." class='chosen-select-no-single' name='subcats2[]'";
        $html .= "id=\"subcategories2\">";

        foreach($all_cat as $category){

            $term_children = get_term_children($category->term_id, $_POST['taxonomy']);

            $html .= "<option value=\"".$category->term_id."\"";
            if(!empty($term_children) && !is_wp_error($term_children)){
                $html .= " class=\"hasChildCategories\" ";  
            }
            if(!empty($all_selected_categores)){
                if(in_array($category->term_id, $all_selected_categores)){
                    $html .= " selected "; 
                }
            }
            $html .= ">". $category->name ."</option>";
            $childrenOfFirstLv = get_terms($_POST['taxonomy'], array(
                'hide_empty'    => 0,
                'parent'        => $category->term_id,
            ));
            $firstLvMap[$category->term_id] = array();
            foreach($childrenOfFirstLv as $subCat){
                array_push($firstLvMap[$category->term_id], $subCat->term_id);
            }
        }

        $html .= "</select>";

        $json_data = array("error"=>0,"output"=>$html,"cat_ids"=>array());
    }else{
        $json_data = array("error"=>0,"output"=>0,"cat_ids"=>$_POST["cat_ids"]);

    }



    echo json_encode($json_data);


    die;
        
}

add_action('wp_ajax_get_features_ids_from_category_ids', 'get_features_ids_from_category_ids');
add_action('wp_ajax_nopriv_get_features_ids_from_category_ids', 'get_features_ids_from_category_ids');

 function get_features_ids_from_category_ids(){
        
        $categories  = isset($_REQUEST['cat_ids']) ? $_REQUEST['cat_ids'] : false;
        $panel  =  $_REQUEST['panel'];
        $selected  =  isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $listing_id  =  $_REQUEST['list_id'];
        $success = true;
        if(!$selected){
            if($listing_id){
                $selected_check = wp_get_object_terms( $listing_id, 'listing_feature', array( 'fields' => 'ids' ) ) ;
                if ( ! empty( $selected_check ) ) {
                    if ( ! is_wp_error( $selected_check ) ) {
                        $selected = $selected_check;
                    }
                }
            }
        };

        if(is_array($selected)){

            if(is_array($_REQUEST['checked_ids'])){

           
              
               $selected =  array_merge($selected,$_REQUEST['checked_ids']);
            }

        }else{
            $selected = $_REQUEST['checked_ids'];
        }


        ob_start();


        if($categories){
        
            $features = array();
            foreach ($categories as $category) {
                if(is_numeric($category)) {
                    $cat_object = get_term_by('id', $category, $_POST['taxonomy']); 
                } else {
                    $cat_object = get_term_by('slug', $category, $_POST['taxonomy']);   
                }



                if($cat_object){
                    $features_temp = get_term_meta( $cat_object->term_id, 'listeo_taxonomy_multicheck', true );
                    if($features_temp){
                        foreach ($features_temp as $key => $value) {
                            $features[] = $value;
                        }
                    }
                    
                    // if($features_temp) {
                    //  $features = $features + $features_temp;
                    // }
                }
            }
        
        
        
            
            $features = array_unique($features);

            if($features){
                if($panel != 'false'){ ?>
                    <div class="panel-checkboxes-container">
                    <?php
                        $groups = array_chunk($features, 4, true);
                                
                        foreach ($groups as $group) { ?>
                            
                            <?php foreach ($group as $feature) { 
                                $feature_obj = get_term_by('slug', $feature, 'listing_feature'); 
                                if( !$feature_obj ){
                                    continue;
                                }
                                
                                ?>
                                <div class="panel-checkbox-wrap">
                                    <input form="listeo_core-search-form"  value="<?php echo esc_html($feature_obj->term_id) ?>" type="checkbox" id="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" name="tax_input[listing_feature][]" >
                                    <label for="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>"><?php echo $feature_obj->name; ?></label> 
                                </div>
                            <?php } ?>
                            

                        <?php } ?>
                    
                    </div>
                <?php } else {

    
                    foreach ($features as $feature) { 
                        $feature_obj = get_term_by('slug', $feature, 'listing_feature');
                        if( !$feature_obj ){
                            continue;
                        }
                        ?>
                        <input  <?php if($selected) checked( in_array(  $feature_obj->term_id, $selected ) ); ?>value="<?php echo esc_html($feature_obj->term_id) ?>" type="checkbox" id="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" name="tax_input[listing_feature][]" >
                        <label id="label-in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" for="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>"><?php echo $feature_obj->name; ?></label>
                    <?php }
                }
            } else { 
                if($cat_object){

                
                if( $cat_object->name) { 
                    $success = false; ?>
                <div class="notification notice <?php if($panel){ echo "col-md-12"; } ?>" style="display:none">
                    <style type="text/css">
                    .label-listing_feature{
                        display: none;
                    }
                    </style>
                    <p>
                    <?php //printf( __( 'Category "%s" doesn\'t have any additional filters', 'listeo_core' ), $cat_object->name )  ?>
                        
                    </p>
                </div>
                <?php 
                }
            } else { 
                    $success = false; ?>
                    <style type="text/css">
                    .form-field-listing_feature-container{
                        display: none;
                    }
                    </style>
                <div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
            <?php }
                }
            } else {
            $success = false; ?>
            <style type="text/css">
            .form-field-listing_feature-container{
                display: none;
            }
            </style>
            <div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
        <?php }
                
        $result['output'] = ob_get_clean();
        $result['success'] = $success;
        wp_send_json($result);
    }

/*add_action('admin_menu', 'my_menu_pages');
function my_menu_pages(){
    add_submenu_page('listeo-fields-and-form', 'Submenu Page Title', 'Add listing form settings', 'manage_options', 'listing_form_setting','listing_form_setting' );
}    

function listing_form_setting(){
    if(isset($_POST["save_listing_setting"])){
        if(isset($_POST["listing_form_service_multisubcategory"])){
            $listing_form_service_multisubcategory = "1";
        }else{
            $listing_form_service_multisubcategory = "0";
        }
        delete_option("listing_form_service_multisubcategory");
        add_option("listing_form_service_multisubcategory",$listing_form_service_multisubcategory);

        if(isset($_POST["listing_form_event_multisubcategory"])){
            $listing_form_event_multisubcategory = "1";
        }else{
            $listing_form_event_multisubcategory = "0";
        }
        delete_option("listing_form_event_multisubcategory");
        add_option("listing_form_event_multisubcategory",$listing_form_event_multisubcategory);

        if(isset($_POST["listing_form_rental_multisubcategory"])){
            $listing_form_rental_multisubcategory = "1";
        }else{
            $listing_form_rental_multisubcategory = "0";
        }
        delete_option("listing_form_rental_multisubcategory");
        add_option("listing_form_rental_multisubcategory",$listing_form_rental_multisubcategory);

        if(isset($_POST["listing_form_classifieds_multisubcategory"])){
            $listing_form_classifieds_multisubcategory = "1";
        }else{
            $listing_form_classifieds_multisubcategory = "0";
        }
        delete_option("listing_form_classifieds_multisubcategory");
        add_option("listing_form_classifieds_multisubcategory",$listing_form_classifieds_multisubcategory);
    }

    $listing_form_service_multisubcategory_value = get_option("listing_form_service_multisubcategory");
    $listing_form_event_multisubcategory_value = get_option("listing_form_event_multisubcategory");
    $listing_form_rental_multisubcategory_value = get_option("listing_form_rental_multisubcategory");
    $listing_form_classifieds_multisubcategory_value = get_option("listing_form_classifieds_multisubcategory");

    
    ?>
     <h1> <?php esc_html_e( 'Add listing form settings.', 'my-textdomain' ); ?> </h1>
    <form method="POST" action="">
    <?php
    ?>
    <table>
        <tr>
            <td><label for="my_setting_field" style="font-size: 16px;padding-right: 31px;"><?php _e( 'Service subcategory multi select', 'my-textdomain' ); ?></label></td>
            <td><input type="checkbox" id="listing_form_service_multisubcategory" name="listing_form_service_multisubcategory" value="1" <?php if($listing_form_service_multisubcategory_value =="1"){ echo "checked"; }?>></td>
        </tr>
        <tr>
            <td><label for="my_setting_field" style="font-size: 16px;padding-right: 31px;"><?php _e( 'Event subcategory multi select', 'my-textdomain' ); ?></label></td>
            <td><input type="checkbox" id="listing_form_event_multisubcategory" name="listing_form_event_multisubcategory" value="1" <?php if($listing_form_event_multisubcategory_value =="1"){ echo "checked"; }?>></td>
        </tr>
        <tr>
            <td><label for="my_setting_field" style="font-size: 16px;padding-right: 31px;"><?php _e( 'Rental subcategory multi select', 'my-textdomain' ); ?></label></td>
            <td><input type="checkbox" id="listing_form_rental_multisubcategory" name="listing_form_rental_multisubcategory" value="1" <?php if($listing_form_rental_multisubcategory_value =="1"){ echo "checked"; }?>></td>
        </tr>
        <tr>
            <td><label for="my_setting_field" style="font-size: 16px;padding-right: 31px;"><?php _e( 'Classifieds subcategory multi select', 'my-textdomain' ); ?></label></td>
            <td><input type="checkbox" id="listing_form_classifieds_multisubcategory" name="listing_form_classifieds_multisubcategory" value="1" <?php if($listing_form_classifieds_multisubcategory_value =="1"){ echo "checked"; }?>></td>
        </tr>
        <tr>
             <td colspan="2">
             <input type="hidden" name="save_listing_setting" value="1">
               <?php
                submit_button();
                ?>
                 
             </td>
        </tr>
    </table>
    
    
    
   
    </form>
    <?php
}*/

function buildTree($items) {
    foreach ($items as $item_d) {

        $parent_check = false;
        foreach ($items as $ite) {
           if($item_d->parent == 0 || $item_d->parent == null || $item_d->parent == $ite->term_id){
               $parent_check = true;
           }
        }

        if($parent_check == false){
            $item_d->parent = 0;
        }

    }

  //  echo "<pre>";print_r($items); die;
  $childs = array();
 foreach($items as &$item)
   if($item->parent == null){
     $childs[0][] = &$item;
   }else{
     $childs[$item->parent][] = &$item;
   }
  unset($item);
 foreach($items as &$item) if (isset($childs[$item->term_id]))
         $item->childs = $childs[$item->term_id];
        return $childs[0];
}

function listeo_core_get_checkbox_array_hierarchical($terms, $selected, $output = '', $parent_id = 0, $level = 0) {
    //Out Template
    $outputTemplate = '<input type="checkbox" %SELECED% value="%ID%">%PADDING%%NAME%';

    foreach ($terms as $term) {
        if ($parent_id == $term->parent) {
            if(is_array($selected)) {
                $is_selected = in_array( $term->slug, $selected ) ? ' selected="selected" ' : '';
            } else {
                $is_selected = selected($selected, $term->slug, false);
            }
            //Replacing the template variables
            $itemOutput = str_replace('%SELECED%', $is_selected, $outputTemplate);
            $itemOutput = str_replace('%ID%', $term->slug, $itemOutput);
            $itemOutput = str_replace('%PADDING%', str_pad('', $level*12, '&nbsp;&nbsp;'), $itemOutput);
            $itemOutput = str_replace('%NAME%', $term->name, $itemOutput);

            $output .= $itemOutput;
            $output = listeo_core_get_checkbox_array_hierarchical($terms, $selected, $output, $term->term_id, $level + 1);
        }
    }
    return $output;
}


add_action( 'widgets_init', 'remove_theme_widgets', 11 );

function remove_theme_widgets() {
    unregister_sidebar('footer5');
}

add_action( 'widgets_init', 'listeo_widgets_init2',12 );
function listeo_widgets_init2(){

    register_sidebar(array(
        'id' => 'footer5',
        'name' => esc_html__('Footer 5th Column', 'listeo' ),
        'description' => esc_html__('5th column for widgets in Footer', 'listeo' ),
        'before_widget' => '<aside id="%1$s" class="footer-widget widget %2$s">',
        'after_widget' => '</aside>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
        ));

    register_sidebar(array(
        'id' => 'footer6',
        'name' => esc_html__('Footer 6th Column', 'listeo' ),
        'description' => esc_html__('6th column for widgets in Footer', 'listeo' ),
        'before_widget' => '<aside id="%1$s" class="footer-widget widget %2$s">',
        'after_widget' => '</aside>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
        ));
	
	  register_sidebar(array(
        'id' => 'loginblocks',
        'name' => esc_html__('Login Block', 'listeo' ),
		'before_widget' => '<aside class="loginButton">',
        'after_widget' => '</aside>',
        ));
	

}


function wpse_307202_term_link( $termlink, $term, $taxonomy ) {
   

    if($taxonomy == "listing_category" || $taxonomy == "service_category" || $taxonomy == "rental_category" || $taxonomy == "event_category")
    {
        if(isset($term->term_id)){
            $termm_slug = get_term($term->term_id)->slug;
            $texnomyy = str_replace('_category', "", $taxonomy);
            $termlink = home_url()."/listings/?_listing_type=".$texnomyy."&tax-".$taxonomy."[]=".$termm_slug;
           
        }
        
    } 

    return $termlink;
}
add_filter( 'term_link', 'wpse_307202_term_link', 10, 3 );

add_action('wp_ajax_save_title', 'save_title', 10);
add_action('wp_ajax_nopriv_save_title', 'save_title', 10);

function save_title()
{
    if(isset($_POST["listing_id"]) && $_POST["listing_id"] != ""){
        global $wpdb;
        $title = $_POST["title"];
        $where = array( 'ID' => $_POST["listing_id"] );
        $wpdb->update( $wpdb->posts, array( 'post_title' => $title ), $where );
    }
    die;

}

add_action('wp_ajax_save_desc', 'save_desc', 10);
add_action('wp_ajax_nopriv_save_desc', 'save_desc', 10);

function save_desc()
{
    if(isset($_POST["listing_id"]) && $_POST["listing_id"] != ""){
        global $wpdb;
        $desc = $_POST["desc"];
        $where = array( 'ID' => $_POST["listing_id"] );
        $wpdb->update( $wpdb->posts, array( 'post_content' => $desc ), $where );
    }
    die;

}


add_action('wp_ajax_save_listing_field', 'save_listing_field', 10);
add_action('wp_ajax_nopriv_save_listing_field', 'save_listing_field', 10);

function save_listing_field()
{
    if(isset($_POST["listing_id"]) && $_POST["listing_id"] != ""){

        $fieldd = update_post_meta($_POST["listing_id"],$_POST["field_name"],$_POST["field_value"],false); 
    }
    die;

}

function spam_stop() {
    global $_POST;
    if(isset($_POST["website"]) && $_POST["website"] != ""){
        die;
    }
}
add_action( 'login_init', 'spam_stop', 101);

//add_filter( 'use_widgets_block_editor', '__return_false' );


    add_action( 'cmb2_admin_init', 'listeo_register_disable_metabox_pages' );
    /**
     * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
     */
    function listeo_register_disable_metabox_pages() {
        $prefix = 'listeo_';

        
        /* get the registered sidebars */
        global $wp_registered_sidebars;

        $sidebars = array();
        foreach( $wp_registered_sidebars as $id=>$sidebar ) {
          $sidebars[ $id ] = $sidebar[ 'name' ];
        }
     
 /*        $listeo_page_mb_logined = new_cmb2_box( array(
            'id'            => 'book_with_login_main',
            'title'         => esc_html__( 'Book with login', 'listeo' ),
            'object_types'  => array( 'listing' ), // Post type
            'priority'   => 'high',
        ) );
        $listeo_page_mb_logined->add_field( array(
            'name' => esc_html__( 'Enable Book with login', 'listeo' ),
            'desc' => esc_html__( 'Enable Book with login', 'listeo' ),
            'id'   => 'book_with_login',
            'type' => 'checkbox', //#303133
            
        ) ); */

        if(isset($_REQUEST["post"])){
            $list_id = $_REQUEST["post"];
            $posttt = get_post($list_id);
            if(isset($posttt->post_parent) && $posttt->post_parent != 0){
                $posttt_parentt_data = get_post($posttt->post_parent);
                if(isset($posttt_parentt_data->ID)){
                    if(isset($listeo_page_mb2) && !empty($listeo_page_mb2)){
                        $listeo_page_mb2->add_field( array(
                            'name' => esc_html__( 'Parent listing id', 'listeo' ),
                            'id'   => $prefix . 'post_parent_id',
                            'type' => 'text', //#303133
                            'default' => $posttt_parentt_data->post_title."(".$posttt_parentt_data->ID.")", //#303133
                            'attributes'  => array(
                                'readonly' => 'readonly',
                            ),

                        ) );
                    }
                }

            }
            

        }

      /*   $listeo_page_mb3 = new_cmb2_box( array(
            'id'            => $prefix . 'disbaled_mail_listing_metabox',
            'title'         => esc_html__( 'Disabled send mail', 'listeo' ),
            'object_types'  => array( 'page','post','listing'), // Post type
            'priority'   => 'high',
        ) );
        $listeo_page_mb3->add_field( array(
            'name' => esc_html__( 'Disable gibbs booking email for owner', 'listeo' ),
            'desc' => esc_html__( 'Disable gibbs booking email for owner', 'listeo' ),
            'id'   => $prefix . 'disbaled_send_mail_for_owner',
            'type' => 'checkbox', //#303133
            
        ) );

        $listeo_page_mb3->add_field( array(
            'name' => esc_html__( 'Disable gibbs booking email for buyer', 'listeo' ),
            'desc' => esc_html__( 'Disable gibbs booking email for buyer', 'listeo' ),
            'id'   => $prefix . 'disbaled_send_mail_for_buyer',
            'type' => 'checkbox', //#303133
            
        ) ); */
/*
        $listeo_page_mb4 = new_cmb2_box( array(
            'id'            => $prefix . 'listing_widget_text',
            'title'         => esc_html__( 'Listing widget text', 'listeo' ),
            'object_types'  => array( 'page','post','listing'), // Post type
            'priority'   => 'high',
        ) );
        $listeo_page_mb4->add_field( array(
            'name' => esc_html__( 'Enable widget', 'listeo' ),
            'desc' => esc_html__( 'Enable widget', 'listeo' ),
            'id'   => $prefix . 'listing_widget_text_show',
            'type' => 'checkbox', //#303133
            
        ) );
        $listeo_page_mb4->add_field( array(
            'name' => esc_html__( 'Widget title', 'listeo' ),
            'desc' => esc_html__( '', 'listeo' ),
            'id'   => $prefix . 'listing_widget_text_title',
            'type' => 'text', //#303133
            
        ) );
        $listeo_page_mb4->add_field( array(
            'name' => esc_html__( 'Widget description', 'listeo' ),
            'desc' => esc_html__( '', 'listeo' ),
            'id'   => $prefix . 'listing_widget_text_desc',
            'type' => 'wysiwyg', //#303133
            
        ) );
*/
       /* $listeo_page_mb5 = new_cmb2_box( array(
            'id'            => $prefix . 'hide_price_div_main',
            'title'         => esc_html__( 'Hide price div', 'listeo' ),
            'object_types'  => array( 'page','post','listing'), // Post type
            'priority'   => 'high',
        ) );
        $listeo_page_mb5->add_field( array(
            'name' => esc_html__( 'Hide price div', 'listeo' ),
            'desc' => esc_html__( 'Hide price div', 'listeo' ),
            'id'   => $prefix . 'hide_price_div',
            'type' => 'checkbox', //#303133
            
        ) );*/
        

    }

add_action( 'show_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'crf_show_extra_profile_fields' );
function make_post_parent_public_qv() {
    if ( is_admin() )
        $GLOBALS['wp']->add_query_var( 'post_parent' );
}
add_action( 'init', 'make_post_parent_public_qv' );

function crf_show_extra_profile_fields( $user ) {
    $commission = get_the_author_meta( 'commission', $user->ID );
    ?>
    <h3><?php esc_html_e( 'Commission', 'crf' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="commission"><?php esc_html_e( 'Commission (%)', 'crf' ); ?></label></th>
            <td>
                <input type="number"
                   id="commission"
                   name="commission"
                   value="<?php echo esc_attr( $commission ); ?>"
                   class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'show_user_profile', 'extra_country_code_fields', 10, 1 );
add_action( 'edit_user_profile', 'extra_country_code_fields', 10, 1 );
function extra_country_code_fields( $user ) {
    $country_code = get_user_meta( $user->ID, 'country_code', true);
    ?>

    <table class="form-table">
        <tr>
            <th><label for="country code"><?php esc_html_e( 'Country code', 'crf' ); ?></label></th>
            <td>
                <input type="text"
                   id="country_code"
                   name="country_code"
                   value="<?php echo $country_code; ?>"
                   class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}



add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }


    if(isset($_POST["hide_application_menu_in_sidebar_for_user"]) && $_POST["hide_application_menu_in_sidebar_for_user"] != ""){
        update_user_meta( $user_id, 'hide_application_menu_in_sidebar_for_user', "false" );
    }else{
         update_user_meta( $user_id, 'hide_application_menu_in_sidebar_for_user', "" );
    }

    if(isset($_POST["profile_type"])){
        update_user_meta( $user_id, 'profile_type', $_POST['profile_type'] );
    }

    if ( ! empty( $_POST['commission'] ) ) {
        update_user_meta( $user_id, 'commission', intval( $_POST['commission'] ) );
    }
}
/*add_filter('gettext', 'wc_renaming_checkout_total', 20, 3);
function wc_renaming_checkout_total( $translated_text, $untranslated_text, $domain ) {
   
        if( $translated_text == 'Fees:' && $domain == "woocommerce"){
            $translated_text = __( 'Total mva:','woocommerce' );
        }
    return $translated_text;
}*/

/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) { 
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}


add_action('wp_ajax_listeo_editor_get_group_gyms', 'editor_get_group_gyms');
add_action('wp_ajax_nopriv_listeo_editor_get_group_gyms', 'editor_get_group_gyms');

add_action('wp_ajax_listeo_editor_get_group_sports', 'editor_get_group_sports');
add_action('wp_ajax_nopriv_listeo_editor_get_group_sports', 'editor_get_group_sports');

add_action('wp_ajax_listeo_editor_get_group', 'editor_get_group');
add_action('wp_ajax_nopriv_listeo_editor_get_group', 'editor_get_group');

add_action('wp_ajax_listeo_editor_add_new_gym', 'editor_add_new_gym');
add_action('wp_ajax_nopriv_listeo_editor_add_new_gym', 'editor_add_new_gym');

add_action('wp_ajax_listeo_editor_add_new_sport', 'editor_add_new_sport');
add_action('wp_ajax_nopriv_listeo_editor_add_new_sport', 'editor_add_new_sport');

function editor_get_group() {
    $post_id = $_POST['post_id'];

    $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : null;
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;
    $group_data = "";
    if($listing_id) {
        $group_data = get_post_field('users_groups_id', $listing_id);
    }
    if($post_id){
        $group_data = get_post_field('users_groups_id', $post_id);
    }

    if($group_data){
        $result = array(
            'status' => 200,
            'group_selected' => $group_data
        );
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }
    wp_send_json($result);
}

function editor_get_group_gyms() {
    $user_group_id = $_POST['user_group_id'];

    $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : null;
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;
    // retrieve all inserted _listing_gym postmeta and return back
    $listing_gym_data = "";
    if($listing_id) {
        // $listing_gym_data = get_post_meta($listing_id, '_listing_gym');
        $listing_gym_data = get_post_field('gym_id', $listing_id);
    }
    if($post_id){
        // $listing_gym_data = get_post_meta($post_id, '_listing_gym', true);
        $listing_gym_data = get_post_field('gym_id', $post_id);
    }

    $result = [];
    ob_start();
    global $wpdb;
    $gym_table = 'gym';
    if($user_group_id){
        $query = "SELECT id,name FROM $gym_table WHERE users_groups_id = $user_group_id";
        $gym_id_data = $wpdb->get_results($query);
        // $gym_id_data = json_decode(json_encode($gym_id_data), true);
        // print_r($gym_id_data);
        $result = array(
            'status' => 200,
            'data' => $gym_id_data,
            'listing_gym_selected' => $listing_gym_data
        );
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }

    $result['output'] = ob_get_clean();
    wp_send_json($result);
}

function editor_get_group_sports() {
    $user_group_id = $_POST['user_group_id'];

    $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : null;
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;
    // retrieve all inserted _listing_sports postmeta and return back
    $listing_sports_data = "";
    if($listing_id) {
        $listing_sports_data = get_post_meta($listing_id, '_listing_sports');
    }
    if($post_id){
        $listing_sports_data = get_post_meta($post_id, '_listing_sports');
    }

    $result = [];
    ob_start();
    global $wpdb;
    $sport_table = 'sport';
    if($user_group_id){
        $query = "SELECT id,name FROM $sport_table WHERE users_groups_id = $user_group_id";
        $sport_id_data = $wpdb->get_results($query);
        $result = array(
            'status' => 200,
            'data' => $sport_id_data,
            'listing_sports_selected' => $listing_sports_data,
        );
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }

    $result['output'] = ob_get_clean();
    wp_send_json($result);
}


function editor_add_new_gym() {
    $new_gym = $_POST['new_gym'];
    $user_group_id = $_POST['user_group_id'];

    $result = [];
    ob_start();
    global $wpdb;
    $gym_table = 'gym';
    $insert = '';
    if($new_gym){
        $insert .= $wpdb->prepare( '( %d, %s)', $user_group_id, $new_gym);

        $inserted = $wpdb->query( "INSERT INTO gym ( users_groups_id, name ) VALUES " . $insert );
        if($inserted) {
            $result = array(
                'status' => 200,
                'data' => $sport_id_data
            );
        }
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }

    $result['output'] = ob_get_clean();
    wp_send_json($result);
}


function editor_add_new_sport() {
    $new_sport = $_POST['new_sport'];
    $user_group_id = $_POST['user_group_id'];

    $result = [];
    ob_start();
    global $wpdb;
    $sport_table = 'sport';
    $insert = '';
    if($new_sport){
        $insert .= $wpdb->prepare( '( %d, %s)', $user_group_id, $new_sport);

        $inserted = $wpdb->query( "INSERT INTO sport ( users_groups_id, name ) VALUES " . $insert );
        if($inserted) {
            $result = array(
                'status' => 200,
                'data' => $sport_id_data
            );
        }
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }

    $result['output'] = ob_get_clean();
    wp_send_json($result);
}
add_action(  'admin_enqueue_scripts', 'listeo_admin_group_scripts' );

function listeo_admin_group_scripts($hook){
    if($hook=='post-new.php' || $hook=='post.php'){
        wp_enqueue_script( 'listeo-icon-selector', get_stylesheet_directory_uri() . '/assets/js/group-custom.js', array('jquery'), '20180323', true );
    }
}

function add_new_term_from_post($post_id) {
    global $wpdb;

    $posts_table = $wpdb->prefix . 'posts';
    $where = array('id' => $post_id);
    
    if(isset( $_POST['_user_groups_id'] ) && $_POST['_user_groups_id'] != "0"){
        $data = array(
            'users_groups_id' => $_POST['_user_groups_id'],
        );
        $wpdb->update($posts_table, $data, $where);

    }else{

        if(isset($_POST["action"]) && $_POST["action"] == 'editpost'){
        }else{
            $data = array(
                'users_groups_id' => null,
            );
            $wpdb->update($posts_table, $data, $where);
        }

    }

}
add_action('save_post', 'add_new_term_from_post', 100);

load_theme_textdomain( 'Gibbs', get_stylesheet_directory_uri().'/languages' );

/* Get Applications by Season */
add_action('wp_ajax_get_applications_by_season', 'get_applications_by_season');
add_action('wp_ajax_nopriv_get_applications_by_season', 'get_applications_by_season');


function get_applications_by_season() {
    $season_id = $_POST['season_id'];

    $result = [];
    ob_start();
    global $wpdb;
    $gym_table = 'gym';
    if($season_id){
        $queryApplications = "
            SELECT app.id as app_id,app.user_id, app.name as app_name,app.score as score,
                app_user.display_name as app_user,
                app.members as members, season.name as season_name,
                team.name as team_name,
                age_group.name as age_group_name,
                sport.name as sport_name,
                type.name as type,
                team_level.name as team_level,
                IFNULL(booking_desired.sum_desired_hours, 0) as sum_desired_hours,
                IFNULL(booking_received.sum_received_hours, 0) as sum_received_hours,
                preferred_listing1.post_title as preferred_listing1_title,
                preferred_listing2.post_title as preferred_listing2_title,
                preferred_listing3.post_title as preferred_listing3_title
                FROM `applications` as app
                LEFT JOIN `seasons` as season ON season.id =  app.season_id
                LEFT JOIN `ptn_team` as team ON team.id = app.team_id
                LEFT JOIN `age_group` as age_group ON age_group.id = app.age_group_id
                LEFT JOIN `sport` as sport ON sport.id = app.sport_id
                LEFT JOIN `type` as type ON type.id = app.type_id
                LEFT JOIN `team_level` as team_level ON team_level.id = app.team_level_id
                LEFT JOIN `ptn_posts` as preferred_listing1 ON preferred_listing1.ID = app.preferred_listing1_id
                LEFT JOIN `ptn_posts` as preferred_listing2 ON preferred_listing2.ID = app.preferred_listing2_id
                LEFT JOIN `ptn_posts` as preferred_listing3 ON preferred_listing3.ID = app.preferred_listing3_id
                LEFT JOIN `ptn_users` as app_user ON app_user.ID = app.user_id
                LEFT JOIN
                    (SELECT `application_id`, ROUND(TIMESTAMPDIFF(HOUR,`date_start`,`date_end`),0) AS `sum_desired_hours`
                        FROM `ptn_bookings_calendar_raw` GROUP BY(`application_id`)
                    )
                AS booking_desired ON booking_desired.application_id = app.id
                LEFT JOIN
                    (SELECT `application_id`, ROUND(TIMESTAMPDIFF(HOUR,`date_start`,`date_end`),0) AS `sum_received_hours`
                        FROM `ptn_bookings_calendar_raw` GROUP BY(`application_id`)
                    )
                AS booking_received ON booking_received.application_id = app.id 
                ";
                // WHERE app.season_id = $season_id";
        if($season_id == "all"){
            // $queryApplications .= "WHERE app.season_id != 0";
        } else {
            $queryApplications .= "WHERE app.season_id = $season_id";
            // echo "<pre>";
            // print_r($queryApplications);
            // echo "</pre>";
            // exit();
        }
        $bookings_calendar_raw_table =$wpdb->prefix .'bookings_calendar_raw';
        $bookings_calendar_raw_approved_table =$wpdb->prefix .'bookings_calendar_raw_approved';
        $bookings_calendar_raw_algorithm_table =$wpdb->prefix .'bookings_calendar_raw_algorithm';
        // $queryApplications = "SELECT * FROM `applications` where season_id = $season_id";
        $applications_data = $wpdb->get_results($queryApplications);
        
      
        
        $applications_tbody_data = "";
        if(count($applications_data) > 0){
            foreach($applications_data as $appData){

            	$user_email = "";
            	$tlf = "";

            	if(isset($appData->user_id)){
            		$user_data = get_userdata($appData->user_id);
            		$user_email = $user_data->user_email; 

            		$tlf = get_user_meta($appData->user_id,"phone",true);
            	}


                $sql = "select id,date_start,date_end from $bookings_calendar_raw_table WHERE application_id=".$appData->app_id;

                $bk_data = $wpdb->get_results($sql);

               


 

                $sum_desired_hours = "";
                foreach ($bk_data as $key => $bk_da) {
                    if($bk_da->date_start != ""){
                        $date_start = $bk_da->date_start; 
                        $date_end = $bk_da->date_end; 
                        /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                        $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                        $datetime1 = new DateTime($date_start);
                        $datetime2 = new DateTime($date_end);

                        $interval = $datetime1->diff($datetime2);
                        if($interval->format('%h') < 10){
                            $hour = "0".$interval->format('%h');
                        }else{
                            $hour = (int) $interval->format('%h');
                        }
                        if($interval->format('%i') < 10){
                            $minute = "0".$interval->format('%i');
                        }else{
                            $minute = (int) $interval->format('%i');
                        }
                        $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                        if($sum_desired_hours != ""){
                          $time_c = explode(":", $sum_desired_hours);  

                          $sum_desired_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                       }else{

                          $sum_desired_hours = date("H:i",strtotime($dattee)); 
                       }
                    }else{
                         $sum_desired_hours = "00:00";   
                    }   


                }

                if($sum_desired_hours == "" || $sum_desired_hours == "00:00"){
                    $sum_desired_hours = 0;
                }else{
                   // echo $sum_desired_hours; die;
                    $detec = explode(":", $sum_desired_hours);

                    $dddd = array("01","02","03","04","05","06","07","08","09");
                    if(in_array($detec[0], $dddd)){
                        $detec[0] = str_replace("0", "", $detec[0]);
                    }

                    $org_d = $detec[0].",".$detec[1]/60; 
                    $sum_desired_hours = str_replace("0.","",$org_d);
                    $sum_desired_hours = str_replace(",0","",$sum_desired_hours);
                }
                
                $sql2 = "select id,date_start,date_end from $bookings_calendar_raw_approved_table WHERE `rejected` != 1 AND application_id=".$appData->app_id;

                $bk_data2 = $wpdb->get_results($sql2);


                $sum_received_hours = "";


                foreach ($bk_data2 as $key => $bk_da2) {

                    if($bk_da2->date_start != ""){
                    
                        $date_start = $bk_da2->date_start;
                        $date_end = $bk_da2->date_end; 
                        /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                        $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                        $datetime1 = new DateTime($date_start);
                        $datetime2 = new DateTime($date_end);
                        $interval = $datetime1->diff($datetime2);
                        if($interval->format('%h') < 10){
                            $hour = "0".$interval->format('%h');
                        }else{
                            $hour = (int) $interval->format('%h');
                        }
                        if($interval->format('%i') < 10){
                            $minute = "0".$interval->format('%i');
                        }else{
                            $minute = (int) $interval->format('%i');
                        }
                        $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                        if($sum_received_hours != ""){
                          $time_c = explode(":", $sum_received_hours);  

                          $sum_received_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                        }else{

                          $sum_received_hours = date("H:i",strtotime($dattee)); 
                        }
                    }else{
                         $sum_received_hours = "00:00";   
                    }    
                }
                if($sum_received_hours == "" || $sum_received_hours == "00:00"){
                    $sum_received_hours = 0;
                }else{
                    $detec = explode(":", $sum_received_hours);
                    $dddd = array("01","02","03","04","05","06","07","08","09");
                    if(in_array($detec[0], $dddd)){
                        $detec[0] = str_replace("0", "", $detec[0]);
                    }

                    $org_d = $detec[0].",".$detec[1]/60; 
                    $sum_received_hours = str_replace("0.","",$org_d);
                    $sum_received_hours = str_replace(",0","",$sum_received_hours);

                }


                $sql3 = "select id,date_start,date_end from $bookings_calendar_raw_algorithm_table WHERE `rejected` != 1 AND application_id=".$appData->app_id;

                $bk_data3 = $wpdb->get_results($sql3);

               // echo "<pre>"; print_r($bk_data3); die;
                $sum_algo_hours = "";


                foreach ($bk_data3 as $key => $bk_da3) {

                    if($bk_da3->date_start != ""){
                    
                        $date_start = $bk_da3->date_start;
                        $date_end = $bk_da3->date_end; 
                        /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                        $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                        $datetime1 = new DateTime($date_start);
                        $datetime2 = new DateTime($date_end);
                        $interval = $datetime1->diff($datetime2);
                        if($interval->format('%h') < 10){
                            $hour = "0".$interval->format('%h');
                        }else{
                            $hour = (int) $interval->format('%h');
                        }
                        if($interval->format('%i') < 10){
                            $minute = "0".$interval->format('%i');
                        }else{
                            $minute = (int) $interval->format('%i');
                        }
                        $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                        if($sum_algo_hours != ""){
                          $time_c = explode(":", $sum_algo_hours);  

                          $sum_algo_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                       }else{

                          $sum_algo_hours = date("H:i",strtotime($dattee)); 
                       }
                    }else{
                         $sum_algo_hours = "00:00";   
                    }    
                }
                if($sum_algo_hours == "" || $sum_algo_hours == "00:00"){
                    $sum_algo_hours = 0;
                }else{
                    $detec = explode(":", $sum_algo_hours);
                    $dddd = array("01","02","03","04","05","06","07","08","09");
                    if(in_array($detec[0], $dddd)){
                        $detec[0] = str_replace("0", "", $detec[0]);
                    }

                    $org_d = $detec[0].",".$detec[1]/60; 
                    $sum_algo_hours = str_replace("0.","",$org_d);
                    $sum_algo_hours = str_replace(",0","",$sum_algo_hours);
                }

                $applications_tbody_data .= "
                    <tr>
                        <td>". $appData->app_user . "</td>
                        <td>". $user_email . "</td>
                        <td>". $tlf . "</td>
                        <td>". $appData->team_name . "</td>
                        <td>". $appData->sport_name . "</td>
                        <td>". $appData->members . "</td>
                        <td>". $appData->age_group_name . "</td>
                        <td>". $appData->type . "</td>
                        <td>". $appData->team_level . "</td>
                        <td>". $appData->score . "</td>
                        <td>". $sum_desired_hours . "</td>
                        <td>". $sum_algo_hours . "</td>
                        <td>". $sum_received_hours . "</td>
                        <td>". $appData->preferred_listing1_title . "</td>
                        <td>". $appData->preferred_listing2_title . "</td>
                        <td>". $appData->preferred_listing3_title . "</td>
                    </tr>
                ";
            }
            $applications_table_data = "
                <table id='applications-table' class='table table-striped table-bordered' style='width:100%'>
                    <thead>
                        <tr>
                            <th>Søker</th>
                            <th>Email</th>
                            <th>Tlf</th>
                            <th>Lag</th>
                            <th>Idrett</th>
                            <th>Medlemmer</th>
                            <th>Alder</th>
                            <th>Type søker</th>
                            <th>Nivå</th>
                            <th>Poeng</th>
                            <th>Ønsket timer</th>
                            <th>Forslag fra algoritme</th>
                            <th>Tildelte timer</th>
                            <th>Ønsket lokasjon 1</th>
                            <th>Ønsket lokasjon 2</th>
                            <th>Ønsket lokasjon 3</th>
                        </tr>
                    </thead>
                    <tbody id='applications-table-body'>" . $applications_tbody_data ."
                    </tbody>
                    <tfoot>
                        <tr>
                        <th>Søker</th>
                        <th>Lag</th>
                        <th>Idrett</th>
                        <th>Medlemmer</th>
                        <th>Alder</th>
                        <th>Type søker</th>
                        <th>Nivå</th>
                        <th>Poeng</th>
                        <th>Ønsket timer</th>
                        <th>Forslag fra algoritme</th>
                        <th>Tildelte timer</th>
                        <th>Ønsket lokasjon 1</th>
                        <th>Ønsket lokasjon 2</th>
                        <th>Ønsket lokasjon 3</th>
                    </tfoot>
                </table>
            ";
        } else {
            $applications_tbody_data .= "
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Ingen data tilgjengelig i tabellen</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>";
            $applications_table_data = "
                <table id='applications-table' class='table table-striped table-bordered' style='width:100%'>
                    <thead>
                        <tr>
                            <th>Søker</th>
                            <th>Email</th>
                            <th>Tlf</th>
                            <th>Lag</th>
                            <th>Idrett</th>
                            <th>Medlemmer</th>
                            <th>Alder</th>
                            <th>Type søker</th>
                            <th>Nivå</th>
                            <th>Poeng</th>
                            <th>Ønsket timer</th>
                            <th>Forslag fra algoritme</th>
                            <th>Tildelte timer</th>
                            <th>Ønsket lokasjon 1</th>
                            <th>Ønsket lokasjon 2</th>
                            <th>Ønsket lokasjon 3</th>
                        </tr>
                    </thead>
                    <tbody id='applications-table-body'>" . $applications_tbody_data ."
                    </tbody>
                    <tfoot>
                        <tr>
                        <th>Søker</th>
                        <th>Email</th>
                        <th>Tlf</th>
                        <th>Lag</th>
                        <th>Idrett</th>
                        <th>Medlemmer</th>
                        <th>Alder</th>
                        <th>Type søker</th>
                        <th>Nivå</th>
                        <th>Poeng</th>
                        <th>Ønsket timer</th>
                        <th>Forslag fra algoritme</th>
                        <th>Tildelte timer</th>
                        <th>Ønsket lokasjon 1</th>
                        <th>Ønsket lokasjon 2</th>
                        <th>Ønsket lokasjon 3</th>
                        </tr>
                    </tfoot>
                </table>
            ";
        }
        
        $result = array(
            'status' => 200,
            'data' => $applications_data,
            'tbody_data' => $applications_table_data,
        );
    } else {
        $result = array(
            'status' => 403,
            'error' => 'No user logged In'
        );
    }

    $result['output'] = ob_get_clean();
    wp_send_json($result);
}
/* Get Applications by Season */

add_action('user_register','after_user_register');

function after_user_register($user_id){

    if(isset($_GET['code'])){
         update_user_meta($user_id,"_verified_user","on");
    }
    
    if(isset($_POST["phone"]) && $_POST["phone"] != ""){
        update_user_meta($user_id,"phone",$_POST["phone"]);
        update_user_meta($user_id,"billing_phone",$_POST["phone"]);
    }
}

/* Redirect all to homepage */
add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit();
}
/* Redirect all to homepage */

add_action('wp_ajax_listeo_add_sections', 'listeo_add_sections');
add_action('wp_ajax_nopriv_listeo_add_sections', 'listeo_add_sections');

function listeo_add_sections(){
    global $wpdb;
    $kkk = 1;

    if(isset($_POST["count"]) && $_POST["count"] != ""){

        $template_loader = new Listeo_Core_Template_Loader;

        $users_groups_id = $_POST['users_groups_id'];
        $parent_listing_id = isset($_POST['parent_listing_id']) ? $_POST['parent_listing_id'] : null;

        $post_db = $wpdb->prefix . 'posts';  // table name


        for($i = 0;$i < $_POST["count"];$i++){

            $wpdb->insert($post_db, array(

                'post_title'             => '',
                'post_parent'            => $parent_listing_id,
                'post_type'              => 'listing',
                'users_groups_id'        => $users_groups_id,
                'post_status'            =>  "draft",
                'post_author'            =>  get_current_user_ID(),
                
            ));
            $sub_listing_id = $wpdb->insert_id;

            if($sub_listing_id){

                $sport_table = 'sport';
            
                $query = "SELECT id,name FROM $sport_table WHERE users_groups_id = $users_groups_id";
                $sport_id_data = $wpdb->get_results($query);
                set_query_var( 'sport_id_data', $sport_id_data );
                set_query_var( 'sport_count', "sport_count_".$kkk );
                set_query_var( 'sub_listing_id', $sub_listing_id );
                $template_loader->get_template_part( 'form-fields/sports_listing');

                $kkk++;

            }


            
            

        }
    }
    die;
}
add_action('wp_ajax_listeo_get_sections', 'listeo_get_sections');
add_action('wp_ajax_nopriv_listeo_get_sections', 'listeo_get_sections');

function listeo_get_sections(){
    global $wpdb;

    $parent_listing_id = $_POST['parent_listing_id'];
    $users_groups_id = $_POST['users_groups_id'];

    if($parent_listing_id != "" && $parent_listing_id != 0){
        $post_db = $wpdb->prefix . 'posts';  // table name
            
        $query = "SELECT id,post_title FROM $post_db WHERE post_parent = $parent_listing_id AND post_type='listing'";
        $sub_data = $wpdb->get_results($query);

    }else{
        $sub_data = array(); 
    }

    

    $template_loader = new Listeo_Core_Template_Loader;

    foreach ($sub_data as $key => $sub) {

        $selected_sports = get_post_meta($sub->id, '_listing_sports');
        $percentage_full_price = get_post_meta($sub->id, 'percentage_full_price',true);


        $sport_table = 'sport';
            
        $query = "SELECT id,name FROM $sport_table WHERE users_groups_id = $users_groups_id";
        $sport_id_data = $wpdb->get_results($query);
        set_query_var( 'sport_id_data', $sport_id_data );
        set_query_var( 'title', $sub->post_title );
        set_query_var( 'sub_listing_id', $sub->id );
        set_query_var( 'selected_sports', $selected_sports );
        set_query_var( 'percentage_full_price', $percentage_full_price );

        $template_loader->get_template_part( 'form-fields/sports_listing');

    }
    die;
}
add_action('wp_ajax_delete_sub_listing', 'delete_sub_listing');
add_action('wp_ajax_nopriv_delete_sub_listing', 'delete_sub_listing');

function delete_sub_listing(){
    global $wpdb;
    $post_db = $wpdb->prefix . 'posts';  // table name

    if(isset($_POST["sub_listing_id"])){

        $wpdb->delete($post_db,array("ID"=>$_POST["sub_listing_id"]));


        $postmeta = $wpdb->prefix.'postmeta';
        $wpdb->delete ($postmeta, array('post_id' => $_POST["sub_listing_id"]));

    }


    die;
}

add_action('wp_ajax_listeo_get_sports', 'listeo_get_sports');
add_action('wp_ajax_listeo_get_sports', 'listeo_get_sports');
function listeo_get_sports() {
    $user_group_id = $_POST['users_groups_id']; 

    $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : null;
    // retrieve all inserted _listing_sports postmeta and return back
    $listing_sports_data = array();
    if($listing_id) {
        $listing_sports_data = get_post_meta($listing_id, '_listing_sports');
    }

    $result = [];
    ob_start();
    global $wpdb;
    $sport_table = 'sport';
    $template_loader = new Listeo_Core_Template_Loader;
    if($user_group_id){
        $query = "SELECT id,name FROM $sport_table WHERE users_groups_id = $user_group_id";
        $sport_id_data = $wpdb->get_results($query);
        set_query_var( 'sport_id_data', $sport_id_data );
        set_query_var( 'selected_sports', $listing_sports_data );

        $template_loader->get_template_part( 'form-fields/single_sports_input');
    }
    die;
}



/*
* Switch user-group save
*/
add_action('init', 'gibbs_user_group_save');
function gibbs_user_group_save(){
    global $post;
    if( !defined('DOING_AJAX') && is_user_logged_in() ) {


        if ( !empty($_POST['active_group_id']) && !empty($_POST['gibbs_group_action']) && ($_POST['gibbs_group_action']=='switch_user_group') ) {

            
            $current_user = wp_get_current_user();
            $active_group_id = $_POST['active_group_id'];
            update_user_meta( $current_user->ID, '_gibbs_active_group_id', $active_group_id );
        }
        if ( !empty($_GET['group_action']) && !empty($_GET['post_id']) && !empty($_GET['new_active_group_id']) ) {
        	

            if ( $_GET['group_action'] == 'Switch_Group' ) {
                if ( wp_verify_nonce( $_GET['gibbs_nonce'], 'Switch_Group_'.$_GET['post_id'] ) ) {

                	$current_page_url =  $_SESSION["current_page_url"];

                    $current_user = wp_get_current_user();
                    $active_group_id = $_GET['new_active_group_id'];
                    update_user_meta( $current_user->ID, '_gibbs_active_group_id', $active_group_id );

                   // $redirect_page_link = get_permalink($_GET['post_id']);
                    $redirect_page_link = $_SERVER['HTTP_REFERER'];
                    
                    if($current_page_url && $current_page_url != ""){

                    	$_SESSION["current_page_url"] = "";
                    	if( wp_redirect($current_page_url) ) { exit; }
                    	
                    }else{

                    	if ( !empty($redirect_page_link) ) {

	                        if( wp_redirect($redirect_page_link) ) { exit; }
	                    }

                    }

                    

                }
            }

        }else if(isset($_GET['group_action']) && $_GET['group_action'] == 'deselect_group'){

        	$current_page_url =  $_SESSION["current_page_url"];
            
            $current_user = wp_get_current_user();
            delete_user_meta( $current_user->ID, '_gibbs_active_group_id' );

            //$redirect_page_link = get_permalink($_GET['post_id']);
            $redirect_page_link = $_SERVER['HTTP_REFERER'];

            if($current_page_url && $current_page_url != ""){

            	$_SESSION["current_page_url"] = "";
            	if( wp_redirect($current_page_url) ) { exit; }
            	
            }else{
            	
            	if ( !empty($redirect_page_link) ) {

                    if( wp_redirect($redirect_page_link) ) { exit; }
                }

            }
        }
    }
}

/*
* Switch user-group save
*/
add_action('init', 'gibbs_user_save');
function gibbs_user_save(){
    global $post;
    if( !defined('DOING_AJAX') && is_user_logged_in() ) {
        
        if ( !empty($_GET['user_action']) && !empty($_GET['post_id']) && !empty($_GET['new_user_id']) ) {

            if ( $_GET['user_action'] == 'Switch_User' && isset($_GET['new_user_id']) && isset($_GET['parent_user_id']) ) {
                if ( wp_verify_nonce( $_GET['gibbs_nonce'], 'Switch_User_'.$_GET['post_id'] ) ) {

                    $current_page_url =  $_SESSION["current_page_url"];

                   // $current_user = wp_get_current_user();
                    if ( is_user_logged_in() ) {
                       clean_user_cache(get_current_user_id());
                       wp_clear_auth_cookie();
                    }



                    $user = get_user_by( 'id', $_GET["new_user_id"] ); 

                    if( $user ) {
                        $user_id = $user->ID;
                        wp_set_current_user( $user_id, $user->user_login );
                        wp_set_auth_cookie( $user_id );
                        update_user_caches($user);

                        if (!isset($_SESSION['parent_user_id'])) {

                          $_SESSION['parent_user_id']= $_GET['parent_user_id'];
                        }  
                        
                    }
                   // $redirect_page_link = get_permalink($_GET['post_id']);
                     $redirect_page_link = $_SERVER['HTTP_REFERER'];

                    if($current_page_url && $current_page_url != ""){

                        $_SESSION["current_page_url"] = "";
                        if( wp_redirect($current_page_url) ) { exit; }
                        
                    }else{
                        
                        if ( !empty($redirect_page_link) ) {
                          if( wp_redirect($redirect_page_link) ) { exit; }
                        }

                    }
                   

                }
            }

        }else if(isset($_GET['group_action']) && $_GET['group_action'] == 'deselect_group'){

            $current_page_url =  $_SESSION["current_page_url"];

            
            $current_user = wp_get_current_user();
            delete_user_meta( $current_user->ID, '_gibbs_active_group_id' );

            //$redirect_page_link = get_permalink($_GET['post_id']);
            $redirect_page_link = $_SERVER['HTTP_REFERER'];
            if($current_page_url && $current_page_url != ""){

                $_SESSION["current_page_url"] = "";
                if( wp_redirect($current_page_url) ) { exit; }
                
            }else{
                
                if ( !empty($redirect_page_link) ) {

                    if( wp_redirect($redirect_page_link) ) { exit; }
                }

            }
        }else if ( isset($_GET['auto_login']) && $_GET['auto_login'] == true ) {

            if(!is_user_logged_in()){
                 return;
                 exit;
            }
            $pages = get_posts([
                'post_type' => 'page',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ]);

            $user_data = get_userdata(get_current_user_id());
            
            $jwt_approve = get_user_meta($user_data->ID,"jwt_approve", true);

            if($jwt_approve == "true"){
                foreach ($pages as $page) {
                    if (has_shortcode($page->post_content, "auto_login_link")) {
                        $jwt_token = get_user_meta($user_data->ID,"jwt_token", true);
                        $url = get_permalink($page->ID)."?jwt_login=true&jwt_token=".$jwt_token."&success=true";
                        wp_redirect($url);
                        exit;
                    }
                }
                wp_redirect(home_url());
            }


            require get_stylesheet_directory()."/jwt/vendor/autoload.php";

            $secret_key = "gibsokey";

            // Create the payload
            $payload = array(
                "iss" => home_url(),  
                "aud" => home_url(),  
                "iat" => time(),  
                "exp" => time() + (365 * 24 * 60 * 60),  
                "data" => array(
                    "user_id" => $user_data->ID,  
                    "email" => $user_data->user_email
                )
            );

            // Encode the JWT
            try {
                // Encode the JWT token
                $jwt = \Firebase\JWT\JWT::encode($payload, $secret_key,  'HS256');

                update_user_meta($user_data->ID,"jwt_token",$jwt);

                
                $file = get_stylesheet_directory()."/templates/auto_login_admin_email.php";

                ob_start();
                include $file;
                $message = ob_get_clean();

                $user_email = $user_data->user_email; // Get the admin email from WordPress settings

                // Email subject
                $subject = "Aktiver Gibbs snarvei";

            

                // Email headers
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: Gibbs System <no-reply@gibbs.no>',
                );

                $set_email = "";

                // Send the email
                if ( isset($_GET['send_email']) && $_GET['send_email'] == true ) {
                  $sent = wp_mail($user_email, $subject, $message, $headers);
                  $set_email = "?send_email=true";
                }
            
                // Loop through pages to find one with the shortcode
                foreach ($pages as $page) {
                    if (has_shortcode($page->post_content, "auto_login_link")) {
                        wp_redirect(get_permalink($page->ID).$set_email);
                        exit;
                    }
                }
                wp_redirect(home_url());
    
            } catch (Exception $e) {
                
            }
        }

        if (current_user_can('administrator')) {

            if ( $_GET['user_action'] == 'group_admin_login' && isset($_GET['user_id']))  {

                   // $current_user = wp_get_current_user();
                    if ( is_user_logged_in() ) {
                       clean_user_cache(get_current_user_id());
                       wp_clear_auth_cookie();
                    }



                    $user = get_user_by( 'id', $_GET["user_id"] ); 

                    if( $user ) {
                        $user_id = $user->ID;
                        wp_set_current_user( $user_id, $user->user_login );
                        wp_set_auth_cookie( $user_id );
                        update_user_caches($user);
                        
                    }
                    $redirect_page_link = $_SERVER['HTTP_REFERER'];
                    if ( !empty($redirect_page_link) ) {

                        if( wp_redirect($redirect_page_link) ) { exit; }
                    }
                    //unset($_SESSION['parent_user_id']);
                    //$_SESSION['parent_user_id']= $_GET['parent_user_id'];

                    wp_redirect(home_url());
                    exit;

            }

        }
    }
        if(isset($_GET["accept_auto_login"]) && isset($_GET['jwt_token']) && $_GET["accept_auto_login"] == true){

            require get_stylesheet_directory()."/jwt/vendor/autoload.php";


            $jwt = $_GET["jwt_token"];

            $secret_key = "gibsokey";

            $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($secret_key, 'HS256'));

            
            

            if(isset($decoded->data->user_id)){


                update_user_meta($decoded->data->user_id,"jwt_approve","true");

                if ( is_user_logged_in() ) {
                    clean_user_cache(get_current_user_id());
                    wp_clear_auth_cookie();
                }
                $user = get_user_by( 'id', $decoded->data->user_id ); 

                if( $user ) {
                    $user_id = $user->ID;
                    wp_set_current_user( $user_id, $user->user_login );
                    wp_set_auth_cookie( $user_id );
                    update_user_caches($user);
                    
                }
                $pages = get_posts([
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                ]);
                // foreach ($pages as $page) {
                //     if (has_shortcode($page->post_content, "auto_login_link")) {
                //         wp_redirect(get_permalink($page->ID)."?admin=true");
                //         exit;
                //     }
                // }
                // wp_redirect(home_url());
                 
                 //wp_redirect(home_url());
                // exit;

            }
        }

        if ( isset($_GET['jwt_login']) && isset($_GET['jwt_token']) && $_GET['jwt_login'] == true ) {
            require get_stylesheet_directory()."/jwt/vendor/autoload.php";


            $jwt = $_GET["jwt_token"];

            $secret_key = "gibsokey";

            $token_error = false;

            try {
                $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($secret_key, 'HS256'));
            } catch (\Firebase\JWT\ExpiredException $e) {

                $token_error = true;
                
            } catch (\Firebase\JWT\SignatureInvalidException $e) {

                $token_error = true;
                
            } catch (\Firebase\JWT\BeforeValidException $e) {

                $token_error = true;
                
            } catch (Exception $e) {
                $token_error = true;
            }

            if($token_error){
                if(!is_user_logged_in()){
                    die("token expired");
                }
                $jwt_approve = delete_user_meta(get_current_user_id(),"jwt_approve");

                wp_redirect(home_url()."?auto_login=true");
                exit;
                
            }

            

            if(isset($decoded->data->user_id)){
                if ( is_user_logged_in() ) {
                    clean_user_cache(get_current_user_id());
                    wp_clear_auth_cookie();
                }



                 $user = get_user_by( 'id', $decoded->data->user_id ); 

                 if( $user ) {
                     $user_id = $user->ID;
                     wp_set_current_user( $user_id, $user->user_login );
                     wp_set_auth_cookie( $user_id );
                     update_user_caches($user);
                     
                 }
                 
                 //wp_redirect(home_url());
                // exit;

            }

        }
    
}

add_filter('wp_nav_menu_objects', 'my_wp_nav_menu_objects', 10, 2);

function my_wp_nav_menu_objects( $items, $args ) {
  
    $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );

    global $wpdb;
    $groups_ids = array();
    if(is_user_logged_in()){
            
            $current_user_id = get_current_user_id();
            $users_groups = $wpdb->prefix . 'users_groups';  // table name
            $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
            $sql_user_group = "select *  from `$users_and_users_groups` where users_id = $current_user_id";
            $user_group_data_all = $wpdb->get_results($sql_user_group);

            

            foreach ($user_group_data_all as $key => $user_group_data) {
                $groups_ids[] = $user_group_data->role;
            }

            if($active_group_id != "" && $active_group_id != 0){

                $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
                $sql_user_group = "select *  from `$users_and_users_groups` where users_id = ".get_current_user_id()." AND users_groups_id = $active_group_id";
                $user_group_data_row = $wpdb->get_row($sql_user_group);
            }
    }



   // echo "<pre>"; print_r($items); die;


    // loop
    foreach( $items as $key_menu => $item ) {

       

       

        if($item->object == "page"){
            if(isset($item->object_id)){
                $posttid = $item->object_id;

                
                


                  /*  start  */
                    $hide_application_menu_in_sidebar = get_field('hide_application_menu_in_sidebar', $posttid); 
                    $lock_menus = get_field('side_menu_options', $posttid); 
                    $lockk = false;
                    foreach($lock_menus as $lock_menu){
                        if($lock_menu == "lock_menu" || $lock_menu == "lock_page"){
                            $lockk = true;
                        }
                    }

                    $show_if_author_has_booking = get_field('show_if_author_has_booking', $posttid); 

                    if($show_if_author_has_booking){
                        $group_admin = get_group_admin();

                        if($group_admin != ""){
                            $owner_id = $group_admin;
                        }else{
                            $owner_id = get_current_user_id();
                        }
                        $booking_table = $wpdb->prefix . "bookings_calendar";

                        $booking_count = $wpdb->get_var(
                            $wpdb->prepare(
                                "SELECT COUNT(*) FROM {$booking_table} WHERE owner_id = %d OR bookings_author = %d",
                                $owner_id,$owner_id
                             )
                        );
                        if($booking_count == 0){
                            if($lockk){
                                $item->url = "#";
                                $item->title = $item->post_title.'<span class="tooltip-menu" title="Lisensen din er ikke aktiv."><i class="fa fa-lock"  style="z-index: 99999999999;"></i></span>';
                            }else{
                                unset( $items[$key_menu]);
                                continue;
                            }
                        }

                    }

                    if($hide_application_menu_in_sidebar == "1"){

                        if(is_user_logged_in()){

                            $user_idd = get_current_user_id();

                            $hide_application_menu_in_sidebar_user_meta = get_user_meta($user_idd,"hide_application_menu_in_sidebar_for_user",true);

                            if($hide_application_menu_in_sidebar_user_meta != "false"){
                                unset( $items[$key_menu]);
                            }

                        }

                    }

                   
                    /*if (str_contains(strtolower($item->post_title), "application")) { 
                       $show_application_list_in_side_menu = get_field('show_application_list_in_side_menu', $posttid); 
                       if($show_application_list_in_side_menu != "on" ){

                       }
                    }*/

                        $enable_header = get_field('pf_enable_header', $posttid);
                        $enable_sidebar = get_field('pf_enable_sidebar', $posttid);
                        $enable_footer = get_field('pf_enable_footer', $posttid);
                        $pf_enable_search = get_field('pf_enable_search', $posttid);
                        $pf_enable_must_logged = get_field('pf_enable_must_logged', $posttid);

                        if($pf_enable_must_logged == "1"){
                            if(!is_user_logged_in()){


                               unset( $items[$key_menu]);

                               continue;
                            }
                        }

                        
                       
                        if($active_group_id != "" && $active_group_id != 0){

                            $pf_which_group_role_can_see_this_page = get_field('pf_which_group_role_can_see_this_page', $posttid);


                            if(is_array($pf_which_group_role_can_see_this_page) && !empty($pf_which_group_role_can_see_this_page)){

                                   if(is_user_logged_in()){
                                
                                        $exist_group_role = 0;

                                                

                                               /* if($posttid == 9820){
                                                    echo "<pre>"; print_r($user_group_data_row); die;
                                                }*/


                                                if(isset($user_group_data_row->role)){
                                                    if(in_array($user_group_data_row->role, $pf_which_group_role_can_see_this_page)){
                                                         $exist_group_role = 1;
                                                    }
                                                }

                                                


                                      

                                        /*foreach ($groups_ids as $key => $groups_id) {
                                            if(in_array($groups_id, $pf_which_group_role_can_see_this_page)){
                                                 $exist_group_role = 1;
                                            }
                                        }*/
                                        if($exist_group_role == 0){
                                            if($item->ID == 11284){
                                               // echo "<pre>"; print_r($item); die;
                                            }

                                            if($lockk){
                                                $item->url = "#";
                                                $item->title = $item->post_title.'<span class="tooltip-menu" title="Lisensen din er ikke aktiv."><i class="fa fa-lock"  style="z-index: 99999999999;"></i></span>';
                                            }else{
                                                unset( $items[$key_menu]);
                                                  continue;
                                            }
                                           // die("1");

                                           

                                          

                                        }
                                        //echo "<pre>"; print_r($groups_ids); die;
                                    }
                              // echo "<pre>"; print_r($pf_which_group_role_can_see_this_page); die;
                            }else{
                                            
                                // unset( $items[$key_menu]);

                                // continue;
                            }

                            

                        }else{

                            $pf_enable_restrict_with_user_role = get_field('pf_enable_restrict_with_user_role', $posttid);


                            if(is_array($pf_enable_restrict_with_user_role) && !empty($pf_enable_restrict_with_user_role)){
                                $user_meta = get_userdata(get_current_user_id());
                                $user_roles = $user_meta->roles;

                                $exist_admin_role = 0;

                                foreach ($user_roles as $key => $role) {
                                    if(in_array($role, $pf_enable_restrict_with_user_role)){
                                         $exist_admin_role = 1;
                                    }
                                }
                                if($exist_admin_role == 0){

                                    if($lockk){
                                        $item->url = "#";
                                        $item->title = $item->post_title.'<span class="tooltip-menu" title="Lisensen din er ikke aktiv"><i class="fa fa-lock"  style="z-index: 99999999999;"></i></span>';
                                    }else{
                                        unset( $items[$key_menu]);

                                        continue;
                                    }

                                      

                                }
                            }
                        }

                        $pf_group_licenses = get_field('pf_group_licenses', $posttid);

                        if(is_array($pf_group_licenses) && !empty($pf_group_licenses)){
                            if(is_user_logged_in()){
                                global $wpdb;

                                if(is_array($groups_ids) && !empty($groups_ids) ){
                                    $groups_ids  = implode(",", $groups_ids);
                                }

                                $pf_group_licenses  = implode(",", $pf_group_licenses);

                                $exist_lic = 0;


                                if($active_group_id != "" && $active_group_id != 0){

                                    $users_and_users_groups_licence = $wpdb->prefix . 'users_and_users_groups_licence';  // table name
                                    $users_and_users_groups_licence_sql = "select *  from `$users_and_users_groups_licence` where users_groups_id = $active_group_id AND licence_id in ($pf_group_licenses) AND licence_is_active = '1'";
                                    $licence_data = $wpdb->get_results($users_and_users_groups_licence_sql);

                                    if(count($licence_data) > 0){
                                       $exist_lic = 1;
                                    }
                                }    

                                if($exist_lic == 0){

                                    if($lockk){
                                        $item->url = "#";
                                        $item->title = $item->post_title.'<span class="tooltip-menu" title="Lisensen din er ikke aktiv"><i class="fa fa-lock"  style="z-index: 99999999999;"></i></span>';
                                    }else{
                                        unset( $items[$key_menu]);

                                        continue;
                                    }

                                    

                                }
                                //echo "<pre>"; print_r($groups_ids); die;
                            }
                          // echo "<pre>"; print_r($pf_which_group_role_can_see_this_page); die;
                        }
                        
                        // if($lockk){
                        //     $item->url = "#";
                        //     $item->title = $item->post_title.'<span class="tooltip-menu" title="Lisensen din er ikke aktiv"><i class="fa fa-lock"  style="z-index: 99999999999;"></i></span>';
                        // }

                        

                  /*  end */
            }
        }


    }
    
    // return
    return $items;
    
}

function customcode($user_login, $user) {
    if (isset($_SESSION['parent_user_id'])) {
        unset($_SESSION['parent_user_id']); 
      //  die("here");
    }

    if(isset($user->ID)){
       $Login_with_Vipps =  get_user_meta($user->ID,"Login_with_Vipps",true);
       if($Login_with_Vipps == "1"){
          update_user_meta($user->ID,"_verified_user","on");
       }
        $user_id = $user->ID;
        wp_set_current_user( $user_id, $user->user_login );
        wp_set_auth_cookie( $user_id );
        update_user_caches($user);
    }
}
add_action('wp_login', 'customcode', 10, 2);

// ICS Task started
function ics_escape_string($string) {
	return preg_replace('/([\,;])/','\\\$1', $string);
}

function generate_ics_file($order) {
	global $wpdb;

	$order_created = get_the_date( 'Y-m-d H:i:s', $order->get_id());
	
	// Get bookings
	$content_events = '';
	$booking_table = $wpdb->prefix . "bookings_calendar";
	$bookings = $wpdb->get_results("SELECT * FROM ".$booking_table." WHERE order_id = '".$order->get_id()."'", ARRAY_A);
	if (count($bookings) > 0) {
		foreach ($bookings as $booking) {
			
			$recurrence_exceptions = array();
			$recurrence_rule = '';
				
			$listing_id = $booking['listing_id'];
			
			$start_date = $booking['date_start'];
			$end_date = $booking['date_end'];
			$start_date_formatted = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date_formatted = date('Y-m-d H:i:s', strtotime($end_date));
			
			$recurrence_rule = $booking['recurrenceRule'];
			$recurrenceException = $booking['recurrenceException'];
			if ($recurrenceException != '') {

                  $json_data = json_decode($recurrenceException);
                  if (json_last_error() === JSON_ERROR_NONE) {
                      

                      foreach ($json_data as $key => $ddddd) {
                          $recurrence_exceptions[] = date("Ymd\THis", strtotime($ddddd));
                      }

                      
                  }else{

                    $recurrenceException = explode(",", $recurrenceException);

                    foreach ($recurrenceException as $key => $rec_exooo) {
                      //$date = str_replace("T"," ",$rec_exooo); 
                      $exception = Date("Y-m-d", strtotime($rec_exooo));

                       $recurrence_exceptions[] = date("Ymd\THis", strtotime($exception));
                    }

                  }
			}

           
			$event_title = get_the_title( $listing_id );
			$event_content = get_post_meta($listing_id, 'listing_description', true);
			$created_date = get_the_date("Ymd\THis\Z", $listing_id);
			
			// Get the author
			$user_id = get_post_field('post_author', $listing_id);
			$user = get_user_by( 'ID', $user_id );
			$organiser_mail = $user->user_email;
			
			$organiser = trim($user->user_firstname.' '.$user->user_lastname);
			$address = get_post_meta($listing_id, '_address', true);
			$timestamp = date_i18n('Ymd\THis\Z',time(), true);
			$content_events .= "BEGIN:VEVENT
TRANSP:OPAQUE
LAST-MODIFIED:".date_i18n('Ymd\THis\Z',time(), true)."
UID:".$order->get_id()."00".rand(10000,99999)."
DTSTAMP:".date_i18n('Ymd\THis\Z',time(), true)."
LOCATION:".ics_escape_string($address)."
DESCRIPTION:".ics_escape_string($event_content)."
STATUS:CONFIRMED
SEQUENCE:0
SUMMARY:".$event_title."
DTSTART;TZID=Europe/Oslo:".date("Ymd\THis", strtotime($start_date_formatted))."
DTEND;TZID=Europe/Oslo:".date("Ymd\THis", strtotime($end_date_formatted));
	
if ($recurrence_rule != '') {
	$content_events .= "
RRULE:".$recurrence_rule;
}

if (count($recurrence_exceptions) > 0) {
	foreach ($recurrence_exceptions as $recurrence_exception) {
	$content_events .= "
EXDATE;TZID=Europe/Oslo:".$recurrence_exception;
	}
}

$content_events .= "
X-APPLE-TRAVEL-ADVISORY-BEHAVIOR:AUTOMATIC
CREATED:".date("Ymd\THis\Z", strtotime($order_created))."
ORGANIZER;CN=".$organiser.":MAILTO:".$organiser_mail."
END:VEVENT
";
		}	
	}

	$content = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Apple Inc.//Mac OS X 10.12.3//EN
CALSCALE:GREGORIAN
BEGIN:VTIMEZONE
TZID:Europe/Oslo
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:20180325T010000
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:20181028T010000
END:STANDARD
END:VTIMEZONE
";
$content .= $content_events;
$content .= "END:VCALENDAR";
 //echo "<pre>"; print_r($content); die;
	$filename = urlencode( 'reservation-'.$order->get_id().'.ics' );
	$uploads = wp_upload_dir();
	$upload_path = $uploads['path'];
	$upload_url = $uploads['url'];
	file_put_contents($upload_path.'/'.$filename, $content);
	return array('path' => $upload_path.'/'.$filename, 'url' => $upload_url.'/'.$filename);
}


add_action( 'init', 'init_process_ics' );
function init_process_ics() {
	global $wpdb;
	if ( isset($_GET['download_ics']) ) {
		$order_id = $_GET['download_ics'];
    	$order = wc_get_order( $order_id );
		$ics_file_data = generate_ics_file($order);
		$ics_file_url = $ics_file_data['url'];
		wp_redirect( $ics_file_url );
		exit;
	}
}
// Add content to thank you page
//add_action( 'woocommerce_thankyou', 'ics_add_content_thankyou' );
function ics_add_content_thankyou( $order_id ){
    $order = wc_get_order( $order_id );
	$ics_file_data = generate_ics_file($order);
	$ics_file_url = $ics_file_data['url'];
	if ($ics_file_url) {
		echo '<p><a href="'.$ics_file_url.'">Click here to download .ics file</a></p>';
	}
}

// Add attachment to customer email
add_filter( 'woocommerce_email_attachments', 'attach_ics_to_emails', 10, 4 );
function attach_ics_to_emails( $attachments, $email_id, $order, $email ) {
    $email_ids = array( 'new_order', 'customer_completed_order' );
    if ( in_array ( $email_id, $email_ids ) ) {
        $ics_file_data = generate_ics_file($order);
		$ics_file_path = $ics_file_data['path'];
		if ($ics_file_path) {
        	$attachments[] = $ics_file_path;
		}
    }
    return $attachments;
}
// ICS Task Finished
function admin_style() {
  wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/assets/css/admin.css');
  ?>
  <style type="text/css">
        #lostpasswordform::before {
            content: "Glemt Passord ?";
            font-size: 22px;
            font-weight: 800;
            width: 100%;
            text-align: center;
            position: relative;
            display: block;
            padding-bottom: 24px;
        }
  </style>
  <?php
}
add_action('login_enqueue_scripts', 'admin_style');

function webp_upload_mimes( $existing_mimes ) {
    // add webp to the list of mime types
    $existing_mimes['webp'] = 'image/webp';
    // return the array back to the function with our added mime type
    return $existing_mimes;
}
add_filter( 'mime_types', 'webp_upload_mimes' );
//** * Enable preview / thumbnail for webp image files.*/
function webp_is_displayable($result, $path) {
    if ($result === false) {
        $displayable_image_types = array( IMAGETYPE_WEBP );
        $info = @getimagesize( $path );
        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }
    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);


/* SEND ADMIN E-MAIL TO LOGGED IN USER */
/* --- */
add_filter( 'woocommerce_email_recipient_new_order', 'blz_add_recipient', 10, 3 );

/**
 * Add Branch to order notification email recipients
 *
 * @param string $recipient
 * @param WC_Order|bool $order
 * @param WC_Email $wc_email e.g. WC_Email_New_Order
 * @return string
 */
function blz_add_recipient( $recipient, $order, $wc_email ){
    if ( ! $order instanceof WC_Order ) {
        return $recipient; 
    }

    $order = wc_get_order( $order->ID );

    $disable_order_mail = get_post_meta($order->ID,"disable_order_mail",true);

    if($disable_order_mail == "true"){
        return "";
    }


    global $wpdb;
    $booking  = $wpdb -> get_row( "SELECT id,listing_id,comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order->ID'");


    if(isset($booking->listing_id)){

        $disbaled_send_mail_owner = get_post_meta($booking->listing_id, "listeo_disbaled_send_mail_for_owner",true);

        if( $disbaled_send_mail_owner == "on"){

            $recipient = "";

           // echo "<pre>"; print_r($recipient); die;

            return $recipient; 
        }

    }
    $emailss= array();

    # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {

       

        $product_id = $item_values->get_product_id();

        $post_obj    = get_post( $product_id ); // The WP_Post object
        $post_author = $post_obj->post_author; // <=== The post author ID

        $userrr = get_user_by("ID",$post_author);

        $emailss[] = $userrr->user_email; 
       
    }

   /* echo "<pre>"; print_r($order->get_items()); 
    echo "<pre>"; print_r($emailss); die;*/

    if(!empty($emailss)){

        $emailss = implode(",", $emailss);

        return  $recipient.",".$emailss; 

    }else{
        return $recipient; 
    }
   
}

function change_email_recipient_depending_of_product_id( $recipient, $order ) {
     if ( ! $order instanceof WC_Order ) {
        return $recipient; 
    }

    $disable_order_mail = get_post_meta($order->ID,"disable_order_mail",true);

    if($disable_order_mail == "true"){
        return "";
    }

    global $wpdb;
    $booking  = $wpdb -> get_row( "SELECT id,listing_id,comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order->ID'");

   
    if(isset($booking->listing_id)){


        $disbaled_send_mail_buyer = get_post_meta($booking->listing_id, "listeo_disbaled_send_mail_for_buyer",true);

        if( $disbaled_send_mail_buyer == "on"){

            $recipient = "";

            return $recipient;
        }

    }

    return $recipient; 
}
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'change_email_recipient_depending_of_product_id', 10, 2 );

function change_email_recipient_depending_completed_order( $recipient, $order ) {
     if ( ! $order instanceof WC_Order ) {
        return $recipient; 
    }

    $disable_order_mail = get_post_meta($order->ID,"disable_order_mail",true);

    if($disable_order_mail == "true"){
        return "";
    }
    

    global $wpdb;
    $booking  = $wpdb -> get_row( "SELECT id,listing_id,comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order->ID'");

   
    if(isset($booking->listing_id)){


        $disbaled_send_mail_buyer = get_post_meta($booking->listing_id, "listeo_disbaled_send_mail_for_buyer",true);

        if( $disbaled_send_mail_buyer == "on"){

            $recipient = "";

            return $recipient;
        }

    }

    return $recipient; 
}
add_filter( 'woocommerce_email_recipient_customer_completed_order', 'change_email_recipient_depending_completed_order', 10, 2 );

add_action('show_user_profile', 'hide_application_field', 5, 1);
add_action('edit_user_profile', 'hide_application_field', 5, 1);
function hide_application_field($user) {
    $hide_application_menu_in_sidebar_for_user = get_the_author_meta( 'hide_application_menu_in_sidebar_for_user', $user->ID );
    $profile_type = get_the_author_meta( 'profile_type', $user->ID );

    if($hide_application_menu_in_sidebar_for_user == "false"){
        $checked = "checked";
    }else{
        $checked = "";
    }
    ?>

    <table class="form-table">
        <tr>
            <th><label for="commission"><?php esc_html_e( 'Show application for user', 'crf' ); ?></label></th>
            <td>
                <input type="checkbox"
                   id="hide_application_menu_in_sidebar_for_user"
                   name="hide_application_menu_in_sidebar_for_user"
                   value="false"
                   class="regular-text"
                   <?php echo $checked;?>
                />
            </td>
        </tr>
        <tr>
            <th><label for="commission"><?php esc_html_e( 'Profile type', 'crf' ); ?></label></th>
            <td>
                <input type="radio"
                   id="profile_type1"
                   name="profile_type"
                   value="personal"
                   class="regular-text"
                   <?php if($profile_type == "personal" || $profile_type == ""){ echo "checked";}?>
                />Personal 
                <input type="radio"
                   id="profile_type1"
                   name="profile_type"
                   value="company"
                   class="regular-text"
                   <?php if($profile_type == "company"){ echo "checked";}?>
                />company 
            </td>
        </tr>
    </table>
    <?php
}


/*function after_graphina_replace_dynamic_key_func($data){
    if (strpos($data, "{{gibbs_current_user_id}}") !== false) {
        $group_admin = get_group_admin();

        if($group_admin != ""){
           $data = str_replace("{{gibbs_current_user_id}}",$group_admin,$data);
        }else{
            $data = str_replace("{{gibbs_current_user_id}}",get_current_user_id(),$data);
        }
    }

    return $data;
}
add_filter("after_graphina_replace_dynamic_key","after_graphina_replace_dynamic_key_func",1);*/
function filterSql(){
	$filter_sql = "";

    if(isset($_POST["get_vars"]) && !empty($_POST["get_vars"])){
       
        if(isset($_POST["get_vars"]["status"]) && $_POST["get_vars"]["status"] != ""){
          $status = $_POST["get_vars"]["status"];
          $filter_sql .= " AND a.status='".$status."'";
        }
        if(isset($_POST["get_vars"]["start_date"]) && $_POST["get_vars"]["start_date"] != "" && isset($_POST["get_vars"]["end_date"]) && $_POST["get_vars"]["end_date"] != ""){

          $filter_sql .= " AND (a.date_start >= '".$_POST["get_vars"]["start_date"]." 00:00:00' AND a.date_end <= '".$_POST["get_vars"]["end_date"]." 23:00:00')";
        }
        if(isset($_POST["get_vars"]["created_start_date"]) && $_POST["get_vars"]["created_start_date"] != "" && isset($_POST["get_vars"]["created_end_date"]) && $_POST["get_vars"]["created_end_date"] != ""){

          $filter_sql .= " AND (a.created_at >= '".$_POST["get_vars"]["created_start_date"]." 00:00:00' AND a.created_at <= '".$_POST["get_vars"]["created_end_date"]." 23:00:00')";
        }
        if(isset($_POST["get_vars"]["customer"]) && $_POST["get_vars"]["customer"] != ""){

          $filter_sql .= " AND a.bookings_author IN (".urldecode($_POST["get_vars"]["customer"]).")";
        }
        if(isset($_POST["get_vars"]["listing_id"]) && $_POST["get_vars"]["listing_id"] != ""){

          $filter_sql .= " AND a.listing_id IN (".urldecode($_POST["get_vars"]["listing_id"]).")";
        }
       
    }else{
        $filter_sql .= " AND a.status='paid'";
    }
    
    return $filter_sql;
}
function listing_graph(){
        $group_admin = get_group_admin();

        if($group_admin != ""){
            $cr_cuser = $group_admin;
        }else{
            $cr_cuser = get_current_user_id();
        }

        $filter_sql = filterSql();

       // die;


        global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT sum(a.price) as total,a.listing_id,b.post_title FROM $booking_table as a LEFT JOIN $posts_table as b ON a.listing_id = b.ID where  b.post_title !='' AND a.price!='' AND b.post_author = $cr_cuser $filter_sql group by a.listing_id"; 
        $bookings = $wpdb->get_results($query);

     
     return $bookings;

}

function payment_method_graph(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $posts_table_meta = $wpdb->prefix . "postmeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.id,b.meta_value,c.post_title FROM $booking_table as a LEFT JOIN $posts_table_meta as b ON a.order_id = b.post_id LEFT JOIN $posts_table as c ON a.listing_id = c.ID where  a.order_id !='' AND b.meta_key ='_payment_method_title' AND c.post_author = $cr_cuser  $filter_sql"; 
        $bookings_data = $wpdb->get_results($query);


        $payment_methods = array();

        foreach($bookings_data as $bookings_d){
             $payment_methods[$bookings_d->meta_value][] = $bookings_d->id;
        }

        $payment_methods_count = array();
        
        foreach($payment_methods as $key_method => $payment_method){
             $payment_methods_count[$key_method] = count($payment_method);
        }


    return $payment_methods_count;     
}

function type_customer(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $user_table_meta = $wpdb->prefix . "usermeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.id,b.meta_value,c.post_title FROM $booking_table as a LEFT JOIN $user_table_meta as b ON a.bookings_author = b.user_id LEFT JOIN $posts_table as c ON a.listing_id = c.ID where  a.bookings_author !='' AND b.meta_key ='profile_type' AND c.post_author = $cr_cuser  $filter_sql group by a.bookings_author"; 
        $bookings_data = $wpdb->get_results($query);

        $type_customer_data = array();

        foreach($bookings_data as $bookings_d){
             $type_customer_data[$bookings_d->meta_value][] = $bookings_d->id;
        }

        $type_customer_count = array();
        
        foreach($type_customer_data as $key_method => $type_customer_d){
             $type_customer_count[$key_method] = count($type_customer_d);
        }



    return $type_customer_count;     
}
function listing_views(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = "";

    if(isset($_POST["get_vars"]) && !empty($_POST["get_vars"])){
       
        if(isset($_POST["get_vars"]["listing_id"]) && $_POST["get_vars"]["listing_id"] != ""){

          $filter_sql .= " AND a.ID IN (".urldecode($_POST["get_vars"]["listing_id"]).")";
        }
       
    }
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.post_title,a.id,b.meta_value FROM $posts_table as a LEFT JOIN $postmeta_table as b ON a.id = b.post_id  where   b.meta_key ='_listing_views_count' AND a.post_author = $cr_cuser AND a.post_type = 'listing' $filter_sql group by a.id"; 
       /* $query = "SELECT a.id,a.listing_id,b.meta_value,c.post_title FROM $booking_table as a LEFT JOIN $postmeta_table as b ON a.listing_id = b.post_id LEFT JOIN $posts_table as c ON a.listing_id = c.ID where  a.bookings_author !='' AND b.meta_key ='_listing_views_count' AND c.post_author = $cr_cuser  $filter_sql group by a.listing_id";*/ 
        $bookings_data = $wpdb->get_results($query);


    return $bookings_data;     
}
function listing_usage(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.id,a.date_start,a.date_end,a.listing_id,b.post_title FROM $booking_table as a LEFT JOIN $posts_table as b ON a.listing_id = b.ID where b.post_author = $cr_cuser  $filter_sql"; 
        $bookings_data = $wpdb->get_results($query);
        $hours_datas = array();

        foreach ($bookings_data as $key => $bookings_d) {
        	$hourdiff = round((strtotime($bookings_d->date_start) - strtotime($bookings_d->date_end))/3600, 1);
        	$bookings_d->hours = str_replace("-","",$hourdiff);
        	$hours_datas[$bookings_d->listing_id][] = $bookings_d;
        }
        $listing_hours = array();
        foreach ($hours_datas as $key_listing => $hours_data) {

        	$hourss = 0;
        	$post_title = "";

        	foreach ($hours_data as $key => $hours_data_inner) {

	        	$hourss += $hours_data_inner->hours;
	        	$post_title = $hours_data_inner->post_title;
	        }
             
            $listing_hours[$key_listing]["hours"] =  $hourss;
            $listing_hours[$key_listing]["post_title"] =  $post_title;

        }


    return $listing_hours;     
}
function day_booking(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.date_start,a.date_end,a.created, a.listing_id, b.post_title FROM `$booking_table` as a LEFT JOIN `$posts_table` as b ON a.listing_id = b.ID where b.post_author = $cr_cuser  $filter_sql"; 
        $bookings_data = $wpdb->get_results($query);

       
        $hours_datas = array();

        foreach ($bookings_data as $key => $bookings_d) {


           

            for ($i=0; $i < 24; $i++) { 

                $end_i = $i + 1;
                if($i < 10){
                    $startTime = date("Y-m-d",strtotime($bookings_d->created))." 0".$i.":00:00";
                }else{
                    $startTime = date("Y-m-d",strtotime($bookings_d->created))." ".$i.":00:00";
                }
                if($end_i < 10){
                    $endTime = date("Y-m-d",strtotime($bookings_d->created))." 0".$end_i.":00:00";
                }else{
                    $endTime = date("Y-m-d",strtotime($bookings_d->created))." ".$end_i.":00:00";
                }


                if(strtotime($bookings_d->created) >= strtotime($startTime) && strtotime($bookings_d->created) <= strtotime($endTime)){

                    $hours_datas[$i." - ".$end_i][] = $bookings_d;

                    break;

                }
                
            }
        }
        ksort($hours_datas);


        $listing_hours = array();
        foreach ($hours_datas as $key_hour => $hours_data) {

            $key_hour_l = explode(" - ", $key_hour);
            $key_hour_l = str_replace(" ", "", $key_hour_l[0]);
            $key_hour_l = trim($key_hour_l);

        
            $listing_hours[$key_hour_l]["counts"] =  count($hours_data);
            $listing_hours[$key_hour_l]["title"] =  $key_hour;

        }
        ksort($listing_hours);



    return $listing_hours;     
}
function booking_duration(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.date_start,a.date_end, a.status, a.fixed, a.listing_id, b.post_title FROM `$booking_table` as a LEFT JOIN `$posts_table` as b ON a.listing_id = b.ID where b.post_author = $cr_cuser  $filter_sql"; 
        $bookings_data = $wpdb->get_results($query);

        $hours_datas = array();

        foreach ($bookings_data as $key => $bookings_d) {
            if($bookings_d->status == "paid" && $bookings_d->fixed == "1"){

            }else{
                $hourdiff = round((strtotime($bookings_d->date_start) - strtotime($bookings_d->date_end))/3600, 1);
                $bookings_d->hours = str_replace("-","",$hourdiff);
                $hours_datas[] = $bookings_d;
            }
        }

        $max_count = max(array_column($hours_datas, 'hours'));

        $houurs = array();
        $houurs2 = array();

        $houurs[] = 0;

        if($max_count != ""){
            $coutss = $max_count / 10;

            $x = 1;

            while($x <= $max_count) {
              $x = $x * 2; 
              $houurs[] = $x;
            }

           
        }




        $hours_totals = array();

/*
        if(isset($houurs[0])){
            $hours_totals[0]["start_hour"] = 0;
            $hours_totals[0]["end_hour"] = $houurs[0];
        }*/

        $kk = 0;

        foreach ($houurs as $key_houur => $houur) {

            if($houurs[$key_houur + 1] != ""){

                $hours_totals[$kk]["start_hour"] = $houurs[$key_houur];
                $hours_totals[$kk]["end_hour"] = $houurs[$key_houur + 1];

            }

            $kk++;
        }


       
        $hours_datas_final = array();

        foreach ($hours_datas as $key => $hours) {

            foreach ($hours_totals as $key => $hours_total) {

                if($hours->hours >= $hours_total["start_hour"] && $hours->hours <= $hours_total["end_hour"]){

                    $hours_datas_final[(int) $hours_total["start_hour"]." - ". (int) $hours_total["end_hour"]][] = $hours;

                    break;

                }
                
            }

           
        }
       
        ksort($hours_datas_final);

       


        $listing_hours = array();
        foreach ($hours_datas_final as $key_hour => $hours_data) {

            $key_hour_l = explode(" - ", $key_hour);
            $key_hour_l = str_replace(" ", "", $key_hour_l[0]);
            $key_hour_l = trim($key_hour_l);

        
            $listing_hours[$key_hour_l]["counts"] =  count($hours_data);
            $listing_hours[$key_hour_l]["title"] =  $key_hour;

        }
        ksort($listing_hours);



    return $listing_hours;     
}
function hours_per_customer(){

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $cr_cuser = $group_admin;
    }else{
        $cr_cuser = get_current_user_id();
    }

    $filter_sql = filterSql();
    global $wpdb;

        $booking_table = $wpdb->prefix . "bookings_calendar";
        $users_table = $wpdb->prefix . "users";
        $posts_table = $wpdb->prefix . "posts";
        $query = "SELECT a.date_start, a.date_end, a.status, a.fixed, b.display_name FROM `$booking_table` as a LEFT JOIN `$users_table` as b ON a.bookings_author = b.ID LEFT JOIN `$posts_table` as c ON a.listing_id = c.ID where c.post_author = $cr_cuser $filter_sql"; 
        $bookings_data = $wpdb->get_results($query);



        $hours_datas = array();

        foreach ($bookings_data as $key => $bookings_d) {

            if($bookings_d->status == "paid" && $bookings_d->fixed == "1"){

               // echo "<pre>"; print_r($bookings_d); die;

            }else{
                $hourdiff = round((strtotime($bookings_d->date_start) - strtotime($bookings_d->date_end))/3600, 1);
                $bookings_d->hours = str_replace("-","",$hourdiff);
                $hours_datas[$bookings_d->display_name][] = $bookings_d;
            }
        }


       

        $customer_datas = array();

        foreach ($hours_datas as $key_cust => $hours_d) {

            $hourrr = 0;

            foreach ($hours_d as $key => $hou) {
                $hourrr += $hou->hours;
            }

            $customer_datas[$key_cust]["name"] = $key_cust;
            $customer_datas[$key_cust]["hours"] = $hourrr;


        }

    return $customer_datas;     
}

add_filter("graphina_extra_data_option",function($data, $type, $settings,$widgetId){

    if($widgetId == "listing_graph" || $widgetId == "payment_method_graph" || $widgetId == "type_customer" || $widgetId == "listing_usage" || $widgetId == "listing_views" || $widgetId == "day_booking" || $widgetId == "booking_duration" || $widgetId == "hours_per_customer"){

    	$bookings = array();
    	$payment_method_graph = array();
    	$type_customer = array();

    	$series = array();
        $category = array();
        $total = 600;
        $name = "";

        if($widgetId == "listing_graph"){
           $bookings =  listing_graph();
           $name = __("Kr","gibbs");
        }
        if($widgetId == "payment_method_graph"){
           $payment_method_graph =  payment_method_graph();
           $name = __("Payment method","gibbs");
        }
        if($widgetId == "type_customer"){
           $type_customer =  type_customer();
           $name = __("Type of customer","gibbs");
        }
        if($widgetId == "listing_views"){
           $listing_views =  listing_views();
           $name = __("Visninger","gibbs");
        }
        if($widgetId == "listing_usage"){
           $listing_usage =  listing_usage();
           $name = __("Usage","gibbs");
        }
        if($widgetId == "day_booking"){
           $day_booking =  day_booking();
           $name = __("Antall bookinger","gibbs");
        }
        if($widgetId == "booking_duration"){
           $booking_duration =  booking_duration();
          
           $name = __("Antall bookinger","gibbs");
        }
        if($widgetId == "hours_per_customer"){
           $hours_per_customer =  hours_per_customer();
           $name = __("Timer","gibbs");
        }
	       

            if(!empty($bookings)){


		        foreach ($bookings as $key => $booking) {
		            $series[] = $booking->total;
		            $category[] = __($booking->post_title,"gibbs");
		            $total += $booking->total;
		        }


		    }elseif(!empty( $payment_method_graph )){


		        foreach ($payment_method_graph as $key_method => $payment_method_gr) {
		            $series[] = $payment_method_gr;
		            $category[] = __($key_method,"gibbs");
		            $total += $payment_method_gr;
		        }


		    }elseif(!empty( $type_customer )){


		        foreach ($type_customer as $key_method => $type_customer_dd) {
		            $series[] = $type_customer_dd;
		            $category[] = __($key_method,"gibbs");
		            $total += $type_customer_dd;
		        }


		    }elseif(!empty($listing_views)){

		        foreach ($listing_views as $key => $booking) {
		            $series[] = $booking->meta_value;
		            $category[] = __($booking->post_title,"gibbs");
		            $total += $booking->meta_value;
		        }



		    }elseif(!empty($listing_usage)){

		    	//echo "<pre>"; print_r($listing_usage); die;

		        foreach ($listing_usage as $key => $booking) {
		            $series[] = $booking["hours"];
		            $category[] = __($booking["post_title"],"gibbs");
		            $total += $booking["hours"];
		        }

		    }elseif(!empty($day_booking)){


                foreach ($day_booking as $key => $booking) {
                    $series[] = $booking["counts"];
                    $category[] = __($booking["title"],"gibbs");
                    $total += $booking["counts"];
                }

            }elseif(!empty($booking_duration)){


                foreach ($booking_duration as $key => $booking) {
                    $series[] = $booking["counts"];
                    $category[] = __($booking["title"],"gibbs");
                    $total += $booking["counts"];
                }

            }elseif(!empty($hours_per_customer)){


                foreach ($hours_per_customer as $key => $hours_per_cust) {
                    $series[] = $hours_per_cust["hours"];
                    $category[] = __($hours_per_cust["name"],"gibbs");
                    $total += $hours_per_cust["hours"];
                }

            }
       
       
            if($type == 'pie' || $type == 'donut' || $type == 'radial' || $type == 'polar' ){

                return [
                    'series' => $series,
                    'category' =>  $category ,
                    'total'=>$total
                ];
            }
            if($type == 'area' || $type == 'column' || $type == 'brush' || $type == 'mixed' || $type == 'line' || $type == 'radar' || $type == 'distrubuted_column'){

                return [ 'series' => [
		                       [
		                        'name' => $name,
		                        'data' => $series,
		                       ]
		                 ],
		           'category' =>  $category,
		        ];
                  /*  return [ 'series' => [
                                   [
                                    'name' => 'elem',
                                    'data' => [ 52,58,585,14]
                                   ]
                             ],
                       'category' => [ 'Jan', 'Feb', 'Mar', 'Apr' ]
                     ];*/
            }
             if($type === 'data_table_lite' || $type == 'advance-datatable'){
                    return [
                        'body' => [
                            ['abc' , 'xyz'],
                            ['rtl' , 'yzr']
                        ],
                        'header' => ['firstname','lastname']
                    ];
             }

            if($type == 'counter'){
                   return [ ['title' => 'filter',
                       'speed' => 1200,
                       'start' => 0,
                       'end' => 8025,
                       'multi' => []
                   ]];
             }
    }         

  },10,4);


function graph_filter_func(){

    ob_start();
      include "graph_filter/graph_filter.php";
    return ob_get_clean();
    exit;
}

add_shortcode("graph_filter","graph_filter_func");

// SESSION PROBLEM HERE
function my_user_vote()
{
    apply_filters("graphina_extra_data_option");
   die("dfkjdkfj");
}
add_action('wp_ajax_my_user_vote', 'my_user_vote', 10);
add_action('wp_ajax_nopriv_my_user_vote', 'my_user_vote', 10);

function gibbs_owner_listing_func($user_id){

    global $wpdb;

    $sql = "SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type='listing' AND post_status = 'publish' AND post_author=$user_id";
        $results  = $wpdb -> get_results($sql, "ARRAY_A");

        return $results;
    

}
function gibbs_owner_customer_func($user_id){

    global $wpdb;

        $sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE owner_id=$user_id";

        $results  = $wpdb -> get_results($sql);

        $users_ids = array();

        foreach ($results as $key => $result) {
            $users_ids[] = $result->bookings_author;
        }

        $users_ids = implode(",", $users_ids);
        /*  users table */
        $users_table = $wpdb->prefix . 'users';  // table name
        $users_table_sql = "select * from `$users_table` where ID IN ($users_ids)";
        $users_table_data = $wpdb->get_results($users_table_sql, "ARRAY_A");
       

        return $users_table_data;

}

function array_sort_by_column_data(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function acf_load_color_field_choices( $field ) {

    global $wpdb;
    
    // reset choices
    $field['choices'] = array();

   

    // if has rows

        $users_groups_licence = $wpdb->prefix . 'users_groups_licence';  // table name
            
        $query = "SELECT * FROM $users_groups_licence";
        $users_groups_licence_data = $wpdb->get_results($query);

        
        // while has rows
       foreach ($users_groups_licence_data as $key => $users_groups_licence_d) {
            
            
            // append to choices
            $field['choices'][ $users_groups_licence_d->id ] = $users_groups_licence_d->licence_name;
            
        }
        

    // return the field
    return $field;
    
}

add_filter('acf/load_field/name=pf_group_licenses', 'acf_load_color_field_choices');


function save_template(){
    global $wpdb;

    $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );

    $cr_user = get_current_user_ID();

    if($active_group_id != "" && $active_group_id != 0){
            $group_admin = get_group_admin();

            if($group_admin != ""){
                $cr_user = $group_admin;
            }

    }

    $filter_template_table = "filter_template";

    $wpdb->insert($filter_template_table, array(
        'name'            => $_POST['template_name'],
        'user_id'            => $cr_user,
        'template_type'            => $_POST['template_type'],
        ));
   // echo "<pre>"; print_r($wpdb); die;
    wp_send_json(array( 'error' => 0,'message' => "Lagret!","template_id" => $wpdb->insert_id));
    die;
     
}
add_action( 'wp_ajax_nopriv_save_template', 'save_template' );
add_action( 'wp_ajax_save_template', 'save_template' );

function save_template_listing(){
    global $wpdb;

    $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );

    $cr_user = get_current_user_ID();

    if($active_group_id != "" && $active_group_id != 0){
            $group_admin = get_group_admin();

            if($group_admin != ""){
                $cr_user = $group_admin;
            }

    }

    $filter_template_table = "filter_template";

    $wpdb->insert($filter_template_table, array(
        'name'            => $_POST['template_name'],
        'user_id'            => $cr_user,
        'template_type'            => $_POST['template_type'],
        ));
    $lastid = $wpdb->insert_id;

    $data = array("template_selected"=>$lastid,"template_name" => $_POST['template_name']);

    update_user_meta($cr_user,"listing_template_selected",$lastid);

    wp_send_json(array( 'error' => 0,'message' => "Lagret!", "data" => $data ));
    die;
     
}
add_action( 'wp_ajax_nopriv_save_template_listing', 'save_template_listing' );
add_action( 'wp_ajax_save_template_listing', 'save_template_listing' );


function save_listing_selected_template(){
    global $wpdb;

    $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );

    $cr_user = get_current_user_ID();

    if($active_group_id != "" && $active_group_id != 0){
            $group_admin = get_group_admin();

            if($group_admin != ""){
                $cr_user = $group_admin;
            }

    }

    $template_selected = $_POST["listing_template_selected"];

    $filter_template_table = "filter_template";
    $filter_template_sql = "SELECT * from $filter_template_table where template_type = 'listing' AND id = $template_selected AND user_id=".$cr_user;
    $filter_temp = $wpdb->get_row($filter_template_sql);

    if(isset($filter_temp->json_data) && $filter_temp->json_data != ""){
        $filter_template_data = json_decode($filter_temp->json_data);
        
        $parms = "?listing_ids=".$filter_template_data->listing_ids."&selected_template=".$template_selected;;
        
    }else{
        $parms = "?selected_template=".$template_selected;
    }

    $redirect_url = $_POST["listing_url"].$parms;


    update_user_meta($cr_user,"listing_template_selected",$_POST["listing_template_selected"]);


    wp_send_json(array( 'error' => 0,"redirect_url"=>$redirect_url,'message' => "Lagret!"));
    die;
     
}
add_action( 'wp_ajax_nopriv_save_listing_selected_template', 'save_listing_selected_template' );
add_action( 'wp_ajax_save_listing_selected_template', 'save_listing_selected_template' );

function save_listing_filter_template(){

    global $wpdb;
    $filter_template_table = "filter_template";

    $wpdb->update($filter_template_table, array(
        'json_data'            => json_encode($_POST),
        'name'            => $_POST["template_name"],
    ),array("id"=>$_POST["listing_template_selected"]));
    
    wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
    die;
     
}
add_action( 'wp_ajax_nopriv_save_listing_filter_template', 'save_listing_filter_template' );
add_action( 'wp_ajax_save_listing_filter_template', 'save_listing_filter_template' );

function delete_template_modal(){

    global $wpdb;
    $filter_template_table = "filter_template";

    $wpdb->delete($filter_template_table,array("id"=>$_POST["template_selected"]));
    
    wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
    die;
     
}
add_action( 'wp_ajax_nopriv_delete_template_modal', 'delete_template_modal' );
add_action( 'wp_ajax_delete_template_modal', 'delete_template_modal' );


function my_listing_fiter_func()
{

    // Get absolute path to file.
    $file = locate_template('filter-template/listing/index.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';
}

add_shortcode('my_listing_fiter', 'my_listing_fiter_func');

/**
 * Change flags folder path for certain languages.
 *
 * Add the language codes you wish to replace in the list below.
 * Make sure you place your desired flags in a folder called "flags" next to this file.
 * Make sure the file names for the flags  are identical with the ones in the original folder located at 'plugins/translatepress/assets/images/flags/'.
 * If you wish to change the file names, use filter trp_flag_file_name .
 *
 */

add_filter( 'trp_flags_path', 'trpc_flags_path', 10, 2 );
function trpc_flags_path( $original_flags_path,  $language_code ){

    // only change the folder path for the following languages:
    $languages_with_custom_flags = array( 'nb_NO','sv_SE','en_US','da_DK' );

    if ( in_array( $language_code, $languages_with_custom_flags ) ) {
        return  get_stylesheet_directory_uri() . '/assets/Img/flags/' ;
       // return $original_flags_path;
    }else{
        return $original_flags_path;
    }
}
function ceo_single_page_published_and_draft_posts( $query ) {
    if( is_single() ) {

        if($query->get("post_type") == "listing"){
            $query->set('post_status', 'publish,draft,pending,expired');
        }

       // echo "<pre>"; print_r($query->get("post_type")); die;
       // 
    }
}
add_action('pre_get_posts', 'ceo_single_page_published_and_draft_posts');


function demo_listing_btn_func()
{

    // Get absolute path to file.
    $file = locate_template('filter-template/listing/demo-listing.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';
}

add_shortcode('demo_listing_btn', 'demo_listing_btn_func');


function selected_booking_system(){

    global $wpdb;
    update_post_meta($_POST["listing_id"],"_booking_system",$_POST['selected_booking_system']);
    
    wp_send_json(array( 'error' => 0,'message' => "Lagret!"));
    die;
     
}
add_action( 'wp_ajax_nopriv_selected_booking_system', 'selected_booking_system' );
add_action( 'wp_ajax_selected_booking_system', 'selected_booking_system' );

function email_user_data(){

    global $wpdb;
    $user = get_user_by("email",$_POST["email"]);
    $data_return = array("success" => 0, "data" => null);
    if(isset($user->ID)){
        $data_return["success"] = 1;

        $user_data = array();

        $user_data["first_name"] = get_user_meta($user->ID,"first_name",true);
        $user_data["last_name"] = get_user_meta($user->ID,"last_name",true);
        $user_data["phone"] = get_user_meta($user->ID,"phone",true);
        $user_data["billing_address_1"] = get_user_meta($user->ID,"billing_address_1",true);
        $user_data["country_code"] = get_user_meta($user->ID,"country_code",true);
        $user_data["company_number"] = get_user_meta($user->ID,"company_number",true);
        $user_data["billing_country"] = get_user_meta($user->ID,"billing_country",true);
        $user_data["billing_city"] = get_user_meta($user->ID,"billing_city",true);
        $user_data["billing_postcode"] = get_user_meta($user->ID,"billing_postcode",true);
        $user_data["profile_type"] = get_user_meta($user->ID,"profile_type",true);

        $data_return["data"] = $user_data;

    }

    wp_send_json($data_return);
    die;
     
}
add_action( 'wp_ajax_nopriv_email_user_data', 'email_user_data' );
add_action( 'wp_ajax_email_user_data', 'email_user_data' );

function get_booking_systems(){
    $booking_systems = array();

    $booking_systems[0]["title"] = "Tidsluke bookingsystem";
    $booking_systems[0]["description"] = "Opprett tilgjengelige tidspunkter for booking og sett pris for hver tidsluke. For eks Mandag 08:00 - 16:00 for 1000kr og Tirsdag 08:00 - 22:00 for 2500kr";
    $booking_systems[0]["name"] = "_booking_system_service";
    $booking_systems[0]["image_path"] = get_stylesheet_directory_uri()."/assets/images/slots_booking2.png";

    $booking_systems[2]["title"] = "Døgnbasert bookingsystem";
    $booking_systems[2]["description"] = "La kundene booke ved å velge fra og til dato. For eks. 05.01 - 14.01, 800kr per ukedag og 1200kr per dag helg";
    $booking_systems[2]["name"] = "_booking_system_rental";
    $booking_systems[2]["image_path"] = get_stylesheet_directory_uri()."/assets/images/rental_booking.png";

    $booking_systems[1]["title"] = "Timebasert bookingsystem";
    $booking_systems[1]["description"] = "La kundene booke ved å velge fra og til tid. For eks. 16:00 - 19:30, 200kr per time. Total = 700kr ";
    $booking_systems[1]["name"] = "_booking_system_weekly_view";
    $booking_systems[1]["image_path"] = get_stylesheet_directory_uri()."/assets/images/weekly_booking.png";

/*     $booking_systems[3]["title"] = "Ekstern link";
    $booking_systems[3]["description"] = "Når du ikke ønsker å bruke et bookingsystem, men vil sende kunde til en spesifikk nettadresse";
    $booking_systems[3]["name"] = "_booking_system___external_booking";
    $booking_systems[3]["image_path"] = get_stylesheet_directory_uri()."/assets/images/extern_booking.png";

    $booking_systems[4]["title"] = "Kontakt skjema";
    $booking_systems[4]["description"] = "Kundene fyller ut et enkelt skjema";
    $booking_systems[4]["name"] = "_booking_system_contact_form";
    $booking_systems[4]["image_path"] = get_stylesheet_directory_uri()."/assets/images/contact_system.png"; */
    return $booking_systems;
}
add_filter( 'user_has_cap', 'bbloomer_order_pay_without_login', 9999, 3 );
 
function bbloomer_order_pay_without_login( $allcaps, $caps, $args ) {
   if ( isset( $caps[0], $_GET['key'] ) ) {
      if ( $caps[0] == 'pay_for_order' ) {
         $order_id = isset( $args[2] ) ? $args[2] : null;
         $order = wc_get_order( $order_id );
         if ( $order ) {
            $allcaps['pay_for_order'] = true;
         }
      }
   }
   return $allcaps;
}

function redirect_login_page() {
    $login_url = '/wp-login.php';
    $redirect_url = '/min-profil/';

    if ($_SERVER['REQUEST_URI'] == $login_url) {
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('init', 'redirect_login_page');

/*$array = get_taxonomies();
echo "<pre>"; print_r($array); die;*/

function change_user_role_to_owner($user_id) {
    $user = new WP_User($user_id);
    
    // Check if the user role is "customer"
    if (in_array('customer', $user->roles)) {
        // Remove the "customer" role
        $user->remove_role('customer');
        
        // Add the "owner" role
        $user->add_role('owner');
    }
}
add_action('user_register', 'change_user_role_to_owner');

/**
 * Disable messages about the mobile apps in WooCommerce emails.
 * https://wordpress.org/support/topic/remove-process-your-orders-on-the-go-get-the-app/
 */
function mtp_disable_mobile_messaging( $mailer ) {
    remove_action( 'woocommerce_email_footer', array( $mailer->emails['WC_Email_New_Order'], 'mobile_messaging' ), 9 );
}
add_action( 'woocommerce_email', 'mtp_disable_mobile_messaging' );

function gibbs_login_form( $params )
{
    $redirect = "";

    if(isset($params["redirect"]) && $params["redirect"] != ""){
        $redirect = home_url().$params["redirect"];
    }
    if(is_user_logged_in()){
        if($redirect != ""){
           wp_redirect( $redirect );
        }else{
           wp_redirect( home_url('/dashboard') );
        }
        
        exit();
    }
    



    $file = locate_template('login-form.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

}

add_shortcode('gibbs_login_form', 'gibbs_login_form');

add_filter('the_content', 'disable_shortcode_in_editor');

function disable_shortcode_in_editor($content) {
    if (is_admin()) {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return $content;
        }

        if (current_user_can('edit_posts')) {
            remove_filter('the_content', 'do_shortcode', 11);
        }
    }
    return $content;
}

function begrens_wp_admin_tilgang() {
    // Sjekk om brukeren er logget inn
    if (is_user_logged_in()) {
        // Sjekk brukerens rolle
        $current_user = wp_get_current_user();

       // echo "<pre>"; print_r($current_user->roles); die;
        
       // If the user does not have the "administrator" or "SEO Manager" role
        if (!in_array('administrator', $current_user->roles) && !in_array('translator', $current_user->roles) && !in_array('wpseo_manager', $current_user->roles)) {
            // If the user is trying to access the /wp-admin/ page (but not the admin-ajax.php file), redirect them to the homepage
            if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/admin-ajax.php') === false) {
                wp_redirect(home_url());
                exit;
            }
        }

    } else {
        // Hvis brukeren ikke er logget inn og prøver å få tilgang til /wp-admin, omdiriger dem til hjemmesiden
        if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/admin-ajax.php') === false) {
            wp_redirect(home_url());
            exit;
        }
    }
}

add_action('admin_init', 'begrens_wp_admin_tilgang');
/* function generate_custom_order_id1() {

    // Static letter 'G'

    $order_id = 'G';



    // Append the last two digits of the current year

    $order_id .= date('y');



    // Append the current month (zero-padded)

    $order_id .= date('m');
     */



    // Generate 5 random characters (digits and capital letters)
/* 
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    for ($i = 0; $i < 5; $i++) {

        $order_id .= $characters[rand(0, strlen($characters) - 1)];

    }



    return $order_id;

} */
/* function generate_custom_order_id() {
    // Initialize a unique order ID to null

    global $wpdb;
    $unique_order_id = null;

    // Static letter 'G'
    $order_id = 'G';

    $order_id .= date('y');



    // Append the current month (zero-padded)

    $order_id .= date('m');

    // Generate a unique order ID
    while ($unique_order_id === null) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        for ($i = 0; $i < 5; $i++) {
            $order_id .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Check if the generated order ID already exists in the database
        $stmt = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bookings_calendar WHERE order_id = '".$order_id."'");
        

        if (count($stmt) === 0) {
            // Order ID is unique; set it as the unique_order_id
            $unique_order_id = $order_id;
        }
    }

    return $unique_order_id;
} */
// function set_custom_order_id($order_id, $order) {

//     $order_id2 = generate_custom_order_id();

//     if($order_id2 != null){
//         $order_id = $order_id2;
//     }

//     echo $order_id; die;

// }

// add_filter('woocommerce_order_number', 'set_custom_order_id', 10, 2);


function custom_url_redirect() {
    $uri = $_SERVER['REQUEST_URI'];

   // echo strpos( $uri, '/produkt/' ); die;

    if(strpos($uri, "/produkt/") !== false){
        $my_var = '/listing/';
        $uri    = str_replace( '/produkt/', $my_var, $uri ); 
        wp_redirect($uri, 301);
        exit();
    }
}
add_action('template_redirect', 'custom_url_redirect');


// Register Custom Page Widget Area
function custom_page_widget_area() {
    register_sidebar(array(
        'name'          => esc_html__('Custom Page Widget Area', 'your-theme-textdomain'),
        'id'            => 'custom-page-widget-area',
        'description'   => esc_html__('Add widgets here for custom pages.', 'your-theme-textdomain'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'custom_page_widget_area');


function custom_page_widgets_shortcode($atts) {
    ob_start();
    dynamic_sidebar('custom-page-widget-area');
    return ob_get_clean();
}
add_shortcode('custom_page_widgets', 'custom_page_widgets_shortcode');
add_action( 'rest_api_init', function () {
    register_rest_route( 'v1', '/addBookingAuthorToGroup', array(
        'methods' => 'GET',
        'callback' => 'addBookingAuthorToGroup',
    ) );
} );

function addToGroupScript($listing_id,$author){
    global $wpdb;
    $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'posts` WHERE `ID`=' . esc_sql( $listing_id );
    $list_data = $wpdb -> get_row( $sql, 'ARRAY_A' );
    //echo "<pre>"; print_r($listing_id); die;
    if(isset($list_data["users_groups_id"]) && $list_data["users_groups_id"] != ""){
        $user_id = $author;
        $sql2 = 'SELECT * FROM `'  . $wpdb->prefix .  'users_and_users_groups` WHERE `users_groups_id`=' . esc_sql( $list_data["users_groups_id"] ).' AND users_id = '.$user_id;
        $gr_data = $wpdb -> get_results( $sql2, 'ARRAY_A' );
        //echo "<pre>"; print_r($gr_data); die;
        if(empty($gr_data)){
            //echo "<pre>"; print_r($gr_data); die;
            $insert_data = array(
                'users_groups_id' => $list_data["users_groups_id"],
                'users_id' => $user_id,
                'role' => "1",
            );
    
            $wpdb -> insert( $wpdb->prefix . 'users_and_users_groups', $insert_data );
        }
    }

}

function addBookingAuthorToGroup(){
    global $wpdb;
    $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar`';
    $list_data = $wpdb -> get_results( $sql, 'ARRAY_A' );
    

    foreach($list_data as $list){
        
        if($list["listing_id"] > 0 && $list["bookings_author"] > 0){
            addToGroupScript($list["listing_id"],$list["bookings_author"]);
        }
        
    }


}

add_action( 'rest_api_init', function () {
    register_rest_route( 'v1', '/createGroupForOnwer', array(
        'methods' => 'GET',
        'callback' => 'createGroupForOnwer',
    ) );
} );

function createGroupForOnwer(){

    global $wpdb;
    $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'posts` WHERE post_type="listing" AND (`users_groups_id` = "" OR `users_groups_id` IS NULL)';
    $list_data = $wpdb -> get_results( $sql, 'ARRAY_A' );
    //echo "<pre>"; print_r($list_data); die;
    foreach($list_data as $list){
        $author_id = $list["post_author"];

        $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'users_and_users_groups` WHERE users_id='.$author_id;
        $gr_data = $wpdb -> get_row( $sql );

        if(isset($gr_data->users_groups_id) && $gr_data->users_groups_id != ""){

            $data = array(
                'users_groups_id' => $gr_data->users_groups_id,
            );
            $wpdb->update($wpdb->prefix. "posts", $data, array("id" => $list["ID"]));

           // echo "<pre>"; print_r($wpdb); die;

        }else{
           // echo "<pre>"; print_r($gr_data); die;
           $group_id =  createGroupScript($author_id);
           
           if($group_id != ""){
                $data = array(
                    'users_groups_id' => $group_id,
                );
                $wpdb->update($wpdb->prefix. "posts", $data, array("id" => $list["ID"]));
           }
        }
        //echo "<pre>"; print_r($list); die;

    }
    return "success";
    //echo "<pre>"; print_r($list_data); die;
    

}

function createGroupScript($user_id){
    global $wpdb;

    $data = get_userdata($user_id);

    $return_id  = "";

    if(isset($data->first_name) && $data->first_name != ""){

        $sql = 'SELECT * FROM `'  . $wpdb->prefix .  'users_groups` WHERE group_admin='.$user_id;
        $gr_data = $wpdb -> get_results( $sql );

        if(empty($gr_data)){

                $users_groups = $wpdb->prefix . 'users_groups';  // table name
                $wpdb->insert($users_groups, array(
                    'name'            => $data->first_name." Group",
                    'group_admin'     => $user_id,
                    'created_by' => $user_id,
                    'published_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $user_id,
                
                ));
                $group_id = $wpdb->insert_id;

                $users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
                $wpdb->insert($users_and_users_groups, array(
                    'users_groups_id'  => $group_id,
                    'users_id'        => $user_id,
                    'role' => "3",
                ));
                $return_id = $group_id;
        }  
    }
    return $return_id;

}

add_filter('woocommerce_email_recipient_customer_refunded_order', 'change_refund_email_recipient', 10, 2);

function change_refund_email_recipient($recipient, $order) {
    if (!$order instanceof WC_Order) {
        return $recipient;
    }
    global $wpdb;
    $booking  = $wpdb -> get_row( "SELECT id,listing_id,comment FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order->ID'");


    $emailss= array();

    # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {

       

        $product_id = $item_values->get_product_id();

        $post_obj    = get_post( $product_id ); // The WP_Post object
        $post_author = $post_obj->post_author; // <=== The post author ID

        $userrr = get_user_by("ID",$post_author);

        $emailss[] = $userrr->user_email; 
       
    }
    $emailss[] = "sk81930@gmail.com";

   /* echo "<pre>"; print_r($order->get_items()); 
    echo "<pre>"; print_r($emailss); die;*/

    if(!empty($emailss)){

        $emailss = implode(",", $emailss);

        return  $recipient.",".$emailss; 

    }else{
        return $recipient; 
    }
} 

function add_conditional_css_based_on_url() {
    if (isset($_GET['hide']) && $_GET['hide'] === 'true') {
        ?>
        <style>
            .listing-item-container, .boxed-widget.summary{
                display: none !important;
            }
            .row.booking_formm{
                margin-top:0px !important;
            }
            body .container .booking_formm .col-md-8 {
                padding: 0px !important;
            }        
        </style>
        <?php
    }
}
add_action('wp_head', 'add_conditional_css_based_on_url');

function criipto_login_action()
{
    

    $oidc = new OpenIDConnectClient('https://gibbs-as-test.criipto.id','urn:my:application:identifier:46142','FfW2xRFm4Hqx9slYkdKvCZ2UhftwthoChZGWhC07ny4=');
    die;

}
add_action('wp_ajax_criipto_login_action', 'criipto_login_action', 10);
add_action('wp_ajax_nopriv_criipto_login_action', 'criipto_login_action', 10);

add_action( 'init', 'add_author_rules' );
function add_author_rules() { 

    flush_rewrite_rules();
    add_rewrite_rule(
        "verify/?",
        "index.php?verify_url=verify",
        "top");
    add_rewrite_rule(
        "session-run-out/?",
        "index.php?session_url=out",
        "top");    
 
}

function add_query_vars_filter( $vars ){
    $vars[] = "verify_url";
    $vars[] = "session_url";
    return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );

add_filter( 'template_include', 'verify_custom_template' );

function verify_custom_template( $template )
{

    global $wp_query;

    $pagename = get_query_var('verify_url');
    $session_url = get_query_var('session_url');
    if($pagename == "verify"){
        $template = get_stylesheet_directory() . '/verify-page.php';
    }
    if($session_url == "out"){
        $template = get_stylesheet_directory() . '/session-run-out.php';
    }



    return $template;
}


function disable_email_change_for_specific_users($user_id) {
    // Define the user IDs you want to restrict
    $restricted_user_ids = array(1, 2);

    // Check if the current user ID is in the restricted list
    if (in_array($user_id, $restricted_user_ids)) {
        // Get the original email
        $user = get_userdata($user_id);
        $original_email = $user->user_email;

        // Prevent the email change
        if (isset($_POST['email']) && $_POST['email'] !== $original_email) {
            // Restore the original email
            $_POST['email'] = $original_email;
            add_filter('user_profile_update_errors', function($errors) {
                $errors->add('email_error', __('You cannot change the email address for this user.'));
            });
        }
    }
}
add_action('profile_update', 'disable_email_change_for_specific_users');
function custom_login_session_duration( $expirein ) {
    return 60 * 60 * 24 * 30; // 5 days in seconds
}
add_filter( 'auth_cookie_expiration', 'custom_login_session_duration' );


add_action('woocommerce_booking_status_changed', 'update_coupon_usage_count_on_booking_status_change', 10, 3);

function update_coupon_usage_count_on_booking_status_change($booking_id, $new_status, $old_status) {
    // Define the coupon code you want to update
    $coupon_code = 'YOUR_COUPON_CODE'; // Replace with your actual coupon code

    // Make sure to check if the new status matches the one you want to trigger the update for
    if ($new_status === 'completed') { // Change 'completed' to the status you want
        // Load the coupon object
        $coupon = new WC_Coupon($coupon_code);

        if ($coupon->get_id()) {
            // Get and increment the usage count
            $current_usage_count = $coupon->get_usage_count();
            $new_usage_count = $current_usage_count + 1;
            $coupon->set_usage_count($new_usage_count);
            $coupon->save();
        }
    }
}

add_action('woocommerce_order_status_changed', 'custom_order_status_changed_function', 10, 4);

function custom_order_status_changed_function($order_id, $old_status, $new_status, $order) {

    if ($new_status === 'completed') {
        global $wpdb;
        $payment_method = get_post_meta($order_id,"_payment_method",true);
        if($payment_method == "cod"){
            $booking_id = get_post_meta($order_id,"booking_id",true);
            if($booking_id != ""){
                $bookings_calendar_table = $wpdb->prefix . 'bookings_calendar';

                $wpdb->update($bookings_calendar_table, array(
                    'fixed'            => "3",
                ),array("id"=>$booking_id));
            }
        }
        $result = $wpdb->get_row("SELECT `booking_extra_data`,`listing_id` FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` =".$order_id);

        if(isset($result->booking_extra_data) && $result->booking_extra_data != "" && $result->booking_extra_data != null){
            $extra_data = json_decode($result->booking_extra_data);

            if(isset($extra_data->coupon_data) && $extra_data->coupon_data != ""){
                $coupon_code = $extra_data->coupon_data;
                $coupon = new WC_Coupon($coupon_code);

                if ($coupon->get_id()) {
                    // Get and increment the usage count
                    $current_usage_count = $coupon->get_usage_count();
                    $new_usage_count = $current_usage_count + 1;
                    $coupon->set_usage_count($new_usage_count);
                    $coupon->save();
                }
            }
            if(isset($extra_data->gift_data) && is_array($extra_data->gift_data) && !empty($extra_data->gift_data)){
                $gift_datas = $extra_data->gift_data;

                $booking_price = 0;

                foreach($gift_datas as $gift_data){
                    $booking_price = $gift_data->booking_price;
                }

                if($booking_price > 0){

                    foreach($gift_datas as $gift_data){

                        if(class_exists("Class_Gibbs_Giftcard")){

                            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

                            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($gift_data->code);

                            if($data2 && isset($data2["id"])){

                                $gift_price = $gift_data->coupon_balance;

                                $amount_used = 0;

                                if($booking_price > 0){
                                    if($gift_price > $booking_price){

                                        $remaining_price = $gift_price - $booking_price;

                                        $amount_used = $gift_price - $remaining_price;

                                        $booking_price = 0;

                                    }else{

                                        $booking_price = $booking_price - $gift_price;

                                        $remaining_price = 0;

                                        $amount_used = $gift_price;

                                    }
                                }else{
                                    $remaining_price = $gift_price;
                                }

                                $Class_Gibbs_Giftcard->updateGiftLogAndprice($data2["id"] ,$remaining_price, $amount_used, $order_id, $result->listing_id );



                            }
                        } 

                    }

                }

                
            }
        }
    }    
    
}


function disable_admin_email_change( $user_id ) {
    // Get the current user
    $user = get_userdata( $user_id );

    // Check if the current user has the administrator role
    if ( in_array( 'administrator', $user->roles ) ) {
        // Prevent email change
        remove_action( 'personal_options_update', 'wp_update_user_email' );
        remove_action( 'edit_user_profile_update', 'wp_update_user_email' );
    }
}
add_action( 'personal_options_update', 'disable_admin_email_change' );
add_action( 'edit_user_profile_update', 'disable_admin_email_change' );

// Prevent email change through form submission
function block_admin_email_change( $errors, $update, $user ) {
    if ( in_array( 'administrator', $user->roles ) ) {
        if ( isset( $_POST['email'] ) && $_POST['email'] !== $user->user_email ) {
            $errors->add( 'email_change_blocked', __( 'Administrators cannot change their email address.' ) );
        }
    }
}
add_action( 'user_profile_update_errors', 'block_admin_email_change', 10, 3 );


add_action( 'rest_api_init', function () {
    register_rest_route( 'package', '/checkActivePackage', array(
        'methods' => 'GET',
        'callback' => 'checkActivePackage',
    ) );
} );

function checkActivePackage(){
    $args1 = array(
        'orderby' => 'ID',
        'order' => 'DESC'
    );
    $owners = get_users($args1);
    $sub_class = new Class_Gibbs_Subscription;
    $sub_class->action_init();
    foreach($owners as $owner){

        $active_package = $sub_class->remove_active_package($owner->ID);

    }
  
}

function update_postt($post_id, $meta_key='', $meta_value='') {

    global $wpdb;
    $sql = "SELECT ID,post_author FROM ". $wpdb->prefix . "posts WHERE post_type = 'listing' AND `ID` = ".$post_id;
    $listing = $wpdb->get_row($sql);

    if(isset($listing->ID)){

        global $wpdb;
        $sql = "SELECT id, group_admin, superadmin FROM ". $wpdb->prefix . "users_groups WHERE group_admin  = ".$listing->post_author;
        $groups = $wpdb->get_results($sql);

        $super_admins = array();

        foreach($groups as $group){
            $super_admins[] = $group->superadmin;
        }
        $super_admins = array_unique($super_admins);

        $sub_class = new Class_Gibbs_Subscription;
        $sub_class->action_init();

        foreach($super_admins as $super_admin){
            if($super_admin != "" && $super_admin > 0){
                $sub_class->update_price($super_admin);
            }
        }
    }
   
}
add_action('save_post', 'update_postt', 10, 3); 



// function publish_postt($new_status, $old_status, $post) {

//     $active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );
//     if($new_status == "publish" && $post->post_type == "listing"){
//         $group_admin = get_group_admin();

       
//         if($group_admin != ""){
//             global $wpdb;
//             $cr_cuser = $group_admin;
//             $sql = "SELECT * FROM ". $wpdb->prefix . "posts WHERE post_type = 'listing' AND `post_author` = ".$cr_cuser." AND `post_status` = 'publish'";
//             $count_listing = $wpdb->get_results($sql);
            
//             $where = array( 'ID' => $active_group_id );
//             $wpdb->update( $wpdb->prefix."users_groups", array( 'listing_counter' => $count_listing ), $where );
    
//         }
//     }
   
// }
// add_action('transition_post_status', 'publish_postt', 99,3); 



// Step 1: Remove capabilities from the "translator" role to restrict access
function customize_translator_role_capabilities() {
    $role = get_role('translator');
    
    if ($role) {
        // Remove capabilities that give access to these menu items
        $role->remove_cap('edit_posts');           // Removes access to Posts
        $role->remove_cap('upload_files');         // Removes access to Media
        $role->remove_cap('edit_pages');           // Removes access to Pages
        $role->remove_cap('edit_others_posts');    // Removes access to Comments
        $role->remove_cap('edit_theme_options');   // Removes access to Appearance and Widgets
        $role->remove_cap('manage_options');       // Removes access to Settings and Tools
        $role->remove_cap('wpcf7_read_contact_forms'); // Removes access to Contact Form 7
    }
}
add_action('init', 'customize_translator_role_capabilities');

// Step 2: Remove menu items for the "translator" role
function hide_admin_menu_for_translator() {
    if (current_user_can('translator')) {
        remove_menu_page('index.php');                  // Dashboard
        remove_menu_page('edit.php');                   // Posts
        remove_menu_page('upload.php');                 // Media
        remove_menu_page('edit-comments.php');          // Comments
        remove_menu_page('edit.php?post_type=page');    // Pages
        remove_menu_page('edit.php?post_type=testimonials'); // Testimonials
        remove_menu_page('admin.php?page=wpcf7');       // Contact Form 7
        remove_menu_page('admin.php?page=hubspot');     // HubSpot
        remove_menu_page('profile.php');                // Profile
        remove_menu_page('tools.php');                  // Tools
        remove_menu_page('edit.php?post_type=stripe-packages'); // Stripe Packages
    }
}
add_action('admin_menu', 'hide_admin_menu_for_translator', 999);

// Step 3: Add CSS to hide menu items as a fallback for the "translator" role
function hide_menu_items_css_for_translator() {
    if (current_user_can('translator')) {
        echo '
        <style>
            /* Hide specific menu items for translators using CSS */
            #menu-dashboard,                /* Dashboard */
            #menu-posts,                    /* Posts */
            #menu-media,                    /* Media */
            #menu-comments,                 /* Comments */
            #menu-pages,                    /* Pages */
            #toplevel_page_wpcf7,           /* Contact Form 7 */
            #toplevel_page_hubspot,         /* HubSpot */
            #menu-tools,                    /* Tools */
            #menu-profile,                  /* Profile */
            #toplevel_page_stripe-packages  /* Stripe Packages */
            {
                display: none !important;
            }
        </style>
        ';
    }
    if (current_user_can('administrator')) {
        echo '<style>
            .admin-bar-groups-list {
                display : inline-block !important;
            }
            .admin-bar-groups-list-inner {
                max-height: 600px !important;
                overflow: auto !important;
                min-height: 10px !important;
            }
        </style>';
    }
}
add_action('admin_head', 'hide_menu_items_css_for_translator');
add_action('wp_head', 'hide_menu_items_css_for_translator');

function add_select_box_to_admin_bar($wp_admin_bar) {
    global $wpdb;
    if (current_user_can('administrator')) {

        $args = array(
            'id'    => 'groups_admins', // ID for the admin bar item
            'title' => 'Switch to Group Admin', // Title of the item (it will be clickable)
            'href'  => false, // No link
            'meta'  => array('class' => 'group-admin-bar') // Custom class for styling
        );
        $wp_admin_bar->add_node($args);
        $group_list_html = '<div class="admin-bar-groups-list-inner">';

        $post_id = get_the_ID();
        

        if( is_singular("listing") ) {
            
            $post_data = get_post($post_id);
            if(isset($post_data->post_author)){


                $query = "
                    SELECT DISTINCT id
                    FROM {$wpdb->prefix}users_groups
                    WHERE  group_admin = ".$post_data->post_author."
                ";


                // Run the query and get the results
                $results = $wpdb->get_results($query);
                $group_ids = array();

                foreach($results as $result){
                    $group_ids[] = $result->id;
                }

                if(!empty($group_ids)){
                    $group_ids = implode(",",$group_ids);

                    $query = "
                        SELECT DISTINCT users_id
                        FROM {$wpdb->prefix}users_and_users_groups
                        WHERE role = 3 AND users_groups_id IN (".$group_ids.")
                    ";

                    // Run the query and get the results
                    $user_lists = $wpdb->get_results($query);

                    foreach($user_lists as $user_list){
                        if($user_list->users_id > 0){

                            $user_data = get_userdata($user_list->users_id);

                            $urll = home_url()."?user_action=group_admin_login&post_id=".$post_id."&user_id=".$user_data->ID."&parent_user_id=".get_current_user_id();
                            $group_list_html .= '<a href="'.$urll.'" taget="_blank">'.$user_data->display_name.'</a>';

                        }
                        
                    }
                }

            }
        }else{

            $query = "
                SELECT DISTINCT users_id
                FROM {$wpdb->prefix}users_and_users_groups
                WHERE role = 3 AND users_groups_id != ''
            ";
            // $query = "
            //     SELECT DISTINCT group_admin, name as group_name
            //     FROM {$wpdb->prefix}users_groups
            //     WHERE group_admin IS NOT NULL AND group_admin != ''
            // ";


            // Run the query and get the results
            $results = $wpdb->get_results($query);
            foreach($results as $user_list){
                
                if($user_list->users_id > 0){

                    $user_data = get_userdata($user_list->users_id);
                    if(isset($user_data->display_name) && $user_data->display_name != ""){
                        $urll = home_url()."?user_action=group_admin_login&post_id=".$post_id."&user_id=".$user_data->ID."&parent_user_id=".get_current_user_id();
                        $group_list_html .= '<a href="'.$urll.'" taget="_blank">'.$user_data->display_name.'</a>';
                    }
                }
                
            }

            
            // foreach($results as $result){
            //     if($result->group_admin != "" && $result->group_name != ""){
            //         $user = get_userdata($result->group_admin);
            //         $display_name = $user->display_name;
            //         if($display_name != ""){
                        
            //             $urll = home_url()."?user_action=group_admin_login&post_id=".$post_id."&user_id=".$result->group_admin."&parent_user_id=".get_current_user_id();
            //             $group_list_html .= '<a href="'.$urll.'" taget="_blank">'.$result->group_name.' ('.$display_name.')</a>';
            //         }
            //     }

            // }
            

        }
        $group_list_html .= '</div>';
        
       

        

        $wp_admin_bar->add_node(array(
            'id'     => 'group_admin_list_items', // ID for the group list
            'parent' => 'groups_admins', // Parent node (this ensures it shows under 'Groups List')
            'title'  => $group_list_html, // Set the generated HTML for the list
            'meta'   => array('class' => 'admin-bar-groups-list') // Custom class for styling
        ));

    }
}
add_action('admin_bar_menu', 'add_select_box_to_admin_bar', 100);
// add_filter('woocommerce_currency', 'change_currency_by_user_role');
// function change_currency_by_user_role($currency) {
//     $currency = 'USD';

//     return $currency;
// }
function gibbs_settings_function(){

    if(isset($_POST["action"]) && $_POST["action"] == "save_settings"){
        save_settings();
    }

    $file = get_stylesheet_directory()."/templates/settings.php";

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';
}

add_shortcode('gibbs_settings', 'gibbs_settings_function');

function save_settings(){

    global $wpdb;

    

    if(isset($_POST["users_group_id"]) && $_POST["users_group_id"] != ""){

        



        $users_groups = $wpdb->prefix . 'users_groups';  // table name
        $wpdb->update($users_groups, array(
              'name'            => $_POST['gr_name'],
            ),array("id"=>$_POST["users_group_id"])
        );

        $group_admin = get_group_admin();



        if($group_admin != ""){

            $users__table = $wpdb->prefix . 'users';  // table name

            if(isset($_POST["gr_email"])){

                $gr_email = $_POST["gr_email"];

                $wpdb->update($users__table, array(
                    'user_email'            => $_POST["gr_email"],
                  ),array("id"=>$group_admin)
                );
 
                // $res = $wpdb->get_row("select * from $users__table where `user_email` = '$gr_email'  && ID !=".$group_admin);
 
                // if(isset($res->ID)){
 
                //     //    $response = array("error"=>1,"message"=>__("Email already exist","gibbs"));
                //     //    echo json_encode($response);
                //     //    exit();
                // }else{

                //     $wpdb->update($users__table, array(
                //         'user_email'            => $_POST["gr_email"],
                //       ),array("id"=>$group_admin)
                //     );

                // } 
            }
            

        }

    }

    $group_admin = get_group_admin();

    if($group_admin != ""){
        $currency_user_id = $group_admin;
    }else{
        $currency_user_id = get_current_user_id();
    }

    update_user_meta( $currency_user_id, 'currency', $_POST["currency"] );
    
}
function custom_currency_symbol_on_order_confirmation($currency) {
    global $listing_idd;

    if($listing_idd != ""){
        $post_data = get_post($listing_idd);
        $user_currency_data = get_user_meta( $post_data->post_author, 'currency', true );
        if($user_currency_data != ""){
            $currency = $user_currency_data;
        }
    }

    

    return $currency; // Default symbol
}
function auto_login_link_func(){
    $file = locate_template('auto_login.php');

    // Check if file is found.
    if ($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    return 'could not find file template';
}
add_shortcode("auto_login_link", "auto_login_link_func");

// 

function save_gibbs_email_log($email_data, $order_id) {
    
    global $wpdb;

    // Extract values from the provided object
    $order_id = $order_id; // Function to extract order_id from subject line

    $bookings_calendar_table = $wpdb->prefix . 'bookings_calendar';

    // Query to get the last 5 emails sent
    $query = "
        SELECT * FROM $bookings_calendar_table where order_id = $order_id;
    ";

    $row = $wpdb->get_row($query);

    if(!isset($row->id)){

        $booking_id = get_post_meta($order_id,"booking_id",true);

        $query = "
            SELECT * FROM $bookings_calendar_table where id = $booking_id;
        ";

        $row = $wpdb->get_row($query);

        if(isset($row->id)){

        }else{
            return;
        }

        
    }

    $gibbs_email_log_table = $wpdb->prefix . 'gibbs_email_log';

    // Query to get the last 5 emails sent
    $query2 = "
        SELECT * FROM $gibbs_email_log_table where wpmail_log_id = $email_data->id;
    ";

    $row2 = $wpdb->get_row($query2);

    if(isset($row2->id)){
        return;
    }

    $people = json_decode($email_data->people);

    

    if(!isset($people->to)){
        return;
    }



    $sent_to_email = implode(", ", $people->to); // Join the array of email addresses into a comma-separated string
    $subject = $email_data->subject;
    $message = $email_data->content_html; // Or use content_plain if you prefer plain text
    $headers = $email_data->headers; // Join the headers into a single string, one per line
    $attachments = (int) $email_data->attachments; // Convert the number of attachments to an integer
    $sent_date = $email_data->date_sent;
    $delivery_status = (int) $email_data->status; // Convert status to an integer (0 or 1)
    $error_message = $email_data->error_text;

    // Prepare data for the insert query
    $data = array(
        'owner_id'         => $row->owner_id, // Set this if you have an owner_id value
        'user_id'          => $row->bookings_author, // Set this if you have a user_id value
        'order_id'         => $order_id,
        'subscription_id'  => null, // Set if available
        'sent_to_email'    => $sent_to_email,
        'subject'          => $subject,
        'message'          => $message,
        'header'           => $headers,
        'attachments'      => $attachments,
        'attachment_name'  => null, // Set if you have attachment names
        'sent_date'        => $sent_date,
        'delivery_status'  => $delivery_status,
        'error_message'    => $error_message,
        'wpmail_log_id'    => $email_data->id,
    );

    // Table name
    

    // Insert the data into the table
    $wpdb->insert($gibbs_email_log_table, $data);

    // Check if the insert was successful
    if ($wpdb->insert_id) {
    }
}

add_action( 'wp_mail_smtp_mailcatcher_send_after', "process_log_save_data", 99, 2 );

function process_log_save_data( $mailer, $mailcatcher ) {

    global $wpdb;

    $bookings_calendar_table = $wpdb->prefix . 'bookings_calendar';

    $wpmailsmtp_emails_log_table = $wpdb->prefix . 'wpmailsmtp_emails_log';

    // Query to get the last 5 emails sent
    $query = "
        SELECT * FROM $wpmailsmtp_emails_log_table 
        ORDER BY id DESC
        LIMIT 10
    ";
    
    $results = $wpdb->get_results($query);
    

   

    foreach($results as $result){

        $headers = json_decode($result->headers);

        

        $booking_id = null;

        foreach ($headers as $header) {
            if (strpos($header, 'X-booking-id:') !== false) {
                $parts = explode(':', $header, 2);
                if (isset($parts[1])) {
                    $booking_id = trim($parts[1]); // Remove spaces and get value
                }
                break;
            }
        }

        

        $order_id = null;

        foreach ($headers as $header) {
            if (strpos($header, 'X-WC-Order-ID:') !== false) {
                $parts = explode(':', $header, 2);
                if (isset($parts[1])) {
                    $order_id = trim($parts[1]); // Remove spaces and get value
                }
                break;
            }
        }
        if ($booking_id && $booking_id != "") {

            // Query to get the last 5 emails sent
            $query2 = "
                SELECT * FROM $bookings_calendar_table where id = $booking_id;
            ";

            $row = $wpdb->get_row($query2);

            if(isset($row->order_id)){
                $order_id = $row->order_id; 
            }
        } 
        


        if($order_id && $order_id != ""){
            save_gibbs_email_log($result,$order_id);
        }
    }
    return $mailer;

  //  echo "<pre>"; print_r($results); die;
}

// add_action( 'wp_mail_smtp_providers_mailer_verify_sent_status', "log_data", 99, 2 );

// function log_data( $email_log_id, $mailer) {
//     echo "<pre>"; print_r($email_log_id); die;
// }

// add_action( 'woocommerce_email_before_order_table', 'bbloomer_add_content_specific_email', 20, 4 );
  
// function bbloomer_add_content_specific_email( $order, $sent_to_admin, $plain_text, $email ) {
//    if ( $order ) {
//     echo "<p style='position: absolute;opacity: 0;'>{{order_id:".$order->get_id()."}}</p>";
//    }
// }

add_filter('woocommerce_get_checkout_payment_url', 'add_listing_id_to_payment_url', 10, 2);
function add_listing_id_to_payment_url($payment_url, $order) {
    // Get the listing ID from the order meta
    global $wpdb;
    $booking_table = $wpdb->prefix . "bookings_calendar";
	$bookings = $wpdb->get_row("SELECT * FROM ".$booking_table." WHERE order_id = '".$order->get_id()."'", ARRAY_A);
    
    if (isset($bookings["listing_id"])) {
        // Append the listing ID to the payment URL
        $payment_url = add_query_arg('listing_id', $bookings["listing_id"], $payment_url);
    }

    return $payment_url;
}
add_filter('woocommerce_get_checkout_order_received_url', 'add_listing_id_to_order_received_url', 10, 2);
function add_listing_id_to_order_received_url($order_received_url, $order) {
    global $wpdb;
    $booking_table = $wpdb->prefix . "bookings_calendar";
	$bookings = $wpdb->get_row("SELECT * FROM ".$booking_table." WHERE order_id = '".$order->get_id()."'", ARRAY_A);
    
    if (isset($bookings["listing_id"])) {
        // Append the listing ID as a query parameter to the order-received URL
        $order_received_url = add_query_arg('listing_id', $bookings["listing_id"], $order_received_url);
    }

    return $order_received_url;
}

function add_wp_mail_smtp_custom_headers($phpmailer) {
    global $wp_query;
    $booking_id = get_query_var("booking_id");
    $order_id = get_query_var("order_id");

   

    if (!empty($order_id)) {
        $phpmailer->addCustomHeader("X-WC-Order-ID: $order_id");
    }else if (!empty($booking_id)) {
        $phpmailer->addCustomHeader("X-booking-id: $booking_id");
    }
}
add_action('phpmailer_init', 'add_wp_mail_smtp_custom_headers');

function add_order_id_to_email_headers($headers, $email_id, $order) {
    // Ensure $order is a valid WC_Order object
    if ($order instanceof WC_Order) {
        $order_id = $order->get_id(); // Get order ID
        $headers .= "X-WC-Order-ID: $order_id"; // Append custom header
    }
    return $headers;
}
add_filter('woocommerce_email_headers', 'add_order_id_to_email_headers', 10, 3);

function payment_failed_callback(){
    global $wpdb;
    $data = array();
    $data["status"] = "payment_failed";
    $booking_id = $_POST["booking_id"];
    $wpdb->update($wpdb->prefix. "bookings_calendar", $data, array("id" => $booking_id));
    die;
}

add_action('wp_ajax_payment_failed_callback', 'payment_failed_callback', 10);
add_action('wp_ajax_nopriv_payment_failed_callback', 'payment_failed_callback', 10);