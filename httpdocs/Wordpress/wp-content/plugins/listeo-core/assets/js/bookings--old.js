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
	for(var i = 0; i < 20; i++){
		$('.tabela').find(`.tes${i} strong`).text(`${i}:00`);
	}
	
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

	    if(F_message === ""){
	        jQuery("#label_message span").show();
	        valid = 0;
	    }else{
	    	jQuery("#label_message span").hide();
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
			
			//$('#booking-confirmation').submit();
		}
		*/
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

	// Book now button
	$('.listing-widget').on('click', 'a.book-now', function(e){
        


		/*if(jQuery('.discount-dropdown').length == 1 ){
			if(jQuery('.discount-input:checked').length == 0){
				$([document.documentElement, document.body]).animate({
					scrollTop: $(".discount-dropdown").offset().top - 200
				}, 2000);
	
				$('.discount-dropdown a').css({border: '0 solid red'}).animate({
					borderWidth: 4
				}, 500);
				
				setTimeout(() => {
					$('.discount-dropdown a').animate({
						borderWidth: 0
					}, 500);
				}, 1500);
				return;
			}
		}*/
		if(jQuery("#mobFromHours").val() != "" && jQuery("#mobFromHours").val() != undefined){
			jQuery("#fromHours").html("<option value='"+jQuery("#mobFromHours").val()+"'>"+jQuery("#mobFromHours").val()+"</option>");
			jQuery("#toHours").html("<option value='"+jQuery("#mobToHours").val()+"'>"+jQuery("#mobToHours").val()+"</option>");
		}
		
		var button = $(this);
	
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
					var firstday = localStorage.getItem('firstDate');
					var secondday;
					window.setTimeout(function(){
						secondday = $('.time-slot .endDate').attr('date');
					},100);
					

					window.setTimeout(function(){
						var startDataSql = firstday;
					var endDataSql = secondday;

					var ajax_data = {
						'listing_type' : $('#listing_type').val(),
						'listing_id' : 	$('#listing_id').val()
						//'nonce': nonce		
					};
					var invalid = false;
					if ( startDataSql ) ajax_data.date_start = startDataSql;
					if ( endDataSql ) ajax_data.date_end = endDataSql;
					var st = $('.startDate').text();
					var et = parseInt($('.endDate').text());
					et = et +1;
					var d = $('.endDate').parent().parent().attr('day');

					if ( $('input#slot').val() ){
						ajax_data.slot = $('input#slot').val();
					}else{
						ajax_data.slot = `["${st} - ${et}:00","${d}|0"]`;
					} 

					if ( $('.time-picker#_hour').val() ) ajax_data._hour = $('.time-picker#_hour').val();
					if ( $('.time-picker#_hour_end').val() ) ajax_data._hour_end = $('.time-picker#_hour_end').val();
					if ( $('.adults').val() ) ajax_data.adults = $('.adults').val();
					if ( $('.childrens').val() ) ajax_data.childrens = $('.childrens').val();
					if ( $('#tickets').val() ) ajax_data.tickets = $('#tickets').val();
					if ( $('#coupon_code').val() ) ajax_data.coupon = $('#coupon_code').val();

					if ( $('#listing_type').val() == 'service' ) {
						
						if( $('input#slot').val() == undefined || $('input#slot').val() == '' ) {
							inputClicked = false;
							invalid = false;
						}
						if( $('.time-picker').length  ) {
							
							invalid = false;
						}
					}

					
					if(invalid == false) {
						var services = [];
	 					// $.each($("input[name='_service[]']:checked"), function(){            
	      				//     		services.push($(this).val());
	   				   //});
	            		$.each($("input.bookable-service-checkbox:checked"), function(){   
							var quantity = $(this).parent().find('input.bookable-service-quantity').val();
				    		services.push({"service" : $(this).val(), "value" : quantity});
						});
	            		ajax_data.services = services;
						$('input#booking').val( JSON.stringify( ajax_data ) );
						$('#form-booking').submit();
					

					}
					}, 100);

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
			slots.isSecondSlotTaken = wpkIsDateInArray( date, wpkStartDates );
			slots.isFirstSlotTaken = wpkIsDateInArray( date, wpkEndDates );
		}
		
		return slots;

	}

	function wpkIsDateInArray( date, array ) {
		return jQuery.inArray( date.format("YYYY-MM-DD"), array ) !== -1;
	}


	$('#date-picker').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: ( $('#date-picker').attr('listing_type') == 'rental' ? false : true ), 
		timePicker: false,
		minDate: moment().subtract(0, 'days'),
		minSpan : { days:  $('#date-picker').data('minspan') },
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
			'listing_type' : $('#date-picker').attr('listing_type'),
			'listing_id' : 	$('input#listing_id').val(),
			'tickets' : 	$('input#tickets').val(),
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
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
						$('#negative-feedback').fadeOut();
						$('a.book-now').removeClass('inactive');
						if(data.data.normal_price > 0 ) {
							if(listeo_core.currency_position=='before'){
								if($('.categoryName span').attr('data-cat') == 'utstr'){
									$('.booking-normal-price span').html(data.data.multiply+' x '+listeo_core.currency_symbol+' '+data.data.normal_price);
								}else{
									$('.booking-normal-price span').html(listeo_core.currency_symbol+' '+data.data.normal_price);
								}
							} else {
								if ($('.categoryName span').attr('data-cat') == 'utstr') {
									$('.booking-normal-price span').html(data.data.multiply+' x '+data.data.normal_price + ' ' + listeo_core.currency_symbol);
								}else {
									$('.booking-normal-price span').html(data.data.normal_price + ' ' + listeo_core.currency_symbol);
								}
							}
							$('.booking-normal-price').fadeIn();
						}

						if(data.data.services_price > 0){
							if(listeo_core.currency_position=='before'){
								$('.booking-services-cost span').html(listeo_core.currency_symbol+' '+data.data.services_price);
							} else {
								$('.booking-services-cost span').html(data.data.services_price+' '+listeo_core.currency_symbol);
							}

							$('.booking-services-cost').fadeIn();
						}

						if(data.data.price > 0 ) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);
							} else {
								$('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);
							}

							$('.booking-estimated-cost').fadeIn();
						}
            }
        });
	}
	$('.listeo-booking-widget-apply_new_coupon').on('click', function(e){
    	setTimeout(function(){
             check_booking();
    	},4000);
    });
   

	
	// function when checking booking by widget
	function check_booking() 
	{
		
		inputClicked = true;
		if ( is_open === false ) { 
			return 0;
		}
		
		
		// if we not deal with services with slots or opening hours
		// if ( $('#date-picker').attr('listing_type') == 'service' && 
		// ! $('input#slot').val() && ! $('.time-picker').val() ) 
		// {
		// 	$('#negative-feedback').fadeIn();
		// 	console.log('inside negative geed back');

		// 	return;
		// }

		
		
		var firstday = localStorage.getItem('firstDate');
		var secondday;
		var firstDateAvailableNumber = 0;
		var firstDateSelectedNumber = 0;
		var secondDateAvailableNumber = 0;
		var secondDateSelectedNumber = 0;
		var dailyPrice = parseInt(jQuery('.js-daily-price').data('price'));
		var hourPrice = parseInt(jQuery('.js-hour-price').data('price'));
		var weekendPrice = parseInt(jQuery('.js-weekly-price').data('price'));
		// debugger;

		if(hourPrice.toString() == 'NaN'){
			hourPrice = dailyPrice;
		}
		if(weekendPrice.toString() == 'NaN'){
			weekendPrice = dailyPrice;
		}
		var firstProp = firstday;
		var secondProp = secondday;
		var totalPrice = 0;
		var totalDays = 0;
		var localStorageTotalPrice;
		var lastDayPrice = 0;
		var _firstDate = new Date(firstProp);
		var _secondDate = new Date(secondProp);
		var midDate = new Date();
		var minDays;
		var minHours;
		window.setTimeout(function(){
			secondday = $('.time-slot .endDate').attr('date');



			firstProp = firstday;
			secondProp = secondday;

			
			localStorageTotalPrice;
			lastDayPrice = 0;
			_firstDate = new Date(firstProp);
			_secondDate = new Date(secondProp);
			midDate = new Date();

			var Difference_In_Time = _secondDate.getTime() - _firstDate.getTime(); 
			
			// To calculate the no. of days between two dates 
			var _numberOfDays = Difference_In_Time / (1000 * 3600 * 24); 

			var is24 = 0;
			var decrease = 0;
			
			var fdgd = _firstDate.getDay();
			var sdgd =  _secondDate.getDay();
			
			if(fdgd == 0){
				fdgd = 6;
			}else{
				fdgd -= 1;
			}

			if(sdgd == 0){
				sdgd = 6;
			}else{
				sdgd -= 1;
			}
			
			// for (let i = fdgd; i <= sdgd; i++) {
			// 	for (let j = 0; j < 24; j++){
			// 		jQuery(`.${i}.${j}${days[i]}`).filter(function () {
			// 			if(jQuery(this).hasClass('available')){
			// 				is24++;
			// 			}
			// 		});
			// 	}
			// }
			_numberOfDays -= decrease;
				setTimeout(() => {
					jQuery('.tests').filter(function(){
						if(jQuery(this).attr('date') == `${firstProp}`){
							firstDateAvailableNumber++;
							if(jQuery(this).parent().css('background-color') == 'rgb(0, 132, 116)'){
								firstDateSelectedNumber++;
							}
						}
						if(jQuery(this).attr('date') == `${secondProp}`){
							secondDateAvailableNumber++;
							if(jQuery(this).parent().css('background-color') == 'rgb(0, 132, 116)'){
								secondDateSelectedNumber++;
							}
						}
					});
				}, 500);
				

				setTimeout(() => {
					
					if(firstProp == secondProp){
						for (let i = fdgd; i <= sdgd; i++) {
							for (let j = 0; j < 24; j++){
								jQuery(`.${i}.${j}${days[i]}`).filter(function () {
									if(jQuery(this).hasClass('available')){
										if(jQuery(this).find('label').css('background-color') == 'rgb(0, 132, 116)') {
											is24++;
										}
									}
								});
							}
						}

						if(weekendPrice == 0 && dailyPrice == 0){
							totalPrice += is24 * hourPrice;
						}else if(weekendPrice == 0 && hourPrice == 0){
							decrease = Math.floor(is24 / 24);
							totalPrice = decrease * dailyPrice;
							is24 = is24%24
							if(is24 > 0){
								totalPrice += dailyPrice;
							}

						} else if((dailyPrice == 0 && hourPrice == 0) || (dailyPrice.toString() == 'NaN' && hourPrice.toString() == 'NaN') ){
							decrease = Math.floor(is24 / 24);
							totalPrice = decrease * weekendPrice;
							is24 = is24%24
							if(is24 > 0){
								totalPrice += weekendPrice;
							}

						} else{
							if(firstDateAvailableNumber == firstDateSelectedNumber){
								if(_firstDate.getDay() == 6 || _firstDate.getDay() == 0){
									totalPrice = weekendPrice;
								}else{
									totalPrice = dailyPrice;
								}
							}else{
								if(_firstDate.getDay() == 6 || _firstDate.getDay() == 0){
									totalPrice = firstDateSelectedNumber * hourPrice;
									if(totalPrice > weekendPrice){
										totalPrice = weekendPrice;
									}
								}else{
									totalPrice = firstDateSelectedNumber * hourPrice;
									if(totalPrice > dailyPrice){
										totalPrice = dailyPrice;
									}
								}
							}
						}
					}else{

						for (let i = fdgd; i <= sdgd; i++) {
							for (let j = 0; j < 24; j++){
								jQuery(`.${i}.${j}${days[i]}`).filter(function () {
									if(jQuery(this).hasClass('available')){
										if(jQuery(this).find('label').css('background-color') == 'rgb(0, 132, 116)') {
											is24++;
										}
									}
								});
							}
						}
						if(weekendPrice == 0 && dailyPrice == 0){
							totalPrice += is24 * hourPrice;
						}else if(weekendPrice == 0 && hourPrice == 0){

							decrease = Math.floor(is24 / 24);
							totalPrice = decrease * dailyPrice;
							is24 = is24%24
							if(is24 > 0){
								totalPrice += dailyPrice;
							}

						} else if((dailyPrice == 0 && hourPrice == 0) || (dailyPrice.toString() == 'NaN' && hourPrice.toString() == 'NaN')){
							decrease = Math.floor(is24 / 24);
							totalPrice = decrease * weekendPrice;
							is24 = is24%24
							if(is24 > 0){
								totalPrice += weekendPrice;
							}

						}else {
							if(firstDateAvailableNumber == firstDateSelectedNumber){
								if(_firstDate.getDay() == 6 || _firstDate.getDay() == 0){
									totalPrice = weekendPrice;
								}else{
									totalPrice = dailyPrice;
								}
							}else{
								if(_firstDate.getDay() == 6 || _firstDate.getDay() == 0){
									totalPrice = firstDateSelectedNumber * hourPrice;
									if(totalPrice > weekendPrice){
										totalPrice = weekendPrice;
									}
								}else{
									totalPrice = firstDateSelectedNumber * hourPrice;
									if(totalPrice > dailyPrice){
										totalPrice = dailyPrice;
									}
								}
							}


							if(_numberOfDays > 1){
								for(var i = 1; i < _numberOfDays; i++){
									midDate.setDate(_firstDate.getDate() + i);
									if(midDate.getDay() == 6 || midDate.getDay() == 0){
										totalPrice += weekendPrice;
									}else{
										totalPrice += dailyPrice;
									}
								}
							}

							if(secondDateAvailableNumber == secondDateSelectedNumber){
								if(_secondDate.getDay() == 6 || _secondDate.getDay() == 0){
									totalPrice += weekendPrice;
								}else{
									totalPrice += dailyPrice;
								}
							}else{
								lastDayPrice = secondDateSelectedNumber * hourPrice;
								if(_secondDate.getDay() == 6 || _secondDate.getDay() == 0){
									if(lastDayPrice > weekendPrice){
										totalPrice += weekendPrice;
									}else{
										totalPrice +=lastDayPrice;
									}
								}else{
									if(lastDayPrice > dailyPrice){
										totalPrice += dailyPrice;
									}else{
										totalPrice +=lastDayPrice;
									}
								}

							}
						}
					}
				}, 500);
				localStorage.setItem('totalPrice',totalPrice);
				
		},1000);
        jQuery(".booking-error-message").hide();
        jQuery('.booking-discount-price').hide();
		jQuery('.booking-post-price').hide();



		window.setTimeout(function(){
			var startDataSql = firstday;
			var endDataSql = secondday;
			var discount = jQuery('input[name="discount"]:checked').val();

			// preparing data for ajax
			var ajax_data = {
				'action': 'check_avaliabity', 
				'listing_type' : $('#date-picker').attr('listing_type'),
				'listing_id' : 	$('input#listing_id').val(),
				'date_start' : startDataSql,
				'date_end' : endDataSql,
				'discount' : discount,
				'coupon' : 	$('input#coupon_code').val(),
				//'nonce': nonce		
			};
			var services = [];
			
			$.each($("input.bookable-service-checkbox:checked"), function(){   
				var quantity = $(this).parent().find('input.bookable-service-quantity').val();
				services.push({"service" : $(this).val(), "value" : quantity});
			});
		
			ajax_data.services = services;
			
			var st = $('.startDate').text();
			var et = parseInt($('.endDate').text());
			et = et + 1;
			

			var d = $('.endDate').parent().parent().attr('day');
			if ( $('input#slot').val() ){
				ajax_data.slot = $('input#slot').val();
			}else{
				ajax_data.slot = `["${st} - ${et}:00","${d}|0"]`;
			}
			 
			if ( $('input.adults').val() ) ajax_data.adults = $('input.adults').val();
			if ( $('.time-picker').val() ) ajax_data.hour = $('.time-picker').val();
			

			// loader class
			$('a.book-now').addClass('loading');
			$('a.book-now-notloggedin').addClass('loading');

			ajax_data.totalDays = totalDays;
			ajax_data.totalPrice = totalPrice;


			//change discount  MULTIPLE AJAX REQUESTS !!!!
			
			$.ajax({
				type: 'POST', dataType: 'json',
				url: listeo.ajaxurl,
				data: ajax_data,
				
				success: function(data){
					jQuery(".show_charged").show();

					if(jQuery("#toHours").val() != "Select time"){
						 jQuery('#toHours').removeAttr("style");
					}

					// jQuery(".booking-error-message").show();

					// loader clas

					if (data.success == true && ( ! $(".time-picker").length || is_open != false ) ) {
						if ( data.data.free_places > 0) {
								$('a.book-now').data('freeplaces',data.data.free_places);
								$('.booking-error-message').fadeOut();
								$('a.book-now').removeClass('inactive');

                            
                            if(data.data.discount_price != undefined && data.data.discount_price > 0){

                            	if (listeo_core.currency_position == 'before') {
									
									$('.booking-discount-price span').html(listeo_core.currency_symbol + ' ' + data.data.discount_price);
									$('.booking-post-price span').html(listeo_core.currency_symbol + ' ' + data.data.post_price);
									
								} else {
									$('.booking-discount-price span').html(data.data.discount_price + ' ' + listeo_core.currency_symbol);
									$('.booking-post-price span').html(data.data.post_price + ' ' + listeo_core.currency_symbol);
									
								}
								//$('.booking-discount-price').fadeIn();
								//$('.booking-post-price').fadeIn();

                            }

							if (data.data.normal_price > 0) {
								// Add services, tax, normal at normal price
								var allValuesTaxes = 0;
								if(data.data.normal_price){
									allValuesTaxes += data.data.normal_price;
								}
								// if(data.data.services_price){
								// 	allValuesTaxes += data.data.services_price;
								// }
								if(data.data.taxprice){
									allValuesTaxes += data.data.taxprice;
								}

								if (listeo_core.currency_position == 'before') {
									if ($('.categoryName span').attr('data-cat') == 'utstr') {
										// $('.booking-normal-price span').html(data.data.multiply + ' x ' + listeo_core.currency_symbol + ' ' + data.data.normal_price);
										$('.booking-normal-price span').html(data.data.multiply + ' x ' + listeo_core.currency_symbol + ' ' + allValuesTaxes);
									} else {
										// $('.booking-normal-price span').html(listeo_core.currency_symbol + ' ' + data.data.normal_price);
										$('.booking-normal-price span').html(listeo_core.currency_symbol + ' ' + allValuesTaxes);
									}
								} else {
									if ($('.categoryName span').attr('data-cat') == 'utstr') {
										// $('.booking-normal-price span').html(data.data.multiply + ' x ' + data.data.normal_price + ' ' + listeo_core.currency_symbol);
										$('.booking-normal-price span').html(data.data.multiply + ' x ' + allValuesTaxes + ' ' + listeo_core.currency_symbol);
									} else {
										// $('.booking-normal-price span').html(data.data.normal_price + ' ' + listeo_core.currency_symbol);
										$('.booking-normal-price span').html(allValuesTaxes + ' ' + listeo_core.currency_symbol);
									}
								}

								$('.booking-normal-price').fadeIn();
								$('.free-booking').fadeOut();
							} else {
								$('.booking-normal-price').fadeOut();
								$('.free-booking').fadeIn();
							}

							if(data.data.services_price > 0 ) {
								if(listeo_core.currency_position=='before'){
									$('.booking-services-cost span').html(listeo_core.currency_symbol+' '+data.data.services_price);
								} else {
									$('.booking-services-cost span').html(data.data.services_price+' '+listeo_core.currency_symbol);
								}

								$('.booking-services-cost').fadeIn();
							} else {
								//$('.booking-services-cost span').html( 'GRATIS');
								$('.booking-services-cost').fadeOut();
							}
							if(data.data.price < 1){
								jQuery(".show_charged").hide();
							}

							if(data.data.price > 0 ) {
								var _total_price = 0;
								var _coupon_price = 0;

								_total_price = data.data.price;

								/* Check coupon price value and change price value*/
								if(data.data.coupon_price){
									_coupon_price = data.data.coupon_price;
									_total_price = data.data.normal_price + data.data.services_price + data.data.taxprice;
									// _total_price = data.data.normal_price + data.data.taxprice;

									if(listeo_core.currency_position=='before'){
										$('.booking-estimated-discount-cost span').html(listeo_core.currency_symbol+' '+data.data.coupon_price);	
									} else {
										$('.booking-estimated-discount-cost span').html(data.data.coupon_price+' '+listeo_core.currency_symbol);	
									}
									$('.booking-estimated-cost').addClass('estimated-with-discount');
									$('.booking-estimated-discount-cost').fadeIn();
								}  else {
									_total_price = data.data.price;
									$('.booking-estimated-cost').removeClass('estimated-with-discount');
									$('.booking-estimated-discount-cost').fadeOut();
								}

								if(listeo_core.currency_position=='before'){
									// $('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);
									$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+_total_price);
									$('.booking-estimated-cost div.tax-span').html(listeo_core.currency_symbol+' '+data.data.taxprice+data.data.services_tax_price);
								} else {
									// $('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);
									$('.booking-estimated-cost span').html(_total_price+' '+listeo_core.currency_symbol);
									$('.booking-estimated-cost div.tax-span').html(data.data.taxprice+data.data.services_tax_price+' '+listeo_core.currency_symbol);
								}

								$('.booking-estimated-cost').fadeIn();
								$('.coupon-widget-wrapper').fadeIn();
							} else {
								$('.booking-estimated-cost span').html( '0 '+listeo_core.currency_symbol);

								$('.booking-estimated-cost').fadeOut();
							}

					    } else {
							$('a.book-now').data('freeplaces',0);
							if(jQuery('.categoryName span').attr('data-cat') == 'utstr'){
								jQuery([document.documentElement, document.body]).animate({
									scrollTop: jQuery("#equipmentCalendar").offset().top - 200
								}, 2000);
					
								jQuery('#equipmentCalendar').css({border: '0 solid red'}).animate({
									borderWidth: 4
								}, 500);
								
								setTimeout(() => {
									jQuery('#equipmentCalendar').animate({
										borderWidth: 0
									}, 500);
								}, 1500);
							}
							if(endDataSql){
								$('.booking-error-message').fadeIn();
							}
							$('.booking-estimated-cost').fadeOut();
							$('.booking-estimated-cost span').html('');
						}
					} else {
						$('a.book-now').data('freeplaces',0);
						if(jQuery('.categoryName span').attr('data-cat') == 'utstr'){
							jQuery([document.documentElement, document.body]).animate({
								scrollTop: jQuery("#equipmentCalendar").offset().top - 200
							}, 2000);
				
							jQuery('#equipmentCalendar').css({border: '0 solid red'}).animate({
								borderWidth: 4
							}, 500);
							
							setTimeout(() => {
								jQuery('#equipmentCalendar').animate({
									borderWidth: 0
								}, 500);
							}, 1500);
						}
						if(endDataSql){
							$('.booking-error-message').fadeIn();
						}
						$('.booking-estimated-cost').fadeOut();
					}

					$('a.book-now').removeClass('loading');
					$('a.book-now-notloggedin').removeClass('loading');

					
					if($('#divtoshow').is(':visible')){
						$('.booking-estimated-cost').hide();
						$('.booking-services-cost').hide();
						$('.booking-normal-price').hide();
						$('.free-booking').fadeOut();
					}else{
						if(data.data.price > 0 ){
							jQuery('.booking-estimated-cost').show();
							jQuery(".coupon-widget-wrapper").show();
						}
					}

					// if(data.data.price <= 0 && data.data.services_price <= 0 && data.data.normal_price <= 0){
					// 	$('.free-booking').fadeIn();
					// }else{
					// 	$('.free-booking').fadeOut();
					// }
				}
			});
		}, 2000);

	}

	var is_open = true;
	var lastDayOfWeek;




	// update slots and check hours setted to this day
	function update_booking_widget () 
	{   
		
		// function only for services
		if ( $('#date-picker').attr('listing_type') != 'service') return;
		$('a.book-now').addClass('loading');
		$('a.book-now-notloggedin').addClass('loading');
		// get day of week
		var date = $('#date-picker').data('daterangepicker').endDate._d;
		var dayOfWeek = date.getDay() - 1;
	
		if(date.getDay() == 0){
			dayOfWeek = 6;
		}
		
		
		
		var firstday = localStorage.getItem('firstDate');
		
		var secondday;
		window.setTimeout(function(){
			secondday = $('.time-slot .endDate').attr('date');
			

		},100);

		window.setTimeout(function(){
			var startDataSql = firstday;
			var endDataSql = secondday;
			
		
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
					var cou = 0;
					var firstinput;
					var secondinput;
					
					$( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).show( );
					$(".time-slot").each(function() {
						var timeSlot = $(this);
						$(this).find('input').on('click',function() {
							if(cou == 0){
								firstinput =  timeSlot.find('.tests').attr('class').split(' ')[1];
								cou++;
							}else if(cou == 1){
								secondinput =  timeSlot.find('.tests').attr('class').split(' ')[1];
								cou++;					
							}else{
								firstinput =  timeSlot.find('.tests').attr('class').split(' ')[1];
								secondinput = undefined;
								cou = 1;
							}
							
							var timeSlotVal = timeSlot.find('.tests').attr('class').split(' ').pop();
							secondinput = parseInt(secondinput)+1;
							var slotArray = [`${firstinput} - ${secondinput}:00`, timeSlot.find('input').val()];
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
		
		}, 100)

		// check if opening days are active
		if ( $(".time-picker").length ) {
			if(availableDays){
                
                
				if ( availableDays[dayOfWeek].opening == 'Closed' || availableDays[dayOfWeek].closing == 'Closed') 
				{

					$('#negative-feedback').fadeIn();

					//$('a.book-now').css('background-color','grey');
					
					is_open = false;
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
					

				} else {
					
					is_open = false;
					$('#negative-feedback').fadeIn();
					//$('a.book-now').attr('href','#').css('background-color','grey');
					$('.booking-estimated-cost span').html('');

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
			onChange: function(selectedDates, dateStr, instance) {
                
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
      if(oldValue == 2) {
      	//$button.parents('.single-service').find('label').trigger('click');
      	$button.parents('.single-service').find('input.bookable-service-checkbox').prop("checked",true);
      	updateCounter();
      }
});


if ( $( "#booking-date-range" ).length ) {

	// to update view with bookin

	var bookingsOffset = 0;

	// here we can set how many bookings per page
	var bookingsLimit = 5;

	// function when checking booking by widget
	function listeo_bookings_manage(page) 
	{
		
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
	        ranges: {
	           'Today': [moment(), moment()],
	           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'This Month': [moment().startOf('month'), moment().endOf('month')],
	           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
