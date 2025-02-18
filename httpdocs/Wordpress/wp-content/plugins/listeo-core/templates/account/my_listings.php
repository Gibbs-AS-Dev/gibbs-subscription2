<?php 
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
?> 
<div class="row">
	

<div class="col-lg-12 col-md-12">

	<?php if( empty($ids) ) : ?>
		<?php if( $status == 'active') : ?>
		<div class="notification notice margin-bottom-20">
			<p><?php printf( _e( 'You haven\'t submitted any listings yet, you can add your first one <a href="%s">below</a>', 'listeo_core' ), get_permalink( get_option( 'listeo_submit_page' ) ) );	 ?></p>
		</div>
		<a href="<?php echo get_permalink( get_option( 'listeo_submit_page' ) ); ?>" class="margin-top-35 button"><?php esc_html_e('Submit New Listing','listeo_core'); ?></a>
		<?php else : ?>
			<div class="notification notice margin-bottom-20">
				<p><?php esc_html_e( 'You don\'t have any listings here', 'listeo_core' );	 ?></p>
			</div>
		<?php endif; ?>
	<?php else: ?>
		
	<?php if(!empty($message )) { echo $message; } ?>

	<?php

	 $search_value = isset($_GET['search']) ? $_GET['search'] : '';?>
	<div class="dashboard-list-box margin-top-0">
		<form id="my-listings-search-form" action="">
		<input type="hidden" name="status" value="<?php echo esc_attr($status); ?>">
		<input type="text" name="search" id="my-listings-search" placeholder="Search listing" value="<?php echo esc_attr($search_value); ?>">
			<button type="submit"><i class="fa fa-search"></i></button>
		</form>
		<h4>
			<?php switch ($status) {
				case 'active':
					esc_html_e('Active Listings', 'listeo_core');
					break;
				case 'pending':
					esc_html_e('Pending Listings', 'listeo_core');
					break;
				case 'expired':
					esc_html_e('Expired Listings', 'listeo_core');
					break;
				
				default:
					esc_html_e('Active Listings', 'listeo_core');
					break;
			} ?>

		</h4>
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
						<?php } ?></a>
					</div>
					<div class="list-box-listing-content">

						<div class="inner">
							<h3><?php echo get_the_title( $listing ); //echo listeo_core_get_post_status($listing_id) ?></h3>
							<span class="listing-address"><?php the_listing_address($listing); ?></span>
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
								<h3><?php esc_html_e('iCal Import','listeo_core'); ?></h3>
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
									<div class="checkboxes in-row margin-bottom-20">
										<input type="checkbox" name="import_ical_force_update" class="import_ical_force_update input-checkbox" id="import_ical_force_update">	
									 	<label for="import_ical_force_update"><?php esc_html_e('Force Update conflicting ical events','listeo_core'); ?></label>
								    </div>
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
					<?php if(get_option('listeo_ical_page')): ?>
					<div class="ical-dropdown-btn">
						<?php esc_html_e('iCal','listeo_core') ?>
						<ul>
							<li>
								<a href="#ical-export-dialog-<?php echo esc_attr($listing_id);?>" class="button popup-with-zoom-anim"><?php esc_html_e('iCal Export','listeo_core') ?></a>
							</li>
							<li>
								<a href="#ical-import-dialog-<?php echo esc_attr($listing_id);?>" class="button popup-with-zoom-anim"><?php esc_html_e('iCal Import','listeo_core') ?></a>
							</li>
						</ul>
					</div>
					<?php endif; ?>
					
					<?php
						$actions = array();

						switch ( $listing->post_status ) {
							case 'publish' :
									$actions['edit'] = array( 'label' => __( 'Edit', 'listeo_core' ), 'icon' => 'sl sl-icon-note', 'nonce' => false );
									if(get_option('listeo_new_listing_requires_purchase')){
										$actions['renew'] = array( 
											'label' => __( 'Change Package', 'listeo_core' ), 
											'icon' => 'sl sl-icon-wrench', 
											'nonce' => false
											 );	
									}
									
									//$actions['unpublish'] = array( 'label' => __( 'Hide', 'listeo_core' ), 'icon' => 'sl sl-icon-ban', 'nonce' => true );
								break;
							
							case 'pending_payment' :
							case 'pending' :
								
								$actions['edit'] = array( 'label' => __( 'Edit', 'listeo_core' ), 'icon' => 'pencil', 'nonce' => false );
								
							break;

							case 'expired' :
								
								$actions['renew'] = array( 'label' => __( 'Renew', 'listeo_core' ), 'icon' => 'refresh', 'nonce' => true );
								
							break;
						}

						$actions['delete'] = array( 'label' => __( 'Delete', 'listeo_core' ), 'icon' => 'sl sl-icon-close', 'nonce' => true );

						$actions           = apply_filters( 'listeo_core_my_listings_actions', $actions, $listing );

						foreach ( $actions as $action => $value ) {

							if($action == 'edit' || $action == 'renew'){
								$action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID ), get_permalink( get_option( 'listeo_submit_page' )) );
							} else {
								$action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID ) );
							}
							if(!get_option('listeo_new_listing_requires_purchase') && $action == 'renew'){
								$action_url = add_query_arg( array( 'action' => $action,  'listing_id' => $listing->ID ) );
							}
							if ( $value['nonce'] ) {
								$action_url = wp_nonce_url( $action_url, 'listeo_core_my_listings_actions' );
							}
					
							echo '<a  href="' . esc_url( $action_url ) . '" class="button gray ' . esc_attr( $action ) . ' listeo_core-dashboard-action-' . esc_attr( $action ) . '">';
							
							if(isset($value['icon']) && !empty($value['icon'])) {
								echo '<i class="'.$value['icon'].'"></i> ';
							}

							 echo esc_html( $value['label'] ) . '</a>';
						}
					?>
				
				</div>
			</li>

		<?php } ?>
		</ul>
	</div>
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

	<?php if(get_option('listeo_submit_page')){ ?>
		<a href="<?php echo get_permalink( get_option( 'listeo_submit_page' ) ); ?>" class="margin-top-35 button"><?php esc_html_e('Submit New Listing','listeo_core'); ?></a>
	<?php } ?>
	
	<?php endif; ?>

</div>
</div>