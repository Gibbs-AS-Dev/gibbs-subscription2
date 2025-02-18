<?php 
   // $app_id = 1390;
	$table_bookings_calendar_raw = $wpdb->prefix.'bookings_calendar_raw';
	$table_bookings_calendar_raw_approved = $wpdb->prefix.'bookings_calendar_raw_approved';

	$approved_quaries = "";

	$data_bookings_calendar_raw = $wpdb->get_results("SELECT *, GROUP_CONCAT((select post_title from  ptn_posts where ID = listing_id)) AS listings, GROUP_CONCAT((select ID from  ptn_posts where ID = listing_id)) AS listing_ids FROM $table_bookings_calendar_raw WHERE application_id = ".$app_id." GROUP BY first_event_id ORDER BY `id` ASC",ARRAY_A);

	$data_bookingss = array();

	if($form_name == "form-1"){
		$date_format = "l";
	 }else{
		 $date_format = "d.m.Y";
	 }

	 echo "<pre>"; print_r($data_bookings_calendar_raw);echo "</pre>"; die;

	foreach ($data_bookings_calendar_raw as $key => $data_bookings_calendar) {
		$raw_date = date($date_format,strtotime($data_bookings_calendar["date_start"]))." - ".date($date_format,strtotime($data_bookings_calendar["date_end"]));
		$raw_date =  __($raw_date,'gibbs_core_'.$form_name);
		$data_bookingss[$key]["raw_date"] = $raw_date."<br>".date("H:i",strtotime($data_bookings_calendar["date_start"]))." - ".date("H:i",strtotime($data_bookings_calendar["date_end"]));
		$data_bookingss[$key]["raw_listings"] = str_replace(",",",<br>",$data_bookings_calendar["listings"]);

		$raw_listings = explode(",",$data_bookings_calendar["listings"]);


		$data_bookingss[$key]["raw_rejected"] = false;

		if($data_bookings_calendar["rejected"] == "1"){
			$data_bookingss[$key]["raw_rejected"] = true;
		}

		$data_bookingss[$key]["comment"] = "";

		$data_bookingss[$key]["approved_rejected"] = false;

		$data_bookingss[$key]["approved_date"] = "";
		$data_bookingss[$key]["approved_listings"] = "";
		$data_bookingss[$key]["id"] = $data_bookings_calendar["id"];

		// $sqll = "SELECT *, GROUP_CONCAT((select post_title from  ptn_posts where ID = listing_id)) AS listings FROM $table_bookings_calendar_raw_approved WHERE first_event_id = ".$data_bookings_calendar["id"]." GROUP BY first_event_id ORDER BY `id` ASC";

		$sqll = "SELECT * FROM $table_bookings_calendar_raw_approved WHERE first_event_id = ".$data_bookings_calendar["id"];

		$data_bookings_calendar_raw_approved = $wpdb->get_results($sqll,ARRAY_A);
		
		if(!empty($data_bookings_calendar_raw_approved)){

			$commets = [];

			foreach ($data_bookings_calendar_raw_approved as $key2 => $data_bookings_calendar_raw_app) {
				$approved_date = date($date_format,strtotime($data_bookings_calendar_raw_app["date_start"]))." - ".date($date_format,strtotime($data_bookings_calendar_raw_app["date_end"]));
				$approved_date =  __($approved_date,'gibbs_core_'.$form_name);
				$data_bookingss[$key]["approved_date"] = $approved_date."<br>".date("H:i",strtotime($data_bookings_calendar_raw_app["date_start"]))." - ".date("H:i",strtotime($data_bookings_calendar_raw_app["date_end"]));
				// $data_bookingss[$key]["approved_listings"] = str_replace(",",",<br>",$data_bookings_calendar_raw_app["listings"]);
				if($data_bookings_calendar_raw_app["rejected"] == "1"){
					$data_bookingss[$key]["approved_rejected"] = true;
				}

				

				$sqll2 = "SELECT *, GROUP_CONCAT((select post_title from  ptn_posts where ID = listing_id)) AS listings, GROUP_CONCAT((select ID from  ptn_posts where ID = listing_id)) AS listing_ids FROM $table_bookings_calendar_raw_approved WHERE first_event_id = ".$data_bookings_calendar_raw_app["first_event_id"]." GROUP BY first_event_id ORDER BY `id` ASC";
				$data_bookings_calendar_raw_approved_listings = $wpdb->get_results($sqll2,ARRAY_A);
				//echo "<pre>"; print_r($data_bookings_calendar_raw_approved_listings);echo "</pre>";
				foreach ($data_bookings_calendar_raw_approved_listings as $data_bookings_calendar_raw_approved_listing) {
					$data_bookingss[$key]["approved_listings"] = str_replace(",",",<br>",$data_bookings_calendar_raw_approved_listing["listings"]);
			
				}

				$approved_listings = explode(",",$data_bookings_calendar_raw_approved_listing["listings"]);

				$changed_approved = false;

				if(date("d-m-Y",strtotime($data_bookings_calendar["date_start"])) != date("d-m-Y",strtotime($data_bookings_calendar_raw_app["date_start"])) || date("d-m-Y",strtotime($data_bookings_calendar["date_end"])) != date("d-m-Y",strtotime($data_bookings_calendar_raw_app["date_end"]))){
					$commets[]= "<div class='icc'></i><span>Endret dato</span></div>";
				$changed_approved = true;

				}
				if(date("H:i",strtotime($data_bookings_calendar["date_start"])) != date("H:i",strtotime($data_bookings_calendar_raw_app["date_start"])) || date("H:i",strtotime($data_bookings_calendar["date_end"])) != date("H:i",strtotime($data_bookings_calendar_raw_app["date_end"]))){
					$commets[]= "<div class='icc'></i><span>Endret tid</span></div>";
					$changed_approved = true;
				}

				$listing_diffrent = array_diff($raw_listings,$approved_listings);
				//echo "<pre>"; print_r($listing_diffrent);echo "</pre>";
				if(!empty($listing_diffrent)){
					$commets[]= "<div class='icc'><span>Endret rom</span></div>";
					$changed_approved = true;
				}

				if($changed_approved == false && $data_bookings_calendar_raw_app["rejected"] == 0 ){
					$commets[]= "<div class='icc'></i><span>Godkjent</span></div>";
				}
				if($data_bookings_calendar_raw_app["rejected"] == 1 ){
					$commets[]= "<div class='icc'><span>Avslått</span></div>";
					//echo "<pre>111"; print_r($data_bookings_calendar_raw_app); die;
				}
		
			}

			$commets = array_unique($commets);

			$data_bookingss[$key]["comment"] = implode(", ",$commets);

		}
		// if($data_bookings_calendar["id"] == "8407"){
		// 	echo "<pre>"; print_r($data_bookingss); die;
		// }
	}
	
    if(!empty($data_bookingss)){  
		
		
		?>

    	<tr>
    		<td scope="row"style="width: 20%" colspan="2"><h4 style="padding-left: 10px"><?php echo __("Reservations","gibbs_core_".$form_name);?></h4></td>
    	</tr>

	    <tr style="width: 100%">
	        <td colspan="2" scope="row" style="width: 100%">

	        	<table class="table reservation_table" style="width: 100%; ">

	        		   

					

						            <?php //if($key_ress == 0){ ?>
						            	<thead>

						                	<tr>
											   <th><?php echo  __("Søkt tid",'gibbs_core_'.$form_name);?></th>
											   <th><?php echo  __("Søkt rom",'gibbs_core_'.$form_name);?></th>
											   <th><?php echo  __("Tildelt tid",'gibbs_core_'.$form_name);?></th>
											   <th><?php echo  __("Tildelt rom",'gibbs_core_'.$form_name);?></th>
											   <th><?php echo  __("Algoritme",'gibbs_core_'.$form_name);?></th>
						                	</tr>
					                	</thead>

					                <?php //} ?>

					                <tbody>

					                    
									        <?php

										    foreach ($data_bookingss as $key_reservation => $reservation) { 
												//echo "<pre>"; print_r($reservation); die;

										    		?>
													<tr>
									        		
									                    <td><?php echo ($reservation["raw_rejected"])?'Avslått':$reservation["raw_date"];?></td>
														<td><?php echo $reservation["raw_listings"];?></td>
														<td><?php echo ($reservation["approved_rejected"])?'Avslått':$reservation["approved_date"];?></td>
														<td><?php echo $reservation["approved_listings"];?></td>
														<td><?php echo $reservation["comment"];?></td>
													</tr>
									        <?php } ?> 
								        
								        
								     </tbody>    
				       
			      
		            </table>
	        </td> 
        </tr>     
    <?php } ?>