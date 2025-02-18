<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;

$key = $data->key;

$multi = false;


?>

<select <?php if($multi) echo "multiple"; ?> class="<?php if($multi) echo "chosen-select-no-single"; ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : $key ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key );  if($multi) echo "[]"; ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>>
	
	<?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
		<option value=""><?php echo esc_attr($field['placeholder']);?></option>
	<?php endif ?>
	<?php 
		$default = ( isset( $field['value']  ) && !empty($field['value']) ) ? $field['value'] : CMB2_Utils::timezone_string();
		echo wp_timezone_choice($default);
 ?>
</select>