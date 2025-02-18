<?php

$success_section = success_section();
?>
<div class="main-form-div fade-sec">
	<?php foreach ($success_section as $key_section => $top_section) { ?>	

		<div class="row">
			<h2><?php echo $top_section["title"];?></h2>
			<hr />
		</div>

		<?php if(isset($top_section["fields"]["rows"])){

			    $columns = count($top_section["fields"]["rows"]) / 12;

				foreach ($top_section["fields"]["rows"] as  $rows) { ?>

					<div class="row">

					    <?php	
					        foreach ($rows as $key_row => $row) { 

							 	if($row['type'] == "text"){ 

							 		include("fields/text.php");

							    }elseif($row['type'] == "tel"){ 

							 		include("fields/tel.php");

							    }else if($row['type'] == "email"){ 

							    	include("fields/email.php");

							    }else if($row['type'] == "select"){ 

							    	include("fields/select.php");

							    } else if($row['type'] == "custom_text"){ 

								    	include("fields/custom_text.php");

								} else if($row['type'] == "custom_button"){ 

								    	include("fields/custom_button.php");

								}


						    } 
						?>	 

					</div> 


					
			<?php } ?>	
		<?php } ?>	
		

	<?php } ?>

</div>