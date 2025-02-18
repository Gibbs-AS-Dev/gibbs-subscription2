<style>
body.modal-open {
    overflow: hidden;
}

.sss {
    
    width: 72%;
    margin: 0 auto;
    height: 446px;
}
.rgbox {
    width: 100%;
    height: 470px;
}
.rgbximage {
    width: 100%;
    height: 470px;
    float: none;
    clear: both;
}

.rgbximage {
    background-repeat: no-repeat !important;
    background-size: cover !important;
    background-position: center !important;

-webkit-touch-callout: none;
}
div#listing-gallery {
    height: 470px;
    
}


@media screen and (min-width:990px){
    div#listing-gallery{
        margin-top: 30px;
    }
}




.mySlides.fade {
    height: 470px;
    width: 100%;
}


.slideshow-container {
  max-width: 780px;
  position: relative;
  margin: auto;
}
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: -3px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
    color: #f2f2f2;
    font-size: 15px;
    position: absolute;
    bottom: 0;
    left: 50%;
    font-weight: bold;
}
.prev:hover, .next:hover {
        background-color: #008474;
        color:#fff;
    
}    
.prev, .next {
 
    cursor: pointer;
    position: absolute;
    top: 50%;
    padding: 16px;
    margin-top: -22px;
    font-weight: bold;
    font-size: 18px;
    user-select: none;
    background-color: rgba(20,20,20,0.45);
    color: #fff;
    height: 60px;
    width: 60px;
    border-radius: 50%;
    text-align: center;
    transition: 0.4s;
    margin-left: 20px;
    margin-right: 20px;
}
.cus_tooltip {
    width: 30px;
    float: right;
}

</style>

<?php $gallery = get_post_meta( $post->ID, '_gallery', true );
$count_gallery = listeo_count_gallery_items($post->ID);

$gallery_text = get_post_meta( $post->ID , 'gallery_text', true );

if(!empty($gallery)) : ?>
    <!-- Slider -->
    <div id="listing-gallery" class="listing-section">
      <a href="<?php echo home_url();?>/listings" style="display:none"><div class="back-btn"><i class="fas fa-arrow-left"></i></div></a>
        <div class="row margin-bottom-20">
        
        <?php if($count_gallery <= 300) {
                $counter = 1; 
                $counters = 0;
				
				?>
					<div class="slideshow-container">
					<div class="rgbox">
                <?php 
                $title = '';
				$copyright = '';
                foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                      $counter++; 
                    $image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery','medium');
			
					
				if($counter > 1){
				   
				 if($gallery_text != ''){
				      
				$t = unserialize($gallery_text);
		     //print_r($t);
					$title = $t[$counters]['title'];
					$copyright = $t[$counters]['copyright'];
				}
        $counters++;
					?>
					
				

<div class="mySlides">

  <div class="numbertext"><?php echo $counters. '/' .$count_gallery ?></div>
  <a style="width:100%;margin:0;" class="trigger_popup_fricc-<?php echo $counters;?>"><div class="rgbximage" <?php if($counters > 1){ echo 'data-';}?>style="background:url(<?php echo esc_url($image[0])?>)"></div></a>
  <?php if($title != '' || $copyright != ''){ ?>
					<div class="text">
						<?php if($title != ''){ ?>
						<a href="#">
							<div class="cus_tooltip"><i class="fa fa-info"></i>
								<span class="tooltiptext">
									<?php echo $title; ?>
								</span>
							</div>
						</a>
						<?php } ?>
						<?php if($copyright != ''){ ?>						
						<a href="#"><div class="cus_tooltip"><i class="fa fa-copyright"></i>
						<span class="tooltiptext">
									<?php echo $copyright; ?>
								</span>
							</div>
						</a>
						 <?php } ?>
					</div>
					<?php } ?>
					
			<div class="hover_bkgr_fricc-<?php echo $counters; ?>">
              <span class="helper"></span>
              <div>
                  <div class="popupCloseButton-<?php echo $counters; ?>">&times;</div>
                   <div class="numbertext"><?php echo $counters. '/' .$count_gallery ?></div>
                      <img src="<?php echo esc_url($image[0])?>" style="width:100%; max-width:900px; "  >
                      <?php if($title != '' || $copyright != ''){ ?>
                      <div class="text">
                        <?php if($title != ''){ ?>
                        <a href="#">
                          <div class="cus_tooltip"><i class="fa fa-info"></i>
                            <span class="tooltiptext">
                              <?php echo $title; ?>
                            </span>
                          </div>
                        </a>
                        <?php } ?>
                        <?php if($copyright != ''){ ?>            
                        <a href="#"><div class="cus_tooltip"><i class="fa fa-copyright"></i>
                        <span class="tooltiptext">
                              <?php echo $copyright; ?>
                            </span>
                          </div>
                        </a>
                         <?php } ?>
                      </div>
                      <?php } ?>
                
            </div>
      </div>
					
</div>

<style>
.hover_bkgr_fricc-<?php echo $counters; ?>{
    background:rgba(0,0,0,.4);
    cursor:pointer;
    display:none;
    height:100%;
    position:fixed;
    text-align:center;
    top:0;
    width:100%;
    z-index:100000000000;
    left:0;
    opacity: 1;
    overflow-y:scroll;
    background-color: #1a1515;
}


.hover_bkgr_fricc-<?php echo $counters; ?> .helper{
    display:inline-block;
    height:100%;
    vertical-align:middle;
}
.hover_bkgr_fricc-<?php echo $counters; ?> > div {
    background-color: #fff;
    box-shadow: 10px 10px 60px #555;
    display: inline-block;
    height: auto;
    min-height: 100px;
    vertical-align: middle;
    width:90%;
    position: relative;
    border-radius: 8px;
}
.popupCloseButton-<?php echo $counters; ?> {
   
    cursor: pointer;
    display: inline-block;
    font-family: arial;
    font-weight: bold;
    position: absolute;
    top: 15px;
    right: 8px;
    font-size: 25px;
    line-height: 30px;
    width: 30px;
    height: 30px;
    text-align: center;
    color: white;
    padding: 0px;
    background: rgba(0,0,0,.6);
    border-radius: 6px;
}

.trigger_popup_fricc-<?php echo $counters; ?> {
    cursor: pointer;
    font-size: 20px;
    margin: 20px;
    display: inline-block;
    font-weight: bold;
}
</style>
					
					<script>
					jQuery(window).load(function () {
    jQuery(".trigger_popup_fricc-<?php echo $counters; ?>").click(function(){
       jQuery('.hover_bkgr_fricc-<?php echo $counters; ?>').show();
       jQuery("body").addClass("modal-open");
    });
    jQuery('.hover_bkgr_fricc-<?php echo $counters; ?>').click(function(){
        jQuery('.hover_bkgr_fricc-<?php echo $counters; ?>').hide();
        jQuery("body").removeClass("modal-open");
    });
    jQuery('.popupCloseButton-<?php echo $counters; ?>').click(function(){
        jQuery('.hover_bkgr_fricc-<?php echo $counters; ?>').hide();
    });
});

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  //var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  
  slides[slideIndex-1].style.display = "block";  
  
}
					</script>
                    
                    <?php } } ?>
                    </div>
                    <?php if($count_gallery != 1) { ?>
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>
<?php } ?>

    </div>
            <?php } ?>
           
        </div>

        
    </div>

<?php endif; ?>



<script>

    jQuery(document).ready(function(){

        jQuery(".rgbximage").each(function(){
           if(jQuery(this).attr("data-style") != undefined){

                var styllee = jQuery(this).attr("data-style");
                jQuery(this).attr("style",styllee);
                jQuery(this).removeAttr("data-style")

           }
        })
        
    })
</script>

