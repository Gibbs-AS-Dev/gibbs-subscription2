jQuery(function($){

    var storageKey = "booking_timer_data";

    var booking_data = JSON.parse(localStorage.getItem(storageKey)) || [];
    if(booking_data && booking_data.length > 0){

        $page_name = "";

        if(typeof hasPage != "undefined"){
            $page_name = hasPage;
        }

        

        function closeBooking(close_bk_id){

            var data = {
                action: 'payment_failed_callback', 
                booking_id: close_bk_id
            };
    
            // Perform AJAX request
            $.post(myAjax.ajaxurl, data, function(response) {
                console.log('Response from server: ' + response);
            });

            var booking_data = JSON.parse(localStorage.getItem(storageKey)) || [];
            booking_data = booking_data.filter(booking => parseInt(booking.bk_id) !== parseInt(close_bk_id));

            localStorage.setItem(storageKey, JSON.stringify(booking_data));
    
            // Remove the HTML element
            $("#booking_timer_" + close_bk_id).remove();

        }

        function displayTimers(booking_data) {
            $(".booking-timer").remove();

            var timerHtml = "<div class='main-booking-timer'>";
    
            booking_data.forEach(function(booking) {
                timerHtml += `
                    <div class="row booking-timer" id="booking_timer_${booking.bk_id}">
                        <div class="col-md-12 listing_title" style="margin-top:15px">
                            <div class="alert alert-info" role="alert">
                                <div class=info-div"">
                                    <span> Fullfør bestillingen før reservasjonen utløper:
                                    </span>
                                    <span id="bk_timer_${booking.bk_id}"></span>
                                </div>
                                <div class="timer-btns">
                                    <button class="btn btn-primary complete_bk" data-bkid="${booking.bk_id}" data-url="${booking.current_url}">Fullfør</button>
                                    <button class="btn btn-secondary close_bk" data-bkid="${booking.bk_id}">Avslutt</button>
                                </div>
                            </div>
                            
                        </div> 
                    </div>
                `;
            });
            timerHtml += "</div>";
            $("body").append(timerHtml);

            if (window.location.href.includes("gibbspay")) {
                $(".timer-btns").hide();
            }
            
        }
    
        // Display timers initially
        displayTimers(booking_data);

        let intervalId = null;
    
        // Function to update all countdown timers
        function updateTimers() {
            var booking_data = JSON.parse(localStorage.getItem(storageKey)) || [];
            
    
            booking_data = booking_data.filter(function(booking) {
                if (booking.time > 0) {
                    booking.time--;
    
                    // Format minutes and seconds
                    var mins = Math.floor(booking.time / 60);
                    var secs = Math.floor(booking.time % 60);
                    mins = mins < 10 ? "0" + mins : mins;
                    secs = secs < 10 ? "0" + secs : secs;
    
                    // Update UI
                    $("#bk_timer_" + booking.bk_id).html(mins + ":" + secs);
    
                    return true; // Keep active timers
                } else {
                    // Timer expired - display message and remove
                    $("#bk_timer_" + booking.bk_id).html("Utgått");
                    setTimeout(() => {
                        $("#booking_timer_" + booking.bk_id).remove();
                    }, 2000);
                    closeBooking(booking.bk_id);

                    if($page_name == "form-pay"){
                        window.location.href = booking.listing_linkk;
                    }else{
                        var timerHtml2 = `<div class='close-body-timer'>
                                            <div class="row booking-timer">
                                                <div class="col-md-12 listing_title" style="margin-top:15px">
                                                    <div class="alert alert-info" role="alert">
                                                        <div class=info-div"">
                                                            <span> Time has run out. Would you like to try again. 
                                                            </span>
                                                        </div>
                                                        <div class="close-timer-btns d-flex justify-content-center align-items-center gap-5">
                                                            <a href="${booking.listing_linkk}"><button class="btn btn-primary">Try Again</button></a>
                                                            <button class="btn btn-secondary close_body_timer"">Avslutt</button>
                                                        </div>
                                                    </div>
                                                    
                                                </div> 
                                            </div>
                                        </div>`;

                        jQuery("body").append(timerHtml2);
                    }

                    if(intervalId){
                        clearInterval(intervalId);
                    }

                    

    
                    return false; // Remove expired timer
                }
            });
    
            // Save updated timer list
            localStorage.setItem(storageKey, JSON.stringify(booking_data));
        }
    
        // Start countdown interval for all active timers
        intervalId = setInterval(updateTimers, 1000);

        

        $(document).on("click", ".complete_bk", function() {
            var bookingUrl = $(this).data("url");
            if (bookingUrl) {
                window.location.href = bookingUrl; 
            }
        });
    
        // Event Listener for "Close Booking"
        $(document).on("click", ".close_bk", function() {
            var close_bk_id = $(this).data("bkid");
            closeBooking(close_bk_id)
           
        });
        $(document).on("click", ".close_body_timer", function() {
            jQuery(".close-body-timer").remove();
        });

        
    }

    
});