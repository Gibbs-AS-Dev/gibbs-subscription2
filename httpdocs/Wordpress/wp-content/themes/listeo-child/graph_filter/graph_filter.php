<?php
global $wp;
$group_admin = get_group_admin();

if($group_admin != ""){
    $cr_cuser = $group_admin;
}else{
    $cr_cuser = get_current_user_id();
}

$listings = gibbs_owner_listing_func($cr_cuser);

array_sort_by_column_data($listings, 'post_title');


$customers = gibbs_owner_customer_func($cr_cuser);

array_sort_by_column_data($customers, 'display_name');
$statuses = array("paid"=>"Betalt","waiting"=>"Venter","cancelled"=>"Avslått","expired"=>"Utgått");
//echo "<pre>"; print_r($customers); die;
?>

<div class="filter_div">
	<form method="get" action="">
		<div class="row">
			<div class="col-md-2">
				<div class="listing_div">
					<label class="">Annonse</label>
					<input type="hidden" name="listing_id" value="<?php echo $_GET['listing_id'];?>">
					<select class="filter_in filter_select2_field" multiple>
						<option value="">Alle annonser</option>
						<?php foreach ($listings as $key => $listing) { 

							$listing_ids = explode(",", $_GET['listing_id']);

							?>
							<option value="<?php echo $listing['ID'];?>" <?php if(in_array($listing['ID'],$listing_ids)){ echo 'selected';}?>><?php echo $listing['post_title'];?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="listing_div">
					<label class="">Kunde</label>
					<input type="hidden" name="customer" value="<?php echo $_GET['customer'];?>">
					<select class="filter_in filter_select2_field" multiple>
						<option value="">Alle kunder</option>
						<?php foreach ($customers as $key => $customer) { 
							$customer_ids = explode(",", $_GET['customer']);
							?>
							<option value="<?php echo $customer['ID'];?>" <?php if(in_array($customer['ID'],$customer_ids)){  echo 'selected';}?>><?php echo $customer['display_name'];?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="listing_div">
					<label class="">Dato</label>
					<input class="filter_in filter_date" type="text" readonly="" <?php if($_GET["start_date"] != "" && $_GET["end_date"] != ""){ ?> placeholder='<?php echo $_GET["start_date"];?> - <?php echo $_GET["end_date"];?>' <?php }else{ ?> placeholder="Alle" <?php } ?>>
					<input class="filter_in filter_start_date" type="hidden" name="start_date" <?php if($_GET["start_date"] != ""){ ?> value='<?php echo $_GET["start_date"];?>' <?php } ?>>
					<input class="filter_in filter_end_date" type="hidden" name="end_date" <?php if($_GET["end_date"] != ""){ ?> value='<?php echo $_GET["end_date"];?>' <?php } ?>>
				</div>
			</div>
			<div class="col-md-2">
				<div class="listing_div">
					<label class="">Opprettet dato</label>
					<input class="filter_in filter_created_date" type="text" readonly="" <?php if($_GET["created_start_date"] != "" && $_GET["created_end_date"] != ""){ ?> placeholder='<?php echo $_GET["created_start_date"];?> - <?php echo $_GET["created_end_date"];?>' <?php }else{ ?> placeholder="Alle" <?php } ?>>
					<input class="filter_in filter_created_start_date" type="hidden" name="created_start_date" <?php if($_GET["created_start_date"] != ""){ ?> value='<?php echo $_GET["created_start_date"];?>' <?php } ?>>
					<input class="filter_in filter_created_end_date" type="hidden" name="created_end_date" <?php if($_GET["created_end_date"] != ""){ ?> value='<?php echo $_GET["created_end_date"];?>' <?php } ?>>
				</div>
			</div>
			<div class="col-md-1">
				<div class="listing_div">
					<label class="">Status</label>
					<select class="filter_in" name="status">
						<?php foreach ($statuses as $key_status => $status) { ?>
							<option value="<?php echo $key_status;?>" <?php if($_GET["status"] == $key_status){ echo 'selected';}?>><?php echo $status;?></option>
						<?php } ?>
						<option value="">Alle</option>
					</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="button_div">
					<label style="visibility: hidden;">..</label>
					<div class="filter_btns">
						<button class="btn btn-primary btn_bottom" type="submit">
							<span class="in-text">Bruk</span>
				    		<span class="loader-div"><span class="loader"></span></span>
						</button>
				    	<button class="btn btn-primary clear_filter btn_bottom" type="button" data-link="<?php echo home_url( $wp->request );?>">
				    		<span class="in-text">Tilbakestill filter</span>
				    		<span class="loader-div"><span class="loader"></span></span>
				    	</button>
					</div>
				</div>
			</div>
		</div>
	</form>	
</div>

<script type="text/javascript">
	let start_date = new Date();
	let end_date = new Date();
	let created_start_date = new Date();
	let created_end_date = new Date();
	if(('<?php echo $_GET["start_date"];?>' != "") && ('<?php echo $_GET["end_date"];?>' != "")){
       start_date = new Date("<?php echo $_GET["start_date"];?>");
       end_date = new Date("<?php echo $_GET["end_date"];?>");
	}
	if(('<?php echo $_GET["created_start_date"];?>' != "") && ('<?php echo $_GET["created_end_date"];?>' != "")){
		created_start_date = new Date("<?php echo $_GET["created_start_date"];?>");
		created_end_date = new Date("<?php echo $_GET["created_end_date"];?>");
	}
	jQuery(document).ready(function(){
	    jQuery(".filter_date").daterangepicker({
              "opens": "right",
              autoUpdateInput: false,
              startDate: start_date,
              endDate: end_date,
              // checking attribute listing type and set type of calendar
              singleDatePicker: false, 
              timePicker: false,
              locale: {
                  format: wordpress_date_format.date,
                  "firstDay": parseInt(wordpress_date_format.day),
                  "applyLabel"  : listeo_core.applyLabel,
                      "cancelLabel" : listeo_core.cancelLabel,
                      "fromLabel"   : listeo_core.fromLabel,
                      "toLabel"   : listeo_core.toLabel,
                      "customRangeLabel": listeo_core.customRangeLabel,
                      "daysOfWeek": [
                          listeo_core.day_short_su,
                          listeo_core.day_short_mo,
                          listeo_core.day_short_tu,
                          listeo_core.day_short_we,
                          listeo_core.day_short_th,
                          listeo_core.day_short_fr,
                          listeo_core.day_short_sa
                      ],
                      "monthNames": [
                          listeo_core.january,
                          listeo_core.february,
                          listeo_core.march,
                          listeo_core.april,
                          listeo_core.may,
                          listeo_core.june,
                          listeo_core.july,
                          listeo_core.august,
                          listeo_core.september,
                          listeo_core.october,
                          listeo_core.november,
                          listeo_core.december,
                      ],
                    
                },
          });
          jQuery(".filter_date").on('apply.daterangepicker', function (ev, picker) {
          	  let start_date = moment( picker.startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
              let end_date = moment( picker.endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

              ev.currentTarget.value = start_date+" - "+end_date;

              jQuery(".filter_start_date").val(start_date)
              jQuery(".filter_end_date").val(end_date)
          });

		  jQuery(".filter_created_date").daterangepicker({
              "opens": "right",
              autoUpdateInput: false,
              startDate: start_date,
              endDate: end_date,
              // checking attribute listing type and set type of calendar
              singleDatePicker: false, 
              timePicker: false,
              locale: {
                  format: wordpress_date_format.date,
                  "firstDay": parseInt(wordpress_date_format.day),
                  "applyLabel"  : listeo_core.applyLabel,
                      "cancelLabel" : listeo_core.cancelLabel,
                      "fromLabel"   : listeo_core.fromLabel,
                      "toLabel"   : listeo_core.toLabel,
                      "customRangeLabel": listeo_core.customRangeLabel,
                      "daysOfWeek": [
                          listeo_core.day_short_su,
                          listeo_core.day_short_mo,
                          listeo_core.day_short_tu,
                          listeo_core.day_short_we,
                          listeo_core.day_short_th,
                          listeo_core.day_short_fr,
                          listeo_core.day_short_sa
                      ],
                      "monthNames": [
                          listeo_core.january,
                          listeo_core.february,
                          listeo_core.march,
                          listeo_core.april,
                          listeo_core.may,
                          listeo_core.june,
                          listeo_core.july,
                          listeo_core.august,
                          listeo_core.september,
                          listeo_core.october,
                          listeo_core.november,
                          listeo_core.december,
                      ],
                    
                },
          });
          jQuery(".filter_created_date").on('apply.daterangepicker', function (ev, picker) {
          	  let created_start_date = moment( picker.startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
              let created_end_date = moment( picker.endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

              ev.currentTarget.value = created_start_date+" - "+created_end_date;

              jQuery(".filter_created_start_date").val(created_start_date)
              jQuery(".filter_created_end_date").val(created_end_date)
          });
          jQuery('.clear_filter').on("click",function(){
          	 var data_link = jQuery(this).attr("data-link");
          	 window.location.href = data_link;
          })
          jQuery('.btn_bottom').on("click",function(){
          	 jQuery(this).find(".loader-div").show();
          })

          function select2func(){
		    jQuery(".filter_select2_field").select2({
		                    placeholder: 'Velg',
		                    closeOnSelect: false,
		                    /*
		                    width: 'resolve',
		                    dropdownAutoWidth: 'true',
		                    allowClear: 'true'*/
		                  });

		    changeslect();
		  }
		  function changeslect(){

		        jQuery("body").find("select").each(function(){

		          if(this.multiple == true){


		             let optionss = [];

		             jQuery(this).find("option:selected").each(function(){
		                optionss.push(this.value);
		             })

		              /*if(optionss > 0 ){

		                jQuery(this).parent().find(".select2-container").find(".selection").html("Selected ("+optionss.length+")");
		              }else{
		                jQuery(this).parent().find(".select2-selection--multiple").html("");
		              }*/
		              var uldiv = jQuery(this).siblings('span.select2').find('ul')
		              var count = jQuery(this).select2('data').length
		              if(count==0){
		                uldiv.html("")
		                jQuery(this).siblings('span.select2').find(".select2-search").show();
		              }
		              else{
		                jQuery(this).siblings('span.select2').find(".select2-search").hide();
		                uldiv.html("<li>Valgt ("+count+")</li>")
		              }

		             let data = optionss.join(",");
		             jQuery(this).parent().find("input").val(data);
		          } 
		      })  

		    }

		    select2func();

		    jQuery(document).on("change","select",function(){
		       changeslect();
		    });
		});		
</script>