window.calendar = '';

//Kamils comment



// sk change
(function ($) {
    jQuery("body").addClass("mobiscroll_calender");
    jQuery("body").addClass("season_calender_body");

    function getMonthDays(month) {
        var values = [];
        for (var i = 1; i <= MAX_MONTH_DAYS[month - 1]; i++) {
            values.push(i);
        }
        return values;
    }

    function move_algo_booking(){

        let selected_season_data = jQuery(".season_change").val();

        if(selected_season == ""){
             showToast('Season not selected!');
        }else{

            mobiscroll.confirm({
                message: 'Er du sikker at du skal overføre til neste steg?',
                okText: 'Ja',
                cancelText: 'Avbryt',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        showLoader();
                        jQuery.ajax({
                            type: "POST",
                            url: WPMCalendarV2Obj.ajaxurl,
                            data: {
                                action: 'move_algo_booking_mobiscroll',
                                selected_season: selected_season,
                            },
                            success: function (response) {
                                hideLoader();
                                showToast('Da er de overført :) !');
                                //window.location.reload();
                            }
                        });

                    }
                }
            });
        }    
    }
    function run_algo_booking(form){

        let selected_season_data = jQuery(".season_change").val();


        if(selected_season == ""){
             showToast('Season not selected!');
        }else{

            let keep_simple_location = form.find('select[name=change_location_single_booking]').val();
            let keep_complex_location = "0"

            if(form.find('select[name=change_location_grouped_booking]').length > 0){
                 keep_complex_location = form.find('select[name=change_location_grouped_booking]').val();
            }

            let time_date_option = form.find('select[name=algo_time]').val();

            let x_weeks = "0"

            if(form.find('select[name=algo_move_booking]').length > 0){
                 x_weeks = form.find('select[name=algo_move_booking]').val();
            }

            let split_complex = form.find('select[name=algo_optimalization]').val();;
            let season_id = selected_season;

            mobiscroll.confirm({
                message: 'Er du sikker at du vil kjøre algoritmen nå?',
                okText: 'Ja',
                cancelText: 'Avbryt',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        showLoader();
                        jQuery.ajax({
                            type: "POST",
                            url: "https://scheduling.gibbs.no/"+window.location.host+"/bookings_scheduling_.php",
                            data: {
                                action: "run_algorithm",
                                season: season_id,
                                keepsimplelocation: keep_simple_location,
                                keepcomplexlocation: keep_complex_location,
                                timedateoption: time_date_option,
                                xweeks: x_weeks,
                                splitcomplex: split_complex,
                            },
                            beforeSend: function(xhr) {
                                 xhr.setRequestHeader("Authorization", "Bearer "+script_vars['current_user_jwt'])
                            },
                            success: function (response) {
                                hideLoader();
                                showToast('Fordelingen er gjort! :)');

                                jQuery(".refresh-calendar").click();
                            },
                            error: function(response) { 
                                hideLoader();
                                showToast(JSON.stringify(response));
                                jQuery(".refresh-calendar").click();
                            } 
                        });

                    }
                }
            });
        }    
    }

    function get_resources(){

                resources = prepareFullCalendar('');

                var section_resources_value = resources;

                if (typeof (filter_group) != 'undefined' && filter_group != null && filter_group != '') {
                    var selected_values = filter_group.toString();
                    selected_values = selected_values.split(',');
                    filterCalForGroup(selected_values, calendar);
                }

                if (Array.isArray(filter_locations) && filter_locations.length > 0) {
                    var selectedListings = filter_locations.map(function (listingId) {
                        return parseInt(listingId)
                    });

                    section_resources_value = resources.filter(function (resource) {
                        return selectedListings.includes(resource.value)
                    });

                     filterListingSelect.setVal(selectedListings);
                } else {
                    filterListingSelect.setVal([]);
                    section_resources_value = resources;
                }

                return section_resources_value;

               // console.log(section_resources_value)
                //calendar.setOptions({ resources: section_resources_value });
    }

    function getRecurrenceText() {
        var text;

        switch (recurrenceRepeat) {
            case 'daily':
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' days') : 'Daily';
                break;
            case 'weekly':
                var weekDays = recurrenceWeekDays.split(',');
                var weekDaysText = weekDays.map(function (weekDay) {
                    return DAY_NAMES[DAY_NAMES_MAP[weekDay]];
                }).join(', ');
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' weeks') : 'Weekly';
                text += ' on ' + weekDaysText;
                break;
            case 'monthly':
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' months') : 'Monthly';
                text += ' on day ' + recurrenceDay;
                break;
            case 'yearly':
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' years') : 'Annualy';
                text += ' on ' + MONTH_NAMES[recurrenceMonth - 1] + ' ' + recurrenceDay;
                break;
        }

        switch (recurrenceCondition) {
            case 'until':
                text += ' until ' + mobiscroll.util.datetime.formatDate('MMMM D, YYYY', new Date(recurrenceUntil));
                break;
            case 'count':
                text += ', ' + recurrenceCount + ' times';
                break;
        }

        return text;
    }

    function getRecurrenceRule() {
        var d = new Date(eventStart);
        var weekNr = Math.ceil(d.getDate() / 7);
        var weekDay = DAY_NAMES_SHORT[d.getDay()];
        var month = d.getMonth() + 1;
        var monthDay = d.getDate();

        switch (eventRecurrence) {
            // Predefined recurring rules
            case 'daily':
                return { repeat: 'daily' };
            case 'weekly':
                return { repeat: 'weekly', weekDays: weekDay };
            case 'monthly':
                return { repeat: 'monthly', day: monthDay };
            case 'monthly-pos':
                return { repeat: 'monthly', weekDays: weekDay, pos: weekNr };
            case 'yearly':
                return { repeat: 'yearly', day: monthDay, month: month };
            case 'yearly-pos':
                return { repeat: 'yearly', weekDays: weekDay, month: month, pos: weekNr };
            case 'weekday':
                return { repeat: 'weekly', weekDays: 'MO,TU,WE,TH,FR' };
            // Custom recurring rule
            case 'custom':
            case 'custom-value':
                var rule = {
                    repeat: recurrenceRepeat,
                    interval: recurrenceInterval
                };
                switch (recurrenceRepeat) {
                    case 'weekly':
                        rule.weekDays = recurrenceWeekDays;
                        break;
                    case 'monthly':
                        rule.day = recurrenceDay;
                        break;
                    case 'yearly':
                        rule.day = recurrenceDay;
                        rule.month = recurrenceMonth;
                        break;
                }
                switch (recurrenceCondition) {
                    case 'until':
                        rule.until = recurrenceUntil;
                        break;
                    case 'count':
                        rule.count = recurrenceCount;
                        break;
                }
                return rule;
            default:
                return null;
        }
    }

    function convertRecurrenceRuleToString(rule) {

        if (!rule) {
            return '';
        }

        var ruleStr = '';

        Object.keys(rule).forEach(function (key, index) {
            switch (key) {
                case 'repeat':
                    ruleStr += 'FREQ=' + rule[key].toUpperCase() + ';';
                    break;
                case 'weekDays':
                    ruleStr += 'BYDAY=' + rule[key] + ';';
                    break;
                default:
                    ruleStr += key.toUpperCase() + '=' + rule[key] + ';';
            }
        });

        return ruleStr;
    }

    function convertRecurrenceRuleToObject(ruleStr) {
        if (!ruleStr) {
            return '';
        }

        var rule = {};

        var ruleParts = ruleStr.split(';');

        ruleParts.forEach(function (rulePart) {
            var rulePartParts = rulePart.split('=');

            switch (rulePartParts[0]) {
                case 'FREQ':
                    rule.repeat = rulePartParts[1].toLowerCase();
                    break;
                case 'BYDAY':
                    rule.weekDays = rulePartParts[1];
                    break;
                default:
                    if (rulePartParts[0] !== '') {
                        rule[rulePartParts[0].toLowerCase()] = rulePartParts[1].toLowerCase();
                    }
            }
        });

        return rule;
    }

    function getRecurrenceTypes(date, recurrence) {
        var d = new Date(date);
        var weekDay = DAY_NAMES[d.getDay()];
        var weekNr = Math.ceil(d.getDate() / 7);
        var month = MONTH_NAMES[d.getMonth()].text;
        var monthDay = d.getDate();
        var ordinal = { 1: 'first', 2: 'second', 3: 'third', 4: 'fourth', 5: 'fifth' };
        var data = [
            { value: 'norepeat', text: 'Does not repeat' },
            { value: 'daily', text: 'Daily' },
            { value: 'weekly', text: 'Weekly on ' + weekDay },
            { value: 'monthly', text: 'Monthly on day ' + monthDay },
            { value: 'monthly-pos', text: 'Monthly on the ' + ordinal[weekNr] + ' ' + weekDay },
            { value: 'yearly', text: 'Annually on ' + month + ' ' + monthDay },
            { value: 'yearly-pos', text: 'Annually on the ' + ordinal[weekNr] + ' ' + weekDay + ' of ' + month },
            { value: 'weekday', text: 'Every weekday (Monday to Friday)' },
            { value: 'custom', text: 'Custom' }
        ];
        if (recurrence === 'custom-value') {
            data.push({ value: 'custom-value', text: getRecurrenceText() });
        }
        return data;
    }

    function getEventRecurrence(event) {
        var recurringRule = event.recurring;
        if (recurringRule) {
            var repeat = recurringRule.repeat;
            if (recurringRule.interval > 1 || recurringRule.count || recurringRule.until) {
                return 'custom-value';
            }
            switch (repeat) {
                case 'weekly':
                    var weekDays = recurringRule.weekDays || '';
                    if (weekDays === 'MO,TU,WE,TH,FR') {
                        return 'weekday';
                    }
                    if (weekDays.split(',').length > 1) {
                        return 'custom-value';
                    }
                case 'monthly':
                case 'yearly':
                    if (recurringRule.pos) {
                        return repeat + '-pos';
                    }
                default:
                    return repeat;
            }
        }
        return 'norepeat';
    }

    function toggleDatetimePicker(allDay) {
        // Toggle between date and datetime picker
        eventStartEndPicker.setOptions({
            controls: allDay ? ['date'] : ['datetime'],
            responsive: allDay ? { medium: { controls: ['calendar'], touchUi: false } } : { medium: { controls: ['calendar', 'time'], touchUi: false } }
        });
    }

    function toggleRecurrenceEditor(recurrence) {
        console.log(recurrence)
        if (recurrence === 'custom') {
            $('.popup-event-recurrence-editor').show();
        } else {
            $('.popup-event-recurrence-editor').hide();
        }
    }

    function toggleRecurrenceText(repeat) {
        $('.md-recurrence-text').each(function () {
            var $cont = $(this);
            if ($cont.hasClass('md-recurrence-' + repeat)) {
                $cont.show();
            } else {
                $cont.hide();
            }
        });
    }

    function navigateToEvent(event) {
        var d = new Date(event.start);
        var year = d.getFullYear();
        var month = d.getMonth();
        var day = d.getDate();
        var recurringRule = event.recurring;
        var addMonth = 0;
        var addYear = 0;
        if (recurringRule) {
            var recurringDay = recurringRule.day;
            var recurringMonth = recurringRule.month - 1;
            switch (recurringRule.repeat) {
                case 'monthly':
                    if (day > recurringDay) {
                        addMonth = recurringRule.interval || 1;
                    }
                    day = recurringDay;
                    break;
                case 'yearly':
                    if (month > recurringMonth || (month === recurringMonth - 1 && day > recurringDay)) {
                        addYear = recurringRule.interval || 1;
                    }
                    day = recurringDay;
                    month = recurringMonth;
                    break;
            }
        }
        calendar.navigate(new Date(year + addYear, month + addMonth, day, d.getHours()));
    }

    

   

    

    // Fills the popup with the event's data
    function fillPopup(event) {




        // Load event properties
        eventId = event.id;
        eventTitle = event.title;
        eventListing = event.listing;
        eventStatus = (event.status_manuale.value)?event.status_manuale.value:"";
        eventDescription = event.description || '';
        eventAllDay = event.allDay;
        eventStart = event.start;
        eventEnd = event.end;
        eventColor = event.color;
        eventRecurringException = event.recurringException || [];
        eventRecurrence = getEventRecurrence(event);
        eventClient = event.client ? event.client.value : event.wpm_client;
        eventTeam = event.team ? event.team.value : event.wpm_team;
        eventResource = event.resource;


        if(type_of_form == "1"){
             jQuery("#week_day_start").find("option").removeAttr("selected");

            weekday = moment(eventStart).format("yyyy-MM-DD");

            $week_day_start.val(weekday)
        }




        if(eventTitle  == "New event"){
            eventTitle = "Ingen tittel";
        }

        if(eventListing != undefined && Array.isArray(eventListing)){

            eventListing.push(event.resource);

        }else{
            eventListing = [];
            eventListing.push(event.resource);
        }

        eventListingSelect.setVal(eventListing);

        if(eventStatus != undefined){
           eventStatusSelect.val(eventStatus);
        }else{
            eventStatusSelect.val("");
        }



        // Load recurrence rule properties, with default values
        var recurringRule = event.recurring || {};
        recurrenceRepeat = recurringRule.repeat || 'daily';
        recurrenceInterval = recurringRule.interval || 1;
        recurrenceCondition = recurringRule.until ? 'until' : (recurringRule.count ? 'count' : 'never');
        recurrenceMonth = recurringRule.month || 1;
        recurrenceDay = recurringRule.day || 1;
        recurrenceWeekDays = recurringRule.weekDays || 'SU';
        recurrenceCount = recurringRule.count || 10;
        recurrenceUntil = recurringRule.until;

        // Set event fields
       

        /*if(eventStatus && eventStatus != undefined){
            eventStatusSelect.setVal(eventStatus);
        }*/
        
        $('.popup-event-description').val(eventDescription);
        eventStartEndPicker.setVal([eventStart, eventEnd]);
        
    }
     function openCustomerPopup(){
        addCustomerPopup.setOptions({
            maxWidth: 700,
            onClose: function () {    
            }
        });
        addCustomerPopup.open();

        



        jQuery(".close_customer_popup").on("click",function(){
            addCustomerPopup.close();
        })

        jQuery("#customerForm").on("submit",function(){

            jQuery(".show_info_div").html('');

            let dialCode = jQuery("#customer_phone").intlTelInput("getSelectedCountryData").dialCode;
            jQuery(this).find("input[name=country_code]").val("+"+dialCode);

            $.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: jQuery(this).serialize(),
                success: function (response) {

                    if(response.success == true){
                        jQuery(".show_info_div").html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        jQuery("#customerForm")[0].reset();
                        get_customer_list();
                        setTimeout(function(){
                            jQuery(".show_info_div").html('');
                               addCustomerPopup.close();
                        },2000)
                    }else if(response.message){
                        jQuery(".show_info_div").html('<div class="alert alert-danger" role="alert">'+response.message+'</div>')
                    }
                   
                }
            });

        })
    }

   
    function createAddPopup(event, target) {
        var success = false;

        jQuery(".cal_custom_fields").html("");

        



        // Hide delete button inside add popup
        $eventDeleteButton.parent().hide();

        // Set popup header text and buttons
        addEditPopup.setOptions({
            maxWidth: 600,
            anchor: target,                          // More info about anchor: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-anchor
            onClose: function () {                   // More info about onClose: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onClose
                // Remove event if popup is cancelled
                if (!success) {
                    calendar.removeEvent(event);
                }
            }
        });

        jQuery(".close_event_popup").on("click",function(){
            addEditPopup.close();
        })
        jQuery(".openCustomer").on("click",function(){
            openCustomerPopup();
        })
        jQuery("#eventForm").on("submit",function(){

            let error = 0

            if(eventClient == "" || eventClient == undefined){
                jQuery("#wpm-client").parent().find("input").focus();
                jQuery("#wpm-client").parent().addClass("required_focus");
                error = 1;
                return false;
            }
            if(eventListing == ""  || eventListing == undefined || eventListing.length < 0){
                jQuery("#wpm-listing").parent().find("input").focus();
                jQuery("#wpm-listing").parent().addClass("required_focus");
                error = 1;
                return false;
            }
            if(eventStatus == ""  || eventStatus == undefined){
                jQuery("#status_manuale").parent().find("select").focus();
                jQuery("#status_manuale").parent().addClass("required_focus");
                error = 1;
                return false;
            }

            if(error == 0){

                if(eventTitle == "New event"){
                    if(eventClientName != "" && eventClientName != undefined){
                        eventTitle = eventClientName;
                    }else{
                        eventTitle = "Ingen tittel";
                    }
                }

                var newEvent = {
                        id: eventId,
                        title: eventTitle,
                        wpm_client: eventClient,
                        listings: eventListing,
                        description: eventDescription,
                        allDay: eventAllDay,
                        start: eventStart,
                        end: eventEnd,
                        recurring: getRecurrenceRule(),
                        status: eventStatus,
                        recurrenceId: eventId,
                        gymSectionId: eventResource,
                        recurringException: eventRecurringException,
                        recurrenceException: eventRecurringException,
                        recurrenceEditMode: '',
                        resource: eventResource,
                        gymId: '',
                        repert: ''
                    };

                   


                    newEvent.recurrenceRule = convertRecurrenceRuleToString(newEvent.recurring);

                    calendar.updateEvent(newEvent);

                    navigateToEvent(newEvent);



                    add_booking(newEvent);

                    success = true;

                    addEditPopup.close();

            }


        })

       

        fillPopup(event);
        addEditPopup.open();

        setTimeout(function(){
           jQuery("#event-title").focus();
        },500)

        
    }

   

    function createEditPopup(event, target) {
        setTimeout(function(){
            tooltip.close();
        },20)
        // Show delete button inside edit popup
        jQuery(".cal_custom_fields").html("");
        $eventDeleteButton.parent().show();


        editedEvent = event;
        originalRecurringEvent = event.recurring ? event.original : null;
        eventOccurrence = event;

        //console.log(event)

        // Set popup header text and buttons

        addEditPopup.setOptions({
            maxWidth: 600,
            anchor: target, 
            cssClass: 'editPopup',
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Edit',
                keyCode: 'enter',
                handler: function (target_d) {
                    /*start*/
                    console.log(event)
                        let error = 0

                        if(type_of_form == "1"){

                            if(week_day_start && week_day_start != ""){
                                let date_st = week_day_start+" "+moment(eventStart).format("HH:mm:ss");
                                let date_ed = week_day_start+" "+moment(eventEnd).format("HH:mm:ss");
                                eventStart = new Date(date_st);
                                eventEnd = new Date(date_ed);
                            }
                        }


                        if(eventClient == "" || eventClient == undefined){
                            eventClient = 0;
                        }
                        if(eventStatus == ""  || eventStatus == undefined){
                              eventStatus = "waiting";
                        }

                        if(moment(eventStart).format("YYYY-MM-DD HH:mm") == moment(eventEnd).format("YYYY-MM-DD HH:mm")){
                            showToastMessage("End date should greater then start date","danger","center");
                            error = 1;
                            return false;
                        }

                        if(error == 0){

                            if(eventTitle == "New event" || eventTitle == "Ingen tittel" || eventTitle == ""){
                                if(eventClient != "" && eventClient != undefined && eventClient != 0){
                                    eventTitle = "";
                                }else{
                                    eventTitle = "Ingen tittel";
                                }
                            }
                                if (originalRecurringEvent) {
                                    createRecurrenceEditPopup(false);
                                } else {
                                    //console.log(eventStatus);
                                    var updatedEvent = {
                                        id: eventId,
                                        title: eventTitle,
                                        wpm_client: eventClient,
                                        team: eventTeam,
                                        description: eventDescription,
                                        allDay: eventAllDay,
                                        start: eventStart,
                                        end: eventEnd,
                                        recurring: getRecurrenceRule(),
                                        status: eventStatus,
                                        recurrenceId: eventId,
                                        gymSectionId: eventResource,
                                        recurringException: eventRecurringException,
                                        recurrenceEditMode: '',
                                        resource: eventResource,
                                        gymId: '',
                                        repert: ''
                                    };
                                    updatedEvent.recurrenceRule = convertRecurrenceRuleToString(updatedEvent.recurring);

                                    update_booking(updatedEvent);

                                    calendar.updateEvent(updatedEvent);
                                    navigateToEvent(updatedEvent);   
                                    addEditPopup.close();
                                }

                        }
                    /*end*/    
                },
                cssClass: 'mbsc-edit-popup-button-primary'
            }],
        });


        jQuery(".close_event_popup").on("click",function(){
            addEditPopup.close();
        })

        $eventClient.change();

        var booking_id = event.id;

       

        let datas = {
           "action" : "get_season_custom_fields_for_calender_mobiscroll",
           "listing_id" : event.resourceId,
           "booking_id" : booking_id,
           "season_view" : jQuery(".change_season_view").val(),
        }
        jQuery.ajax({
              type: "POST",
              url: WPMCalendarV2Obj.ajaxurl,
              data: datas,
              success: function(resultData){
                  jQuery(".cal_custom_fields").html(resultData);
              }
        });

        jQuery("#eventForm").on("submit",function(){

            let error = 0

            if(eventClient == "" || eventClient == undefined){
                jQuery("#wpm-client").parent().find("input").focus();
                jQuery("#wpm-client").parent().addClass("required_focus");
                error = 1;
                return false;
            }
            if(eventListing == ""  || eventListing == undefined || eventListing.length < 0){
                jQuery("#wpm-listing").parent().find("input").focus();
                jQuery("#wpm-listing").parent().addClass("required_focus");
                error = 1;
                return false;
            }
            if(eventStatus == ""  || eventStatus == undefined){
                jQuery("#status_manuale").parent().find("select").focus();
                jQuery("#status_manuale").parent().addClass("required_focus");
                error = 1;
                return false;
            }

            if(error == 0){

                if(eventTitle == "New event"){
                    if(eventClientName != "" && eventClientName != undefined){
                        eventTitle = eventClientName;
                    }else{
                        eventTitle = "Ingen tittel";
                    }
                }

                    if (originalRecurringEvent) {
                        createRecurrenceEditPopup(false);
                    } else {
                        //console.log(eventStatus);
                        var updatedEvent = {
                            id: eventId,
                            title: eventTitle,
                            wpm_client: eventClient,
                            team: eventTeam,
                            description: eventDescription,
                            allDay: eventAllDay,
                            start: eventStart,
                            end: eventEnd,
                            recurring: getRecurrenceRule(),
                            status: eventStatus,
                            recurrenceId: eventId,
                            gymSectionId: eventResource,
                            recurringException: eventRecurringException,
                            recurrenceEditMode: '',
                            resource: eventResource,
                            gymId: '',
                            repert: ''
                        };



                        updatedEvent.recurrenceRule = convertRecurrenceRuleToString(updatedEvent.recurring);
                        update_booking(updatedEvent);

                        calendar.updateEvent(updatedEvent);
                        navigateToEvent(updatedEvent);
                        addEditPopup.close();
                    }

            }


        })

        //fillPopup(event);
      //  jQuery("#wpm-client").change();


        //ajaxCallEvent(event);
        fillPopup(event);
        addEditPopup.open();


    }

    function ajaxCallEvent(event) {

        data = {
            action: "wpm_get_season_booking_info",
        }
        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl + "?booking_id=" + event.id,
            data: data,
            success: function (response) {
                $("#calendar-edit-popup").html(response)

                repeatPopUp = $('#event-repeat-popup').mobiscroll().popup({

                    fullScreen: true,
                    width: '100%',
                    maxWidth: 800,
                    maxHeight: '40vh'
                }).mobiscroll('getInst');

                repeatPopUp.setOptions({
                    headerText: 'Repeat Pattern',
                })

                //  
                $("#wpm-repeating").on("click", function () {
                    repeatPopUp.open();
                })

                fields_init('edit')
                fillPopup(event);
            }
        });
    }

    function showEventSummary(event, target) {
      //  console.log(event)


        let left_info_data = jQuery("#season-calendar-event-tooltip-popup").find(".left_info_data");
        let right_info_data = jQuery("#season-calendar-event-tooltip-popup").find(".right_info_data");

        left_info_data.html("");
        right_info_data.html("");

        /* left side*/

        left_info_data.append('<p class="tooltip-custom heading_div">'+
                    '<h4><b>Søkt</b></h4>'+
                '</p>');

        if(event.org_data && event.org_data.name && event.org_data.name != ""){
            left_info_data.append('<p class="tooltip-custom listing_div">'+
                    '<b>Annonse:</b> <span>'+event.org_data.name+'</span>'+
                '</p>')
        }
        if(event.org_data && event.org_data.date_start && event.org_data.date_start != ""){

            let printDate = "";

            if(type_of_form == "2"){
                printDate = moment(event.org_data.date_start).format("MMMM DD,YYYY, HH:mm")+" - "+moment(event.org_data.date_end).format("MMMM DD,YYYY, HH:mm");
            }else{
                 printDate = moment(event.org_data.date_start).format("dddd")+" "+moment(event.org_data.date_start).format("HH:mm")+" - "+moment(event.org_data.date_end).format("HH:mm");

            }
            left_info_data.append('<p class="tooltip-custom time_div">'+
                    '<b>Tid:</b> <span>'+printDate+'</span>'+
                '</p>')
        }

        left_info_data.append('<hr />');


        if(event.app_data && event.app_data.score && event.app_data.score != ""){
            left_info_data.append('<p class="tooltip-custom score_div">'+
                    '<b>Søker poeng:</b> <span>'+event.app_data.score+'</span>'+
                '</p>')
        }
/*         if(event.app_data && event.app_data.sum_desired_hours && event.app_data.sum_desired_hours != ""){
            left_info_data.append('<p class="tooltip-custom sum_desired_hours_div">'+
                    '<b>Ønsket timer:</b> <span>'+event.app_data.sum_desired_hours+'</span>'+
                '</p>')
        }

        if(event.app_data && event.app_data.sum_algo_hours && event.app_data.sum_algo_hours != ""){
            left_info_data.append('<p class="tooltip-custom sum_algo_hours_div">'+
                    '<b>Forslag fra algoritme:</b> <span>'+event.app_data.sum_algo_hours+'</span>'+
                '</p>')
        }

        if(event.app_data && event.app_data.sum_received_hours && event.app_data.sum_received_hours != ""){
            left_info_data.append('<p class="tooltip-custom sum_received_hours_div">'+
                    '<b>Tildelt timer:</b> <span>'+event.app_data.sum_received_hours+'</span>'+
                '</p>')
        }
 */

        left_info_data.append('<hr />');

        if(event.extra_info){
            if(event.extra_info.age_group && event.extra_info.age_group != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("age_group")){
               left_info_data.append('<p class="tooltip-custom age_group_div">'+
                    '<b>Aldersgruppe:</b> <span>'+event.extra_info.age_group+'</span>'+
                '</p>')
            }
            if(event.extra_info.sport && event.extra_info.sport != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("sport")){
               left_info_data.append('<p class="tooltip-custom sport_div">'+
                    '<b>Idrett:</b> <span>'+event.extra_info.sport+'</span>'+
                '</p>')
            }
            if(event.extra_info.members && event.extra_info.members != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("members")){
               left_info_data.append('<p class="tooltip-custom members_div">'+
                    '<b>Antall medlemmer:</b> <span>'+event.extra_info.members+'</span>'+
                '</p>')
            }
            if(event.extra_info.team_name && event.extra_info.team_name != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("team_name")){
               left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Lag:</b> <span>'+event.extra_info.team_name+'</span>'+
                '</p>')
            }
            if(event.extra_info.team_level && event.extra_info.team_level != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("level")){
               left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Nivå:</b> <span>'+event.extra_info.team_level+'</span>'+
                '</p>')
            }
            if(event.extra_info.type && event.extra_info.type != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("type")){
               left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Type:</b> <span>'+event.extra_info.type+'</span>'+
                '</p>')
            }
        }

        /* end left side*/

        /* right side */
        if(event.id && event.id != ""){
            let first_event_text = "";




            if(event.first_event_id && event.first_event_id != "" && event.first_event_id != event.id){
                first_event_text = '<span><svg width="1em" height="1em" viewBox="0 0 55 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M15.1445 23.8044H13.8945C10.3789 23.8044 7.64453 21.0701 7.64453 17.5544C7.64453 14.1169 10.3789 11.3044 13.8945 11.3044H26.3945C29.832 11.3044 32.6445 14.1169 32.6445 17.5544C32.6445 21.0701 29.832 23.8044 26.3945 23.8044H25.3008C25.2227 24.1951 25.1445 24.6638 25.1445 25.0544C25.1445 26.8513 26.2383 28.2576 27.8789 28.7263C33.3477 27.9451 37.6445 23.2576 37.6445 17.5544C37.6445 11.3826 32.5664 6.30444 26.3945 6.30444H13.8945C7.64453 6.30444 2.64453 11.3826 2.64453 17.5544C2.64453 23.8044 7.64453 28.8044 13.8945 28.8044H15.6133C15.3008 27.6326 15.1445 26.3826 15.1445 25.0544C15.1445 24.6638 15.1445 24.2732 15.1445 23.8044ZM41.3945 13.8044H39.5977C39.9102 15.0544 40.1445 16.3044 40.1445 17.5544C40.1445 18.0232 40.0664 18.4138 40.0664 18.8044H41.3945C44.832 18.8044 47.6445 21.6169 47.6445 25.0544C47.6445 28.5701 44.832 31.3044 41.3945 31.3044H28.8945C25.3789 31.3044 22.6445 28.5701 22.6445 25.0544C22.6445 21.6169 25.3789 18.8044 28.8945 18.8044H29.9102C29.9883 18.4138 30.1445 18.0232 30.1445 17.5544C30.1445 15.8357 28.9727 14.4294 27.332 13.9607C21.8633 14.7419 17.6445 19.4294 17.6445 25.0544C17.6445 31.3044 22.6445 36.3044 28.8945 36.3044H41.3945C47.5664 36.3044 52.6445 31.3044 52.6445 25.0544C52.6445 18.8826 47.5664 13.8044 41.3945 13.8044Z" fill="currentColor"/></svg>'+""+event.first_event_id+"</span>";
            }
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+
                '</p>')
        }

        if(event.customer && event.customer != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("customer_name")){
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Kunde:</b> <span>'+event.customer+'</span>'+
                '</p>')
        }
        if(event.start  && event.start != ""){
            let printDate2 = "";



            if(type_of_form == "2"){
                printDate2 = moment(event.start).format("MMMM DD,YYYY, HH:mm")+" - "+moment(event.end).format("MMMM DD,YYYY, HH:mm");
            }else{
                 printDate2 = moment(event.start).format("dddd")+" "+moment(event.start).format("HH:mm")+" - "+moment(event.end).format("HH:mm");

            }
            right_info_data.append('<p class="tooltip-custom time_div">'+
                    '<b>Tid:</b> <span>'+printDate2+'</span>'+
                '</p>')
        }
       
        if(event.comment && event.comment != ""){
            try {
                let commentData = JSON.parse(event.comment);
                if(commentData.message && commentData.message != ""){
                    jQuery(".custom_info_data").append('<p class="tooltip-custom customer_div">'+
                            '<b>Kommentar:</b> <span>'+ commentData.message +'</span>'+
                        '</p>')
                }
            } catch (e) {
                return false;
            }
            
        }
        if(event.description && event.description != ""){
            jQuery(".custom_info_data").append('<p class="tooltip-custom customer_div">'+
                    '<b>Notat:</b> <span>'+ event.description +'</span>'+
                '</p>')
        }

       
        eventStart = event.start;
        eventRecurrence = getEventRecurrence(event);

        

        
        if(event.comment && event.comment != ""){
            let commentData = JSON.parse(event.comment);
            if(commentData.message && commentData.message != ""){
                left_info_data.append('<p class="tooltip-custom customer_div">'+
                        '<b>Kommentar:</b> <span>'+ commentData.message +'</span>'+
                    '</p>')
            }
        }
        if(event.description && event.description != ""){
            left_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Notat:</b> <span>'+ event.description +'</span>'+
                '</p>')
        }


        tooltip.setOptions({ anchor: target });

        tooltip.open();

        // Bind edit event
        $('.view-more').on('click', function () {
            createEditPopup(event, target)

            tooltip.close();
        });

        $('.tooltip-close').on('click', function () {
            tooltip.close();
        });

        $('.tooltip-delete').on('click', function () {

            let deleteConfirm = mobiscroll.confirm({
                title: 'Delete event?',
                message: 'Are you sure you want to delete this event?',
                okText: 'Yes',
                cancelText: 'Cancel',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        eventStart = event.start;
                        eventEnd = event.end;

                        originalRecurringEvent = event.recurring ? event.original : null;
                        eventOccurrence = event;

                        if (editedEvent.recurring) {
                            createRecurrenceEditPopup(true);
                        } else {
                            calendar.removeEvent(editedEvent);
                            addEditPopup.close();

                            delete_booking(editedEvent);
                        }

                        tooltip.close()

                    }
                }
            });

        });
    }

    function createRecurrenceEditPopup(isDelete) {
        $recurrenceEditModeText.text(isDelete ? 'Delete' : 'Edit');
        recurrenceDelete = isDelete;
        recurrenceEditModePopup.open();
    }

    function showLoader() {
        $('#loader').html('<div class="lds-ring"><div></div><div></div><div></div><div></div></div>').show();
    }

    function hideLoader() {
        $('#loader').hide();
    }

    function showToast(message) {
        $('#toast-container').find('.message-text').text(message);

        $('#toast-container').fadeIn(1000)

        // Hide after 5 secs
        setTimeout(function () {
            $('#toast-container').fadeOut(1000)
        }, 5500);
    }
    function showToastMessage(message,color) {
        mobiscroll.toast({
            message: message,
            color: color,
            duration : 6000,
            display : "bottom"
        });
    }

    var formatDate = mobiscroll.util.datetime.formatDate;
    var startDate, endDate;

    var MAX_MONTH_DAYS = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var DAY_NAMES = ['Søndag','Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag' ];
    var DAY_NAMES_SHORT = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
    var DAY_NAMES_MAP = { 'SU': 0, 'MO': 1, 'TU': 2, 'WE': 3, 'TH': 4, 'FR': 5, 'SA': 6  };
    var MONTH_NAMES = [
        { value: 1, text: 'January' },
        { value: 2, text: 'February' },
        { value: 3, text: 'March' },
        { value: 4, text: 'April' },
        { value: 5, text: 'May' },
        { value: 6, text: 'June' },
        { value: 7, text: 'July' },
        { value: 8, text: 'August' },
        { value: 9, text: 'September' },
        { value: 10, text: 'October' },
        { value: 11, text: 'November' },
        { value: 12, text: 'December' },
    ];

    var originalRecurringEvent;
    var eventOccurrence;
    var eventRecurrenceRule;
    var newEvent;
    var editedEvent;

    // Settings
    var calendarEventList = false;
    var translations = WPMCalendarV2Obj.translations;
    var templateSelected = WPMCalendarV2Obj.template_selected !== '' ? WPMCalendarV2Obj.template_selected : "";
    var calendarStartDay = WPMCalendarV2Obj.cal_start_day !== '' ? WPMCalendarV2Obj.cal_start_day : 1;
    var calendarEndDay = WPMCalendarV2Obj.cal_end_day !== '' ? WPMCalendarV2Obj.cal_end_day : 0;
    var calendarStartTime = WPMCalendarV2Obj.cal_starttime !== '' ? WPMCalendarV2Obj.cal_starttime : '09:00';
    var calendarEndTime = WPMCalendarV2Obj.cal_endtime !== '' ? WPMCalendarV2Obj.cal_endtime : '17:00';
    var calendarTimeCellStep = WPMCalendarV2Obj.cal_time_cell_step !== '' ? WPMCalendarV2Obj.cal_time_cell_step : 60;
    var calendarTimeLabelStep = WPMCalendarV2Obj.cal_time_label_step !== '' ? WPMCalendarV2Obj.cal_time_label_step : 60;
    var calendarWeekNumbers = WPMCalendarV2Obj.cal_show_week_nos !== '' ? WPMCalendarV2Obj.cal_show_week_nos : false;

    var calendarAdditionalInfo = WPMCalendarV2Obj.additional_info !== '' ? WPMCalendarV2Obj.additional_info : "";
    var calendarShowAdminIcons = WPMCalendarV2Obj.show_admin_icons !== '' ? WPMCalendarV2Obj.show_admin_icons : "";



    var showRejected = WPMCalendarV2Obj.show_rejected !== '' ? WPMCalendarV2Obj.show_rejected : "yes";

    if(!Array.isArray(calendarAdditionalInfo)){
        calendarAdditionalInfo = [];
    }
    if(!Array.isArray(calendarShowAdminIcons)){
        calendarShowAdminIcons = [];
    }

    let seasons_data = WPMCalendarV2Obj.seasons_data !== '' ? WPMCalendarV2Obj.seasons_data : [];
    let selected_season = WPMCalendarV2Obj.selected_season !== '' ? WPMCalendarV2Obj.selected_season : "";

    let type_of_form = WPMCalendarV2Obj.type_of_form !== '' ? WPMCalendarV2Obj.type_of_form : "1";
    let season_view = WPMCalendarV2Obj.season_view;
    let season_start = WPMCalendarV2Obj.season_start !== '' ? WPMCalendarV2Obj.season_start : "";
    let season_end = WPMCalendarV2Obj.season_end !== '' ? WPMCalendarV2Obj.season_end : "";

    var update_season_template_auto = WPMCalendarV2Obj.update_season_template_auto !== '' ? WPMCalendarV2Obj.update_season_template_auto : "";

    //type_of_form = "2";

    let week_day_start = "";

    var eventId;
    var eventClient;
    var eventClientName;
    var eventListing;
    var eventTeam;
    var eventTitle;
    var eventDescription;
    var eventAllDay;
    var eventStart;
    var eventEnd;
    var eventColor;
    var eventRecurrence;
    var eventRecurringException = [];
    var eventResource;
    var eventStatus;

    var recurrenceRepeat;
    var recurrenceInterval;
    var recurrenceCondition;
    var recurrenceMonth;
    var recurrenceDay;
    var recurrenceWeekDays;
    var recurrenceCount;
    var recurrenceUntil;
    var recurrenceDelete;
    var recurrenceEditMode = 'current';


    var $eventTitle = $('#event-title');
    var $eventClient = $('#wpm-client');
    var $eventListing = $('#wpm-listing');
    var $eventTeam = $('#wpm-team');
    var $eventStatus = $('#status_manuale');
    var $eventDescription = $('.popup-event-description');
    var $eventAllDay = $('#popup-event-all-day');
    var $eventDeleteButton = $('#popup-event-delete');
    var $eventRecurrence = $('.popup-event-recurrence');
    var $eventRecurrenceEditor = $('.popup-event-recurrence-editor');

    var $recurrenceInterval = $('.recurrence-interval');
    var $recurrenceCount = $('.recurrence-count');
    var $recurrenceEditModeText = $('#recurrence-edit-mode-text');
    var $recurrenceWeekDays = $('.md-recurrence-weekdays');
    var $tooltip = $('#season-calendar-event-tooltip-popup');

    // Init events
    var section_resources = [];

    var gym_resources = WPMCalendarV2Obj.gym_resources;

    if (gym_resources) {
        section_resources = gym_resources.listings;
        workingHours = gym_resources.workingHours;
        //Replacing Values of object of both arrays
        section_resources.forEach(function (item, index) {
            section_resources[index]['value'] = Number(item.value);
        });
    }

    var filter_locations = WPMCalendarV2Obj.filter_location;
    var calendar_view = WPMCalendarV2Obj.calendar_view;
    var cal_type = WPMCalendarV2Obj.cal_type;
    var cal_view = WPMCalendarV2Obj.cal_view;
    var calendar_view_val;

    if (!calendar_view || calendar_view == "" || calendar_view == 0) {
        calendar_view_val = 'timeline_week';
    } else {
        if (Array.isArray(calendar_view) && calendar_view.length > 0) {
            calendar_view_val = calendar_view[0];
        } else if(calendar_view != "") {
            calendar_view_val = calendar_view;
        }else{
             calendar_view_val = 'timeline_week';
        }
    }
    calendar_view_val = calendar_view_val.trim()

    if(type_of_form == "1"){
        calendar_view_val = 'timeline_week';
    }

    resources = prepareFullCalendar();
    var businessHours = [];
    if (resources && resources.length > 0 && typeof (resources[0]['businessHours']) != 'undefined' && (resources[0]['businessHours']) != 'null' && (resources[0]['businessHours']) != '') {
        businessHours = resources[0]['businessHours'];
    }

    var section_resources_value = resources;
    var colors = [];
    var __data = resources;
    for (let i = 0; i < __data.length; ++i) {
       // console.log('__data' + JSON.stringify(__data[i]));
        for (let b = 0; b < __data[i]['businessHours'].length; ++b) {
            if (__data[i]['businessHours'][b].startTime != '00:00' && __data[i]['businessHours'][b].startTime != '00:00:00' && __data[i]['businessHours'][b].endTime != '23:59:59' && __data[i]['businessHours'][b].endTime != '24:00') {


                var color = { 'start': '00:00', 'end': __data[i]['businessHours'][b].startTime, 'background': '#DADADA', 'resource': __data[i].id, 'recurring': { 'repeat': 'weekly', 'weekDays': __data[i]['businessHours'][b].weekday } };
                colors.push(color);


                var color = { 'start': __data[i]['businessHours'][b].endTime, 'end': '24:00', 'background': '#DADADA', 'resource': __data[i].id, 'recurring': { 'repeat': 'weekly', 'weekDays': __data[i]['businessHours'][b].weekday } };
                colors.push(color);
            }
        }

    }

    if (typeof (filter_location) != 'undefined' && filter_location != null && filter_location != '') {
        var __data = section_resources;
        var selected_values = filter_location.toString();
        var section_resources_value = [];
        for (let i = 0; i < __data.length; ++i) {
            if (selected_values.indexOf(__data[i].value) >= 0) {
                section_resources_value.push(__data[i]);
                businessHours = resources[i]['businessHours'];

            }
        }
    }

    var schedulerTasks = [];
    newTasks = schedulerTasks;
    newResources = [];
  //  newResources = section_resources;
    var section_resources_value = section_resources;
    if (typeof (filter_location) != 'undefined' && filter_location != null && filter_location != '') {
        var __data = section_resources;
        var selected_values = filter_location.toString();
        var newResources = [];
        for (let i = 0; i < __data.length; ++i) {
            if (selected_values.indexOf(__data[i].value) >= 0) {
                newResources.push(__data[i]);
            }
        }

    }

    var current_language = WPMCalendarV2Obj.current_language;
    var mobi_locale = 'en';

    if (current_language == "nb-NO") {
        mobi_locale = 'no';
    } else {
        mobi_locale = current_language.split('-')[0];
    }

    // Event summary tooltup
    var $week_day_start = $('#week_day_start').mobiscroll('getInst');
    $week_day_start.change(function(){
        week_day_start = this.value;
    });



    var tooltip = $tooltip.mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: false,
        width: 600
    }).mobiscroll('getInst');

    mobiscroll.setOptions({
        locale: mobiscroll.locale[mobi_locale],                     // Specify language like: locale: mobiscroll.localePl or omit setting to use default
        theme: 'gibbs-material',                                    // Specify theme like: theme: 'ios' or omit setting to use default
        themeVariant: 'light'                        // More info about themeVariant: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-themeVariant
    });

    var filterListingSelect = $('#filter-listing-input').mobiscroll().select({
        data: section_resources,
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        
        filter: true,
        selectMultiple: true,
        tags: true,
        onChange: function (args) {
            // Filter resources
            filteredListings = section_resources.filter(function (resource, index) {
                return args.value.includes(resource.value)
            })
        }
    }).mobiscroll('getInst');

    let resources_data = get_resources();

    let date_min = new Date();

    date_min.setFullYear(date_min.getFullYear() - 10);

    let date_max = new Date();
    date_max.setFullYear(date_max.getFullYear() + 10);

    let date_start = new Date("2021/02/01 00:00:01");

    if(type_of_form == "2"){

        if(season_start != ""){
            date_start = new Date(season_start+" 00:00:00");
            date_min = new Date(season_start+" 00:00:00");

            if(season_end != ""){
                date_max = new Date(season_end+" 23:59:00");
            }
        }
       

    }
    
    // Init the event calendar
    window.calendar = calendar = $('#scheduler').mobiscroll().eventcalendar({
        dateFormat: 'YYYY-MM-DD',
        selectedDate: date_start,
        refDate: date_start,
        min: date_min,
        max: date_max,
        modules: [mobiscroll.print],
        theme: 'gibbs-material',
        clickToCreate: 'double',                     // More info about clickToCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-clickToCreate
        dragToCreate: true,                          // More info about dragToCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToCreate
        dragToMove: (season_view == "forespurte" || season_view == "algoritme") ? false : true,                            // More info about dragToMove: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToMove
        dragToResize: (season_view == "forespurte" || season_view == "algoritme") ? false : true,
        showEventTooltip: false,                       // More info about dragToResize: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToResize
        data: [],                              // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
        // colors: calendar_view_val === 'schedule_month' || calendar_view_val === 'schedule_year' ? [] : colors,
        resources: resources_data,
        onEventClick: function (args) {              // More info about onEventClick: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventClick
            editedEvent = args.event

            originalRecurringEvent = args.event;

            let args_event = args.event;
            let currentTargett = args.domEvent.currentTarget;

            if (editedEvent.title !== "New event") {

                setTimeout(function(){
                   // showEventSummary(args_event, currentTargett);
                },10)

            }    

        },
        onEventDoubleClick: function (args) {
            if(season_view == "manuelle"){
               createEditPopup(args.event, args.domEvent.currentTarget);
            }else{
                return false;
            }   
        },
        onEventUpdate: function (args) { // More info about onEventUpdate: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventUpdate
            if (args.newEvent) {
                fillPopup(args.newEvent);
            }

            var event = args.event;

            if (event.recurring) {
                originalRecurringEvent = args.oldEvent;
                eventOccurrence = args.oldEventOccurrence;
                eventResource = args.newEvent.resource;

                if (args.isDelete) {
                    eventRecurringException = originalRecurringEvent.recurringException || [];
                    eventStart = eventOccurrence.start;
                    createRecurrenceEditPopup(true);
                } else {
                    createRecurrenceEditPopup(false);
                }
                return false;
            }
        },
        onEventDragEnd: function (args, inst) {
            var event = args.event

            if (event.title !== "New event" && !event.recurring) {
                event.wpm_client = event.client ? event.client.value : '';
                event.sectionResourcesId = event.resource;
                event.gymSectionId = event.resource;
                event.resourceId = event.resource;

                update_booking(event)
            }
        },
        onEventCreate: function (args) {             // More info about onEventCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventCreate
            return false;
        },
        onEventCreated: function (args) {            // More info about onEventCreated: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventCreated
            return false;
        },
        onPageLoaded: function (args, inst) {
            var end = args.lastDay;
            startDate = args.firstDay;
            endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate() - 1, 0);
            setTimeout(function(){
                $("#selected-day").html($('.cal-header-nav > button').html())
            },200)

            // set button text
            // $rangeButton.text(getFormattedRange(startDate, endDate));
            // // set range value
            // reportRangePicker.setVal([startDate, endDate]);
        },
        renderScheduleEvent: function (data) {
            if (data.allDay) {
                return '<div style="background:#88D6FD;color: #fff;" class="md-custom-event-allday-title">' + data.title + '</div>';
            } else {
                var icons = '';
                var event = data.original;

                var date_icon = false;
                var time_icon = false;
                var listing_icon = false;

                if(event.org_data){
                    if(event.org_data.date_start){
                        var org_date_start = moment(event.org_data.date_start).format("YYYY-MM-DD");
                        var current_date_start = moment(event.start).format("YYYY-MM-DD");
                        if(org_date_start != current_date_start && calendarShowAdminIcons.includes("date")){
                            date_icon = true;
                        }

                        var org_date_time = moment(event.org_data.date_start).format("HH:mm");
                        var current_date_time = moment(event.start).format("HH:mm");
                        if(org_date_time != current_date_time && calendarShowAdminIcons.includes("time")){
                            time_icon = true;
                        }


                    }
                    if(event.org_data.listing_id){
                        if(event.org_data.listing_id != event.sectionResourcesId && calendarShowAdminIcons.includes("listing")){
                            listing_icon = true;
                        }
                    }
                }

                if (date_icon) {
                    icons += '<span  data-content="Endret dato" class="tooltip_info"><i class="fas fa-calendar" aria-hidden="true"></i></span>';
                }
                if (time_icon) {
                    icons += '<span  data-content="Endret tid" class="tooltip_info"><i class="fas fa-clock"></i></span>';
                }
                if (listing_icon) {
                    icons += '<span  data-content="Endret lokasjon" class="tooltip_info"><i class="fas fa-location-dot"></i></span>';
                }




                if (event.recurring && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("repeated")) {
                    icons += '<span  data-content="Repeterende hendelse" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M37.6357 3.99194C38.7295 3.99194 39.8232 4.77319 39.8232 6.10132V17.5076C39.8232 18.2888 39.1201 18.9919 38.2607 18.9919H26.9326C25.6045 18.9919 24.8232 17.8982 24.8232 16.8826C24.8232 16.3357 24.9795 15.7888 25.4482 15.3982L28.9639 11.8044C26.7764 10.0076 23.9639 8.99194 20.9951 8.99194C14.1201 8.99194 8.57324 14.6169 8.57324 21.4919C8.57324 28.3669 14.1201 33.9138 20.9951 33.9138C26.7764 33.9138 28.0264 30.9451 30.0576 30.9451C31.4639 30.9451 32.4795 32.1169 32.4795 33.4451C32.4795 36.1794 26.1514 38.9138 20.9951 38.9138C11.3857 38.9138 3.57324 31.1013 3.57324 21.4919C3.57324 11.8044 11.3857 3.99194 21.0732 3.99194C25.292 3.99194 29.3545 5.55444 32.4795 8.28882L36.2295 4.61694C36.6201 4.14819 37.167 3.99194 37.6357 3.99194Z" fill="currentColor"/></svg></span>';
                    //      } else if (event.recurrenceId && !event.recurrenceId.toString().startsWith('mbsc_') && event.recurrenceId !== "0") {
                } /* else if (event.recurrenceId && typeof (event.rrule) === "undefined" && event.recurrenceId !== "0" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("not_repeated")) {

                    icons += '<span data-content="Avkoblet repeterende hendelse" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M40.3945 5.4021V16.6521C40.3945 18.0583 39.2227 19.1521 37.8945 19.1521H26.6445C25.2383 19.1521 24.1445 18.0583 24.1445 16.6521C24.1445 15.324 25.2383 14.1521 26.6445 14.1521H31.5664C29.2227 11.1052 25.5508 9.23022 21.5664 9.23022C14.6914 9.23022 9.14453 14.7771 9.14453 21.6521C9.14453 28.6052 14.6914 34.1521 21.5664 34.1521C24.3008 34.1521 26.8789 33.2927 29.0664 31.6521C30.1602 30.8708 31.7227 31.1052 32.582 32.199C33.4414 33.2927 33.207 34.8552 32.1133 35.7146C29.0664 37.9802 25.3945 39.1521 21.5664 39.1521C11.957 39.1521 4.14453 31.3396 4.14453 21.6521C4.14453 12.0427 11.957 4.23022 21.5664 4.23022C27.0352 4.23022 32.0352 6.73022 35.3945 10.949V5.4021C35.3945 4.07397 36.4883 2.9021 37.8945 2.9021C39.2227 2.9021 40.3945 4.07397 40.3945 5.4021Z" fill="currentColor"/><rect x="6.75098" y="3.16968" width="2.95873" height="46.1925" transform="rotate(-36.3892 6.75098 3.16968)" fill="currentColor"/></svg></span>';
                } */
                let isShowLinkedIcon = true;

                if(Array.isArray(event.listing) && event.listing.length > 0 && event.listing.length == 1){
                     isShowLinkedIcon = false;
                }
                if (event.first_event_id && event.first_event_id != event.id && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("linked") && isShowLinkedIcon) {
                    icons += '<span data-content="Sammenkoblet bookinger" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 55 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M15.1445 23.8044H13.8945C10.3789 23.8044 7.64453 21.0701 7.64453 17.5544C7.64453 14.1169 10.3789 11.3044 13.8945 11.3044H26.3945C29.832 11.3044 32.6445 14.1169 32.6445 17.5544C32.6445 21.0701 29.832 23.8044 26.3945 23.8044H25.3008C25.2227 24.1951 25.1445 24.6638 25.1445 25.0544C25.1445 26.8513 26.2383 28.2576 27.8789 28.7263C33.3477 27.9451 37.6445 23.2576 37.6445 17.5544C37.6445 11.3826 32.5664 6.30444 26.3945 6.30444H13.8945C7.64453 6.30444 2.64453 11.3826 2.64453 17.5544C2.64453 23.8044 7.64453 28.8044 13.8945 28.8044H15.6133C15.3008 27.6326 15.1445 26.3826 15.1445 25.0544C15.1445 24.6638 15.1445 24.2732 15.1445 23.8044ZM41.3945 13.8044H39.5977C39.9102 15.0544 40.1445 16.3044 40.1445 17.5544C40.1445 18.0232 40.0664 18.4138 40.0664 18.8044H41.3945C44.832 18.8044 47.6445 21.6169 47.6445 25.0544C47.6445 28.5701 44.832 31.3044 41.3945 31.3044H28.8945C25.3789 31.3044 22.6445 28.5701 22.6445 25.0544C22.6445 21.6169 25.3789 18.8044 28.8945 18.8044H29.9102C29.9883 18.4138 30.1445 18.0232 30.1445 17.5544C30.1445 15.8357 28.9727 14.4294 27.332 13.9607C21.8633 14.7419 17.6445 19.4294 17.6445 25.0544C17.6445 31.3044 22.6445 36.3044 28.8945 36.3044H41.3945C47.5664 36.3044 52.6445 31.3044 52.6445 25.0544C52.6445 18.8826 47.5664 13.8044 41.3945 13.8044Z" fill="currentColor"/></svg></span>';
                }
                if (event.unlink_first_event_id && event.unlink_first_event_id != "" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("not_linked") && isShowLinkedIcon) {
                    icons += '<span data-content="Avkoblet sammenkoblet hendelse" class="tooltip_info"><i class="fa fa-unlink"></i></span>';
                }
                if (event.description && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("notes")) {
                    icons += '<span data-content="Notat" class="tooltip_info"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H288V368c0-26.5 21.5-48 48-48H448V96c0-35.3-28.7-64-64-64H64zM448 352H402.7 336c-8.8 0-16 7.2-16 16v66.7V480l32-32 64-64 32-32z"/></svg></span>';

                    /*icons += '<span title"Notes"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21.0732 4.45679C9.97949 4.45679 1.07324 11.8005 1.07324 20.7068C1.07324 24.613 2.71387 28.1287 5.44824 30.9412C4.51074 34.9255 1.22949 38.363 1.22949 38.4412C0.995117 38.5974 0.995117 38.9099 1.07324 39.1443C1.15137 39.3787 1.38574 39.4568 1.69824 39.4568C6.85449 39.4568 10.6826 37.0349 12.6357 35.4724C15.2139 36.4099 18.0264 36.9568 21.0732 36.9568C32.0889 36.9568 40.9951 29.6912 40.9951 20.7068C40.9951 11.8005 32.0889 4.45679 21.0732 4.45679Z" fill="currentColor"/></svg></span>';*/
                }
                if(event.comment && event.comment != "" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("comment")){
                    try {
                        let commentData = JSON.parse(event.comment);
                        if(commentData.message && commentData.message != ""){
                            icons += '<span data-content="Kommentar" class="tooltip_info" ><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21.0732 4.45679C9.97949 4.45679 1.07324 11.8005 1.07324 20.7068C1.07324 24.613 2.71387 28.1287 5.44824 30.9412C4.51074 34.9255 1.22949 38.363 1.22949 38.4412C0.995117 38.5974 0.995117 38.9099 1.07324 39.1443C1.15137 39.3787 1.38574 39.4568 1.69824 39.4568C6.85449 39.4568 10.6826 37.0349 12.6357 35.4724C15.2139 36.4099 18.0264 36.9568 21.0732 36.9568C32.0889 36.9568 40.9951 29.6912 40.9951 20.7068C40.9951 11.8005 32.0889 4.45679 21.0732 4.45679Z" fill="currentColor"/></svg></span>';
                        }
                    } catch (e) {
                        return false;
                    }
                    
                }
                if(event.custom_fields && event.custom_fields != "" && event.custom_fields == true && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("custom_field")){
                    try {
                        icons += '<span  data-content="Har annen informasjon" class="tooltip_info"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M413.5 237.5c-28.2 4.8-58.2-3.6-80-25.4l-38.1-38.1C280.4 159 272 138.8 272 117.6V105.5L192.3 62c-5.3-2.9-8.6-8.6-8.3-14.7s3.9-11.5 9.5-14l47.2-21C259.1 4.2 279 0 299.2 0h18.1c36.7 0 72 14 98.7 39.1l44.6 42c24.2 22.8 33.2 55.7 26.6 86L503 183l8-8c9.4-9.4 24.6-9.4 33.9 0l24 24c9.4 9.4 9.4 24.6 0 33.9l-88 88c-9.4 9.4-24.6 9.4-33.9 0l-24-24c-9.4-9.4-9.4-24.6 0-33.9l8-8-17.5-17.5zM27.4 377.1L260.9 182.6c3.5 4.9 7.5 9.6 11.8 14l38.1 38.1c6 6 12.4 11.2 19.2 15.7L134.9 484.6c-14.5 17.4-36 27.4-58.6 27.4C34.1 512 0 477.8 0 435.7c0-22.6 10.1-44.1 27.4-58.6z"/></svg></span>';
                    } catch (e) {
                        return false;
                    }
                    
                }

                var eventClass = '';

                if (event.status) {
                    eventClass = event.status ? 'calendar-status-' + event.status : 'calendar-status-Lukket';
                } else {
                    eventClass = 'calendar-status-new';
                }

                let title_div_data = [];

                if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("event_title") && event.title != '' && event.title != null){
                    title_div_data.push(event.title);
                }
                if(event.customer && event.customer != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("customer_name")){
                     title_div_data.push(event.customer);
                }
                if(event.extra_info){
                    if(event.extra_info.age_group && event.extra_info.age_group != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("age_group")){
                       title_div_data.push(event.extra_info.age_group);
                    }
                    if(event.extra_info.sport && event.extra_info.sport != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("sport")){
                        title_div_data.push(event.extra_info.sport);
                    }
                    if(event.extra_info.members && event.extra_info.members != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("members")){
                        title_div_data.push(event.extra_info.members);
                    }
                    if(event.extra_info.type && event.extra_info.type != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("type")){
                        title_div_data.push(event.extra_info.type);
                    }
                    if(event.extra_info.team_level && event.extra_info.team_level != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("level")){
                        title_div_data.push(event.extra_info.team_level);
                    }
                    if(event.extra_info.team_name && event.extra_info.team_name != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("team_name")){
                        title_div_data.push(event.extra_info.team_name);
                    }
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }

                title_div_data = title_div_data.join(", ");

               /*  if(title_div_data.length > 20){

                    title_div_data = title_div_data.substring(0, 20) + " ...";
                } */
                const maxLength = 28; // Maximum characters before truncating

                let all_title_div_data = title_div_data;

    
                if (title_div_data.length > maxLength) {
                    const truncatedText = title_div_data.substring(0, maxLength);
                    const remainingText = title_div_data.substring(maxLength);
                    
                    title_div_data = truncatedText /* + `<span class="read-more-text">... <u>Read More</u></span>` */;
                }
                all_title_div_data = all_title_div_data+" "+ data.start + ' - ' + data.end


                return '<div class="md-custom-event-cont ' + eventClass + '" style="color: #fff;">' +
                    '<div class="md-custom-event-wrapper settings-info" data-content="'+all_title_div_data+'">' +
                    '<div class="md-custom-event-details">' +
                    '<div class="md-custom-event-title">' + title_div_data + '<span style="margin-right: auto">' + icons + '</span>' + '</div>' +
                    '<div class="md-custom-event-time">' + data.start + ' - ' + data.end + '</div>' +
                    '</div></div></div>';
            };
        },
        renderEvent: function (data) {
            if (data.allDay) {
                return '<div style="background:#88D6FD;color: #fff;" class="md-custom-event-allday-title">' + data.title + '</div>';
            } else {
                var icons = '';
                var event = data.original;





                if (event.recurring && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("repeated")) {
                    icons += '<span  data-content="Repeterende hendelse" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M37.6357 3.99194C38.7295 3.99194 39.8232 4.77319 39.8232 6.10132V17.5076C39.8232 18.2888 39.1201 18.9919 38.2607 18.9919H26.9326C25.6045 18.9919 24.8232 17.8982 24.8232 16.8826C24.8232 16.3357 24.9795 15.7888 25.4482 15.3982L28.9639 11.8044C26.7764 10.0076 23.9639 8.99194 20.9951 8.99194C14.1201 8.99194 8.57324 14.6169 8.57324 21.4919C8.57324 28.3669 14.1201 33.9138 20.9951 33.9138C26.7764 33.9138 28.0264 30.9451 30.0576 30.9451C31.4639 30.9451 32.4795 32.1169 32.4795 33.4451C32.4795 36.1794 26.1514 38.9138 20.9951 38.9138C11.3857 38.9138 3.57324 31.1013 3.57324 21.4919C3.57324 11.8044 11.3857 3.99194 21.0732 3.99194C25.292 3.99194 29.3545 5.55444 32.4795 8.28882L36.2295 4.61694C36.6201 4.14819 37.167 3.99194 37.6357 3.99194Z" fill="currentColor"/></svg></span>';
                    //      } else if (event.recurrenceId && !event.recurrenceId.toString().startsWith('mbsc_') && event.recurrenceId !== "0") {
                } /* else if (event.recurrenceId && typeof (event.rrule) === "undefined" && event.recurrenceId !== "0" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("not_repeated")) {


                    icons += '<span data-content="Avkoblet repeterende hendelse" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M40.3945 5.4021V16.6521C40.3945 18.0583 39.2227 19.1521 37.8945 19.1521H26.6445C25.2383 19.1521 24.1445 18.0583 24.1445 16.6521C24.1445 15.324 25.2383 14.1521 26.6445 14.1521H31.5664C29.2227 11.1052 25.5508 9.23022 21.5664 9.23022C14.6914 9.23022 9.14453 14.7771 9.14453 21.6521C9.14453 28.6052 14.6914 34.1521 21.5664 34.1521C24.3008 34.1521 26.8789 33.2927 29.0664 31.6521C30.1602 30.8708 31.7227 31.1052 32.582 32.199C33.4414 33.2927 33.207 34.8552 32.1133 35.7146C29.0664 37.9802 25.3945 39.1521 21.5664 39.1521C11.957 39.1521 4.14453 31.3396 4.14453 21.6521C4.14453 12.0427 11.957 4.23022 21.5664 4.23022C27.0352 4.23022 32.0352 6.73022 35.3945 10.949V5.4021C35.3945 4.07397 36.4883 2.9021 37.8945 2.9021C39.2227 2.9021 40.3945 4.07397 40.3945 5.4021Z" fill="currentColor"/><rect x="6.75098" y="3.16968" width="2.95873" height="46.1925" transform="rotate(-36.3892 6.75098 3.16968)" fill="currentColor"/></svg></span>';
                } */
                let isShowLinkedIcon = true;

                if(Array.isArray(event.listing) && event.listing.length > 0 && event.listing.length == 1){
                     isShowLinkedIcon = false;
                }
                if (event.first_event_id && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("linked") && isShowLinkedIcon) {
                    icons += '<span data-content="Sammenkoblet bookinger" class="tooltip_info"><svg width="1em" height="1em" viewBox="0 0 55 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M15.1445 23.8044H13.8945C10.3789 23.8044 7.64453 21.0701 7.64453 17.5544C7.64453 14.1169 10.3789 11.3044 13.8945 11.3044H26.3945C29.832 11.3044 32.6445 14.1169 32.6445 17.5544C32.6445 21.0701 29.832 23.8044 26.3945 23.8044H25.3008C25.2227 24.1951 25.1445 24.6638 25.1445 25.0544C25.1445 26.8513 26.2383 28.2576 27.8789 28.7263C33.3477 27.9451 37.6445 23.2576 37.6445 17.5544C37.6445 11.3826 32.5664 6.30444 26.3945 6.30444H13.8945C7.64453 6.30444 2.64453 11.3826 2.64453 17.5544C2.64453 23.8044 7.64453 28.8044 13.8945 28.8044H15.6133C15.3008 27.6326 15.1445 26.3826 15.1445 25.0544C15.1445 24.6638 15.1445 24.2732 15.1445 23.8044ZM41.3945 13.8044H39.5977C39.9102 15.0544 40.1445 16.3044 40.1445 17.5544C40.1445 18.0232 40.0664 18.4138 40.0664 18.8044H41.3945C44.832 18.8044 47.6445 21.6169 47.6445 25.0544C47.6445 28.5701 44.832 31.3044 41.3945 31.3044H28.8945C25.3789 31.3044 22.6445 28.5701 22.6445 25.0544C22.6445 21.6169 25.3789 18.8044 28.8945 18.8044H29.9102C29.9883 18.4138 30.1445 18.0232 30.1445 17.5544C30.1445 15.8357 28.9727 14.4294 27.332 13.9607C21.8633 14.7419 17.6445 19.4294 17.6445 25.0544C17.6445 31.3044 22.6445 36.3044 28.8945 36.3044H41.3945C47.5664 36.3044 52.6445 31.3044 52.6445 25.0544C52.6445 18.8826 47.5664 13.8044 41.3945 13.8044Z" fill="currentColor"/></svg></span>';
                }
                if (event.unlink_first_event_id && event.unlink_first_event_id != "" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("not_linked") && isShowLinkedIcon) {
                    icons += '<span data-content="Unlink" class="tooltip_info"><i class="fa fa-unlink"></i></span>';
                }
                if (event.description && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("notes")) {
                    icons += '<span data-content="Notat" class="tooltip_info"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H288V368c0-26.5 21.5-48 48-48H448V96c0-35.3-28.7-64-64-64H64zM448 352H402.7 336c-8.8 0-16 7.2-16 16v66.7V480l32-32 64-64 32-32z"/></svg></span>';

                    /*icons += '<span title"Notes"><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21.0732 4.45679C9.97949 4.45679 1.07324 11.8005 1.07324 20.7068C1.07324 24.613 2.71387 28.1287 5.44824 30.9412C4.51074 34.9255 1.22949 38.363 1.22949 38.4412C0.995117 38.5974 0.995117 38.9099 1.07324 39.1443C1.15137 39.3787 1.38574 39.4568 1.69824 39.4568C6.85449 39.4568 10.6826 37.0349 12.6357 35.4724C15.2139 36.4099 18.0264 36.9568 21.0732 36.9568C32.0889 36.9568 40.9951 29.6912 40.9951 20.7068C40.9951 11.8005 32.0889 4.45679 21.0732 4.45679Z" fill="currentColor"/></svg></span>';*/
                }
                if(event.comment && event.comment != "" && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("comment")){
                    try {
                        let commentData = JSON.parse(event.comment);
                        if(commentData.message && commentData.message != ""){
                            icons += '<span data-content="Kommentar" class="tooltip_info" ><svg width="1em" height="1em" viewBox="0 0 43 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21.0732 4.45679C9.97949 4.45679 1.07324 11.8005 1.07324 20.7068C1.07324 24.613 2.71387 28.1287 5.44824 30.9412C4.51074 34.9255 1.22949 38.363 1.22949 38.4412C0.995117 38.5974 0.995117 38.9099 1.07324 39.1443C1.15137 39.3787 1.38574 39.4568 1.69824 39.4568C6.85449 39.4568 10.6826 37.0349 12.6357 35.4724C15.2139 36.4099 18.0264 36.9568 21.0732 36.9568C32.0889 36.9568 40.9951 29.6912 40.9951 20.7068C40.9951 11.8005 32.0889 4.45679 21.0732 4.45679Z" fill="currentColor"/></svg></span>';
                        }
                    } catch (e) {
                       /// return false;
                    }
                    
                }
                if(event.custom_fields && event.custom_fields != "" && event.custom_fields == true && Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.includes("custom_field")){
                    try {
                        icons += '<span  data-content="Annen informasjon" class="tooltip_info"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M413.5 237.5c-28.2 4.8-58.2-3.6-80-25.4l-38.1-38.1C280.4 159 272 138.8 272 117.6V105.5L192.3 62c-5.3-2.9-8.6-8.6-8.3-14.7s3.9-11.5 9.5-14l47.2-21C259.1 4.2 279 0 299.2 0h18.1c36.7 0 72 14 98.7 39.1l44.6 42c24.2 22.8 33.2 55.7 26.6 86L503 183l8-8c9.4-9.4 24.6-9.4 33.9 0l24 24c9.4 9.4 9.4 24.6 0 33.9l-88 88c-9.4 9.4-24.6 9.4-33.9 0l-24-24c-9.4-9.4-9.4-24.6 0-33.9l8-8-17.5-17.5zM27.4 377.1L260.9 182.6c3.5 4.9 7.5 9.6 11.8 14l38.1 38.1c6 6 12.4 11.2 19.2 15.7L134.9 484.6c-14.5 17.4-36 27.4-58.6 27.4C34.1 512 0 477.8 0 435.7c0-22.6 10.1-44.1 27.4-58.6z"/></svg></span>';
                    } catch (e) {
                       // return false;
                    }
                    
                }

               


                var eventClass = '';

                if (event.status) {
                    eventClass = event.status ? 'calendar-status-' + event.status : 'calendar-status-Lukket';
                } else {
                    eventClass = 'calendar-status-new';
                }

                let title_div_data = [];

                if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("event_title")){
                    title_div_data.push(event.title);
                }
                if(event.customer && event.customer != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("customer_name")){
                     title_div_data.push(event.customer);
                }
                if(event.extra_info){
                    if(event.extra_info.age_group && event.extra_info.age_group != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("age_group")){
                       title_div_data.push(event.extra_info.age_group);
                    }
                    if(event.extra_info.sport && event.extra_info.sport != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("sport")){
                        title_div_data.push(event.extra_info.sport);
                    }
                    if(event.extra_info.members && event.extra_info.members != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("members")){
                        title_div_data.push(event.extra_info.members);
                    }
                    if(event.extra_info.type && event.extra_info.type != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("type")){
                        title_div_data.push(event.extra_info.type);
                    }
                    if(event.extra_info.team_level && event.extra_info.team_level != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("level")){
                        title_div_data.push(event.extra_info.team_level);
                    }
                    if(event.extra_info.team_name && event.extra_info.team_name != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("team_name")){
                        title_div_data.push(event.extra_info.team_name);
                    }
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }

                title_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        //debugger;
                        tl_div = tl_div.trim();

                        title_div_data_f.push(tl_div);
                    }

                })

                title_div_data = title_div_data_f.join(", ");

             /*    if(title_div_data.length > 20){

                    title_div_data = title_div_data.substring(0, 20) + " ...";
                } */

                var visibleEvent = "";

                if(data.startDate && data.endDate){
                    let diffTime = Math.abs(data.startDate - data.endDate);
                    let minutess = Math.floor((diffTime/1000)/60);

                    if(minutess < 31){

                        visibleEvent = "style='visibility:hidden'";

                    }
                }

                return '<div class="md-custom-event-cont ' + eventClass + '" style="color: #fff;">' +
                    '<div class="md-custom-event-wrapper">' +
                    '<div class="md-custom-event-details" '+visibleEvent+'>' +
                    '<div class="md-custom-event-title"><span class="ev-title">' + title_div_data + '</span><span style="margin-right: auto">' + icons + '</span>' + '</div>' +
                    '<div class="md-custom-event-time">' + data.start + ' - ' + data.end + '</div>' +
                    '</div></div></div>';
            };
        },
        renderHeader: function () {
            
            var renderHeader = '<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr" style="width:100%;">';

            // Date picker

            let date_div_hide = ""

            if(type_of_form != "2"){
                date_div_hide = "d-none";
            }

            renderHeader += '<div class="date_div '+date_div_hide+'">';

                renderHeader += '<div class="report-range-picker"><button mbsc-button data-variant="flat" id="selected-day" class="mbsc-calendar-button">' +
                    '<span class="mbsc-calendar-title report-range-picker-text">' +
                    '</span></button></div>';
                renderHeader += '<div mbsc-calendar-nav class="cal-header-nav d-none"></div>';

                // Prev next buttons
                renderHeader += '<div mbsc-calendar-prev class="cal-header-prev"></div>' +
                    '<div mbsc-calendar-next class="cal-header-next"></div>';

                // Today
                renderHeader += '<div mbsc-calendar-today></div>';

            renderHeader += '</div>';


            // Refresh button
            renderHeader += '<div class="refresh-calendar"><i class="fa fa-arrows-rotate" aria-hidden="true"></i></div>';

            
            
            

            return renderHeader;
        },
        renderResource: function (resource) {
            return '<div class="md-resource-details-cont">' +
                '<div class="md-resource-header mbsc-timeline-resource-title" data-id="' + resource.id + '" data-content="' + resource.full_text + '" data-sports="' + resource.sports + '">' + resource.name + '</div>' +
                '</div>';
        },
        onSelectedDateChange: function (event, inst) {

            $("#selected-day").html($('.cal-header-nav > button').html())

        }
    }).mobiscroll('getInst');

    get_season_booking_data(calendar,true,true);

    // Update settings on page load 
    updateCalendarSettings(true);




    var eventStatusSelect;
    var eventStartEndPicker;
    var eventRecurrenceSelect;
    var recurrenceDaySelect;
    var recurrenceMonthSelect;
    var recurrenceMonthDaySelect;
    var recurrenceUntilDatepicker;

    let tvListing = "";
    let fieldsInfoTv = "";
    let additionalInfoTv = "";
    let adminIconShowTv = "";


    fields_init();
    function fields_init(popup = 'add') {

        // Attach event handlers
        $('.popup-event-description').on('change', function () {
            eventDescription = this.value;
        });

        eventTeamSelect = $eventTeam.mobiscroll().select({
            touchUi: true,
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {

                eventTeam = args.value;
            }
        }).mobiscroll('getInst');

     


        /*eventClientSelect = $eventClient.mobiscroll("getInst");
        eventClientSelect.change(function(){
           eventClient = this.value;
        });*/

        eventListingSelect = $eventListing.mobiscroll().select({
            touchUi: true,
            filter: true,
            selectMultiple: true,
            tags: true,
            mobiscroll: "Select",
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {
                jQuery(".required_focus").removeClass("required_focus");


                eventListing = args.value;
            }
        }).mobiscroll('getInst');

        tvListingSelect = jQuery("#tv-listing").mobiscroll().select({
            touchUi: true,
            filter: true,
            selectMultiple: true,
            tags: true,
            mobiscroll: "Select",
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            display: "anchored",
            onChange: function (args) {
                tvListing = args.value;
            }
        }).mobiscroll('getInst');
        fieldsInfoTvSelect = jQuery("#fields-info-tv").mobiscroll().select({
            touchUi: true,
            filter: true,
            selectMultiple: true,
            tags: true,
            mobiscroll: "Select",
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            display: "anchored",
            onChange: function (args) {
                fieldsInfoTv = args.value;
            }
        }).mobiscroll('getInst');
        additionalInfoTvSelect = jQuery("#additional-info-tv").mobiscroll().select({
            touchUi: true,
            filter: true,
            selectMultiple: true,
            tags: true,
            mobiscroll: "Select",
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            display: "anchored",
            onChange: function (args) {
                additionalInfoTv = args.value;
            }
        }).mobiscroll('getInst');
        adminIconShowTvSelect = jQuery("#admin_icon_show_tv").mobiscroll().select({
            touchUi: true,
            filter: true,
            selectMultiple: true,
            tags: true,
            mobiscroll: "Select",
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            display: "anchored",
            onChange: function (args) {
                adminIconShowTv = args.value;
            }
        }).mobiscroll('getInst');



        

        /*eventStatusSelect = $('.wpm-status').mobiscroll().select({
            inputElement: document.getElementById(popup + '-status-input'),
            touchUi: true,
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {
                eventStatus = args.value;
            }
        }).mobiscroll('getInst');*/

        eventStatusSelect = $('#status_manuale').mobiscroll('getInst');
        eventStatusSelect.change(function(){
            jQuery(".required_focus").removeClass("required_focus");

            eventStatus = this.value;
        });


        eventStartEndPicker = $('#' + popup + '-event-dates').mobiscroll().datepicker({
            controls: (type_of_form == "1")?['time']:['calendar','time'],
            display: 'anchored', 
            select: 'range',
            startInput: '#' + popup + '-event-start',
            endInput: '#' + popup + '-event-end',
            showRangeLabels: true,
            touchUi: false,
            responsive: { medium: { touchUi: false } },
            onChange: function (args) {
                var dates = args.value;
               // console.log(dates)
                eventStart = dates[0];
                eventEnd = dates[1];
                eventRecurrenceSelect.setOptions({ data: getRecurrenceTypes(eventStart) });
            }
        }).mobiscroll('getInst');

        eventRecurrenceSelect = $('.popup-event-recurrence').mobiscroll().select({
            data: [],                                    // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
            touchUi: true,
            responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            onChange: function (args) {
                eventRecurrence = args.value;
                toggleRecurrenceEditor(eventRecurrence);
            }
        }).mobiscroll('getInst');

        recurrenceDaySelect = $('.recurrence-day').mobiscroll().select({
            data: getMonthDays(1),                       // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
            touchUi: true,
            responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            maxWidth: 80,
            onChange: function (args) {
                recurrenceDay = args.value;
            }
        }).mobiscroll('getInst');

        recurrenceMonthSelect = $('.recurrence-month').mobiscroll().select({
            data: MONTH_NAMES,                           // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
            touchUi: true,
            responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            onChange: function (args) {
                recurrenceMonth = args.value;
                var maxDay = MAX_MONTH_DAYS[recurrenceMonth - 1];
                if (recurrenceDay > maxDay) {
                    recurrenceMonthDaySelect.setVal(maxDay);
                }
                recurrenceMonthDaySelect.setOptions({ data: getMonthDays(recurrenceMonth) });
            }
        }).mobiscroll('getInst');

        recurrenceMonthDaySelect = $('.recurrence-month-day').mobiscroll().select({
            data: getMonthDays(1),                       // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
            touchUi: true,
            responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            maxWidth: 80,
            onChange: function (args) {
                recurrenceDay = args.value;
            }
        }).mobiscroll('getInst');

        recurrenceUntilDatepicker = $('.recurrence-until').mobiscroll().datepicker({
            controls: ['calendar'],
            display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
            touchUi: false,
            dateFormat: 'YYYY-MM-DD',                    // More info about dateFormat: https://docs.mobiscroll.com/5-21-1/eventcalendar#localization-dateFormat
            returnFormat: 'iso8601',
            onChange: function (args) {
                recurrenceUntil = args.value;
            },
            onOpen: function () {
                // Check the until stop condition radio
                recurrenceCondition = 'until';
                $('#recurrence-condition-until').mobiscroll('getInst').checked = true;
            }
        }).mobiscroll('getInst');

        $recurrenceWeekDays.on('change', function () {
            var values = [];
            $recurrenceWeekDays.each(function () {
                if (this.checked) {
                    values.push(this.value);
                }
            });
            recurrenceWeekDays = values.join(',');
        });

        $recurrenceInterval.on('change', function () {
            var value = +this.value;
            recurrenceInterval = !value || value < 1 ? 1 : value;
            this.value = recurrenceInterval;
        })

        $recurrenceCount.on('change', function () {
            var value = +this.value;
            recurrenceCount = !value || value < 1 ? 1 : value;
            this.value = recurrenceCount;
        }).on('click', function () {
            // Check the count stop condition radio
            recurrenceCondition = 'count';
            $('#recurrence-condition-count').mobiscroll('getInst').checked = true;
        });

        $('.md-recurrence-repeat').on('change', function () {
            recurrenceRepeat = this.value;
            toggleRecurrenceText(recurrenceRepeat);
        });

        $('.md-recurrence-edit-mode').on('change', function () {
            recurrenceEditMode = this.value;
        });

        $('.md-recurrence-condition').on('change', function () {
            recurrenceCondition = this.value;
        });

    }

    // Init popup for event create/edit
    var tvView = $('#tv-view').mobiscroll().popup({
        showOverlay: true,
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        onClose: function (event, inst) {

        },
        cssClass: 'md-recurring-tv-view-popup'
    }).mobiscroll('getInst');

    var addEditPopup = $('#season-calendar-add-edit-popup').mobiscroll().popup({
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh',
        onClose: function (event, inst) {

            var element = $('.add-edit-info').detach();
            $("#season-calendar-add-edit-popup").append(element)
            $(".calendar-edit-sections").html('')
        },
        /*  display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: false,
        fullScreen: true,
        scrollLock: false,
        height: 500,                                 // More info about height: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-height
        /* responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
             medium: {
                 display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                 width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                 fullScreen: false,
                 touchUi: false
             }
         },*/
        cssClass: 'md-recurring-event-editor-popup'
    }).mobiscroll('getInst');

    var addCustomerPopup = $('#add-customer-popup').mobiscroll().popup({
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh',
        onClose: function (event, inst) {

            /*var element = $('.add-edit-info').detach();
            $("#add-customer-popup").append(element)*/
        },
        cssClass: 'md-customer-popup'
    }).mobiscroll('getInst');

    var editPopup = $('#calendar-edit-popup').mobiscroll().popup({
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh'
    }).mobiscroll('getInst');

    // Init recurring edit mode popup
    var recurrenceEditModePopup = $('#recurrence-edit-mode-popup').mobiscroll().popup({
        maxWidth: 700,
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: false,
        buttons: ['cancel', {
            text: 'Ok',
            keyCode: 'enter',
            handler: function () {
                if (recurrenceDelete) {
                    deleteRecurringEvent();
                } else {
                    updateRecurringEvent();
                }
                addEditPopup.close();
                editPopup.close();
                recurrenceEditModePopup.close();
            },
            cssClass: 'mbsc-popup-button-primary'
        }],
        onClose: function () {
            // Reset edit mode to current
            recurrenceEditMode = 'current';
            $('#recurrence-edit-mode-current').mobiscroll('getInst').checked = true;
        },
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'center',                   // Specify display mode like: display: 'bottom' or omit setting to use default
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-recurring-event-editor-popup'
    }).mobiscroll('getInst');



    $eventAllDay.on('change', function () {
        eventAllDay = this.checked;
        toggleDatetimePicker(eventAllDay);
    });

    $eventDeleteButton.on('click', function () {

       

            let deleteConfirm = mobiscroll.confirm({
                title: 'Delete event?',
                message: 'Are you sure you want to delete this event?',
                okText: 'Yes',
                cancelText: 'Cancel',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        if (editedEvent.recurring) {
                            createRecurrenceEditPopup(true);
                        } else {
                            calendar.removeEvent(editedEvent);
                            addEditPopup.close();

                            delete_booking(editedEvent);
                        }

                    }
                }
            });

        /*if (confirm("Are you sure you want to delete this event?") == true) {

            if (editedEvent.recurring) {
                createRecurrenceEditPopup(true);
            } else {
                calendar.removeEvent(editedEvent);
                addEditPopup.close();

                delete_booking(editedEvent);
            }
        }*/
    });



    // Resource tooltip
    var $resourceTooltip = $('#calendar-resource-info-tooltip');
    var resourceTooltipTimer;
    var resourceTooltip = $resourceTooltip.mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: false,
        closeOnOverlayClick: false,
        width: 350
    }).mobiscroll('getInst');

    var selectedResourceId;

    $(document).on('click', '.mbsc-timeline-resource', function (e) {

        if (resourceTooltipTimer) {
            clearTimeout(resourceTooltipTimer);
            resourceTooltipTimer = null;
        }

        var $resourceTitle = $(this).find('.md-resource-details-cont .mbsc-timeline-resource-title');

        if ($resourceTitle.data('id')) {
            selectedResourceId = $resourceTitle.data('id');
        }

       var tooltipContent = $resourceTitle.data('content');
        $resourceTooltip.find('.k-tooltip-content').find(".res_tooltip").html('');

        $resourceTooltip.find('.k-tooltip-content').find(".res_tooltip").append("<h4><b>"+tooltipContent+"</b></h4>");
       

        //$resourceTooltip.find('.k-tooltip-content p .title-text').html(tooltipContent);

        if ($resourceTitle.data('sports')) {
            $resourceTooltip.find('.k-tooltip-content').find(".res_tooltip").append("<p><b>Passer for:</b> <span>"+$resourceTitle.data('sports')+"</span>")
        }

        resourceTooltip.setOptions({
            anchor: jQuery(this).find(".md-resource-header")[0]
        });

        resourceTooltip.open();
    });

    $resourceTooltip.on('click', '.tooltip-view-resource', function () {
        var resourceUrl = '/my-listings/add-listings/?action=edit&listing_id=' + selectedResourceId;

        Object.assign(document.createElement('a'), {
            target: '_blank',
            rel: 'noopener noreferrer',
            href: resourceUrl,
        }).click();
    })

    $resourceTooltip.on('click', '.tooltip-close-resource', function () {
        resourceTooltipTimer = setTimeout(function () {
            //resourceTooltip.close();
        }, 180);
    })

    $(document).mouseup(function (e) {
        if (!$resourceTooltip.is(e.target) && $resourceTooltip.has(e.target).length === 0) {
            resourceTooltipTimer = setTimeout(function () {
               // resourceTooltip.close();
            }, 180);
        }
    })

    // Refresh calendar
    $(document).on('click', '#tv-view-btn', function (e) {

        tvView.setOptions({
            maxWidth: 600,
             height: 400,
            anchor: e.target, 
            cssClass: 'tvPopup',
        });

        tvView.open();
        
    });

    $(document).on('click', '.refresh-calendar', function () {
            $(this).find("svg").css({
                "-webkit-animation-name":"spin",
                "-webkit-animation-duration":"1000ms",
                "-webkit-animation-iteration-count":"3",
            });
        console.log('refresh cal')
        get_season_booking_data(calendar);
        calendar.refresh();
        let _thatt = this;
        setTimeout(function(){
            $(_thatt).find("svg").removeAttr("style");
        },3000)
    });

    // Filters
    var $listingFilterPopup = $('#filter-listing-popup');
    var $eventListToggle = $('#show-daily-summary-week');
    var $weekNumbersSetting = $('#show-week-numbers');
    var $hoursSettingContainer = $('#display-hours-container');
    var $timescaleToSettingContainer = $('#time-scale-to-container');
    //var $timeLabelTimelineContainer = $('#time-label-timeline-container');

    if (calendar_view_val === 'timeline_month' || calendar_view_val === 'timeline_year') {
        calendarEventList = true;
    } else {
        calendarEventList = false
    }

    

    var $rangeButton = $('.report-range-picker-text');

    // returns the formatted date
    function getFormattedRange(start, end) {
        return formatDate('MMM D, YYYY', new Date(start)) + (end && getNrDays(start, end) > 1 ? (' - ' + formatDate('MMM D, YYYY', new Date(end))) : '');
    }

    // returns the number of days between two dates
    function getNrDays(start, end) {
        return Math.round(Math.abs((end.setHours(0) - start.setHours(0)) / (24 * 60 * 60 * 1000))) + 1;
    }

    var settingsPopup = $('#settings-popup-season').mobiscroll().popup({
        showOverlay: false,
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        fullScreen: true,
        scrollLock: false,
        maxWidth: 400,
        height: 500,                                 // More info about height: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-height
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-settings-popup'
    }).mobiscroll('getInst');
    var toastTemplatePopup = $('#toastTemplatePopup').mobiscroll().popup({
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        fullScreen: true,
        scrollLock: false,
        height: 65,                                 // More info about height: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-height
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-template-popup'
    }).mobiscroll('getInst');


    var templatePopup = $('#season-template-popup').mobiscroll().popup({
        maxWidth: 350,
        display: 'center',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        scrollLock: false,
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-template-popup'
    }).mobiscroll('getInst');

    let algorithm_popup = $('#algorithm_popup').mobiscroll().popup({
        maxWidth: 450,
        display: 'center',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        scrollLock: false,
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-template-popup'
    }).mobiscroll('getInst');

    $(document).on('click', '.algorithm_popup_btn', function (e) {

        algorithm_popup.setOptions({
            anchor: e.currentTarget,
        });

        algorithm_popup.open();

        
        jQuery(".algo-close").click(function(){

            algorithm_popup.close();

        });


    })
    jQuery(".algorithm_popup_form").submit(function(){

        save_template();

        algorithm_popup.close();

        run_algo_booking(jQuery(this));

       

    });
    $(document).on('click', '.move_approve_booking', function (e) {

        move_algo_booking();

    })

    $(document).on('change', '.cal_view_select', function (e) {
        calendar_view_val = e.target.value;
        console.log('here')
        if (calendar_view_val === 'timeline_month' || calendar_view_val === 'timeline_year') {
            calendarEventList = true;

        } else {
            calendarEventList = false
        }

        $eventListToggle.mobiscroll('getInst').checked = calendarEventList;

        onEventListToogle();

        updateCalendarSettings()



        // Persist to the db
        save_calendar_filters({
            name: 'calendar_view',
            value: calendar_view_val
        })



    })

    var settingsDaysFromSelect = $('#display-days-from-input').mobiscroll().select({
        data: DAY_NAMES.map(function (val, idx) {
            return {
                value: idx,
                text: val
            }
        }),
        locale: mobiscroll.locale[mobi_locale],
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarStartDay = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');;

    var settingsDaysToSelect = $('#display-days-to-input').mobiscroll().select({
        data: DAY_NAMES.map(function (val, idx) {
            return {
                value: idx,
                text: val
            }
        }),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarEndDay = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsHoursFromSelect = $('#display-hours-from').mobiscroll().select({
        inputElement: document.getElementById('display-hours-from-input'),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarStartTime = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsHoursToSelect = $('#display-hours-to').mobiscroll().select({
        inputElement: document.getElementById('display-hours-to-input'),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarEndTime = args.value;

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsTimeScaleToSelect = $('#time-scale-to').mobiscroll().select({
        inputElement: document.getElementById('time-scale-to-input'),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarTimeCellStep = args.value;
            calendarTimeLabelStep = args.value;

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    /*   var settingsTimeLabelsSelect = $('#time-label-timeline').mobiscroll().select({
           inputElement: document.getElementById('time-label-timeline-input'),
           touchUi: false,
           responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
           maxWidth: 80,
           onChange: function (args) {
               calendarTimeLabelStep = args.value;
   
               updateCalendarSettings()
           }
       }).mobiscroll('getInst'); */

    $eventListToggle.change(function (e) {
        calendarEventList = e.target.checked;

        onEventListToogle()
    })

    $weekNumbersSetting.change(function (e) {
        calendarWeekNumbers = e.target.checked;

        updateCalendarSettings()
    })
    additionalInfo = $("#additional-info").mobiscroll().select({
        target : jQuery("#additional-info"),
        touchUi: true,
        filter: true,
        selectMultiple: true,
        tags: true,
        mobiscroll: "Select",
        responsive: { small: { touchUi: false } },
        maxWidth: 80,
        onChange: function (args) {
            calendarAdditionalInfo = args.value
        }
    }).mobiscroll('getInst');

    show_rejected = $("#show_rejected").mobiscroll('getInst');
    show_rejected.change(function(){
        showRejected = this.value;
    })

    adminIconShow = $("#admin_icon_show").mobiscroll().select({
        target : jQuery("#admin_icon_show"),
        touchUi: true,
        filter: true,
        selectMultiple: true,
        tags: true,
        mobiscroll: "Select",
        responsive: { small: { touchUi: false } },
        maxWidth: 80,
        onChange: function (args) {

            calendarShowAdminIcons = args.value
        }
    }).mobiscroll('getInst');


    $(document).on('click', '.btn-config', function (e) {

        

        settingsDaysFromSelect.setVal(parseInt(calendarStartDay))
        settingsDaysToSelect.setVal(parseInt(calendarEndDay))
        settingsHoursFromSelect.setVal(calendarStartTime)
        settingsHoursToSelect.setVal(calendarEndTime)
        additionalInfo.setVal(calendarAdditionalInfo)
        adminIconShow.setVal(calendarShowAdminIcons)
        show_rejected.val(showRejected);

        if(Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.length < 1){
            calendarShowAdminIcons.push("dummy");
        }



        settingsTimeScaleToSelect.setVal(calendarTimeCellStep.toString())
        //settingsTimeLabelsSelect.setVal(calendarTimeLabelStep.toString())

        $weekNumbersSetting.mobiscroll('getInst').checked = calendarWeekNumbers;

        settingsPopup.setOptions({
            anchor: e.currentTarget,
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Lagre',
                keyCode: 'enter',
                handler: function () {
                    updateCalendarSettings();;

                    save_calendar_filters({
                        name: ['cal_start_day', 'cal_end_day', 'cal_starttime', 'cal_endtime', 'cal_time_cell_step', 'cal_time_label_step', 'cal_show_week_nos','additional_info','show_admin_icons','show_rejected'],
                        value: [calendarStartDay, calendarEndDay, calendarStartTime, calendarEndTime, calendarTimeCellStep, calendarTimeCellStep, calendarWeekNumbers,calendarAdditionalInfo,calendarShowAdminIcons,showRejected]
                    })
                    

                    settingsPopup.close();
                }
            }]
        });

        settingsPopup.open()
    })
    jQuery(".tv-create-btn").on("click",function(){
        let url_values = [];
        jQuery(".tv_view_main").find("select").each(function(){
 
             var keyF = jQuery(this).attr("name");
             var valueF = this.value;
             if(keyF == "listings"){
                 if(tvListing && Array.isArray(tvListing) && tvListing.length > 0){
                     valueF = tvListing.join(",");
                 }else{
                     valueF = "";
                 }
             }
             if(keyF == "fields-info-tv"){
                 if(fieldsInfoTv && Array.isArray(fieldsInfoTv) && fieldsInfoTv.length > 0){
                     valueF = fieldsInfoTv.join(",");
                 }else{
                     return true;
                     valueF = "";
                 }
             }
             if(keyF == "additional-info-tv"){
                 if(additionalInfoTv && Array.isArray(additionalInfoTv) && additionalInfoTv.length > 0){
                     valueF = additionalInfoTv.join(",");
                 }else{
                     return true;
                     valueF = "";
                 }
             }
             if(keyF == "tv-additional-fields"){
                 if(tvAdditionalFields && Array.isArray(tvAdditionalFields) && tvAdditionalFields.length > 0){
                     valueF = tvAdditionalFields.join(",");
                 }else{
                     return true;
                     valueF = "";
                 }
             }
             if(keyF == "admin_icon_show_tv"){
                if(adminIconShowTv && Array.isArray(adminIconShowTv) && adminIconShowTv.length > 0){
                    valueF = adminIconShowTv.join(",");
                }else{
                    return true;
                    valueF = "";
                }
            }

 
             if(valueF != ""){
 
                url_values.push(keyF+"="+valueF);
             }
        })

 
        if(url_values && url_values.length > 0){
           let url = url_values.join("&");
           let location_url = "/infoskjerm-kalender/?"+url;
           window.open(location_url)
        }
 
     })
    $(document).on('click', '.btn-template', function (e) {
        templatePopup.setOptions({
            anchor: e.currentTarget,
        });
        templatePopup.open()
    })


        jQuery(document).on("click",".close_template",function(e){    
            templatePopup.close();
        })
        jQuery(document).on("click",".create_template_cal",function(){
            templatePopup.close();

            jQuery("#templateCreateModal").show();

        })
        jQuery(document).on("submit",".template_form",function(e){

                templatePopup.close();

                showLoader();
                jQuery("#templateCreateModal").hide();

                e.preventDefault();
               jQuery(".template_form").find(".submit_btn").prop("disabled",true);

                var formdata = jQuery(this).serialize();

                jQuery.ajax({
                      type: "POST",
                      url: WPMCalendarV2Obj.ajaxurl,
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

                            save_template(data.template_id);
                        }
                        /*setTimeout(function(){
                            jQuery(".alert_error_message").hide();
                            jQuery(".alert_error_message").html("");
                        },4000);*/
                      }
                });
        })
        jQuery(document).on("click",".edit_template",function(e){
            templatePopup.close();

            var template_selected = jQuery(this).attr("data-id");
            var template_name = jQuery(this).attr("data-name");
            jQuery("#editTemplateModal").find(".delete_template_modal").remove();
            jQuery("#editTemplateModal").find(".close_template_btn").remove();
            jQuery("#editTemplateModal").find(".select_template_btn").remove();
            jQuery("#editTemplateModal").find(".template-create-btn").remove();
            jQuery("#editTemplateModal").find(".submit_btn").removeClass("gray_btn");
            jQuery("#editTemplateModal").find(".template_selected").val(template_selected);
            jQuery("#editTemplateModal").find(".template_name").val(template_name);
            jQuery("#editTemplateModal").show();

        })
        jQuery(document).on("submit","#editTemplateModal form",function(e){

            showLoader();
            jQuery("#editTemplateModal").hide();

            let formData = jQuery(this).serialize();

            jQuery.ajax({
                  type: "POST",
                  url: WPMCalendarV2Obj.ajaxurl,
                  data: formData,
                  dataType: 'json',
                  success: function (data) {
                   // hideLoader();
                    window.location.reload();
                  }
            });
              
        })
        jQuery(document).on("click",".delete_template_form",function(e){

            let template_selected = jQuery(this).attr("data-id");

            let thatt = this;

            let deleteConfirm = mobiscroll.confirm({
                title: 'Slett visning',
                message: 'Er du sikker du vil slette visningen?',
                okText: 'Ja',
                cancelText: 'Nei',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                       jQuery.ajax({
                              type: "POST",
                              url: WPMCalendarV2Obj.ajaxurl,
                              data: {"action" : "delete_template_modal", "template_selected" : template_selected},
                              dataType: 'json',
                              success: function (data) {
                                jQuery(thatt).parent().parent().remove();
                               // window.location.reload();
                              }
                        });

                    }
                }
            });
              
        })
        jQuery(document).on("click",".template_li .title_divs",function(e){

            let template_selected = jQuery(this).attr("data-id");

            let thatt = this;
            

            templatePopup.close();
            showLoader();

            jQuery.ajax({
                  type: "POST",
                  url: WPMCalendarV2Obj.ajaxurl,
                  data: {"action" : "season_change_template", "template_selected" : template_selected},
                  dataType: 'json',
                  success: function (data) {
                        hideLoader();

                        if(data.template_data){
                            window.location.reload();
                            
                            jQuery(".title_divs").removeClass("selected");

                            jQuery(thatt).addClass("selected");
                            apply_template_data(data.template_data);
                        }
                  }
            });
        })

    function apply_template_data(data){

        if(data.template_selected){
            templateSelected = data.template_selected !== '' ? data.template_selected : "";
        }
        if(data.cal_start_day){
            calendarStartDay = data.cal_start_day !== '' ? data.cal_start_day : 1;
        }
        if(data.cal_end_day){
            calendarEndDay = data.cal_end_day !== '' ? data.cal_end_day : 5;
        }

        if(data.cal_starttime){
            calendarStartTime = data.cal_starttime !== '' ? data.cal_starttime : '09:00';
        }

        if(data.cal_endtime){
            calendarEndTime = data.cal_endtime !== '' ? data.cal_endtime : '17:00';
        }

        if(data.cal_time_cell_step){
            calendarTimeCellStep = (data.cal_time_cell_step && data.cal_time_cell_step !== '') ? data.cal_time_cell_step : 60;
        }

        if(data.cal_time_label_step){
            calendarTimeLabelStep = (data.cal_time_label_step && data.cal_time_label_step !== '') ? data.cal_time_label_step : 60;
        }

        if(data.cal_show_week_nos){
            calendarWeekNumbers = (data.cal_show_week_nos !== '' && data.cal_show_week_nos == "true") ? true : false;
        }

        if(data.additional_info){
            calendarAdditionalInfo = data.additional_info !== '' ? data.additional_info : "";
        }

        if(!Array.isArray(calendarAdditionalInfo)){
            calendarAdditionalInfo = ["event_title","customer_name"];
        }

        if(data.show_admin_icons){
            calendarShowAdminIcons = data.show_admin_icons !== '' ? data.show_admin_icons : "";
        }

        if(data.filter_location){
            filter_locations = data.filter_location !== '' ? data.filter_location : [];
        }else{
            filter_locations = [];
        }
        if(data.show_rejected){
            showRejected = data.show_rejected !== '' ? data.show_rejected : "yes";
        }



        if(data.calendar_view){
            calendar_view = data.calendar_view !== '' ? data.calendar_view : "";
            if (!calendar_view || calendar_view == "" || calendar_view == 0) {
                calendar_view_val = 'timeline_week';
            } else {
                if (Array.isArray(calendar_view) && calendar_view.length > 0) {
                    calendar_view_val = calendar_view[0];
                } else if(calendar_view != "") {
                    calendar_view_val = calendar_view;
                }else{
                     calendar_view_val = 'timeline_week';
                }
            }
            calendar_view_val = calendar_view_val.trim();
        }

        
        resources_data = get_resources();
        calendar.setOptions({ resources: resources_data });
        updateCalendarSettings();
        get_season_booking_data(calendar)
    }

    var filteredListings = [];

    var listingsFilterPopup = $listingFilterPopup.mobiscroll().popup({
        display: 'center',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        scrollLock: false,
        height: 180,                                // More info about height: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-height
        responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            medium: {
                display: 'anchored',                         // Specify display mode like: display: 'bottom' or omit setting to use default
                width: '100%',                          // More info about width: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-width
                fullScreen: false,
                touchUi: false
            }
        },
        cssClass: 'md-listings-filter-popup'
    }).mobiscroll('getInst');

    

    $(document).on('click', '.btn-filter', function (e) {

        listingsFilterPopup.setOptions({
            anchor: e.currentTarget,
            headerText: 'Filter',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Update',
                keyCode: 'enter',
                handler: function () {
                    if (filteredListings.length === 0) {
                        calendar.setOptions({ resources: section_resources })
                    } else {
                        calendar.setOptions({ resources: filteredListings })
                    }

                    toggleFilterCounter(filteredListings)

                    // Persist to the db
                    save_calendar_filters(
                        {
                            name: 'filter_location',
                            value: filteredListings.map(function (listing) {
                                return listing.value;
                            })
                        },
                        false
                    )

                    listingsFilterPopup.close();
                }
            }]
        })

        listingsFilterPopup.open();
    });

    $(document).on('click', '.filter-clear', function () {

        filteredListings = [];

        filterListingSelect.setVal([]);

        calendar.setOptions({ resources: section_resources })

        toggleFilterCounter([])

        // Persist to the db
        save_calendar_filters({
            name: 'filter_location',
            value: []
        })
    })

    // Toggle count on page load
   // console.log({ filter_locations })
    toggleFilterCounter(filter_locations)

    function toggleFilterCounter(list) {
        if (Array.isArray(list) && list.length > 0) {
            $('#filter-count').html(list.length).show()
        } else {
            $('#filter-count').hide()
        }
    }

    // Search
    var searchTimer;
    let minSearch = new Date(new Date().setFullYear(new Date().getFullYear() - 2));
    let maxSearch = new Date(new Date().setFullYear(new Date().getFullYear() + 2));

    /*if(type_of_form == "1"){

        minSearch = new Date("2021-02-01 00:00:00");
        maxsearch = new Date("2021-02-07 23:59:00");

    }*/


    var searchList = $('#search-list').mobiscroll().eventcalendar({
        view: {
            agenda: {
                type: 'year',
                size:5
            }
        },
        min: minSearch,
        max: maxSearch,
        showControls: false,
        renderEventContent: function (data) {
            var currentResource = '';


            for (var i = 0; i < newResources.length; i++) {
                if (newResources[i].id === data.resource) {
                    currentResource = newResources[i].text;
                }
            }
            var listing_name = "";

            if(Array.isArray(resources_data) && resources_data.length > 0){

                if(data && data.resource){

                    var filteredArray = resources_data.filter(function(itm){
                      return parseInt(itm.id) == parseInt(data.resource);
                    });
                    if(filteredArray.length > 0){
                        if(filteredArray[0] && filteredArray[0].name){
                            listing_name = filteredArray[0].name;
                        }
                    }
                   
                }

            }
            if(listing_name == ""){
                return false;
            }
            let event = data.original;

                let title_div_data = [];

                if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("event_title")){
                    title_div_data.push(event.title);
                }
                if(event.customer && event.customer != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("customer_name")){
                     title_div_data.push(event.customer);
                }

                if(event.extra_info){
                    if(event.extra_info.age_group && event.extra_info.age_group != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("age_group")){
                       title_div_data.push(event.extra_info.age_group);
                    }
                    if(event.extra_info.sport && event.extra_info.sport != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("sport")){
                        title_div_data.push(event.extra_info.sport);
                    }
                    if(event.extra_info.members && event.extra_info.members != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("members")){
                        title_div_data.push(event.extra_info.members);
                    }
                    if(event.extra_info.type && event.extra_info.type != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("type")){
                        title_div_data.push(event.extra_info.type);
                    }
                    if(event.extra_info.team_level && event.extra_info.team_level != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("level")){
                        title_div_data.push(event.extra_info.team_level);
                    }
                    if(event.extra_info.team_name && event.extra_info.team_name != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("team_name")){
                        title_div_data.push(event.extra_info.team_name);
                    }
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }

                title_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        //debugger;
                        tl_div = tl_div.trim();

                        title_div_data_f.push(tl_div);
                    }

                })

                title_div_data = title_div_data_f.join(", ");

             /*    if(title_div_data.length > 20){

                    title_div_data = title_div_data.substring(0, 20) + " ...";
                } */

            // console.log('renderEventContent --> ', data, currentResource)

            return '<div class="mbsc-event-text" style="font-weight: 500;">' + title_div_data + '</div><br />'+listing_name+'' +
                '<div>' +
                '<div class="mbsc-event-text" style="padding-top: 10px;font-size: .875em;">' + currentResource + '</div>' +
                '</div>';
        },
        onEventClick: function (args) {
            calendar.navigate(args.event.start);
            calendar.setSelectedEvents([args.event]);
            searchPopup.close();
        },
    }).mobiscroll('getInst');;




    var $searchInput = $('#scheduler-search-input');

    var searchPopup = $('#search-popup').mobiscroll().popup({
        display: 'anchored',
        showArrow: false,
        showOverlay: false,
        scrollLock: false,
        contentPadding: false,
        focusOnOpen: false,
        focusOnClose: false,
        focusElm: $searchInput[0],
        anchor: $searchInput[0],
        height: 300,
    }).mobiscroll('getInst');

    $(document).on('input', '#scheduler-search-input', function (e) {
        var searchText = e.target.value;

        console.log('searching...', searchText)
        clearInterval(searchTimer)

        searchTimer = null

       // searchTimer = setTimeout(function () {
            var filteredTasks = [];

            if (searchText.length > 0) {

                

                for (var i = 0; i < schedulerTasks.length; i++) {
                        if (schedulerTasks[i].title && schedulerTasks[i].title != "" && schedulerTasks[i].title.toLowerCase().includes(searchText.toLowerCase())) {
                            filteredTasks.push(schedulerTasks[i]);
                        }else if(schedulerTasks[i].customer && schedulerTasks[i].customer != "" && schedulerTasks[i].customer.toLowerCase().includes(searchText.toLowerCase())){
                            filteredTasks.push(schedulerTasks[i]);
                        }else if(schedulerTasks[i].id && schedulerTasks[i].id != "" && schedulerTasks[i].id.toLowerCase().includes(searchText.toLowerCase())){
                            filteredTasks.push(schedulerTasks[i]);
                        }else if(schedulerTasks[i].description && schedulerTasks[i].description != "" && schedulerTasks[i].description.toLowerCase().includes(searchText.toLowerCase())){
                            filteredTasks.push(schedulerTasks[i]);
                        }else if(schedulerTasks[i].order_id && schedulerTasks[i].order_id != "" && schedulerTasks[i].order_id.toLowerCase().includes(searchText.toLowerCase())){
                            filteredTasks.push(schedulerTasks[i]);
                        }else if(schedulerTasks[i].customer_email && schedulerTasks[i].customer_email != "" && schedulerTasks[i].customer_email.toLowerCase().includes(searchText.toLowerCase())){
                            filteredTasks.push(schedulerTasks[i]);
                        }

                }

                searchList.setEvents(filteredTasks)

                searchPopup.setOptions({ anchor: e.currentTarget });

                if(type_of_form == "1"){

                    searchList.setOptions({ 
                         dateFormatLong: "DDDD"
                    });

                }

                searchPopup.open()

                calendar.setEvents(filteredTasks)

            } else {
                filteredTasks = schedulerTasks
                calendar.setEvents(schedulerTasks)

                searchPopup.close()
            }
        //}, 180)
    })

    $searchInput.on('focus', function (ev) {
        if (ev.target.value.length > 0) {
            searchPopup.open();
        }
    });

    // Settings tooltip
    var settingsTooltipTimer;
    var settingsTooltip = $('#settings-info-tooltip').mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: true,
        closeOnOverlayClick: false,
        width: "10%"
    }).mobiscroll('getInst');

    $(document).on('mouseenter', '.settings-info, .tooltip_info', function (e) {
        if (settingsTooltipTimer) {
            clearTimeout(settingsTooltipTimer);
            settingsTooltipTimer = null;
        }

        var tooltipContent = $(this).data('content');

        $('#settings-info-tooltip-content').html(tooltipContent);

        settingsTooltip.setOptions({
            anchor: e.currentTarget
        });

        settingsTooltip.open();
    });

    $(document).on('mouseleave', '.settings-info, .tooltip_info', function (e) {
        settingsTooltipTimer = setTimeout(function () {
            settingsTooltip.close();
        }, 180);
    });

    $('#settings-info-tooltip').on('mouseleave', function () {
        settingsTooltipTimer = setTimeout(function () {
            settingsTooltip.close();
        }, 180);
    });

    $(document).on('click', '#print-calendar', function (e) {
        calendar.print();
    });

    jQuery(document).on("submit",".editPopup #eventForm",function(){

        jQuery(".editPopup").find(".mbsc-edit-popup-button-primary").click();

    })

    function onEventListToogle() {

        if (calendar_view_val.includes('year')) {
            $hoursSettingContainer.css('display', 'none');
            //  $timeLabelTimelineContainer.css('display', 'none');
            $timescaleToSettingContainer.css('display', 'none');
        } else {
            $hoursSettingContainer.css('display', 'block');
            //   $timeLabelTimelineContainer.css('display', 'block');
            $timescaleToSettingContainer.css('display', 'block');

            if (calendarEventList) {
                $hoursSettingContainer.addClass('disabled-cont');
                //     $timeLabelTimelineContainer.addClass('disabled-cont');
                $timescaleToSettingContainer.addClass('disabled-cont');
            } else {
                $hoursSettingContainer.removeClass('disabled-cont');
                //       $timeLabelTimelineContainer.removeClass('disabled-cont');
                $timescaleToSettingContainer.removeClass('disabled-cont');
            }

            if (calendar_view_val === 'timeline_year' || calendar_view_val === 'timeline_month') {
                $hoursSettingContainer.addClass('disabled-cont');
            }
        }
    }



    function updateCalendarSettings() {

        removeTimelineMonthColor(calendar_view_val);

        var view_parts = calendar_view_val.split('_');
        var calendar_type = view_parts[0];
        var calendar_view_type = view_parts[1];
        var calendar_type_new;

        var options = {};

        if (calendar_type === 'schedule') {

            if (calendar_view_type === 'month') {
                options = {
                    view: {
                        calendar: {
                            labels: true,
                            startDay: calendarStartDay,
                            endDay: calendarEndDay,
                            weekNumbers: calendarWeekNumbers,
                            count : true
                        }
                    }
                }

                calendar_type_new = 'calendar';
            } else if (calendar_view_type === 'year') {
                options = {
                    view: {
                        calendar: {
                            type: calendar_view_type,
                            startDay: calendarStartDay,
                            endDay: calendarEndDay,
                            weekNumbers: calendarWeekNumbers,
                            count : true
                        }
                    },
                    height: '100%'
                }

                calendar_type_new = 'calendar';
            } else {
                options = {
                    view: {
                        schedule: {
                            type: calendar_view_type,
                            //startDay: calendarStartDay,
                           // endDay: calendarEndDay,
                            weekNumbers: calendarWeekNumbers
                        }
                    }
                }

                calendar_type_new = calendar_type;
            }

            /*if (calendarEventList) {
                options.view[calendar_type_new].eventList = true;
            } else {*/
                options.view[calendar_type_new].startTime = calendarStartTime;
                options.view[calendar_type_new].endTime = calendarEndTime;
                options.view[calendar_type_new].timeCellStep = calendarTimeCellStep;
                options.view[calendar_type_new].timeLabelStep = calendarTimeLabelStep;
            //}
        } else if (calendar_type === 'timeline') {
            var timelineConfig = {
                type: calendar_view_type,
                weekNumbers: calendarWeekNumbers,
            };

            calendar_type_new = calendar_type;

            if (calendar_view_type === 'month') {
                timelineConfig.size = 1;
                timelineConfig.resolution = 'day';
            } else if (calendar_view_type === 'year') {
                timelineConfig.size = 1;
                timelineConfig.resolution = 'month';
                timelineConfig.eventList = true
            }

            if (calendar_view_val !== 'timeline_year') {
                //timelineConfig.startDay = calendarStartDay;
                //timelineConfig.endDay = calendarEndDay;
            }

            options = {
                view: {
                    timeline: timelineConfig
                }
            };

            if (calendar_view_val === 'timeline_month') {
                options.view.timeline.eventList = true;
            } else {
                if (calendar_view_type !== 'year') {
                    options.view.timeline.startTime = calendarStartTime;
                    options.view.timeline.endTime = calendarEndTime;

                    options.view.timeline.timeCellStep = calendarTimeCellStep;
                    options.view.timeline.timeLabelStep = calendarTimeLabelStep;
                }
            }
            options.view.timeline.rowHeight = "equal";
        } else {
            options = {
                view: {
                    agenda: { type: 'day' }
                }
            };
        }
        
        if(type_of_form == "1"){
            if(options.view && options.view.timeline && options.view.timeline.weekNumbers ){
                options.view.timeline.weekNumbers = false;
            }
            if(options.view && options.view.schedule && options.view.schedule.weekNumbers ){
                options.view.schedule.weekNumbers = false;
            }
            options.renderDay =  function (day) {
                                        var date = day.date;


                                        var formatDate = mobiscroll.util.datetime.formatDate;
                                        if(type_of_form == "1"){
                                            if (calendar_view_type === 'month') {
                                               return formatDate('DDD', date);
                                            }else{
                                               return formatDate('DDDD', date);
                                            }   
                                        }
                                };
        }

        options.colors = calendar_view_val === 'schedule_month' || calendar_view_val === 'schedule_year' || calendar_view_val === 'timeline_year' ? [] : colors;

            calendar.setOptions(options)


    }
    function removeTimelineMonthColor(calendar_view_val){

        if (calendar_view_val == 'timeline_month') {

            jQuery("body").addClass("timeline_month_color");

        }else{

            jQuery("body").removeClass("timeline_month_color");

        }

    }
    function save_template(template_id = ""){

        let template_selected_id = templateSelected;

        if(template_id != ""){
             template_selected_id = template_id;
        }else{
           // alert(update_template_auto)
            if(update_season_template_auto != "yes"){
                return false;
            }
        }
        if(Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.length < 1){
            calendarShowAdminIcons.push("dummy");
        }
;



        if(template_selected_id && template_selected_id != "" && template_selected_id != undefined){



            let template_data = {
                action: "save_season_listing_filter_template_mobiscroll",
                template_selected: template_selected_id,
                cal_start_day: calendarStartDay,
                cal_end_day: calendarEndDay,
                cal_starttime: calendarStartTime,
                cal_endtime: calendarEndTime,
                cal_time_cell_step: calendarTimeCellStep,
                cal_time_label_step: calendarTimeLabelStep,
                cal_show_week_nos: calendarWeekNumbers,
                filter_location: (filterListingSelect && filterListingSelect != undefined) ? filterListingSelect.getVal() : [],
                calendar_view: jQuery(".cal_view_select").val(),
                additional_info: calendarAdditionalInfo,
                show_admin_icons: calendarShowAdminIcons,
                show_rejected: showRejected,
            }

            jQuery(".algorithm_popup_form").find("select").each(function(){
                
                template_data[jQuery(this).attr("name")] = jQuery(this).val();
            });



            $.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: template_data,
                success: function (response) {
                    if(template_id != ""){

                        window.location.reload();
                    
                    }else{
                        showToastMessage("Visning er lagret!","success");
                        hideLoader();
                        get_season_booking_data(calendar)
                    }
                    
                }
            });
        }
        /*calendarStartDay, calendarEndDay, calendarStartTime, calendarEndTime, calendarTimeCellStep, calendarTimeCellStep, calendarWeekNumbers*/
    }

    function open_template_popup(){

        toastTemplatePopup.setOptions({
            anchor: jQuery(".btn-template")[0],     
        });
        toastTemplatePopup.open();

        
        jQuery(".save_template_changes").on("click",function(){

            toastTemplatePopup.close();

            save_template();

        });
        jQuery(".new_template_btn").on("click",function(){

            toastTemplatePopup.close();

            jQuery("#templateCreateModal").show();

        });
        /*calendarStartDay, calendarEndDay, calendarStartTime, calendarEndTime, calendarTimeCellStep, calendarTimeCellStep, calendarWeekNumbers*/
    }

    function save_calendar_filters(data, shouldShowLoader = false) {
       // console.log('save_calendar_filters --> ', data)
        data.action = 'save_season_cal_filters';

        if (shouldShowLoader) {
            showLoader();
        }
        setTimeout(function(){
            save_template();
           //open_template_popup();
        },500)
        

        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: data,
            success: function (response) {
                hideLoader();

                get_season_booking_data(calendar)
            }
        });
    }

    function prepareFullCalendar() {
       // console.log({ section_resources, gym_resources })
        var __data = section_resources;
        section_resources_value = section_resources;
        var section_resources_array = [];
        var curDate = new Date();
        var utcdate = (curDate.getUTCDate());
        //console.log('utcdate'+utcdate.toString().length);
        utcdate = (utcdate.toString().length == 1) ? '0' + utcdate : utcdate;
        //console.log('section_resources'+JSON.stringify(section_resources));
        var start_date = curDate.getUTCFullYear() + '-0' + (curDate.getUTCMonth() + 1) + '-' + utcdate + 'T';
        const weekday = [ "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        var day = weekday[curDate.getDay()].toLowerCase();

        for (let i = 0; i < __data.length; ++i) {
            section_resources_value = section_resources;
            section_resources_array[__data[i].value] = __data[i];
            section_resources_value[i]['businessHours'] = [];
            var workingHours = __data[i].workingHours;
            end_date_str = '';
            start_date_str = '';
            var startTime = '00:00:00';
            var endTime = '23:59:59';
            startTime = workingHours.monday.start;
            endTime = workingHours.monday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [1], 'weekday': 'MO' });

            startTime = workingHours.tuesday.start;
            endTime = workingHours.tuesday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [2], 'weekday': 'TU' });

            startTime = workingHours.wednesday.start;
            endTime = workingHours.wednesday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [3], 'weekday': 'WE' });

            startTime = workingHours.thursday.start;
            endTime = workingHours.thursday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [4], 'weekday': 'TH' });

            startTime = workingHours.friday.start;
            endTime = workingHours.friday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [5], 'weekday': 'FR' });

            startTime = workingHours.saturday.start;
            endTime = workingHours.saturday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [6], 'weekday': 'SA' });

            startTime = workingHours.sunday.start;
            endTime = workingHours.sunday.end;
            if (endTime == '00:00') {
                endTime = '24:00';
            }
            if (startTime == null) {
                startTime = '00:00:00';
                endTime = '23:59:59';
            }
            section_resources_value[i]['businessHours'].push({ startTime: startTime, endTime: endTime, daysOfWeek: [0, 7], 'weekday': 'SU' });

        }

        return section_resources_value;
    }

    function filterCalForGroup(groups, calendar) {
        console.log('filtercalgroup');
        var selected_user_ids = [];
        var selected_listing = [];
        jQuery.each(groups, function (index, value) {
            if (value != '') {
                var selgroup = clublist.group_list[value];
                jQuery.each(selgroup, function (index1, value1) {
                    selected_user_ids.push(value1);
                });
                var sellistings = clublist.group_listings[value];
                jQuery.each(sellistings, function (index1, value1) {
                    selected_listing.push(value1);
                });
            }
        });
        var newTasks = [];
        var newResources = [];
        jQuery.each(schedulerTasks, function (key, value) {
            if (selected_user_ids.indexOf(value.client.value) >= 0) {
                newTasks.push(value);
            }
        });
        jQuery.each(section_resources, function (key, value) {
            if (selected_listing.indexOf(value.id) >= 0) {
                newResources.push(value);
            }
        });
        //calendar.setEvents(newTasks);
        calendar.setOptions({ 'resources': newResources });
        //calendar.refresh();
    }

    function libEvents(schedular_tasks) {
        if (schedular_tasks) {
            for (var i = 0; i < schedular_tasks.length; i++) {
                schedular_tasks[i]['start'] = new Date(schedular_tasks[i]['start']);
                schedular_tasks[i]['end'] = new Date(schedular_tasks[i]['end']);

                if (schedular_tasks[i]['rrule']) {
                    schedular_tasks[i]['rrule'] = (schedular_tasks[i]['rrule']).replace('\\n', '\n');
                    schedular_tasks[i]['recurring'] = convertRecurrenceRuleToObject(schedular_tasks[i]['rrule']);
                }

                schedular_tasks[i]['resourceId'] = Number(schedular_tasks[i]['gymSectionId']);
                schedular_tasks[i]['sectionResourcesId'] = Number(schedular_tasks[i]['gymSectionId']);

                if (schedular_tasks[i]['recurrenceId']) {
                    if (schedular_tasks[i]['recurrenceId'] == '0' || schedular_tasks[i]['recurrenceId'] == null) {
                        schedular_tasks[i]['recurrenceId'] = null
                    } else {
                        schedular_tasks[i]['recurrenceId'] = Number(schedular_tasks[i]['recurrenceId']);
                    }
                }

                if (schedular_tasks[i]['recurringException'] !== undefined && schedular_tasks[i]['recurringException'] !== '' && schedular_tasks[i]['recurringException'] !== null) {
                    var schedular_task_recurring_exception = schedular_tasks[i]['recurringException'];

                    if (typeof schedular_task_recurring_exception === 'string') {
                        schedular_tasks[i]['recurringException'] = schedular_tasks[i]['recurringException'].replace("[", '').replace(']', '').replaceAll("'", '').split(',');
                    }
                }
            }

            return schedular_tasks;
        }
    }

    function initFunctions(){


        var reportRangePicker = $(document).find('.report-range-picker').mobiscroll().datepicker({  
        min: date_min,    
        max: date_max,    
        controls: ['calendar'],
        display: 'anchored',
        showOverlay: false,
        touchUi: true,
        buttons: [],
        onOpen: function (inst) {
            inst.inst.setActiveDate(Object.keys(calendar._selectedDates)[0])
        },onInit: function (event, inst) {
           if(inst.getVal() == null){
              inst.setVal(date_start);
           }
        },
        onClose: function (args, inst) {

             var date = inst.getVal();

             calendar.navigate(date);

             var month_c = moment(date).format("MMMM");
             var year_c = moment(date).format("YYYY");

             $("#selected-day").find(".mbsc-calendar-month").text(month_c)
             $("#selected-day").find(".mbsc-calendar-year").text(year_c)

        }
        /*
        select: 'range',
        display: 'anchored',
        showOverlay: false,
        touchUi: true,
        buttons: [],
        onClose: function (args, inst) {
            var date = inst.getVal();
            if (date[0] && date[1]) {
                if (date[0].getTime() !== startDate.getTime()) {
                    // navigate the calendar
                    calendar.navigate(date[0]);
                }
                startDate = date[0];
                endDate = date[1];
                // set calendar view
                var view_parts = calendar_view_val.split("_")
     
                calendar.setOptions({
                    refDate: startDate,
                    view: {
                        [view_parts[0]]: {
                            type: view_parts[1],
                            size: getNrDays(startDate, endDate)
                        }
                    }
                });
            } else {
                reportRangePicker.setVal([startDate, endDate])
            }
        }
        */
    }).mobiscroll('getInst');

    }

    // Get all bookings
    function get_season_booking_data(calendar, shouldShowLoader = false, first_load = false) {
        removeTimelineMonthColor(calendar_view_val)
        $("#selected-day").html($('.cal-header-nav > button').html())
        var cal_viewww = "";;

        if (cal_type == "view_only") {
            cal_viewww = cal_view;
        }

        if (shouldShowLoader) {
            showLoader();
        }
        $listingss = [];
        if(first_load == true){

            if (Array.isArray(filter_locations) && filter_locations.length > 0) {
                 $listingss = filter_locations;
            }

        }else{
            if(filterListingSelect && filterListingSelect != undefined && filterListingSelect.getVal()  != undefined){

                if(filterListingSelect.getVal().length > 0){
                    $listingss = filterListingSelect.getVal();
                }

            }
        }



        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {
                action: 'get_season_booking_data',
                cal_type: cal_type,
                cal_view: cal_viewww,
                season_view: cal_viewww,
                selected_season: WPMCalendarV2Obj.selected_season,
                calender_type: "season_tv",
                listing: WPMCalendarV2Obj.listings,
                additional_info: calendarAdditionalInfo,
                show_rejected: showRejected,
            },
            success: function (response) {
               // console.log(response)

               schedulerTasks = libEvents(response.schedular_tasks);
               
                calendar.setEvents(schedulerTasks);

                initFunctions();

                if (shouldShowLoader) {
                    hideLoader();
                }
            }
        });
       /* var reportRangePicker = $('.report-range-picker').mobiscroll().datepicker({
            controls: ['calendar'],
            display: 'anchored',
            showOverlay: false,
            touchUi: true,
            buttons: [],
            onOpen: function (inst) {
                inst.inst.setActiveDate(Object.keys(calendar._selectedDates)[0])
            },
            onClose: function (args, inst) {
                console.log(inst)
                var date = inst.getVal();

                calendar.navigate(date);

            }

        }).mobiscroll('getInst');*/
    }

    // Get bookings for selected client
    function get_booking_by_user(eventClientUser = "", eventTeamUser = "") {

        if(eventClientUser != ""){
            eventClient = eventClientUser;
        }
        jQuery.post(
            WPMCalendarV2Obj.ajaxurl,
            {
                action: 'get_season_booking_by_user',
                id: eventClient,
                cal_type: cal_type,
            },
            function (response) {

                eventTeamSelect.setVal("");

                if (response.user_teams.length) {


                    var userTeams = [];
                    var output = '<option value="">Select</option>';
                    response.user_teams.forEach(function (team) {

                        userTeams.push({text:team.name,value:team.id});

                       /* output += '<option value="' + team.id + '">' + team.name + '</option>';
                        userTeams.push(team.id)*/
                    });
                    eventTeamSelect.setOptions({ data: userTeams});

                    if(eventTeamUser != ""){
                        eventTeamSelect.setVal(eventTeamUser);
                    }
                } else {
                   eventTeamSelect.setOptions({ data: []});
                }

               // getReservationTableNew(response);
            }
        );
    }

    function add_booking(data) {
        data.action = 'wpm_add_record';
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:SS');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:SS');

        showLoader();

        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {

                get_season_booking_data(calendar);

                showToast('Event created successfully');

                hideLoader();
            }
        );
    }

    function update_booking(data) {
        //console.log(data)
        data.action = 'wpm_season_update_record';
        data.season_view = jQuery(".change_season_view").val();
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:SS');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:SS');

        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {
                var cpt = 1;

                showToast('Oppdatert');

                get_season_booking_data(calendar);

            });
    }

    function delete_booking(data) {
        data.action = 'wpm_delete_record';
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:SS');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:SS');

        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {

                showToast('Event deleted successfully');

            });
    }

    jQuery(document).on('change',".change_season_view",function(){

        showLoader();

        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {
                action: 'save_season_view_mobiscroll',
                season_view: jQuery(this).val(),
            },
            success: function (response) {
                 window.location.reload();
            }
        });
    })

    jQuery(document).on('change',".season_change",function(){
        showLoader();
        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {
                action: 'save_selected_season_mobiscroll',
                season_id: jQuery(this).val(),
            },
            success: function (response) {
                 window.location.reload();
            }
        });
    })
    jQuery(document).on('click',".move_raw_booking",function(){

        let selected_season_data = jQuery(".season_change").val();

        if(selected_season_data == ""){
             showToast('Season not selected!');
        }else{

            mobiscroll.confirm({
                message: 'Er du sikker at du vil flytte endringene? Hvis algoritmen er kjørt, vil dette slette poeng fra algoritmen og ersatte algoritme data.',
                okText: 'Ja',
                cancelText: 'Avbryt',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        showLoader();
                        jQuery.ajax({
                            type: "POST",
                            url: WPMCalendarV2Obj.ajaxurl,
                            data: {
                                action: 'move_raw_booking_mobiscroll',
                                selected_season: selected_season,
                            },
                            success: function (response) {
                                hideLoader();
                                showToast('Flyttet :)');
                                //window.location.reload();
                            }
                        });

                    }
                }
            });
        }    

            
    })

    jQuery(document).on('change',".export_select",function(){

    

        if(this.value != ""){

            let selected_season_data = jQuery(".season_change").val();
            let export_type = jQuery(this).val();

            mobiscroll.confirm({
                message: 'Er du sikker?',
                okText: 'Ja',
                cancelText: 'Avbryt',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        showLoader();
                        jQuery.ajax({
                            type: "POST",
                            url: WPMCalendarV2Obj.ajaxurl,
                            data: {
                                action: 'export_booking_mobiscroll',
                                export_type: export_type,
                                selected_season: selected_season_data,
                                type_of_form: type_of_form,
                            },
                            success: function (response) {
                                hideLoader();
                                jQuery(".export_select").val("");
                                jQuery(".export_select").change();
                                if(export_type == "export"){

                                    showToast('Flyttet godkjente bookinger til administrasjon kalender :)');

                                }else if(export_type == "export_csv"){
                                    window.open(response);
                                }
                               // showToast('Successfully copied data!');
                                //window.location.reload();
                            }
                        });

                    }
                }
            });

        }
        
    })

    jQuery(document).ready(function(){

        $(document).on("click",".update_template_auto_main", function(){
            let inputt = jQuery(this).find("input")[0];
            update_season_template_auto = "no";
            if(inputt.checked == true){
               update_season_template_auto = "yes";
            }
            formData = {};
            formData.action = 'save_season_template_auto_checkbox';
            formData.update_season_template_auto = update_season_template_auto;
            $.post( WPMCalendarV2Obj.ajaxurl,
                formData,
                function (response) {

                 showToastMessage("Lagret!","success");
                   

            });
        })

    })


})(jQuery);

mobiscroll.setOptions({
    locale: mobiscroll.localeNo,
    theme: 'ios',
    themeVariant: 'light'
});

jQuery(function ($) {

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min) + min);
    }

    var resourceNr = 200;
    var eventsNr = 10000;
    var myResources = [];
    var myEventColors = ['#ff0101', '#239a21', '#8f1ed6', '#01adff', '#d8ca1a'];

    for (var i = 1; i <= resourceNr; i++) {
        myResources.push({ name: 'Resource ' + i, id: i });
    }

    $('#demo-big-data').mobiscroll().eventcalendar({
        resources: myResources,
        view: {
            timeline: {
                type: 'year',
                eventList: true
            }
        },
        onPageLoading: function (args, inst) {
            setTimeout(function () {
                var myEvents = [];
                var year = args.firstDay.getFullYear();
                // Generate random events
                for (var i = 0; i < eventsNr; i++) {
                    var day = getRandomInt(1, 31);
                    var length = getRandomInt(2, 5);
                    var resource = getRandomInt(1, resourceNr + 1);
                    var month = getRandomInt(0, 12);
                    var color = getRandomInt(0, 6);
                    myEvents.push({
                        color: myEventColors[color],
                        end: new Date(year, month, day + length),
                        resource: resource,
                        start: new Date(year, month, day),
                        title: 'Event ' + i,
                    });
                }
                inst.setEvents(myEvents);
            });
        }
    });
});