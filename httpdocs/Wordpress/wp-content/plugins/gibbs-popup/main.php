<?php


  $content_post = get_post($page_id);
  $title = $content_post->post_title;
  $content = $content_post->post_content;
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  $content = str_replace('et_pb_section_0', '', $content);
  
  
?>
<div id="gibbsModal" class="modal gibbs_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo $title;?></h2>
    </div>
    <div class="modal-body">
       <?php echo $content; ?>
    </div>
    <div class="modal-footer">
        <div class="popup-footer">
          <div class="button_div">
            <a href="https://gibbs.no/kontakt-oss/"><button type="button" class="inr_btn">Kontakt oss</button></a>
            <a href="https://www.gibbs.no/my-listings/add-listings/"><button type="button" class="inr_btn">Prøv gratis</button></a>
          </div>
        </div>
    </div>
  </div>

  <script type="text/javascript">
  
    // Get the modal
    //var team_sizeModal = document.getElementById("team_sizeModal");
    


    jQuery(document).on("click",".close_user",function(){
      jQuery("#gibbsModal").hide();
    })
      
    jQuery(document).on("click",function(event){
      let gibbsModal = document.getElementById("gibbsModal");
      if (event.target == gibbsModal) {
        gibbsModal.style.display = "none";
      }
    })
  </script>

</div>
