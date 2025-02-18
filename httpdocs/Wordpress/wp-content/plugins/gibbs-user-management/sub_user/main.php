<?php


$current_user_id = get_current_user_ID();

/* user groups and users_and_users_groups*/
global $wpdb;
//$sub_users = get_user_meta(get_current_user_id(),"sub_users",true);


$userss = get_users();

$sub_users = array();

foreach ($userss as $key => $user) {
	$sub_users_d = get_user_meta($user->ID,"sub_users",true);
	if(!empty($sub_users) && !is_array($sub_users) && $sub_users != ""){
          $sub_users = array($sub_users);
    }
	if(!empty($sub_users_d)){
		if(in_array(get_current_user_id(), $sub_users_d)){
			$sub_users[] = $user->ID;
		}
	}
}

//echo "<pre>"; print_r($user_management_group_id); die;
/* display row checkbox */
?>

<div class="container main_user_manage" style="display: none;">
	<div class="main_user_manage_row">
			<div class="row-data-filter user_cols_main">

				<div class="listing-outer">
					<div class="search-box-inner" id="user_modalbtn">
						<span class="user_icon"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
						<div class="dropdown">
						  <button class="dropbtn"><span class="filter_text" ><?php echo __("Invite administrator","gibbs");?></span></button>
						</div>
					</div>
					
				</div>
				<div class="listing-outer">
					<div class="search-box-inner" id="new_user_modalbtn">
						<div class="dropdown">
						  <button class="dropbtn"><span class="filter_text" ><?php echo __("Legg til bruker","gibbs");?></span></button>
						</div>
					</div>
				</div>

				

				
				
		</div>
		<div class="user_table_content" >
			<div class="table_div">
				<table class="table user-table table-hover align-items-center datatable" style="width:100%;">
		        <thead>
		            <tr>
		            	
		                <th class="target_email"><?php echo __("Email","gibbs");?></th>
		                <th class="target_first_name"><?php echo __("First name","gibbs");?></th>
		                <th class="target_last_name"><?php echo __("Last name","gibbs");?></th>
		                <th class="target_phone"><?php echo __("Phone","gibbs");?></th>
		                <th class="target_action"></th>
		               
		                
		            </tr>
		        </thead>
		        <tbody>

		        	<?php
		        	if(!empty($sub_users)){
						foreach ($sub_users as $key => $sub_user) {

							$user = get_user_by("ID",$sub_user);

							$first_name = get_user_meta($user->ID,"first_name",true);
			        	   	$last_name = get_user_meta($user->ID,"last_name",true);
			        	   	$phone = get_user_meta($user->ID,"phone",true);
			        	?>
			        	<tr>
			        	    <td class="target_email"><?php echo $user->user_email;?></td>
			                <td class="target_first_name"><?php echo $first_name;?></td>
			                <td class="target_last_name"><?php echo $last_name;?></td>
			                <td class="target_phone"><?php echo $phone;?></td>
			                <td><span onClick="deleteSubUser(<?php echo $user->ID;?>)" class="delte_sub_user" >Slett</span></td>
			            </tr>
			        	<?php   	

						}
					}	
					?>
		            
		        </tbody>
		    </table>
		    </div>
		</div>
	</div>
	<?php 
	/*foreach ($users_table_data as $users_table_d2) {

		require(__DIR__."/users_edit_modal.php");
	}*/
	?>
	<?php 
	//require(__DIR__."/user_group_modal.php");
	require(__DIR__."/admin_modal.php");
	require(__DIR__."/new_user_modal.php");
	?>
</div>

<script>
jQuery(document).ready(function(){
	jQuery('.main_user_manage').show();
})
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function deleteSubUser(user_id) {

	jQuery(".delete_form").remove();

	if (confirm("Are you sure!")) {
		var f = document.createElement("form");
	    f.setAttribute('class',"delete_form");
	    f.setAttribute('method',"post");
	    f.setAttribute('action',"<?php echo admin_url( 'admin-ajax.php' );?>");

	    var i = document.createElement("input"); //input element, text
	    i.setAttribute('type',"text");
	    i.setAttribute('name',"user_id");
	    i.setAttribute('value',user_id);
	    f.appendChild(i);


	    var a_i = document.createElement("input"); //input element, text
	    a_i.setAttribute('type',"hidden");
	    a_i.setAttribute('name',"action");
	    a_i.setAttribute('value',"remove_subuser");
	    f.appendChild(a_i);
	    jQuery("body").append(f);
	    f.submit();
	}
}

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
</script>