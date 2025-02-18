<?php


$current_user_id = get_current_user_ID();

/* user groups and users_and_users_groups*/
global $wpdb;
$users_groups = $wpdb->prefix . 'users_groups';  // table name
$users_and_users_groups = $wpdb->prefix . 'users_and_users_groups';  // table name
$sql_user_group = "select b.id,b.name  from `$users_and_users_groups` as a left join `$users_groups` as b ON a.users_groups_id = b.id where a.users_id = $current_user_id";
$user_group_data_all = $wpdb->get_results($sql_user_group);



$user_group_data = array();

if($type != "user-access"){
	$user_group_data[0]->id = 0;
	$user_group_data[0]->name = __("Mine","gibbs");
	$kkk = 1;
}else{
    $kkk = 0;	
}
foreach ($user_group_data_all as $key => $user_group_da) {
	$user_group_data[$kkk]->id = $user_group_da->id;
	$user_group_data[$kkk]->name = $user_group_da->name;
	$kkk++;
}
/*$user_management_group_id = get_user_meta($current_user_id,"user_management_group_id_".$type,true);
if($user_management_group_id == ""){
	if(!empty($user_group_data)){
		$user_management_group_id = $user_group_data[0]->id;
	}
}
*/
$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

if($active_group_id != ""){
	$user_management_group_id = $active_group_id;
}else{
	$user_management_group_id = "0";
}
//echo $user_management_group_id; die;
//$booking_user_group_selected_id = $group_id;



$users_ids = array();

if($user_management_group_id == 0){

	$bookingss_table = $wpdb->prefix . 'bookings_calendar';  // table name
	$bookingss_table_sql = "select distinct bookings_author from `$bookingss_table` where owner_id = $current_user_id";
    $bookingss_data = $wpdb->get_results($bookingss_table_sql);

    foreach ($bookingss_data as $key => $bookingss_da) {
    	//$users_ids[] = $bookingss_da->bookings_author;
    }


}else{

	

	if($type == "user-access"){


		$users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';  // table name
		$users_and_users_groups_table_sql = "select users_id from `$users_and_users_groups_table` where users_groups_id = $user_management_group_id";
		$users_and_users_groups_data = $wpdb->get_results($users_and_users_groups_table_sql);

		foreach ($users_and_users_groups_data as $key => $users_and_users_groups_ids) {
			$users_ids[] = $users_and_users_groups_ids->users_id;
		}

	}else{


		$posts_table = $wpdb->prefix . 'posts';  // table name
		$posts_table_sql = "select ID from `$posts_table` where users_groups_id = $user_management_group_id";
		$posts_data = $wpdb->get_results($posts_table_sql);

		$group_admin = get_group_admin();

		if($group_admin == ""){
			$group_admin = $current_user_id;
		}
		


		//echo "<pre>"; print_r($posts_data);

		$listing_ids = array();

		foreach ($posts_data as $key => $posts_da) {
			$listing_ids[] = $posts_da->ID;
		}
	    $listing_ids = implode(",", $listing_ids);
		$bookingss_table = $wpdb->prefix . 'bookings_calendar';  // table name
		if($listing_ids != ""){
           $where = " (listing_id IN ($listing_ids)) OR (owner_id = $group_admin)";
		}else{
           $where = " owner_id = $group_admin";
		}
		$bookingss_table_sql = "select distinct bookings_author from `$bookingss_table` where $where";
		$bookingss_data = $wpdb->get_results($bookingss_table_sql);


	    
	    foreach ($bookingss_data as $key => $bookingss_da) {
	    	$users_ids[] = $bookingss_da->bookings_author;
	    }

	    //echo "<pre>"; print_r($bookingss_table_sql); die;


	    $users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';  // table name
		$users_and_users_groups_table_sql = "select users_id from `$users_and_users_groups_table` where users_groups_id = $user_management_group_id";
		$users_and_users_groups_data = $wpdb->get_results($users_and_users_groups_table_sql);

		foreach ($users_and_users_groups_data as $key => $users_and_users_groups_ids) {
			$users_ids[] = $users_and_users_groups_ids->users_id;
		}
	}

	
}

$group_admin = get_group_admin();

$users_listings = array();
// foreach ($users_ids as  $users_i) {

// 	$bookingss_table1 = $wpdb->prefix . 'bookings_calendar';  // table name
// 	$bookingss_table_sql1 = "select id from `$bookingss_table1` where bookings_author = ".$users_i;
// 	$bookingss_ids = $wpdb->get_results($bookingss_table_sql1);


	
    

//     $total_amount = 0;
//     $total_orders = 0;

//     foreach ($bookingss_ids as $key => $bookingss_id) {

//     	$postmeta_table = $wpdb->prefix . 'postmeta';  // table name
//     	$booking_id = $bookingss_id->id;
// 		$postmeta_table_sql = "select * from `$postmeta_table` where meta_key = 'booking_id' AND meta_value='$booking_id'";
		
// 		$postmeta_data = $wpdb->get_row($postmeta_table_sql);

// 		if($postmeta_data){
// 			$order_id = $postmeta_data->post_id;
// 			$total_amount += get_product_gross_revenue($order_id);
// 			$total_orders += get_total_product($order_id);
// 		}

		
//     	/*$total_amount += get_product_gross_revenue($bookingss_listing);
//     	$total_orders += get_total_product($bookingss_listing);*/
//     }

// 	$users_listings[$users_i]["total_amount"] = $total_amount;
// 	$users_listings[$users_i]["total_orders"] = $total_orders;
// }
//echo "<pre>"; print_r($users_ids); die;

/* end user groups and users_and_users_groups*/

/* from listing */

/* */
/* end users_and_users_groups*/
$users_ids = implode(",", $users_ids);
/*  users table */
$users_table = $wpdb->prefix . 'users';  // table name
$users_table_sql = "select * from `$users_table` where ID IN ($users_ids)";
$users_table_data = $wpdb->get_results($users_table_sql);

/*  end users table */
foreach ($users_table_data as $key => $users_tab) {
	$users_and_users_groups_table = $wpdb->prefix . 'users_and_users_groups';  // table name
	if($user_management_group_id != 0){
        $users_and_users_groups_table_sql = "select role from `$users_and_users_groups_table` where users_groups_id = ".$user_management_group_id." AND users_id = ".$users_tab->ID;
	}else{
        $users_and_users_groups_table_sql = "select role from `$users_and_users_groups_table` where  users_id = ".$users_tab->ID;
	}
	
	$users_and_users_groups_data = $wpdb->get_row($users_and_users_groups_table_sql);

	

	/*$roles = [];
    
    foreach ($users_and_users_groups_data as $key => $users_and_users_groups_dd) {
    	$roles[] = $users_and_users_groups_dd->role;
    }

    if(empty($roles)){
    	$roles[] = 0;
    }
    $users_tab->role =  $roles;*/



	if(isset($users_and_users_groups_data->role)){
		$users_tab->role =  $users_and_users_groups_data->role;
	}else{
		$users_tab->role =  0;
	}
	if(isset($users_listings[$users_tab->ID]['total_amount'])){
		$users_tab->total_amount = $users_listings[$users_tab->ID]['total_amount'];
	}else{
		$users_tab->total_amount = "";
	}
	if(isset($users_listings[$users_tab->ID]['total_orders'])){
		$users_tab->total_orders = $users_listings[$users_tab->ID]['total_orders'];
	}else{
		$users_tab->total_orders = "";
	}
}
//echo "<pre>"; print_r($users_table_data); die;

/* display row checkbox*/
$display_row_checkbox_array = array();
$display_row_checkbox = get_user_meta(get_current_user_ID(),"display_row_checkbox",true);
if($display_row_checkbox && $display_row_checkbox != ""){
	$display_row_checkbox_array_data  = json_decode($display_row_checkbox);
	if(!empty($display_row_checkbox_array_data)){
		$display_row_checkbox_array = $display_row_checkbox_array_data;
	}
}


//echo "<pre>"; print_r($user_management_group_id); die;
/* display row checkbox */
if(isset($_GET["email_exists"]) && $_GET["email_exists"] == "true"){ ?>

	<div class="alert alert-danger" role="alert" style="color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;">
	 Eposten er brukt!
	</div>

<?php }
?>


<div class="container main_user_manage" style="display: none;">
	<div class="main_user_manage_row">
			<div class="row-data-filter user_cols_main">



				<div class="listing-outer" >
					<div class="search-box-inner">
						<i class="fa-solid fa-magnifying-glass" style="width: 30px;"></i></span> <div class="dropdown">
						
						  <input type="text" class="search_in">
						</div>
					</div>
				</div>

				<!-- <div class="listing-outer_first">
					<div class="search-box-inner">
						<span class="user_icon"><i class="fa fa-user" aria-hidden="true"></i></span> <div class="dropdown">
							 <?php foreach ($user_group_data as $key => $groups1) { ?>
							 	<?php if($groups1->id == $user_management_group_id){ ?>
	                                <button class="dropbtn"><?php echo $groups1->name;?></button>
	                            <?php } ?>
	                        <?php } ?>
						  <div id="groupDropdown" class="dropdown-content">
						    <input type="text" placeholder="Search.." onkeyup="filterFunction(this)">
						    <?php foreach ($user_group_data as $key => $groups) { ?>
						    	<a class="group_drp <?php if($groups->id == $user_management_group_id){ echo 'active';}?>" href="javascript:void(0)" data-id="<?php echo $groups->id;?>"><?php echo $groups->name;?></a>
						    <?php } ?>
						  </div>
						</div>
					</div>
				</div> -->

				<!-- <div class="listing-outer">
					<div class="search-box-inner"> -->
					<!-- 	<span class="user_icon"><i class="fa fa-filter" aria-hidden="true"></i></span> 
						<div class="dropdown">
	                       <button class="dropbtn"><?php echo __("Filter","gibbs");?></button>
							  <div id="myDropdown" class="dropdown-content">
							    <ul class="user_management_ul">
							  		<?php if($type != "user-access"){ ?>
								  		<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input id="row_display_name" type="checkbox" class="user_management_checkbox" data-target="target_display_name" value="row_display_name" <?php if(!in_array("row_display_name", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_display_name"><?php echo __("Display Name","gibbs");?></label>    
								            </div>  
								  		</li>
								  	<?php } ?>
							  		<li>
							  			<div class="dynamic checkboxes in-row"> 
							                <input  id="row_email" type="checkbox" class="user_management_checkbox" data-target="target_email" value="row_email" <?php if(!in_array("row_email", $display_row_checkbox_array)){ echo "checked";}?>>
							                <label for="row_email"><?php echo __("Email","gibbs");?></label>    
							            </div>  
							  		</li>
							  		<li>
							  			<div class="dynamic checkboxes in-row"> 
							                <input  id="row_first_name" type="checkbox" class="user_management_checkbox" data-target="target_first_name" value="row_first_name" <?php if(!in_array("row_first_name", $display_row_checkbox_array)){ echo "checked";}?>>
							                <label for="row_first_name"><?php echo __("First name","gibbs");?></label>    
							            </div>  
							  		</li>
							  		<li>
							  			<div class="dynamic checkboxes in-row"> 
							                <input  id="row_last_name" type="checkbox" class="user_management_checkbox" data-target="target_last_name" value="row_last_name" <?php if(!in_array("row_last_name", $display_row_checkbox_array)){ echo "checked";}?>>
							                <label for="row_last_name"><?php echo __("Last name","gibbs");?></label>    
							            </div>  
							  		</li>
							  		<li>
							  			<div class="dynamic checkboxes in-row"> 
							                <input  id="row_phone" type="checkbox" class="user_management_checkbox" data-target="target_phone" value="row_phone" <?php if(!in_array("row_phone", $display_row_checkbox_array)){ echo "checked";}?>>
							                <label  for="row_phone"><?php echo __("Phone","gibbs");?></label>    
							            </div>  
							  		</li>
							  		<?php if($type != "user-access"){ ?>
								  		<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input  id="row_zip" type="checkbox" class="user_management_checkbox" data-target="target_zip" value="row_zip" <?php if(!in_array("row_zip", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_zip"><?php echo __("Zip","gibbs");?></label>    
								            </div>  
								  		</li>
							  		
								  		<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input  id="row_city" type="checkbox" class="user_management_checkbox" data-target="target_city" value="row_city" <?php if(!in_array("row_city", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_city"><?php echo __("City","gibbs");?></label>    
								            </div>  
								  		</li>

								  		<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input  id="row_org" type="checkbox" class="user_management_checkbox" data-target="target_org" value="row_org" <?php if(!in_array("row_org", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_org"><?php echo __("Organisasjonsnummer","gibbs");?></label>    
								            </div>  
								  		</li>
								  		<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input  id="row_address" type="checkbox" class="user_management_checkbox" data-target="target_address" value="row_address" <?php if(!in_array("row_address", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_address"><?php echo __("Address","gibbs");?></label>    
								            </div>  
								  		</li>
							  		<?php }else{ ?>

							  			<li>
								  			<div class="dynamic checkboxes in-row"> 
								                <input  id="row_role" type="checkbox" class="user_management_checkbox" data-target="target_role" value="row_role" <?php if(!in_array("role", $display_row_checkbox_array)){ echo "checked";}?>>
								                <label for="row_role"><?php echo __("Role","gibbs");?></label>    
								            </div>  
								  		</li>

							  		<?php } ?>
							  	</ul>
							  </div>
						</div> -->
				<!-- 	</div>
				</div> -->
				<?php if($type == "user-access"){ ?>
					<div class="listing-outer">
						<div class="search-box-inner" id="user_modalbtn">
							<span class="user_icon"><i class="fa fa-plus-circle" aria-hidden="true"></i></span> <div class="dropdown">
							  <button class="dropbtn"><span class="filter_text" ><?php echo __("Add new customer","gibbs");?></span></button>
							</div>
						</div>
					</div>
				<!-- 	<div class="listing-outer">
						<div class="search-box-inner" id="usergroup_modalbtn">
							<span class="user_icon"><i class="fas fa-edit" aria-hidden="true"></i></span> <div class="dropdown">
							  <button class="dropbtn"><span class="filter_text span-btn" ><?php echo __("Edit group","gibbs");?></span></button>
							</div>
						</div>
					</div> -->
				<!-- 	<div class="listing-outer">
						<div class="search-box-inner" id="usergroup_addnew">
							<span class="user_icon"><i class="fa fa-plus-circle" aria-hidden="true"></i></span> <div class="dropdown">
							  <button class="dropbtn"><span class="filter_text" ><?php echo __("Add new group ","gibbs");?></span></button>
							</div>
						</div>
					</div> -->
					
				<?php } ?>


				

				
				
		</div>
		<div class="user_table_content" >
			<div class="table_div">
				<table class="table user-table table-hover align-items-center datatable" style="width:100%;">
		        <thead>
		            <tr>
		            	<?php if($type != "user-access"){ ?>
			                <th class="target_display_name"><?php echo __("Display Name","gibbs");?></th>
			            <?php } ?>
		                <th class="target_email"><?php echo __("Email","gibbs");?></th>
		                <th class="target_first_name"><?php echo __("First name","gibbs");?></th>
		                <th class="target_last_name"><?php echo __("Last name","gibbs");?></th>
		                <th class="target_phone"><?php echo __("Phone","gibbs");?></th>
		                <?php if($type != "user-access"){ ?>
			                <th class="target_zip"><?php echo __("Zip","gibbs");?></th>
			                <th class="target_city"><?php echo __("City","gibbs");?></th>
			                <th class="target_org"><?php echo __("Org no.","gibbs");?></th>
			                <th class="target_address"><?php echo __("Address","gibbs");?></th>
			                <!-- <th class="target_total_spend"><?php echo __("total spend","gibbs");?></th>
			                <th class="target_bookings"><?php echo __("Bookings","gibbs");?></th> -->
			            <?php }else{ ?>
			            	<th class="target_role"><?php echo __("Role","gibbs");?></th>
			            	
			            <?php } ?>	
			            <th><?php echo __("Action","gibbs");?></th>
		                
		            </tr>
		        </thead>
		        <tbody>
		        	<?php 


		        	   foreach ($users_table_data as $users_table_d) {
		        	   	$first_name = get_user_meta($users_table_d->ID,"first_name",true);
		        	   	$last_name = get_user_meta($users_table_d->ID,"last_name",true);
		        	   	$phone = get_user_meta($users_table_d->ID,"phone",true);
		        	   	$user_zipcode = get_user_meta($users_table_d->ID,"billing_postcode",true);
		        	   	$user_city = get_user_meta($users_table_d->ID,"billing_city",true);
		        	   	$org_pers_num = get_user_meta($users_table_d->ID,"company_number",true);
		        	   	$user_address = get_user_meta($users_table_d->ID,"billing_address_1",true);
		        	   	$meta_dtaa = get_user_meta($users_table_d->ID);
		        	   	if($type == "user-access"){
		        	    	if($users_table_d->role == "1" ){
                              continue;
		        	    	}
		        	    }else{
		        	    	if($users_table_d->role != "1" ){
                              //continue;
		        	    	}

		        	    }	

		        	    if($group_admin == $users_table_d->ID){
		        	    	continue;
		        	    }
		        	?>
				            <tr>
				            	<?php if($type != "user-access"){ ?>
					                <td class="target_display_name"><?php echo $users_table_d->display_name;?></td>
					            <?php } ?>
				                <td class="target_email"><?php echo $users_table_d->user_email;?></td>
				                <td class="target_first_name"><?php echo $first_name;?></td>
				                <td class="target_last_name"><?php echo $last_name;?></td>
				                <td class="target_phone"><?php echo $phone;?></td>
				                <?php if($type != "user-access"){ ?>
					                <td class="target_zip"><?php echo $user_zipcode;?></td>
					                <td class="target_city"><?php echo $user_city;?></td>
					                <td class="target_org"><?php echo $org_pers_num;?></td>
					                <td class="target_address"><?php echo $user_address;?></td>
					                <!-- <td class="target_total_spend"><?php echo get_woocommerce_currency_symbol();?> <?php echo $users_table_d->total_amount;?></td>
					                <td class="target_bookings"><?php echo $users_table_d->total_orders;?></td> -->
					                <td>
					                	<span  class="remove_user_from_user_group" user_id="<?php echo $users_table_d->ID;?>" users_group_id="<?php echo $user_management_group_id;?>"><i class="fa fa-trash" style="color:red;cursor: pointer;"></i></span>
					                </td>
					            <?php }else{ ?>
					            	<td class="target_role"><?php 
					            	if($users_table_d->role == "1"){
					            		echo __("Kunde","gibbs");
					            	}elseif($users_table_d->role == "2"){
					            	    echo __("Ansatt","gibbs");
					            	}elseif($users_table_d->role == "3"){
					            		echo __("Administrator","gibbs");
					            	}elseif($users_table_d->role == "4"){
					            		echo __("Saksbehandler","gibbs");
					            	}
					            	?></td>
					            	<td>
					            		<span  id="user_edit_modal_btn<?php echo $users_table_d->ID;?>" ><i class="fa fa-edit"></i></span>
					            		<span  class="remove_user_from_user_group" user_id="<?php echo $users_table_d->ID;?>" users_group_id="<?php echo $user_management_group_id;?>"><i class="fa fa-trash" style="color:red;cursor: pointer;"></i></span>
					            	</td>
					            <?php } ?>

				            </tr>
		            <?php } ?>
		           
		            
		        </tbody>
		    </table>
		    </div>
		</div>
	</div>
	<?php 
	foreach ($users_table_data as $users_table_d2) {
		if($type == "user-access"){

		   require(__DIR__."/users_edit_modal.php");
		}   
	}
	?>
	<?php 
	require(__DIR__."/user_group_modal.php");
	require(__DIR__."/users_modal.php");
	?>
</div>

<script>
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunctionbtn() {
  document.getElementById("myDropdown").classList.toggle("show");
}

jQuery(document).ready(function(){
	jQuery('.main_user_manage').show();
})

function filterFunction(div) {

  var input, filter, ul, li, a, i;
  input = div;
  filter = input.value.toUpperCase();
  div = jQuery(div).parent()[0];
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}

jQuery(".main_user_manage").find(".dropdown-content a").click(function(){
   jQuery(this).toggleClass("active");
})

jQuery(document).on('click', function (e) {
    if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".search-box-inner").length === 0 && jQuery(e.target).closest(".dropdown-content").length === 0) {
        jQuery(".dropdown-content").removeClass("show");
    }
});
jQuery(document).on("click",".search-box-inner span,.search-box-inner button,.search-box-inner i",function(){
	var parent_div = jQuery(this).parent();
	jQuery(".search-box-inner").not(parent_div).find(".dropdown-content").removeClass("show");
	jQuery(this).parent().find(".dropdown-content").toggleClass("show");
})
jQuery(document).on('click','.group_drp',function(e) {

	jQuery(this).parent().find(".active").removeClass("active");
    jQuery(this).addClass("active");

    jQuery(".delete_form").remove();
    

    var f = document.createElement("form");
    f.setAttribute('class',"delete_form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

    var i = document.createElement("input"); //input element, text
    i.setAttribute('type',"text");
    i.setAttribute('name',"user_management_group_id");
    i.setAttribute('value',"<?php echo $user_management_group_id;?>");
    f.appendChild(i);

    var i2 = document.createElement("input"); //input element, text
    i2.setAttribute('type',"text");
    i2.setAttribute('name',"type");
    i2.setAttribute('value',"<?php echo $type;?>");
    f.appendChild(i2);

    var a_i = document.createElement("input"); //input element, text
    a_i.setAttribute('type',"hidden");
    a_i.setAttribute('name',"action");
    a_i.setAttribute('value',"user_management_save_group_id");
    f.appendChild(a_i);
    jQuery("body").append(f);
    f.submit();
})

jQuery(document).ready( function () {
    const oTable = jQuery(".datatable").DataTable({
					    	"language": {
					        "sProcessing":    "behandling...",
					        "sLengthMenu":    "Vis _MENU_ poster",
					        "sZeroRecords":   "Ingen resultater",
					        "sEmptyTable":    "Ingen data tilgjengelig i denne tabellen",
					        "sInfo":          "Viser _START_ til _END_ av _TOTAL_ brukere",
					        "sInfoEmpty":     "Viser poster fra 0 til 0 av totalt 0 poster",
					        "sInfoFiltered":  "(filtrerer totalt _MAX_ poster)",
					        "sInfoPostFix":   "",
					        "sSearch":        "Søke:",
					        "sUrl":           "",
					        "sInfoThousands":  ",",
					        "sLoadingRecords": "Lader...",
					        "oPaginate": {
					            "sFirst":    "Først",
					            "sLast":    "Siste",
					            "sNext":    "Følgende",
					            "sPrevious": "Fremre"
					        },
					        "oAria": {
					            "sSortAscending":  ": Merk av for å sortere kolonnen i stigende rekkefølge",
					            "sSortDescending": ": Merk av for å sortere kolonnen synkende"
					        }
					    }
				    });
    jQuery(".search_in").keyup(function(){
		 oTable.search( jQuery(this).val() ).draw();
	})
} );
function checkbox_function(){
	var checked_arr = [];
	jQuery(".user_management_checkbox").each(function(){

		var data_target = jQuery(this).data("target");

		if(jQuery(this)[0].checked == true){

			jQuery("."+data_target).show();

		}else{

			checked_arr.push(jQuery(this).val());
			jQuery("."+data_target).hide();
			
		}
	});

	jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {action:"display_row_checkbox","checkboxs":checked_arr},
        dataType: 'json',
        success: function (data) {
          
         
        }
    });
}
checkbox_function();
jQuery(".user_management_checkbox").change(function(){

	checkbox_function();
})

jQuery(".remove_user_from_user_group").click(function(){

	jQuery(".delete_form").remove();

	if (confirm("Are you sure!")) {
		var f = document.createElement("form");
	    f.setAttribute('class',"delete_form");
	    f.setAttribute('method',"post");
	    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

	    var i = document.createElement("input"); //input element, text
	    i.setAttribute('type',"text");
	    i.setAttribute('name',"users_group_id");
	    i.setAttribute('value',jQuery(this).attr("users_group_id"));
	    f.appendChild(i);

	    var i2 = document.createElement("input"); //input element, text
	    i2.setAttribute('type',"text");
	    i2.setAttribute('name',"user_id");
	    i2.setAttribute('value',jQuery(this).attr("user_id"));
	    f.appendChild(i2);

	    var a_i = document.createElement("input"); //input element, text
	    a_i.setAttribute('type',"hidden");
	    a_i.setAttribute('name',"action");
	    a_i.setAttribute('value',"remove_user_from_user_group");
	    f.appendChild(a_i);
	    jQuery("body").append(f);
	    f.submit();
	}
    
})
</script>