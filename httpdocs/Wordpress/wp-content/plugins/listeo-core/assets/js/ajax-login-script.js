/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){ 
    
    // $( 'body' ).on( 'keyup', 'input[name=password]', function( event ) {
    //     $('.pwstrength_viewport_progress').addClass('password-indicator-visible');
      
    //   });
    $('input[name=password]').keypress(function() {
      $('.pwstrength_viewport_progress').addClass("password-strength-visible").animate({ opacity: 1 });
    });
    

    var options = {};
    options.ui = {
        //container: "#password-row",
        viewports: {
            progress: ".pwstrength_viewport_progress",
          //  verdict: ".pwstrength_viewport_verdict"
        },     
        colorClasses: ["bad", "short", "normal", "good", "good", "strong"],
        showVerdicts: false,
        //useVerdictCssClass
    };
    options.common = {
        debug: true,
        onLoad: function () {
            $('#messages').text('Start typing password');
        }
    };
    $(':password').pwstrength(options);

    // function wdmChkPwdStrength( $pwd,  $confirmPwd, $strengthStatus, $submitBtn, blacklistedWords ) {
    //     var pwd = $pwd.val();
    //     var confirmPwd = $confirmPwd.val();

    //     // extend the blacklisted words array with those from the site data
    //     blacklistedWords = blacklistedWords.concat( wp.passwordStrength.userInputDisallowedList() )

    //     // every time a letter is typed, reset the submit button and the strength meter status
    //     // disable the submit button
    //     //$submitBtn.attr( 'disabled', 'disabled' );
    //     $strengthStatus.removeClass( 'short bad good strong' );

    //     // calculate the password strength
    //     var pwdStrength = wp.passwordStrength.meter( pwd, blacklistedWords, confirmPwd );

    //     // check the password strength
    //     switch ( pwdStrength ) {

    //         case 2:
    //         $strengthStatus.addClass( 'bad' ).html( pwsL10n.bad );
    //         break;

    //         case 3:
    //         $strengthStatus.addClass( 'good' ).html( pwsL10n.good );
    //         break;

    //         case 4:
    //         $strengthStatus.addClass( 'strong' ).html( pwsL10n.strong );
    //         break;

    //         case 5:
    //         $strengthStatus.addClass( 'short' ).html( pwsL10n.mismatch );
    //         break;

    //         default:
    //         $strengthStatus.addClass( 'short' ).html( pwsL10n.short );

    //     }
    //     return pwdStrength;
    // }

    // Perform AJAX login on form submit
    $('#sign-in-dialog form#login').on('submit', function(e){
        var redirecturl = $('input[name=_wp_http_referer]').val();
        var success;
        var role_editor = false;
        $('form#login .notification').removeClass('error').addClass('notice').show().text(listeo_login.loadingmessage);
            if ($(".xoo-el-popup-active")[0]){
                var usernameInput =  $('.xoo-el-modal form#login #user_login').val();
                var passwordInput =  $('.xoo-el-modal form#login #user_pass').val();
            } else {
                var usernameInput =  $('form#login #user_login').val();
                var passwordInput =  $('form#login #user_pass').val();
            }
          //  debugger;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: listeo_login.ajaxurl,
                data: { 
                    'action': 'listeoajaxlogin', 
                    'username': usernameInput, 
                    'password': passwordInput, 
                    'login_security': $('form#login #login_security').val()
                   },
             
                }).done( function( data ) {
                    if (data.loggedin == true){
                        // console.log(data.role.editor);
                        // return false;
                        if(data.role.editor){
                            role_editor = true;
                            window.location.href = '/kalender/';
                            return false;
                        }
                         else {
                            window.location.href = redirecturl;
                        }
                        $('form#login .notification').show().removeClass('error').removeClass('notice').addClass('success').text(data.message);
                        success = true;
                    } else {
                        $('form#login .notification').show().addClass('error').removeClass('notice').removeClass('success').text(data.message);
                    }
            } )
            .fail( function( reason ) {
                // Handles errors only
                console.debug( 'reason'+reason );
            } )
            
            .then( function( data, textStatus, response ) {
                if(success){
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: listeo_login.ajaxurl,
                        data: { 
                            'action': 'get_logged_header', 
                        },
                        success: function(new_data){
                            $('body').removeClass('user_not_logged_in');                        
                            $('.header-widget').html(new_data.data.output);
                            var magnificPopup = $.magnificPopup.instance; 
                              if(magnificPopup) {
                                  magnificPopup.close();   
                              }
                            if(role_editor){
                                window.location.href = '/kalender';
                                console.log('coming here');
                                return false;
                            }
                        }
                    });
                    var post_id = $('#form-booking').data('post_id');
                    var owner_widget_id = $('.widget_listing_owner').attr('id');
                    var freeplaces = $('.book-now-notloggedin').data('freeplaces');
                    
                    if(post_id) {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: listeo_login.ajaxurl,
                            data: { 
                                'action': 'get_booking_button',
                                'post_id' : post_id,
                                'owner_widget_id' : owner_widget_id,
                                'freeplaces' : freeplaces

                            },
                            success: function(new_data){
                                var freeplaces = $('.book-now-notloggedin').data('freeplaces');
                                $('.book-now-notloggedin').replaceWith(new_data.data.booking_btn);
                                $('.like-button-notlogged').replaceWith(new_data.data.bookmark_btn);
                                $('#owner-widget-not-logged-in').replaceWith(new_data.data.owner_data);
                            }
                        });
                    }
                }
                
             
                // In case your working with a deferred.promise, use this method
                // Again, you'll have to manually separates success/error
            }) 
        e.preventDefault();
    });

    // Perform AJAX login on form submit
    $('#sign-in-dialog form#register').on('submit', function(e){
        var popupActive = false;
        var formParentBlock = '';

        $('form#register .notification').removeClass('error').addClass('notice').show().text(listeo_login.loadingmessage);

        if ($(".xoo-el-popup-active")[0]){
            popupActive = true;
            formParentBlock = '.xoo-el-modal ';
        } else {
            formParentBlock = '#wrapper ';
        }

        var form = $(this).serializeArray();

        var action_key = {
              name: "action",
              value: 'listeoajaxregister'
        };

        var privacy_key = {
              name: "privacy_policy",
              value: $('form#register #privacy_policy:checked').val()
        };   
      
        form.push(action_key);
        form.push(privacy_key);
        
   // 'g-recaptcha-response': $('form#register #g-recaptcha-response').val(),
   //              'token': $('form#register #token').val(),
   //              'g-recaptcha-action': $('form#register #action').val()

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: listeo_login.ajaxurl,
            data: form,
            // data: { 
            //     'action': 'listeoajaxregister', 
            //     'role': $('form#register .account-type-radio:checked').val(), 
            //     'username': $('form#register #username2').val(), 
            //     'email':    $('form#register #email').val(), 
            //     'password': $('form#register #password2').val(), 
            //     'first-name': $('form#register #first-name').val(), 
            //     'last-name': $('form#register #last-name').val(), 
            //     'password': $('form#register #password1').val(), 
            //     'privacy_policy': $('form#register #privacy_policy:checked').val(), 
            //     'register_security': $('form#register #register_security').val(),
            //     'g-recaptcha-response': $('form#register #g-recaptcha-response').val()
            // },
            success: function(data){

                if (data.registered == true){
                    $('form#register .notification').show().removeClass('error').removeClass('notice').addClass('success').text(data.message);
                    // $( 'body, html' ).animate({
        //                 scrollTop: $('#sign-in-dialog').offset().top
        //             }, 600 );
                    $('#register').find('input:text').val(''); 
                    $('#register input:checkbox').removeAttr('checked');
                  //  if(listeo_core.autologin){
                        setTimeout(function(){
                            window.location.reload(); // you can pass true to reload function to ignore the client cache and reload from the server
                        },2000);    
                 //   }
                    

                } else {
                    $('form#register .notification').show().addClass('error').removeClass('notice').removeClass('success').text(data.message);
                      
                    if(listeo_core.recaptcha_status){
                        if(listeo_core.recaptcha_version == 'v3'){
                            getRecaptcha();        
                        }
                    }
                }

            }
        });
        e.preventDefault();
    });


});



})(this.jQuery);