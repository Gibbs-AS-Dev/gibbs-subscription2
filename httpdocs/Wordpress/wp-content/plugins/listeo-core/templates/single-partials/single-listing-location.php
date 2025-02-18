<!-- Location -->
<?php 
$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
$longitude = get_post_meta( $post->ID, '_geolocation_long', true ); 
$address = get_post_meta( $post->ID, '_address', true ); 
$disable_address = get_option('listeo_disable_address');
if(!empty($latitude) && $disable_address) {
	$dither= '0.001';
	$latitude = $latitude + (rand(5,15)-0.5)*$dither;
}
if(!empty($latitude)) : 

$terms = get_the_terms( $post->ID, 'listing_category' );
$icon = '';
if($terms ) {
	$term = array_pop($terms);	
	
	$t_id = $term->term_id;
	// retrieve the existing value(s) for this meta field. This returns an array
	$icon = get_term_meta($t_id,'icon',true);
	
}

if(isset($t_id)){
	$_icon_svg = get_term_meta($t_id,'_icon_svg',true);
	$_icon_svg_image = wp_get_attachment_image_src($_icon_svg,'medium');
}
if (isset($_icon_svg_image) && !empty($_icon_svg_image)) { 
	$icon_svg = listeo_render_svg_icon($_icon_svg);
    //$icon = '<img class="listeo-map-svg-icon" src="'.$_icon_svg_image[0].'"/>';


} else { 

	if(empty($icon)){
		$icon = get_post_meta( $post->ID, '_icon', true );
	}

	if(empty($icon)){
		$icon = '<i class="im im-icon-Map-Marker2"></i>';
	}
}

?>
<!-- Location -->
<div id="listing-location" class="listing-section">
	<h3 class="listing-desc-headline margin-top-60 margin-bottom-30"><?php esc_html_e('Location','listeo_core'); ?></h3>

	<div id="singleListingMap-container" class="<?php if($disable_address) { echo 'circle-point'; } ?> " >
		<div id="singleListingMap" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>" data-map-icon="<?php echo esc_attr($icon); ?>" <?php if(isset($icon_svg)) { ?> data-map-icon-svg="<?php echo esc_attr($icon_svg); ?>"<?php } ?>></div>
		<?php if(get_option('listeo_map_provider') == 'google_not_valid_anymore') { ?><a href="#" id="streetView"><?php esc_html_e('Street View','listeo_core'); ?></a> <?php } ?>
		<?php if(!$disable_address) { ?>
		<a target="_blank" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr($latitude.','.$longitude); ?>" id="getDirection"><?php esc_html_e('Få veibeskrivelse','listeo_core'); ?></a>
		<?php }?>
	</div>
</div>

<?php endif;  ?>

