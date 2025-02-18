<?php
global $wp;
$ids = '';
if(isset($data)) :
    $ids	 	= (isset($data->ids->posts)) ? $data->ids->posts : '' ;
    $status	 	= (isset($data->status)) ? $data->status : '' ;
endif;
$message = $data->message;
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles );
if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
    $template_loader = new Listeo_Core_Template_Loader;
    $template_loader->get_template_part( 'account/owner_only');
    return;
endif;
$max_num_pages = $data->ids->max_num_pages;
if(isset($_GET["search"])){
    $search_value = $_GET["search"];
}


?>
<div class="row">


    <div class="col-lg-12 col-md-12">

         <?php
        $hide_clear = true;
        if(isset($_GET["search"]) && $_GET["search"] != ""){
             $hide_clear = false;
        }
        ?>

       

        <div class="dashboard-list-box margin-top-0">
            <!-- <div class="filter_div"> -->
                <?php
                $listingss = Listeo_Core_Users::get_user_all_listings();

               // echo "<pre>"; print_r($listingss);
                if(empty($listingss)){ 
                    //$demo_url =  add_query_arg( array( 'action' => "listing_demo",  'listing_id' => $listing->ID,'current_page' => home_url( $wp->request ) ));

                    ?>
                    <!-- <div class="btn-listing-demo"><a href="<?php echo $demo_url;?>" class="">Opprett demo utleieobjekt</a></div> -->
                <?php }
                ?>
                <!--  <form id="my-listings-search-form" action="">
                    <input type="text" name="search" id="my-listings-search" placeholder="Søk" value="<?php echo esc_attr($search_value); ?>">
                    <button type="submit"><i class="fa fa-search"></i></button>
                    <button type="button" class="my_listings_clear_button" onclick="window.location.href='/my-listings';" <?php if($hide_clear){ ?> style="display: none;" <?php } ?>> Nullstill</button>
                </form> -->
           <!--  </div> -->

        <?php if( empty($ids) ) : ?>
            <div class="notification dfdf notice margin-bottom-20" style="position: absolute;width: 92%;margin-top: 66px;">
                <p><?php esc_html_e( 'You don\'t have any listings here', 'listeo_core' );	 ?></p>
            </div>
        <?php else: ?>

            <?php if(!empty($message )) { echo $message; } ?>
            

               
                

            

                <!-- <h4>
                    <?php switch ($status) {
                        case 'active':
                            //esc_html_e('Active Listings', 'listeo_core');
                            break;
                        case 'pending':
                           // esc_html_e('Pending Listings', 'listeo_core');
                            break;
                        case 'expired':
                           // esc_html_e('Expired Listings', 'listeo_core');
                            break;
                        
                        default:
                           // esc_html_e('Active Listings', 'listeo_core');
                            break;
                    } ?>

                </h4> -->
                <ul>
                    <?php
                    foreach ($ids as $listing_id) {
                        $listing = get_post($listing_id);
                        ?>
                        <li>
                            <div class="list-box-listing">
                                <div class="list-box-listing-img">
                                    <a href="<?php echo get_permalink( $listing ) ?>"><?php
                                        if(has_post_thumbnail($listing_id)){
                                            echo get_the_post_thumbnail($listing_id,'listeo_core-preview');
                                        } else {
                                            $gallery = (array) get_post_meta( $listing_id, '_gallery', true );

                                            $ids = array_keys($gallery);
                                            if(!empty($ids[0]) && $ids[0] !== 0){
                                                $image_url = wp_get_attachment_image_url($ids[0],'listeo_core-preview');
                                            } else {
                                                $image_url = get_listeo_core_placeholder_image();
                                            }
                                            ?>
                                            <img src="<?php echo esc_attr($image_url); ?>" alt="">
                                        <?php } ?>

                                        <i class="direct_icon fas fa-arrow-up-right-from-square"></i>
                                    </a>

                                </div>
                                <div class="list-box-listing-content">

                                    <div class="inner">
                                        <h3><?php echo get_the_title( $listing ); //echo listeo_core_get_post_status($listing_id) ?></h3>
                                        <span class="listing-address"><?php the_listing_address($listing); ?></span>
                                        <div class="status_div"><span class="listing_status <?php echo strtolower($listing->post_status);?>-status"><?php echo __(ucfirst($listing->post_status),"gibbs");?></span></div>
                                        <span class="expiration-date"><?php esc_html_e('Expiring: ','listeo_core'); ?> <?php echo listeo_core_get_expiration_date($listing_id); ?></span>
                                        <?php $rating = get_post_meta($listing_id, 'listeo-avg-rating', true);
                                        if(isset($rating) && $rating > 0 ) :  $rating_type = get_option('listeo_rating_type','star');
                                        if($rating_type == 'numerical') { ?>
                                        <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                                            <?php } else { ?>
                                            <div class="star-rating" data-rating="<?php echo $rating; ?>">
                                                <?php } ?>
                                                <?php $number = get_comments_number($listing_id);  ?>
                                                <div class="rating-counter">(<?php printf( _n( '%s review', '%s reviews', $number,'listeo_core' ), number_format_i18n( $number ) );  ?>)</div>
                                            </div>
                                            <?php endif; ?>

                                        </div>
                                        <?php if(get_option('listeo_ical_page')): ?>
                                            <div id="ical-export-dialog-<?php echo esc_attr($listing_id);?>" class="listeo-dialog ical-export-dialog zoom-anim-dialog mfp-hide">

                                                <div class="small-dialog-header">
                                                    <h3>
                                                        <?php printf(__("iCal file for %s", 'listeo_core'), get_the_title($listing_id)); ?>
                                                    </h3>
                                                </div>
                                                <!--Tabs -->
                                                <div class="sign-in-form style-1">


                                                    <div><input type="text" class="listeo-export-ical-input" value="<?php echo listeo_ical_export_url($listing_id); ?>"></div>

                                                </div>
                                            </div>
                                            <div id="ical-import-dialog-<?php echo esc_attr($listing_id);?>" class="listeo-dialog ical-import-dialog zoom-anim-dialog  mfp-hide">

                                                <div class="small-dialog-header">
                                                    <h3><?php esc_html_e('iCal Import','listeo'); ?></h3>
                                                </div>
                                                <!--Tabs -->
                                                <div class="sign-in-form style-1">

                                                    <div class="saved-icals">
                                                        <?php echo listeo_get_saved_icals($listing_id); ?>
                                                    </div>


                                                    <h4><?php esc_html_e('Import New Calendar','listeo_core'); ?></h4>

                                                    <form action="" data-listing-id="<?php echo esc_attr($listing_id); ?>" class="ical-import-form" id="ical-import-form-<?php echo esc_attr($listing_id);?>">
                                                        <p>
                                                            <input required placeholder="<?php esc_html_e('Name','listeo_core'); ?>" type="text"  class="import_ical_name" name="import_ical_name" >
                                                        </p>
                                                        <p>
                                                            <input required placeholder="<?php esc_html_e('URL to .ical, .ics, .ifb or .icalendar file','listeo_core'); ?>" type="text"  class="import_ical_url" name="import_ical_url">
                                                        </p>
                                                        <button class="button"><i class="fa fa-circle-o-notch fa-spin"></i><?php esc_html_e('Save','listeo_core'); ?></button>
                                                    </form>
                                                    <div class="notification notice margin-top-20" style="display: none">
                                                        <p></p>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php endif; ?>


                                    </div>
                                </div>
                                <div class="buttons-to-right">
<!--                                    --><?php //if($listing->post_status == "publish"):
//                                        if(get_post_meta( $listing->ID , '_booking_status',true) == 'on'): ?>
<!--                                        <a href="--><?php //echo get_permalink($listing->ID) ?><!--?check_availability=1" class="button gray"><i class="fa fa-calendar-check-o" aria-hidden="true">   <span style="font-weight: bold;">Check Availability</span></i></a>-->
<!--                                    --><?php //endif; endif;?>
                                   <!--  <a href="#" class="button gray"><span style="font-weight: bold;">Duplicate</span></a> -->


                                 
                                    <?php
                                    $actions = array();
                                    

                                    $actions['createQR'] = array( 'label' => __( ' QR ', 'listeo_core ' ), 'icon' => 'fa-solid fa-download', 'nonce' => false );

                                    $actions['shareListing'] = array( 'label' => __( ' Del ' ), 'icon' => 'fa-solid fa-share', 'nonce' => false );

                                    switch ( $listing->post_status ) {
                                        case 'publish' :
                                            
                                            $actions['edit'] = array( 'label' => __( ' Rediger ', 'listeo_core ' ), 'icon' => 'fa fa-edit', 'nonce' => false );
                                            $actions['duplicate'] = array( 'label' => __( ' Bruk som mal', 'listeo_core' ), 'icon' => 'fa fa-copy', 'nonce' => false );
                                            $actions['hide'] = array( 'label' => __( 'Hide ', 'listeo_core ' ), 'icon' => 'eye-slash', 'nonce' => true );
                                            break;

                                        case 'pending_payment' :

                                        case 'draft' :
                                        
                                            $actions['edit'] = array( 'label' => __( ' Rediger ', 'listeo_core ' ), 'icon' => 'fa fa-edit', 'nonce' => false );
                                            $actions['duplicate'] = array( 'label' => __( ' Bruk som mal ', 'listeo_core' ), 'icon' => 'fa fa-copy', 'nonce' => false );
                                            $actions['hide'] = array( 'label' => __( 'Hide ', 'listeo_core ' ), 'icon' => 'eye-slash', 'nonce' => true );
                                            break;
                                        case 'pending' :
                                           
                                            $actions['edit'] = array( 'label' => __( ' Rediger', 'listeo_core' ), 'icon' => 'fa fa-edit', 'nonce' => false );
                                            $actions['duplicate'] = array( 'label' => __( ' Bruk som mal', 'listeo_core' ), 'icon' => 'fa fa-copy', 'nonce' => false );
                                            $actions['hide'] = array( 'label' => __( 'Hide', 'listeo_core' ), 'icon' => 'eye-slash', 'nonce' => true );
                                            break;

                                        case 'expired' :
                             
                                            $actions['edit'] = array( 'label' => __( ' Rediger', 'listeo_core' ), 'icon' => 'fa fa-edit', 'nonce' => false );
                                            $actions['duplicate'] = array( 'label' => __( ' Bruk som mal', 'listeo_core' ), 'icon' => 'fa fa-copy', 'nonce' => false );
                                            $actions['hide'] = array( 'label' => __( 'Hide', 'listeo_core' ), 'icon' => 'eye-slash', 'nonce' => true );
                                            break;
                                    }

                                    $actions['delete'] = array( 'label' => __( 'Delete', 'listeo_core' ), 'icon' => 'sl sl-icon-close', 'nonce' => true );

                                    $actions           = apply_filters( 'listeo_core_my_listings_actions', $actions, $listing );
                                    
                                    $current_page =  home_url( $wp->request );

                                    $extra_tags = "";

                                    foreach ( $actions as $action => $value ) {
                                        if($action == 'edit' || $action == 'renew'){
                                            $action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID ), get_permalink( get_option( 'listeo_submit_page' )) );
                                        } else {
                                            $action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID ) );
                                        }
                                        if($action == 'shareListing'){
                                            $action_url = "#";
                                            $extra_tags = "data-url='".get_permalink( $listing )."'";
                                        }
                                        if($action == 'createQR'){
                                            $action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID,'link' => urlencode(get_permalink( $listing )) ));
                                        }
                                        if($action == 'duplicate'){
                                            
                                            if(isset($_GET['listings_paged'])){
                                                $current_page =$current_page."?listings_paged=".$_GET['listings_paged'];
                                            }
                                            $action_url = add_query_arg( array( 'action' => "duplicate",  'listing_id' => $listing->ID,'current_page' => $current_page  ), get_permalink( get_option( 'listeo_submit_page' )) );
                                        }
                                        if ( $value['nonce'] ) {
                                            $action_url = wp_nonce_url( $action_url, 'listeo_core_my_listings_actions' );
                                        }

                                        echo '<a href="' . esc_url( $action_url ) . '" class="button gray ' . esc_attr( $action ) . ' listeo_core-dashboard-action-' . esc_attr( $action ) . '" '.$extra_tags.'>';

                                        if(isset($value['icon']) && !empty($value['icon'])) {
                                            echo '<i class="'.$value['icon'].'"></i>';
                                        }

                                        echo esc_html( $value['label'] ) . '</a>';
                                    }
                                    ?>

                                </div>
                        </li>

                    <?php } ?>
                </ul>
            
            <?php

            $paged = (isset($_GET['listings_paged'])) ? $_GET['listings_paged'] : 1;

            ?>
            <div class="clearfix"></div>
            <div class="pagination-container margin-top-30 margin-bottom-0">
                <nav class="pagination">
                    <?php
                    $big = 999999999;
                    echo paginate_links( array(
                        'base'      => add_query_arg('listings_paged','%#%'),
                        'format' 	=> '?listings_paged=%#%',
                        'current' 	=> max( 1, $paged ),
                        'total' 	=> $max_num_pages,
                        'type' 		=> 'list',
                        'prev_next'    => true,
                        'prev_text'    => '<i class="sl sl-icon-arrow-left"></i>',
                        'next_text'    => '<i class="sl sl-icon-arrow-right"></i>',
                        'add_args'        => false,
                        'add_fragment'    => ''

                    ) );?>
                </nav>
            </div>

            <?php /*if(get_option('listeo_submit_page')){ ?>
		<a href="<?php echo get_permalink( get_option( 'listeo_submit_page' ) ); ?>" class="margin-top-35 button"><?php esc_html_e('Submit New Listing','listeo_core'); ?></a>
	<?php }*/ ?>

        <?php endif; ?>
        </div>
        <script type="text/javascript">
            jQuery("#my-listings-search").keyup(function(){
                if(this.value != ""){
                   jQuery(".my_listings_clear_button").show();
                }else{
                    jQuery(".my_listings_clear_button").hide();
                }
            })

            jQuery(".elementor-tabs-wrapper .elementor-tab-title").click(function(){
                var data_id = jQuery(this).attr("id");
                localStorage.setItem("listing_active_tab", data_id);
            })

            

            jQuery(document).ready(function(){
                setTimeout(function(){
                    var listing_active_tab = localStorage.getItem("listing_active_tab");
                   
                    if(listing_active_tab && listing_active_tab != ""){
                       
                              jQuery("#"+listing_active_tab)[0].click();
                        
                       
                    }
                },500)
            })
        </script>

    </div>
</div>

