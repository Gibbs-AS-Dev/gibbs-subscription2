<?php




/* Determine the type of form */
if(isset($_GET["action"])) {
	$form_type = $_GET["action"];
} else {
	$form_type = 'submit';
}
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles ); 
if(!in_array($role,array('administrator','admin','owner','editor','translator'))) :
	$template_loader = new Listeo_Core_Template_Loader; 
	$template_loader->get_template_part( 'account/owner_only'); 
	return;
endif;
?>

<?php 

/*if(!empty($_POST)){
	echo "<pre>"; print_r($_POST); die;
}*/
$catss = array("service_category",'rental_category','event_category','classifieds_category');
foreach ($catss as  $value_cat) {
?>	


<div class="add-listing-section type-selection1 catt_all" id="cat_sec_<?php echo $value_cat;?>" style="display: none">

			<!-- Headline -->
			<!-- <div class="add-listing-headline">
				<h3><?php esc_html_e('Velg om du ønkser booking med') ?></h3>
			</div> -->
			<?php 	$listing_types = get_option('listeo_listing_types',array( 'service', 'rental', 'event' ));
					if(empty($listing_types)) { $listing_types = array('service'); }
				?>
			<div class="row">
				<div class="col-lg-12">
					<div class="listing-type-container">

						<?php
						 	$category_type = 'event';

							//$catsArray = array(63,129,89,130,93,162,360);
							$listing_category_terms_all = get_terms( array(
							          'taxonomy' => $value_cat,
							          'hide_empty'  => false,
							          'orderby' => 'term_order',
					        ));
					        $listing_category_terms = array();

					        foreach ($listing_category_terms_all as $key => $parent) {
					        	if($parent->parent == "0"){
					        		$listing_category_terms[] = $parent;
					        	}
					        	
					        }




							// Loop all the categories in the tye
					       	foreach($listing_category_terms as $key => $value){

					       		// Manually set the category types to listing_category categories

								$category_type = 'service';

					       		?>

								<a onclick="document.querySelector('input[name=listing_top_category]').value = <?php echo $value->term_id; ?>;" href="javascript:void(0)" class="listing-type list_cat" data-term_id="<?php echo $value->term_id; ?>" data-type="<?php echo $category_type; ?>" style="min-width:calc(100%/2.5);">
									<?php

									// get category icon
									/*  $icon = get_term_meta($value->term_id,'icon',true);
									 $icon1 = get_term_meta($value->term_id,'_icon_svg',true);  */
									// $icon1 = "";
									 

									// Default icon if missing
							       /*  if(empty($icon)) { $icon = 'im im-icon-Globe' ; } */

							        ?>
								<!-- 	<span class="listing-type-icon"> -->
								<!-- 		<?php
										if($icon1 != ""){
		                                    $_icon_svg_image = wp_get_attachment_image_src($icon1,'medium');
		                                   echo $icon = listeo_render_svg_icon($icon1);
                                           // echo $icon = '<img class="listeo-map-svg-icon" src="'.$_icon_svg_image[0].'"/>';
										}else{ ?>
                                             <i class="<?php echo $icon; ?>"></i>
										<?php } ?> -->
										
									</span>
									<h3 class="listing_type_top_category" data-type="<?php echo $value->term_id; ?>" >

									<?php
									// Check if the condition is met before displaying the image and the name
									$q_args = array(
										'pad_counts' => 'true',
										'hide_empty' => false,
										'parent' => $value->term_id,
									);

									$passendeFor = get_terms($value_cat, $q_args);
									$onlyNames = array();

									foreach ($passendeFor as $k => $v) {
										array_push($onlyNames, $v->name);
									}

									if (!empty($onlyNames)) {
										echo '<img src="/wp-content/themes/listeo-child/assets/images/booking-with-landing.jpg" alt="image of landingpage with booking Logo" style="margin-bottom: 30px; border-radius:8px;" />';


										
										echo $value->name;
									} else {
										echo '<img src="/wp-content/themes/listeo-child/assets/images/booking-without-landing.jpg" alt="booking system without Logo" style="margin-bottom: 30px; border-radius:8px;" />';
										echo $value->name;
									}
									?>
								</h3>
								<br>
								<p>
								<?php
								if (!empty($onlyNames)) {
									echo '<span >Her kan du legge til beskrivelse, bilder, kontaktinformasjon med mer. ideelt for deg som ikke har din egen hjemmeside. </span>';
								} else {
									echo '<span s>Velg dette alternativet hvis du kun ønsker bookingkalender.</span>';
								}
								?>

								</p>

								</a>

					       	<?php }

						?>
					</div>
					<!-- <div class="add-listing-headline">
						<h4><?php esc_html_e('Tilbyr du noe som ikke er nevnt i kategoriene over? Kontakt oss på kontakt@gibbs.no','listeo_core') ?></h4>
					</div> -->
				</div>
			</div>

		</div>
<?php	} ?>
<form action="<?php  echo esc_url( $data->action ); ?>" method="post" id="submit-listing-form" class="listing-manager-form" enctype="multipart/form-data">
	
	<div id="add-listing">

		<!-- Section -->
		<div class="add-listing-section type-selection">

			<!-- Headline -->
			<div class="add-listing-headline">
				<h3><?php esc_html_e('Choose Listing Type','listeo_core') ?></h3>
			</div>
			<?php 	$listing_types = get_option('listeo_listing_types',array( 'service', 'rental', 'event' ,'classifieds')); 
					if(empty($listing_types)) { $listing_types = array('service'); } 
				?>
			<div class="row">
				<div class="col-lg-12">
					<div class="listing-type-container" style="display: none">
						<?php if(in_array('service',$listing_types)): ?>
						<a href="#" class="listing-type" data-type="service" data_href="cat_sec_service_category">
							<span class="listing-type-icon">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 315 315" style="enable-background:new 0 0 315 315;" xml:space="preserve">
<g>
	<g>
		<g>
			<path d="M157.5,0C93.319,0,41.103,52.215,41.103,116.397c0,62.138,106.113,190.466,110.63,195.898
				c1.425,1.713,3.538,2.705,5.767,2.705c2.228,0,4.342-0.991,5.767-2.705c4.518-5.433,110.63-133.76,110.63-195.898
				C273.897,52.215,221.682,0,157.5,0z M157.5,295.598c-9.409-11.749-28.958-36.781-48.303-65.397
				c-34.734-51.379-53.094-90.732-53.094-113.804C56.103,60.486,101.59,15,157.5,15c55.91,0,101.397,45.486,101.397,101.397
				c0,23.071-18.359,62.424-53.094,113.804C186.457,258.817,166.909,283.849,157.5,295.598z"/>
			<path d="M195.657,213.956c-3.432-2.319-8.095-1.415-10.413,2.017c-10.121,14.982-21.459,30.684-33.699,46.67
				c-2.518,3.289-1.894,7.996,1.395,10.514c1.36,1.042,2.963,1.546,4.554,1.546c2.254,0,4.484-1.013,5.96-2.941
				c12.42-16.22,23.933-32.165,34.219-47.392C199.992,220.938,199.09,216.275,195.657,213.956z"/>
			<path d="M157.5,57.5C123.589,57.5,96,85.089,96,119s27.589,61.5,61.5,61.5S219,152.911,219,119S191.411,57.5,157.5,57.5z
				 M157.5,165.5c-25.64,0-46.5-20.86-46.5-46.5s20.86-46.5,46.5-46.5c25.641,0,46.5,20.86,46.5,46.5S183.141,165.5,157.5,165.5z"/>
		</g>
	</g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>

                            </span>
							<h3><?php esc_html_e('Service','listeo_core') ?></h3>
						</a>
						<?php endif; ?>
						<?php if(in_array('rental',$listing_types)): ?>
						<a href="#" class="listing-type" data-type="rental" data_href="cat_sec_rental_category" style="display:none">
							<span class="listing-type-icon">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 254.182 254.182" style="enable-background:new 0 0 254.182 254.182;" xml:space="preserve">
<g>
	<path d="M211.655,137.102c-4.143,0-7.5,3.358-7.5,7.5v77.064h-41.373v-77.064c0-4.142-3.357-7.5-7.5-7.5H98.903
		c-4.143,0-7.5,3.358-7.5,7.5v77.064H50.026v-77.064c0-4.142-3.357-7.5-7.5-7.5c-4.143,0-7.5,3.358-7.5,7.5v84.564
		c0,4.142,3.357,7.5,7.5,7.5h56.377h56.379h56.373c4.143,0,7.5-3.358,7.5-7.5v-84.564
		C219.155,140.46,215.797,137.102,211.655,137.102z M106.403,221.666v-69.564h41.379v69.564H106.403z"/>
	<path d="M251.985,139.298L132.389,19.712c-2.928-2.929-7.677-2.928-10.607,0L2.197,139.298c-2.929,2.929-2.929,7.678,0,10.606
		c2.93,2.929,7.678,2.929,10.607,0L127.086,35.622l114.293,114.283c1.464,1.464,3.384,2.196,5.303,2.196
		c1.919,0,3.839-0.732,5.304-2.197C254.914,146.976,254.914,142.227,251.985,139.298z"/>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>

                            </span>
							<h3><?php esc_html_e('Rent','listeo_core') ?></h3>
						</a>
						<?php endif; ?>
						<?php if(in_array('event',$listing_types)): ?>
						<a href="#" class="listing-type" data-type="event" data_href="cat_sec_event_category">
							<span class="listing-type-icon">
							<svg width="512px" height="512px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
  <path fill="var(--ci-primary-color, currentColor)" d="M472,96H384V40H352V96H160V40H128V96H40a24.028,24.028,0,0,0-24,24V456a24.028,24.028,0,0,0,24,24H472a24.028,24.028,0,0,0,24-24V120A24.028,24.028,0,0,0,472,96Zm-8,352H48V128h80v40h32V128H352v40h32V128h80Z" class="ci-primary"/>
  <polygon fill="var(--ci-primary-color, currentColor)" points="243.397 313.373 189.012 258.988 166.385 281.616 243.397 358.627 369.012 233.012 346.384 210.385 243.397 313.373" class="ci-primary"/>
</svg>

							</span>
						<h3><?php esc_html_e('Event','listeo_core') ?></h3>
						</a>
						<?php endif; ?>
						<?php if(in_array('classifieds',$listing_types)): ?>
						<a href="#" class="listing-type" data-type="classifieds" data_href="cat_sec_classifieds_category">
							<span class="listing-type-icon">
							<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001; margin-top: -7px !important;height: 28px;" xml:space="preserve">
<g>
	<g>
		<path d="M489.693,32.51l-280.307,98.585L91.35,128.168c-10.899-0.277-21.309,3.818-29.109,11.428
			c-13.716,13.379-12.146,28.55-12.146,38.976C19.855,178.573,0,201.743,0,227.116c0,25.219,19.677,48.544,50.095,48.544v11.135
			c0,21.834,17.761,39.89,39.591,40.251l15.377,0.255l-9.896,98.45c-2.935,29.198,20.018,54.701,49.478,54.701h0.108
			c25.643,0,46.913-19.24,49.478-44.754l1.989-19.783c42.724,9.015,83.483-19.386,91.15-61.06l202.623,66.97
			c10.781,3.563,22.008-4.444,22.008-15.904V48.312C512,36.768,500.575,28.683,489.693,32.51z M50.095,245.172
			c-13.205,0-19.607-9.309-19.607-18.056c0-8.787,6.427-18.056,19.607-18.056V245.172z M163.897,432.648
			c-0.993,9.872-9.223,17.315-19.143,17.315h-0.108c-11.355,0-20.284-9.822-19.143-21.164l10.152-100.994l37.206,0.615
			L163.897,432.648z M195.294,295.286L90.24,293.549c-3.664-0.06-6.644-3.091-6.644-6.755V168.413c0-3.657,2.956-6.756,6.756-6.756
			l104.942,2.6V295.286z M201.077,382.659l5.308-54.448c0,0,26.793,8.731,48.651,15.955
			C253.067,370.863,227.235,389.524,201.077,382.659z M478.499,382.743c-9.309-3.076-240.663-79.543-249.705-82.532v-140.43
			l249.705-87.822V382.743z"/>
	</g>
</g>
<g>
	<g>
		<path d="M349.446,174.366h-68.999c-9.251,0-16.75,7.5-16.75,16.75s7.5,16.75,16.75,16.75h68.999c9.251,0,16.75-7.5,16.75-16.75
			S358.697,174.366,349.446,174.366z"/>
	</g>
</g>
<g>
	<g>
		<path d="M349.446,234.369h-68.999c-9.251,0-16.75,7.5-16.75,16.75s7.5,16.75,16.75,16.75h68.999c9.251,0,16.75-7.5,16.75-16.75
			S358.697,234.369,349.446,234.369z"/>
	</g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>

</span>
							<h3><?php esc_html_e('Classifieds','listeo_core') ?></h3>
						</a>
						<?php endif; ?>
						<input type="hidden" id="listing_type" name="_listing_type">
					</div>
				</div>
			</div>
			
		</div>
	<div class="submit-page">

	<p>
		<input type="hidden" id="list_submit" value="0">
		<input type="hidden" 	name="listeo_core_form" value="<?php echo $data->form; ?>" />
		<input type="hidden" 	name="listing_id" value="<?php echo esc_attr( $data->listing_id ); ?>" />
		<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
		<input type="hidden"	name="listing_top_category"		value="<?php echo $_POST['listing_top_category'] ?>" />
		<button type="submit" name="continue"  style="display: none" class="button margin-top-20"><?php echo esc_attr( $data->submit_button_text ); ?> <i class="fa fa-circle-arrow-right"></i></button>

	</p>

</form>
</div>
</div>
<?php 
add_action("wp_footer", "add_model");
function add_model(){
	$type = "add_listing";
   require_once(get_stylesheet_directory()."/modal/booking_system_modal.php");
}
?>
<script type="text/javascript">
    //localStorage.setItem("listing_data", "");
    

	jQuery(document).submit("#submit-listing-form",function(e){

		if(jQuery(document).find("#list_submit").val() == "0"){
			e.preventDefault();

			jQuery(".catt_all").hide();

			if(jQuery(this).find("#listing_type").val() == "service"){
				jQuery("#cat_sec_service_category").show();
			}
			if(jQuery(this).find("#listing_type").val() == "rental"){
				jQuery("#cat_sec_rental_category").show();
			}
			if(jQuery(this).find("#listing_type").val() == "event"){
				jQuery("#cat_sec_event_category").show();
			}
			if(jQuery(this).find("#listing_type").val() == "classifieds"){
				jQuery("#cat_sec_classifieds_category").show();
			}

			
		    jQuery(".listing-manager-form").hide();

		}

        

	})
	jQuery("#listing_type").val('service');

    jQuery("body").find("#submit-listing-form").submit();


	jQuery(".list_cat").on("click",function(e){
		let term_id = jQuery(this).attr("data-term_id");

		jQuery("input[name=listing_top_category]").val(term_id);
		jQuery(document).find("#list_submit").val("1");
		jQuery("#bookingSystemModal").show();
		//jQuery("body").find("#submit-listing-form").submit();
	});
</script>