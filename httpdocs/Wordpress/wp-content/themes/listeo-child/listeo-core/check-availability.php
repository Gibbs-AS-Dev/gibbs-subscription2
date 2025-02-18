<?php
$template_loader = new Listeo_Core_Template_Loader;

if (!defined('ABSPATH')) {
    exit;
}

get_header(get_option('header_bar_style', 'standard'));
$post_info = $post;
$post_meta = get_post_meta($post_info->ID);

// get slots and check if not empty
if (isset($post_meta['_slots_status'][0]) && !empty($post_meta['_slots_status'][0])) {
    if (isset($post_meta['_slots'][0])) {
        $slots = json_decode($post_meta['_slots'][0]);
        if (strpos($post_meta['_slots'][0], '-') == false) $slots = false;
    } else {
        $slots = false;
    }
} else {
    $slots = false;
}

$days_list = array(
    0	=> __('Monday','listeo_core'),
    1 	=> __('Tuesday','listeo_core'),
    2	=> __('Wednesday','listeo_core'),
    3 	=> __('Thursday','listeo_core'),
    4 	=> __('Friday','listeo_core'),
    5 	=> __('Saturday','listeo_core'),
    6 	=> __('Sunday','listeo_core'),
);

?>
<div class="container">
    <div id="clockLoading">
        <span class="timer-loader" style="position: absolute; top: 65%; left: 49%; Z-INDEX: 100;">Loading&#8230;</span>
    </div>
    <div id="unavailabilityPopup" style="display:none;z-index:1000; width:auto; height:auto; background:white; margin:auto;position:fixed; top: 15%;left:35%">
        <div class="row" style="background: #F7F7F7;">
            <div class="col-xs-6 col-md-11" style=" font-size: 20px;padding: 20px; color: black;">
                <span>Set unavailability</span>
            </div>
            <div class="col-xs-6 col-md-1" style="font-size: 20px;padding: 20px 20px 20px 0;">
                <i class="fa fa-times closePopupNewOffer" aria-hidden="true" style="padding: 6px; border-radius: 20px;"></i>
            </div>
        </div>
        <div class="row" style="padding: 15px 20px 0px 15px;background: white;">
            <div class="col-xs-6 col-md-6">
                <span>Fra:</span>
            </div>
            <div class="col-xs-6 col-md-6">
                <span>Til:</span>
            </div>
        </div>
        <div class="row" style="padding: 0px 15px 5px 15px;background: white;">
            <div class="col-xs-6 col-md-6" style="padding:0 15px 15px 15px; text-align: center;">
                <input id="startDateUnavailability" type="datetime-local" min='<?php echo date('Y-m-d') . "T00:00"; ?>' />
            </div>
            <div class="col-xs-6 col-md-6" style="padding:0 15px 15px 15px; text-align: center;">
                <input id="endDateUnavailability" type="datetime-local" min='<?php echo date('Y-m-d') . "T00:00"; ?>'/>
            </div>
        </div>

        <div class="row" style="padding: 0px 15px 5px 15px;background: white;">
            <div class="col-xs-6  col-md-6" style="padding: 14px 0 14px 20px">
                <span style="font-size: larger">Set status: </span>
            </div>
            <div class="col-xs-6  col-md-6">
                <select style="appearance: auto;pointer-events: none;">
                    <option>Unavailable</option>
                </select>
            </div>
        </div>
        <div class="row" style="padding: 0 15px 5px 15px;background: white;">
            <div class="col-xs-12  col-md-12" style = "padding:0 0 0px 20px;">
                <span style="font-size: larger">Note:</span>
            </div>
        </div>

        <div class="row" style="padding: 0px 15px 5px 15px;background: white;">
            <div class="col-xs-12  col-md-12" style = "padding:0 15px 15px 20px;">
                <textarea style="pointer-events:none;" placeholder="We are renovating."></textarea>
            </div>
        </div>

        <div class="row" style="padding: 0px 15px 5px 15px;background: white;">
            <div class="col-xs-12  col-md-12" style = "padding:0 0 15px 0; text-align: center;">
                <a id="sendUnavailability" class="button gray"><i class="fa fa-paper-plane"></i> Save</a>
            </div>
        </div>
    </div>
    <div id="singleNewOffer" style="display:none;z-index:1000; width:auto; height:auto; background:white; margin:auto;position:fixed; top: 30%;left:35%">
        <div class="row" style="background: #F7F7F7;">
            <div class="col-xs-6 col-md-11" style=" font-size: 20px;padding: 20px; color: black;">
                <span>Send new offer</span>
            </div>
            <div class="col-xs-6 col-md-1" style="font-size: 20px;padding: 20px 20px 20px 0;">
                <i class="fa fa-times closePopupNewOffer" aria-hidden="true" style="padding: 6px; border-radius: 20px;"></i>
            </div>
        </div>
        <div class="row" style="padding: 15px 15px 5px 15px;background: white;">
            <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
                <input id="_startDate" type="datetime-local" min='<?php echo date("Y-m-d") . "T00:00"; ?>' />
            </div>
            <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
                <input id="_endDate" type="datetime-local" min='<?php echo date("Y-m-d") . "T00:00"; ?>'/>
            </div>
        </div>
        <div class="row" style="padding: 15px 15px 5px 15px;background: white;" >
            <div class="col-xs-12 col-md-12">
                <div style=" padding: 0; ">
                    <textarea class="_message1" style="height: 25px;margin: 1px;" placeholder="Din melding"></textarea>
                </div>
            </div>
        </div>
        <div class="row" style="background: white;">
            <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
                <div class="col-xs-5 col-md-5" style="padding: 0; text-align: center;">
                        <span style="font-size: 13px;">Ny pris<span/>
                </div>
                <div class="col-xs-7 col-md-7" style=" padding: 0; ">
                    <input class="_price1" type="number" style="height: 25px;margin: 1px;"/>
                </div>
            </div>
            <div class="col-xs-6 col-md-6" style = "padding: 20px; text-align: center;">
                <a  class="button gray singleOffer"><i class="fa fa-paper-plane"></i> Gi nytt tilbud</a>
            </div>
        </div>
    </div>
    <div id="reservationPopup" style="visibility: hidden;border-radius: 5px;position:fixed; z-index:1000; width:auto; height:auto; background:white; top: 40%; left: 38%">
        <div class="row" style="padding: 15px 40px;">
            <span>List of reservations</span>
            <i class="fa fa-times listOfReservationCloseBtn" style="float:right;padding: 6px;" aria-hidden="true"></i>
        </div>
        <div class="row" style="text-align: center">
            <div class="col-lg-3">
                <h5>ID</h5>
            </div>
            <div class="col-lg-3">
                <h5>START TIME</h5>
            </div>
            <div class="col-lg-3">
                <h5>END TIME</h5>
            </div>
            <div class="col-lg-3">
                <h5>CLIENT NAME</h5>
            </div>
        </div>
        <div class="row">
            <ul id="listOfReservations" style="list-style: none;text-align: center;">
            </ul>
        </div>
    </div>
    <div id="singleReservationPopup" style="visibility: hidden;border-radius: 5px;position:fixed; z-index:1000; width:auto; height:auto; background:white; top: 20%; left: 38%">
        <div class="row" style="padding: 0px 40px;">
            <i class="fa fa-times listOfReservationCloseBtn" style="float:right;padding: 6px;" aria-hidden="true"></i>
        </div>
        <ul id="singleReservation" style="list-style: none;padding: 0px;">

        </ul>
    </div>
    <div class="col-lg-12" style="display:none;">
        <div class="panel-dropdown time-slots-dropdown">
            <a href="#" placeholder="<?php esc_html_e('Time Slots','listeo_core') ?>"><?php esc_html_e('Time Slots','listeo_core') ?></a>

            <div class="panel-dropdown-content padding-reset">
                <div class="no-slots-information"><?php esc_html_e('No slots for this day','listeo_core') ?></div>
                <div class="panel-dropdown-scrollable">
                    <input id="slot" type="hidden" name="slot" value="" />
                    <input id="listing_id" type="hidden" name="listing_id" value="<?php echo $post_info->ID; ?>" />
                    <?php foreach( $slots as $day => $day_slots) {
                        if ( empty( $day_slots )) continue;
                        ?>
                        <?php foreach( $day_slots as $number => $slot) {
                            $slot = explode('|' , $slot); ?>
                            <!-- Time Slot -->
                            <div class="time-slot" day="<?php echo $day; ?>">
                                <input type="radio" name="time-slot" id="<?php echo $day.'|'.$number; ?>" value="<?php echo $day.'|'.$number; ?>">
                                <label for="<?php echo $day.'|'.$number; ?>">
                                    <p class="day"><?php echo $days_list[$day]; ?></p>
                                    <div style="display: none;" class="tests" data="helo"><?php echo $slot[0]; ?></div>
                                    <span style="display: none;"><?php echo $slot[1]; esc_html_e(' slots available','listeo_core') ?></span>
                                </label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row margin-top-35">
        <div class="col-lg-12" style="text-align: center;color: black;">
            <span class="col-md-4 h1">Tilgjengelighet for</span>
            <select id="selectPost" style="appearance: auto;font-size: x-large;padding: inherit;" class="panel-dropdown col-md-8 margin-top-15">
                <?php
                global $current_user;
                wp_get_current_user();
                $author_query = array('posts_per_page' => '-1','author' => $current_user->ID);
                $author_posts = new WP_Query($author_query);
                while($author_posts->have_posts()) : $author_posts->the_post();
                    if(get_post_meta( $post->ID , '_booking_status',true) == 'on'){
                        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $post_link = get_permalink() . "?check_availability=1";
                        if($actual_link == $post_link){
                            ?>
                            <option selected="selected" data-address="<?php echo $post_link; ?>"><?php the_title(); ?></option>
                        <?php }else{ ?>
                            <option data-address="<?php echo $post_link ?>"><?php the_title(); ?></option>
                        <?php } }
                endwhile;
                ?>
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-lg-12 margin-top-35 margin-bottom-75">
        <table class="check-availability-table">
            <thead>
            <tr>
                <th class="firstArrow"><input value="<" type='button' class="previousbtn"></th>
                <th></th>
                <th></th>
                <th><select  id="getMonth" style="text-align-last: center;border:0;box-shadow: none;padding: 0 0 0 15%;font-size: large;appearance: auto;">
                        <option value='1'>Januar</option>
                        <option value='2'>Februar</option>
                        <option value='3'>Mars</option>
                        <option value='4'>April</option>
                        <option value='5'>Mai</option>
                        <option value='6'>Juni</option>
                        <option value='7'>Juli</option>
                        <option value='8'>August</option>
                        <option value='9'>September</option>
                        <option value='10'>Oktober</option>
                        <option value='11'>November</option>
                        <option value='12'>Desember</option>
                    </select></th>
                <th><select id="getYear" style="border:0;box-shadow: none;padding: 0 0 0 34%;font-size: large;appearance: auto;">

                    </select></th>
                <th></th>
                <th></th>
                <th class="secondArrow"><input style="text-align-last: center;float:right;" value=">" type="button" class="btn btn-warning nextbtn"></th>
            </tr>
            <tr id="daysNames">
                <th>UKE</th>
                <th id="0">MAN</th>
                <th id="1">TIR</th>
                <th id="2">ONS</th>
                <th id="3">TOR</th>
                <th id="4">FRE</th>
                <th id="5">LØR</th>
                <th id="6">SØN</th>
            </tr>
            <tr id="dateNumbers">
                <th id="monthOver"></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours0"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours1"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours2"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours3"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours4"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours5"></span></th>
                <th class="halfborderright halfborderleft"><span id="dateOverHours6"></span></th>
            </tr>
            </thead>
            <tbody>
            <tr class="0">
                <th class='halfright'><span class='halfright1'>00:00</span></th>
                <td class="0 0mon"></td>
                <td class="1 0tue"></td>
                <td class="2 0wed"></td>
                <td class="3 0thu"></td>
                <td class="4 0fri"></td>
                <td class="5 0sat"></td>
                <td class="6 0sun"></td>
            </tr>
            <tr class="1">
                <th class='halfright'><span class='halfright1'>01:00</span></th>
                <td class="0 1mon"></td>
                <td class="1 1tue"></td>
                <td class="2 1wed"></td>
                <td class="3 1thu"></td>
                <td class="4 1fri"></td>
                <td class="5 1sat"></td>
                <td class="6 1sun"></td>
            </tr>
            <tr class="2">
                <th class='halfright'><span class='halfright1'>02:00</span></th>
                <td class="0 2mon"></td>
                <td class="1 2tue"></td>
                <td class="2 2wed"></td>
                <td class="3 2thu"></td>
                <td class="4 2fri"></td>
                <td class="5 2sat"></td>
                <td class="6 2sun"></td>

            </tr>
            <tr class="3">
                <th class='halfright'><span class='halfright1'>03:00</span></th>
                <td class="0 3mon"></td>
                <td class="1 3tue"></td>
                <td class="2 3wed"></td>
                <td class="3 3thu"></td>
                <td class="4 3fri"></td>
                <td class="5 3sat"></td>
                <td class="6 3sun"></td>
            </tr>
            <tr class="4">
                <th class='halfright'><span class='halfright1'>04:00</span></th>
                <td class="0 4mon"></td>
                <td class="1 4tue"></td>
                <td class="2 4wed"></td>
                <td class="3 4thu"></td>
                <td class="4 4fri"></td>
                <td class="5 4sat"></td>
                <td class="6 4sun"></td>
            </tr>
            <tr class="5">
                <th class='halfright'><span class='halfright1'>05:00</span></th>
                <td class="0 5mon"></td>
                <td class="1 5tue"></td>
                <td class="2 5wed"></td>
                <td class="3 5thu"></td>
                <td class="4 5fri"></td>
                <td class="5 5sat"></td>
                <td class="6 5sun"></td>
            </tr>
            <tr class="6">
                <th class='halfright'><span class='halfright1'>06:00</span></th>
                <td class="0 6mon"></td>
                <td class="1 6tue"></td>
                <td class="2 6wed"></td>
                <td class="3 6thu"></td>
                <td class="4 6fri"></td>
                <td class="5 6sat"></td>
                <td class="6 6sun"></td>
            </tr>
            <tr class="7">
                <th class='halfright'><span class='halfright1'>07:00</span></th>
                <td class="0 7mon"></td>
                <td class="1 7tue"></td>
                <td class="2 7wed"></td>
                <td class="3 7thu"></td>
                <td class="4 7fri"></td>
                <td class="5 7sat"></td>
                <td class="6 7sun"></td>

            </tr>
            <tr class="8">
                <th class='halfright'><span class='halfright1'>08:00</span></th>
                <td class="0 8mon"></td>
                <td class="1 8tue"></td>
                <td class="2 8wed"></td>
                <td class="3 8thu"></td>
                <td class="4 8fri"></td>
                <td class="5 8sat"></td>
                <td class="6 8sun"></td>

            </tr>
            <tr class="9">
                <th class='halfright'><span class='halfright1'>09:00</span></th>
                <td class="0 9mon"></td>
                <td class="1 9tue"></td>
                <td class="2 9wed"></td>
                <td class="3 9thu"></td>
                <td class="4 9fri"></td>
                <td class="5 9sat"></td>
                <td class="6 9sun"></td>
            </tr>
            <tr class="10">
                <th class='halfright'><span class='halfright1'>10:00</span></th>
                <td class="0 10mon"></td>
                <td class="1 10tue"></td>
                <td class="2 10wed"></td>
                <td class="3 10thu"></td>
                <td class="4 10fri"></td>
                <td class="5 10sat"></td>
                <td class="6 10sun"></td>
            </tr>
            <tr class="11">
                <th class='halfright'><span class='halfright1'>11:00</span></th>
                <td class="0 11mon"></td>
                <td class="1 11tue"></td>
                <td class="2 11wed"></td>
                <td class="3 11thu"></td>
                <td class="4 11fri"></td>
                <td class="5 11sat"></td>
                <td class="6 11sun"></td>
            </tr>
            <tr class="12">
                <th class='halfright'><span class='halfright1'>12:00</span></th>
                <td class="0 12mon"></td>
                <td class="1 12tue"></td>
                <td class="2 12wed"></td>
                <td class="3 12thu"></td>
                <td class="4 12fri"></td>
                <td class="5 12sat"></td>
                <td class="6 12sun"></td>
            </tr>
            <tr class="13">
                <th class='halfright'><span class='halfright1'>13:00</span></th>
                <td class="0 13mon"></td>
                <td class="1 13tue"></td>
                <td class="2 13wed"></td>
                <td class="3 13thu"></td>
                <td class="4 13fri"></td>
                <td class="5 13sat"></td>
                <td class="6 13sun"></td>
            </tr>
            <tr class="14">
                <th class='halfright'><span class='halfright1'>14:00</span></th>
                <td class="0 14mon"></td>
                <td class="1 14tue"></td>
                <td class="2 14wed"></td>
                <td class="3 14thu"></td>
                <td class="4 14fri"></td>
                <td class="5 14sat"></td>
                <td class="6 14sun"></td>
            </tr>
            <tr class="15">
                <th class='halfright'><span class='halfright1'>15:00</span></th>
                <td class="0 15mon"></td>
                <td class="1 15tue"></td>
                <td class="2 15wed"></td>
                <td class="3 15thu"></td>
                <td class="4 15fri"></td>
                <td class="5 15sat"></td>
                <td class="6 15sun"></td>
            </tr>
            <tr class="16">
                <th class='halfright'><span class='halfright1'>16:00</span></th>
                <td class="0 16mon"></td>
                <td class="1 16tue"></td>
                <td class="2 16wed"></td>
                <td class="3 16thu"></td>
                <td class="4 16fri"></td>
                <td class="5 16sat"></td>
                <td class="6 16sun"></td>
            </tr>
            <tr class="17">
                <th class='halfright'><span class='halfright1'>17:00</span></th>
                <td class="0 17mon"></td>
                <td class="1 17tue"></td>
                <td class="2 17wed"></td>
                <td class="3 17thu"></td>
                <td class="4 17fri"></td>
                <td class="5 17sat"></td>
                <td class="6 17sun"></td>
            </tr>
            <tr class="18">
                <th class='halfright'><span class='halfright1'>18:00</span></th>
                <td class="0 18mon"></td>
                <td class="1 18tue"></td>
                <td class="2 18wed"></td>
                <td class="3 18thu"></td>
                <td class="4 18fri"></td>
                <td class="5 18sat"></td>
                <td class="6 18sun"></td>
            </tr>
            <tr class="19">
                <th class='halfright'><span class='halfright1'>19:00</span></th>
                <td class="0 19mon"></td>
                <td class="1 19tue"></td>
                <td class="2 19wed"></td>
                <td class="3 19thu"></td>
                <td class="4 19fri"></td>
                <td class="5 19sat"></td>
                <td class="6 19sun"></td>
            </tr>
            <tr class="20">
                <th class='halfright'><span class='halfright1'>20:00</span></th>
                <td class="0 20mon"></td>
                <td class="1 20tue"></td>
                <td class="2 20wed"></td>
                <td class="3 20thu"></td>
                <td class="4 20fri"></td>
                <td class="5 20sat"></td>
                <td class="6 20sun"></td>
            </tr>
            <tr class="21">
                <th class='halfright'><span class='halfright1'>21:00</span></th>
                <td class="0 21mon"></td>
                <td class="1 21tue"></td>
                <td class="2 21wed"></td>
                <td class="3 21thu"></td>
                <td class="4 21fri"></td>
                <td class="5 21sat"></td>
                <td class="6 21sun"></td>
            </tr>
            <tr class="22">
                <th class='halfright'><span class='halfright1'>22:00</span></th>
                <td class="0 22mon"></td>
                <td class="1 22tue"></td>
                <td class="2 22wed"></td>
                <td class="3 22thu"></td>
                <td class="4 22fri"></td>
                <td class="5 22sat"></td>
                <td class="6 22sun"></td>
            </tr>
            <tr class="23">
                <th class='halfright'><span class='halfright1' style="left: 21px;">23:00</span></th>
                <td class="0 23mon"></td>
                <td class="1 23tue"></td>
                <td class="2 23wed"></td>
                <td class="3 23thu"></td>
                <td class="4 23fri"></td>
                <td class="5 23sat"></td>
                <td class="6 23sun"></td>
            </tr>
            </tbody>
        </table>
        <div class="row col-lg-12 margin-top-25">
            <div class="row text-center" style="font-size:18px;">
                <div class="col-md-3" style="padding: 0;"><span style="height: 14px;width: 14px;background-color: #1D9781;border-radius: 50%;display: inline-block;"></span><span> Valgt</span></div>
                <div class="col-md-2" style="padding: 0;"><span style="height: 14px;width: 14px;background-color: #DA697A;border-radius: 50%;display: inline-block;"></span><span> Booket</span></div>
                <div class="col-md-2" style="padding: 0;"><span style="height: 14px;width: 14px;background-color: #FF9900;border-radius: 50%;display: inline-block;"></span><span> Venter</span></div>
                <div class="col-md-2" style="padding: 0;"><span style="border: black 1px solid;height: 14px;width: 14px;background-color: #FFFFFF;border-radius: 50%;display: inline-block;"></span><span> Tilgjengelig</span></div>
                <div class="col-md-3" style="padding: 0;"><span style="height: 14px;width: 14px;background-color: #C1C1C1;border-radius: 50%;display: inline-block;"></span><span> Utilgjengelig</span></div>
            </div>
        </div>
    </div>
    <div id="small-dialog" style="display:none;position: fixed;left: 38%;z-index: 100;">
        <div class="small-dialog-header">
            <h3 style="display: inline-block"><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
            <i class="fa fa-times listOfReservationCloseBtn" style="float: right"></i>
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
</div>

<?php
get_footer();

?>
<script type="text/javascript">
    //get Week function

    Date.prototype.getWeek = function () {
        var date = new Date(this.getTime());
        date.setHours(0, 0, 0, 0);
        date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
        var week1 = new Date(date.getFullYear(), 0, 4);
        return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000
            - 3 + (week1.getDay() + 6) % 7) / 7);
    }

    function startOfWeek(date) {
        var diff = date.getDate() - date.getDay() + (date.getDay() === 0 ? -6 : 1);
        return new Date(date.setDate(diff));
    }

    function endOfWeek(date) {
        var lastday = date.getDate() - (date.getDay() - 1) + 6;
        return new Date(date.setDate(lastday));
    }

    function loading(seconds) {
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');
        setTimeout(function () {
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, seconds);
    }

    window.mobileCheck = function () {
        let check = false;
        (function (a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    };

    <?php
    global $wpdb;
    $id = $post_info->ID;
    $_currDate = date("m/d/Y");
    $results = $wpdb->get_results("SELECT * FROM '" . $wpdb->prefix . "bookings_calendar' WHERE 'listing_id' = '$id'");
    $unavailableResults = $wpdb->get_results("SELECT * FROM '" . $wpdb->prefix . "r' WHERE 'listing_id' = '$id' AND 'status' = 'unavailable'");
    $waiting = array();
    $approved = array();
    $rejected = array();
    $_currDate = date("m/d/Y");
    foreach ($results as $item) {
//        if ($_currDate < $item->date_start) {
        $start = date_format(date_create($item->date_start),"m/d/Y");
        $end = date_format(date_create($item -> date_end),"m/d/Y");
        $stHour = date_format(date_create($item -> date_start),"H");
        $enHour = date_format(date_create($item -> date_end),"H");

        if ($item->status == 'waiting' || $item->status == 'attention') {
            $waiting[] = "{$start}|{$end}|{$stHour}|{$enHour}";
        } elseif ($item->status == 'confirmed' || $item->status == 'paid') {
            $approved[] = "{$start}|{$end}|{$stHour}|{$enHour}";
        } else {
            $rejected[] = "{$start}|{$end}|{$stHour}|{$enHour}";
        }
//        }
    }
    $waitingLength = count($waiting);
    $approvedLength = count($approved);
    $rejectedLength = count($rejected);


    $unavailable = array();
    foreach($unavailableResults as $item){
        $date_startconverted = date($item -> date_start);
        if($_currDate < $date_startconverted ) {
            $unavailable[] =  "{$item -> date_start}|{$item -> date_end}|{$item -> hour_start}|{$item -> hour_end}";
        }
    }
    $unavailableLength = count($unavailable);
    ?>

    var month = new Array();
    month[0] = "Jan";
    month[1] = "Feb";
    month[2] = "Mar";
    month[3] = "Apr";
    month[4] = "Mai";
    month[5] = "Jun";
    month[6] = "Jul";
    month[7] = "Aug";
    month[8] = "Sep";
    month[9] = "Okt";
    month[10] = "Nov";
    month[11] = "Des";

    let waitingLength = '<?php echo $waitingLength;?>';
    let waiting = '<?php echo json_encode($waiting);?>';
    let approvedLength = '<?php echo $approvedLength;?>';
    let approved = '<?php echo json_encode($approved);?>';
    let rejectedLength = '<?php echo $rejectedLength;?>';
    let rejected = '<?php echo json_encode($rejected);?>';
    let unavailableLength = '<?php echo $unavailableLength;?>';
    let unavailable = '<?php echo json_encode($unavailable);?>';
    waiting = waiting.slice(0, -1);
    waiting = waiting.substr(1);
    waiting = waiting.split(",");

    approved = approved.slice(0, -1);
    approved = approved.substr(1);
    approved = approved.split(",");

    rejected = rejected.slice(0, -1);
    rejected = rejected.substr(1);
    rejected = rejected.split(",");

    unavailable = unavailable.slice(0, -1);
    unavailable = unavailable.substr(1);
    unavailable = unavailable.split(",");

    jQuery('.check-availability-table').css('opacity', '0.1');
    var cou = 0;
    var firstinput;
    var secondinput;
    var v = jQuery('.btn1').val();
    var ifSunday = new Date();

    window.setTimeout(function () {
        jQuery('#slot').val(v);
        jQuery('.check-availability-table').css('opacity', '1');
        goToNextWeek = false;
        goToNextWeek2 = false;
        if (ifSunday.getDay() == 0) {
            window.setTimeout(function () {
                jQuery('.nextbtn').click();
                var goToNextWeek = false;
                var goToNextWeek2 = false;
                jQuery('#clockLoading').show();
                jQuery('.check-availability-table').css('opacity', '0.1');
            }, 2000)
        }
        jQuery('#clockLoading').hide();
        jQuery('.check-availability-table').css('opacity', '1');
    }, 3000);

    var goToNextWeek = false;
    var goToNextWeek2 = false;

    var day;
    let dayArray = [];
    var time;
    var availableSlots;
    var timeFrom;
    var timeTo;
    let days = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
    let timeFromAr = [];
    let timeToAr = [];

    <?php foreach( $slots as $day => $day_slots) {
    if (empty($day_slots)) continue;?>
    <?php foreach( $day_slots as $number => $slot) {
    $slot = explode('|', $slot); ?>
    day = "<?php echo $day; ?>";
    dayArray.push(day);
    time = "<?php echo $slot[0]; ?>";
    availableSlots = "<?php echo $slot[1]; esc_html_e(' slots available', 'listeo_core') ?>";
    timeFrom = time.substring(0, 2);
    timeTo = time.substring(time.indexOf("-") + 2);
    timeTo = timeTo.substring(0, timeTo.indexOf(":"));

    var tf = parseInt(timeFrom);
    if (tf < 10) {
        timeFrom = time.substring(1, 2);
    } else {
        timeFrom = time.substring(0, 2);
    }

    var limit = parseInt(timeTo);
    timeFromAr.push(timeFrom);
    if (limit == 0) {
        limit = 23;
    }
    timeToAr.push(limit);

    for (var i = timeFrom; i <= limit; i++) {
        if (jQuery('.check-availability-table .${day}').hasClass(i + days[day])) {
            jQuery('.check-availability-table .${i}${days[day]}').addClass('available');
            jQuery('.check-availability-table .${i}${days[day]}').css('background', 'white');
        }
    }

    <?php } ?>
    <?php } ?>

    var smallest = Math.min.apply(null, timeFromAr);
    var biggest = Math.max.apply(null, timeToAr);

    for (var s = 0; s < smallest; s++) {
        jQuery('.check-availability-table tr.${s}').hide();
    }
    for (var s = biggest + 1; s <= 24; s++) {
        jQuery('.check-availability-table tr.${s}').hide();
    }

    //append last hour
    if (biggest == 24) {
        jQuery('.${biggest-1} .halfright').append('<span class="halfbottom" style="color:black;">24:00</span>');
    } else if (biggest > 9) {
        jQuery('.${biggest} .halfright').append('<span class="halfbottom" style="color:black;">${biggest + 1}:00</span>');

    } else {
        jQuery('.${biggest} .halfright').append('<span class="halfbottom" style="color:black;">0${biggest + 1}:00</span>');
    }
    //css for 2nd last
    if (biggest < 23) {
        jQuery('.${biggest} .halfright .halfright1').css('left', '20.5px');
    }

    let indexesAr = [];
    var as = 0;
    for (var i = smallest; i <= biggest; i++) {
        indexesAr[i] = as;
        as++;
    }

    var dateOver = new Date();
    jQuery('#monthOver').html('${dateOver.getWeek()}');

    // var startDateOver = new Date();
    // startDateOver = startOfWeek(startDateOver);
    // var endDateOver = new Date();
    // endDateOver = endOfWeek(endDateOver);

    // DISPLAY MONTH
    // jQuery('#displayMonth').attr('colspan', '4');
    // jQuery('#displayMonth').html('${startDateOver.getDate()}. ${month[startDateOver.getMonth()]} - ${endDateOver.getDate()}. ${month[endDateOver.getMonth()]}');
    // jQuery('#displayMonth').css("text-align", "center");

    var a = startOfWeek(dateOver);
    for (let z = 0; z < 7; z++) {
        jQuery('#dateOverHours${z}').html('${a.getDate()}');
        jQuery('#dateOverHours${z}').attr('over-date', '${a}');
        a = new Date(a.setDate(a.getDate() + 1));
    }

    var curr = new Date; // get current date
    var asdf = new Date();
    var first = curr.getDate() - curr.getDay() + 1; // First day is the day of the month - the day of the week
    var last = first + 6; // last day is the first day + 6
    var firstday = new Date(curr.setDate(first)).toUTCString();
    var lastday = new Date(curr.setDate(last)).toUTCString();
    var a;
    var additionalSlot;
    var notG;
    for (var g = 0; g < timeFromAr.length; g++) {
        notG = parseInt(dayArray[g]);
        var slot = jQuery('.time-slot:eq(${g})');
        if (notG == 6) {
            asdf = new Date();
        }
        let xxx = new Date(jQuery('#dateOverHours0').attr('over-date'));
        firstday = new Date(xxx.setDate(xxx.getDate() + notG));
        const f2 = "MM/DD/YYYY";
        firstday = moment(firstday).format(f2);
        for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {

            a = slot.clone();
            additionalSlot = slot.clone();
            a.find(".tests").parent().attr("for", '${g}|${i}');
            a.find(".tests").addClass('${i}:00');
            a.find(".tests").html('${i}:00');
            a.find("input").attr("id", '${g}|${i}');
            a.find(".tests").attr("date", '${firstday}');
            a.appendTo('.check-availability-table .${i}${days[notG]}');
        }
    }
    additionalSlot.appendTo('.additionalSlot');
    additionalSlot.find(".tests").addClass('11:00');
    additionalSlot.find(".tests").html('11:00');
    additionalSlot.find('.tests').attr("date", "08/12/2022");

    //Here start new task for slots
    var supportDate = new Date();

    switch (supportDate.getDay()) {
        case 0:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 6));
            break;
        case 1:
            supportDate = new Date(supportDate.setDate(supportDate.getDate()));
            break;
        case 2:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 1));
            break;
        case 3:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 2));
            break;
        case 4:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 3));
            break;
        case 5:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 4));
            break;
        case 6:
            supportDate = new Date(supportDate.setDate(supportDate.getDate() - 5));
            break;
    }

    const f2 = "MM/DD/YYYY";
    formatirana = null;
    let nextDate = new Date();
    var asdf = new Date();

    jQuery('.nextbtn').click(function () {

        jQuery('.reservationTag').remove();

        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');
        setTimeout(function(){
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, 1000);

        switch (nextDate.getDay()) {
            case 0:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 1));
                break;
            case 1:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 7));
                break;
            case 2:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 6));
                break;
            case 3:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 5));
                break;
            case 4:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 4));
                break;
            case 5:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 3));
                break;
            case 6:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() + 2));
                break;
        }

        let selectedYear = nextDate.getFullYear();
        let selectedMonth = nextDate.getMonth();
        jQuery('#getMonth option:eq(${selectedMonth})').prop("selected",true);
        jQuery('#getYear option:contains(${selectedYear})').prop("selected",true);


        jQuery('#monthOver').html('${nextDate.getWeek()}');

        var overHourDate = nextDate;
        for (let z = 0; z < 7; z++) {
            jQuery('#dateOverHours${z}').html('${overHourDate.getDate()}');
            jQuery('#dateOverHours${z}').attr('over-date', '${overHourDate}');
            overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
        }

        let next = new Date(nextDate.setDate(nextDate.getDate() - 1));

        var asdedas = nextDate;
        var curr = nextDate; // get current date
        var a;
        var notG;
        for (var g = 0; g < timeFromAr.length; g++) {
            notG = parseInt(dayArray[g]);
            firstday = new Date(asdedas.setDate(curr.getDate() + notG));
            const f2 = "MM/DD/YYYY";
            firstday = moment(firstday).format(f2);
            for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                var slot1 = jQuery('.${g}.${i}${days[g]} .tests');
                a = slot1;
                a.attr("date", '${firstday}');
            }
            asdedas.setDate(curr.getDate() - notG);
        }


        jQuery('.time-slot label').css('background', 'white');
        jQuery('.time-slot label').css('pointer-events', '');
        jQuery('.time-slot label').removeClass('booked');
        jQuery('table tr td.available').css("background", 'white');
        jQuery('table tr td.available').css("border-bottom-color", '#C0C3C3');

        for (var i = 0; i < waitingLength; i++) {
            var startDate = waiting[i][0];
            var endDate = waiting[i][1];
            var startHour = waiting[i][2];
            var endHour = waiting[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
            }
        }
        for (var i = 0; i < approvedLength; i++) {
            var startDate = approved[i][0];
            var endDate = approved[i][1];
            var startHour = approved[i][2];
            var endHour = approved[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
            }
        }
        // for (var i = 0; i < rejectedLength; i++) {
        //     var startDate = rejected[i][0];
        //     var endDate = rejected[i][1];
        //     var startHour = rejected[i][2];
        //     var endHour = rejected[i][3];
        //     if (startDate === endDate) {
        //         paintOneRow(startDate, startHour, endHour, color = '#FFFFFF', false);
        //     } else if (startDate < endDate) {
        //         paintMoreRows(startDate, endDate, startHour, endHour, color = '#FFFFFF', false);
        //     }
        // }
        for (var i = 0; i < unavailableLength; i++) {
            var startDate = unavailable[i][0];
            var endDate = unavailable[i][1];
            var startHour = unavailable[i][2];
            var endHour = unavailable[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            }
        }

        //DISPLAY MONTHS
        var _nextDate = nextDate;
        var _startDateOver = startOfWeek(_nextDate);
        var _endDateOver = endOfWeek(_nextDate);

        jQuery('#displayMonth').attr('colspan', '4');
        jQuery('#displayMonth').html('${_startDateOver.getDate()}. ${month[_startDateOver.getMonth()]} - ${_endDateOver.getDate()}. ${month[_endDateOver.getMonth()]}');
        jQuery('#displayMonth').css("text-align", "center");
        //END

        //White after bgColor green
        jQuery('.available .time-slot label').each(function () {
            var a = jQuery(this).css("background-color");
            if (a == 'rgb(0, 132, 116)') {
                jQuery(this).parent().parent().css('background', 'white');
            }
        });

        setTimeout(() => {
            jQuery('.available .time-slot label').each(function () {
                var a = jQuery(this).css("background-color");
                if (a == 'rgb(255, 153, 0)') {
                    jQuery(this).parent().parent().css('background', '#FF9900');
                }
                if (a == 'rgb(218, 105, 122)') {
                    jQuery(this).parent().parent().css('background', '#da697a');
                }
            });
        }, 200);
        jQuery(".check-availability-table tr td").css("border", "1px solid black");

    });

    jQuery('.previousbtn').click(function () {
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');

        jQuery('.reservationTag').remove();

        setTimeout(function(){
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, 1000);
        switch (nextDate.getDay()) {
            case 0:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 7));
                break;
            case 1:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 1));
                break;
            case 2:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 2));
                break;
            case 3:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 3));
                break;
            case 4:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 4));
                break;
            case 5:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 5));
                break;
            case 6:
                nextDate = new Date(nextDate.setDate(nextDate.getDate() - 6));
                break;
        }

        let selectedYear = nextDate.getFullYear();
        let selectedMonth = nextDate.getMonth();
        jQuery('#getMonth option:eq(${selectedMonth})').prop("selected",true);
        jQuery('#getYear option:contains(${selectedYear})').prop("selected",true);

        jQuery('#monthOver').html('${nextDate.getWeek()}');

        let overHourDate = nextDate;
        for (let z = 6; z > -1; z--) {
            jQuery('#dateOverHours${z}').html('${overHourDate.getDate()}');
            overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() - 1));
        }

        let next = new Date(nextDate.setDate(nextDate.getDate() + 1));

        var asdedas = nextDate;
        var curr = nextDate; // get current date
        startOfWeek(nextDate);
        var a;
        var notG;
        for (var g = 0; g < timeFromAr.length; g++) {
            notG = parseInt(dayArray[g]);
            firstday = new Date(asdedas.setDate(curr.getDate() + notG));
            const f2 = "MM/DD/YYYY";
            firstday = moment(firstday).format(f2);
            for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                var slot1 = jQuery('.${g}.${i}${days[g]} .tests');
                a = slot1;
                a.attr("date", '${firstday}');
            }
            asdedas.setDate(curr.getDate() - notG);
        }

        jQuery('.time-slot label').css('background', 'white');
        jQuery('.time-slot label').css('pointer-events', '');
        jQuery('.time-slot label').removeClass('booked');
        for (var i = 0; i < waitingLength; i++) {

            var startDate = waiting[i][0];
            var endDate = waiting[i][1];
            var startHour = waiting[i][2];
            var endHour = waiting[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
            }
        }
        for (var i = 0; i < approvedLength; i++) {

            var startDate = approved[i][0];
            var endDate = approved[i][1];
            var startHour = approved[i][2];
            var endHour = approved[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
            }
        }
        // for (var i = 0; i < rejectedLength; i++) {
        //
        //     var startDate = rejected[i][0];
        //     var endDate = rejected[i][1];
        //     var startHour = rejected[i][2];
        //     var endHour = rejected[i][3];
        //     if (startDate === endDate) {
        //         paintOneRow(startDate, startHour, endHour, color = 'white', false);
        //     } else if (startDate < endDate) {
        //         paintMoreRows(startDate, endDate, startHour, endHour, color = 'white', false);
        //     }
        // }
        for (var i = 0; i < unavailableLength; i++) {
            var startDate = unavailable[i][0];
            var endDate = unavailable[i][1];
            var startHour = unavailable[i][2];
            var endHour = unavailable[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            }
        }

        var currentDate = new Date();
        jQuery('.available .time-slot label').each(function () {
            var a = jQuery(this).css("background-color");
            if (a == 'rgb(0, 132, 116)') {
                jQuery(this).parent().parent().css('background', 'white');
            }
        });

        //OVER MONTHS
        var _overDate = nextDate;
        var __startDateOver = startOfWeek(_overDate);
        var __endDateOver = endOfWeek(_overDate);

        jQuery('#displayMonth').attr('colspan', '4');
        jQuery('#displayMonth').html('${__startDateOver.getDate()}. ${month[__startDateOver.getMonth()]} - ${__endDateOver.getDate()}. ${month[__endDateOver.getMonth()]}');
        jQuery('#displayMonth').css("text-align", "center");
        //END

        setTimeout(() => {
            jQuery('.available .time-slot label').each(function () {
                var a = jQuery(this).css("background-color");
                var b = jQuery(this).parent().parent().css("background-color");

                if (a == 'rgb(255, 153, 0)') {
                    jQuery(this).parent().parent().css('background', '#FF9900');
                }
                if (a == 'rgb(218, 105, 122)') {
                    jQuery(this).parent().parent().css('background', '#da697a');
                }
                if (a == 'rgb(255, 255, 255)' && b == 'rgb(255, 153, 0)') {
                    jQuery(this).parent().parent().css('background', 'white');
                }
                if (a == 'rgb(255, 255, 255)' && b == 'rgb(218, 105, 122)') {
                    jQuery(this).parent().parent().css('background', 'white');
                }
            })
        }, 300);
        jQuery(".check-availability-table tr td").css("border", "1px solid black");



    });

    jQuery(".check-availability-table tr td").css("border", "1px solid black");

    jQuery('.check-availability-table tbody td label').css('background-color','rgb(255,255,255)');

    for (var i = 0; i < waitingLength; i++) {
        waiting[i] = waiting[i].slice(0, -1);
        waiting[i] = waiting[i].substr(1);
        waiting[i] = waiting[i].split("|");
        var startDate = waiting[i][0];
        var endDate = waiting[i][1];
        var startHour = waiting[i][2];
        var endHour = waiting[i][3];
        if (startDate === endDate) {
            paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
        } else if (startDate < endDate) {
            paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
        }
    }
    for (var i = 0; i < approvedLength; i++) {
        approved[i] = approved[i].slice(0, -1);
        approved[i] = approved[i].substr(1);
        approved[i] = approved[i].split("|");
        var startDate = approved[i][0];
        var endDate = approved[i][1];
        var startHour = approved[i][2];
        var endHour = approved[i][3];
        if (startDate === endDate) {
            paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
        } else if (startDate < endDate) {
            paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
        }
    }
    // for (var i = 0; i < rejectedLength; i++) {
    //     rejected[i] = rejected[i].slice(0, -1);
    //     rejected[i] = rejected[i].substr(1);
    //     rejected[i] = rejected[i].split("|");
    //     var startDate = rejected[i][0];
    //     var endDate = rejected[i][1];
    //     var startHour = rejected[i][2];
    //     var endHour = rejected[i][3];
    //     if (startDate === endDate) {
    //         paintOneRow(startDate, startHour, endHour, color = 'white', false);
    //     } else if (startDate < endDate) {
    //         paintMoreRows(startDate, endDate, startHour, endHour, color = 'white', false);
    //     }
    // }
    for (var i = 0; i < unavailableLength; i++) {
        unavailable[i] = unavailable[i].slice(0, -1);
        unavailable[i] = unavailable[i].substr(1);
        unavailable[i] = unavailable[i].split("|");
        var startDate = unavailable[i][0];
        var endDate = unavailable[i][1];
        var startHour = unavailable[i][2];
        var endHour = unavailable[i][3];
        if (startDate === endDate) {
            paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
        } else if (startDate < endDate) {
            paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
        }
    }

    function paintOneRow(startDate, startHour, endHour, color, clickable) {
        let nextHour = parseInt(startHour);
        startHour = parseInt(startHour);
        endHour = parseInt(endHour);
        let firstTd = true;
        let numberOfReservations = 0;
        if (startHour < endHour) {
            while (nextHour < endHour) {
                firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
                if (firstPropDay.getDay() == 0) {
                    jQuery('.6 .tests').filter(function () {
                        if (jQuery(this).attr('date') == startDate) {
                            if (jQuery(this).text() == '${nextHour}:00') {
                                jQuery(this).parent().css("background", '${color}');
                                jQuery(this).parent().parent().parent().css("background", '${color}');
                                if (clickable) {
                                    jQuery(this).parent().addClass('booked');
                                }
                                if(color != 'rgb(155, 161, 163)'){
                                    if(firstTd){
                                        if(!jQuery(this).parent().find('span').hasClass('reservationTag')){
                                            jQuery(this).parent().append('<span class='reservationTag' style='color: black;font-size: x-large;font-weight: 900;'>Reservation 1</span>');
                                            jQuery(this).parent().css('padding','inherit');
                                        }else {
                                            numberOfReservations = parseInt(jQuery(this).parent().find('.reservationTag').text().split(" ")[1]) + 1;
                                            jQuery(this).parent().find('.reservationTag').text(numberOfReservations);
                                        }
                                        firstTd = false;
                                    }
                                }
                            }
                        }
                    });
                } else {
                    jQuery('.${firstPropDay.getDay() - 1} .tests').filter(function () {
                        if (jQuery(this).attr('date') == startDate) {
                            if (jQuery(this).text() == '${nextHour}:00') {
                                jQuery(this).parent().css("background", '${color}');
                                jQuery(this).parent().parent().parent().css("background", '${color}');
                                if (clickable) {
                                    jQuery(this).parent().addClass('booked');
                                }
                                if(color != 'rgb(155, 161, 163)') {
                                    if (firstTd) {
                                        if (!jQuery(this).parent().find('span').hasClass('reservationTag')) {
                                            jQuery(this).parent().append('<span class='reservationTag' style='color: black;font-size: large;font-weight: 900;'>Reservation 1</span>');
                                            jQuery(this).parent().css('padding', 'inherit');
                                        } else {
                                            numberOfReservations = parseInt(jQuery(this).parent().find('.reservationTag').text().split(" ")[1]) + 1;
                                            jQuery(this).parent().find('.reservationTag').text("Reservations "+numberOfReservations);
                                        }
                                        firstTd = false;
                                    }
                                }
                            }
                        }
                    });
                }
                nextHour++;
            }
        } else if (startHour == endHour) {
            firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
            if (firstPropDay.getDay() == 0) {
                jQuery('.6 .tests').filter(function () {
                    if (jQuery(this).attr('date') == startDate) {
                        if (jQuery(this).text() == '${startHour}:00') {
                            jQuery(this).parent().css("background", '${color}');
                            jQuery(this).parent().parent().parent().css("background", '${color}');
                            if (clickable) {
                                jQuery(this).parent().addClass('booked');
                            }
                            if(color != 'rgb(155, 161, 163)') {
                                if (firstTd) {
                                    if (!jQuery(this).parent().find('span').hasClass('reservationTag')) {
                                        jQuery(this).parent().append('<span class='reservationTag' style='color: black;font-size: large;font-weight: 900;'>Reservation 1</span>');
                                        jQuery(this).parent().css('padding', 'inherit');
                                    } else {
                                        numberOfReservations = parseInt(jQuery(this).parent().find('.reservationTag').text().split(" ")[1]) + 1;
                                        jQuery(this).parent().find('.reservationTag').text("Reservations "+numberOfReservations);
                                    }
                                    firstTd = false;
                                }
                            }
                        }
                    }
                });
            } else {
                jQuery('.${firstPropDay.getDay() - 1} .tests').filter(function () {
                    if (jQuery(this).attr('date') == startDate) {
                        if (jQuery(this).text() == '${startHour}:00') {
                            jQuery(this).parent().css("background", '${color}');
                            jQuery(this).parent().parent().parent().css("background", '${color}');
                            if (clickable) {
                                jQuery(this).parent().addClass('booked');
                            }
                            if(color != 'rgb(155, 161, 163)') {
                                if (firstTd) {
                                    if (!jQuery(this).parent().find('span').hasClass('reservationTag')) {
                                        jQuery(this).parent().append('<span class='reservationTag' style='color: black;font-size: large;font-weight: 900;'>Reservation 1</span>');
                                        jQuery(this).parent().css('padding', 'inherit');
                                    } else {
                                        numberOfReservations = parseInt(jQuery(this).parent().find('.reservationTag').text().split(" ")[1]) + 1;
                                        jQuery(this).parent().find('.reservationTag').text("Reservations "+numberOfReservations);
                                    }
                                    firstTd = false;
                                }
                            }
                        }
                    }
                });
            }
        }


    }
    function paintMoreRows(startDate, endDate, startHour, endHour, color, clickable) {
        let nextHour = parseInt(startHour);
        startHour = parseInt(startHour);
        endHour = parseInt(endHour);
        let firstTd = true;
        let numberOfReservations;
        var firstPropDay = new Date(moment(startDate).format('MM/DD/YYYY'));
        var secondPropDay = new Date(moment(endDate).format('MM/DD/YYYY'));

        var _stdt = firstPropDay.getWeek();
        var _endt = secondPropDay.getWeek();


        while (nextHour < 24) {
            jQuery('.${firstPropDay.getDay() - 1} .tests').filter(function () {
                if (jQuery(this).attr('date') == startDate) {
                    if (jQuery(this).text() == '${nextHour}:00') {
                        jQuery(this).parent().css("background", '${color}');
                        jQuery(this).parent().parent().parent().css("background", '${color}');
                        if (clickable) {
                            jQuery(this).parent().addClass('booked');
                        }
                        if(color != 'rgb(155, 161, 163)') {
                            if (firstTd) {
                                if (!jQuery(this).parent().find('span').hasClass('reservationTag')) {
                                    jQuery(this).parent().append('<span class='reservationTag' style='color: black;font-size: large;font-weight: 900;'>Reservation 1</span>');
                                    jQuery(this).parent().css('padding', 'inherit');
                                } else {
                                    numberOfReservations = parseInt(jQuery(this).parent().find('.reservationTag').text().split(" ")[1]) + 1;
                                    jQuery(this).parent().find('.reservationTag').text("Reservations "+numberOfReservations);
                                }
                                firstTd = false;
                            }
                        }
                    }
                }
            });
            nextHour++;
        }

        let preEndDay;
        if (secondPropDay.getDay() == 0) {
            preEndDay = 6;
        } else {
            preEndDay = secondPropDay.getDay() - 1;
        }

        if (_stdt == _endt) {
            let preEndDay;
            if (secondPropDay.getDay() == 0) {
                preEndDay = 6;
            } else {
                preEndDay = secondPropDay.getDay() - 1;
            }

            let fpd = firstPropDay;

            for (let y = firstPropDay.getDay(); y < preEndDay; y++) {
                let dddd = new Date();
                dddd = moment(fpd).format('MM/DD/YYYY');
                let pom = 0;

                while (pom < 24) {
                    jQuery('.${y}.${pom}${days[y]} .tests').filter(function () {
                        let thiss = jQuery(this).attr('date');
                        var increased = new Date(dddd);
                        increased.setDate(increased.getDate() + 1);
                        increased = moment(increased).format('MM/DD/YYYY');

                        if (jQuery(this).attr('date') == increased) {
                            if (jQuery(this).text() === '${pom}:00') {
                                jQuery(this).parent().css("background", '${color}');
                                jQuery(this).parent().parent().parent().css("background", '${color}');
                                if (clickable) {
                                    jQuery(this).parent().addClass('booked');
                                }
                            }
                        }
                    });
                    pom++;
                }
                fpd = new Date(fpd.setDate(fpd.getDate() + 1));
            }
        } else {
            let preEndDay;
            if (secondPropDay.getDay() == 0) {
                preEndDay = 6;
            } else {
                preEndDay = secondPropDay.getDay() - 1;
            }

            let fpd = firstPropDay;
            for (let y = firstPropDay.getDay(); y < 7; y++) {
                let dddd = new Date();
                dddd = moment(fpd).format('MM/DD/YYYY');
                let pom = 0;

                while (pom < 24) {
                    jQuery('.${y}.${pom}${days[y]} .tests').filter(function () {
                        let thiss = jQuery(this).attr('date');
                        var increased = new Date(dddd);
                        increased.setDate(increased.getDate() + 1);
                        increased = moment(increased).format('MM/DD/YYYY');

                        if (jQuery(this).attr('date') == increased) {
                            if (jQuery(this).text() === '${pom}:00') {
                                jQuery(this).parent().css("background", '${color}');
                                jQuery(this).parent().parent().parent().css("background", '${color}');
                                if (clickable) {
                                    jQuery(this).parent().addClass('booked');
                                }
                            }
                        }
                    });
                    pom++;
                }
                fpd = new Date(fpd.setDate(fpd.getDate() + 1));
            }

            for (let y = 0; y < preEndDay; y++) {
                let dddd = new Date();
                dddd = moment(fpd).format('MM/DD/YYYY');
                let pom = 0;
                while (pom < 24) {
                    jQuery('.${y}.${pom}${days[y]} .tests').filter(function () {
                        let thiss = jQuery(this).attr('date');
                        var increased = new Date(dddd);
                        increased.setDate(increased.getDate() + 1);
                        increased = moment(increased).format('MM/DD/YYYY');

                        if (jQuery(this).attr('date') == increased) {
                            if (jQuery(this).text() === '${pom}:00') {
                                jQuery(this).parent().css("background", '${color}');
                                jQuery(this).parent().parent().parent().css("background", '${color}');
                                if (clickable) {
                                    jQuery(this).parent().addClass('booked');
                                }
                            }
                        }
                    });
                    pom++;
                }
                fpd = new Date(fpd.setDate(fpd.getDate() + 1));
            }
        }

        nextHour = 0;
        while (nextHour < endHour - 1) {
            nextHour++;
            jQuery('.${preEndDay} .tests').filter(function () {
                if (jQuery(this).attr('date') == endDate) {
                    if (jQuery(this).text() == '${nextHour}:00') {
                        jQuery(this).parent().css("background", '${color}');
                        jQuery(this).parent().parent().parent().css("background", '${color}');
                        if (clickable) {
                            jQuery(this).parent().addClass('booked');
                        }
                    }
                }
            });
        }
    }
    jQuery("td").each(function () {
        if (jQuery(this).hasClass('available') == false) {
            jQuery(this).css('background', '#9BA1A3');
        }
    });

</script>

<script>
    jQuery('.listOfReservationCloseBtn').on('click',function (){
        jQuery(this).parent().parent().css('visibility','hidden');
    });

    jQuery('.closePopupNewOffer').on('click',function (){
        jQuery('#singleNewOffer').hide();
        jQuery('#unavailabilityPopup').hide();
    });

    jQuery(document).on('click','#sendUnavailability',function (){
        let listingId = <?php echo $post_info->ID ?>;
        let dateStart = jQuery('#startDateUnavailability').val().split("T")[0];
        let dateEnd = jQuery('#endDateUnavailability').val().split("T")[0];
        let hourStart = jQuery('#startDateUnavailability').val().split("T")[1];
        hourStart = hourStart.substring(0,hourStart.indexOf(":"));
        let hourEnd = jQuery('#endDateUnavailability').val().split("T")[1];
        hourEnd = hourEnd.substring(0,hourEnd.indexOf(":"));
        ajax_data = {
            'action': 'send_unavailability',
            'listingId': listingId,
            'dateStart': dateStart,
            'dateEnd': dateEnd,
            'hourStart': hourStart,
            'hourEnd': hourEnd
        };
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');

        jQuery.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: ajax_data,

            success: function (data) {
                jQuery('#unavailabilityPopup').hide();
                jQuery('#clockLoading').hide();
                jQuery('.check-availability-table').css('opacity', '1');
                location.reload();
            }
        });
    })

    jQuery('.check-availability-table tbody td label').on('click',function(){

        if (jQuery(this).parent().parent().css('background-color') == 'rgb(255, 255, 255)'){
            jQuery(this).css('background-color','white');
        }

        if (jQuery(this).css('background-color') != 'rgb(255, 255, 255)' && jQuery(this).css('background-color') != 'rgba(0, 132, 116, 0.08)' && jQuery(this).css('background-color') != 'rgb(155, 161, 163)') {
            jQuery('#listOfReservations').empty();
            let listingId = <?php echo $post_info->ID; ?>;
            let date = jQuery(this).find('.tests').attr('date');
            let hour = jQuery(this).find('.tests').text();

            ajax_data = {
                'action': 'check_reservation_availability',
                'listingId': listingId,
                'date': date,
                'hour': hour
            };
            jQuery('#clockLoading').show();
            jQuery('.check-availability-table').css('opacity', '0.1');

            jQuery.ajax({
                type: 'POST', dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function (data) {

                    let reservations = data.reservations;

                    reservations.forEach(element => {
                        let option = '<li class="clickLiTag" style="padding:0;width: 500px;height: 60px;"><div id="bookingId" class="col-lg-3">#${element.ID}</div><div class="col-lg-3">${element.date_start}</div><div class="col-lg-3">${element.date_end}</div><div class="col-lg-3">${element.bookings_author}</div></li>';
                        jQuery('#listOfReservations').append(option);
                    })

                    if(jQuery('#listOfReservations li').length > 1){
                        jQuery('#reservationPopup').css('visibility', 'visible');
                    }else{
                        jQuery('#listOfReservations li').click();
                    }
                    jQuery('#clockLoading').hide();
                    jQuery('.check-availability-table').css('opacity', '1');
                }

            });
        }
        else if (jQuery(this).css('background-color') === 'rgb(155, 161, 163)'){
            if (window.confirm(listeo_core.areyousure)) {
                let listingId = <?php echo $post_info->ID; ?>;
                let date = jQuery(this).find('.tests').attr('date');
                let hour = jQuery(this).find('.tests').text();
                // preparing data for ajax
                var ajax_data = {
                    'action': 'check_reservation_unavailability',
                    'listingId': listingId,
                    'date': date,
                    'hour': hour
                };
                jQuery.ajax({
                    type: 'POST', dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data){
                        // display loader class

                        jQuery('#singleReservationPopup').css('visibility','hidden');
                    }
                });
            }
        }else {
            jQuery('#unavailabilityPopup').show();
            let date = jQuery(this).find('.tests').attr('date');
            let hour = jQuery(this).find('.tests').text();
            hour = hour.substring(0,hour.indexOf(':'));
            hour = (parseInt(hour) < 10) ? "0"+hour : hour;
            date = moment(date).format('YYYY-MM-DD');
            jQuery('#startDateUnavailability').val('${date}T${hour}:00');
        }
    });

    jQuery('#reservationPopup ul#listOfReservations').on('click','li',function (){

        jQuery('#reservationPopup').css('visibility','hidden');

        let bookingId = jQuery(this).find('#bookingId').text();
        bookingId = bookingId.substring(1);
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');
        ajax_data = {
            'action': 'get_html_single_reservation',
            'bookingId': bookingId
        };

        jQuery.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: ajax_data,

            success: function (data) {
                jQuery('#singleReservation').empty();
                let x = data.data;
                jQuery('#singleReservation').append(x);
                jQuery('#singleReservationPopup').css('visibility','visible');
                jQuery('#clockLoading').hide();
                jQuery('.check-availability-table').css('opacity', '1');
            }

        });

    });

    jQuery(document).on('click','.giveOffer',function(){
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');
        jQuery('#singleReservationPopup').css('visibility','hidden');
        let msg = jQuery('.singleMessageText p').text();
        let price = jQuery('#priceHiddenInput').val();
        let start = moment(jQuery('#startDateHidden').val()).format("YYYY-MM-DDTHH:mm");
        let end = moment(jQuery('#endDateHidden').val()).format("YYYY-MM-DDTHH:mm");
        jQuery('._message1').val(msg);
        jQuery('._price1').val(price);
        jQuery('#_startDate').val(start);
        jQuery('#_endDate').val(end);
        jQuery('#singleNewOffer').show();
        setTimeout(function(){
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, 1000);
    });
</script>

<script>
    jQuery(document).on('click', '.booking-message', function(e) {
        jQuery('#singleReservationPopup').css('visibility','hidden');
        jQuery('#small-dialog').show();
    });

    jQuery('.singleOffer').on('click', function(){

        let bookingId = jQuery('#bookingId').text();
        bookingId = bookingId.substring(1);
        let startDate = jQuery('#_startDate').val();
        let endDate = jQuery('#_endDate').val();
        let msg = jQuery('._message1').val();
        let price = jQuery('._price1').val();
        let comment = {"message":'${msg}',"billing_address_1":'true'};
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');

        ajax_data = {
            'action': 'check_availability_new_offer',
            'start_date': startDate,
            'end_date': endDate,
            'data_id': bookingId,
            'comment' : comment,
            'price' : price
        };
        jQuery.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: ajax_data,

            success: function(data){
                jQuery('#singleNewOffer').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                jQuery('#singleNewOffer').hide();
                jQuery('#clockLoading').hide();
                jQuery('.check-availability-table').css('opacity', '1');
            }

        });

    });

    jQuery(document).on('click','.reject, .cancel',function(e) {
        e.preventDefault();
        if (window.confirm(listeo_core.areyousure)) {
            var $this = jQuery(this);
            $this.parents('li').addClass('loading');
            var status = 'confirmed';
            if ( jQuery(this).hasClass('reject' ) ) status = 'cancelled';
            if ( jQuery(this).hasClass('cancel' ) ) status = 'cancelled';
            jQuery('#clockLoading').show();
            jQuery('.check-availability-table').css('opacity', '0.1');

            // preparing data for ajax
            var ajax_data = {
                'action': 'listeo_bookings_manage',
                'booking_id' : jQuery(this).data('booking_id'),
                'status' : status,
                //'nonce': nonce
            };
            jQuery.ajax({
                type: 'POST', dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data){
                    // display loader class
                    $this.parents('li').removeClass('loading');
                    jQuery('#singleReservationPopup').css('visibility','hidden');
                    jQuery('#clockLoading').hide();
                    jQuery('.check-availability-table').css('opacity', '1');
                    location.reload();
                }
            });
        }
    });

    jQuery(document).on('click','.delete',function(e) {
        e.preventDefault();
        if (window.confirm(listeo_core.areyousure)) {
            var $this = jQuery(this);
            $this.parents('li').addClass('loading');
            var status = 'deleted';
            jQuery('#clockLoading').show();
            jQuery('.check-availability-table').css('opacity', '0.1');

            // preparing data for ajax
            var ajax_data = {
                'action': 'listeo_bookings_manage',
                'booking_id' : jQuery(this).data('booking_id'),
                'status' : status,
                //'nonce': nonce
            };
            jQuery.ajax({
                type: 'POST', dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data){
                    // display loader class
                    $this.parents('li').removeClass('loading');
                    jQuery('#singleReservationPopup').css('visibility','hidden');
                    jQuery('#clockLoading').hide();
                    jQuery('.check-availability-table').css('opacity', '1');
                    location.reload();
                }
            });
        }
    });

    jQuery(document).on('click','.approve',function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        $this.parents('li').addClass('loading');
        var status = 'confirmed';
        if ( jQuery(this).hasClass('reject' ) ) status = 'cancelled';
        if ( jQuery(this).hasClass('cancel' ) ) status = 'cancelled';
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');


        // preparing data for ajax
        var ajax_data = {
            'action': 'listeo_bookings_manage',
            'booking_id' : jQuery(this).data('booking_id'),
            'status' : status,
            //'nonce': nonce
        };
        jQuery.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: ajax_data,

            success: function(data){
                // display loader class
                $this.parents('li').removeClass('loading');
                jQuery('#singleReservationPopup').css('visibility','hidden');
                jQuery('#clockLoading').hide();
                jQuery('.check-availability-table').css('opacity', '1');
                location.reload();
            }
        });

    });

    jQuery(document).on('click','.mark-as-paid',function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        $this.parents('li').addClass('loading');
        var status = 'paid';
        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');


        // preparing data for ajax
        var ajax_data = {
            'action': 'listeo_bookings_manage',
            'booking_id' : jQuery(this).data('booking_id'),
            'status' : status,
            //'nonce': nonce
        };
        jQuery.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: ajax_data,

            success: function(data){
                // display loader class
                $this.parents('li').removeClass('loading');
                jQuery('#singleReservationPopup').css('visibility','hidden');
                jQuery('#clockLoading').hide();
                jQuery('.check-availability-table').css('opacity', '1');
                location.reload();
            }
        });

    });


</script>

<script type='text/javascript'>
    // Store the current highest year
    let highestYear = new Date().getFullYear();
    // Add the current year to the list
    jQuery('#getYear').append('<option>' + highestYear + '</option>');
    // Increment the years and add them to the list
    for(var i = 1; i <= 10; i++){
        // Append the values (and increment the current highest year)
        jQuery('#getYear').append('<option>' + (++highestYear) + '</option>');
    }

    let selectMonth = new Date().getMonth();
    jQuery('#getMonth option:eq(${selectMonth})').prop("selected","selected");
    // for (let i = 0; i < selectMonth; i++) {
    //     jQuery('#getMonth option:eq(${i})').hide();
    // }


    jQuery('#getYear').on('change', function (){

        jQuery('.reservationTag').remove();

        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');

        setTimeout(function(){
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, 1000);
        let firstDay = parseInt(jQuery('#dateOverHours0').text());
        let selectedYear = parseInt(jQuery('#getYear option:selected').text());
        let selectedMonth = jQuery('#getMonth option:selected').val();
        nextDate = new Date(selectedYear, selectedMonth-1, firstDay);
        nextDate = startOfWeek(nextDate);
        selectedMonth = nextDate.getMonth();
        jQuery('#getMonth option:eq(${selectedMonth})').prop("selected","selected");
        jQuery('#monthOver').html('${nextDate.getWeek()}');

        var overHourDate = nextDate;
        for (let z = 0; z < 7; z++) {
            jQuery('#dateOverHours${z}').html('${overHourDate.getDate()}');
            jQuery('#dateOverHours${z}').attr('over-date', '${overHourDate}');
            overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
        }

        let next = new Date(nextDate.setDate(nextDate.getDate() - 1));

        var asdedas = nextDate;
        var curr = nextDate; // get current date
        var a;
        var notG;
        for (var g = 0; g < timeFromAr.length; g++) {
            notG = parseInt(dayArray[g]);
            firstday = new Date(asdedas.setDate(curr.getDate() + notG));
            const f2 = "MM/DD/YYYY";
            firstday = moment(firstday).format(f2);
            for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                var slot1 = jQuery('.${g}.${i}${days[g]} .tests');
                a = slot1;
                a.attr("date", '${firstday}');
            }
            asdedas.setDate(curr.getDate() - notG);
        }

        jQuery('.time-slot label').css('background', 'white');
        jQuery('.time-slot label').css('pointer-events', '');
        jQuery('.time-slot label').removeClass('booked');
        jQuery('table tr td.available').css("background", 'white');
        jQuery('table tr td.available').css("border-bottom-color", '#C0C3C3');

        for (var i = 0; i < waitingLength; i++) {
            var startDate = waiting[i][0];
            var endDate = waiting[i][1];
            var startHour = waiting[i][2];
            var endHour = waiting[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
            }
        }
        for (var i = 0; i < approvedLength; i++) {
            var startDate = approved[i][0];
            var endDate = approved[i][1];
            var startHour = approved[i][2];
            var endHour = approved[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
            }
        }
        // for (var i = 0; i < rejectedLength; i++) {
        //     var startDate = rejected[i][0];
        //     var endDate = rejected[i][1];
        //     var startHour = rejected[i][2];
        //     var endHour = rejected[i][3];
        //     if (startDate === endDate) {
        //         paintOneRow(startDate, startHour, endHour, color = '#FFFFFF', false);
        //     } else if (startDate < endDate) {
        //         paintMoreRows(startDate, endDate, startHour, endHour, color = '#FFFFFF', false);
        //     }
        // }
        for (var i = 0; i < unavailableLength; i++) {
            var startDate = unavailable[i][0];
            var endDate = unavailable[i][1];
            var startHour = unavailable[i][2];
            var endHour = unavailable[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            }
        }

        //White after bgColor green
        jQuery('.available .time-slot label').each(function () {
            var a = jQuery(this).css("background-color");
            if (a == 'rgb(0, 132, 116)') {
                jQuery(this).parent().parent().css('background', 'white');
            }
        });

        setTimeout(() => {
            jQuery('.available .time-slot label').each(function () {
                var a = jQuery(this).css("background-color");
                if (a == 'rgb(255, 153, 0)') {
                    jQuery(this).parent().parent().css('background', '#FF9900');
                }
                if (a == 'rgb(218, 105, 122)') {
                    jQuery(this).parent().parent().css('background', '#da697a');
                }
            });
        }, 500);
        jQuery(".check-availability-table tr td").css("border", "1px solid black");
    });

    jQuery('#getMonth').on('change', function (){
        jQuery('.reservationTag').remove();

        jQuery('#clockLoading').show();
        jQuery('.check-availability-table').css('opacity', '0.1');
        setTimeout(function(){
            jQuery('#clockLoading').hide();
            jQuery('.check-availability-table').css('opacity', '1');
        }, 1000);
        let firstDay = parseInt(jQuery('#dateOverHours0').text());
        let selectedYear = parseInt(jQuery('#getYear option:selected').text());
        let selectedMonth = jQuery('#getMonth option:selected').val();
        nextDate = new Date(selectedYear, selectedMonth-1, firstDay);
        nextDate = startOfWeek(nextDate);
        selectedMonth = nextDate.getMonth();
        jQuery('#getMonth option:eq(${selectedMonth})').prop("selected","selected");
        jQuery('#monthOver').html('${nextDate.getWeek()}');

        var overHourDate = nextDate;
        for (let z = 0; z < 7; z++) {
            jQuery('#dateOverHours${z}').html('${overHourDate.getDate()}');
            jQuery('#dateOverHours${z}').attr('over-date', '${overHourDate}');
            overHourDate = new Date(overHourDate.setDate(overHourDate.getDate() + 1));
        }

        let next = new Date(nextDate.setDate(nextDate.getDate() - 1));
        var asdedas = nextDate;
        var curr = nextDate; // get current date
        var a;
        var notG;
        for (var g = 0; g < timeFromAr.length; g++) {
            notG = parseInt(dayArray[g]);
            firstday = new Date(asdedas.setDate(curr.getDate() + notG));
            const f2 = "MM/DD/YYYY";
            firstday = moment(firstday).format(f2);
            for (var i = timeFromAr[g]; i <= timeToAr[g]; i++) {
                var slot1 = jQuery('.${g}.${i}${days[g]} .tests');
                a = slot1;
                a.attr("date", '${firstday}');
            }
            asdedas.setDate(curr.getDate() - notG);
        }

        jQuery('.time-slot label').css('background', 'white');
        jQuery('.time-slot label').css('pointer-events', '');
        jQuery('.time-slot label').removeClass('booked');
        jQuery('table tr td.available').css("background", 'white');
        jQuery('table tr td.available').css("border-bottom-color", '#C0C3C3');

        for (var i = 0; i < waitingLength; i++) {
            var startDate = waiting[i][0];
            var endDate = waiting[i][1];
            var startHour = waiting[i][2];
            var endHour = waiting[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#FF9900', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#FF9900', false);
            }
        }
        for (var i = 0; i < approvedLength; i++) {
            var startDate = approved[i][0];
            var endDate = approved[i][1];
            var startHour = approved[i][2];
            var endHour = approved[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = '#DA697A', true);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = '#DA697A', true);
            }
        }
        // for (var i = 0; i < rejectedLength; i++) {
        //     var startDate = rejected[i][0];
        //     var endDate = rejected[i][1];
        //     var startHour = rejected[i][2];
        //     var endHour = rejected[i][3];
        //     if (startDate === endDate) {
        //         paintOneRow(startDate, startHour, endHour, color = '#FFFFFF', false);
        //     } else if (startDate < endDate) {
        //         paintMoreRows(startDate, endDate, startHour, endHour, color = '#FFFFFF', false);
        //     }
        // }
        for (var i = 0; i < unavailableLength; i++) {
            var startDate = unavailable[i][0];
            var endDate = unavailable[i][1];
            var startHour = unavailable[i][2];
            var endHour = unavailable[i][3];
            if (startDate === endDate) {
                paintOneRow(startDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            } else if (startDate < endDate) {
                paintMoreRows(startDate, endDate, startHour, endHour, color = 'rgb(155, 161, 163)', false);
            }
        }

        var currentDate = new Date();

        //White after bgColor green
        jQuery('.available .time-slot label').each(function () {
            var a = jQuery(this).css("background-color");
            if (a == 'rgb(0, 132, 116)') {
                jQuery(this).parent().parent().css('background', 'white');
            }
        });

        setTimeout(() => {
            jQuery('.available .time-slot label').each(function () {
                var a = jQuery(this).css("background-color");
                if (a == 'rgb(255, 153, 0)') {
                    jQuery(this).parent().parent().css('background', '#FF9900');
                }
                if (a == 'rgb(218, 105, 122)') {
                    jQuery(this).parent().parent().css('background', '#da697a');
                }
            });
        }, 500);
        jQuery(".check-availability-table tr td").css("border", "1px solid black");

    });
</script>