<?php



?>

<div id="editTemplateModal" class="modal template_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo __("Endre visning","Gibbs");?></h2>
      <div class="delete_template_modal"><button class="form-control1 delete_btn" type="button">Delete</button></div>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form method="post" class="save_filter_template" action="javascript:void(0)">
          <input type="hidden" name="action" value="<?php echo $template_action;?>">
          <?php
          global $wp;
          $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
          ?>
          <input type="hidden" name="current_page" value="<?php echo $current_url;?>">
          <div class="row">
            <div class="form-group col-sm-12">
                    <label><?php echo __("Visnings navn","gibbs");?></label>

                    <input type="hidden" class="template_selected" name="template_selected"  required>
                    <input type="text" class="template_name" name="template_name" required>

                    <div class="alert alert-info filter-template-hidden-info" role="alert" style="display:none">
                       <i class="fa fa-info-circle"></i> Du har gjort endringer til visningen, vil du lagre disse?.
                    </div>
            </div>
            
           <!--  <div class="form-group col-sm-12">
              <label><?php echo __("Visnings navn","gibbs");?></label>
              <input class="form-control" name="template_name" type="text" placeholder="<?php echo __("Skriv visnings nanv","gibbs");?>" required>
            </div> -->
            <div class="form-group col-sm-12 template_submit_flex">
              <input class="form-control templatebtn template-create-btn" type="button" value="<?php echo __("Opprett visning","Gibbs");?>">
                <div class="right-btn">
                  <button class="form-control1 close_user close_template_btn gray_btn" type="button" ><?php  echo __("Lukk","Gibbs");?></button>
                  <button class="form-control submit_btn gray_btn" type="submit"><i class="fa fa-spinner fa-spin" style="display:none"></i> <?php echo __("Lagre","Gibbs");?></button>
                  <button class="form-control select_template_btn gray_btn" type="button"><i class="fa fa-spinner fa-spin" style="display:none"></i> <?php echo __("Velg","Gibbs");?></button>
                </div>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>
<script type="text/javascript">
  
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
let editTemplateModal = document.getElementById("editTemplateModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal

jQuery(document).on("click",".template-main",function(){

      editTemplateModal.style.display = "block";
})
jQuery(document).on("click",".template-create-btn",function(){

      editTemplateModal.style.display = "none";
})

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_user = document.getElementsByClassName("close_user")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
  jQuery(document).on("click",".close_user",function(){
    jQuery("#editTemplateModal").hide();
  })
  

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == editTemplateModal) {
      templateModal.style.display = "none";
    }
  }
</script>