<?php
$get_user_all_listings = Listeo_Core_Users::get_user_all_listings();


$selected_listing = array();
$listing_count = "";
if(isset($_GET['listing_ids']) && $_GET['listing_ids'] != ""){
    $listing_idsss = explode(",",$_GET['listing_ids']);
    $selected_listing = $listing_idsss;
    $listing_count = count($listing_idsss);

}
$active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );
$cr_user = get_current_user_ID();

if($active_group_id != "" && $active_group_id != 0){
        $group_admin = get_group_admin();

        if($group_admin != ""){
            $cr_user = $group_admin;
        }

}
//echo $listing_count; die;
global $wpdb;
$filter_template_table = "filter_template";
$filter_template_sql = "SELECT * from $filter_template_table where template_type = 'listing' AND user_id=".$cr_user;
$filter_template_data = $wpdb->get_results($filter_template_sql);
$search_value = "";
if(isset($_GET["search"])){
    $search_value = $_GET["search"];
}

$hide_clear = true;
if(isset($_GET["search"]) && $_GET["search"] != ""){
     $hide_clear = false;
}
?>

<div class="filter_div_start">
   <!--  <?php if((isset($_GET['selected_template']) && $_GET['selected_template'] != "") || $_GET['filter'] == "true"){ ?>
        <div class="filter_div_inner">
            <button class="show_all_tmpl btn">Nullstill</button>
        </div>
    <?php } ?> -->
	<?php 
	foreach ($filter_template_data as $key => $filter_template_d) { ?>
	<!-- 	<div class="filter_div_inner">
            <?php if(isset($_GET['selected_template']) && $_GET['selected_template'] == $filter_template_d->id){ ?>
			  <div class="edit_template_div" data-template_id="<?php echo $filter_template_d->id;?>"  data-template_name="<?php echo $filter_template_d->name;?>" ><i class="fa-solid fa-pen"></i></div>
            <?php } ?>
			  <button class="template-filter-btn btn <?php if(isset($_GET['selected_template']) && $_GET['selected_template'] == $filter_template_d->id){ echo 'selected_template';}?>" data-template_id="<?php echo $filter_template_d->id;?>"><i class="fa fa-spinner fa-spin" style="display:none"></i> <?php echo $filter_template_d->name;?></button>
            <?php if(isset($_GET['selected_template']) && $_GET['selected_template'] == $filter_template_d->id){ ?>
    			<div class="info_template_div" data-template_id="<?php echo $filter_template_d->id;?>"  data-template_name="<?php echo $filter_template_d->name;?>"  <?php if(isset($_GET['filter']) && $_GET['filter'] == "true"){ }else{ ?>style="display:none"<?php }?>><i class="fa-solid fa-exclamation"></i></div>
            <?php } ?>
		</div> -->
	<?php } ?>
	<!-- <?php if(isset($_GET['listing_ids']) && $_GET['listing_ids'] != "" && isset($_GET['filter']) && $_GET['filter'] == "true"){ ?>
		<div class="filter_div_inner">
			<button class="template-create-btn btn">Opprett ny visning</button>
		</div>
	<?php } ?> -->
	
	<!-- <div class="filter_div_inner search-box-inner main_dropdown  order_num_filter">
		<div class="dropdown">
		  <button class="dropbtn"><span class="filter_text">Filter</span> <span class="count_filter" <?php if($listing_count == ""){ ?>style="display:none"<?php } ?>><b><?php echo $listing_count;?></b></span> <i class="fa fa-chevron-down"></i></button>
		  <div id="listingDropdown" class="dropdown-content">
			<div class="outer-drop-btn">
				
				<div class="filter_text_top">
                    <h3>Filter <i class="fas fa-times"></i></h3>
                    <button class="listing_filter_button">Velg</button>
                </div>    
				<hr class="row-marg">

				<div class="listing_filter">
                    <select class="select2_field listing_filter_select" multiple>
                        <?php 
                        foreach ($get_user_all_listings as $listing_idd) {
                            $listing_data = get_post($listing_idd);
                             $seleted = "";
                            if(in_array($listing_idd,$selected_listing)){
                                $seleted = "selected";
                            }
                        ?>    
                            <option value="<?php echo $listing_data->ID;?>" <?php echo $seleted;?>><?php echo $listing_data->post_title;?></option>
                        <?php } ?>
                    </select>
                    
                </div>
				
			</div>
		  </div>
		</div>
	</div> -->
	<div class="search_div filter_div_inner">
		<form id="my-listings-search-form1" action="">
       <!--  <input type="hidden" name="status" value="<?php echo esc_attr($status); ?>"> -->
            <input type="text" name="search" id="my-listings-search1" placeholder="Søk" value="<?php echo esc_attr($search_value); ?>">
           <!--  <button type="submit"><i class="fa fa-search"></i></button>  -->
            <button type="button" class="my_listings_clear_button2" onclick="window.location.href='/my-listings';" <?php if($hide_clear){ ?> style="display: none;" <?php } ?>> <i class="fa fa-close"></i></button></button>
        </form>
	</div>
</div>

<?php

	global $wpdb;
	$filter_template_table = "filter_template";
	$filter_template_sql = "SELECT * from $filter_template_table where template_type = 'listing' AND user_id=".get_current_user_ID();
	$filter_template_data = $wpdb->get_results($filter_template_sql);

	if(isset($_GET["selected_template"])){
	    $template_selected = $_GET["selected_template"];
	}

	//$template_selected =  get_user_meta(get_current_user_ID(),"listing_template_selected",true);

	$template_action = "save_listing_filter_template";

	$filter_template_type = "listing";

	require_once(get_stylesheet_directory()."/filter-template/modal/template_create_modal.php"); 
	require_once(get_stylesheet_directory()."/filter-template/modal/template_modal.php"); 
	require_once(get_stylesheet_directory()."/filter-template/modal/edit_template_modal.php"); 
?>
<?php if(isset($_GET['filter']) && $_GET['filter'] == "true"){ ?>
	<script type="text/javascript">
		jQuery(".filter-template-hidden-info").show();
	</script>
<?php } ?>
<style type="text/css">
	.template_modal .template-create-btn{
		visibility: hidden;
	}
</style>

<script type="text/javascript">
	    jQuery(document).on("click",".main_dropdown .dropbtn,.main_dropdown i",function(){
			var parent_div = jQuery(this).parent();
			jQuery(".main_dropdown").not(parent_div).find(".dropdown-content").removeClass("show");
			jQuery(this).parent().find(".dropdown-content").toggleClass("show");

		})
		jQuery(document).on('click', function (e) {
			//debugger;
		    if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".select2-dropdown").length === 0  && jQuery(e.target).closest(".dropdown-content").length === 0) {
		        jQuery(".dropdown-content").removeClass("show");
		    }
		});
</script>


<script type="text/javascript">


        let urlParams = new URLSearchParams(window.location.search);
        let filter = urlParams.get('filter');

        if(filter == "true"){
           jQuery(".submit_btn").removeClass("gray_btn");
        }

        function save_filter_template(template_selected,template_name,type = "edit"){
            let formData = new FormData();

            formData.append("listing_ids","<?php echo $_GET['listing_ids'];?>");
            formData.append("listing_template_selected",template_selected);
            formData.append("template_name",template_name);
            formData.append("action","save_listing_filter_template");

            jQuery(".submit_btn").find(".fa-spin").show();



            jQuery.ajax({
              type: "POST",
              url: "<?php echo admin_url( 'admin-ajax.php' );?>",
              data: formData,
              processData: false,
              contentType: false,
              success: function (response) {
                jQuery(".submit_btn").find(".fa-spin").hide();

                  jQuery("#templateModal").hide();

                  jQuery(".template_selected").find("option").removeAttr("selected");

                  jQuery(".submit_btn").addClass("gray_btn");
                  jQuery(".select_template_btn").addClass("gray_btn");

                  var linkk = window.location.href;
                  if(type == "create"){
                    linkk += "&selected_template="+template_selected;
                  }
                  var linkk = linkk.replace("&filter=true","");

                  window.location.href = linkk;

                
              }
            }); 
        }



        jQuery(".save_filter_template").submit(function(e){

                  var template_selected = jQuery(this).find(".template_selected").val();
                  var template_name = jQuery(this).find(".template_name").val();

                  if(template_selected != ""){
                      

                     save_filter_template(template_selected,template_name);




                  }else{

                    alert("Please select template")

                  }

                  /*jQuery.ajax({
                      type: "POST",
                      url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                      data: {action:"save_selected_template","template_selected": this.value},
                      dataType: 'json',
                      success: function (data) {
                         window.location.reload();
                      }
                  });*/

        });

        function template_select(template_selected){

        	var listing_url = window.location.href.split('?')[0];

             



        	jQuery.ajax({
              type: "POST",
              url: "<?php echo admin_url( 'admin-ajax.php' );?>",
              data: {action:"save_listing_selected_template","listing_template_selected": template_selected,"listing_url":listing_url},
              dataType: 'json',
              success: function (data) {
                if(data.redirect_url){
                   window.location.href = data.redirect_url;
                }else{
                    jQuery(".template-filter-btn").find(".fa-spin").hide();
                    jQuery("#templateModal").hide();
                }
              }
            });
        }
               

        jQuery(".select_template_btn").click(function(e){

           var template_selected = jQuery(".template_selected").val();

           //jQuery(".template_submit_flex").find(".right-btn").hide();

           

           template_select(template_selected)

        });
        jQuery(".template-filter-btn").click(function(e){

             jQuery(this).find(".fa-spin").hide();

             if(jQuery(this).hasClass("selected_template")){

                jQuery(this).parent().find('.edit_template_div').click();

             }else{

                var template_selected = jQuery(this).attr("data-template_id");

               //jQuery(".template_submit_flex").find(".right-btn").hide();

               template_select(template_selected)

             }

        	

           
            

        });
        jQuery(".edit_template_div, .info_template_div").click(function(e){


            jQuery("#editTemplateModal").show();
        	var template_selected = jQuery(this).attr("data-template_id");
        	var template_selected_name = jQuery(this).attr("data-template_name");
        	jQuery("#editTemplateModal").find(".template_selected").val(template_selected)
        	jQuery("#editTemplateModal").find(".template_name").val(template_selected_name)
        	jQuery(".template_selected").find("option").removeAttr("selected");
        	jQuery(".template_selected").find("option[value="+template_selected+"]").attr("selected",true);
        	jQuery(".template_selected").find("option[value="+template_selected+"]").prop("selected",true);
        	jQuery(".template_selected").change();
        	jQuery(".submit_btn").removeClass("gray_btn");
        	jQuery(".select_template_btn").removeClass("gray_btn");
        	jQuery(".select_template_btn").hide();

            /*if(template_selected == "<?php echo $_GET['selected_template'];?>"){
                  jQuery(".filter-template-hidden-info").show();
            }else{
                jQuery(".filter-template-hidden-info").hide();
            }*/

            

        });
        jQuery(".template_selected").change(function(e){

            jQuery(".select_template_btn").removeClass("gray_btn");
            //jQuery(".submit_btn").removeClass("gray_btn");

        });
        jQuery(".listing_filter_button").click(function(e){

            let slect_values = [];

           jQuery(".listing_filter_select:first").find("option:selected").each(function(){
               slect_values.push(this.value);
           });

            if(slect_values.length > 0){
                 var selected_template = jQuery(".template_selected").val();

                //debugger;
              slect_values = slect_values.join(",");

              var template_selected_parms = "";

              if(selected_template != ""){
                 template_selected_parms = "&selected_template="+selected_template
              }
              template_selected_parms += "&filter=true";
              let urll = window.location.href.split('?')[0];
              urll = urll.replace("#","");

               window.location.href = urll+"?listing_ids="+slect_values+template_selected_parms;
            }

        });
        select2funclisting();
         function select2funclisting(){
            jQuery(".select2_field").select2({
                            placeholder: 'Velg',
                            closeOnSelect: false
                            /*
                            width: 'resolve',
                            dropdownAutoWidth: 'true',
                            allowClear: 'true'*/
                          });

            changeslectlisting();
          }
        jQuery(".select2_field").change(function(){
            changeslectlisting();
        }) 
        jQuery(".show_all_tmpl").click(function(){
           var listing_url = window.location.href.split('?')[0];
           window.location.href = listing_url;
        })  
        function changeslectlisting(){

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
                 //jQuery(this).parent().find("input").val(data);
              } 
           })  

        }  

        jQuery(".template_form").submit(function(e){

          e.preventDefault();
          jQuery(".template_form").find(".submit_btn").prop("disabled",true);

          var formdata = jQuery(this).serialize();

          formdata = formdata.replace("save_template","save_template_listing")

          jQuery.ajax({
              type: "POST",
              url: "<?php echo admin_url( 'admin-ajax.php' );?>",
              data: formdata,
              dataType: 'json',
              success: function (data) {
                if(data.error == 1){
                   jQuery(".template_form").find(".submit_btn").prop("disabled",false);
                   jQuery(".alert_error_message").show();
                   jQuery(".alert_error_message").html(data.message);

                }else{

                    jQuery(".alert_success_message").show();
                    jQuery(".alert_success_message").html(data.message);

                    save_filter_template(data.data.template_selected,data.data.template_name,"create");

                    /*setTimeout(function(){
                        jQuery(".alert_error_message").hide();
                        jQuery(".alert_error_message").html("");
                        window.location.reload();
                    },4000);*/
                }
                /*setTimeout(function(){
                    jQuery(".alert_error_message").hide();
                    jQuery(".alert_error_message").html("");
                },4000);*/
              }
          });
        })
        jQuery(".delete_template_modal").click(function(e){

        

              var data = {
                  template_selected : jQuery(".template_selected").val(),
                  action : "delete_template_modal"
              }

              jQuery.ajax({
                  type: "POST",
                  url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                  data: data,
                  dataType: 'json',
                  success: function (data) {
                     let urll = window.location.href.split('?')[0];
                     urll = urll.replace("#","");

                     window.location.href = urll;
                  }
              });
        })

</script>