jQuery( function( $ ) {
  // $('.add-listing-section #_new_gym').after('<button class="button" id="btn_add_new_gym">Add</button>');
  // $('.add-listing-section #_new_gym').after('<button class="button" id="btn_reload_gym">Reload</button>');
  
  jQuery('.add-listing-section .form-field-_new_gym-container').after('<div class="col-md-3 .form-field-_gym_buttons-container"><button style="margin-top:35px;" class="button" id="btn_add_new_gym">Add</button></div>');
  jQuery('.add-listing-section #_new_gym').after('<button style="width:1px;background:transparent;border:none;" id="btn_reload_gym"></button>');

  // $('.add-listing-section #_new_sport').after('<button class="button" id="btn_add_new_sport">Add</button>');
  // $('.add-listing-section #_new_sport').after('<button class="button" id="btn_reload_sport">Reload</button>');

  jQuery('.add-listing-section .form-field-_new_sport-container').after('<div class="col-md-3 .form-field-_sport_buttons-container"><button style="margin-top:35px;" class="button" id="btn_add_new_sport">Add</button></div>');
  jQuery('.add-listing-section #_new_sport').after('<button style="width:1px;background:transparent;border:none;" id="btn_reload_sport"></button>');
  
  var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();

  jQuery(window).load(function() {
    getSelectedGroup();
    // jQuery(".add-listing-section #btn_reload_gym").click();
    // jQuery(".add-listing-section #btn_reload_sport").click();
  });

  /* functions */
  function showGymContainer() {
    // Show Gym Container | Show Add Gym button container
    jQuery('.add-listing-section .form-field-_listing_gym-container').show();
  }

  function ShowNewGym(){
    jQuery('.add-listing-section .form-field-_new_gym-container').show();
    jQuery('.add-listing-section #btn_add_new_gym').show();
  }

  function showSportContainer(){
    // Show Sport Container | Show Add Sport button container
    jQuery('.add-listing-section .form-field-_listing_sports-container').show();
    jQuery('.add-listing-section .form-field-_new_sport-container').show();
    jQuery('.add-listing-section #btn_add_new_sport').show();
  }

  function hideGymContainer() {
    // Hide Gym Container | Hide Add Gym button container
    jQuery('.add-listing-section .form-field-_listing_gym-container').hide();
    jQuery('.add-listing-section .form-field-_new_gym-container').hide();
    jQuery('.add-listing-section #btn_add_new_gym').hide();
  }

  function hideSportContainer(){
    // Hide Sport Container | Hide Add Sport button container
    jQuery('.add-listing-section .form-field-_listing_sports-container').hide();
    jQuery('.add-listing-section .form-field-_new_sport-container').hide();
    jQuery('.add-listing-section #btn_add_new_sport').hide();
  }

  function hideNewSportContainerAndButton(){
    jQuery('.add-listing-section .form-field-_new_sport-container').hide();
    jQuery('.add-listing-section #btn_add_new_sport').hide(); 
  }

  function getSelectedGroup(){
    var listing_id = getUrlVars()["listing_id"];
    var groupVal = jQuery(".add-listing-section #_user_groups_id").val();
    
    if(!groupVal){
      hideGymContainer();
      hideSportContainer();
    }

    if(typeof ajaxurl != "undefined"){

      jQuery.ajax({
        type: 'POST', 
        dataType: 'json',
        url: ajaxurl,
        data: { 
          'action': 'listeo_editor_get_group',
          'listing_id' : listing_id
        },
        success: function(data){
          if(data.status == 200) {
            var groupSelectedVal = data.group_selected;
            if(groupSelectedVal){
              showGymContainer();
              ShowNewGym();
              showSportContainer();
            }
            jQuery(".add-listing-section #_user_groups_id").val(groupSelectedVal);
            // then reload the Gym and Sport
            jQuery(".add-listing-section #btn_reload_gym").click();
            jQuery(".add-listing-section #btn_reload_sport").click();
          }
        }
      });
    }
  }
  // Read a page's GET URL variables and return them as an associative array.
    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
  /* functions*/

  /************* Gym Change Function *************/
  jQuery('.add-listing-section #_listing_gym').on('change',function(e) {
    e.preventDefault();
    ShowNewGym();
  });
  /************* Gym Change Function *************/

  /************* User Group Change Function *************/
  jQuery('.add-listing-section #_user_groups_id').on('change',function(e) {
    e.preventDefault();
    var user_group_id = jQuery(this).val();
    if(user_group_id){
      showGymContainer();
      showSportContainer();
      jQuery(".add-listing-section #btn_reload_gym").click();
      jQuery(".add-listing-section #btn_reload_sport").click();
    }
  });
  /************* User Group Change Function *************/

  /************* Add New Gym Button Click *************/
  jQuery('.add-listing-section #btn_add_new_gym').on('click',function(e) {
    e.preventDefault();
    var new_gym = jQuery('.add-listing-section #_new_gym').val();
    var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();

    /* Add New Gym */
    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
        'action': 'listeo_editor_add_new_gym', 
        'new_gym' : new_gym,
        'user_group_id' : user_group_id,
      },
      success: function(data){
        if(data.status == 200) {
          $(".add-listing-section #btn_reload_gym").click();
          // getGymListByGroup(user_group_id);
          jQuery('.add-listing-section #_new_gym').val('');
        }
        // Update Gym List If Succesfully inserted
      }
    });

  });
  /************* Add New Gym Button Click*************/

  /************* Add New Sport Button Click *************/
  jQuery('.add-listing-section #btn_add_new_sport').on('click',function(e) {
    e.preventDefault();
    var new_sport = jQuery('.add-listing-section #_new_sport').val();
    var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();

    /* Add New Sport */
    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
        'action': 'listeo_editor_add_new_sport', 
        'new_sport' : new_sport,
        'user_group_id' : user_group_id,
      },
      success: function(data){
        if(data.status == 200) {
          $(".add-listing-section #btn_reload_sport").click();
          // getSportListByGroup(user_group_id);
          jQuery('.add-listing-section #_new_sport').val('');
        }
        // Update Sport List If Succesfully inserted
      }
    });
  });
  /************* Add New Sport Button Click *************/

  /************* Reload Gym Button Click *************/
  jQuery('.add-listing-section #btn_reload_gym').on('click',function(e) {
    e.preventDefault();

    // var gymSelectedVal = jQuery(".add-listing-section #_listing_gym option:selected").val();
    
    jQuery(".add-listing-section #_listing_gym").hide();
    var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();
    
    var listing_id = getUrlVars()["listing_id"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
          'action': 'listeo_editor_get_group_gyms', 
          'user_group_id' : user_group_id,
          'listing_id' : listing_id
         },
      success: function(data){
        jQuery(".add-listing-section #_listing_gym").show();
        if(data.status == 200) {
          var gymSelectedVal = data.listing_gym_selected;
          var gymData = data.data;
          jQuery(".add-listing-section #_listing_gym").empty();
          jQuery(".add-listing-section #_listing_gym").append('<option value="">Please Select Gym</option>');
          $.each( gymData, function( key, value ) {
            jQuery("<option></option>",{
              value:value.id,
              text: value.name
            }).appendTo(jQuery(".add-listing-section #_listing_gym"));
          });
          // Select Previous selected again
          jQuery(".add-listing-section #_listing_gym").val(gymSelectedVal);
        }
      }
    });
  });

  /************* Reload Sport Button Click *************/
  jQuery('.add-listing-section #btn_reload_sport').on('click',function(e) {
    e.preventDefault();

    var sportSelectedValsArr = [];
    var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();

    // var sportSelectedValsArr = []
    // $(".add-listing-section .form-field-_listing_sports-container .checkboxes input[name='_listing_sports[]']:checked").each(function ()
    // {
    //   sportSelectedValsArr.push($(this).val());
    // });

    jQuery(".form-field-_listing_sports-container .checkboxes").hide();

    var listing_id = getUrlVars()["listing_id"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
        'action': 'listeo_editor_get_group_sports', 
        'user_group_id' : user_group_id,
        'listing_id' : listing_id
      },
      success: function(data){
        jQuery(".add-listing-section #_listing_sports").show();
        if(data.status == 200) {
          var sportSelectedValsArr = data.listing_sports_selected;
          var sportData = data.data;
          if(sportData.length > 0){
            jQuery(".form-field-_listing_sports-container .checkboxes").empty();
            $.each( sportData, function( key, value ) {
              var checked = '';
              if(sportSelectedValsArr.includes(value.id)) {
                checked = 'checked="checked"';
                // console.log('value exists = ', value.id);
              }
              jQuery(".form-field-_listing_sports-container .checkboxes")
              .append(
                '<input id="'+ value.id +'" type="checkbox" name="_listing_sports[]" '+ checked +' value="'+ value.id +'">\
                <label for="'+ value.id +'">'+ value.name+'</label>'
              );
            });
            jQuery(".form-field-_listing_sports-container .checkboxes").show();
          } else {
            jQuery(".form-field-_listing_sports-container .checkboxes").empty();
            jQuery(".form-field-_listing_sports-container .checkboxes")
              .append(
                '<h5>No Listing Sports for this Group Found! <small>Please Add Sport</small></h5>'
              );
            jQuery(".form-field-_listing_sports-container .checkboxes").show();
          }
        } else {
          jQuery(".form-field-_listing_sports-container .checkboxes").empty();
          jQuery(".form-field-_listing_sports-container .checkboxes")
            .append(
              '<h5>No Listing Sports for this Group Found! <small>Please Add Sport</small></h5>'
            );
          jQuery(".form-field-_listing_sports-container .checkboxes").show();
        }
      }
    });
  });

});