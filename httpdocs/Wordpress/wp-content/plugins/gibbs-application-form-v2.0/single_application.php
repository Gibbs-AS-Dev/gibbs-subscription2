<div class="single_application fade-sec">
	<?php foreach ($application_fields as $key_application => $application) { ?>	

			<div class="row sections">
				<div class="single_application_title d-flex justify-content-between">
				   <h2><?php echo $application["title"];?></h2>
				   <button class="delete_application ">   <i class="fa fa-trash delete_application"></i> Slett søknad</button>
				 
				</div>
				<hr />
			</div>

			<?php if(isset($application["fields"]["rows"])){


					foreach ($application["fields"]["rows"] as $key_app => $rows) { ?>

						<div class="row">

						    <?php	
						        foreach ($rows as $key_row => $row) { 

						        	if($key_row == "aditional_rows"){
						        		if(!empty($row)){
										 	app_fields_func($row);
										}
						        	}

								 	if($row['type'] == "text"){ 

								 		include("fields/text.php");

								    }elseif($row['type'] == "date"){ 

								 		include("fields/date.php");

								    }elseif($row['type'] == "textarea"){ 

								 		include("fields/textarea.php");

								    }elseif($row['type'] == "tel"){ 

								 		include("fields/tel.php");

								    }else if($row['type'] == "email"){ 

								    	include("fields/email.php");

								    }else if($row['type'] == "number"){ 

								    	include("fields/number.php");

								    }else if($row['type'] == "select"){ 

								    	include("fields/select.php");

								    }else if($row['type'] == "custom_text"){ 

								    	include("fields/custom_text.php");

								    }else if($row['type'] == "get_day"){ 

								    	include("fields/get_day.php");

								    }else if($row['type'] == "add_reservation"){ 

								    	include("fields/add_reservation.php");

								    } else if($row['type'] == "add_reservation"){ 

								    	include("fields/add_reservation.php");

								    } 


							    } 
							?>	 

						</div> 
						
				<?php } ?>	
			<?php } 
			//echo "<pre>"; print_r($application["fields"]); die;

			?>
			<?php if(isset($application["fields"]["aditional_rows"])){
                /*if(!empty($application["fields"]["aditional_rows"])){
                	echo "<pre>"; print_r($application["fields"]["aditional_rows"]); die;
					about_fields_func($application["fields"]["aditional_rows"]);
			    }*/

			} ?>	
	<?php } ?>			
</div>