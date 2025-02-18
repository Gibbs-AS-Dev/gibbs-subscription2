<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kvittering</title>
	<?php 
	function dequeue_my_css() {
		//die;
		wp_dequeue_style('bootstrap');
		wp_deregister_style('bootstrap');

		wp_dequeue_style('application_form_common-style');
		wp_deregister_style('application_form_common-style');

		wp_dequeue_style('user_management-style');
		wp_deregister_style('user_management-style');

		wp_dequeue_style('rules-style');
		wp_deregister_style('rules-style');

		
	}
	add_action('wp_enqueue_scripts','dequeue_my_css',200);wp_head(); ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">
    	/** Define the margins of your page **/
		@page {
		   margin: 5px 0px;
		}

		@media (min-width: 600px) {
  .kamils_container {
    padding: 10px 20px;

  }
}

.inner-tb, h2, .reservation_table td {
    color: black;
}

.table thead th {
    
    background-color: #F6F8FA!important;
    color: black !important;
}

.table tr:nth-child(even) {
    background-color: #F6F8FA!important;
}

h4{
	display: none;
}

.table td, .table th {
    border: 0.5px solid #ddd !important;
    
}

.first_divv .inner-tb:not(:nth-child(4n + 4)):not(:nth-child(4n + 3)) {
    border: 0.5px solid #ddd !important;
	background-color: #F6F8FA!important;
}


		html {
			background: #F6F8FA;
		}
		body {
			background: #fff;
			width: 1190px;
			max-width: 100%;
			margin: auto;
			border-radius: 10px;
			}

		header {
		   
		    height: 70px;
		    padding: 30px;
		    font-size: 20px !important;
		    color: white;
		    display: flex;
  			align-items: center;
 			 justify-content: center;
		    line-height: 35px;
			background: #F6F8FA;
		}
		header img{
		    height : 70px;
		}
		table-danger, .table-danger>td, .table-danger>th {
		    background-color: #AA4513;
		}
		table {
		    width: 100%;
		    text-align: left;
		    padding: 13px;
		}
		.application{
			 page-break-before: always;
		}
		.table-danger tbody+tbody, .table-danger td, .table-danger th, .table-danger thead th {
		    border-color: #cf7f59;
		}
		.table thead th {
		    color: #fff;
		}
		.reservation_table th {
		    border-top: 0px !important;
		}
		.table {
		  font-family: Arial, Helvetica, sans-serif;
		  border-collapse: collapse;
		  width: 100%;
		}

		/* .table td, .table th {
		  border: 1px solid #ddd;
		  padding: 8px;
		} */

		.table tr:nth-child(even){background-color: #f2f2f2;}

		.first_divv .inner-tb:not(:nth-child(4n + 4)):not(:nth-child(4n + 3)) {
			background-color: #f2f2f2;
		}

		/* .table tr:hover {background-color: #ddd;} */

		.table th {
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: left;
			background-color: #008e7f;
			color: white;
			padding-left: 10px;
		}
		.reservation_table tr {
		    width: 100%;
		}
		td.first_divv {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
		}
		.inner-tb {
			width: 50%;
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			border: 1px solid #ddd;
			box-sizing: border-box;
		}
		.inner-tb .first {
			width: 45%;
			display: flex;
			border-right: 1px solid #ddd;
			box-sizing: border-box;
		}
		.inner-tb span {
			padding: 10px;
			width: 45%;
		}
		.reservation_table td {
			padding: 10px;
		}
		.icc {
			display: flex;
			gap: 9px;
			padding: 9px;
		}
		.td_div {
			height: 87px;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		
		
    </style>
	
</head>
<body>

<?php 

$pdf_data = $json_data;

$pdf_data["phone"] = $pdf_data["country_code"].$pdf_data["phone"];

unset($pdf_data["action"]);
unset($pdf_data["application_id"]);
unset($pdf_data["group_id"]);
unset($pdf_data["season_id"]);
unset($pdf_data["redirect"]);
unset($pdf_data["redirect_draft"]);
unset($pdf_data["full"]);
unset($pdf_data["applicant_type"]);
unset($pdf_data["term_and_condition"]);
unset($pdf_data["country_code"]);
//echo "<pre>"; print_r($pdf_data); die;
?>	
    <!-- Define header and footer blocks before your content -->
    <header>
        <img src="<?php echo plugin_dir_url(__FILE__);?>/pdfTemplate/Frame-966.png">
    </header>
   <!--  <footer>
        Copyright © <?php //echo date("Y");?> 
    </footer> -->

    
    <div class="container kamils_container" style="
	background: white;
    border-radius: 10px;">
    	<?php
    	if(isset($pdftext["title"])){ ?>
	    	<div class="top-text">
	    		<h2><?php echo $pdftext["title"];?></h2>
	    		<?php if(isset($pdftext["description"]) && $pdftext["description"] != ""){ ?>
	    		   <p style="padding-bottom: 10px;"><?php echo $pdftext["description"];?></p>
	    		<?php } ?>   
	    	</div>
	    <?php } ?>
        <table class="table table-bordered1 mb-5">
            <thead>
                <!-- <tr class="table-danger">
                    <th scope="col">ID</th>
                    <th scope="col">QR Code ID</th>
                    <th scope="col">QR Code</th>
                </tr> -->
            </thead>
            <tbody>

			    <tr>
					<td class="first_divv">
						<?php foreach ($pdf_data as $key_data => $data) { 
							if(!is_array($data)){
							$label = str_replace("_", " ", $key_data);
							$label = ucfirst($label);
							$value = $data
							?>
								<div class="inner-tb">
									<span class="first"><b><?php echo  __($label,'gibbs_core_'.$form_name);?>:</b></span>
									<span><?php echo $value;?></span>
							    </div>
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
                
                
            </tbody>
        </table>

        <?php 

        global $wpdb;

		$table = 'applications';

	    $data_applications = $wpdb->get_results("SELECT * FROM $table WHERE application_data_id = ".$_GET["application_id"]." ORDER BY `id` ASC");


        if(isset($pdf_data["application"]) && !empty($pdf_data["application"])){ 

			$inc_app = 0;

        	foreach ($pdf_data["application"] as $key_app => $application_dd) { 
				    
				    $app_id = $data_applications[$inc_app]->id;

					//echo $app_id; die;

        		

		            	?>
		            <div class="application">	
			            <h2> Søknad #<?php echo $key_app;?></h2>	

				        <table class="table table-bordered1 mb-5">
				            <thead>
				                <!-- <tr class="table-danger">
				                    <th scope="col">ID</th>
				                    <th scope="col">QR Code ID</th>
				                    <th scope="col">QR Code</th>
				                </tr> -->
				            </thead>
				            <tbody>

							    <tr>
					                <td class="first_divv">

				            	

						            	<?php 

						            	    foreach ($application_dd as $key_app_data => $application) { 



							            		if(!is_array($application)){

							            			if($key_app_data == "age"){

							            				if($user_form == "2"){
                                                             continue;
							            				}else{

							            					if(array_key_exists($application, $get_age)){
		                                                       $application = $get_age[$application];
								            				}else{
		                                                       continue;
								            				}

							            				}

							            				

							            			}
													if($form_name == "form-2" && $key_app_data == "comments"){
                                                       continue;
													}
							            			if($key_app_data == "level"){

							            				if(array_key_exists($application, $get_levels)){
	                                                       $application = $get_levels[$application];
							            				}else{
	                                                       continue;
							            				}

							            			}
							            			if($key_app_data == "sports"){

							            				if(array_key_exists($application, $get_sports)){
	                                                       $application = $get_sports[$application];
							            				}else{
	                                                       continue;
							            				}

							            			}
							            			if($key_app_data == "pri-1"){

							            				if(array_key_exists($application, $get_locations_data)){
	                                                       $application = $get_locations_data[$application];
							            				}else{
	                                                       continue;
							            				}

							            			}
							            			if($key_app_data == "pri-2"){

							            				if(array_key_exists($application, $get_locations_data)){
	                                                       $application = $get_locations_data[$application];
							            				}else{
	                                                       continue;
							            				}

							            			}
							            			if($key_app_data == "pri-3"){

							            				if(array_key_exists($application, $get_locations_data)){
	                                                       $application = $get_locations_data[$application];
							            				}else{
	                                                       continue;
							            				}

							            			}

							                       $label = str_replace("_", " ", $key_app_data);
							                       $label = ucfirst($label);
							                       $value = strip_tags($application);
					            		?>
								            		<div class="inner-tb">
									                    <span class="first"><b><?php echo  __($label,'gibbs_core_'.$form_name);?>:</b></span>
									                    <span style="word-wrap: break-word;"><?php echo $value;?></span>
									                </div>
									            <?php }elseif($key_app_data == "reservations"){
											    	
											    } ?>  
											    <?php if($key_app_data == "application_fields"){

											    	    foreach ($application as $key_app => $app) {
											    	    	
										                       $label = str_replace("_", " ", $key_app);
										                       $label = ucfirst($label);
										                       $value = $app;
									            		?>
											            		<div class="inner-tb">
									                                <span class="first"><b><?php echo  __($label,'gibbs_core_'.$form_name);?>:</b></span>
												                    <span><?php echo $value;?></span>
												                </div>
											    	    <?php }

							            			}
							            		?>	
									    <?php } ?>      
									</td>
				                </tr>
								<?php require("reservation-app.php");?>
				            </tbody>
				        </table>
				    </div>
            <?php $inc_app++; } ?>
        <?php } ?>   
    </div>
    
</body>
</html>
<?php //die; ?>