<?php 
    if(!empty($application)){   ?>

    	<tr>
    		<td scope="row"style="width: 20%" colspan="2"><h4><?php echo __("Reservations","gibbs_core");?></h4></td>
    	</tr>

	    <tr style="width: 100%">
	        <td colspan="2" scope="row" style="width: 100%">

	        	<table class="table reservation_table" style="width: 100%">

	        		   

					<?php   foreach ($application as $key_ress => $reservation_data) { 

						//echo "<pre>"; print_r($reservation_data); die;

					 ?>

						            <?php //if($key_ress == 0){ ?>
						            	<thead>

						                	<tr>
						                		<?php foreach ($reservation_data as $key_reservation => $reservation) { 
					                                  
										               $label = str_replace("_", " ", $key_reservation);
										               $label = ucfirst($label);
										                if($key_reservation == "custom_fields"){

								            				 continue;

								            			}
						                			?>
							                		<th><?php echo  __($label,'gibbs_core');?></th>
							                	<?php } ?>	
						                	</tr>
					                	</thead>

					                <?php //} ?>

					                <tbody>

					                    <tr>
									        <?php

										    	foreach ($reservation_data as $key_reservation => $reservation) { 

										    		if($key_reservation == "location"){

										    			$locations = explode(",", $reservation);

										    			$loc_data = array();

										    			foreach ($locations as $key => $loc) {
										    				if(array_key_exists($loc, $get_locations_data)){
		                                                       $loc_data[] = $get_locations_data[$loc];
								            				}
										    			}

										    			if(!empty($loc_data)){

										    				$reservation = implode(", ",$loc_data);

										    			}else{
	                                                       $reservation = "-";
							            				}

							            			}
							            			if($key_reservation == "day"){

							            				if(array_key_exists($reservation, $get_days)){
	                                                       $reservation = $get_days[$reservation];
							            				}else{
	                                                       $reservation = "-";
							            				}

							            			}
							            			if($key_reservation == "custom_fields"){

							            				 continue;

							            			}
							            			if($key_reservation == "sub-location"){

							            				$sub_loc_array = explode(",", $reservation);

							            				$location_sub = array();

							            				if(!empty($sub_loc_array)){
							            					foreach ($sub_loc_array as  $sub_loc) {
							            						$location_sub[] = get_sub_location_name($sub_loc);
							            					}
							            				}


							            				$reservation = implode(", ",$location_sub);

							            			}

											   ?>
									        		
									                    <td><?php echo $reservation;?></td>
									                
									        <?php } ?> 
								        </tr>
								        
									        <?php

										    	foreach ($reservation_data as $key_reservation_field => $reservation_field) { 

										    		

										    		
							            			if($key_reservation_field == "custom_fields"){



							            				if(!empty($reservation_field)){
							            					foreach ($reservation_field as $reserv_data) {
							            						foreach ($reserv_data as $cs_field => $reserv) { ?>
							            							<tr >
									            						<td colspan="7"><b><?php echo $cs_field;?></b> : <?php echo $reserv;?></td>
									            					</tr>
								            					<?php }
							            					}
							            				}

							            			}else{
							            				 continue;
							            			}
							            			

											   ?>
									        		
									                    
									                
									        <?php } ?> 
								     </tbody>    
							          
				        <?php } ?> 
				       
			      
		            </table>
	        </td> 
        </tr>     
    <?php } ?>