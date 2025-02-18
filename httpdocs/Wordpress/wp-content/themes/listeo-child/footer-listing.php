<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package listeo
 */

?>

<!-- Footer
================================================== -->
<?php
$sticky = get_option('listeo_sticky_footer') ;
$style = get_option('listeo_footer_style') ;

if(is_singular()){

    $sticky_singular = get_post_meta($post->ID, 'listeo_sticky_footer', TRUE);

    switch ($sticky_singular) {
        case 'on':
        case 'enable':
            $sticky = true;
            break;

        case 'disable':
            $sticky = false;
            break;

        case 'use_global':
            $sticky = get_option('listeo_sticky_footer');
            break;

        default:
            $sticky = get_option('listeo_sticky_footer');
            break;
    }

    $style_singular = get_post_meta($post->ID, 'listeo_footer_style', TRUE);
    switch ($style_singular) {
        case 'light':
            $style = 'light';
            break;

        case 'dark':
            $style = 'dark';
            break;

        case 'use_global':
            $style = get_option('listeo_footer_style');
            break;

        default:
            $sticky = get_option('listeo_footer_style');
            break;
    }
}

$sticky = apply_filters('listeo_sticky_footer_filter',$sticky);
?>
<div id="footer" class="<?php echo esc_attr($style); echo esc_attr(($sticky == 'on' || $sticky == 1 || $sticky == true) ? " sticky-footer" : ''); ?> ">
    <!-- Main -->
    <div class="container">
        <div class="row">
            <?php
            $footer_layout = get_option( 'pp_footer_widgets','3,3,2,2,2' );

            $footer_layout_array = explode(',', $footer_layout);
            $x = 0;
            foreach ($footer_layout_array as $value) {
                $x++;
                ?>
                <div class="col-md-<?php echo esc_attr($value); ?> col-sm-6 col-xs-12">
                    <?php
                    if( is_active_sidebar( 'footer'.$x ) ) {
                        dynamic_sidebar( 'footer'.$x );
                    }
                    ?>
                </div>
            <?php } ?>

        </div>
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-12">
                <div class="copyrights"> <?php $copyrights = get_option( 'pp_copyrights' , '&copy; Theme by Purethemes.net. All Rights Reserved.' );

                    echo wp_kses($copyrights,array( 'a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(),));
                    ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Back To Top Button -->
<div id="backtotop"><a href="#"></a></div>

<?php 
if(is_singular('listing')) {
global $post;
$_booking_status = get_post_meta($post->ID, '_booking_status');
$contact_form_label = "";
$external_booking_label = "";

$_booking_system_contact_form = get_post_meta( $post->ID, '_booking_system_contact_form', true );


$_booking_system___external_booking = get_post_meta( $post->ID, '_booking_system___external_booking', true );
$_booking_system_rental = get_post_meta( $post->ID, '_booking_system_rental', true );
$_booking_system_equipment = get_post_meta( $post->ID, '_booking_system_equipment', true );
$_booking_system_weekly_view = get_post_meta( $post->ID, '_booking_system_weekly_view', true );
$_booking_system_service = get_post_meta( $post->ID, '_booking_system_service', true );

if($_booking_system___external_booking || $_booking_system_rental || $_booking_system_equipment || $_booking_system_weekly_view  ||  $_booking_system_service){
     if($_booking_system___external_booking){
         $external_booking_label = get_post_meta( $post->ID, '_booking_button_text', true );
     }
}else{
     if($_booking_system_contact_form){
        $contact_form_label = get_option("contact_form_booking_label");
     }
}


if($_booking_status) { ?>
<!-- Booking Sticky Footer -->
<div class="booking-sticky-footer">
    <div class="container">
            <div class="col-xs-9 bsf-left">
                <?php
                $price_min = get_post_meta( $post->ID, '_price_min', true );
                if (is_numeric($price_min)) {
                    $price_min_raw = number_format_i18n($price_min);
                }
                $currency_abbr = get_option( 'listeo_currency' );
                $currency_postion = get_option( 'listeo_currency_postion' );
                $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

                if($price_min) { ?>
                    <h4>
                        <?php esc_html_e('Starting from','listeo');
                        if($currency_postion == 'after') { 
                            echo $price_min_raw . $currency_symbol; 
                        } else { 
                            echo $currency_symbol . $price_min_raw; 
                        } ?>
                    </h4>
                <?php } else { ?>
                    <h4><?php esc_html_e('Select dates to see prices','listeo'); ?></h4>
                <?php } ?>

                <?php
                if(!get_option('listeo_disable_reviews')){
                $rating = get_post_meta($post->ID, 'listeo-avg-rating', true);
                if(isset($rating) && $rating > 0 ) {
                    $rating_type = get_option('listeo_rating_type','star');
                    if($rating_type == 'numerical') { ?>
        
                        <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>"></div>
                        <?php } else { ?>
                        <div class="star-rating" data-rating="<?php echo $rating; ?>"></div>
                        <?php } ?>
                    <?php }
                    } ?>
                    <!-- </div> -->
                    <div class="row col-xs-9 bsf-leftDropdown" style="margin-top:15px; padding:0; display:none;">
                <div class="col-xs-12" style="text-align: center;">
                    <span class="col-xs-2" style="padding:14px;">FRA: </span>
                    <div class="col-xs-10">
                        <select style="font-size: 16px;font-weight:600;color:#888; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="fromH" id="mobFromHours">
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12" style="text-align: center;">
                    <span class="col-xs-2" style="padding:14px;">TIL: </span>
                    <div class="col-xs-10">
                        <select style="font-size: 16px;font-weight:600;color:#888; font-family: source sans pro; box-shadow: 0px 1px 2px 2px #EDEDED;border-color: white;" name="toH" id="mobToHours">
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>

                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="col-xs-3 bsf-right" style="padding:0px;">
                <?php
                if($contact_form_label != ""){ ?>
                    <a href="javascript:void(0)" class="button click_contact_widget_btn"><?php echo $contact_form_label; ?></a>
                <?php }elseif($external_booking_label != ""){ ?>
                    <a href="javascript:void(0)" class="button click_external_booking_widget_btn"><?php echo $external_booking_label; ?></a>
                <?php }else{?>
                   <a href="javascript:void(0)" class="button click_widget_btn"><?php esc_html_e('See Availability', 'listeo'); ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div> <!-- weof wrapper -->
<?php }
    } ?>
<?php if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-video.php') || is_page_template('template-home-search-splash.php')) && get_option('listeo_home_typed_status','enable') == 'enable') {
    $typed = get_option('listeo_home_typed_text');
    $typed_array = explode(',',$typed);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
    <script>
        var typed = new Typed('.typed-words', {
            strings: <?php echo json_encode($typed_array); ?>,
            typeSpeed: 80,
            backSpeed: 80,
            backDelay: 4000,
            startDelay: 1000,
            loop: true,
            showCursor: true
        });
    </script>
<?php } ?>
<?php wp_footer(); ?>
<script type="text/javascript">
jQuery(".click_widget_btn").click(function(){
    jQuery('html, body').animate({
        scrollTop: jQuery(".listing-widget").offset().top
    }, 2000);
    jQuery("body").find("#date-picker").addClass("focus_div");
    setTimeout(function(){
        jQuery("body").find("#date-picker").removeClass("focus_div");
    },6000);
})
jQuery(".click_contact_widget_btn").click(function(){
    jQuery('html, body').animate({
        scrollTop: jQuery(".contact_widget").offset().top
    }, 2000);
})
jQuery(".click_external_booking_widget_btn").click(function(){
    jQuery('html, body').animate({
        scrollTop: jQuery(".booking-external-widget").offset().top - 40
    }, 2000);
})
</script>
</body>
</html>