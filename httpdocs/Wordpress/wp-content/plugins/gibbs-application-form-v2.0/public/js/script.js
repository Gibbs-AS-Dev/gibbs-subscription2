// var phone = document.getElementById('telephone');
// console.log("intltelInput");
// phone.intlTelInput();
// console.log(window.intlTelInput);

/*Functions To Be Used
 */
function toggelApplicationSection() {
  var req_field_count = jQuery(
    ".caccordion.accordion-user-info *[required]"
  ).length;
  var req_field_filled = 0;
  jQuery(".caccordion.accordion-user-info *[required]").each(function () {
    if (jQuery(this).val() != "") {
      req_field_filled++;
    }
  });
  // all required fields got filled
  if (req_field_filled == req_field_count) {
    // console.log(req_field_filled, req_field_count);
    //do stuff
    // 1) Show the Application Sction
    if(AFBSI.seasons.length){
      jQuery(".caccordion.accordion-application").show();
    }
    jQuery("#applications").show();
    // 2) Show Add App Buton
    jQuery("#add_new_app").show();
    // 3) Show Submit Button
    jQuery("#afb-form-submit").show();
    // 4) Show the comment box
    jQuery(".application-comment").show();
    // 5) Show Comment
    //6)
    jQuery(".add_application").show();
    jQuery(".terms-and-condition-box").show();
  } else {

    jQuery("#telephone").removeAttr("placeholder");
    jQuery("#add_new_app").show();
  }
}
/*
 * Logic Begins
 */
(function ($) {
  $(document).ready(function () {
    // Some Functions 
    function resetApplicationsForNewSeason(){
      $(".deletion-hidden-input-field").remove();
      var applications = $(".applications#applications");
      $(applications).find(".caccordion.accordion-application").not(':first').remove();
      $(applications).find("select,input,textarea").val("");
      $(applications).find(".delete-application-button").attr("application-id",null);
    }
    if(AFBSI.seasons){
      console.log(AFBSI);
    }
    var activeSeasonGroup;
    var userGroup = $("#user_group_dropdown");
    var userInfo = $(".caccordion.accordion-user-info");
    function toggelUserInfo(value,dropdownChange = false) {
      var valueN = Number(value);
      
      $("#user_group_dropdown").find('option').each(function(){

        // if(AFBSI.seasons.length !==0){
        //   AFBSI.seasons
        // }
      });
      var seasonId;
       $("#user_group_dropdown").find('option').each(function(){if($(this).val()== value){seasonId = $(this).attr('season_id')}});
       if(AFBSI.seasons.length !==0 && seasonId){
        var isSeason = false;
        AFBSI.seasons.forEach(function(item){
          if(item == seasonId){
            isSeason = true
          }
        });

       }
      //  if(seasonId){
      //    $.post(
      //      AFBUI.ajaxurl,
      //      {
      //        action: 'check_season_availability',
      //        seasonId
      //      },
      //      function(response){
      //       if(response.has_season){
      //         swal({title:"You Already Submitted the application for this season",default:"Edit Existing"})
      //       }
      //      }
      //    );
      //  }
      // console.log(valueN);
      if (!isNaN(valueN) && valueN !== 0) {
        if(!isSeason){
          resetApplicationsForNewSeason();
        }
        if(isSeason){
          if(dropdownChange){
            activeSeasonGroup = valueN;
          }
          swal({
            title: "Søknad er alledre sendt",
            text:"Hva ønsker du å gjøre?",
            className: "afb-swal-modal",
            buttons: {
              cancelNew: {
                text: 'Lukk',
                className: 'swal-button--cancel',
                value: null
              },
              edit: {
                text: "Se over sendt søknad",
                value: "edit",
              },
          }}).then(function(value){
            if(value == null){
              // redirect to another page
              window.location.href= AFBUI.contactPageUrl;
            }else if(value == "addApp"){
               // Clean the Data and Show Empty Everything
              $("#user_group_dropdown").val("");
              resetApplicationsForNewSeason();
              
              
            }else if(value == "edit"){
              // if(userGroupLastValue !== valueN)
              if(activeSeasonGroup){
                //location.reload(true);
              }


            }
            userGroupLastValue = valueN;
          });
        }
        //userGroupLastValue = valueN;
        /*
         *User Group Dropdown Has Value Perform Actions
         */
        //1) Show User Info

        
        //2) Limit The Listing Select Options Based On the User Group Selected
        $.post(
          AFBUI.ajaxurl,
          {
            action: "get_listing_dropdown_options",
            user_group: valueN,
          },
          function (response) {

            // Remove All the Options
            function changeTheDropdown(classes, defaultText, response, select_first_option = false) {
              var parentBlock = $(classes.substring(12, classes.length) + '-block');
              // if response is only one - select by default and hide the parent block
              if(select_first_option == true && response.length == 1){
                  response.forEach(function (item) {
                    optionItem += `<option selected value='${item.id}'>${item.name}</option>`;
                  });
                  $(classes).each(function (index, elm) {
                    $(this).html(optionItem);
                  });
                  // hide parent block
                  parentBlock.hide();
                  // $(this).parent().find('age-group-block').hide();
              } else {
                // Append New Options | And Select The Selected if Present
                var optionItem = `<option selected value="">${defaultText}</option>`;
                if (response.length > 0) {
                  response.forEach(function (item) {
                    optionItem += `<option value='${item.id}'>${item.name}</option>`;
                  });
                }
                if (optionItem) {
                  $(classes).each(function (index, elm) {
                    var current_val = $(this).find("option:selected").val();
                    $(this).html(optionItem);
                    $(this).val(current_val);
                  });
                }

                // show parent Block
                parentBlock.show();

              }
            } //cahngeTheDropdownEnd

            changeTheDropdown(
              ".form-select.locations-dropdown",
              "Velg",
              response.listings
            );
            changeTheDropdown(
              ".form-select.age-group-dropdown",
              "Velg",
              response.age_groups,
              true
            );
            changeTheDropdown(
              ".form-select.level-dropdown",
              "Velg",
              response.team_levels,
              true
            );
            changeTheDropdown(
              ".form-select.sport-dropdown",
              "Velg",
              response.sports,
              true
            );
            // changeTheDropdown(".form-select.priority"),"Velg Orange",response.listings);
            changeTheDropdown(
              ".form-select.priority",
              "Velg",
              response.listings
            );
            changeTheDropdown(
              ".form-select.user-type-dropdown",
              "Velg",
              response.user_types,
              true
            );
            toggelApplicationSection();
            $(userInfo).show();


            /*
            $('.form-select.locations-dropdown').each(function(index){
              $(this).empty();
              var option = $('<option selected>Location</option>');
              option.val("");
              $(this).append(option);
            });

            // Append New Options 
            if(response.listings.length > 0){
              var listingOptions = "";
              response.listings.forEach(function(item){
              listingOptions +=`<option value='${item.id}'>${item.post_name}</option>`;
              });

              if(listingOptions){
                $('.form-select.locations-dropdown').each(function(index){
                  $(this).html(listingOptions);
                  $(this).prepend($("<option selected>Location</option>").val(""));
                })
              }
            }
            */
          }
        ); //Post Request For Change of User Group

        //3) Display Application If User Info is set After userGroupValue
        
      } else {
        $(userInfo).hide();
        $("#applications").hide();
        
        $(".add_application").hide();
        $("#afb-form-submit").hide();
        $(".application-comment").hide();
        $(".terms-and-condition-box").hide();
      }
    }
    toggelUserInfo($(userGroup).val());
    $(userGroup).change(function (e) {
      var season_id = jQuery(this).find("option:selected").attr("season_id");
      jQuery("input[name=season_id]").val(season_id);
      toggelUserInfo(e.target.value,true);
    });
    //time options disable feature helper function for filter
    function disableTimeFilterHelper(optVal,openingHour,closingHour){
      var currentTime = Date.parse(`1/1/2011 ${optVal}`);
      var openingTime = Date.parse(`1/1/2011 ${openingHour}`);
      var closingTime = Date.parse(`1/1/2011 ${closingHour}`);
      if(currentTime >= openingTime && currentTime <= closingTime ){
        return false;
      }else{
        return true;
      }
    //   if( Date.parse(`1/1/2011 ${optVal}`) <= Date.parse(`1/1/2011 ${openingHour}`) 
    //   && Date.parse(`1/1/2011 ${optVal}`) >= Date.parse(`1/1/2011 ${closingHour}`)
    // ){
    //   return true;
    // }
    // return true;
      // if (closingHour == "00:00") {
      //   return (
      //     Date.parse(`1/1/2011 ${openingHour}`) >
      //     Date.parse(`1/1/2011 ${optVal}`) &&
      //     optVal !== "00:00"
      //   );
      // } else if (
      //   Date.parse(`1/1/2011 ${openingHour}`) <=
      //   Date.parse(`1/1/2011 ${optVal}`) &&
      //   Date.parse(`1/1/2011 ${closingHour}`) >=
      //   Date.parse(`1/1/2011 ${optVal}`)
      // ) {
      //   return false; // not disable
      // } else {
      //   return true;
      // }
    }
    function disableWorkingHours(optVal,openingHour,closingHour,){
      if( Date.parse(`1/1/2011 ${optVal}`) <= Date.parse(`1/1/2011 ${openingHour}`) 
        && Date.parse(`1/1/2011 ${optVal}`) >= Date.parse(`1/1/2011 ${closingHour}`)
      ){
        return true;
      }
    }
    // Limit and disable time
    function onLocationChange(e, elem = false) {
      if (elem) {
        var element = $(e);
      } else {
        var element = $(e.target);
      }
      var listing = $(element).val();
      var parentDiv = $(element).closest(".row.location");
      
      var daysBlock = $(parentDiv).find(".days-block");
      var subLocationBlock = $(parentDiv).find(".sublocation-block");
      var parentLocationBlock = $(parentDiv).find(".location-block");

      var subLocation = $(parentDiv).find(".form-select.sublocations-dropdown");
      var subLocationSelected = $(parentDiv).find(".sublocations-selected");
      var openingHour = $(parentDiv).find(".form-select.from-time");
      var closingHour = $(parentDiv).find(".form-select.to-time");
      //reset options

      // $()
      $(subLocation).find("option").prop("disabled", false);
      $(openingHour).find("option").prop("disabled", false);
      $(closingHour).find("option").prop("disabled", false);
    if(!elem){
      $(subLocation).prop('disabled',true);
      $(openingHour).prop('disabled',true);
      $(closingHour).prop('disabled',true);
    }
      // make opening and closingHour required if listing value is selected
      if (listing) {
        $(openingHour).prop("required", true);
        $(closingHour).prop("required", true);
      } else {
        $(openingHour).prop("required", false);
        $(closingHour).prop("required", false);
      }
      var that;
      that = this;
      //listing = "4435";
      var day = $(element).attr("day");
      if (listing) {
        $.post(
          AFBUI.ajaxurl,
          {
            action: "get_working_hours",
            listing,
            day,
          },
          function (response) {
             jQuery(that).parent().parent().find(".fra_div").show();
             jQuery(that).parent().parent().find(".til_div").show();
            // response = {opening: '14:00', closing: '22:00'};
            console.log(response);
            if (response.opening && response.closing) {
              $(openingHour).attr({
                "opening-time": response.opening,
                "closing-time": response.closing,
              });
              $(closingHour).attr({
                "opening-time": response.opening,
                "closing-time": response.closing,
              });
              function disableTimeOptions(element) {
                // console.log(element);
                var options = $(element)
                  .find("option")
                  .filter(function (el) {
                    var optValue = $(this).val();
                    var isDisabled = disableTimeFilterHelper(optValue,response.opening,response.closing);
                    if(isDisabled){
                      return el;
                    }
                  });
                // console.log("options");
                // console.log(options);
                $(options).prop("disabled", true);
              } //function close
              disableTimeOptions(openingHour);
              disableTimeOptions(closingHour);
              $(openingHour).prop('disabled',false);
              $(closingHour).prop('disabled',false);
            }
            if(response.sublocation.length){
              $(subLocationBlock).show();
              subLocationSelectedArr = []
              if(subLocationSelected.val()){
                subLocationSelectedArr = JSON.parse(subLocationSelected.val());
              }
              console.log('Sublocation selected =', subLocationSelectedArr);
              

              $(subLocation).empty();
              $.each(response.sublocation, function(key, sub) {
                if (jQuery.inArray(sub.id, subLocationSelectedArr) >= 0) {
                  $(subLocation)
                  .append($("<option selected></option>")
                    .attr("value", sub.id)
                    .text(sub.post_title));
                } else {
                  $(subLocation)
                  .append($("<option></option>")
                    .attr("value", sub.id)
                    .text(sub.post_title)); 
                }
              });
              $(subLocation).prop('disabled',false);
              // console.log('append the options');
            } else {
              $(subLocationBlock).hide();
              $(subLocation).prop('disabled',true);
            }
          }
        );
      }
    }
    $(".form-select.locations-dropdown").on("change", onLocationChange);
    $.each($(".form-select.locations-dropdown"), function () {
      onLocationChange($(this), true);
    });
    //
    //Limiting to time on change of the  from time
    function limitToTime() {
      var openingHoursElement = $(this);
      var closingHoursElement = $(this)
        .parent()
        .parent()
        .find(".form-select.to-time");
      $(closingHoursElement).find("option").attr('disabled',false);
      $(closingHoursElement).find('option').filter(function(){
        var optVal = $(this).val();
        //return disableTimeFilterHelper(optVal, $(closingHoursElement).attr('opening-time'),$(closingHoursElement).attr('closing-time'),true);
      }).attr('disabled',true);
      $(closingHoursElement)
        .find("option")
        .filter(function () {
          return $(this).val() <= $(openingHoursElement).val();
        })
        .attr("disabled", true);
    var closingTime =$(openingHoursElement).attr('closing-time');
    //console.log(closingTime);
    //$().find().filter().attr("disabled",true);
    $(closingHoursElement).find("option").filter(function(){ return $(this).val() > closingTime}).attr("disabled",true);
    //console.log($(closingHoursElement).html());
    
    
    //console.log(closingHoursElement);
    }
    $(".caccordion.accordion-application")
      .find(".form-select.from-time")
      .on("change", limitToTime);

    //Priority Selecting Logic
    function onPrioritySelect() {
      var selected_values = [];
      $(this)
        .parent()
        .parent()
        .find("select")
        .each(function () {
          selected_values.push($(this).find("option:selected").val());
        });
      $(this)
        .parent()
        .parent()
        .find("select")
        .each(function () {
          $(this)
            .find("option")
            .each(function (io, eo) {
              if ($(this).val() == "") return;
              if (jQuery.inArray($(this).val(), selected_values) >= 0) {
                // if found
                if (!$(this).is(":selected")) {
                  $(this).prop("disabled", true);
                }
              } else {
                $(this).prop("disabled", false);
              }
            });
        });
    }
    $(".form-select.priority").on("change", onPrioritySelect);

    var telephone = document.getElementById("telephone");
    $(telephone).intlTelInput({
      initialCountry: "NO",
      separateDialCode: true,
      hiddenInput: "full",
      preferredCountries: ["NO"],
      utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.14/js/utils.js",
    });
    /******************************
     * Show Application Only When User Info Feilds are occupied
     */
    /*
    (function () {
      var req_field_count = $(
        ".caccordion.accordion-user-info *[required]"
      ).length;
      var req_field_filled = 0;
      $(".caccordion.accordion-user-info *[required]").each(function () {
        if ($(this).val() != "") {
          req_field_filled++;
        }
      });
      // all required fields got filled
      if (req_field_filled == req_field_count) {
        console.log(req_field_filled, req_field_count);
        //do stuff
        // 1) Show the Application Sction
        $(".caccordion.accordion-application").show();
        $("#applications").show();
        // 2) Show Add App Buton
        $("#add_new_app").show();
        // 3) Show Submit Button
        $("#afb-form-submit").show();
      } else {
        $(".caccordion.accordion-application").hide();
        $("#applications").hide();
        $("#add_new_app").hide();
        $("#afb-form-submit").hide();
      }
    })();
    */
    toggelApplicationSection();

    var userInfoForm = $(".caccordion.accordion-user-info");
    $(userInfoForm).on("change", function (e) {
      e.preventDefault();
      var req_field_count = $(
        ".caccordion.accordion-user-info *[required]"
      ).length;
      var req_field_filled = 0;
      $(".caccordion.accordion-user-info *[required]").each(function () {
        if ($(this).val() != "") {
          req_field_filled++;
        }
      });
      // all required fields got filled
      if (req_field_filled == req_field_count) {
        // console.log(req_field_filled, req_field_count);
        //do stuff
        // 1) Show the Application Sction
        if(AFBSI.seasons.length){
          // jQuery(".caccordion.accordion-application").show();
          $(".caccordion.accordion-application").show();
        }
        $("#applications").show();
        // 2) Show Add App Buton
        $("#add_new_app").show();
        // 3) Show Submit Button
        $("#afb-form-submit").show();
        // 4) Hide Comment
        $(".application-comment").show();
        $(".add_application").show();
        $(".terms-and-condition-box").show();
      } else {
        $(".caccordion.accordion-application").hide();
        $("#applications").hide();
       
        $("#afb-form-submit").hide();
        $(".application-comment").hide();
        $(".add_application").hide();
        $(".terms-and-condition-box").hide();
      }
    });
    /**************************************
     ***************************************/
    // Delete Application Feature
    function onDeleteApplication(event) {
      event.preventDefault();
      event.stopPropagation();
      swal("Valgt søknad er slettet").then(function () {
        function getDeleteHiddenField(deletionId = "") {
          return `<input class='deletion-hidden-input-field' type="hidden" name="deletion_id[]" value="${deletionId}">`;
        }
        function sortApplicationNumber() {
          var counter = 1;
          $(".caccordion.accordion-application").each(function () {
            $(this)
              .find(".accordion-header > button.accordion-button")
              .html(`</i> Søknad #${counter}`);
            counter++;
          });
        }
        var delButton = $(event.target).closest('a');
        console.log($(delButton));
        var deletionId = $(delButton).attr("application-id");
        var numberOfApplications = $(
          ".caccordion.accordion-application"
        ).length;
        if (numberOfApplications == 1) {
          //Add Hidden Field to the form if Id is present
          if (deletionId) {
            $("#afb-form").append($(getDeleteHiddenField(deletionId)));
          }
          // Reset the Application
          $(".caccordion.accordion-application")
            .first()
            .find("input, textarea,select")
            .val("");
          sortApplicationNumber();
          $(delButton).closest(".caccordion.accordion-application").hide(); //hide from dom
        } else {
          console.log("deletion id",deletionId)
          if (deletionId) {
            $("#afb-form").append($(getDeleteHiddenField(deletionId))); //apeend hidden field
            $(delButton).closest(".caccordion.accordion-application").remove(); //remove from dom
          } else {
            // delete accordion from the dom
            $(delButton).closest(".caccordion.accordion-application").remove();
          }
          sortApplicationNumber();
        }
      });
      /* Production
      swal({
        title: "Are you sure?",
        text: "Are your sure to delete the application ?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          swal("Application has been deleted!", {
            icon: "success",
          });
        }
      });
      */
    }
    $(".delete-application-button").on("click", onDeleteApplication);

    /******************************
     * Ajax Form jQuery Form Plugin
     */
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".caccordion.accordion-application").length + 3;
    var appIndex = $(".caccordion.accordion-application").length;
    function clone(e) {
      e.preventDefault();
      jQuery('.multiple_select').select2("destroy");
      
      /* check if ediiting for the first then display block the application1*/
      if(jQuery("#applications > .caccordion.accordion-application").first().attr("style") == "display: none;"){
        jQuery("#applications > .caccordion.accordion-application").first().attr("style", "display: block;");
        let get_parent_accordID = jQuery("#applications > .caccordion.accordion-application").first().attr("id");
        let applicationID = get_parent_accordID.charAt(get_parent_accordID.length-1)
        jQuery("#applications > .caccordion.accordion-application").find("select.days-dropdown").addClass("days-dropdown-" + applicationID);
        jQuery("#applications > .caccordion.accordion-application").find("select.days-dropdown").attr("data-application", applicationID);
        jQuery("#applications > .caccordion.accordion-application").find(".selected-days").attr("id", 'selected-days-'+applicationID);
        jQuery("#applications > .caccordion.accordion-application").find(".add-new-day").attr("application-id", applicationID);
        jQuery("#applications > .caccordion.accordion-application .location").find(".sublocation-block").hide();
        selectRefresh();
        return false;
      }
      // console.log($("#applications > .caccordion.accordion-application"));
      // $(".form-select.locations-dropdown").on("change",onLocationChange);//a
      var clone = $("#applications > .caccordion.accordion-application")
        .first()
        .clone();
      clone.find("div.location:not(:first)").remove();
      clone.find("input").val(""); //empty all input value
      clone.find("textarea").val(""); //empty text aria
      clone.find("option").prop("disabled", false); //enable all disabled options
      clone
        .find(".form-select.locations-dropdown")
        .on("change", onLocationChange); //attatch event listener
      clone
        .find(".delete-application-button")
        .attr("application-id", null)
        .on("click", onDeleteApplication);
      clone.find(".form-select.from-time,.form-select.to-time").attr("required", false);
      clone.find(".form-select.from-time").val("").on("change", limitToTime);
      clone.find(".form-select.to-time").val("");
      clone.find('.form-select.priority').on('change',onPrioritySelect);
      // clone.find("select").selectedIndex = 0;
      clone.find(".application-info").find("select").val("");
      clone.find("select.locations-dropdown").val("");
      console.log('clone Index', cloneIndex);
      clone.find("select.days-dropdown").addClass("days-dropdown-" + cloneIndex);
      clone.find(".sublocation-block").hide();
      clone.find(".location-block").hide();
      clone.find(".fra_div").hide();
      clone.find(".til_div").hide();

      var application_Index = $(".caccordion.accordion-application").length;
      var sublocationDropdown = clone.find('.sublocations-dropdown');
      clone.find('.sublocations-dropdown').attr("index",application_Index);
      clone.find('.locations-dropdown').attr("index",application_Index);
      clone.find('.from-time').attr("index",application_Index);
      clone.find('.to-time').attr("index",application_Index);


      clone
        .find("select.locations-dropdown")
        .siblings("input")
        .attr("name", "");
      clone
        .appendTo($(".caccordion.accordion-application").first().parent())
        .attr("id", "accordion-application" + cloneIndex)
        .find("*")
        .each(function () {
          // Increment Id
          var id = this.id || "";
          var match = id.match(regex) || [];
          if (match.length == 3) {
            this.id = match[1] + cloneIndex;
          }

          // Increment data-bs-target if present
          var attr = $(this).attr("data-bs-target");
          if (typeof attr !== "undefined" && attr !== false) {
            $(this).attr("data-bs-target", "#collapse" + cloneIndex);
          }

          // Incrementing Number of the Application
          if ($(this).hasClass("accordion-button")) {
            var applicationNumber = $(
              ".caccordion.accordion-application"
            ).length;
            var replacedHtml =
              '&nbsp;Søknad #' +
              applicationNumber;
            $(this).html(replacedHtml);
          }
        })
        .on("click", "#add_new_app", clone);

      cloneIndex++;
      selectRefresh();
      setTimeout(function(){

        jQuery(".level-dropdown").each(function(){

           if(jQuery(this).find("option").length == 1){
             jQuery(this).find("option:first").prop("selected",true);
             jQuery(this).val(jQuery(this).find("option:first").val());
           }

        })
        jQuery(".sport-dropdown").each(function(){

           if(jQuery(this).find("option").length == 1){
             jQuery(this).find("option:first").prop("selected",true);
             jQuery(this).val(jQuery(this).find("option:first").val());
           }

        })

     },500)
    }
    function remove() {
      $(this).parents(".clonedInput").remove();
    }

    $("#add_new_app").on("click", clone);

    $("button.remove").on("click", remove);
    /**************************************
     ***************************************/

    /******************************
     * Ajax Form
     */
    // console.log("ajax form", jQuery.ajaxForm());
    $("#afb-form").ajaxForm({
      url: AFBUI.ajaxurl,
      type: "post",
      beforeSubmit: function () {
        // console.log(ini);
        /*
    ****************************************
    Bootstrap Form Validation | Submit Only If Validated
    ****************************************
    */
        var season_id = $("#user_group_dropdown")
          .find("option")
          .filter(function () {
            return $(this).val() == $("#user_group_dropdown").val();
          })
          .attr("season_id");
        console.log("Season_ID");
        console.log(season_id);

        var cancelSubmit;
        var forms = document.querySelectorAll(".needs-validation");
        Array.prototype.slice.call(forms).forEach(function (form) {
          form.classList.add("was-validated");
          if (!form.checkValidity()) {
            // Not Valid
            cancelSubmit = false;
          }
        });
        //There must be one location dropdown selection
        var appsWithLocationSelection = 0;
        $('.caccordion.accordion-application').each(function(){
          var selectedDropdowns = 0;
          $(this).find(".form-select.locations-dropdown").each(function(){if($(this).val()){
            selectedDropdowns++;
          }});
          if(selectedDropdowns > 0 ){
            appsWithLocationSelection++;
          }
        });
        if(appsWithLocationSelection !== $('.caccordion.accordion-application').length){
          swal({
            title: `Fyll ut alle felt`,
            icon: "warning",
            time: 2*1000,
            buttons: {
              cancel: false,
              default: "Ok",
            },
            dangerMode: true,
          });
          cancelSubmit = false
        }
        console.log("cancel submit value");
        console.log(cancelSubmit);
        // cancelSubmit = false;
        // find('.form-select.locations-dropdown').find("option").filter
        if (cancelSubmit == false) {
          return false;
        }

        /**************************************
         ***************************************/
      },
      data: {
        action: "user_info",
        season_id: jQuery("#user_group_dropdown").find("option:selected").attr("season_id")
      },
      success: function (response) {
        if(response){

          jQuery(".form_submit_button, .form_submit_text, .form_submit_button2").remove();

          swal({
            title: "Suksess!",
            text: "Din søknad er mottattt!\n   Du blir nå videresendt...",
            timer: 3 * 1000,
            buttons: false,
            icon: "success",
          }).then(function () {
            window.location.href = response.redirect_url;
          });
        }
      },
    });
    /**************************************
     ***************************************/
    /*$('.form.user_info').on("submit",function(e){
      e.preventDefault();
      var code = $(telephone).intlTelInput("getSelectedCountryData").dialCode;
      var phoneNumber = $(telephone).val();
      // var orgPers = $("#org/pers").val();
      //var type = $("#type").val();
      var userName = $("#userName").val();
      var email = $("#user_email").val();
      var address= $("#user_address").val();
      var zipCode = $("#user_zip").val();
      var city = $("#user_city").val();
      // User Group
      var userGroupValue = $(userGroup).val();

      $.post(
        AFBUI.ajaxurl,
        {
          action: "user_info",
          code,
          phone_number: phoneNumber,
          email,
          address,
          zip_code: zipCode,
          city,
          user_group: userGroupValue,
          user_name: userName,

        },
        function(res){
          console.log("Everything Was Success");
          // show success notification
          $('.toast').toast("show");
        }
      );


      
      
    });*/


  /********** Add New Day ***********/
  /********** Add New Day ***********/
  function onAddNewDay(e){
    e.preventDefault();
    jQuery('.multiple_select').select2("destroy");
    let applicationID = $(this).attr('application-id');
    /* if not applicationID found, then add the parent accordion ID*/
    var get_parent_accordion = $(this).parent().closest('.accordion-application');
    var get_parent_accordionID = $(this).parent().closest('.accordion-application').attr('id');
    /* clone the row location*/
    var clone = $(".row .location")
      .first()
      .clone();

    clone.find("option").prop("disabled", false); //enable all disabled options
    clone
      .find(".form-select.locations-dropdown")
      .on("change", onLocationChange); //attatch event listener
    clone.find(".form-select.from-time,.form-select.to-time").attr("required", false);
    clone.find(".form-select.from-time").val("").on("change", limitToTime);
    clone.find(".form-select.to-time").val("");
    clone.find("select.locations-dropdown").val("");
    clone.find("select.sublocations-dropdown").val("");
    clone.find("select.days-dropdown").val("");
    clone.find("select.days-dropdown").addClass("days-dropdown-"+applicationID);
    clone.find("select.days-dropdown").attr("data-application",applicationID);
    clone.find(".sublocation-block").hide();
    clone.find(".location-block").hide();
    clone.find(".fra_div").hide();
    clone.find(".til_div").hide();

   /* var application_Index = $(".caccordion.accordion-application").length;

    var sublocationDropdown = clone.find('.sublocations-dropdown');
    var indexx = clone.find('.sublocations-dropdown').attr("index",application_Index);*/



    clone
      .find("select.locations-dropdown")
      .siblings("input")
      .attr("name", "");
    /* get parent of current application */
    var parent_accordion = $(this).parent().closest('.accordion-body');
    var sublocations_Index = parent_accordion.find(".sublocations-dropdown").attr("index");

    clone.find('.sublocations-dropdown').attr("index",sublocations_Index);

    var locations_Index = parent_accordion.find(".locations-dropdown").attr("index");

    clone.find('.locations-dropdown').attr("index",locations_Index);


     var from_Index = parent_accordion.find(".from-time").attr("index");

    clone.find('.from-time').attr("index",from_Index);

    var to_Index = parent_accordion.find(".to-time").attr("index");

    clone.find('.to-time').attr("index",to_Index);

    var daysArr = [];
    parent_accordion.find('.location select').each(function() {
      let dayVal = $(this).attr('day');
      if(dayVal){
        if(daysArr.indexOf(dayVal) === -1) {
          daysArr.push(dayVal);
        }
      }
    });
    /* check previous selected days, select next day from the array */
    var days_array = ['','monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    /* update naming conventions related to the day */
    // var selectedDays = JSON.parse(jQuery(this).closest('.selected-days').val());
    /* insert before add new btn */
    $(clone).insertBefore( $(this));

    /* Selected Days */
    var selectedDays = $('#selected-days-'+applicationID).val();
    if(selectedDays){
      selectedDays = JSON.parse(selectedDays);
      Array.from(selectedDays).forEach(entry => {
        jQuery('select.days-dropdown-'+ applicationID +' option[value='+entry+']').attr("disabled", "disabled");
      });
    }
    selectRefresh();
  }

  $(document.body).on("click", '.add-new-day', onAddNewDay);
  // $(".add-new-day").on("click", onAddNewDay);
  /********** Add New Day ***********/
  /********** Add New Day ***********/
});
})(jQuery);

// season_id: $("#user_group_dropdown").filter(function(){$(this).val() == $($("#user_group_dropdown"))}).attr('season_id'),

/* on day change dropdown, change the names for the*/
jQuery(document.body).on("change", '.days-dropdown', function(){
  var applicationID = jQuery(this).data('application');
  var parent_accordion = jQuery(this).parent().closest('.location');
  var days_array = ['','monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
  console.log('selected day =',days_array[this.value]);
  console.log('selected old day =',days_array[this.defaultValue]);
  console.log('application id =', applicationID);

  if(this.value){
    jQuery(this).parent().parent().find(".location-block").show();
    /* change the names for the location div */
    var dayText = days_array[this.value];
    var dayDropdown = parent_accordion.find('.days-dropdown');
    dayDropdown.attr('name',dayText+'-day[]');
    dayDropdown.attr('day',dayText);

    /* Location */
    var locationDropdown = parent_accordion.find('.locations-dropdown');
    var locations_indexx = parent_accordion.find('.locations-dropdown').attr("index");
    locationDropdown.attr('name',dayText+'-location['+locations_indexx+'][]');
    locationDropdown.attr('day',dayText);

    /* SubLocation */
    var sublocationDropdown = parent_accordion.find('.sublocations-dropdown');
    var sublocations_indexx = parent_accordion.find('.sublocations-dropdown').attr("index");
    sublocationDropdown.attr('name',dayText+'-sublocation['+sublocations_indexx+'][]');
    sublocationDropdown.attr('day',dayText);

    /* From Time */
    var fromTimeDropdown = parent_accordion.find('.form-select.from-time');
    var from_indexx = parent_accordion.find('.from-time').attr("index");
    fromTimeDropdown.attr('name',dayText+'-from-time['+from_indexx+'][]');
    fromTimeDropdown.attr('day',dayText);

    /* To Time */
    var toTimeDropdown = parent_accordion.find('.form-select.to-time');
    var to_indexx = parent_accordion.find('.to-time').attr("index");
    toTimeDropdown.attr('name',dayText+'-to-time['+to_indexx+'][]');
    toTimeDropdown.attr('day',dayText);

    /* Selected Days */
    /* get seleced value if already exists then display error otherwise append to list*/
    var currentDay = this.value;
    var selectedDays = jQuery('#selected-days-'+applicationID).val();
    let arrrrr = [];

    jQuery(this).parent().parent().parent().parent().find(".days-dropdown").each(function(){
      if(this.value != ""){
        arrrrr.push(this.value);
      }
    });
    
    if(arrrrr.length > 0){

      selectedDays = JSON.stringify(arrrrr);
      jQuery('#selected-days-'+applicationID).val(selectedDays);
      jQuery(this).parent().parent().parent().parent().find(".days-dropdown").find("option").removeAttr("disabled");
      Array.from(arrrrr).forEach(entry => {
        jQuery('select.days-dropdown-'+ applicationID +' option[value='+entry+']').attr("disabled", "disabled");
      });

    }else{
      jQuery('#selected-days-'+applicationID).val("");

      jQuery(this).parent().parent().parent().parent().find(".days-dropdown").find("option").removeAttr("disabled");

    }
  }
});

jQuery("#user_group_dropdown").change(function(){
  setTimeout(function(){
     
   jQuery("#telephone").removeAttr("placeholder");
  },200)

 var season_id = jQuery(this).find("option:selected").attr("season_id");
jQuery("input[name=season_id]").val(season_id);
setTimeout(function(){
  toggelApplicationSection();
},500)

})

jQuery(document).ready(function(){

jQuery("#user_group_dropdown").change();
toggelApplicationSection();
})


  
