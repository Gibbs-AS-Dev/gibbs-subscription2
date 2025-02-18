window.calendar = '';


(function ($) {

    jQuery("body").addClass("mobiscroll_calender");
    jQuery("body").addClass("tv_calender_body");


    setInterval(function () {
        
        get_booking_data(calendar)

    }, WPMCalendarV2Obj.refresh * 60 * 1000)




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

    var formatDate = mobiscroll.util.datetime.formatDate;

    // Settings
    var calendarEventList = false;
    var calendarStartDay = WPMCalendarV2Obj.cal_start_day !== '' ? WPMCalendarV2Obj.cal_start_day : 1;
    var calendarEndDay = WPMCalendarV2Obj.cal_end_day !== '' ? WPMCalendarV2Obj.cal_end_day : 5;
    var calendarStartTime = WPMCalendarV2Obj.cal_starttime != '' ? WPMCalendarV2Obj.cal_starttime : '09:00';
    var calendarEndTime = WPMCalendarV2Obj.cal_endtime != '' ? WPMCalendarV2Obj.cal_endtime : '17:00';
    var calendarTimeCellStep = WPMCalendarV2Obj.cal_time_cell_step !== '' ? WPMCalendarV2Obj.cal_time_cell_step : 60;
    var calendarTimeLabelStep = WPMCalendarV2Obj.cal_time_label_step !== '' ? WPMCalendarV2Obj.cal_time_label_step : 60;
    var calendarWeekNumbers = WPMCalendarV2Obj.cal_show_week_nos !== '' ? WPMCalendarV2Obj.cal_show_week_nos : true;
    var show_book_now = WPMCalendarV2Obj.show_book_now != '' ? WPMCalendarV2Obj.show_book_now : 'no';

    var calendarAdditionalInfo = WPMCalendarV2Obj.additional_info !== '' ? WPMCalendarV2Obj.additional_info : "";
    var calendarShowFieldInfo = WPMCalendarV2Obj.show_fields_info !== '' ? WPMCalendarV2Obj.show_fields_info : "";

    if(!Array.isArray(calendarAdditionalInfo)){
        calendarAdditionalInfo = ["empty"];
    }
    if(!Array.isArray(calendarShowFieldInfo)){
        calendarShowFieldInfo = ["empty"];
    }

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
    var calendar_view_val = 'timeline_week';

    calendar_view_val = calendar_view.trim()

    resources = prepareFullCalendar();
    var businessHours = [];
    if (Array.isArray(resources) && resources.length > 0 && typeof (resources[0]['businessHours']) != 'undefined' && (resources[0]['businessHours']) != 'null' && (resources[0]['businessHours']) != '') {
        businessHours = resources[0]['businessHours'];
    }

    var section_resources_value = resources;
    var colors = [];
    var __data = resources;
    for (let i = 0; i < __data.length; ++i) {
        console.log('__data' + JSON.stringify(__data[i]));
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
    newResources = section_resources;
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


    if (WPMCalendarV2Obj.header == 1)

        header = function () {
            var renderHeader = '<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr" style="width:100%;">';

            // Date picker
            renderHeader += '<div class="report-range-picker"><button mbsc-button data-variant="flat" id="selected-day" class="mbsc-calendar-button">' +
                '<span class="mbsc-calendar-title report-range-picker-text">' +
                '</span></button></div>';
            renderHeader += '<div mbsc-calendar-nav class="cal-header-nav d-none"></div>';

            // Prev next buttons
            renderHeader += '<div mbsc-calendar-prev class="cal-header-prev"></div>' +
                '<div mbsc-calendar-next class="cal-header-next"></div>';

            // Today
            renderHeader += '&nbsp;&nbsp;<div mbsc-calendar-today style="min-width: 50px"></div>&nbsp;&nbsp;';


            // Refresh button
            renderHeader += '<div class="refresh-calendar"><i class="fa fa-rotate" aria-hidden="true"></i></div>';


            return renderHeader;
        }

    else

        header = function () {
            return '';
        }


    mobiscroll.setOptions({
        locale: mobiscroll.locale[mobi_locale],                     // Specify language like: locale: mobiscroll.localePl or omit setting to use default
        theme: 'ios',                                    // Specify theme like: theme: 'ios' or omit setting to use default
        themeVariant: 'light'                        // More info about themeVariant: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-themeVariant
    });

    // Init the event calendar
    window.calendar = calendar = $('#scheduler').mobiscroll().eventcalendar({
        modules: [mobiscroll.print],               // More info about clickToCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-clickToCreate
        view: {
            timeline: {
                type: 'week'
            }
        },        // More info about view: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-view
        data: [],                              // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
        // colors: calendar_view_val === 'schedule_month' || calendar_view_val === 'schedule_year' ? [] : colors,
        resources: newResources,

        onPageLoaded: function (args, inst) {
            var end = args.lastDay;
            startDate = args.firstDay;
            endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate() - 1, 0);
            setTimeout(function(){
                $("#selected-day").html($('.cal-header-nav > button').html())
            },100)
            

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
                if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
                    title_div_data.push(event.amount_guest);
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
                fields_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        //debugger;
                        tl_div = tl_div.trim();

                        title_div_data_f.push(tl_div);
                    }

                })

                if(event.fields_info_data){
                    let fields_info_data = event.fields_info_data;
                    fields_info_data.forEach(function(tl_div2){



                        if(tl_div2.label){
                            //debugger;

                           //var $name_b = "<b>"+tl_div2.label+"</b> : "+tl_div2.value;
                           var $name_b = tl_div2.value;
    
                           fields_div_data_f.push($name_b);
                        }
    
                    })

                }


                title_div_data = title_div_data_f.join(", ");

                let fields_div_data = fields_div_data_f.join(", ");

              /*   if(title_div_data.length > 20){

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

                var visibleEvent = "";

                if(data.startDate && data.endDate){
                    let diffTime = Math.abs(data.startDate - data.endDate);
                    let minutess = Math.floor((diffTime/1000)/60);

                    if(minutess < 31){

                        visibleEvent = "style='visibility:hidden'";

                    }
                }

                return '<div class="md-custom-event-cont ' + eventClass + '" style="color: #fff;">' +
                    '<div class="md-custom-event-wrapper settings-info" data-content="'+all_title_div_data+'">' +
                    '<div class="md-custom-event-details" '+visibleEvent+'>' +
                    '<div class="md-custom-event-title"><span class="ev-title">' + title_div_data + '</span><span style="margin-right: auto">' + icons + '</span>' + '</div>' +
                    '<div class="md-custom-event-fields"><span class="ev-field">' + fields_div_data + '</span></div>' +
                    '<div class="md-custom-event-time">' + data.start + ' - ' + data.end + '</div>' +
                    '</div></div></div>';
            };
        },renderEvent: function (data) {
            if (data.allDay) {
                return '<div style="background:#88D6FD;color: #fff;" class="md-custom-event-allday-title">' + data.title + '</div>';
            } else {
                var icons = '';
                var event = data.original;



                var eventClass = '';

                if (event.status) {
                    eventClass = event.status ? 'calendar-status-' + event.status : 'calendar-status-Lukket';
                } else {
                    eventClass = 'calendar-status-new';
                }

                let title_div_data = [];

                title_div_data.push(event.title);

                title_div_data.push(event.customer);

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
        renderHeader: header,
        showControls: WPMCalendarV2Obj.header,
        renderResource: function (resource) {

            var bookUrl = '/?post_type=listing&p=' + resource.id;

            let book_text = "";
            if(show_book_now == "yes"){
                book_text = "<div class='main-bk-btn'>" +
                                "<button class='bk_now' onclick='window.open(`" + bookUrl + "`, `_blank`)'>" +
                                    "Bestill <i class='fas fa-arrow-up-right-from-square'></i>" +
                                "</button>" +
                            "</div>";

            }
            return '<div class="md-resource-details-cont">' +
                '<div class="md-resource-header mbsc-timeline-resource-title" data-id="' + resource.id + '" data-content="' + resource.full_text + '" data-sports="' + resource.sports + '">' + resource.name + ' '+ book_text +'</div>' +
                '</div>';
        },
        onSelectedDateChange: function (event, inst) {
            $("#selected-day").html($('.cal-header-nav > button').html())

        }
    }).mobiscroll('getInst');

    get_booking_data(calendar);

    // Update settings on page load 
    updateCalendarSettings(true);




    if (calendar_view_val === 'timeline_month' || calendar_view_val === 'timeline_year') {
        calendarEventList = true;
    } else {
        calendarEventList = false
    }


    // returns the formatted date
    function getFormattedRange(start, end) {
        return formatDate('MMM D, YYYY', new Date(start)) + (end && getNrDays(start, end) > 1 ? (' - ' + formatDate('MMM D, YYYY', new Date(end))) : '');
    }

    // returns the number of days between two dates
    function getNrDays(start, end) {
        return Math.round(Math.abs((end.setHours(0) - start.setHours(0)) / (24 * 60 * 60 * 1000))) + 1;
    }



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



    $(document).on('click', '.btn-config', function (e) {

        settingsDaysFromSelect.setVal(calendarStartDay)
        settingsDaysToSelect.setVal(calendarEndDay)
        settingsHoursFromSelect.setVal(calendarStartTime)
        settingsHoursToSelect.setVal(calendarEndTime)

        settingsTimeScaleToSelect.setVal(calendarTimeCellStep.toString())
        settingsTimeLabelsSelect.setVal(calendarTimeLabelStep.toString())

        $weekNumbersSetting.mobiscroll('getInst').checked = calendarWeekNumbers;

        settingsPopup.setOptions({
            anchor: e.currentTarget,
            headerText: 'Calendar settings',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Save',
                keyCode: 'enter',
                handler: function () {
                    updateCalendarSettings();

                    save_calendar_filters({
                        name: ['cal_start_day', 'cal_end_day', 'cal_starttime', 'cal_endtime', 'cal_time_cell_step', 'cal_time_label_step', 'cal_show_week_nos'],
                        value: [calendarStartDay, calendarEndDay, calendarStartTime, calendarEndTime, calendarTimeCellStep, calendarTimeLabelStep, calendarWeekNumbers]
                    })

                    settingsPopup.close();
                }
            }]
        });

        settingsPopup.open()
    })

    var filteredListings = [];


    var filterListingSelect = $('#filter-listing-input').mobiscroll().select({
        data: section_resources,
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 300,
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

    $(document).on('click', '.btn-filter', function (e) {

        listingsFilterPopup.setOptions({
            anchor: e.currentTarget,
            headerText: 'Filter',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Velg',
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
                        true
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




    // Search
    var searchTimer;

    var searchList = $('#search-list').mobiscroll().eventcalendar({
        view: {
            agenda: {
                type: 'month',
                size: 3
            }
        },
        renderEventContent: function (data) {
            var currentResource = '';

            for (var i = 0; i < newResources.length; i++) {
                if (newResources[i].id === data.resource) {
                    currentResource = newResources[i].text;
                }
            }

            console.log('renderEventContent --> ', data, currentResource)

            return '<div class="mbsc-event-text" style="font-weight: 500;">' + data.title + '</div>' +
                '<div>' +
                '<div class="mbsc-event-text" style="padding-top: 10px;font-size: .875em;">' + currentResource + '</div>' +
                '</div>';
        },
        showControls: true,
        onEventClick: function (args) {
            calendar.navigate(args.event.start);
            calendar.setSelectedEvents([args.event]);
            searchPopup.close();
        },
    }).mobiscroll('getInst');;

    var settingsTooltipTimer;
    var settingsTooltip = $('#settings-info-tooltip').mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: true,
        closeOnOverlayClick: false,
        width: "10%",
        cssClass: 'tooltippopup',
    }).mobiscroll('getInst');

    $(document).on('mouseenter', '.settings-info, .tooltip_info', function (e) {
        //settingsTooltip.close();
        //alert()

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

    if ($(window).width() > 700){

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
    }else{
        $(document).click(function (e) {
            if(jQuery(e.target).closest(".settings-info").length < 1){
                settingsTooltip.close();
            }
        });
    }


    function onEventListToogle() {

        if (calendar_view_val.includes('year')) {
            $hoursSettingContainer.css('display', 'none');
            $timeLabelTimelineContainer.css('display', 'none');
            $timescaleToSettingContainer.css('display', 'none');
        } else {
            $hoursSettingContainer.css('display', 'block');
            $timeLabelTimelineContainer.css('display', 'block');
            $timescaleToSettingContainer.css('display', 'block');

            if (calendarEventList) {
                $hoursSettingContainer.addClass('disabled-cont');
                $timeLabelTimelineContainer.addClass('disabled-cont');
                $timescaleToSettingContainer.addClass('disabled-cont');
            } else {
                $hoursSettingContainer.removeClass('disabled-cont');
                $timeLabelTimelineContainer.removeClass('disabled-cont');
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
                            startDay: calendarStartDay,
                            endDay: calendarEndDay,
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
                timelineConfig.startDay = calendarStartDay;
                timelineConfig.endDay = calendarEndDay;
            }
            //timelineConfig.rowHeight = "equal";
           // timelineConfig.eventList = true;
            options = {
                    view: {
                        timeline: timelineConfig,
                    }
                };
            options.view.timeline.rowHeight = "equal";    

            /*if(calendar_view_type === 'month' || calendar_view_type === 'year'){
                options = {
                    view: {
                        timeline: timelineConfig,
                        //calendar : { count : true}
                    }
                };
            }else{
                options = {
                    view: {
                        timeline: timelineConfig
                    }
                };
            }*/



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
        } else {
            options = {
                view: {
                    agenda: { type: 'day' }
                }
            };
        }
       



        options.colors = calendar_view_val === 'schedule_month' || calendar_view_val === 'schedule_year' || calendar_view_val === 'timeline_year' ? [] : colors,

            calendar.setOptions(options)


    }

    function removeTimelineMonthColor(calendar_view_val){

        if (calendar_view_val == 'timeline_month') {

            jQuery("body").addClass("timeline_month_color");

        }else{

            jQuery("body").removeClass("timeline_month_color");

        }

    }

    function save_calendar_filters(data, shouldShowLoader = false) {
        console.log('save_calendar_filters --> ', data)
        data.action = 'save_cal_filters';

        if (shouldShowLoader) {
            showLoader();
        }

        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: data,
            success: function (response) {
                hideLoader();

                get_booking_data(calendar)
            }
        });
    }

    function prepareFullCalendar() {
        console.log({ section_resources, gym_resources })
        var __data = section_resources;
        section_resources_value = section_resources;
        var section_resources_array = [];
        var curDate = new Date();
        var utcdate = (curDate.getUTCDate());
        //console.log('utcdate'+utcdate.toString().length);
        utcdate = (utcdate.toString().length == 1) ? '0' + utcdate : utcdate;
        //console.log('section_resources'+JSON.stringify(section_resources));
        var start_date = curDate.getUTCFullYear() + '-0' + (curDate.getUTCMonth() + 1) + '-' + utcdate + 'T';
        const weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
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

    // Get all bookings
    function get_booking_data(calendar, shouldShowLoader = false) {
        removeTimelineMonthColor(calendar_view_val)
        $("#selected-day").html($('.cal-header-nav > button').html())
        var cal_viewww = "";;

        if (cal_type == "view_only") {
            cal_viewww = cal_view;
        }

        if (shouldShowLoader) {
            showLoader();
        }

        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {
                action: 'get_booking_data',
                cal_type: cal_type,
                cal_view: cal_viewww,
                calender_type: "tv",
                listing: WPMCalendarV2Obj.listings,
                additional_info: calendarAdditionalInfo,
                show_fields_info: calendarShowFieldInfo,
            },
            success: function (response) {
                console.log(response)
                var filter_location = response.filter_location;

                schedulerTasks = libEvents(response.schedular_tasks);

                /*resources = prepareFullCalendar('');

                var section_resources_value = resources;

                if (typeof (filter_group) != 'undefined' && filter_group != null && filter_group != '') {
                    var selected_values = filter_group.toString();
                    selected_values = selected_values.split(',');
                    filterCalForGroup(selected_values, calendar);
                }

                if (Array.isArray(filter_location) && filter_location.length > 0) {
                    var selectedListings = filter_location.map(function (listingId) {
                        return parseInt(listingId)
                    });

                    section_resources_value = resources.filter(function (resource) {
                        return selectedListings.includes(resource.value)
                    });

                    //filterListingSelect.setVal(selectedListings);
                } else {
                    section_resources_value = resources;
                }

                calendar.setOptions({ resources: section_resources_value });*/
                calendar.setEvents(schedulerTasks);

                initFunctions();

                if (shouldShowLoader) {
                    hideLoader();
                }
            }
        });
        /*var reportRangePicker = $('.report-range-picker').mobiscroll().datepicker({
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

        var resourceUrl = '/?post_type=listing&p=' + selectedResourceId;

        Object.assign(document.createElement('a'), {
            target: '_blank',
            rel: 'noopener noreferrer',
            href: resourceUrl,
        }).click();

        //resourceTooltip.open();
    });

    $resourceTooltip.on('click', '.tooltip-view-resource', function () {
        var resourceUrl = '/?post_type=listing&p=' + selectedResourceId;

        Object.assign(document.createElement('a'), {
            target: '_blank',
            rel: 'noopener noreferrer',
            href: resourceUrl,
        }).click();
    })

    $resourceTooltip.on('click', '.tooltip-close-resource', function () {
        resourceTooltipTimer = setTimeout(function () {
            resourceTooltip.close();
        }, 180);
    })

    $(document).mouseup(function (e) {
        if (!$resourceTooltip.is(e.target) && $resourceTooltip.has(e.target).length === 0) {
            resourceTooltipTimer = setTimeout(function () {
                resourceTooltip.close();
            }, 180);
        }
    })

    function initFunctions(){

            var reportRangePicker = $(document).find('.report-range-picker').mobiscroll().datepicker({
            controls: ['calendar'],
            display: 'anchored',
            showOverlay: false,
            touchUi: true,
            buttons: [],
            onOpen: function (inst) {
                inst.inst.setActiveDate(Object.keys(calendar._selectedDates)[0])
            },
            onClose: function (args, inst) {

                 var date = inst.getVal();

                if(date){

                     calendar.navigate(date);

                    setTimeout(function(){

                        let month_c = moment(date).format("MMMM");
                        let year_c = moment(date).format("YYYY");

                        month_c = ucwords(month_c);
                        year_c = ucwords(year_c);

                        $("#selected-day").find(".mbsc-calendar-month").text(month_c)
                        $("#selected-day").find(".mbsc-calendar-year").text(year_c)

                    },200)

                     
                 }

                 

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

    jQuery(document).ready(function(){
        $('.cal-header-nav > button').on('DOMSubtreeModified', function(){
           $("#selected-day").html($('.cal-header-nav > button').html())
        });
    });





})(jQuery);