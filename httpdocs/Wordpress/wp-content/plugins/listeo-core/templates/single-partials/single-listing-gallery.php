<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );

if(!empty($gallery)) : ?>

	<!-- Slider -->
	<?php 
	echo '<div class="listing-slider mfp-gallery-container margin-bottom-0">';
	$count = 0;
	foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
		$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
		$thumb = wp_get_attachment_image_src( $attachment_id, 'medium' );
		echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery"></a>';
	}
	echo '</div>';
 endif; ?>
