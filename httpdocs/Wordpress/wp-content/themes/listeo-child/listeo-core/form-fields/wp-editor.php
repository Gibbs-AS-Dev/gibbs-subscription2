<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;


if(isset($field['description']) && !empty($field['description'])){
	echo '<div class="notification closeable notice"><p class="description" id="'.$key.'-description">'.$field['description'].'</p></div>';
}
if ( ! empty( $field['required'] ) ){
   $required = "required";
   $addlClass = 'my-required-field';
}else{
   $addlClass = "";
   $required = "";
}

$addlClass = ($required) ? 'my-required-field' : '';

$editor = apply_filters( 'submit_listing_form_wp_editor_args', array(
	'textarea_name' => isset( $field['name'] ) ? $field['name'] : $key,
	'media_buttons' => false,
	'textarea_rows' => 8,
	'quicktags'     => false,
	'editor_class'  => $addlClass,
	'tinymce'       => array(
		'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
		'paste_as_text'                 => true,
		'paste_auto_cleanup_on_paste'   => true,
		'paste_remove_spans'            => true,
		'paste_remove_styles'           => true,
		'paste_remove_styles_if_webkit' => true,
		'paste_strip_class_attributes'  => true,
		'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
		'toolbar2'                      => '',
		'toolbar3'                      => '',
		'toolbar4'                      => ''
	),
) );
wp_editor( isset( $field['value'] ) ? wp_kses_post( $field['value'] ) : '', $key, $editor );
?>
<div class="margin-top-30"></div>
<?php if ( ! empty( $field['required'] ) ){
 ?>

<script type="text/javascript">
	
	jQuery(document).on("click","button[name=submit_listing]",function(){
		/*alert("<?php echo $field['name'];?>")
		if(jQuery("#<?php echo $field['name'];?>").val() == ""){
			alert("<?php echo $field['name'];?> fields is required!")
		}*/
	})
</script>
<?php } ?>

<?php if ( isset( $field['number_of_char'] ) && $field['number_of_char'] != "" ){
 ?>

<script type="text/javascript">
	    jQuery(document).ready(function(){
             jQuery("#<?php echo $field['name'];?>").attr("limitchar","<?php echo $field['number_of_char'];?>");
		})	
</script>
<?php } ?>