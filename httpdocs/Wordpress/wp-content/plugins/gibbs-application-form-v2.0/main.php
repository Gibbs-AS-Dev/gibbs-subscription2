<?php
global $wp;
$top_sections = top_section();
$add_button_section = add_button_section();
$bottom_sections = bottom_section();

global $season_id; 


$seasons_table = 'seasons';  // table name
$sql_season = "select * from `$seasons_table` where id = ".$_GET['season_id'];
$season_data = $wpdb->get_row($sql_season);

$start_date = $season_data->season_start;
$end_date = $season_data->season_end;
//echo "<pre>"; print_r($json_data); die;
?>
<?php if($season_active_edit == false){ ?>
<div class="row">
	<div class="col-md-12 mt-10">
		<div class="alert alert-danger" role="alert">
		   <i class="far fa-info-circle"></i> Sesongen er inaktiv eller fristen har gått ut. Dine endringer vil ikke bli lagret.
		</div>
	</div>
</div>
<?php } ?>
<div class="main-form-div" style="display: none">
	<form method="post" class="application_form_new" action="<?php echo home_url();?>/wp-admin/admin-ajax.php">
		<?php if(isset($_GET["application_id"])){ ?>
		     <input type="hidden" name="action" value="application_update">
		     <input type="hidden" name="application_id" value="<?php echo $_GET["application_id"];?>">
		<?php }else{ ?>
             <input type="hidden" name="action" value="application_submit">
		<?php }  ?>
		<input type="hidden" name="group_id" value="<?php echo $_GET['group_id'];?>">
		<input type="hidden" name="season_id" value="<?php echo $_GET['season_id'];?>">
		<input type="hidden" name="redirect" value="<?php echo home_url( $wp->request );?>">
		<input type="hidden" name="start_date" value="<?php echo $start_date;?>">
		<input type="hidden" name="end_date" value="<?php echo $end_date;?>">

		<?php if(isset($_GET["admin"]) && $_GET["admin"] == "true"){ ?>
            <input type="hidden" name="from_admin" value="true">
        <?php } ?>
		<?php foreach ($top_sections as $key_section => $top_section) { ?>	


			<div class="main-section-div fade-sec">

				<div class="row">
					<div class="top_section_title d-flex justify-content-between">
					    <h2><?php echo $top_section["title"];?></h2>
					    <div class="save_as_draft_title_main">
					    	<?php if($season_active_edit != false){ ?>
						    	<?php if(isset($_GET["application_id"])){ 
                                     if(isset($_GET["admin"]) && $_GET["admin"] == "true"){
                                     	$delete_redirect = $top_section["delete_form"]["redirect-admin"];
                                     }else{
                                     	$delete_redirect = $top_section["delete_form"]["redirect"];
                                     }
						    		?>
									<button type="button" class="btn btn-primary delete_form" data-id="<?php echo $_GET['application_id'];?>" data-redirect="<?php echo $delete_redirect;?>"><?php echo $top_section["delete_form"]["label"];?></button>
								<?php } ?>
								<?php 
								    if(isset($_GET["admin"]) && $_GET["admin"] == "true"){
                                     	$redirect_draft = $top_section["save_as_draft"]["redirect-admin"];
                                    }else{
                                     	$redirect_draft = $top_section["save_as_draft"]["redirect"];
                                    }
                                ?>     

								<input type="hidden" name="redirect_draft" value="<?php echo$redirect_draft;?>">

								<button type="button" class="btn btn-primary save_as_draft" ><?php echo $top_section["save_as_draft"]["label"];?></button>
							<?php }else{ ?>
								<style type="text/css">
									.delete_application, .delete_reserve, .new_org, .add_reservation_cls, .add_new_section{
										display: none;
									}
								</style>
                                <a href="<?php echo $_SERVER['HTTP_REFERER'];?>"><button type="button" class="btn btn-primary go_back" >Gå tilbake</button></a>
							<?php } ?>	
						</div>
					</div>
					
					<hr />
				</div>

				<?php if(isset($top_section["fields"]["rows"])){

					    $columns = count($top_section["fields"]["rows"]) / 12;

						foreach ($top_section["fields"]["rows"] as  $rows) { ?>

							<div class="row">

							    <?php	
							        foreach ($rows as $key_row => $row) { 

							        	$profile_type = get_user_meta(get_current_user_ID(),"profile_type",true);

							        	if($profile_type == "company"){
							        		$typee = "for_company";
							        	}else{
							        		$typee = "for_private";
							        	}

									 	if($row['type'] == "new_org_message"){ 

									 		include("fields/new_org_message.php");

									    }if($row['type'] == "heading_with_hr"){ 

									 		include("fields/heading_with_hr.php");

									    }if($row['type'] == $typee){ 

									    	$row_prev = $row;

									    	foreach ($row["fields"] as $key_fields => $field) {
									    		$row = $field;
									    		if($row['type'] == "text"){ 

											 		include("fields/text.php");

											    }else if($row['type'] == "select"){ 

											 		include("fields/select.php");

											    }
									    	}

									    	$row = $row_prev;

									 		

									    }if($row['type'] == "text"){ 

									 		include("fields/text.php");

									    }elseif($row['type'] == "tel"){ 

									 		include("fields/tel.php");

									    }else if($row['type'] == "email"){ 

									    	include("fields/email.php");

									    }else if($row['type'] == "select"){ 

									    	include("fields/select.php");

									    } 


								    } 
								?>	 

							</div> 


							
					<?php } ?>
					<?php
					 if(!empty($top_section["fields"]["aditional_rows"])){
					 	about_fields_func($top_section["fields"]["aditional_rows"]);
					 }
					?>
				<?php } ?>	
			</div>
			

		<?php } ?>

		<div class="row">

			<div class="col-md-12 application_section">
				<?php 

	              if(isset($json_data["application"]) && !empty($json_data["application"])){

	              	 foreach ($json_data["application"] as $key_app => $application) {
	              	 	 $application_fields = get_application($key_app,$_GET['group_id'],$_GET['season_id']);
				         include("single_application.php");
	              	 }
	              }else{
	              	$application_fields = get_application(1,$_GET['group_id'],$_GET['season_id']);
				    include("single_application.php");
	              }
				   
				?>   
			</div>

		</div>

		<div class="row">

			<?php foreach ($add_button_section as $key_add_button => $add_button) {
				    $row = $add_button;

					if($row["name"] == "add_new_section"){ ?>


							<div class="col-md-12 add_new_section">
								<button type="button" class="btn btn-primary"<?php if(isset($row["attribute"])){ echo $row["attribute"]; }?>><?php echo $row["label"];?></button>  
							</div>

				    <?php } ?>

			<?php } ?>
		</div>
		<?php if($season_active_edit != false){ ?>
			<div class="row bottom_section fade-sec">

				<?php foreach ($bottom_sections as $key_bottom_section => $bottom_section) {
					    $row = $bottom_section;

						if($row["name"] == "term_and_condition"){ ?>
		                    <div class="col-md-12 text-align-center term_and_condition">
					    	   <?php include("fields/checkbox.php");?>
					    	</div>   

					    <?php }else if($row["name"] == "save_as_draft"){ ?>
					    	<?php 
							    if(isset($_GET["admin"]) && $_GET["admin"] == "true"){
                                 	$redirect_draft = $row["redirect-admin"];
                                }else{
                                 	$redirect_draft = $row["redirect"];
                                }
                            ?>  

					    	<div class="col-md-6 save_as_draft_main">
								<button type="button" class="btn btn-primary save_as_draft" data-redirect="<?php echo $redirect_draft;?>"><?php echo $row["label"];?></button>
							</div>

					    <?php }else if($row["name"] == "submit"){ ?>

					    	<div class="col-md-6 submit_main">
								<button type="button" class="btn btn-primary submit_form" ><?php echo $row["label"];?></button>
							</div>

					    <?php } ?>
					   

					

				<?php } ?>
			</div>
		<?php } ?>
	</form>

	<?php
	$users_modal_path = str_replace("/fields","",plugin_dir_path(__FILE__));
	   require_once($users_modal_path."/modal/users_modal.php");
	?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".main-form-div").show();
	})
</script>