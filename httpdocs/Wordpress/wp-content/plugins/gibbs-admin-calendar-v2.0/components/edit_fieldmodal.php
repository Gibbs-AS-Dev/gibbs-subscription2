<div id="edit_fieldmodal<?php echo $booking->id;?>" class="modal common_modal edit_fieldmodal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <h2><?php  echo __("Rediger","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <form method="post" class="fields_form" action="<?php echo home_url();?>/wp-admin/admin-ajax.php">
        <input type="hidden" name="action" value="edit_field_save_for_calender_mobiscroll">
        <input type="hidden" name="booking_id" value="<?php echo $booking->id;?>">
        <input type="hidden" name="cal_type" value="<?php echo $_POST['cal_type'];?>">
        <input type="hidden" name="cal_view" value="<?php echo $_POST['cal_view'];?>">
        <div id="edit_form_div">
          

              <?php 
               // if(!empty($fields_rows)){ 
                  echo repeated_fields($fields_rows,$group_id);
               // }
              ?>


          <div class="row" style="background: white;">
            <!--     <div class="col-xs-12 col-md-12" style="padding:20px; text-align: center;">
                    
                </div> -->
                <div class="col-xs-12 col-md-12" style = "padding: 20px; text-align: end;">
                    <button type="button"  class="button closebtn">Lukk</button>
                    <button type="button"  class="button fields_save">Lagre</button>
                </div>
            </div>
         
      </div>
    </form>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".edit_fieldmodal_btn<?php echo $booking->id;?>",function(){

        jQuery("#edit_fieldmodal<?php echo $booking->id;?>").show();
})

jQuery(document).on("click",".close_modal,.closebtn",function(){
        jQuery("#edit_fieldmodal<?php echo $booking->id;?>").hide();
})

jQuery(document).on('submit','.edit_form<?php echo $booking->id;?>',function(e) {
    e.preventDefault();

    jQuery(".booking_datatable").addClass("loading_class");

    var formdata = jQuery(this).serialize();

   
    jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo home_url();?>/wp-admin/admin-ajax.php",
        data: formdata,
        success: function(data){
           window.location.reload()
        }
    });
});

jQuery(".fields_save").click(function(e){
    e.preventDefault();
    jQuery(".empty_div").removeClass("empty_div");


      let error = false;
      jQuery(".fields_form").find(".required").each(function(){

          if(this.value == ""){
            jQuery(this).focus();
            jQuery(this).addClass("empty_div");
            jQuery(this).parent().find(".select2-container").addClass("empty_div");
            error = true;
            
          }
      })
      jQuery(".fields_form").find("input[type=checkbox]").each(function(){

          if(jQuery(this).hasClass("required")){
               if(this.checked == false){
                  jQuery(this).focus();
                  jQuery(this).addClass("empty_div_checkbox");
                  error = true;
                  return false;
               }
          }
      })

      if(error == false){
        jQuery(".fields_form").submit();
      }
})


</script>