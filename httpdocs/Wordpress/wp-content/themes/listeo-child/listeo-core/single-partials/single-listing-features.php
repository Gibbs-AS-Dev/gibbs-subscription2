<!-- Features -->
<?php   

$listing_type = get_post_meta( get_the_ID(), '_listing_type', true);
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
}
      

$taxonomies = get_option('listeo_single_taxonomies_checkbox_list', array('listing_feature') );

foreach($taxonomies as $tax){
	if($tax != "listing_feature"){
		$term_list = get_the_term_list( $post->ID, $tax );
		$tax_obj = get_taxonomy( $tax );
		$taxonomy = get_taxonomy_labels( $tax_obj );

		
		if(!empty($term_list)) { ?>
			<h3 class="listing-desc-headline <?php echo $tax;?>_heading"><?php echo $taxonomy->name; ?></h3>
			<?php echo get_the_term_list( $post->ID, $tax, '<ul class="listing-features checkboxes margin-top-0"><li>', '</li><li>', '</li></ul>' );
		}
	}

}; 
if( isset($type_terms) ) {

	$selected_cat_top = "";
	foreach ($type_terms as $key => $listing_top_cat) {
		$all_selected_categores[] = $listing_top_cat->term_id;
		if($listing_top_cat->parent == 0){
			$selected_cat_top = $listing_top_cat->term_id;
		}
	}
	if($selected_cat_top != ""){
        $multi_select = get_term_meta( $selected_cat_top, 'mutiselect_category', true );

       // if($multi_select == "on"){
			 $multiselect_region_text_checkbox = get_term_meta( $selected_cat_top, 'multiselect_region_text_checkbox', true );
			if($multiselect_region_text_checkbox == "on"){
				$multiselect_region_text = get_term_meta( $selected_cat_top, 'multiselect_region_text', true );
				if($multiselect_region_text != ""){

		?>
				   <script type="text/javascript">
				   	  //jQuery(document).ready(function(){
                          jQuery(".region_heading").html("<?php echo $multiselect_region_text;?>");
				   	 // });
				   	  
				   </script>
		<?php
		        }
		    }	
		//}
	}
   
}  
?>