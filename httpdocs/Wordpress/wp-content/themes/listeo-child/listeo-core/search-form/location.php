<?php
if(isset($_GET[$data->name])) {
	$value = stripslashes(sanitize_text_field($_GET[$data->name]));
} else {
	if(isset($data->default) && !empty($data->default)){
		$value = $data->default;
	} else {
		$value = '';	
	}
} 
?>
<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
	<div id="autocomplete-container">
		<input  autocomplete="off" name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>" type="text" placeholder="<?php echo esc_attr($data->placeholder);?>" value="<?php if(isset($value)){ echo esc_attr($value); }  ?>"/>
	</div>
	<a href="#"><i title="<?php esc_html_e('Find My Location','listeo_core') ?>" class="tooltip left fa fa-map-marker"></i></a>
	<span class="type-and-hit-enter"><?php esc_html_e('type and hit enter','listeo_core') ?></span>
</div>
<script type="text/javascript">
    jQuery("#<?php echo $data->name;?>-panel").find("input[type=text]").change(function(){
    	
    	var htmll = [];
    	var count_list = "";
    	var cross = "";
    	var le_ch = jQuery(this).val().length;
    	if(le_ch > 0){
    		count_list += "(1) "; 
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").show();
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").css({"float":"right"})
    	}else{
    		jQuery("#<?php echo $data->name;?>-panel").find(".fa-times").removeAttr("style");
    	}

    	//jQuery("#<?php echo $data->name;?>").find("input").each(function(){
            // htmll += jQuery(this).val()+", ";
             htmll.push(jQuery(this).val());
    	//});
    	//jQuery("#<?php echo $data->name;?>-panel").find(".cross_a").remove();



    	jQuery("#<?php echo $data->name;?>-panel").find(".greenThenWhite").html(count_list+" "+htmll.join(", "));
    	
    	
    })

	//jQuery("#<?php echo $data->name;?>-panel").find("input[type=text]").change();
</script>
