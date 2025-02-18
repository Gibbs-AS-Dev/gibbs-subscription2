<?php

$booking_systems = array();

$booking_systems = get_booking_systems();

$booking_systemm = "";
if(isset($_GET['listing_id'])){
  $booking_systemm = get_post_meta($_GET['listing_id'],"_booking_system",true);
}

?>


<div id="bookingSystemModal" class="modal template_modal">

  <!-- Modal content -->
   <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo __("Velg bookingsystem ","Gibbs");?></h2>
    <!--   <p>Ved å velge en mal, vil du få et forslag til hvordan et utleieobjekt kan se ut og hva slags informasjon det anbefales å legge inn</p> -->
    </div>
    <div class="modal-body">
       <div class="listing_demo_div dashboard-list-box">
            <ul>
            <?php
              foreach ($booking_systems as $booking_system) {

              ?>  

                  
                <li>
                  <div class="list-box-listing">
                     <div class="list-box-listing-img">
                                    <a href="javascript::void(0)"><?php
                                             $image_url = $booking_system["image_path"];
                                            ?>
                                            <img src="<?php echo esc_attr($image_url); ?>" alt="">

                                        <!-- <i class="direct_icon fa-solid fa-arrow-up-right-from-square"></i> -->
                                    </a>

                                </div>
                    <div class="list-box-listing-content">
                      <div class="inner">
                        <h3><?php echo $booking_system["title"]; //echo listeo_core_get_post_status($listing_id) ?></h3>
                        <p><?php echo $booking_system["description"]; //echo listeo_core_get_post_status($listing_id) ?></p>
                      </div>
                    </div>
                  </div>
                  <div class="buttons-to-right">
                      <button type="button" data-booking_system="<?php echo $booking_system["name"];?>" class="button select_booking_system <?php if($booking_systemm == $booking_system['name']){ echo 'selected';}?> gray">
                      <i class="fa-regular fa-file-lines"></i> <?php if($booking_systemm == $booking_system['name']){ echo 'Valgt';}else{ echo "Velg";}?></button>
                  </div>
                </li>
              
        <?php } ?>
        </ul>
       </div>
    </div>
  </div>

</div>

<script type="text/javascript">
  
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
let bookingSystemModal = document.getElementById("bookingSystemModal");


// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_user = document.getElementsByClassName("close_user")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
  jQuery(document).on("click",".close_user, .close_template_btn",function(){
    jQuery("#bookingSystemModal").hide();
  })
  jQuery(document).on("click",".select_booking_system_btn",function(){
    jQuery("#bookingSystemModal").show();
  })
  

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == bookingSystemModal) {
      bookingSystemModal.style.display = "none";
    }
  }
  jQuery(".select_booking_system").click(function(){

    let _booking_system = jQuery(this).attr("data-booking_system");
    //alert(_booking_system)

    if("<?php echo $type;?>" == "edit_listing"){

       let selected_booking_system = _booking_system;
       let listing_id = "<?php echo $_GET['listing_id'];?>";

       jQuery.ajax({
           type : "POST",
           url : "<?php echo admin_url( 'admin-ajax.php' );?>",
           data : {action: "selected_booking_system",'listing_id':listing_id,selected_booking_system:selected_booking_system},
           success: function(response) {



            window.location.reload();



                 /*jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')*/
                /*jQuery('html, body').animate({
                  scrollTop: jQuery(".errror_div").offset().top - 150
              }, 1000);*/
            }
      }); 

    }else{

      jQuery("body").find("#submit-listing-form").append("<input type='hidden' name='_booking_system' value='"+_booking_system+"'>")
      jQuery("body").find("#submit-listing-form").submit();

    }

     
    //jQuery("body").find("#submit-listing-form").submit();
  });
</script>