<!-- Features -->
<?php   

$taxonomies = get_option('listeo_single_taxonomies_checkbox_list', array('listing_feature') );

foreach($taxonomies as $tax){
	$term_list = get_the_term_list( $post->ID, $tax );
	$tax_obj = get_taxonomy( $tax );
	$taxonomy = get_taxonomy_labels( $tax_obj );

	
	if(!empty($term_list)) { ?>
		<h3 class="listing-desc-headline"><?php echo $taxonomy->name; ?></h3>
		<?php echo get_the_term_list( $post->ID, $tax, '<ul class="listing-features checkboxes margin-top-0"><li>', '</li><li>', '</li></ul>' );
	}
	

}; 

?>