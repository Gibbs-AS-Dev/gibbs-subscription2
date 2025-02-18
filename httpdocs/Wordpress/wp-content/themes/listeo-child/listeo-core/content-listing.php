<?php

$template_loader = new Listeo_Core_Template_Loader;

$is_featured = listeo_core_is_featured($post->ID);

$listing_type = get_post_meta( $post->ID,'_listing_type',true ); 

$clientAuthor = $_GET['authorid'];
if($_SERVER['SERVER_NAME'] != 'gibbs.no'){
    if(isset($clientAuthor)){
        if($post->post_author == $clientAuthor){
            ?>
            
            <!-- Listing Item -->
            
                <div class="col-lg-12 col-md-12">
            
                    <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> >
            
                        <div class="listing-item <?php if($is_featured){ ?>featured-listing<?php } ?>" style="cursor:default;">
            
                             <div class="listing-small-badges-container">
            
                                <?php if($is_featured){ ?>
            
                                    <div class="listing-small-badge featured-badge"><i class="fa fa-star"></i> <?php esc_html_e('Featured','listeo_core'); ?></div><br>
            
                                <?php } ?>
                            </div>
            
                            <!-- Image -->
            
                            <div class="listing-item-image">
            
                                <?php $template_loader->get_template_part( 'content-listing-search-image');  ?>
            
                                <?php

                                $terms = get_the_terms( get_the_ID(), 'listing_category' );
                                //$terms_org = get_the_terms( get_the_ID(), $listing_type.'_category' );
                                $terms_org = get_the_terms( get_the_ID(), 'service_category' );

                                if(isset($terms_org[1])){

                                    echo '<span class="tag">'.$terms_org[1]->name.'</span>';

                                }elseif(isset($terms_org[0])){
                                    echo '<span class="tag">'.$terms_org[0]->name.'</span>';
                                }else{



                                }
                                if ( $terms && ! is_wp_error( $terms ) ) :

                                    if(isset($terms[1])){

                                        echo '<span class="tag">'.$terms[1]->name.'</span>';

                                    }else{
                                        $main_term = array_pop($terms);

                                        echo '<span class="tag">'.$main_term->name.'</span>';
                                    }
                                    
                                endif; 

                                ?>
            
                            </div>
                            <!-- Content -->
            
                    <a href="<?php the_permalink(); ?>" class="listing-item-content" target="_blank">
            
                        <?php if( $listing_type  == 'service' && get_post_meta( $post->ID,'_opening_hours_status',true )) {
            
                                if( listeo_check_if_open() ){ ?>
            
                                    <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>
            
                                <?php } else {
            
                                    if( listeo_check_if_has_hours() ) { ?>
            
                                        <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>
            
                                    <?php } ?>
            
                            <?php }
            
                        }?>
            
                        <div class="listing-item-inner">
            
                            <h3>
            
                                <?php
                                $titlee = (strlen(get_the_title()) > 90) ? substr(get_the_title(),0,90).'...' : get_the_title();
                                ?>
                                <?php echo $titlee; ?>
            
                                <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?>
            
                            </h3>
            
                            <span><?php the_listing_location_link($post->ID, false); ?></span>
            
            
            
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
            
                                        <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>
            
                                    </div>
            
                            <?php endif;
            
                            }?>
                            <?php if($listing_type  == 'event' || get_the_listing_price_range() ) : ?>
            
                            <div class="listing-list-small-badges-container">
                            <?php  endif; ?>
                                <?php if(get_the_listing_price_range()): ?>
                                    <div class="listing-small-badge pricing-badge">
                                    <img src="/wp-content/fonts/custom_icons/tag_green_circle.svg" alt="price tag icon">
                                        <?php echo get_the_listing_price_range(); ?></div>
                                <?php endif; ?>
                                <?php if(isset(get_post_custom_values($key = '_coronares')[0]) || isset(get_post_custom_values($key = '_standing')[0])){ ?>
                                    <div class="row" style="margin: 5px 0 0px 0px">
                                        <?php if(isset(get_post_custom_values($key = '_standing')[0]) and !empty(get_post_custom_values($key = '_standing')[0])){?>
                                            <div class="col-md-12 listing-small-badge pricing-badge">
                                            <img src="/wp-content/fonts/custom_icons/users_green_circle.svg" alt="users icon">
                                                <span><?php echo get_post_custom_values($key = '_standing')[0] ?> - maks kapasitet</span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row" style="margin: 2px 0 0px 0px">
                                        <?php if(isset(get_post_custom_values($key = '_coronares')[0]) and !empty(get_post_custom_values($key = '_coronares')[0])){ ?>
                                            <div class="col-md-12 listing-small-badge pricing-badge" style="padding: 0">
                                            <img src="/wp-content/fonts/custom_icons/virus_green_circle.svg" alt="virus icon">
                                                <span><?php echo get_post_custom_values($key = '_coronares')[0] ?> - korona kapasitet</span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php
            
                                if( $listing_type  == 'event') {
            
                                    $date_format = listeo_date_time_wp_format_php();
            
                                    $_event_datetime = get_post_meta($post->ID,'_event_date', true);
                                    if($_event_datetime) {
                                           $_event_date = list($_event_datetime) = explode(' ', $_event_datetime);
            
                                        if($_event_date) :
            
                                           //Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator is a dash (-) or a dot (.), then the European d-m-y format is assumed.
            
                                            if(substr($date_format, 0, 1) === 'd'){
            
                                                $_event_date[0] = str_replace('/', '-', $_event_date[0]);
            
                                            }
            
                                            ?>
            
                                        <div class="listing-small-badge"><i class="fa fa-calendar-check-o"></i><?php echo esc_html(date($date_format, strtotime($_event_date[0]))); ?></div> <br>
            
                                        <?php endif;
            
                                    }
            
                                }  ?>
            
                            <?php if($listing_type  == 'event' || get_the_listing_price_range() ) : ?>
                            </div>
                            <?php  endif; ?>
                        </div>
            
                        <?php
            
                            if( listeo_core_check_if_bookmarked($post->ID) ) {
            
                            $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>
            
                            <span class="like-icon listeo_core-unbookmark-it liked"
            
                            data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                            data-nonce="<?php echo esc_attr($nonce); ?>" ></span>
            
                        <?php } else {
            
                            if(is_user_logged_in()){
            
                                $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>
            
                                <span class="save listeo_core-bookmark-it like-icon"
            
                                data-post_id="<?php echo esc_attr($post->ID); ?>"
            
                                data-nonce="<?php echo esc_attr($nonce); ?>" ></span>
            
                            <?php } else { ?>
                                <span class="save like-icon tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></span>
                            <?php } ?>
                        <?php } ?>
            
                    </div>
                    </a>
                </div>
                </div>    

            
            <!-- Listing Item / End -->
                            <?php }
    }else{
        ?>
            <!-- Listing Item -->
            <div class="col-lg-12 col-md-12">

                <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> >

                    <div class="listing-item <?php if($is_featured){ ?>featured-listing<?php } ?>" style="cursor:default;">

                        <div class="listing-small-badges-container">

                            <?php if($is_featured){ ?>

                                <div class="listing-small-badge featured-badge"><i class="fa fa-star"></i> <?php esc_html_e('Featured','listeo_core'); ?></div><br>

                            <?php } ?>
                        </div>

                        <!-- Image -->

                        <div class="listing-item-image">

                            <?php $template_loader->get_template_part( 'content-listing-search-image');  ?>

                            <?php

                            $taxPrice11 = get_post_meta(get_the_ID(),'_tax',true);

                            $taxPrice11 = get_post_meta(get_the_ID(),'_tax',true);

                            if($taxPrice11 != ""){
                               $taxPrice= ((int)$taxPrice11) / 100;
                            }else{
                                 $taxPrice= "none";
                            }


                            $terms = get_the_terms( get_the_ID(), 'listing_category' );
                            $terms_org = get_the_terms( get_the_ID(), $listing_type.'_category' );


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

                                    //$icon_svg = "<img src='".$_icon_svg_image."' />";
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

                                      //  $icon_svg = "<img src='".$_icon_svg_image."' />";
                                    }else{
                                        $icon = get_term_meta($main_term->term_id,'icon',true);
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
                        <!-- Content -->

                <a href="<?php the_permalink(); ?>" class="listing-item-content" target="_blank">

                    <?php if( $listing_type  == 'service' && get_post_meta( $post->ID,'_opening_hours_status',true )) {

                            if( listeo_check_if_open() ){ ?>

                                <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>

                            <?php } else {

                                if( listeo_check_if_has_hours() ) { ?>

                                    <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>

                                <?php } ?>

                        <?php }

                    }?>

                    <div class="listing-item-inner">
                        <div class="row">
                            <div class="col-md-10 tittle_col">
                                <h3>

                                   <?php
                                    $titlee = (strlen(get_the_title()) > 90) ? substr(get_the_title(),0,90).'...' : get_the_title();
                                    ?>
                                    <?php echo $titlee; ?>

                                    <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?>

                                </h3>
                            </div>
                            <div class="col-md-2">
                                 
                            </div>
                            <div class="col-md-10">

                           
                      

                        

                                <span style="display: none"><?php the_listing_location_link($post->ID, false); ?></span>

                              

                                <?php

                                if(!get_option('listeo_disable_reviews')){

                                    $rating = get_post_meta($post->ID, 'listeo-avg-rating', true);

                                    if(isset($rating) && $rating > 0 ) :

                                        $rating_type = get_option('listeo_rating_type','star');

                                        if($rating_type == 'numerical') { ?>

                                            <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                                                <?php $number = listeo_get_reviews_number($post->ID);  ?>

                                                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>

                                            </div>

                                        <?php } else { ?>

                                            <div class="star-rating" data-rating="<?php echo $rating; ?>">
                                                <?php $number = listeo_get_reviews_number($post->ID);  ?>

                                                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>

                                            </div>

                                        <?php } ?>

                                            

                                <?php endif;
                                

                                }?>
                                <?php if(get_the_listing_address()): ?>
                                    <!-- <span>
                                            <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                                            <?php //the_listing_address(); ?>
                                    </span> -->
                                <?php endif; ?>


                                <div class="row row_featured">
                                    <?php  $feature_html = array();

                                        $list_cats = array();
                                        foreach ($terms_org as $key => $terms_o) {
                                            $list_cats[] = $terms_o->term_id;
                                        }

                                        $autobook = get_post_meta(get_the_ID(),'_instant_booking',true); 
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

                                     $capacity = get_post_meta(get_the_ID(),"_standing",true);
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

                                    $price_min = get_post_meta( get_the_ID(), '_price_min', true );
                                    $price_max = get_post_meta( get_the_ID(), '_price_max', true );
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

                                    $_event_date = get_post_meta( get_the_ID(), '_event_date', true );
                                    $_event_date_end = get_post_meta( get_the_ID(), '_event_date_end', true );
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

                                    $_event_date = get_post_meta( get_the_ID(), '_event_date', true );
                                    $_event_date_end = get_post_meta( get_the_ID(), '_event_date_end', true );
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

                                     $_event_tickets = get_post_meta(get_the_ID(),"_event_tickets",true);
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

                                             $_listing_only_for_group = get_post_meta(get_the_ID(),"_listing_only_for_group");
                                             
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


                                

                                <?php  //$template_loader->get_template_part( 'single-partials/content-listing','featuresNew' );  ?> 
                                

                            </div>
                            

                        </div>


                    </div>

                    <div class="bookmark_div">

                        <?php

                            if( listeo_core_check_if_bookmarked($post->ID) ) {

                            $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>

                            <span class="like-icon listeo_core-unbookmark-it liked"

                            data-post_id="<?php echo esc_attr($post->ID); ?>"

                            data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

                        <?php } else {

                            if(is_user_logged_in()){

                                $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>

                                <span class="save listeo_core-bookmark-it like-icon"

                                data-post_id="<?php echo esc_attr($post->ID); ?>"

                                data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

                            <?php } else { ?>
                                <span class="save like-icon tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></span>
                            <?php } ?>
                        <?php } ?>

                    </div>


                   

                </div>

                </a>
            </div>    
            </div>    

        <?php
    }
}else{?>
    <!-- listing start -->
    <div class="col-lg-12 col-md-12">

    <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> >

        <div class="listing-item <?php if($is_featured){ ?>featured-listing<?php } ?>" style="cursor:default;">

            <div class="listing-small-badges-container">

                <?php if($is_featured){ ?>

                    <div class="listing-small-badge featured-badge"><i class="fa fa-star"></i> <?php esc_html_e('Featured','listeo_core'); ?></div><br>

                <?php } ?>
            </div>

            <!-- Image -->

            <div class="listing-item-image">

                <?php $template_loader->get_template_part( 'content-listing-search-image');  ?>

                <?php

                $terms = get_the_terms( get_the_ID(), 'listing_category' );
                $terms_org = get_the_terms( get_the_ID(), $listing_type.'_category' );

                if(isset($terms_org[1])){

                    echo '<span class="tag">'.$terms_org[1]->name.'</span>';

                }elseif(isset($terms_org[0])){
                    echo '<span class="tag">'.$terms_org[0]->name.'</span>';
                }else{



                }
                if ( $terms && ! is_wp_error( $terms ) ) :

                    if(isset($terms[1])){

                        echo '<span class="tag">'.$terms[1]->name.'</span>';

                    }else{
                        $main_term = array_pop($terms);

                        echo '<span class="tag">'.$main_term->name.'</span>';
                    }
                    
                endif; 

                ?>

            </div>
            <!-- Content -->

    <a href="<?php the_permalink(); ?>" class="listing-item-content" target="_blank">

        <?php if( $listing_type  == 'service' && get_post_meta( $post->ID,'_opening_hours_status',true )) {

                if( listeo_check_if_open() ){ ?>

                    <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>

                <?php } else {

                    if( listeo_check_if_has_hours() ) { ?>

                        <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>

                    <?php } ?>

            <?php }

        }?>

        <div class="listing-item-inner">

            <h3>

                <?php
                $titlee = (strlen(get_the_title()) > 90) ? substr(get_the_title(),0,90).'...' : get_the_title();
                ?>
                <?php echo $titlee; ?>

                <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?>

            </h3>

            <span><?php the_listing_location_link($post->ID, false); ?></span>



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

                        <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>

                    </div>

            <?php endif;

            }?>
            <?php if($listing_type  == 'event' || get_the_listing_price_range() ) : ?>

            <div class="listing-list-small-badges-container">
                <h3>Te go rabote</h3>
            <?php  endif; ?>
                <?php if(get_the_listing_price_range()): ?>
                    <div class="listing-small-badge pricing-badge">
                    <img src="/wp-content/fonts/custom_icons/tag_green_circle.svg" alt="price tag icon">
                        <?php echo get_the_listing_price_range(); ?></div>
                <?php endif; ?>
                <?php if(isset(get_post_custom_values($key = '_coronares')[0]) || isset(get_post_custom_values($key = '_standing')[0])){ ?>
                    <div class="row" style="margin: 5px 0 0px 0px">
                        <?php if(isset(get_post_custom_values($key = '_standing')[0]) and !empty(get_post_custom_values($key = '_standing')[0])){?>
                            <div class="col-md-12 listing-small-badge pricing-badge">
                            <img src="/wp-content/fonts/custom_icons/users_green_circle.svg" alt="users icon">
                                <span><?php echo get_post_custom_values($key = '_standing')[0] ?> - maks kapasitet</span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row" style="margin: 2px 0 0px 0px">
                        <?php if(isset(get_post_custom_values($key = '_coronares')[0]) and !empty(get_post_custom_values($key = '_coronares')[0])){ ?>
                            <div class="col-md-12 listing-small-badge pricing-badge" style="padding: 0">
                            <img src="/wp-content/fonts/custom_icons/virus_green_circle.svg" alt="virus icon">
                                <span><?php echo get_post_custom_values($key = '_coronares')[0] ?> - korona kapasitet</span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php

                if( $listing_type  == 'event') {

                    $date_format = listeo_date_time_wp_format_php();

                    $_event_datetime = get_post_meta($post->ID,'_event_date', true);
                    if($_event_datetime) {
                        $_event_date = list($_event_datetime) = explode(' ', $_event_datetime);

                        if($_event_date) :

                        //Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator is a dash (-) or a dot (.), then the European d-m-y format is assumed.

                            if(substr($date_format, 0, 1) === 'd'){

                                $_event_date[0] = str_replace('/', '-', $_event_date[0]);

                            }

                            ?>

                        <div class="listing-small-badge"><i class="fa fa-calendar-check-o"></i><?php echo esc_html(date($date_format, strtotime($_event_date[0]))); ?></div> <br>

                        <?php endif;

                    }

                }  ?>

            <?php if($listing_type  == 'event' || get_the_listing_price_range() ) : ?>
            </div>
            <?php  endif; ?>
        </div>

        <?php

            if( listeo_core_check_if_bookmarked($post->ID) ) {

            $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>

            <span class="like-icon listeo_core-unbookmark-it liked"

            data-post_id="<?php echo esc_attr($post->ID); ?>"

            data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

        <?php } else {

            if(is_user_logged_in()){

                $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>

                <span class="save listeo_core-bookmark-it like-icon"

                data-post_id="<?php echo esc_attr($post->ID); ?>"

                data-nonce="<?php echo esc_attr($nonce); ?>" ></span>

            <?php } else { ?>
                <span class="save like-icon tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"   ></span>
            <?php } ?>
        <?php } ?>

    </div>
    </a>
    </div>
    </div>    

<?php }?>