<?php

	$gallery = (array) get_post_meta( $id, '_gallery', true );

	$ids = array_keys($gallery);
	if(empty($ids[0]) && $ids[0] == 0){ ?>
		<a href="<?php the_permalink(); ?>" style="z-index:2;position:absolute;height:100%;"><img class="fade-img" style="z-index:1;" src="<?php echo esc_attr(get_listeo_core_placeholder_image()); ?>"></a>
	<?php } else {

		if(count($gallery) > 1) : ?>
			
			<div style="display:none;height:0px;" class="slideshow_arrows">
				<!-- Left btn -->
				<div onclick="plusSlides(-1, <?php echo $id; ?>)" style="height:220px;z-index:501;width:35px;cursor:pointer;">
					<span style="z-index:500;position:absolute;margin-top:100px;text-align:center;color:grey;background-color:rgba(255,255,255,.6);width:25px;height:25px;border-radius:50%;box-sizing:content-box;font-size:1rem;"><i class="fa fa-chevron-left" style="padding-right:1px;" aria-hidden="true"></i></span>
				</div>
				<!-- middle padding -->
				<div style="flex-grow: 1;flex: 1;"></div>
				<!-- Right btn -->
				<div onclick="plusSlides(1, <?php echo $id; ?>)" style="height:220px;z-index:501;width:35px;cursor:pointer;">
					<span style="z-index:500;position:absolute;margin-top:100px;text-align:center;color:grey;background-color:rgba(255,255,255,.6);width:25px;height:25px;border-radius:50%;box-sizing:content-box;font-size:1rem;right:0px;"><i class="fa fa-chevron-right" style="padding-left:1px;" aria-hidden="true"></i></span>
				</div>
			</div>


			<div style="text-align:center;position:absolute;top:0px;width:100%;height:25px;" id="dots-<?php echo $id; ?>">
				<?php
					for ($x = 0; $x <= count($gallery) -1; $x++) { ?>
						<span style="padding:7px;top:0px;position:relative;left:0px;margin-right:0px;z-index:500;" class="dot tag <?php if($x == 0) echo " active"; ?>" onclick="currentSlide(<?php echo ($x+1) . "," . $id; ?>)"></span>
				<?php } ?>
			</div>
		<?php endif; ?>
		<div style="height:100%;" class="search_slide" id="id-<?php echo $id; ?>">
			<?php $gallery = get_post_meta( $post->ID, '_gallery', true );
			if(!empty($gallery)) :
				?> <a href="<?php the_permalink(); ?>" style="z-index:2;position:absolute;height:100%;width:100%;"><?php
				foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
					$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' ); ?>
					<img class="fade-img" style="z-index:1;" src="<?php echo esc_attr($image[0]); ?>">
				<?php } ?>
				</a> <?php
			 endif; ?>
		</div>
	<?php }
