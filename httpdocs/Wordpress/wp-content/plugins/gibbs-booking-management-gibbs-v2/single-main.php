<?php
require 'single_booking_function.php';
$user_id = get_current_user_ID();
global $wpdb;

$gibbs_user_groups = gibbs_user_groups($user_id); 


$user_group_data = array();
$user_group_data[0]->id = 0;
$user_group_data[0]->name = __("Mine","gibbs");
$kkk = 1;

$roless = array();
$users_exits_ids = array();
foreach ($gibbs_user_groups as $key => $user_group_da) {
	if($user_group_da->role == "3"){
		$user_group_data[$kkk]->id = $user_group_da->id;
		$user_group_data[$kkk]->name = $user_group_da->name;
		$roless[] = $user_group_da->role;
		$users_exits_ids[] = $user_group_da->id;
		$kkk++;
	}	
}
/*$booking_user_group_selected_id = get_user_meta($user_id,"booking_user_group_selected_id",true);

if($booking_user_group_selected_id == ""){
	if(!empty($user_group_data)){
		$booking_user_group_selected_id = $user_group_data[0]->id;
	}
}else{
	if(!in_array($booking_user_group_selected_id, $users_exits_ids)){
        $booking_user_group_selected_id = $user_group_data[0]->id;
	}
}*/
$group_id = UserGroups::get_active_group_id();

$booking_user_group_selected_id = $group_id;

$booking = get_single_booking_by_id($_GET["booking_id"],$user_id,$booking_user_group_selected_id,$page_type);

$data = $booking;
$order_content = "";
if(isset($booking->order_id) && $booking->order_id != "" && $booking->order_id != 0 && $booking->status == "paid"){
	$order = wc_get_order( $booking->order_id );




	ob_start();
	//include the specified file
	require (get_stylesheet_directory()."/woocommerce/checkout/thankyou.php");

	$order_content = ob_get_clean(); 
	
}
$custom_field_data_sets = CustomFieldsRenderer::get_custom_field_data_sets($booking->fields_data);
$app_field_datas = array();

$about_fields_data = array();



if(isset($booking->application_id) && $booking->application_id != ""){


    $application = $wpdb->get_row("SELECT id, application_data_id, app_fields_data FROM `applications` WHERE id=".$booking->application_id);

    if(isset($application->app_fields_data) && $application->app_fields_data != ""){

         $app_field_datas = maybe_unserialize($application->app_fields_data);

    }
    
    if(isset($application->application_data_id) && $application->application_data_id != ""){

       $application_data = $wpdb->get_row("SELECT about_fields_data FROM `application_data` WHERE id=".$application->application_data_id);
       

        if(isset($application_data->about_fields_data) && $application_data->about_fields_data != ""){

             $about_fields_data = maybe_unserialize($application_data->about_fields_data);

        }

    }

}
if($app_field_datas == ""){
    $app_field_datas = array();
}
if($about_fields_data == ""){
    $about_fields_data = array();
}
$fields_rows = array();

$exist_fields = true;

if(function_exists('advanced_fields')){
    global $wpdb;
    $listings_table =$wpdb->prefix. 'posts';
    $listings = $wpdb->get_row("SELECT users_groups_id FROM $listings_table WHERE ID=".$booking->listing_id);
    $group_id = $listings->users_groups_id; 
    if($group_id != "")
    {
        foreach ($custom_field_data_sets as $key_index => $field_data)
        {
            $fields_rows[] = advanced_fields(0, $group_id, 0, $field_data, $key_index, true);
        }
    }
}
if(empty($fields_rows)){

    $fields_rowssss = advanced_fields(0,$group_id,0,array(),0,true,"booking_calender");

    if(!empty($fields_rowssss)){

       $fields_rows[] = $fields_rowssss;

    }else{
       $exist_fields = false;
    }
}

if(!empty($fields_rows)){
    $field_name_labels = get_lables($group_id);
}
$group_admin = get_group_admin();
if($group_admin == ""){
	$group_admin = get_current_user_ID();
}
$field_btn_action = get_user_meta($group_admin,"field_btn_action",true);
if($field_btn_action == "false" || $field_btn_action == ""){
?>
<style type="text/css">
	.delete_field_div, .add_field_btn{
		display: none !important;
	}
</style>
<?php } ?>

<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/bootstrap.min.css">
<section class="dash-sec single_bk" style="display: none">
	<div class="outer-card">
		<div class="row">
		<div class="col-md-2">
			<div class="img-txt">
				<h3><?php echo $data->pr_name;?></h3>
			</div>
		</div>
		<div class="col-md-10">
			<div class="outer-right">
				<h4><?php if($data->order_id != "" && $data->order_id != 0){ 
                                echo "#".$data->order_id;
                            } ?>  <?php echo $data->bookings_author_name;?> 
							                           
								<?php if($data->status == "paid" && $data->fixed == "2"){ ?>
			                        <span class="season-btn"><?php echo __("Sesongbooking");?></span>
			                    <?php }elseif($data->status == "paid" && $data->fixed == "3"){ ?>
			                        <span class="season-btn"><?php echo __("Usendt faktura");?></span>
			                    <?php }elseif($data->status == "paid" && $data->fixed == "4"){ ?>
			                        <span class="season-btn"><?php echo __("Sendt faktura");?></span>
			                    <?php }else{ ?>
                            	    <span class="<?php echo $data->status;?>-btn"> <?php echo __($data->status,"gibbs");?></span>
                                <?php } ?>
				            
				</h4>
				<p><i class="fas fa-calendar"></i> <?php echo date("d M Y H:i",strtotime($data->date_start))." - ".date("d M Y H:i",strtotime($data->date_end));?> 
				  <!--   <?php if($data->conflict == "true"){ ?>
                       <i class="fa fa-exclamation-circle" title="Conflict booking. Please open the booking to see more details." style="color: red;"></i>
                    <?php } ?> -->
			   </p>
				<p><i class="fas fa-map-marker-alt"></i> <?php echo $data->listing_name;?> </p>
				<?php if($data->message != ""){ ?>
				  <p style="word-break: break-all;"><i class="fas fa-envelope"></i> <?php echo $data->message;?> </p>
				<?php } ?>  
			</div>
			<div class="notes-outer">
				<?php if($page_type == "owner"){ ?>
					<div class="search-box-inner notes-drop">
						<div class="dropdown">
						  <button class="dropbtn"><span class="filter_text">Notat</span> <i class="fas fa-sticky-note"></i></button>
						  <div id="listingDropdown" class="dropdown-content">
						    <div class="outer-drop-btn">
						    	<form method="post" class="savenoteform" action="<?php echo home_url();?>/wp-admin/admin-ajax.php">
						    		<input type="hidden" name="action" value="savenote">
						    		<input type="hidden" name="booking_id" value="<?php echo $data->id;?>">
						    	
							    	<h3>Booking notater <!-- <i class="fas fa-times"></i> --></h3>
							    	<hr class="row-marg">
							    	<h4> <span>Bare booking administrator(er) vil kunne se notatene</span></h4>
							    	<textarea name="description"><?php echo $data->description;?></textarea>
							    	<div class="btns-outers">
							    		<!-- <a href="#" class="close-btn btn-modal">Close</a> -->
							    		<a href="javscript:void(0)" class="save-btn btn-modal savenote">Lagre</a>
							    	</div>
						    	</form>
						    </div>
						  </div>
						</div>
					</div>
				<?php } ?>
				<div class="search-box-inner action-modal1 action_btns">
					<div class="dropdown">
					  <button class="dropbtn"><span class="filter_text"> </span> Handling &nbsp<i class="fa-solid fa-chevron-down"></i></button>
					  <div id="listingDropdown" class="dropdown-content">
					    <?php require(__DIR__."/modules/actionlist.php");?>
					  </div>
					</div>
					<?php require(__DIR__."/modal/sent_invoice_modal.php");?>
					<?php require(__DIR__."/modal/accept_decline_modal.php");?>
                    <?php require(__DIR__."/modal/edit_modal.php");?>
                    <?php require(__DIR__."/modal/reciept_modal.php");?>
				</div>
			</div>
		</div>
	</div>
	</div>
	<hr class="row-marg">
	<div class="row">
		<div class="col-md-12">
			<div class="tabs-outer">

				  <ul class="nav nav-tabs">
				    <li <?php if(!isset($_GET["tab"]) || $_GET["tab"] =="details"){ echo 'class="active"';}?>><a data-toggle="tab" href="#menu1" data-link="details">Bookingdetaljer</a></li>
				   <!--  <li <?php if(isset($_GET["tab"]) && $_GET["tab"] =="related_booking"){ echo 'class="active"';}?>>
				    	<a data-toggle="tab" href="#menu2" data-link="related_booking">Relaterte bookinger</a> -->
				    <!-- 	<span class="conflict_count_span" style="display: none;"></span> -->
				    </li>
				    <li <?php if(isset($_GET["tab"]) && $_GET["tab"] =="message"){ echo 'class="active"';}?>><a data-toggle="tab" href="#message" data-link="message">Meldinger</a></li>
			<!-- 	    <li <?php if(isset($_GET["tab"]) && $_GET["tab"] =="aktivitetslogg"){ echo 'class="active"';}?>><a data-toggle="tab" href="#menu5" data-link="aktivitetslogg">Aktivitetslogg</a></li>
				    <li <?php if(isset($_GET["tab"]) && $_GET["tab"] =="adgangslogg"){ echo 'class="active"';}?>><a data-toggle="tab" href="#menu6" data-link="adgangslogg">Adgangslogg</a></li> -->
				  </ul>

				  <div class="tab-content">
				    <div id="menu1" class="tab-pane fade  <?php if(!isset($_GET['tab']) || $_GET["tab"] =="details"){ echo 'in active';}?>">
				      <div class="row">
				      	<div class="col-md-4">
				      		<div class="outer-form-tabs">
						      	<table>
						      		<tr>
						      			<th colspan="2">Detaljer <?php if($page_type == "owner"){ ?> <!-- <i class="fa fa-edit edit_modalbtn<?php echo $data->id;?>"  data-booking_id="<?php echo $data->id; ?>"></i> --><?php } ?></th>
						      		</tr>
						      		<?php if($data->created != ""){ ?>
							      		<tr>
							      			<td>Opprettet:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->created;?></span></div></td>
							      		</tr>
							      	<?php } ?>
						      		<?php if($data->updated_at != ""){ ?>
							      		<tr>
							      			<td>Oppdatert:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->updated_at;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->id != "" && $data->id != 0){ ?>
							      		<tr>
							      			<td>Booking ID:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->id;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->order_id != "" && $data->order_id != 0){ ?>
							      		<tr>
							      			<td>Order nummer:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->order_id;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->price == "" || $data->price == 0){ ?>    
							      		<tr>
							      			<td>Total pris:</td>
							      			<td><div class="outer-table-data"><span><?php echo __("Gratis","gibbs");?></span></div></td>
							      		</tr>
					                   
					                <?php }else{ ?>
					                	<tr>
							      			<td>Total pris:</td>
							      			<td><div class="outer-table-data"><span><?php echo get_woocommerce_currency_symbol();?> <?php echo $data->price;?></span></div></td>
							      		</tr>
					                    
					                <?php } ?>
							      	<?php if($data->discount_group != ""){ ?>
							      		<tr>
							      			<td>Målgruppe:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->discount_group;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->purpose != ""){ ?>
							      		<tr>
							      			<td>Hensikt:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->purpose;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	
						      	</table>
						      </div>
				      	</div>
				      	<div class="col-md-4">
				      		<div class="outer-form-tabs">
						      	<table>
						      		<tr>
						      			<th colspan="2">Kunde Info</th>
						      		</tr>
						      		<?php if($data->display_name != ""){ ?>
							      		<tr>
							      			<td>Kunde:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->display_name;?></span></div></td>
							      		</tr>
							      	<?php } ?>
						      		<?php if($data->customer_email != ""){ ?>
							      		<tr>
							      			<td>E-post:</td>
							      			<td><div class="outer-table-data"><span><a href="mailto:<?php echo $data->customer_email;?>"><?php echo $data->customer_email;?></a></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->customer_tlf != ""){ ?>
							      		<tr>
							      			<td>Tlf:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->customer_tlf;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->customer_address != ""){ ?>
							      		<tr>
							      			<td>Adresse:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->customer_address;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->customer_zip != ""){ ?>
							      		<tr>
							      			<td>Postnr:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->customer_zip;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->customer_city != ""){ ?>
							      		<tr>
							      			<td>By:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->customer_city;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->org_number != ""){ ?>
							      		<tr>
							      			<td>Org nummer:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->org_number;?></span></div></td>
							      		</tr>
							      	<?php } ?>
						      	</table>
						      </div>
				      	</div>
				      	<div class="col-md-4">
				      		<div class="outer-form-tabs">
						      	<table>
						      		<tr>
						      			<th colspan="2">Fakturaadresse <?php if($page_type == "owner"){ ?> <!-- <i class="fa fa-edit edit_modalbtn<?php echo $data->id;?>"  data-booking_id="<?php echo $data->id; ?>"></i> --><?php } ?></th>
						      		</tr>
						      		<?php if($data->billing_name != ""){ ?>
							      		<tr>
							      			<td>Kunde:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_name;?></span></div></td>
							      		</tr>
							      	<?php } ?>
						      		<?php if($data->billing_email != ""){ ?>
							      		<tr>
							      			<td>E-post:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_email;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->billing_tlf != ""){ ?>
							      		<tr>
							      			<td>Tlf:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_tlf;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->billing_address != ""){ ?>
							      		<tr>
							      			<td>Adresse:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_address;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->billing_zip != ""){ ?>
							      		<tr>
							      			<td>Postnr:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_zip;?></span></div></td>
							      		</tr>
							      	<?php } ?>
							      	<?php if($data->billing_city != ""){ ?>
							      		<tr>
							      			<td>By:</td>
							      			<td><div class="outer-table-data"><span><?php echo $data->billing_city;?></span></div></td>
							      		</tr>
							      	<?php } ?>
						      	</table>
						      </div>
				      	</div>
				      </div>
				      
					    <div class="row mt-20">
<?php
    if (!empty($custom_field_data_sets) || $exist_fields == true)
    {
?>
                            <div class="col-md-4">
                                <div class="outer-form-tabs">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th colspan="4">Annen informasjon <?php if ($exist_fields == true)
                                                                                    { ?><i class="fa fa-edit edit_fieldmodal_btn<?php echo $booking->id; ?>"></i>
                                                        <div class="booking_fields_modal"><?php require(__DIR__ . "/modal/edit_fieldmodal.php"); ?> <?php } ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= CustomFieldsRenderer::get_data_sets_as_html_table_rows($custom_field_data_sets, $field_name_labels) ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
<?php
    }
?>

					      	    <?php if(!empty($about_fields_data)){?>
									    <div class="col-md-4">
									        <div class="outer-form-tabs">
									            <table>
									                <tr>
									                    <th colspan="4">
									                        Annen informasjon - Om søker
									                    </th>
									                </tr>
									                    
									                        <?php foreach ($about_fields_data as $key_about_fields => $field_about) { 
									                            $key_about_fields = str_replace("_", " ", $key_about_fields);
									                            $key_about_fields = str_replace("-", " ", $key_about_fields);

									                            ?>
									                            <tr>
									                                <td><?php echo $key_about_fields;?>: <span><?php echo $field_about;?></span></td>
									                             </tr>    
									                        <?php } ?>
									                
									                
									            </table>
									          </div>
									    </div>
								<?php } ?>
								<?php if(!empty($app_field_datas)){ ?>
									    <div class="col-md-4">
									        <div class="outer-form-tabs">
									            <table>
									                <tr>
									                    <th colspan="4">
														Annen informasjon - Søknad
									                    </th>
									                </tr>
									                        <?php foreach ($app_field_datas as $key_app_fields => $field_app) { 
									                            $key_app_fields = str_replace("_", " ", $key_app_fields);
									                            $key_app_fields = str_replace("-", " ", $key_app_fields);

									                            ?>
									                             <tr>
									                                <td><?php echo $key_app_fields;?>: <span><?php echo $field_app;?></span></td>
									                            </tr>    
									                        <?php } ?>
									                
									                
									            </table>
									          </div>
									    </div>
								<?php } ?>
					      
					      </div>
					    
					    
				    </div>
				    <div id="menu2" class="tab-pane fade <?php if(isset($_GET["tab"]) && $_GET["tab"] =="related_booking"){ echo 'in active"';}?>" >
				        <div class="col-xs-12 col-md-12 conflict_booking" style="display: none">
			                  <i class="fa fa-exclamation-circle"></i><span></span>
			            </div>
			            <div class="col-xs-12 col-md-12">
					        <div  class="booking_table_main" style="display: block;overflow: auto;">
								
							</div>
						</div>
				    </div>
				    <div id="message" class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] =="message"){ echo 'in active';}?>">
				       <?php require("messages.php");?>
				    </div>
				    <div id="menu5" class="tab-pane fade <?php if(isset($_GET["tab"]) && $_GET["tab"] =="aktivitetslogg"){ echo 'in active"';}?>" >
				      
				    	<div class="outer-card">
				    		<ul class="timeLineItem">
				    			<li><i class="fas fa-sack-dollar"></i> <p><strong>Betalt</strong> 24 Mai, 2022 kl 14:23 av Petter Hansen</p></li>
				    			<li><i class="fas fa-edit"></i> <p><strong>Redigert</strong> 23 Mai, 2022 kl 14:00 av Henrik Danielsen</p></li>
				    			<li><i class="fas fa-edit"></i> <p><strong>Redigert</strong> 23 Mai, 2021 kl 13:52 av Henrik Danielsen</p></li>
				    		</ul>
				    	</div>

				    </div>

				    <div id="menu6" class="tab-pane fade <?php if(isset($_GET["tab"]) && $_GET["tab"] =="adgangslogg"){ echo 'in active"';}?>" >
				      <div class="outer-card">
				    		<ul class="timeLineItem">
							<li><i class="fas fa-door-closed"></i> <p><strong>Lukket dør</strong> 28 Mai, 2022 kl 20:35 av Petter Hansen</p></li>
				    			<li><i class="fas fa-door-open"></i> <p><strong>Apnet dor</strong> 28 Mai, 2022 kl 20:33 av Petter Hansen</p></li>
				    			<li><i class="fas fa-door-closed"></i> <p><strong>Lukket dør</strong> 28 Mai, 2022 kl 18:24 av Petter Hansen</p></li>
				    			<li><i class="fas fa-door-open"></i> <p><strong>Apnet dor</strong> 28 Mai, 2022 kl 18:23 av Petter Hansen</p></li>
								<li><i class="fas fa-envelope"></i> <p><strong>Sendt adgangaskode på epost</strong> 24 Mai, 2022 kl 14:23 av Systemet</p></li>
				    		</ul>
				    	</div>
				    </div>
				  </div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo plugin_dir_url(__FILE__);?>js/bootstrap.min.js"></script>
<script src="<?php echo plugin_dir_url(__FILE__);?>js/rrule-tz.min.js"></script>
<?php 
$data = utf8_converter_dd($data);
$bookingggg = json_encode($data);
?>
<script>
	jQuery(".single_bk").show();
var bookingggg = <?php echo $bookingggg;?>;
let page = 1;
let totalCount = 10;

var bookings = [];

/*if("<?php echo $data->recurrenceRule;?>" != ""){


	var recBooking = new rrule.RRule.fromString("<?php echo $data->recurrenceRule;?>");
	if(recBooking.options.until == null){
		var dddd = new Date(recBooking.options.dtstart);
		dddd.setDate(dddd.getDate() + 700);
		recBooking.options.until = dddd;
	}

	var dt_startt = new Date(bookingggg.date_start);
	recBooking.options.dtstart = dt_startt;

	function isDeleted(orignalItem, booking) {
		var allRecArr = orignalItem.recurrenceException.split(",");
		if (allRecArr.find(item => item == booking)) {
			return true;
		} else {
			return false;
		}
	}
	function libRecExp(currentItem, eventItem) {
		var eventTime = new Date(eventItem.date_start);//object
		var eventHours = eventTime.getHours();
		var eventMin = eventTime.getMinutes();
		currentItem.setHours(eventHours, eventMin, 0, 0);
		return currentItem.toISOString();


	}

	recBooking = recBooking.all();

	if (recBooking.length > 0) {
		recBooking.forEach(function (item) { //Bookings List

			var month = item.getMonth() + 1;
			var dateee = item.getDate();

			if(month < 10){
	           month = "0"+month;
			}
			if(dateee < 10){
	           dateee = "0"+dateee;
			}
			var rec_date = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+item.getHours()).slice(-2)+":"+("0"+item.getMinutes()).slice(-2)+":"+("0"+item.getSeconds()).slice(-2);
			let tempObj = Object.assign({});
			//tempObj['recExp'] = libRecExp(item, bookingggg);
			tempObj['rec_date'] = rec_date;
			var tempRecExp = libRecExp(item, bookingggg);
			tempObj['rec_exp'] = tempRecExp;
			bookings.push(tempObj);
		});

	}
}*/

     let responsivePriority_len = 2;

	if(jQuery(".booking_datatable").find("tr").find("td").length  > 0){
       responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 1;
	}
	const dataJson = {
                    "paging" : false,
                    "info" : false,
			    	"language": {
			        "sProcessing":    "behandling...",
			        "sLengthMenu":    "Vis _MENU_ poster",
			        "sZeroRecords":   "Ingen resultater",
			        "sEmptyTable":    "Ingen data tilgjengelig i denne tabellen",
			        "sInfo":          "Viser _START_ til _END_ av _TOTAL_ bookinger",
			        "sInfoEmpty":     "Viser poster fra 0 til 0 av totalt 0 poster",
			        "sInfoFiltered":  "(filtrerer totalt _MAX_ poster)",
			        "sInfoPostFix":   "",
			        "sSearch":        "Søke:",
			        "sUrl":           "",
			        "sInfoThousands":  ",",
			        "sLoadingRecords": "Lader...",
			        "oPaginate": {
			            "sFirst":    "Først",
			            "sLast":    "Siste",
			            "sNext":    "Følgende",
			            "sPrevious": "Fremre"
			        },
			        "oAria": {
			            "sSortAscending":  ": Merk av for å sortere kolonnen i stigende rekkefølge",
			            "sSortDescending": ": Merk av for å sortere kolonnen synkende"
			        }
			    },
			    "bSort": true,
			    "aaSorting": [[ 3, "desc" ]],
			    "columnDefs": [ { 'targets': [0], // column index (start from 0)
			        'orderable': false, // set orderable false for selected columns
							    },
                                { responsivePriority: 20, targets: responsivePriority_len },
			    ],
		    };

    let exp_time = ""; 
	if(bookingggg && bookingggg.date_start)	    {
        exp_time = moment(bookingggg.date_start).toISOString();   
	}

	rec_booking_func(bookings,bookingggg,"<?php echo $user_id;?>", "<?php echo $booking_user_group_selected_id;?>", "<?php echo $page_type;?>", exp_time,page,totalCount);

	
function rec_booking_func(bookings,bookingggg,user_id,booking_user_group_selected_id,page_type,exp_time,page,totalCount){
	jQuery(".booking_datatable").addClass("loading_class");
	jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {"bookings_data":bookings,"booking_id":bookingggg.id,"user_id":user_id,"group_id":booking_user_group_selected_id,"page_type":page_type,"exp_time":exp_time,"page":page,"totalCount":totalCount,"action":"rec_booking"},  
        success: function(data){
        	jQuery(".booking_datatable").removeClass("loading_class");
        	jQuery('.booking_datatable').DataTable().destroy();
           // jQuery('.booking_datatable').DataTable().destroy();
        	jQuery('.booking_table_main').html(data.content);

        	/* if(data.conflict_count > 0 ){
        		jQuery(".conflict_booking").find("span").html("Fant <b>"+data.conflict_count+"</b> booking(er) som overlapper.")
        		jQuery(".conflict_booking").show();
        		jQuery(".conflict_count_span").show();
        		jQuery(".conflict_count_span").html(data.conflict_count);
        	} */
        	if(dataJson.columnDefs != undefined && dataJson.columnDefs[1] != undefined){
        		responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 1;
        		dataJson.columnDefs[1].targets = responsivePriority_len;
        	}

            jQuery('.booking_datatable').DataTable(dataJson).draw();

        }
    });
}

function filter_function($dd = "",$empty = "", page){
	rec_booking_func(bookings,bookingggg,"<?php echo $user_id;?>", "<?php echo $booking_user_group_selected_id;?>", "<?php echo $page_type;?>", exp_time,page,totalCount);
}
jQuery(document).on("click",".delete_rec",function(){
	var rec_exp = jQuery(this).data("rec_exp");
	var booking_id = jQuery(this).data("booking_id");
	if(confirm("are you sure!")){
		jQuery.ajax({
	        type: 'POST', 
	        dataType: 'json',
	        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
	        data: {"rec_exp":rec_exp,"booking_id":booking_id,"action":"add_rec_exp"},  
	        success: function(data){
	           window.location.reload();
	        }
	    });
	}    
}) 
jQuery(document).on("click",".un_delete_rec",function(){
	var rec_exp = jQuery(this).data("rec_exp");
	var booking_id = jQuery(this).data("booking_id");
	jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {"rec_exp":rec_exp,"booking_id":booking_id,"action":"remove_rec_exp"},  
        success: function(data){
           window.location.reload();
        }
    });
})    
jQuery(document).on('click', function (e) {
    if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".search-box-inner").length === 0 && jQuery(e.target).closest(".dropdown-content").length === 0) {
        jQuery(".dropdown-content").removeClass("show");
    }
});
jQuery(document).on("click",".search-box-inner span,.search-box-inner button,.search-box-inner i",function(){
	var parent_div = jQuery(this).parent();
	jQuery(".search-box-inner").not(parent_div).find(".dropdown-content").removeClass("show");
	jQuery(this).parent().find(".dropdown-content").toggleClass("show");
})
jQuery(".daterange_pick").find("span").click(function(){
	jQuery(this).parent().find("#booking-date-range-enabler2").click();
})
jQuery(".savenote").click(function(){

	var formdata = jQuery(".savenoteform").serialize();

   
    jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: formdata,
        success: function(data){
           window.location.reload();
        }
    });
})
    
jQuery(".nav-tabs li a").on("click",function(){
	var _url = location.href;
	_url = _url.replace("&tab=message","");
	_url = _url.replace("&tab=related_booking","");
	_url = _url.replace("&tab=details","");
	_url = _url.replace("&tab=aktivitetslogg","");
	_url = _url.replace("&tab=adgangslogg","");

	var  param = "";

    if(jQuery(this).attr("data-link") == "related_booking"){
    	
    	
    	 param = "&tab=related_booking";
	    

    }else if(jQuery(this).attr("data-link") == "message"){
    	
    	
    	 param = "&tab=message";

    }else if(jQuery(this).attr("data-link") == "details"){
    	
    	
    	 param = "&tab=details";

    }else if(jQuery(this).attr("data-link") == "aktivitetslogg"){
    	
    	
		param = "&tab=aktivitetslogg";

   }else if(jQuery(this).attr("data-link") == "adgangslogg"){
    	
    	
		param = "&tab=adgangslogg";

   }

    _url = _url + param;

	window.history.pushState("", "", _url);
})

function change_status_first_event(booking_id,status){
	let ajax_data2 = {
            'action': 'change_status_first_event',
            'booking_id' : booking_id,
            'status' : status,
            //'nonce': nonce
        };
	jQuery.ajax({
            type: 'POST', 
            dataType: 'json',
            url: "<?php echo admin_url( 'admin-ajax.php' );?>",
            data: ajax_data2,
            success: function(data){
                window.location.reload();
                //change_status_first_event();
            }
    });

}

</script>