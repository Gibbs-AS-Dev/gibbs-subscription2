window.calendar = '';


(function ($) {

    jQuery("body").addClass("mobiscroll_body");
    jQuery("body").addClass("mobiscroll_calender");
    jQuery("body").addClass("admin_calender_body");

    function getMonthDays(month) {
        var values = [];
        for (var i = 1; i <= MAX_MONTH_DAYS[month - 1]; i++) {
            values.push(i);
        }
        return values;
    }

    var allcalenderrs = [];

    function checkSync(event){

        $return = true;
        let calIds = [];

        if(event.google_cal_data != undefined && event.google_cal_data["google_cal_id"] != undefined){
            if(googleCalendarSync && googleCalendarSync.isSignedIn()){
                if(calendarData && calendarData[event.google_cal_data["google_cal_id"]] != undefined){
                   return true;
                }else{
                    showToastMessage("Google calender id not match! please login with match google account","error");
                    $return = false;
                }
            }else{
                showToastMessage("Google calender not login!","error");
                $return = false;
            }
        }

        return $return;

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
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' days') : 'Daglig';
                break;
            case 'weekly':
                var weekDays = recurrenceWeekDays.split(',');
                var weekDaysText = weekDays.map(function (weekDay) {
                    return DAY_NAMES[DAY_NAMES_MAP[weekDay]];
                }).join(', ');
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' weeks') : 'Ukentlig';
                text += ' hver ' + weekDaysText;
                break;
            case 'monthly':
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' months') : 'Månedlig';
                text += ' on day ' + recurrenceDay;
                break;
            case 'yearly':
                text = recurrenceInterval > 1 ? ('Every ' + recurrenceInterval + ' years') : 'Årlig';
                text += ' on ' + MONTH_NAMES[recurrenceMonth - 1] + ' ' + recurrenceDay;
                break;
        }

        switch (recurrenceCondition) {
            case 'until':
                text += ' Til den ' + mobiscroll.util.datetime.formatDate('MMMM D, YYYY', new Date(recurrenceUntil));
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
        var ordinal = { 1: 'første', 2: 'andre', 3: 'tredje', 4: 'fjerde', 5: 'femte' };
        var data = [
            { value: 'norepeat', text: 'Repeteres ikke' },
            { value: 'daily', text: 'Daglig' },
            { value: 'weekly', text: 'Ukentlig hver' +' ' + weekDay },
            { value: 'monthly-pos', text: 'Månedlig hver  ' + ordinal[weekNr] + ' ' + weekDay },
            { value: 'yearly', text: 'Årlig'+ ' ' + monthDay +' ' + month  },
            { value: 'weekday', text: 'Hver ukedag (Man-Fre)' },
            { value: 'custom', text: 'Egendefinert' }
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
        /*eventStartEndPicker.setOptions({
            controls: allDay ? ['date'] : ['datetime'],
            responsive: allDay ? { medium: { controls: ['calendar'], touchUi: false } } : { medium: { controls: ['calendar', 'time'], touchUi: false } }
        });*/
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

    function confirmPopup(title,desc){
        mobiscroll.confirm({
            title: title,
            message: desc,
            okText: 'Ok',
            callback: function (resultConfirm) {
                if(resultConfirm){


                }
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
        reportRangePicker.navigate(event.start);
        calendar._refDate = event.start;
    }

    function updateRecurringEvent() {

        var editFromPopup = addEditPopup.isVisible();

        var updatedEvent = {
            title: eventTitle,
            wpm_client: eventClient,
            description: eventDescription,
            guest: eventGuest,
            allDay: eventAllDay,
            color: eventColor,
            start: eventStart,
            end: eventEnd,
            status: eventStatus,
            price: eventPrice,
            recurrenceId: eventId,
            gymSectionId: eventResource,
            recurrenceEditMode: recurrenceEditMode,
            resource: eventResource,
            resourceId: eventResource,
            team: '',
            gymId: '',
            repert: ''
        };

        if (recurrenceEditMode !== 'current') {
            updatedEvent.id = eventId;
            updatedEvent.recurring = getRecurrenceRule();
            updatedEvent.recurringException = eventRecurringException;

            updatedEvent.recurrenceRule = convertRecurrenceRuleToString(updatedEvent.recurring);
        }

        var result = mobiscroll.updateRecurringEvent(
            originalRecurringEvent,
            eventOccurrence,
            editFromPopup ? null : newEvent,
            editFromPopup ? updatedEvent : null,
            recurrenceEditMode,
        );

        var updatedBooking = result.updatedEvent;

        updatedBooking.create_new_event = 0;

        if (result.newEvent) {
            console.log('new event --> ', result.newEvent)
            calendar.addEvent(result.newEvent);
            updatedBooking.newEvent = result.newEvent
            updatedBooking.create_new_event = 1;
        }

        console.log("recurrance mode")

        if (updatedEvent.id === 'mbsc_1') {
            updatedEvent.id = eventId;
        }
        calendar.updateEvent(updatedBooking);

        if (updatedBooking.resource !== eventResource) {
            updatedBooking.sectionResourcesId = eventResource;
            updatedBooking.gymSectionId = eventResource;
            updatedBooking.resourceId = eventResource;
            updatedBooking.resource = eventResource;
        }

        updatedBooking.start = eventStart;
        updatedBooking.end = eventEnd;
        updatedBooking.wpm_client = eventClient;

        if (updatedBooking.recurringException && recurrenceEditMode === 'current') {
            updatedBooking.recurrenceException = updatedBooking.recurringException.map(function (date) {
                return moment(date).format('YYYY-MM-DD');
            });
        } else {
            updatedBooking.recurrenceException = [];
        }

        updatedBooking.recurrenceEditMode = recurrenceEditMode;

        update_booking(updatedBooking);
    }

    function deleteRecurringEvent() {

        switch (recurrenceEditMode) {
            case 'following':
                originalRecurringEvent.recurring.until = moment(eventStart).format('YYYY-MM-DD');

                calendar.updateEvent(originalRecurringEvent);

                originalRecurringEvent.recurrenceRule = convertRecurrenceRuleToString(originalRecurringEvent.recurring);
                originalRecurringEvent.recurrenceEditMode = 'following';

                // Prevent editing until
                originalRecurringEvent.event_action = 'delete';

                delete originalRecurringEvent['recurrenceException'];

                update_booking(originalRecurringEvent);
                break;
            case 'all':
                calendar.removeEvent(originalRecurringEvent);

                originalRecurringEvent.recurrenceEditMode = recurrenceEditMode;

                delete_booking(originalRecurringEvent);

                break;
            default:
                eventRecurringException.push(moment(eventStart).format('YYYY-MM-DD'));

                originalRecurringEvent.recurringException = eventRecurringException;
                calendar.updateEvent(originalRecurringEvent);

                originalRecurringEvent.recurrenceException = originalRecurringEvent.recurringException.map(function (date) {
                    return moment(date).format('YYYY-MM-DD');
                });

                // Prevent creating another event on the API
                originalRecurringEvent.event_action = 'delete';

                update_booking(originalRecurringEvent);
        }
    }

    jQuery(document).on("click",".copy_text",function(){


        var aux = document.createElement("div");
        aux.setAttribute("contentEditable", true);
        aux.innerHTML = jQuery(this).parent().find("input").val();
        aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)"); 
        document.body.appendChild(aux);
        aux.focus();
        document.execCommand("copy");
        document.body.removeChild(aux);

        showToastMessage("Linken har blitt kopiert!","success");


    })

    async function getEventDataFromAjax(event){
      debugger;
    }

    // Fills the popup with the event's data
    function fillPopup(event) {


        




        // Load event properties
        accessCode = event.access_code;
        eventId = event.id;
        eventTitle = event.title;
        eventListing = event.listing;
        eventStatus = event.status;
        eventPrice = event.price;
        eventDescription = event.description || '';
        eventGuest = event.amount_guest || 1;
        phoneNumber = event.phone_number || 1;
        eventAllDay = event.allDay;
        eventStart = event.start;
        eventEnd = event.end;
        eventColor = event.color;
        eventRecurringException = event.recurringException || [];
        eventRecurrence = getEventRecurrence(event);
        eventClient = event.client ? event.client.value : event.wpm_client;
        eventTeam = event.team ? event.team.value : event.wpm_team;
        eventResource = event.resource;
        google_cal_data = event.google_cal_data;
        outlook_cal_data = event.outlook_cal_data;

        if(typeof accessCode != "undefined" && accessCode != ""){
            jQuery('.access_code_div').show();
            jQuery('.access_code').val(accessCode);
        }else{
            jQuery('.access_code_div').hide();
            jQuery('.accessCode').val("");
        }


        var optionss = "";
        if(eventStatus && eventStatus == "expired"){
           optionss += "<option value='expired'>Utløpt</option>";
        }else if(eventStatus && eventStatus == "payment_failed"){
            optionss += "<option value='payment_failed'>Ufullført bestilling</option>";
        }else if(eventStatus && eventStatus == "pay_to_confirm"){

            optionss += "<option value='pay_to_confirm'>Bestillingen pågår</option>";

        }else if(eventStatus && eventStatus == "cancelled"){
            optionss += "<option value='cancelled'>Kansellert</option>";
        }else if(eventStatus && eventStatus == "closed"){
            optionss += "<option value='closed'>Stengt</option>";
        }else if(eventStatus && eventStatus == "waiting"){
            optionss += "<option value='waiting'>Reservasjon </option>";
            optionss += "<option value='confirmed'>Godkjent </option>";
            optionss += "<option value='manual_invoice'> Faktura </option>";
            optionss += "<option value='paid'>Betalt </option>";
            optionss += "<option value='cancelled'>Kansellert </option>";
        }else if(eventStatus && eventStatus == "confirmed"){
            optionss += "<option value='confirmed'>Godkjent </option> ";
            optionss += "<option value='paid'>Betalt </option>";
            optionss += "<option value='sesongbooking'>Sesongbooking </option>";
            optionss += "<option value='cancelled'>Kansellert</option> ";
        }else if(eventStatus && eventStatus == "paid"){
            optionss += "<option value='paid'>Betalt</option>";
            optionss += "<option value='cancelled'>Kansellert</option>";
        }else if(eventStatus && eventStatus == "sesongbooking"){
            optionss += "<option value='sesongbooking'>Sesongbooking </option>";
            optionss += "<option value='cancelled'>Kansellert </option>";
        }else if(eventStatus && eventStatus == "manual_invoice"){
            optionss += "<option value='manual_invoice'>Faktura</option>";
            optionss += "<option value='cancelled'>Kansellert </option>";
        }else{
            optionss += "<option value=''>Velg status</option>";
            optionss += "<option value='waiting'>Reservasjon</option>";
            optionss += "<option value='confirmed'>Godkjent</option>";
            optionss += "<option value='manual_invoice'> Faktura</option>";
            optionss += "<option value='paid'>Betalt</option>";
            optionss += "<option value='closed'>Stengt</option>";
        }
        jQuery("#wpm-status").html(optionss);

       




        if(eventTitle  == "New event"){
            eventTitle = "Ingen tittel";
        }

        if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("event_title")){
            jQuery("#eventForm").find("#event-title").show()
        }else{
            jQuery("#eventForm").find("#event-title").hide()
        }

        if(eventTitle != undefined){
           eventTitleSelect.val(eventTitle);
        }else{
           eventTitleSelect.val("");
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
        jQuery(".paylink_main").find(".payment_plus").hide();
        jQuery("#bk_price").hide();
        jQuery(".price_plus").show();
       // jQuery(".payment_plus").show();
        jQuery(".paylink_main_inner").hide();

        jQuery("#bk_price").removeAttr("readonly");

        refund_price.val("");
        jQuery(".all_refund_data").html("");

        if(event.refund_data && Array.isArray(event.refund_data) && event.refund_data.length > 0){


            var all_refund_data = jQuery(".all_refund_data");

            event.refund_data.forEach(function(refund_data){
                let refund_datee = moment(refund_data.date).format("DD MMMM YYYY");
                let refund_time = moment(refund_data.date).format("HH:mm");
                all_refund_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Refundert:</b> <span>'+refund_data.price+'  <br> '+refund_datee+' | '+refund_time+'</span> '+
                    /*  '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ */
                '</p>')
            })
            
        }

        //debugger;


        

        if(eventPrice != undefined){
           bk_price.val(eventPrice);
           
           if(eventPrice > 0){

              jQuery(".paylink_main").find(".payment_plus").show();
              jQuery("#bk_price").show();

              jQuery(".price_plus").hide();
              jQuery(".payment_plus").hide();
              jQuery(".paylink_main_inner").show();

           }
           if(eventStatus == "confirmed" || eventStatus == "waiting"){

           }else{
              jQuery("#bk_price").attr("readonly",true);
           }
        }else{
            bk_price.val("");
        }

        jQuery(".refund_main_inner").hide();
        jQuery(".refund_plus").show();
        jQuery("#refund_price_used").val("");
        jQuery(".refund_main_inner_refunded").hide();


        jQuery(".paylink_main").find(".paylink_main_inner").find(".link_gen_span").show();
        jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("");

        if(event.order_id && event.order_id != undefined && event.order_id != "" && event.order_id > 0 && event.price > 0 && event.status != "paid"){
           // debugger;

            //jQuery(".paylink_main").show();
            jQuery(".paylink_main").find(".paylink_main_inner").find(".link_gen_span").hide();
            //jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("<span>"+event.payment_url+"</span><i class='fa fa-copy copy_text'></i>");
            jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("<button type='button' class='showpaylink' order_id='"+event.order_id+"'>Vis betalingslenke</button>");

        }
        jQuery(".rec_divv").hide();

        jQuery("#repeter_switch").mobiscroll('getInst').checked = false;
        jQuery("#sendmail").mobiscroll('getInst').checked = false;
        jQuery("#sendmail")[0].checked = false;
        sendmail = false;

        

        jQuery(".send_mail_switch_label").hide();

        if(eventStatus && (eventStatus == "confirmed" || eventStatus == "paid" || eventStatus == "cancelled")){
           jQuery(".send_mail_switch_label").show();
        }
        jQuery(".refund_main").hide();

        jQuery(".refund_plus").html('<i class="fa fa-plus"></i> Refunder beløp');

        if(event.order_id && event.order_id != undefined && event.order_id != "" && event.order_id > 0){
            jQuery(".refund_plus").attr("order_id",event.order_id);
        }else{
            jQuery(".refund_plus").attr('order_id','');
        }
        

        if(eventStatus && (eventStatus == "close" || eventStatus == "closed" || eventStatus == "paid" || eventStatus == "sesongbooking" || eventStatus == "cancelled" || eventStatus == "manual_invoice" || eventStatus == "waiting")){
           jQuery(".paylink_main").hide();

           //debugger;


           if(event.charge_id && event.charge_id != ""){
             jQuery(".refund_main").show();
           }

           
           //debugger;
        }else{
            jQuery(".paylink_main").show();
        }

        // if(event.refund_data && event.refund_data.price && event.refund_data.price != undefined){

        //     jQuery(".refund_main").show();
        //     jQuery(".refund_main_inner").hide();
        //     jQuery(".refund_plus").hide();
        //     jQuery(".refund_main_inner_refunded").show();

        //     var price_number = event.refund_data.price_number;
        //     price_number = price_number.replace("-","");

        //     jQuery("#refund_price_used").val(price_number);
            
        // }

        

        repeterSwitchSelect.checked = true;

        if(event.listing && event.listing != undefined){

            if(event.recurrenceRule && event.recurrenceRule != ""){
                jQuery(".rec_divv").show();
                jQuery("#repeter_switch").mobiscroll('getInst').checked = true;
            }

            jQuery(".send_mail_switch_label").hide();

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
        if(eventClient && eventClient != undefined && eventClient != 0){
            eventClientSelect.setVal(eventClient);
        }else{
            eventClientSelect.setVal("");
        }

        /*if(eventStatus && eventStatus != undefined){
            eventStatusSelect.setVal(eventStatus);
        }*/
        
        $('.popup-event-description').val(eventDescription);
        $('.popup-event-guest').val(eventGuest);
        $eventAllDay.mobiscroll('getInst').checked = eventAllDay;
        eventStartEndPicker.setVal([eventStart, eventEnd]);
        eventRecurrenceSelect.setOptions({ data: getRecurrenceTypes(eventStart, eventRecurrence) });
        eventRecurrenceSelect.setVal(eventRecurrence);
        toggleDatetimePicker(eventAllDay);
        toggleRecurrenceEditor(eventRecurrence);

        // Set custom recurring rule field values
        $recurrenceInterval.val(recurrenceInterval);
        $recurrenceCount.val(recurrenceCount);
        recurrenceMonthSelect.setVal(recurrenceMonth);
        recurrenceDaySelect.setVal(recurrenceDay);
        recurrenceMonthDaySelect.setVal(recurrenceDay);
        recurrenceUntilDatepicker.setVal(recurrenceUntil);
        $('.recurrence-repeat-' + recurrenceRepeat).mobiscroll('getInst').checked = true;
        $('.recurrence-condition-' + recurrenceCondition).mobiscroll('getInst').checked = true;
        $recurrenceWeekDays.each(function () {
            $(this).mobiscroll('getInst').checked = recurrenceWeekDays.includes(this.value);
        });

        //get_booking_by_user(eventClient,eventTeam);

        toggleRecurrenceText(recurrenceRepeat);
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

        
    }
    jQuery(document).on("click",".price_plus",function(){

        jQuery("#bk_price").fadeIn();
        jQuery(this).fadeOut();

    });
    jQuery(document).on("click",".refund_plus",function(){

        jQuery(this).html("loading...");

        let _that_refund = this;

        let datas = {
            "action": "check_refund",
            "order_id": jQuery(this).attr("order_id")
        };
        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: datas,
            success: function(resultData) {
                //debugger;
                if (typeof resultData.data["refund_all"] !== "undefined" && resultData.data["refund_all"] == "true") {

                    jQuery(".refund_main").show();
                    jQuery(".refund_main_inner").hide();
                    jQuery(".refund_plus").hide();
                    jQuery(".refund_main_inner_refunded").show();

                    var price_number = resultData.data["price_number"];
                    price_number = price_number.replace("-","");

                    jQuery("#refund_price_used").val(price_number);
                    
                }else if (typeof resultData.data["add_refund"] !== "undefined" && resultData.data["add_refund"] == "true") {

                    jQuery(".refund_main_inner").fadeIn();
                    jQuery(_that_refund).fadeOut();
                    refund_price.val(resultData.data["price_number"]);
                    
                }else if (typeof resultData.data["refund_new"] !== "undefined" && resultData.data["refund_new"] == "true") {

                    jQuery(".refund_main_inner").fadeIn();
                    jQuery(_that_refund).fadeOut();
                    refund_price.val(bk_price.val());
                    
                }else{
                    jQuery(".refund_main").hide();
                }    
            },
            error: function(error) {
                //reject(error); // Reject the promise on error
            }
        });

        

    });
    jQuery(document).on("click",".payment_plus",function(){

        jQuery(".paylink_main_inner").show();
        jQuery(this).hide();

        eventStatus = "confirmed";
        eventStatusSelect.val(eventStatus);
        jQuery(".send_mail_switch_label").show();

    });
        jQuery("#customerForm").on("submit",function(){

            jQuery(".show_info_div").html('');

            let dialCode = jQuery("#customer_phone").intlTelInput("getSelectedCountryData").dialCode;
            jQuery(this).find("input[name=country_code]").val("+"+dialCode);

            jQuery(".overlay").show();
            

            $.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: jQuery(this).serialize(),
                success: function (response) {

                    jQuery(".overlay").hide();


                    if(response.success == true){
                        jQuery(".extra_customer_div").hide();
                        jQuery(".show_info_div").html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        jQuery("#customerForm")[0].reset();
                        get_customer_list(response.user_id);
                        setTimeout(function(){
                            jQuery(".show_info_div").html('');
                             //  addCustomerPopup.close();
                        },2000)
                    }else if(response.message){
                        jQuery(".show_info_div").html('<div class="alert alert-danger" role="alert">'+response.message+'</div>')
                    }
                   
                }
            });

        })

    function get_customer_list(addCustomerId = "") {


        $.ajax({
            type: "GET",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {action:"get_customer_list"},
            success: function (response) {
                jQuery(".overlay").hide();

                if(response.customer_list){

                    $listss = [];

                    response.customer_list.forEach(function(cust){
                        $listss.push({text: cust.display_name, value : cust.ID});
                    })

                    if($listss.length > 0){
                        eventClientSelect.setOptions({ data: $listss });

                        if(addCustomerId != ""){
                            setTimeout(function(){
                                $eventClient.val(String(addCustomerId))
                                eventClientSelect.setVal(String(addCustomerId));
                                addCustomerPopup.close();

                                eventClient = addCustomerId;

                            },100)
                            
                        }


                    }

                    

                }
               
            }
        });


    }

    function createAddPopup(event, target) {
        var success = false;
        
        jQuery(".tabs-event").find(".tab-event").first().click();
        jQuery(".tabs-event").hide();



        jQuery(".cal_custom_fields").html("");

        if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
            jQuery(".adult_div").show();
        }else{
            jQuery(".adult_div").hide();
        }
        
        //jQuery(".adult_div").hide();

        jQuery("#calendar-add-edit-popup").addClass("addEvent");
        jQuery("#calendar-add-edit-popup").removeClass("updateEvent");

        



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
            },
            cssClass: 'addPopup',
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Add',
                keyCode: 'enter',
                handler: function (target_d) {
                    console.log(event)
                    /*start*/

                    

                        let error = 0

                        if(eventClient == "" || eventClient == undefined){
                            eventClient = 0;
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
                        if(eventStart == ""  || eventStart == undefined){
                            jQuery("#add-event-start").parent().find("input").focus();
                            jQuery("#add-event-start").parent().addClass("required_focus");
                            error = 1;
                            return false;
                        }
                        if(eventEnd == ""  || eventEnd == undefined){
                            jQuery("#add-event-end").parent().find("input").focus();
                            jQuery("#add-event-end").parent().addClass("required_focus");
                            error = 1;
                            return false;
                        }
                        if(eventStatus == ""  || eventStatus == undefined){
                            jQuery("#wpm-status").parent().find("select").focus();
                            jQuery("#wpm-status").parent().addClass("required_focus");
                            error = 1;
                            return false;
                        }
                        if(moment(eventStart).format("YYYY-MM-DD HH:mm") == moment(eventEnd).format("YYYY-MM-DD HH:mm")){
                            showToastMessage("End date should greater then start date","danger","center");
                            error = 1;
                            return false;
                        }


                        if(error == 0){


                            if((eventPrice == "" || eventPrice == 0 || eventPrice == undefined) && eventStatus == "confirmed"){

                                /*eventStatus = "paid";
                                eventStatusSelect.val(eventStatus);*/
                                showToastMessage("since booking is free and approved, it changed to paid automatically","success");

                            }

                              

                            if(eventTitle == "New event" || eventTitle == "Ingen tittel" || eventTitle == ""){
                                if(eventClient != "" && eventClient != undefined){
                                    eventTitle = "";
                                }else{
                                    eventTitle = "";
                                }
                            }


                            var newEvent = {
                                    id: eventId,
                                    title: eventTitle,
                                    wpm_client: eventClient,
                                    listings: eventListing,
                                    description: eventDescription,
                                    guest: eventGuest,
                                    allDay: eventAllDay,
                                    start: eventStart,
                                    end: eventEnd,
                                    recurring: getRecurrenceRule(),
                                    status: eventStatus,
                                    price: eventPrice,
                                    recurrenceId: eventId,
                                    gymSectionId: eventResource,
                                    recurringException: eventRecurringException,
                                    recurrenceException: eventRecurringException,
                                    recurrenceEditMode: '',
                                    resource: eventResource,
                                    gymId: '',
                                    repert: '',
                                    sendmail: sendmail,
                                };



                               


                                newEvent.recurrenceRule = convertRecurrenceRuleToString(newEvent.recurring);

                                calendar.updateEvent(newEvent);

                                navigateToEvent(newEvent);



                                add_booking(newEvent);

                                success = true;

                                addEditPopup.close();

                        }

                    /*end*/    
                },
                cssClass: 'mbsc-add-popup-button-primary'
            }],
        });



        jQuery(".close_event_popup").on("click",function(){
            addEditPopup.close();
        })
        jQuery(document).on("click",".openCustomer",function(){
            openCustomerPopup();
        })
         fillPopup(event);
        addEditPopup.open();

        setTimeout(function(){
            eventListingSelect.setOptions({
                disabled: false,
            })
            $eventListing.parent().find("input").removeAttr("readonly")
           jQuery("#event-title").focus();
        },500)
        
       

       

        
    }

    function updateEventtData(is_logged = false){
        let error = 0

        if(eventClient == "" || eventClient == undefined){
            eventClient = 0;
            jQuery("#wpm-client").parent().find("input").focus();
            jQuery("#wpm-client").parent().addClass("required_focus");
            error = 1;
            return false;
        }
        if(eventStatus == ""  || eventStatus == undefined){
            jQuery("#wpm-status").parent().find("select").focus();
            jQuery("#wpm-status").parent().addClass("required_focus");
            error = 1;
            return false;
        }
        if(eventStart == ""  || eventStart == undefined){
            jQuery("#add-event-start").parent().find("input").focus();
            jQuery("#add-event-start").parent().addClass("required_focus");
            error = 1;
            return false;
        }
        if(eventEnd == ""  || eventEnd == undefined){
            jQuery("#add-event-end").parent().find("input").focus();
            jQuery("#add-event-end").parent().addClass("required_focus");
            error = 1;
            return false;
        }
        /*if(eventStatus == ""  || eventStatus == undefined){
              eventStatus = "waiting";
        }*/

        

        if(moment(eventStart).format("YYYY-MM-DD HH:mm") == moment(eventEnd).format("YYYY-MM-DD HH:mm")){
            showToastMessage("End date should greater then start date","danger","center");
            error = 1;
            return false;
        }

        if(error == 0){

            if((eventPrice == "" || eventPrice == 0 || eventPrice == undefined) && eventStatus == "confirmed"){

                /*eventStatus = "paid";
                eventStatusSelect.val(eventStatus);*/
                showToastMessage("since booking is free and approved, it changed to paid automatically","success");

            }

            if(eventTitle == "New event" || eventTitle == "Ingen tittel" || eventTitle == ""){
                if(eventClient != "" && eventClient != undefined && eventClient != 0){
                    eventTitle = "";
                }else{
                    eventTitle = "Ingen tittel";
                }
            }
            if(eventListing == ""  || eventListing == undefined || eventListing.length < 0){
                jQuery("#wpm-listing").parent().find("input").focus();
                jQuery("#wpm-listing").parent().addClass("required_focus");
                error = 1;
                return false;
            }
                if (originalRecurringEvent) {
                    createRecurrenceEditPopup(false);
                } else {
                   
                    //console.log(eventStatus);
                    var updatedEvent = {
                        id: eventId,
                        refund: refund_price.val(),
                        title: eventTitle,
                        wpm_client: eventClient,
                        team: eventTeam,
                        description: eventDescription,
                        guest: eventGuest,
                        allDay: eventAllDay,
                        start: eventStart,
                        end: eventEnd,
                        recurring: getRecurrenceRule(),
                        status: eventStatus,
                        price: eventPrice,
                        recurrenceId: eventId,
                        gymSectionId: eventResource,
                        recurringException: eventRecurringException,
                        recurrenceEditMode: '',
                        resource: eventResource,
                        gymId: '',
                        repert: '',
                        sendmail: sendmail,
                        google_cal_data:google_cal_data,
                        outlook_cal_data:outlook_cal_data,
                        is_logged:is_logged,
                    };
                    updatedEvent.recurrenceRule = convertRecurrenceRuleToString(updatedEvent.recurring);

                    if(event.first_event_id && event.first_event_id != ""){
                       createLinkEditPopup(updatedEvent);
                    }else{   
                        
                        update_booking(updatedEvent);

                        calendar.updateEvent(updatedEvent);
                        navigateToEvent(updatedEvent);
                        
                    }    
                    addEditPopup.close();
                }

        }
    }

    const fetchCustomAccessCodeData = async (listing_id,booking_data) => {
        let datas = {
            "action": "getEventAjaxData",
            "listing_id": listing_id,
            "booking_id": booking_data
        };

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: datas,
                success: function (resultData) {
                    resolve(resultData);
                },
                error: function (error) {
                    reject(error);
                }
            });
        });
    };

   

    async function createEditPopup(event, target) {

        tooltip.close();
        // Show delete button inside edit popup
        jQuery(".cal_custom_fields").html("");

        if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
            jQuery(".adult_div").show();
        }else{
            jQuery(".adult_div").hide();
        }

        

        

        

        jQuery("#calendar-add-edit-popup").addClass("updateEvent");
        jQuery("#calendar-add-edit-popup").removeClass("addEvent");
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
                handler: async function (target_d) {
                    /*start*/
                    console.log(event)
                    if(refund_price.val() != ""){
                        let deleteConfirm = mobiscroll.confirm({
                            title: 'Er du sikker på at du ønsker å refundere?',
                            message: 'Det kan ta 1-5 virkedager før kunden får pengene på sin konto',
                            okText: 'Ja',
                            cancelText: 'Nei',
                            anchor: target, 
                            callback: function (resultConfirm) {
                                if(resultConfirm){
            
                                   updateEventtData()
            
                                }
                            }
                        });
                        console.log(deleteConfirm)
                    }else{
                        jQuery("#calendar-add-edit-popup").find(".pop-new").remove();
                        jQuery("#calendar-add-edit-popup").prepend('<div class="popup-loader pop-new" style="position: absolute;width: 100%;height: 100vh;z-index: 9999;display: flex;justify-content: center;align-items: center;background: #a39c9c00;margin: 0;padding: 0}"><div class="loaderdivv"></div></div>')
                        const customData = await fetchCustomAccessCodeData(event.resourceId,event.id);
                        jQuery("#calendar-add-edit-popup").find(".pop-new").remove();
                        var hasChangeAccess = false;
                        if (typeof customData.data["access_code"] !== "undefined") {

                            if(event.start != eventStart || event.end != eventEnd){
                                hasChangeAccess = true;
                            }
                            if(event.client && event.client.value != eventClient){
                                hasChangeAccess = true;
                            }
                            
                        }
                        
                        if(hasChangeAccess){

                            let accessConfirm = mobiscroll.confirm({
                                title: 'Er du sikker?',
                                message: 'Ved å endre tid, vil tilgangskoden endres og sendes på nytt til kunden.',
                                okText: 'Ja',
                                cancelText: 'Nei',
                                anchor: target, 
                                callback: function (resultConfirm) {
                                    if(resultConfirm){
                                        
                                       updateEventtData(true)
                
                                    }
                                }
                            });

                        }else{
                           updateEventtData();
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
        jQuery(document).on("click",".openCustomer",function(){
            openCustomerPopup();
        })

        $eventClient.change();

        var booking_id = eventId;

        jQuery(".cal_custom_fields").html("");

        jQuery(".cal_custom_fields").append('<div class="popup-loader"><div class="loaderdivv"></div></div>')
       

        let datas = {
           "action" : "get_custom_fields_for_calender_mobiscroll",
           "listing_id" : eventResource,
           "booking_id" : booking_id,
           "cal_type" : "",
           "cal_view" : "",
        }
        jQuery.ajax({
              type: "POST",
              url: WPMCalendarV2Obj.ajaxurl,
              data: datas,
              success: function(resultData){
                  jQuery(".cal_custom_fields").html("");
                  
                  jQuery(".cal_custom_fields").html(resultData);
                  jQuery(".mbsc-popup-content").addClass("scroller")
              }
        });
        fillPopup(event);

        jQuery(".tabs-event").show();
        jQuery(".tabs-event").find(".tab-event").first().click();
        userInfoShow();
        

        

        addEditPopup.open();

        if(event.status == "pay_to_confirm"){
            setTimeout(function(){

                jQuery("#calendar-add-edit-popup").before(
                    `<div class="info-top-pay_to_confirm">
                        <p>Bestillingen pågår. Tiden er reservert for kunden i opptil 30 minutter fra bestillingstidspunktet.</p>
                    </div>`
                );

            },500)
        }
        

        eventListingSelect.setOptions({
            disabled: true,
        })

        setTimeout(function(){
           $eventListing.parent().find("input").attr("readonly",true)
        },500)

       

        //fillPopup(event);
      //  jQuery("#wpm-client").change();


        //ajaxCallEvent(event);
       


    }

    function ajaxCallEvent(event) {

        data = {
            action: "wpm_get_booking_info",
        }
        $.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl + "?booking_id=" + event.id,
            data: data,
            success: function (response) {
                $("#calendar-edit-popup").html(response)

                repeatPopUp = $('#event-repeat-popup').mobiscroll().popup({

                    fullScreen: true,
                    showOverlay: false,
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

    jQuery(document).on("click",".showpaylink",function(){
        let datas = {
            "action": "getPayLink",
            "order_id": jQuery(this).attr("order_id")
        };
        jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("Loading...");

        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: datas,
            success: function(resultData) {
                if (typeof resultData.data["payment_url"] !== "undefined" && resultData.data["payment_url"] != "") {
                    jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("<input type='text' value='"+resultData.data["payment_url"]+"'><i class='fa fa-copy copy_text'></i>");
                }else{
                    jQuery(".paylink_main").find(".paylink_main_inner").find(".payment_url").html("");
                }    
            },
            error: function(error) {
                //reject(error); // Reject the promise on error
            }
        });
    });

    async function getEventAjaxData(event) {
        let datas = {
            "action": "getEventAjaxData",
            "listing_id": event.resourceId,
            "booking_id": event.id
        };

        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: datas,
            success: function(resultData) {
                let left_info_data = jQuery("#calendar-event-tooltip-popup").find(".left_info_data");

                if (typeof resultData.data["access_code"] !== "undefined") {
                    left_info_data.append('<p class="tooltip-custom customer_div">' +
                        '<b>Adgangskode:</b> <span>' + resultData.data["access_code"] + '</span>' +
                        '</p>');
                }       
            },
            error: function(error) {
                //reject(error); // Reject the promise on error
            }
        });
    }


   async function showEventSummary(event, target) {
    //showLoader();
      //  console.log(event)
        getEventAjaxData(event);
       // hideLoader();

       

       
        let left_info_data = jQuery("#calendar-event-tooltip-popup").find(".left_info_data");
        let right_info_data = jQuery("#calendar-event-tooltip-popup").find(".right_info_data");

        left_info_data.html("");
        right_info_data.html("");


        /* left side*/
        if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("event_title")){
            left_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<h4><b>'+event.title+'</b></h4>'+
                '</p>')
        }

        if(event.id && event.id != ""){
            jQuery("#calendar-event-tooltip-popup").find(".topAction").attr("data-event_id",event.id);
            let first_event_text = "";



            if(event.first_event_id && event.first_event_id != "" && event.first_event_id != event.id){
                first_event_text = '<span><svg width="1em" height="1em" viewBox="0 0 55 43" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M15.1445 23.8044H13.8945C10.3789 23.8044 7.64453 21.0701 7.64453 17.5544C7.64453 14.1169 10.3789 11.3044 13.8945 11.3044H26.3945C29.832 11.3044 32.6445 14.1169 32.6445 17.5544C32.6445 21.0701 29.832 23.8044 26.3945 23.8044H25.3008C25.2227 24.1951 25.1445 24.6638 25.1445 25.0544C25.1445 26.8513 26.2383 28.2576 27.8789 28.7263C33.3477 27.9451 37.6445 23.2576 37.6445 17.5544C37.6445 11.3826 32.5664 6.30444 26.3945 6.30444H13.8945C7.64453 6.30444 2.64453 11.3826 2.64453 17.5544C2.64453 23.8044 7.64453 28.8044 13.8945 28.8044H15.6133C15.3008 27.6326 15.1445 26.3826 15.1445 25.0544C15.1445 24.6638 15.1445 24.2732 15.1445 23.8044ZM41.3945 13.8044H39.5977C39.9102 15.0544 40.1445 16.3044 40.1445 17.5544C40.1445 18.0232 40.0664 18.4138 40.0664 18.8044H41.3945C44.832 18.8044 47.6445 21.6169 47.6445 25.0544C47.6445 28.5701 44.832 31.3044 41.3945 31.3044H28.8945C25.3789 31.3044 22.6445 28.5701 22.6445 25.0544C22.6445 21.6169 25.3789 18.8044 28.8945 18.8044H29.9102C29.9883 18.4138 30.1445 18.0232 30.1445 17.5544C30.1445 15.8357 28.9727 14.4294 27.332 13.9607C21.8633 14.7419 17.6445 19.4294 17.6445 25.0544C17.6445 31.3044 22.6445 36.3044 28.8945 36.3044H41.3945C47.5664 36.3044 52.6445 31.3044 52.6445 25.0544C52.6445 18.8826 47.5664 13.8044 41.3945 13.8044Z" fill="currentColor"/></svg>'+""+event.first_event_id+"</span>";
            }
            if(event.order_id && event.order_id != ""){
                left_info_data.append('<p class="tooltip-custom customer_div">'+
                        '<b>Ordre ID:</b> <span>'+event.order_id+'</span> '+
                       /*  '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ */
                    '</p>')
            }
           /*  left_info_data.append('<p class="tooltip-custom customer_div">'+
                         '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ 
                    '</p>') */

            if (event.price && event.price != "" && event.price !== "0") {
                // Only append the price information if the price is neither empty nor "0"
                left_info_data.append('<p class="tooltip-custom customer_div">'+
                        '<b>Pris:</b> <span>'+event.price+'</span> '+
                        '</p>');
            }

            if(event.refund_data && Array.isArray(event.refund_data) && event.refund_data.length > 0){

                event.refund_data.forEach(function(refund_data){
                    let refund_datee = moment(refund_data.date).format("DD MMMM YYYY");
                    let refund_time = moment(refund_data.date).format("HH:mm");
                    left_info_data.append('<p class="tooltip-custom customer_div">'+
                        '<b>Refundert:</b> <span>'+refund_data.price+'  <br> '+refund_datee+' | '+refund_time+'</span> '+
                        /*  '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ */
                    '</p>')
                })
                
            }

            // if(event.refund_data && event.refund_data.price && event.refund_data.price != undefined){
            //     let refund_datee = moment(event.refund_data.date).format("DD MMMM YYYY");
            //     let refund_time = moment(event.refund_data.date).format("HH:mm");
            //     left_info_data.append('<p class="tooltip-custom customer_div">'+
            //     '<b>Refundert:</b> <span>'+event.refund_data.price+'  <br> '+refund_datee+' | '+refund_time+'</span> '+
            //     /*  '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ */
            //    '</p>')
            // }
            // if (typeof ajaxEventData["refund_data"] !== "undefined" && typeof ajaxEventData["refund_data"].price !== "undefined") {
            //     let refund_datee = moment(ajaxEventData["refund_data"].date).format("DD MMMM YYYY");
            //     let refund_time = moment(ajaxEventData["refund_data"].date).format("HH:mm");
            //     left_info_data.append('<p class="tooltip-custom customer_div">' +
            //         '<b>Refundert:</b> <span>' + ajaxEventData["refund_data"].price + '  <br> ' + refund_datee + ' | ' + refund_time + '</span>' +
            //         '</p>');
            // }
             
               
             
                    
            if(event.access_code && event.access_code != ""){
                left_info_data.append('<p class="tooltip-custom customer_div">'+
                        '<b>Adgangskode:</b> <span>'+event.access_code+'</span> '+
                        /*  '<b>Booking ID:</b> <span>'+event.id+'</span> '+first_event_text+ */
                    '</p>')
            }
            // if (typeof ajaxEventData["access_code"] !== "undefined") {
            //     left_info_data.append('<p class="tooltip-custom customer_div">' +
            //         '<b>Adgangskode:</b> <span>' + ajaxEventData["access_code"] + '</span>' +
            //         '</p>');
            // }        
        }

       
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
                    '<b>Tildelte timer:</b> <span>'+event.app_data.sum_received_hours+'</span>'+
                '</p>')
        } */


        left_info_data.append('<hr />');

        if(event.extra_info){
            if(event.extra_info.age_group && event.extra_info.age_group != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("age_group")){
               left_info_data.append('<p class="tooltip-custom age_group_div">'+
                    '<b>Age group:</b> <span>'+event.extra_info.age_group+'</span>'+
                '</p>')
            }
            if(event.extra_info.sport && event.extra_info.sport != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("sport")){
               left_info_data.append('<p class="tooltip-custom sport_div">'+
                    '<b>Sport:</b> <span>'+event.extra_info.sport+'</span>'+
                '</p>')
            }
            if(event.extra_info.members && event.extra_info.members != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("members")){
               left_info_data.append('<p class="tooltip-custom members_div">'+
                    '<b>Members:</b> <span>'+event.extra_info.members+'</span>'+
                '</p>')
            }
            if(event.extra_info.team_name && event.extra_info.team_name != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("team_name")){
               left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Team:</b> <span>'+event.extra_info.team_name+'</span>'+
                '</p>')
            }
            if(event.extra_info.team_level && event.extra_info.team_level != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("level")){
               left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Team:</b> <span>'+event.extra_info.team_level+'</span>'+
                '</p>')
            }
            if(event.extra_info.type && event.extra_info.type != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("type")){
                left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>Team:</b> <span>'+event.extra_info.type+'</span>'+
                '</p>')
            }
        }
        if(event.service_data != undefined && Array.isArray(event.service_data) && event.service_data.length > 0){
            event.service_data.forEach(function(service){
                left_info_data.append('<p class="tooltip-custom Team_div">'+
                    '<b>'+service.service.name+'</b> <span>Antall='+service.countable+',</span>'+
                    '<span>Pris='+service.price+'</span>'+
                '</p>')
            })
        }

        left_info_data.append('<p class="tooltip-custom custom_fields_dd_main"><div class="tooltip-loader"><div class="loaderdivv"></div></div></p>')

        let datas = {
           "action" : "get_custom_fields_for_calender_mobiscroll",
           "listing_id" : event.resourceId,
           "booking_id" : event.id,
           "cal_type" : "",
           "cal_view" : "",
           "get_type" : "info_data",
        }
        jQuery.ajax({
              type: "POST",
              url: WPMCalendarV2Obj.ajaxurl,
              data: datas,
              success: function(resultData){

                jQuery(".tooltip-loader").remove();

                  left_info_data.append('<p class="tooltip-custom custom_fields_dd_main"><div class="custom_fields_dd">'+
                        resultData +
                    '</p>')

                  /*jQuery(".cal_custom_fields").html("");
                  jQuery(".cal_custom_fields").html(resultData);*/
              }
        });

        /* end left side*/

        /* right side */

        
        

        if(event.customer && event.customer != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("customer_name")){
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Kunde:</b> <span>'+event.customer+'</span>'+
                '</p>')
        }
        if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Antall:</b> <span>'+event.amount_guest+'</span>'+
                '</p>')
        }
        if(event.start  && event.start != ""){
            let printDate2 = "";

            if(moment(event.start).format("YYYY-MM-DD") == moment(event.end).format("YYYY-MM-DD")){
                printDate2 = moment(event.start).format("MMMM DD,YYYY, HH:mm")+" - "+moment(event.end).format("HH:mm");
            }else{
                printDate2 = moment(event.start).format("MMMM DD,YYYY, HH:mm")+" - "+moment(event.end).format("MMMM DD,YYYY, HH:mm");
            }

            right_info_data.append('<p class="tooltip-custom time_div">'+
                    '<b>Tid:</b> <span>'+printDate2+'</span>'+
                '</p>')
        }


        
        eventStart = event.start;
        eventRecurrence = getEventRecurrence(event);

        

        if(eventRecurrence != "" && eventRecurrence != undefined &&  eventRecurrence != "norepeat"){

            fillPopup(event);

            var recces = getRecurrenceTypes(eventStart, eventRecurrence);

            var Rec_value = eventRecurrence;
            
            recces.forEach(function(recc){

                if(recc.value == eventRecurrence){
                    Rec_value = recc.text;
                }

            })


            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Repeterer:</b> <span>'+Rec_value+'</span>'+
                '</p>')
        }

        if(event.comment && event.comment != ""){
            try {
                let commentData = JSON.parse(event.comment);
                if(commentData.message && commentData.message != ""){
                    right_info_data.append('<p class="tooltip-custom customer_div">'+
                            '<b>Kommmentar</b> <span>'+ commentData.message +'</span>'+
                        '</p>')
                }
            } catch (e) {
               // return false;
            }
            
        }
        if(event.description && event.description != ""){
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Notat:</b> <span>'+ event.description +'</span>'+
                '</p>')
        }

       // debugger;

        if(event.created_date && event.created_date != undefined){
            var created_date = moment(event.created_date).format("MMMM DD,YYYY, HH:mm");
            right_info_data.append('<p class="tooltip-custom customer_div">'+
                    '<b>Opprettet:</b> <span>'+created_date+'</span>'+
                '</p>')
        }

        

        tooltip.setOptions({ 
            anchor: target,
            cssClass: 'mbsc-tooltip-button-primary'
        });

        tooltip.open();

        // Bind edit event
        $(document).on('click', ".mbsc-tooltip-button-primary .view-more", function () {

            if(event.id == jQuery(this).parent().attr("data-event_id")){
                createEditPopup(event, target)

                tooltip.close();
            }
        });

        $('.tooltip-close').on('click', function () {
            tooltip.close();
        });

       
    }


    function deleteEventFunc(event, target){

            let deleteConfirm = mobiscroll.confirm({
                title: 'Slett hendelse?',
                message: 'Er du sikker på at du vil slette?',
                okText: 'Ja',
                cancelText: 'Avbryt',
                anchor: target, 
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
    }

    function createRecurrenceEditPopup(isDelete) {
        $recurrenceEditModeText.text(isDelete ? 'Delete' : 'Edit');
        recurrenceDelete = isDelete;
        recurrenceEditModePopup.open();
    }
    function createLinkEditPopup(updatedEventD, type = "edit") {

        linkEditModePopup.setOptions({
            buttons: ['cancel', {
                text: 'Ok',
                keyCode: 'enter',
                handler: function () {
                    linkEditModePopup.close();
                    updateLinkEvent(updatedEventD, type);
                },
                cssClass: 'mbsc-popup-button-primary'
            }],
            onClose: function () {
                // Reset edit mode to current
                /*linkEditMode = "current";
                $('#event-link-edit-mode-current').mobiscroll('getInst').checked = true;*/
            },
            responsive: {                                // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
                medium: {
                    display: 'center',                   // Specify display mode like: display: 'bottom' or omit setting to use default
                    fullScreen: false,
                    touchUi: false
                }
            },
            cssClass: 'md-link-event-editor-popup'
        });
        linkEditModePopup.open();
    }
    function updateLinkEvent(updatedEventLink, type = "edit") {

        // linkEditMode

        updatedEventLink.editLinkEvent = true;
        updatedEventLink.linkEditMode = linkEditMode;
        updatedEventLink.linkEditType = type;

        update_booking(updatedEventLink);

        
        if(type == "delete"){
            calendar.removeEvent(updatedEventLink);
        }else{
            calendar.updateEvent(updatedEventLink);
            navigateToEvent(updatedEventLink);
        }
        



         /*start*/

                   /* console.log(event)
                        let error = 0

                        if(eventClient == "" || eventClient == undefined){
                            eventClient = 0;
                        }
                        if(eventStatus == ""  || eventStatus == undefined){
                              eventStatus = "waiting";
                        }

                        if(error == 0){

                            if(eventTitle == "New event" || eventTitle == "No title" || eventTitle == ""){
                                if(eventClient != "" && eventClient != undefined && eventClient != 0){
                                    eventTitle = "";
                                }else{
                                    eventTitle = "No title";
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

                        }*/
                    /*end*/ 
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
        }, 10000);
    }
    function showToastMessage(message,color,display = "bottom") {
        mobiscroll.toast({
            message: message,
            color: color,
            duration : 10000,
            display : display
        });
    }

    var formatDate = mobiscroll.util.datetime.formatDate;
    var startDate, endDate;

    var MAX_MONTH_DAYS = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var DAY_NAMES = ['Søndag','Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag' ];
    var DAY_NAMES_SHORT = [ 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA', ];
    var DAY_NAMES_MAP = { 'SU': 0, 'MO': 1, 'TU': 2, 'WE': 3, 'TH': 4, 'FR': 5, 'SA': 6  };
    var MONTH_NAMES = [
        { value: 1, text: 'Januar' },
        { value: 2, text: 'Februar' },
        { value: 3, text: 'Mars' },
        { value: 4, text: 'April' },
        { value: 5, text: 'Mai' },
        { value: 6, text: 'Juni' },
        { value: 7, text: 'Juli' },
        { value: 8, text: 'August' },
        { value: 9, text: 'September' },
        { value: 10, text: 'Oktober' },
        { value: 11, text: 'November' },
        { value: 12, text: 'Desember' },
    ];

    var originalRecurringEvent;
    var eventOccurrence;
    var eventRecurrenceRule;
    var newEvent;
    var editedEvent;

    // Settings
    var calendarEventList = (WPMCalendarV2Obj.cal_show_daily_summery_weak !== '' && WPMCalendarV2Obj.cal_show_daily_summery_weak == "true") ? true : false;;
    var templateSelected = WPMCalendarV2Obj.template_selected !== '' ? WPMCalendarV2Obj.template_selected : "";
    var calendarStartDay = WPMCalendarV2Obj.cal_start_day !== '' ? WPMCalendarV2Obj.cal_start_day : 1;
    var calendarEndDay = WPMCalendarV2Obj.cal_end_day !== '' ? WPMCalendarV2Obj.cal_end_day : 0;
    var calendarStartTime = WPMCalendarV2Obj.cal_starttime !== '' ? WPMCalendarV2Obj.cal_starttime : '09:00';
    var calendarEndTime = WPMCalendarV2Obj.cal_endtime !== '' ? WPMCalendarV2Obj.cal_endtime : '17:00';
    var calendarTimeCellStep = (WPMCalendarV2Obj.cal_time_cell_step && WPMCalendarV2Obj.cal_time_cell_step !== '') ? WPMCalendarV2Obj.cal_time_cell_step : 60;
    var calendarTimeLabelStep = (WPMCalendarV2Obj.cal_time_label_step && WPMCalendarV2Obj.cal_time_label_step !== '') ? WPMCalendarV2Obj.cal_time_label_step : 60;
    var calendarWeekNumbers = (WPMCalendarV2Obj.cal_show_week_nos !== '' && WPMCalendarV2Obj.cal_show_week_nos == "true") ? true : false;
    var calendarshow_bk_payment_failed = (WPMCalendarV2Obj.show_bk_payment_failed !== '' && WPMCalendarV2Obj.show_bk_payment_failed == "true") ? true : false;
    var calendarshow_bk_pay_to_confirm = (WPMCalendarV2Obj.show_bk_pay_to_confirm !== '' && WPMCalendarV2Obj.show_bk_pay_to_confirm == "true") ? true : false;
    var calendarAdditionalInfo = WPMCalendarV2Obj.additional_info !== '' ? WPMCalendarV2Obj.additional_info : "";
    var calendarShowAdminIcons = WPMCalendarV2Obj.show_admin_icons !== '' ? WPMCalendarV2Obj.show_admin_icons : "";
    var calendarShowFieldInfo = WPMCalendarV2Obj.show_fields_info !== '' ? WPMCalendarV2Obj.show_fields_info : "";

    if(!Array.isArray(calendarAdditionalInfo)){
        calendarAdditionalInfo = ["event_title","customer_name"];
    }
    if(!Array.isArray(calendarShowAdminIcons)){
        calendarShowAdminIcons = ["repeated","linked","not_repeated","not_linked","comment","notes","custom_field"];
    }
    if(!Array.isArray(calendarShowFieldInfo)){
        calendarShowFieldInfo = [];
    }

    var update_template_auto = WPMCalendarV2Obj.update_template_auto !== '' ? WPMCalendarV2Obj.update_template_auto : "yes";
    update_template_auto = "yes";
    let reportRangePicker;

    var eventId;
    var eventClient;
    var eventClientName;
    var eventListing;
    var eventTeam;
    var eventTitle;
    var eventDescription;
    var eventGuest;
    var phoneNumber;
    var eventAllDay;
    var eventStart;
    var eventEnd;
    var eventColor;
    var eventRecurrence;
    var eventRecurringException = [];
    var eventResource;
    var eventStatus;
    var eventPrice;
    var repeterSwitch;
    var sendmail = false;
    var google_cal_data;
    var outlook_cal_data;

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
    var linkEditMode = 'current';

    var $eventTitle = $('#event-title');
    var $eventClient = $('#wpm-client');
    var $eventListing = $('#wpm-listing');
    var $eventTeam = $('#wpm-team');
    var $eventStatus = $('#wpm-status');
    var $eventDescription = $('.popup-event-description');
    var $eventGuest = $('.popup-event-guest');
    var $eventAllDay = $('#popup-event-all-day');
    var $eventDeleteButton = $('#popup-event-delete');
    var $eventRecurrence = $('.popup-event-recurrence');
    var $eventRecurrenceEditor = $('.popup-event-recurrence-editor');

    var $recurrenceInterval = $('.recurrence-interval');
    var $recurrenceCount = $('.recurrence-count');
    var $recurrenceEditModeText = $('#recurrence-edit-mode-text');
    var $recurrenceWeekDays = $('.md-recurrence-weekdays');
    var $tooltip = $('#calendar-event-tooltip-popup');

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
    var tooltip = $tooltip.mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: false,
        maxWidth: 500
    }).mobiscroll('getInst');

    mobiscroll.setOptions({
        locale: mobiscroll.locale[mobi_locale],                     // Specify language like: locale: mobiscroll.localePl or omit setting to use default
        theme: 'gibbs-material',                                    // Specify theme like: theme: 'ios' or omit setting to use default
        themeVariant: 'light'                        // More info about themeVariant: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-themeVariant
    });

    var filterListingSelect = $('#filter-listing-input').mobiscroll().select({
        showOverlay : false,
        data: section_resources,
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        
        filter: true,
        selectMultiple: true,
        tags: true,
        cssClass: 'md-listing-select-popup',
        onChange: function (args) {
            // Filter resources
            filteredListings = section_resources.filter(function (resource, index) {
                return args.value.includes(resource.value)
            })
        }
    }).mobiscroll('getInst');

    let resources_data = get_resources();

    let event_start_datee = null;
    let event_end_datee = null;
    
    // Init the event calendar
    window.calendar = calendar = $('#scheduler').mobiscroll().eventcalendar({
        responsive: {
            custom: { // Custom breakpoint
                breakpoint: 800,
                controls: ['calendar'],
                display: 'anchored',
                touchUi: true
            }
        },
        showOverlay: false,
        newEventText : "",
        modules: [mobiscroll.print],
        theme: 'gibbs-material',
        clickToCreate: 'single',                     // More info about clickToCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-clickToCreate
        dragToCreate: true,                          // More info about dragToCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToCreate
        dragToMove: true,                            // More info about dragToMove: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToMove
        dragToResize: true,
        showEventTooltip: false,                       // More info about dragToResize: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-dragToResize
        //clickToCreate: true,
        view: {
            timeline: {
                type: 'week',
                startDay: 1,
                endDay: 5,
                startTime: '09:00',
                endTime: '22:00',
                timeCellStep: 60,
                timeLabelStep: 60,
                weekNumbers: false,
                currentTimeIndicator: true
            }
        },        // More info about view: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-view
        data: [],                              // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
        // colors: calendar_view_val === 'schedule_month' || calendar_view_val === 'schedule_year' ? [] : colors,
        resources: resources_data,
        onEventClick: function (args) {              // More info about onEventClick: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventClick
            editedEvent = args.event

            if(checkSync(editedEvent) == false){
                return false;
            }

            originalRecurringEvent = args.event;


            let args_event = args.event;
            let currentTargett = args.domEvent.currentTarget;

             

            if (editedEvent.title !== "New event") {

                setTimeout(function(){
                    showEventSummary(args_event, currentTargett);
                },10)

            }    

            
        },
        onCellClick: function (args) {              // More info about onEventClick: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventClick
            
            if(( window.innerWidth <= 800 )){
              var start_ddd = new Date(moment(args.date).format("YYYY-MM-DD HH:mm:ss"));
              var end_ddd = args.date;
              end_ddd.setHours(end_ddd.getHours()+1);
              let eventt = {
                   allDay :  false,
                   start :  start_ddd,
                   end :  end_ddd,
                   id :  "mbsc_1",
                   slot :  false,
                   resource :  args.resource,
                   title :  "",
              };
              createAddPopup(eventt, args.target);
           }else{
            return;
           }
           

            
        },
        onEventDoubleClick: function (args) {
            if(checkSync(args.event) == false){
                return false;
            }
            createEditPopup(args.event, args.domEvent.currentTarget);

            setTimeout(function(){
                tooltip.close()
            },20)
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
        onEventDragStart: function (args, inst) {
            var event = args.event
            event_start_datee = event.start;
            event_end_datee = event.end;
            if(event.first_event_id && event.first_event_id != ""){

                if(args.action == "move"){

                    inst.setOptions({dragToMove : false})

                    setTimeout(function(){
                        inst.setOptions({dragToMove : true})
                    },1000)

                    return false;
                }    

            }else{

                inst.setOptions({dragToMove : true})

            }
        },
        onEventDragEnd: async function (args, inst) {
            var event = args.event
            var ev_id = event.id;

            


            if (ev_id.includes("mbsc") == false && !event.recurring) {
                event.wpm_client = event.client ? event.client.value : '';
                event.sectionResourcesId = event.resource;
                event.gymSectionId = event.resource;
                event.resourceId = event.resource;

                if(event.first_event_id && event.first_event_id != ""){
                    createLinkEditPopup(event);
                }else{

                        showLoader();
                        const customData = await fetchCustomAccessCodeData(event.resourceId,ev_id);
                        hideLoader();
                        var hasChangeAccess = false;
                        if (typeof customData.data["access_code"] !== "undefined") {

                            hasChangeAccess = true;
                            
                        }
                        
                        if(hasChangeAccess){

                            let accessConfirm = mobiscroll.confirm({
                                title: 'Er du sikker?',
                                message: 'Ved å endre tid, vil tilgangskoden endres og sendes på nytt til kunden.',
                                okText: 'Ja',
                                cancelText: 'Nei',
                                callback: function (resultConfirm) {
                                    if(resultConfirm){
                                        event.is_logged = true;
                                        update_booking(event)
                                    }else{
                                       event.start = event_start_datee;
                                       event.end = event_end_datee;
                                       event_start_datee = null;
                                       event_end_datee = null;
                                       inst.updateEvent(event);
                                    }
                                }
                            });

                        }else{
                            update_booking(event)
                        }
                    
                }   

                
            }
        },
        onEventCreate: function (args) {             // More info about onEventCreate: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventCreate
            
            if (args.originEvent) {
                // Store created event on recurring occurrence drag
                newEvent = args.event;
                // Prevent event creation on recurring occurrence drag
                return false;
            }
            if(jQuery(".mbsc-popup-anchored").length > 0){ 
                  return false;
            }
        },
        onEventCreated: function (args) {            // More info about onEventCreated: https://docs.mobiscroll.com/5-21-1/eventcalendar#event-onEventCreated 
            createAddPopup(args.event, args.target);
        },
        onPageLoaded: function (args, inst) {
            var end = args.lastDay;
            startDate = args.firstDay;
            endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate() - 1, 0);
            setTimeout(function(){
                $("#selected-day").html($('.cal-header-nav > button').html())
            },100)
            if(cal_date != ""){
                const threeMonthsBefore = new Date(cal_date);
                threeMonthsBefore.setMonth(threeMonthsBefore.getMonth() - 1);

                const threeMonthsAfter = new Date(cal_date);
                threeMonthsAfter.setMonth(threeMonthsAfter.getMonth() + 2);

                // Compare the dates and show an alert if the condition is met
                if (startDate < threeMonthsBefore || startDate > threeMonthsAfter) {
                    if(calendar){
                        get_booking_data(calendar);
                    }
                }
            }
            

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
                        icons += '<span  data-content="Annen informasjon" class="tooltip_info"><svg width="1em" height="1em xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M413.5 237.5c-28.2 4.8-58.2-3.6-80-25.4l-38.1-38.1C280.4 159 272 138.8 272 117.6V105.5L192.3 62c-5.3-2.9-8.6-8.6-8.3-14.7s3.9-11.5 9.5-14l47.2-21C259.1 4.2 279 0 299.2 0h18.1c36.7 0 72 14 98.7 39.1l44.6 42c24.2 22.8 33.2 55.7 26.6 86L503 183l8-8c9.4-9.4 24.6-9.4 33.9 0l24 24c9.4 9.4 9.4 24.6 0 33.9l-88 88c-9.4 9.4-24.6 9.4-33.9 0l-24-24c-9.4-9.4-9.4-24.6 0-33.9l8-8-17.5-17.5zM27.4 377.1L260.9 182.6c3.5 4.9 7.5 9.6 11.8 14l38.1 38.1c6 6 12.4 11.2 19.2 15.7L134.9 484.6c-14.5 17.4-36 27.4-58.6 27.4C34.1 512 0 477.8 0 435.7c0-22.6 10.1-44.1 27.4-58.6z"/></svg></span>';
                    } catch (e) {
                       // return false;
                    }
                    
                }

                if(event.refund_data && Array.isArray(event.refund_data) && event.refund_data.length > 0){
                    try {
                        icons += '<span data-content="Refundert" class="tooltip_info"><svg width="1em" height="1em xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M192 96H320l47.4-71.1C374.5 14.2 366.9 0 354.1 0H157.9c-12.8 0-20.4 14.2-13.3 24.9L192 96zm128 32H192c-3.8 2.5-8.1 5.3-13 8.4l0 0 0 0C122.3 172.7 0 250.9 0 416c0 53 43 96 96 96H416c53 0 96-43 96-96c0-165.1-122.3-243.3-179-279.6c-4.8-3.1-9.2-5.9-13-8.4zM289.9 336l47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47z"/></svg></span>';

                    } catch (e) {
                       // return false;
                    }
                }
                if(event.google_cal_data != undefined && event.google_cal_data["google_cal_id"] != undefined){
                    
                    try {
                        icons += '<span data-content="Google" class="tooltip_info"><svg  height="1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg></span>';

                    } catch (e) {
                       // return false;
                    }
                }
                if(event.outlook_cal_data != undefined && event.outlook_cal_data["outlook_cal_id"] != undefined){
                    
                    try {
                        icons += '<span data-content="Outlook" class="tooltip_info"><svg height="1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 32h214.6v214.6H0V32zm233.4 0H448v214.6H233.4V32zM0 265.4h214.6V480H0V265.4zm233.4 0H448V480H233.4V265.4z"/></svg></span>';

                    } catch (e) {
                       // return false;
                    }
                }
                if(event.service_data != undefined && Array.isArray(event.service_data) && event.service_data.length > 0){
                    
                    try {
                        icons += '<span data-content="Services" class="tooltip_info"><svg height="1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M50.7 58.5L0 160l208 0 0-128L93.7 32C75.5 32 58.9 42.3 50.7 58.5zM240 160l208 0L397.3 58.5C389.1 42.3 372.5 32 354.3 32L240 32l0 128zm208 32L0 192 0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-224z"/></svg></span>';

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
                if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
                    title_div_data.push(event.amount_guest);
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }
               // debugger;
                //debugger;
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

                title_div_data_f = [];
                fields_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        tl_div = tl_div.toString();
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
                /* title_div_data_f.push("sdhsjhdkjshdkjshdkjshdkjhskdhskdhksjhdksjhd skdhskjhdks dskhdskjhdkjshdkshdkjshdhskdjhskjdhskjdhsjhdkj"); */


                title_div_data = title_div_data_f.join(", ");

                let fields_div_data = fields_div_data_f.join(", ");

                const maxLength = 28; // Maximum characters before truncating

    
                if (title_div_data.length > maxLength) {
                    const truncatedText = title_div_data.substring(0, maxLength);
                    const remainingText = title_div_data.substring(maxLength);
                    
                    title_div_data = truncatedText /* + `<span class="read-more-text">... <u>Read More</u></span>` */;
                }



              /*   if(title_div_data.length > 20){

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
                if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
                    title_div_data.push(event.amount_guest);
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }
                //debugger;
                //debugger;
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

                title_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        tl_div = tl_div.toString();
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
            renderHeader += '<div class="report-range-picker"><button mbsc-button data-variant="flat" id="selected-day" class="mbsc-calendar-button">' +
                '<span class="mbsc-calendar-title report-range-picker-text">' +
                '</span></button></div>';
            renderHeader += '<div mbsc-calendar-nav class="cal-header-nav d-none"></div>';

            // Prev next buttons
            renderHeader += '<div mbsc-calendar-prev class="cal-header-prev"></div>' +
                '<div mbsc-calendar-next class="cal-header-next"></div>';

            // Today
            renderHeader += '<div mbsc-calendar-today></div>';


            // Refresh button
            renderHeader += '<div class="refresh-calendar"><i class="fa fa-rotate" aria-hidden="true"></i></div>';

            renderHeader += '<button id="demo-google-cal-sign-in" class="google-sign-in mbsc-reset mbsc-font mbsc-button mbsc-gibbs-material mbsc-material mbsc-ltr mbsc-button-flat">Sign in with Google</button>';

            renderHeader += '<button id="demo-outlook-cal-sign-in" class="outlook-sign-in mbsc-reset mbsc-font mbsc-button mbsc-gibbs-material mbsc-material mbsc-ltr mbsc-button-flat">outlook calendars</button>';

            

            // template filter
            

            // Search input
            renderHeader += '<div id="cal_options">';

            renderHeader += '<input type="text" placeholder="Søk..." id="scheduler-search-input" style="max-width: 20%" />';


            renderHeader += '<div class="btn-template">' +
                '<i class="fa fa-filter-list btn-template-btn"></i><span class="translation-block">' + ' </span>' +
                '</div>';

           
    // Filter
    let filter_count_d = "";

    $filter_count_hide = 'style="display: none"';

    if(filterListingSelect && filterListingSelect != undefined && filterListingSelect.getVal()  != undefined){

        if(filterListingSelect.getVal().length > 0){
            filter_count_d = filterListingSelect.getVal().length;
            $filter_count_hide = "";
        }

    }
    renderHeader += '<div class="filter-container"><div id="filter-count" '+$filter_count_hide+'>'+filter_count_d+'</div><div class="btn-filter">' +
        '<span><i class="fa fa-filter dropbtn1"></i></span>' +
        '</div></div>';

            // Settings
            renderHeader += '<div class="btn-config">' +
                '<i class="fa fa-cog dropbtn1"></i><span class="translation-block">' + ' </span>' +
                '</div>';

        

            // View select
            renderHeader += '<div class="fc-button-group cal_view_select_outer dropdown_chevron_timeline""><select id="cal_view_select" class="cal_view_select">' +
                '<option value="timeline_day" class="fc-timelineDay-button fc-button fc-state-default" ' + (calendar_view_val === 'timeline_day' ? 'selected' : '') + '>Tidslinje dag</option>' +
                '<option value="timeline_week" class="fc-timelineWeek-button fc-button fc-state-default" ' + (calendar_view_val === 'timeline_week' ? 'selected' : '') + '>Tidslinje uke</option>' +
                '<option value="timeline_month"class="fc-timelineMonth-button fc-button fc-state-default" ' + (calendar_view_val === 'timeline_month' ? 'selected' : '') + '>Tidslinje måned</option>' +
/*                 '<option value="timeline_year" class="fc-timelineYear-button fc-button fc-state-default" ' + (calendar_view_val === 'timeline_year' ? 'selected' : '') + '>Tidslinje år</option>' + */
                '<option value="schedule_day" class="fc-scheduleDay-button fc-button fc-state-default" ' + (calendar_view_val === 'schedule_day' ? 'selected' : '') + '>Dag</option>' +
                '<option value="schedule_week" class="fc-scheduleWeek-button fc-button fc-state-default" ' + (calendar_view_val === 'schedule_week' ? 'selected' : '') + '>Uke</option>' +
                '<option value="schedule_month"class="fc-scheduleMonth-button fc-button fc-state-default" ' + (calendar_view_val === 'schedule_month' ? 'selected' : '') + '>Måned</option>' +
                '<option value="schedule_year" class="fc-scheduleYear-button fc-button fc-state-default" ' + (calendar_view_val === 'schedule_year' ? 'selected' : '') + '>År</option>' +
                '<option value="agenda" class="fc-agenda-button fc-button fc-state-default" ' + (calendar_view_val === 'agenda' ? 'selected' : '') + '>Agenda</option>' +
                '</select></div></div>';
            renderHeader += '</div>';

            return renderHeader;
        },
        renderResource: function (resource) {
            return '<div class="md-resource-details-cont">' +
                '<div class="md-resource-header mbsc-timeline-resource-title" data-id="' + resource.id + '" data-content="' + resource.full_text + '" data-sports="' + resource.sports + '">' + resource.name + '</div>' +
                '</div>';
        },
        onSelectedDateChange: function (event, inst) {

            reportRangePicker.navigate(event.date)

            $("#selected-day").html($('.cal-header-nav > button').html())

            if(calendar){
               // get_booking_data(calendar);
            }

            

        }
    }).mobiscroll('getInst');

    get_booking_data(calendar,true);

    // setTimeout(() => {
    //     get_booking_data(calendar,true);
    // }, 1000);

    // Update settings on page load 
    updateCalendarSettings(true);




    var eventClientSelect;
    var eventTeamSelect;
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
    let refund_price;
    let refundPrice;
    


    fields_init();
    function fields_init(popup = 'add') {

        // Attach event handlers
        $('.popup-event-description').on('change', function () {
            eventDescription = this.value;
        });
        $('.popup-event-guest').on('change', function () {
            eventGuest = this.value;
        });

        eventTeamSelect = $eventTeam.mobiscroll().select({
            touchUi: true,
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {

                eventTeam = args.value;
            }
        }).mobiscroll('getInst');

        eventTitleSelect = $eventTitle.mobiscroll('getInst');
        eventTitleSelect.change(function(){
            //debugger;
           eventTitle = this.value;
        });
        // eventTitleSelect.keypress(function(e){
        //     if(e.target.value.length > 100){
        //         return false;
        //     }
        // });

        eventClientSelect = $eventClient.mobiscroll().select({
            touchUi: true,
            filter: true,
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {
                jQuery(".required_focus").removeClass("required_focus");

                eventClient = args.value;
                eventClientName = args.valueText;

                //get_customer_comment();
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
            display: "anchored",
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



        

        /*eventStatusSelect = $('.wpm-status').mobiscroll().select({
            inputElement: document.getElementById(popup + '-status-input'),
            touchUi: true,
            responsive: { small: { touchUi: false } },
            maxWidth: 80,
            onChange: function (args) {
                eventStatus = args.value;
            }
        }).mobiscroll('getInst');*/

        eventStatusSelect = $('#wpm-status').mobiscroll('getInst');
        eventStatusSelect.change(function(){

            jQuery(".required_focus").removeClass("required_focus");
        
            if(this.value == "closed"){
                jQuery(".price_divv").fadeOut();
            }else{
                jQuery(".price_divv").fadeIn();
            }

            if(this.value == "confirmed" ){
                jQuery(".paylink_main_inner").fadeIn();
                jQuery(".payment_plus").fadeOut();
            }else{
                jQuery(".paylink_main_inner").fadeOut();
            }
            if(this.value == "confirmed" || this.value == "paid" || this.value == "cancelled"){
                jQuery(".send_mail_switch_label").show();
            }else{
                jQuery(".send_mail_switch_label").hide();
                jQuery("#sendmail").mobiscroll('getInst').checked = false;
                jQuery("#sendmail")[0].checked = false;
                sendmail = false
            }

            if(this.value == "close" || this.value == "closed"){
               jQuery(".paylink_main").hide();
            }else{
               jQuery(".paylink_main").show(); 
            }
            eventStatus = this.value;
        });
        
        repeterSwitchSelect = $('#repeter_switch').mobiscroll('getInst');
        repeterSwitchSelect.change(function(){
            if(this.checked == true){
                jQuery(".rec_divv").show();
                eventRecurrenceSelect.setVal("daily");
                repeterSwitch = true;
            }else{
                eventRecurrenceSelect.setVal("norepeat");
                jQuery(".rec_divv").hide();
                repeterSwitch = false;
            }
            eventRecurrenceSelect.change();

        });

        sendmailSelect = $('#sendmail').mobiscroll('getInst');
        sendmailSelect.change(function(){
            if(this.checked == true){
                sendmail = true
            }else{
                sendmail = false
            }
        });

        bk_price = $('#bk_price').mobiscroll('getInst');
        bk_price.on("input",function(){
            jQuery(".paylink_main_inner").fadeOut();
            if(this.value > 0){
              jQuery(".paylink_main").find(".payment_plus").fadeIn();
              /*eventStatus = "confirmed";
              eventStatusSelect.val(eventStatus);*/
            }else{
              jQuery(".paylink_main").find(".payment_plus").fadeOut();
            }
            eventPrice = this.value;
        });

        refund_price = $('#refund_price').mobiscroll('getInst');
        refund_price.on("input",function(){
            refundPrice = this.value;
        });


        eventStartEndPicker = $('#' + popup + '-event-dates').mobiscroll().datepicker({
            controls: ['calendar','time'],
            rangeStartLabel : "Fra",
            rangeEndLabel : "Til",
            display: 'anchored', 
            select: 'range',
            startInput: '#' + popup + '-event-start',
            endInput: '#' + popup + '-event-end',
            showRangeLabels: true,
            touchUi: false,
            responsive: { medium: { touchUi: false } },
           // buttons : ["set","cancel"],
            onChange: function (args) {
                jQuery(".required_focus").removeClass("required_focus");
                var dates = args.value;
               // console.log(dates)
                eventStart = dates[0];
                eventEnd = dates[1];
                eventRecurrenceSelect.setOptions({ data: getRecurrenceTypes(eventStart) });
            }
        }).mobiscroll('getInst');

        jQuery("#add-event-end").on("click",function(){

            setTimeout(function(){
               jQuery(".mbsc-range-end").click();
            },100)
            
        })

        eventRecurrenceSelect = $('.popup-event-recurrence').mobiscroll().select({
            data: [],                                    // More info about data: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-data
            touchUi: true,
            responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
            onChange: function (args) {
                eventRecurrence = args.value;
                toggleRecurrenceEditor(eventRecurrence);
            },onTempChange: function (args) {
                eventRecurrence = args.value;
                if(eventRecurrence == "norepeat"){
                    jQuery(".rec_divv").hide();
                    jQuery("#repeter_switch").mobiscroll('getInst').checked = false;
                }
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

        $('.md-link-edit-mode').on('change', function () {
            linkEditMode = this.value;
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

    var addEditPopup = $('#calendar-add-edit-popup').mobiscroll().popup({
        showOverlay: true,
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh',
        onClose: function (event, inst) {

            var element = $('.add-edit-info').detach();
            $("#calendar-add-edit-popup").append(element)
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

    var dataContentPopup = $('#data-content-popup').mobiscroll().popup({
        showOverlay: true,
        fullScreen: true,
        width: '100%',
        maxWidth: 700,
        maxHeight: '60vh',
        onClose: function (event, inst) {

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
        cssClass: 'data-content-popup-inner'
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
        showOverlay: false,
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
    var linkEditModePopup = $('#link-edit-mode-popup').mobiscroll().popup({
        maxWidth: 700,
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: false,
    }).mobiscroll('getInst');

    function updateRecurringEvent() {

        var editFromPopup = addEditPopup.isVisible();

        var updatedEvent = {
            title: eventTitle,
            wpm_client: eventClient,
            description: eventDescription,
            guest: eventGuest,
            allDay: eventAllDay,
            color: eventColor,
            start: eventStart,
            end: eventEnd,
            status: eventStatus,
            price: eventPrice,
            recurrenceId: eventId,
            gymSectionId: eventResource,
            recurrenceEditMode: recurrenceEditMode,
            resource: eventResource,
            resourceId: eventResource,
            team: '',
            gymId: '',
            repert: ''
        };

        if (recurrenceEditMode !== 'current') {
            updatedEvent.id = eventId;
            updatedEvent.recurring = getRecurrenceRule();
            updatedEvent.recurringException = eventRecurringException;

            updatedEvent.recurrenceRule = convertRecurrenceRuleToString(updatedEvent.recurring);
        }

        var result = mobiscroll.updateRecurringEvent(
            originalRecurringEvent,
            eventOccurrence,
            editFromPopup ? null : newEvent,
            editFromPopup ? updatedEvent : null,
            recurrenceEditMode,
        );

        var updatedBooking = result.updatedEvent;

        updatedBooking.create_new_event = 0;

        if (result.newEvent) {
            console.log('new event --> ', result.newEvent)
            calendar.addEvent(result.newEvent);
            updatedBooking.newEvent = result.newEvent
            updatedBooking.create_new_event = 1;
        }

        console.log("recurrance mode")

        if (updatedEvent.id === 'mbsc_1') {
            updatedEvent.id = eventId;
        }
        calendar.updateEvent(updatedBooking);

        if (updatedBooking.resource !== eventResource) {
            updatedBooking.sectionResourcesId = eventResource;
            updatedBooking.gymSectionId = eventResource;
            updatedBooking.resourceId = eventResource;
            updatedBooking.resource = eventResource;
        }

        updatedBooking.start = eventStart;
        updatedBooking.end = eventEnd;
        updatedBooking.wpm_client = eventClient;

        if (updatedBooking.recurringException && recurrenceEditMode === 'current') {
            updatedBooking.recurrenceException = updatedBooking.recurringException.map(function (date) {
                return moment(date).format('YYYY-MM-DD');
            });
        } else {
            updatedBooking.recurrenceException = [];
        }

        updatedBooking.recurrenceEditMode = recurrenceEditMode;

        update_booking(updatedBooking);
    }



    $eventAllDay.on('change', function () {
        eventAllDay = this.checked;
        toggleDatetimePicker(eventAllDay);
    });

    $eventDeleteButton.on('click', function () {


            let deleteConfirm = mobiscroll.confirm({
                title: 'Slett hendelse?',
                message: 'Er du sikker på at du vil slette?',
                okText: 'Ja',
                cancelText: 'Avbryt',
                callback: function (resultConfirm) {
                    if(resultConfirm){

                        if (editedEvent.recurring) {
                            createRecurrenceEditPopup(true);
                        }else if(editedEvent.first_event_id && editedEvent.first_event_id != ""){

                            createLinkEditPopup(editedEvent,"delete");
                            addEditPopup.close();

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
            $resourceTooltip.find('.k-tooltip-content').find(".res_tooltip").append("<p><b>Sports:</b> <span>"+$resourceTitle.data('sports')+"</span>")
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

    // Refresh calendar
    $(document).on('click', '.refresh-calendar', function () {
            $(this).find("svg").css({
                "-webkit-animation-name":"spin",
                "-webkit-animation-duration":"1000ms",
                "-webkit-animation-iteration-count":"3",
            });
        get_booking_data(calendar);

        calendar.refresh();
        let _thatt = this;
        setTimeout(function(){
            $(_thatt).find("svg").removeAttr("style");
        },3000)
        
    });
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

    // Filters
    var $listingFilterPopup = $('#filter-listing-popup');
    var $eventListToggle = $('#show-daily-summary-week');
    var $weekNumbersSetting = $('#show-week-numbers');
    var $show_bk_payment_failed = $('#show_bk_payment_failed');
    var $show_bk_pay_to_confirm = $('#show_bk_pay_to_confirm');
    var $hoursSettingContainer = $('#display-hours-container');
    var $timescaleToSettingContainer = $('#time-scale-to-container');
    //var $timeLabelTimelineContainer = $('#time-label-timeline-container');

    /*if (calendar_view_val === 'timeline_month' || calendar_view_val === 'timeline_year') {
    } else {
        calendarEventList = false
    }*/

   // alert(calendarEventList)



    

    var $rangeButton = $('.report-range-picker-text');

    // returns the formatted date
    function getFormattedRange(start, end) {
        return formatDate('MMM D, YYYY', new Date(start)) + (end && getNrDays(start, end) > 1 ? (' - ' + formatDate('MMM D, YYYY', new Date(end))) : '');
    }

    // returns the number of days between two dates
    function getNrDays(start, end) {
        return Math.round(Math.abs((end.setHours(0) - start.setHours(0)) / (24 * 60 * 60 * 1000))) + 1;
    }

    var settingsPopup = $('#settings-popup').mobiscroll().popup({
        showOverlay: true,
        display: 'bottom',                           // Specify display mode like: display: 'bottom' or omit setting to use default
        contentPadding: true,
        fullScreen: true,
        scrollLock: false,
        maxWidth: 400,
        height: 400,                                 // More info about height: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-height
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
        showOverlay: false,
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


    var templatePopup = $('#template-popup').mobiscroll().popup({
        showOverlay: false,
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

    $(document).on('change', '.cal_view_select', function (e) {
        calendar_view_val = e.target.value;
        console.log('here')
        if (calendar_view_val === 'timeline_month' || calendar_view_val === 'timeline_year') {

           $eventListToggle.mobiscroll('getInst').checked = calendarEventList;

        } else {

            $eventListToggle.mobiscroll('getInst').checked = false;
        }

        

        onEventListToogle();

        updateCalendarSettings()



        // Persist to the db
        save_calendar_filters({
            name: 'calendar_view',
            value: calendar_view_val
        })
        



    })

    var settingsDaysFromSelect = $('#display-days-from-input').mobiscroll().select({
        showOverlay: false,
        data: DAY_NAMES.map(function (val, idx) {
            return {
                value: idx,
                text: val
            }
        }),
        locale: mobiscroll.locale[mobi_locale],
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
       // maxWidth: 80,
        onChange: function (args) {
            calendarStartDay = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');;

    var settingsDaysToSelect = $('#display-days-to-input').mobiscroll().select({
        showOverlay: false,
        data: DAY_NAMES.map(function (val, idx) {
            return {
                value: idx,
                text: val
            }
        }),
        touchUi: false,
        responsive: { small: { touchUi: false } },   // More info about responsive: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-responsive
        //maxWidth: 80,
        onChange: function (args) {
            calendarEndDay = args.value

            updateCalendarSettings()
        }
    }).mobiscroll('getInst');

    var settingsHoursFromSelect = $('#display-hours-from').mobiscroll().select({
        showOverlay: false,
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
        showOverlay: false,
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
        showOverlay: false,
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
    $show_bk_payment_failed.change(function (e) {
        calendarshow_bk_payment_failed = e.target.checked;
    })
    $show_bk_pay_to_confirm.change(function (e) {
        calendarshow_bk_pay_to_confirm = e.target.checked;
    })

    let tvAdditionalFields = "";

    tvAdditionalFieldsSelect = $("#tv-additional-fields").mobiscroll().select({
        showOverlay: false,
        target : jQuery("#tv-additional-fields"),
        touchUi: true,
        filter: true,
        selectMultiple: true,
        tags: true,
        mobiscroll: "Select",
        responsive: { small: { touchUi: false } },
        maxWidth: 80,
        onChange: function (args) {
           tvAdditionalFields = args.value;
        }
    }).mobiscroll('getInst');

    additionalInfo = $("#additional-info").mobiscroll().select({
        showOverlay: false,
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
    adminIconShow = $("#admin_icon_show").mobiscroll().select({
        showOverlay: false,
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

    fieldsInfoShow = $("#fields-info").mobiscroll().select({
        showOverlay: false,
        target : jQuery("#fields-info"),
        touchUi: true,
        filter: true,
        selectMultiple: true,
        tags: true,
        mobiscroll: "Select",
        responsive: { small: { touchUi: false } },
        maxWidth: 80,
        onChange: function (args) {

            calendarShowFieldInfo = args.value
        }
    }).mobiscroll('getInst');


    $(document).on('click', '.btn-config', function (e) {

        if(calendarStartDay && calendarStartDay != ""){}else{
            calendarStartDay = 1;
        }
        if(calendarEndDay && calendarEndDay != ""){}else{
            calendarEndDay = 0;
        }


        

        settingsDaysFromSelect.setVal(parseInt(calendarStartDay))
        settingsDaysToSelect.setVal(parseInt(calendarEndDay))
        settingsHoursFromSelect.setVal(calendarStartTime)
        settingsHoursToSelect.setVal(calendarEndTime)
        additionalInfo.setVal(calendarAdditionalInfo)
        adminIconShow.setVal(calendarShowAdminIcons)
        fieldsInfoShow.setVal(calendarShowFieldInfo)




        settingsTimeScaleToSelect.setVal(calendarTimeCellStep.toString())
        //settingsTimeLabelsSelect.setVal(calendarTimeLabelStep.toString())

        $weekNumbersSetting.mobiscroll('getInst').checked = calendarWeekNumbers;
        $show_bk_payment_failed.mobiscroll('getInst').checked = calendarshow_bk_payment_failed;
        $show_bk_pay_to_confirm.mobiscroll('getInst').checked = calendarshow_bk_pay_to_confirm;

        $eventListToggle.mobiscroll('getInst').checked = calendarEventList;

        if(Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.length < 1){
            calendarShowAdminIcons.push("dummy");
        }

        if(Array.isArray(calendarShowFieldInfo) && calendarShowFieldInfo.length < 1){
            calendarShowFieldInfo.push("dummy");
        }

        settingsPopup.setOptions({
            anchor: e.currentTarget,
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            headerText: 'Kalender innstillinger',                // More info about headerText: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-headerText
            buttons: ['cancel', {                    // More info about buttons: https://docs.mobiscroll.com/5-21-1/eventcalendar#opt-buttons
                text: 'Velg',
                keyCode: 'enter',
                handler: function () {
                    updateCalendarSettings();;

                    save_calendar_filters({
                        name: ['cal_start_day', 'cal_end_day', 'cal_starttime', 'cal_endtime', 'cal_time_cell_step', 'cal_time_label_step', 'cal_show_week_nos', 'cal_show_daily_summery_weak', 'additional_info','show_admin_icons', 'show_fields_info','show_bk_payment_failed','show_bk_pay_to_confirm'],
                        value: [calendarStartDay, calendarEndDay, calendarStartTime, calendarEndTime, calendarTimeCellStep, calendarTimeCellStep, calendarWeekNumbers,calendarEventList,calendarAdditionalInfo,calendarShowAdminIcons,calendarShowFieldInfo,calendarshow_bk_payment_failed,calendarshow_bk_pay_to_confirm]
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
                  data: {"action" : "change_template", "template_selected" : template_selected},
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

    $(document).on('click', '.showDataContent', function (e) {
        dataContentPopup.open();
        var data_html = jQuery(this).parent().find(".sms_email_content").html();
        jQuery("#data-content-popup").html(data_html);
    });
    $(document).on('click', '.tabs-event .tab-event', function (e) {
        e.preventDefault();
        $(this).parent().find(".active").removeClass("active");
        $(this).addClass("active");
        if($(this).attr("data-tab") == "eventForm"){

            $("#user-info").hide();
            $("#sms-email-info").hide();
            $(".event-info_main").show();
            jQuery(".cal_custom_fields").show();

        }else if($(this).attr("data-tab") == "user-info"){

            $("#user-info").show();
            $("#sms-email-info").hide();
            $(".event-info_main").hide();
            jQuery(".cal_custom_fields").hide();
            

        }else if($(this).attr("data-tab") == "sms-email-info"){

            

            $("#sms-email-info").show();
            $("#user-info").hide();
            $(".event-info_main").hide();
            jQuery(".cal_custom_fields").hide();
            SmsEmailInfoShow();
            
            
            

        }
        return false;
    })   
    
    function userInfoShow(){
       
        jQuery("#user-info").html('<div class="popup-loader"><div class="loaderdivv"></div></div>')

        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {"action" : "get_user_info", "booking_id" : eventId},
            success: function (data) {
                jQuery(".cal_custom_fields").show();
                jQuery("#user-info").html(data);
                if(data != ""){
                    booking_phone_init();
                }
                
            }
        });

    }
    function SmsEmailInfoShow(){
       
        jQuery("#sms-email-info").html('<div class="popup-loader"><div class="loaderdivv"></div></div>')

        jQuery.ajax({
            type: "POST",
            url: WPMCalendarV2Obj.ajaxurl,
            data: {"action" : "get_sms_email_info", "booking_id" : eventId},
            success: function (data) {
                jQuery(".cal_custom_fields").show();
                jQuery("#sms-email-info").html(data);
                if(data != ""){
                    let SmsEmailLogTable = initializeSmsEmailTable();
                }
                
            }
        });

    }

    function apply_template_data(data){

        if(data.template_selected){
            templateSelected = data.template_selected !== '' ? data.template_selected : "";
        }
        if(data.cal_start_day){
            calendarStartDay = data.cal_start_day !== '' ? data.cal_start_day : 1;
        }else{
            if(calendarStartDay && calendarStartDay != ""){}else{
                calendarStartDay = 1;
            }
        }
        if(data.cal_end_day){
            calendarEndDay = data.cal_end_day !== '' ? data.cal_end_day : 5;
        }else{
            if(calendarEndDay && calendarEndDay != ""){}else{
                calendarEndDay = 0;
            }
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
        if(data.show_bk_payment_failed){
            calendarshow_bk_payment_failed = (data.show_bk_payment_failed !== '' && data.show_bk_payment_failed == "true") ? true : false;
        }
        if(data.show_bk_pay_to_confirm){
            calendarshow_bk_pay_to_confirm = (data.show_bk_pay_to_confirm !== '' && data.show_bk_pay_to_confirm == "true") ? true : false;
        }
        if(data.cal_show_daily_summery_weak){
            calendarEventList = (data.cal_show_daily_summery_weak !== '' && data.cal_show_daily_summery_weak == "true") ? true : false;
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

        if(data.show_fields_info){
            calendarShowFieldInfo = data.show_fields_info !== '' ? data.show_fields_info : "";
        }

        if(data.filter_location){
            filter_locations = data.filter_location !== '' ? data.filter_location : [];
        }else{
            filter_locations = [];
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
        get_booking_data(calendar)
    }

    var filteredListings = [];

    var listingsFilterPopup = $listingFilterPopup.mobiscroll().popup({
        showOverlay: false,
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

    var searchList = $('#search-list').mobiscroll().eventcalendar({
        view: {
            agenda: {
                type: 'year',
                size: 10
            }
        },
        min: new Date(new Date().setFullYear(new Date().getFullYear() - 1)),
        max : new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
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
                if(event.amount_guest && event.amount_guest != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("amount_guest")){
                    title_div_data.push(event.amount_guest);
                }
                if(event.phone_number && event.phone_number != "" && Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.includes("phone_number")){
                    title_div_data.push(event.phone_number);
                }
               // debugger;

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

                title_div_data_f = [];

                title_div_data.forEach(function(tl_div){



                    if(tl_div && tl_div != ""){
                        tl_div = tl_div.toString();
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
        showControls: true,
        onEventClick: function (args) {
            calendar.navigate(args.event.start);
            calendar.setEvents(schedulerTasks);
            calendar.setSelectedEvents([args.event]);
            navigateToEvent(args.event);
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

        searchTimer = setTimeout(function () {
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
                console.log(calendar.options)

                searchPopup.setOptions({ anchor: e.currentTarget });

                searchPopup.open()

                calendar.setEvents(filteredTasks)

            } else {
                filteredTasks = schedulerTasks
                calendar.setEvents(schedulerTasks)

                searchPopup.close()
            }
        }, 180)
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

    $(document).on('click', '#print-calendar', function (e) {
        calendar.print();
    });

    jQuery(document).on("submit",".addPopup #eventForm",function(){

        jQuery(".addPopup").find(".mbsc-add-popup-button-primary").click();

    })
    jQuery(document).on("submit",".editPopup #eventForm",function(){

        jQuery(".editPopup").find(".mbsc-edit-popup-button-primary").click();

    })

        jQuery(".customer_email").change(function(){
            jQuery(".show_info_div").html("");
            jQuery(".extra_customer_div").hide();
        })
        jQuery(".customer_email_btn").click(function(){
            jQuery(".show_info_div").html("");
            jQuery(".extra_customer_div").hide();
            var emaill = jQuery(this).parent().find(".customer_email").val();
            if(emaill != ""){
                jQuery(".overlay").show();
                ajax_data = {
                    'action': 'check_customer_email',
                    'email': emaill,
                };
                jQuery.ajax({
                    type: 'POST',
                    url: listeo.ajaxurl,
                    data: ajax_data,
                    success: function(response) {

                        jQuery(".overlay").hide();


                        if(response.success){

                            if(response.exist == false){

                                jQuery(".extra_customer_div").show();

                            }else{

                                if(response.type == "already"){

                                    jQuery(".show_info_div").html('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                                    //get_customer_list(response.user_id);

                                }else{

                                     jQuery(".overlay").show();
                                    jQuery(".show_info_div").html('<div class="alert alert-success" role="alert">'+response.message+'</div>');

                                    get_customer_list(response.user_id);
                                }

                            }

                        }
                    }

                });
            }
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

        removeTimelineMonthColor(calendar_view_val)

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
    function save_template(template_id = ""){

        let template_selected_id = templateSelected;

        if(template_id != ""){
             template_selected_id = template_id;
        }else{
           // alert(update_template_auto)
            if(update_template_auto != "yes"){
                return false;
            }
        }
        if(Array.isArray(calendarAdditionalInfo) && calendarAdditionalInfo.length < 1){
            calendarAdditionalInfo.push("dummy");
        }
        if(Array.isArray(calendarShowAdminIcons) && calendarShowAdminIcons.length < 1){
            calendarShowAdminIcons.push("dummy");
        }

        if(Array.isArray(calendarShowFieldInfo) && calendarShowFieldInfo.length < 1){
            calendarShowFieldInfo.push("dummy");
        }
        



        if(template_selected_id && template_selected_id != "" && template_selected_id != undefined){

            //showLoader();

            let template_data = {
                action: "save_listing_filter_template_mobiscroll",
                template_selected: template_selected_id,
                cal_start_day: calendarStartDay,
                cal_end_day: calendarEndDay,
                cal_starttime: calendarStartTime,
                cal_endtime: calendarEndTime,
                cal_time_cell_step: calendarTimeCellStep,
                cal_time_label_step: calendarTimeLabelStep,
                cal_show_week_nos: calendarWeekNumbers,
                show_bk_payment_failed: calendarshow_bk_payment_failed,
                show_bk_pay_to_confirm: calendarshow_bk_pay_to_confirm,
                cal_show_daily_summery_weak: calendarEventList,
                filter_location: (filterListingSelect && filterListingSelect != undefined) ? filterListingSelect.getVal() : [],
                calendar_view: jQuery(".cal_view_select").val(),
                additional_info: calendarAdditionalInfo,
                show_admin_icons: calendarShowAdminIcons,
                show_fields_info: calendarShowFieldInfo,
            }

           // debugger;

            $.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: template_data,
                success: function (response) {
                    if(template_id != ""){

                        window.location.reload();
                    
                    }else{
                        showToastMessage("Visningen ble oppdatert!","success");
                        hideLoader();
                        get_booking_data(calendar)
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
        data.action = 'save_cal_filters';

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

                get_booking_data(calendar)
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
    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    function initFunctions(schedulerTasks){

        reportRangePicker = $(document).find('.report-range-picker').mobiscroll().datepicker({
        controls: ['calendar'],
        display: 'anchored',
        showOverlay: false,
        touchUi: false,
        onOpen: function (inst) {
            inst.inst.setActiveDate(Object.keys(calendar._selectedDates)[0])
        },
        onClose: function (args, inst) {

             var date = inst.getVal();

            if(date){

                calendar.navigate(date);

                calendar.setEvents(schedulerTasks);



                setTimeout(function(){

                    let month_c = moment(date).format("MMMM");
                    let year_c = moment(date).format("YYYY");

                    month_c = ucwords(month_c);
                    year_c = ucwords(year_c);

                    $("#selected-day").find(".mbsc-calendar-month").text(month_c)
                    $("#selected-day").find(".mbsc-calendar-year").text(year_c)

                    if(calendar){
                        //get_booking_data(calendar);
                    }

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
    let cal_date = "";
    let last_date = "";

    // Get all bookings
    function get_booking_data(calendar, shouldShowLoader = false, first_load = false) {
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

        jQuery("#loader").show();

        setTimeout(function(){
            

            //if(first_load == true){

                if(calendar._firstDay){
                    cal_date = moment(calendar._firstDay).format('YYYY-MM-DD HH:mm:ss');
                }
                if(calendar._lastDay){
                    last_date = moment(calendar._lastDay).format('YYYY-MM-DD HH:mm:ss');
                }
            //}

            var mobiscroll_view = jQuery(".cal_view_select").val();

            console.log(calendar)



            $.ajax({
                type: "POST",
                url: WPMCalendarV2Obj.ajaxurl,
                data: {
                    action: 'get_booking_data',
                    cal_type: cal_type,
                    cal_view: cal_viewww,
                    listing: $listingss,
                    additional_info: calendarAdditionalInfo,
                    cal_date: cal_date,
                    last_date: last_date,
                    mobiscroll_view: mobiscroll_view,
                },
                success: function (response) {
                    jQuery("#loader").hide();
                // console.log(response)

                schedulerTasks = libEvents(response.schedular_tasks);
                    /*var filter_location = response.filter_location;

                    schedulerTasks = libEvents(response.schedular_tasks);

                    resources = prepareFullCalendar('');

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

                        filterListingSelect.setVal(selectedListings);
                    } else {
                        section_resources_value = resources;
                    }

                // console.log(section_resources_value)
                    calendar.setOptions({ resources: section_resources_value });*/
                    calendar.setEvents(schedulerTasks);

                    initFunctions(schedulerTasks);

                    if (shouldShowLoader) {
                        hideLoader();
                    }
                }
            });
        },500);    
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
                action: 'get_booking_by_user',
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
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:ss');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:ss');

        showLoader();

        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {

                get_booking_data(calendar);

                let title_t = ""

                if(data.title != ""){
                    title_t = data.title+" ";
                }

                let date_d = moment(data.start).format('DD MMMM, YYYY');

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
                if(listing_name != ""){
                    title_t = title_t+" "+listing_name;
                }

                
                var message_d = ""+title_t+" "+date_d+".";

                showToastMessage(message_d,"success");

                hideLoader();
            }
        );
    }

    function update_booking(data) {
        //console.log(data)

        if(!data.google_cal_data){
            data.google_cal_data = google_cal_data;
        }

        var google_cal_idd = "";

        if(data.google_cal_data && data.google_cal_data.google_cal_id && data.google_cal_data.google_cal_id != ""){

            var eventt = {};
            eventt.id= data.google_cal_data.googleEventId
            eventt.start= data.start;
            eventt.end= data.end;
            eventt.title= data.title;
            google_cal_idd = data.google_cal_data.google_cal_id;

        }

        if(!data.outlook_cal_data){
            data.outlook_cal_data = outlook_cal_data;
        }

        var outlook_cal_idd = "";

        if(data.outlook_cal_data && data.outlook_cal_data.outlook_cal_id && data.outlook_cal_data.outlook_cal_id != ""){

            var eventt_outlook = {};
            eventt_outlook.id= data.outlook_cal_data.outlookEventId
            eventt_outlook.start= data.start;
            eventt_outlook.end= data.end;
            eventt_outlook.title= data.title;
            outlook_cal_idd = data.outlook_cal_data.outlook_cal_id;

        }
        
        data.action = 'wpm_update_record';
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:ss');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:ss');


        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {

                
                if(google_cal_idd != ""){
                    googleCalendarSync
                    .updateEvent(google_cal_idd, eventt)
                    .then(function () {

                    })
                    .catch(function (error) {
                    });
                }    
                if(outlook_cal_idd != ""){
                    outlookCalendarSync
                    .updateEvent(outlook_cal_idd, eventt_outlook)
                    .then(function () {

                    })
                    .catch(function (error) {
                    });
                } 
                if(response && response.error && response.message){

                    if(response.message == "refund"){
                      
                       confirmPopup("Refundering ikke mulig!","Du har dessverre ikke nok i din saldo for å refundere. Vent til din saldo har økt :)");

                    }else if(response && response.error_type == "refund_woo"){
                      
                        confirmPopup("Refundering ikke mulig!",response.message);
 
                    }else{

                        showToastMessage(response.message,"error");

                    }
                    get_booking_data(calendar);


                }else{

                    var cpt = 1;

                    let title_t = ""

                    if(data.title != ""){
                        title_t = data.title+"";
                    }

                    let date_d = moment(data.start).format('DD MMMM, YYYY');

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
                    if(listing_name != ""){
                        title_t = title_t+" "+listing_name;
                    }
                    
                    var message_d = "Oppdatert "+title_t+" "+date_d+".";

                    showToastMessage(message_d,"success");

                // showToast('Event updated successfully');

                    get_booking_data(calendar);

                }
                

            });
    }

    function delete_booking(data) {
        data.action = 'wpm_delete_record';
        data.start = moment(data.start).format('YYYY-MM-DD HH:mm:ss');
        data.end = moment(data.end).format('YYYY-MM-DD HH:mm:ss');

        $.post(
            WPMCalendarV2Obj.ajaxurl,
            data,
            function (response) {


                if (response.success) {
                    let title_t = ""

                    if(data.title != ""){
                        title_t = data.title+"";
                    }

                    let date_d = moment(data.start).format('DD MMMM, YYYY');

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
                    if(listing_name != ""){
                        title_t = title_t+" "+listing_name;
                    }

                    
                    var message_d = ""+title_t+" "+date_d+" har blitt slettet";

                    showToastMessage(message_d,"success");

                    get_booking_data(calendar);
                } else {
                    // Handle error
                    var errorMessage = response.data.message || "An unknown error occurred.";
                    showToastMessage(errorMessage, "error");
                }

                

                //showToast('Event deleted successfully');

            });
    }

    jQuery(document).ready(function(){
        $('.cal-header-nav > button').on('DOMSubtreeModified', function(){
           $("#selected-day").html($('.cal-header-nav > button').html())
        });
        $(document).on('click', '.tooltip-delete',function (e) {
           // alert()

            $eventDeleteButton.click();

        });

        $(document).on("click",".update_template_auto_main", function(){

            let inputt = jQuery(this).find("input")[0];

            update_template_auto = "no";
            if(inputt.checked == true){
               update_template_auto = "yes";
            }
            formData = {};
            formData.action = 'save_template_auto_checkbox';
            formData.update_template_auto = update_template_auto;
            $.post( WPMCalendarV2Obj.ajaxurl,
                formData,
                function (response) {

                 showToastMessage("Lagret endringer :)","success");
                   

            });
        })

    })

    var googleEventPopup = $('#google-event-popup').mobiscroll().popup({
        showOverlay: true,
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh',
    }).mobiscroll('getInst');
    
    var outlookEventPopup = $('#outlook-event-popup').mobiscroll().popup({
        showOverlay: true,
        fullScreen: true,
        width: '100%',
        maxWidth: 1200,
        maxHeight: '80vh',
    }).mobiscroll('getInst');

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

    const CLIENT_ID = '688154971889-l2b8j2dkbnga95ajb0evg0adftgg11ti.apps.googleusercontent.com';
    const API_KEY = 'AIzaSyDXzYoWJvtwCuLKI4Z7eNz0ovRx0L_r_J0';

    // Discovery doc URL for APIs used by the quickstart
    const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest';

    // Authorization scopes required by the API; multiple scopes can be
    // included, separated by spaces.
    const SCOPES = 'https://www.googleapis.com/auth/calendar';

    let tokenClient;
    let gapiInited = false;
    let gisInited = false;

    function gapiLoaded() {
       gapi.load('client', initializeGapiClient);
    }

    async function initializeGapiClient() {
        await gapi.client.init({
          apiKey: API_KEY,
          discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
    }
    /**
       * Callback after Google Identity Services are loaded.
       */
    function gisLoaded() {
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: CLIENT_ID,
          scope: SCOPES,
          callback: '', // defined later
        });
        gisInited = true;
    }

    var googleCalendarSync = mobiscroll.googleCalendarSync;

    var $loginButton = $('#demo-google-cal-sign-in');
    var $logoutButton = $('#demo-google-cal-sign-out');

    var $calendarList = $('#demo-google-cal-list');
    var $loggedOutCont = $('#demo-logged-out-cont');
    var $loggedInCont = $('#demo-logged-in-cont');
    var $cont = $('#demo-google-calendar-cont');
    var $editButton = $('#demo-google-cal-edit');

    var startDate;
    var endDate;
    var calendarIds = [];
    var events = [];
    var calendarData = {};
    var primaryCalendarId;
    var debounce;

    function toggleContainers(loggedIn) {
        if (loggedIn) {
        $loggedOutCont.hide().attr('aria-hidden', 'true');
        $loggedInCont.show().attr('aria-hidden', 'false');
        } else {
        $loggedInCont.hide().attr('aria-hidden', 'true');
        $loggedOutCont.show().attr('aria-hidden', 'false');
        }
    }

    function loadEvents(checked, calendarId) {
        if (checked) {
        loadingEvents(true);
        calendarIds.push(calendarId);
        googleCalendarSync
            .getEvents([calendarId], startDate, endDate)
            .then(function (resp) {
            loadingEvents(false);
            events = events.concat(resp);
            inst.setEvents(events);
            })
            .catch(onError);
        } else {
        var index = calendarIds.indexOf(calendarId);
        if (index !== -1) {
            calendarIds.splice(index, 1);
        }
        events = events.filter(function (event) {
            return event.googleCalendarId !== calendarId;
        });
        inst.setEvents(events);
        }
    }

    function onError(resp) {
        console.log(resp)
        // mobiscroll.toast({
        //     message: resp.error ? resp.error : resp.result.error.message,
        // });
    }

    function updateEventWithRecurrence(calendarId, event) {
        const eventD = {
          'summary': 'Updated Event with Recurrence',
          'description': 'This is an updated event with a recurrence rule.',
          'start': event.start,
          'end': event.end,
          'recurrence': [
            'RRULE:FREQ=WEEKLY;COUNT=10' // Recurrence rule
          ]
        };

        
    
        gapi.client.calendar.events.update({
          'calendarId': calendarId,
          'eventId': event.id,
          'resource': eventD
        }).then(function(response) {
            debugger;
          console.log('Event updated:', response);
        });
    }

    function onSignedIn() {
        if(googleCalendarSync.isSignedIn()){
            jQuery("#demo-google-cal-sign-in").html("Sync with google")
        }
        toggleContainers(true);
        googleCalendarSync
        .getCalendars()
        .then(function (calendars) {

           

            var calList = '<div class="mbsc-form-group-title">My Calendars</div>';

            calendars.sort(function (c) {
            return c.primary ? -1 : 1;
            });
            primaryCalendarId = calendars[0].id;
            calendarIds.push(primaryCalendarId);
            console.log(calendars)

            calList += '<div class="main-cal-list">';
                calList +='<div class="inner-cal google-listing-main">';
                    

                        for (var i = 0; i < calendars.length; ++i) {
                            var c = calendars[i];
                            calList +='<div class="main-select">'
                                calList +='<div class="select-b"><select class="google_listing">';

                                    calList +='<option value="'+c.id+'">'+c.summary+'</option>';
                                    calendarData[c.id] = { name: c.summary, color: c.backgroundColor };
                                calList +="</select></div>";
                                calList +='<div class="select-b"><select class="event_listinggg" multiple>';
                                    section_resources.forEach(function (listinggg) {
                                        calList +='<option value="'+listinggg.id+'">'+listinggg.name+'</option>';
                                    })
                                calList +="</select></div>";
                            calList +="</div>";     
                        }
                        calList +='<div class="inner-cal google-listing-main" style="justify-content: center;">';
                                calList +='<button mbsc-button data-variant="flat" id="submit-google-cal" class="google-sign-in mbsc-reset mbsc-font mbsc-button mbsc-gibbs-material mbsc-material mbsc-ltr mbsc-button-flat">Sync</button';
                        calList +="</div>";  
                    
                calList +="</div>";  
                

            calList +="</div>";

            $calendarList.html(calList);
            mobiscroll.enhance($calendarList[0]);

            $(".event_listinggg").select2({
                placeholder: "Select",
                allowClear: true,
                minimumResultsForSearch: 10,
            });

            loadingEvents(true);

            const endDatee = new Date();
            endDatee.setFullYear(endDatee.getFullYear() + 2);

            googleCalendarSync
            .getEvents([primaryCalendarId], new Date(), endDatee)
            .then(function (resp) {
               // debugger;
            })
            .catch(onError);
        })
        .catch(onError);
    }

    function onSignedOut() {
        toggleContainers(false);
        calendarIds = [];
        calendarData = {};
        events = [];
        $calendarList.empty();
        googleEventPopup.close();
        jQuery("#demo-google-cal-sign-in").html("Sign in with Google")
    }

    function loadingEvents(show) {
        if (show) {
        $cont.addClass('md-loading-events');
        } else {
        $cont.removeClass('md-loading-events');
        }
    }
   

    function initSync(){
        googleCalendarSync.init({
            apiKey: 'AIzaSyDXzYoWJvtwCuLKI4Z7eNz0ovRx0L_r_J0',
            clientId: '688154971889-l2b8j2dkbnga95ajb0evg0adftgg11ti.apps.googleusercontent.com',
            onSignedIn: onSignedIn,
            onSignedOut: onSignedOut,
        });
    }
    initSync();

    async function listUpcomingEvents() {
        googleEventPopup.open();
        let response;
        try {
            response = await gapi.client.calendar.calendarList.list();

            var calList = '<div class="mbsc-form-group-title">My Calendars</div>';

            var calendars = response.result.items;

            calendars.sort(function (c) {
            return c.primary ? -1 : 1;
            });
            primaryCalendarId = calendars[0].id;
            calendarIds.push(primaryCalendarId);
            console.log(calendars)

            calList += '<div class="main-cal-list">';
                calList +='<div class="inner-cal google-listing-main">';
                    

                        for (var i = 0; i < calendars.length; ++i) {
                            var c = calendars[i];
                            calList +='<div class="main-select">'
                                calList +='<div class="select-b"><select class="google_listing">';

                                    calList +='<option val="'+c.id+'">'+c.summary+'</option>';
                                    calendarData[c.id] = { name: c.summary, color: c.backgroundColor };
                                calList +="</select></div>";
                                calList +='<div class="select-b"><select class="event_listinggg" multiple>';
                                    section_resources.forEach(function (listinggg) {
                                        calList +='<option val="'+listinggg.id+'">'+listinggg.name+'</option>';
                                    })
                                calList +="</select></div>";
                            calList +="</div>";     
                        }
                        calList +='<div class="inner-cal google-listing-main" style="justify-content: center;">';
                                calList +='<button mbsc-button data-variant="flat" id="submit-google-cal" class="google-sign-in mbsc-reset mbsc-font mbsc-button mbsc-gibbs-material mbsc-material mbsc-ltr mbsc-button-flat">Sync</button';
                        calList +="</div>";  
                    
                calList +="</div>";  
                

            calList +="</div>";

            $calendarList.html(calList);
            $loggedInCont.show().attr('aria-hidden', 'false');
        } catch (err) {
          document.getElementById('content').innerText = err.message;
          return;
        }

        // const events = response.result.items;
        // if (!events || events.length == 0) {
        //   document.getElementById('content').innerText = 'No events found.';
        //   return;
        // }
        // // Flatten to string to display
        // const output = events.reduce(
        //     (str, event) => `${str}${event.summary} (${event.start.dateTime || event.start.date})\n`,
        //     'Events:\n');
        // document.getElementById('content').innerText = output;
    }
    
    // sign in
    jQuery(document).on('click', "#demo-google-cal-sign-in", function () {
        if (!googleCalendarSync.isSignedIn()) {
            googleCalendarSync.signIn().then(async function(response) {
                googleEventPopup.open();
            }).catch(onError);
        }else{
            googleEventPopup.open();
        }
    });

    async function eventsData(google_cal_id,ev_listingg){
        if(ev_listingg.length > 0){

                loadingEvents(true);


                const endDatee = new Date();
                endDatee.setFullYear(endDatee.getFullYear() + 2);
    
                googleCalendarSync
                .getEvents([google_cal_id], new Date(), endDatee)
                .then(function (resp) {

                    debugger;



                        if(resp && resp.length > 0){

                            respData = [];

                            resp.forEach(function(resss){
                                if(resss.googleEvent){

                                    var iddd = resss.id;

                                    if(resss.googleEvent.recurringEventId){
                                        //iddd = resss.googleEvent.recurringEventId
                                    }else{
                                        respData[iddd] = resss;
                                    }

                                    // if(respData[iddd] != undefined){
                                        
                                    // }else{
                                    //     respData[iddd] = resss;
                                    // }
                                    
                                }

                            })


                            Object.keys(respData).forEach(function(keyev) {
                                

                                var googleEvent = respData[keyev];
                                googleEventId = keyev;
                                
                                var calEvent = {
                                    title: googleEvent.title,
                                    wpm_client: "",
                                    listings: ev_listingg,
                                    description: "",
                                    guest: 1,
                                    allDay: null,
                                    start: googleEvent.start,
                                    end: googleEvent.end,
                                    recurring: "",
                                    status: "paid",
                                    price: "",
                                    recurrenceId: "",
                                    gymSectionId: ev_listingg[0],
                                    recurringException: "",
                                    recurrenceException: "",
                                    recurrenceEditMode: '',
                                    resource: ev_listingg[0],
                                    gymId: '',
                                    repert: '',
                                    sendmail: false,
                                    sendType: "google-cal",
                                    googleEventId: googleEventId,
                                    google_cal_id: google_cal_id,
                                };

                                add_booking(calEvent);
                                
                            })

                            


                        }
                        googleEventPopup.close();

                        


                                


                                    // newEvent.recurrenceRule = convertRecurrenceRuleToString(newEvent.recurring);

                                    // calendar.updateEvent(newEvent);

                                    // navigateToEvent(newEvent);



                                    // add_booking(newEvent);
                    })
                    .catch(onError);
            
        }
    }

    jQuery(document).on('click', "#submit-google-cal", function () {
        // init google client
       jQuery(".google-listing-main").find(".main-select").each(function(){
           var google_cal_id = jQuery(this).find(".google_listing").val();
           var ev_listingg = [];
           jQuery(this).find(".event_listinggg option:selected").each(function(){
            ev_listingg.push(jQuery(this).attr("value"));
           })

           eventsData(google_cal_id,ev_listingg);
           
           
       })
    });

    // switch click
    $loggedInCont.on('change', '.google-calendar-switch', function (ev) {
        loadEvents(ev.target.checked, ev.target.value);
    });

    

    // sign out
    $logoutButton.on('click', function () {
        googleCalendarSync.signOut().catch(onError);
    });

    $('.md-sync-events-google-menu').removeClass('mbsc-hidden');

    var outlookCalendarSync = mobiscroll.outlookCalendarSync;

    var $loginButton_outlook = $('#demo-outlook-cal-sign-in');
    var $logoutButton_outlook = $('#demo-outlook-cal-sign-out');

    var $calendarList_outlook = $('#demo-outlook-cal-list');
    var $loggedOutCont_outlook = $('#demo-logged-out-cont_outlook');
    var $loggedInCont_outlook = $('#demo-logged-in-cont_outlook');
    var $cont_outlook = $('#demo-outlook-calendar-cont_outlook');
    var $editButton_outlook = $('#demo-outlook-cal-edit_outlook');

    var startDate_outlook;
    var endDate_outlook;
    var calendarIds_outlook = [];
    var events_outlook = [];
    var calendarData_outlook = {};
    var primaryCalendarId_outlook;
    var debounce_outlook;

    function toggleContainers_outlook(loggedIn_outlook) {
        if (loggedIn_outlook) {
            $loggedOutCont_outlook.hide();
            $loggedInCont_outlook.show();
        } else {
            $loggedInCont_outlook.hide();
            $loggedOutCont_outlook.show();
        }
    }

    function onSignedIn_outlook() {
        toggleContainers_outlook(true);
        outlookCalendarSync.getCalendars().then(function (calendars) {
            
            var calList = '<div class="mbsc-form-group-title">My Calendars</div>';

            calendars.sort(function (c) {
            return c.primary ? -1 : 1;
            });
            primaryCalendarId_outlook = calendars[0].id;
            calendarIds_outlook.push(primaryCalendarId_outlook);
            console.log(calendars)

            calList += '<div class="main-cal-list">';
                calList +='<div class="inner-cal outlook-listing-main">';
                    

                        for (var i = 0; i < calendars.length; ++i) {
                            var c = calendars[i];
                            calList +='<div class="main-select">'
                                calList +='<div class="select-b"><select class="outlook_listing">';

                                    calList +='<option value="'+c.id+'">'+c.name+'</option>';
                                    calendarData_outlook[c.id] = { name: c.name, color: c.color };
                                calList +="</select></div>";
                                calList +='<div class="select-b"><select class="outlook_event_listinggg" multiple>';
                                    section_resources.forEach(function (listinggg) {
                                        calList +='<option value="'+listinggg.id+'">'+listinggg.name+'</option>';
                                    })
                                calList +="</select></div>";
                            calList +="</div>";     
                        }
                        calList +='<div class="inner-cal outlook-listing-main-button-div" style="justify-content: center;">';
                                calList +='<button mbsc-button data-variant="flat" id="submit-outlook-cal" class="google-sign-in mbsc-reset mbsc-font mbsc-button mbsc-gibbs-material mbsc-material mbsc-ltr mbsc-button-flat">Sync</button';
                        calList +="</div>";  
                    
                calList +="</div>";  
                

            calList +="</div>";

            $calendarList_outlook.html(calList);
            mobiscroll.enhance($calendarList_outlook[0]);

            $(".outlook_event_listinggg").select2({
                placeholder: "Select",
                allowClear: true,
                minimumResultsForSearch: 10,
            });
        });
    }
    
    function onSignedOut_outlook() {
        toggleContainers_outlook(false);
        calendarIds_outlook = [];
        calendarData_outlook = {};
        events_outlook = [];
        $calendarList_outlook.empty();
    }

    async function eventsOutlookData(outlook_cal_id,ev_listingg){
        
        if(ev_listingg.length > 0){

                const endDatee = new Date();
                endDatee.setFullYear(endDatee.getFullYear() + 2);

    
                outlookCalendarSync
                .getEvents([outlook_cal_id], new Date(), endDatee)
                .then(function (resp) {




                        if(resp && resp.length > 0){

                            respData = [];

                            resp.forEach(function(resss){
                                if(resss.outlookEvent){

                                    var iddd = resss.id;

                                    if(resss.outlookEvent.occurrenceId){
                                        //iddd = resss.googleEvent.recurringEventId
                                    }else{
                                        respData[iddd] = resss;
                                    }

                                    // if(respData[iddd] != undefined){
                                        
                                    // }else{
                                    //     respData[iddd] = resss;
                                    // }
                                    
                                }

                            })



                            Object.keys(respData).forEach(function(keyev) {
                                

                                var outlookEvent = respData[keyev];
                                outlookEventId = keyev;
                                
                                var calEvent = {
                                    title: outlookEvent.title,
                                    wpm_client: "",
                                    listings: ev_listingg,
                                    description: "",
                                    guest: 1,
                                    allDay: null,
                                    start: outlookEvent.start,
                                    end: outlookEvent.end,
                                    recurring: "",
                                    status: "paid",
                                    price: "",
                                    recurrenceId: "",
                                    gymSectionId: ev_listingg[0],
                                    recurringException: "",
                                    recurrenceException: "",
                                    recurrenceEditMode: '',
                                    resource: ev_listingg[0],
                                    gymId: '',
                                    repert: '',
                                    sendmail: false,
                                    sendType: "google-cal",
                                    outlookEventId: outlookEventId,
                                    outlook_cal_id: outlook_cal_id,
                                };

                                add_booking(calEvent);
                                
                            })

                            


                        }
                        outlookEventPopup.close();

                        


                                


                                    // newEvent.recurrenceRule = convertRecurrenceRuleToString(newEvent.recurring);

                                    // calendar.updateEvent(newEvent);

                                    // navigateToEvent(newEvent);



                                    // add_booking(newEvent);
                    })
                    .catch(onError);
            
        }
    }

    jQuery(document).on('click', "#submit-outlook-cal", function () {
        // init google client
       jQuery(".outlook-listing-main").find(".main-select").each(function(){
           var outlook_cal_id = jQuery(this).find(".outlook_listing").val();
           var ev_listingg = [];
           jQuery(this).find(".outlook_event_listinggg option:selected").each(function(){
            ev_listingg.push(jQuery(this).attr("value"));
           })

           eventsOutlookData(outlook_cal_id,ev_listingg);
           
           
       })
    });
    

    // sign in
    jQuery(document).on('click', "#demo-outlook-cal-sign-in", function () {
        if (!outlookCalendarSync.isSignedIn()) {
            outlookCalendarSync.signIn().then(async function(response) {
                outlookEventPopup.open();
            }).catch(function(error){
            });
        }else{
            outlookEventPopup.open();
        }
    });



    // switch click
    $loggedInCont_outlook.on('change', '.outlook-calendar-switch', function (ev) {
        loadEvents(ev.target.checked, ev.target.value);
    });

    // edit click
    $editButton_outlook.on('change', function (ev) {
        var isEditable = ev.target.checked;
        inst.setOptions({
        clickToCreate: isEditable,
        dragToCreate: isEditable,
        dragToMove: isEditable,
        dragToResize: isEditable,
        });
    });

    // sign out
    $logoutButton_outlook.on('click', function () {
        outlookCalendarSync.signOut().catch(onError);
    });

    

    // init client
    outlookCalendarSync.init({
        clientId: '265985f7-7ad1-4b28-b3f4-b5ebb94e5434',
        redirectUri: 'https://staging5.dev.gibbs.no/kalender/',
        onSignedIn: onSignedIn_outlook,
        onSignedOut: onSignedOut_outlook,
    });
    

   

    


})(jQuery);

mobiscroll.setOptions({
    locale: mobiscroll.localeNo,
    theme: 'ios',
    themeVariant: 'light'
});



