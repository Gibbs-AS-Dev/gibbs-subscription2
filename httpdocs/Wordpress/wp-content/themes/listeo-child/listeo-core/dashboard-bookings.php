<?php $template_loader = new Listeo_Core_Template_Loader;
$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$roles = $current_user->roles;
$role = array_shift( $roles );
// if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
// 	$template_loader = new Listeo_Core_Template_Loader; 
// 	$template_loader->get_template_part( 'account/owner_only'); 
// 	return;
// endif;
$type='';



if(isset($data->type)){
    if($data->type == 'user_booking') { $type="user"; }
}?>
<?php

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = strtok($url, '?');

$currentUrl = $url;
$slug1 = basename(get_permalink());
if ($url == $currentUrl && $slug1 != "bookinger") :

    $bookings_page = get_option('listeo_bookings_page');
    $isCurrentPageWithUrlParam = (isset($_GET["status"])) ? trim($_GET["status"]) : "";
    ?>

    <div class="margin-bottom-20" style="display:flex;" id="differentStatuses">

        <a <?php if ($isCurrentPageWithUrlParam == "attention") {
            echo "class=\"activeSubpage\"";
        }
        else {
            echo "href=\"" . $currentUrl . "?status=attention\"";
        } ?>><?php esc_html_e('Trenger din oppmerksomhet','listeo'); ?>
            <?php
                $count_pending = listeo_count_my_bookings_by_status($user_id, 'attention');
               /* $count_pending1 = listeo_count_my_bookings_with_status($user_id,'attention');
                $count_unpaid = listeo_count_my_bookings_with_status($user_id,'confirmed');*/
                $sum_counter = $count_pending;
                if (isset($sum_counter)): ?><span class="nav-tag <?php if ($isCurrentPageWithUrlParam == "waiting") echo "active" ?>"><?php echo esc_html($sum_counter); ?></span>
            <?php endif; ?>
        </a>

        <a <?php if ($isCurrentPageWithUrlParam == "paid") {
            echo "class=\"activeSubpage\"";
        }
        else {
            echo "href=\"" . $currentUrl . "?status=paid\"";
        } ?>><?php esc_html_e('Betalte bookinger','listeo'); ?> </a>

        <a <?php if ($isCurrentPageWithUrlParam == "cancelled") {
            echo "class=\"activeSubpage\"";
        }
        else {
            echo "href=\"" . $currentUrl . "?status=cancelled\"";
        } ?>><?php esc_html_e('Avslåtte/utgåtte forespørsler','listeo'); ?></a>
    </div>
<?php endif;
?>
<div class="row">
    <!-- Listings -->

    <div class="col-lg-12 col-md-12">
        <div class="dashboard-list-box  margin-top-0">

            <!-- Booking Requests Filters  -->
            <div class="booking-requests-filter">
                <?php if( $type == "user") : ?>
                    <input type="hidden" id="dashboard_type" name="dashboard_type" value="user">
                <?php endif; ?>
                <?php if( ( $type !== "user" && !isset($_GET['status']) ) || ( $type!== "user" && isset($_GET['status']) && $_GET['status'] == 'approved')) : ?>
                    <!-- Sort by -->
                    <div class="sort-by">
                        <div class="sort-by-select">
                            <select data-placeholder="<?php esc_attr_e('Default order','listeo_core') ?>" class="chosen-select-no-single" id="listing_status">
                                <option value="approved"><?php echo esc_html__( 'All Statuses', 'listeo_core') ?></option>
                                <option value="confirmed"><?php echo esc_html__( 'Unpaid', 'listeo_core') ?></option>
                                <option value="paid"><?php echo esc_html__( 'Paid', 'listeo_core') ?></option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['status']) && $_GET['status'] != 'approved'){ ?>
                    <input type="hidden" id="listing_status" value="<?php echo $_GET['status']; ?>">
                <?php } ?>
                <?php if( $type!== "user" && isset($data->listings) && !empty($data->listings)) : ?>
                    <!-- Sort by -->
                    <div class="sort-by">
                        <div class="sort-by-select select2_sort">
                            <select data-placeholder="Default order" class="chosen-select-no-single" id="listing_id">
                                <option value="show_all"><?php echo esc_html__( 'All Listings', 'listeo_core') ?></option>
                                <?php foreach( $data->listings as $listing_id) {?>
                                    <option value="<?php echo $listing_id; ?>"><?php echo get_the_title( $listing_id ); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Date Range -->
                <div id="booking-date-range-enabler">
                    <span><?php esc_html_e('Pick a Date','listeo_core'); ?></span>
                </div>

                <div id="booking-date-range" style="display: none;">
                    <span></span>
                </div>


            </div>

            <!-- Reply to review popup -->


            <h4><?php ($type=="user") ? esc_html_e('Your Bookings', 'listeo_core') : esc_html_e('Booking Requests', 'listeo_core');
                ?> <i class="fa fa-circle-o-notch fa-spin booking-loading-icon"></i> </h4>


            <ul id="no-bookings-information" style="display: none">
                <?php esc_html_e( 'We haven\'t found any bookings for that criteria', 'listeo_core' ); ?>
            </ul>
            <?php if(isset($data->bookings) && empty($data->bookings)) { ?>
                <ul id="no-bookings-information">
                    <?php esc_html_e( 'You don\'t have any bookings yet', 'listeo_core' ); ?>
                </ul>
            <?php } else { ?>
                <ul id="booking-requests">
                    <?php
                    foreach ($data->bookings as $key => $value) {
                        $value['listing_title'] = get_the_title( $value['listing_id'] );
                        if($type == "user" ){
                            $template_loader->set_template_data( $value )->get_template_part( 'booking/content-user-booking' );
                        } else {
                            $template_loader->set_template_data( $value )->get_template_part( 'booking/content-booking' );
                        }

                    } ?>
                </ul>
            <?php } ?>

        </div>
        <div class="pagination-container ">
            <?php echo listeo_core_ajax_pagination( $data->pages, 1  ); ?>
        </div>
        <div id="small-dialog" class="zoom-anim-dialog mfp-hide">
            <div class="small-dialog-header">
                <h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
            </div>
            <div class="message-reply margin-top-0">
                <form action="" id="send-message-from-widget" data-booking_id="">
					<textarea
                            data-recipient=""
                            data-referral=""
                            required
                            cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message','listeo_core'); // echo $owner_data->first_name; ?>"></textarea>
                    <button class="button">
                        <i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>
                    <div class="notification closeable success margin-top-20"></div>

                </form>

            </div>
        </div>
        <a style="display:none;" href="#small-dialog" id="message_modalbtn" class="send-message-to-owner button popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_html_e('Send Message', 'listeo_core'); ?></a>
    </div>
</div>


<script type="text/javascript" >
    if(jQuery('body').is('.page-id-69')){
        var parentIdCut;
        let ajax_dataa;
        var id;

        jQuery('.approve').click(function (e) {
            id = jQuery(this).attr('data-booking_id');
            ajax_dataa = {
                'action': 'db_update_test',
                'status': 'approved',
                'reservation_id': id
            };

            e.preventDefault();
            console.log('hooked up');
            jQuery.ajax({
                type: "POST",
                url: listeo.ajaxurl,
                data: ajax_dataa,
                success: function () {
                    console.log('success');
                    console.log(id);
                }
            });
        });

        jQuery('.reject').click(function (e) {
            id = jQuery(this).attr('data-booking_id');
            ajax_dataa = {
                'action': 'db_update_test',
                'status': 'rejected',
                'reservation_id': id
            };
            e.preventDefault();
            console.log('hooked up');
            jQuery.ajax({
                type: "POST",
                url: listeo.ajaxurl,
                data: ajax_dataa,
                success: function () {
                    console.log('success');
                    console.log(id);
                }
            });
        });



        var input = document.getElementById("sub");
        var start = new Date(1);
        var end = new Date(1);
        start = moment( start, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
        end = moment( end, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

        var ajax_data;

        jQuery('.fa').on('click', function(){

            var parentId = jQuery(this).parent().attr('id');
            parentIdCut = parentId.substr(  parentId.indexOf( '-' ) + 6 );
            jQuery(this).parent().append(`<i class="fa fa-check-square" style="color: limegreen; font-size:30px; bottom: 0px;position: relative;left: 860px;"></i>`);
            var dateText = jQuery(`#${parentId} #date`).text();
            dateText = dateText.trim();
            var detailsText = jQuery(`#${parentId} #details`).text();
            detailsText = detailsText.trim();

            detailsText = detailsText.substring(0, detailsText.indexOf(' '));
            var locationText = jQuery(`#${parentId} #location`).text();
            locationText = locationText.trim();
            var nameText = jQuery(`#${parentId} #client #name`).text();
            var emailText = jQuery(`#${parentId} #client #email`).text();
            var phoneText = jQuery(`#${parentId} #client #phone`).text();
            // var addressText = jQuery(`#${parentId} #billing_address_1`).text();
            var messageText = jQuery(`#${parentId} .inner-booking-list:contains("Message") p`).text();
            const format2 = "YYYY-MM-DD";
            var minDate = new Date();
            var dateTime2 = moment(minDate).format(format2);

            jQuery(`#${parentId} #date`).html(`<input data="${dateText}" type="datetime-local" min="${dateTime2}T08:00" max="2021-06-14T22:00" value="${dateText}" style="height: 25px;margin: 1px;position: relative;top: 25px;"/><input data="enddate" type="datetime-local" min="${dateTime2}T08:00" max="2021-06-14T22:00" value="${dateText}" style=" height: 25px;margin: 1px; left: 280px;bottom: 0px;position: relative;"/>`);
            jQuery(`#${parentId} #details`).html(`<input data="${detailsText}" value="${detailsText}" style="height: 25px;margin: 1px;"/>`);
            jQuery(`#${parentId} #location`).html(`<input data="${locationText}" value="${locationText}" style="height: 25px;margin: 1px;"/>`);
            jQuery(`#${parentId} #client #name`).html(`<input data="${nameText}" value="${nameText}" style="height: 25px;margin: 1px;top: 25px;position: relative;"/>`);
            jQuery(`#${parentId} #email`).html(`<input data="${emailText}" value="${emailText}" style="height: 25px;margin: 1px; top: 25px;position: relative;"/>`);
            jQuery(`#${parentId} #client #phone`).html(`<input data="${phoneText}" value="${phoneText}" style="height: 25px;margin: 1px;top: -2px; position: relative;"/>`);
            // jQuery(`#${parentId} #billing_address_1`).html(`<input data="${addressText}" value="${addressText}" style="height: 25px;margin: 1px;top: 25px;position: relative;"/>`);
            jQuery(`#${parentId} .inner-booking-list:contains("Message") p`).html(`<input data="${messageText}" value="${messageText}" style="height: 25px;margin: 1px;position: relative;"/>`);



            jQuery('.fa-check-square').on('click', function(){
                var dateData = jQuery(`#${parentId} #date [data="${dateText}"]`).val();
                var endData = jQuery(`#${parentId} #date [data="enddate"]`).val();
                var detailsData = jQuery(`#${parentId} #details [data="${detailsText}"]`).val();
                var locationData = jQuery(`#${parentId} #location [data="${locationText}"]`).val();
                var nameData = jQuery(`#${parentId} #client #name [data="${nameText}"]`).val();
                var emailData = jQuery(`#${parentId} #email [data="${emailText}"]`).val();
                var phoneData = jQuery(`#${parentId} #client #phone [data="${phoneText}"]`).val();
                // var addressData = jQuery(`#${parentId} #billing_address_1 [data="${addressText}"]`).val();
                var messageData = jQuery(`#${parentId} .inner-booking-list:contains("Message") p [data="${messageText}"]`).val();



                var firstname = nameData.substring(0, nameData.indexOf(' '));
                var lastname = nameData.substring(nameData.indexOf(' '), nameData.length);
                if (firstname.length < 1){
                    firstname = lastname;
                    lastname ='';
                }
                let comment = {"first_name":`${firstname}`,"last_name":`${lastname}`,"email":`${emailData}`,"phone":`${phoneData}`,"adults":`${detailsData}`,"message":`${messageData}`,"service":false,"billing_address_1":false,"billing_postcode":false,"billing_city":false,"billing_country":false};

                // Object.values(comment).forEach((value) => {
                // 	console.log(value);
                // })
                ajax_data = {
                    'action': 'change_date',
                    'data_id': parentIdCut,
                    'date_start' : dateData,
                    'date_end' : endData,
                    'comment' : comment
                };
                jQuery.ajax({
                    type: 'POST', dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data){
                        console.log('success');
                    }
                });

                const f2 = "DD/MM/YYYY HH:mm";
                var dt2 = moment(dateData).format(f2);
                const f1 = "DD/MM/YYYY HH:mm";
                var dt1 = moment(endData).format(f1);
                var time = dt2.split(' ').pop();
                var time2 = dt1.split(' ').pop();
                dt2 = dt2.substring(0, dt2.indexOf(' '));
                dt1 = dt1.substring(0, dt1.indexOf(' '));
                jQuery(`#${parentId} #date`).html(dt2 +' at ' +time+ ' - ' + dt1 +' at ' +time2);
                jQuery(`#${parentId} #details`).html(detailsData + ' Guests');
                jQuery(`#${parentId} #location`).html(locationData);
                jQuery(`#${parentId} #client #name`).html(nameData);
                jQuery(`#${parentId} #email`).html(emailData);
                jQuery(`#${parentId} #client #phone`).html(phoneData);
                // jQuery(`#${parentId} #billing_address_1`).html(addressData);
                if(messageData == undefined){
                    jQuery(`#${parentId} .inner-booking-list:contains("Message") p`).html('');
                }else{
                    jQuery(`#${parentId} .inner-booking-list:contains("Message") p`).html(messageData);
                }
                jQuery(this).remove();
            });

        });

        // GIVE NEW OFFER
        var element;

        jQuery('#header-container').on('click','.sendoffer', function(){
            jQuery(`#${id} #price`).parent().parent().css('display', 'block');

            var parentId = element.attr('id');
            var recipient = jQuery(`#${parentId} .booking-message`).data('recipient');
            var referral = jQuery(`#${parentId} .booking-message`).data('booking_id');
            var msgforsending = jQuery(`._message`).val();


            jQuery('#send-message-from-widget textarea').attr('data-referral',referral).attr('data-recipient',recipient);
            jQuery('#send-message-from-widget textarea').val(msgforsending);
            jQuery('#send-message-from-widget').parent().parent().css('visibility','hidden');
            jQuery('.send-message-to-owner').trigger('click');
            jQuery('#send-message-from-widget .button').click();


            parentIdCut = parentId.substr(  parentId.indexOf( '-' ) + 6 );
            jQuery(this).parent().append(`<i class="fa fa-check-square" style="color: limegreen; font-size:30px; bottom: 0px;position: relative;left: 860px;"></i>`);
            var dateText = jQuery(`#${parentId} #date`).text();
            dateText = dateText.trim();
            var detailsText = jQuery(`#${parentId} #details`).text();
            detailsText = detailsText.trim();

            detailsText = detailsText.substring(0, detailsText.indexOf(' '));
            var locationText = jQuery(`#${parentId} #location`).text();
            locationText = locationText.trim();
            var nameText = jQuery(`#${parentId} #client #name`).text();
            var emailText = jQuery(`#${parentId} #client #email`).text();
            var phoneText = jQuery(`#${parentId} #client #phone`).text();
            // var addressText = jQuery(`#${parentId} #billing_address_1`).text();
            var messageText = jQuery(`#${parentId} .inner-booking-list:contains("Message") p`).text();
            var nameData = jQuery(`#${parentId} #client #name a`).text();

            const format2 = "YYYY-MM-DD";
            var minDate = new Date();
            var dateTime2 = moment(minDate).format(format2);
            var msg = jQuery('._message').val();
            var pric = jQuery('._price').val();
            var firstname = nameData.substring(0, nameData.indexOf(' '));
            var lastname = nameData.substring(nameData.indexOf(' '), nameData.length);
            if (firstname.length < 1){
                firstname = lastname;
                lastname ='';
            }
            var dateData = jQuery(`#${parentId} #date [data="${dateText}"]`).val();
            var endData = jQuery(`#${parentId} #date [data="enddate"]`).val();
            var detailsData = jQuery(`#${parentId} #details [data="${detailsText}"]`).val();
            var locationData = jQuery(`#${parentId} #location [data="${locationText}"]`).val();
            var emailData = jQuery(`#${parentId} #email a`).text();
            var phoneData = jQuery(`#${parentId} #client #phone a`).text();
            // var addressData = jQuery(`#${parentId} #billing_address_1 [data="${addressText}"]`).val();
            let comment = {"first_name":`${firstname}`,"last_name":`${lastname}`,"email":`${emailData}`,"phone":`${phoneData}`,"message":`${msg}`,"billing_address_1":'true'};


            ajax_data = {
                'action': 'new_offer',
                'data_id': parentIdCut,
                'comment' : comment,
                'price' : pric
            };
            jQuery.ajax({
                type: 'POST', dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data){
                    console.log('success');
                }
            });
            jQuery('.popup').hide();
            setTimeout(function(){
                location.reload();
            },1000);
        });
        jQuery('#header-container').on('click','.closepopup' , function(){
            console.log('close clicked');
            jQuery('.popup').hide();
        });
    }else if(jQuery('body').is('.page-id-51')){
        // APPROVE BUTTON

        jQuery('.approvebtn').on('click', function(e){
            var parentId = jQuery(this).parent().parent().attr('id');
            var IdCut = parentId.substr(  parentId.indexOf( '-' ) + 6 );

            var data = {
                'action': 'change_status',
                'booking_id': IdCut,
                'status': 'confirmed'
            };

            e.preventDefault();
            jQuery.ajax({
                type: "POST",
                url: listeo.ajaxurl,
                data: data,
                success: function () {
                    console.log('success');
                }
            });
            setTimeout(function(){
                location.reload();
            },1000);
        });
    }

</script>