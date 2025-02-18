<?php
$template_loader = new Listeo_Core_Template_Loader;
$author_id=$post->post_author;
$args = array(
    'author'        =>  $author_id,
    'post_type'     =>  'listing',
    'post_status'   =>  'publish',
    'post_parent'   =>  '0',
    'exclude'      =>   $post->ID,
    'orderby'       =>  'rand',
    'order'         =>  'ASC',
    'posts_per_page'=>  3
);

$posts = get_posts( $args );
$gallery = (array) get_post_meta( $post->ID, '_gallery', true );
$taxPrice11 = get_post_meta($post->ID,'_tax',true);

if($taxPrice11 != ""){
   $taxPrice= ((int)$taxPrice11) / 100;
}else{
     $taxPrice= "none";
}
foreach($posts as $post1){

    $post_dd = get_postdata($post1->ID);
    if(isset($post_dd["Author_ID"])){
        $user_data = get_userdata($post_dd["Author_ID"]);
        $listing_ownername = $user_data->display_name; 
        $listing_ownerimage = get_avatar_url($post_dd["Author_ID"]);
    }else{
        $listing_ownername = ""; 
        $listing_ownerimage = "";
    }
    $listing_type = get_post_meta($post1->ID,'_listing_type',true ); 
    $is_featured = listeo_core_is_featured($post1->ID);
    $gallery = (array) get_post_meta( $post1->ID, '_gallery', true );
    $ids = array_keys($gallery);
?>
<div class="col-md-4 col-sm-4">
    <div class="inner_divv">
    <div style="margin-bottom: 0" class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post1); ?> >
        <div class="listing-item <?php if($is_featured){ ?>featured-listing<?php } ?>" style="cursor:default;">
            <!-- Image -->
            <div class="listing-item-image">

                <?php if($listing_ownername != "" && $listing_ownerimage != ""){ ?>
                     <div class="search_owner_top" style="display:none">
                         <img src="<?php echo $listing_ownerimage;?>">
                         <span style="display: none"><?php echo $listing_ownername;?></span>
                     </div>
                <?php } ?>

                <a href="<?php the_permalink($post1->ID); ?>" style="z-index:2;position:absolute;height:100%;width:100%;" target="_blank"><?php
                    $image = wp_get_attachment_image_src( $ids[0], 'listeo-gallery' ); ?>
                    <img class="fade" style="z-index:1;" src="<?php echo esc_attr($image[0]); ?>">
                </a>

                 <?php

                    $terms = get_the_terms( $post1->ID, 'listing_category' );
                    $terms_org = get_the_terms($post1->ID, $listing_type.'_category' );


                    //echo "<pre>"; print_r($terms_org); die;

                    if(isset($terms_org[1])){

                        $icon_svg = get_term_meta($terms_org[1]->term_id,"_icon_svg",true); 



                        if($icon_svg != ""){
                            $_icon_svg_image = wp_get_attachment_url($icon_svg,'medium'); 
                            $icon_svg = listeo_render_svg_icon($icon_svg);

                            //$icon_svg = "<img src='".$_icon_svg_image."' />";
                        }else{
                            $icon = get_term_meta($terms_org[1]->term_id,'icon',true);
                            if($icon != ""){
                                $icon_svg = '<i class="'.$icon.'"></i>';
                            }else{
                                $icon_svg = "";
                            }
                        }


                        echo '<span class="tag cat_tag">'.$icon_svg.$terms_org[1]->name.'</span>';

                    }elseif(isset($terms_org[0])){

                        $icon_svg = get_term_meta($terms_org[0]->term_id,"_icon_svg",true); 

                        if($icon_svg != ""){
                            $_icon_svg_image = wp_get_attachment_url($icon_svg,'medium'); 
                            $icon_svg = listeo_render_svg_icon($icon_svg);

                           // $icon_svg = "<img src='".$_icon_svg_image."' />";
                        }else{
                            $icon = get_term_meta($terms_org[0]->term_id,'icon',true);
                            if($icon != ""){
                                $icon_svg = '<i class="'.$icon.'"></i>';
                            }else{
                                $icon_svg = "";
                            }
                        }

                        echo '<span class="tag cat_tag">'.$icon_svg.$terms_org[0]->name.'</span>';
                    }else{



                    }
                    if ( $terms && ! is_wp_error( $terms ) ) :

                        if(isset($terms[1])){

                            $icon_svg = get_term_meta($terms[1]->term_id,"_icon_svg",true); 

                            if($icon_svg != ""){
                                $_icon_svg_image = wp_get_attachment_url($icon_svg,'medium'); 
                                $icon_svg = listeo_render_svg_icon($icon_svg);

                               // $icon_svg = "<img src='".$_icon_svg_image."' />";
                            }else{
                                    $icon = get_term_meta($terms[1]->term_id,'icon',true);
                                    if($icon != ""){
                                        $icon_svg = '<i class="'.$icon.'"></i>';
                                    }else{
                                        $icon_svg = "";
                                    }
                            }

                            echo '<span class="tag cat_tag">'.$icon_svg.$terms[1]->name.'</span>';

                        }else{
                            $main_term = array_pop($terms);
                            $icon_svg = get_term_meta($main_term->id,"_icon_svg",true); 

                            if($icon_svg != ""){
                                $_icon_svg_image = wp_get_attachment_url($icon_svg,'medium'); 
                                $icon_svg = listeo_render_svg_icon($icon_svg);

                                //$icon_svg = "<img src='".$_icon_svg_image."' />";
                            }else{
                                    $icon = get_term_meta($main_term->id,'icon',true);
                                    if($icon != ""){
                                        $icon_svg = '<i class="'.$icon.'"></i>';
                                    }else{
                                        $icon_svg = "";
                                    }
                            }

                            echo '<span class="tag cat_tag">'.$icon_svg.$main_term->name.'</span>';
                        }
                        
                    endif; 

                ?>

            </div>
        </div>
    </div>
    <div class="card-body" style="text-align:center;">
        <div class="row">
            <div class="col-md-10 tittle_col">
                <h3>

                    <?php
                    $titlee = (strlen(get_the_title($post1->ID)) > 30) ? substr(get_the_title($post1->ID),0,30).'...' : get_the_title($post1->ID);
                    ?>
                    <?php echo $titlee; ?>

                    <?php if( get_post_meta($post1->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?>

                </h3>
            </div>
            <div class="col-md-2">
                 
            </div>
            <div class="col-md-10">
                
                <?php

                if(!get_option('listeo_disable_reviews')){

                    $rating = get_post_meta($post1->ID, 'listeo-avg-rating', true);

                    if(isset($rating) && $rating > 0 ) :

                        $rating_type = get_option('listeo_rating_type','star');

                        if($rating_type == 'numerical') { ?>

                            <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                                <?php $number = listeo_get_reviews_number($post1->ID);  ?>

                                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>

                            </div>

                        <?php } else { ?>

                            <div class="star-rating" data-rating="<?php echo $rating; ?>">
                                <?php $number = listeo_get_reviews_number($post1->ID);  ?>

                                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>

                            </div>

                        <?php } ?>

                            

                <?php endif;
                

                }?>
                 <div class="row row_featured">
                    <?php  $feature_html = array();
                     error_reporting(E_ALL);
                     ini_set('display_errors', 0);

                        $list_cats = array();
                        foreach ($terms_org as $key => $terms_o) {
                            $list_cats[] = $terms_o->term_id;
                        }

                        $autobook = get_post_meta($post1->ID,'_instant_booking',true); 
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
                                    $get_address = get_post_meta($post1->ID,"_address",true);
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
                                            $cll = "col-md-12 col-xs-12";
                                        }
                                        $tittle = strlen($tittle) > 40 ? substr($tittle,0,40)."..." : $tittle;


                                        $feature_html[1]["html"] = '<div class="'.$cll.' listing-small-badge  rglstntxtbx " style="padding: 0;">
                                                                '.$_icon_svg .'
                                                                <span>'.$tittle.'</span>
                                                            </div>';
                                        $feature_html[1]["order"] = $order_number; 
                                    }                   

                                   
                                }

                            }

                     }
                     ?>


                    <!-- End address -->

                    
                                    <!-- capacity_feature -->

                                     <?php 

                                     $capacity = get_post_meta($post1->ID,"_standing",true);
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

                                    $price_min = get_post_meta( $post1->ID, '_price_min', true );
                                    $price_max = get_post_meta( $post1->ID, '_price_max', true );
                                    $decimals = get_option('listeo_number_decimals',2);
                                    if(!empty($price_min) || !empty($price_max)) {
                                        if (is_numeric($price_min)) {
                                            $price_min_raw = number_format_i18n($price_min,$decimals);
                                            if($taxPrice != "none"){
                                               $price_min_raw = ($price_min_raw * $taxPrice) + $price_min_raw;
                                            }
                                        } 
                                        if (is_numeric($price_max)) {
                                            $price_max_raw = number_format_i18n($price_max,$decimals);
                                            if($taxPrice != "none"){
                                                $price_max_raw = ($price_max_raw * $taxPrice) + $price_max_raw;
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
                                                            $cll = "col-md-12 col-xs-12";
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

                                    $_event_date = get_post_meta( $post1->ID, '_event_date', true );
                                    $_event_date_end = get_post_meta( $post1->ID, '_event_date_end', true );
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

                                    $_event_date = get_post_meta( $post1->ID, '_event_date', true );
                                    $_event_date_end = get_post_meta( $post1->ID, '_event_date_end', true );
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

                                     $_event_tickets = get_post_meta($post1->ID,"_event_tickets",true);
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

                                             $_listing_only_for_group = get_post_meta($post1->ID,"_listing_only_for_group");
                                             
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
        </div>
        <div class="bookmark_div">

            <?php

                if( listeo_core_check_if_bookmarked($post1->ID) ) {

                $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>

                <span class="like-icon listeo_core-unbookmark-it liked"

                data-post_id="<?php echo esc_attr($post1->ID); ?>"

                data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

            <?php } else {

                if(is_user_logged_in()){

                    $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>

                    <span class="save listeo_core-bookmark-it like-icon"

                    data-post_id="<?php echo esc_attr($post1->ID); ?>"

                    data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

                <?php } else { ?>
                    <span class="save like-icon tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></span>
                <?php } ?>
            <?php } ?>

        </div>

    </div>
    </div> 
</div>
<?php }