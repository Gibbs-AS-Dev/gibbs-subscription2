<?php
$template_loader = new Listeo_Core_Template_Loader;
$useragent=$_SERVER['HTTP_USER_AGENT'];
$tablet_browser = 0;
$mobile_browser = 0;

$coupon_exist_for_listing = new Listeo_Core_Coupons;
$coupon_exist_for_listing  = $coupon_exist_for_listing->coupon_exist_for_listing($post->ID);

$_require_validated_user = get_post_meta($post->ID,'_require_validated_user',true);

add_action("wp_footer", "add_overlay");
function add_overlay(){ ?>
   
<?php } 



if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $tablet_browser++;
}

if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $mobile_browser++;
}

if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
    $mobile_browser++;
}

$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
$mobile_agents = array(
    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
    'newt','noki','palm','pana','pant','phil','play','port','prox',
    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
    'wapr','webc','winw','winw','xda ','xda-');

if (in_array($mobile_ua,$mobile_agents)) {
    $mobile_browser++;
}



if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
    $mobile_browser++;
    //Check for tablets on opera mini alternative headers
    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
      $tablet_browser++;
    }
}


if(isset($_GET['check_availability']) && $_GET['check_availability'] == 1){
    $template_loader->get_template_part( 'check-availability','check-availability' );
}else if(isset($_GET['discounts']) && $_GET['discounts'] == 1){
    $template_loader->get_template_part( 'discounts','discounts' );

}else{



if($tablet_browser > 843843843840 && preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)&&preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
    $template_loader->get_template_part( 'single-listing','mobile' );
}else{



if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(get_option('header_bar_style','standard') );
if(is_user_logged_in()){
?>
    <style type="text/css">
        .menu-last{
            display: none !important;
        }
        @media (max-width: 991px){
            body div#logo{
                display: block !important;
            }
            body .mobileViewToggle{
                display: none !important;
            }
            body .main-nav {
                display: block !important;
                left: 49px;
            }
            .left-side-divv-single #logo {
                top: 0px !important;
            }
        }
    </style>
<?php }else{ ?>
    <style type="text/css">
        .mobile-header, .get_your_trial{
            display: none !important;
        }
        @media (max-width: 991px){
            body .trp-language-switcher .trp-ls-shortcode-current-language, body .trp-language-switcher .trp-ls-clicked {
                width: 123px !important;
            }
            .trp_language_switcher_shortcode {
                align-items: end !important;
                text-align: end;
                display: flex;
                justify-content: flex-end;
                width: 100%;
                margin-left: 67px;
            }
            body .main-nav:not(.main-nav-small) a {
                margin-right: 0px;
            }
                
        }
    </style>
<?php } ?> 
<script>
    jQuery("#header-new").find(".container-fluid").first().addClass("container").removeClass("container-fluid");
</script>
<?php

if($coupon_exist_for_listing == false){ ?>
<style type="text/css">
    .coupon-widget-wrapper{
        display: none !important;
    }
</style>
<?php }

$_hide_price_div = get_post_meta($post->ID,'_hide_price_div',true);

$_hide_price_div_single_listing = get_post_meta($post->ID,'_hide_price_div_single_listing',true);

$gallery_style = get_post_meta( $post->ID, '_gallery_style', true );

if(empty($gallery_style)) { $gallery_style = get_option('listeo_gallery_type','top'); }

$count_gallery = listeo_count_gallery_items($post->ID);

if($count_gallery < 400 ){
    $gallery_style = 'content';
}
if( get_post_meta( $post->ID, '_gallery_style', true ) == 'top' && $count_gallery == 1 ) {
    $gallery_style = 'none';
}

if ( have_posts() ) :
if( $gallery_style == 'top1' ) :
    $template_loader->get_template_part( 'single-partials/single-listing','gallery' );
else: ?>
    <!-- Gradient-->
    <div class="single-listing-page-titlebar"></div>
<?php endif; ?>
<?php
$listeo_hide_searchbar_single_listing = get_post_meta($post->ID,'listeo_hide_searchbar_single_listing',true);

if($post->post_parent != "" && $post->post_parent != "0" &&  $post->post_parent != null){
  $linkk = get_permalink($post->post_parent);
  if($linkk && $linkk != ""){
    wp_redirect($linkk);
    exit;
  }
  
}


$taxPrice11 = get_post_meta($post->ID,'_tax',true);

if($taxPrice11 != ""){
   $taxPrice= ((int)$taxPrice11) / 100;
}else{
     $taxPrice= "none";
}

$tax_txt = "";

if($taxPrice != "none" && $taxPrice != "0"){

    $tax_txt = "ink. mva";

}

$from_price = get_post_meta($post->ID,'_hour_price',true);
if($from_price == ""){
    $from_price = get_post_meta($post->ID,'_normal_price',true);
}
if($from_price == ""){
    $from_price = get_post_meta($post->ID,'_weekday_price',true);
}
?>
<?php
if($listeo_hide_searchbar_single_listing){
?>
<style type="text/css"> 
#listeo_core-search-form{
    display: none;
}
</style>
<?php  
}

$cr_user = get_current_user_id();

$group_admin = get_group_admin();

if($group_admin != ""){
    $cr_user = $group_admin;
}
$show_widget = false;
if($cr_user == $post->post_author){
   $show_widget = true;
}

?>
<?php if(isset($_GET["hide"]) && $_GET["hide"] == true){ ?>
    <style type="text/css">
        #header{
            display: none;
        }
        .listing-share{
            display: none;
        }
        .single-listing-page-titlebar{
            background: none;
        }
        .single_listing .sticky, .single_listing{
            padding: 0px;
        }
    </style>
<?php } ?>
<style>
    a.xoo-el-login-tgr.login_reg_popup {
        display: none;
    }
</style>
<!-- Content
================================================== -->
<div class="container notification_div" <?php if(isset($_GET["hide"]) && $_GET["hide"] == true){ ?> style="display:none" <?php } ?>>
    <?php

     if($post->post_status == "draft" || $post->post_status == "pending" || $post->post_status == "trash"){ ?>

        <div class="row">

            <div class="col-md-12 listing_title">
                <div class="alert alert-info" role="alert">
                    <?php
                     $titlee1 = (strlen(get_the_title()) > 200) ? substr(get_the_title(),0,200).'...' : get_the_title();
                    ?>
                   <?php echo __("Annonsen","gibbs");?> <?php echo $titlee1;?><?php echo __(" er ikke tilgjengelig offentlig siden den er et utkast","gibbs");?> 
                </div>
            </div>
                 

        </div>

    <?php }else if($post->post_status == "expired"){ ?>

        <div class="row">

            <div class="col-md-12 listing_title">
                <div class="alert alert-info" role="alert">
                    <?php
                     $titlee1 = (strlen(get_the_title()) > 200) ? substr(get_the_title(),0,200).'...' : get_the_title();
                    ?>
                    <?php echo __("Annonsen","gibbs");?> <?php echo $titlee1;?><?php echo __(" er ikke tilgjengelig offentlig siden den er inaktiv","gibbs");?> 
                </div>
            </div>
                 

        </div>

    <?php } ?>
</div>
    
<div class="container single_listing">
    <div class="row sticky-wrapper">
        <?php

            $_show_price_warranty_widget = get_post_meta( $post->ID, '_show_price_warranty_widget', true );
            if(!$_show_price_warranty_widget){
            ?>
            <style type="text/css">
            .price_widget {
             display: none !important;
            }
            </style>
            <?php
            }
        ?>


        <?php

            $_show_borettslag_widget = get_post_meta( $post->ID, '_show_borettslag_widget', true );
            if(!$_show_borettslag_widget){
            ?>
            <style type="text/css">
            .borettslag_widget {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>

        <?php

            $_show_custom_contact_info = get_post_meta( $post->ID, '_show_custom_contact_info', true );
            if(!$_show_custom_contact_info){
            ?>
            <style type="text/css">
            .info-box-new {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>

        <?php
            $_show_other_listings = get_post_meta( $post->ID, '_show_other_listings', true );
            if(!$_show_other_listings){
            ?>
            <style type="text/css">
            .related_list {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>

          <?php
            $_show_reviews = get_post_meta( $post->ID, '_show_reviews', true );
            if(!$_show_reviews){
            ?>
            <style type="text/css">
            .add-review-box, #listing-reviews, .star-rating {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>
        <?php
            $_hide_logo_for_listing = get_post_meta( $post->ID, '_hide_logo_for_listing', true );
            if($_hide_logo_for_listing){
            ?>
            <style type="text/css">
             #logo {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>
           <?php
            $_hide_header_for_listing = get_post_meta( $post->ID, '_hide_header_for_listing', true );
            if($_hide_header_for_listing){
            ?>
            <style type="text/css">
             #header-container  {
            display: none !important;
            }
            </style>
            <?php
            }
        ?>

        <!-- Sidebar
    ================================================== -->
        <?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>
        <div class="col-lg-4 col-md-4 col-lg-push-8  margin-top-12 sticky sdfdf">

            <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?>
                <!-- Verified Badge -->
                <div class="verified-badge with-tip" data-tip-content="<?php esc_html_e('Listing has been verified and belongs to the business owner or manager.','listeo_core'); ?>">
                    <i class="sl sl-icon-check"></i> <?php esc_html_e('Verified Listing','listeo_core') ?>
                </div>
            <?php else:
                if(get_option('listeo_claim_page_button')){
                    $claim_page = get_option('listeo_claim_page');?>
                    <div class="claim-badge with-tip" data-tip-content="<?php esc_html_e('Click to claim this listing.','listeo_core'); ?>">
                        <?php
                        $link =  add_query_arg ('subject', get_permalink(), get_permalink($claim_page)) ; ?>

                        <a href="<?php echo $link; ?>"><i class="sl sl-icon-question"></i> <?php esc_html_e('Not verified. Claim this listing!','listeo_core') ?></a>
                    </div>
                <?php }

            endif; ?>
            <?php  
                $_listing_widget_title = get_post_meta($post->ID,'_listing_widget_title',true);
                $_listing_widget_description = get_post_meta($post->ID,'_listing_widget_description',true);
                if( $_listing_widget_title != "" || $_listing_widget_description != ""){ ?>

                    <div class="custom-widget-listing">
                        <h3> <?php echo get_post_meta($post->ID,'_listing_widget_title',true) ?></h3>
                        <div class="descc">
                            <?php echo get_post_meta($post->ID,'_listing_widget_description',true) ?>
                        </div>
                    </div>
                     
                <?php }

                $verify_listing = get_post_meta($post->ID,"_verify_listing",true); 

                if($verify_listing == "on" && is_user_logged_in()){
                    $book_with_login = "";

					//echo "<pre>"; print_r($_verified_user); die("jhjkh");
                    $_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);



					if($_verified_user == "on") { /* ?>

                    <div class="verified-badge">
                         <i class="sl sl-icon-check"></i> <?php esc_html_e('You are verified','listeo_core') ?>
                     </div>
						
					<?php  */}else{ ?>

                    <div class="custom-widget-listing">
                         <div class="descc">
                            Krever BankID verifisering <button class="btn btn-primary verify-btn" onClick="(function(){ jQuery('#varify_modal').show();})();return false;">Klikk her</button>

                        </div>
                    </div>
                    
                   <?php }


                }
            ?>


            <?php 

                

                     get_sidebar('listing');
                

            ?>
            <div class="overlay" style="display: none;position: absolute;">
                <div class="overlay__inner">
                    <div class="overlay__content"><span class="spinner"></span></div>
                </div>
            </div>
        </div>


        <?php
if(isset($_GET["hide"]) && $_GET["hide"] === "true") {
    echo '<style>.custom-widget-listing { display: none; }</style>';
}
?>

        <?php }?>
        <!-- Sidebar / End -->
        <?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>
        <?php while ( have_posts() ) : the_post();  ?>
        <div class="col-lg-8 col-md-8 col-lg-pull-4 padding-right-30 left-side-divv-single" <?php if(isset($_GET["hide"]) && $_GET["hide"] == true){ ?> style="display:none" <?php } ?>> 
         <!-- Content
                ================================================== -->
                
                <?php
                if($gallery_style == 'none' ) :
                    $gallery = get_post_meta( $post->ID, '_gallery', true );
                    if(!empty($gallery)) :

                        foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                            $image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
                            //echo '<img src="'.esc_url($image[0]).'" class="single-gallery margin-bottom-40" style="margin-top:-30px;"></a>';
                        }

                    endif;
                endif;

                if ($count_gallery > 1 && $count_gallery < 400){
                    $template_loader->get_template_part( 'single-partials/single-listing','gallery-custom' );
                }elseif ($count_gallery == 1){
                    $template_loader->get_template_part( 'single-partials/single-listing','gallery-custom' );
                }?>
            <!-- Titlebar -->
            <div id="titlebar" class="listing-titlebar">
                <div class="listing-titlebar-title">
                    <div class="row">
                        <div class="col-md-11  margin-bottom-0">
                       
                        <h3>
                            <?php
                            $titlee = (strlen(get_the_title()) > 200) ? substr(get_the_title(),0,200).'...' : get_the_title();
                            ?>
                            <?php echo $titlee; ?>
                           
                             <?php
                                $type_terms = array();
                                if(!get_option('listeo_disable_reviews')){
                                $rating = get_post_meta($post->ID, 'listeo-avg-rating', true);
                                    if(isset($rating) && $rating > 0 ) :
                                        $rating_type = get_option('listeo_rating_type','star');
                                        if($rating_type == 'numerical') { ?>
                                        <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                                        <?php } else { ?>
                                        <div class="star-rating" data-rating="<?php echo $rating; ?>">
                                            <?php } ?>
                                            <?php $number = listeo_get_reviews_number($post->ID);  ?>
                                            <div class="rating-counter"><a href="#listing-reviews">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</a></div>
                                        </div>
                                    <?php endif;
                                }?>
                            <?php
                            $terms = get_the_terms( get_the_ID(), 'listing_category' );
                            if ( $terms && ! is_wp_error( $terms ) ) :
                                $categories = array();
                                foreach ( $terms as $term ) {

                                    $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                                        esc_url( get_term_link( $term->slug, 'listing_category' ) ),
                                        esc_html( $term->name )
                                    );
                                }

                                $categories_list = join( ", ", $categories );
                                ?>
                                <span class="listing-tag" style="display:none">
                                <?php  //echo ( $categories_list ) ?>
                            </span>
                            <?php endif; ?>
                            <?php $listing_type = get_post_meta( get_the_ID(), '_listing_type', true);
                             $type_terms = get_the_terms( get_the_ID(), 'service_category' );
                                    $taxonomy_name = 'service_category';



                           /* switch ($listing_type) {
                                case 'service':
                                    $type_terms = get_the_terms( get_the_ID(), 'service_category' );
                                    $taxonomy_name = 'service_category';
                                    break;
                                case 'rental':
                                    $type_terms = get_the_terms( get_the_ID(), 'rental_category' );
                                    $taxonomy_name = 'rental_category';
                                    break;
                                case 'event':
                                    $type_terms = get_the_terms( get_the_ID(), 'event_category' );
                                    $taxonomy_name = 'event_category';
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            */
                            ?>
                          </h3>
                          <?php if( isset($type_terms) ) {
                                if ( $type_terms && ! is_wp_error( $type_terms ) ) :
                                    $categories = array();
                                    /*foreach ( $type_terms as $term ) {
                                        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                                            esc_url( get_term_link( $term->slug, $taxonomy_name ) ),
                                            esc_html( $term->name )
                                        );
                                    }*/
                                    
                                    $type_terms = buildTree($type_terms);


                                   



                                    foreach ( $type_terms as $term ) {
                                        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                                            esc_url( get_term_link( $term->slug, $taxonomy_name ) ),
                                            esc_html( $term->name )
                                        );
                                        if(isset($term->childs)){

                                            $categories[] = get_childs($term->childs); 

                                            

                                                foreach ($term->childs as $childs2) {

                                                    if(isset($childs2->childs)){

                                                        $categories[] = get_childs($childs2->childs); 

                                                        if(isset($childs2->childs)){


                                                                foreach ($childs2->childs as $childs3) {

                                                                    if(isset($childs3->childs)){

                                                                        $categories[] = get_childs($childs3->childs); 
                                                                    }   
                                                                
                                                               }
                                                            
                                                        }; 
                                                    }   
                                                
                                               }
                                            
                                        }; 
                                    
                                    }

                                    $categories_list = join( " / ", $categories );
                                    ?>
                                    <span class="listing-tag">
                                <?php  echo ( $categories_list ) ?>
                            </span>
                                <?php endif;
                            }
                            ?>
                          <?php if(get_the_listing_address()): ?>
                           <!--  <span style="padding: 10px 0px;">
                                <a href="#listing-location" class="listing-address">
                                    <i class="fa fa-map-marker"></i>
                                    <?php the_listing_address(); ?>
                                </a>
                            </span> -->
                         <?php endif; ?>
                          <div class="row row_featured">
                            <?php  $feature_html = array();

                                $list_cats = array();
                                foreach ($type_terms as $key => $terms_o) {
                                    $list_cats[] = $terms_o->term_id;
                                }

                                $autobook = get_post_meta($post->ID,'_instant_booking',true); 
                                if($autobook == 'on'){

                                   


                                    // WP_Query arguments
                                    $args = array (
                                        'post_type'              => array( 'special_featured' ),
                                        'post_status'            => array( 'publish' ),
                                        'meta_query'             => array(
                                            array(
                                                'key'       => 'feature_type_for',
                                                'value'     => 'instant_booking',
                                            ),
                                        ),
                                    );

                                    // The Query
                                    $instant_bookingss = new WP_Query( $args );
                                    $instant_bookingss = $instant_bookingss->posts;

                                    if(isset($instant_bookingss[0])){

                                        $cat_exist = false;

                                        $cat_feature = get_post_meta($instant_bookingss[0]->ID,"cat_feature",true);
                                        if($cat_feature != ""){
                                            $cat_feature = json_decode($cat_feature);

                                            foreach ($cat_feature as $key12 => $value12) {
                                               if(in_array($value12, $list_cats)){
                                                 $cat_exist = true;
                                                 break;
                                               }
                                            }
                                        }
                                        if($cat_exist == true){

                                            $tittle =  $instant_bookingss[0]->post_title;

                                            $_icon_svg = get_post_meta($instant_bookingss[0]->ID,"_icon_svg",true);
                                            $activate_full_row = get_post_meta($instant_bookingss[0]->ID,"activate_full_row",true);
                                            $order_number = get_post_meta($instant_bookingss[0]->ID,"order_number",true);

                                            if($_icon_svg != ""){
                                                $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                $_icon_svg = "<img src='".$_icon_svg."' />";
                                            }else{
                                                $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                            }
                                            if($activate_full_row == "1"){
                                                $cll = "col-md-12 col-xs-12";
                                            }else{
                                                $cll = "col-md-6 col-xs-6";
                                            }
                                            $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                            $feature_html[0]["html"] = '<div class="'.$cll.' listing-small-badge rglstntxtbx " style="padding: 0;">
                                                                    '.$_icon_svg .'
                                                                    <span>'.$tittle.'</span>
                                                                </div>';
                                            $feature_html[0]["order"] = $order_number;                    

                                           
                                        }

                                    }
                                
                                
                               ?>
                               
                                    


                            <?php } ?>

                            <!-- addresss -->

                             <?php 
                             if(get_the_listing_address()){

                                // WP_Query arguments
                                    $args = array (
                                        'post_type'              => array( 'special_featured' ),
                                        'post_status'            => array( 'publish' ),
                                        'meta_query'             => array(
                                            array(
                                                'key'       => 'feature_type_for',
                                                'value'     => 'address',
                                            ),
                                        ),
                                    );

                                    // The Query
                                    $address_feature = new WP_Query( $args );
                                    $address_feature = $address_feature->posts;

                                    if(isset($address_feature[0])){

                                                $cat_exist = false;

                                                $cat_feature = get_post_meta($address_feature[0]->ID,"cat_feature",true);
                                                if($cat_feature != ""){
                                                    $cat_feature = json_decode($cat_feature);

                                                    foreach ($cat_feature as $key12 => $value12) {
                                                       if(in_array($value12, $list_cats)){
                                                         $cat_exist = true;
                                                         break;
                                                       }
                                                    }
                                                }
                                                if($cat_exist == true){

                                                    $tittle =  $address_feature[0]->post_title;
                                                    $gaddress = get_post_meta(get_the_ID(),"_address",true);
                                                    $friendly_address = get_post_meta( $post->ID, '_friendly_address', true );
                                                    $get_address =  (!empty($friendly_address)) ? $friendly_address : $gaddress;
                                                    $get_address = apply_filters( 'the_listing_location', $get_address, $post );
                                                    if($get_address != ""){



                                                        $tittle = str_replace("{address}", $get_address, $tittle);


                                                        $_icon_svg = get_post_meta($address_feature[0]->ID,"_icon_svg",true);
                                                        $activate_full_row = get_post_meta($address_feature[0]->ID,"activate_full_row",true);
                                                        $order_number = get_post_meta($address_feature[0]->ID,"order_number",true);

                                                        if($_icon_svg != ""){
                                                            $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                            $_icon_svg = "<img src='".$_icon_svg."' />";
                                                        }else{
                                                            $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                        }
                                                        if($activate_full_row == "1"){
                                                            $cll = "col-md-12 col-xs-12";
                                                        }else{
                                                            $cll = "col-md-6 col-xs-6";
                                                        }
                                                        $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                        $feature_html[1]["html"] = '<div class="'.$cll.' listing-small-badge  rglstntxtbx " style="padding: 0;"><a href="#listing-location" class="listing-address">
                                                                                '.$_icon_svg .'
                                                                                <span>'.$tittle.'</span>
                                                                            </a></div>';
                                                        $feature_html[1]["order"] = $order_number; 
                                                    }                   

                                                   
                                                }

                                            }

                             }
                             ?>


                            <!-- End address -->

                            
                                            <!-- capacity_feature -->

                                             <?php 

                                             $capacity = get_post_meta($post->ID,"_standing",true);
                                             if($capacity != ""){

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'capacity',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $capacity_feature = new WP_Query( $args );
                                                    $capacity_feature = $capacity_feature->posts;

                                                    if(isset($capacity_feature[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($capacity_feature[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){

                                                            $tittle =  $capacity_feature[0]->post_title;
                                                           
                                                            $tittle = str_replace("{capacity}", $capacity, $tittle);


                                                                $_icon_svg = get_post_meta($capacity_feature[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($capacity_feature[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($capacity_feature[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[2]["html"] = '<div class="'.$cll.' listing-small-badge  rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[2]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End capacity_feature -->

                                            <!-- price -->

                                            <?php 

                                            

                                            $price_min = get_post_meta( $post->ID, '_price_min', true );
                                            $price_max = get_post_meta( $post->ID, '_price_max', true );
                                            $decimals = get_option('listeo_number_decimals',2);
                                            if(!empty($price_min) || !empty($price_max)) {

                                                if (is_numeric($price_min)) {
                                                    $price_min_raw = number_format_i18n($price_min,$decimals);
                                                    if($taxPrice != "none"){
                                                       $price_min_raw = round(($price_min_raw * $taxPrice) + $price_min_raw);
                                                    }
                                                } 
                                                if (is_numeric($price_max)) {
                                                    $price_max_raw = number_format_i18n($price_max,$decimals);
                                                    if($taxPrice != "none"){
                                                        $price_max_raw = round(($price_max_raw * $taxPrice) + $price_max_raw);
                                                    }
                                                }
                                                $currency_abbr = get_option( 'listeo_currency' );
                                                $currency_postion = get_option( 'listeo_currency_postion' );
                                                $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                                                if($currency_postion == 'after') {
                                                    if(!empty($price_min_raw) && !empty($price_max_raw)){
                                                        $price_min =  $price_min_raw . $currency_symbol;
                                                        $price_max =  $price_max_raw . $currency_symbol;    
                                                    } else 
                                                    if(!empty($price_min_raw) && empty($price_max_raw)) {
                                                        $price_min =  $price_min_raw . $currency_symbol;
                                                    } else {
                                                        $price_max =  $price_max_raw . $currency_symbol;
                                                    }
                                                    
                                                } else {
                                                    if(!empty($price_min_raw) && !empty($price_max_raw)){
                                                        $price_min =  $currency_symbol . $price_min_raw;
                                                        $price_max =  $currency_symbol . $price_max_raw;    
                                                    } else 
                                                    if(!empty($price_min_raw) && empty($price_max_raw)) {
                                                        $price_min =  $currency_symbol .$price_min_raw;
                                                    } else {
                                                        $price_max =   $currency_symbol .$price_max_raw ;
                                                    }

                                                }

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'price',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $price_feature = new WP_Query( $args );
                                                    $price_feature = $price_feature->posts;

                                                    if(isset($price_feature[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($price_feature[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){


                                                            

                                                            $tittle =  $price_feature[0]->post_title;
                                                           
                                                            $tittle = str_replace("{price_from}", $price_min, $tittle);
                                                            $tittle = str_replace("{price_to}", $price_max, $tittle);


                                                                $_icon_svg = get_post_meta($price_feature[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($price_feature[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($price_feature[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[3]["html"] = '<div class="'.$cll.' listing-small-badge rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[3]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End price_feature -->

                                            <!--  event date -->

                                            <?php 

                                            $_event_date = get_post_meta( $post->ID, '_event_date', true );
                                            $_event_date_end = get_post_meta( $post->ID, '_event_date_end', true );
                                            if(!empty($_event_date) || !empty($_event_date_end)) {
                                                $event_start_date = "";
                                                $event_end_date = "";

                                                if(!empty($_event_date)){
                                                    $_event_date = str_replace("/","-",$_event_date);
                                                    $event_start_date = date("d M, Y",strtotime($_event_date));
                                                }
                                                if(!empty($_event_date_end)){
                                                    $_event_date_end = str_replace("/","-",$_event_date_end);
                                                    $event_end_date = date("d M, Y",strtotime($_event_date_end));
                                                }
                                               

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'Event_date',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $event_feature = new WP_Query( $args );
                                                    $event_feature = $event_feature->posts;

                                                    if(isset($event_feature[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($event_feature[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){

                                                            $tittle =  $event_feature[0]->post_title;
                                                           
                                                            $tittle = str_replace("{event_start_date}", $event_start_date, $tittle);
                                                            $tittle = str_replace("{event_end_date}", $event_end_date, $tittle);


                                                                $_icon_svg = get_post_meta($event_feature[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($event_feature[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($event_feature[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{ 
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[4]["html"] = '<div class="'.$cll.' listing-small-badge rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[4]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End event date -->

                                             <!--  event time -->

                                            <?php 

                                            $_event_date = get_post_meta( $post->ID, '_event_date', true );
                                            $_event_date_end = get_post_meta( $post->ID, '_event_date_end', true );
                                            if(!empty($_event_date) || !empty($_event_date_end)) {
                                                $event_start_time = "";
                                                $event_end_time = "";

                                                if(!empty($_event_date)){
                                                    $_event_date = str_replace("/","-",$_event_date);
                                                    $event_start_time = date("H:i",strtotime($_event_date));
                                                }
                                                if(!empty($_event_date_end)){
                                                    $_event_date_end = str_replace("/","-",$_event_date_end);
                                                    $event_end_time = date("H:i",strtotime($_event_date_end)); 
                                                }
                                               

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'Event_time',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $event_feature = new WP_Query( $args );
                                                    $event_feature = $event_feature->posts;

                                                    if(isset($event_feature[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($event_feature[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){

                                                            $tittle =  $event_feature[0]->post_title;
                                                           
                                                            $tittle = str_replace("{event_start_time}", $event_start_time, $tittle);
                                                            $tittle = str_replace("{event_end_time}", $event_end_time, $tittle);


                                                                $_icon_svg = get_post_meta($event_feature[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($event_feature[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($event_feature[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{ 
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[5]["html"] = '<div class="'.$cll.' listing-small-badge rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[5]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End event time -->

                                            <!-- ticket_feature -->

                                             <?php 

                                             $_event_tickets = get_post_meta($post->ID,"_event_tickets",true);
                                             if($_event_tickets != ""){

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'Event_tickets',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $ticket_feature = new WP_Query( $args );
                                                    $ticket_feature = $ticket_feature->posts;

                                                    if(isset($ticket_feature[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($ticket_feature[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){

                                                            $tittle =  $ticket_feature[0]->post_title;
                                                           
                                                            $tittle = str_replace("{event_ticket}", $_event_tickets, $tittle);


                                                                $_icon_svg = get_post_meta($ticket_feature[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($ticket_feature[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($ticket_feature[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[6]["html"] = '<div class="'.$cll.' listing-small-badge  rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[6]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End ticket_feature -->


                                            <!-- internal booking  -->

                                             <?php 

                                             $_listing_only_for_group = get_post_meta($post->ID,"_listing_only_for_group");
                                             
                                             if(!empty($_listing_only_for_group)){

                                                // WP_Query arguments
                                                    $args = array (
                                                        'post_type'              => array( 'special_featured' ),
                                                        'post_status'            => array( 'publish' ),
                                                        'meta_query'             => array(
                                                            array(
                                                                'key'       => 'feature_type_for',
                                                                'value'     => 'internal_booking_only',
                                                            ),
                                                        ),
                                                    );

                                                    // The Query
                                                    $internal_booking_only = new WP_Query( $args );
                                                    $internal_booking_only = $internal_booking_only->posts;





                                                    if(isset($internal_booking_only[0])){

                                                        $cat_exist = false;

                                                        $cat_feature = get_post_meta($internal_booking_only[0]->ID,"cat_feature",true);
                                                        if($cat_feature != ""){
                                                            $cat_feature = json_decode($cat_feature);

                                                            foreach ($cat_feature as $key12 => $value12) {
                                                               if(in_array($value12, $list_cats)){
                                                                 $cat_exist = true;
                                                                 break;
                                                               }
                                                            }
                                                        }
                                                        if($cat_exist == true){


                                                            $tittle =  $internal_booking_only[0]->post_title;
                                                           
                                                            $tittle = str_replace("{_listing_only_for_group}", $_listing_only_for_group, $tittle);


                                                                $_icon_svg = get_post_meta($internal_booking_only[0]->ID,"_icon_svg",true);
                                                                $activate_full_row = get_post_meta($internal_booking_only[0]->ID,"activate_full_row",true);
                                                                $order_number = get_post_meta($internal_booking_only[0]->ID,"order_number",true);

                                                                if($_icon_svg != ""){
                                                                    $_icon_svg = wp_get_attachment_url($_icon_svg,'medium'); 

                                                                    $_icon_svg = "<img src='".$_icon_svg."' />";
                                                                }else{
                                                                    $_icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                                                                }
                                                                if($activate_full_row == "1"){
                                                                    $cll = "col-md-12 col-xs-12";
                                                                }else{
                                                                    $cll = "col-md-6 col-xs-6";
                                                                }
                                                                $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                                                $feature_html[7]["html"] = '<div class="'.$cll.' listing-small-badge  rglstntxtbx " style="padding: 0;">
                                                                                        '.$_icon_svg .'
                                                                                        <span>'.$tittle.'</span>
                                                                                    </div>';
                                                                $feature_html[7]["order"] = $order_number; 
                                                                          

                                                           
                                                        }

                                                    }

                                             }
                                             ?>


                                            <!-- End internal booking -->

                            <?php
                            if(!empty($feature_html)){

                                usort($feature_html, function($a, $b) {
                                    return $a['order'] - $b['order'];
                                });

                                foreach ($feature_html as $key => $feature_ht) {
                                    echo $feature_ht["html"];
                                }
                            }

                            

                            ?>

                        </div>

                        </div>
                        <div class="col-md-1 share_bk">

                            <?php
            
                            if( listeo_core_check_if_bookmarked($post->ID) ) {
            
                            $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>
            
                            <!-- <i class="fa fa-heart like-icon1 listeo_core-unbookmark-it liked"
            
                            data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                            data-nonce="<?php echo esc_attr($nonce); ?>" ></i> -->
            
                        <?php } else {
            
                            if(is_user_logged_in()){
            
                                $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>
            
                               <!--  <i class="fa fa-heart save listeo_core-bookmark-it like-icon1"
            
                           //     data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                                data-nonce="<?php echo esc_attr($nonce); ?>" ></i> -->
            
                            <?php } else { ?>
                            <!--     <i class="fa fa-heart save like-icon1 tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></i> -->
                            <?php } ?>
                        <?php } ?>

                        <!--     <img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/share_inactive.svg" style="display:none" /> -->

                        </div>
                    </div>
                    
                   
                </div>

            </div>
            
                
               
                <?php //$template_loader->get_template_part( 'single-partials/single-listing','featuresNew' );  ?>  

                <!-- Listing Nav -->
                <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                           if($autobook == 'on'){
                           ?>
               <!--  <div class="listing-small-badge pricing-badge rgdivbk" style="padding: 0; margin-bottom: 20px;box-shadow:none">
                            <img style="width: 28px;height: 28px;margin: 2px 0 6px 0;" src="/wp-content/uploads/2020/10/check_icon_circle.png" alt="coronares">
                            <span style="font-size: 17px;padding-left: 5px;">Automatisk booking</span>
                        </div> -->
                        <?php } ?>



                 <hr style="argin-top: 1px;margin-bottom:4px;display:none">

                <div id="listing-nav" class="listing-nav-container" style="display:none">
                    <ul class="listing-nav">
                        <li><a href="#listing-overview" class="active"><?php esc_html_e('Overview','listeo_core'); ?></a></li>
<!--                        --><?php //if($count_gallery > 0 && $gallery_style == 'content') : ?><!--<li><a href="#listing-gallery">--><?php //esc_html_e('Gallery','listeo_core'); ?><!--</a></li>-->
<!--                        --><?php //endif;
                        $_menu = get_post_meta( get_the_ID(), '_menu_status', 1 );

                        if(!empty($_menu)) {
                            $_bookable_show_menu =  get_post_meta(get_the_ID(), '_hide_pricing_if_bookable',true);
                            if(!$_bookable_show_menu){ ?>
                                <li><a href="#listing-pricing-list"><?php esc_html_e('Pricing','listeo_core'); ?></a></li>
                            <?php } ?>

                        <?php }

                        $video = get_post_meta( $post->ID, '_video', true );
                        if(!empty($video)) :  ?>
                            <li><a href="#listing-video"><?php esc_html_e('Video','listeo_core'); ?></a></li>
                        <?php endif;
                        $latitude = get_post_meta( $post->ID, '_geolocation_lat', true );
                        if(!empty($latitude)) :  ?>
                            <li><a href="#listing-location"><?php esc_html_e('Location','listeo_core'); ?></a></li>
                        <?php
                        endif;
                        if(!get_option('listeo_disable_reviews')){
                            $reviews = get_comments(array(
                                'post_id' => $post->ID,
                                'status' => 'approve' //Change this to the type of comments to be displayed
                            ));
                            if ( $reviews ) : ?>
                                <li><a href="#listing-reviews"><?php esc_html_e('Reviews','listeo_core'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="#add-review"><?php esc_html_e('Add Review','listeo_core'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>

               

                <!-- Overview -->
                <div id="listing-overview" class="listing-section">
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','main-details' );  ?>

                    <!-- Description -->

                    <?php the_content(); ?>
                    <?php
                        $_listing_core = get_post_meta($post->ID, '_listing_core', true);
                        if($_listing_core != ''){
                            $_listing_core = unserialize($_listing_core);
                            
                    ?>
                        <div id="listing-cast-list" class="listing-section cast">
                            <?php
                                foreach($_listing_core as $key => $d){
                                    if(count($d['artist']) > 0){
                                    ?>
                                    <h3 class="listing-desc-headline"><?php echo $d['title']; ?></h3>
                                    <div id="listing-artist" class="listing-artist-section">
                                        <?php
                                        for($i=0;$i<count($d['artist']);$i++){
                                            ?>
                                            <div id="" class="artist_col">
                                            <?php if($d['artist'][$i]['image'] != ''){ 
                                            
                                             $feat_image_url = wp_get_attachment_url( $d['artist'][$i]['image'] );
                                            ?>
                                                <div class="artist_image">
                                                    <img style="width: 100px;height:100px;" src="<?php echo $feat_image_url; ?>" alt="standing">
                                                </div>
                                            <?php } ?>
                                                
                                                <div class="artist_info">
                                                    <h5><?php echo $d['artist'][$i]['title']; ?></h5>
                                                    <div class="artist_url">
                                                        <a href="<?php echo $d['artist'][$i]['link']; ?>">Les mer</a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php
                                        } 
                                        ?>
                                    </div>
                                    <?php
                                    }
                                }
                            ?>
                            
                        </div>
                    <?php } ?>
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','socials' );  ?>

                    <?php
                    if(get_post_meta( $post->ID , '_booking_status',true) == 'on'):
                        if(get_post_custom_values($key = '_category')[0] == "utstr"):
                            include("single-partials/equipment-calendar.php");
                        endif;
                    endif; ?>

                    <?php if(!empty(get_post_custom_values($key = '_standing')[0])
                || !empty(get_post_custom_values($key = '_classroom')[0])
                || !empty(get_post_custom_values($key = '_banquet')[0])
                || !empty(get_post_custom_values($key = '_coronares')[0])
                || !empty(get_post_custom_values($key = '_captest')[0])
                || !empty(get_post_custom_values($key = '_theatre')[0])
                || !empty(get_post_custom_values($key = '_horseshoe')[0])
                || !empty(get_post_custom_values($key = '_squarefeet')[0])
                ){ ?>
                    

                <div id="listing-capacity-list" class="listing-section capacity" style="display:none">
                    <h3 class="listing-desc-headline margin-top-30 margin-bottom-30">Kapasitet</h3>
                    <div class="row" style="padding: 10px 0px 10px 0px;">
                    <?php //if(isset(get_post_custom_values($key = '_standing')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_standing',true) == ""){ ?>
                        <div class="col-md-3 standing" style="text-align:center;">
                            <img style="width: 15px;" src="/wp-content/uploads/2020/10/standing.svg" alt="standing">     
                            <h5>Maks kapasitet</h5>
                            <h5><?php echo get_post_custom_values($key = '_standing')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_classroom')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_classroom',true) == ""){ ?>
                        <div class="col-md-3 classroom" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/classroom.svg" alt="classroom">
                            <h5>Klasserom</h5>
                            <h5><?php echo get_post_custom_values($key = '_classroom')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_banquet')[0])){ ?>
                         <?php if(!get_post_meta(get_the_ID(),'_banquet',true) == ""){ ?>
                        <div class="col-md-3 banquet" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/banquet.svg" alt="banquet">
                            <h5>Banquet</h5>
                            <h5><?php echo get_post_custom_values($key = '_banquet')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_coronares')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_coronares',true) == ""){ ?>
                        <div class="col-md-3 coronares" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/coronares5.svg" alt="coronares5">
                            <h5>Korona plass</h5>
                            <h5><?php echo get_post_custom_values($key = '_coronares')[0]; ?></h5>
                            <i style="left: 80%; position: absolute;" class="tip" data-tip-content="Dette er kapasiteten til lokalet med dagens korona restriksjoner"><div class="tip-content">Dette er kapasiteten til lokalet med dagens korona restriksjoner</div></i>
                        </div>
                        

                        <?php } ?>


                    </div>
                    <div class="row" style="padding: 10px 0px 10px 0px;">
                    <?php //if(isset(get_post_custom_values($key = '_captest')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_captest',true) == ""){ ?>
                        <div class="col-md-3 sitting" style="text-align:center;">
                            <img style="width: 20px;" src="/wp-content/uploads/2020/10/sitting.svg" alt="sitting">
                            <h5>Sittende</h5>
                            <h5><?php echo get_post_custom_values($key = '_captest')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_theatre')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_theatre',true) == ""){ ?>
                        <div class="col-md-3 theatre" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/theatre.svg" alt="theatre">
                            <h5>Teater</h5>
                            <h5><?php echo get_post_custom_values($key = '_theatre')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_horseshoe')[0])){ ?>
                        <?php if(!get_post_meta(get_the_ID(),'_horseshoe',true) == ""){ ?>
                        <div class="col-md-3 horseshoe" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/horseshoe.svg" alt="horseshoe">
                            <h5>Banquet</h5>
                            <h5><?php echo get_post_custom_values($key = '_horseshoe')[0]; ?></h5>
                        </div>

                        <?php } ?>

                        <?php //if(isset(get_post_custom_values($key = '_squarefeet')[0])){ ?>
                         <?php if(!get_post_meta(get_the_ID(),'_squarefeet',true) == ""){ ?>
                        <div class="col-md-3 squarefeet" style="text-align:center;">
                            <img style="width: 30px;" src="/wp-content/uploads/2020/10/kvm.svg" alt="squarefeet">
                            <h5>Areal</h5>
                            <h5><?php echo get_post_custom_values($key = '_squarefeet')[0]; ?></h5>
                        </div>

                        <?php } ?>


                    </div>
                </div>

                <?php } ?>
                    <?php //$template_loader->get_template_part( 'single-partials/single-listing','features' );  ?>
                </div>

                <div id="listing-feature-list" class="listing-section features features_sec" style="margin-top: 0px;display:none">
                    <h3 class="listing-desc-headline margin-bottom-30">Tilbyr</h3>
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','featuresNew' );  ?> 
                </div>    

<!--                --><?php //if( $count_gallery > 0 && $gallery_style == 'content') : $template_loader->get_template_part( 'single-partials/single-listing','gallery-content' ); endif; ?>
                <?php  
                $slots_type = get_post_meta($post->ID,"slots_type",true);

                if($slots_type == "standard"){
                    $_hide_price_div = "on";
                }
                $empty_price = true;
                
                if (($_hide_price_div != "on") && ($_hide_price_div_single_listing != "on")) { ?>
                <div id="listing-pricing-list" class="listing-section time-price">
                    <?php
                    if( (get_post_custom_values($key = '_hour_price')[0] !== '') || 
                        (get_post_custom_values($key = '_normal_price')[0] !== '') || 
                        (get_post_custom_values($key = '_weekday_price')[0] !== '')
                    ): 
                    ?>
                        <h3 class="listing-desc-headline margin-top-30 margin-bottom-30">Priser</h3>
                    <?php endif;?>
                    <div class="pricing-list-container">
                        <ul class="pricing-menu-no-title">
                            <?php 

                            $_booking_system_service = get_post_meta(get_the_ID(),"_booking_system_service",true);
                            $_booking_slots = get_post_meta(get_the_ID(),"_booking_slots",true);

                        //    echo "<pre>"; print_r($_booking_system_service ); die;

                            if($_booking_system_service == "on" && !empty($_booking_slots)){


                                $key_price = 0;

                                foreach ($_booking_slots as $key => $slots) {
                                    $slott = explode("|", $slots);

                                    $from_day = $slott[0];
                                    $from_time = $slott[1];
                                    $to_day = $slott[2];
                                    $to_time = $slott[3];
                                    $slot_price = $slott[4];
                                    $slots = $slott[5];
                                    $slot_id = $slott[6];

                                    $days_list = array(
                                        1   => __('Monday','listeo_core'),
                                        2   => __('Tuesday','listeo_core'),
                                        3   => __('Wednesday','listeo_core'),
                                        4   => __('Thursday','listeo_core'),
                                        5   => __('Friday','listeo_core'),
                                        6   => __('Saturday','listeo_core'),
                                        7   => __('Sunday','listeo_core')
                                    );
                                
                                    if($slot_price == ""){
                                        continue;
                                    }else{
                                        $empty_price = false;
                                    }

                                    ?>
                                    <li <?php if($key_price > 4){?> class="extra_pricer" style="display:none;"<?php } ?>>
                                        <h5 style="text-transform: capitalize;"><?php echo $days_list[$from_day]." ".$from_time;?> Til <?php echo $days_list[$to_day]." ".$to_time;?> </h5>
                                        <span class="js-hour-price">
                                        <?php
                                            echo $slot_price." kr";
                                        ?>
                                        </span>
                                    </li>
                                    <?php

                                    $key_price++;
                                }
                                if($empty_price == true){
                                ?>
                                    <script>

                                    jQuery("#listing-pricing-list").hide();

                                    </script>
                                <?php
                                }

                                echo '<div class="show-more-button-price-main"><a href="javascript:void(0)" class="show-more-button-price show-more-button-price-more" data-more-title="Vis mer" data-less-title="Vis mindre">Vis mer<i class="fa fa-angle-down"></i></a><a href="javascript:void(0)" class="show-more-button-price show-more-button-price-less" data-more-title="Vis mer" data-less-title="Vis mindre" style="display:none">Vis mindre<i class="fa fa-angle-up"></i></a></div>';

                            }else{



                                    $listing_type = get_post_meta( get_the_ID(), '_listing_type', true);
                                    $_booking_system_weekly_view = get_post_meta( get_the_ID(), '_booking_system_weekly_view', true);





                                    if( (get_post_custom_values($key = '_hour_price')[0] != '') && $listing_type != "rental" && $_booking_system_weekly_view != ""){
                                        $hour_show = "";
                                    }else{
                                        $hour_show = "style='display:none'";
                                    }
                                    $timePriceWithTax = round(($taxPrice * get_post_custom_values($key = '_hour_price')[0])+get_post_custom_values($key = '_hour_price')[0]);
                                    if($timePriceWithTax < 1){
                                        $hour_show = "style='display:none'";
                                    }
                                    $price_showw = $timePriceWithTax." kr ".$tax_txt;
                                    if(get_post_custom_values($key = '_hour_price')[0] == '0'){
                                        $price_showw = "GRATIS";
                                    }

                                    if($timePriceWithTax == ""){
                                        $hour_show = "style='display:none'";
                                    }
                                    ?>
                                    <li <?php echo $hour_show;?>>
                                        <h5>Timepris</h5>
                                        <span class="js-hour-price" data-price="<?php echo get_post_custom_values($key = '_hour_price')[0]; ?>" >
                                        <?php
                                        echo $price_showw;
                                        ?>
                                        </span>
                                    </li>
                                    <?php 
                                    if( get_post_custom_values($key = '_normal_price')[0] != ''){
                                        $normal_show = "";
                                    }else{
                                        $normal_show = "style='display:none'";
                                    }
                                    $dailyPriceWithTax = round(($taxPrice * get_post_custom_values($key = '_normal_price')[0])+get_post_custom_values($key = '_normal_price')[0]);
                                    if($dailyPriceWithTax < 1){
                                        $normal_show = "style='display:none'";
                                    }
                                    ?>
                                    <li <?php echo $normal_show;?>>
                                        <h5>Dagspris Man-Fre</h5>
                                            <span class="js-daily-price" data-price="<?php echo get_post_custom_values($key = '_normal_price')[0]; ?>">
                                            <?php
                                            if(get_post_custom_values($key = '_normal_price')[0] == '0'){
                                                echo "GRATIS";
                                            }else{
                                                echo $dailyPriceWithTax." kr ".$tax_txt;
                                            }
                                            ?>
                                            </span>
                                    </li>
                                    <?php 
                                    $weeklyPriceWithTax = round(($taxPrice * get_post_custom_values($key = '_weekday_price')[0])+get_post_custom_values($key = '_weekday_price')[0]);
                                    if( get_post_custom_values($key = '_weekday_price')[0] != ''){
                                        $weekday_show = "";
                                    }else{
                                        $weekday_show = "style='display:none'";
                                    }
                                    if($weeklyPriceWithTax < 1){
                                        $weekday_show = "style='display:none'";
                                    }
                                    ?>
                                    <li <?php echo $weekday_show;?>>
                                        <h5>Dagspris Lør-Søn</h5>
                                            <span class="js-weekly-price" data-price="<?php echo get_post_custom_values($key = '_weekday_price')[0]; ?>">
                                            <?php
                                            if(get_post_custom_values($key = '_weekday_price')[0] == '0'){
                                                echo "GRATIS";
                                            }else{
                                                echo $weeklyPriceWithTax." kr ".$tax_txt;
                                            }
                                            ?>
                                    </li>
                                <?php } ?>    
                        </ul>
                    </div>
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','pricing' );  ?>
                </div>
                <?php } ?>

                <div class="minHours" style="display:none;">
                    <span data-min="<?php echo get_post_custom_values($key = '_min_hours')[0]; ?>"></span>
                </div>
                <div class="minDays" style="display:none;">
                    <span data-min="<?php echo get_post_custom_values($key = '_min_days')[0]; ?>"></span>
                </div>
                <?php $template_loader->get_template_part( 'single-partials/single-listing','opening' );  ?>
                <?php $template_loader->get_template_part( 'single-partials/single-listing','video' );  ?>
                <?php $template_loader->get_template_part( 'single-partials/single-listing','location' );  ?>
                <div class="stuffPdf" style="margin-bottom: 70px;">
                    <?php ?>
                    <h3 style="margin-bottom: 30px;">Tilleggsinformasjon</h3>
                    <hr/>
                    <div id="stuffPdfLinks">
                        <?php for ($i = 0; $i < 5; $i++) {
                            $link = get_post_custom_values($key = '_stuff_pdf_dokument'.$i)[0];
                            $path = explode("/", $link);
                            $linkName = end($path);
                            if($link != null){ ?>
                                <i class='fa fa-file-download'></i><span style='margin-left:5px;'><a style='color:#0C7868;' target='_blank' href='<?php echo $link?>'><?php echo $linkName ?></a></span><br>
                            <?php }
                        }?>
                    </div>
                </div>

                <div class="pdfDoc" style="margin-bottom: 70px;">
                    <h3 style="margin-bottom: 30px;">Utleier krever at du har lest og godkjent betingelser før forespørsel kan sendes</h3>
                    <hr/>
                    <div id="pdfLinks">
                        <?php for ($i = 0; $i < 10; $i++) {
                            $link = get_post_custom_values($key = '_pdf_document'.$i)[0];
                            $path = explode("/", $link);
                            $linkName = end($path);
                            if($link != null){ ?>
                                <i class='fa fa-file-download'></i><span style='margin-left:5px;'><a style='color:#0C7868;' target='_blank' href='<?php echo $link?>'><?php echo $linkName ?></a></span><br>
                            <?php }
                        }?>
                    </div>
                </div>

                <?php if(!get_option('listeo_disable_reviews')){
                    $template_loader->get_template_part( 'single-partials/single-listing','reviews' ); } ?>

                <div class="categoryName" style="display:none;">
                    <span data-cat="<?php echo get_post_custom_values($key = '_category')[0]; ?>"></span>
                </div>
            </div>
            <?php endwhile; // End of the loop. ?>
            <?php } ?>


        </div>
    </div>

    <?php
    if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
    ?>
        <div class="container related_list"  <?php if(isset($_GET["hide"]) && $_GET["hide"] == true){ ?> style="display:none;margin-bottom: 10%;" <?php }else{ ?> style="margin-bottom: 10%;" <?php } ?>>
            <div class="row text-center margin-bottom-30">
                <h3>Andre annonser av samme bruker</h3>
            </div>
            <?php
                $template_loader->set_template_data( $post )->get_template_part( 'listing-cards','listing-cards' );
            ?>
        </div>
    <?php } ?>



        <?php else : ?>

        <?php get_template_part( 'content', 'none' ); ?>

    <?php endif; ?>


    <?php get_footer(); ?>
    <script>
        jQuery(document).ready(function(){

            if(jQuery('#pdfLinks').children().length == 0){
                jQuery('.pdfDoc').hide();
            }

            if(jQuery('#stuffPdfLinks').children().length == 0){
                jQuery('.stuffPdf').hide();
            }
        });
    </script>
    <?php   }
}   ?>


<?php


if($_require_validated_user == "on"){

    if(is_user_logged_in()){
             $_verified_user = get_user_meta(get_current_user_id(),"_verified_user",true);

             if($_verified_user == "on") {

                ?>
                <script type="text/javascript">
                  // jQuery("#varify_modal").show();
                </script>
                <div class="vipps_div verified_user"  style="display: none"></div>
                <?php
            }
    }
}
function get_childs($termmm){
    $childs1_array = array();
    foreach ($termmm as $childs1) {
        $term = get_term( $childs1->term_id, $taxonomy_name );
        $childs1_array[] = '<a href="'.get_term_link($term).'">'.$childs1->name.'</a>';
    }
    return $categories_list = join( ", ", $childs1_array ); 
}
?>
<script type="text/javascript">
    jQuery(".show-more-button-price-more").click(function(){

        jQuery(".extra_pricer").slideDown();
        jQuery(this).hide();
        jQuery(".show-more-button-price-less").show();
    })
     jQuery(".show-more-button-price-less").click(function(){

        jQuery(".extra_pricer").slideUp();
        jQuery(this).hide();
        jQuery(".show-more-button-price-more").show();
    })
</script>

<div class="loader_div"></div>


<!-- Custom coloring when only booking widget is visible -->
<?php
if(isset($_GET["hide"]) && $_GET["hide"] === "true") {
    echo '<style>

    .single-listing-page-titlebar, .left-side-divv-single{
        display: none;
    }
    
    .col-lg-4 { 
     
        min-height: 640px;
    }
    
header#header-container {
    
    display: none;
}


   
 /* Custom brand color on booking widget  */

 /*    .qtyTotal, .mm-menu em.mm-counter, .mm-counter, .category-small-box:hover, .option-set li a.selected, .pricing-list-container h4:after, #backtotop a, .chosen-container-multi .chosen-choices li.search-choice, .select-options li:hover, button.panel-apply, .layout-switcher a:hover, .listing-features.checkboxes li:before, .comment-by a.comment-reply-link:hover, .add-review-photos:hover, .office-address h3:after, .post-img:before, button.button, .booking-confirmation-page a.button.color, input[type="button"], input[type="submit"], a.button, a.button.border:hover, button.button.border:hover, table.basic-table th, .plan.featured .plan-price, mark.color, .style-4 .tabs-nav li.active a, .style-5 .tabs-nav li.active a, .dashboard-list-box .button.gray:hover, .change-photo-btn:hover, .dashboard-list-box a.rate-review:hover, input:checked + .slider, .add-pricing-submenu.button:hover, .add-pricing-list-item.button:hover, .custom-zoom-in:hover, .custom-zoom-out:hover, #geoLocation:hover, #streetView:hover, #scrollEnabling:hover, .code-button:hover, .category-small-box-alt:hover .category-box-counter-alt, #scrollEnabling.enabled, #mapnav-buttons a:hover, #sign-in-dialog .mfp-close:hover, .button.listeo-booking-widget-apply_new_coupon:before, #small-dialog .mfp-close:hover, .daterangepicker td.end-date.in-range.available, .daterangepicker .ranges li.active, .day-slot-headline, .add-slot-btn button:hover, .daterangepicker td.available:hover, .daterangepicker th.available:hover, .time-slot input:checked ~ label, .daterangepicker td.active, .daterangepicker td.active:hover, .daterangepicker .drp-buttons button.applyBtn, .uploadButton .uploadButton-button:hover {
        background-color: red !important;
    }
    .drp-calendar .prev, .drp-calendar .next{
        background: red !important;
    }

    .time-slotinput:empty ~ label:after, .time-slot label:before {
       
        background: #red !important;
      
    } 
    

    .drp-calendar .prev, .drp-calendar .next{
        background: red !important;
    }

    */
    
    </style>';
}
?>
