<?php
if(isset($data)) :


endif;
$monthNames = ["Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember"];
if($data->comment == 'owner reservations'){
    return;
}
$class = array();
$tag = array();
$show_approve = false;
$show_reject = false;
$show_cancel = false;

$payment_method = '';
if(isset($data->order_id) && !empty($data->order_id) && $data->status == 'confirmed'){
    $payment_method = get_post_meta( $data->order_id, '_payment_method', true );
    if(get_option('listeo_disable_payments')){
        $payment_method = 'cod';
    }
}

switch ($data->status) {
    case 'waiting' :
        $class[] = 'waiting-booking';
        $tag[] = '<span class="booking-status pending">'.esc_html__('Pending', 'listeo_core').'</span>';
        $show_approve = true;
        $show_reject = true;
        break;

    case 'attention' :
        $class[] = 'waiting-booking';
        $tag[] = '<span class="booking-status pending">'.esc_html__('Pending', 'listeo_core').'</span>';
        $show_approve = true;
        $show_reject = true;
        break;

    case 'confirmed' :
        $class[] = 'approved-booking';
        $tag[] = '<span  class="booking-status">'.esc_html__('Approved', 'listeo_core').'</span>';

        if($data->price>0){
            $tag[] = '<span class="booking-status unpaid">'.esc_html__('Unpaid', 'listeo_core').'</span>';
        }

        $show_approve = false;
        $show_reject = false;
        $show_cancel = true;
        break;

    case 'paid' :

        $class[] = 'approved-booking';
        $tag[] = '<span class="booking-status">'.esc_html__('Approved', 'listeo_core').'</span>';
        if($data->price>0){
            $tag[] = '<span class="booking-status paid">'.esc_html__('Paid', 'listeo_core').'</span>';
        }
        $show_approve = false;
        $show_reject = false;
        $show_cancel = true;
        break;

    case 'cancelled' :

        $class[] = 'canceled-booking';
        $tag[] = '<span class="booking-status">'.esc_html__('Canceled', 'listeo_core').'</span>';
        $show_approve = false;
        $show_reject = false;
        $show_delete = true;
        break;

    default:
        # code...
        break;
}


?>

<li class="<?php echo implode(' ',$class); ?>" id="booking-list-<?php echo esc_attr($data->ID);?>">

    <div class="list-box-listing bookings">
<!--        <div class="list-box-listing-img"><a href="--><?php //echo get_author_posts_url($data->bookings_author); ?><!--">--><?php //echo get_avatar($data->bookings_author, '70') ?><!--</a></div>-->
        <div class="list-box-listing-content" style="padding: 0px 20px;">
            <div class="inner">
                <h3 id="title"><a href="<?php echo get_permalink($data->listing_id); ?>"><?php echo get_the_title($data->listing_id); ?></a> <?php echo implode(' ',$tag); ?></h3>

                <div class="inner-booking-list">
                    <h5><?php esc_html_e('Booking Date:', 'listeo_core'); ?></h5>
                    <ul class="booking-list">
                        <?php
                        //get post type to show proper date
                        $listing_type = get_post_meta($data->listing_id,'_listing_type', true);

                        if($listing_type == 'rental') { ?>
                            <li class="highlighted" id="date"><?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_start)); ?> - <?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_end)); ?></li>
                        <?php } else if($listing_type == 'service') { ?>
                            <li class="highlighted" id="date">
                                <input type="hidden" id="startDateHidden" value="<?php echo $data->date_start ?>">
                                <input type="hidden" id="endDateHidden" value="<?php echo $data->date_end ?>">
                                <?php
                                $timestamp1 = strtotime($data->date_start);
                                if($timestamp1 != 0){
                                    $day = date('j, ', $timestamp1);
                                    $numMonth = date("m" ,$timestamp1);
                                    $month = $monthNames[$numMonth - 1];
                                    $year = date(", Y H:i",$timestamp1);
                                    echo $day . $month . $year;
                                }else{
                                    $time = date('j, F, Y H:i', $timestamp1); echo $time;
                                }
                                ?> - <?php

                                $tim = substr($data->date_end, strrpos($data->date_end, ' ') + 1) . "\n";
                                $da1 = strtok($data->date_start, ' ');
                                $da2 = strtok($data->date_end, ' ');
                                $ti1 = substr($data->date_start, strrpos($data->date_start, ' ') + 1) . "\n";
                                $ti2 = $tim;
                                if ($tim == 0) {
                                    $b = strtok($data->date_end, ' ');
                                    $b = strtotime($b);
                                    if($b != 0){
                                        $day = date('j, ', $b);
                                        $numMonth = date("m" ,$b);
                                        $month = $monthNames[$numMonth - 1];
                                        $year = date(", Y",$b);
                                        echo $day . $month . $year . ' 24:00';
                                    }else {
                                        $ab = date('j, F, Y', $b);
                                        echo $ab . ' 24:00';
                                    }
                                } else {
                                    $timestamp2 = strtotime($data->date_end);
                                    if($timestamp2 != 0){
                                        $day = date('j, ', $timestamp2);
                                        $numMonth = date("m" ,$timestamp2);
                                        $month = $monthNames[$numMonth - 1];
                                        $year = date(", Y H:i",$timestamp2);
                                        echo $day . $month . $year;
                                    }else {
                                        $time2 = date('j, F, Y H:i', $timestamp2);
                                        echo $time2;
                                    }
                                }
                                ?></li>
                        <?php } else { //event?>
                            <li class="highlighted" id="date">

                                <?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_start)); ?>

                                <?php
                                $event_start = get_post_meta($data->listing_id,'_event_date', true);
                                $event_date = explode(' ', $event_start);
                                if( isset($event_date[1]) ) { ?>
                                    <?php esc_html_e('at','listeo_core'); ?>

                                    <?php echo date_i18n(get_option( 'time_format' ), strtotime($event_date[1]));
                                }?>
                            </li>
                        <?php }
                        ?>

                    </ul>
                </div>

                <?php $details = json_decode($data->comment);


                if (
                    (isset($details->childrens) && $details->childrens > 0)
                    ||
                    (isset($details->adults) && $details->adults > 0)
                    ||
                    (isset($details->tickets) && $details->tickets > 0)
                ) { ?>
                    <div class="inner-booking-list">
                        <h5><?php esc_html_e('Booking Details:', 'listeo_core'); ?></h5>
                        <ul class="booking-list">
                            <li class="highlighted" id="details">
                                <?php if( isset($details->childrens) && $details->childrens > 0) : ?>
                                    <?php printf( _n( '%d Child', '%s Children', $details->childrens, 'listeo_core' ), $details->childrens ) ?>
                                <?php endif; ?>
                                <?php if( isset($details->adults)  && $details->adults > 0) : ?>
                                    <?php
                                    $ar = json_encode(get_the_terms($data->listing_id, 'listing_category'));
                                    $category_id = substr($ar, 12);
                                    $category_id = substr($category_id, 0, strpos($category_id, ","));
                                    if($category_id == '164' || $category_id == '165' || $category_id == '297' || $category_id == '314' || $category_id == '82' || $category_id == '244' || $category_id == '245' || $category_id == '246' || $category_id == '247' || $category_id == '249' || $category_id == '250' || $category_id == '251' || $category_id == '253' || $category_id == '254' || $category_id == '158'){
                                        printf( _n( '%d Antall', '%s Antall', $details->adults, 'listeo_core' ), $details->adults );
                                    }else{
                                        printf( _n( '%d Antall', '%s Antall', $details->adults, 'listeo_core' ), $details->adults );
                                    }?>
                                <?php endif; ?>
                                <?php if( isset($details->tickets)  && $details->tickets > 0) : ?>
                                    <?php printf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'listeo_core' ), $details->tickets ) ?>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                <?php } ?>

                <?php
                $currency_abbr = get_option( 'listeo_currency' );
                $currency_postion = get_option( 'listeo_currency_postion' );
                $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                if($data->price): ?>
                    <div class="inner-booking-list">
                        <h5><?php esc_html_e('Price:', 'listeo_core'); ?></h5>
                        <ul class="booking-list">
                            <li class="highlighted" id="price">
                                <?php if($currency_postion == 'before') { echo $currency_symbol.' '; }  ?>
                                <?php echo number_format_i18n($data->price); ?>
                                <?php if($currency_postion == 'after') { echo ' '.$currency_symbol; }  ?>
                                <input id="priceHiddenInput" type="hidden" value="<?php echo $data->price ?>">
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="inner-booking-list">

                    <h5><?php esc_html_e('Client:', 'listeo_core'); ?></h5>
                    <ul class="booking-list" id="client">
                        <?php if( isset($details->first_name) || isset($details->last_name) ) : ?>
                            <li id="name">
                                <a href="<?php echo get_author_posts_url($data->bookings_author); ?>"><?php if(isset($details->first_name)) echo $details->first_name; ?> <?php if(isset($details->last_name)) echo $details->last_name; ?></a></li>
                        <?php endif; ?>
                        <?php if( isset($details->email)) : ?><li id="email"><a href="mailto:<?php echo esc_attr($details->email) ?>"><?php echo $details->email; ?></a></li>
                        <?php endif; ?>
                        <?php if( isset($details->phone)) : ?><li id="phone"><a href="tel:<?php echo esc_attr($details->phone) ?>"><?php echo $details->phone; ?></a></li>
                        <?php endif; ?>
                    </ul>

                </div>
                <?php if( isset($details->billing_address_1) ) : ?>
                    <div class="inner-booking-list">

                        <h5><?php esc_html_e('Address:', 'listeo_core'); ?></h5>
                        <ul class="booking-list" id="client">

                            <?php if( isset($details->billing_address_1) ) : ?>
                                <li id="billing_address_1"><?php echo $details->billing_address_1; ?> </li>
                            <?php endif; ?>
                            <?php if( isset($details->billing_address_1) ) : ?>
                                <li id="billing_postcode"><?php echo $details->billing_postcode; ?> </li>
                            <?php endif; ?>
                            <?php if( isset($details->billing_city) ) : ?>
                                <li id="billing_city"><?php echo $details->billing_city; ?> </li>
                            <?php endif; ?>
                            <?php if( isset($details->billing_country) ) : ?>
                                <li id="billing_country"><?php echo $details->billing_country; ?> </li>
                            <?php endif; ?>

                        </ul>
                    </div>
                <?php endif; ?>
                <?php if( isset($details->service) && !empty($details->service)) : ?>
                    <div class="inner-booking-list">
                        <h5><?php esc_html_e('Extra Services:', 'listeo_core'); ?></h5>
                        <?php echo listeo_get_extra_services_html($details->service); //echo wpautop( $details->service); ?>
                    </div>
                <?php endif; ?>
                <?php if( isset($details->message) && !empty($details->message)) : ?>
                    <div class="inner-booking-list singleMessageText">
                        <h5><?php esc_html_e('Message:', 'listeo_core'); ?></h5>
                        <?php echo wpautop( $details->message); ?>
                    </div>
                <?php endif; ?>


                <div class="inner-booking-list">
                    <h5><?php esc_html_e('Request sent:', 'listeo_core'); ?></h5>
                    <ul class="booking-list">
                        <li class="highlighted" id="price">
                            <?php
                            $timestamp2 = strtotime($data->created);
                            if($timestamp2 != 0){
                                $day = date('j, ', $timestamp2);
                                $numMonth = date("m" ,$timestamp2);
                                $month = $monthNames[$numMonth - 1];
                                $year = date(", Y ",$timestamp2);
                                $hour = date(' H:i',$timestamp2);
                                echo $day . $month . $year ."på".$hour;
                            }else {
                                $time2 = date('j, F, Y H:i', $timestamp2);
                                echo $time2;
                            }
                            ?>
                            <!--							--><?php //echo date_i18n(get_option( 'date_format' ), strtotime($data->created)); ?>
                            <!--							--><?php
                            //								$date_created = explode(' ', $data->created);
                            //									if( isset($date_created[1]) ) { ?>
                            <!--									--><?php //esc_html_e('at','listeo_core'); ?>
                            <!--									-->
                            <!--							--><?php //echo date_i18n(get_option( 'time_format' ), strtotime($date_created[1])); } ?>
                        </li>
                    </ul>
                </div>



            </div>
        </div>
    </div>
    <div>
            <a href="#small-dialog" data-recipient="<?php echo esc_attr($data->bookings_author); ?>" data-booking_id="booking_<?php echo esc_attr($data->ID); ?>" class="booking-message rate-review popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_attr_e('Send Message','listeo_core') ?></a>


            <a href="#" class="button gray giveOffer" style="padding: 5px 7px;top: 15px;"><i class="fa fa-paper-plane"></i> Gi tilbud</a>


        <?php if($payment_method == 'cod'){ ?>
            <a href="#" class="button gray mark-as-paid" style="padding: 5px 5px;top: 15px;" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i style="padding: 0px;font-size: smaller;" class="sl sl-icon-check"></i> <?php esc_html_e('Confirm Payment', 'listeo_core'); ?></a>
        <?php } ?>

        <?php if($show_reject) : ?>
            <a href="#" class="button gray reject" style="padding: 5px 5px;top: 15px;" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i style="padding: 0px;font-size: smaller;" class="sl sl-icon-close"></i> <?php esc_html_e('Reject', 'listeo_core'); ?></a>
        <?php endif; ?>

        <?php if($show_cancel) : ?>
            <a href="#" class="button gray cancel" style="padding: 5px 5px;top: 15px;" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i style="padding: 0px;font-size: smaller;" class="sl sl-icon-close"></i> <?php esc_html_e('Cancel', 'listeo_core'); ?></a>
        <?php endif; ?>

        <?php if(isset($show_delete) && $show_delete == true) : ?>
            <a href="#" class="button gray delete" style="padding: 5px 5px;top: 15px;" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i style="padding: 0px;font-size: smaller;" class="sl sl-icon-trash"></i> <?php esc_html_e('Delete', 'listeo_core'); ?></a>
        <?php endif; ?>

        <?php if($show_approve) : ?>
            <a href="#" class="button gray approve" style="padding: 5px 6px;top: 15px;" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i style="padding: 0px;font-size: smaller;" class="sl sl-icon-check"></i> <?php esc_html_e('Approve', 'listeo_core'); ?></a>
        <?php endif; ?>
    </div>
</li>
<script>
    jQuery('.buttons-to-right').each(function (){
        var id = jQuery(this).parent().attr('id');
        var text1 = jQuery(`#${id} #date`).text();
        var text1Color = jQuery(`#${id} #price`).css('color');
        var adres = jQuery(`#${id} #client #billing_address_1`).text();
        text1 = text1.trim();
        if(text1 == '1, January, 1970 00:00 - 1, January, 1970 24:00' || text1 == '1, January, 1970 12:00 - 1, January, 1970 24:00' || text1 == '01/01/1970 at 12:00 am - 12:00 am' || text1 == '30/11/-0001 at 12:00 am - 12:00 am' || text1 =='01/01/1970 00:00 - 01/01/1970 24:00' || text1 =='30/11/-0001 00:00 - 30/11/-0001 24:00') {
            jQuery(`#${id} #price`).parent().parent().css('display', 'none');
            jQuery(`#${id} #date`).parent().parent().css('display', 'none');
            jQuery(`#${id} #client #billing_address_1`).parent().parent().css('display', 'none');
            jQuery(`#${id} .buttons-to-right .approve`).hide();
            jQuery(`#${id} .fa.fa-pencil-square-o`).hide();
            if (adres == 'true ') {
                jQuery(`#${id} .booking-status.pending`).text('Venter på svar');
                jQuery(`#${id} .buttons-to-right .newoffer`).text('Gi et nytt tilbud');
                jQuery(`#${id} #price`).parent().parent().css('display', 'block');
            }
        }else{
            if (adres == 'true ') {
                console.log('addres is true');
                jQuery(`#${id} .booking-status.pending`).text('Venter på svar');
                jQuery(`#${id} #client #billing_address_1`).parent().parent().css('display', 'none');
                jQuery(`#${id} #price`).parent().parent().css('display', 'block');
                jQuery(`#${id} .buttons-to-right .newoffer`).text('Gi nytt tilbud');
            }
        }
    });
</script>