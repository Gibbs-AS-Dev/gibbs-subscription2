<?php

// get user email

$current_user = wp_get_current_user();
function isMobileNew() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up.browser|up.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


$email = $current_user->user_email;
$first_name =  $current_user->first_name;
$last_name =  $current_user->last_name;
//echo $first_hour = explode(' ',$data->_hour, 3)[0];
//$first_hour = explode(':',$first_hour)[0];
//$second_hour = explode(' ',$data->_hour, 3)[2];
//$second_hour = explode(':',$second_hour)[0];

// get meta of listing


// get first images
$gallery = get_post_meta( $data->listing_id, '_gallery', true );
$instant_booking = get_post_meta( $data->listing_id, '_instant_booking', true );
$listing_type = get_post_meta( $data->listing_id, '_listing_type', true );

$_booking_system_weekly_view = get_post_meta ( $data->listing_id, '_booking_system_weekly_view', true );


if($_booking_system_weekly_view == "0"){
    $_booking_system_weekly_view = "";
}

if($listing_type == "service" && $_booking_system_weekly_view != ""){
   require_once("booking-old.php");
}else{

foreach ( (array) $gallery as $attachment_id => $attachment_url ) 
{
	$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );	
	break;
}

if(!$image){
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $data->listing_id ), 'listeo-gallery' , false );
}

?>
<?php
$_hide_price_div = get_post_meta($data->listing_id,'_hide_price_div',true);
$_show_hide_amount = get_post_meta($data->listing_id,'_show_hide_amount',true);
$_manual_invoice_payment = get_post_meta($data->listing_id, '_manual_invoice_payment', true);
 ?>
 <style type="text/css">
    .main-nav{
        display: none !important;
    }
     header#header-container {
        display: none;
    }
</style> 
<?php
$group_admin = get_group_admin();
if($group_admin == ""){
    $group_admin = get_current_user_ID();
}


$coupon = (isset($data->coupon)) ? $data->coupon : false ;

$gift_price = 0;                            
if($coupon) {
    $coupons = explode(',',$coupon);
    // echo "<pre>"; print_r($coupon); die;
    foreach ($coupons as $key => $new_coupon) {
        if(class_exists("Class_Gibbs_Giftcard")){

            $Class_Gibbs_Giftcard = new Class_Gibbs_Giftcard;

            $data2 = $Class_Gibbs_Giftcard->getGiftDataByGiftCode($new_coupon);

            if($data2 && isset($data2["id"])){

                $gift_price += $data2["remaining_saldo"];
                continue;

            }
        }   
    }
    
}
if($gift_price > 0){


    $price_ajax = $data->price;
    
    if($price_ajax > 0){
        
        if($gift_price > $price_ajax){
            $data->remaining_saldo = $gift_price - $price_ajax;
            $price_ajax = 0;
        }else{
            $price_ajax = $price_ajax - $gift_price;
        }

		if(isset($data->price_sale)){
			$data->price_sale = $price_ajax;
		}else{
			$data->price = $price_ajax;
		}

		//echo "<pre>"; print_r($data); die;

        
        
    }
}

$field_btn_action = get_user_meta($group_admin,"field_btn_action",true);
if($field_btn_action == "false" || $field_btn_action == ""){
?>
<style type="text/css">
    .delete_field_div, .add_field_btn{
        display: none !important;
    }
</style>
<?php }else{ ?>  
    <style type="text/css">
        .delete_field_div, .add_field_btn{
            display: block !important;
        }
    </style>
<?php } ?> 
<div class="row booking_formm" <?php if(isMobileNew()){?> style="display: flex;flex-direction: column-reverse;" <?php }?>>
	
		<!-- Content
		================================================== -->
		<div class="col-lg-8 col-md-8 padding-right-30">

			

			<form id="booking-confirmation" action="" method="POST">
			<input type="hidden" name="adults" value="<?php if( isset($data->adults)) echo $data->adults; ?>" />
			<input type="hidden" name="confirmed" value="yessir" />
			<input type="hidden" name="discount-type" value="<?php echo $_POST['discount'];?>" />
			<input type="hidden" name="discount" value="<?php echo $_POST['discount'];?>" />
			<input type="hidden" name="uid" value="<?php echo $current_user->ID; ?>" />
			<input type="hidden" name="value" value="<?php echo $data->submitteddata; ?>" />
			<input type="hidden" name="listing_id" id="listing_id" value="<?php echo $data->listing_id; ?>">
			<input type="hidden" name="coupon_code" class="input-text" id="coupon_code" value="<?php if( isset($data->coupon)) echo $data->coupon; ?>" placeholder="<?php esc_html_e('Coupon code','listeo_core'); ?>"> 
			<?php
			if(isset($data->taxprice) && $data->taxprice != "" && $data->taxprice > 0){ ?>
					         <input type="hidden" name="taxPrice" value="<?php echo $data->taxprice; ?>" />
			<?php } ?>		         
			<div class="row">

				    <div class="first_div_class">
					<h3 class="margin-top-0 margin-bottom-20"><?php esc_html_e('Kontaktinformasjon', 'listeo_core'); ?></h3>
    				<div class="col-md-12" style="display:flex;">
    					<input style="margin-right:10px;" type="radio" id="radio_personal" name="personalorcompany" value="Personal" checked="checked">
    					<p style="line-height:15px;">Privat</p>
    					<input style="margin-right:10px;margin-left:20px;" type="radio" name="personalorcompany" value="Company" >
    					<p style="line-height:15px;">Bedrift</p>
    				</div>



    				<div id="PCPersonal" class="desc">
    					<div class="col-md-6">
    						<div class="input-with-icon medium-icons">
									<label id="label_email"><?php esc_html_e('E-post*'); ?><span style='display:none;color:red;'> *</span></label> 
		                                <input type="email" class="email_class" name="email" value="<?php esc_html_e($email); ?>" required> 

    							 
    						</div>
    						<div class="error_message error_message_email"></div>
    					</div>

    					<div class="col-md-6 phone_field">
	                        <div class="input-with-icon medium-icons">
		                        <label id="label_phone"><?php esc_html_e('Telefon*', 'listeo_core'); ?><span style='display:none;color:red;'> </span></label>
		                        <input type="text" class="phone_class" id="pphone" name="phone" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'phone', true) ); ?>" maxlength="8" required>
		                       <!--  <input type="hidden" id="phone_with_code" name="phone_with_code"/>
		                        <input type="hidden" id="phone" name="phone"/> -->
		                        <i style='display:none' class="im im-icon-Phone-2"></i>
	                        </div>
	                        <div class="error_message error_message_phone"></div>
                        </div>

    					<div class="col-md-6">
    						<label id="label_firstname"><?php esc_html_e('Fornavn*', 'listeo_core'); ?> <span style='display:none;color:red;'> *</span></label>
							<input type="text"  name="firstname" value="<?php esc_html_e($first_name); ?>" required> 
							<input type="hidden" id="country_code" name="country_code" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'country_code', true) ); ?>"> 
                        </div>

                        <div class="col-md-6" id="lastname"> 
                            <label id="label_lastname"><?php esc_html_e('Etternavn*', 'listeo_core'); ?><span style='display:none;color:red;'> *</span></label> 
                            <input type="text"  name="lastname" value="<?php esc_html_e($last_name); ?>" required> 
                        </div> 
                        <div class="col-md-6" id="org" style="display:none"> 
                            <label id="">Organisasjonsnummer*</label> 
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" name="organization_number"> 
                        </div>

                        
                        
                    </div>

    					

    									<?php if(get_option('listeo_add_address_fields_booking_form')) : ?>

    										<?php
					                            $hide_div_invoice = "";
												$required = "required";
					                            if($_manual_invoice_payment == "dont_show_invoice"){
					                                $address1 = "Ikke valgt";
					                                $postcode = "1111";
					                                $billing_city = "Ikke valgt";
					                                $hide_div_invoice = "style='display:none'";
													$required = "";

					                            }else{
					                                $address1 = get_user_meta($current_user->ID, 'billing_address_1', true);
					                                $postcode = get_user_meta($current_user->ID, 'billing_postcode', true);
					                                $billing_city = get_user_meta($current_user->ID, 'billing_city', true);
					                            }
					                            ?>
    										<div class="col-md-6" <?php echo $hide_div_invoice;?>>

    											<label id="label_billing_address_1"><?php esc_html_e('Adresse*', 'listeo_core'); ?><span style='display:none;color:red;'> *</span></label>
    											<input type="text" name="billing_address_1" value="<?php echo $address1; ?>" <?php echo $required;?>>

    										</div>

    										<div class="col-md-6" <?php echo $hide_div_invoice;?>>

    											<label id="label_billing_postcode"><?php esc_html_e('Postnummer*', 'listeo_core'); ?><span style='display:none;color:red;'> *</span></label>
												<input type="text" name="billing_postcode" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="4" value="<?php echo $postcode; ?>" <?php echo $required;?>> 
                                                <div class="error_message error_message_postcode"></div>
    										</div>

    										<div class="col-md-6" style="display:none;">

    											<label id="label_billing_country"><?php esc_html_e('Country', 'listeo_core'); ?><span style='display:none;color:red;'> *</span></label>
    											<input style="display:none;" type="text" name="billing_country" value="NO" >
    											<input type="text" name="" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_country', true) ); ?>" >

    										</div>

    										<div class="col-md-6" <?php echo $hide_div_invoice;?>>

    											<label id="label_billing_city"><?php esc_html_e('Sted*', 'listeo_core'); ?><span style='display:none;color:red;'> *</span></label>
                                                <input type="text" name="billing_city"  value="<?php echo $billing_city; ?>" <?php echo $required;?>> 

    										</div>

    									<?php endif; ?>
    									

    								</div>
    							
    								<?php
			                           $fields_rows = array();

										if(function_exists('advanced_fields')){
											global $wpdb;
										    $listings_table =$wpdb->prefix. 'posts';
										    $listings = $wpdb->get_row("SELECT users_groups_id FROM $listings_table WHERE ID=".$data->listing_id);
										    $group_id = $listings->users_groups_id;
										    if($group_id != ""){
										       $fields_rowssss = advanced_fields(0,$group_id,0,array(),0,true,"booking_summery");


										       if(!empty($fields_rowssss)){

										       	   $fields_rows[] = $fields_rowssss;

										       }
										    }
										}

							                if(!empty($fields_rows)){  ?>
							                	<div class="second_div_booking">
	                                               <div class="row">
	                                                   <div class="col-md-12">
	                                                      <h3>Tilleggsinformasjon</h3>
	                                                      <hr />
								                          <?php echo repeated_fields($fields_rows,$group_id); ?>
	                                                   </div>
	                                              </div>
	                                          </div>
							                <?php }
									?>

									 <div class="third_div_booking">

									 	<!-- /// -->
    									<div class="col-md-12 margin-top-15">
										<h3 class="margin-top-0 margin-bottom-20"><?php esc_html_e('Melding', 'listeo_core'); ?></h3>
                                        <hr />
    										<textarea name="message" placeholder="<?php esc_html_e('En kort melding angående henvendelsen eller noe du lurer på (valgfritt)','listeo_core'); ?>" id="booking_message1" cols="20"></textarea>
    									</div>
    									
									 </div>	
				
						<div class="pdfDoc" style="background:#f9f9f9;font-family:source sans pro;padding:15px;">
    								<h4 style="font-weight: bolder">Utleier krever at du har lest og godkjent betingelser</h4>

    								<div id="pdfLinks">
    									
	    									<?php for ($i = 0; $i < 10; $i++) {
	    										$link = get_post_custom_values($key = '_pdf_document'.$i,$data->listing_id)[0];
	    										$path = explode("/", $link);
	    										$linkName = end($path);
	    										if($link != null){ ?>
	    											<input style="margin: 0 0 0 15px;width: auto;height:auto;" type="checkbox" id="pdfApprove">
    									            <div>
	    												<i class='fa fa-file-download'></i><span style='margin-left:5px;'><a style='color:#0C7868;' target='_blank' href='<?php echo $link?>'><?php echo $linkName ?></a></span><br>
	    											</div>
	    										<?php }
	    									}?>
	    								
    								</div>

    								<?php 

					                //$text_checkbox = '<span>Ved å gå videre godkjenner du <a href="https://gibbs-produkter.no/no/vilkar-og-betingelser/">vilkår og betingelser</a> for nettstedet.</span>';
					                ?>

					               <!--  <div class="row">
					                    <input style="margin: 0 0 0 15px;width: auto;height:auto;" type="checkbox" id="pdfApprove">
					                    <?php echo $text_checkbox;?>
					                </div> -->
                                    <div class="checkbox-error" style="display: none;" id="checkbox-error">Vennligst godkjenn vilkårene</div>
    							</div>

								</div>
								 <div class="row summary_term_contition">
					               <span>Ved å gå videre godkjenner du <a href="https://gibbs-produkter.no/no/vilkar-og-betingelser/">vilkår og betingelser</a> for nettstedet.</span>
					            </div>
								 <?php if($_manual_invoice_payment == "only_show_invoice" && $data->price > 0){ ?>
						                <div class="row">
						                    <div class="alert alert-info" role="alert">
						                       
						                       <?php echo __(" Faktura blir ettersendt av utleier og blir gjort utenom gibbs.no","gibbs");?> 
						                    </div>
						                </div>
						            <?php } ?>
            <div class="booking-submit-btn-block">
				<button type="submit"  class="ss button booking-confirmation-btn margin-top-20"><div class="loadingspinnerx"></div><span class="book-now-text">
					<?php 
					if ($instant_booking == 'on') {
		                echo $book_btn_text = get_option("instant_booking_label");
		            } else {
		                echo $book_btn_text = get_option("non_instant_booking_label");
		            }
		           /* if($book_btn_text != ""){
		            	$book_btn = $book_btn_text;
		            }
					if(get_option('listeo_disable_payments')) {
					
				 		($instant_booking == 'on') ? esc_html_e('Confirm', 'listeo_core') : esc_html_e('Confirm and Book', 'listeo_core') ;  
					} else {
					    
						($instant_booking == 'on') ? esc_html_e('Confirm and Pay', 'listeo_core') : esc_html_e('Confirm and Book', 'listeo_core') ;  
					}*/
				?></span>
				</button>
				<?php if($data->price > 0): ?>
                   <!--  <p style="text-align: center;">Du blir ikke belastet ennå</p> -->
                <?php endif;?>
			</div>
			</form>
			
		</div>
	

		<!-- Sidebar
		================================================== -->
		<div class="col-lg-4 col-md-4 margin-top-0 margin-bottom-30">

			<!-- Booking Summary -->
			<div class="listing-item-container compact order-summary-widget">
				<div class="listing-item">
					<img src="<?php echo $image[0]; ?>" alt="">

					<div class="listing-item-content">
						<?php $rating = get_post_meta($data->listing_id, 'listeo-avg-rating', true); 
						if(isset($rating) && $rating > 0 ) : ?>
							<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>"></div>
						<?php endif; ?>
						<h3><?php echo get_the_title($data->listing_id); ?></h3>
						<?php //if(get_the_listing_address($data->listing_id)) { ?><span><?php //the_listing_address($data->listing_id); ?></span><?php //} ?>
					</div>
				</div>
			</div>
			<div class="boxed-widget opening-hours summary margin-top-0">
				<h3><?php esc_html_e('Booking Summary', 'listeo_core'); ?></h3>
				<?php 
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
					
				?>
				<ul id="booking-confirmation-summary">

					<?php if($listing_type == 'event') { ?>
						<li id='booking-confirmation-summary-date'>
							<?php esc_html_e('Date Start', 'listeo_core'); ?> 
							<span>
								<?php 
									$meta_value = get_post_meta($data->listing_id,'_event_date',true);
									$meta_value_timestamp = get_post_meta($data->listing_id,'_event_date_timestamp',true);
									
									if(!empty($meta_value_timestamp)){
										echo date_i18n(get_option( 'date_format' ), $meta_value_timestamp);
										$meta_value_date = explode(' ', $meta_value,2); 
										$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
										if( isset($meta_value_date[1]) ) { 
											$time = str_replace('-','',$meta_value_date[1]);
											$meta_value = esc_html__(' at ','listeo_core'); 
											$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

										} 
										echo $meta_value; 
									} else {
										$meta_value_date = explode(' ', $meta_value,2); 
										$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
										$meta_value = date_i18n(listeo_date_time_wp_format_php(), strtotime($meta_value_date[0])); 

										if( isset($meta_value_date[1]) ) { 
											$time = str_replace('-','',$meta_value_date[1]);
											$meta_value .= esc_html__(' at ','listeo_core'); 
											$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

										} 
										echo $meta_value; 

									}
									
								?>
								
							</span>
						</li>
						<?php 
						$meta_value = get_post_meta($data->listing_id,'_event_date_end',true);
						
						if(isset($meta_value) && !empty($meta_value))  : ?>
						<li id='booking-confirmation-summary-date'>
							<?php esc_html_e('Date End', 'listeo_core'); ?> 
							<span>
								<?php 
									$meta_value = get_post_meta($data->listing_id,'_event_date_end',true);
									$meta_value_end_timestamp = get_post_meta($data->listing_id,'_event_date_end_timestamp',true);
									if(!empty($meta_value_end_timestamp)){
										echo date_i18n(get_option( 'date_format' ), $meta_value_end_timestamp);
										$meta_value_date = explode(' ', $meta_value,2); 

										$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
										if( isset($meta_value_date[1]) ) { 
											$time = str_replace('-','',$meta_value_date[1]);
											$meta_value = esc_html__(' at ','listeo_core'); 
											$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

										} 
										echo $meta_value; 

									} else {
										$meta_value_date = explode(' ', $meta_value,2); 

										$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
										$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
										
									
										//echo strtotime(end($meta_value_date));
										//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
										if( isset($meta_value_date[1]) ) { 
											$time = str_replace('-','',$meta_value_date[1]);
											$meta_value .= esc_html__(' at ','listeo_core'); 
											$meta_value .= date_i18n(get_option( 'time_format' ), strtotime($time));

										} echo $meta_value; 
									}
									?>
							</span>
						</li>
						<?php endif; ?>
					<?php } else { ?>

						<li id='booking-confirmation-summary-date'>
							<?php esc_html_e('Date', 'listeo_core'); ?> <span><?php echo $data->date_start; ?> <?php if ( isset( $data->date_end ) && $data->date_start != $data->date_end ) echo '<b> - </b>' . $data->date_end; ?></span>
						</li>
						<?php if ( isset($data->_hour) ) { ?>
						<li id='booking-confirmation-summary-time' style="display: none">
							<?php esc_html_e('Time', 'listeo_core'); ?> <span><?php echo $data->_hour; if(isset($data->_hour_end)) { echo ' - '; echo $data->_hour_end; }; ?></span>
						</li>
						<?php } ?>
						<?php if($listing_type == 'event') { ?>
							<li id='booking-confirmation-summary-time'>
							<?php 

							$event_start = get_post_meta($data->listing_id,'_event_date',true); 

							$event_start_date = explode(' ', $event_start,2); 
						
							if( isset($event_start_date[1]) ) { 
								$time = str_replace('-','',$event_start_date[1]);
								$event_hour_start = date_i18n(get_option( 'time_format' ), strtotime($time));
							} 

							$event_end  = get_post_meta($data->listing_id,'_event_date_end',true);

							$event_start_end = explode(' ', $event_end,2); 
						
							if( isset($event_start_end[1]) ) { 
								$time = str_replace('-','',$event_start_end[1]);
								$event_hour_end = date_i18n(get_option( 'time_format' ), strtotime($time));
							} 
							?>
							<?php esc_html_e('Time', 'listeo_core'); ?> 
							<span><?php echo $event_hour_start; ?> <?php if ( isset( $event_hour_end ) && $event_hour_start != $event_hour_end ) echo '<b> - </b>' . $event_hour_end; ?></span>
						</li>
						<?php } ?>
					<?php } ?>
					<?php $max_guests = get_post_meta($data->listing_id,"_max_guests",true);  
					if(get_option('listeo_remove_guests')){
						$max_guests = 1;
					}
					if(!get_option('listeo_remove_guests')) : ?>

						<?php if($_show_hide_amount != "on"){ ?>

							<?php if ( isset( $data->adults ) || isset( $data->childrens ) ) { ?>
								<li id='booking-confirmation-summary-guests'>
									<?php esc_html_e('Guests', 'listeo_core'); ?> <span><?php if ( isset( $data->adults ) ) echo $data->adults;
									if ( isset( $data->childrens ) ) echo $data->childrens . ' Childrens ';
									?></span>
								</li>
							<?php } 
						}
					
					endif;
					
					if ( isset( $data->tickets )) { ?>
						<li id='booking-confirmation-summary-tickets'>
							<?php esc_html_e('Tickets', 'listeo_core'); ?> <span><?php if ( isset( $data->tickets ) ) echo $data->tickets;?></span>
						</li>
					<?php } ?>
					
					<?php if( isset($data->services) && !empty($data->services)) { 
						$additional_service_label_name = get_post_meta($data->listing_id, 'additional_service_label_name', true);
						?>
						<li id='booking-confirmation-summary-services'>
							<h5 id="summary-services">
							        <?php 
										if($additional_service_label_name != ""){
											echo $additional_service_label_name;
										}else{
											esc_html_e('Additional Services','listeo_core');
										}
									?> 
							</h5>
							<ul>
							<?php 
							$bookable_services = listeo_get_bookable_services($data->listing_id);
							$i = 0;
							if($listing_type == 'rental') {
								if(isset($data->date_start) && !empty($data->date_start) && isset($data->date_end) && !empty($data->date_end)){

		        					$firstDay = new DateTime( $data->date_start );
	    	    					$lastDay = new DateTime( $data->date_end . '23:59:59') ;

	        						$days = $lastDay->diff($firstDay)->format("%a");
								} else {
									$days = 1;
								} 
							} else {
								$days = 1;
							}
							if(isset($data->adults)){
								$guests = $data->adults;	
							} else{
								$guests = $data->tickets; 
							}
							

							foreach ($bookable_services as $key => $service) {
							
							 	// $data->date_start
							 	// $data->date_end;
							 	// days
							 
							 	$countable = array_column($data->services,'value');
							 	
							 	if(in_array(sanitize_title($service['name']),array_column($data->services,'service'))) { 
							 		?>
							 		<li>
							 			<span><?php 
										if(empty($service['price']) || $service['price'] == 0) {
											esc_html_e('Free','listeo_core');
										} else {
											if($currency_postion == 'before') { echo $currency_symbol.' '; } 
											$service_price = listeo_calculate_service_price($service, $guests, $days, $countable[$i] );
											if(isset($service['tax']) && $service['tax'] > 0){
												$service_price += (($service['tax']/100) * $service_price);
											}
											
											$decimals = get_option('listeo_number_decimals',2);
											echo number_format_i18n($service_price,$decimals);
											if($currency_postion == 'after') { echo ' '.$currency_symbol; }
										}
										?></span>
										<?php echo esc_html(  $service['name'] ); 
											if( isset($countable[$i]) && $countable[$i] > 1 ) { ?>
												<em>(*<?php echo $countable[$i];?>)</em>
											<?php } ?> 
									</li>
							 	<?php  $i++;
							 	}
							 	
							 }  ?>
						 	</ul>
						</li>
					<?php }
					$decimals = get_option('listeo_number_decimals',2); ?>
					
					<?php if(!get_option('listeo_remove_coupons')): ?>
					<li class="booking-confirmation-coupons">
						<div class="coupon-booking-widget-wrapper">
							<a id="listeo-coupon-link" href="#"><?php esc_html_e('Have a coupon?','listeo_core'); ?></a>
							<div class="coupon-form">
									
									<input type="text" name="apply_new_coupon" class="input-text" id="apply_new_coupon" value="" placeholder="<?php esc_html_e('Tast inn koden din her','listeo_core'); ?>"> 
									<a href="#" class="button listeo-booking-widget-apply_new_coupon" name="apply_new_coupon"><?php esc_html_e('Apply','listeo_core'); ?></a>
							</div>
						<div id="coupon-widget-wrapper-output">
							<div  class="notification error closeable" ></div>
							<div  class="notification success closeable" id="coupon_added"><?php esc_html_e('Suksess','listeo_core'); ?></div>
						</div>
							<div id="coupon-widget-wrapper-applied-coupons">
								<?php 
									if(isset($data->coupon) && !empty($data->coupon)){
										$coupons = explode(',',$data->coupon);
										foreach ($coupons as $key => $value) {
													echo "<span data-coupon='{$value}'>{$value} <i class=\"fa fa-times\"></i></span>";
												}		
									}
								?>
							</div>
						</div>

					
					</li>
					<?php endif; ?>
					<?php if($data->price < 1): ?>
                         <li class="total-costs" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>><?php esc_html_e('Totalkostnad', 'listeo_core'); ?><span>GRATIS</span></li>
                    <?php endif; ?>
					<?php 
        				$decimals = get_option('listeo_number_decimals',2);
        
					if($data->price>0): ?>
						 <?php
                            if(isset($_POST["discount"]) && $_POST["discount"] != ""){
                            ?>   
                            <li class="discount-costs price_div_booking"><?php esc_html_e('Valgt målgruppe', 'listeo_core'); ?><span><?php echo $_POST["discount"];?></span></li>
                         <?php } ?> 
					     <?php 


					    if(isset($data->taxprice) && $data->taxprice != "" && $data->taxprice > 0){ ?>


					        <li class="total-costs" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>>
						     	<?php esc_html_e('Valgt tid (ink. mva)', 'listeo_core'); ?>
						     	<span>
	    							<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo
	    							$data->normalprice; if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?>
	    						</span>
	    					</li>
	    					
						    <li class="total-costs" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>>
						     	<?php esc_html_e('Total mva', 'listeo_core'); ?>
						     	<span>
	    							<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo
	    							$data->taxprice; if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?>
	    						</span>
	    					</li>
    					<?php } ?>

						<li class="total-costs <?php if(isset($data->price_sale)): ?> estimated-with-discount<?php endif;?>" data-price="<?php echo esc_attr($data->price); ?>" <?php if($_hide_price_div == "on"){ ?> style="display: none" <?php } ?>><?php esc_html_e('Total Cost', 'listeo_core'); ?><span> 
						<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo number_format_i18n($data->price,$decimals); if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span></li>
						<?php endif; ?>	
					<?php if(isset($data->price_sale)): ?>

						<?php $decimals = get_option('listeo_number_decimals',2); ?>
						<li class="total-discounted_costs"><?php esc_html_e('Final Cost', 'listeo_core'); ?><span> 
						<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo number_format_i18n($data->price_sale,$decimals); if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span></li>
						
					<?php else: ?>
						<li style="display:none;" class="total-discounted_costs"><?php esc_html_e('Final Cost', 'listeo_core'); ?><span> </span></li>
					<?php endif; ?>
				</ul>

			</div>
			<!-- Booking Summary / End -->

		</div>
</div>
<style type="text/css">
.booking-confirmation-coupons{
	display: none;
}
</style>
<?php
add_action("wp_footer", "add_overlay");
function add_overlay(){ ?>
   <div class="overlay" style="display: none;">
        <div class="overlay__inner">
            <div class="overlay__content"><span class="spinner"></span></div>
        </div>
    </div>
<?php }
$_SESSION['date_start'] = $data->date_start;
$_SESSION['date_end'] = $data->date_end;
$_SESSION['time'] = $data->_hour; // both start and end
$_SESSION['title'] = get_the_title($data->listing_id);

$_SESSION['id'] = $data->listing_id;
$_SESSION['status'] = 'waiting';

?>
<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/intlTelInput.js?ver=5.7.2"></script>
<script>
	setTimeout(function(){
	   window.location.href='<?php echo esc_url(wp_get_referer() ?: home_url()); ?>';
	},5 * 60 * 1000)  
	var phoneLengthMapping = {
		"af": { min: 9, max: 9 },
		"al": { min: 9, max: 9 },
		"dz": { min: 9, max: 9 },
		"ad": { min: 6, max: 6 },
		"ao": { min: 9, max: 9 },
		"ar": { min: 10, max: 10 },
		"au": { min: 9, max: 9 },
		"at": { min: 10, max: 13 },
		"bd": { min: 10, max: 10 },
		"be": { min: 8, max: 9 },
		"br": { min: 10, max: 11 },
		"ca": { min: 10, max: 10 },
		"cn": { min: 11, max: 11 },
		"dk": { min: 8, max: 8 },
		"eg": { min: 10, max: 10 },
		"fi": { min: 7, max: 12 },
		"fr": { min: 9, max: 9 },
		"de": { min: 7, max: 15 },
		"in": { min: 10, max: 10 },
		"id": { min: 9, max: 12 },
		"it": { min: 9, max: 10 },
		"jp": { min: 10, max: 11 },
		"mx": { min: 10, max: 10 },
		"nl": { min: 9, max: 9 },
		"no": { min: 8, max: 8 },
		"pk": { min: 10, max: 10 },
		"pl": { min: 9, max: 9 },
		"ru": { min: 10, max: 10 },
		"sa": { min: 9, max: 9 },
		"za": { min: 9, max: 9 },
		"es": { min: 9, max: 9 },
		"se": { min: 7, max: 13 },
		"ch": { min: 9, max: 9 },
		"tr": { min: 10, max: 10 },
		"gb": { min: 10, max: 10 },
		"us": { min: 10, max: 10 },
		"vn": { min: 9, max: 11 }
	};
    var input = document.querySelector("#pphone");
     if(input != null){
		var lang_code = "no"
        if(jQuery("html").attr("lang") != undefined){
          var langg = jQuery("html").attr("lang");
          lang_code = langg.split("-")[1];

          if(lang_code != ""){
            lang_code = lang_code.toLowerCase();
          }
        }
        var iti = window.intlTelInput(input, {
                initialCountry: lang_code,
			    allowExtensions: true,
                formatOnDisplay: true,
                autoFormat: true,
                numberType: "MOBILE",
                preventInvalidNumbers: true,
                separateDialCode: true,
        utilsScript: "<?php echo get_stylesheet_directory_uri();?>/assets/js/utils.js?ver=5.7.2",
        });

	    	// input.addEventListener("keypress", function (e) {
            //     var countryData = iti.getSelectedCountryData();
            //     var countryCode = countryData.iso2;
            //     var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

            //     // Get current input value length
            //     var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

            //     if (phoneNumber.length >= maxLength && !e.metaKey && !e.ctrlKey) {
			// 		input.setCustomValidity("Incorrect number of digit only allow " + maxLength);
			// 		input.reportValidity();
            //     }else{
			// 		input.setCustomValidity("");
			// 	}
            // });

            // Validate on input
            input.addEventListener("input", function () {
                var countryData = iti.getSelectedCountryData();
                var countryCode = countryData.iso2;
                var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

                if (isValidPhoneNumber(phoneNumber, countryCode, iti)) {
                    input.setCustomValidity(""); // Valid number
                } else {
                    input.setCustomValidity("Invalid phone number for " + countryData.name);
                }
            });
            input.addEventListener("change", function () {
                input.reportValidity();
            });

            // Utility function to validate phone number length based on country
            function isValidPhoneNumber(phoneNumber, countryCode, itiInstance) {
                // Check if the number is valid based on intlTelInput's isValidNumber()
                if (!itiInstance.isValidNumber()) return false;

                // Get the length requirements from the mapping
                var lengthData = phoneLengthMapping[countryCode];
                if (lengthData) {
                    var nationalNumber = phoneNumber.replace(/\D/g, ""); // Remove non-digit characters
                    return nationalNumber.length >= lengthData.min && nationalNumber.length <= lengthData.max;
                }

                // Default fallback for countries not in the mapping
                return true;
            }


		jQuery("#pphone").keyup(function(){
			
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},500)
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},50)
		})
		jQuery("#pphone").on("countrychange", function () {
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},500)
			setTimeout(function(){
				jQuery("#pphone").attr("placeholder","");
			},50)
		});
    } 
    //get Week function
    Date.prototype.getWeek = function() {
    	var date = new Date(this.getTime());
    	date.setHours(0, 0, 0, 0);
    	date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
    	var week1 = new Date(date.getFullYear(), 0, 4);
    	return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000
    		- 3 + (week1.getDay() + 6) % 7) / 7);
    }

    function getDates(startDate, stopDate, checkMon, checkTue, checkWen, checkThu, checkFri, checkSut, checkSun) {
    	let dateArray = [];
    	let currentDate = moment(startDate);
    	let endDate = moment(stopDate);
    	while (currentDate <= endDate) {
    		currentDate = new Date(currentDate);
    		switch (currentDate.getDay()) {
    			case 0:
    			if (checkSun)
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			break;
    			case 1:
    			if (checkMon)
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			break;
    			case 2:
    			if (checkTue)
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			break;
    			case 3:
    			if (checkWen)
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			break;
    			case 4:
    			if (checkThu) {
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			}
    			break;
    			case 5:
    			if (checkFri) {
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			}
    			break;
    			case 6:
    			if (checkSut) {
    				dateArray.push(moment(currentDate).format('MM/DD/YYYY'));
    			}
    			break;
    		}
    		currentDate = moment(currentDate).add(1, 'days');
    	}
    	return dateArray;
    }

	if(jQuery('#pdfLinks').children().length == 0){
    	jQuery('#pdfApprove').click();
    	jQuery('.pdfDoc').hide();
    	//jQuery(".external_div_checked").show();
    	jQuery('#pdfApprove').prop("checked",true);
    }

    // if(!jQuery('#pdfApprove').is(':checked')){
    // 	jQuery('.booking-confirmation-btn').css('pointer-events','none');
    // }

    

    // jQuery('#pdfApprove').change(function() {
    // 	if(!jQuery('#pdfApprove').is(':checked')){
    // 		jQuery('.booking-confirmation-btn').css('pointer-events','none');
    // 	}else{
    // 		jQuery('.booking-confirmation-btn').css('pointer-events','');
    // 	}
    // });

    <?php if($data->date_start == null):?>
    	jQuery('#repeatedBookingElement').hide();
    <?php endif; ?>

    <?php if($data->date_start != null):?>

    	jQuery('#repeatOptions input').change(function(){
    		<?php if ($data->date_start != $data->date_end ):?>
    			jQuery('.selectDates .date a').parent().hide()
    			if(jQuery('#repeatOptions input:eq(0)').is(':checked')){
    				jQuery('#repeatBookingButtonMultiplyDaysOne').parent().show();
    			}else if(jQuery('#repeatOptions input:eq(1)').is(':checked')){
    				jQuery('#repeatBookingButtonMultiplyDaysTwo').parent().show();
    			}else if(jQuery('#repeatOptions input:eq(2)').is(':checked')){
    				jQuery('#repeatBookingButtonMultiplyDaysThree').parent().show();
    			}
    			<?php else:?>
    				jQuery('.selectDates .secondRepeatdButtons a').parent().hide()
    				if(jQuery('#repeatOptions input:eq(0)').is(':checked')){
    					jQuery('#repeatBookingButton').parent().show();
    				}else if(jQuery('#repeatOptions input:eq(1)').is(':checked')){
    					jQuery('#repeatBookingButtonOptionTwo').parent().show();
    				}else if(jQuery('#repeatOptions input:eq(2)').is(':checked')){
    					jQuery('#repeatBookingButtonOptionThree').parent().show();
    				}
    			<?php endif; ?>
    		});

    	<?php if ($data->date_start != $data->date_end ):
    		$reservationFirstDay = strtotime($data->date_start);
    		$resStartDay = date('N',$reservationFirstDay);
    		$reservationSecondDay = strtotime($data->date_end);
    		$resEndDay = date('N',$reservationSecondDay);
    		?>
    		let startCounter = <?php echo $resStartDay?>;
    		let endCounter = <?php echo $resEndDay?>;

    		if(startCounter > endCounter){
    			for (let i = startCounter; i < 8 ; i++) {
    				switch (i){
    					case 1:
    					jQuery('#repeatMan').attr("checked", "checked");
    					break;
    					case 2:
    					jQuery('#repeatTir').attr("checked", "checked");
    					break;
    					case 3:
    					jQuery('#repeatOns').attr("checked", "checked");
    					break;
    					case 4:
    					jQuery('#repeatTor').attr("checked", "checked");
    					break;
    					case 5:
    					jQuery('#repeatFre').attr("checked", "checked");
    					break;
    					case 6:
    					jQuery('#repeatLor').attr("checked", "checked");
    					break;
    					case 7:
    					jQuery('#repeatSon').attr("checked", "checked");
    					break;
    				}
    			}
    			for (let j = 1; j <= endCounter ; j++) {
    				switch (j){
    					case 1:
    					jQuery('#repeatMan').attr("checked", "checked");
    					break;
    					case 2:
    					jQuery('#repeatTir').attr("checked", "checked");
    					break;
    					case 3:
    					jQuery('#repeatOns').attr("checked", "checked");
    					break;
    					case 4:
    					jQuery('#repeatTor').attr("checked", "checked");
    					break;
    					case 5:
    					jQuery('#repeatFre').attr("checked", "checked");
    					break;
    					case 6:
    					jQuery('#repeatLor').attr("checked", "checked");
    					break;
    					case 7:
    					jQuery('#repeatSon').attr("checked", "checked");
    					break;
    				}
    			}
    		}else {
    			for (let i = startCounter; i <= endCounter; i++) {
    				switch (i) {
    					case 1:
    					jQuery('#repeatMan').attr("checked", "checked");
    					break;
    					case 2:
    					jQuery('#repeatTir').attr("checked", "checked");
    					break;
    					case 3:
    					jQuery('#repeatOns').attr("checked", "checked");
    					break;
    					case 4:
    					jQuery('#repeatTor').attr("checked", "checked");
    					break;
    					case 5:
    					jQuery('#repeatFre').attr("checked", "checked");
    					break;
    					case 6:
    					jQuery('#repeatLor').attr("checked", "checked");
    					break;
    					case 7:
    					jQuery('#repeatSon').attr("checked", "checked");
    					break;
    				}
    			}
    		}
    		jQuery('#repeatDays').hide();
    	<?php endif; ?>

    	jQuery('#repeatBookingButton').on('click',function(){

    		jQuery('#listOfAvailableDates').empty()

    		if(jQuery('#repeatToDate').val() == ''){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		let repeatEndDate = jQuery('#repeatToDate').val();
    		let repeatMon = jQuery('#repeatMan').is(':checked');
    		let repeatTue = jQuery('#repeatTir').is(':checked');
    		let repeatWen = jQuery('#repeatOns').is(':checked');
    		let repeatThu = jQuery('#repeatTor').is(':checked');
    		let repeatFri = jQuery('#repeatFre').is(':checked');
    		let repeatSut = jQuery('#repeatLor').is(':checked');
    		let repeatSun = jQuery('#repeatSon').is(':checked');

    		if(!(repeatMon || repeatTue || repeatWen || repeatThu || repeatFri || repeatSut || repeatSun)){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DAYS</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		ajax_data = {
    			'action': 'listeo_check_repeat_booking_availability',
    			'repeat_end_date' : repeatEndDate,
    			'listing_id': <?php echo $data->listing_id ?>,
    			'date_from' : <?php echo json_encode($data->date_start) ?>,
    			'date_to' : <?php echo json_encode($data->date_end) ?>,
    			
    		};

    		jQuery.ajax({
    			type: 'POST', dataType: 'json',
    			url: listeo.ajaxurl,
    			data: ajax_data,

    			success: function(data){
    				let reservations = data.reservations;
    				let endDate = jQuery('#repeatToDate').val();
    				let startDate=new Date('<?php echo $data->date_start; ?>');
    				startDate.setDate(startDate.getDate() + 1);
    				let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);

    				dates.forEach(myFunction);

    				function myFunction(item) {
    					if(reservations.includes(item)){
    						let tagli = `<li style="pointer-events: none; color: red;"><input style="width: auto; height: auto;" type='checkbox' value='${item}'><span style="text-decoration:line-through">  ${item}</span></li>`;
    						jQuery('#listOfAvailableDates').append(tagli);
    					}else{
    						let tagli = `<li><input style="width: auto; height: auto;" type='checkbox' value='${item}'><span>  ${item}</span></li>`;
    						jQuery('#listOfAvailableDates').append(tagli);
    					}
    				}

    			}

    		});

    	});
    	jQuery('#repeatBookingButtonOptionTwo').on('click',function(){

    		jQuery('#listOfAvailableDates').empty()

    		if(jQuery('#repeatToDate').val() == ''){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		let repeatEndDate = jQuery('#repeatToDate').val();
    		let repeatMon = jQuery('#repeatMan').is(':checked');
    		let repeatTue = jQuery('#repeatTir').is(':checked');
    		let repeatWen = jQuery('#repeatOns').is(':checked');
    		let repeatThu = jQuery('#repeatTor').is(':checked');
    		let repeatFri = jQuery('#repeatFre').is(':checked');
    		let repeatSut = jQuery('#repeatLor').is(':checked');
    		let repeatSun = jQuery('#repeatSon').is(':checked');

    		if(!(repeatMon || repeatTue || repeatWen || repeatThu || repeatFri || repeatSut || repeatSun)){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DAYS</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		ajax_data = {
    			'action': 'listeo_check_repeat_booking_availability',
    			'repeat_end_date' : repeatEndDate,
    			'listing_id': <?php echo $data->listing_id ?>,
    			'date_from' : <?php echo json_encode($data->date_start) ?>,
    			'date_to' : <?php echo json_encode($data->date_end) ?>,
    		
    		};

    		jQuery.ajax({
    			type: 'POST', dataType: 'json',
    			url: listeo.ajaxurl,
    			data: ajax_data,

    			success: function(data){
    				let reservations = data.reservations;
    				let endDate = jQuery('#repeatToDate').val();
    				let startDate= new Date ('<?php echo $data->date_start; ?>');
    				startDate.setDate(startDate.getDate() + 1);
    				let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);
    				let oddOrEven = startDate.getWeek() % 2;

    				dates.forEach(myFunction);

    				function myFunction(item) {
    					if(reservations.includes(item)){
    						newItem = new Date(item);
    						if((newItem.getWeek()%2) == oddOrEven){
    							let tagli = `<li style="pointer-events: none; color: red;"><input style="width: auto; height: auto;" type='checkbox' value='${item}'><span style="text-decoration:line-through">  ${item}</span></li>`;
    							jQuery('#listOfAvailableDates').append(tagli);
    						}
    					}else{
    						newItem = new Date(item);
    						if((newItem.getWeek()%2) == oddOrEven){
    							let tagli = `<li><input style="width: auto; height: auto;" type='checkbox' value='${item}'><span>  ${item}</span></li>`;
    							jQuery('#listOfAvailableDates').append(tagli);
    						}
    					}
    				}

    			}

    		});

    	});
    	jQuery('#repeatBookingButtonOptionThree').on('click',function(){

    		jQuery('#listOfAvailableDates').empty()

    		if(jQuery('#repeatToDate').val() == ''){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		let repeatEndDate = jQuery('#repeatToDate').val();
    		let repeatMon = jQuery('#repeatMan').is(':checked');
    		let repeatTue = jQuery('#repeatTir').is(':checked');
    		let repeatWen = jQuery('#repeatOns').is(':checked');
    		let repeatThu = jQuery('#repeatTor').is(':checked');
    		let repeatFri = jQuery('#repeatFre').is(':checked');
    		let repeatSut = jQuery('#repeatLor').is(':checked');
    		let repeatSun = jQuery('#repeatSon').is(':checked');

    		if(!(repeatMon || repeatTue || repeatWen || repeatThu || repeatFri || repeatSut || repeatSun)){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DAYS</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		ajax_data = {
    			'action': 'listeo_check_repeat_booking_availability',
    			'repeat_end_date' : repeatEndDate,
    			'listing_id': <?php echo $data->listing_id ?>,
    			'date_from' : <?php echo json_encode($data->date_start) ?>,
    			'date_to' : <?php echo json_encode($data->date_end) ?>,
    			
    		};

    		jQuery.ajax({
    			type: 'POST', dataType: 'json',
    			url: listeo.ajaxurl,
    			data: ajax_data,

    			success: function(data){
    				let reservations = data.reservations;
    				let endDate = jQuery('#repeatToDate').val();
    				let startDate= new Date ('<?php echo $data->date_start; ?>');
    				startDate.setDate(startDate.getDate() + 1);
    				let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);

    				dates.forEach(myFunction);

    				function myFunction(item) {
    					let tagli;
    					if(reservations.includes(item)){
    						tagli = `<li style="pointer-events: none; color: red;"><input style="display:inline; width: auto; height: auto;" type='radio' name='monthDates' value='${item}'><span style="text-decoration:line-through">  ${item}</span></li>`;
    					}else{
    						tagli = `<li><input style="display:inline; width: auto; height: auto;" type='radio' name='monthDates' value='${item}'><span>  ${item}</span></li>`;
    					}
    					jQuery('#listOfAvailableDates').append(tagli);
    				}

    			}

    		});

    	});

    	jQuery('#repeatBookingButtonMultiplyDaysOne').on('click',function(){

    		jQuery('#listOfAvailableDates').empty();

    		if(jQuery('#repeatToDate').val() == ''){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		let repeatEndDate = jQuery('#repeatToDate').val();
    		let repeatMon = jQuery('#repeatMan').is(':checked');
    		let repeatTue = jQuery('#repeatTir').is(':checked');
    		let repeatWen = jQuery('#repeatOns').is(':checked');
    		let repeatThu = jQuery('#repeatTor').is(':checked');
    		let repeatFri = jQuery('#repeatFre').is(':checked');
    		let repeatSut = jQuery('#repeatLor').is(':checked');
    		let repeatSun = jQuery('#repeatSon').is(':checked');

    		let endDate = jQuery('#repeatToDate').val();
    		let startDate=new Date('<?php echo $data->date_end; ?>');
    		startDate.setDate(startDate.getDate() + 1);
    		let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);

    		let check = 0;
    		jQuery('#repeatDays input').each(function(){
    			if(jQuery(this).is(':checked')){
    				check++;
    			}
    		})
    		let sumDates = [];
    		let finalDates = [];

    		dates.forEach((element) => {
    			sumDates.push(element);
    			if(sumDates.length === check) {
    				finalDates.push(sumDates);
    				sumDates = [];
    			}
    		});

    		ajax_data = {
    			'action': 'listeo_check_repeat_booking_availability_multiply_days',
    			'repeat_end_date' : repeatEndDate,
    			'listing_id': <?php echo $data->listing_id ?>,
    			'date_from' : <?php echo json_encode($data->date_start) ?>,
    			'date_to' : <?php echo json_encode($data->date_end) ?>,
    			
    			'repeatMon' : jQuery('#repeatMan').is(':checked'),
    			'repeatTue' : jQuery('#repeatTir').is(':checked'),
    			'repeatWen' : jQuery('#repeatOns').is(':checked'),
    			'repeatThu' : jQuery('#repeatTor').is(':checked'),
    			'repeatFri' : jQuery('#repeatFre').is(':checked'),
    			'repeatSut' : jQuery('#repeatLor').is(':checked'),
    			'repeatSun' : jQuery('#repeatSon').is(':checked'),
    			'availableDates' : finalDates
    		};

    		jQuery.ajax({
    			type: 'POST', dataType: 'json',
    			url: listeo.ajaxurl,
    			data: ajax_data,

    			success: function(data){
    				let newFinalDates = data.finalDates;
    				function searchForArray(haystack, needle){
    					let i, j, current, currentNeedle;
    					for(i = 0; i < haystack.length; ++i){
    						if(needle[i] !== undefined) {
    							if (needle[i].length === haystack[i].length) {
    								current = haystack[i];
    								currentNeedle = needle[i];
    								for (j = 0; j < currentNeedle.length && currentNeedle[j] === current[j]; ++j) ;
    									if (j === currentNeedle.length) {
    										let tagli = `<li style="margin: 25px;"><input style="width: auto; height: auto;" type='checkbox' value='${current[0] +' - '+current[current.length-1]}'><span>  ${current[0] +' - '+current[current.length-1]}</span></li>`;
    										jQuery('#listOfAvailableDates').append(tagli);
    									}
    								}
    							}else {
    								current = haystack[i];
    								console.log(current);
    								let tagli = `<li style="margin: 25px;pointer-events: none; color: red;"><input style="width: auto; height: auto;" type='checkbox' value='${current[0] +' - '+current[current.length-1]}'><span style="text-decoration:line-through">  ${current[0] +' - '+current[current.length-1]}</span></li>`;
    								jQuery('#listOfAvailableDates').append(tagli);
    							}
    						}
    					}

    					searchForArray(finalDates,newFinalDates);
    				}

    			});

    	});
    	jQuery('#repeatBookingButtonMultiplyDaysTwo').on('click',function(){

    		jQuery('#listOfAvailableDates').empty();

    		if(jQuery('#repeatToDate').val() == ''){
    			let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
    			jQuery('#listOfAvailableDates').append(tagli);
    			return ;
    		}

    		let repeatEndDate = jQuery('#repeatToDate').val();
    		let repeatMon = jQuery('#repeatMan').is(':checked');
    		let repeatTue = jQuery('#repeatTir').is(':checked');
    		let repeatWen = jQuery('#repeatOns').is(':checked');
    		let repeatThu = jQuery('#repeatTor').is(':checked');
    		let repeatFri = jQuery('#repeatFre').is(':checked');
    		let repeatSut = jQuery('#repeatLor').is(':checked');
    		let repeatSun = jQuery('#repeatSon').is(':checked');

    		let endDate = jQuery('#repeatToDate').val();
    		let startDate=new Date('<?php echo $data->date_end; ?>');
    		startDate.setDate(startDate.getDate() + 1);
    		let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);

    		let check = 0;
    		jQuery('#repeatDays input').each(function(){
    			if(jQuery(this).is(':checked')){
    				check++;
    			}
    		})
    		let sumDates = [];
    		let finalDates = [];

    		dates.forEach((element) => {
    			sumDates.push(element);
    			if(sumDates.length === check) {
    				finalDates.push(sumDates);
    				sumDates = [];
    			}
    		});

    		ajax_data = {
    			'action': 'listeo_check_repeat_booking_availability_multiply_days',
    			'repeat_end_date' : repeatEndDate,
    			'listing_id': <?php echo $data->listing_id ?>,
    			'date_from' : <?php echo json_encode($data->date_start) ?>,
    			'date_to' : <?php echo json_encode($data->date_end) ?>,
    		
    			'repeatMon' : jQuery('#repeatMan').is(':checked'),
    			'repeatTue' : jQuery('#repeatTir').is(':checked'),
    			'repeatWen' : jQuery('#repeatOns').is(':checked'),
    			'repeatThu' : jQuery('#repeatTor').is(':checked'),
    			'repeatFri' : jQuery('#repeatFre').is(':checked'),
    			'repeatSut' : jQuery('#repeatLor').is(':checked'),
    			'repeatSun' : jQuery('#repeatSon').is(':checked'),
    			'availableDates' : finalDates
    		};

    		jQuery.ajax({
    			type: 'POST', dataType: 'json',
    			url: listeo.ajaxurl,
    			data: ajax_data,

    			success: function(data){
    				let newFinalDates = data.finalDates;
    				let oddOrEven = startDate.getWeek() % 2;
    				function searchForArray(haystack, needle){
    					let i, j, current, currentNeedle, newItem;
    					for(i = 0; i < haystack.length; ++i){
    						if(needle[i] !== undefined) {
    							if (needle[i].length === haystack[i].length) {
    								current = haystack[i];
    								currentNeedle = needle[i];
    								for (j = 0; j < currentNeedle.length && currentNeedle[j] === current[j]; ++j) ;
    									if (j === currentNeedle.length) {
    										newItem = new Date(current[0]);
    										if((newItem.getWeek()%2) === oddOrEven) {
    											let tagli = `<li style="margin: 25px;"><input style="width: auto; height: auto;" type='checkbox' value='${current[0] + ' - ' + current[current.length - 1]}'><span>  ${current[0] + ' - ' + current[current.length - 1]}</span></li>`;
    											jQuery('#listOfAvailableDates').append(tagli);
    										}
    									}
    								}
    							}else {
    								current = haystack[i];
    								newItem = new Date(current[0]);
    								if((newItem.getWeek()%2) === oddOrEven) {
    									let tagli = `<li style="margin: 25px;pointer-events: none; color: red;"><input style="width: auto; height: auto;" type='checkbox' value='${current[0] + ' - ' + current[current.length - 1]}'><span style="text-decoration:line-through">  ${current[0] + ' - ' + current[current.length - 1]}</span></li>`;
    									jQuery('#listOfAvailableDates').append(tagli);
    								}
    							}
    						}
    					}

    					searchForArray(finalDates,newFinalDates);
    				}

    			});

    	});
jQuery('#repeatBookingButtonMultiplyDaysThree').on('click',function(){

	jQuery('#listOfAvailableDates').empty();

	if(jQuery('#repeatToDate').val() == ''){
		let tagli = `<li style="pointer-events: none; color: red;"><span>PLEASE SELECT DATE</span></li>`;
		jQuery('#listOfAvailableDates').append(tagli);
		return ;
	}

	let repeatEndDate = jQuery('#repeatToDate').val();
	let repeatMon = jQuery('#repeatMan').is(':checked');
	let repeatTue = jQuery('#repeatTir').is(':checked');
	let repeatWen = jQuery('#repeatOns').is(':checked');
	let repeatThu = jQuery('#repeatTor').is(':checked');
	let repeatFri = jQuery('#repeatFre').is(':checked');
	let repeatSut = jQuery('#repeatLor').is(':checked');
	let repeatSun = jQuery('#repeatSon').is(':checked');

	let endDate = jQuery('#repeatToDate').val();
	let startDate=new Date('<?php echo $data->date_end; ?>');
	startDate.setDate(startDate.getDate() + 1);
	let dates = getDates(startDate,endDate,repeatMon,repeatTue,repeatWen,repeatThu,repeatFri,repeatSut,repeatSun);

	let check = 0;
	jQuery('#repeatDays input').each(function(){
		if(jQuery(this).is(':checked')){
			check++;
		}
	})
	let sumDates = [];
	let finalDates = [];

	dates.forEach((element) => {
		sumDates.push(element);
		if(sumDates.length === check) {
			finalDates.push(sumDates);
			sumDates = [];
		}
	});

	ajax_data = {
		'action': 'listeo_check_repeat_booking_availability_multiply_days',
		'repeat_end_date' : repeatEndDate,
		'listing_id': <?php echo $data->listing_id ?>,
		'date_from' : <?php echo json_encode($data->date_start) ?>,
		'date_to' : <?php echo json_encode($data->date_end) ?>,
		
		'repeatMon' : jQuery('#repeatMan').is(':checked'),
		'repeatTue' : jQuery('#repeatTir').is(':checked'),
		'repeatWen' : jQuery('#repeatOns').is(':checked'),
		'repeatThu' : jQuery('#repeatTor').is(':checked'),
		'repeatFri' : jQuery('#repeatFre').is(':checked'),
		'repeatSut' : jQuery('#repeatLor').is(':checked'),
		'repeatSun' : jQuery('#repeatSon').is(':checked'),
		'availableDates' : finalDates
	};

	jQuery.ajax({
		type: 'POST', dataType: 'json',
		url: listeo.ajaxurl,
		data: ajax_data,

		success: function(data){
			let newFinalDates = data.finalDates;
			function searchForArray(haystack, needle){
				let i, j, current, currentNeedle;
				for(i = 0; i < haystack.length; ++i){
					if(needle[i] !== undefined) {
						if (needle[i].length === haystack[i].length) {
							current = haystack[i];
							currentNeedle = needle[i];
							for (j = 0; j < currentNeedle.length && currentNeedle[j] === current[j]; ++j) ;
								if (j === currentNeedle.length) {
									let tagli = `<li style="margin: 25px;"><input style="display:inline; width: auto; height: auto;" type='radio' name='monthDates' value='${current[0] +' - '+current[current.length-1]}'><span>  ${current[0] +' - '+current[current.length-1]}</span></li>`;
									jQuery('#listOfAvailableDates').append(tagli);
								}
							}
						}else {
							current = haystack[i];
							let tagli = `<li style="margin: 25px;pointer-events: none; color: red;"><input style="display:inline; width: auto; height: auto;" type='radio' name='monthDates' value='${current[0] +' - '+current[current.length-1]}'><span style="text-decoration:line-through">  ${current[0] +' - '+current[current.length-1]}</span></li>`;
							jQuery('#listOfAvailableDates').append(tagli);
						}
					}
				}

				searchForArray(finalDates,newFinalDates);
			}

		});

});
<?php endif; ?>
jQuery(document).click(function(){
	jQuery(".error_message").hide();
})
function flag_function(){
	if(jQuery(".iti__active").attr("data-dial-code") == "47"){
		jQuery(".phone_class").prop("maxlength",12);
		jQuery("input[name=billing_postcode]").prop("maxlength",4);
	}else{
		jQuery(".phone_class").removeAttr("maxlength");
		jQuery("input[name=billing_postcode]").removeAttr("maxlength");
	}
}
jQuery(".phone_class").keypress(function(){
	flag_function();
})

jQuery(document).on("click",".iti__country",function(){
	jQuery(".phone_class").val("");
	jQuery("input[name=billing_postcode]").val("");
	setTimeout(function(){
           flag_function();
	},500);
});
jQuery(document).ready(function(){
	setTimeout(function(){
           flag_function();
	},500);
})


jQuery(document).on("submit","#booking-confirmation",function(e){

    var error = 0;
    
    jQuery(".empty_div").removeClass("empty_div");

    let error_bk = false;
    jQuery(this).find(".required").each(function(){

        if(this.value == ""){
          jQuery(this).focus();
          jQuery(this).addClass("empty_div");
          jQuery(this).parent().find(".select2-container").addClass("empty_div");
          error_bk = true;
          
        }
    })


    jQuery(this).find("input[type=checkbox]").each(function(){

        if(jQuery(this).hasClass("required")){
             if(this.checked == false){
                jQuery(this).focus();
                jQuery(this).addClass("empty_div_checkbox");
                error_bk = true;
                return false;
             }
        }
    })

	var dail_code = jQuery(this).find("#pphone").parent().parent().find(".iti__selected-dial-code");

	if(dail_code && dail_code.length > 0){
		dail_code = dail_code.html();
		dail_code = dail_code.replace("+","");
		phone_val = jQuery(this).find("#pphone").val();

		var allow_phone = jQuery(this).find(".allow_phone").val();

		if (phone_val.startsWith(dail_code) && allow_phone != "true") {
			// Show warning if phone number starts with the dial code
			var phoneField = jQuery(this).find("#pphone");
			if (phoneField.siblings('.phone-warning').length === 0) {
				phoneField.focus();
				phoneField.parent().after(`
					<div class="phone-warning" style="z-index:99999;margin-top: -14px;position:absolute;padding: 10px;background-color: #fff8DD;;border-radius: 5px;color: #333;display: flex;justify-content: space-between;align-items: center;">
						Er nummeret riktig?
						<button class="btn btn-warning btn-sm close-warning warning_phone" style="background:#008474;color:#fff;font-size: 14px;">Ja, det er riktig</button>
					</div>
				`);
			}
			error_bk = true;
			return false;
		}
	}


    if(error_bk == true){
       error = 1;
       return false;
       e.preventDefault();
    }

    if(jQuery('#pdfApprove').length > 0){

      var checkBoxVal = jQuery('#pdfApprove').val();
            var checkBoxValChecked = jQuery('#pdfApprove').is(':checked');
            if(checkBoxValChecked === false) {
              jQuery('#checkbox-error').show(); 
               error = 1;
              return false;
              e.preventDefault();
            }
    }        

    /*if(jQuery(this).find('#pphone').val().length != 8){
        
        error = 1;
        alert("Bare 8 nummer tillatt i telefonnummer.")
        e.preventDefault();
        
    }*/
    var emailaddress = jQuery(this).find('input[name=email]').val();
    if( !validateEmail(emailaddress)) { 
        
        error = 1;
       jQuery(this).find('input[name=email]').focus();
       jQuery(".error_message_email").text("Skriv inn gyldig e -postadresse.");
       jQuery(".error_message_email").show();

       
    }

    if (jQuery(".iti__active").attr("data-dial-code") == "47") {
    const phoneNumber = jQuery(".phone_class").val().replace(/\D/g, ""); // Remove non-digit characters
    if (phoneNumber.length < 7 || phoneNumber.length > 16) {
        jQuery(".error_message_phone").text("Telefonnummeret må være på 7 til 16 sifre.");
        jQuery(".error_message_phone").show();
        error = 1;
    }
	    if("<?php echo $hide_div_invoice;?>" == ""){
			if(jQuery("input[name=billing_postcode]").val().length < 4){
				jQuery(".error_message_postcode").text("Postnummer må være 4-sifret.");
				jQuery(".error_message_postcode").show();
				error = 1;
			}
		}
		
	}

    var button = jQuery(".booking-confirmation-btn");
    button.removeClass('loading');

    
    if(error == 1){
    	e.preventDefault();
    
        setTimeout(function(){
            var button = jQuery(".booking-confirmation-btn");
            button.removeClass('loading');
        },1000);
    }else{
       var button = jQuery(".booking-confirmation-btn");
       button.addClass('loading');
    }  


})
jQuery(document).on("click","#booking-confirmation .warning_phone",function(){

	jQuery("#booking-confirmation").append("<input type='hidden' class='allow_phone' value='true'>");

	jQuery(".phone-warning").remove();

})

jQuery('#pphone').keypress(function(event){
   if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
       event.preventDefault(); //stop character from entering input
   }
   

});

function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}

jQuery('.booking-confirmation-btn').on('click',function(event){
    
    

	    //event.preventDefault()
	let informations = <?php echo wp_unslash(htmlspecialchars_decode($_POST['value'])) ?>;
	let firstName = jQuery("input[name='firstname']").val();

	let uid = jQuery("input[name='uid']").val();
	let lastName = jQuery("input[name='lastname']").val();

    let pnumber = iti.getSelectedCountryData();
    let country_code = "+"+pnumber["dialCode"];
    //let phone = country_code +'-'+ jQuery("#pphone").val();
    let phone = jQuery("#pphone").val();
    
    jQuery("#country_code").val(country_code)
   // jQuery("input[name='phone']").val(country_code+'-'+jQuery("input[name='pphone']").val())
    let email = jQuery.trim(jQuery("input[name='email']").val());
    let message = jQuery("textarea[name='message']").val();
   
	let billing_address_1 = jQuery("input[name='billing_address_1']").val();
	let billing_postcode = jQuery("input[name='billing_postcode']").val();
	let billing_city = jQuery("input[name='billing_city']").val();
	let billing_country = jQuery("input[name='billing_country']").val();
	let organization_number = jQuery("input[name='organization_number']").val();
    jQuery(".er_error").remove();
    /*if(jQuery("#booking-confirmation").find("input[name=firstname]").val() == ""){
       alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=firstname]").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=lastname]").val() == ""){
      alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=lastname]").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=email]").val() == ""){
       alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=email]").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=billing_address_1]").val() == ""){
       alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=billing_address_1]").focus();
    }else if(jQuery("#pphone").val() == ""){
        alert("Alle felt må fylles ut.")
        jQuery("#booking-confirmation").find("#pphone").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=billing_postcode]").val() == ""){
      alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=billing_postcode]").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=billing_city]").val() == ""){
       alert("Alle felt må fylles ut.")
       jQuery("#booking-confirmation").find("input[name=billing_city]").focus();
    }else if(jQuery("#booking-confirmation").find("input[name=firstname]").val() == ""){
        alert("Alle felt må fylles ut.")
      // jQuery("#label_message").parent().append('<span class="er_error" style="color:red;">Alle felt må fylles ut.</span>');
       jQuery("#booking-confirmation").find("input[name=firstname]").focus();
    }*/
    ajax_data_rg= {
			'action': 'insert_repeat_booking',
			'value': informations,
			'uid': uid,
			'firstname': firstName,
			'lastname': lastName,
			'phone': phone,
			'country_code': country_code,
			'message': message,
			'email': email,
			'billing_address_1': billing_address_1,
			'billing_postcode': billing_postcode,
			'billing_city': billing_city,
			'billing_country': billing_country,
			'organization_number':organization_number
		};
    jQuery.ajax({
			type: 'POST',
			url: '<?php echo get_stylesheet_directory_uri().'/rgajax/rgajax.php' ?>',
			data: ajax_data_rg,

			success: function(response){
			},
			error: function(){
			    }

		});
    
    
    setTimeout(function(){
         jQuery(".er_error").remove();
    },7000)

	let allDates = [];
	if(jQuery('#repeatOptions input').is(':checked')){
		if(jQuery('#availableDates ul li').length && jQuery('#availableDates ul li input:checked').length){
			jQuery('#availableDates ul li input:checked').each(function(){
				allDates.push(jQuery(this).val());
			});
		}
	}


	let counter = 0;
	while (counter < allDates.length){

		if(allDates[counter].includes('-')){
			res = allDates[counter].split(' ');
			informations.date_start = res[0];
			informations.date_end = res[2];
		}else {
			informations.date_start = allDates[counter];
			informations.date_end = allDates[counter];
		}

		ajax_data = {
			'action': 'insert_repeat_booking',
			'value': informations,
			'firstname': firstName,
			'lastname': lastName,
			'phone': phone,
			'country_code': country_code,
			'message': message,
			'email': email,
			'billing_address_1': billing_address_1,
			'billing_postcode': billing_postcode,
			'billing_city': billing_city,
			'billing_country': billing_country,
			'organization_number':organization_number
		};
		console.log(ajax_data)
		jQuery.ajax({
			type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,

			success: function(data){
				console.log('repeated success');
			}

		});
		
		
		counter++;
	}

});


var $ = jQuery;
$(document).ready(function() {
	$("input[name$='personalorcompany']").click(function() {
		var test = $(this).val();
		if(test=="Personal"){
			jQuery("input[name=organization_number]").removeAttr("required");
			jQuery("input[name=lastname]").attr("required","true");
			
			$("#org").css("display","none");
			$("#lastname").show();
			$('#label_firstname').html("Fornavn <span style='display:none;color:red;'> *</span>");
			$('#label_phone').html("Telefon <span style='display:none;color:red;'> *</span>");
		}else{
            jQuery("input[name=lastname]").removeAttr("required");
			jQuery("input[name=organization_number]").attr("required","true");
			$("#lastname").hide();
			$("#org").css("display","block") 
			$('#label_firstname').html("Navn* <span style='display:none;color:red;'> *</span>");
			$('#label_phone').html("Telefon* <span style='display:none;color:red;'> *</span>");
			//$("input[name='lastname']").val("");
		}
	});
});

</script>
<?php 
}

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		setTimeout(function(){
	        jQuery(".online_menu").attr("style","display:none !important");
	    },100)
	     jQuery(".email_class").change(function(){
            if(this.value != ""){
            	jQuery(".overlay").show();
                ajax_data = {
                    'action': 'email_user_data',
                    'email': this.value,
                };
                jQuery.ajax({
                    type: 'POST',
                    url: listeo.ajaxurl,
                    data: ajax_data,
                    success: function(response) {
                    		jQuery(".overlay").hide();
                       if(response.success){


                       
						    iti.destroy()

                       
							let datats = response.data;

							let phone = datats.phone;

							if(datats.country_code && datats.country_code != ""){

							phone = datats.country_code+datats.phone;

							}
							jQuery("#pphone").val(phone);

							iti._init()

							let cnt = iti.getSelectedCountryData();

							if(iti.getSelectedCountryData().iso2 != undefined){
							var counrt_code = iti.getSelectedCountryData().iso2;
							iti.setCountry(counrt_code)
							jQuery("#pphone").val(datats.phone);

							}
                          jQuery("input[name=firstname]").val(datats.first_name);
                          jQuery("input[name=lastname]").val(datats.last_name);
                          if(datats.billing_postcode != ""){
                            jQuery("input[name=billing_postcode]").val(datats.billing_postcode);
                          }else{
                            jQuery("input[name=billing_postcode]").val("1111");
                          }
                          if(datats.billing_city != ""){
                            jQuery("input[name=billing_city]").val(datats.billing_city);
                          }else{
                            jQuery("input[name=billing_city]").val("Ikke valgt");
                          }
                          if(datats.billing_address_1 != ""){
                            jQuery("input[name=billing_address_1]").val(datats.billing_address_1);
                          }else{
                            jQuery("input[name=billing_address_1]").val("Ikke valgt");
                          }
                          

                       }
                    }

                });
            }
        })
	});
</script>