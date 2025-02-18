<?php
/**

 * listing Submission Form

 */

if(isset($data->listing_id) && $data->listing_id != ""){
	$postt = get_post($data->listing_id);

	if($postt->post_parent != "" && $postt->post_parent != "0" &&  $postt->post_parent != null){
		global $wp;
		$urll =  home_url( $wp->request );

		$urll = $urll."?action=edit&listing_id=".$postt->post_parent;
        wp_redirect($urll);
	    exit;
	  
	}
}

	

if ( ! defined( 'ABSPATH' ) ) exit;



if(isset($_GET["action"]) && $_GET["action"] == 'edit' && !listeo_core_if_can_edit_listing($data->listing_id) ){ ?>

	<div class="notification closeable notice">

		<?php esc_html_e('You can\'t edit that listing' , 'listeo_core');?>

	</div>

	

<?php 

		return;

	}	
add_action("wp_footer", "add_model");
function add_model(){
	$type = "edit_listing";
   require_once(get_stylesheet_directory()."/modal/booking_system_modal.php");
}
$current_user = wp_get_current_user();

$roles = $current_user->roles;

$role = array_shift( $roles ); 


if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
	$template_loader = new Listeo_Core_Template_Loader; 

	$template_loader->get_template_part( 'account/owner_only'); 

	return;

endif;

$selected_cat_top = $_POST["listing_top_category"];

if(isset($_GET["listing_id"]) && $_GET['listing_id'] != null){
    
    $listing_type = get_post_meta( $_GET['listing_id'], '_listing_type', true );

    $texnomy = "service_category";

   /* if($listing_type == "service"){
    	$texnomy = "service_category";
    }elseif($listing_type == "event"){
    	$texnomy = "event_category";
    }elseif($listing_type == "rental"){
    	$texnomy = "rental_category";
    }else{
    	$texnomy = "classifieds_category";
    }
*/
	$listing_top_categories = wp_get_object_terms($_GET['listing_id'], $texnomy);

    

	foreach ($listing_top_categories as $key => $listing_top_cat) {
		$all_selected_categores[] = $listing_top_cat->term_id;
		if($listing_top_cat->parent == 0){
			$selected_cat_top = $listing_top_cat->term_id;
		}
	}
}else{
	if($selected_cat_top == ""){
		die("Category not selected!");
	}
}

/* Get the form fields */

$fields = array();

if(isset($data)) :

	$fields	 	= (isset($data->fields)) ? $data->fields : '' ;

endif;
$listing_category_id = "";

if(isset($data->listing_id)){

	$catt = get_the_terms($data->listing_id,"listing_category");

	foreach ($catt as $key11 => $value11) {
		$listing_category_id = $catt[0]->term_id;
	}
}


if(isset($_POST["listing_top_category"])){
	$top_cat = $_POST["listing_top_category"];
}else{
	$top_cat = $selected_cat_top;
}
$contact_field = "";
if($top_cat != ""){
	$contact_form_cats_json = get_option("contact_form_cat");

	$contact_form_cats = array();
	if($contact_form_cats_json != ""){
		$contact_form_cats = json_decode($contact_form_cats_json);
	}
	
	if(!empty($contact_form_cats)){
		if(in_array($top_cat, $contact_form_cats)){
		 	$contact_field = "<input type='checkbox' name='_booking_system_contact_form' value='on' checked  style='display:none;'>";
		}
	}
	

}



/* Determine the type of form */

	if(isset($_GET["action"])) {

		$form_type = $_GET["action"];

	} else {

		$form_type = 'submit';

	}

	

?>
<?php if($_GET["listing_id"] == ""){ ?>
	<style type="text/css">
		.section_listing-main{
			display: none !important;
		}
	</style>

<?php
}
?>
<style type="text/css">
.hidden_div {
	visibility: hidden;
}

.hidden_div_none {
	display: none;
}
.label-region{
	display: none;
}
.label-region{
	display: none;
}
#catRegion{
	display: none !important;
}


</style>


<?php 



	if(isset($_POST['_listing_type'])) {

		$listing_type = $_POST['_listing_type'];

	} else {

		$listing_type = get_post_meta( $data->listing_id , '_listing_type', true );
		
		if(empty($listing_type)) {

			$listing_types = get_option('listeo_listing_types',array( 'service', 'rental', 'event' ));

			if(is_array($listing_types) && sizeof($listing_types) == 1 ){

				$listing_type = $listing_types[0];

			} else {

				$listing_type = 'service';	

			}			

		}

	}


	?>


<div class="submit-page  <?php echo esc_attr('type-'.$listing_type); ?>">
	<div class="errror_div"></div>
	<div class="success_div"></div>

<?php if ( $form_type === 'edit') { 

	?>

	<div class="notification closeable notice"><p><?php esc_html_e('You are currently editing:' , 'listeo_core'); if(isset($data->listing_id) && $data->listing_id != 0) {   $listing = get_post( $data->listing_id ); echo ' <a href="'.get_permalink( $data->listing_id ).'">'.$listing->post_title .'</a>';  }?></p></div> 

<?php } ?>

<?php
$_listing_widget_show = "";
if(isset($data->listing_id)) {

	$_listing_widget_show = get_post_meta($data->listing_id, '_listing_widget_show',true);


}

	if ( isset( $data->listing_edit ) && $data->listing_edit ) {

		?>

		<div class="notification closeable notice">

		<?php printf( '<p><strong>' . __( "You are editing an existing listing. %s", 'listeo_core' ) . '</strong></p>', '<a href="?new=1&key=' . $data->listing_edit . '">' . __( 'Add A New Listing', 'listeo_core' ) . '</a>' ); ?>

		</div>

	<?php }

	?>

<form action="<?php  echo esc_url( $data->action ); ?>" method="post" id="submit-listing-form" class="listing-manager-form" enctype="multipart/form-data">


<?php
//echo "<pre>"; print_r($fields); die;
 foreach ( $fields as $key => $section ) :  ?>

	<!-- Section -->

	<?php 

		if(isset($data->listing_id)) {

			$switcher_value = get_post_meta($data->listing_id, '_'.$key.'_status',true);

		} else {

			$switcher_value = false;

		}

	?>


<?php if($key == "gallery"){ 
$gallery_text = get_post_meta( $data->listing_id , 'gallery_text', true );
if($gallery_text != ''){
$t = unserialize($gallery_text);
	?>
	<script>
		var my = '<?php echo  json_encode($t); ?>';
	</script>
	<?php
}else{
	$t = array(
		'title' => '',
		'copyright' => '',
	);
	?>
	<script>
		var my = '<?php echo  json_encode($t); ?>';
	</script>
	<?php
}

}
$show_field_if_booking_enable1 = "";

			if(isset($section["show_field_if_booking_enable"])){
				if($section["show_field_if_booking_enable"] == "1"){
                   $show_field_if_booking_enable1 = "1";
				}
			}
		?>
	<div class="add-listing-section row <?php if(isset($section['class'])){ echo esc_html($section['class']); }?> <?php if(isset($section['class']) && $section['class'] == 'section_listing'){ echo 'section_listing-main';}?> <?php if(isset($section['opened_section']) && $section['opened_section'] == "1"){ echo "";}else{ echo "section_closed"; } ?> <?php if($show_field_if_booking_enable1 == "1"){ echo 'show_if_booking_enable';}?> <?php echo esc_attr(' '.$key.' '); 

		if(isset($section['onoff']) && $section['onoff'] == true && $switcher_value == 'on') { 

			echo esc_attr('switcher-on'); } ?>" <?php if($show_field_if_booking_enable1 == "1"){ echo 'style="display:none"';}?> >		

		<!-- Headline -->

		<div class="add-listing-headline <?php /*if(isset($section['class'])) echo esc_html($section['class']);*/ ?> <?php if($key == "booking_system_weekly_view" || $key == "tilgjengelighet" || $key == "menu" || $key == "discounts"){ echo 'template_div_main'; }?>">

			<h3>

				<?php if(isset($section['icon']) && !empty($section['icon'])) : ?><i class="<?php echo esc_html($section['icon']); ?>"></i> <?php endif; ?>

				<?php if(isset($section['title'])) echo esc_html($section['title']); ?>

				<?php if($key=="slots"): ?>

					<br><span id="add-listing-slots-notice"><?php esc_html_e("By default booking widget in your listing has time picker. Enable this section to configure time slots.",'listeo_core'); ?> </span>

				<?php endif; ?>

				<?php if($key=="availability_calendar"): ?>

					<br><span id="add-listing-slots-notice"><?php esc_html_e("Click date in calendar to mark the day as unavailable.",'listeo_core'); ?> </span>

				<?php endif; ?>
				

			</h3>
			<?php if($key == "booking_system_weekly_view" || $key == "tilgjengelighet" || $key == "menu" || $key == "discounts"){ ?>
				   
              <!--  <div class="template_div">
               	  <div class="temp temp_1">
               	  	<div class="templateModal_modalbtn"><span>Mal for idrett <i class="fa fa-chevron-down"></i><span></div>
               	  </div>
               	  <div class="temp temp_2" style="display: none">
               	  	<button class="btn btn-primary">Oppdater <i class="fa fa-check"></i></button>
               	  </div>
               </div> -->

			<?php 
			   require("template_modal.php");
		    } ?>

				<?php if(isset($section['onoff']) && $section['onoff'] == true) : ?> 

					<!-- Switcher -->

					<?php 

					if(isset($data->listing_id)) {

						$value = get_post_meta($data->listing_id, '_'.$key.'_status',true);

						
						if( $value === false && isset($section['onoff_state']) && $section['onoff_state'] == 'on' ) {

							$value = 'on';

						}

					} else {

						$value = false;

						if( isset($section['onoff_state']) && $section['onoff_state'] == 'on' ) {

							$value = 'on';

						}

					}

					?>

					<label class="switch"><input <?php checked($value,'on') ?> id="_<?php echo esc_attr($key).'_status'; ?>" name="_<?php echo esc_attr($key).'_status'; ?>" type="checkbox"><span class="slider round"></span></label>



				<?php endif; ?>	

				<span class="toggle"></span>

		</div>
		<div class="inner_section">
		
		<?php if($key=="booking"): ?>

			<div class="notification notice margin-top-40 margin-bottom-20">


				<p><?php esc_html_e("By turning on switch on the right, you'll enable booking feature, it will add Booking widget on your listing. You'll see more configuration settings below.",'listeo_core'); ?> </p>			

			</div>

		<?php endif; ?>

		
		

			<?php if(isset($section['onoff']) && $section['onoff'] == true) : ?> 

			<div class="switcher-content">

		<?php endif; ?>								

		<?php foreach ( $section['fields'] as $key => $field ) :

			$show_field_if_booking_enable = "";

			if(isset($field["show_field_if_booking_enable"])){
				if($field["show_field_if_booking_enable"] == "1"){
                   $show_field_if_booking_enable = "1";
				}
			}

			





				if(isset($field['type']) && $field['type'] == "skipped" ) { continue; } 


				if( isset($field['render_row_col']) && !empty($field['render_row_col']) ) : 

					listeo_core_render_column( $field['render_row_col'] , $field['name'] ); 

				else:

					listeo_core_render_column( 12, $field['name'] ); 

				endif; 

			?>
			<?php if($show_field_if_booking_enable == "1"){
					echo "<span class='show_if_booking_enable' style='display:none'>";
				}
				?>

			<?php if(isset($field['type']) && $field['type'] != 'hidden') : ?>


				<label class="label-<?php echo esc_attr( $key ); ?>" for="<?php echo esc_attr( $key ); ?>">

					<?php echo stripslashes($field['label']) . apply_filters( 'submit_listing_form_required_label', isset($field['required']) ? '' : ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>', $field ); ?>

					<?php if( isset($field['tooltip']) && !empty($field['tooltip']) ) { ?>

						<i class="tip" data-tip-content="<?php esc_attr_e( $field['tooltip'] ); ?>"></i>

					<?php } ?>

				</label>

			<?php endif; ?>



				

			<?php

				$template_loader = new Listeo_Core_Template_Loader;
				if($key == "_user_groups_id"){
					$template_loader->set_template_data( array( 'key' => $key, 'field' => $field,	) )->get_template_part( 'form-fields/user_group');
				}else{
					$template_loader->set_template_data( array( 'key' => $key, 'field' => $field,	) )->get_template_part( 'form-fields/' . $field['type'] );

				}

				
				if($key == '_discount'){
					$template_loader->get_template_part( 'form-fields/discounts');
				}
				if($key == "_listing_sports"){
					$template_loader->get_template_part( 'form-fields/single_listing_sports');
				}
				

				if(isset($section["class"]) && $section["class"] == "section_listing"){
					$template_loader->get_template_part( 'form-fields/section_listing');
				}
			?>
				<?php if($key=="_address"): ?>

				<div class="col-md-121"><div id="submit_map"></div></div>

				<?php endif; ?>	
			<?php
			if($show_field_if_booking_enable == "1"){
				echo "</span>";
			}
			?>

			
			</div>	


	<?php endforeach; ?> 

		<?php if(isset($section['onoff']) && $section['onoff'] == true) : ?> 

		</div>

		<?php endif; ?>	
		</div>

	</div> <!-- end section  -->

		
<?php endforeach; ?> 


	<div class="divider margin-top-40"></div>

	<p>
		<?php echo $contact_field;?>
		<?php
		if(isset($_GET["listing_id"])){
			$booking_status = get_post_meta($_GET["listing_id"] , '_booking_status', true );
		}else{
			$booking_status = "";
		}
		?>

		<input type="hidden" 	name="_listing_type" value="<?php  echo esc_attr($listing_type); ?>">
		<input type="checkbox" 	id="_booking_status" name="_booking_status" value="on" <?php if($booking_status){echo "checked";}?> style="display:none">

		<input type="hidden" 	name="listeo_core_form" value="<?php echo $data->form; ?>" />

		<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />

		<input type="hidden"	name="listing_top_category"		value="<?php echo $selected_cat_top; ?>" />
		<input type="hidden"	name="listing_category111"		value="<?php echo $listing_category_id; ?>" />
		
		
		<?php if(isset($_GET["listing_id"])){ ?>

			<button type="submit" data-status="draft" value="<?php echo esc_attr( $data->submit_button_text ); ?>" name="submit_listing"  class="button margin-top-20 submit_btnn save_and_draft"><?php echo __("Lagre som utkast","Gibbs");?>  <?php //echo esc_attr( $data->submit_button_text ); ?></button>
			<button type="submit" data-status="publish" value="<?php echo esc_attr( $data->submit_button_text ); ?>" name="submit_listing"  class="button margin-top-20 submit_btnn save_and_publish"><?php echo __("Publiser","Gibbs");?>  <?php //echo esc_attr( $data->submit_button_text ); ?></button>
			<button type="submit" data-status="expired" value="<?php echo esc_attr( $data->submit_button_text ); ?>" name="submit_listing"  class="button margin-top-20 submit_btnn save_and_deactivate"><?php echo __("Deaktiver","Gibbs");?> <?php //echo esc_attr( $data->submit_button_text ); ?></button>
		<?php }else{ ?>

			<button type="submit" data-status="draft" value="<?php echo esc_attr( $data->submit_button_text ); ?>" name="submit_listing"  class="button margin-top-20 submit_btnn save_and_draft"><?php echo __("Lagre som utkast","Gibbs");?>  <?php //echo esc_attr( $data->submit_button_text ); ?></button>
			<button type="submit" data-status="publish" value="<?php echo esc_attr( $data->submit_button_text ); ?>" name="submit_listing"  class="button margin-top-20 submit_btnn save_and_publish"><?php echo __("Publiser","Gibbs");?>  <?php //echo esc_attr( $data->submit_button_text ); ?></button>
		<?php } ?>	
	</p>

</form>
</div>
<div class="loadingdiv"></div>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script>	
	function sortFeaturesAlphabetically(){
		// Get all the feature labels
		var old = jQuery('.dynamic.listeo_core-term-checklist.listeo_core-term-checklist-listing_feature label');

		// sort alphabetically
		old.detach().sort(function(a, b){
		    var at = jQuery(a).text();
		    var bt = jQuery(b).text();
		    return (at > bt) ? 1 : ((at < bt) ? -1 : 0);
		});

		// prepend correct input checkbox
		old.each(function(){
			var nr = jQuery(this).attr('id').slice("label-in-listing_feature-".length);
			jQuery(this).prepend(jQuery('input#in-listing_feature-'+ nr));
		});

		// remove old checkboxes (placed wrong)
		jQuery('.dynamic.listeo_core-term-checklist.listeo_core-term-checklist-listing_feature input').nextUntil('label').andSelf().remove();

		// append newly sorted list
		old.appendTo(jQuery('.dynamic.listeo_core-term-checklist.listeo_core-term-checklist-listing_feature'));

		// extract input from inside label to before label
		jQuery('.dynamic.listeo_core-term-checklist.listeo_core-term-checklist-listing_feature > label > input').each(function() {
			jQuery(this).insertBefore(jQuery(this).parent());
		})
	}

	jQuery(document).ajaxComplete(function() {
	  sortFeaturesAlphabetically();
	});

	jQuery(".list_cat").on("click",function(e){
        jQuery("#cat_sec").hide();
        jQuery(".submit-page").removeClass("hidden_div");
        jQuery(".submit-page").removeClass("hidden_div_none");
        e.preventDefault();

	});
	
</script>

<script type="text/javascript">
    
	jQuery(document).ready(function(){

		
		 jQuery('.form-field-_category-container').hide();
		jQuery("button[name=submit_listing]").on("click",function(){

			var status = jQuery(this).data("status");
			if(status != undefined){
				jQuery(".listing_status").remove();

				jQuery("#submit-listing-form").append("<input type='hidden' class='listing_status' name='listing_status' value='"+status+"'>");
            }
            setTimeout(function(){

				if(jQuery("input:focus").offset() != undefined){
	                jQuery('html, body').animate({
				        scrollTop: jQuery("input:focus").offset().top - 150 //#DIV_ID is an example. Use the id of your destination on the page
				    }, 'slow');
				}
				if(jQuery("select:focus").offset() != undefined){
	                jQuery('html, body').animate({
				        scrollTop: jQuery("select:focus").offset().top - 150 //#DIV_ID is an example. Use the id of your destination on the page
				    }, 'slow');
				}


			},100);


			jQuery("body").find("input[type=checkbox]").each(function(){
				
				if(jQuery(this).attr("required") != undefined){
					var nnn = jQuery(this).attr("name");

	                if(jQuery("input[name="+nnn+"]:checked").length < 1){
							jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>'+nnn+' is required</p><a class="close"></a></div>')
			        		jQuery('html, body').animate({
						        scrollTop: jQuery(".errror_div").offset().top - 150
						    }, 1000);
						
						
					}
				}
			});

			if(jQuery("body").find("#_discount").attr("required") != undefined){
				if(jQuery("body").find("#_discount:checked").length < 1){
					jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>Discount is required</p><a class="close"></a></div>')
	        		jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);
				}
				
			}
			jQuery("body").find("input[type=text],input[type=number]").each(function(){
				
                if(jQuery(this).attr("required") != undefined){
					if(jQuery(this).val() ==  ""){
						jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>'+jQuery(this).attr("name")+' is required</p><a class="close"></a></div>')
		        		jQuery('html, body').animate({
					        scrollTop: jQuery(".errror_div").offset().top - 150
					    }, 1000);
					}
					
				}
			});


			jQuery("input[name=_normal_price]").each(function(){
				if(jQuery(this).val() != ""){
					jQuery("input[name=_normal_price]").val(jQuery(this).val());
				}
			})
			


        });


        jQuery("#submit-listing-form").on("submit",function(e){

        	if(jQuery(".listeo_core-term-checklist-listing_feature").hasClass("required")){
	        	if(jQuery(".listeo_core-term-checklist-listing_feature").find("input:checked").length < 1){

	        		jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>Feature checkbox is required</p><a class="close"></a></div>')
	        		jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);
	        		e.preventDefault();
	        	}
	        }

	        
	        
	       
	      

	        if(jQuery(".gallery_div").hasClass("required")){
	        	if(jQuery(".gallery_div").find("input").length < 1){

	        		jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>Gallery is required</p><a class="close"></a></div>')
	        		jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);
	        		e.preventDefault();
	        	}
	        }
	        if(jQuery("#listing_description").hasClass("my-required-field")){
	        	if(jQuery("#listing_description").val() == ""){

	        		jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>Discription is required</p><a class="close"></a></div>')
	        		jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);
	        		e.preventDefault();
	        		
	        	}
	        }
	        jQuery("input,textarea").each(function(e){
	        	if(jQuery(this).attr("limitchar") != undefined && jQuery(this).attr("limitchar") != ""){
	        		var limitchar = parseInt(jQuery(this).attr("limitchar"));
					if(jQuery(this).val().length >= limitchar){
						jQuery(".errror_div").html('<div class="notification closeable error listing-manager-error"><p>Only '+limitchar+' character are allowed in '+jQuery(this).attr("name")+'</p><a class="close"></a></div>')
		        		jQuery('html, body').animate({
					        scrollTop: jQuery(".errror_div").offset().top - 150
					    }, 1000);
		        		e.preventDefault();
					}
	        	}
	        });

	        localStorage.setItem("listing_data", "");


		}); 

		if(jQuery("body").find("input[name=_booking_status]:checked").length > 0){
			jQuery("body").find(".show_if_booking_enable").show();
			jQuery("#_slots_status").prop("checked",true);
		}
		
		/*jQuery("input").keypress(function(e){
			if(jQuery(this).attr("limitchar") != undefined && jQuery(this).attr("limitchar") != ""){
				var limitchar = parseInt(jQuery(this).attr("limitchar"));
				if(jQuery(this).val().length >= limitchar){
					alert("Only "+limitchar+" are allowed");
					e.preventDefault();
					return false;
				}
			}
		});*/
        jQuery(".add-listing-headline .toggle").click(function(){
        	  var that;
        	  that = this;
              jQuery(this).parent().parent().find(".inner_section").slideToggle('slow', function() {
			    jQuery(that).parent().parent().toggleClass('section_closed');
			  });
        });
      
        
        /*jQuery(".add-listing-headline").click(function(){
        	var that;
        	  that = this;
              jQuery(this).parent().find(".inner_section").slideToggle('slow', function() {
			    jQuery(that).parent().toggleClass('section_closed');
			  });
        	
        });*/
	});

	
</script>	

<?php
if(!isset($_GET["listing_id"])){
?>	
<script type="text/javascript">
    var data_local = localStorage.getItem("listing_data");
	if(data_local != undefined && data_local != "" && data_local != null){

      
        var obj = JSON.parse(data_local);

    }else{
      var obj = {};
    }

    jQuery(document).ready(function(){
    	jQuery("select").each(function(){
        	var __that;
		    __that = this;
	        var data_local = localStorage.getItem("listing_data");
	        if(data_local != undefined && data_local != "" && data_local != null){
		        var data_store = JSON.parse(data_local);
		        var selectt = jQuery(this).attr("name");
		        if(data_store[selectt] != undefined){
		        	/*data_store[selectt].forEach(function(index){
		        		
	                   jQuery(__that).find("option[value="+index+"]").attr("checked","checked");
	                   jQuery(__that).find("option[value="+index+"]").prop("checked",true);
		        	   
		        	})*/
		        	jQuery(__that).val(data_store[selectt]).trigger("chosen:updated");
		        	
		        	
		        	
		        }
		    }    
	    });
        jQuery("#list_category").change();

    })

	jQuery( document ).ajaxComplete(function(event, xhr, options) {
		    var data_local = localStorage.getItem("listing_data");
		    if(data_local != undefined && data_local != "" && data_local != null){

		    	var data_store = JSON.parse(data_local);

			   if(options.data.includes("listeo_get_sub_category")){

			      	
			       
				        
				        var selectt = jQuery("body").find("#subcategories").attr("name");
				        if(data_store[selectt] != undefined){

				        	jQuery("#subcategories").val(data_store[selectt]).trigger("chosen:updated");
				        	
				        	
				        }

			   }
			   if(options.data.includes("get_features_ids_from_category_ids")){
                        var checkede = "tax_input[listing_feature][]";
                        if(data_store[checkede] != undefined){

				        	for (var ik = 0; ik < data_store[checkede].length; ik++) {
				        		jQuery(".listeo_core-term-checklist-listing_feature").find("input[value="+data_store[checkede][ik]+"]").prop("checked",true);
				        	};
				        	
				        	
				        }

			   }

			   if(options.data.includes("listeo_get_sub_region_category")){
                        var selectt = jQuery("body").find("#subregionselect").attr("name");
				        if(data_store[selectt] != undefined){

				        	jQuery("#subregionselect").val(data_store[selectt]).trigger("chosen:updated");
				        	jQuery("#subregionselect").change();
				        	
				        	
				        }
 
			    }
			}
	});


    

    jQuery("input,textarea").each(function(){
		var data_local = localStorage.getItem("listing_data");
		if(data_local != undefined && data_local != ""){

			var data_store = JSON.parse(data_local);
			var inputt = jQuery(this).attr("name");
			if(data_store[inputt] != undefined){
				jQuery(this).val(data_store[inputt]);
			}
			if(data_store["_discount"] != undefined){
				if(data_store["_discount"][0] == "on"){
					setTimeout(function(){
                         jQuery("input[name=_discount]").prop("checked",true);
                         jQuery(".discount-opacity").removeClass("discount-opacity");
					},1000)
					setTimeout(function(){
                         jQuery("input[name=_discount]").prop("checked",true);
                         jQuery(".discount-opacity").removeClass("discount-opacity");
					},3000)
					setTimeout(function(){
                         jQuery("input[name=_discount]").prop("checked",true);
                         jQuery(".discount-opacity").removeClass("discount-opacity");
					},5000)
					
				}
			}

		}
	})

    jQuery(document).ready(function(){
    	tinyMCE.activeEditor.on('keyup', function(ed, e) {
		   savehit(this.id,this.getContent());
		});
    })
	jQuery("input,textarea").keyup(function(){

	   	savehit(jQuery(this).attr("name"),jQuery(this).val());
	  
	})
	jQuery(document).on("change","input[type=text]",function(){
		
	   	savehit(jQuery(this).attr("name"),jQuery(this).val());
	  
	})

	jQuery(document).on("click",".applyBtn",function(){
		
	   jQuery(".form-field-_event_date-container").find("input").each(function(){
	   	   savehit(jQuery(this).attr("name"),jQuery(this).val());
	   })
	   jQuery(".form-field-_event_date_end-container").find("input").each(function(){
	   	   savehit(jQuery(this).attr("name"),jQuery(this).val());
	   })
	  
	})
	jQuery(document).on("click","input[type=checkbox]",function(){

	     	var checked_v = {};

			var aarray = [];

			var nnnme = jQuery(this).attr("name");

			jQuery(this).parent().find("input:checked").each(function(){
	            aarray.push(this.value);
			})

			var inputt_name = jQuery(this).attr("name");

			obj[inputt_name] = aarray;
			localStorage.setItem("listing_data", JSON.stringify(obj));
	  
	})

	
    jQuery("body").find("#_coupon_for_widget,#_color").change(function(){
      savehit(jQuery(this).attr("name"),jQuery(this).val());
    });
	jQuery(document).on("click",".chosen-results li,.chosen-container,.search-choice-close,option",function(){
		
		jQuery("select").each(function(){

			var _that;
			_that =this;

			var checked_v = {};

			var aarray = [];

			jQuery(this).find("option:checked").each(function(){
	            aarray.push(this.value);
			})

			var select_name = jQuery(this).attr("name");

			obj[select_name] = aarray;
			localStorage.setItem("listing_data", JSON.stringify(obj));

		})

		

	   //	savehit(jQuery(this).attr("name"),jQuery(this).val());
	  
	})
	/*jQuery("input,textarea").change(function(){
	   ajaxhit(jQuery(this).attr("name"),jQuery(this).attr("value"));
	})*/

	function savehit($input_name,$input_value){
	
	   obj[$input_name] = $input_value;

	   localStorage.setItem("listing_data", JSON.stringify(obj));
	}
</script>


<?php }else{ 

	function mce_autosave_mod( $init ) {
		$init['setup'] = 'function(a){a.on("change",function(b){;})}';

	    return $init;
	}
	add_filter('tiny_mce_before_init', 'mce_autosave_mod');

	?>

<script type="text/javascript">

localStorage.setItem("listing_data", "");
jQuery("#listing_title").change(function(){
  var listing_id = "<?php echo $_GET['listing_id'];?>";
  var title = jQuery(this).val();
  if(title != ""){

  	jQuery.ajax({
         type : "POST",
         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
         data : {action: "save_title",'listing_id':listing_id,title:title},
         success: function(response) {

               jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
          }
    });   

  }
    


})
jQuery(document).ready(function(){

	tinyMCE.activeEditor.onChange.add(function (ed, e) {
        // Update HTML view textarea (that is the one used to send the data to server).
          var listing_id = "<?php echo $_GET['listing_id'];?>";
		  var desc = ed.getContent();
		  if(desc != ""){

		  	jQuery.ajax({
		         type : "POST",
		         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
		         data : {action: "save_desc",'listing_id':listing_id,desc:desc},
		         success: function(response) {
                    jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
		              
		          }
		    });   

		  }
    });

    jQuery("body").find(".kapasitet").find("input").change(function(){
       var field_name  = jQuery(this).attr("name");
       var field_value  = jQuery(this).val();
       var listing_id = "<?php echo $_GET['listing_id'];?>";

       if(field_value != ""){
           jQuery.ajax({
		         type : "POST",
		         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
		         data : {action: "save_listing_field",'listing_id':listing_id,field_name:field_name,field_value:field_value},
		         success: function(response) {
                    jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
		              
		          }
		    }); 
       }
    })
    jQuery("body").find("#_address").change(function(){
       var field_name  = jQuery(this).attr("name");
       var field_value  = jQuery(this).val();
       var listing_id = "<?php echo $_GET['listing_id'];?>";

       if(field_value != ""){
           jQuery.ajax({
		         type : "POST",
		         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
		         data : {action: "save_listing_field",'listing_id':listing_id,field_name:field_name,field_value:field_value},
		         success: function(response) {
                    jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
		              
		          }
		    }); 
       }
    })


    jQuery("body").find(".basic_prices").find("input").change(function(){
       var field_name  = jQuery(this).attr("name");
       var field_value  = jQuery(this).val();
       var listing_id = "<?php echo $_GET['listing_id'];?>";

       if(field_value != ""){
           jQuery.ajax({
		         type : "POST",
		         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
		         data : {action: "save_listing_field",'listing_id':listing_id,field_name:field_name,field_value:field_value},
		         success: function(response) {
                    jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
		              
		          }
		    }); 
       }
    })

    jQuery("body").find(".basic_prices").find("input").change(function(){
       var field_name  = jQuery(this).attr("name");
       var field_value  = jQuery(this).val();
       var listing_id = "<?php echo $_GET['listing_id'];?>";

       if(field_value != ""){
           jQuery.ajax({
		         type : "POST",
		         url : "<?php echo admin_url( 'admin-ajax.php' );?>",
		         data : {action: "save_listing_field",'listing_id':listing_id,field_name:field_name,field_value:field_value},
		         success: function(response) {
                    jQuery(".success_div").html('<div class="notification closeable success"><p>Lagret!</p><a class="close"></a></div>')
	        		/*jQuery('html, body').animate({
				        scrollTop: jQuery(".errror_div").offset().top - 150
				    }, 1000);*/
		              
		          }
		    }); 
       }
    })

   

})
</script>


<?php } ?>
<style type="text/css">
	.form-field-_booking_system_weekly_view-container,.form-field-_booking_system_rental-container,.form-field-_booking_system_service-container,.form-field-_booking_system_equipment-container,.form-field-_booking_system___external_booking-container{
		display: none;
	}
</style>
<script type="text/javascript">

/*jQuery(".add-listing-headline .switch").each(function(){
	jQuery(this).parent().parent().find(".toggle").hide();
	jQuery(this).parent().parent().find(".switch").css({"right":"19px"});

	if(jQuery(this).find("input").prop("checked") == true){
		jQuery(this).parent().parent().removeClass("section_closed");
		jQuery(this).parent().parent().find(".toggle").hide();
		jQuery(this).parent().parent().find(".switch").css({"right":"19px"});
		jQuery(this).parent().parent().addClass("switcher-on");
		jQuery(this).parent().parent().find(".toggle").hide();
		jQuery(this).parent().parent().find(".switch").find("input").prop("checked",true);
		jQuery(this).parent().show();
	}

})
jQuery(".switcher-content").each(function(){

	if(jQuery(this).find(".switch_box").find("input").prop("checked") == true){
		jQuery(this).parent().parent().removeClass("section_closed");
		jQuery(this).parent().parent().find(".toggle").hide();
		jQuery(this).parent().parent().find(".switch").css({"right":"19px"});
		jQuery(this).parent().parent().addClass("switcher-on");
		jQuery(this).parent().parent().find(".toggle").hide();
		jQuery(this).parent().parent().find(".switch").find("input").prop("checked",true);
		jQuery(this).parent().show();
	}

})
*/
// copy bottom

    jQuery("input[name=_service_booking_system_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_service_booking_system_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system___equipment_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system___equipment_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system_weekly_view_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system_weekly_view_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_rental_booking_system_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_rental_booking_system_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system___external_booking_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system___external_booking_status]").parent().parent().parent().find(".switch").css({"right":"19px"});


jQuery(document).ready(function(){
	jQuery("input[name=_booking_status]").change(function(){
        if(jQuery(this).prop("checked") == true){
			jQuery("body").find(".show_if_booking_enable").show();
			jQuery("#_slots_status").prop("checked",true);
		}else{
			jQuery("body").find(".show_if_booking_enable").hide();
			jQuery("#_slots_status").prop("checked",false);
		}
       
    })

   

	jQuery("input[name=_rental_booking_system_status]").change(function(){
		var that;
		that = this;
		if(jQuery(this).prop("checked") == true){


             

			jQuery(this).parent().parent().addClass("switcher-on");

    		//jQuery(this).parent().parent().parent().find(".toggle").click();

    		setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
    		},10)


            jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status]").change();

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_service]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_service]").change();

            

            /////////////

            jQuery("input[name=_booking_system_rental]").prop("checked",true);
            jQuery("input[name=_booking_system_rental]").change();

            jQuery("input[name=_booking_status]").prop("checked",true);
            jQuery("input[name=_booking_status]").attr("checked",true);

			jQuery("body").find(".show_if_booking_enable").show();

			jQuery("body").find(".availability-slots").parent().parent().parent().parent().hide();


		}else{

			setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
    		},10)
			//////////

			jQuery("input[name=_booking_system_rental]").prop("checked",false);
            jQuery("input[name=_booking_system_rental]").change();

            ///////////////////////////////
			jQuery("input[name=_booking_status]").prop("checked",false);
			jQuery("input[name=_booking_status]").removeAttr("checked");
			jQuery("body").find(".show_if_booking_enable").hide();
		}
	})
	jQuery("input[name=_service_booking_system_status]").change(function(){
		var that;
		that = this;
		if(jQuery(this).prop("checked") == true){

			jQuery(this).parent().parent().addClass("switcher-on");
    		//jQuery(this).parent().parent().parent().find(".toggle").click();

    		setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
    		},10)

			jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_rental_booking_system_status]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_rental_booking_system_status]").change();

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_rental]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_rental]").change();

           

            ///////////////////////////

            jQuery("input[name=_booking_system_service]").prop("checked",true);
            jQuery("input[name=_booking_system_service]").change();


            //////////////////////////////
            jQuery("input[name=_booking_status]").prop("checked",true);
            jQuery("input[name=_booking_status]").attr("checked",true);
			jQuery("body").find(".show_if_booking_enable").show();
			jQuery(".availability-slots-new").show();
			jQuery(".availability-slots").hide();
		}else{

			jQuery(".availability-slots-new").hide();
			jQuery(".availability-slots").show();

			setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
    		},10)

			//jQuery(this).parent().parent().parent().find(".toggle").click();
			/////////////
			jQuery("input[name=_booking_system_service]").prop("checked",false);
            jQuery("input[name=_booking_system_service]").change();

            ///////////////////
			jQuery("input[name=_booking_status]").prop("checked",false);
			jQuery("input[name=_booking_status]").removeAttr("checked");
			jQuery("body").find(".show_if_booking_enable").hide();
		}
	})

	jQuery("input[name=_booking_system___external_booking_status]").change(function(){
		var that;
		that = this;
		if(jQuery(this).prop("checked") == true){

			jQuery(this).parent().parent().addClass("switcher-on");
    	//	jQuery(this).parent().parent().parent().find(".toggle").click();

    		setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
    		},10)

			jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___equipment_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").change();

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system_service], input[name=_booking_system_rental]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system_equipment], input[name=_booking_system_service], input[name=_booking_system_rental]").change();



            jQuery("input[name=_booking_system___external_booking]").prop("checked",true);
            jQuery("input[name=_booking_system___external_booking]").change();


           // jQuery("input[name=_booking_status]").prop("checked",true);
           // jQuery("input[name=_booking_status]").attr("checked",true);
			//jQuery("body").find(".show_if_booking_enable").show();
		}else{

			setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
    		},10)

			//jQuery(this).parent().parent().parent().find(".toggle").click();

			jQuery("input[name=_booking_system___external_booking]").prop("checked",false);
            jQuery("input[name=_booking_system___external_booking]").change();

			jQuery("input[name=_booking_status]").prop("checked",false);
			jQuery("input[name=_booking_status]").removeAttr("checked");
			jQuery("body").find(".show_if_booking_enable").hide();
		}
	})

	jQuery("input[name=_booking_system___equipment_status]").change(function(){
		var that;
		that = this;
		if(jQuery(this).prop("checked") == true){

			jQuery(this).parent().parent().addClass("switcher-on");
    		//jQuery(this).parent().parent().parent().find(".toggle").click();

    		setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
    		},10)

           jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").change();

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system___external_booking], input[name=_booking_system_service], input[name=_booking_system_rental]").prop("checked",false);

            jQuery("input[name=_booking_system_weekly_view], input[name=_booking_system___external_booking], input[name=_booking_system_service], input[name=_booking_system_rental]").change();



            jQuery("input[name=_booking_system_equipment]").prop("checked",true);
            jQuery("input[name=_booking_system_equipment]").change();


            jQuery("input[name=_booking_status]").prop("checked",true);
            jQuery("input[name=_booking_status]").attr("checked",true);
			jQuery("body").find(".show_if_booking_enable").show();
		}else{

			setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
    		},10)

			//jQuery(this).parent().parent().parent().find(".toggle").click();
			jQuery("input[name=_booking_system_equipment]").prop("checked",false);
            jQuery("input[name=_booking_system_equipment]").change();


			jQuery("input[name=_booking_status]").prop("checked",false);
			jQuery("input[name=_booking_status]").removeAttr("checked");
			jQuery("body").find(".show_if_booking_enable").hide();
		}
	})

	jQuery("input[name=_booking_system_weekly_view_status]").change(function(){
		var that;
		that = this;
		if(jQuery(this).prop("checked") == true){


			jQuery(this).parent().parent().addClass("switcher-on");
    		//jQuery(this).parent().parent().parent().find(".toggle").click();

    		setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
    		},10)


            jQuery("input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").prop("checked",false);

            jQuery("input[name=_booking_system___equipment_status], input[name=_booking_system___external_booking_status], input[name=_service_booking_system_status], input[name=_rental_booking_system_status]").change();

            jQuery("input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_service], input[name=_booking_system_rental]").prop("checked",false);

            jQuery("input[name=_booking_system_equipment], input[name=_booking_system___external_booking], input[name=_booking_system_service], input[name=_booking_system_rental]").change();



            jQuery("input[name=_booking_system_weekly_view]").prop("checked",true);
            jQuery("input[name=_booking_system_weekly_view]").change();


            jQuery("input[name=_booking_status]").prop("checked",true);
            jQuery("input[name=_booking_status]").attr("checked",true);
			jQuery("body").find(".show_if_booking_enable").show();
			
		}else{

			setTimeout(function(){
              jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
    		},10)
			//jQuery(this).parent().parent().parent().find(".toggle").click();
			jQuery("input[name=_booking_system_weekly_view]").prop("checked",false);
            jQuery("input[name=_booking_system_weekly_view]").change();


			jQuery("input[name=_booking_status]").prop("checked",false);
			jQuery("input[name=_booking_status]").removeAttr("checked");
			jQuery("body").find(".show_if_booking_enable").hide();
		}
	})

	// copy top
    jQuery("input[name=_service_booking_system_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_service_booking_system_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system___equipment_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system___equipment_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system_weekly_view_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system_weekly_view_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_rental_booking_system_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_rental_booking_system_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_booking_system___external_booking_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_booking_system___external_booking_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    jQuery("input[name=_discount]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_discount]").parent().parent().parent().find(".switch_box").css({"right":"19px"});

    jQuery("input[name=_menu_status]").parent().parent().parent().find(".toggle").hide();
    jQuery("input[name=_menu_status]").parent().parent().parent().find(".switch").css({"right":"19px"});

    ///
    if(jQuery("input[name=_booking_system_service]").prop("checked") == true){
     	jQuery("input[name=_service_booking_system_status]").prop("checked",true);
     	jQuery("input[name=_service_booking_system_status]").change();
    }

    if(jQuery("input[name=_booking_system_equipment]").prop("checked") == true){
        jQuery("input[name=_booking_system___equipment_status]").prop("checked",true);
        jQuery("input[name=_booking_system___equipment_status]").change();
    }
    if(jQuery("input[name=_booking_system_weekly_view]").prop("checked") == true){
        jQuery("input[name=_booking_system_weekly_view_status]").prop("checked",true);
        jQuery("input[name=_booking_system_weekly_view_status]").change();
    }
    if(jQuery("input[name=_booking_system_rental]").prop("checked") == true){
    	jQuery("input[name=_rental_booking_system_status]").prop("checked",true);
        jQuery("input[name=_rental_booking_system_status]").change();
    }
    if(jQuery("input[name=_booking_system___external_booking]").prop("checked") == true){
    	jQuery("input[name=_booking_system___external_booking_status]").prop("checked",true);
        jQuery("input[name=_booking_system___external_booking_status]").change();
    }

    if(jQuery("body").find("input[name=_booking_system_weekly_view_status]").prop("checked")){
    	jQuery(".day-slots").each(function(){
    		if(jQuery(this).find(".no-slots-fadein").length == 0){
    			jQuery(this).find(".add-slot").hide();
    			jQuery(this).find(".availableHours").hide();
    			jQuery(this).find(".single-slot-right").css("visibility","hidden");
    		}
    	})
	   
	}

	jQuery("body").find("input[name=_hour_price]").change(function(){
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();

        if(hour_price != ""){
        	if(normal_price == ""){
        		normal_price = 9999999999999999999999999999999999;
        	}
        	if(weekday_price == ""){
        		weekday_price = 9999999999999999999999999999999999;
        	}
        	var min_value = Math.min(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_min]").val(min_value);
        }

        // max
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();

        if(hour_price != ""){
        	if(normal_price == ""){
        		normal_price = 0;
        	}
        	if(weekday_price == ""){
        		weekday_price = 0;
        	}
        	var max_value = Math.max(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_max]").val(max_value);
        }

	});
	jQuery("body").find("input[name=_normal_price]").change(function(){
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();

        if(normal_price != ""){
        	if(hour_price == "" || hour_price == undefined){
        		hour_price = 9999999999999999999999999999999999;
        	}
        	if(weekday_price == ""){
        		weekday_price = 9999999999999999999999999999999999;
        	}
        	var min_value = Math.min(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_min]").val(min_value);
        }

        //
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();
       

        if(normal_price != ""){
        	if(hour_price == "" || hour_price == undefined){
        		hour_price = 0;
        	}
        	if(weekday_price == ""){
        		weekday_price = 0;
        	}
        	var max_value = Math.max(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_max]").val(max_value);
        }

	});
	jQuery("body").find("input[name=_weekday_price]").change(function(){
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();

        if(weekday_price != ""){
        	if(normal_price == ""){
        		normal_price = 9999999999999999999999999999999999;
        	}
        	if(hour_price == "" || hour_price == undefined){
        		hour_price = 9999999999999999999999999999999999;
        	}
        	var min_value = Math.min(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_min]").val(min_value);
        }

        //

        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();

        if(weekday_price != ""){
        	if(normal_price == ""){
        		normal_price = 0;
        	}
        	if(hour_price == "" || hour_price == undefined){
        		hour_price = 0;
        	}
        	var max_value = Math.max(hour_price, normal_price,weekday_price);
        	jQuery("input[name=_price_max]").val(max_value);
        }

	});
	/*jQuery("body").find("input[name=_weekday_price]").change(function(){
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();



        var min_value = Math.min(hour_price, normal_price,weekday_price);
        alert(min_value);

	});
	jQuery("body").find("input[name=_normal_price]").change(function(){
        var hour_price = jQuery("body").find("input[name=_hour_price]").val();
        var normal_price = jQuery("body").find("input[name=_normal_price]").val();
        var weekday_price = jQuery("body").find("input[name=_weekday_price]").val();



        var min_value = Math.min(hour_price, normal_price,weekday_price);
        alert(min_value);

	});*/
});
jQuery("#discount-percentage").keyup(function(event){
  
 /* var val1 = jQuery(this).val().replace(".","");
  jQuery(this).val(val1)*/
            
});

jQuery(".template_div_main").parent().find("input").keypress(function(){
  jQuery(".template_div_main").parent().find(".temp_2").show();
});

jQuery(".submit_btnn").click(function(){

	var status = jQuery(this).data("status");
	jQuery(".listing_status").remove();

	jQuery("#submit-listing-form").append("<input type='hidden' class='listing_status' name='listing_status' value='"+status+"'>");

	jQuery(".main_submit_listing").click();
})

jQuery("#_listing_widget_show").parent().parent().parent().parent().parent().find(".add-listing-headline").find("input").change(function(){
	let that = this;
	if(jQuery(this).prop("checked") == true){

		jQuery(this).parent().parent().addClass("switcher-on");
		//jQuery(this).parent().parent().parent().find(".toggle").click();

		setTimeout(function(){
          jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
		},10)
	}else{
		setTimeout(function(){
          jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
		},10)
		
	}
})
jQuery(document).on("change","#_discount1,#_menu_status",function(){
	let that = this;
	if(jQuery(this).prop("checked") == true){

		jQuery(this).parent().parent().addClass("switcher-on");
		//jQuery(this).parent().parent().parent().find(".toggle").click();

		setTimeout(function(){
          jQuery(that).parent().parent().parent().find(".inner_section").slideDown();
		},10)
	}else{
		setTimeout(function(){
          jQuery(that).parent().parent().parent().find(".inner_section").slideUp();
		},10)
		
	}
})

jQuery(document).ready(function(){
	jQuery("#_discount1,#_menu_status").change();
	if("<?php echo $_listing_widget_show;?>" == "on"){

		jQuery("#_listing_widget_show").parent().parent().parent().parent().parent().find(".add-listing-headline").find("input").prop("checked",true);
		jQuery("#_listing_widget_show").parent().parent().parent().parent().parent().find(".add-listing-headline").find("input").change();


	}
	jQuery(".booking_system_select").find("option").each(function(){

		var bookig_vl = jQuery(this).val();

		var clsss_bk = "form-field-"+bookig_vl+"-container";

		if(jQuery("."+clsss_bk).length == 0 && bookig_vl != "_booking_system_contact_form"){
			jQuery(this).remove();
		}

	})
	let current_bk = "";

	if("<?php echo $_GET['listing_id'];?>"){

		current_bk = "<?php echo get_post_meta($_GET['listing_id'],"_booking_system",true);?>";

	}else{
		current_bk = "<?php echo $_POST['_booking_system'];?>"
	}


	function booking_system_show(bookig_vl){

		//jQuery(".booking_system_main_div").append("<button class='booking_system_select_btn'>Select</button>");

		jQuery(".booking_system_div").hide();

		//var bookig_vl = jQuery(this).val();

		var current_bk_container = "form-field-"+bookig_vl+"-container";

		

		jQuery(".booking_system_select").find("option").each(function(){

			let bookig_opt_val = jQuery(this).val();

			var clsss_bk= "form-field-"+bookig_opt_val+"-container";

			if(clsss_bk != current_bk_container){
				jQuery("."+clsss_bk).parent().parent().parent().remove();
			}


		})



		jQuery(".booking_notification").hide();

		jQuery("."+bookig_vl).show();

		var clsss= "form-field-"+bookig_vl+"-container";

		jQuery(".booking_system_div ").find(".add-listing-headline").find("input").prop("checked",false);

		if(jQuery("."+clsss).parent().parent().parent().hasClass("booking_system_div")){

		   jQuery("."+clsss).parent().parent().parent().show();
		   jQuery("."+clsss).parent().parent().parent().find(".add-listing-headline").find("input").prop("checked",true);
		   jQuery("."+clsss).parent().parent().parent().find(".add-listing-headline").find("input").change();
		}

		if(bookig_vl == "_booking_system___external_booking" || bookig_vl == "_booking_system_contact_form"){

			jQuery(".show_if_booking_enable").hide()

		}
		if (bookig_vl == "_booking_system_weekly_view") {
		jQuery(".section_listing").show();
		} else {
			jQuery(".section_listing").hide();
		}

		if (bookig_vl == "_booking_system_rental") {
    jQuery(".rental_section").show();
} else {
    jQuery(".rental_section").remove();  // Completely removes the element
}

if (bookig_vl == "_booking_system_service") {
    jQuery(".slots_section").show();
} else {
    jQuery(".slots_section").remove();
}

if (bookig_vl == "_booking_system_weekly_view") {
    jQuery(".weekly_section").show();
} else {
    jQuery(".weekly_section").remove();
}


	}




	jQuery(document).on("change",".booking_system_select",function(){

		let selected_booking_system = this.value;
		let listing_id = "<?php echo $_GET['listing_id'];?>";

		jQuery(".booking_system_main_div").append("<span class='booking_system_loader'></span>");
          
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
			

	})
	jQuery(".booking_system_select").find("option[value="+current_bk+"]").prop("selected",true);
	//jQuery(".booking_system_select").change();

	booking_system_show(current_bk);

	/*if(isset("<?php echo $_POST['_booking_system'];?>") && "<?php echo $_POST['_booking_system'];?>" != ""){
		;
		jQuery(".booking_system_select").parent().parent().parent().hide();
	}*/


})
jQuery(document).keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
    }
});
</script>
<?php
	if(isset($_POST['_booking_system']) && $_POST['_booking_system'] != ""){ ?>
		<style type="text/css">
			.bookingsystem{
                display: none;
			}
		</style>
		<script type="text/javascript">
			jQuery(".booking_system_select").parent().parent().parent().parent().hide();
		</script>
		<?php

	}