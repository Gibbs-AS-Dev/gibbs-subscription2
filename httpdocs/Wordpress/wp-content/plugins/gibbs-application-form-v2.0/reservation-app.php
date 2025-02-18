<?php 
   // $app_id = 1390;
	$table_bookings_calendar_raw = $wpdb->prefix.'bookings_calendar_raw';
	$table_bookings_calendar_raw_approved = $wpdb->prefix.'bookings_calendar_raw_approved';
	$table_posts = $wpdb->prefix.'posts';

	$approved_quaries = "";

	$data_bookings_calendar_raw = $wpdb->get_results("SELECT *, GROUP_CONCAT((select post_title from  ptn_posts where ID = listing_id)) AS listings, GROUP_CONCAT((select ID from  ptn_posts where ID = listing_id)) AS listing_ids FROM $table_bookings_calendar_raw WHERE application_id = ".$app_id." GROUP BY first_event_id ORDER BY `id` ASC",ARRAY_A);

	$data_bookingss = array();

	if($form_name == "form-1"){
		$date_format = "l";
	 }else{
		 $date_format = "d.m.Y";
	 }

	 

	foreach ($data_bookings_calendar_raw as $key => $data_bookings_calendar_dddd) {

		$data_bookings_calendar_raw_inner = $wpdb->get_results("SELECT a.*, b.post_title as listing_name FROM $table_bookings_calendar_raw as a left join $table_posts as b ON a.listing_id = b.ID  WHERE a.first_event_id = ".$data_bookings_calendar_dddd["first_event_id"]." ORDER BY a.`id` ASC",ARRAY_A);

		$k_inc = 0;

		$bk_data = array();

		foreach ($data_bookings_calendar_raw_inner as $key2 => $data_bookings_calendar) {
        

			$raw_date = date($date_format,strtotime($data_bookings_calendar["date_start"]))." - ".date($date_format,strtotime($data_bookings_calendar["date_end"]));
			$raw_date =  __($raw_date,'gibbs_core_'.$form_name);
			$bk_data[$k_inc]["raw_date"] = $raw_date."<br>".date("H:i",strtotime($data_bookings_calendar["date_start"]))." - ".date("H:i",strtotime($data_bookings_calendar["date_end"]));
			$bk_data[$k_inc]["raw_listing"] = $data_bookings_calendar["listing_name"];


			$bk_data[$k_inc]["raw_rejected"] = false;

			if($data_bookings_calendar["rejected"] == "1"){
				$bk_data[$k_inc]["raw_rejected"] = true;
			}

			$bk_data[$k_inc]["comment"] = "";

			$bk_data[$k_inc]["approved_rejected"] = false;

			$bk_data[$k_inc]["approved_date"] = "";
			$bk_data[$k_inc]["approved_listing"] = "";
			$bk_data[$k_inc]["id"] = $data_bookings_calendar["id"];

			// $sqll = "SELECT *, GROUP_CONCAT((select post_title from  ptn_posts where ID = listing_id)) AS listings FROM $table_bookings_calendar_raw_approved WHERE first_event_id = ".$data_bookings_calendar["id"]." GROUP BY first_event_id ORDER BY `id` ASC";

			$sqll = "SELECT a.*, b.post_title as listing_name FROM $table_bookings_calendar_raw_approved as a left join $table_posts as b ON a.listing_id = b.ID   WHERE a.id = ".$data_bookings_calendar["id"];

			$data_bookings_calendar_raw_approved = $wpdb->get_results($sqll,ARRAY_A);

			
			

			
			
			if(!empty($data_bookings_calendar_raw_approved)){

			   

				$commets = [];

				foreach ($data_bookings_calendar_raw_approved as $key2 => $data_bookings_calendar_raw_app) {
				 
					$approved_date = date($date_format,strtotime($data_bookings_calendar_raw_app["date_start"]))." - ".date($date_format,strtotime($data_bookings_calendar_raw_app["date_end"]));
					$approved_date =  __($approved_date,'gibbs_core_'.$form_name);
					$bk_data[$k_inc]["approved_date"] = $approved_date."<br>".date("H:i",strtotime($data_bookings_calendar_raw_app["date_start"]))." - ".date("H:i",strtotime($data_bookings_calendar_raw_app["date_end"]));
					// $data_bookingss[$key]["approved_listings"] = str_replace(",",",<br>",$data_bookings_calendar_raw_app["listings"]);
					if($data_bookings_calendar_raw_app["rejected"] == "1"){
						$bk_data[$k_inc]["approved_rejected"] = true;
					}

					

					$bk_data[$k_inc]["approved_listing"] = $data_bookings_calendar_raw_app["listing_name"];


					$changed_approved = false;

					if(date("d-m-Y",strtotime($data_bookings_calendar["date_start"])) != date("d-m-Y",strtotime($data_bookings_calendar_raw_app["date_start"])) || date("d-m-Y",strtotime($data_bookings_calendar["date_end"])) != date("d-m-Y",strtotime($data_bookings_calendar_raw_app["date_end"]))){
						$commets[]= "Endret dato";
					    $changed_approved = true;

					}
					if(date("H:i",strtotime($data_bookings_calendar["date_start"])) != date("H:i",strtotime($data_bookings_calendar_raw_app["date_start"])) || date("H:i",strtotime($data_bookings_calendar["date_end"])) != date("H:i",strtotime($data_bookings_calendar_raw_app["date_end"]))){
						$commets[]= "Endret tid";
						$changed_approved = true;
					}

					//echo "<pre>"; print_r($listing_diffrent);echo "</pre>";
					if($data_bookings_calendar["listing_id"] != $data_bookings_calendar_raw_app["listing_id"]){
						$commets[]= "Endret rom";
						$changed_approved = true;
					}

					if($changed_approved == false && $data_bookings_calendar_raw_app["rejected"] == 0 ){
						$commets[]= "Godkjent";
					}
					if($data_bookings_calendar_raw_app["rejected"] == 1 ){
						$commets[]= "Avslått";
						//echo "<pre>111"; print_r($data_bookings_calendar_raw_app); die;
					}
					// if($data_bookings_calendar["id"] == "8410"){
					// 	echo "<pre>"; print_r($data_bookings_calendar);
					//     echo "<pre>"; print_r($data_bookings_calendar_raw_app);echo "</pre>"; die;
					// }
			
				}

				$commets = array_unique($commets);
				

				$bk_data[$k_inc]["comment"] = implode(" ",$commets);

			}
			$k_inc++;
			//echo "<pre>"; print_r($bk_data);echo "</pre>";
			//echo "<pre>"; print_r($data_bookings_calendar_raw_approved);echo "</pre>"; die;
		}	

		$raw_date = [];
		$raw_listing = [];
		$raw_rejected = [];
		$comment = [];
		$approved_rejected = [];
		$approved_date = [];
		$approved_listing = [];
		$id = [];

        $incc = 1;

		foreach ($bk_data as $key_bk => $bk_d) {
			$raw_date[$key_bk] = "<div class='td_div'>".$incc.".) ".(($bk_d["raw_rejected"] == 1)?"Avslått":$bk_d["raw_date"])."</div>";
			$raw_listing[$key_bk] = "<div class='td_div'>".$incc.".) ".$bk_d["raw_listing"]."</div>";
			$raw_rejected[$key_bk] = "<div class='td_div'>".$incc.".) ".$bk_d["raw_rejected"]."</div>";
			$comment[$key_bk] = "<div class='td_div'>"."<div class='icc'>".$incc.".) <span>".$bk_d["comment"]."</span></div>"."</div>";
			$approved_rejected[$key_bk] = "<div class='td_div'>".$incc.".) ".$bk_d["approved_rejected"]."</div>";
			$approved_date[$key_bk] = "<div class='td_div'>".$incc.".) ".(($bk_d["approved_rejected"] == 1)?"Avslått":$bk_d["approved_date"])."</div>";
			$approved_listing[$key_bk] = "<div class='td_div'>".$incc.".) ".$bk_d["approved_listing"]."</div>";
			$id[$key_bk] = $bk_d["id"];
			$incc++;
		}

		$data_bookingss[$key]["raw_date"] = implode("",$raw_date);
		$data_bookingss[$key]["raw_listing"] = implode("",$raw_listing);
		$data_bookingss[$key]["raw_rejected"] = implode("",$raw_rejected);
		$data_bookingss[$key]["comment"] = implode(" ",$comment);
		$data_bookingss[$key]["approved_rejected"] = implode("",$approved_rejected);
		$data_bookingss[$key]["approved_date"] = implode("",$approved_date);
		$data_bookingss[$key]["approved_listing"] = implode("",$approved_listing);
		$data_bookingss[$key]["id"] = implode("<br>",$id);

		//echo "<pre>"; print_r($data_bookingss);echo "</pre>"; die;
		// if($data_bookings_calendar["id"] == "8407"){
		// 	echo "<pre>"; print_r($data_bookingss); die;
		// }
	}
	//echo "<pre>"; print_r($data_bookingss);echo "</pre>"; die;
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
									        		
									                    <td><?php echo $reservation["raw_date"];?></td>
														<td><?php echo $reservation["raw_listing"];?></td>
														<td><?php echo $reservation["approved_date"];?></td>
														<td><?php echo $reservation["approved_listing"];?></td>
														<td><?php echo $reservation["comment"];?></td>
													</tr>
									        <?php } ?> 
								        
								        
								     </tbody>    
				       
			      
		            </table>
	        </td> 
        </tr>     
    <?php } ?>