<div id="lg_reg_modal" class="modal gibbs_lg_modal">

  <!-- Modal content -->
  <div class="modal-content">
   <!--  <div class="modal-header"> -->
      <span class="close close_modal">&times;</span>
    <!-- </div> -->
    <div class="modal-body">
        <?php include "reg-login.php";?>
    </div>
  </div>

</div>

<script type="text/javascript">


jQuery(document).on("click",".login_reg_popup",function(e){
      e.preventDefault();
      setTimeout(function(){
        jQuery("#lg_reg_modal").show();
        jQuery("#lg_reg_modal").addClass("show");
      },100)
        
})

jQuery(document).on("click",".close_modal,.closebtn",function(){
         jQuery("#lg_reg_modal").hide();
         jQuery("#lg_reg_modal").removeClass("show");
})

jQuery(document).on("click",function(e){
    if(jQuery(e.target).closest(".modal-body").length == 0){
      
       if(jQuery("#lg_reg_modal").hasClass("show") == true){
        //debugger;
        jQuery("#lg_reg_modal").hide();
        jQuery("#lg_reg_modal").removeClass("show");
       }
      //
    }
})



</script>