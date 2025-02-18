(function($) {
    // Refresh calendar
    $(document).on('click', '.refresh-calendar', function() {
        console.log('refresh cal')
        get_booking_data(calendar);
        calendar.refresh();
    });

    // Filters
    var $eventListToggle = $('#show-daily-summary-week');
    var $weekNumbersSetting = $('#show-week-numbers');
    var $hoursSettingContainer = $('#display-hours-container');
    var $timescaleToSettingContainer = $('#time-scale-to-container');
    var $timeLabelTimelineContainer  = $('#time-label-timeline-container');

    var settingsPopup = $('#settings-popup').mobiscroll().popup({
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: false,
        fullScreen: true,
        scrollLock: false,
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

    $(document).on('change', '.cal_view_select', function(e) {
        calendar_view_val = e.target.value;
 
        onEventListToogle();
       
        updateCalendarSettings()
    })

    var settingsDaysFromSelect = $('#display-days-from-input').mobiscroll().select({
        data: DAY_NAMES.map( function (val, idx) {
            return {
                value: idx,
                text: val
            }
        } ),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarStartDay = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');;

    var settingsDaysToSelect = $('#display-days-to-input').mobiscroll().select({
        data: DAY_NAMES.map( function (val, idx) {
            return {
                value: idx,
                text: val
            }
        } ),
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
            calendarEndTime =  args.value;

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsTimeScaleToSelect = $('#time-scale-to').mobiscroll().select({
        inputElement: document.getElementById('time-scale-to-input'),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarTimeCellStep =  args.value;

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsTimeLabelsSelect = $('#time-label-timeline').mobiscroll().select({
        inputElement: document.getElementById('time-label-timeline-input'),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        maxWidth: 80,
        onChange: function (args) {
            calendarTimeLabelStep =  args.value;

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    $eventListToggle.change(function(e) {
        calendarEventList = e.target.checked;

        onEventListToogle()
    })

    $weekNumbersSetting.change(function(e) {
        calendarWeekNumbers = e.target.checked;
        
        updateCalendarSettings()
    })


    $(document).on('click', '.btn-config', function(e) {

        settingsDaysFromSelect.setVal(calendarStartDay)
        settingsDaysToSelect.setVal(calendarEndDay)
        settingsHoursFromSelect.setVal(calendarStartTime)
        settingsHoursToSelect.setVal(calendarEndTime)
        settingsTimeScaleToSelect.setVal(calendarTimeCellStep)
        settingsTimeLabelsSelect.setVal(calendarTimeLabelStep)

        $weekNumbersSetting.mobiscroll('getInst').checked = calendarWeekNumbers;
    
        settingsPopup.setOptions({
            anchor: e.target,
            headerText: 'Calendar settings',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Save',
                keyCode: 'enter',
                handler: function () {
                    updateCalendarSettings();
                }
            }]
        });

        settingsPopup.open()
    })

    function onEventListToogle() {
        if(calendar_view_val === 'year') {
            $hoursSettingContainer.css('display', 'none');
            $timeLabelTimelineContainer.css('display', 'none');
            $timescaleToSettingContainer.css('display', 'none');
        } else {
            $hoursSettingContainer.css('display', 'block');
            $timeLabelTimelineContainer.css('display', 'block');
            $timescaleToSettingContainer.css('display', 'block');

            if(calendarEventList) {
                $hoursSettingContainer.addClass('disabled-cont');
                $timeLabelTimelineContainer.addClass('disabled-cont');
                $timescaleToSettingContainer.addClass('disabled-cont');
            } else {
                $hoursSettingContainer.removeClass('disabled-cont');
                $timeLabelTimelineContainer.removeClass('disabled-cont');
                $timescaleToSettingContainer.removeClass('disabled-cont');
            }
        }
    }

    function updateCalendarSettings() {
        var view_parts = calendar_view_val.split('_');
        var calendar_type = view_parts[0];
        var calendar_view_type = view_parts[1] ;
        
        var options = {};

        if(calendar_type === 'schedule') {
            
            if(calendar_view_type === 'month') {
                options = {
                    schedule: {
                        labels: true
                    }
                }
            } else {
                options = {
                    schedule: {
                        type: calendar_view_type
                    }
                }
            }
        } else if(calendar_type === 'timeline') {

            options = {
                timeline: {
                    type: calendar_view_type,
                    startDay: calendarStartDay,
                    endDay: calendarEndDay,
                    weekNumbers: calendarWeekNumbers
                }
            };

            if(calendarEventList) {
                options.timeline.eventList = true;
            } else {
                options.timeline.startTime = calendarStartTime;
                options.timeline.endTime = calendarEndTime;
                options.timeline.timeCellStep = calendarTimeCellStep;
                options.timeline.timeLabelStep = calendarTimeLabelStep;
            }
        } else {
            options = {
                calendar: { type: 'week' },
                agenda: { type: 'week' }
            };
        }

        calendar.setOptions({
            view: options
        })
    }
})(jQuery);