<?php
global $wp;

$listing_demos = array();

$listing_demos[1] = array(
  "id" => 11862,
  "title" => "Mal for hytter med døgnbestilling",
  "description" => "Denne malen gir et godt utgangspunkt for å legge ut en hytte eller noe annet som blir booket på døgnbasis",
);

$listing_demos[2] = array(
  "id" => 11859,
  "title" => "Mal for selskapslokale med tidsluker",
  "description" => "Denne malen egner seg godt for selskapslokaler som har predefinerte tider en leietaker kan booke på",
);

$listing_demos[0] = array(
   "id" => 11845,
   "title" => "Mal for idrettsanlegg med timebestilling",
   "description" => "Benytt denne malen for å ha sette raskt igang med et utkast for din annonse/utleieobjekt",
);

/* $listing_demos[3] = array(
  "id" => 11856,
  "title" => "Mal for badstue med tidsluker",
  "description" => "Denne malen egner seg godt for badstuer med predefinerte tider",
); */

$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );
?>
<div class="listing_top_div">
  <a class="button" href="/my-listings/add-listings/" class=""><?php esc_html_e('Add listing','listeo_core'); ?></a>
  <div class="btn-listing-demo <?php if(empty($active_group_id)){echo "empty_group";}?>"><a href="javascript:void(0)" class="">Velg en mal</a></div>
</div>

<div id="listingDemoModal" class="modal template_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo __("Velg en mal ","Gibbs");?></h2>
      <p>Ved å velge en mal, vil du få et forslag til hvordan et utleieobjekt kan se ut og hva slags informasjon det anbefales å legge inn</p>
    </div>
    <div class="modal-body">
       <div class="listing_demo_div dashboard-list-box">
       	    <ul>
       	    <?php
	       	    foreach ($listing_demos as $listing_demo) {
	       	    	$listingg = get_post($listing_demo["id"]);

	       	    	$demo_url =  add_query_arg( array( 'action' => "listing_demo",  'listing_id' => $listing_demo["id"], 'current_page' => home_url( $wp->request ) ));

	       	    ?>	

	       	        
						    <li>
						      <div class="list-box-listing">
						         <div class="list-box-listing-img">
                                    <a href="<?php echo get_permalink( $listingg ) ?>"><?php
                                        if(has_post_thumbnail($listing_demo["id"])){
                                            echo get_the_post_thumbnail($listing_demo["id"],'listeo_core-preview');
                                        } else {
                                            $gallery = (array) get_post_meta( $listing_demo["id"], '_gallery', true );

                                            $ids = array_keys($gallery);
                                            if(!empty($ids[0]) && $ids[0] !== 0){
                                                $image_url = wp_get_attachment_image_url($ids[0],'listeo_core-preview');
                                            } else {
                                                $image_url = get_listeo_core_placeholder_image();
                                            }
                                            ?>
                                            <img src="<?php echo esc_attr($image_url); ?>" alt="">
                                        <?php } ?>

                                        <i class="direct_icon fas fa-arrow-up-right-from-square"></i>
                                    </a>

                                </div>
						        <div class="list-box-listing-content">
						          <div class="inner">
						            <h3><?php echo $listing_demo["title"]; //echo listeo_core_get_post_status($listing_id) ?></h3>
                        <p><?php echo $listing_demo["description"]; //echo listeo_core_get_post_status($listing_id) ?></p>
						          </div>
						        </div>
						      </div>
						      <div class="buttons-to-right">

                    <form action="<?php echo $demo_url;?>" method="post">
                      <input type="hidden" name="title" value="<?php echo $listing_demo["title"];?>" required>
                      <input type="hidden" name="description" value="<?php echo $listing_demo["description"];?>" required>
                      <input type="hidden" name="listing_id" value="<?php echo $listing_demo["id"];?>" required>
						        
						          <button type="submit" class="button gray">
						          <i class="fa-regular fa-file-lines"></i> Velg</button>
                    </form>
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
let listingDemoModal = document.getElementById("listingDemoModal");

//var team_sizebtn = document.getElementById("team_size");

// Get the button that opens the modal

jQuery(document).on("click",".btn-listing-demo",function(){
  if(jQuery(this).hasClass("empty_group")){
    setTimeout(() => {
      jQuery("#menudrpcontent").addClass("show");
    }, 100);
    
    jQuery(".gr_divv").addClass("focus_div");
  }else{
    listingDemoModal.style.display = "block";
  }

      
})

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_user = document.getElementsByClassName("close_user")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
  jQuery(document).on("click",".close_user",function(){
    jQuery("#listingDemoModal").hide();
  })

  jQuery(document).click(function(event){
    if (event.target == listingDemoModal) {
      listingDemoModal.style.display = "none";
    }

  });
  
</script>