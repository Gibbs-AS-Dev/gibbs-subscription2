<div id="settings-popup-season" class="settings-popup-div">
    <div class="row">
        <div class="col-md-4">
            <div class="form-fields">
                <label>Fra tid </label>
                <select id="display-hours-from">
                    <option value="00:00">00:00</option>
                    <option value="01:00">01:00</option>
                    <option value="02:00">02:00</option>
                    <option value="03:00">03:00</option>
                    <option value="04:00">04:00</option>
                    <option value="05:00">05:00</option>
                    <option value="06:00">06:00</option>
                    <option value="07:00">07:00</option>
                    <option value="08:00">08:00</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="13:00">13:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="18:00">18:00</option>
                    <option value="19:00">19:00</option>
                    <option value="20:00">20:00</option>
                    <option value="21:00">21:00</option>
                    <option value="22:00">22:00</option>
                    <option value="23:00">23:00</option>
                    <option value="24:00">24:00</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-fields">
                <label>Til tid </label>
                <select id="display-hours-to">
                    <option value="00:00">00:00</option>
                    <option value="01:00">01:00</option>
                    <option value="02:00">02:00</option>
                    <option value="03:00">03:00</option>
                    <option value="04:00">04:00</option>
                    <option value="05:00">05:00</option>
                    <option value="06:00">06:00</option>
                    <option value="07:00">07:00</option>
                    <option value="08:00">08:00</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="13:00">13:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="18:00">18:00</option>
                    <option value="19:00">19:00</option>
                    <option value="20:00">20:00</option>
                    <option value="21:00">21:00</option>
                    <option value="22:00">22:00</option>
                    <option value="23:00">23:00</option>
                    <option value="24:00">24:00</option>
                </select>
            </div>
            
        </div>
        <div class="col-md-4">
            <div class="form-fields">
                <label>Skala </label>
                <select id="time-scale-to">
                    <option value="5">5 minutter</option>
                    <option value="10">10 minutter</option>
                    <option value="15">15 minutter</option>
                    <option value="30">30 minutter</option>
                    <option value="60">1 time</option>
                    <option value="120">2 timer</option>
                    <option value="180">3 timer</option>
                    <option value="240">4 timer</option>
                    <option value="360">6 timer</option>
                    <option value="480">8 timer</option>
                    <option value="720">12 timer</option>
                    <option value="1440">24 timer</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row"  style="display:none">
        <div>
            <div class="col-md-6">
                <label>Fra dag </label>
                <span class="md-recurrence-input">
                    <label>
                        <input mbsc-input id="display-days-from-input" data-dropdown="true" data-input-style="outline" />

                    </label>
                </span>
            </div>
            <div class="col-md-6">

                <label>Til dag </label>
                <span class="md-recurrence-input">
                    <label>
                        <input mbsc-input id="display-days-to-input" data-dropdown="true" data-input-style="outline" />

                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="row" style="display: none">
        <div class="col-md-12">
            <div class="form-fields">
                <label>
                    <input type="checkbox" mbsc-switch data-label="Vis hendelser som store" id="show-daily-summary-week" />
                </label>
            </div>
        </div>
    </div>
    <div class="row" style="display: none">
        <div class="col-md-12">
            <div class="form-fields">
                <label>
                    <input type="checkbox" mbsc-switch data-label="Vis uke" id="show-week-numbers" />
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-fields pdf_view">
                <label>Eksporter til PDF </label>
                <button id="print-calendar" class="mbsc-popup-button mbsc-popup-button-anchored mbsc-ltr mbsc-popup-button-primary mbsc-reset mbsc-font mbsc-button mbsc-material mbsc-ltr mbsc-button-flat"><i class="fa fa-download"></i></button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-fields pdf_view" id="tv-view-btn" >
                <label>Kalender visning for TV </label>
                <button  class="mbsc-popup-button mbsc-popup-button-anchored mbsc-ltr mbsc-popup-button-primary mbsc-reset mbsc-font mbsc-button mbsc-material mbsc-ltr mbsc-button-flat"><i class="fa fa-screencast"></i></button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-fields">
                <label>Annen informasjon </label>
                <select id="additional-info">
                  <!--  <option value="event_title">Tittel</option> -->
                   <option value="customer_name">Kunde</option>
                   <option value="age_group">Aldersgruppe</option>
                   <option value="phone_number">Kunde telefon</option>
                   <option value="level">Nivå</option>
                   <option value="type">Type søker</option>
                   <option value="sport">Idrett</option>
                   <option value="members">Antall medlemmer</option>
                   <option value="team_name">Lag navn</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-fields">
                <label>Kalender ikoner</label>
                <select id="admin_icon_show">
                   <option value="date">Vis endret dato </option>
                   <option value="time">Vis endret tid </option>
                   <option value="listing">Vis endret lokasjon </option>
                   <option value="linked">Sammenkoblet </option>
                   <option value="comment">Kommentar</option>
                   <option value="notes">Notat</option>
                   <option value="custom_field">Annen informasjon</option>
                   <!-- option value="not_linked">Avkoblet sammenkoblet</option> -->
                   <!-- <option value="repeated">Repeterende</option> -->
                   <!--   <option value="not_repeated">Avkoblet repeterende</option> -->
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Sesongbooking</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-fields">
                <label>Vis avslåtte </label>
                <select id="show_rejected">
                   <option value="yes">Ja</option>
                   <option value="no">Nei</option>
                </select>
            </div>
        </div>
    </div>
    
   
</div>

<div id="settings-info-tooltip">
    <div id="settings-info-tooltip-content" class="settings-info-tooltip-main"></div>
</div>