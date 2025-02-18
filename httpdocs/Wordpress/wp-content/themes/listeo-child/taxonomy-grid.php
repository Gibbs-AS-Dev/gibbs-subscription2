<?php

		$shortcode_atts  = array(
			'taxonomy' => '',
			'show_counter' 	=> '',
			'only_top' 	=> 'yes'
		);

		$taxonomy_names = get_object_taxonomies( 'listing','object' );

		foreach ($taxonomy_names as $key => $value) {
			$shortcode_atts[$value->name.'_include'] = '';
			$shortcode_atts[$value->name.'_exclude'] = '';
		}
		$taxonomy = $atts['taxonomy'];
        $atts = shortcode_atts($shortcode_atts, $atts, 'terms-grid' );

		$query_args = array(
			'include' => $atts[$taxonomy.'_include'],
			'exclude' => $atts[$taxonomy.'_exclude'],
			'pad_counts' => 'true',
			'hide_empty' => false,
            'orderby' => 'include',
		);
		if($atts['only_top'] == 'yes'){
			$query_args['parent'] = 0;
		}

       	$terms = get_terms( $atts['taxonomy'],$query_args);

       	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
       	?>

		<div class="categories-boxes-container" style="left:-10px!important;">

			<!-- Item -->
			<?php
      		foreach ( $terms as $term ) {
		        $t_id = $term->term_id;

				// retrieve the existing value(s) for this meta field. This returns an array
				$icon = get_term_meta($t_id,'icon',true);
		        if(empty($icon)) {
					$icon = 'im im-icon-Globe' ;
		        }

		        ?>
			<a href="<?php echo get_term_link( $term ); ?>" class="category-small-box">
				<?php if (!empty($imageicon)) { ?>
            		<img src="'<?php esc_attr($imageicon); ?>"/>
          		<?php } else {
          			if($icon != 'emtpy') {
          				$check_if_im = substr($icon, 0, 3);
	                    if($check_if_im == 'im ') {
	                       echo' <i class="'.esc_attr($icon).'"></i>';
	                    } else {
	                       echo ' <i class="fa '.esc_attr($icon).'"></i>';
	                    }
          			}
          		} ?>
				<h4><?php echo $term->name; ?></h4>
				<span  class="category-box-counter"><?php echo $term->count ?></span>
			</a>
			<?php } ?>
		</div>
 		<?php }
?>
