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

	if($tax == "listing_feature"){
		$term_list = get_the_terms( $post->ID, $tax );
		$tax_obj = get_taxonomy( $tax );
		$taxonomy = get_taxonomy_labels( $tax_obj );
  

		
		if(!empty($term_list)) { 
			echo "<div class='row feature_roww'>";
			foreach ($term_list as $key => $term_l) {

				$term = get_term( $term_l->term_id, $tax );
                $term_link = get_term_link( $term );
                $icon_svg = get_term_meta($term_l->term_id,"_icon_svg",true); 

                if($icon_svg != ""){
                    $_icon_svg_image = wp_get_attachment_url($icon_svg,'medium'); 

                    $icon_svg = "<img src='".$_icon_svg_image."' />";
                }else{
                	$icon_svg = "<img src='".home_url()."/wp-content/fonts/custom_icons/tag_green_circle.svg' />";
                }

			?>
			<div class="col-md-6 col-xs-12 margin-bottom-10 listing-features-new">
				<?php //echo $icon_svg;?>
				<i class="fa-solid fa-square-check "></i><a href="<?php echo $term_link;?>"><?php echo $term_l->name;?></a>
			</div>

			  <?php //echo get_the_term_list( $post->ID, $tax, '<div class="row"><div class="col-md-6 margin-bottom-10">', '</div><div class="col-md-6 margin-bottom-10">', ' </div></div>' ); ?>
        <?php
            }
            echo "</div>";
		}else{
        ?>
        <style type="text/css">
            .features_sec{
                display: none;
            }
        </style>
        <?php
        }
	}	
	

} 

?>