<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>
<div class="section_listing" style="padding: 0px 0px 80px 0px;">

    <div class="row">

        <div class="col-md-2">
           <div id="add_sections"><span>Legg til ny inndeling/bane</span><i class="fa fa-plus" aria-hidden="true"></i></div>
        </div>
        <div class="col-md-4 section_checkbox_main">
            <div class="checkbox_div2">
              <div class="dynamic checkboxes in-row">  
                
                <input  id="all_section_sp" type="checkbox" name="all_section_sp" >
                <label for="all_section_sp">Alle inndelinger er egnet for det samme</label>    
              </div>  
            </div>
        </div>
        
    </div>
    <div class="section_main_div">

    </div>
</div>
<?php
$users_groups_id = "";
$parent_listing_id = "";
if(isset($_REQUEST['listing_id']) && $_REQUEST['listing_id'] != ""){
    $post_dd = get_post($_REQUEST['listing_id']);
    $users_groups_id = $post_dd->users_groups_id;
    $parent_listing_id = $_REQUEST['listing_id'];
}

?>

 <script type="text/javascript">

    function get_sections(users_groups_id,parent_listing_id){
        jQuery.ajax({
          type: 'POST', 
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
          data: { 
            'action': 'listeo_get_sections', 
            'users_groups_id' : users_groups_id,
            'parent_listing_id' : parent_listing_id,
          },
          success: function(data){
              jQuery(".section_main_div").html(data);
          }
        });
    }

    get_sections("<?php echo $users_groups_id;?>","<?php echo $parent_listing_id;?>");

    function add_sections(count,users_groups_id,parent_listing_id){
        jQuery.ajax({
          type: 'POST', 
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
          data: { 
            'action': 'listeo_add_sections', 
            'count' : count,
            'users_groups_id' : users_groups_id,
            'parent_listing_id' : parent_listing_id,
          },
          success: function(data){
              jQuery(".section_main_div").append(data);
              jQuery("#add_sections").prop("disabled",false);
          }
        });
    }


   

    jQuery(document).on("click","#add_sections",function(){

        jQuery(".form-field-_listing_sports-container").hide();

        jQuery("#add_sections").prop("disabled",true);

        if(jQuery(".section_col").length < 15){

            if(jQuery(".section_col").length == 0){
               add_sections(2,jQuery("#_user_groups_id").val(),"<?php echo $parent_listing_id;?>");
            }else{
               add_sections(1,jQuery("#_user_groups_id").val(),"<?php echo $parent_listing_id;?>");
            }
        }    


    })


    jQuery(document).on("change","#_user_groups_id",function(){
        get_sections(jQuery(this).val(),"<?php echo $parent_listing_id;?>");
    })

    jQuery("#all_section_sp").on("change",function(){
        if(this.checked == true){
          jQuery(".listeo_core-sports_list:first").find("input").each(function(){
              var this_v = this.value;
              jQuery("input[value="+this_v+"]").prop("checked",this.checked);
          })
        }
    })

    jQuery(document).on("change",".listeo_core-sports_list input",function(){

         if(jQuery("#all_section_sp")[0].checked == true){
            var this_v = this.value;
            jQuery("input[value="+this_v+"]").prop("checked",this.checked);
         }

    });

    jQuery(document).on("click",".delete_sub_listing",function(){
      if(jQuery(".section_main_div").find(".delete_sub_listing").length == 2){
        if (confirm("Are you sure you want to delete!") == true) {
          jQuery(".section_main_div").find(".delete_sub_listing").each(function(){
              var sub_listing_id = jQuery(this).attr("sub_listing_id");
              var _that = this;
              jQuery.ajax({
                type: 'POST', 
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: { 
                  'action': 'delete_sub_listing', 
                  'sub_listing_id' : sub_listing_id,
                },
                success: function(data){
                   jQuery(_that).parent().parent().parent().remove();
                }
              });
          })
        }
      }else{

        if (confirm("Are you sure you want to delete!") == true) {
          var sub_listing_id = jQuery(this).attr("sub_listing_id");
          var _that = this;
          jQuery.ajax({
            type: 'POST', 
            url: "<?php echo admin_url( 'admin-ajax.php' );?>",
            data: { 
              'action': 'delete_sub_listing', 
              'sub_listing_id' : sub_listing_id,
            },
            success: function(data){
               jQuery(_that).parent().parent().parent().remove();
            }
          });
        } 

      }
        

    });


     
 </script>