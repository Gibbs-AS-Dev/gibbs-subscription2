<?php
$wpml_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
$sort = "1";
if(isset($page_type)){
	if($page_type == "buyer"){
		$sort = "2";
	}
}
$current_user = wp_get_current_user();

$active_group_id = get_user_meta( $current_user->ID, '_gibbs_active_group_id',true );

$group_id = "";
if($active_group_id != ""){
	$group_id = $active_group_id;
}else{
	$group_id = "0";
}
?>



<script type="text/javascript">
	
	jQuery(document).on("click",".open_link",function(){
		var hreff = window.location.href;
		var this_link = jQuery(this).data("link");
	    if(hreff.includes("?")){
	    	hreff = hreff.split("?")[0];
	    }
	    hreff = hreff.replace("#","");
		


		var linkk = hreff+this_link;

		if(jQuery(this).attr("new_tab")  == "true"){
            window.open(linkk, '_blank')
		}else{
            window.location.href = linkk;
		}
		
	})
	jQuery(document).on("click",".open_link_booking",function(){
		var hreff = window.location.href;
		var this_link = jQuery(this).data("link");
	    if(hreff.includes("?")){
	    	hreff = hreff.split("?")[0];
	    }
	    hreff = hreff.replace("#","");
		


		var linkk = hreff+this_link;

		if(jQuery(this).attr("new_tab")  == "true"){
            window.open(linkk, '_blank')
		}else{
            window.location.href = linkk;
		}
		
	})
	jQuery(document).on("click","#generatePDF",function () {
		html2canvas(document.getElementById("thankyou-pdf"), {
		  onrendered: function(canvas) {
		    var imgData = canvas.toDataURL('image/png');
		    var doc = new jsPDF('p', 'px', [700, 700]);
		    
		    doc.addImage(imgData, 'PNG', 20, 20);
		    doc.save('Ordre-kvittering.pdf');
		  }
		});
	});
	jQuery(document).on("click",".open_all_link",function(){

		var this_link = jQuery(this).data("link");

		if(jQuery(this).attr("new_tab")  == "true"){
            window.open(this_link, '_blank')
		}else{
            window.location.href = this_link;
		}
	})
</script>
<script type="text/javascript">
	var customerr = document.getElementById("customer");
	if(customerr && customerr != undefined){
		jQuery('#customer').multiselect({
		    columns: 1,
		    search:true,
		    placeholder: 'Velg',
		});
	}
	var listingg = document.getElementById("listing");
	if(listingg && listingg != undefined){
		jQuery('#listing').multiselect({
		    columns: 1,
		    search:true,
		    placeholder: 'Velg',
		});
	}
</script>
<script type="text/javascript">

	let totalCount = 10;

     let responsivePriority_len = 2;

	if(jQuery(".booking_datatable").find("tr").find("th").length  > 0){
       responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 1;
	}
const dataJson = {
	
                   // "pageLength" : 10,
                    "paging" : false,
                    "info" : false,
			    	"language": {
			        "sProcessing":    "behandling...",
			        "sLengthMenu":    "Vis _MENU_ poster",
			        "sZeroRecords":   "Ingen resultater",
			        "sEmptyTable":    "Ingen data tilgjengelig i denne tabellen",
			        "sInfo":          "Viser _START_ til _END_ av _TOTAL_ bookinger",
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
			    },
			    "bSort": true,
			    "rowReorder": {
		            "selector": 'td:nth-child(2)'
		        },
			    "responsive": true,
			    "aaSorting": [],
			    "columnDefs": [ { 'targets': [3], 
							        'orderable': true, 
							    },
							    { responsivePriority: 20, targets: 0 },
								{ responsivePriority: 21, targets: responsivePriority_len },
			                ],
		    };
jQuery(document).ready( function () {

	jQuery('.booking_main').show();

	
    const oTable1 = jQuery(".booking_datatable").DataTable(dataJson);
    jQuery(".search_in").keyup(function(){
		manage_filter();
	})
} );


jQuery(".booking_main").find(".tablinks").click(function(){
	var data_link  = jQuery(this).data("id");
	var page_type  = jQuery(this).data("page_type");
	jQuery(".tab-ul").find(".active").removeClass("active");
	jQuery(this).addClass("active");

	/*if(jQuery(this).data("id") == "approved"){

	}*/
	manage_filter();
	jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {action:"booking_tab","booking_tab":data_link,"page_type":page_type},
        success: function (data) {
          
          //window.location.reload();
         
        }
    });
})
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */


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

jQuery(".booking_main").find(".dropdown-content a").click(function(){
   jQuery(this).toggleClass("active");
   manage_filter();
})

jQuery(document).on("click",".close_filter_btn", function(){
	jQuery(".dropdown-content").removeClass("show");
})

jQuery(document).on('click', function (e) {
    if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".search-box-inner").length === 0 && jQuery(e.target).closest(".dropdown-content").length === 0) {
        jQuery(".dropdown-content").removeClass("show");
    }
});
jQuery(document).on("click",".search-box-inner .dropbtn,.search-box-inner i",function(){
	var parent_div = jQuery(this).parent();
	//jQuery(".search-box-inner").not(parent_div).find(".dropdown-content").removeClass("show");
	jQuery(this).parent().find(".dropdown-content").toggleClass("show");
	jQuery(this).parent().find("select").click();
	jQuery(this).parent().find(".search_in").focus();

})

jQuery(".daterange_pick").find("span").click(function(){
	jQuery(this).parent().find("#booking-date-range-enabler2").click();
})
jQuery(".daterange_pick2").find("span").click(function(){
	jQuery(this).parent().find("#booking-created-date-range-enabler2").click();
})

function manage_filter(date_close = "",export_booking_csv="", page = 1,date_close2 = ""){

	jQuery(".booking_datatable_loader").remove();

	jQuery(".booking_datatable").addClass("loading_class");

	if(export_booking_csv != ""){

	   jQuery(".booking_datatable").append(`<div class="booking_datatable_loader">
	                                        <div class="loader-div">
											   <div class="loader"></div>
											   <p>Det kan ta litt tid å laste ned alle boookinger. Kanskje ta en kaffe pause? 😊 </p>
											</div>
											
										</div>`);
	}									

	//debugger;


	const listing_ids = [];
	const customer_ids = [];
	const group_ids = [];
    let _count_listing_ids = 0;
	jQuery("#listing").find("option").each(function(){
		
		if(this.selected == true){
			_count_listing_ids++;
			listing_ids.push(this.value);
			jQuery(".listing_div").find(".count_filter").html("("+_count_listing_ids+") <i class='fa fa-times close_filter listing_clear'>");
			jQuery(".listing_div").find(".fa-chevron-down").hide();
		}
	})
	if(_count_listing_ids == 0){
		jQuery(".listing_div").find(".count_filter").html("");
		jQuery(".listing_div").find(".fa-chevron-down").show();
	}

	let _count_customer_ids = 0;
	jQuery("#customer").find("option").each(function(){
		
		if(this.selected == true){
			_count_customer_ids++;
			customer_ids.push(this.value);
			jQuery(".customer_div").find(".count_filter").html("("+_count_customer_ids+") <i class='fa fa-times close_filter customer_clear'>");
			jQuery(".customer_div").find(".fa-chevron-down").hide();
		}
	})
	if(_count_customer_ids == 0){
		jQuery(".customer_div").find(".count_filter").html("");
		jQuery(".customer_div").find(".fa-chevron-down").show();
	}


	var group_id = "<?php echo $group_id;?>";

	$show_date = true;

	if(jQuery(".date_close")[0] != undefined && jQuery(".date_close")[0].style.display != undefined && jQuery(".date_close")[0].style.display == "none"){

		$show_date = false;

	}


	if(jQuery('#booking-date-range').data('daterangepicker') != undefined && jQuery("#booking-date-range-enabler2").attr("show_date") != "false" && $show_date != false){

		var startDataSql = moment( jQuery('#booking-date-range').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
	   var endDataSql = moment( jQuery('#booking-date-range').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

	}else{
		var startDataSql = "";
	    var endDataSql = "";

	}
	$show_date2 = true;

	if(jQuery(".date_close2")[0] != undefined && jQuery(".date_close2")[0].style.display != undefined && jQuery(".date_close2")[0].style.display == "none"){

		$show_date2 = false;

	}
	if(jQuery('#booking-created-date-range').data('daterangepicker') != undefined && jQuery("#booking-created-date-range-enabler2").attr("show_date2") != "false" && $show_date2 != false){

		var startCreatedDataSql = moment( jQuery('#booking-created-date-range').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
	   var endCreatedDataSql = moment( jQuery('#booking-created-date-range').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

	}else{
		var startCreatedDataSql = "";
	    var endCreatedDataSql = "";

	}



	

	var search_text = jQuery(".search_in").val();

	if(search_text != ""){
		    jQuery(".search_div").find(".filter_text").hide();
		    jQuery(".search_div").find(".count_filter").html(" "+search_text+" <i class='fa fa-times close_filter search_clear'>");
			jQuery(".search_div").find(".fa-chevron-down").hide();
	}else{
		    jQuery(".search_div").find(".filter_text").show();
		    jQuery(".search_div").find(".count_filter").html("");
			jQuery(".search_div").find(".fa-chevron-down").show();

	}

	var status = jQuery(".tab-ul").find(".active").data("id");

	var order_number_checkbox = jQuery("#order_number_checkbox:checked").val();

	if(order_number_checkbox != "" && order_number_checkbox != undefined){
		    jQuery(".order_num_filter").find(".count_filter").html("(1) <i class='fa fa-times close_filter filterr_clear'>");
			jQuery(".order_num_filter").find(".fa-chevron-down").hide();
	}else{
		    jQuery(".order_num_filter").find(".count_filter").html("");
			jQuery(".order_num_filter").find(".fa-chevron-down").show();

	}

	const sta = status;



	jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {action:"gibbs_booking_data","listing_ids":listing_ids,"customer_ids":customer_ids,"group_id":group_id,"startDataSql":startDataSql,"endDataSql":endDataSql,"startCreatedDataSql":startCreatedDataSql,"endCreatedDataSql":endCreatedDataSql,"search_text":search_text,"status":status,"order_number_checkbox":order_number_checkbox,date_close:date_close,date_close2:date_close2,"export_booking_csv":export_booking_csv,"page_type":"<?php echo $page_type;?>","page": page, "totalCount": totalCount},
        dataType: 'json',
        success: function (data) {

        	if(data.export_csv == "true"){
        		manage_export(data.booking_data,startDataSql,endDataSql,data.csv_file_name);
        	}else{


	        	jQuery(".booking_datatable").removeClass("loading_class");
				jQuery(".booking_datatable_loader").remove();


	        	jQuery('.booking_datatable').DataTable().destroy();
	        	jQuery('.booking_table_main').html(data.content);
	        	jQuery('.bulk_action').html(data.bulk_action);

	        	//jQuery(".tab-ul").find(".active").find("span").html("("+data.count+")");

	        	jQuery(".tab-ul").find(".count_waiting").html("("+data.count_waiting+")");

	        	/*jQuery(".tab-ul").find(".count_waiting").html("("+data.count_waiting+")");
	        	jQuery(".tab-ul").find(".count_approved").html("("+data.count_approved+")");
	        	jQuery(".tab-ul").find(".count_expired").html("("+data.count_expired+")");
	        	jQuery(".tab-ul").find(".count_all").html("("+data.count_all+")");
	        	jQuery(".tab-ul").find(".count_invoice").html("("+data.count_invoice+")");
	        	jQuery(".tab-ul").find(".count_invoice_sent").html("("+data.count_invoice_sent+")");
	        	jQuery(".tab-ul").find(".count_paid").html("("+data.count_paid+")");*/
	        	
	        	if(dataJson.columnDefs != undefined && dataJson.columnDefs[2] != undefined){
	        		responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 1;
	        		dataJson.columnDefs[2].targets = responsivePriority_len;
	        	}
	        	

	        	/*if(jQuery(".search_in").val() != ""){
	        		jQuery('.booking_datatable').DataTable(dataJson).search( jQuery(".search_in").val() ).draw();
	        	}else{
	        		jQuery('.booking_datatable').DataTable(dataJson).draw();
	        	}*/
	        	jQuery('.booking_datatable').DataTable(dataJson).draw();

        	}

        	

        	

         
        }
    });

}
jQuery(".booking_datatable").addClass("loading_class");

jQuery(document).ready(function(){
	manage_filter();
})


jQuery(document).on('click','.booking_approved',function(e) {
    e.preventDefault();


    if (window.confirm("Er du sikker?")) {

    	jQuery(".booking_datatable").addClass("loading_class");

    	let booking_id = jQuery(this).attr("booking_id");
    	let status = "confirmed";
    	let ajax_data = {
            'action': 'listeo_bookings_manage',
            'booking_id' : booking_id,
            'status' : status,
            'owner_action' : true,
            //'nonce': nonce
        };
        jQuery.ajax({
            type: 'POST', 
            dataType: 'json',
            url: "<?php echo admin_url( 'admin-ajax.php' );?>",
            data: ajax_data,
            success: function(data){
               // window.location.reload();
                change_status_first_event(booking_id,status);
            }
        });
    }
});
function change_status_first_event(booking_id,status,fixed = "", order_id = ""){
	let ajax_data2 = {
            'action': 'change_status_first_event',
            'booking_id' : booking_id,
            'status' : status,
			'fixed' : fixed,
			'order_id' : order_id
            //'nonce': nonce
        };
	jQuery.ajax({
            type: 'POST', 
            dataType: 'json',
            url: "<?php echo admin_url( 'admin-ajax.php' );?>",
            data: ajax_data2,
            success: function(data){
                window.location.reload();
                //change_status_first_event();
            }
    });

}
jQuery(document).on('click','.booking_rejected',function(e) {
    e.preventDefault();


    if (window.confirm("Er du sikker?")) {

    	jQuery(".booking_datatable").addClass("loading_class");

    	var booking_id = jQuery(this).attr("booking_id");
    	var status = "cancelled";
    	var ajax_data = {
            'action': 'listeo_bookings_manage',
            'booking_id' : booking_id,
            'status' : status,
            //'nonce': nonce
        };
        jQuery.ajax({
            type: 'POST', 
            dataType: 'json',
            url: "<?php echo admin_url( 'admin-ajax.php' );?>",
            data: ajax_data,
            success: function(data){
                 window.location.reload();
            }
        });
    }
});

jQuery(document).on('change','#usergroup',function(e) {


    var ajax_data = {
        'action': 'booking_user_group_selected_id',
        'booking_user_group_selected_id' : jQuery(this).val(),
        //'nonce': nonce
    };

    jQuery.ajax({
        type: 'POST', 
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: ajax_data,
        success: function(data){
             //window.location.reload();
        }
    });

});
jQuery(".date_close").click(function(){
	jQuery('#booking-date-range').data('daterangepicker').setStartDate(undefined);
	jQuery('#booking-date-range').data('daterangepicker').setEndDate(undefined);
    jQuery("#booking-date-range-enabler2").show();
    jQuery("#booking-date-range-enabler2").attr("show_date","false");
    jQuery("#booking-date-range").hide();
    jQuery("#booking-date-range").find(".filter_text").html("<?php esc_html_e('Date','listeo_core'); ?>");
    setTimeout(function(){
    	jQuery("body").find(".daterangepicker").hide();
    	jQuery(".search_in").click();
    	jQuery(".date_close").hide();
    },100)



    var date_close = "true";
    
    manage_filter(date_close);

})
jQuery(".date_close2").click(function(){
	jQuery('#booking-created-date-range').data('daterangepicker').setStartDate(undefined);
	jQuery('#booking-created-date-range').data('daterangepicker').setEndDate(undefined);
    jQuery("#booking-created-date-range-enabler2").show();
    jQuery("#booking-created-date-range-enabler2").attr("show_date2","false");
    jQuery("#booking-created-date-range").hide();
    jQuery("#booking-created-date-range").find(".filter_text").html("<?php esc_html_e('Date','listeo_core'); ?>");
    setTimeout(function(){
    	jQuery("body").find(".daterangepicker").hide();
    	jQuery(".search_in").click();
    	jQuery(".date_close2").hide();
    },100)



    var date_close2 = "true";
    
    manage_filter("","",1,date_close2);

})


jQuery(document).on('DOMSubtreeModified',"#booking-date-range .user_icon", function(){
  if(jQuery(this).text() != ""){
  	if( jQuery(this).text() != "Velg Dato"){

  		jQuery(".date_close").show();
  		//manage_filter();

  	}
  }
});

jQuery(document).on("click", function(e){
  if(jQuery("#booking-date-range .user_icon").text() != ""){
  	if( jQuery("#booking-date-range .user_icon").text() != "Velg Dato"){

  		jQuery(".date_close").show();
  		//manage_filter();

  	}else{
        jQuery(".date_close").hide();
  	}
  }


});

jQuery(document).on("click", ".message_modal", function(e){
  
   if(jQuery(e.target).closest(".modal-content").length == 0){
   	   jQuery(".message_modal").hide();
   }
  
});
/*jQuery( '#booking-date-range' ).on( 'apply.daterangepicker', function(e){
	jQuery(".date_close").show();
	jQuery('#booking-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	manage_filter();
});*/
function cb2(start, end) {
	jQuery(".date_close").show();
	jQuery("#booking-date-range-enabler2").removeAttr("show_date");
    jQuery('#booking-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    manage_filter();
}

    jQuery('#booking-date-range-enabler2').on('click',function(e){
    	e.preventDefault();
    	//debugger;
    	jQuery(this).hide();
    	//cb2(start, end);
	    jQuery("#booking-date-range").show().daterangepicker({
	    	"opens": "left",
		    "autoUpdateInput": false,
		    "alwaysShowCalendars": true,
	        ranges: {
	           'Idag': [moment(), moment()],
	           'Igår': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Siste 7 dager': [moment().subtract(6, 'days'), moment()],
	           'Siste 30 dager': [moment().subtract(29, 'days'), moment()],
	           'Nåværende måned': [moment().startOf('month'), moment().endOf('month')],
	           'Forrige måned': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			locale: {
				format: wordpress_date_format.date,
				"firstDay": parseInt(wordpress_date_format.day),
				"applyLabel"	: listeo_core.applyLabel,
		        "cancelLabel"	: listeo_core.cancelLabel,
		        "fromLabel"		: listeo_core.fromLabel,
		        "toLabel"		: listeo_core.toLabel,
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
		  	}
	    }, cb2).trigger('click');
	   // cb2(start, end);
    })
	function cb3(start, end) {
		jQuery(".date_close2").show();
		jQuery("#booking-created-date-range-enabler2").removeAttr("show_date2");
		jQuery('#booking-created-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		manage_filter();
	}
	jQuery('#booking-created-date-range-enabler2').on('click',function(e){
    	e.preventDefault();
    	//debugger;
    	jQuery(this).hide();
    	//cb2(start, end);
	    jQuery("#booking-created-date-range").show().daterangepicker({
	    	"opens": "left",
		    "autoUpdateInput": false,
		    "alwaysShowCalendars": true,
	        ranges: {
	           'Idag': [moment(), moment()],
	           'Igår': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Siste 7 dager': [moment().subtract(6, 'days'), moment()],
	           'Siste 30 dager': [moment().subtract(29, 'days'), moment()],
	           'Nåværende måned': [moment().startOf('month'), moment().endOf('month')],
	           'Forrige måned': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			locale: {
				format: wordpress_date_format.date,
				"firstDay": parseInt(wordpress_date_format.day),
				"applyLabel"	: listeo_core.applyLabel,
		        "cancelLabel"	: listeo_core.cancelLabel,
		        "fromLabel"		: listeo_core.fromLabel,
		        "toLabel"		: listeo_core.toLabel,
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
		  	}
	    }, cb3).trigger('click');
	   // cb2(start, end);
    })

    jQuery(document).on("click",".listing_clear",function(){
    	jQuery("#listing").find("option").removeAttr("selected");
    	jQuery("#listing").find("option").prop("selected",false);
    	jQuery("#listing").multiselect('refresh');
    	jQuery("#listing").multiselect('reload');
    	manage_filter();
    })
    jQuery(document).on("click",".customer_clear",function(){
    	jQuery("#customer").find("option").removeAttr("selected");
    	jQuery("#customer").find("option").prop("selected",false);
    	jQuery("#customer").multiselect('refresh');
    	jQuery("#customer").multiselect('reload');
    	manage_filter();
    })
    jQuery(document).on("click",".search_clear",function(){
    	jQuery(".search_in").val("");
    	manage_filter();
    })
    jQuery(document).on("click",".filterr_clear",function(){
    	jQuery("#order_number_checkbox").prop("checked",false);
    	manage_filter();
    })
jQuery(".booking_check_all_checkbox input").click(function(){
	var that;
	that = this;
	setTimeout(function(){

		if(jQuery(that)[0].checked == true) {
			 jQuery("input[name=booking_check]").prop("checked",true);
			 jQuery("input[name=booking_check]").change();
		}else{
			 jQuery("input[name=booking_check]").prop("checked",false);
			 jQuery("input[name=booking_check]").change();

		}
          
	},100)
})
jQuery("#order_number_checkbox").click(function(){
	manage_filter();
})
jQuery(".column_checkbox label").on("click",function(){
	setTimeout(function(){

		jQuery(".booking_datatable").addClass("loading_class");


			const active_column = [];
			
			jQuery("input[name=column_checkbox]").each(function(){
				
				if(this.checked == true){
					active_column.push(this.value);
				}
			})


			

			jQuery.ajax({
		        type: "POST",
		        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
		        data: {action:"save_active_column","active_column":active_column},
		        dataType: 'json',
		        success: function (data) {
                    manage_filter();
		        }
		    });

	},100)
})

jQuery(".show_booking_column p").click(function(){
		var html = jQuery(this).html();
		jQuery(".booking_show_main").html(html);

		var valuee = jQuery(this).data("value");
		valuee = parseInt(valuee);

		totalCount = valuee;
		manage_filter();

		/*jQuery('.booking_datatable').DataTable().destroy();

		jQuery('.booking_datatable').DataTable(dataJson).page.len(valuee).draw();*/
	})

jQuery(".export_booking_csv").click(function(){
	manage_filter("","export_booking_csv")
})
jQuery("#customer,#listing").change(function(){
	manage_filter();
})

async function manage_export($booking_data,startdate,uptodate,csv_file_name){
	var bookings = [];


	

	$booking_data.forEach(function (booking) { 
        if(booking.recurrenceRule != "" && booking.recurrenceRule != null){

        	var $ruless =  booking.recurrenceRule;

        	var rules = [];

        	$ruless = $ruless.split(";");

        	$ruless.forEach(function(rrrle){

        		if(rrrle != ""){
        			var splitt = rrrle.split("=");

        			if(splitt[0] == "UNTIL"){
        				var untill = moment(splitt[1]).format("YYYY-MM-DD");
        				untill = new Date(untill).toISOString();
        				untill = untill.replaceAll("-","");
        				untill = untill.replaceAll(":","");
        				untill = untill.split(".");
        				rules.push(splitt[0]+"="+untill[0]);
        			}else{
        				rules.push(rrrle);
        			}


        		}

        	})

        	var recurrenceRule = rules.join(";");
        	var recBooking = new rrule.RRule.fromString(recurrenceRule);
        	if(recBooking.options.until == null){
				var dddd = new Date(recBooking.options.dtstart);
				dddd.setDate(dddd.getDate() + 700);
				recBooking.options.until = dddd;
			}

			var dt_startt = new Date(booking.date_start);
			recBooking.options.dtstart = dt_startt;
			function isJsonString(str) {
			    try {
			        JSON.parse(str);
			    } catch (e) {
			        return false;
			    }
			    return true;
			}

            function isDeleted(orignalItem, booking) {
            	var bookingDate = moment.utc(booking).format("YYYY-MM-DD");

            	var rec_datess = [];

            	if(isJsonString(orignalItem)){
                    
                    rec_datess = JSON.parse(orignalItem);

            	}else{

            		var allRecArr = orignalItem.split(",");

            		

            		allRecArr.forEach(function(rec_dd){

            			rec_datess.push(moment.utc(rec_dd).format("YYYY-MM-DD"));

            		})

            	}

            	if (rec_datess.find(item => item == bookingDate)) {
                    return true;
                } else {
                    return false;
                }
                
            }

			function libRecExp(currentItem, eventItem) {
				var eventTime = new Date(eventItem.date_start);//object
				var eventHours = eventTime.getHours();
				var eventMin = eventTime.getMinutes();
				currentItem.setHours(eventHours, eventMin, 0, 0);
				return currentItem.toISOString();


			}

			recBooking = recBooking.all();

			if (recBooking.length > 0) {
				recBooking.forEach(function (item) { //Bookings List

					var b_date_start = new Date(booking.date_start);
					var b_date_end = new Date(booking.date_end);

					var month = item.getMonth() + 1;
					var dateee = item.getDate();

					if(month < 10){
			           month = "0"+month;
					}
					if(dateee < 10){
			           dateee = "0"+dateee;
					}
					var rec_date = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+b_date_start.getHours()).slice(-2)+":"+("0"+b_date_start.getMinutes()).slice(-2)+":"+("0"+b_date_start.getSeconds()).slice(-2);
					var endd_date = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+b_date_end.getHours()).slice(-2)+":"+("0"+b_date_end.getMinutes()).slice(-2)+":"+("0"+b_date_end.getSeconds()).slice(-2);
					let tempObj = Object.assign({});
					tempObj["booking_id"] = booking.id;
					//tempObj['recExp'] = libRecExp(item, bookingggg);
					tempObj['rec_date'] = rec_date;
					/*tempObj['date_start'] = rec_date;
					tempObj['date_end'] = endd_date;*/
					var tempRecExp = libRecExp(item, booking);
					tempObj['rec_exp'] = tempRecExp;
					if(startdate != "" && uptodate != ""){

						var uptodate_sql = new Date(uptodate+" 23:59:00");
						var startdate_sql = new Date(startdate+" 00:00:00");

						if(item >= startdate_sql && item <= uptodate_sql){
							if (booking.recurrenceException) {
								var isDel = isDeleted(booking.recurrenceException, tempRecExp);
								if (!isDel) {
									bookings.push(tempObj);
								}
							} else {
								bookings.push(tempObj);
							}
						}

					}else{
						if (booking.recurrenceException) {
							var isDel = isDeleted(booking.recurrenceException, tempRecExp);
							if (!isDel) {
								bookings.push(tempObj);
							}
						} else {
							bookings.push(tempObj);
						}

					}


					
				});

			}

        }else{
        	let tempObj = Object.assign({});
        	tempObj["booking_id"] = booking.id;
        	bookings.push(tempObj);
        }
	});

	function groupBy(list, keyGetter) {
	    const map = new Map();
	    list.forEach((item) => {
	         const key = keyGetter(item);
	         const collection = map.get(key);
	         if (!collection) {
	             map.set(key, [item]);
	         } else {
	             collection.push(item);
	         }
	    });
	    return map;
	}
	const grouped = groupBy(bookings, booking => booking.booking_id);

	const org_booking = [];


	bookings.forEach(function (result,$key) { 

		let tempObj = Object.assign({});
		tempObj["booking_id"] = result.booking_id;
		if(result.rec_date != undefined){
			tempObj["rec_date"] = result.rec_date;
		}
		if(result.rec_exp != undefined){
			tempObj["rec_exp"] = result.rec_exp;
		}

		if(grouped.get(result.booking_id) != undefined){
			tempObj["count_booking"] = grouped.get(result.booking_id).length;
		}
		org_booking.push(tempObj);



	});



	const perChunk = 700; // Number of items per chunk    
    const inputArray = org_booking; // Original booking data

    // Divide the input array into chunks
    const results = inputArray.reduce((resultArray, item, index) => { 
        const chunkIndex = Math.floor(index / perChunk);

        if (!resultArray[chunkIndex]) {
            resultArray[chunkIndex] = []; // Start a new chunk
        }

        resultArray[chunkIndex].push(item);
        return resultArray;
    }, []);

    const count_res = results.length;

    // ✅ Function to send AJAX requests sequentially
    async function sendAjaxRequests() {
        let lastResponse = null;

        for (let i = 0; i < count_res; i++) {
            try {
                let response = await jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    timeout: 1000000,
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {
                        "bookings": results[i],
                        "csv_file_name": csv_file_name,
                        "page_type": "<?php echo $page_type; ?>",
                        "action": "export_boooking_csv"
                    }
                });

                lastResponse = response; // Store the last successful response
                console.log(`Chunk ${i + 1} completed`);
            } catch (error) {
                console.error(`Error in chunk ${i + 1}:`, error);
                alert(`An error occurred while processing chunk ${i + 1}.`);
                return; // Stop execution on error
            }
        }

        // ✅ After all requests, open the last CSV file
        if (lastResponse && lastResponse.url) {
			window.location.href = lastResponse.url; 
			jQuery(".booking_datatable").removeClass("loading_class");
			jQuery(".booking_datatable_loader").remove();
            setTimeout(() => {
                // ✅ Delete the CSV file
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    timeout: 1000000,
                    url: "<?php echo $wpml_home_url; ?>/wp-admin/admin-ajax.php",
                    data: { "action": "delete_csv_file", "csv_file_name": csv_file_name},
                    success: function () {
                        
                        //alert("All bookings have been successfully exported!");
                    },
                    error: function () {
                        console.error("Error deleting CSV file.");
                    }
                });

            }, 10000); // Delay to ensure file is generated
        }
    }

    // ✅ Start the sequential AJAX requests
    await sendAjaxRequests();

	/*jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        timeout: 1000000,
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        data: {"bookings":bookings,"action":"export_boooking_csv"},  
        success: function(data){
           // jQuery('.booking_datatable').DataTable().destroy();
        	jQuery('.booking_table_main').html(data.content);

        	if(data.conflict_count > 0 ){
        		jQuery(".conflict_booking").find("span").html("Found <b>"+data.conflict_count+"</b> bookings that are conflicting.")
        		jQuery(".conflict_booking").show();
        		jQuery(".conflict_count_span").show();
        		jQuery(".conflict_count_span").html(data.conflict_count);
        	}

            jQuery('.booking_datatable').DataTable(dataJson).draw();

        }
    });*/
	//exportToCsv('export.csv',bookings);
   
}
function exportToCsv(filename, rows) {
    var processRow = function (row,count) {

    	var finalVal = '';

    	if(count > 0){
    		var keys = Object.keys(row);
    		for (var k = 0; k < keys.length; k++) {

    			if(keys)
		            var innerValue = values[j] === null ? '' : values[j].toString();
		            if (values[j] instanceof Date) {
		                innerValue = values[j].toLocaleString();
		            };

		            var result = innerValue.replace(/"/g, '""');
		            if (result.search(/("|,|\n)/g) >= 0)
		                result = '"' + result + '"';
		            if (j > 0)
		                finalVal += ',';
		            finalVal += result;
	        }

    	}else{
    		var values = Object.values(row);
    		for (var j = 0; j < values.length; j++) {
		            var innerValue = values[j] === null ? '' : values[j].toString();
		            if (values[j] instanceof Date) {
		                innerValue = values[j].toLocaleString();
		            };

		            var result = innerValue.replace(/"/g, '""');
		            if (result.search(/("|,|\n)/g) >= 0)
		                result = '"' + result + '"';
		            if (j > 0)
		                finalVal += ',';
		            finalVal += result;
	        }
    	}
        return finalVal + '\n';

        
    };

    var csvFile = '';
    for (var i = 0; i < rows.length; i++) {
        csvFile += processRow(rows[i],i);
    }

    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
    

	jQuery(document).ready(function(){
		if(window.matchMedia("(max-width: 767px)").matches){	
			jQuery(".mobileDropdown").on('click', function(){
				jQuery(".booking_main_start .tab-ul").toggle();
			});


			jQuery(".booking_main_start .tab-ul li").on('click', function(){
				var itemInfo = this.innerHTML;
				console.log(itemInfo);
				jQuery('.mobileDropdown').html(itemInfo);
				jQuery('.mobileDropdown').trigger('click');
			});

			jQuery(".filterPop").on('click', function(){
				jQuery(".left_filter").addClass('showFilter');
			});

			jQuery(".mobileFilterClose, .filterHeader .closeButt").on('click', function(){
				jQuery(".left_filter").removeClass('showFilter');
			});
		}
	});
    

</script>


