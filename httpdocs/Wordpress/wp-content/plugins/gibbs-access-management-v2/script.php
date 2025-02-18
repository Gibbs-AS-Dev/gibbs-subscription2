<?php
$wpml_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
?>
<script type="text/javascript">

	jQuery(document).ready(function(){
		manage_filter();
	})

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
	$sort = "2";


	let totalCount = 10;

     let responsivePriority_len = 5;

	if(jQuery(".booking_datatable").find("tr").find("th").length  > 0){
       responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 3;
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
			        "sInfo":          "Viser _START_ til _END_ av _TOTAL_ tilganger",
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
				"aaSorting": [],
			    "bSort": true,
			    "rowReorder": {
		            "selector": 'td:nth-child(0)'
		        },
			    "responsive": true,
			    "columnDefs": [ { 'targets': [0], // column index (start from 0)
							        'orderable': false, // set orderable false for selected columns
							    },
							    { responsivePriority: 15, targets: 0 },
								{ responsivePriority: 15, targets: responsivePriority_len },
			                ],
		    };
jQuery(document).ready( function () {

	jQuery('.booking_main').show();

	
    const oTable1 = jQuery(".booking_datatable").DataTable(dataJson);
    jQuery(".search_in").keyup(function(){
		manage_filter();
	})
} );

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


jQuery(document).on('click', function (e) {
    if (jQuery(e.target).closest(".dropdown").length === 0 && jQuery(e.target).closest(".search-box-inner").length === 0 && jQuery(e.target).closest(".dropdown-content").length === 0) {
        jQuery(".dropdown-content").removeClass("show");
    }
});
jQuery(document).on("click",".search-box-inner .dropbtn,.search-box-inner i",function(){
	var parent_div = jQuery(this).parent();
	jQuery(".search-box-inner").not(parent_div).find(".dropdown-content").removeClass("show");
	jQuery(this).parent().find(".dropdown-content").toggleClass("show");
	jQuery(this).parent().find("select").click();
	jQuery(this).parent().find(".search_in").focus();

})

jQuery(".daterange_pick").find("span").click(function(){
	jQuery(this).parent().find("#booking-date-range-enabler2").click();
})

function manage_filter(date_close = "",export_booking_csv="", page = 1){

	jQuery(".booking_datatable").addClass("loading_class");


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





	jQuery.ajax({
        type: "POST",
        url: "<?php echo $wpml_home_url;?>/wp-admin/admin-ajax.php",
        data: {action:"gibbs_access_management_data","listing_ids":listing_ids,"startDataSql":startDataSql,"endDataSql":endDataSql,"search_text":search_text,date_close:date_close,page:page},
        dataType: 'json',
        success: function (data) {

	        	jQuery(".booking_datatable").removeClass("loading_class");


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
	        		responsivePriority_len = jQuery(".booking_datatable").find("tr").find("th").length - 3;
	        		dataJson.columnDefs[2].targets = responsivePriority_len;
	        	}
	        	

	        	/*if(jQuery(".search_in").val() != ""){
	        		jQuery('.booking_datatable').DataTable(dataJson).search( jQuery(".search_in").val() ).draw();
	        	}else{
	        		jQuery('.booking_datatable').DataTable(dataJson).draw();
	        	}*/
	        	jQuery('.booking_datatable').DataTable(dataJson).draw();
         
        }
    });

}
jQuery(".booking_datatable").addClass("loading_class");


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
		        url: "<?php echo $wpml_home_url;?>/wp-admin/admin-ajax.php",
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

jQuery("#customer,#listing").change(function(){
	manage_filter();
})

    

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
