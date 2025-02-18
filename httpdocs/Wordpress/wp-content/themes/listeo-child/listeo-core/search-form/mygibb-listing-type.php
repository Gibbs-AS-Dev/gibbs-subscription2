<?php

if(isset($data->options_cb) && !empty($data->options_cb)){

	switch ($data->options_cb) {
		case 'listeo_core_get_offer_types':
			$data->options = listeo_core_get_offer_types_flat(false);
			break;

		case 'listeo_get_listing_types':
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

//var_dump($data);
if(isset($_GET[$data->name])) {
	$selected = sanitize_text_field($_GET[$data->name]);
} else {
	$selected = '';
	if(isset($data->default) && !empty($data->default)){
		$selected = $data->default;
	} else {
		$selected = '';	
	}
} 
global $wp_query;
$vars = $wp_query->query_vars;
//echo "<pre>"; print_r($vars); die;

?>
<div class="checkboxes one-in-row <?php if(isset($data->class)) { echo esc_attr($data->class); }?>" id="<?php echo esc_attr($data->name); ?>">

<?php if(isset($data->dynamic) && $data->dynamic=='yes'){ ?>
	<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','listeo_core') ?></p> </div>
<?php } else {


	if(isset($_GET[$data->name])) {
		$selected = array($_GET[$data->name]);
	} else {
		$selected = array();
	} 
	
		//$data->options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
		/*if(is_tax($data->taxonomy)){
			$selected[get_query_var($data->taxonomy)] = 'on';
		}	*/
		foreach ($data->options as $key => $value) { ?>

			<?php 
					if(isset($vars[$key."_category"])){
                        $selected[] = $key; 
					}
					?>

			<input <?php if ( in_array ($key, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_attr($key);?>" value="<?php echo esc_html($key) ?>" type="checkbox" name="<?php echo esc_attr($data->name);?>">
			<label for="<?php echo esc_attr($key);?>"><?php echo esc_html($value) ?></label>
		
	<?php } 

}
?>


</div>

<?php if(!$data->multi){ ?>
<script type="text/javascript">
  jQuery(document).on("click","#<?php echo esc_attr($data->name); ?> input",function(){
  	 jQuery("#<?php echo esc_attr($data->name); ?>").find("input").not(this).prop("checked",false);
  
  });
</script>

<?php } ?>

<!-- 
<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
		<select <?php if( isset($data->multi) && $data->multi == '1') { echo 'multiple class="select2-multiple"'; } else { echo 'class="select2-single"'; } ?> name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>"  data-placeholder="<?php echo esc_attr($data->placeholder);?>"  >
			<option value="-1"><?php echo esc_attr($data->placeholder);?></option> 
			<?php 
			if( is_array( $data->options ) ) :
				foreach ($data->options as $key => $value) { ?>
					<?php 
					if(isset($vars[$key."_category"])){
                        $selected = $key; 
					}
					?>
					<option <?php selected($selected, $key) ?> value="<?php echo esc_html($key);?>"><?php echo esc_html($value);?></option>
				<?php }
			endif;
			?>
		</select>
</div> -->
 <script type="text/javascript">


          	

          jQuery("#_listing_type").find("input").on("change",function(){

             // jQuery(".panel-apply").click();

              if(jQuery("#_listing_type").find("input:checked").val() == "event"){
                jQuery("#tax-event_category-panel").show();
                jQuery("#tax-service_category-panel").hide()
                jQuery("#tax-service_category-panel").find("input").prop("checked",false);
                jQuery("#tax-service_category-panel").find(".greenThenWhite").html("");
                jQuery("#tax-rental_category-panel").hide()
                jQuery("#tax-rental_category-panel").find("input").prop("checked",false);
                jQuery("#tax-rental_category-panel").find(".greenThenWhite").html("");
              }else if(jQuery("#_listing_type").find("input:checked").val() == "service"){

                jQuery("#tax-service_category-panel").show();
                jQuery("#tax-rental_category-panel").hide();
                jQuery("#tax-rental_category-panel").find("input").prop("checked",false)
                jQuery("#tax-rental_category-panel").find(".greenThenWhite").html("");
                jQuery("#tax-event_category-panel").hide();
                jQuery("#tax-event_category-panel").find("input").prop("checked",false)
                jQuery("#tax-event_category-panel").find(".greenThenWhite").html("");
                
              }else if(jQuery("#_listing_type").find("input:checked").val() == "rental"){
                jQuery("#tax-rental_category-panel").show();
                jQuery("#tax-service_category-panel").hide();
                jQuery("#tax-service_category-panel").find("input").prop("checked",false);
                jQuery("#tax-service_category-panel").find(".greenThenWhite").html("");
                jQuery("#tax-event_category-panel").hide();
                jQuery("#tax-event_category-panel").find("input").prop("checked",false);
                jQuery("#tax-event_category-panel").find(".greenThenWhite").html("");
                
              }else{
                jQuery("#tax-rental_category-panel").show();
                jQuery("#tax-service_category-panel").show();
                jQuery("#tax-event_category-panel").show();
                
               /* debugger;
                jQuery("#tax-rental_category-panel").find(".greenThenWhite").html("");
                jQuery("#tax-service_category-panel").find(".greenThenWhite").html("");
                jQuery("#tax-event_category-panel").find(".greenThenWhite").html("");*/
              }
              jQuery("#_listing_type-panel").find(".text_inner").show();

              if(jQuery("#_listing_type-panel").find("input:checked").length == 0){
                  jQuery("#_listing_type-panel").find(".greenThenWhite").html("");
                  jQuery("#_listing_type-panel").find(".fa-times").hide();
              }else{
              	if(jQuery("#_listing_type-panel").find("input:checked").length != 0){
              		jQuery("#_listing_type-panel").find(".text_inner").hide();
              		//jQuery("#_listing_type-panel").find(".cross_a").remove();
              		jQuery("#_listing_type-panel").find(".fa-times").show();
    		        jQuery("#_listing_type-panel").find(".fa-times").css({"float":"right"})
              		jQuery("#_listing_type-panel").find(".greenThenWhite").html("(1) "+jQuery("#_listing_type-panel").find("input:checked").next().text());
              	}else{
              		jQuery("#_listing_type-panel").find(".greenThenWhite").html("");
              		jQuery("#_listing_type-panel").find(".fa-times").hide();
              	}
              	


              }
               
          });
            jQuery(document).ready(function(){
	 	    	setTimeout(function(){

	               jQuery("#_listing_type").find("input:first").change();

	 	    	},500);
	 	    	
	 	    })

        </script>
