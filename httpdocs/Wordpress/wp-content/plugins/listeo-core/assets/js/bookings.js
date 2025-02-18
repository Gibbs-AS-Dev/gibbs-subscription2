/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){
	jQuery('.booking-confirmation-btn').click(function(){
	  var checkBoxVal = jQuery('#pdfApprove').val();
	    var checkBoxValChecked = jQuery('#pdfApprove').is(':checked');
	    if(checkBoxValChecked === false) {
	      jQuery('#checkbox-error').show(); 
	      return false;
	    }
	});

	$(document).on('change','.discount-input', function(){
		var text = jQuery('.discount-input:checked').val();
		jQuery('.services-counter-discount').show();
		jQuery('.services-counter-discount').text(text);
		check_booking();
	});

	var inputClicked = false;
/*----------------------------------------------------*/
	/*  Booking widget and confirmation form
	/*----------------------------------------------------*/
	$('a.booking-confirmation-btn').on('click', function(e){
		
		
		//e.preventDefault();

		let F_firstName = jQuery("input[name='firstname']").val();
		let F_lastName = jQuery("input[name='lastname']").val();
		let F_email = jQuery.trim(jQuery(".email_class").val());
		let F_phone = jQuery.trim(jQuery(".phone_class").val());
		let F_message = jQuery("textarea[name='message']").val();
		let F_billing_address_1 = jQuery("input[name='billing_address_1']").val();
		let F_billing_postcode = jQuery("input[name='billing_postcode']").val();
		let F_billing_city = jQuery("input[name='billing_city']").val();
		let F_billing_country = jQuery("input[name='billing_country']").val();

		/*var valid = 1;

	    if(F_firstName === ""){
	        jQuery("#label_firstname span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_firstname span").hide();
	    }

	    if(F_lastName === ""){
	        jQuery("#lastname label span").show();
	        valid = 0;
	    }else{
	    	jQuery("#lastname label span").hide();
	    }

	    if(F_email === ""){
	        jQuery("#label_email span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_email span").hide();
	    }

	    if(F_phone === ""){
	        jQuery("#label_phone span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_phone span").hide();
	    }


	    if(F_billing_address_1 === ""){
	        jQuery("#label_billing_address_1 span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_billing_address_1 span").hide();
	    }

	    if(F_billing_postcode === ""){
	        jQuery("#label_billing_postcode span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_billing_postcode span").hide();
	    }

	    if(F_billing_city === ""){
	        jQuery("#label_billing_city span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_billing_city span").hide();
	    }

	    if(valid === 1){

	    	var button = $(this);
			button.addClass('loading');
			
			$('#booking-confirmation').submit();
		}*/
		//$('#submit_form').click();
		
	});
$(document).bind('change', function(e){
    if( $(e.target).is(':invalid') ){
        $(e.target).parent().addClass('invalid');
    } else {
        $(e.target).parent().removeClass('invalid');
    }
});
	$('#listeo-coupon-link').on('click', function(e){
		e.preventDefault();
		$('.coupon-form').toggle();
	});

	function validate_coupon(listing_id,price) {

		var current_codes = $('#coupon_code').val();
		if(current_codes){
			var codes = current_codes.split(',');
			$.each(codes, function(index, item) {
				console.log(item);
			    var ajax_data = {
					'listing_id' : 	listing_id,
					'coupon' : 	item,
					'coupons' : codes,
					'price' : 	price,
					'action' : 'listeo_validate_coupon'	
				};
				$.ajax({
		            type: 'POST', 
		            dataType: 'json',
					url: listeo.ajaxurl,
					data: ajax_data,
					
		            success: function(data){
						
						if(data.success){
					
							
							
						} else {

							
							$('#coupon-widget-wrapper-output div.error').html(data.message).show();
							$('#coupon-widget-wrapper-applied-coupons span[data-coupon="'+item+'"] i').trigger('click');
							$('#apply_new_coupon').val('');
							$("#coupon-widget-wrapper-output .error").delay(3500).hide(500);
						
						}
						$('a.listeo-booking-widget-apply_new_coupon').removeClass('active');
		            }
		        });
			});
		}
		
			

	}

	// Apply new coupon
	$('a.listeo-booking-widget-apply_new_coupon').on('click', function(e){
		e.preventDefault();
		$(this).addClass('active');
		$('#coupon-widget-wrapper-output div').hide();
		
		var ajax_data = {
			'listing_id' : 	$('#listing_id').val(),
			'coupon' : 	$('#apply_new_coupon').val(),
			'price' : 	$('.booking-estimated-cost').data('price'),
			'action' : 'listeo_validate_coupon'	
		};

		//check if it was already addd
		
		var current_codes = $('#coupon_code').val();
		var result = current_codes.split(',');
		var arraycontainscoupon = (result.indexOf($('#apply_new_coupon').val()) > -1);
		
		$('#coupon-widget-wrapper-output div').hide();
		if(arraycontainscoupon) {
			$(this).removeClass('active');
			$('input#apply_new_coupon').removeClass('bounce').addClass('bounce');
			return;			
		}
		$.ajax({
            type: 'POST', 
            dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
				
				if(data.success){
			
					if(current_codes.length>0){
						$('#coupon_code').val(current_codes + ',' + data.coupon);	
					} else {
						$('#coupon_code').val(data.coupon);	
					}
					$('#apply_new_coupon').val('');
					$('#coupon-widget-wrapper-applied-coupons').append("<span data-coupon="+data.coupon+">"+data.coupon+"<i class='fa fa-times'></i></span>")
					$('#coupon-widget-wrapper-output .success').show();
					if($('#booking-confirmation-summary').length>0){
						calculate_booking_form_price();
					} else {
						if($("#form-booking").hasClass('form-booking-event')){
							calculate_price();
						} else {
							check_booking();	
						}
						
					}
					$("#coupon-widget-wrapper-output .success").delay(3500).hide(500);
					
				} else {

					$('input#apply_new_coupon').removeClass('bounce').addClass('bounce');
					$('#coupon-widget-wrapper-output div.error').html(data.message).show();
					
					$('#apply_new_coupon').val('');
					$("#coupon-widget-wrapper-output .error").delay(3500).hide(500);
				
				}
				$('a.listeo-booking-widget-apply_new_coupon').removeClass('active');
            }
        });
	});


	// Remove coupon from widget and calculate price again
	$('#coupon-widget-wrapper-applied-coupons').on('click', 'span i', function(e){

		var coupon = $(this).parent().data('coupon');
		

		var coupons = $('#coupon_code').val();	
		var coupons_array = coupons.split(',');
		coupons_array = coupons_array.filter(function(item) {
			console.log(item);
			console.log(coupon);
		    return item !== coupon
		})
		
		$('#coupon_code').val(coupons_array.join(","));	
		$(this).parent().remove();
		if($('#booking-confirmation-summary').length>0){
			calculate_booking_form_price();
		} else {
			check_booking();
			calculate_price();
			
		}
	});



	//Book now button
	$('.listing-widget').on('click', 'a.book-now', function(e){

		/*if(jQuery("body").find("input[id=listing_type]").val() != undefined && jQuery("body").find("input[id=listing_type]").val() == "service"){
			return;
			e.preventDefault();
		}*/
		var button = $(this);

		if($('#date-picker').val()){
			inputClicked = true;
			//check_booking();

		}

		if(inputClicked == false){
			$('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').addClass('bounce');
		} else {
				button.addClass('loading');
		}
		e.preventDefault();

		var freeplaces = button.data('freeplaces');
	

	
		setTimeout(function() {
			  button.removeClass('loading');
			  $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
			  
		}, 3000);

		try {
			if ( freeplaces > 0 ) 
			{

					// preparing data for ajax
					var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
					var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
			
					var ajax_data = {
						'listing_type' : $('#listing_type').val(),
						'listing_id' : 	$('#listing_id').val()
						//'nonce': nonce		
					};
					var invalid = false;
					if ( startDataSql ) ajax_data.date_start = startDataSql;
					if ( endDataSql ) ajax_data.date_end = endDataSql;
					if ( $('input#slot').val() ) ajax_data.slot = $('input#slot').val();
					if ( $('.time-picker#_hour').val() ) ajax_data._hour = $('.time-picker#_hour').val();
					if ( $('.time-picker#_hour_end').val() ) ajax_data._hour_end = $('.time-picker#_hour_end').val();
					if ( $('.adults').val() ) ajax_data.adults = $('.adults').val();
					if ( $('.childrens').val() ) ajax_data.childrens = $('.childrens').val();
					if ( $('#tickets').val() ) ajax_data.tickets = $('#tickets').val();
					if ( $('#coupon_code').val() ) ajax_data.coupon = $('#coupon_code').val();

					if ( $('#listing_type').val() == 'service' ) {
						
						if( $('input#slot').val() == undefined || $('input#slot').val() == '' ) {
							inputClicked = false;
							invalid = true;
						}
						if( $('.time-picker').length  ) {
							
							invalid = false;
						}
					}
					
					if(invalid == false) {

						var services = [];
	 					// $.each($("input[name='_service[]']:checked"), function(){            
	      //           		services.push($(this).val());
	      //       		});
	            		$.each($("input.bookable-service-checkbox:checked"), function(){   
							var quantity = $(this).parent().find('input.bookable-service-quantity').val();
				    		services.push({"service" : $(this).val(), "value" : quantity});
						});
	            		ajax_data.services = services;
						$('input#booking').val( JSON.stringify( ajax_data ) );
						$('#form-booking').submit();
					

					}

			} 
		} catch (e) {
			console.log(e);
		}

		if ( $('#listing_type').val() == 'event' )
		{
			
			var ajax_data = {
				'listing_type' : $('#listing_type').val(),
				'listing_id' : 	$('#listing_id').val(),
				'date_start' : $('.booking-event-date span').html(),
				'date_end' : $('.booking-event-date span').html(),
				//'nonce': nonce		
			};
			if ( $('#coupon_code').val() ) ajax_data.coupon = $('#coupon_code').val();
			var services = [];
			$.each($("input.bookable-service-checkbox:checked"), function(){   
				var quantity = $(this).parent().find('input.bookable-service-quantity').val();
	    		services.push({"service" : $(this).val(), "value" : quantity});
			});
    		ajax_data.services = services;
			
			// converent data
			ajax_data['date_start'] = moment(ajax_data['date_start'], wordpress_date_format.date).format('YYYY-MM-DD');
			ajax_data['date_end'] = moment(ajax_data['date_end'], wordpress_date_format.date).format('YYYY-MM-DD');
			if ( $('#tickets').val() ) ajax_data.tickets = $('#tickets').val();
			$('input#booking').val( JSON.stringify( ajax_data ) );
			
			$('#form-booking').submit();
			
		}
		
	});

	if(Boolean(listeo_core.clockformat)){
		var dateformat_even = wordpress_date_format.date+' HH:mm';
	} else {
		var dateformat_even = wordpress_date_format.date+' hh:mm A';
	}


	function updateCounter() {
	    var len = $(".bookable-services input[type='checkbox']:checked").length;
	    if(len>0){
	    	$(".booking-services span.services-counter").text(''+len+'');
	    	$(".booking-services span.services-counter").addClass('counter-visible');
	    } else{
	    	$(".booking-services span.services-counter").removeClass('counter-visible');
	    	$(".booking-services span.services-counter").text('0');
	    }
	}

	$('.single-service').on('click', function() {
		updateCounter();
		$(".booking-services span.services-counter").addClass("rotate-x");

		setTimeout(function() {
			$(".booking-services span.services-counter").removeClass("rotate-x");
		}, 300);
	});
	

	// $( ".input-datetime" ).each(function( index ) {
	// 	var $this = $(this);
	// 	var input = $(this).next('input');
	//   	var date =  parseInt(input.val());	
	//   	if(date){
	// 	  	var a = new Date(date);
	// 		var timestamp = moment(a);
	// 		$this.val(timestamp.format(dateformat_even));	
	//   	}
		
	// });
	
	//$('#_event_date').val(timestamp.format(dateformat_even));
	
	$('.input-datetime').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: true, 
		timePicker: true,
		autoUpdateInput: false,
		timePicker24Hour: Boolean(listeo_core.clockformat),
		minDate: moment().subtract(0, 'days'),
		
		locale: {
			format 			: dateformat_even,

			"firstDay"		: parseInt(wordpress_date_format.day),
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
	  	},
	  
	  	
	});

	$('.input-datetime').on('apply.daterangepicker', function(ev, picker) {
      	$(this).val(picker.startDate.format(dateformat_even));
	});

	$('.input-datetime').on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});

	
	$('.input-date').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: true, 
		timePicker: false,
		autoUpdateInput: false,
		
		minDate: moment().subtract(0, 'days'),
		
		locale: {
			format 			: 'YYYY-MM-DD',
			"firstDay"		: parseInt(wordpress_date_format.day),
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
	  	},
	  
	  	
	});

	$('.input-date').on('apply.daterangepicker', function(ev, picker) {
      	$(this).val(picker.startDate.format('YYYY-MM-DD'));
	});

	$('.input-date').on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});
	// $('.input-datetime').on( 'apply.daterangepicker', function(){
		
	// 	var picked_date = $(this).val();
	// 	var input = $(this).next('input');
	// 	input.val(moment(picked_date,dateformat_even).format('YYYY-MM-DD HH:MM:SS'));
	// } );

	function wpkGetThisDateSlots( date ) {

		var slots = {
			isFirstSlotTaken: false,
			isSecondSlotTaken: false
		}
		
		if ( $( '#listing_type' ).val() == 'event' )
			return slots;
			
		if ( typeof disabledDates !== 'undefined' ) {
			if ( wpkIsDateInArray( date, disabledDates ) ) {
				slots.isFirstSlotTaken = slots.isSecondSlotTaken = true;
				return slots;
			}
		}

		if ( typeof wpkStartDates != 'undefined' && typeof wpkEndDates != 'undefined' ) {
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();
			var cr_date = yyyy+"-"+mm+"-"+dd;


			var cr_dd = String(date._d.getDate()).padStart(2, '0');
			var cr_mm = String(date._d.getMonth() + 1).padStart(2, '0'); //January is 0!
			var cr_yyyy = date._d.getFullYear();
			var cr_cr_date = cr_yyyy+"-"+cr_mm+"-"+cr_dd;

			if(cr_date == cr_cr_date){
				if ( wpkIsDateInArray( date, wpkStartDates ) ) {
		               slots.isSecondSlotTaken = true;
					   slots.isFirstSlotTaken = true;
			    }else{
			    	slots.isSecondSlotTaken = wpkIsDateInArray( date, wpkStartDates );
			        slots.isFirstSlotTaken = wpkIsDateInArray( date, wpkEndDates );
			    }
			}else{
			   slots.isSecondSlotTaken = wpkIsDateInArray( date, wpkStartDates );
			   slots.isFirstSlotTaken = wpkIsDateInArray( date, wpkEndDates );
			}

			





			/*
			
			if ( wpkIsDateInArray( cr_date, wpkStartDates ) ) {
               debugger;
               slots.isSecondSlotTaken = true;
			   slots.isFirstSlotTaken = true;
               
			}else{*/
			//}
			
		}
		
		return slots;

	}

	function wpkIsDateInArray( date, array ) {
		return jQuery.inArray( date.format("YYYY-MM-DD"), array ) !== -1;
	}


	$('#date-picker').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: ( $('#date-picker').data('listing_type') == 'rental' ? false : true ), 
		timePicker: false,
		minDate: moment().subtract(0, 'days'),
		minSpan : { days:  $('#date-picker').data('minspan') },
		startDate : Cookies.get('listeo_rental_startdate'),
    	endDate : Cookies.get('listeo_rental_enddate'),
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
	      
		},

		isCustomDate: function( date ) {

			var slots = wpkGetThisDateSlots( date );

			if ( ! slots.isFirstSlotTaken && ! slots.isSecondSlotTaken )
				return [];

			if ( slots.isFirstSlotTaken && ! slots.isSecondSlotTaken ) {
				return [ 'first-slot-taken' ];
			}

			if ( slots.isSecondSlotTaken && ! slots.isFirstSlotTaken ) {
				return [ 'second-slot-taken' ];
			}
			
		},

		isInvalidDate: function(date) {

			// working only for rental
						

			if ($('#listing_type').val() == 'event' ) return false;
			if ($('#listing_type').val() == 'service' && typeof disabledDates != 'undefined' ) {
				if ( jQuery.inArray( date.format("YYYY-MM-DD"), disabledDates ) !== -1) return true;
			}
			if ($('#listing_type').val() == 'rental' ) {
	
				var slots = wpkGetThisDateSlots( date );

				return slots.isFirstSlotTaken && slots.isSecondSlotTaken;
			}
		}

	});

	$('#date-picker').on('show.daterangepicker', function(ev, picker) {

        $('.daterangepicker').addClass('calendar-visible calendar-animated');
        $('.daterangepicker').removeClass('calendar-hidden');
    });
    $('#date-picker').on('hide.daterangepicker', function(ev, picker) {
    	
        $('.daterangepicker').removeClass('calendar-visible');
        $('.daterangepicker').addClass('calendar-hidden');
	});

	function calculate_price(){
		
		var ajax_data = {
			'action': 'calculate_price', 
			'listing_type' : $('#date-picker').data('listing_type'),
			'listing_id' : 	$('input#listing_id').val(),
			'tickets' : 	$('input#tickets').val(),
			'coupon' : 	$('input#coupon_code').val(),
			//'nonce': nonce		
		};
		var services = [];

		$.each($("input.bookable-service-checkbox:checked"), function(){   
			var quantity = $(this).parent().find('input.bookable-service-quantity').val();
    		services.push({"service" : $(this).val(), "value" : quantity});
		});
		ajax_data.services = services;
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){

						$('#negative-feedback').fadeOut();
						$('a.book-now').removeClass('inactive');
						if(data.data.price) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);	
								
							} else {
								$('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);	
							}
							$('.booking-estimated-cost').data('price',data.data.price)
							$('.booking-estimated-cost').fadeIn();
						}
						if(data.data.price_discount) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-discount-cost span').html(listeo_core.currency_symbol+' '+data.data.price_discount);	
							} else {
								$('.booking-estimated-discount-cost span').html(data.data.price_discount+' '+listeo_core.currency_symbol);	
							}
							$('.booking-estimated-cost').addClass('estimated-with-discount');
							$('.booking-estimated-discount-cost').fadeIn();

						} else {
							$('.booking-estimated-cost').removeClass('estimated-with-discount');
							$('.booking-estimated-discount-cost').fadeOut();
						}
            }
        });
	}

	function calculate_booking_form_price() {
		var ajax_data = {
			'action'		:   'listeo_calculate_booking_form_price', 
			'coupon' 		: 	$('input#coupon_code').val(),
			'price' 		: 	$('li.total-costs').data('price'),
		}

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
						
						if(data.price >= 0) {
							
							if(listeo_core.currency_position=='before'){
								$('.total-discounted_costs span').html(listeo_core.currency_symbol+' '+data.price);	
							} else {
								$('.total-discounted_costs span').html(data.price+' '+listeo_core.currency_symbol);	
							}
							
							$('.total-discounted_costs').fadeIn();
							$('.total-costs').addClass('estimated-with-discount');

						} else {
							$('.total-discounted_costs ').fadeOut();
							$('.total-costs').removeClass('estimated-with-discount');
						}
            }
        });

	};
	
	// function when checking booking by widget
	function check_booking() 
	{
		inputClicked = true;
		if ( is_open === false ) return 0;
		
		// if we not deal with services with slots or opening hours
		if ( $('#date-picker').data('listing_type') == 'service' && 
		! $('input#slot').val() && ! $('.time-picker').val() ) 
		{
			$('#negative-feedback').fadeIn();
			
			return;
		}
		
		Cookies.set('listeo_rental_startdate', $('#date-picker').data('daterangepicker').startDate.format(wordpress_date_format.date));
        Cookies.set('listeo_rental_enddate',  $('#date-picker').data('daterangepicker').endDate.format(wordpress_date_format.date));

		var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
		var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
		
		console.log($('#date-picker').data('daterangepicker').startDate);

		var discount = jQuery('input[name="discount"]:checked').val();
		
		// preparing data for ajax
		var ajax_data = {
			'action': 'check_avaliabity', 
			'listing_type' : $('#date-picker').data('listing_type'),
			'listing_id' : 	$('input#listing_id').val(),
			'coupon' : 	$('input#coupon_code').val(),
			'date_start' : startDataSql,
			'discount' : discount,
			'date_end' : endDataSql,
			//'nonce': nonce		
		};
		var services = [];
		// $.each($("input.bookable-service-checkbox:checked"), function(){            
  //   		services.push($(this).val());
		// });
		// $.each($("input.bookable-service-quantity"), function(){            
  //   		services.push($(this).val());
		// });
		$.each($("input.bookable-service-checkbox:checked"), function(){   
			var quantity = $(this).parent().find('input.bookable-service-quantity').val();
    		services.push({"service" : $(this).val(), "value" : quantity});
		});
	
		ajax_data.services = services;
		
		if ( $('input#slot').val() ) ajax_data.slot = $('input#slot').val();
		if ( $('input.adults').val() ) ajax_data.adults = $('input.adults').val();
		if ( $('.time-picker').val() ) ajax_data.hour = $('.time-picker').val();
		

		// loader class
		$('a.book-now').addClass('loading');
		$('a.book-now-notloggedin').addClass('loading');
		$('.booking-estimated-cost-tax').hide();
		$('.booking-normal-price').hide();

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
            	jQuery(".show_charged").show();

				// loader clas
				if (data.success == true && ( ! $(".time-picker").length || is_open != false ) ) {
                   if ( data.data.free_places > 0) {
                   		$('a.book-now,a.book-now-notloggedin').data('freeplaces',data.data.free_places);
						$('.booking-error-message').fadeOut();
						$('a.book-now').removeClass('inactive');
						if(data.data.price) {
							
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);
								if(data.data.taxprice != undefined){
									if(data.data.taxprice > 0){
										$('.booking-normal-price span').html(listeo_core.currency_symbol+' '+data.data.normalprice);
										$('.booking-estimated-cost-tax div.tax-span').html(listeo_core.currency_symbol+' '+data.data.taxprice);
										$('.booking-estimated-cost-tax').fadeIn();
										$('.booking-normal-price').fadeIn();
									}
									
								}
									
							} else {
								$('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);	
								if(data.data.taxprice != undefined){
									if(data.data.taxprice > 0){
										$('.booking-normal-price span').html(data.data.normalprice+' '+listeo_core.currency_symbol);
										$('.booking-estimated-cost-tax div.tax-span').html(data.data.taxprice+' '+listeo_core.currency_symbol);
										$('.booking-estimated-cost-tax').fadeIn();
										$('.booking-normal-price').fadeIn();
									}
									
								}
							}
							$('.booking-estimated-cost').data('price',data.data.price)
							$('.booking-estimated-cost').fadeIn();
							if(data.data.price < 1){
								jQuery(".show_charged").hide();
								jQuery(".booking-estimated-cost").find("span").html("GRATIS");
							}
						} else {
							jQuery(".show_charged").hide();
							jQuery(".booking-estimated-cost").find("span").html("GRATIS");
							//$('.booking-estimated-cost span').html( '0 '+listeo_core.currency_symbol);	
							$('.booking-estimated-cost').fadeOut();
						}
						if(data.data.price_discount) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-discount-cost span').html(listeo_core.currency_symbol+' '+data.data.price_discount);	
							} else {
								$('.booking-estimated-discount-cost span').html(data.data.price_discount+' '+listeo_core.currency_symbol);	
							}
							$('.booking-estimated-cost').addClass('estimated-with-discount');
							$('.booking-estimated-discount-cost').fadeIn();

						} else {
							$('.booking-estimated-cost').removeClass('estimated-with-discount');
							$('.booking-estimated-discount-cost').fadeOut();
						}
						validate_coupon($('input#listing_id').val(),data.data.price);
						$('.coupon-widget-wrapper').fadeIn();
				   } else {
				   		$('a.book-now,a.book-now-notloggedin').data('freeplaces',0);
						$('.booking-error-message').fadeIn();
						
						$('.booking-estimated-cost').fadeOut();

						$('.booking-estimated-cost span').html('');

					}
                } else {
                	$('a.book-now,a.book-now-notloggedin').data('freeplaces',0);
					$('.booking-error-message').fadeIn();
					
					$('.booking-estimated-cost').fadeOut();
		   		}
		   		$('a.book-now').removeClass('loading');
		   		$('a.book-now-notloggedin').removeClass('loading');
            }
		});

	}

	var is_open = true;
	var lastDayOfWeek;




	// update slots and check hours setted to this day
	function update_booking_widget () 
	{

		// function only for services
		if ( $('#date-picker').data('listing_type') != 'service') return;
		$('a.book-now').addClass('loading');
		$('a.book-now-notloggedin').addClass('loading');
		// get day of week
		var date = $('#date-picker').data('daterangepicker').endDate._d;
		var dayOfWeek = date.getDay() - 1;
		console.log(date.getDay() - 1);
	
		if(date.getDay() == 0){
			dayOfWeek = 6;
		}
		

		var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
		var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
			
		var ajax_data = {
			'action'		: 'update_slots', 
			'listing_id' 	: 	$('input#listing_id').val(),
			'date_start' 	: startDataSql,
			'date_end' 		: endDataSql,
			'slot'			: dayOfWeek
			//'nonce': nonce		
		};

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
			
            success: function(data){
            	
				$('.time-slots-dropdown .panel-dropdown-scrollable').html(data.data);

				// reset values of slot selector
				if ( dayOfWeek != lastDayOfWeek)
				{
					
					$( '.panel-dropdown-scrollable .time-slot input' ).prop("checked", false);
					
					$('.panel-dropdown.time-slots-dropdown input#slot').val('');
					$('.panel-dropdown.time-slots-dropdown a').html( $('.panel-dropdown.time-slots-dropdown a').attr('placeholder') );
					$(' .booking-estimated-cost span').html(' ');

				}

				lastDayOfWeek = dayOfWeek;

				if ( ! $( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).length ) 
				{

					$( '.no-slots-information' ).show();
					$('.panel-dropdown.time-slots-dropdown a').html( $( '.no-slots-information' ).html() );

				}
					else  
				{

					// when we dont have slots for this day reset cost and show no slots
					$( '.no-slots-information' ).hide();
					$(' .booking-estimated-cost span').html(' ');
					

				}
				// show only slots for this day
				$( '.panel-dropdown-scrollable .time-slot' ).hide( );
				
				$( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).show( );
				$(".time-slot").each(function() {
					var timeSlot = $(this);
					$(this).find('input').on('change',function() {
						var timeSlotVal = timeSlot.find('strong').text();
						var slotArray = [timeSlot.find('strong').text(), timeSlot.find('input').val()];

						$('.panel-dropdown.time-slots-dropdown input#slot').val( JSON.stringify( slotArray ) );
				
						$('.panel-dropdown.time-slots-dropdown a').html(timeSlotVal);
						$('.panel-dropdown').removeClass('active');
						
						check_booking();
					});
				});
				$('a.book-now').removeClass('loading');
				$('a.book-now-notloggedin').removeClass('loading');
            }
        });
		

		// check if opening days are active
		if ( $(".time-picker").length ) {
			if(availableDays){


				if ( availableDays[dayOfWeek].opening == 'Closed' || availableDays[dayOfWeek].closing == 'Closed') 
				{

					$('#negative-feedback').fadeIn();

					//$('a.book-now').css('background-color','grey');
					
					is_open = false;
					console.log('zamkniete tego dnia' + dayOfWeek);
					return;
				}

				// converent hours to 24h format
				var opening_hour = moment( availableDays[dayOfWeek].opening, ["h:mm A"]).format("HH:mm");
				var closing_hour = moment( availableDays[dayOfWeek].closing, ["h:mm A"]).format("HH:mm");


				// get hour in 24 format
				var current_hour = $('.time-picker').val();


				// check if currer hour bar is open
				if ( current_hour >= opening_hour && current_hour <= closing_hour) 
				{

					is_open = true;
					$('#negative-feedback').fadeOut();
					$('a.book-now').attr('href','#').css('background-color','#f30c0c');
					check_booking()
					console.log('otwarte' + dayOfWeek);
					

				} else {
					
					is_open = false;
					$('#negative-feedback').fadeIn();
					//$('a.book-now').attr('href','#').css('background-color','grey');
					$('.booking-estimated-cost span').html('');
					console.log('zamkniete');

				}
			}
		}
	}

	// if slots exist update them
	if ( $( '.time-slot' ).length ) { update_booking_widget(); }
	
	// show only services for actual day from datapicker
	$( '#date-picker' ).on( 'apply.daterangepicker', update_booking_widget );
	$( '#date-picker' ).on( 'change', function(){
		check_booking();
		update_booking_widget();
	});


	// when slot is selected check if there are avalible bookings
	$( '#date-picker' ).on( 'apply.daterangepicker', check_booking );
	$( '#date-picker' ).on( 'cancel.daterangepicker', check_booking );
	
	$(document).on("change", 'input#slot,input.adults, input.bookable-service-quantity, .form-booking-service input.bookable-service-checkbox,.form-booking-rental input.bookable-service-checkbox', function(event) {
		check_booking();
	}); 
	//$('input#slot').on( 'change', check_booking );
	
	$('input#tickets,.form-booking-event input.bookable-service-checkbox').on('change',function(e){
		//check_booking();
		calculate_price();
	});


	// hours picker
	if ( $(".time-picker").length ) {
		var time24 = false;
		
		if(listeo_core.clockformat){
			time24 = true;
		}
		const calendars = $(".time-picker").flatpickr({
			enableTime: true,
			noCalendar: true,
			dateFormat: "H:i",
			time_24hr: time24,
 			disableMobile: "true",
 			

			// check if there are free days on change and calculate price
			onClose: function(selectedDates, dateStr, instance) {
				update_booking_widget();
				check_booking();
			},

		});
		
		if($('#_hour_end').length) {
			calendars[0].config.onClose = [() => {
			  setTimeout(() => calendars[1].open(), 1);
			}];

			calendars[0].config.onChange = [(selDates) => {
			  calendars[1].set("minDate", selDates[0]);
			}];

			calendars[1].config.onChange = [(selDates) => {
			  calendars[0].set("maxDate", selDates[0]);
			}]
		}	 
	};
	

	
/*----------------------------------------------------*/
/*  Bookings Dashboard Script
/*----------------------------------------------------*/
$(".booking-services").on("click", '.qtyInc', function() {
	  var $button = $(this);

      var oldValue = $button.parent().find("input").val();
      console.log(oldValue);
      if(oldValue == 2) {
      	//$button.parents('.single-service').find('label').trigger('click');
      	$button.parents('.single-service').find('input.bookable-service-checkbox').prop("checked",true);
      	updateCounter();
      }
     	if($("#form-booking").hasClass('form-booking-event')){
			calculate_price();
		} else {
			check_booking();	
		}
      //calculate_price();
});


if ( $( "#booking-date-range" ).length ) {

	// to update view with bookin

	var bookingsOffset = 0;

	// here we can set how many bookings per page
	var bookingsLimit = 5;

	// function when checking booking by widget
	function listeo_bookings_manage(page) 
	{
		console.log($('#booking-date-range').data('daterangepicker'));
		if($('#booking-date-range').data('daterangepicker')){
			var startDataSql = moment( $('#booking-date-range').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
			var endDataSql = moment( $('#booking-date-range').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
	
		} else {
			var startDataSql = '';
			var endDataSql = '';
		}
if(!page) { page = 1 }
		
		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'date_start' : startDataSql,
			'date_end' : endDataSql,
			'listing_id' : $('#listing_id').val(),
			'listing_status' : $('#listing_status').val(),
			'dashboard_type' : $('#dashboard_type').val(),
			'limit' : bookingsLimit,
			'offset' : bookingsOffset,
			'page' : page,
			//'nonce': nonce		
		};

		
		// display loader class
		$(".dashboard-list-box").addClass('loading');

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){

				
				// display loader class
				$(".dashboard-list-box").removeClass('loading');

				if(data.data.html){
					$('#no-bookings-information').hide();
					$( "ul#booking-requests" ).html(data.data.html);	
					$( ".pagination-container" ).html(data.data.pagination);	
				} else {
					$( "ul#booking-requests" ).empty();
					$( ".pagination-container" ).empty();
					$('#no-bookings-information').show();
				}
				
            }
		});

	}

	// hooks for get bookings into view
	 $( '#booking-date-range' ).on( 'apply.daterangepicker', function(e){
		listeo_bookings_manage();
	 });
	 $( '#listing_id' ).on( 'change', function(e){
		listeo_bookings_manage();
	 });
	$( '#listing_status' ).on( 'change', function(e){
		listeo_bookings_manage();
	 });

	$( 'div.pagination-container').on( 'click', 'a', function(e) {
		e.preventDefault();
		
		var page   = $(this).parent().data('paged');
		if(page=='next'){
			var page = $('.pagination li.current').data('paged') + 1
		}
		if(page=='prev'){
			var page = $('.pagination li.current').data('paged') - 1
		}

		listeo_bookings_manage(page);

		$( 'body, html' ).animate({
			scrollTop: $(".dashboard-list-box").offset().top
		}, 600 );

		return false;
	} );


	$(document).on('click','.reject, .cancel',function(e) {
		e.preventDefault();
		if (window.confirm(listeo_core.areyousure)) {
			var $this = $(this);
			$this.parents('li').addClass('loading');
			var status = 'confirmed';
			if ( $(this).hasClass('reject' ) ) status = 'cancelled';
			if ( $(this).hasClass('cancel' ) ) status = 'cancelled';

			// preparing data for ajax
			var ajax_data = {
				'action': 'listeo_bookings_manage', 
				'booking_id' : $(this).data('booking_id'),
				'status' : status,
				//'nonce': nonce		
			};
			$.ajax({
	            type: 'POST', dataType: 'json',
				url: listeo.ajaxurl,
				data: ajax_data,
				
	            success: function(data){
						
					// display loader class
					$this.parents('li').removeClass('loading');

					listeo_bookings_manage();
					
	            }
			});
		}
	});

	$(document).on('click','.delete',function(e) {
		e.preventDefault();
		if (window.confirm(listeo_core.areyousure)) {
			var $this = $(this);
			$this.parents('li').addClass('loading');
			var status = 'deleted';
			
			// preparing data for ajax
			var ajax_data = {
				'action': 'listeo_bookings_manage', 
				'booking_id' : $(this).data('booking_id'),
				'status' : status,
				//'nonce': nonce		
			};
			$.ajax({
	            type: 'POST', dataType: 'json',
				url: listeo.ajaxurl,
				data: ajax_data,
				
	            success: function(data){
						
					// display loader class
					$this.parents('li').removeClass('loading');

					listeo_bookings_manage();
					
	            }
			});
		}
	});

	$(document).on('click','.renew_booking',function(e) {
		e.preventDefault();
		if (window.confirm(listeo_core.areyousure)) {
			var $this = $(this);
			$this.parents('li').addClass('loading');
			
			
			// preparing data for ajax
			var ajax_data = {
				'action': 'listeo_bookings_renew_booking', 
				'booking_id' : $(this).data('booking_id'),
				//'nonce': nonce		
			};
			$.ajax({
	            type: 'POST', dataType: 'json',
				url: listeo.ajaxurl,
				data: ajax_data,
				
	            success: function(data){
					if(data.success) {

					} else {
						alert(listeo_core.booked_dates);
					}
					// display loader class
					$this.parents('li').removeClass('loading');

					listeo_bookings_manage();
					
	            }
			});
		}
	});

	
	$(document).on('click','.approve',function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.parents('li').addClass('loading');
		var status = 'confirmed';
		if ( $(this).hasClass('reject' ) ) status = 'cancelled';
		if ( $(this).hasClass('cancel' ) ) status = 'cancelled';

		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'booking_id' : $(this).data('booking_id'),
			'status' : status,
			//'nonce': nonce		
		};
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
					
				// display loader class
				$this.parents('li').removeClass('loading');

				listeo_bookings_manage();
				
            }
		});

	});
	$(document).on('click','.mark-as-paid',function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.parents('li').addClass('loading');
		var status = 'paid';
		
		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'booking_id' : $(this).data('booking_id'),
			'status' : status,
			//'nonce': nonce		
		};
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
					
				// display loader class
				$this.parents('li').removeClass('loading');

				listeo_bookings_manage();
				
            }
		});

	});


	var start = moment().subtract(30, 'days');
    var end = moment();

    function cb(start, end) {
        $('#booking-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    
	var ranges = new Object();
	ranges[listeo_core.today] = [moment(), moment()];
	ranges[listeo_core.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
	ranges[listeo_core.last_7_days] = [moment().subtract(6, 'days'), moment()];
	ranges[listeo_core.last_30_days] = [moment().subtract(29, 'days'), moment()];
	ranges[listeo_core.this_month] = [moment().startOf('month'), moment().endOf('month')];
	ranges[listeo_core.last_month] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    var today = listeo_core.today;
    $('#booking-date-range-enabler').on('click',function(e){
    	e.preventDefault();
    	$(this).hide();
    	cb(start, end);
	    $('#booking-date-range').show().daterangepicker({
	    	"opens": "left",
		    "autoUpdateInput": false,
		    "alwaysShowCalendars": true,
	        startDate: start,
	        endDate: end,
	        ranges: ranges,
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
	    }, cb).trigger('click');
	    cb(start, end);
    })
   

    


    // Calendar animation and visual settings
    $('#booking-date-range').on('show.daterangepicker', function(ev, picker) {

        $('.daterangepicker').addClass('calendar-visible calendar-animated bordered-style');
        $('.daterangepicker').removeClass('calendar-hidden');
    });
    $('#booking-date-range').on('hide.daterangepicker', function(ev, picker) {
    	
        $('.daterangepicker').removeClass('calendar-visible');
        $('.daterangepicker').addClass('calendar-hidden');
	});
	
} // end if dashboard booking

   


	// $('a.reject').on('click', function() {
		
	// 	console.log(picker);
	
	// });
	});

})(this.jQuery);
