<?php
$multi = false;
if(isset($data->multi) && $data->multi) {
	$multi = true;
}
if(isset($_GET[$data->name])) {
	if(is_array($_GET[$data->name])){
		$selected = $_GET[$data->name];
	} else {
		$selected = sanitize_text_field($_GET[$data->name]);	
	}
} else {
	$selected = '';

	if(is_tax($data->taxonomy)){
		$selected = get_query_var($data->taxonomy);
	}

} 

?>

<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?> <?php if(isset($data->dynamic) && $data->dynamic=='yes'){ echo esc_attr('dynamic'); }?>" id="listeo-search-form_<?php echo esc_attr($data->name);?>">
	<select id="<?php echo esc_attr($data->name);?>"
	<?php if($multi) : ?> 
		multiple name="<?php echo esc_attr($data->name);?>[]"  class="select2-multiple" 
	<?php else : ?>
		name="<?php echo esc_attr($data->name);?>"  class="select2-single" 
	<?php endif; ?>
	 data-placeholder="<?php echo esc_attr($data->placeholder);?>">
	 <?php if(!$multi) : ?>
		<option value="0"><?php echo esc_attr($data->placeholder);?></option>
	<?php endif; ?>
		<?php 

		$terms = get_terms($data->taxonomy, array('hide_empty' => true));
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){	
			$options = listeo_core_get_options_array_hierarchical($terms,$selected);
			echo $options;
		}
		//$options = listeo_core_get_options_array('taxonomy',$data->taxonomy);
		//echo get_listeo_core_options_dropdown($options,$selected) ?>
	</select>
</div>
