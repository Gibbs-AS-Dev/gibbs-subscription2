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
            
                    <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> data-href-link="<?php the_permalink(); ?>">
            
                        
                    </div>
                </div>    

            
            <!-- Listing Item / End -->
                            <?php }
    }else{
        ?>
            <!-- Listing Item -->
            <div class="col-lg-12 col-md-12">

                <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> data-href-link="<?php the_permalink(); ?>">

                </div>   
            </div>    

        <?php
    }
}else{?>
    <!-- listing start -->
    <div class="col-lg-12 col-md-12">

    <div class="listing-item-container listing-geo-data  list-layout <?php echo esc_attr('listing-type-'.$listing_type) ?>" <?php echo listeo_get_geo_data($post); ?> data-href-link="<?php the_permalink(); ?>">
    </div>    

<?php }?>