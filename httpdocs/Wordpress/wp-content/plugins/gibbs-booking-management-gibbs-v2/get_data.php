<?php




function gibbs_owner_listings($user_id,$group_id){

	global $wpdb;

	if($group_id == "0"){

		$sql = "SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type='listing' AND post_status = 'publish' AND post_author=$user_id";
		$results  = $wpdb -> get_results($sql);

		return $results;

	}else{


	  $group_user_ids = array();

	  $posts_table = $wpdb->prefix . 'posts';  // table name
	  $posts_table_sql = "select ID,post_title from `$posts_table` WHERE post_type='listing' AND post_status = 'publish' AND users_groups_id = $group_id";
	  $results = $wpdb->get_results($posts_table_sql);



	  $group_admin = get_group_admin();

      if($group_admin != ""){

        $posts_table = $wpdb->prefix . 'posts';  // table name
        $posts_table_sql = "select ID,post_title from `$posts_table` where post_type='listing' AND post_status = 'publish' AND post_author = $group_admin";
        $posts_data = $wpdb->get_results($posts_table_sql);



        foreach ($posts_data as $key => $posts_da) {
          $results[] = $posts_da;
        }

        
      }

	  return $results;

	}
	

}

function gibbs_user_groups($user_id){

	global $wpdb;
	$users_groups = $wpdb->prefix . 'users_groups';  // table name
	$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
	$sql_user_group = "select b.id,b.name,a.role  from `$users_and_users_groups` as a left join `$users_groups` as b ON a.users_groups_id = b.id where a.users_id = $user_id";
	$user_group_data_all = $wpdb->get_results($sql_user_group);


	return $user_group_data_all;

}

function gibbs_owner_customer($user_id,$group_id){

	global $wpdb;

	if($group_id == "0"){

		$sql = "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE owner_id=$user_id";

		$results  = $wpdb -> get_results($sql);

		$users_ids = array();

		foreach ($results as $key => $result) {
	    	$users_ids[] = $result->bookings_author;
	    }

	    $users_ids = implode(",", $users_ids);
		/*  users table */
		$users_table = $wpdb->prefix . 'users';  // table name
		$users_table_sql = "select * from `$users_table` where ID IN ($users_ids)";
		$users_table_data = $wpdb->get_results($users_table_sql);
	   

		return $users_table_data;

	}else{

		  $group_user_ids = array();

	      $posts_table = $wpdb->prefix . 'posts';  // table name
	      $posts_table_sql = "select ID from `$posts_table` where users_groups_id = $group_id";
	      $posts_data = $wpdb->get_results($posts_table_sql);

	      $listing_ids = array();

	      foreach ($posts_data as $key => $posts_da) {
	        $listing_ids[] = $posts_da->ID;
	      }
	      $group_admin = get_group_admin();

	      if($group_admin != ""){

	        $posts_table = $wpdb->prefix . 'posts';  // table name
	        $posts_table_sql = "select ID from `$posts_table` where post_type='listing' AND post_status = 'publish' AND post_author = $group_admin";
	        $posts_data = $wpdb->get_results($posts_table_sql);

	        foreach ($posts_data as $key => $posts_da) {
	          $listing_ids[] = $posts_da->ID;
	        }
	        
	      }
	      $listing_ids = implode(",", $listing_ids);
	      $bookingss_table = $wpdb->prefix . 'bookings_calendar';  // table name
	      $bookingss_table_sql = "select distinct bookings_author from `$bookingss_table` where listing_id IN ($listing_ids)";
	      $bookingss_data = $wpdb->get_results($bookingss_table_sql);
	        
	      foreach ($bookingss_data as $key => $bookingss_da) {
	        $group_user_ids[] = $bookingss_da->bookings_author;
	      }
	      $group_user_ids = implode(",", $group_user_ids);

	        $users_table = $wpdb->prefix . 'users';  // table name
			$users_table_sql = "select * from `$users_table` where ID IN ($group_user_ids)";
			$users_table_data = $wpdb->get_results($users_table_sql);
		   

			return $users_table_data;
		
	}	

}


?>
