<?php

$login_section = array(
			'top_text' => array(
				'title' 	=> __('Send søknad','gibbs_core'),
				'class' 	=> '',
				'icon' 		=> 'sl sl-icon-doc',
				/*fields*/
				'fields' 	=> array(
					/*row start */
						'rows'  => array(
							        array(
									    'custom_text' => array(
											'label'       => __( 'Du må være inlogget for å sende søknad', 'gibbs_core' ),
											'type'        => 'custom_text',
											'class'		  => 'col-md-12',
											
										),
									),array(
										'custom_button' => array(
											'label'       => __( 'Logg inn', 'gibbs_core' ),
											'type'        => 'custom_button',
											'class'		  => 'col-md-12 login_btn',
											'link'		  => home_url()."/login",
										),
									)
							)					
					/*row end */	

				)
				/*end fields*/
			),
		);
?>
<div class="main-form-div fade-sec">
	<?php foreach ($login_section as $key_section => $top_section) { ?>	

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

<script type="text/javascript">
	jQuery(".login_btn button").on("click",function(e){
		e.preventDefault();
        jQuery(".xoo-el-login-tgr").click();
	});
</script>