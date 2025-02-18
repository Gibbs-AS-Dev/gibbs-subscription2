/* ----------------- Start Document ----------------- */
(function($) {
    "use strict";
    /*
    $('body').click(function(e) {
        if (!$(e.target).closest('.add_event_modal').length){
            $(".add_event_modal").fadeOut();
        }
    }); */


    $(document).ready(function() {
        try {

            localStorage.getItem("abc");

        } catch (err) {
            jQuery(".single_listing").remove();
            jQuery(".notification_div").show();
            jQuery(".notification_div").html('<div class="row">' +
                '<div class="col-md-12 listing_title">' +
                '<div class="alert alert-danger" role="alert">Booking not allowed in incognito window</div>' +
                '</div>' +
                '</div>');

        }
        let evt = '';
        const f3 = "YYYY-MM-DD HH:mm";
        const f5 = "YYYY-MM-DD";
        const time_format = "HH:mm";
        $.datetimepicker.setDateFormatter('moment');
        let evts_end = [];
        let wt_end = [];

        let w_evt = '';
        let ev_ind = 0;
        var ev_click = 0;
        if (jQuery('#kt_docs_fullcalendar_populated').length) {

            let maxDateCalender = {};

            if (typeof _max_book_days != 'undefined') {
                if (_max_book_days != "") {
                    _max_book_days = parseInt(_max_book_days);

                    if (_max_book_days > 0) {
                        // alert(moment().add(_max_book_days, 'days').format("YYYY-MM-DD"))

                        maxDateCalender.end = moment().add(_max_book_days, 'days').format("YYYY-MM-DD");
                    }
                }
            }
            if (typeof _min_book_days != 'undefined') {
                if (_min_book_days != "") {
                    _min_book_days = parseInt(_min_book_days);

                    if (_min_book_days > 0) {
                        // alert(moment().add(_max_book_days, 'days').format("YYYY-MM-DD"))

                        maxDateCalender.start = moment().add(_min_book_days, 'days').format("YYYY-MM-DD");
                    }
                }
            }
            console.log(maxDateCalender)
            //document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById("kt_docs_fullcalendar_populated");

            var todayDate = moment().startOf("day");
            var YM = todayDate.format("YYYY-MM");
            var YESTERDAY = todayDate.clone().subtract(1, "day").format("YYYY-MM-DD");
            var TODAY = todayDate.format("YYYY-MM-DD");
            var TOMORROW = todayDate.clone().add(1, "day").format("YYYY-MM-DD");

            var calendarEl = document.getElementById("kt_docs_fullcalendar_populated");
            var calendar = new FullCalendar.Calendar(element, {
                headerToolbar: {
                    left: "prev",
                    center: "addEventButton",
                    right: "next"
                },
                validRange: maxDateCalender,
                timeZone: time_zone,
                height: "auto",
                contentHeight: "auto",
                aspectRatio: 1,
                views: {
                    timeGridFourDay: {
                        type: 'timeGrid',
                        duration: {
                            days: 1
                        },
                        buttonText: '1 day',
                        titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' }
                    },
                    titleFormat: 'dddd, MMMM Do YYYY'
                },
                slotMinTime: "06:00:00",
                slotMaxTime: "24:00:00",
                eventContent: function(info) {
                    return {
                        html: info.event.title
                    };
                },  
                dayHeaderContent: function( dayRenderInfo ) {
                        var monthh = ("0" + (dayRenderInfo.date.getMonth() + 1)).slice(-2);
                        var dayy = ("0" + (dayRenderInfo.date.getDate())).slice(-2);
                        var textdd = dayRenderInfo.text;

                        dayRenderInfo.text = textdd+" ("+dayy+"."+monthh+")";
                },
                dateClick: function(info) {
                    jQuery('.timer-loader-new').fadeIn();
                    var cl = calendar.getEvents();
                    var col = info.dayEl;
                    var sdp = false;
                    if (!col.className.match('fc-day-past')) {
                        sdp = true;
                    }
                    var tk = new Date(info.dateStr);
                    var cur_day = tk.getDay();
                    var tsp = moment(tk).format(time_format);

                    var this_day_time = tmarr[cur_day];
                    var stt = this_day_time.start;
                    var end = this_day_time.end;
                    var now_date = new Date();
                   // now_date.setMonth(now_date.getMonth() - 1);
                    var now_tm = moment(now_date).format(time_format);


                    if (tk >= now_date) {
                        if (tsp >= stt && tsp < end) {
                            /*if(parseInt(ev_click)>1)
                            {
                                jQuery('.timer-loader-new').fadeIn();
                            }else{
                                jQuery('.timer-loader-new').fadeOut();
                            }*/
                            if (jQuery('body').hasClass('user_not_logged_in')) {
                                jQuery('.timer-loader-new').fadeOut();
                                setTimeout(function(){
                                    jQuery("#lg_reg_modal").show();
                                    jQuery("#lg_reg_modal").addClass("show");
                                },100)

                            }else if (jQuery('body').hasClass('book_with_verify')) {
                                jQuery('.timer-loader-new').fadeOut();
                                jQuery("#varify_modal").show();

                            } else if (jQuery(".vipps_div").hasClass("verified_user")) {

                                jQuery("#varify_modal").show();

                            } else {
                                var kl = new Date(info.dateStr);
                                var kkd = moment(kl).format(f5);
                                var firstday = localStorage.getItem('firstDate');
                                var md = new Date(firstday);
                                var ck = moment(md).format(f5);

                                if ((evt) && (kkd == ck)) {
                                    jQuery('.add_event_modal').fadeIn();
                                    /* if(jQuery('.add_event_modal').find(".cst_dt_to").val() == ""){
                                         jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
                                     }else{
                                         jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
                                     }
                                     if(jQuery('.add_event_modal').find(".purpose").val() == ""){
                                         jQuery('.add_event_modal').find(".purpose").addClass("red_border");
                                     }else{
                                         jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
                                     }*/

                                    jQuery('.timer-loader-new').fadeOut();
                                } else {
                                    if (evt) {
                                        evt.remove();
                                        evt = null;
                                    }
                                    var list_id = jQuery('input#listing_id').val();
                                    var ds = new Date(info.dateStr);
                                    var ajax_data = {
                                        'action': 'gibbs_get_hours',
                                        'date_start': moment(ds).format(f3),
                                        'listing_id': list_id
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        dataType: 'json',
                                        url: listeo.ajaxurl,
                                        data: ajax_data,

                                        success: function(data) {
                                            jQuery('.cst_dt_from').html(data.from);
                                            jQuery('.cst_dt_to').html(data.to);
                                            jQuery('.add_event_modal').fadeIn();
                                            /* if(jQuery('.add_event_modal').find(".cst_dt_to").val() == ""){
                                                 jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
                                             }else{
                                                 jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
                                             }
                                             if(jQuery('.add_event_modal').find(".purpose").val() == ""){
                                                 jQuery('.add_event_modal').find(".purpose").addClass("red_border");
                                             }else{
                                                 jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
                                             }*/
                                            jQuery('.timer-loader-new').fadeOut();
                                        },
                                    });
                                }

                            }
                        } else {
                            jQuery('.timer-loader-new').fadeOut();
                        }
                    } else {
                        jQuery('.timer-loader-new').fadeOut();
                        alert("Past date not valid")
                    }


                    /*  var cd = col.querySelector('.fc-bg-event');
                      var sty = window.getComputedStyle(cd);
                      var colr = sty.getPropertyValue('background-color'); */
                    // var pk = find_events(info.dateStr);
                    /*
                        alert('Clicked on: ' + info.dateStr);
                        alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
                        alert('Current view: ' + info.view.type);
                        // change the day's background color just for fun
                        console.log(info.dayEl);
                        info.dayEl.style.backgroundColor = 'red';
                        
                       var ds = new Date(info.dateStr);
                       var CurrentDate = new Date();
                        if(ds>=CurrentDate)
                        {
                            var pb = moment(ds).format(f3);
                            jQuery('.from_tm').val(pb);
                            jQuery('.from_tm').datetimepicker('validate');
                            if(jQuery('body').hasClass('user_not_logged_in'))
                            {
                                    jQuery(".xoo-el-login-tgr").click();
                            }
                            else
                            {
                                    jQuery('.add_event_modal').fadeIn();
                            }
                        }
                        else
                        {
                            alert('Selected time is unavailable');
                        }
                        
                        if(pk && sdp)
                        {
                           
                        }
                        */
                    ev_click++;
                },
                customButtons: {
                    addEventButton: {
                        text: 'Add Event',
                        click: function() {

                            /*         if(jQuery('.fc-addEventButton-button').hasClass('active'))
                                        {
                                            var firstday = localStorage.getItem('firstDate');
                                            var secondday = localStorage.getItem('secondDate');
                                            if(firstday!='' && secondday!='')
                                            {

                                            }
                                            evt.remove();
                                        }
                                        else
                                        { */
                            if (jQuery('body').hasClass('user_not_logged_in')) {
                                setTimeout(function(){
                                    jQuery("#lg_reg_modal").show();
                                    jQuery("#lg_reg_modal").addClass("show");
                                },100)
                            }else if (jQuery('body').hasClass('book_with_verify')) {
                                jQuery('.timer-loader-new').fadeOut();
                                jQuery("#varify_modal").show();

                            } else if (jQuery(".vipps_div").hasClass("verified_user")) {

                                jQuery("#varify_modal").show();

                            } else {
                                jQuery('.add_event_modal').fadeIn();
                                /* if(jQuery('.add_event_modal').find(".cst_dt_to").val() == ""){
                                     jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
                                 }else{
                                     jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
                                 }
                                 if(jQuery('.add_event_modal').find(".purpose").val() == ""){
                                     jQuery('.add_event_modal').find(".purpose").addClass("red_border");
                                 }else{
                                     jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
                                 }*/
                            }
                            /*}
                        
                            var dateStr = prompt('Enter a date in YYYY-MM-DD format');
                            var date = new Date(dateStr + 'T00:00:00'); // will be in local time

                            if (!isNaN(date.valueOf())) { // valid?
                                calendar.addEvent({
                                title: 'dynamic event',
                                start: date,
                                allDay: true
                                });
                                alert('Great. Now, update your database...');
                            } else {
                                alert('Invalid date.');
                            } */
                        }
                    },
                    next: {
                        text: 'Next',
                        click: function() {
                            // do the original command
                            calendar.next();
                            appendBooking();

                            var currentDate = calendar.getDate();
                            var currentMonthYear = moment(currentDate).format("YYYY-MM");

                            // Loop through the <select> options to find a match for the month and year
                            jQuery('.select_month option').each(function() {
                                if (jQuery(this).val().startsWith(currentMonthYear)) {
                                    jQuery('.select_month').val(jQuery(this).val()); // Select the matching option
                                    return false; // Exit the loop
                                }
                            });

                        }
                    },
                    prev: {
                        text: 'Prev',
                        click: function() {
                            // do the original command
                            calendar.prev();

                            appendBooking();

                            var currentDate = calendar.getDate();
                            var currentMonthYear = moment(currentDate).format("YYYY-MM");

                            // Loop through the <select> options to find a match for the month and year
                            jQuery('.select_month option').each(function() {
                                if (jQuery(this).val().startsWith(currentMonthYear)) {
                                    jQuery('.select_month').val(jQuery(this).val()); // Select the matching option
                                    return false; // Exit the loop
                                }
                            });


                        }
                    },
                },
                initialView: "timeGridFourDay",
                initialDate: TODAY,
                editable: true,
                nowIndicator: true,
                eventDrop: function(info) {

                    /*
                    let stt = new Date(info.event.start);
                    let ste = new Date(info.event.end);
                    const f4 = "YYYY-MM-DD HH:mm:ss";
                    let f_date = moment(stt).format(f4);
                    let e_date = moment(ste).format(f4);
                    localStorage.setItem('firstDate', f_date);
                    localStorage.setItem('secondDate', e_date);
                    check_booking(1);
                    */
                    info.revert();
                },

                eventResize: function(info) {

                    let stt = new Date(info.event.start);
                    let ste = new Date(info.event.end);

                    const f4 = "YYYY-MM-DD HH:mm:ss";
                    let f_date = moment(stt).format(f4);
                    let e_date = moment(ste).format(f4);
                    localStorage.setItem('firstDate', f_date);
                    localStorage.setItem('secondDate', e_date);
                    check_booking(1);
                },
                eventMouseEnter: function(event) {

                    let infoss = "";

                    if (event.event.extendedProps.time_event != undefined && event.event.extendedProps.time_event != "") {

                        infoss += time_event;
                        infoss += "<br>" + event.event.extendedProps.listing_name;
                        infoss += "<br>" + event.event.extendedProps.purpose;

                    } else {
                        infoss += event.event.title;
                    }

                    /* var title = moment(waiting_dt[i]["start"]).format("HH:mm")+"-"+moment(waiting_dt[i]["end"]).format("HH:mm");
                     title += "<br>"+waiting_dt[i]["purpose"];*/

                    if (infoss != "") {

                        var tooltip = '<div class="tooltipevent">' + infoss + '</div>';
                        $(".tooltipevent").remove();
                        $(event.el).append(tooltip);
                        $(event.el).parent().addClass("top_tooltip")
                    }
                },
                eventMouseLeave: function(event) {
                    $(".tooltipevent").remove();
                    $(".top_tooltip").removeClass("top_tooltip");
                },
                eventDidMount: function(event) {
                    //debugger;
                },
                dayMaxEvents: true, // allow "more" link when too many events
                navLinks: true,
                locale: "no",
                weekNumbers: true,
                /* validRange: function(nowDate){
                     return {start: nowDate} //to prevent anterior dates
                 }, */
                eventClick: function(info) {
                    if (info.event.groupId == 'new_event') {

                        console.log(info.event);
                        let stt = new Date(info.event.start);
                        let ste = new Date(info.event.end);
                        const f4 = "YYYY-MM-DD HH:mm";
                        let f_date = moment(stt).format(f4);
                        let e_date = moment(ste).format(f4);
                        //jQuery('.cst_dt_from').val(f_date);
                        // jQuery('.cst_dt_to').val(e_date);

                        jQuery('.add_event_modal').fadeIn();
                        /* if(jQuery('.add_event_modal').find(".cst_dt_to").val() == ""){
                             jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
                         }else{
                             jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
                         }
                         if(jQuery('.add_event_modal').find(".purpose").val() == ""){
                             jQuery('.add_event_modal').find(".purpose").addClass("red_border");
                         }else{
                             jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
                         }*/
                    }
                }

            });
            calendar.render();
        }

        function find_events(date) {
            var dt = new Date(date);
            var kd = dt.getDate();
            var cl = calendar.getEvents();
            var s = false;
            for (let p = 0; p < cl.length; p++) {
                var el = cl[p];
                var new_dt = new Date(el.start);
                var k_new = new Date(el.end);
                var dy = new_dt.getDate();

                if (kd == dy) {
                    /*console.log(dy);
                    console.log(dt);
                    console.log(new_dt);*/
                    if (dt.getTime() >= new_dt.getTime() && dt.getTime() < k_new.getTime()) {
                        s = true;
                    }
                }
                /*
                if(dt.getTime()>new_dt.getTime())
                {
                    s = true;
                }
                */
            }
            return s;
        }

        function appendBooking() {
            waiting_dt = localStorage.getItem("waiting_dt");



            if (typeof waiting_dt !== 'undefined') {
                waiting_dt = JSON.parse(waiting_dt);
                let wtln = waiting_dt.length;
                if (wtln > 0) {
                    if (wtln >= 1000) {
                        if (wtln >= 1000) {

                            wtln = 1000;
                            wtln = 1000;

                        }
                        for (let i = 0; i < wtln; i++) {
                            var title = moment(waiting_dt[i]["start"]).format("HH:mm") + "-" + moment(waiting_dt[i]["end"]).format("HH:mm");
                            title += "<br>" + waiting_dt[i]["purpose"];
                            var time_event = moment(waiting_dt[i]["start"]).format("HH:mm") + "-" + moment(waiting_dt[i]["end"]).format("HH:mm");
                            w_evt = calendar.addEvent({
                                groupId: 'waiting_events',
                                title: title,
                                start: waiting_dt[i]["start"],
                                end: waiting_dt[i]["end"],
                                editable: false,
                                overlay: false,
                                color: waiting_dt[i]["color"],
                                purpose: waiting_dt[i]["purpose"],
                                listing_name: waiting_dt[i]["listing_name"],
                                time_event: time_event,
                            });
                            wt_end.push(w_evt);
                            delete waiting_dt[i];
                        }
                    }
                    let org_values = [];
                    waiting_dt.forEach(function(waiting_dt_v) {
                        org_values.push(waiting_dt_v)
                    });

                    localStorage.setItem("waiting_dt", JSON.stringify(org_values));




                }

                /* already booked*/

                already_booked = localStorage.getItem("already_booked");

                if (typeof already_booked !== 'undefined') {
                    already_booked = JSON.parse(already_booked);

                    //debugger;
                    let wtlng = already_booked.length;
                    if (wtlng > 0) {
                        if (wtlng >= 1000) {

                            wtlng = 1000;

                        }

                        for (let p = 0; p < wtlng; p++) {
                            var title = moment(already_booked[p]["start"]).format("HH:mm") + "-" + moment(already_booked[p]["end"]).format("HH:mm");
                            title += "<br>" + already_booked[p]["purpose"];
                            var time_event = moment(already_booked[p]["start"]).format("HH:mm") + "-" + moment(already_booked[p]["end"]).format("HH:mm");
                            w_evt = calendar.addEvent({
                                groupId: 'approved_events',
                                title: title,
                                start: already_booked[p]["start"],
                                end: already_booked[p]["end"],
                                editable: false,
                                overlay: false,
                                color: already_booked[p]["color"],
                                purpose: already_booked[p]["purpose"],
                                listing_name: already_booked[p]["listing_name"],
                                time_event: time_event,
                            });
                            wt_end.push(w_evt);
                            delete already_booked[p];
                        }
                    }

                    let org_values_already = [];
                    already_booked.forEach(function(already_booked_v) {
                        org_values_already.push(already_booked_v)
                    });

                    localStorage.setItem("already_booked", JSON.stringify(org_values_already));



                }
            }
        }

        if (typeof waiting_dt !== 'undefined') {
            appendBooking();
        }
        if (typeof already_booked !== 'undefined') {
            appendBooking();
        }
        //appendBooking();
       // debugger;
        /* jQuery(document).on('change', '.cst_dt_to', function(){
             jQuery('button.add_evnt.button').trigger('click');
         });*/
        jQuery(document).on('click', 'a.today_ic', function() {
            calendar.today();
            buildMonthList();
            return false;
        });
        jQuery(document).on('change', 'select.cst_dt_from', function() {
            var vs = jQuery(this).val();
            var kd = new Date(vs);
            var cvk = moment(kd).add(30, 'minutes');
            var ds = new Date(jQuery('.cst_dt_from').val());
            var list_id = jQuery('input#listing_id').val();
            var ajax_data = {
                'action': 'gibbs_get_hours',
                'date_start': moment(ds).format(f3),
                'listing_id': list_id
            };
            jQuery('.cst_dt_to').prop("disabled", true);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data) {
                    jQuery('.cst_dt_to').prop("disabled", false);
                    jQuery('.cst_dt_from').html(data.from);
                    jQuery('.cst_dt_to').html(data.to);

                },
            });
            // jQuery('select.cst_dt_to').val(moment(cvk).format(f3));
            //console.log(moment(cvk).format(f3));
        });
        jQuery(document).on('change', 'select#fromHours', function() {
            var fdt = jQuery(this).val();
            localStorage.setItem('firstDate', fdt);
            check_booking();
        });
        jQuery(document).on('change', '.cst_dt_to', function() {
            if (this.value == "") {
                jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
            } else {
                jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
            }
        });
        jQuery(document).on('change', '.purpose', function() {
            if (this.value == "") {
                jQuery('.add_event_modal').find(".purpose").addClass("red_border");
            } else {
                jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
            }
        });
        jQuery(document).on('change', 'select#toHours', function() {
            var fdt = jQuery(this).val();
            localStorage.setItem('secondDate', fdt);
            check_booking();
        });
        jQuery(document).on('click', 'input#timeSpanFrom, input#timeSpanTo', function() {
            jQuery('.add_event_modal').fadeIn();
            if (jQuery('.add_event_modal').find(".cst_dt_to").val() == "") {
                jQuery('.add_event_modal').find(".cst_dt_to").addClass("red_border");
            } else {
                jQuery('.add_event_modal').find(".cst_dt_to").removeClass("red_border");
            }
            if (jQuery('.add_event_modal').find(".purpose").val() == "") {
                jQuery('.add_event_modal').find(".purpose").addClass("red_border");
            } else {
                jQuery('.add_event_modal').find(".purpose").removeClass("red_border");
            }
        });
        let cst = new Date();
        let sdd = moment(cst).format(f3);
        var vkd = '';
        var gdd = '';
        var cvl = '';
        /*
        $('.from_tm').datetimepicker({
            value: sdd,
            step: 30,
            format:'YYYY-MM-DD HH:mm',
            onChangeDateTime:function() {
                $('.to_tim').datetimepicker('destroy');
                vkd = jQuery('.from_tm').datetimepicker('getValue'); 
                gdd = new Date(vkd);
                cvl = moment(gdd).add(30, 'minutes');
                $('.to_tim').datetimepicker({
                    value: moment(cvl).format(f3),
                    step: 30,
                    format:'YYYY-MM-DD HH:mm',
                });
            },
        });
        $('.to_tim').datetimepicker({
            value: sdd,
            step: 30,
            format:'YYYY-MM-DD HH:mm',
        });
        */
        jQuery(document).on('click', 'a.cloz_btn', function() {
            jQuery('.add_event_modal').fadeOut();
            jQuery('.weeklymodal').fadeOut();
            return false;
        });
        jQuery(document).ready(function() {
            add_bg_events(0);
        })
        $(".select_month").on("change", function(event) {

            calendar.gotoDate($(this).val());
            setTimeout(function() {
                buildMonthList();
                add_bg_events(0);
            }, 500);
            //calendar.changeView('gotoDate', $(".select_year").val()+"-"+this.value+"-1");
        });
        if (jQuery('#kt_docs_fullcalendar_populated').length) {
            buildMonthList();
        }
        var p = 0;

        function buildMonthList() {
            moment.locale('en_US');
            if (p == 0) {
                $('.select_month').empty();
            }
            var month = calendar.getDate();
            const f9 = 'YYYY-MM';
            var initial = moment(month).format(f9);
            var futureMonth = moment(month);

            var monthss = [];

            jQuery(".select_month").find("option").each(function() {
                monthss.push(this.value);

            })
            for (var i = 0; i < 37; i++) {
                var futureMonthVar = moment(futureMonth).format('YYYY-MM-01');
                if (!monthss.includes(futureMonthVar)) {

                    var opt = document.createElement('option');
                    opt.value = futureMonthVar;
                    opt.text = moment(futureMonth).format('MMMM YYYY');
                    opt.selected = initial === moment(futureMonth).format('YYYY-MM');
                    $('.select_month').append(opt);
                    //month.add(1, 'month');


                }
                futureMonth = moment(futureMonth).add(1, 'M');

            }
            futureMonth = moment(month).add(1, 'M');
            p++;
        }

        jQuery(document).on('click', 'button.add_evnt.button', function(e) {
            e.preventDefault();

            jQuery('.timer-loader-new').show();
            const fnew = "YYYY-MM-DD HH:mm";
            var from_tm = jQuery('select.cst_dt_from').val()
            var to_tim = jQuery('select.cst_dt_to').val();
            var fr = new Date(from_tm);
            var ft = new Date(to_tim);
            if (ft <= fr) {
                var al = "Vennligst velg slutt tid til å være etter start tid";
                jQuery('.event_innr').find(".alrt").remove();
                jQuery('.event_innr a.cloz_btn').after('<div class="alrt">' + al + '</div>');
                jQuery('select.cst_dt_to').val('');

                jQuery('.timer-loader-new').fadeOut();
            } else if (to_tim == "") {

                var al = "Vennligst velg til tid";
                jQuery(".cst_dt_to").addClass("red_border");
                jQuery('.event_innr').find(".alrt").remove();
                jQuery('.event_innr a.cloz_btn').after('<div class="alrt">' + al + '</div>');

                jQuery('.timer-loader-new').fadeOut();

            } else if (jQuery(".purpose").val() == "") {

                var al = "Vennligst velg formål";
                jQuery(".purpose").addClass("red_border");
                jQuery('.event_innr').find(".alrt").remove();
                jQuery('.event_innr a.cloz_btn').after('<div class="alrt">' + al + '</div>');

                jQuery('.timer-loader-new').fadeOut();

            } else {
                jQuery('.event_innr').find(".alrt").remove();
                jQuery(".cst_dt_to").removeClass("red_border");
                jQuery(".purpose").removeClass("red_border");
                jQuery('.add_event_modal').fadeOut();
                localStorage.setItem('firstDate', from_tm);
                localStorage.setItem('secondDate', to_tim);
                jQuery('.col-lg-12.notification.notice.notifitest').fadeOut();
                check_booking();
            }
            /*
            let dtp = new Date(from_tm);
            let dtk = new Date(to_tim);
            let f_date = moment(dtp).format(fnew);
            let e_date = moment(dtk).format(fnew);
            */

            /*
            var from_tm  = jQuery('.from_tm').datetimepicker('getValue');
            var to_tim  = jQuery('.to_tim').datetimepicker('getValue');
            var ltype = jQuery('#date-picker').attr('listing_type');
            var list_id = jQuery('input#listing_id').val();
            const f4 = "YYYY-MM-DD HH:mm:ss";
            const f5 = "YYYY-MM-DDTHH:mm:ss";
            let dtp = new Date(from_tm);
            let dtcs = new Date(to_tim);
            let f_date = moment(dtp).format(f4);
            let l_date = moment(dtcs).format(f4);
            var ajax_data = {
                'action': 'check_cal_availability', 
                'listing_type' : ltype,
                'listing_id' : 	list_id,
                'date_start' : f_date,
                'date_end' : l_date
            };
            jQuery.ajax({
                type: 'POST', 
                dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,
                success: function(data){
                    console.log(data);
                    jQuery('.add_event_modal').fadeOut();
                    calendar.addEvent({
                        title: 'New Booking',
                        start: moment(dtp).format(f5),
                        end: moment(dtcs).format(f5),
                        
                    });
                },
            });
            */
            return false;
        });

        let m_evt = '';
        const f10 = "YYYY-MM-DD";
        if (typeof tmarr !== 'undefined') {
            var cd = calendar.getDate();
            var md = new Date(cd);
            var p = 0;
            for (let m = 0; m < 3; m++) {
                var day_nm = md.getDay();
                var time_dt = tmarr[day_nm];
                var pdp = moment(md).format(f10);
                console.log(day_nm);
                if (time_dt != '') {
                    var cur_start = pdp + ' ' + time_dt.start;
                    var cur_end = pdp + ' ' + time_dt.end;
                    /* console.log(cur_start);
                     console.log(cur_end); */
                    calendar.addEvent({
                        groupId: 'available_events',
                        start: cur_start,
                        end: cur_end,
                        color: "#9BA1A3",
                        display: 'inverse-background',

                    });
                    //console.log(cur_start);
                    //console.log(cur_end);
                    // evts_end.push(m_evt);
                } else {
                    /*
                    m_evt =   calendar.addEvent({
                        groupId: 'available_events',
                        start: pdp+' 00:00',
                        end: pdp+' 24:00',
                        editable: false,
                        overlay: false,
                        display: 'background',
                        color: "#9BA1A3"
                    });
                    evts_end.push(m_evt);
                    */

                }
                md.setDate(md.getDate() + 1);
                p++;
            }
            /*
            let fLen = tmarr.length;
            for (let i = 0; i < fLen; i++) {
                m_evt =   calendar.addEvent({
                    groupId: 'available_events',
                    start: tmarr[i]["start"],
                    end: tmarr[i]["end"],
                    editable: false,
                    overlay: false,
                    display: 'inverse-background',
                    color: "#9BA1A3"
                });
                evts_end.push(m_evt);
            };
            */
            /*
            let fLen = booked.length;
            for (let i = 0; i < fLen; i++) {
                if(booked[i]["start"]!=booked[i]["end"])
                {
                    m_evt =   calendar.addEvent({
                        groupId: 'available_events',
                        start: booked[i]["start"],
                        end: booked[i]["end"],
                        editable: false,
                        overlay: false,
                        color: "#9BA1A3"
                    });
                    evts_end.push(m_evt);
                }
                if(booked[i]["start_new"]!=booked[i]["end_new"])
                {
                    m_evt =   calendar.addEvent({
                        groupId: 'booked_events',
                        start: booked[i]["start_new"],
                        end: booked[i]["end_new"],
                        editable: false,
                        overlay: false,
                        color: "#9BA1A3"
                    });
                    evts_end.push(m_evt);
                }
            } */
        }
        if (typeof waiting_dt !== 'undefined') {
            waiting_dt = localStorage.getItem("waiting_dt");

            if (typeof waiting_dt !== 'undefined') {
                waiting_dt = JSON.parse(waiting_dt);
                let wtln = waiting_dt.length;
                if (wtln > 0) {
                    if (wtln >= 100) {

                        wtln = 100;

                    }
                    for (let i = 0; i < wtln; i++) {
                        var title = moment(waiting_dt[i]["start"]).format("HH:mm") + "-" + moment(waiting_dt[i]["end"]).format("HH:mm");
                        title += "<br>" + waiting_dt[i]["purpose"];
                        var time_event = moment(waiting_dt[i]["start"]).format("HH:mm") + "-" + moment(waiting_dt[i]["end"]).format("HH:mm");
                        w_evt = calendar.addEvent({
                            groupId: 'waiting_events',
                            title: title,
                            start: waiting_dt[i]["start"],
                            end: waiting_dt[i]["end"],
                            editable: false,
                            overlay: false,
                            color: waiting_dt[i]["color"],
                            purpose: waiting_dt[i]["purpose"],
                            listing_name: waiting_dt[i]["listing_name"],
                            time_event: time_event,
                        });
                        wt_end.push(w_evt);
                        delete waiting_dt[i];
                    }
                }
                let org_values = [];
                waiting_dt.forEach(function(waiting_dt_v) {
                    org_values.push(waiting_dt_v)
                });

                localStorage.setItem("waiting_dt", JSON.stringify(org_values));

            }
        }
        if (typeof already_booked !== 'undefined') {
            already_booked = localStorage.getItem("already_booked");

            if (typeof already_booked !== 'undefined') {
                already_booked = JSON.parse(already_booked);
                let wtlng = already_booked.length;
                if (wtlng > 0) {
                    if (wtlng >= 100) {

                        wtlng = 100;

                    }
                    for (let p = 0; p < wtlng; p++) {
                        var title = moment(already_booked[p]["start"]).format("HH:mm") + "-" + moment(already_booked[p]["end"]).format("HH:mm");
                        title += "<br>" + already_booked[p]["purpose"];
                        var time_event = moment(already_booked[p]["start"]).format("HH:mm") + "-" + moment(already_booked[p]["end"]).format("HH:mm");
                        w_evt = calendar.addEvent({
                            groupId: 'approved_events',
                            title: title,
                            start: already_booked[p]["start"],
                            end: already_booked[p]["end"],
                            editable: false,
                            overlay: false,
                            color: already_booked[p]["color"],
                            purpose: already_booked[p]["purpose"],
                            listing_name: already_booked[p]["listing_name"],
                            time_event: time_event,
                        });
                        wt_end.push(w_evt);
                        delete already_booked[p];
                    }
                }

                let org_values_already = [];
                already_booked.forEach(function(already_booked_v) {
                    org_values_already.push(already_booked_v)
                });

                localStorage.setItem("already_booked", JSON.stringify(org_values_already));
            }
        }

        jQuery(document).on('click', '.fc-next-button', function() {
            ev_ind++;
            //console.log(ev_ind);
            remove_all_bg_events();
            add_bg_events(ev_ind);
        });
        jQuery(document).on('click', '.fc-prev-button', function() {
            ev_ind--;
            console.log(calendar.getDate());
            remove_all_bg_events();
            add_bg_events(ev_ind);
        });

        let ev_ip = '';

        let lmmt = '';
        let km = '';
        const f9 = "YYYY-MM-DD";
        let now_tm = '';

        function add_bg_events(ind) {
            if(typeof calendar == "undefined"){
                return false;
            }
            var cd = calendar.getDate();
            var md = new Date(cd);
            var p = 0;
            for (let m = 0; m < 3; m++) {
                var day_nm = md.getDay();
                var time_dt = tmarr[day_nm];
                var pdp = moment(md).format(f10);

                //console.log(md);
                if (time_dt != '') {
                    var cur_start = pdp + ' ' + time_dt.start;
                    var cur_end = pdp + ' ' + time_dt.end;
                    /*console.log(cur_start);
                    console.log(cur_end); */
                    calendar.addEvent({
                        groupId: 'available_events',
                        start: cur_start,
                        end: cur_end,
                        color: "#9BA1A3",
                        display: 'inverse-background',
                        purpose: ""

                    });
                    //console.log(cur_start);
                    //console.log(cur_end);
                    // evts_end.push(m_evt);
                } else {

                    m_evt = calendar.addEvent({
                        groupId: 'available_events',
                        start: pdp + ' 00:00',
                        end: pdp + ' 24:00',
                        editable: false,
                        overlay: false,
                        display: 'background',
                        color: "#9BA1A3",
                        purpose: ""
                    });
                    evts_end.push(m_evt);


                }
                md.setDate(md.getDate() + 1);
                p++;
            }
            /*
            let ppl  = calendar.getDate();
			let kd = new Date(ppl);
            let cur_dt = new Date(ppl);
            ev_ip = parseInt(ind)*3;
            //console.log(ev_ip);
            cur_dt.setDate(cur_dt.getDate()+ev_ip);
            let fdd = moment(cur_dt).format(f9);
            //console.log(fdd);
            if (typeof booked !== 'undefined') {
                let fLen = booked.length;
                for (let i = 0; i < fLen; i++) {
                    var kdd = new Date(booked[i]["start"]);
                    kdd.setDate(kdd.getDate()+ev_ip);
                    var kddss = new Date(booked[i]["end"]);
                    kddss.setDate(kddss.getDate()+ev_ip);

                    var kdd_new = new Date(booked[i]["start_new"]);
                    kdd_new.setDate(kdd_new.getDate()+ev_ip);
                    var kddss_new = new Date(booked[i]["end_new"]);
                    kddss_new.setDate(kddss_new.getDate()+ev_ip);
                  //  console.log(kdd);
                    m_evt =   calendar.addEvent({
                        groupId: 'available_events',
                        start: kdd,
                        end: kddss,
                        editable: false,
                        overlay: false,
                        color: "#9BA1A3"
                    });
                    evts_end.push(m_evt);
                    m_evt =   calendar.addEvent({
                        groupId: 'booked_events',
                        start: kdd_new,
                        end: kddss_new,
                        editable: false,
                        overlay: false,
                        color: "#9BA1A3"
                    });
                    evts_end.push(m_evt);
                }
                }
                */
        }

        function remove_all_bg_events() {
            for (let i = 0; i < evts_end.length; i++) {
                evts_end[i].remove();
            }
        }
        jQuery('.booking-confirmation-btn').click(function() {
            var checkBoxVal = jQuery('#pdfApprove').val();
            var checkBoxValChecked = jQuery('#pdfApprove').is(':checked');
            if (checkBoxValChecked === false) {
                jQuery('#checkbox-error').show();
                return false;
            }
        });



        $(document).on('change', '.discount-input', function() {
            var text = jQuery('.discount-input:checked').val();
            jQuery('.services-counter-discount').show();
            jQuery('.services-counter-discount').text(text);
            check_booking();
        });
        var inputClicked = false;

        /*----------------------------------------------------*/
        /*  Booking widget and confirmation form
        /*----------------------------------------------------*/
        for (var i = 0; i < 20; i++) {
            $('.tabela').find(`.tes${i} strong`).text(`${i}:00`);
        }

        $('a.booking-confirmation-btn').on('click', function(e) {



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

        $('#listeo-coupon-link').on('click', function(e) {
            e.preventDefault();
            $('.coupon-form').toggle();
        });

        function validate_coupon(listing_id, price) {

            var current_codes = $('#coupon_code').val();
            if (current_codes) {
                var codes = current_codes.split(',');
                $.each(codes, function(index, item) {
                    console.log(item);
                    var ajax_data = {
                        'listing_id': listing_id,
                        'coupon': item,
                        'coupons': codes,
                        'price': price,
                        'action': 'listeo_validate_coupon'
                    };
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: listeo.ajaxurl,
                        data: ajax_data,

                        success: function(data) {

                            if (data.success) {



                            } else {


                                $('#coupon-widget-wrapper-output div.error').html(data.message).show();
                                $('#coupon-widget-wrapper-applied-coupons span[data-coupon="' + item + '"] i').trigger('click');
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
        $('a.listeo-booking-widget-apply_new_coupon').on('click', function(e) {
            e.preventDefault();
            $(this).addClass('active');
            $('#coupon-widget-wrapper-output div').hide();

            var ajax_data = {
                'listing_id': $('#listing_id').val(),
                'coupon': $('#apply_new_coupon').val(),
                'price': $('.booking-estimated-cost').data('price'),
                'action': 'listeo_validate_coupon'
            };

            //check if it was already addd

            var current_codes = $('#coupon_code').val();
            var result = current_codes.split(',');
            var arraycontainscoupon = (result.indexOf($('#apply_new_coupon').val()) > -1);

            $('#coupon-widget-wrapper-output div').hide();
            if (arraycontainscoupon) {
                $(this).removeClass('active');
                $('input#apply_new_coupon').removeClass('bounce').addClass('bounce');
                return;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data) {

                    if (data.success) {

                        if (current_codes.length > 0) {
                            $('#coupon_code').val(current_codes + ',' + data.coupon);
                        } else {
                            $('#coupon_code').val(data.coupon);
                        }
                        $('#apply_new_coupon').val('');
                        $('#coupon-widget-wrapper-applied-coupons').append("<span data-coupon=" + data.coupon + ">" + data.coupon + "<i class='fa fa-times'></i></span>")
                        $('#coupon-widget-wrapper-output .success').show();
                        if ($('#booking-confirmation-summary').length > 0) {
                            calculate_booking_form_price();
                        } else {
                            if ($("#form-booking").hasClass('form-booking-event')) {
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
        $('#coupon-widget-wrapper-applied-coupons').on('click', 'span i', function(e) {

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
            if ($('#booking-confirmation-summary').length > 0) {
                calculate_booking_form_price();
            } else {
                check_booking();
                calculate_price();

            }
        });

        // Book now button
        $('.listing-widget').on('click', 'a.book-now', function(e) {

            if(jQuery(this).hasClass("trigger-qty")){
                jQuery(".updateqty-btn").click();
                jQuery(this).removeClass("trigger-qty")
                //return false;
            }



            let _that = this;

            var button = $(_that);

            button.addClass('loading');


            setTimeout(function(){



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
                if (jQuery("#mobFromHours").val() != "" && jQuery("#mobFromHours").val() != undefined) {
                    jQuery("#fromHours").html("<option value='" + jQuery("#mobFromHours").val() + "'>" + jQuery("#mobFromHours").val() + "</option>");
                    jQuery("#toHours").html("<option value='" + jQuery("#mobToHours").val() + "'>" + jQuery("#mobToHours").val() + "</option>");
                }
                if (typeof jQuery("input.adults").attr("data-guest_max") != "undefined") {
                    var guest_max = parseInt(jQuery("input.adults").attr("data-guest_max"));
                    var guest_min = parseInt(jQuery("input.adults").attr("data-guest_min"));

                    var adult_val = parseInt(jQuery("input.adults").val());
                    jQuery(".booking_error_div").remove()

                    if (adult_val < guest_min) {
                        jQuery(".booking-error-message").parent().append('<div class="booking-error-message booking_error_div">Antall må være ' + guest_min + ' eller mer </div>')
                        setTimeout(function() {
                            jQuery(".booking_error_div").remove();
                        }, 5000)
                        button.removeClass('loading');
                        $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
                        return false;
                    }

                    if (adult_val > guest_max) {
                        jQuery(".booking-error-message").parent().append('<div class="booking-error-message booking_error_div">Antall må være ' + guest_max + ' eller mindre </div>')
                        setTimeout(function() {
                            jQuery(".booking_error_div").remove();
                        }, 5000)
                        button.removeClass('loading');
                        $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
                        return false;
                    }


                }

                

                if (inputClicked == false) {
                    $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').addClass('bounce');
                } else {
                    button.addClass('loading');
                }
                e.preventDefault();

                var freeplaces = button.data('freeplaces');



                // setTimeout(function() {

                //     button.removeClass('loading');
                //     $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');

                // }, 3000);



                if (jQuery("#slot").length > 0) {

                    var slot_val = jQuery("input[name=time-slot]:checked").parent().find(".slot_avv").val()
                    var guest_slot = jQuery("input[name=time-slot]:checked").parent().find(".guest_slot").val()

                    if (slot_val != undefined && slot_val != "" && guest_slot != "no") {
                        var slt_vall = parseInt(slot_val);

                        var adlt = jQuery(".adults").val();
                        adlt = parseInt(adlt);

                        if (adlt > slt_vall) {
                            jQuery(".booking-error-message").parent().append('<div class="booking-error-message booking_error_div">Bare ' + slt_vall + ' tilgjengelig!</div>')
                            setTimeout(function() {
                                jQuery(".booking_error_div").remove();
                            }, 5000)
                            button.removeClass('loading');
                            $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
                            //alert("Only " + slt_vall + " slot available!");
                            return false;
                        } 
                    }

                }
                // debugger;
                //  return false;


                 

                try {
                    if (freeplaces > 0) {

                        // preparing data for ajax
                        var firstday = localStorage.getItem('firstDate');
                        var secondday = localStorage.getItem('secondDate');

                        window.setTimeout(function() {
                            var startDataSql = firstday;
                            var endDataSql = secondday;

                            var ajax_data = {
                                'listing_type': $('#listing_type').val(),
                                'listing_id': $('#listing_id').val()
                                //'nonce': nonce		
                            };

                            if ($('#date-picker').data('listing_type') == "rental") {
                                if (startDataSql == endDataSql) {
                                    // Create new Date instance
                                    var date_endd = new Date(endDataSql);
                                    var date_startt = new Date(startDataSql);

                                    // Add a day
                                    date_endd.setDate(date_endd.getDate() + 1);

                                    endDataSql = date_endd.getFullYear() + "-" + ('0' + (date_endd.getMonth() + 1)).slice(-2) + "-" + ('0' + date_endd.getDate()).slice(-2);

                                }

                            }

                            if ($('input#slot').val() != undefined && $('input#slot').val() != "") {

                                startDataSql = moment($('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                                endDataSql = moment($('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                            }
                            var invalid = false;
                            if (startDataSql) ajax_data.date_start = startDataSql;
                            if (endDataSql) ajax_data.date_end = endDataSql;
                            var st = $('.startDate').text();
                            var et = parseInt($('.endDate').text());
                            et = et + 1;
                            var d = $('.endDate').parent().parent().attr('day');

                            if ($('input#slot').val()) {
                                ajax_data.slot = $('input#slot').val();
                            } else {
                                ajax_data.slot = `["${st} - ${et}:00","${d}|0"]`;
                            }

                            if ($('.time-picker#_hour').val()) ajax_data._hour = $('.time-picker#_hour').val();
                            if ($('.time-picker#_hour_end').val()) ajax_data._hour_end = $('.time-picker#_hour_end').val();
                            if ($('.adults').val()) ajax_data.adults = $('.adults').val();
                            if ($('.childrens').val()) ajax_data.childrens = $('.childrens').val();
                            if ($('#tickets').val()) ajax_data.tickets = $('#tickets').val();
                            if ($('#coupon_code').val()) ajax_data.coupon = $('#coupon_code').val();
                            if ($('input[name=av_days]').val()) ajax_data.av_days = $('input[name=av_days]').val();
                            if ($('input[name=endrecdate]').val()) ajax_data.endrecdate = $('input[name=endrecdate]').val();
                            if ($('.purpose').val()) ajax_data.purpose = $('.purpose').val();
                            if ($('input[name=rec]').val()) ajax_data.rec = $('input[name=rec]').val();

                            if (jQuery(".av_dates")[0] != undefined) {

                                let datta = jQuery(".av_dates").attr("data-exp_dates");
                                datta = JSON.parse(datta);

                                let expp = [];

                                datta.forEach(function(data) {
                                    expp.push(data);

                                })

                                ajax_data.expp = expp;
                            }



                            if ($('#listing_type').val() == 'service') {

                                if ($('input#slot').val() == undefined || $('input#slot').val() == '') {
                                    inputClicked = false;
                                    invalid = false;
                                }
                                if ($('.time-picker').length) {

                                    invalid = false;
                                }
                            }


                            if (invalid == false) {
                                var services = [];
                                var sub = [];
                                // $.each($("input[name='_service[]']:checked"), function(){            
                                //     		services.push($(this).val());
                                //});
                                $.each($("input.bookable-service-checkbox:checked"), function() {
                                    var quantity = $(this).parent().find('input.bookable-service-quantity').val();
                                    services.push({
                                        "service": $(this).val(),
                                        "value": quantity
                                    });
                                });
                                if ($('.sub_selector').length) {
                                    $.each($(".sub_selector option:selected"), function() {
                                        var quantity = $(this).text()
                                        sub.push({
                                            "listing_id": $(this).val(),
                                            "listing_name": quantity
                                        });
                                    });
                                }
                                ajax_data.services = services;
                                ajax_data.sub = sub;
                                $('input#booking').val(JSON.stringify(ajax_data));
                                $('#form-booking').submit();


                            }
                        }, 100);

                    }else{
                        button.removeClass('loading');
                        $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
                    }
                } catch (e) {
                    button.removeClass('loading');
                    $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
                    console.log(e);
                }

                if ($('#listing_type').val() == 'event') {

                    var ajax_data = {
                        'listing_type': $('#listing_type').val(),
                        'listing_id': $('#listing_id').val(),
                        'date_start': $('.booking-event-date span').html(),
                        'date_end': $('.booking-event-date span').html(),
                        //'nonce': nonce		
                    };
                    var services = [];
                    $.each($("input.bookable-service-checkbox:checked"), function() {
                        var quantity = $(_that).parent().find('input.bookable-service-quantity').val();
                        services.push({
                            "service": $(_that).val(),
                            "value": quantity
                        });
                    });
                    ajax_data.services = services;

                    // converent data
                    ajax_data['date_start'] = moment(ajax_data['date_start'], wordpress_date_format.date).format('YYYY-MM-DD');
                    ajax_data['date_end'] = moment(ajax_data['date_end'], wordpress_date_format.date).format('YYYY-MM-DD');
                    if ($('#tickets').val()) ajax_data.tickets = $('#tickets').val();
                    $('input#booking').val(JSON.stringify(ajax_data));
                    $('#form-booking').submit();

                }
            },3000)

        });

        if (Boolean(listeo_core.clockformat)) {
            var dateformat_even = wordpress_date_format.date + ' HH:mm';
        } else {
            var dateformat_even = wordpress_date_format.date + ' hh:mm A';
        }


        function updateCounter() {
            var len = $(".bookable-services input[type='checkbox']:checked").length;
            if (len > 0) {
                $(".booking-services span.services-counter").text('' + len + '');
                $(".booking-services span.services-counter").addClass('counter-visible');
            } else {
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

        $('.input-datetime1').daterangepicker({
            "opens": "left",
            // checking attribute listing type and set type of calendar
            singleDatePicker: true,
            timePicker: true,
            autoUpdateInput: false,
            timePicker24Hour: Boolean(listeo_core.clockformat),
            minDate: moment().subtract(0, 'days'),

            locale: {
                format: dateformat_even,
                "firstDay": parseInt(wordpress_date_format.day),
                "applyLabel": listeo_core.applyLabel,
                "cancelLabel": listeo_core.cancelLabel,
                "fromLabel": listeo_core.fromLabel,
                "toLabel": listeo_core.toLabel,
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

        function wpkGetThisDateSlots(date) {

            var slots = {
                isFirstSlotTaken: false,
                isSecondSlotTaken: false
            }

            if ($('#listing_type').val() == 'event')
                return slots;

            if (typeof disabledDates !== 'undefined') {
                if (wpkIsDateInArray(date, disabledDates)) {
                    slots.isFirstSlotTaken = slots.isSecondSlotTaken = true;
                    return slots;
                }
            }

            if (typeof wpkStartDates != 'undefined' && typeof wpkEndDates != 'undefined') {
                slots.isSecondSlotTaken = wpkIsDateInArray(date, wpkStartDates);
                slots.isFirstSlotTaken = wpkIsDateInArray(date, wpkEndDates);
            }

            return slots;

        }

        function wpkIsDateInArray(date, array) {
            return jQuery.inArray(date.format("YYYY-MM-DD"), array) !== -1;
        }
        var slot_bk = false;

        if (typeof slots_booking != "undefined") {
            slot_bk = slots_booking;
        }

        function getFirstDayOfWeek(d) {
            // 👇️ clone date object, so we don't mutate it
            const date = new Date(d);
            const day = date.getDay(); // 👉️ get day of week

            // 👇️ day of month - day of week (-6 if Sunday), otherwise +1
            const diff = date.getDate() - day + (day === 0 ? -6 : 1);

            return new Date(date.setDate(diff));
        }

        function getDates(startDate, stopDate) {
            var dateArray = [];
            var currentDate = moment(startDate);
            var stopDate = moment(stopDate);
            while (currentDate <= stopDate) {
                dateArray.push(moment(currentDate).format('YYYY-MM-DD'))
                currentDate = moment(currentDate).add(1, 'days');
            }
            return dateArray;
        }

        function slotsDates(date_start, slot, booking_data) {


            let datess = [];
            var $slott = slot.split("|");
            var $from_day = parseInt($slott[0]);
            var $from_time = $slott[1];
            var $to_day = parseInt($slott[2]);
            var $to_time = $slott[3];
            var $slot_price = $slott[4];
            var $slots = parseInt($slott[5]);
            var $slot_id = $slott[6];




            var $date_start = "";
            var $date_end = "";

            var firstday = getFirstDayOfWeek(moment(date_start._d).format("YYYY-MM-DD")); // get current date

            const lastday = new Date(firstday);

            lastday.setDate(lastday.getDate() + 12);

            let week_dates = getDates(firstday, lastday);


            if ($from_day > $to_day) {
                let next_arrays = [];
                next_arrays[1] = 8;
                next_arrays[2] = 9;
                next_arrays[3] = 10;
                next_arrays[4] = 11;
                next_arrays[5] = 12;
                next_arrays[6] = 13;
                next_arrays[7] = 14;
                $to_day = next_arrays[$to_day];
            }


            for (var i = 0; i <= week_dates.length; i++) {
                var kk;
                kk = i + 1;
                if (kk == $from_day) {
                    $date_start = week_dates[i];
                }
                if (kk == $to_day) {
                    $date_end = week_dates[i];
                }
            }



            if (($date_start != "" && $date_end != "")) {

                //if(moment(date_start._d).format("YYYY-MM-DD") == "2022-12-02"){

                var $date_start_dd = $date_start + " " + $from_time + ":00";
                var $date_end_dd = $date_end + " " + $to_time + ":00";

                let date_current = new Date();

                let date_only = moment(date_current).format("YYYY-MM-DD");

                if (date_only == $date_start) {
                    /*
                                                date_only = "2023-06-17 07:54:00";
                                               let  $date_start_dd2 = "2023-06-17 06:54:00";
                                               debugger;*/
                    //debugger;
                    if (date_current > new Date($date_end_dd)) {
                        //debugger;
                        return [];
                    }
                }


                let booking_count = 0;


                $date_start_dd = new Date($date_start_dd.replace(" ", "T"));
                $date_end_dd = new Date($date_end_dd.replace(" ", "T"));



                booking_data.forEach(function(booking, index) {

                        var bk_date_start = booking.date_start;
                        var bk_date_end = booking.date_end;

                        bk_date_start = new Date(bk_date_start.replace(" ", "T"));
                        bk_date_end = new Date(bk_date_end.replace(" ", "T"));


                        //if(moment(bk_date_start).format("YYYY-MM-DD HH") == "2022-12-02 08"){

                        //  debugger;




                        if ((bk_date_start >= $date_start_dd && bk_date_start < $date_end_dd) || (bk_date_end > $date_start_dd && bk_date_end <= $date_end_dd) || (bk_date_start >= $date_start_dd && bk_date_end <= $date_end_dd) || ($date_start_dd >= bk_date_start  && $date_end_dd <= bk_date_end )) {
                            //console.log(date_start, booking, $slots)
                            

                            booking_count  += booking.count_slot;
                            

                            
                            /* if(bk_date_end == $date_end_dd){

                                booking_count++;

                            }*/
                        }
                        // }

                    });

                    var tl_slots = $slots;

                    $slots = $slots - booking_count;
                    





                    if ($slots > 0) {

                        
                        if(jQuery("input[name=slot_price_type]:checked").val() == "all_slot_price" && $slots != tl_slots){
                           
                        }else{
                            datess = getDates($date_start, $date_end);
                        }


                        

                        
                    }
               // }      

                //  }
            }

            return datess;

        }

        let maxDateC = moment().add(2000, 'days');
        let minDateC = moment().subtract(1, 'days');

        if ($('#date-picker').attr('data-listing_type') == "rental") {
            minDateC.add(1, 'days');
        }

        if (typeof _max_book_days != 'undefined') {
            if (_max_book_days != "") {
                _max_book_days = parseInt(_max_book_days);

                if (_max_book_days > 0) {

                    maxDateC = moment().add(_max_book_days, 'days');
                }
            }
        }
        if (typeof _min_book_days != 'undefined') {
            if (_min_book_days != "") {
                _min_book_days = parseInt(_min_book_days);

                if (_min_book_days > 0) {

                    minDateC = moment().add(_min_book_days, 'days');
                }
            }
        }

        let slotsDates_data = [];

        // function daysDifference(date1, date2) {
        //     const d1 = new Date(date1);
        //     const d2 = new Date(date2);
        //     const timeDiff = Math.abs(d2 - d1);
        //     return Math.floor(timeDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
        // }

        // const filteredData = booking_data.filter(item => {
        //     const { date_start, date_end } = item;
        //     return daysDifference(date_start, date_end) > 1;
        // });

        // let cr_booking_data = []; 

        // if(filteredData.length > 0){
        //     cr_booking_data = filteredData
        // }
        function parseDateTime(dateTimeString) {
            return new Date(dateTimeString);
        }
        
        // Calculate the date that is 2 days before today
        const today = new Date();
        const twoDaysBeforeToday = new Date(today);
        twoDaysBeforeToday.setDate(today.getDate() - 2);
        if(typeof booking_data != "undefined"){
            booking_data = booking_data.filter(booking => {
                const endDate = parseDateTime(booking.date_end);
                return endDate > twoDaysBeforeToday;
            });
        }
        
        // Filter the bookings
        
        

        let dt_picker = $('#date-picker').daterangepicker({
            "opens": "left",
            autoUpdateInput: false,
            // checking attribute listing type and set type of calendar
            singleDatePicker: ($('#date-picker').attr('data-listing_type') == 'rental' ? false : true),
            timePicker: false,
            minDate: minDateC,
            maxDate: maxDateC,
            minSpan: {
                days: $('#date-picker').data('minspan')
            },
            startDate: ($('#date-picker').attr('data-listing_type') == 'rental' ? new Date() : minDateC),
            locale: {
                format: wordpress_date_format.date,
                "firstDay": parseInt(wordpress_date_format.day),
                "applyLabel": listeo_core.applyLabel,
                "cancelLabel": listeo_core.cancelLabel,
                "fromLabel": listeo_core.fromLabel,
                "toLabel": listeo_core.toLabel,
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

            isCustomDate: function(date) {

               


                var slots = wpkGetThisDateSlots(date);

                if (!slots.isFirstSlotTaken && !slots.isSecondSlotTaken)
                    return [];

                if (slots.isFirstSlotTaken && !slots.isSecondSlotTaken) {
                    return ['first-slot-taken'];
                }

                if (slots.isSecondSlotTaken && !slots.isFirstSlotTaken) {
                    return ['second-slot-taken'];
                }

            },

            isInvalidDate: function(date) {
                // working only for rental

                //booking_data = [];
               
                // Filter the array

               
               





                if (slot_bk == true) {

                    const filteredBookingData = booking_data.filter(item => {

                            
                            var { date_start, date_end } = item;
                            date_start = new Date(date_start);
                            date_start.setHours(0,0,0,0); 

                            date_end = new Date(date_end);
                            date_end.setHours(0,0,0,0); 
                            const given = date._d;
                            given.setHours(0,0,0,0); 
                            const start = date_start;
                            const end  =   date_end;
                            
                            // Check if givenDate is between startDate and endDate inclusive
                            return given >= start && given <= end;
                    });


                   // console.log(filteredBookingData);
    
    

                    let disabled_dates = [];
                    

                    //debugger;




                    if(filteredBookingData && filteredBookingData.length > 0){
                        filteredBookingData.forEach(function(bk_dd) {
                            var date1 = new Date(bk_dd.date_start);
                            var date2 = new Date(bk_dd.date_end);
                            var Difference_In_Time = date2.getTime() - date1.getTime();
                            
                            // Calculating the no. of days between
                            // two dates
                            var Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24));
                            // if(Difference_In_Days >= 3){
                            //     var datess = getDates(bk_dd.date_start,bk_dd.date_end);
                               
                            //     if(datess && datess.length > 0){
                            //         datess.forEach(function(dadtt) {
                            //             disabled_dates.push(dadtt);
                            //         })
                            //     }
                            // }

                            
                        });
                    }


                    let ev_datee = moment(date._d).format("YYYY-MM-DD");
                    if(disabled_dates && disabled_dates.length > 0){
                        if(disabled_dates.includes(ev_datee)){
                            return true;
                        }
                    }




                    var new_date = new Date();

                    new_date.setDate(new_date.getDate() - 1);

                    if (date._d >= new_date) {




                        slots_strings.forEach(function(slot, index) {



                            var slotsDates2 = slotsDates(date, slot, booking_data);

                            slotsDates2.forEach(function(datepush, index) {

                                slotsDates_data.push(datepush);
                            });


                        });

                        if (slotsDates_data.includes(moment(date._d).format("YYYY-MM-DD"))) {

                            return false;
                        } else {
                            return true;
                        }

                    } else {
                        return true;
                    }



                } else {

                    if ($('#listing_type').val() == 'event') return false;
                    if ($('#listing_type').val() == 'service' && typeof disabledDates != 'undefined') {
                        if (jQuery.inArray(date.format("YYYY-MM-DD"), disabledDates) !== -1) return true;
                    }
                    if ($('#listing_type').val() == 'rental') {

                        var slots = wpkGetThisDateSlots(date);

                        return slots.isFirstSlotTaken && slots.isSecondSlotTaken;
                    }
                }
            }

        });

        $('#date-picker').on('show.daterangepicker', function(ev, picker) {
            if (jQuery('body').hasClass('book_with_verify')) {
                $('.daterangepicker').addClass('calendar-hidden');
                jQuery('.timer-loader-new').fadeOut();
                jQuery("#varify_modal").show();
                $('.daterangepicker').addClass('calendar-hidden');
                return false;

            }else{
                $('.daterangepicker').addClass('calendar-visible calendar-animated');
                $('.daterangepicker').removeClass('calendar-hidden');
            }
            
        });
        $('#date-picker').on('hide.daterangepicker', function(ev, picker) {

            $('.daterangepicker').removeClass('calendar-visible');
            $('.daterangepicker').addClass('calendar-hidden');
        });

        function getDates(startDate, stopDate) {
            var dateArray = [];
            var currentDate = moment(startDate);
            var stopDate = moment(stopDate);
            while (currentDate <= stopDate) {
                dateArray.push( moment(currentDate).format('YYYY-MM-DD') )
                currentDate = moment(currentDate).add(1, 'days');
            }
            return dateArray;
        }

        function calculate_price() {

            var ajax_data = {
                'action': 'calculate_price',
                'listing_type': $('#date-picker').attr('listing_type'),
                'listing_id': $('input#listing_id').val(),
                'tickets': $('input#tickets').val(),
                //'nonce': nonce		
            };
            var services = [];
            // $.each($("input.bookable-service-checkbox:checked"), function(){            
            //   		services.push($(this).val());
            // });
            // $.each($("input.bookable-service-quantity"), function(){            
            //   		services.push($(this).val());
            // });
            $.each($("input.bookable-service-checkbox:checked"), function() {
                var quantity = $(this).parent().find('input.bookable-service-quantity').val();
                services.push({
                    "service": $(this).val(),
                    "value": quantity
                });
            });
            ajax_data.services = services;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: listeo.ajaxurl,
                data: ajax_data,

                success: function(data) {
                    $('#negative-feedback').fadeOut();
                    $('a.book-now').removeClass('inactive');
                    if (data.data.normal_price > 0) {
                        if (listeo_core.currency_position == 'before') {
                            if ($('.categoryName span').attr('data-cat') == 'utstr') {
                                $('.booking-normal-price span').html(data.data.multiply + ' x ' + listeo_core.currency_symbol + ' ' + data.data.normal_price);
                            } else {
                                $('.booking-normal-price span').html(listeo_core.currency_symbol + ' ' + data.data.normal_price);
                            }
                        } else {
                            if ($('.categoryName span').attr('data-cat') == 'utstr') {
                                $('.booking-normal-price span').html(data.data.multiply + ' x ' + data.data.normal_price + ' ' + listeo_core.currency_symbol);
                            } else {
                                $('.booking-normal-price span').html(data.data.normal_price + ' ' + listeo_core.currency_symbol);
                            }
                        }
                        $('.booking-normal-price').fadeIn();
                    }

                    if (data.data.services_price > 0) {
                        if (listeo_core.currency_position == 'before') {
                            $('.booking-services-cost span').html(listeo_core.currency_symbol + ' ' + data.data.services_price);
                        } else {
                            $('.booking-services-cost span').html(data.data.services_price + ' ' + listeo_core.currency_symbol);
                        }

                        $('.booking-services-cost').fadeIn();
                    }

                    if (data.data.price > 0) {
                        if (listeo_core.currency_position == 'before') {
                            $('.booking-estimated-cost span').html(listeo_core.currency_symbol + ' ' + data.data.price);
                        } else {
                            $('.booking-estimated-cost span').html(data.data.price + ' ' + listeo_core.currency_symbol);
                        }

                        $('.booking-estimated-cost').fadeIn();
                    }
                }
            });
        }
        $('.listeo-booking-widget-apply_new_coupon').on('click', function(e) {
            setTimeout(function() {
                check_booking();
            }, 4000);
        });




        // function when checking booking by widget
        function check_booking(appd = 0) {




            var rrulestr = "";
            var endrecdate = "";
            var count_rec = 0;
            let rec_bookings = [];


            if (jQuery(".repeated_check")[0] && jQuery(".repeated_check")[0] != undefined && jQuery(".repeated_check")[0].checked && jQuery(".cst_dt_from").val() != "" && jQuery(".cst_dt_to").val() != "") {
                var days = [];
                jQuery(".repeating_div").find(".mainSelect").find(".selected").each(function() {
                    days.push(jQuery(this).data("value"));
                })

                var dt_startt = new Date(jQuery(".cst_dt_from").val());
                var dt_endd = new Date(jQuery(".cst_dt_to").val());

                var end_repeat = jQuery(".end_repeat").val();
                if (end_repeat != "") {
                    end_repeat = end_repeat + ' ' + "23:59:59";

                }
                var week_day = jQuery(".week_day").val();

                let dt_startt2 = "";


                if (days.length > 0) {
                    rrulestr += 'FREQ=WEEKLY';



                    if (dt_startt != "") {

                        dt_startt2 = new Date(dt_startt).toISOString();
                        dt_startt2 = dt_startt2.replace(/-/g, '');
                        dt_startt2 = dt_startt2.replace(/:/g, '');
                        dt_startt2 = dt_startt2.split('.');
                        dt_startt2 = dt_startt2[0] + 'Z';
                        rrulestr += ';DTSTART=' + dt_startt2;

                    }
                    if (end_repeat != "") {

                        var end_untill = new Date(end_repeat).toISOString();
                        end_untill = end_untill.replace(/-/g, '');
                        end_untill = end_untill.replace(/:/g, '');
                        end_untill = end_untill.split('.');
                        end_untill = end_untill[0] + 'Z';
                        if (week_day != "" && week_day != undefined) {
                            rrulestr += ';INTERVAL=' + week_day;
                        }
                        rrulestr += ';UNTIL=' + end_untill;

                    }
                    var byday = days.join();
                    rrulestr += ';BYDAY=' + byday;
                }

                var recBooking = new rrule.RRule.fromString(rrulestr);

                //debugger;

                if (recBooking.options.until == null) {
                    var dddd = new Date(recBooking.options.dtstart);
                    dddd.setDate(dddd.getDate() + 700);
                    recBooking.options.until = dddd;
                }


                //  recBooking.options.dtstart = dt_startt;

                function isDeleted(orignalItem, booking) {
                    var allRecArr = orignalItem.recurrenceException.split(",");
                    if (allRecArr.find(item => item == booking)) {
                        return true;
                    } else {
                        return false;
                    }
                }

                function libRecExp(currentItem, eventItem) {

                    var eventTime = new Date(eventItem.date_start); //object
                    var eventHours = eventTime.getHours();
                    var eventMin = eventTime.getMinutes();
                    currentItem.setHours(eventHours, eventMin, 0, 0);
                    return currentItem.toISOString();


                }

                let recBooking2 = recBooking.all();
                rrulestr = rrulestr.replace(';DTSTART=' + dt_startt2, "");




                if (recBooking2.length > 0) {

                    recBooking2.forEach(function(item) { //Bookings List

                        var month = item.getMonth() + 1;
                        var dateee = item.getDate();

                        if (month < 10) {
                            month = "0" + month;
                        }
                        if (dateee < 10) {
                            dateee = "0" + dateee;
                        }

                        let rec_book = {};
                        rec_book["date_start"] = item.getFullYear() + "-" + month + "-" + dateee + " " + ("0" + dt_startt.getHours()).slice(-2) + ":" + ("0" + dt_startt.getMinutes()).slice(-2) + ":" + ("0" + dt_startt.getSeconds()).slice(-2);
                        rec_book["date_end"] = item.getFullYear() + "-" + month + "-" + dateee + " " + ("0" + dt_endd.getHours()).slice(-2) + ":" + ("0" + dt_endd.getMinutes()).slice(-2) + ":" + ("0" + dt_endd.getSeconds()).slice(-2);

                        var tempRecExp = libRecExp(item, rec_book);
                        rec_book['rec_exp'] = tempRecExp;



                        count_rec++;
                        // endrecdate  = item.getFullYear()+"-" + month + "-"+dateee +" "+("0"+dt_endd.getHours()).slice(-2)+":"+("0"+dt_endd.getMinutes()).slice(-2)+":"+("0"+dt_endd.getSeconds()).slice(-2);

                        rec_bookings.push(rec_book);
                    });

                }


            }
            if (rec_bookings.length > 0) {
                endrecdate = rec_bookings.slice(-1)[0].date_end;
            }



            inputClicked = true;
            if (is_open === false) {
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

            var lv = $('#listing_type').val();
            if (lv == 'rental') {
                var startDate = moment($('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                var endDate = moment($('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

                localStorage.setItem("firstDate", startDate);
                localStorage.setItem("secondDate", endDate);
            }
            var firstday = localStorage.getItem('firstDate');
            var secondday = localStorage.getItem('secondDate');
            let dtp = new Date(firstday);
            let dtk = new Date(secondday);

            const f5 = "YYYY-MM-DDTHH:mm:ss";
            var firstDateAvailableNumber = 0;
            var firstDateSelectedNumber = 0;
            var secondDateAvailableNumber = 0;
            var secondDateSelectedNumber = 0;
            var dailyPrice = parseInt(jQuery('.js-daily-price').data('price'));
            var hourPrice = parseInt(jQuery('.js-hour-price').data('price'));
            var weekendPrice = parseInt(jQuery('.js-weekly-price').data('price'));
            // 

            if (hourPrice.toString() == 'NaN') {
                hourPrice = dailyPrice;
            }
            if (weekendPrice.toString() == 'NaN') {
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
            window.setTimeout(function() {
                // secondday = $('.time-slot .endDate').attr('date');



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
                var sdgd = _secondDate.getDay();

                if (fdgd == 0) {
                    fdgd = 6;
                } else {
                    fdgd -= 1;
                }

                if (sdgd == 0) {
                    sdgd = 6;
                } else {
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
                    jQuery('.tests').filter(function() {
                        if (jQuery(this).attr('date') == `${firstProp}`) {
                            firstDateAvailableNumber++;
                            if (jQuery(this).parent().css('background-color') == 'rgb(0, 132, 116)') {
                                firstDateSelectedNumber++;
                            }
                        }
                        if (jQuery(this).attr('date') == `${secondProp}`) {
                            secondDateAvailableNumber++;
                            if (jQuery(this).parent().css('background-color') == 'rgb(0, 132, 116)') {
                                secondDateSelectedNumber++;
                            }
                        }
                    });
                }, 500);


                setTimeout(() => {

                    if (firstProp == secondProp) {
                        /*
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
                        */
                        is24 = 1;
                        if (weekendPrice == 0 && dailyPrice == 0) {
                            totalPrice += is24 * hourPrice;
                        } else if (weekendPrice == 0 && hourPrice == 0) {
                            decrease = Math.floor(is24 / 24);
                            totalPrice = decrease * dailyPrice;
                            is24 = is24 % 24
                            if (is24 > 0) {
                                totalPrice += dailyPrice;
                            }

                        } else if ((dailyPrice == 0 && hourPrice == 0) || (dailyPrice.toString() == 'NaN' && hourPrice.toString() == 'NaN')) {
                            decrease = Math.floor(is24 / 24);
                            totalPrice = decrease * weekendPrice;
                            is24 = is24 % 24
                            if (is24 > 0) {
                                totalPrice += weekendPrice;
                            }

                        } else {
                            if (firstDateAvailableNumber == firstDateSelectedNumber) {
                                if (_firstDate.getDay() == 6 || _firstDate.getDay() == 0) {
                                    totalPrice = weekendPrice;
                                } else {
                                    totalPrice = dailyPrice;
                                }
                            } else {
                                if (_firstDate.getDay() == 6 || _firstDate.getDay() == 0) {
                                    totalPrice = firstDateSelectedNumber * hourPrice;
                                    if (totalPrice > weekendPrice) {
                                        totalPrice = weekendPrice;
                                    }
                                } else {
                                    totalPrice = firstDateSelectedNumber * hourPrice;
                                    if (totalPrice > dailyPrice) {
                                        totalPrice = dailyPrice;
                                    }
                                }
                            }
                        }
                    } else {
                        /*
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
                        */
                        if (weekendPrice == 0 && dailyPrice == 0) {
                            totalPrice += is24 * hourPrice;
                        } else if (weekendPrice == 0 && hourPrice == 0) {

                            decrease = Math.floor(is24 / 24);
                            totalPrice = decrease * dailyPrice;
                            is24 = is24 % 24
                            if (is24 > 0) {
                                totalPrice += dailyPrice;
                            }

                        } else if ((dailyPrice == 0 && hourPrice == 0) || (dailyPrice.toString() == 'NaN' && hourPrice.toString() == 'NaN')) {
                            decrease = Math.floor(is24 / 24);
                            totalPrice = decrease * weekendPrice;
                            is24 = is24 % 24
                            if (is24 > 0) {
                                totalPrice += weekendPrice;
                            }

                        } else {
                            if (firstDateAvailableNumber == firstDateSelectedNumber) {
                                if (_firstDate.getDay() == 6 || _firstDate.getDay() == 0) {
                                    totalPrice = weekendPrice;
                                } else {
                                    totalPrice = dailyPrice;
                                }
                            } else {
                                if (_firstDate.getDay() == 6 || _firstDate.getDay() == 0) {
                                    totalPrice = firstDateSelectedNumber * hourPrice;
                                    if (totalPrice > weekendPrice) {
                                        totalPrice = weekendPrice;
                                    }
                                } else {
                                    totalPrice = firstDateSelectedNumber * hourPrice;
                                    if (totalPrice > dailyPrice) {
                                        totalPrice = dailyPrice;
                                    }
                                }
                            }


                            if (_numberOfDays > 1) {
                                for (var i = 1; i < _numberOfDays; i++) {
                                    midDate.setDate(_firstDate.getDate() + i);
                                    if (midDate.getDay() == 6 || midDate.getDay() == 0) {
                                        totalPrice += weekendPrice;
                                    } else {
                                        totalPrice += dailyPrice;
                                    }
                                }
                            }

                            if (secondDateAvailableNumber == secondDateSelectedNumber) {
                                if (_secondDate.getDay() == 6 || _secondDate.getDay() == 0) {
                                    totalPrice += weekendPrice;
                                } else {
                                    totalPrice += dailyPrice;
                                }
                            } else {
                                lastDayPrice = secondDateSelectedNumber * hourPrice;
                                if (_secondDate.getDay() == 6 || _secondDate.getDay() == 0) {
                                    if (lastDayPrice > weekendPrice) {
                                        totalPrice += weekendPrice;
                                    } else {
                                        totalPrice += lastDayPrice;
                                    }
                                } else {
                                    if (lastDayPrice > dailyPrice) {
                                        totalPrice += dailyPrice;
                                    } else {
                                        totalPrice += lastDayPrice;
                                    }
                                }

                            }
                        }
                    }
                }, 500);
                localStorage.setItem('totalPrice', totalPrice);

            }, 1000);
            jQuery(".booking-error-message").hide();
            jQuery('.booking-discount-price').hide();
            jQuery('.booking-post-price').hide();
            if (jQuery("#date-picker").data("slot") == true) {




                if (jQuery(".time-slots-dropdown").find("#slot").val() != "" && jQuery(".time-slots-dropdown").find("#slot").val() != undefined) {
                    jQuery('.bkk_service').removeClass("hide_bk");
                } else {
                    jQuery('.bkk_service').addClass("hide_bk");
                }
                jQuery(".time-slots-dropdown").parent().removeClass("hide_bk");



            } else {
                jQuery('.bkk_service').removeClass("hide_bk");
            }




            jQuery(".overlay").show();


            window.setTimeout(function() {
                jQuery(".overlay").show();
                var startDataSql = firstday;
                var endDataSql = secondday;
                var discount = jQuery('input[name="discount"]:checked').val();

                if ($('#date-picker').data('listing_type') == "rental") {
                    if (startDataSql == endDataSql) {
                        // Create new Date instance
                        var date_endd = new Date(endDataSql);
                        var date_startt = new Date(startDataSql);
                        // Add a day
                        date_endd.setDate(date_endd.getDate() + 1);

                        endDataSql = date_endd.getFullYear() + "-" + ('0' + (date_endd.getMonth() + 1)).slice(-2) + "-" + ('0' + date_endd.getDate()).slice(-2);

                        $('#date-picker').data('daterangepicker').setEndDate(date_endd);
                    }

                }
                if ($('#kt_docs_fullcalendar_populated').length < 1) {
                    startDataSql = moment($('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                    endDataSql = moment($('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                }
                let expp = [];

                if (jQuery(".av_dates")[0] != undefined) {

                    let datta = jQuery(".av_dates").attr("data-exp_dates");
                    datta = JSON.parse(datta);



                    datta.forEach(function(data) {

                        expp.push(data);

                    })
                }

                var sub_data = jQuery('select.sub_selector').val();
                var ajax_data = {
                    'action': 'check_avaliabity_custom',
                    'listing_type': $('#date-picker').data('listing_type'),
                    'slot_price_type': $('input[name=slot_price_type]:checked').val(),
                    'listing_id': $('input#listing_id').val(),
                    'date_start': startDataSql,
                    'date_end': endDataSql,
                    'discount': discount,
                    'coupon': $('input#coupon_code').val(),
                    'sub_listings': sub_data,
                    'rec': rrulestr,
                    'count_rec': count_rec,
                    'endrecdate': endrecdate,
                    'rec_bookings': rec_bookings,
                    'expp': expp,
                    //'nonce': nonce		
                };
                var services = [];

                $.each($("input.bookable-service-checkbox:checked"), function() {
                    var quantity = $(this).parent().find('input.bookable-service-quantity').val();
                    services.push({
                        "service": $(this).val(),
                        "value": quantity
                    });
                });

                ajax_data.services = services;

                var st = $('.startDate').text();
                var et = parseInt($('.endDate').text());
                et = et + 1;

                var kc = $('.endDate').text();
                var d = $('.endDate').parent().parent().attr('day');
                /*   if ( $('input#slot').val() ){
                       ajax_data.slot = $('input#slot').val();
                   }else{ */
                //  ajax_data.slot = `["${st} - ${kc}","${d}|0"]`;
                /* } */



                if ($('input.adults').val()) ajax_data.adults = $('input.adults').val();
                if ($('.time-picker').val()) ajax_data.hour = $('.time-picker').val();
                if ($('input#slot').val()) ajax_data.slot = $('input#slot').val();


                // loader class
                $('a.book-now').addClass('loading');
                $('a.book-now-notloggedin').addClass('loading');
                //const f6 = "YYYY-MM-DD HH:mm:ss";
                //totalDays = datediff(parseDate(moment(dtk).format(f6)), parseDate(moment(dtk).format(f6)));
                totalDays = Math.floor((Date.parse(dtk) - Date.parse(dtp)) / 86400000);
                ajax_data.totalDays = totalDays;
                ajax_data.totalPrice = totalPrice;


                //change discount  MULTIPLE AJAX REQUESTS !!!!



                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data) {
                        setTimeout(function() {
                            jQuery(".overlay").hide();
                        }, 100)

                        $(".av_days").hide();
                        $("input[name=av_days]").val("");
                        $("input[name=rec]").val("");
                        $("input[name=endrecdate]").val("");
                        $('.av_days span').html('');
                        $('.price_algo_cl').remove();

                        if (data && data.data && data.data.price_algo) {

                            $('#form-booking').append('<input class="price_algo_cl" type="hidden" value="' + data.data.price_algo + '" name="price_algo">');
                        }

                        if (jQuery("#toHours").val() != "Select time") {
                            jQuery('#toHours').removeAttr("style");
                        }
                        jQuery('.timer-loader-new').fadeOut();
                        // jQuery(".booking-error-message").show();

                        jQuery(".conflict_div").html("");

                        if ((data.data && data.data.av_dates && data.data.av_dates != undefined && data.data.av_dates != "" && data.data.av_dates.length == 0)) {
                            data.data.free_places = 0;
                            $('.booking-estimated-cost').fadeOut();

                        }
                        jQuery(".gift_noti").remove();
                        if (data.data && data.data.remaining_saldo && data.data.remaining_saldo != ""){

                            jQuery("#coupon-widget-wrapper-output").append('<div class="notification success closeable gift_noti" id="coupon_added" style="display: block;"> Ny saldo etter booking '+data.data.remaining_saldo+'kr</div>');

                        }

                        

                        // loader clas
                        if (data.success == true && (!$(".time-picker").length || is_open != false)) {
                            if (data.data.free_places > 0) {
                                const f3 = "YYYY-MM-DD HH:mm";
                                jQuery('p.show_charged').fadeIn();
                                jQuery('a.button.book-now').addClass('new_show');
                                var frm_dt = jQuery('select.cst_dt_from').html();
                                var to_dt = jQuery('select.cst_dt_to').html();
                                jQuery('#fromHours').html(frm_dt);
                                jQuery('#toHours').html(to_dt);
                                var mn = moment(dtp).format(f3);
                                var mk = moment(dtk).format(f3);
                                /* console.log(mn);
                                 console.log(mk); */
                                setTimeout(function() {
                                    jQuery('select#fromHours').val(mn);
                                    jQuery('#toHours').val(mk);
                                    console.log(mk);
                                }, 1000);




                                /* var frm_dt = data.data.from_dates;
                                var to_dt = data.data.to_dates;
                                
                                jQuery('#fromHours').html('');
                                let lmt = frm_dt.length;
                                if(lmt)
                                {
                                    for (let f = 0; f < lmt; f++) {
                                    var pk = frm_dt[f];
                                    jQuery('#fromHours').append(pk);
                                    }
                                }
                                let lmto = to_dt.length;
                                if(lmto)
                                {
                                    jQuery('#toHours').html('');
                                    for (let z = 0; z < lmto; z++) {
                                        var pkp = to_dt[z];
                                        jQuery('#toHours').append(pkp);
                                    }
                                }
                              
                                jQuery('input#timeSpanFrom').val(moment(dtp).format(f3));
                                jQuery('input#timeSpanTo').val(moment(dtk).format(f3));  */
                                if (appd == 0) {
                                    if (jQuery('#kt_docs_fullcalendar_populated').length) {
                                        /*jQuery('.from_tm').datetimepicker('hide');
                                        jQuery('.to_tim').datetimepicker('hide'); */
                                        jQuery('.add_event_modal').fadeOut();
                                        jQuery('span.max_av').text(data.data.free_places);
                                        if (evt) {
                                            //  alert('test');
                                            evt.setDates(moment(dtp).format(f5), moment(dtk).format(f5));
                                            //console.log("Event Start: "+moment(dtp).format(f5));
                                            //console.log("Event End: "+moment(dtk).format(f5));
                                            /*rec_bookings.forEach(function(valueBooking,indexBooking){

                                                var ddd_date = valueBooking.split(" ")[0];

                                                var ddd_start = ddd_date+" "+mn.split(" ")[1];

                                                var ddd_end = ddd_date+" "+mk.split(" ")[1];

                                                let bk_start = new Date(ddd_start);
                                                let bk_end = new Date(ddd_end);

                                                evt.setDates( moment(bk_start).format(f5), moment(bk_end).format(f5));

                                            });*/
                                        } else {

                                            if ((data.data && data.data.av_dates && data.data.av_dates != undefined && data.data.av_dates != "" && data.data.av_dates.length > 0)) {
                                                data.data.av_dates.forEach(function(valueBooking, indexBooking) {

                                                    //   var ddd_date = valueBooking.split(" ")[0];

                                                    /* var ddd_start = ddd_date+" "+mn.split(" ")[1];

                                                     var ddd_end = ddd_date+" "+mk.split(" ")[1];*/
                                                    var ddd_start = valueBooking["date_start"];

                                                    var ddd_end = valueBooking["date_end"];

                                                    let bk_start = new Date(ddd_start);
                                                    let bk_end = new Date(ddd_end);

                                                    var title = moment(bk_start).format("HH:mm") + "-" + moment(bk_end).format("HH:mm");
                                                    title += "<br>Valgt tid";
                                                    /*  title += "<br>"+jQuery(".purpose").val(); */

                                                    evt = calendar.addEvent({
                                                        groupId: 'new_event',
                                                        title: title,
                                                        start: moment(bk_start).format(f5),
                                                        end: moment(bk_end).format(f5),
                                                        color: '#008474',
                                                        overlap: false,
                                                        /* purpose : jQuery(".purpose").val() */

                                                    });

                                                });
                                            } else {
                                                var title = moment(dtp).format("HH:mm") + "-" + moment(dtk).format("HH:mm");
                                                title += "<br>Valgt tid";
                                                /*   title += "<br>"+jQuery(".purpose").val(); */

                                                evt = calendar.addEvent({
                                                    groupId: 'new_event',
                                                    title: title,
                                                    start: moment(dtp).format(f5),
                                                    end: moment(dtk).format(f5),
                                                    color: '#008474',
                                                    overlap: false,
                                                    /*      purpose : jQuery(".purpose").val() */

                                                });
                                            }
                                            jQuery('button.fc-addEventButton-button.fc-button.fc-button-primary').addClass('active');
                                            jQuery('.fc-addEventButton-button').text('Edit Event');
                                        }
                                    }
                                }

                                // console.log(event);

                                $('a.book-now').data('freeplaces', data.data.free_places);
                                $('.booking-error-message').fadeOut();
                                $('a.book-now').removeClass('inactive');


                                if (data.data.discount_price != undefined && data.data.discount_price > 0) {

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
                                if (data.data.normalprice != undefined) {
                                    data.data.normal_price = data.data.normalprice;
                                }

                                if (parseInt(data.data.normal_price) > 0) {
                                    // Add services, tax, normal at normal price
                                    var allValuesTaxes = 0;
                                    if (data.data.normal_price) {
                                        allValuesTaxes += data.data.normal_price;
                                    }
                                    // if(data.data.services_price){
                                    // 	allValuesTaxes += data.data.services_price;
                                    // }
                                    if (data.data.taxprice) {
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

                                    $('.booking-normal-price').fadeOut();
                                    $('.free-booking').fadeOut();
                                } else {
                                    $('.booking-normal-price').fadeOut();
                                    $('.free-booking').fadeIn();
                                }

                                if (data.data.services_price > 0) {
                                    if (listeo_core.currency_position == 'before') {
                                        $('.booking-services-cost span').html(listeo_core.currency_symbol + ' ' + data.data.services_price);
                                    } else {
                                        $('.booking-services-cost span').html(data.data.services_price + ' ' + listeo_core.currency_symbol);
                                    }

                                    $('.booking-services-cost').fadeIn();
                                } else {
                                    //$('.booking-services-cost span').html( 'GRATIS');
                                    $('.booking-services-cost').fadeOut();
                                }

                                if (typeof data.data.price === 'string') {
                                    if (data.data.price.includes(",")) {

                                        data.data.price = data.data.price.replace(",", "");

                                    }
                                }




                                if (parseInt(data.data.price) > 0) {
                                    var _total_price = 0;
                                    var _coupon_price = 0;

                                    _total_price = parseFloat(data.data.price);

                                    //debugger;

                                    if (data.data.coupon_price) {
                                        _coupon_price = data.data.coupon_price;
                                        //   _total_price = data.data.normal_price + data.data.services_price + data.data.taxprice;
                                        if (data.data && data.data.with_coupon_rec_price && data.data.with_coupon_rec_price != undefined && data.data.with_coupon_rec_price != "") {
                                            _total_price = data.data.with_coupon_rec_price;
                                        }
                                        // _total_price = data.data.normal_price + data.data.taxprice;

                                        if (listeo_core.currency_position == 'before') {
                                            $('.booking-estimated-discount-cost span').html(listeo_core.currency_symbol + ' ' + _total_price);
                                        } else {
                                            $('.booking-estimated-discount-cost span').html(_total_price + ' ' + listeo_core.currency_symbol);
                                        }

                                        _total_price = _total_price + _coupon_price;

                                        $('.booking-estimated-cost').addClass('estimated-with-discount');
                                        $('.booking-estimated-discount-cost').fadeIn();
                                    } else if (data.data.price_discount) {
                                        _coupon_price = data.data.price_discount;
                                        // _total_price = data.data.normal_price + data.data.taxprice;

                                        if (listeo_core.currency_position == 'before') {
                                            $('.booking-estimated-discount-cost span').html(listeo_core.currency_symbol + ' ' + _coupon_price);
                                        } else {
                                            $('.booking-estimated-discount-cost span').html(_coupon_price + ' ' + listeo_core.currency_symbol);
                                        }
                                        $('.booking-estimated-cost').addClass('estimated-with-discount');
                                        $('.booking-estimated-discount-cost').fadeIn();
                                    } else {
                                        _total_price = parseInt(data.data.price);
                                        $('.booking-estimated-cost').removeClass('estimated-with-discount');
                                        $('.booking-estimated-discount-cost').fadeOut();
                                    }
                                    /* if(data.data.multiply != undefined){
                                         _total_price = parseInt(data.data.multiply)*parseInt(allValuesTaxes);
                                     }*/
                                    //alert(_total_price);

                                    if (listeo_core.currency_position == 'before') {
                                        // $('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);
                                        $('.booking-estimated-cost span').html(listeo_core.currency_symbol + ' ' + _total_price);
                                        $('.booking-estimated-cost div.tax-span').html(listeo_core.currency_symbol + ' ' + data.data.taxprice + data.data.services_tax_price);
                                    } else {
                                        // $('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);
                                        $('.booking-estimated-cost span').html(_total_price + ' ' + listeo_core.currency_symbol);
                                        $('.booking-estimated-cost div.tax-span').html(data.data.taxprice + data.data.services_tax_price + ' ' + listeo_core.currency_symbol);
                                    }

                                    $('.booking-estimated-cost').fadeIn();
                                    $('.coupon-widget-wrapper').fadeIn();
                                } else {
                                    $('.booking-estimated-cost span').html('0 ' + listeo_core.currency_symbol);

                                    $('.booking-estimated-cost').fadeIn();
                                }



                                if (data.data && data.data.av_days && data.data.av_days != undefined && data.data.av_days != "") {
                                    $(".av_days").show();
                                    $('.av_days span').html(data.data.av_days);
                                    $("input[name=av_days]").val(data.data.av_days);
                                    $("input[name=rec]").val(data.data.rec);
                                    $("input[name=endrecdate]").val(data.data.endrecdate);
                                }

                                if ((data.data && data.data.conflicts_dates && data.data.conflicts_dates != undefined && data.data.conflicts_dates != "" && data.data.conflicts_dates.length > 0)) {
                                    let conflicts_dates = JSON.stringify(data.data.conflicts_dates);
                                    let dayy = "";
                                    if (data.data.conflicts_dates.length == 1) {
                                        dayy = "dag";
                                    } else {
                                        dayy = "dager";
                                    }
                                    jQuery(".conflict_div").append('<div class="alert alert-danger conflicts_dates" data-conflicts_dates=\'' + conflicts_dates + '\' role="alert">' + data.data.conflicts_dates.length + ' ' + dayy + ' <b>utilgjengelig</b> i valgt tid.<br>Klikk her for å se utilgjengelige dager.</div>');
                                }
                                if ((data.data && data.data.av_dates && data.data.av_dates != undefined && data.data.av_dates != "" && data.data.av_dates.length > 0)) {

                                    let av_dates = JSON.stringify(data.data.av_dates);
                                    let exp_dates = JSON.stringify([]);
                                    if ((data.data.exp_dates && data.data.exp_dates != undefined && data.data.exp_dates != "" && data.data.exp_dates.length > 0)) {
                                        exp_dates = JSON.stringify(data.data.exp_dates);

                                    }
                                    let dayy = "";
                                    if (data.data.conflicts_dates.length == 1) {
                                        dayy = "dag";
                                    } else {
                                        dayy = "dager";
                                    }
                                    jQuery(".conflict_div").append('<div class="alert alert-success av_dates"  data-av_dates=\'' + av_dates + '\' data-exp_dates=\'' + exp_dates + '\'  role="alert">' + data.data.av_days + ' ' + dayy + ' er tilgjengelig i valgt tid. .<br>Klikk her for å se tilgjengelige dager.</div>');
                                }


                            } else {

                                if (calendar && calendar != undefined) {

                                    var cl = calendar.getEvents();
                                    if (cl && cl.length > 0) {
                                        cl.forEach(function(value, index) {
                                            if (value.groupId == "new_event") {
                                                value.remove();
                                            }

                                        });
                                    }

                                }


                                $('a.book-now').data('freeplaces', 0);
                                if (jQuery('.categoryName span').attr('data-cat') == 'utstr') {
                                    jQuery([document.documentElement, document.body]).animate({
                                        scrollTop: jQuery("#equipmentCalendar").offset().top - 200
                                    }, 2000);

                                    jQuery('#equipmentCalendar').css({
                                        border: '0 solid red'
                                    }).animate({
                                        borderWidth: 4
                                    }, 500);

                                    setTimeout(() => {
                                        jQuery('#equipmentCalendar').animate({
                                            borderWidth: 0
                                        }, 500);
                                    }, 1500);
                                }
                                if ($('#slot').length > 0) {} else {
                                    if (endDataSql) {
                                        $('.booking-error-message').fadeIn();
                                    }
                                }
                                $('.booking-estimated-cost').fadeOut();
                                $('.booking-estimated-cost span').html('');
                            }
                        } else {
                            $('a.book-now').data('freeplaces', 0);
                            if (jQuery('.categoryName span').attr('data-cat') == 'utstr') {
                                jQuery([document.documentElement, document.body]).animate({
                                    scrollTop: jQuery("#equipmentCalendar").offset().top - 200
                                }, 2000);

                                jQuery('#equipmentCalendar').css({
                                    border: '0 solid red'
                                }).animate({
                                    borderWidth: 4
                                }, 500);

                                setTimeout(() => {
                                    jQuery('#equipmentCalendar').animate({
                                        borderWidth: 0
                                    }, 500);
                                }, 1500);
                            }
                            if (endDataSql) {
                                $('.booking-error-message').fadeIn();
                            }
                            $('.booking-estimated-cost').fadeOut();
                        }

                        $('a.book-now').removeClass('loading');
                        $('a.book-now-notloggedin').removeClass('loading');


                        if ($('#divtoshow').is(':visible')) {
                            $('.booking-estimated-cost').hide();
                            $('.booking-services-cost').hide();
                            $('.booking-normal-price').hide();
                            $('.free-booking').fadeOut();
                        } else {
                            if (data.data.price > 0) {
                                jQuery('.booking-estimated-cost').show();
                                jQuery(".coupon-widget-wrapper").show();
                            }
                        }

                        if (data.data.free_places == 0) {
                            jQuery('.booking-estimated-cost').hide();
                            jQuery(".coupon-widget-wrapper").hide();
                            jQuery(".show_charged").hide();
                            jQuery(".book-now").removeClass("new_show");
                            $('.booking-error-message').fadeIn();
                        }

                        // if(data.data.price <= 0 && data.data.services_price <= 0 && data.data.normal_price <= 0){
                        // 	$('.free-booking').fadeIn();
                        // }else{
                        // 	$('.free-booking').fadeOut();
                        // }
                    }
                });
            }, 500);

        }

        var is_open = true;
        var lastDayOfWeek;

        jQuery(document).on("click", ".conflicts_dates", function() {

            jQuery(".weeklymodal").find(".weeklybody").html("");
            jQuery(".weeklymodal").find(".title").html("");
            jQuery(".weeklymodal").show();
            jQuery(".weeklymodal").find(".title").html("Utilgjengelige dager");


            let html = "<ul class='conflict_ul'>";

            let datta = jQuery(this).data("conflicts_dates");

            datta.forEach(function(data) {
                html += "<li>" + moment(data.date_start).format("dddd DD.MM.YYYY") + "</li>";
            })
            html += "</ul>";

            jQuery(".weeklymodal").find(".weeklybody").html(html);


        });
        jQuery(document).on("click", ".av_dates", function() {

            jQuery(".weeklymodal").find(".weeklybody").html("");
            jQuery(".weeklymodal").find(".title").html("");
            jQuery(".weeklymodal").show();
            jQuery(".weeklymodal").find(".title").html("Tilgjengelige dager");

            let expp = [];

            if (jQuery(".av_dates")[0] != undefined) {

                let datta = jQuery(".av_dates").attr("data-exp_dates");
                datta = JSON.parse(datta);



                datta.forEach(function(data) {
                    expp.push(data);

                })
            }


            let html = "<ul class='av_dates_ul'>";

            let datta = jQuery(this).data("av_dates");

            datta.forEach(function(data) {

                let trash_dd = "<i class='fa fa-trash add_to_exp' data-date='" + data.rec_exp + "'></i>";

                let has_exp = false;

                expp.forEach(function(exp) {
                    if (exp == data.rec_exp) {
                        has_exp = true;
                    }

                })
                if (has_exp) {
                    trash_dd = "<i class='fa-regular fas-trash-can-undo remove_to_exp' data-date='" + data.rec_exp + "'></i>";
                }

                html += "<li>" +
                    "<span class='left'>" + moment(data.date_start).format("dddd DD.MM.YYYY") + "</span>" +
                    "<span class='right'>" + trash_dd + "</span>" +
                    "</li>";
            })
            html += "</ul>";
            /*<i class=\"fa-regular fa-trash-can-undo\"></i>*/
            jQuery(".weeklymodal").find(".weeklybody").html(html);


        });

        jQuery(document).on("click", ".add_to_exp", function() {

            let data_date = jQuery(this).data("date");

            jQuery(this).addClass("remove_to_exp").addClass("fa-regular").addClass("fas-trash-can-undo");
            jQuery(this).removeClass("fa").removeClass("fa-trash").removeClass("add_to_exp");

            if (jQuery(".av_dates")[0] != undefined) {

                let datta = jQuery(".av_dates").attr("data-exp_dates");
                datta = JSON.parse(datta);

                let expp = [];

                datta.forEach(function(data) {
                    expp.push(data);

                })

                expp.push(data_date);

                let exp_date = JSON.stringify(expp);

                jQuery(".av_dates").attr("data-exp_dates", exp_date);
            }



        });
        jQuery(document).on("click", ".remove_to_exp", function() {

            let data_date = jQuery(this).data("date");
            jQuery(this).addClass("fa").addClass("fa-trash").addClass("add_to_exp");
            jQuery(this).removeClass("fa-regular").removeClass("fas-trash-can-undo").removeClass("remove_to_exp");


            if (jQuery(".av_dates")[0] != undefined) {

                let datta = jQuery(".av_dates").attr("data-exp_dates");
                datta = JSON.parse(datta);

                let expp = [];

                datta.forEach(function(data) {
                    if (data_date != data) {
                        expp.push(data);
                    }

                })

                let exp_date = JSON.stringify(expp);

                jQuery(".av_dates").attr("data-exp_dates", exp_date);
            }



        });



        jQuery(document).on("click", ".apply_conflict", function() {

            jQuery('.weeklymodal').hide();
            check_booking();


        });




        // update slots and check hours setted to this day
        function update_booking_widget(ev, picker) {

            if (picker) {
                if (jQuery("#date-picker").data("listing_type") == "rental") {
                    $('#date-picker').val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                } else {
                    $('#date-picker').val(picker.startDate.format('DD/MM/YYYY'));
                }
            }

            // function only for services
            if ($('#date-picker').data('listing_type') != 'service') return;
            $('a.book-now').addClass('loading');
            $('a.book-now-notloggedin').addClass('loading');
            // get day of week
            var date = $('#date-picker').data('daterangepicker').endDate._d;
            var dayOfWeek = date.getDay() - 1;

            if (date.getDay() == 0) {
                dayOfWeek = 6;
            }



            var firstday = localStorage.getItem('firstDate');



            var secondday;
            window.setTimeout(function() {
                secondday = $('.time-slot .endDate').attr('date');


            }, 100);

            window.setTimeout(function() {
                var startDataSql = firstday;
                var endDataSql = secondday;

                //if(startDataSql == "" || startDataSql == null){
                if ($('#kt_docs_fullcalendar_populated').length < 1) {
                    startDataSql = moment($('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                    endDataSql = moment($('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                }



                var ajax_data = {
                    'action': 'update_slots',
                    'listing_id': $('input#listing_id').val(),
                    'slot_price_type': $('input[name=slot_price_type]:checked').val(),
                    'date_start': startDataSql,
                    'date_end': endDataSql,
                    'slot': dayOfWeek
                    //'nonce': nonce		
                };
                $('.time-slots-dropdown a').addClass('loading_slot');

                jQuery(".overlay").show();

                $('.panel-dropdown-scrollable .time-slot input').prop("checked", false);

                $('.panel-dropdown.time-slots-dropdown input#slot').val('');
                $('.panel-dropdown.time-slots-dropdown a').html($('.panel-dropdown.time-slots-dropdown a').attr('placeholder'));
                $(' .booking-estimated-cost span').html(' ');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,


                    success: function(data) {
                        jQuery(".overlay").hide();

                        $('.time-slots-dropdown a').removeClass('loading_slot');

                        // if(data.data == "empty"){
                        //     data.data = false;
                        // }

                        if (data.data == false) {
                            $('.time-slots-dropdown .panel-dropdown-scrollable').html("");
                        } else {

                            $('.time-slots-dropdown .panel-dropdown-scrollable').html(data.data);
                            $('.time-slots-dropdown').addClass('active');
                        }

                        // reset values of slot selector
                        if (dayOfWeek != lastDayOfWeek) {

                            $('.panel-dropdown-scrollable .time-slot input').prop("checked", false);

                            $('.panel-dropdown.time-slots-dropdown input#slot').val('');
                            $('.panel-dropdown.time-slots-dropdown a').html($('.panel-dropdown.time-slots-dropdown a').attr('placeholder'));
                            $(' .booking-estimated-cost span').html(' ');

                        }

                        lastDayOfWeek = dayOfWeek;

                        if (data.data == false) {

                            $('.no-slots-information').show();
                            $('.panel-dropdown.time-slots-dropdown a').html($('.no-slots-information').html());

                        } else {

                            // when we dont have slots for this day reset cost and show no slots
                            $('.no-slots-information').hide();
                            $(' .booking-estimated-cost span').html(' ');


                        }
                        // show only slots for this day
                        // $( '.panel-dropdown-scrollable .time-slot' ).hide( );
                        var cou = 0;
                        var firstinput;
                        var secondinput;

                        // $( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).show( );
                        $(".time-slot").each(function() {
                            var timeSlot = $(this);
                            $(this).find('input').on('change', function() {
                                var timeSlotVal = timeSlot.find('strong').text();
                                var slotArray = [timeSlot.find('strong').text(), timeSlot.find('input').val()];




                                $('.panel-dropdown.time-slots-dropdown input#slot').val(jQuery("input[name=time-slot]:checked").val());

                                $('.panel-dropdown.time-slots-dropdown a').html(timeSlotVal);
                                $('.panel-dropdown').removeClass('active');



                                check_booking();

                                setTimeout(function() {
                                    $('.bkk_service').removeClass("hide_bk");
                                    if ($('input[name=slot_price_type]:checked').val() == "all_slot_price") {
                                        $(".guests_drp").hide();
                                    } else {
                                        if($(".guests_drp").hasClass("hide_guest")){

                                        }else{
                                            $(".guests_drp").show();
                                        }
                                        
                                    }

                                }, 200)
                                if ($('input[name=slot_price_type]:checked').val() && $('input[name=slot_price_type]:checked').val() == "all_slot_price") {
                                    $(".guests_drp").hide();
                                    $('input.adults').val($(".slot_avv").val());
                                    $('input.adults').change();
                                } else {
                                    $('input.adults').val("1");
                                    $('input.adults').change();
                                    if($(".guests_drp").hasClass("hide_guest")){

                                    }else{
                                        $(".guests_drp").show();
                                    }
                                };


                            });
                        });
                        /*$(".time-slot").each(function() {
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
                        });*/
                        $('a.book-now').removeClass('loading');
                        $('a.book-now-notloggedin').removeClass('loading');

                    }
                });

            }, 100)

            // check if opening days are active
            if ($(".time-picker").length) {
                if (availableDays) {


                    if (availableDays[dayOfWeek].opening == 'Closed' || availableDays[dayOfWeek].closing == 'Closed') {

                        $('#negative-feedback').fadeIn();

                        //$('a.book-now').css('background-color','grey');

                        is_open = false;
                        return;
                    }

                    // converent hours to 24h format
                    var opening_hour = moment(availableDays[dayOfWeek].opening, ["h:mm A"]).format("HH:mm");
                    var closing_hour = moment(availableDays[dayOfWeek].closing, ["h:mm A"]).format("HH:mm");


                    // get hour in 24 format
                    var current_hour = $('.time-picker').val();


                    // check if currer hour bar is open
                    if (current_hour >= opening_hour && current_hour <= closing_hour) {

                        is_open = true;
                        $('#negative-feedback').fadeOut();
                        $('a.book-now').attr('href', '#').css('background-color', '#f30c0c');
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
        if ($('.time-slot').length) {
            update_booking_widget();
        }

        // show only services for actual day from datapicker
        $('#date-picker').on('apply.daterangepicker', update_booking_widget);
        $('#date-picker').on('change', function() {
            if ($('#slot').length > 0) {

            } else {
                check_booking();
                update_booking_widget();
            }
            $('.panel-dropdown.time-slots-dropdown input#slot').val("");
        });

        $('#date-picker').on('apply.daterangepicker', check_booking);
        $('#date-picker').on('cancel.daterangepicker', check_booking);

        $(document).on("change", 'input.bookable-service-quantity, .form-booking-service input.bookable-service-checkbox,.form-booking-rental input.bookable-service-checkbox', function(event) {

            check_booking();
        });
        $(document).on("change", 'input[name=slot_price_type]', function(event) {

            //check_booking();
            //jQuery(".bkk_service").addClass("hide_bk");
            // update_booking_widget();
            if (dt_picker.val() != "") {
                dt_picker.trigger('apply');
            }

           // alert(this.value)

            if (this.value == "all_slot_price") {
                $(".guests_drp").hide();
            }
            //jQuery(".bkk_service").addClass("hide_bk");

        });
        $(document).on("click", '.updateqty-btn', function(event) {
            jQuery(".book-now").removeClass("trigger-qty")
            jQuery(".panel-dropdown").removeClass("active");
            check_booking();
        })
        //$('input#slot').on( 'change', check_booking );

        $('input#tickets,.form-booking-event input.bookable-service-checkbox').on('change', function(e) {
            //check_booking();
            calculate_price();
        });


        // hours picker
        if ($(".time-picker").length) {
            var time24 = false;

            if (listeo_core.clockformat) {
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

            if ($('#_hour_end').length) {
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
            if (oldValue == 2) {
                //$button.parents('.single-service').find('label').trigger('click');
                $button.parents('.single-service').find('input.bookable-service-checkbox').prop("checked", true);
                updateCounter();
            }
        });


        if ($("#booking-date-range").length) {

            // to update view with bookin

            var bookingsOffset = 0;

            // here we can set how many bookings per page
            var bookingsLimit = 5;

            // function when checking booking by widget
            function listeo_bookings_manage(page) {

                if ($('#booking-date-range').data('daterangepicker')) {
                    var startDataSql = moment($('#booking-date-range').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
                    var endDataSql = moment($('#booking-date-range').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

                } else {
                    var startDataSql = '';
                    var endDataSql = '';
                }
                if (!page) {
                    page = 1
                }

                // preparing data for ajax
                var ajax_data = {
                    'action': 'listeo_bookings_manage',
                    'date_start': startDataSql,
                    'date_end': endDataSql,
                    'listing_id': $('#listing_id').val(),
                    'listing_status': $('#listing_status').val(),
                    'dashboard_type': $('#dashboard_type').val(),
                    'limit': bookingsLimit,
                    'offset': bookingsOffset,
                    'page': page,
                    //'nonce': nonce		
                };


                // display loader class
                $(".dashboard-list-box").addClass('loading');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data) {


                        // display loader class
                        $(".dashboard-list-box").removeClass('loading');

                        if (data.data.html) {
                            $('#no-bookings-information').hide();
                            $("ul#booking-requests").html(data.data.html);
                            $(".pagination-container").html(data.data.pagination);
                        } else {
                            $("ul#booking-requests").empty();
                            $(".pagination-container").empty();
                            $('#no-bookings-information').show();
                        }

                    }
                });

            }

            // hooks for get bookings into view
            $('#booking-date-range').on('apply.daterangepicker', function(e) {
                listeo_bookings_manage();
            });
            $('#listing_id').on('change', function(e) {
                listeo_bookings_manage();
            });
            $('#listing_status').on('change', function(e) {
                listeo_bookings_manage();
            });

            $('div.pagination-container').on('click', 'a', function(e) {
                e.preventDefault();

                var page = $(this).parent().data('paged');

                listeo_bookings_manage(page);

                $('body, html').animate({
                    scrollTop: $(".dashboard-list-box").offset().top
                }, 600);

                return false;
            });


            $(document).on('click', '.reject, .cancel', function(e) {
                e.preventDefault();
                if (window.confirm(listeo_core.areyousure)) {
                    var $this = $(this);
                    $this.parents('li').addClass('loading');
                    var status = 'confirmed';
                    if ($(this).hasClass('reject')) status = 'cancelled';
                    if ($(this).hasClass('cancel')) status = 'cancelled';

                    // preparing data for ajax
                    var ajax_data = {
                        'action': 'listeo_bookings_manage',
                        'booking_id': $(this).data('booking_id'),
                        'status': status,
                        //'nonce': nonce		
                    };
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: listeo.ajaxurl,
                        data: ajax_data,

                        success: function(data) {

                            // display loader class
                            $this.parents('li').removeClass('loading');

                            listeo_bookings_manage();

                        }
                    });
                }
            });

            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                if (window.confirm(listeo_core.areyousure)) {
                    var $this = $(this);
                    $this.parents('li').addClass('loading');
                    var status = 'deleted';

                    // preparing data for ajax
                    var ajax_data = {
                        'action': 'listeo_bookings_manage',
                        'booking_id': $(this).data('booking_id'),
                        'status': status,
                        //'nonce': nonce		
                    };
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: listeo.ajaxurl,
                        data: ajax_data,

                        success: function(data) {

                            // display loader class
                            $this.parents('li').removeClass('loading');

                            listeo_bookings_manage();

                        }
                    });
                }
            });


            $(document).on('click', '.approve', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'confirmed';
                if ($(this).hasClass('reject')) status = 'cancelled';
                if ($(this).hasClass('cancel')) status = 'cancelled';

                // preparing data for ajax
                var ajax_data = {
                    'action': 'listeo_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce		
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data) {

                        // display loader class
                        $this.parents('li').removeClass('loading');

                        listeo_bookings_manage();

                    }
                });

            });
            $(document).on('click', '.mark-as-paid', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'paid';

                // preparing data for ajax
                var ajax_data = {
                    'action': 'listeo_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce		
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: listeo.ajaxurl,
                    data: ajax_data,

                    success: function(data) {

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



            $('#booking-date-range-enabler').on('click', function(e) {
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
                        "applyLabel": listeo_core.applyLabel,
                        "cancelLabel": listeo_core.cancelLabel,
                        "fromLabel": listeo_core.fromLabel,
                        "toLabel": listeo_core.toLabel,
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
    $(document).on('change', 'select.sub_selector', function() {
        var kl = $(this).val();
        /*
        var url      = window.location.href;
        url = url+"?sub="+kl;
        if(kl!='')
        {
            location.href = url;
        }
        else
        {
            url = url.split('?')[0];
            location.href = url;
        }
        /*var main_container = $(this).parent().parent().parent().parent();
        if(kl!='')
        {
            var ajax_data_new = {
                'action'		: 'get_updated_resvs', 
                'listing_id' 	: 	kl
                //'nonce': nonce		
            };
            jQuery.post(ajax_object.ajax_url, ajax_data_new, function(response) {
                main_container.html(response);
            });
        
        }
        */
        return false;
    });
    $(document).on('click', '.apply_sub', function() {
        var kl = $('.sub_selector').val();
        var url = location.protocol + '//' + location.host + location.pathname

        /* var url      = window.location.href; */
        url = url + "?sub=" + kl;
        if (kl != '') {
            location.href = url;
        } else {
            url = url.split('?')[0];
            location.href = url;
        }
        return false;
    });

    function parseDate(str) {
        var mdy = str.split('/');
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }

    function datediff(first, second) {
        // Take the difference between the dates and divide by milliseconds per day.
        // Round to nearest whole number to deal with DST.
        return Math.round((second - first) / (1000 * 60 * 60 * 24));
    }
})(this.jQuery);


jQuery(document).on("click", ".icon_rp", function() {
    jQuery(this).parent().find("input").click();
})
jQuery(document).on("change", ".repeated_check", function() {
    if (this.checked == true) {
        let ddd = ["SU", "MO", "TU", "WE", "TH", "FR", "SA"];
        var cst_dt_from = jQuery(".cst_dt_from").val();
        if (cst_dt_from != "") {
            var d_number = moment(cst_dt_from).format('d');

            let week_day = ddd[parseInt(d_number)];

            setTimeout(function() {
                jQuery(".k-recur-weekday-buttons").find("span[data-value=" + week_day + "]").click();
            }, 200)


        }
    }
})

jQuery(".qtyButtons").append("<button type='button' class='btn btn-primary updateqty-btn'>Velg</button>")

jQuery(".adults").change(function() {
    jQuery(".qtyTotal").html(this.value);
    jQuery(".book-now").addClass("trigger-qty")
})
jQuery(document).on("click",".xoo-el-login-tgr",function() {
    setTimeout(function(){
        jQuery("#lg_reg_modal").show();
        jQuery("#lg_reg_modal").addClass("show");
    },100)
})