<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(is_user_logged_in()){
?>
    <style type="text/css">
        body.logged-in div#logo {
            display: block !important;
        }
        .menu-last, #kt_header_menu_mobile_toggle{
            display: none !important;
        }
        .left-side #logo {
            top: 0px !important;
        }
        body .main-nav {
            display: block !important;
            left: 49px;
        }
    </style>
<?php }else{ ?>
    <style type="text/css">
        .mobile-header, .get_your_trial{
            display: none !important;
        }
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
    </style>
<?php } 
$template_loader = new Listeo_Core_Template_Loader;

get_header(get_option('header_bar_style','standard') );

$gallery_style = get_post_meta( $post->ID, '_gallery_style', true );

if(empty($gallery_style)) { $gallery_style = get_option('listeo_gallery_type','top'); }

$count_gallery = listeo_count_gallery_items($post->ID);

if($count_gallery < 4 ){
    $gallery_style = 'content';
}
if( get_post_meta( $post->ID, '_gallery_style', true ) == 'top' && $count_gallery == 1 ) {
    $gallery_style = 'none';
}

$_hide_price_div = get_post_meta($post->ID,'_hide_price_div',true);

$_hide_price_div_single_listing = get_post_meta($post->ID,'_hide_price_div_single_listing',true);

$cr_user = get_current_user_id();

$group_admin = get_group_admin();

if($group_admin != ""){
    $cr_user = $group_admin;
}
$show_widget = false;
if($cr_user == $post->post_author){
   $show_widget = true;
}


if ( have_posts() ) :
if( $gallery_style == 'top' ) : ?>

                    <div id="topKategoriDisplayer" style="display:none;flex-direction:row;height:45px;border-bottom:1px solid rgba(0,0,0,.09);">
                        <a style="z-index:500;float:left;padding:5px 20px 0px 20px;pointer-events:auto;line-height:40px;" onclick="window.history.back();"><i class="fa fa-chevron-left"></i></a>
                        <p  onclick="showChooseCategories()" style="height:100%;text-align:center;width:100%;margin-left:-50px;line-height:45px">
                            <?php
                            $titlee = (strlen(get_the_title()) > 200) ? substr(get_the_title(),0,200).'...' : get_the_title();
                            ?>
                            <?php echo $titlee; ?>
                        </p>
                    </div>
                    <style type="text/css">
                     .mobile_div {
                        padding-top: 13px;
                      }

                    </style>

<?php     $template_loader->get_template_part( 'single-partials/single-listing','gallery' );
else: ?>
    <!-- Gradient-->
    <div class="single-listing-page-titlebar" style="display:none"></div>
<?php endif; ?>
<?php
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

$post_dd = get_postdata($post->ID);
if(isset($post_dd["Author_ID"])){
    $user_data = get_userdata($post_dd["Author_ID"]);
    $listing_ownername = $user_data->display_name; 
    $listing_ownerimage = get_avatar_url($post_dd["Author_ID"]);
}else{
    $listing_ownername = ""; 
    $listing_ownerimage = "";
}
?>
<?php
$listeo_hide_searchbar_single_listing = get_post_meta($post->ID,'listeo_hide_searchbar_single_listing',true);
if($listeo_hide_searchbar_single_listing){
?>
<style type="text/css"> 
#listeo_core-search-form{
    display: none;
}
.right-side-searchbar .fa-search {
    display: none !important;
}

</style>
<?php  
}
?>

<?php if(isset($_GET["hide"]) && $_GET["hide"] == true){ ?>
    <style type="text/css">
        #header, .listing-slider, .single_listing{
            display: none;
        }
        .listing-share{
            display: none;
        }
        .single-listing-page-titlebar{
            background: none;
        }
    </style>
<?php } ?>
<?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>

            <div class="col-lg-4 col-md-4 col-lg-push-8  margin-top-20 sticky">

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
            ?>
            <?php get_sidebar('listing'); ?>

            </div>
        <?php } ?>    
<!-- Content
================================================== -->
<div class="container notification_div">
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
<div class="container mobile_div single_listing">
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
            .borettslag_widget  {
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
            .info-box-new  {
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
            if($_hide_logo_for_listing == "on"){
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

        
        <?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>
        <?php while ( have_posts() ) : the_post();  ?>
        <div class="col-lg-8 col-md-8 col-lg-pull-4 padding-right-30">

            <div class="row top_owner_div" style="display:none">
                <div class="col-xs-10">
                    <?php if($listing_ownername != "" && $listing_ownerimage != ""){ ?>
                         <div class="owner_top">
                             <img src="<?php echo $listing_ownerimage;?>">
                             <span><?php echo $listing_ownername;?></span>
                         </div>
                    <?php } ?>
                    
                </div>
                <div class="col-xs-2">
           
                    
                </div>
            </div>    

             <!-- Content
                ================================================== -->
                <?php
                if($gallery_style == 'none' ) :
                    $gallery = get_post_meta( $post->ID, '_gallery', true );
                    if(!empty($gallery)) :

                        foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                            $image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
                          //  echo '<img src="'.esc_url($image[0]).'" class="single-gallery margin-bottom-40" style="margin-top:-30px;"></a>';
                        }

                    endif;
                endif;
                if ($count_gallery > 1 && $count_gallery < 4){
                    $template_loader->get_template_part( 'single-partials/single-listing','gallery-custom' );
                } elseif ($count_gallery == 1){
                    $template_loader->get_template_part( 'single-partials/single-listing','gallery-custom' );
                }?>

            <!-- Titlebar -->
            <div id="titlebar" class="listing-titlebar">
                <div class="listing-titlebar-title">
                    <div class="row">
                        <div class="col-xs-10  margin-bottom-1">
                            <h3>
                            <?php
                            $titlee = (strlen(get_the_title()) > 200) ? substr(get_the_title(),0,200).'...' : get_the_title();
                            ?>
                            <?php echo $titlee; ?>
                            <br>
                            <?php
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
                                    <div class="rating-counter"><a href="#listing-reviews">(<?php printf( _n( '%s review', '%s review', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</a></div>
                                </div>
                                <?php endif;
                            }?>
                            <hr style=" visibility:hidden; margin-bottom:0px; margin-top:-8px; " />
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
                                <span class="listing-tag col-md-12" style="margin: 0">
                                <?php  echo ( $categories_list ) ?>
                            </span>
                            <?php endif; ?>

                            </h3>
                            <?php /*$listing_type = get_post_meta( get_the_ID(), '_listing_type', true);
                            switch ($listing_type) {
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
                            }*/
                            $type_terms = get_the_terms( get_the_ID(), 'service_category' );
                                    $taxonomy_name = 'service_category';
                            if( isset($type_terms) ) {
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
                               <!--  <span>
                                    <a href="#listing-location" class="listing-address">
                                        <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                                        <?php the_listing_address(); ?>
                                    </a>
                                </span> -->
                            <?php endif; ?>

                            
                        </div>
                        <!-- <div class="col-xs-2 share_bk"> -->

                            <?php
            
                            if( listeo_core_check_if_bookmarked($post->ID) ) {
            
                            $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>
            
                         <!--    <i class="fa fa-heart like-icon1 listeo_core-unbookmark-it liked"
            
                            data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                            data-nonce="<?php echo esc_attr($nonce); ?>" ></i> -->
            
                        <?php } else {
            
                            if(is_user_logged_in()){
            
                                $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>
            
                              <!--   <i class="fa fa-heart save listeo_core-bookmark-it like-icon1"
            
                                data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                                data-nonce="<?php echo esc_attr($nonce); ?>" ></i> -->
            
                            <?php } else { ?>
                                <i class="fa fa-heart save like-icon1 tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></i>
                            <?php } ?>
                        <?php } ?>

                          <!--   <img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/share_inactive.svg" style="display:none" /> -->

                        </div>
                        <div class="col-xs-12">

                            <!-- start -->
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
                                                        $cll = "col-md-12 col-xs-12";
                                                    }


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
                                                                            $cll = "col-md-12 col-xs-12";
                                                                        }


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

                                                                    /*$price_min = $price_min * $taxPrice;
                                                                    $price_max = $price_max * $taxPrice;*/

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
                                                                            $cll = "col-md-12 col-xs-12";
                                                                        }


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
                                                                            $cll = "col-md-12 col-xs-12";
                                                                        }


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
                                                                            $cll = "col-md-12 col-xs-12";
                                                                        }


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
                                                                            $cll = "col-md-12 col-xs-12";
                                                                        }


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

                            <!-- end -->
                        </div>


                    </div>
                    <hr style="visibility:hidden; margin-bottom:0px; " />
                    
                    <?php  $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
                           if($autobook == 'on'){
                           ?>
               <!--  <div class="listing-small-badge pricing-badge rgdivbk" style="padding: 0; margin-bottom: 20px;box-shadow:none">
                            <img style="width: 20px;height: 20px;margin: 2px 0 6px 0;" src="/wp-content/fonts/custom_icons/bolt_green_circle.svg" alt="lightning bolticon">
                            <span style="font-size: 17px;padding-left: 5px;">Automatisk booking</span>
                        </div> -->
                        <?php } ?>
                   

                </div>
               <hr style="visibility:hidden; margin-bottom:-19px; " />

<!-- 
               <hr> -->

                <!-- Overview -->
                <div id="listing-overview" class="listing-section">
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','main-details' );  ?>
                    <!-- Description -->

                    <?php the_content(); ?>
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','socials' );  ?>

                    <?php if(get_post_custom_values($key = '_category')[0] == "utstr"):
                        include("single-partials/equipment-calendar.php");
                    endif;?>

                    <?php if(!empty(get_post_custom_values($key = '_standing')[0])
                        || !empty(get_post_custom_values($key = '_classroom')[0])
                        || !empty(get_post_custom_values($key = '_banquet')[0])
                        || !empty(get_post_custom_values($key = '_coronares')[0])
                        || !empty(get_post_custom_values($key = '_captest')[0])
                        || !empty(get_post_custom_values($key = '_theatre')[0])
                        || !empty(get_post_custom_values($key = '_horseshoe')[0])
                        || !empty(get_post_custom_values($key = '_squarefeet')[0])
                    ){ ?>


                        <div id="listing-capacity-list" class="listing-section capacity" style="width: 97%;">
                            <h3 class="listing-desc-headline margin-top-70 margin-bottom-30">Kapasitet</h3>
                            <div class="row" style="padding: 10px 0px 10px 0px;">
                                <?php if(isset(get_post_custom_values($key = '_standing')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 standing" style="text-align:center;">
                                        <img style="width: 15px;" src="/wp-content/uploads/2020/10/standing.svg" alt="standing">
                                        <h5>Maks kapasitet</h5>
                                        <h5><?php echo get_post_custom_values($key = '_standing')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_classroom')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 classroom" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/classroom.svg" alt="classroom">
                                        <h5>Klasserom</h5>
                                        <h5><?php echo get_post_custom_values($key = '_classroom')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_banquet')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 banquet" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/banquet.svg" alt="banquet">
                                        <h5>Banquet</h5>
                                        <h5><?php echo get_post_custom_values($key = '_banquet')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_coronares')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 coronares" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/coronares5.svg" alt="coronares5">
                                        <h5>Korona plass</h5>
                                        <h5><?php echo get_post_custom_values($key = '_coronares')[0]; ?></h5>
                                        <i style="left: 10%; position: absolute;" class="tip" data-tip-content="Dette er kapasiteten til lokalet med dagens korona restriksjoner"></i>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row" style="padding: 10px 0px 10px 0px;">
                                <?php if(isset(get_post_custom_values($key = '_captest')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 sitting" style="text-align:center;">
                                        <img style="width: 20px;" src="/wp-content/uploads/2020/10/sitting.svg" alt="sitting">
                                        <h5>Sittende</h5>
                                        <h5><?php echo get_post_custom_values($key = '_captest')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_theatre')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 theatre" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/theatre.svg" alt="theatre">
                                        <h5>Teater</h5>
                                        <h5><?php echo get_post_custom_values($key = '_theatre')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_horseshoe')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 horseshoe" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/horseshoe.svg" alt="horseshoe">
                                        <h5>Banquet</h5>
                                        <h5><?php echo get_post_custom_values($key = '_horseshoe')[0]; ?></h5>
                                    </div>

                                <?php } ?>

                                <?php if(isset(get_post_custom_values($key = '_squarefeet')[0])){ ?>
                                    <div class="col-md-3 col-xs-3 squarefeet" style="text-align:center;">
                                        <img style="width: 30px;" src="/wp-content/uploads/2020/10/kvm.svg" alt="squarefeet">
                                        <h5>Areal</h5>
                                        <h5><?php echo get_post_custom_values($key = '_squarefeet')[0]; ?></h5>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>

                    <?php } ?>
                    
                </div>
                <div id="listing-feature-list" class="listing-section features features_sec" style="margin-top: 0px">
                    <h3 class="listing-desc-headline margin-bottom-30">Tilbyr</h3>
                    <?php $template_loader->get_template_part( 'single-partials/single-listing','featuresNew' );  ?> 
                </div>    

                <?php // $template_loader->get_template_part( 'single-partials/single-listing','features' );  ?>

               
                <?php  

                $slots_type = get_post_meta(get_the_ID(),"slots_type",true);

                if($slots_type == "standard"){
                    $_hide_price_div = "on";
                }
                $empty_price = true;
                
                if (($_hide_price_div != "on") && ($_hide_price_div_single_listing != "on")) { ?>
                <div id="listing-pricing-list" class="listing-section time-price" style="margin-top: 30px">
                    <?php
                    if( (get_post_custom_values($key = '_hour_price')[0] !== '') || 
                        (get_post_custom_values($key = '_normal_price')[0] !== '') || 
                        (get_post_custom_values($key = '_weekday_price')[0] !== '')
                    ): 
                    ?>
                        <h3 class="listing-desc-headline margin-top-30 margin-bottom-30">Priser</h3>
                    <?php endif;?>
                    <!-- <h3 class="listing-desc-headline margin-bottom-30">Standard priser</h3> -->
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
                                        <h5 style="text-transform: capitalize;"><?php echo $days_list[$from_day]." ".$from_time;?> to <?php echo $days_list[$to_day]." ".$to_time;?> </h5>
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
                                ?>
                                <li <?php echo $hour_show;?>>
                                    <h5>Timepris</h5>
                                    <span class="js-hour-price" data-price="<?php
                                    echo get_post_custom_values($key = '_hour_price')[0]; ?>" >
                                    <?php
                                    if(get_post_custom_values($key = '_hour_price')[0] == '0'){
                                        echo "GRATIS";
                                    }else{
                                        echo $timePriceWithTax." kr ".$tax_txt;
                                    }
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

                                if( (get_post_custom_values($key = '_normal_price')[0] !== '') && (get_post_custom_values($key = '_normal_price')[0] !== '0')):?>
                                <li <?php echo $normal_show;?>>
                                    <h5>Dagspris Man-Fre</h5>
                                        <span class="js-daily-price" data-price="<?php
                                        
                                        echo get_post_custom_values($key = '_normal_price')[0]; ?>"><?php echo $dailyPriceWithTax; ?> kr <?php echo $tax_txt;?></span>
                                </li>
                                <?php endif;
                                $weeklyPriceWithTax = round(($taxPrice * get_post_custom_values($key = '_weekday_price')[0])+get_post_custom_values($key = '_weekday_price')[0]);
                                if( get_post_custom_values($key = '_weekday_price')[0] != ''){
                                    $weekday_show = "";
                                }else{
                                    $weekday_show = "style='display:none'";
                                }
                                if($weeklyPriceWithTax < 1){
                                    $weekday_show = "style='display:none'";
                                }
                                if( (get_post_custom_values($key = '_weekday_price')[0] !== '') && (get_post_custom_values($key = '_weekday_price')[0] !== '0')):?>
                                <li <?php echo $weekday_show;?>>
                                    <h5>Dagspris Lør-Søn</h5>
                                        <span class="js-weekly-price" data-price="<?php
                                        
                                        echo get_post_custom_values($key = '_weekday_price')[0]; ?>"><?php echo $weeklyPriceWithTax; ?> kr <?php echo $tax_txt;?></span>
                                </li>
                                <?php endif; ?>
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

                 <?php $template_loader->get_template_part( 'single-partials/single-listing','video' );  ?>

                <?php $template_loader->get_template_part( 'single-partials/single-listing','opening' );  ?>
                <?php $template_loader->get_template_part( 'single-partials/single-listing','location' );  ?>
                <div class="stuffPdf" style="margin-bottom: 30px;margin-top: 25px;">
                    <h4 style="margin-bottom: 30px;">Tilleggsinformasjon</h4>
                    <hr/>
                    <div id="stuffPdfLinks">
                        <?php for ($i = 0; $i < 5; $i++) {
                            $link = get_post_custom_values($key = '_stuff_pdf_dokument'.$i)[0];
                            $path = explode("/", $link);
                            $linkName = end($path);
                            if($link != null){ ?>
                                <i class='fa fa-file-pdf'></i><span style='margin-left:5px;'><a style='color:#4982B9;' target='_blank' href='<?php echo $link?>'><?php echo $linkName ?></a></span><br>
                            <?php }
                        }?>
                    </div>
                </div>

                <div class="pdfDoc" style="margin-bottom: 30px;">
                    <h4 style="margin-bottom: 30px;font-weight: bolder">Vedlegg</h4>
                    <hr/>
                    <div id="pdfLinks">
                        <?php for ($i = 0; $i < 10; $i++) {
                            $link = get_post_custom_values($key = '_pdf_document'.$i)[0];
                            $path = explode("/", $link);
                            $linkName = end($path);
                            if($link != null){ ?>
                                <i class='fa fa-file-pdf'></i><span style='margin-left:5px;'><a style='color:#4982B9;' target='_blank' href='<?php echo $link?>'><?php echo $linkName ?></a></span><br>
                            <?php }
                        }?>
                    </div>
                </div>

                <div class="categoryName" style="display:none;">
                    <span data-cat="<?php echo get_post_custom_values($key = '_category')[0]; ?>"></span>
                </div>
                <div class="buttonLink" style="display:none;">
                    <span data-cat="<?php echo get_post_custom_values($key = '_buttonLink')[0]; ?>"></span>
                </div>
                <div class="buttonName" style="display:none;">
                    <span data-cat="<?php echo get_post_custom_values($key = '_buttonName')[0]; ?>"></span>
                </div>
            </div>
            <?php endwhile; // End of the loop. ?>
            <?php } ?>

            <!-- Sidebar
================================================== -->
            <?php
            if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
            ?>
        
            <!-- Sidebar / End -->

            <div class="col-lg-8 col-md-8 col-lg-pull-4 padding-right-30">
            <?php if(!get_option('listeo_disable_reviews')){
                $template_loader->get_template_part( 'single-partials/single-listing','reviews' ); } ?>
            </div>
            <?php } ?>

        </div>
    </div>
    <?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>

        <div class="container related_list" style="margin-bottom: 10%;">
            <div class="row text-center margin-bottom-30">
                <h3>Se annet</h3>
            </div>
            <?php
            $template_loader->set_template_data( $post )->get_template_part( 'listing-cards','listing-cards' );
            ?>
        </div>
    <?php } ?>

    <?php else : ?>

        <?php get_template_part( 'content', 'none' ); ?>

    <?php endif; ?>

    <?php
        if(($post->post_status != "draft" && $post->post_status != "pending" && $post->post_status != "trash" && $post->post_status != "expired") || ($show_widget == true && ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "expired"))){ 
        ?>


        <?php //get_footer('listing'); ?>
    <?php } ?>
    <script>
        jQuery('#add-review').css('margin-top','0px');
        jQuery('#listing-video h3').css('margin-top','20px');
        jQuery('#listing-video h3').css('margin-top','20px');
        jQuery('#listing-gallery h3').css('margin-top','20px');
        jQuery('#listing-location h3').css('margin-top','20px');

        <?php for ($i = 0; $i < 10; $i++) {
        $link = get_post_custom_values($key = '_pdf_document'.$i)[0];
        if($link != null){ ?>
        localStorage.setItem('_pdf_document'+<?php echo $i ?>, '<?php echo $link?>',);
        <?php }else{?>
        localStorage.setItem('_pdf_document'+<?php echo $i ?>, '');
        <?php }
        }?>

        if(jQuery('#pdfLinks').children().length == 0){
            jQuery('.pdfDoc').hide();
        }

        if(jQuery('#stuffPdfLinks').children().length == 0){
            jQuery('.stuffPdf').hide();
        }

    </script>
<div style="display: none">
    <?php get_footer();?>
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
</div>    
<?php
function custom_inline_styles() {
    echo '
    <style>
        .left_side_text {
            display: none;
        }
        div#kt_header_menu_mobile_toggle {
            display: none;
        }
        a.xoo-el-login-tgr.login_reg_popup {
            display: none;
        }

        .trp-language-switcher > div > a.trp-ls-shortcode-disabled-language {
            margin-right: 11px !important;
        }
        #header-new .left-side {
            width: 100%;
        }
        body .trp-ls-shortcode-current-language {
            width: 100% !important;
        }
        .main-nav:not(.main-nav-small){
            right: -35px;
            margin: 0px !important;
        }
        .left-side .main-nav a {
            font-size: 16px;
            font-weight: 500;
            width: 56% !important;
        }
        .mobile-header .menu-toggle2 {
        
        display: none;
         }
         .left-side #logo img {
        margin-top: 0px !important;
         }

    </style>
    ';
}
add_action('wp_head', 'custom_inline_styles'); // For front-end

// Uncomment the line below if you also want to apply it to the admin area
// add_action('admin_head', 'custom_inline_styles'); // For admin area
