<!-- The Modal -->
<?php

?>

<div id="templateModal" class="modal main_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_modal">&times;</span>
      <h2><?php  echo __("Dine maler","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <!-- <form class="send_newoffer_form" action="javascript:void(0)" method="post" > -->
        <div id="singleNewOffer">
          
          <div class="row" style="padding: 15px 15px 5px 15px;background: white;" >
              <div class="col-xs-12 col-md-12">
                <p>Velg mal</p>
                  <div style=" padding: 0; ">
                    <select>
                      <option>Mal for idrett<i class="fa fa-paper-plane"></i> </option>
                      <option>Mal for kultur</option>
                      <option>Mal for møterom</option>
                      <option>Velg</option>
                    </select>
                  </div>
              </div>
          </div>
          <div class="row" style="background: white;">
            
              <div class="col-xs-12 col-md-12" style = "padding: 20px; text-align: center;">
              <button type="submit"  class="button gray singleOffer">Opprett ny mal  <i class="fa fa-plus"></i> </button>
              <button type="submit"  class="button gray singleOffer">Lukk  <i class="fa fa-circle-xmark"></i>  </button>
                  <button type="submit"  class="button gray singleOffer">Lagre  <i class="fa fa-save"></i>  </button>
              </div>
          </div>
      </div>
  <!--   </form> -->
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".templateModal_modalbtn",function(){
        jQuery("#templateModal").show();
})

jQuery(document).on("click",".close_modal",function(){
        jQuery("#templateModal").hide();
})
jQuery(".temp_2 button").click(function(e){
  e.preventDefault();
  jQuery(".temp_2").hide();
  jQuery(".temp_3").hide();
})
jQuery(".temp_3 button").click(function(e){
  e.preventDefault();
  jQuery(".temp_2").hide();
  jQuery(".temp_3").hide();
})


</script>