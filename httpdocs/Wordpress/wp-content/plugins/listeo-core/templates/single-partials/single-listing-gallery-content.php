<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );

if(!empty($gallery)) : ?>
<!-- Slider -->
<div id="listing-gallery" class="listing-section">
	<h3 class="listing-desc-headline margin-top-70"><?php esc_html_e('Gallery','listeo_core'); ?></h3>
	<div class="listing-slider-small mfp-gallery-container margin-bottom-0">
	<?php 

		foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
			$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
			echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery"></a>';
		}
		
	?>
	</div>
</div>

<?php endif; ?>
<!-- Slider -->
		