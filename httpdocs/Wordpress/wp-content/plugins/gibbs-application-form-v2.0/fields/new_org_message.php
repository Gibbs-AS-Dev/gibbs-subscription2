<?php
$data = get_user_by("id",get_current_user_ID());
$profile_type = get_user_meta(get_current_user_ID(),"profile_type",true);

if (isset($_SESSION['parent_user_id']) && $_SESSION['parent_user_id'] != "") {
    $sub_current_user_id = $_SESSION['parent_user_id'];
}else{
	$sub_current_user_id = get_current_user_ID();
}

$sub_user_ids = get_user_meta( $sub_current_user_id, 'sub_users',true );

//echo "<pre>"; print_r($sub_user_ids ); die;
$show_org = true;;
if(isset($_GET["admin"]) && $_GET["admin"] == "true"){
   $show_org = false;
}

if(isset($_GET["application_id"]) && $_GET["application_id"] != ""){
	$show_org = false;

	if(!empty($json_data)){
		global $wpdb;

		$application_data_table = 'application_data';  // table name
        $sql_application_data = "select *  from `$application_data_table` where  id=".$_GET['application_id'];
        $application_data = $wpdb->get_row($sql_application_data);

        $season_name = "";
        $deadline = "";
        $group_name = "";

        if(isset($application_data->season_id)){

        	$seasons_table = 'seasons';  // table name
			$sql_season = "select name, season_end from `$seasons_table` where id = ".$application_data->season_id;
			$season_data = $wpdb->get_row($sql_season);

			$season_name = $season_data->name; 
			$deadline = $application_data->deadline; 


        }

         if(isset($application_data->group_id)){

	         $user_groups_table = $wpdb->prefix . 'users_groups';
				$user_groups_results = $wpdb->get_row("SELECT * FROM $user_groups_table where id=".$application_data->group_id);
            if(isset($user_groups_results->name)){
            	$group_name = $user_groups_results->name; 
            }


        }

       if($season_name != "" && $deadline != ""){

	}
	?>
			<div class="<?php echo $row['class'];?> new_org">
				<div class="alert alert-info" role="alert">
				   <i class="far fa-info-circle"></i> Du redigerer din søknad for <b><?php echo $season_name;?></b> for <b><?php echo $group_name;?></b> som har en frist på <b><?php echo $deadline;?></b> 
				</div>
			</div>
	<?php
        }
}
if($show_org == true){
?>
	<div class="<?php echo $row['class'];?> new_org">
		<div class="alert alert-info" role="alert">
		   <i class="far fa-info-circle"></i> Du sender søknad som <b><?php echo $data->display_name;?></b>. Hvis du ønsker å sende søknad som en organisasjon <b id="user_modalbtn">trykk her.</b> <?php if(!empty($sub_user_ids)){ ?>Ønsker du å bytte bruker <b id="same_user">trykk her.</b><?php } ?>
		</div>
	</div>
<?php
	
}
