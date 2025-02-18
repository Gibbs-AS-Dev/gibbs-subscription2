<?php

if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	if(isset($data->default) && !empty($data->default)){
		$value = $data->default;
	} else {
		// $value = '';	
	}
}

?>

<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> 
	<?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">

    <div>
        <span class="range-slider-headline"><?php echo esc_html($data->placeholder); ?></span>
        <span class="range-slider-subtitle">Velg antall kapasitet</span>
        <input autocomplete="off"
            data-slider-min="1"
            data-slider-max="100"
            data-slider-step="1"
            data-slider-value="[1,100]"
            data-slider-currency = <?php esc_attr_e( 'people', 'listeo_core' ); ?>
            name="<?php echo esc_attr($data->name);?>"
            id="<?php echo esc_attr($data->name);?>"
            class="bootstrap-range-slider <?php echo esc_attr($data->name);?>"
            type="text"
            placeholder="<?php echo esc_attr($data->placeholder);?>" 
            value="<?php if(isset($value)){ echo $value;  } ?>" />
    </div>
    <span class="slider-disable" data-disable="<?php esc_html_e('Disable','listeo_core');?><?php echo esc_html($data->placeholder) ?> " data-enable="<?php esc_html_e('Enable','listeo_core');?> <?php echo esc_html($data->placeholder) ?> "><?php esc_html_e('Enable','listeo_core');?> <?php echo esc_html($data->placeholder) ?></span>
</div>