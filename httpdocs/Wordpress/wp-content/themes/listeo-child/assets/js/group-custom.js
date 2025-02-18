jQuery( function( $ ) {

  jQuery('#group_tab #_new_gym').after('<button class="button" id="btn_add_new_gym">Add</button>');
  jQuery('#group_tab #_new_gym').after('<button style="width:1px;background:transparent;border:none;" id="btn_reload_gym"></button>');

  jQuery('#group_tab #_new_sport').after('<button class="button" id="btn_add_new_sport">Add</button>');
  jQuery('#group_tab #_new_sport').after('<button style="width:1px;background:transparent;border:none;" id="btn_reload_sport"></button>');

  jQuery(window).load(function() {
    getSelectedGroup();
    // jQuery("#group_tab  #btn_reload_gym").click();
    // jQuery("#group_tab #btn_reload_gym").click();
    // jQuery("#group_tab #btn_reload_sport").click();
  });

  /* functions */
  function getSelectedGroup(){
    var post_id = getUrlVars()["post"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
        'action': 'listeo_editor_get_group',
        'post_id' : post_id
      },
      success: function(data){
        jQuery("#group_tab #_user_groups_id").show();
        if(data.status == 200) {
          var groupSelectedVal = data.group_selected;
          jQuery("#group_tab #_user_groups_id").val(groupSelectedVal);
          // then reload the Gym and Sport
          jQuery("#group_tab #btn_reload_gym").click();
          jQuery("#group_tab #btn_reload_sport").click();
        }
      }
    });
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

  /************* User Group Change Function *************/
  jQuery('#group_tab #_user_groups_id').on('change',function(e) {
    var user_group_id = jQuery(this).val();

    jQuery("#group_tab #btn_reload_gym").click();
    jQuery("#group_tab #btn_reload_sport").click();

  });
  /************* User Group Change Function *************/

  /************* Add New Gym Button Click *************/
  jQuery('#group_tab #btn_add_new_gym').on('click',function(e) {
    var new_gym = jQuery('#group_tab #_new_gym').val();
    var user_group_id = jQuery('#group_tab #_user_groups_id').val();

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
          jQuery('#group_tab #btn_reload_gym').click();
          jQuery('#group_tab #_new_gym').val('');
        }
        // Update Gym List If Succesfully inserted

      }
    });

  });
  /************* Add New Gym Button Click*************/

  /************* Add New Sport Button Click *************/
  jQuery('#group_tab #btn_add_new_sport').on('click',function(e) {
    var new_sport = jQuery('#group_tab #_new_sport').val();
    var user_group_id = jQuery('#group_tab #_user_groups_id').val();

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
          jQuery('#group_tab #btn_reload_sport').click();
          // getSportListByGroup(user_group_id);
          // new_sport.text('');
          jQuery('#group_tab #_new_sport').val('');
        }
        // Update Sport List If Succesfully inserted
      }
    });
  });
  /************* Add New Sport Button Click *************/

  /************* Button Reload Gym Click *************/
  jQuery('#group_tab #btn_reload_gym').on('click',function(e) {
    e.preventDefault();

    jQuery("#group_tab #_listing_gym").hide();
    var user_group_id = jQuery('#group_tab #_user_groups_id').val();
    
    var post_id = getUrlVars()["post"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
          'action': 'listeo_editor_get_group_gyms', 
          'user_group_id' : user_group_id,
          'post_id' : post_id
         },
      success: function(data){
        jQuery("#group_tab #_listing_gym").show();
        if(data.status == 200) {
          var gymSelectedVal = data.listing_gym_selected;
          var gymData = data.data;
          jQuery("#group_tab #_listing_gym").empty();
          jQuery("#group_tab #_listing_gym").append('<option value="">Please Select Gym</option>');
          $.each( gymData, function( key, value ) {
            jQuery("<option></option>",{
              value:value.id,
              text: value.name
            }).appendTo(jQuery("#group_tab #_listing_gym"));
          });
          // Select Previous selected again
          jQuery("#group_tab #_listing_gym").val(gymSelectedVal);
        }
      }
    });
  });

  /************* Button Reload Sport Click *************/
  jQuery('#group_tab #btn_reload_sport').on('click',function(e) {
    e.preventDefault();

    jQuery("#group_tab #_listing_sports").hide();
    var user_group_id = jQuery('#group_tab #_user_groups_id').val();

    var post_id = getUrlVars()["post"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
          'action': 'listeo_editor_get_group_sports', 
          'user_group_id' : user_group_id,
          'post_id' : post_id,
         },
      success: function(data){
        jQuery("#group_tab #_listing_sports").show();
        if(data.status == 200) {
          var sportData = data.data;
          var sportSelectedValsArr = data.listing_sports_selected;
          jQuery("#group_tab #_listing_sports").empty();
          $.each( sportData, function( key, value ) {
            var selected = ''
            if(sportSelectedValsArr.includes(value.id)) {
              selected = 'selected="selected"';
            }
            jQuery("#group_tab #_listing_sports").append("<option "+ selected +" value="+value.id+">"+ value.name +"</option>");
            // jQuery("<option></option>",{
            //   value:value.id,
            //   text: value.name
            // }).appendTo(jQuery("#group_tab #_listing_sports"));
          });
        }
      }
    });
  });
});