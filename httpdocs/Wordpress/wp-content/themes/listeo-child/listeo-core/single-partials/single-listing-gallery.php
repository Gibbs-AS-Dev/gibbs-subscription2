<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );
$gallery_text = get_post_meta( $post->ID , 'gallery_text', true );
$count_gallery = listeo_count_gallery_items($post->ID);
if(!empty($gallery)) : ?>

	<!-- Slider -->
	<?php 
	echo '<div class="listing-slider mfp-gallery-container margin-bottom-0">';
	$count = 0;
	$countx = 0;
	foreach ( (array) $gallery as $attachment_id => $attachment_url ) { $countx++;
		$title = '';
		$copyright = '';
		 if($gallery_text != ''){
		$t = unserialize($gallery_text);
	
			$title = $t[$count]['title'];
			$copyright = $t[$count]['copyright'];
		} 
		
		$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
		echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery myimgslides">';
		?>
		<span class="cntnum" style="color:#fff;" class="numbertext"><?php echo $countx. '/' .$count_gallery ?></span>
		<?php
		if($title != '' || $copyright != ''){
			echo '<div class="myicon">';
			if($title != ''){
				echo '<div class="myi"><div class="cus_tooltip"><i class="fa fa-info"></i>
						<span class="tooltiptext">
							'.$title.'
						</span>
					</div></div>';
			}
			if($copyright != ''){
				echo '<div class="myi"><div class="cus_tooltip"><i class="fa fa-copyright"></i>
						<span class="tooltiptext">
							'.$copyright.'
						</span>
					</div></div>';
			}
			echo '</div>';
		}
		echo '</a>';
	$count++; }
	echo '</div>';
 endif; ?>
 