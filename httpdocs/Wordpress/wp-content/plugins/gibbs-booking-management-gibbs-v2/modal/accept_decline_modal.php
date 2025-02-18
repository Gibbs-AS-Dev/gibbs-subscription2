<!-- The Modal -->
<?php

?>

<div id="accept_decline_modal<?php echo $data->id;?>" class="modal message_modal action_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <div class="row top_div" >
          <div class="col-xs-12 col-md-12">
              <i class="fa fa-exclamation-circle"></i>
          </div>
          <div class="col-xs-12 col-md-12">
              <span>Er du sikker?</span>
          </div>
      </div>
    </div>
    <div class="modal-body">
      <form class="action_modal_form<?php echo $data->id;?>" action="javascript:void(0)" method="post" >

        <input type="hidden" name="status"  required="">
        <input type="hidden" name="booking_id"  value="<?php echo $data->id;?>" required>
       
        <div id="action_modal_div">
          
          <div class="row" >
            <?php if($data->first_event_id == "true"){ ?>
              <div class="col-xs-12 col-md-12 other_booking">
                  <i class="fa fa-exclamation-circle"></i><span>Denne reservasjonen er relatert til andre. <br> Vennligst trykk åpne for mer informasjon.</span>
              </div>
            <?php } ?>  
        <!--     <?php if($data->conflict == "true"){ ?>
              <div class="col-xs-12 col-md-12 conflict_booking">
                  <i class="fa fa-exclamation-circle"></i><span>Det er en konflikt i denne reservasjonen. <br> Vennligst trykk åpne for mer informasjon.</span>
              </div>
            <?php } ?>  -->
             <!--  <div class="col-xs-12 col-md-12 conflict_booking">
                  <i class="fa fa-exclamation-circle"></i><span>There is the conflict in this booking.Please open the booking to see more info.</span>
              </div> -->
          </div>
          <div class="row" style="background: white;">
              <div class="col-xs-6 col-md-6 left_btn">
                 <button type="submit"  class="button yesbtn">Ja</button>
              </div>
              <div class="col-xs-6 col-md-6 right_btn">
                 <button type="button" data-link="?booking_id=<?php echo $data->id;?>" class="button openbtn open_link">Åpne</button>
              </div>
          </div>
      </div>
    </form>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".accept_decline_modalbtn<?php echo $data->id;?>",function(){

        var status = jQuery(this).data("value");

        jQuery(".action_modal_form<?php echo $data->id;?>").find("input[name=status]").val(status);

        jQuery("#accept_decline_modal<?php echo $data->id;?>").show();
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#accept_decline_modal<?php echo $data->id;?>").hide();
})

jQuery(document).on('submit','.action_modal_form<?php echo $data->id;?>',function(e) {
    e.preventDefault();

    jQuery(".booking_datatable").addClass("loading_class");

    let booking_id = jQuery(this).find("input[name=booking_id]").val();
    let status = jQuery(this).find("input[name=status]").val();
    var ajax_data = {
          'action': 'listeo_bookings_manage',
          'booking_id' : booking_id,
          'status' : status,
          'owner_action' : true,
          //'nonce': nonce
      };
    jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: ajax_data,
        success: function(data){

         // debugger;
          if(data && data.data && data.data.status){
            var fixed = "";
            var order_id = "";
            if(data.data.fixed && data.data.fixed != undefined){
              fixed = data.data.fixed;
            }
            if(data.data.fixed && data.data.fixed != undefined){
              order_id = data.data.order_id;
            }
            change_status_first_event(booking_id,data.data.status, fixed, order_id );
          }else{
            change_status_first_event(booking_id,status);
          }
           //window.location.reload();
           //
        }
    });
});




</script>