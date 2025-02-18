
<?php
$users_groups_id = "";
if(isset($_REQUEST['listing_id']) && $_REQUEST['listing_id'] != ""){
    $post_dd = get_post($_REQUEST['listing_id']);
    $users_groups_id = $post_dd->users_groups_id;
    global $wpdb;
    $posts_table = $wpdb->prefix . 'posts';

    $query = "SELECT * FROM $posts_table WHERE post_type='listing' && post_parent = ".$_REQUEST['listing_id'];
    $post_parentdata = $wpdb->get_results($query);
    if(!empty($post_parentdata)){
     ?>
     <!-- <style type="text/css">
         .form-field-_listing_sports-container{
            display: none !important;
         }
     </style> -->
     <?php   

    }

}

?>
<script type="text/javascript">

    jQuery(document).ready(function(){
         jQuery(".form-field-_listing_sports-container").hide();
    })

    function get_sports(group_id){
        jQuery.ajax({
          type: 'POST', 
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
          data: { 
            'action': 'listeo_get_sports', 
            'users_groups_id' : group_id,
            'listing_id' : "<?php echo $_REQUEST['listing_id'];?>",
          },
          success: function(data){
            data = data.replace("\n","");

            if(data != ""){
                jQuery(".form-field-_listing_sports-container").show();
            } else{
                jQuery(".form-field-_listing_sports-container").hide();
            }   
            jQuery(".form-field-_listing_sports-container").find(".checkboxes").html(data);
            
          }
        });
    }
    get_sports("<?php echo $users_groups_id;?>");

     jQuery(document).on("change","#_user_groups_id",function(){
        get_sports(jQuery(this).val());
        if(jQuery(this).val() != "" && jQuery(this).val() != "0"){
            jQuery(".form-field-_listing_available_for-container").show();
        }else{
            jQuery(".form-field-_listing_available_for-container").hide();
        }
    })
</script>