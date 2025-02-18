<div class="checkboxes one-in-row <?php if(isset($data->class)) { echo esc_attr($data->class); }?>" id="<?php echo esc_attr($data->name); ?>">

<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
	<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
<?php } else {


	if(isset($_GET[$data->name])) {
		$selected = $_GET[$data->name];

	} else {
		$selected = array();
	} 
	

	if(isset($data->taxonomy) && !empty($data->taxonomy)) {
		$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
		if(is_tax($data->taxonomy)){
			$selected[get_query_var($data->taxonomy)] = 'on';
		}	
		foreach ($data->options as $key => $value) { ?>

			<input <?php if ( in_array ($value['slug'], $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value['slug']) ?>-<?php echo esc_attr($data->name); ?>" value="<?php echo esc_html($value['slug']) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value['slug']).']'; ?>">
			<label for="<?php echo esc_html($value['slug']) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value['name']) ?></label>
		
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

			<input <?php if ( in_array ($key, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>" type="checkbox" name="<?php echo $data->name.'[]'; ?>" value="<?php echo esc_html($key);?>">
			<label for="<?php echo esc_html($key) ?>-<?php echo esc_attr($data->name); ?>"><?php echo esc_html($value) ?></label>
		
	<?php } 
	}
}
?>


</div>
<script type="text/javascript">
    jQuery("#<?php echo $data->name;?>").find("input").change(function(){

    	 if(jQuery(this).parent().hasClass("main_span")){
	  	 	 if(jQuery(this).prop("checked") == false){
	  	 	 	jQuery(this).parent().parent().find(".child_span1").hide();
	  	 	 	jQuery(this).parent().parent().find(".child_span1").find("input").prop("checked",false);
	  	 	 	jQuery(this).parent().parent().find(".child_span2").hide();
	  	 	 	jQuery(this).parent().parent().find(".child_span2").find("input").prop("checked",false);
	  	 	 	jQuery(this).parent().parent().find(".child_span3").hide();
	  	 	 	jQuery(this).parent().parent().find(".child_span3").find("input").prop("checked",false);
	  	 	 }
	  	 }

	  	 var dd_child = jQuery(this).data("child");
	  	 if(jQuery(this).prop("checked") == true){
	         
	          jQuery("."+dd_child).show();

	  	 }else{

	          jQuery("."+dd_child).hide();
	          jQuery("."+dd_child).find("input").prop("checked",false);
	  	 }
    	var htmll = [];
    	var count_list = "";
    	var cross = "";
    	var le_ch = jQuery("#<?php echo $data->name;?>").find("input:checked").length;
    	if(le_ch > 0){
    		count_list += "("+le_ch+") "; 
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").show();
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").css({"float":"right"})
    	}else{
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").removeAttr("style");
    	}

    	jQuery("#<?php echo $data->name;?>").find("input:checked").each(function(){
            // htmll += jQuery(this).val()+", ";
             htmll.push(jQuery(this).val());
    	});
    	//jQuery("#<?php echo $data->name;?>-panel").find(".cross_a").remove();



    	jQuery("#<?php echo $data->name;?>-panel").find(".greenThenWhite").html(count_list+" "+htmll.join(", "));
    	
    	
    })

	//jQuery("#<?php echo $data->name;?>").find("input:first").change();
</script>