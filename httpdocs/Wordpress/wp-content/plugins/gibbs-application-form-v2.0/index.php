<?php
/*
Plugin Name: Gibbs Application Form New
Description: Gibbs Application Form shortcode [application-form-gibbs name="form-1"]
Version: 3.0.01
Author: gibbs team
License: GPLv2 or later
Text Domain: Gibbs
*/

if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_script( 'inteljs', plugin_dir_url(__FILE__) . 'public/intltel-input/build/js/intlTelInput-jquery.min.js', array( 'jquery' ), '', false );

wp_enqueue_style( 'application_form_common-style', plugin_dir_url(__FILE__) . 'css/common_style.css' ,[],time());


function application_form_gibbs(){
	wp_enqueue_style( 'application_form-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());
	if(is_user_logged_in()){

		$json_data = array();

		$season_active_edit = true;

		$season_check = false;

		$valid_group = false;

		global $wpdb;
		$user_groups_table = $wpdb->prefix . 'users_groups';
		$seasons_table = "seasons";

		$user_groups_results = $wpdb->get_results("SELECT g.*,s.name as season_name, s.`on/off` as onoff, s.id as season_id FROM $user_groups_table g INNER JOIN $seasons_table s ON s.users_groups_id = g.id");

		$application_data_table = 'application_data';  // table name

		$current_user_id = get_current_user_ID();


		if(!isset($_GET['application_id'])){
		
			
			foreach ($user_groups_results as $key => $user_groups_result) {
				if($_GET["group_id"] ==  $user_groups_result->id && $_GET["season_id"] ==  $user_groups_result->season_id){
					//echo "<pre>"; print_r($user_groups_result); die;
					if($user_groups_result->onoff == "1"){
	                    $valid_group = true;
					}else{
						$message =  __("Sesongen er inaktiv eller fristen har gått ut.","gibbs");
	    				require_once("noData.php");
	    				return;
					    exit;
					}
					
					break;
				}
			}

			if($valid_group == false){
				$message =  __("Incorrect link.","gibbs");
				require_once("noData.php");
				return;
				exit;
			}


			$sql_application_data_season_check = "select json_data  from `$application_data_table` where user_id = $current_user_id";

			$season_check_data = $wpdb->get_results($sql_application_data_season_check);

			

			foreach ($season_check_data as $key => $season_check_da) {
				$json_data_sr = maybe_unserialize($season_check_da->json_data);

				if($json_data_sr['season_id'] == $_GET['season_id']){
					$season_check = true;
				    $valid_group = false;
					break;
				}

			}
		}else{



			

			//echo "<pre>"; print_r($application_data); die;

			$application_data = '';

			if(isset($_GET["admin"]) && $_GET["admin"] == "true"){

				$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

				if($active_group_id != ""){
                     $application_data_table = 'application_data';  // table name
	                 $sql_application_data = "select *  from `$application_data_table` where group_id = ".$active_group_id." AND id=".$_GET['application_id'];
	                 $application_data = $wpdb->get_row($sql_application_data);
				}
			}else{

				$sql_application_data = "select *  from `$application_data_table` where user_id = $current_user_id AND id=".$_GET['application_id'];

			    $application_data = $wpdb->get_row($sql_application_data);

			}
			
			if(isset($application_data->id)){
				$json_data = maybe_unserialize($application_data->json_data);
				$valid_group = true;
				$_GET["group_id"] = $application_data->group_id;
				$_GET["season_id"] = $application_data->season_id;

				$seasons_sql = "SELECT * from seasons where id =".$application_data->season_id;
                $seasons_data = $wpdb->get_row($seasons_sql);
                


                if($seasons_data->{'on/off'} != "1"){
                   $season_active_edit = false;
                }

			}
		}
		$GLOBALS['json_data'] = $json_data;

		

		wp_enqueue_style( 'afb_intltel2_css-style', plugin_dir_url(__FILE__) . 'public/intltel-input/build/css/intlTelInput.css',[],time());


		if(isset($_GET["group_id"]) && $_GET["group_id"] != ""){

			updateApplicationMenu();

			$GLOBALS['group_id'] = $_GET["group_id"];
			$GLOBALS['season_id'] = $_GET["season_id"];

			$user_group_check = user_group_form_check($_GET["group_id"]);

			$form_name = "form-".$user_group_check;

			$form_path = plugin_dir_path(__FILE__)."/forms/".$form_name.".php";

    		if(file_exists($form_path)){

    			require_once($form_path);

    			if(isset($_GET["success"]) && $_GET["success"] == "true"){
    				require_once("success.php");
    			}elseif($season_check == true){
    				require_once("season_not_found.php");
    			}elseif($valid_group){
                    require_once("main.php");
    			}elseif(isset($_GET["application_id"])){
    				$message =  __("Application not found!","gibbs");
    				require_once("noData.php");
    			}else{
    				$message =  __("Season not exist!","gibbs");
    				require_once("noData.php");
    			}

               
    		}else{
    			$message =  __("Form structure is not selected. Please contact kontakt@gibbs.no","gibbs");
    			require_once("noData.php");

    		}
		}else{

			$message =  __("Incorrect link!","gibbs");
    		require_once("noData.php");

		}

	   /* $page_type = "";
	    $booking_type = "";


	    if(isset($_GET["booking_id"])){

	    	if(isset($parms["type"]) && $parms["type"] == "buyer"){
	    		$page_type = "buyer";
	            require(__DIR__."/single-main.php");  
			}else{
				
		    	$page_type = "owner";

		    	require(__DIR__."/single-main.php");  
			}


	    }else{
	    	if(isset($parms["type"]) && $parms["type"] == "buyer"){
	    		$page_type = "buyer";
	            require(__DIR__."/buyer-main.php");  
			}else{
				$page_type = "owner";
				require(__DIR__."/main.php");
			}
	    }*/

	    require(__DIR__."/script.php");

		

	    
	}else{

		require_once("notlogin.php");
		
	}
	
}
add_shortcode("application-form-gibbs","application_form_gibbs");

function application_form_list($parms){

	wp_enqueue_style( 'application_form-style', plugin_dir_url(__FILE__) . 'css/style.css' ,[],time());

	if(is_user_logged_in()){



		wp_enqueue_style( 'application_list-style', plugin_dir_url(__FILE__) . '/list/css/style.css' ,[],time());
	    wp_enqueue_style( 'application_list-jquery-datatable-style', plugin_dir_url(__FILE__) . '/list/css/jquery.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'application_list-rowReorder-datatable-style', plugin_dir_url(__FILE__) . '/list/css/rowReorder.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'application_list-responsive-datatable-style', plugin_dir_url(__FILE__) . '/list/css/responsive.dataTables.min.css' ,[],time());
	    wp_enqueue_style( 'application_list-datatable-script', plugin_dir_url(__FILE__) . '/list/js/datatable.min.js' ,[],time());
	   
	     wp_enqueue_script( 'application_list-datatable-jquery', plugin_dir_url(__FILE__).'/list/js/jquery.dataTables.min.js',array(),null,true);
         wp_enqueue_script( 'application_list-datatable-bootstrap-js', plugin_dir_url(__FILE__).'/list/js/dataTables.bootstrap4.min.js', array( 'jquery' ),null,true);
         wp_enqueue_script( 'application_list-responsive-datatable-script', plugin_dir_url(__FILE__) . '/list/js/dataTables.responsive.min.js' ,[],time());
	     wp_enqueue_script( 'application_list-rowReorder-datatable-script', plugin_dir_url(__FILE__) . '/list/js/dataTables.rowReorder.min.js' ,[],time());

	     updateApplicationMenu();
    


        if(isset($parms["type"]) && $parms["type"] == "admin"){
			  require(__DIR__."/list/admin.php");
		}else{
			 require(__DIR__."/list/main.php");
		}


	   
		

	    
	}else{

		$message =  __("Please login!","gibbs");
    	require_once("noData.php");
		
	}
	
}

add_shortcode("application-form-list-gibbs","application_form_list");

function user_group_form_check($group_id){

	global $wpdb;

    $user_groups_table = $wpdb->prefix . 'users_groups';

	$user_groups_results = $wpdb->get_row("SELECT * FROM $user_groups_table where id=".$group_id);

	if(isset($user_groups_results->type_of_form)){
		return $user_groups_results->type_of_form;
	}else{
		return "";
	}
}

function updateApplicationMenu(){
	$user_idd = get_current_user_id();
    $hide_application_menu_in_sidebar_for_user = get_user_meta($user_idd,"hide_application_menu_in_sidebar_for_user",true);

    if($hide_application_menu_in_sidebar_for_user != "false"){
        update_user_meta($user_idd,"hide_application_menu_in_sidebar_for_user","false");
    }
}

require(__DIR__."/save_data.php");

add_action( 'rest_api_init', function () {
    register_rest_route( 'v1', '/generateapp', array(
        'methods' => 'GET',
        'callback' => 'generateapp',
    ) );
} );

function generateapp(){
	global $wpdb;
	$table = 'application_data';

	$data = $wpdb->get_row("SELECT * FROM $table WHERE id = ".$_GET["application_id"]);


	if(isset($data->json_data)){
		$app_data = maybe_unserialize( unserialize($data->json_data));

       // echo "<pre>"; print_r($app_data);
		sendHtmlForm($app_data,$_GET["application_id"]);
	}
	exit;
}
function sendHtmlForm($json_data, $application_id)
{

	ob_start();

    header("Content-Type: text/html");

	$get_age = get_age($json_data["group_id"]);
	$get_levels = get_levels($json_data["group_id"]);
	$get_sports = get_sports($json_data["group_id"]);
	$get_locations_data = get_locations_data($json_data["group_id"]);
	$get_days = get_days_form();



	$user_group_check = user_group_form_check($json_data["group_id"]);

	$user_form = $user_group_check;

	$form_name = "form-" . $user_group_check;

	$form_path = plugin_dir_path(__FILE__) . "/forms/" . $form_name . ".php";

	if (file_exists($form_path)) {
		//include the specified file
		require($form_path);
		$pdftext = pdftext();
		require(plugin_dir_path(__FILE__) . "/html-form.php");

		echo $html = ob_get_clean();
        exit;
		
	}
}
