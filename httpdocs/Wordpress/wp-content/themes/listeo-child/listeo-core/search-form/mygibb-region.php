<div class="checkboxes one-in-row <?php if(isset($data->class)) { echo esc_attr($data->class); }?>" id="<?php echo esc_attr($data->name); ?>">

<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
	<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
<?php } else {


	if(isset($_GET[$data->name])) {
		$selected = array($_GET[$data->name]);
	} else {
		$selected = array();
	} 

	$topLvCategories_data = get_terms( array(
	          'taxonomy' => $data->taxonomy,
	          'hide_empty'  => false, 
    ));
    $topLvCategories = array();
    foreach ($topLvCategories_data as $key => $dd) {
    	if($dd->parent == 0){
           $topLvCategories[] = $dd;
    	}
    }
	

	if(isset($data->taxonomy) && !empty($data->taxonomy)) {
		$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
		if(is_tax($data->taxonomy)){
			$selected[get_query_var($data->taxonomy)] = 'on';
		}	
		foreach ($topLvCategories as $key => $value) { ?>
            <span class="main_span">
				<input <?php if ( array_key_exists ($value->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value->slug).']'; ?>" data-child="child-<?php echo $value->slug;?>">
				<label for="<?php echo esc_html($value->slug) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value->name) ?></label>
			</span>

			<?php
					$subLvCategories_data1 = get_terms( array(
					          'taxonomy' => $data->taxonomy,
					          'hide_empty'  => false,
					          'parent' =>$value->term_id,
				    ));

				    foreach ($subLvCategories_data1 as $key => $value1) { ?>
			            <span class="child_span1 child-<?php echo $value->slug;?>" style="display:none">
				            <input <?php if ( array_key_exists ($value1->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value1->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value1->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value1->slug).']'; ?>" data-child="child-<?php echo $value1->slug;?>">
					        <label for="<?php echo esc_html($value1->slug) ?>-<?php echo esc_attr($data->name); ?>" style="margin-left: 20px;"><?php echo esc_html($value1->name) ?></label>
					    </span>

				        <?php
							$subLvCategories_data2 = get_terms( array(
							          'taxonomy' => $data->taxonomy,
							          'hide_empty'  => false,
							          'parent' =>$value1->term_id,
						    ));

						    foreach ($subLvCategories_data2 as $key => $value2) { ?>

						    	<span class="child_span2 child-<?php echo $value1->slug;?>" style="display:none">
						  
						            <input <?php if ( array_key_exists ($value2->slug, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value2->slug) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value2->slug) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value2->slug).']'; ?>">
							        <label for="<?php echo esc_html($value2->slug) ?>-<?php echo esc_attr($data->name); ?>" style="margin-left: 40px;"><?php echo esc_html($value2->name) ?></label>
							    </span>

					
				        <?php } ?>

			
		        <?php } ?>
		
	<?php } 
	}

	if(isset($data->options_source) && empty($data->taxonomy) ) {
		if(isset($data->options_cb) && !empty($data->options_cb) ){
			switch ($data->options_cb) {
				case 'listeo_core_get_offer_types':
					$data->options = listeo_core_get_offer_types_flat(false);
					break;

				case 'listeo_core_get_listing_types':
					$data->options = listeo_core_get_listing_types();
					break;

				case 'listeo_core_get_rental_period':
					$data->options = listeo_core_get_rental_period();
					break;
			
				default:
					# code...
					break;
			}	
		}
		if($data->options_source == 'custom') {
			//$data->options = array_flip($data->options);
		}
		
		foreach ($data->options as $key => $value) { ?>

			<input <?php if ( array_key_exists ($key, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($key).']'; ?>">
			<label for="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value) ?></label>
		
	<?php } 
	}
}
?>


</div>

<script type="text/javascript">
  jQuery(document).on("click","#<?php echo esc_attr($data->name); ?> input",function(){
  	 //jQuery("#<?php echo esc_attr($data->name); ?>").find("input").not(this).prop("checked",false);

  	 var dd_child = jQuery(this).data("child");
  	 if(jQuery(this).prop("checked") == true){
         
          jQuery("."+dd_child).show();
  	 }else{
          jQuery("."+dd_child).hide();
          jQuery("."+dd_child).find("input").prop("checked",false);
  	 }
  
  });
</script>