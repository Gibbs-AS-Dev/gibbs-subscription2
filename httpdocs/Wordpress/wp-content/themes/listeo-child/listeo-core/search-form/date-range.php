<?php
if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	$value = '';
}

$date_range_type = (isset($data->date_range_type)) ? $data->date_range_type : 'rental' ;
?>


<div class="search-input-icon <?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
	<input readonly="readonly" autocomplete="off" name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>" class="<?php echo esc_attr($data->name);?>" type="text" placeholder="<?php echo esc_attr($data->placeholder);?>" value="<?php if(isset($value)){ echo $value;  } ?>"/>
	<i class="fa fa-calendar"></i>
</div>
<?php if($date_range_type != 'custom') { ?>
<input type="hidden" <?php if(!isset($_GET['_listing_type'])) { ?> disabled="disabled" <?php } ?> name="_listing_type111" value="<?php echo esc_attr($date_range_type); ?>">
<?php } ?>

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