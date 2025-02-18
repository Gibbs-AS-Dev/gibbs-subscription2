<?php


$current_user_id = get_current_user_ID();

/* user groups and users_and_users_groups*/
global $wpdb;
$application_data_table = 'application_data';  // table name
$sql_application_data = "select *  from `$application_data_table` where user_id = $current_user_id";
$application_data = $wpdb->get_results($sql_application_data);


//echo "<pre>"; print_r($user_management_group_id); die;
/* display row checkbox */
?>

<div class="container main_user_manage form_list" style="display: none;">
	<div class="main_user_manage_row">
			<div class="row-data-filter user_cols_main">



				<div class="listing-outer" style="padding-right: 22px;">
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

				
				

				
				
		</div>
		<div class="user_table_content" >
			<div class="table_div">
				<table class="table user-table table-hover align-items-center datatable" style="width:100%;">
		        <thead>
		            <tr>
		                <th class="group"><?php echo __("Hvor","gibbs");?></th>
		                <th class="season"><?php echo __("Sesong","gibbs");?></th>
		                <th class="deadline"><?php echo __("Frist","gibbs");?></th>
		                <th class="status"><?php echo __("Status","gibbs");?></th>
		                <th><?php echo __("Handling","gibbs");?></th>
		                
		            </tr>
		        </thead>
		        <tbody>
		        	<?php
		        	foreach ($application_data  as $key => $application) { 
                        
					    $users_groups = $wpdb->prefix . 'users_groups';  // table name
						$sql_user_group = "select name  from `$users_groups` where id = ".$application->group_id;
						$user_group_data = $wpdb->get_row($sql_user_group);

						$group_name = $user_group_data->name; 
                        
                        $seasons_table = 'seasons';  // table name
						$sql_season = "select name, season_end from `$seasons_table` where id = ".$application->season_id;
						$season_data = $wpdb->get_row($sql_season);

						$season_name = $season_data->name; 

		        		?>
			            <tr>
			                <td class="group"><?php echo $group_name;?></td>
			                <td class="season"><?php echo $season_name;?></td>
			                <td class="deadline"><?php echo $application->deadline;?></td>
			                <td class="status ap_status"><?php if($application->status == "1"){ ?><span class='sent-status'>Sent</span><?php }else{ ?><span class='draft-status'>Utkast</span><?php } ?></td>
			                <td>
			                	<div class="search-box-inner action-modal1 action_btns">
			                        <div class="dropdown">
			                          <button class="dropbtn">  <i class="fa-solid fa-ellipsis"></i> </button>
			                          <div id="listingDropdown" class="dropdown-content">
			                              <div class="outer-actions1">
										  <?php if($application->status == "1"){ ?>
												<form action="<?php echo home_url();?>/wp-json/v1/generateapp" method="get" target="_blank">
                                                    <!-- <input type="hidden" value="generate_pdf_application" name="action"> -->
													<input type="hidden"  name="application_id" value="<?php echo $application->id;?>">
													<p class="submit_appp" onClick="this.parentElement.submit()" type="submit">Kvittering på søknad <i class="fa fa-file"></i></p>
												</form>
			                              	   <!-- <p data-link="<?php echo $application->pdf_link;?>" class="open_form_link" data-download="true">Last ned PDF <i class="fa fa-file-pdf"></i></p> -->
			                              	<?php } ?>
			                              	<p data-link="<?php echo home_url();?>/application?application_id=<?php echo $application->id;?>" class="open_form_link">Rediger <i class="fa fa-edit"></i></p>
			                              	<p data-id="<?php echo $application->id;?>" class="delete_form">Slett <i class="fa fa-trash"></i></p>
			                              </div>
			                          </div>
			                        </div>
			                    </div>
			                </td>
			            </tr>
			        <?php } ?>    
		            
		        </tbody>
		    </table>
		    </div>
		</div>
	</div>
	
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



jQuery(document).ready( function () {
	const dataJson = {
	
                   // "pageLength" : 10,
                    "paging" : false,
                    "info" : false,
			    	"language": {
			        "sProcessing":    "Behandler...",
			        "sLengthMenu":    "Vis _MENU_ poster",
			        "sZeroRecords":   "Ingen resultater",
			        "sEmptyTable":    "Du har ikke sendt søknad enda",
			        "sInfo":          "Viser _START_ til _END_ av _TOTAL_ treff",
			        "sInfoEmpty":     "Viser poster fra 0 til 0 av totalt 0 poster",
			        "sInfoFiltered":  "(filtrerer totalt _MAX_ poster)",
			        "sInfoPostFix":   "",
			        "sSearch":        "Søk:",
			        "sUrl":           "",
			        "sInfoThousands":  ",",
			        "sLoadingRecords": "Laster...",
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
			    },
			    "bSort": true,
			    "rowReorder": {
		            "selector": 'td:nth-child(2)'
		        },
			    "responsive": true,
			    "columnDefs": [ { 'targets': [2], // column index (start from 0)
							        'orderable': true, // set orderable false for selected columns
							    },
							    { responsivePriority: 20, targets: 4 },
			                ],
		    };
    const oTable = jQuery(".datatable").DataTable(dataJson);
    jQuery(".search_in").keyup(function(){
		 oTable.search( jQuery(this).val() ).draw();
	})
	jQuery(document).on("click",".search-box-inner .dropbtn,.search-box-inner i",function(){
		var parent_div = jQuery(this).parent();
		jQuery(".search-box-inner").not(parent_div).find(".dropdown-content").removeClass("show");
		jQuery(this).parent().find(".dropdown-content").toggleClass("show");
		jQuery(this).parent().find("select").click();
		jQuery(this).parent().find(".search_in").focus();

	})
	 jQuery(document).on('click', function (e) {
        if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".search-box-inner").length === 0 && jQuery(e.target).closest(".dropdown-content").length === 0) {
            jQuery(".dropdown-content").removeClass("show");
        }
    });

	jQuery(".open_form_link").on("click",function(){
		var linkk = jQuery(this).attr("data-link");

		if(jQuery(this).attr("data-download") != undefined){
             window.open(linkk , '_blank');
		}else{
			window.location.href = linkk;
		}
		
	})

	    jQuery(document).on("click",".delete_form",function(){
           
            if(confirm("Are you sure")){

                let datas = {
                   "action" : "delete_formm",
                   "application_id" : jQuery(this).attr("data-id"),
                }  
                jQuery.ajax({
                          type: "POST",
                          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                          data: datas,
                          dataType: "json",
                          success: function(resultData){

                            if(resultData.success == true){
                              window.location.reload();
                            }else{
                               
                            }

                             //  jQuery(".main_get_day_"+application_id).append(resultData.content);
                          }
                    });
            }    
        })
} );
</script>
