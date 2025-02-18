	/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){ 


  $('.listeo_core-dashboard-action-delete').click(function(e) {
        e.preventDefault();
        if (window.confirm(listeo_core.areyousure)) {
            location.href = this.href;
        }
    });

   // $('.booking-widget').after("<div class='booking-widget-hook'></div>");
   
   //  $(window).on('load resize', function() {
   //        var winWidth = $(window).width();
   //        if (winWidth < 992) {
   //           $('.booking-widget').insertAfter("#titlebar");
   //        } else if (winWidth > 992) {
   //           $('.booking-widget').insertAfter(".booking-widget-hook");
   //        }
   //  });
  $(document).on('click', ".listeo_core-bookmark-it",function(e){
  //$('body').on('click', ".listeo_core-bookmark-it", function(e){

      e.preventDefault();
      if($(this).is('.clicked,.liked')){
      	return;
      }
    	$(this).addClass('clicked');
      jQuery(".loader_div").addClass('loading_overlay');


      var post_id 	= $(this).data("post_id"),
      handler 		= $(this),
      nonce 			= $(this).data("nonce"),
      addedtolist 	= $(this).data("saved-title")

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: listeo.ajaxurl,
         data 	: {
          action: "listeo_core_bookmark_this", 
          post_id : post_id, 
          nonce: nonce
        },
         success	: function(response) {
           jQuery(".loader_div").removeClass('loading_overlay');
          console.log(response);
            if(response.type == "success") {
               handler.removeClass('listeo_core-bookmark-it').addClass('liked').addClass('listeo_core-unbookmark-it').removeClass('clicked');
               var confirmed = handler.data('confirm');
               handler.children('.like-icon').addClass('liked').removeClass('clicked').parent().html('<span class="like-icon liked"></span> '+confirmed);
          	   
            }
            else {
               
               handler.removeClass('clicked');
               handler.children('.like-icon').removeClass('liked');
            }
         }
      })   
  });


  


  $(document).on('click', ".listeo_core-unbookmark-it",function(e){
      e.preventDefault();
      jQuery(".loader_div").addClass('loading_overlay');
      var handler = $(this);
      var post_id = $(this).data("post_id");
      var nonce = $(this).data("nonce");
      $(this).addClass('clicked');
      handler.closest('li').addClass('opacity-05');
      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: listeo.ajaxurl,
          data 	: {action: "listeo_core_unbookmark_this", post_id : post_id, nonce: nonce},
          success	: function(response) {
            jQuery(".loader_div").removeClass('loading_overlay');
           console.log(handler);
           console.log(response);
            if(response.type == "success") {
              handler.removeClass('listeo_core-unbookmark-it').addClass('listeo_core-bookmark-it');
                handler.closest('li').fadeOut();
                handler.removeClass('clicked');
                handler.removeClass('liked');
                handler.children('.like-icon').removeClass('liked');
            }
            else {
               
               handler.closest('li').removeClass('opacity-05');
            }
           
         }
      })   
  });

  // Choose listing type step
  $(".add-listing-section.type-selection a").on('click', function(e) {
      e.preventDefault();
      var type = $(this).data('type');
      $("#listing_type").val(type);
      $("#submit-listing-form").submit();
  });

  $(".add-listing-section.type-selection a").on('click', function(e) {
      e.preventDefault();
      var type = $(this).data('type');
      $("#listing_type").val(type);
      $("#submit-listing-form").submit();
  });

  
  var elements = document.querySelectorAll('input,select,textarea');

  for (var i = elements.length; i--;) {
      elements[i].addEventListener('invalid', function () {
          this.scrollIntoView(false);
      });
  }

  
  $('.add-listing-section.availability_calendar').on("click", 'span.calendar-day-date', function(e) { 
      e.preventDefault();
      var td = $(this).parent();
      var timestamp = td.data('timestamp');
      var date = td.data('date');
      var $el = $(".listeo-calendar-avail");

      if(td.hasClass('not_active')){
          td.removeClass('not_active');
          var current_dates = $el.val();
          console.log(current_dates);
          console.log(date + "|")
          current_dates = current_dates.replace(date + "|","");
          console.log(current_dates);
          $el.val(current_dates);
      } else {
         td.addClass('not_active');
         $el.val( $el.val() + date + "|");
      }
      
      
  });

  $('.add-listing-section.availability_calendar').on("click", 'button', function(e) { 
    e.preventDefault();
      var td = $(this).parent().parent();
      var timestamp = td.data('timestamp');
      var date = td.data('date');
      var $el = $(".listeo-calendar-avail");
      var current_price = $(this).prev('span').text();
   
      var price = (function ask() {        
        var n = prompt(listeo_core.prompt_price);
        console.log(typeof n);
        if (n === null) {
         return n;
        } else if ( n === '' ) {
          return current_price;
        }
         else {
          return isNaN(n) ? ask() : n;  
        }
        
      }());
      var json = {};
      var reg_price;
      if(td.hasClass('weekend')){
        reg_price = $('#_weekday_price').val();
      } else {
        reg_price = $('#_normal_price').val();
      }
      if (price != null && price != reg_price) {
        $(this).parent().find('span').html(price);
          // json.push({
          //   date : price
          // });
          var current_value = $(".listeo-calendar-price").val();
          if(current_value) {
            var json = jQuery.parseJSON($(".listeo-calendar-price").val());  
          }
          json[date] = price;
          var stringit = JSON.stringify(json);
          $(".listeo-calendar-price").val(stringit);
      }
      if(price== reg_price){
          $(this).parent().find('span').html(price);
          var current_value = $(".listeo-calendar-price").val();
          if(current_value) {
            var json = jQuery.parseJSON($(".listeo-calendar-price").val());  
          }
          delete json[date];
          var stringit = JSON.stringify(json);
          $(".listeo-calendar-price").val(stringit);
      }

  });

  $('#_normal_price').on('input', function(e) {
      e.preventDefault();
      var price = $(this).val();
      $('.listeo-calendar-day:not(.weekend) .calendar-price span').html(price);
      submit_calendar_update_price();
  });

  $('#_weekend_price,#_weekday_price').on('input', function(e) {
      e.preventDefault();
      var price = $(this).val();
      $('.listeo-calendar-day.weekend .calendar-price span').html(price);
      submit_calendar_update_price();
  
  });


  $('.add-listing-section.availability_calendar').on("click", '.prev', function(event) { 
      var month =  $(this).data("prev-month");
      var year =  $(this).data("prev-year");
      getCalendar(month,year);
  });
  $('.add-listing-section.availability_calendar').on("click", '.next', function(event) { 
      var month =  $(this).data("next-month");
      var year =  $(this).data("next-year");
      getCalendar(month,year);
  });
  $('.add-listing-section.availability_calendar').on("blur", '#currentYear', function(event) { 
      var month =  $('#currentMonth').text();
      var year = $('#currentYear').text();
      getCalendar(month,year);
  });

  function getCalendar(month,year){
     $.ajax({
         type   : "post",
         dataType : "json",
         url    : listeo_core.ajax_url,
         data   : { action: "listeo_core_calendar", month : month, year: year},
         success  : function(data) {
            $("#listeo-calendar-outer").html(data.response);  
             var _normal_price = $('#_normal_price').val();
  $('.listeo-calendar-day:not(.weekend) .calendar-price span').html(_normal_price);
  var _weekend_price = $('#_weekday_price').val();
   $('.listeo-calendar-day.weekend .calendar-price span').html(_weekend_price);
            submit_calendar_update_price();
            submit_calendar_update_unav_days();
         }
      })   
  }

  function submit_calendar_update_unav_days(){
      var days = $(".listeo-calendar-avail").val();
      if(days){
        var array = days.split("|");
        
        $.each( array, function( key, day ) {
          if( day ) {
            $("td.listeo-calendar-day[data-date='" + day +"']").addClass('not_active');
          }
        });
      }
      
  }

  function submit_calendar_update_price(){
      var prices = $(".listeo-calendar-price").val();
      if(prices){
         var obj = JSON.parse(prices);
      
      $.each( obj, function( day, price ) {
        if( day ) {
          $("td.listeo-calendar-day[data-date='" + day +"'] .calendar-price span").text(price);
        }
      });
      }
     
  }
  var _normal_price = $('#_normal_price').val();
  $('.listeo-calendar-day:not(.weekend) .calendar-price span').html(_normal_price);
  var _weekend_price = $('#_weekday_price').val();
   $('.listeo-calendar-day.weekend .calendar-price span').html(_weekend_price);
  submit_calendar_update_price();
  submit_calendar_update_unav_days();

    // send slots in json
    var slot_container = 0;
    var slots = new Array();

    $( "#submit-listing-form" ).submit(function( e ) {
       // e.preventDefault();
        // $( ".slots-container" ).each( function() {
        //     var inside_slots = new Array();
        //     var slot_number = 0;
        //    $( this ).find( '.single-slot-time' ).each( function(slot_time) {
        //         inside_slots[slot_number] = $( this ).text() + '|' + $( this ).parent().parent().find('#slot-qty').val();
        //         slot_number++;
        //    });
        //    slots[slot_container] = inside_slots;
        //    slot_container++;
        // });
        //debugger;
        $( ".opening_slots_container" ).each( function() {
            var inside_slots = new Array();
            var slot_number = 0;

            var timmee = $( this ).find(".op_from_time").val()+" - "+$( this ).find(".op_to_time").val();

            let checkedd = "0";

            //debugger;

            if($( this ).find(".op_closed")[0].checked == true){
              checkedd = "1";
            }

           


            inside_slots[slot_number] = timmee + '|1|'+checkedd;

            slots[slot_container] = inside_slots;
            slot_container++;
        });
        
        $( '#_slots' ).val(JSON.stringify(slots));
        //console.log(JSON.stringify(slots));
        //$( this ).submit();
    });

   /* var json = jQuery.parseJSON($(".listeo-calendar-price").val());  
    
    json[date] = price;
    var stringit = JSON.stringify(json);*/


  $('#listeo-activities-list a.close-list-item').on('click',function(e) {
        var $this = $(this),
        id = $(this).data('id'),
        nonce = $(this).data('nonce');
       
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'remove_activity', 
                'id': id,
                'nonce': nonce
               },
            success: function(data){
              
                if (data.success == true){
                  $this.parent().addClass('wait').fadeOut( "normal", function() {
                    $this.remove();
                  });
                } else {
                                      
                }

            }
        });
        e.preventDefault();
    });

    $('#listeo-clear-activities').on('click',function(e) {
        var $this = $(this),
        nonce = $(this).data('nonce');
       
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'remove_all_activities', 
                'nonce': nonce
               },
            success: function(data){
              
                if (data.success == true){
                  $('ul#listeo-activities-list li:not(.cleared)').remove();
                  $('li.cleared').show();
                  $this.parent().parent().find('.pagination-container').remove();
                } else {
                                      
                }

            }
        });
        e.preventDefault();
    });

    $('select#sort-reviews-by').on('change',function(e) {
       var button = $(this);
       button.parents('.dashboard-list-box').addClass('loading');
       var page = button.find('#reviews_list_visitors').data('page');
       var post_id = $(this).val();
       $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'reload_reviews', 
                'id': post_id,
                'page': page,
                //'nonce': nonce
               },
            success: function(data){
                button.parents('.dashboard-list-box').removeClass('loading');
                if (data.success == true){
                    $('#reviews_list_visitors').html(data.comments);
                    $('#visitor-reviews-pagination').html(data.pagination);
                     $('.popup-with-zoom-anim').magnificPopup({
                       type: 'inline',

                       fixedContentPos: false,
                       fixedBgPos: true,

                       overflowY: 'auto',

                       closeBtnInside: true,
                       preloader: false,

                       midClick: true,
                       removalDelay: 300,
                       mainClass: 'my-mfp-zoom-in'
                    });
                } else {
                    console.log('error');                    
                }

            }
        });
        e.preventDefault();
     });

    
    $('#visitor-reviews-pagination').on('click','a', function(e){
      
        var page = $(this).parent().data('paged');
        var post_id = $('#sort-reviews-by').val();
        $('.reviews-visitior-box').addClass('loading');
         $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'reload_reviews', 
                'id': post_id,
                'page': page,
               
               },
            success: function(data){
                 $('.reviews-visitior-box').removeClass('loading');
                if (data.success == true){
                    $('#reviews_list_visitors').html(data.comments);
                    $('#visitor-reviews-pagination').html(data.pagination);
                     $('.popup-with-zoom-anim').magnificPopup({
                       type: 'inline',

                       fixedContentPos: false,
                       fixedBgPos: true,

                       overflowY: 'auto',

                       closeBtnInside: true,
                       preloader: false,

                       midClick: true,
                       removalDelay: 300,
                       mainClass: 'my-mfp-zoom-in'
                    });
                } else {
                    console.log('error');                                       
                }

            }
        });
        e.preventDefault();
    });


    
    $('.reviews-visitior-box').on('click','.reply-to-review-link', function(e){
        $('#comment_reply').val();
        var post_id = $(this).data('postid');
        var review_id = $(this).data('replyid');

        $('#send-comment-reply input#reply-post-id').val(post_id);
        $('#send-comment-reply input#reply-review-id').val(review_id);
    });

    $('.reviews-visitior-box').on('click','.edit-reply', function(e){
        //var review_id = $(this).parents('.review-li').data('review');
        $('#send-comment-edit-reply textarea#comment_reply').val('');
        var comment_id = $(this).data('comment-id');
         $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'get_comment_review_details', 
                'comment':  comment_id,
                //'nonce': nonce
               },
            success: function(data){
                var comment_content = data.comment_content;
                $('#send-comment-edit-reply textarea#comment_reply').val(comment_content);
                
            }
        });
        

        $('#send-comment-edit-reply input#reply_id').val(comment_id);
        

    });


    $('#send-comment-edit-reply').on('submit',function(e) {
        $('#send-comment-edit-reply button').addClass('loading');
        var content = $(this).find('textarea#comment_reply').val();
        var reply_id = $(this).find('input#reply_id').val();
        $.ajax({
            type: 'POST', 
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'edit_reply_to_review', 
                //'post_id':  $(this).find('input#reply-post-id').val(),
                'reply_id':  $(this).find('input#reply_id').val(),
                'content' : content,
                //'nonce': nonce
               },
            success: function(data){
              
                if (data.success == true){
                   $('#send-comment-edit-reply button').removeClass('loading');
                    $('.edit-reply[data-comment-id="'+reply_id+'"]').data('comment-content',content);
                   // $('#review-'+review_id+' .edit-reply')
                   $('.mfp-close').trigger('click');
                } else {
                    $('#send-comment-edit-reply button').removeClass('loading');   
                                    
                }

            }
        });
        e.preventDefault();
    })

    $('#send-comment-reply').on('submit',function(e) {

      $('#send-comment-reply button').addClass('loading');
      var review_id = $(this).find('input#reply-review-id').val();

       $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'reply_to_review', 
                'post_id':  $(this).find('input#reply-post-id').val(),
                'review_id':  review_id,
                'content' : $(this).find('textarea#comment_reply').val(),
                //'nonce': nonce
               },
            success: function(data){
              console.log(data);
                if (data.success == true){
                   $('#send-comment-reply button').removeClass('loading');
                   $('.mfp-close').trigger('click');
                   $('#review-'+review_id+' .reply-to-review-link').html('<i class="sl sl-icon-check"></i> '+listeo_core.replied).off('click');
                   //location.reload();
                } else {
                  $('#send-comment-reply button').removeClass('loading');   
                                    
                }

            }
        });
        e.preventDefault();
     });

  var critera = listeo_core.review_criteria.split(',');

  $('.your-reviews-box').on('click','.edit-review', function(e){
        //var review_id = $(this).parents('.review-li').data('review');
        $('#send-comment-edit-review input[type=radio]').prop( "checked", false );
        $('#send-comment-edit-review textarea#comment_reply').val('');
        $('.message-reply').addClass('loading');
        var comment_id = $(this).data('comment-id');

         $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'get_comment_review_details', 
                'comment':  comment_id,
                //'nonce': nonce
               },
            success: function(data){
              
                $('#send-comment-edit-review input#reply_id').val(comment_id);
                $('#send-comment-edit-review input#rating-'+data.comment_rating).prop( "checked", true );
               $('.sub-ratings-container').html(data.ratings);
                // $.each( critera, function( index, value ){
                //     var rate_val = data[value];
                    
                //     console.log(rate_val);
                    
                //     $("#send-comment-edit-review input#rating-"+value+"-"+rate_val).prop( "checked", true );
                // });
                $('#send-comment-edit-review textarea#comment_reply').val(data.comment_content);
                $('.message-reply').removeClass('loading');
            }
        });

        // var button = $(this);
        // var comment_content = $(this).data('comment-content');
        // var comment_rating = $(this).data('comment-rating');

        // $('#send-comment-edit-review input#reply_id').val(comment_id);
        
        // $('#send-comment-edit-review input#rating-'+comment_rating).prop( "checked", true );
        // $.each( critera, function( index, value ){
        //     var rate_val = button.data('comment-'+value);
        //     $('#send-comment-edit-review input#rating-'+value+'-'+rate_val).prop( "checked", true );
        // });
        // $('#send-comment-edit-review textarea#comment_reply').val(comment_content);

    });


   $('.listeo_core-dashboard-delete-review').click(function(e) {
        e.preventDefault();
        if (window.confirm(listeo_core.areyousure)) {
            location.href = this.href;
        }
    });
        
    function get_url_extension( url ) {
        return url.split(/\#|\?/)[0].split('.').pop().trim();
    }

    $('body').on('submit', ".ical-import-form", function(e){

        e.preventDefault();

        $(this).find('button').addClass('loading');
        $('input.import_ical_url').removeClass('bounce');
        
        var form = $(this);
        var listing_id   = $(this).data('listing-id');
        var name         = $(this).find('input.import_ical_name').val();
        var url          = $(this).find('input.import_ical_url').val();
        var force_update = $(this).find('input.import_ical_force_update').prop('checked');
        var filetype = get_url_extension(url); //validate for .ical, .ics, .ifb, .icalendar
        
        var valid_filetypes = [ 'ical', 'ics', 'ifb', 'icalendar', 'calendar' ];
        
        if( url.indexOf('calendar') !== -1 || url.indexOf('accommodation_id') !== -1 || url.indexOf('ical') !== -1 || $.inArray( filetype, valid_filetypes ) > -1 ) {
        
            $.ajax({
              type: 'POST', 
              dataType: 'json',
              url: listeo.ajaxurl,
              data: { 
                  'action': 'add_new_listing_ical', 
                  'name':   name,
                  'url':    url,
                  'listing_id':    listing_id,
                  'force_update': force_update
                  //'nonce': nonce
                 },
              success: function(data){
                  
                if (data.type == 'success'){
                    
                    form.find('button').removeClass('loading');
                    form.find('input.import_ical_name').val('');
                    form.find('input.import_ical_url').val('');
                    form.parents('.ical-import-dialog').find('.saved-icals').html(data.output);
                    $('.ical-import-dialog .notification').removeClass('error notice').addClass('success').show().html(data.notification);
                
                }
                
                if (data.type == 'error'){
                  form.find('button').removeClass('loading');
                  
                  $('.ical-import-dialog .notification').removeClass('success notice').addClass('error').show().html(data.notification);
                }

              }
          });
        } else {
          $(this).find('button').removeClass('loading');
          $('input.import_ical_url').addClass('bounce');
          window.setTimeout( function(){ $('input.import_ical_url').removeClass('bounce'); }, 1000);
        }
      

    });

      
    $('body').on('click', "a.ical-remove", function(e){
        e.preventDefault();
        var $this = $(this),
        index = $(this).data('remove'),
        nonce = $(this).data('nonce');
        var listing_id  = $(this).data('listing-id');
        $this.parents('.saved-icals').addClass('loading');

        $.ajax({
            type: 'POST', 
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action':     'add_remove_listing_ical', 
                'index':      index,
                'listing_id': listing_id,
                //'nonce': nonce
               },
            success: function(data){
                
               if (data.type == 'success'){
                  $this.parents('.saved-icals').removeClass('loading').html(data.output);

               }
               $('.ical-import-dialog .notification').show().html(data.notification);

            }
        });
    });

    $('body').on('click', "a.update-all-icals", function(e){
        e.preventDefault();
        var $this = $(this),
        listing_id  = $(this).data('listing-id');
        $this.addClass('loading');
         $.ajax({
            type: 'POST', 
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action':     'refresh_listing_import_ical', 
                'listing_id': listing_id,
                //'nonce': nonce
               },
            success: function(data){
                $this.removeClass('loading');
               if (data.type == 'success'){
                  $('.ical-import-dialog .notification').removeClass('error notice').addClass('success').show().html(data.notification);
               } else if(data.type == 'error') {
                  $('.ical-import-dialog .notification').removeClass('success notice').addClass('error').show().html(data.notification);
               }
             
            }
        });
    });


    $('#send-comment-edit-review').on('submit',function(e) {
        $('#send-comment-edit-review button').addClass('loading');
        var value = 'service';
        var button = $(this);
        var content = $(this).find('textarea#comment_reply').val();
        var reply_id = $(this).find('input#reply_id').val();
        var reply_rating= $(this).find('input[type="radio"]:checked').val();
        
        var data = { 
                'action': 'edit_review', 
                //'post_id':  $(this).find('input#reply-post-id').val(),
                'reply_id':  $(this).find('input#reply_id').val(),
                'content' : content,
                //'nonce': nonce
               }; 
        $.each( critera, function( index, value ){
          data['rating_'+value] = button.find('input[type="radio"][name="'+value+'"]:checked').val();;
        });
        console.log(data);
       
        
        $.ajax({
            type: 'POST', 
            dataType: 'json',
            url: listeo.ajaxurl,
            data: data,
            success: function(data){
              
                if (data.success == true){
                   $('#send-comment-edit-review button').removeClass('loading');
                   
                   // $('#review-'+review_id+' .edit-reply')
                   $('.mfp-close').trigger('click');
                } else {
                    $('#send-comment-edit-review button').removeClass('loading');   
                                    
                }

            }
        });
        e.preventDefault();
    })


    
    $('a.listeo_core-rate-review').on('click',function(e) {

      e.preventDefault();
        var $this = $(this),
        comment = $(this).data('comment'),
        nonce = $(this).data('nonce');
       
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'listeo_core_rate_review', 
                'comment': comment,
                //'nonce': nonce
               },
            success: function(data){
              
                
                 $this.html(data.output)
                
            }
        });
         e.preventDefault();
     });

    // Contact Form Ajax

    $('#send-message-from-widget').on('submit',function(e) {
      $('#send-message-from-widget button').addClass('loading').prop('disabled', true);

       $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'listeo_send_message', 
                'recipient' : $(this).find('textarea#contact-message').data('recipient'),
                'referral' : $(this).find('textarea#contact-message').data('referral'),
                'message' : $(this).find('textarea#contact-message').val(),
                //'nonce': nonce
               },
            success: function(data){
              
                if(data.type == "success") {

                  $('#send-message-from-widget button').removeClass('loading');
                  $('#send-message-from-widget .notification').show().html(data.message);
                  window.setTimeout( closepopup, 3000 );
                  $(this).find('textarea#contact-message').val('');
                  
                } else {
                    $('#send-message-from-widget .notification').removeClass('success').addClass('error').show().html(data.message);
                    $('#send-message-from-widget button').removeClass('loading').prop('disabled', false);
                }

            }
        });
        e.preventDefault();
    }); 

    function closepopup(){
      var magnificPopup = $.magnificPopup.instance; 
      if(magnificPopup) {
          magnificPopup.close();   
          $('#send-message-from-widget button').removeClass('loading').prop('disabled', false);
      }
    }  

    $('#send-message-from-chat').on('submit',function(e) {
      
      var message = $(this).find('textarea#contact-message').val();

      if(message){
        $(this).find('textarea#contact-message').removeClass('error');
        $('.loading').show();
        $(this).find('button').prop('disabled', true);
         $.ajax({
              type: 'POST', dataType: 'json',
              url: listeo.ajaxurl,
              data: { 
                  'action': 'listeo_send_message_chat', 
                  'recipient' : $(this).find('input#recipient').val(),
                  'conversation_id' : $(this).find('input#conversation_id').val(),
                  'message' : message,
                  //'nonce': nonce
                 },
              success: function(data){
                
                  if(data.type == "success") {
                      $(this).addClass('success');     
                      window.location.reload();               
                      refreshMessages();
                      $('#send-message-from-chat textarea').val('');
                      $('#send-message-from-chat button').prop('disabled', false);
                  } else {
                      $(this).addClass('error')                    
                  }

              }
          });
       } else {
          $(this).find('textarea#contact-message').addClass('error');

       }
        e.preventDefault();
    }); 

    $(document).on('click', '.booking-message', function(e) {
      var recipient = $(this).data('recipient');
      var referral = $(this).data('booking_id');
    $('#send-message-from-widget textarea').val('');
    $('#send-message-from-widget .notification').hide();

    
      $('#send-message-from-widget textarea').data('referral',referral).data('recipient',recipient);
      
    
      $('.send-message-to-owner').trigger('click');
    });
    
    function refreshMessages(){
      if($('.message-bubbles').length){


        $.ajax({
            type: 'POST', dataType: 'json',
            url: listeo.ajaxurl,
            data: { 
                'action': 'listeo_get_conversation', 
                'conversation_id' : $('#send-message-from-chat input#conversation_id').val(),
                //'nonce': nonce
               },
            success: function(data){
              
                if(data.type == "success") {
                   // $('.message-bubbles').html(data.message);
                }
                $('.loading').hide();
             
            },
            complete: function() {
              setTimeout(refreshMessages, 4000);
            }
        });
 
      }
    }
    setTimeout(refreshMessages, 4000);

   
if($("#coupon_bg-uploader").length>0) {
   /* Upload using dropzone */
    Dropzone.autoDiscover = false;

    var couponDropzone = new Dropzone ("#coupon_bg-uploader", {
      url: listeo_core.upload,
      maxFiles:1,
      maxFilesize:listeo_core.maxFilesize,
      dictDefaultMessage: listeo_core.dictDefaultMessage,
      dictFallbackMessage: listeo_core.dictFallbackMessage,
      dictFallbackText: listeo_core.dictFallbackText,
      dictFileTooBig: listeo_core.dictFileTooBig,
      dictInvalidFileType: listeo_core.dictInvalidFileType,
      dictResponseError: listeo_core.dictResponseError,
      dictCancelUpload: listeo_core.dictCancelUpload,
      dictCancelUploadConfirmation: listeo_core.dictCancelUploadConfirmation,
      dictRemoveFile: listeo_core.dictRemoveFile,
      dictMaxFilesExceeded: listeo_core.dictMaxFilesExceeded,
        acceptedFiles: 'image/*',
      accept: function(file, done) {
         
          done();
        },
      init: function() {
            this.on("addedfile", function() {
              if (this.files[1]!=null){
                this.removeFile(this.files[0]);
              }
            });
      },   

        success: function (file, response) {
            file.previewElement.classList.add("dz-success");
            file['attachment_id'] = response; // push the id for future reference
            console.log(file['attachment_id']);
            $("#coupon_bg-uploader-id").val(file['attachment_id']);

        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        },
        // update the following section is for removing image from library
        addRemoveLinks: true,
        removedfile: function(file) {
          var attachment_id = file['attachment_id'];
            $("#coupon_bg-uploader-id").val('');
            $.ajax({
                type: 'POST',
                url: listeo_core.delete,
                data: {
                    media_id : attachment_id
                }, 
                success: function (result) {
                     console.log(result);
                  },
                  error: function () {
                      console.log("delete error");
                  }
            });
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
        }
    });

    couponDropzone.on("maxfilesexceeded", function(file)
    {
        this.removeFile(file);
    });
    if($('.edit-coupon-photo').attr('data-photo')){
      var mockFile = { name: $('.edit-coupon-photo').attr('data-name'), size: $('.edit-coupon-photo').attr('data-size') };
        couponDropzone.emit("addedfile", mockFile);
        couponDropzone.emit("thumbnail", mockFile, $('.edit-coupon-photo').attr('data-photo'));
        couponDropzone.emit("complete", mockFile);
        couponDropzone.files.push(mockFile);
      // If you use the maxFiles option, make sure you adjust it to the
      // correct amount:
      
      couponDropzone.options.maxFiles = 1;
    }


}
    
if($("#avatar-uploader").length>0) {
   /* Upload using dropzone */
    Dropzone.autoDiscover = false;

    var avatarDropzone = new Dropzone ("#avatar-uploader", {
      url: listeo_core.upload,
      maxFiles:1,
      maxFilesize:listeo_core.maxFilesize,
      dictDefaultMessage: listeo_core.dictDefaultMessage,
      dictFallbackMessage: listeo_core.dictFallbackMessage,
      dictFallbackText: listeo_core.dictFallbackText,
      dictFileTooBig: listeo_core.dictFileTooBig,
      dictInvalidFileType: listeo_core.dictInvalidFileType,
      dictResponseError: listeo_core.dictResponseError,
      dictCancelUpload: listeo_core.dictCancelUpload,
      dictCancelUploadConfirmation: listeo_core.dictCancelUploadConfirmation,
      dictRemoveFile: listeo_core.dictRemoveFile,
      dictMaxFilesExceeded: listeo_core.dictMaxFilesExceeded,
        acceptedFiles: 'image/*',
      accept: function(file, done) {
         
          done();
        },
      init: function() {
            this.on("addedfile", function() {
              if (this.files[1]!=null){
                this.removeFile(this.files[0]);
              }
            });
      },   

        success: function (file, response) {
            file.previewElement.classList.add("dz-success");
            file['attachment_id'] = response; // push the id for future reference
            $("#avatar-uploader-id").val(file['attachment_id']);

        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        },
        // update the following section is for removing image from library
        addRemoveLinks: true,
        removedfile: function(file) {
          var attachment_id = file['attachment_id'];
            $("#avatar-uploader-id").val('');
            $.ajax({
                type: 'POST',
                url: listeo_core.delete,
                data: {
                    media_id : attachment_id
                }, 
                success: function (result) {
                     console.log(result);
                  },
                  error: function () {
                      console.log("delete error");
                  }
            });
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
        }
    });

    avatarDropzone.on("maxfilesexceeded", function(file)
    {
        this.removeFile(file);
    });
    if($('.edit-profile-photo').attr('data-photo')){
      var mockFile = { name: $('.edit-profile-photo').attr('data-name'), size: $('.edit-profile-photo').attr('data-size') };
        avatarDropzone.emit("addedfile", mockFile);
        avatarDropzone.emit("thumbnail", mockFile, $('.edit-profile-photo').attr('data-photo'));
        avatarDropzone.emit("complete", mockFile);
        avatarDropzone.files.push(mockFile);
      // If you use the maxFiles option, make sure you adjust it to the
      // correct amount:
      
      avatarDropzone.options.maxFiles = 1;
    }


}


  $('.dynamic #tax-listing_category,.dynamic #tax-listing_category-panel input').on('change',function(e) {
      var cat_ids = []
      
      $('#tax-listing_feature-panel .checkboxes').addClass('loading');
      $('#tax-listing_feature-panel .panel-buttons').hide();
      var panel = false;
      if($('#tax-listing_category-panel').length>0){
          panel = true;
          
          $("#tax-listing_category-panel input[type=checkbox]:checked").each(function(){
            
              cat_ids.push($(this).val());
          });
      } else {
          if($('#tax-listing_feature-panel').length>0){
          panel = true;  
          }
          if($(this).prop('multiple')){
              $('#tax-listing_category :selected').each(function(i, sel){ 
                  cat_ids.push( $(sel).val() ); 

              });
          } else {
            cat_ids.push($(this).val());  
          }
          
      }
      $.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_from_category', 
              'cat_ids' : cat_ids,
              'panel' : panel,
              //'nonce': nonce
             },
          success: function(data){
            $('#tax-listing_feature-panel .checkboxes').removeClass('loading');
            $('#tax-listing_feature-panel .checkboxes .row').html(data['output']).removeClass('loading');
            $('#tax-listing_feature').html(data['output']).removeClass('loading');
            if(data['success']){
              $('#tax-listing_feature-panel .panel-buttons').show();
            }

          }            
      });
  });
  $('.dynamic #tax-listing_category').trigger('change');
//listeo_get_listing_types_from_categories
   $('.dynamic-taxonomies #tax-listing_category,.dynamic-taxonomies #tax-listing_category-panel input').on('change',function(e) {
      var cat_ids = []
      
        $('.dynamic-taxonomies #listeo-search-form_tax-service_category').hide();
        $('.dynamic-taxonomies #listeo-search-form_tax-rental_category').hide();
        $('.dynamic-taxonomies #listeo-search-form_tax-event_category').hide();
        $('.dynamic-taxonomies #listeo-search-form_tax-service_category select, .dynamic-taxonomies #tax-service_category-panel select').val('');
        $('.dynamic-taxonomies #listeo-search-form_tax-rental_category select, .dynamic-taxonomies #tax-rental_category-panel select').val('');
        $('.dynamic-taxonomies #listeo-search-form_tax-event_category select, .dynamic-taxonomies #tax-event_category-panel select').val('');
        $('.dynamic-taxonomies #tax-service_category-panel').hide();
        $('.dynamic-taxonomies #tax-rental_category-panel').hide();
        $('.dynamic-taxonomies #tax-event_category-panel').hide();
        $('.dynamic-taxonomies #tax-service_category-panel input:checkbox').prop('checked', false);
        $('.dynamic-taxonomies #tax-rental_category-panel input:checkbox').prop('checked', false);
        $('.dynamic-taxonomies #tax-event_category-panel input:checkbox').prop('checked', false);

      $('.dynamic-taxonomies #tax-event_category-panel input:checkbox').removeAttr('checked');
      var panel = false;
      if($('#tax-listing_category-panel').length>0){
          panel = true;
          
          $("#tax-listing_category-panel input[type=checkbox]:checked").each(function(){
            
              cat_ids.push($(this).val());
          });
      } else {
          if($('#tax-listing_feature-panel').length>0){
          panel = true;  
          }
          if($(this).prop('multiple')){
              $('#tax-listing_category :selected').each(function(i, sel){ 
                  cat_ids.push( $(sel).val() ); 

              });
          } else {
            cat_ids.push($(this).val());  
          }
          
      }
      $.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_listing_types_from_categories', 
              'cat_ids' : cat_ids,
              'panel' : panel,
              //'nonce': nonce
             },
          success: function(data){
            // $('#tax-listing_feature-panel .checkboxes').removeClass('loading');
            // $('#tax-listing_feature-panel .checkboxes .row').html(data['output']).removeClass('loading');
            // $('#tax-listing_feature').html(data['output']).removeClass('loading');
            // if(data['success']){
            //   $('#tax-listing_feature-panel .panel-buttons').show();
            // }
            // 
            if(data['success']){
               var types = data['output'];
               if(types.includes('service')){
                $('.dynamic-taxonomies #listeo-search-form_tax-service_category').show();
                $('.dynamic-taxonomies #tax-service_category-panel').css('display', 'inline-block');
               } 
               if(types.includes('rental')){
                $('.dynamic-taxonomies #listeo-search-form_tax-rental_category').show();
                $('.dynamic-taxonomies #tax-rental_category-panel').css('display', 'inline-block');
               }
               if(types.includes('event')){
                $('.dynamic-taxonomies #listeo-search-form_tax-event_category').show();
                $('.dynamic-taxonomies #tax-event_category-panel').css('display', 'inline-block');
               }
            }
            

          }            
      });
  });

  $('.add-listing-section #listing_category,.add-listing-section #tax-listing_category').on('change',function(e) {
    
    var listing_id = $( "input[name='listing_id']" ).val();
    if($(this).prop('multiple')){
        var cat_ids;
        cat_ids = $(this).val();
    } else {
        var cat_ids = [];
        cat_ids.push($(this).val());  
    }
    
     $.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_ids_from_category', 
              'cat_ids' : cat_ids,
              'listing_id' : listing_id,
              'selected' :selected_listing_feature,
              'panel' : false,
              //'nonce': nonce
             },
          success: function(data){
            $('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').removeClass('loading');
            $('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').html(data['output']).removeClass('loading')
            
          }            
      });
  
  });

  var selected_listing_feature = [];
  if( $('.add-listing-section').length ){
    
          $.each($("input[name='tax_input[listing_feature][]']:checked"), function(){            
              selected_listing_feature.push($(this).val());
          });
          
          $('select#listing_category').trigger('change');
          $('select#tax-listing_category').trigger('change');

  }

  if( $('body').hasClass('tax-listing_category') || $('body').hasClass('post-type-archive-listing')){
    $('select#tax-listing_category').trigger('change');
    $('#tax-listing_category-panel input:checked').trigger('change');
  }
  $('#tax-listing_category-panel input:checked').trigger('change');
  // foreach
  // if($('.panel-dropdown-content .notification').length>0) {
  //   console.log($(this));
  //   $(this).find('.panel-buttons').hide();
  // }
  $( ".panel-dropdown-content .notification" ).each(function( index ) {
      $(this).parent().parent().find('.panel-buttons').hide();
  });

  
    var uploadButton = {
        $button    : $('.uploadButton-input'),
        $nameField : $('.uploadButton-file-name')
    };
 
    uploadButton.$button.on('change',function() {
        _populateFileField($(this));
    });
 
    function _populateFileField($button) {
        var selectedFile = [];
        for (var i = 0; i < $button.get(0).files.length; ++i) {
            selectedFile.push($button.get(0).files[i].name +'<br>');
        }
        uploadButton.$nameField.html(selectedFile);
    }


    /*----------------------------------------------------*/
  /* Time Slots
  /*----------------------------------------------------*/

    // Add validation parts
    $('.day-slots').each(function(){

      var daySlots = $(this);

    daySlots.find('.add-slot-btn').on('click', function(e) {
      e.preventDefault();

      



      var slotTime_Start = daySlots.find('.add-slot-inputs input.time-slot-start').val();
      var slotTimePM_AM_Start = daySlots.find('.add-slot-inputs select.time-slot-start').val();

      var slotTime_End = daySlots.find('.add-slot-inputs input.time-slot-end').val();
      var slotTimePM_AM_End = daySlots.find('.add-slot-inputs select.time-slot-end').val();

      if(slotTime_Start != "" && slotTime_End != ""){

        if(jQuery("body").find("input[name=_booking_system_weekly_view_status]").prop("checked")){
           daySlots.find(".add-slot").hide();
           daySlots.find(".availableHours").hide();
           daySlots.find(".single-slot-right").css("visibility","hidden");

        }
      }

      // Checks if input values are not blank
      if( slotTime_Start.length > 0 && slotTime_End.length > 0) {

            // New Time Slot Div
          var newTimeSlot = daySlots
                  .find('.single-slot.cloned')
                  .clone(true)
                  .addClass('slot-animation')
                  .removeClass('cloned');

          setTimeout(function(){
            newTimeSlot.removeClass('slot-animation');
          }, 300);

          newTimeSlot.find('.plusminus input').val('1');

          // Plus - Minus Init
            newTimeSlot.find('.plusminus').numberPicker();

          // Check if there's am/pm dropdown
            var $twelve_hr = $('.add-slot-inputs select.twelve-hr');

            if ( $twelve_hr.length){
                newTimeSlot.find('.single-slot-time').html(slotTime_Start + ' ' + '<i class="am-pm">'+slotTimePM_AM_Start+'</i>' + ' - '+ slotTime_End + ' ' + '<i class="am-pm">'+slotTimePM_AM_End+'</i>');
            } else {
              newTimeSlot.find('.single-slot-time').html(''+ slotTime_Start + ' - ' + slotTime_End);
            }

            // Appending new slot
          newTimeSlot.appendTo(daySlots.find('.slots-container'));

          // Refresh sotrable script
          $(".slots-container").sortable('refresh');
      } 

      // Validation Error
      else {
        daySlots.find('.add-slot').addClass('add-slot-shake-error');
        setTimeout(function(){
          daySlots.find('.add-slot').removeClass('add-slot-shake-error');
        }, 600);
      }
      
    });

      // Removing "no slots" message
    function hideSlotInfo() {
      var slotCount = daySlots.find(".slots-container").children().length;
      if ( slotCount < 1 ) {
        daySlots.find(".no-slots")
            .addClass("no-slots-fadein")
            .removeClass("no-slots-fadeout");
      } 
    }
    hideSlotInfo();


    // Removing Slot
    daySlots.find('.remove-slot').bind('click', function(e) {
        e.preventDefault();
          $(this).closest('.single-slot').animate({height: 0, opacity: 0}, 'fast', function() { 
            $(this).remove();
           
          });
          daySlots.find(".add-slot").show();
          daySlots.find(".availableHours").show();
          daySlots.find(".single-slot-right").css("visibility","visible");

      // Removing "no slots" message
      setTimeout(function(){
        hideSlotInfo()
      }, 400);

    });

      // Showing "no slots" message
    daySlots.find('.add-slot-btn').on('click', function(e) {
      e.preventDefault();
      var slotCount = daySlots.find(".slots-container").children().length;
      if ( slotCount >= 1 ) {
        daySlots.find(".no-slots")
            .removeClass("no-slots-fadein")
            .addClass("no-slots-fadeout");
      } 
    });

    });

    // Sotrable Script
    $( ".slots-container" ).sortable();

  // 24 hour clock type switcher
  if ( $('.availability-slots').attr('data-clock-type') == '24hr' ) {
    $('.availability-slots').addClass('twenty-four-clock');
    $('.availability-slots').find('input[type="time"]').attr({ "max" : "24:00"});
  }




    // Switcher
  $(".add-listing-section").each(function() {

    var switcherSection = $(this);
    var switcherInput = $(this).find('.switch input');

    if(switcherInput.is(':checked')){
      $(switcherSection).addClass('switcher-on');
    }

    switcherInput.change(function(){
      if(this.checked===true){
        $(switcherSection).addClass('switcher-on');
        
        if(switcherInput.attr('id') == '_booking_status'){
          //$('.add-listing-section.slots,.add-listing-section.basic_prices,.add-listing-section.availability_calendar').show();
          $('.show_if_booking_enable').show();
        }
      } else {
        $(switcherSection).removeClass('switcher-on');
        if(switcherInput.attr('id') == '_booking_status'){
          //$('.add-listing-section.slots,.add-listing-section.basic_prices,.add-listing-section.availability_calendar').hide();
          $('.show_if_booking_enable').hide();
        }
      }
    });

  });

  if($('#_booking_status').is(':checked'))  {
    // $('.add-listing-section.slots,.add-listing-section.basic_prices,.add-listing-section.availability_calendar').show();
  } else {
    //$('.add-listing-section.slots,.add-listing-section.basic_prices,.add-listing-section.availability_calendar').hide();
  }

    /*----------------------------------------------------*/
  /*  Booking Sticky Footer
  /*----------------------------------------------------*/
  $('.booking-sticky-footer a.button').on('click', function(e) {
    var $anchor = $(this);
    $("html, body").animate({ scrollTop: $($anchor.attr('href')).offset().top - 100 }, 1000);
  });
  

  /*----------------------------------------------------*/
  /* Opening Hours
  /*----------------------------------------------------*/

  $('body').on('click', ".opening-day-remove", function(e){
      e.preventDefault();
      var div_class = $(this).data('remove');
      $(this).parent().parent().remove();
      $('div.'+div_class).remove();
  }); 

  $('body').on('click', ".opening-day-add-hours", function(e){
      e.preventDefault();
      var dayname = $(this).data('dayname');
      var count = $(this).parents('.opening-day').find('.row').length;
      var id = $(this).data('id');
      var i = $(this).parents('.opening-day').find('.row').length;
      

      var newElem = $(''+
        '<div class="row"><div class="col-md-2 opening-day-tools"><a class="opening-day-remove button" data-remove="'+dayname+'-opening-hours-row'+count+'" href="#">'+listeo_core.remove+'</a>'+
          '</div><div class="col-md-5 '+dayname+'-opening-hours-row'+count+'">'+
            '<input type="text" class="listeo-flatpickr" name="_'+id+'_opening_hour[]" placeholder="'+listeo_core.opening_time+'" value=""></div>'+
          '<div class="col-md-5 '+dayname+'-opening-hours-row'+count+'" >'+
            '<input type="text" class="listeo-flatpickr" name="_'+id+'_closing_hour[]" placeholder="'+listeo_core.closing_time+'" value="">'+
          '</div></div>'
      );

      newElem.appendTo($(this).parents('.opening-day'));
      var time24 = false;
    
      if(listeo_core.clockformat){
        time24 = true;
      }
      $(this).parents('.opening-day').find('.row:last .listeo-flatpickr').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: time24,
        disableMobile: true
      });
  });

  
    
    $('input[type=radio][name=user_role]').change(function() {
       
      $('#listeo-core-registration-fields').html('');
      if(this.value == 'owner') {
         $('#listeo-core-registration-fields').html($('#listeo-core-registration-owner-fields').html());
       } else {
         $('#listeo-core-registration-fields').html($('#listeo-core-registration-guest-fields').html());
       }

  
    });
  
  /*----------------------------------------------------*/
  /* Pricing List
  /*----------------------------------------------------*/



  function newMenuItem() {
    // var newElem = $('tr.pricing-list-item:not(.pricing-submenu)').last().clone(true);
    // console.log(newElem.length);
    // if(!newElem){
      var newElem = $(''+
        '<tr class="pricing-list-item pattern" data-iterator="0">'+
          '<td>'+
            '<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>'+
            '<div class="fm-input pricing-name"><input type="text" placeholder="'+listeo_core.menu_title+'" name="_menu[0][menu_elements][0][name]" /></div>'+
            '<div class="fm-input pricing-ingredients"><input type="text" placeholder="'+listeo_core.menu_desc+'" name="_menu[0][menu_elements][0][description]"/></div>'+
            '<div class="fm-input pricing-price">'+
            '<i class="data-unit">'+listeo_core.currency+'</i>'+
            '<input type="number" step="0.01" placeholder="'+listeo_core.menu_price+'" name="_menu[0][menu_elements][0][price]" /></div>'+
            '<div class="fm-input pricing-tax"><i class="data-unit" style="padding: 0 0 0 15px;">mva %</i><select class="" name="_menu[0][menu_elements][0][tax]"><option value="">Mva (optional)</option><option value="25">25%</option><option value="15">15%</option><option value="12">12%</option><option value="0">0%</option></select></div>'+
            '<div class="fm-input pricing-bookable"><div class="switcher-tip" data-tip-content="'+listeo_core.pricingTooltip+'">'+
            '<input type="checkbox" class="input-checkbox switch_1" name="_menu[0][menu_elements][0][bookable]" /></div></div>'+
            '<div class="fm-input pricing-bookable-options">'+
                    '<select class="select2-single" name="_menu[0][menu_elements][0][bookable_options]" id="">'+
                      '<option value="onetime">'+listeo_core.onetimefee+'</option>'+
                      '<option value="byguest">'+listeo_core.multiguest+'</option>'+
                      '<option value="bydays">'+listeo_core.multidays+'</option>'+
                      '<option value="byguestanddays">'+listeo_core.multiguestdays+'</option>'+
                    '</select>'+
                    '<div class="checkboxes in-row pricing-quanity-buttons">'+
                      '<input type="checkbox"  class="input-checkbox" name="_menu[0][menu_elements][0][bookable_quantity]" id="_menu[0][menu_elements][0][bookable_quantity]" />'+
                      '<label for="_menu[0][menu_elements][0][bookable_quantity]">'+listeo_core.quantitybuttons+'</label>'+
                    '</div>'+
              '</div>'+
            '<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>'+
          '</td>'+
        '</tr>');
    //}
    
    
    newElem.find('input').val('');

    var prev_category_number = $('.pricing-submenu').last().data('number');
    var prev_data_iterator = $('tr.pricing-list-item:not(.pricing-submenu)').last().data('iterator');
    
    if(prev_category_number == undefined) {
      prev_category_number = 0;
    }
    
    var next_data_iterator = prev_data_iterator + 1;
    
    var last_table_el = $('tr.pricing-list-item').last();
    

    newElem.find('input').each(function() {
        // replace 1st number with current category title number
        
        this.name = this.name.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
        this.id = this.id.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
        //replace 2nd number / if it's new category start from 0, if not iterate
        if(last_table_el.hasClass('pricing-submenu')){
          next_data_iterator = 0;
          // replace 2nd number
          this.name = replaceLast( this.name, '[0]', '[' + next_data_iterator + ']'  );
          this.id = replaceLast( this.id, '[0]', '[' + next_data_iterator + ']'  );
        } else {
          // replace 2nd number
          this.name = replaceLast( this.name, '[0]', '[' + next_data_iterator + ']' ); 
          this.id = replaceLast( this.id, '[0]', '[' + next_data_iterator + ']' ); 
        }
       
    });
    newElem.find('select').each(function() {
        // replace 1st number with current category title number
        
        this.name = this.name.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
        this.id = this.id.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
        //replace 2nd number / if it's new category start from 0, if not iterate
        if(last_table_el.hasClass('pricing-submenu')){
          next_data_iterator = 0;
          // replace 2nd number
          this.name = replaceLast( this.name, '[0]', '[' + next_data_iterator + ']'  );
          this.id = replaceLast( this.id, '[0]', '[' + next_data_iterator + ']'  );
        } else {
          // replace 2nd number
          this.name = replaceLast( this.name, '[0]', '[' + next_data_iterator + ']' ); 
          this.id = replaceLast( this.id, '[0]', '[' + next_data_iterator + ']' ); 
        }
       
    });

    newElem.find('label').each(function() {
      
        //replace 1st number with current category title number
        this.htmlFor = this.htmlFor.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
        //console.log(this.htmlFor);
        
        //replace 2nd number / if it's new category start from 0, if not iterate
        if(last_table_el.hasClass('pricing-submenu')){
          next_data_iterator = 0;
          // replace 2nd number
          this.htmlFor = replaceLast( this.htmlFor, '[0]', '[' + next_data_iterator + ']'  );
        } else {
          // replace 2nd number
          this.htmlFor = replaceLast( this.htmlFor, '[0]', '[' + next_data_iterator + ']' ); 
        }
        
    });
    //console.log(newElem);
    

    newElem.appendTo('table#pricing-list-container').data('iterator',next_data_iterator).find('select').trigger("chosen:updated");

     $('#pricing-list-container').data('iterator',next_data_iterator);
  }

var test =  '_menu[0][menu_elements][0][bookable_quantity]';
console.log(test);
console.log(test.search(/\[\d+\]/));

  function replaceLast( string, search, replace ) {
    // find the index of last time word was used
    var n = string.lastIndexOf( search );

    // slice the string in 2, one from the start to the lastIndexOf
    // and then replace the word in the rest
    return string.slice( 0, n ) + string.slice( n ).replace( search, replace );
  };

  if ($("table#pricing-list-container").is('*')) {

    $('.add-pricing-list-item').on('click', function(e) {
      e.preventDefault();
      newMenuItem();
    });

    // remove ingredient
    $(document).on( "click", "#pricing-list-container .delete", function(e) {
      e.preventDefault();
      $(this).parent().parent().remove();
    });

    // add submenu
    $('.add-pricing-submenu').on('click', function(e) {
      e.preventDefault();
      var i = $('.pricing-submenu').length;

      var newElem = $(''+
        '<tr class="pricing-list-item pricing-submenu" data-number="'+i+'">'+
          '<td>'+
            '<div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div>'+
            '<div class="fm-input"><input name="_menu['+i+'][menu_title]" type="text" placeholder="'+listeo_core.category_title+'" /></div>'+
            '<div class="fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>'+
          '</td>'+
        '</tr>');

      newElem.appendTo('table#pricing-list-container');
    });

    $('table#pricing-list-container tbody').sortable({
      forcePlaceholderSize: true,
      forceHelperSize: false,
      placeholder : 'sortableHelper',
      zIndex: 999990,
      opacity: 0.6,
      tolerance: "pointer",
      start: function(e, ui ){
           ui.placeholder.height(ui.helper.outerHeight());
      },
      stop: function (event, ui) {

            updateNames($(this))
        }
    });

      $(window).on('load resize', function() {
          var winWidth = $(window).width();
          if (winWidth < 992) {
            $('table#pricing-list-container tbody').sortable('disable');
          } else if (winWidth > 992) {
             $('table#pricing-list-container tbody').sortable('enable');
          }
    });
  }

  //updates list name numbers
  function updateNames($list) {
  var cat_i = 0;
  var subcat_i = 0; 
    $list.find('tr').each(function() {

      var prev_data_iterator = $(this).data('iterator');

      //category
      if($(this).hasClass('pricing-submenu')) {
        var cat_input = $(this).find('input');
        cat_input.each(function () {
              this.name = this.name.replace(/(\[\d\])/, '[' + cat_i + ']');            
          })
          $(this).data('number',cat_i)
        
      } else {
        var prev_category_number = $(this).prevAll('.pricing-submenu').first().data('number');
      
        var subcat_input = $(this).find('input');
        
        subcat_input.each(function() {  
          // replace 1st number with current category title number
          this.name = this.name.replace(/\[\d+\]/, '[' +prev_category_number+ ']');
          this.name = replaceLast( this.name, '[' + prev_data_iterator + ']', '[' + subcat_i + ']' ); 
      });
      $(this).data('iterator',subcat_i);
      subcat_i++;

      }
      
      if($(this).hasClass('pricing-submenu')) {
        cat_i++;
          subcat_i = 0;
      }
        // $inp.each(function () {
        //     this.name = this.name.replace(/(\[\d\])/, '[' + idx + ']');            
        // })
    });
}


    // Unit character
    var fieldUnit = $('.pricing-price').children('input').attr('data-unit');
    $('.pricing-price').children('input').before('<i class="data-unit">'+ fieldUnit + '</i>');


    if( $('body').hasClass('page-template-template-home-search-splash') || $('body').hasClass('page-template-template-home-search') || $('body').hasClass('page-template-template-split-map')) {
      var open_cal = 'right';
    } else {
      var open_cal = 'left';
    }
  
    $('.date_range').daterangepicker({
        "opens": open_cal,
        // checking attribute listing type and set type of calendar
        autoUpdateInput: false,
       
        minDate: moment().subtract(0, 'days'),
        locale: {
          format: wordpress_date_format.date,
          "firstDay"    : parseInt(wordpress_date_format.day),
          "applyLabel"  : listeo_core.applyLabel,
              "cancelLabel" : listeo_core.clearLabel,
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
    
    $('.date_range').on('apply.daterangepicker', function(ev, picker) {
        $("input[name=_listing_type]").prop('disabled', false);
        $(this).val( picker.startDate.format(wordpress_date_format.date) + ' - ' + picker.endDate.format(wordpress_date_format.date)).trigger("change");;
        Cookies.set('listeo_rental_startdate', picker.startDate.format(wordpress_date_format.date));
        Cookies.set('listeo_rental_enddate',  picker.endDate.format(wordpress_date_format.date));

    });

    $('.date_range').on('cancel.daterangepicker', function(ev, picker) {
        $("input[name=_listing_type]").prop('disabled', true);
        $(this).val('').trigger("change");
        
    });

     $('.date_range').on('show.daterangepicker', function(ev, picker) {

        $('.daterangepicker').addClass('calendar-visible calendar-animated bordered-alt-style');
        $('.daterangepicker').removeClass('calendar-hidden');
        $("input[name=_listing_type]").prop('disabled', false);
    });
    $('.date_range').on('hide.daterangepicker', function(ev, picker) {
      
        $('.daterangepicker').removeClass('calendar-visible');
        $('.daterangepicker').addClass('calendar-hidden');
  });


$('input.slot-time-input').keydown(function (e) {
    if (e.ctrlKey || e.metaKey) {
      return true;
    }

    if (e.which >= 37 && e.which <= 40) {
      return true;
    }

    if (e.which !== 8 && e.which !== 0 && e.key.match(/[^:0-9]/)) {
      return false;
    }
  }).keyup(function (e) {
    var $this = $(this);

    if (e.ctrlKey || e.metaKey || e.which === 8 || e.which === 0 || (e.which >= 37 && e.which <= 40)) {
      return true;
    }

    var ss = parseInt(this.selectionStart);

    var v = $this.val();
    var t = v.replace(/[^0-9]/g, '');
    if ( $('.availability-slots').attr('data-clock-type') == '24hr' ) {
      var h = Math.max(0, Math.min(24, parseInt(t.substr(0, 2))));
    } else {
      var h = Math.max(0, Math.min(12, parseInt(t.substr(0, 2))));
    }
    var m = Math.max(0, Math.min(59, parseInt(t.substr(2))));

    if (t.length < 3) {
      m = '';
    }

    var r;

    if (v.length === 2) {
      r = String('0' + h).substr(String(h).length-1) + ':';
      ss++;
    } else if (v.length >= 3 && v.length < 5) {
      r = String('0' + h).substr(String(h).length-1) + ':' + m;
      ss++;
    } else if (v.length === 5) {
      r = String('0' + h).substr(String(h).length-1) + ':' + String('0' + m).substr(String(m).length-1);
    }

    if (r && r !== $this.val()) {
      $this.val(r);
      this.selectionStart = this.selectionEnd = ss;
    }
  }).blur(function (e) {
    var $this = $(this);

    var v = $this.val();
    var t = v.replace(/[^0-9]/g, '');
    var h = Math.max(0, Math.min(23, parseInt(t.substr(0, 2))));
    var m = Math.max(0, Math.min(59, parseInt(t.substr(2)))) || 0;
    var r = '';
        
    if (!isNaN(h)) {
      r = String('0' + h).substr(String(h).length-1) + ':' + String('0' + m).substr(String(m).length-1);
    }

    $this.val(r);
  });


  $( document.body ).on( 'click', '.remove-uploaded-file', function() {
    

    $(this).closest( '.job-manager-uploaded-file' ).remove();
    $(this).closest( '.listeo-uploaded-file' ).remove();
  
    return false;
  });

  //select export ical 
  //
  $("input.listeo-export-ical-input").blur(function() {
    if ($(this).attr("data-selected-all")) {
    //Remove atribute to allow select all again on focus        
    $(this).removeAttr("data-selected-all");
    }
  });

  $("input.listeo-export-ical-input").click(function() {
    if (!$(this).attr("data-selected-all")) {
      try {
        $(this).selectionStart = 0;
        $(this).selectionEnd = $(this).value.length + 1;
        //add atribute allowing normal selecting post focus
        $(this).attr("data-selected-all", true);
      } catch (err) {
        $(this).select();
        //add atribute allowing normal selecting post focus
        $(this).attr("data-selected-all", true);
      }
    }
  });
// ------------------ End Document ------------------ //
});

})(this.jQuery);
/**/