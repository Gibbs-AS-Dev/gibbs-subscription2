<?php 
if(isset($data)) :
    $style        = (isset($data->style)) ? $data->style : '' ;
    $grid_columns        = (isset($data->grid_columns)) ? $data->grid_columns : '2' ;
endif; 

$template_loader = new Listeo_Core_Template_Loader;
$listing_type = get_post_meta( $post->ID,'_listing_type',true ); 
$is_featured = listeo_core_is_featured($post->ID);
$is_instant = listeo_core_is_instant_booking($post->ID);
?>
<!-- Listing Item -->
<?php if(isset($style) && $style == 'grid') {
    switch ($grid_columns) {
        case '1':
            echo '<div class="col-md-12">';
            break;
        case '2':
            echo '<div class="col-lg-6 col-md-12">';
            break;
         case '3':
            echo '<div class="col-lg-4 col-md-6">';
            break;
        
        default:
            echo '<div class="col-lg-6 col-md-12">';
            break;
    }
} ?>
<a href="<?php the_permalink(); ?>" class="listing-item-container listing-geo-data"  <?php echo listeo_get_geo_data($post); ?>>
    <div class="listing-item  <?php if($is_featured){ ?>featured-listing<?php } ?>">
        
        <div class="listing-small-badges-container">
            <?php if($is_featured){ ?>
                <div class="listing-small-badge featured-badge"><i class="fa fa-star"></i> <?php esc_html_e('Featured','listeo_core'); ?></div>
            <?php } ?> 
            <?php if($is_instant){ ?>
                <div class="listing-small-badge instant-badge"><i class="fa fa-bolt"></i> <?php esc_html_e('Instant Booking','listeo_core'); ?></div>
            <?php } ?>
            <?php if(get_the_listing_price_range()): ?>
                <div class="listing-small-badge pricing-badge"><i class="fa fa-<?php echo esc_attr(get_option('listeo_price_filter_icon','tag')); ?>"></i><?php echo get_the_listing_price_range(); ?></div>
            <?php endif; ?>
            <?php  
            if( $listing_type  == 'event') { 
                
                $date_format = listeo_date_time_wp_format_php();
                $_event_datetime = get_post_meta($post->ID,'_event_date', true); // mm/dd/yy
                if($_event_datetime) {
                    $_event_date = list($_event_datetime) = explode(' ', $_event_datetime);
                
                    if($_event_date) : 
                        $date_format = get_option( 'date_format' );
                           //var_dump($_event_date[0]);
                                            //$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
                        $meta_value_stamp_obj = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $_event_date[0]);
                        if($meta_value_stamp_obj){
                            $meta_value_stamp = $meta_value_stamp_obj->getTimestamp();
                        } else {
                            $meta_value_stamp = false;
                        }
                                           
                        $meta_value = date_i18n(get_option( 'date_format' ),$meta_value_stamp);?>
                    <div class="listing-small-badge"><i class="fa fa-calendar-check"></i><?php echo esc_html($meta_value); ?></div> 
                    <?php endif; 
                }
                
            }  ?>
            
        </div>
        <?php 
		$template_loader->get_template_part( 'content-listing-image');  ?>
        
        <?php
        
        if( $listing_type  == 'service' && get_post_meta( $post->ID,'_opening_hours_status',true )) { 
                if( listeo_check_if_open() ){ ?>
                    <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>
                <?php } else { 
                    if( listeo_check_if_has_hours() ) { ?>
                        <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>
                    <?php } ?>
            <?php } 
        } 
       ?>
        
        <div class="listing-item-content">

            <?php 
            $terms = get_the_terms( get_the_ID(), 'listing_category' );            
            if ( $terms && ! is_wp_error( $terms ) ) :  
                $main_term = array_pop($terms); ?>
                <span class="tag"><?php echo $main_term->name; ?></span>
            <?php endif; ?>

            <h3><?php the_title(); ?> <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?></h3>
            <?php if(get_the_listing_address()) { ?><span><?php the_listing_address(); ?></span><?php } ?>

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
                    <span class="save like-icon tooltip left"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"  ></span>
                <?php } ?>
        <?php } ?>
    </div>
    
    <?php
        if(!get_option('listeo_disable_reviews')){

            if($listing_type=='classifieds'){
   
                
                        $price = get_post_meta($post->ID, '_classifieds_price', true);
                        $currency_abbr = get_option( 'listeo_currency' );
                        $currency_postion = get_option( 'listeo_currency_postion' );
                        $currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
                        if($price){ ?>
                            <div class="listing-classifieds-badges-container">
                       <div class="listing-small-badge pricing-badge classifieds-pricing-badge"><i class="fa fa-<?php echo esc_attr(get_option('listeo_price_filter_icon','tag')); ?>"></i><?php if($currency_postion == "before") { echo $currency_symbol;} echo get_post_meta($post->ID, '_classifieds_price', true);  if($currency_postion == "after") { echo $currency_symbol;}?></div></div>
                    <?php } 
            
            } else {


            $rating = get_post_meta($post->ID, 'listeo-avg-rating', true); 
            if(!$rating && get_option('listeo_google_reviews_instead')){
                    $reviews = listeo_get_google_reviews($post);
                    if(!empty($reviews['result']['reviews'])){
                        $rating = number_format_i18n($reviews['result']['rating'],1); 
                    }
                }
                if(isset($rating) && $rating > 0 ) : $rating_type = get_option('listeo_rating_type','star');
                if($rating_type == 'numerical') { ?>
                    <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                <?php } else { ?>
                    <div class="star-rating" data-rating="<?php echo $rating; ?>">
                <?php } ?>
                <?php $number = listeo_get_reviews_number($post->ID); 
                 if(!get_post_meta($post->ID, 'listeo-avg-rating', true) && get_option('listeo_google_reviews_instead')){ 
                                       $number = $reviews['result']['user_ratings_total'];
                                     }  ?>
                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>
                
                </div>
            <?php else: ?>
                    <div class="star-rating" >
                        
                        <div class="rating-counter"><span><?php esc_html_e('No reviews yet','listeo_core') ?></span></div>
                    </div>
            <?php endif;
            }   
        } ?>
    
</a>

<?php if(isset($style) && $style == 'grid') { ?>
    </div>
<?php } ?>


<!-- Listing Item / End -->