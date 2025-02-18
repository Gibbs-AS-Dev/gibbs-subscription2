<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined( 'ABSPATH' ) || exit;
?>
<style type="text/css">
    .hs-cookie-notification-position-bottom{
        display: none;
    }
    #chat-widget-push-to-talk, #welcomeMessages {
		display: none;
	}
</style>
<?php
if(isset($_GET['invoice-success']) && $_GET['invoice-success'] == 1){
    include('invoice-success.php');
}else{
    $countries = array
    (
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );
    $key_country = null;

    $totals = $order->get_order_item_totals();

    if(!is_user_logged_in() && $order->customer_id != ""){

       wp_set_current_user($order->customer_id);

    }



    $user = wp_get_current_user();

   // echo "<pre>"; print_r(WC()->payment_gateways()); die;
    $user_name = get_user_meta( intval($user), 'billing_first_name',true);

    $user_last_name = get_user_meta( intval($user), 'billing_last_name',true);
    $user_city = get_user_meta( intval($user), 'billing_city',true);
    $user_postcode = get_user_meta( intval($user), 'billing_postcode',true);
    $user_country = get_user_meta( intval($user), 'billing_country',true);
    if (in_array($user_country, $countries)){
        $set_user_country = $user_country;
        $key_country = array_search($user_country, $countries);
    }
    $user_phone = get_user_meta( intval($user), 'billing_phone',true);
    $user_email = get_user_meta( intval($user), 'billing_email',true);


    $user_company_name = get_user_meta( intval($user), 'billing_company',true);
    $user_address = get_user_meta(intval($user), 'billing_address_1',true);
    $user_personal_number = get_user_meta(intval($user),'personal_number',true);
    $user_company_number = get_user_meta(intval($user),'company_number',true);

    $order_id = $order->get_id();
    global $wpdb;
    $dbcomment  = $wpdb -> get_results( "SELECT id,listing_id,comment,status,booking_extra_data FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order_id'")[0];
    $commentJson = json_decode($dbcomment->comment);
    $totalTaxPrice = $commentJson->total_tax;
    


    $coupen_exist = false;
    if(isset($dbcomment->booking_extra_data) && $dbcomment->booking_extra_data != ""){
        $booking_extra_data = json_decode($dbcomment->booking_extra_data);
        if(isset($booking_extra_data->coupon_data)){
            $coupen_exist = true;
        }
    }
    $manual_invoice = "";

    if(isset($dbcomment->listing_id)){
       $manual_invoice =  get_post_meta($dbcomment->listing_id,"_manual_invoice_payment",true);
       $listing_linkk  = get_permalink($dbcomment->listing_id);
    }else{
        $listing_linkk  = home_url();
    }

    $bk_id = "";
    if(isset($dbcomment->id)){
       $bk_id =  $dbcomment->id;
    }

    $av_days = "";

    if(isset($commentJson->av_days)){
       $av_days = $commentJson->av_days;
    }



    //echo "<pre>"; print_r( $commentJson); die;


    ?>

    <?php
    if(isset($dbcomment->status) && $dbcomment->status == "pay_to_confirm"){ 
        $booking_id = get_post_meta($order_id,'booking_id',true);
        
        ?>
        <div class="row">

            <div class="col-md-12 listing_title" style="margin-top:15px">
                <div class="alert alert-info" role="alert">
                   <?php echo __("Denne reservasjonen er holdt av til deg. Vennligst fullfør reservasjonen innen tiden går ut","gibbs");?>: <span id="bk_timer_<?php echo $booking_id;?>"></span>
                </div>
            </div> 

        </div>
    <?php } ?>    
    <form id="order_review" class="listeo-pay-form" method="post">
        <input type="hidden" value="<?php echo $order_id;?>" name="order_id" required>

        <table class="shop_table">
            <thead>
            <tr>
                <th class="product-name" colspan="2"><?php esc_html_e( 'Produkt', 'listeo' ); ?></th>
                <!--                <th class="product-quantity">--><?php //esc_html_e( 'Qty', 'listeo' ); ?><!--</th>-->
                <th class="product-total"><?php esc_html_e( 'Total', 'listeo' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ( count( $order->get_items() ) > 0 ) : ?>
                <?php foreach ( $order->get_items() as $item_id => $item ) :

                    $services = get_post_meta($order->get_id(),'listeo_services',true);
                    ?>
                    <?php
                    if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                        continue;
                    }
                    ?>
                    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                        <td class="product-name" colspan="2">
                            <?php
                            $discountt = "";
                            echo apply_filters( 'woocommerce_order_item_name',  $item->get_name() , $item, false ); // @codingStandardsIgnoreLine

                            echo "<br>";

                            do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

                            wc_display_item_meta( $item );

                            do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );

                            ?>
                            <?php
                            $booking_id = get_post_meta($order->get_id(),'booking_id',true);
                            if($booking_id){
                                $bookings = new Listeo_Core_Bookings_Calendar;
                                $booking_data = $bookings->get_booking($booking_id);

                                $listing_id = get_post_meta($order->get_id(),'listing_id',true);
                                $listing_id = get_post_meta($order->get_id(),'listing_id',true);



                                //get post type to show proper date
                                $discountt = get_post_meta($booking_id,'discount-type', true);
                                if($discountt)

                                echo '<div class="inner-booking-list">';
                                if($listing_type == 'rental') { ?>
                                    <h5><?php esc_html_e('Dato:', 'listeo_core'); ?></h5>
                                    <?php echo date_i18n(get_option( 'date_format' ), strtotime($booking_data['date_start'])); ?> - <?php echo date_i18n(get_option( 'date_format' ), strtotime($booking_data['date_end'])); ?></li>
                                <?php } else if($listing_type == 'service') { ?>
                                    <h5><?php esc_html_e('Dato:', 'listeo_core'); ?></h5>
                                    <?php echo date_i18n(get_option( 'date_format' ), strtotime($booking_data['date_start'])); ?>
                                    <?php esc_html_e('at','listeo_core'); ?> <?php echo date_i18n(get_option( 'time_format' ), strtotime($booking_data['date_start'])); ?> <?php if($booking_data['date_start'] != $booking_data['date_end']) echo  '- '.date_i18n(get_option( 'time_format' ), strtotime($booking_data['date_end'])); ?></li>
                                <?php } else { //event?>


                                    <?php
                                        $startDate = date_i18n(get_option( 'date_format' ), strtotime($booking_data['date_start']));
                                        $startTime = date_i18n(get_option( 'time_format' ), strtotime($booking_data['date_start']));
                                        echo $startDate . " " . $startTime;

                                        if($booking_data['date_start'] != $booking_data['date_end']){
                                            $endDate = date_i18n(get_option( 'date_format' ), strtotime($booking_data['date_end']));
                                            $endTime = date_i18n(get_option( 'time_format' ), strtotime($booking_data['date_end']));
                                            echo " - ". $endDate . " " . $endTime;
                                        }
                                    ?>
                                    <?php
                                    $event_start = get_post_meta($listing_id,'_event_date', true);
                                    $event_date = explode(' ', $event_start);
                                    if( isset($event_date[1]) ) { ?>
                                        <?php esc_html_e('at','listeo_core'); ?>

                                        <?php echo date_i18n(get_option( 'time_format' ), strtotime($event_date[1]));
                                    }?>

                                <?php } ?>
                                </div>
                                <div class="inner-booking-list">
                                    <h5><?php esc_html_e('Tilleggstjenester:', 'listeo_core'); ?></h5>
                                    <?php echo listeo_get_extra_services_html($services); //echo wpautop( $details->service); ?>
                                </div>
                                <?php
                                $details = json_decode($booking_data['comment']);
                                if (
                                    (isset($details->childrens) && $details->childrens > 0)
                                    ||
                                    (isset($details->adults) && $details->adults > 0)
                                    ||
                                    (isset($details->tickets) && $details->tickets > 0)
                                ) { ?>
                                    <div class="inner-booking-list">
                                        <h5><?php esc_html_e('Detaljer:', 'listeo_core'); ?></h5>
                                        <ul class="booking-list">
                                            <li class="highlighted" id="details">
                                                <?php if( isset($details->childrens) && $details->childrens > 0) : ?>
                                                    <?php printf( _n( '%d Child', '%s Children', $details->childrens, 'listeo_core' ), $details->childrens ) ?>
                                                <?php endif; ?>
                                                <?php if( isset($details->adults)  && $details->adults > 0) : ?>
                                                    <?php printf( _n( '%d stk', '%s stk', $details->adults, 'listeo_core' ), $details->adults ) ?>
                                                <?php endif; ?>
                                                <?php if( isset($details->tickets)  && $details->tickets > 0) : ?>
                                                    <?php printf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'listeo_core' ), $details->tickets ) ?>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                <?php }
                                if($av_days != ""){
                                    ?>
                                    <div class="inner-booking-list">
                                        <h5><?php esc_html_e('Totalt antall dager:', 'listeo_core'); ?></h5>
                                        <?php echo $av_days; //echo wpautop( $details->service); ?>
                                    </div>
                                    <?php

                                }



                            }
                            if($discountt != ""){
                            ?>
                                <div class="inner-booking-list">
                                       <h5><?php esc_html_e('Målgruppe:', 'listeo_core'); ?></h5>
                                        <ul class="booking-list">
                                            <li class="highlighted" id="details"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                                                <?php echo $discountt;?>
                                            </li>
                                        </ul>  
                                </div>          

                            <?php } ?>
                        </td>
                        <!--                        <td class="product-quantity">--><?php //echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?><!--</td>--><?php //// @codingStandardsIgnoreLine ?>
                        <td class="product-subtotal">
                            <?php if($coupen_exist){ ?>
                                <?php echo wc_price($order->get_total()); ?>
                            <?php } else{ ?>
                               <?php echo wp_kses_post($order->get_formatted_line_subtotal( $item )); ?>
                            <?php } ?>   
                        </td><?php // @codingStandardsIgnoreLine ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <?php if ( $totals ) : ?>
                <?php foreach ( $totals as $key_total=> $total ) : 
                    if($coupen_exist){
                        if($key_total != "order_total"){
                          continue;
                        }
                    }

                    
                    ?>
                    <tr>
                        <th scope="row" colspan="2"><?php echo wp_kses_post($total['label']); ?></th><?php // @codingStandardsIgnoreLine ?>
                        <td class="product-total"><?php echo wp_kses_post($total['value']); ?></td><?php // @codingStandardsIgnoreLine ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tfoot>
        </table>

        <div id="payment">
            <?php if ( $order->needs_payment() ) : ?>
                <ul class="wc_payment_methods payment_methods methods">
                    <?php
                    //$manual_invoice

                    if ( ! empty( $available_gateways ) ) {
                        if($manual_invoice == "dont_show_invoice"){
                             unset($available_gateways["cod"]);
                        }elseif($manual_invoice == "only_show_invoice"){
                            foreach ( $available_gateways as $keyy => $gateway ) {
                                if($keyy != "cod"){

                                    unset($available_gateways[$keyy]);
                                }
                                
                            }

                            if($order_id != ""){
                                $booked_id = array( 'order_id' => $order_id );
                            }else{

                            	$booked_id = array( 'id' => $bk_id );

                            }
                             if($bk_id != ""){
                                 $wpdb->update( 
                                    $wpdb->prefix . "bookings_calendar", 
                                    array( 
                                        'fixed' => "3", 
                                    ), 
                                    $booked_id
                                    
                                );

                             }

                               
                             
                        }elseif($manual_invoice == "show_invoice_with_other"){
                           
                        }else{
                            unset($available_gateways["cod"]);
                        }
                        if(count($available_gateways) == 1){ ?>

                            <style type="text/css">
                                .page-container-col{
                                    display: none;
                                }
                            </style>

                        <?php
                        }
                        foreach ( $available_gateways as $gateway ) {
                            wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
//                            if(esc_attr( $gateway->id ) == 'dibs_easy' ):
//                                ?>
<!--                                <div class="dibs-easy-form">-->
<!--                                    <p>Utleier vil sende faktura til deg utenfor gibbs.</p>-->
<!--                                    <div class="row dibs-radio-buttons" style="padding: 10px 10px 0px 10px;">-->
<!--                                        <div class="col-xs-12 col-md-12">-->
<!--                                            <input style="float:left;" type="radio" id="dibs-company-radio" name="type-radio" value="dibs-company">-->
<!--                                            <label style="margin-left: 20px;" for="dibs-company-radio">Betal som organisasjon</label>-->
<!---->
<!--                                        </div>-->
<!--                                        <div class="col-xs-12 col-md-12">-->
<!--                                            <input style="float:left;" type="radio" id="dibs-person-radio" name="type-radio" value="dibs-person">-->
<!--                                            <label style="margin-left: 20px;" for="dibs-person-radio">Betal som privatperson</label>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="row user-info-form" style="">-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div style="font-weight: bold;" class="dibs-first-name" ><span class="change-text">Fornavn</span><span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;" class="dibs-first-name" type="text" value="--><?php //if(isset($user_name)){echo $user_name;}else{ echo 'Fornavn';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div style="font-weight: bold;" class="dibs-last-name"><span class="change-text">Etternavn</span><span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;" class="dibs-last-name" type="text" value="--><?php //if($user_last_name){echo $user_last_name;}else{ echo 'Etternavn';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div class="invoice-number-change" style="font-weight: bold;">E-post<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;"   class="dibs-email" type="text" value="--><?php //if($user_email){echo $user_email;}else{ echo 'E-post';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div class="invoice-number-change" style="font-weight: bold;">Telefon (add +47)<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;"   class="dibs-phone" type="text" value="--><?php //if($user_phone){echo $user_phone;}else{ echo '+47';}?><!--">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="row user-info-form" style="">-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div style="font-weight: bold;" >Addresse<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;" class="dibs-address" type="text" value="--><?php //if($user_address){echo $user_address;}else{ echo 'Addresse';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div style="font-weight: bold;">Postnummer<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;" class="dibs-postcode" type="text" value="--><?php //if($user_postcode){echo $user_postcode;}else{ echo 'Postnummer';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div class="invoice-number-change" style="font-weight: bold;">By<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <input style="line-height: inherit; height: 25px;font-size: 12px;"   class="dibs-city" type="text" value="--><?php //if($user_city){echo $user_city;}else{ echo 'By';}?><!--">-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-3 col-md-3">-->
<!--                                            <div class="dibs-country-change" style="font-weight: bold;">Land<span style="font-weight: lighter;color: red;">*</span></div>-->
<!--                                            <select style="line-height: inherit; height: 25px;font-size: 12px;padding: 0 15px;" name="country-name" id="dibs-country-change">-->
<!--                                                <option class="dibs-country" value="None">-------</option>-->
<!--                                                --><?php
//                                                foreach ($countries as $key=>$country){
//                                                    ?>
<!--                                                    <option class="dibs-country" value="--><?php //echo $key ?><!--">--><?php //echo $country ?><!--</option>-->
<!--                                                    --><?php
//                                                }
//                                                ?>
<!--                                            </select>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                            --><?php
//                            endif;
                        }
                    } else {
                        echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'listeo' ) ) . '</li>'; // @codingStandardsIgnoreLine
                    }
                    ?>
                    <li style="display: none;" class="invoice-method">
                        <input id="payment_method_invoice" type="radio" class="input-radio" name="payment_method" value="invoice" data-order_button_text="">
                        <label> Få tilsendt faktura fra utleier </label>
                    </li>
                    <div class="payment_box payment_method_invoice" style="display: block;">
                        <p>Utleier vil sende faktura til deg utenfor gibbs.</p>
                        <div class="row radio-buttons" style="padding: 10px 10px 0px 10px;">
                            <div class="col-xs-12 col-md-12">
                                <input style="float:left;" type="radio" id="company-radio" name="type-radio" value="company">
                                <label style="margin-left: 20px;" for="company-radio">Organisasjoner/Bedrifter</label>

                            </div>
                            <div class="col-xs-12 col-md-12">
                                <input style="float:left;" type="radio" id="person-radio" name="type-radio" value="person">
                                <label style="margin-left: 20px;" for="person-radio">Privatpersoner</label>
                            </div>
                        </div>
                        <div class="row user-info-form" style="visibility:none;">
                            <div class="col-xs-12 col-md-12">
                                <div style="font-weight: bold;" >Navn<span style="font-weight: lighter;color: red;">*</span></div>
                                <input style="line-height: inherit; height: 25px;font-size: 12px;" class="invoice-name" type="text" value="<?php if($user_company_name){echo $user_company_name;}else{ echo 'Bedrift';}?>">
                            </div>
                            <div class="col-xs-6 col-md-6">
                                <div style="font-weight: bold;">Adresse<span style="font-weight: lighter;color: red;">*</span></div>
                                <input style="line-height: inherit; height: 25px;font-size: 12px;" class="invoice-address" type="text" value="<?php if($user_address){echo $user_address;}else{ echo 'Address';}?>">
                            </div>
                            <div class="col-xs-6 col-md-6">
                                <div class="invoice-number-change" style="font-weight: bold;">Personnumber<span style="font-weight: lighter;color: red;">*</span></div>
                                <input style="line-height: inherit; height: 25px;font-size: 12px;"   class="invoice-number" type="text" value="<?php if($user_company_number){echo $user_company_number;}else{ echo 'Org nummer';}?>">
                            </div>
                        </div>
                    </div>
                </ul>
            <?php endif; ?>
            <div class="form-row">
                <input type="hidden" name="woocommerce_pay" value="1" />

                <?php wc_get_template( 'checkout/terms.php' ); ?>

                <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

                <?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

                <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

                <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
            </div>
        </div>
    </form>
    <button class="button alt" type="submit" style="display:none;"  id="invoice_pay_button" value="Pay using invoice" data-value="Pay using invoice">Betal med faktura</button>
<!--    <button class="button alt" type="submit" style="display:none;"  id="dibs_pay_button" value="Pay using invoice" data-value="Pay using invoice">Betal for ordren</button>-->
    <?php

    ?>

    <div class='invoice-success' style="display: none; box-shadow: grey 0px 0px 100px 5px; z-index: 1000; text-align: center; background: white; position: absolute; top: 60%; width: 100%;">
        <?php include('invoice-success.php');?>
    </div>
<?php }
add_action("wp_footer", "add_overlay");
function add_overlay(){ ?>
   <div class="overlay">
        <div class="overlay__inner">
            <div class="overlay__content"><span class="spinner"></span></div>
        </div>
    </div>
<?php }

?>

<script>
    jQuery(document).ready(function(){

        
        //if( '<?php //echo $key_country?>//' !== ''){
        //    var aaa = '<?php //echo $key_country; ?>//';
        //
        //    jQuery(`#dibs-country-change option[value=${aaa}]`).prop("selected",true);
        //}
        jQuery('#payment_method_invoice').on('click', function(){
            jQuery('.payment_method_invoice').slideDown("slow");
            jQuery('.payment_box.payment_method_paypal').hide();
            jQuery('.payment_box.payment_method_vipps').hide();
            jQuery('.payment_box.payment_method_stripe').hide();
            // jQuery('.dibs-easy-form').hide();
            jQuery('#invoice_pay_button').show();
            // jQuery('#dibs_pay_button').hide();

            jQuery('#place_order').hide();

        });

        jQuery('.payment_method_paypal').on('click', function(){
            jQuery('.payment_box.payment_method_vipps').hide();
            jQuery('.payment_box.payment_method_stripe').hide();
            // jQuery('.dibs-easy-form').hide();
            jQuery('#invoice_pay_button').hide();
            jQuery('#place_order').show();
            // jQuery('#dibs_pay_button').hide();
        });

        jQuery('.wc_payment_method').on('click', function(){
            jQuery('#payment_method_invoice').prop('checked', false);
            jQuery('.payment_method_invoice').hide();
            jQuery('#invoice_pay_button').hide();

        });

        jQuery('.radio-buttons').on('click', function(){
            var name = '<?php if($user_name){echo $user_name;}else{echo 'Navn';}?>'
            var company = '<?php if($user_company_name){echo $user_company_name;}else{echo 'Bedrift';}?>'
            var address = '<?php if($user_address){echo $user_address;}else{echo 'Adresse';}?>';
            var personalNumber = '<?php if($user_personal_number){echo $user_personal_number;}else{echo 'Personnummer';}?>';
            var companyNumber = '<?php if($user_company_number){echo $user_company_number;}else{echo 'Org nummer';}?>';
            if(jQuery('.radio-buttons input[name=type-radio]:checked').val() =='person'){
                jQuery('.invoice-number-change').text('Personnummer');
                jQuery('.user-info-form').show();
                jQuery('.invoice-name').attr('value',`${name}`);
                jQuery('.invoice-number').attr('value',`${personalNumber}`);
                jQuery('.invoice-address').attr('value',`${address}`);

            }else{
                jQuery('.invoice-number-change').text('Org nummer');

                jQuery('.user-info-form').show();
                jQuery('.invoice-name').attr('value',`${company}`);
                jQuery('.invoice-number').attr('value',`${companyNumber}`);
                jQuery('.invoice-address').attr('value',`${address}`);

            }
        });

        jQuery('#order_review').on('click', function(e){
           if(jQuery("#terms").prop("checked") == false){
           	 e.preventDefault();
           	 jQuery(".error_p").remove();
           	 jQuery(".woocommerce-terms-and-conditions-wrapper").find(".validate-required").append("<p class='error_p' style='color: red;'>Du må godkjenne for å gå videre :)</p>")
           }
        })


        jQuery('#invoice_pay_button').on('click', function(){
            // jQuery('#dibs_pay_button').hide();

            var origin = window.location.origin;
            var _name = jQuery('.invoice-name').val();
            var _number = jQuery('.invoice-number').val();
            var _address = jQuery('.invoice-address').val();
            var _type;
            var _booking_id = ' <?php
                $order_id = $order->get_id();
                global $wpdb;
                $results = $wpdb->get_results("SELECT `ID` FROM `" . $wpdb->prefix . "bookings_calendar` WHERE `order_id` = '$order_id' ");
                echo $results[0]->ID;
                ?>';
            var allow = false;
            if(jQuery('#person-radio').is(':checked')){
                _type = 'person';
                allow = true;
            }else if(jQuery('#company-radio').is(':checked')){
                _type = 'company';
                allow = true;
            }else{
                alert('Please select company or person!');
                allow = false;
            }
            if(allow){
                if(_name == 'Navn' || _number == 'Org nummer' || _name == 'Bedrift' || _number == 'Personnummer' || _address == 'Adresse'){
                    alert('Fill the empty fields!');
                    return false;
                }else{
                    var ajax_data = {
                        'action': 'invoice_radio_button',
                        'name': _name,
                        'address': _address,
                        'number': _number,
                        'type': _type,
                        'booking_id': _booking_id
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: listeo.ajaxurl,
                        data: ajax_data,
                        success: function () {
                            jQuery('.invoice-success').fadeIn(1000);
                            setTimeout(() => {
                                window.open(`${origin}/gibbstest/dashboard/my-bookings/?status=waiting`,'_self');
                            }, 3000);
                        }
                    });
                }
            }

        });


        // jQuery('#payment_method_dibs_easy').on('click', function(){
        //     // jQuery('.dibs-easy-form').slideDown('slow');
        //     jQuery('.payment_box.payment_method_paypal').hide();
        //     jQuery('.payment_box.payment_method_vipps').hide();
        //     jQuery('.payment_box.payment_method_stripe').hide();
        //     jQuery('.payment_box.payment_method_invoice').hide();
        //     // jQuery('#dibs_pay_button').show();
        //     jQuery('#place_order').hide();
        //     jQuery('.dibs-radio-buttons input#dibs-person-radio').prop("checked",true);
        // });

        //jQuery('.dibs-radio-buttons').on('click', function(){
        //
        //    let companyName = '<?php //if($user_company_name){echo $user_company_name;}else{echo 'Bedriftsnavn';}?>//';
        //    let companyNumber = '<?php //if($user_company_number){echo $user_company_number;}else{echo 'Organisasjons nummer';}?>//';
        //    let userName = '<?php //if($user_name){echo $user_name;}else{echo 'Fornavn';}?>//';
        //    let userLastname = '<?php //if($user_last_name){echo $user_last_name;}else{echo 'Etternavn';}?>//';
        //
        //    if(jQuery('.dibs-radio-buttons input[name=type-radio]:checked').val() =='dibs-company'){
        //        jQuery('div.dibs-first-name span.change-text').text("Bedriftsnavn");
        //        jQuery('div.dibs-last-name span.change-text').text('Organisasjons nummer');
        //        jQuery('input.dibs-first-name').val(companyName);
        //        jQuery('input.dibs-last-name').val(companyNumber);
        //    }else{
        //        jQuery('div.dibs-first-name span.change-text').text("Fornavn");
        //        jQuery('div.dibs-last-name span.change-text').text('Etternavn');
        //        jQuery('input.dibs-first-name').val(userName);
        //        jQuery('input.dibs-last-name').val(userLastname);
        //    }
        //});

        //jQuery('#dibs_pay_button').on('click', function(){
        //
        //    let _name = jQuery('input.dibs-first-name').val();
        //    let _last_name = jQuery('input.dibs-last-name').val();
        //    let _email = jQuery('input.dibs-email').val();
        //    let _phone = jQuery('input.dibs-phone').val();
        //    let _address = jQuery('input.dibs-address').val();
        //    let _postcode = jQuery('input.dibs-postcode').val();
        //    let _city = jQuery('input.dibs-city').val();
        //    let _country = jQuery('#dibs-country-change option:checked').val();
        //    let _personOrCompany = jQuery('.dibs-radio-buttons input[name=type-radio]:checked').val();
        //
        //    if((_name, _last_name, _email, _phone, _address, _postcode, _city) === '' || _name == 'Fornavn' || _last_name == 'Etternavn ' || _country == "None"
        //        || _email == 'E-post' || _phone == '+47' || _address == 'Addresse' || _postcode == "Postnummer" || _city == 'By'){
        //        alert('Fill the empty fields!');
        //        return false;
        //    }else{
        //        let ajax_data = {
        //            'action': 'nets_user_form',
        //            'name': _name,
        //            'last_name' : _last_name,
        //            'email': _email,
        //            'phone' : _phone,
        //            'address': _address,
        //            'postcode': _postcode,
        //            'city' : _city,
        //            'country' : _country,
        //            'personOrCompany': _personOrCompany,
        //            'order_id': <?php //echo $order->get_id()?>
        //        };
        //
        //        jQuery.ajax({
        //            type: "POST",
        //            url: listeo.ajaxurl,
        //            data: ajax_data,
        //            success: function (data) {
        //              console.log('NETS SUCCESS');
        //                setTimeout(function(){
        //                    jQuery('#place_order').click();
        //
        //                }, 4000);
        //            }
        //        });
        //    }
        //
        //});
    });
</script>
<script>
let hasPage = "form-pay";
jQuery(function($){
    var bk_id = "<?php echo $bk_id; ?>";
    var current_url = window.location.href;
    var storageKey = "booking_timer_data";
    
    
    var default_time = 60 * 15; // 60 minutes in seconds
    var current_date = new Date().getTime(); // Get current timestamp in milliseconds

    // Retrieve existing booking data from localStorage
    var booking_data = localStorage.getItem(storageKey);
    booking_data = booking_data ? JSON.parse(booking_data) : [];

    // Find existing booking entry
    var existingIndex = booking_data.findIndex(item => item.bk_id === bk_id);

    if (existingIndex !== -1) {
        // Update existing entry
        var storedTime = booking_data[existingIndex].start_time;
        var timeElapsed = Math.floor((current_date - storedTime) / 1000); // Convert ms to sec
        var remaining_time = default_time - timeElapsed;

        booking_data[existingIndex].time = remaining_time > 0 ? remaining_time : 0;
        booking_data[existingIndex].current_url = current_url;
    } else {
        // Insert new booking with fresh time
        booking_data.push({
            bk_id: bk_id,
            current_url: current_url,
            listing_linkk: "<?php echo $listing_linkk;?>",
            time: default_time,
            start_time: current_date
        });
    }

    // Save updated data to localStorage
    localStorage.setItem(storageKey, JSON.stringify(booking_data));
    jQuery(document).ready(function(){

        jQuery(".main-booking-timer").remove();
    })

});


</script>
<?php
if(count($available_gateways) == 1){ ?>

   <script>

    jQuery(document).ready(function(){
        setTimeout(function(){


            jQuery(".woocommerce-form__input-checkbox").prop("checked",true);
            jQuery("#order_review").submit();


        },1000)
    })

     
   </script>
   <style type="text/css">
        header#header-container {
            display: none;
        }
   </style>
<?php
}else{ ?>
    <script type="text/javascript">

        jQuery(document).ready(function(){
           jQuery(".overlay").hide();
        });
        
    </script>

<?php }