<!-- The Modal -->
<?php

?>

<div id="edit_modal<?php echo $data->id;?>" class="modal message_modal edit_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
     <!--  <h2><?php  echo __("Rask redigering","Gibbs");?></h2> -->
    </div>
    <div class="modal-body">
      <form class="edit_form<?php echo $data->id;?>" action="javascript:void(0)" method="post" >
        <input type="hidden" name="booking_id" value="<?php echo $data->id;?>">
        <input type="hidden" name="booking_author" value="<?php echo $data->bookings_author;?>">
        <input type="hidden" name="action" value="save_single_booking">
        <input type="hidden" name="order_id" value="<?php echo $data->order_id;?>">
        <div id="edit_form_div">
          
          <div class="row" style="padding: 15px 15px 5px 15px;background: white;" >
              <div class="col-xs-6 col-md-6">
                <div class="form-group" style="display: none;">
                  <label for="price">Price</label>
                  <input type="number" name="price" class="form-control" id="price" value="<?php echo $data->price;?>" aria-describedby="emailHelp" placeholder="Enter Price">
                </div>
               <!--  <div class="form-group">
                  <label for="price">Discount Group</label>
                  <select class="form-control">
                    <option>Select discount</option>
                  </select>
                </div> -->
                <div class="form-group">
                  <label for="price">Notat</label>
                  <textarea class="note" name="description" style="height: 25px;margin: 1px;"><?php echo $data->description;?></textarea>
                </div>
              </div>
              <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <label for="billing_name">Faktura navn</label>
                    <input type="text" name="billing_name" value="<?php echo $data->billing_name;?>" class="form-control" id="billing_name" aria-describedby="emailHelp" placeholder="Enter name">
                  </div>
                  <div class="form-group">
                    <label for="billing_email">Faktura e-post</label>
                    <input type="text" name="billing_email" value="<?php echo $data->billing_email;?>" class="form-control" id="billing_email" aria-describedby="emailHelp" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="billing_phone">Faktura tlf</label>
                    <input type="text" name="billing_tlf"   value="<?php echo $data->billing_tlf;?>" class="form-control" id="billing_phone" aria-describedby="emailHelp" placeholder="Enter phone">
                  </div>
                  <div class="form-group">
                    <label for="billing_address">Faktura adresse</label>
                    <input type="text" name="billing_address"  value="<?php echo $data->billing_address;?>" class="form-control" id="billing_address" aria-describedby="emailHelp" placeholder="Enter address">
                  </div>
                  <div class="form-group">
                    <label for="billing_zipcode">Faktura postnr</label>
                    <input type="text" name="billing_zip"  value="<?php echo $data->billing_zip;?>" class="form-control" id="billing_zipcode" aria-describedby="emailHelp" placeholder="Enter zipcode">
                  </div>
                  <div class="form-group">
                    <label for="billing_city">Faktura by</label>
                    <input type="text" name="billing_city"  value="<?php echo $data->billing_city;?>" class="form-control" id="billing_city" aria-describedby="emailHelp" placeholder="Enter city">
                  </div>
                  <div class="row" style="background: white;">
                      <div class="col-xs-6 col-md-6" style="padding:20px; text-align: center;">
                          
                      </div>
                      <div class="col-xs-6 col-md-6" style = "padding: 20px; text-align: center;">
                          <button type="button"  class="button closebtn">Lukk</button>
                          <button type="submit"  class="button savebtn">Lagre</button>
                      </div>
                  </div>
              </div>
          </div>
         
      </div>
    </form>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".edit_modalbtn<?php echo $data->id;?>",function(){

        jQuery("#edit_modal<?php echo $data->id;?>").show();
})

jQuery(document).on("click",".close_modal,.closebtn",function(){
        jQuery("#edit_modal<?php echo $data->id;?>").hide();
})

jQuery(document).on('submit','.edit_form<?php echo $data->id;?>',function(e) {
    e.preventDefault();

    jQuery(".booking_datatable").addClass("loading_class");

    var formdata = jQuery(this).serialize();

   
    jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: formdata,
        success: function(data){
           window.location.reload()
        }
    });
});




</script>