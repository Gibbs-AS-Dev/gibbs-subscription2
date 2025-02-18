<!-- Listings -->
<?php 
$ajax_browsing  = get_option('listeo_ajax_browsing');
$search_data = '';

if(isset($data)) :

	$style 			= (isset($data->style)) ? $data->style : 'list-layout' ;
	$custom_class 	= (isset($data->class)) ? $data->class : '' ;
	$in_rows	 	= (isset($data->in_rows)) ? $data->in_rows : '' ;
	$grid_columns	= (isset($data->grid_columns)) ? $data->grid_columns : '' ;
	$per_page		= (isset($data->per_page)) ? $data->per_page : get_option('listeo_listings_per_page',10) ;
	$ajax_browsing  = (isset($data->ajax_browsing)) ? $data->ajax_browsing : get_option('listeo_ajax_browsing');

	if(isset($data->{'tax-region'} )) {
		$search_data .= ' data-region="'.esc_attr($data->{'tax-region'}).'" ';
	}
	
	if(isset($data->{'tax-listing_category'} )) {
		$search_data .= ' data-category="'.esc_attr($data->{'tax-listing_category'}).'" ';
	}

	if(isset($data->{'tax-listing_feature'} )) {
		$search_data .= ' data-feature="'.esc_attr($data->{'tax-listing_feature'}).'" ';
	}
	
	if(isset($data->{'tax-rental_category'} )) {
		$search_data .= ' data-rental-category="'.esc_attr($data->{'tax-rental_category'}).'" ';
	}
	if(isset($data->{'tax-service_category'} )) {
		$search_data .= ' data-service-category="'.esc_attr($data->{'tax-service_category'}).'" ';
	}	
	if(isset($data->{'tax-event_category'} )) {
		$search_data .= ' data-event-category="'.esc_attr($data->{'tax-event_category'}).'" ';
	}

endif; 

 ?>

<div id="listeo-listings-container" 
data-counter="<?php echo esc_attr($data->counter); ?>" 
data-style="<?php echo esc_attr($style); ?>"  
data-custom_class="<?php echo esc_attr($custom_class); ?>" 
data-per_page="<?php echo esc_attr($per_page); ?>" 
data-grid_columns="<?php echo esc_attr($grid_columns); ?>" 
<?php echo $search_data; ?>
class="row  <?php echo esc_attr($custom_class); if( isset($ajax_browsing) && $ajax_browsing == 'on' ) { echo esc_attr('ajax-search'); } ?>">
	<div class="loader-ajax-container" style=""> <div class="loader-ajax"></div> </div>
