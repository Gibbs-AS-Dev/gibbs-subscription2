<?php
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
$group_admin = get_group_admin();
if($group_admin == ""){
	$group_admin = get_current_user_ID();
}

$page_data = get_post($page_id);

// Query gift card posts
$args = array(
    'post_type' => 'giftcard', 
    'post_status' => array('publish', 'draft'), 
    'posts_per_page' => -1,
    'author'         => $group_admin

);
$giftcards = new WP_Query($args);
?>

<div class="giftcard giftcard-listing-page">
    <!-- Create Gift Card Button -->
    <a href="<?php echo get_permalink($page_id);?>">
        <button class="create-giftcard-button">Opprett nytt gavekort</button>
    </a>    
    
    <div class="giftcard-list">
        <?php if ($giftcards->have_posts()): ?>
            <?php while ($giftcards->have_posts()): $giftcards->the_post(); ?>
                <?php 
                    // Determine status and set label and class accordingly
                    $status = get_post_status();
                    $status_label = ($status == 'publish') ? 'Active' : 'Inactive';
                    $status_class = ($status == 'publish') ? 'published' : 'draft';
                    $post_link = get_permalink(); // Get the link to the single gift card page
                ?>
                <div class="giftcard-item">
                    <a class="linkk" href="<?php echo esc_url($post_link); ?>" target="_blank">
                        <div class="giftcard-thumbnail">
                            <!-- Thumbnail with link to the single gift card page -->
                            
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                <?php else: ?>
                                    <img src="<?php echo GIBBS_GIFT_URL.'/images/gift-default.png';?>">
                                <?php endif; ?>
                            
                        </div>
                    </a>
                    <div class="giftcard-content">
                        <h3><?php the_title(); ?></h3>
                        <span class="status-label <?php echo $status_class; ?>">
                            <?php echo ($status == 'publish') ? 'Aktivert' : 'Deaktivert'; ?>
                        </span>
                    </div>
                    <div class="giftcard-actions">
                        <a href="#" class="btn gray shareGift" data-url="<?php echo esc_url($post_link); ?>"><i class="fa fa-share"></i>Del</a>
                        <a href="<?php echo esc_url($post_link); ?>" target="_blank"><button class="btn qr-btn"><i class="fa fa-arrow-up-right-from-square"></i>  Forhåndsvis</button></a>
                        <a href="/?action=createQR&listing_id=<?php echo get_the_id();?>&link=<?php echo urlencode(get_permalink());?>"><button class="btn qr-btn"><i class="fa fa-download"></i>  Last ned QR</button></a>
                        <a href="<?php echo get_permalink($page_id);?>?edit=<?php echo get_the_id();?>"><button class="btn edit-btn"><i class="fa fa-edit"></i>  Rediger</button></a>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else: ?>
            <p style="padding: 12px; border-radius: 8px; background-color: #EFF7FA; color: #000; font-weight: bold; font-size: 1.1em; display: flex;  align-items: center; justify-content: center;">
             Ingen gavekort her, nei. Opprett et da vel! 😊</p>
        <?php endif; ?>
    </div>
</div>

<div id="shareGiftModal" class="modal template_modal">

  <!-- Modal content -->
   <div class="modal-content" style="width: 96%; max-width: 800px">
    <div class="modal-header">
      <span class="close close_gift">&times;</span>
      <!-- <h2><?php  echo __("Del");?></h2> -->
    <!--   <p>Ved å velge en mal, vil du få et forslag til hvordan et utleieobjekt kan se ut og hva slags informasjon det anbefales å legge inn</p> -->
    </div>
    <div class="modal-body">
       <div class="listing_demo_div dashboard-list-box1">
            <div class="row">
                <div class="col-md-6">
                    <div class="inner-d">
                        <h3>Del link</h3>
                        <p>Del en link på sosiale medier eller på din hjemmeside. </p>
                        
                        <div class="copy-text copy-text1">
                            <input type="text" class="text-in linkk" value="" style="width:100%" readonly />
                            <button><i class="fa-solid fa-copy"></i></button>
                        </div>
                        <div class="linkk-div" style="height: 100px;">
                           <!--  <div class="btn-main-lk">
                               <button class="btn-lk">Book nå (Kun til demo) </button>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="inner-d">
                        <h3>Widget/iframe</h3>
                        <p>Installer en gavekort widget i din nettside </p>
                        
                        <div class="copy-text copy-text2" style="display: flex;">
                            <input type="text" class="text-in iframee" value="" style="width:100%" readonly />
                            <button><i class="fa-solid fa-copy"></i></button>
                        </div>
                    <!--     <br>
                        <p>Slik vil widgeten se up på din hjemmeside</p>
                        <img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/booking_slots.png" style="height: 300px;width: 100%;"> -->
                    </div>
                </div>
            </div>
       </div>
    </div>
  </div>

</div>
<script type="text/javascript">
  
// Get the modal
//var team_sizeModal = document.getElementById("team_sizeModal");
//let shareListingModal = document.getElementById("shareListingModal");


// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];
var close_user = document.getElementsByClassName("close_gift")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
jQuery(document).on("click",".close_gift, .close_template_btn",function(){
  jQuery("#shareGiftModal").hide();
})

jQuery(document).on("click",".shareGift",function(){
  let linkk = jQuery(this).attr("data-url");

  // Append "?hide=true" only to the iframe link
  let modifiedIframeLink = linkk + "?iframe=true";

  // Update the iframe HTML string with width, height, and border properties
  let iframeHtml = "<iframe  id='gibbs_iframe'  src='" + modifiedIframeLink + "' style='border:0; min-width: 300px; min-height: 800px; max-width: 450px; max-height: 800px;'></iframe><divv src='https://www.gibbs.no/iframe.js'></divv>";

  //let escapedIframeHtml = iframeHtml.replace(/</g, "&lt;").replace(/>/g, "&gt;");

  jQuery(".linkk").val(linkk); // Set original link without modification
  jQuery(".iframee").val(iframeHtml); // Set modified link for the iframe

  var scr = jQuery(".iframee").val().replaceAll("divv","script");
  jQuery(".iframee").val(scr);

  jQuery("#shareGiftModal").show();
})

  

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == jQuery("#shareGiftModal")) {
        jQuery("#shareGiftModal").hide();
    }
  }
    let copyText1 = document.querySelector(".copy-text1");
    copyText1.querySelector("button").addEventListener("click", function () {
        let input = copyText1.querySelector("input.text-in");
        input.select();
        document.execCommand("copy");
        copyText1.classList.add("active");
        window.getSelection().removeAllRanges();
        setTimeout(function () {
            copyText1.classList.remove("active");
        }, 2500);
    });

    let copyText2 = document.querySelector(".copy-text2");
    copyText2.querySelector("button").addEventListener("click", function () {
        let input = copyText2.querySelector("input.text-in");
        input.select();
        document.execCommand("copy");
        copyText2.classList.add("active");
        window.getSelection().removeAllRanges();
        setTimeout(function () {
            copyText2.classList.remove("active");
        }, 2500);
    });

  
</script>