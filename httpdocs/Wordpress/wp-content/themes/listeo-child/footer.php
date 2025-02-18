<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package listeo
 */


 
?>

<!-- Footer
================================================== -->
<?php
$sticky = get_option('listeo_sticky_footer') ;
$style = get_option('listeo_footer_style') ;

$enable_footer = get_field('pf_enable_footer');

if(is_singular()){

	$sticky_singular = get_post_meta($post->ID, 'listeo_sticky_footer', TRUE);

	switch ($sticky_singular) {
		case 'on':
		case 'enable':
			$sticky = true;
			break;

		case 'disable':
			$sticky = false;
			break;

		case 'use_global':
			$sticky = get_option('listeo_sticky_footer');
			break;

		default:
			$sticky = get_option('listeo_sticky_footer');
			break;
	}

	$style_singular = get_post_meta($post->ID, 'listeo_footer_style', TRUE);
	switch ($style_singular) {
		case 'light':
			$style = 'light';
			break;

		case 'dark':
			$style = 'dark';
			break;

		case 'use_global':
			$style = get_option('listeo_footer_style');
			break;

		default:
			$sticky = get_option('listeo_footer_style');
			break;
	}
}

$sticky = apply_filters('listeo_sticky_footer_filter',$sticky);

?>

<?php if(!is_archive() && !is_page_template('template-dashboard.php') && !is_singular('listing') && !is_page('bookinger')) { ?>
<div id="footer" class="<?php echo esc_attr($style); echo esc_attr(($sticky == 'on' || $sticky == 1 || $sticky == true) ? " sticky-footer" : ''); ?> " <?php if($enable_footer != "1"){ echo 'style="display:none"';}?>>
	<!-- Main -->
	<div class="container">
		<div class="row">
			<?php
			$footer_layout = get_option( 'pp_footer_widgets','3,3,2,2,2' );

	        $footer_layout_array = explode(',', $footer_layout);
	        $x = 0;
	        foreach ($footer_layout_array as $value) {
	            $x++;
	             ?>
	             <div class="col-md-<?php echo esc_attr($value); ?> col-sm-6 col-xs-12">
	                <?php
					if( is_active_sidebar( 'footer'.$x ) ) {
						dynamic_sidebar( 'footer'.$x );
					}
	                ?>
	            </div>
	        <?php } ?>

		</div>
		<!-- Copyright -->
		<div class="row">
			<div class="col-md-12">
				<div class="copyrights"> <?php $copyrights = get_option( 'pp_copyrights' , '&copy; Theme by Purethemes.net. All Rights Reserved.' );

		            echo wp_kses($copyrights,array( 'a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(),));
		         ?></div>
			</div>
		</div>
	</div>
</div>

<?php } ?>


<div id="shareListingModal" class="modal template_modal">

  <!-- Modal content -->
   <div class="modal-content" style="width: 96%; max-width: 800px">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <!-- <h2><?php  echo __("Del");?></h2> -->
    <!--   <p>Ved å velge en mal, vil du få et forslag til hvordan et utleieobjekt kan se ut og hva slags informasjon det anbefales å legge inn</p> -->
    </div>
    <div class="modal-body">
       <div class="listing_demo_div dashboard-list-box1">
            <div class="row">
                <div class="col-md-6">
                    <div class="inner-d">
                        <h3>Booking link</h3>
                        <p>Del en booking link på sosiale medier eller på din hjemmeside. </p>
                        
                        <div class="copy-text copy-text1">
                            <input type="text" class="text-in linkk" value="" readonly />
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
                        <p>Installer en booking widget i din nettside </p>
                        
                        <div class="copy-text copy-text2" style="display: flex;">
                            <input type="text" class="text-in iframee" value="" readonly />
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
var close_user = document.getElementsByClassName("close_user")[0];

// When the user clicks the button, open the modal 
/*team_sizebtn.onclick = function() {
  team_sizeModal.style.display = "block";
}*/
jQuery(document).on("click",".close_user, .close_template_btn",function(){
  jQuery("#shareListingModal").hide();
})

jQuery(document).on("click",".shareListing",function(){
  let linkk = jQuery(this).attr("data-url");

  // Append "?hide=true" only to the iframe link
  let modifiedIframeLink = linkk + "?hide=true";

  // Update the iframe HTML string with width, height, and border properties
  let iframeHtml = "<iframe  id='gibbs_iframe'  src='" + modifiedIframeLink + "' style='border:0; min-width: 300px; min-height: 800px; max-width: 450px; max-height: 800px;'></iframe><divv src='https://www.gibbs.no/iframe.js'></divv>";

  //let escapedIframeHtml = iframeHtml.replace(/</g, "&lt;").replace(/>/g, "&gt;");

  jQuery(".linkk").val(linkk); // Set original link without modification
  jQuery(".iframee").val(iframeHtml); // Set modified link for the iframe

  var scr = jQuery(".iframee").val().replaceAll("divv","script");
  jQuery(".iframee").val(scr);

  jQuery("#shareListingModal").show();
})

  

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    /*if (event.target == team_sizeModal) {
      team_sizeModal.style.display = "none";
    } */
    if (event.target == jQuery("#shareListingModal")) {
        jQuery("#shareListingModal").hide();
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
<!-- Back To Top Button -->
<div id="backtotop"><a href="#"></a></div>
<?php
// left menu
if(is_user_logged_in()){
?>	
</div>
</div>
<?php } 
// left menu end
?>
</div> <!-- weof wrapper -->
<?php if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-video.php') || is_page_template('template-home-search-splash.php')) && get_option('listeo_home_typed_status','enable') == 'enable') {
	$typed = get_option('listeo_home_typed_text');
	$typed_array = explode(',',$typed);
	?>
						<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
						<script>

						if(document.querySelector(".typed-words") != null){
							var typed = new Typed('.typed-words', {
							strings: <?php echo json_encode($typed_array); ?>,
							typeSpeed: 80,
							backSpeed: 80,
							backDelay: 4000,
							startDelay: 1000,
							loop: true,
							showCursor: true
							});
						}
						</script>
					<?php } ?>
<?php wp_footer(); ?>

<?php
	if ( has_nav_menu( 'main-nav' ) ) :

		$navWrapper = 'main-nav main-nav-small';
    	//include('mainNav.php');

		if(is_archive('listing-split') || is_singular('listing')){ ?>
			<script>
				var homeLinks = document.querySelectorAll(".main-nav .home-icon");
				for (i = 0; i < homeLinks.length; i++) {
					homeLinks[i].classList.add("current-page-ancestor");
				}
			</script>
		<?php } ?>

	<script>
	<?php endif;  ?>
	<?php if(is_user_logged_in()){
		$unreadMsg = listeo_get_unread_counter();


		$user_id = get_current_user_id();
		global $wpdb;
		
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE (`bookings_author` = '$user_id') AND (`type` = 'reservation') and status in('confirmed')", "ARRAY_A" );

		$bookingsNeedPayment = 0;

		foreach($result as $key => $val) {
			if($val["expiring"] !== null && !empty($val["expiring"])){ 
				if (new Datetime() < new DateTime($val["expiring"]))
					$bookingsNeedPayment++;					
			}
		}
		$count_pending_buyer = listeo_count_my_bookings_by_status($user_id, 'confirmed'); 
        $count_pending = listeo_count_my_bookings_by_status($user_id, 'attention');
        /*$count_pending1 = listeo_count_my_bookings_with_status($user_id,'attention');
        $count_unpaid = listeo_count_my_bookings_with_status($user_id,'confirmed');
*/
        $sendte_sum_counter = $count_pending;

        $count_pending = listeo_count_bookings($user_id, 'waiting');
        $count_pending1 = listeo_count_bookings($user_id,'attention');
        $mottatte_sum_counter = $count_pending + $count_pending1;

		$countReceivedPending = $sendte_sum_counter + $mottatte_sum_counter;
		$countReceivedPending_new = $count_pending + $count_pending_buyer;
		$countReceivedPending_sidebar =  $mottatte_sum_counter;

		if($count_pending_buyer && $count_pending_buyer != "" && $count_pending_buyer > 0){
			?>
			jQuery("body").find(".overview_count_buyer").after("<span class='count_ov' style=\"margin-right: 5px;margin-left: -5px;color: white;height: 16px !important;font-weight: 500;font-size: 10px !important;padding: 5px 4px 6px 4px;background-color: #008474;border-radius: 100px;display: flex;justify-content: center;align-items: center;\">"+<?php echo $count_pending_buyer ?>+"</span>");
			<?php
		}


		if($unreadMsg > 0){ ?>
			jQuery("body").find(".message_count").after("<span class='count_ms' style=\"margin-right: 5px;margin-left: -5px;color: white;height: 16px !important;font-weight: 500;font-size: 10px !important;padding: 5px 4px 6px 4px;background-color: #008474;border-radius: 100px;display: flex;justify-content: center;align-items: center;\">"+<?php echo $unreadMsg ?>+"</span>");
			printNavCount(<?php echo $unreadMsg ?>, ".main-nav .inbox-icon");
		<?php }
		if(($countReceivedPending_new) > 0) { ?>
			//jQuery("body").find(".overview_count").after("<span class='count_ov' style=\"background-color:#008474;position:absolute;left:2%;min-width:11px;height:20px;top:0px;border-radius:61px;color:white;z-index:1500;border:solid white 2px;box-sizing:border-box;font-size:11px;padding:0 4px;\">"+<?php echo $countReceivedPending ?>+"</span>");
			printNavCount(<?php echo ($countReceivedPending_new) ?>, ".main-nav .overview-icon");
		<?php }
		if(($countReceivedPending_sidebar) > 0) { ?>
			jQuery("body").find(".overview_count").after("<span class='count_ov' style=\"margin-right: 5px;margin-left: -5px;color: white;height: 16px !important;font-weight: 500;font-size: 10px !important;padding: 5px 4px 6px 4px;background-color: #008474;border-radius: 100px;display: flex;justify-content: center;align-items: center;\">"+<?php echo $countReceivedPending_sidebar ?>+"</span>");
			//printNavCount(<?php echo ($countReceivedPending) ?>, ".main-nav .overview-icon");
		<?php }
	} else if(is_page_template('template-dashboard.php')){ ?>
		jQuery(document).ready(function(){
			jQuery(document).ajaxSuccess(function(e) {
			   if(jQuery('#sign-in-dialog #tab1 .notification').hasClass('success') || jQuery('#sign-in-dialog #tab2 .notification').hasClass('success')){
			   		<?php echo "window.location = '" . get_permalink(get_page_by_title('my-profile')) . "'"; ?>
			   }
			});
		})
	<?php } ?>

	function printNavCount(nr, selector){
		var messagesLink = document.querySelectorAll(selector);
		for (i = 0; i < messagesLink.length; i++) {
			if(messagesLink[i].parentNode.parentNode.classList.contains('main-nav-small')){
				messagesLink[i].insertAdjacentHTML("afterbegin", "<span style=\"background-color:#008474;position:absolute;left:51%;min-width:20px;height:20px;line-height:16px;top:-8px;border-radius:50px;color:white;z-index:1500;border:solid white 2px;box-sizing:border-box;font-size:11px;padding:0 7px;\">"+nr+"</span>");
			} else {
				messagesLink[i].insertAdjacentHTML("afterbegin", "<span style=\"background-color:#008474;position:absolute;left:23px;min-width:20px;height:20px;line-height:16px;top:-8px;border-radius:50px;color:white;z-index:1500;border:solid white 2px;font-size:11px;box-sizing:border-box;padding:0 7px;\">"+nr+"</span>");
			}
		}
	}

	function toggleSearchBar() {
		var topNavSearchBar = document.querySelector(".right-side-searchbar");
		console.log('HERE MOBVILE');
		// Search bar from not visible to visible
		if (topNavSearchBar.classList.contains("expandedNavbar")) {
			topNavSearchBar.classList.remove("expandedNavbar");
		} else {
			topNavSearchBar.classList.add("expandedNavbar");
			topNavSearchBar.querySelector("input").focus();
		}
	}
	</script>
	<script src="<?php echo site_url(); ?>/wp-content/themes/listeo-child/assets/js/intlTelInput.js?ver=5.7.2"></script>
    <script>
    var input = document.querySelector("#pphone");
    /* if(input != null){
	    var iti = window.intlTelInput(input, {
			   initialCountry: "no",
	    utilsScript: "https://staging4.pre.gibbs.no/wp-content/themes/listeo-child/assets/js/utils.js?ver=5.7.2",
	    });
    } */
    
   jQuery( "#pphone" ).change(function() {
			var pnumber = iti.getNumber();
			jQuery("#phone_with_code").val(pnumber);
		});
  </script>

<script type="text/javascript">
        jQuery( ".register" ).submit(function(e) {
			
			if(jQuery(this).find('input[name=phone]').val().length < 8){
				e.preventDefault();
				jQuery(".phone_error").remove();
				jQuery(this).find('input[name=phone]').parent().parent().append('<span class="phone_error" style="color:red">Telefonnummeret må være større enn 7 tegn.</span>')
			}

			setTimeout(function(){
                 jQuery(".phone_error").remove();
			},4000)

		});
		jQuery('input[name=phone]').keypress(function(event){
	       if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
	           event.preventDefault(); //stop character from entering input
	       }

	   });
		jQuery('input[type=radio][name=discount]').change(function(){
	      //jQuery(".booking-services a").click();
	   });

		jQuery(document).on("click",'.book-now-notloggedin',function(e){
	      // jQuery(".xoo-el-login-tgr").click();
      		jQuery('.single-listing .calendar-visible').css('z-index', '100');
	      e.preventDefault();
	      return false;
	    });
</script>
<script>
            
		jQuery(document).ready(function(){


			

			jQuery('.main-search-input button.button').click(function(evt){
				jQuery('.right-side-searchbar').addClass('over-search');

				if(!jQuery('.right-side-searchbar').hasClass('over-search')){
						evt.preventDefault();

						jQuery('.right-side-searchbar').addClass('over-search');
						jQuery('.right-side-searchbar').append("<div class='cross'>&#10060;</div>");
						jQuery('.right-side-searchbar.over-search .cross').click(function(){
							jQuery('.right-side-searchbar').removeClass('over-search');

						});
				}

			})
			jQuery('.wrapper .sidebar').mouseover(function(){

				if(jQuery('.wrapper').hasClass('active'))
				jQuery(".wrapper").addClass("mover");

			})
			jQuery('.wrapper .sidebar').mouseout(function(){
				if(jQuery('.wrapper').hasClass('active'))
				jQuery(".wrapper").removeClass("mover");
			})


			jQuery(".hamburger__inner").click(function(){
			  jQuery(".wrapper").toggleClass("active");
			  if(jQuery(".wrapper").hasClass("active")){
			  	localStorage.setItem("colspad", "1");
			  }else{
			  	localStorage.setItem("colspad", "0");
			  }
			})

			jQuery(".top_navbar .fas").click(function(){
			   jQuery(".profile_dd").toggleClass("active");
			});
			jQuery(document).on("click",".ul_menu .show_menu_icon",function(){
                jQuery(this).next().slideToggle("active");
			});
			jQuery(document).on("click",".ul_menu li show_menu_icon",function(e){
				
				if(jQuery(this).parent().find(".sub-menu") !=  undefined){
					if(jQuery(this).parent().find(".sub-menu").length > 0){
						e.preventDefault();
					    jQuery(this).parent().find(".sub-menu").slideToggle("active");
					    return false;
					}
					
				}
				return true;
                
			});
			jQuery(".main_container").find(".sub-menu").each(function(){
				jQuery(this).before("<span class='show_menu_icon'><i class='fa fa-chevron-down'></i></span>")
			})
			jQuery(".mobile-menu").find(".sub-menu").each(function(){
				jQuery(this).before("<span class='show_menu_icon'><i class='fa fa-chevron-down'></i></span>")
			})
			jQuery(".discount-input").click(function(){
			  var book_cl;
			  book_cl = jQuery(this).parent().data("class");
			 // jQuery(".discount-input").change();

			  setTimeout(function(){
			     jQuery("."+book_cl).removeClass("active");
			  },300)
			})
			
			jQuery(".current-menu-item").find(".sub-menu").show();
		})
		jQuery(".sub-menu").find(".current_page_item").parent().show()
	</script>
	<?php if(is_user_logged_in()){ ?>
	<script type="text/javascript">
	 jQuery("body").find("#menu-new-main-menu-1").parent().addClass("online_menu");
	 /* check if current page is not booking confirmation*/
	 if(!jQuery('.booking_formm').length ){
	 	jQuery("body").find("#menu-new-main-menu-1").parent().addClass("online_menu");
	 }
	</script>

	<?php } ?>

<script>
	var buttonHTML = jQuery('.loginButton').html();
	console.log(buttonHTML);
	//jQuery('.xoo-el-modal .continue-with-vipps-wrapper').next().html(buttonHTML);
	jQuery(buttonHTML).insertAfter('.xoo-el-modal .continue-with-vipps-wrapper');

 
    jQuery('.outerBlock .tab-ul li.tablinks').on('click', function(){
        jQuery('.outerBlock .tab-ul li.tablinks').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.adgandBlock').toggle();
        jQuery('.MatchannonsedBlock').toggle();
        });
        
        jQuery('#kobling').on('click', function(){
        jQuery('#koblingModal').show();
        });
        
        jQuery('#koblingModal .close').on('click', function(){
        jQuery('#koblingModal').hide();
        });
        
        jQuery('#dropdownMenuPopup').on('click', function(){
        jQuery('#listingDropdown22').show();
        });
        
        jQuery('#listingDropdown22 svg.svg-inline--fa.fa-times').on('click', function(){
        jQuery('#listingDropdown22').hide();
        });
	
	jQuery('.mobileSearch').on('click', function(){
		jQuery('.right-side-searchbar').addClass('showSearch');
	});
	
		jQuery(document).on('click', 'svg.svg-inline--fa.fa-times', function(){
		jQuery('.right-side-searchbar').removeClass('showSearch');
	});

	
</script>	

<script type="text/javascript">
    jQuery(".listeo_core-dashboard-action-duplicate").click(function(e){

        if(!confirm('Er du sikker?')){
            e.preventDefault();
           
           return false;

        }

    });
	jQuery(document).ready(function(){
		jQuery('.menu-toggle2').click(function(){
			jQuery(".mobile-header").find('nav').toggleClass('active');
		})
		jQuery('.close-tgl').click(function(){
			jQuery(".mobile-header").find('nav').removeClass('active');
		})
	})
</script>

</body>
</html>
