<?php

if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	if(isset($data->default) && !empty($data->default)){
		$value = $data->default;
	} else {
		$value = '';	
	}
}

?>

<div id="<?php echo esc_attr($data->name);?>-content"
	class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> 
	<?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
	<div class="row">
		<div class="col-md-3">
			<?php echo esc_html_e( 'From Date' ); ?>
			<input autocomplete="off" 
				name="<?php echo esc_attr($data->name);?>-from-date"
				data-type="date"
				id="<?php echo esc_attr($data->name);?>-from-date"
				class="<?php echo esc_attr($data->name);?>"
				type="text"
				placeholder="<?php echo esc_attr($data->placeholder);?>" 
				value="<?php if(isset($value)){ echo $value;  } ?>" />
		</div>
		<div class="col-md-3">
			<?php echo esc_html_e( 'To Date' ); ?>
			<input autocomplete="off"
				name="<?php echo esc_attr($data->name);?>-to-date"
				data-type="date"
				id="<?php echo esc_attr($data->name);?>-to-date"
				class="<?php echo esc_attr($data->name);?>" type="text"
				placeholder="<?php echo esc_attr($data->placeholder);?>" 
				value="<?php if(isset($value)){ echo $value;  } ?>" />
		</div>
	
		<div class="col-md-3 form-group"
			id="<?php echo esc_attr($data->name);?>-to-time">
			<?php echo esc_html_e( 'From Time' ); ?>
			<select name="<?php echo esc_attr($data->name);?>-from-time"
				data-type="time"
				id="<?php echo esc_attr($data->name);?>-from-time"
				class="<?php echo esc_attr($data->name);?>" type="time" />
				<option value=''>
					<?php esc_html_e( 'Select time' ); ?>
				</option>
			<?php foreach( halfHourTimes('G:i') as $time ) {
				printf('<option value="%s">%s</option>', $time, $time );
			} ?>
			</select>
		</div>

		<div class="col-md-3 form-group"
			id="<?php echo esc_attr($data->name);?>-to-time">
			<?php echo esc_html_e( 'To Time' ); ?>
			<select name="<?php echo esc_attr($data->name);?>-to-time"
				data-type="time"
				id="<?php echo esc_attr($data->name);?>-to-time"
				class="<?php echo esc_attr($data->name);?>" type="time" />
				<option value=''>
					<?php esc_html_e( 'Select time' ); ?>
				</option>
			<?php foreach( halfHourTimes('G:i') as $time ) {
				printf('<option value="%s">%s</option>', $time, $time );
			} ?>
			</select>
		</div>
	</div>
	<div class="row">
		<button type="button" class="button" style="float:right;"
			id="<?php echo esc_attr($data->name);?>-apply">
			<?php esc_html_e( 'Apply', '' ); ?>
		</button>
		<button type="button" class="button" style="float:right;"
			id="<?php echo esc_attr($data->name);?>-clear">
			<?php esc_html_e( 'Clear', '' ); ?>
		</button>
	</div>
</div>