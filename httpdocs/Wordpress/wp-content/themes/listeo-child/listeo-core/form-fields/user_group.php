<?php
global $wpdb;
        $cuser_id = get_current_user_ID();
        $groups_table =$wpdb->prefix .'users_groups';
        $users_groups_table =$wpdb->prefix .'users_and_users_groups';
        $groups = array();
        if($cuser_id){
        	$query = "SELECT id,name FROM $groups_table WHERE id IN (SELECT users_groups_id FROM $users_groups_table WHERE users_id = $cuser_id)";
            $group_id_data = $wpdb->get_results($query);
            foreach ($group_id_data as $key => $group_dd) {
            	$groups[$group_dd->id] = $group_dd->name;
            }
           
        }
$users_groups_id = "";
if(isset($_REQUEST['listing_id']) && $_REQUEST['listing_id'] != ""){
    $users_groups_id = $post_dd->users_groups_id;
    $post_dddd =   get_post($_REQUEST['listing_id']);

}
$field = $data->field;
if(empty($groups)){
?>
<style type="text/css">
	.form-field-_user_groups_id-container{
		display: none !important;
	}
</style>
<?php	
}
?>
<select name="<?php echo $field['name'];?>" id="<?php echo $field['name'];?>">
	<option value="0">Velg</option>
	<?php
	foreach ($groups as $key_gr => $group) {
    ?>
      <option value="<?php echo $key_gr;?>" <?php if(isset($post_dddd->users_groups_id) && $post_dddd->users_groups_id  == $key_gr){ echo 'selected';}?>><?php echo $group;?></option>
    <?php
	}
	?>
    
</select>

<?php
if(isset($post_dddd->users_groups_id) && $post_dddd->users_groups_id != "" && $post_dddd->users_groups_id != "0"){ ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
         jQuery(".form-field-_listing_available_for-container").show();
    })
</script>
<?php }else{ ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
         jQuery(".form-field-_listing_available_for-container").hide();
    })
</script>
<?php } ?>
