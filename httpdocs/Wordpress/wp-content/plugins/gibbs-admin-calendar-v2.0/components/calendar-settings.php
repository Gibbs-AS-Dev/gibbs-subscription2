<?php
$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );

$sqlll = "select * from ".$wpdb->prefix."users_and_users_groups_licence where users_groups_id = $active_group_id AND licence_is_active = 1";
$group_data = $wpdb->get_results($sqlll);


$app_fieldss = array();
if($active_group_id != ""){

    $app_fieldss = get_app_fields($active_group_id);

}
?>
<div id="settings-popup" class="settings-popup-div">
    <div class="row">
        <div class="col-md-4">
            <div class="form-fields">
                <label>Vis fra </label>
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
                <label>Vis til </label>
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
                <label>Skala <span class="settings-info" data-content="Bestem time avstand mellom hver celle. Fungerer kun i visninger som viser timer"> <i class="fa fa-question-circle"></i></span></label>
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
    <div class="row" style="display:none">
        <div>
            <div class="col-md-6">
                <label>Start dag <span class="settings-info" data-content="Velg hvilken ukedag kalender skal starte fra. Ønsker du å vise du arbeidsdager, velg mandag og så fredag. Fungerer kun uke og måned visning"> <i class="fa fa-question-circle"></i></span></label>
                <span class="md-recurrence-input">
                    <label>
                        <input mbsc-input id="display-days-from-input" data-dropdown="true" data-input-style="outline" />

                    </label>
                </span>
            </div>
            <div class="col-md-6">

                <label>Slutt dag <span class="settings-info" > </span></label>
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
            <div class="form-fields show-daily-summary-week-div">
                <label>
                    <span class="settings-info" data-content="Dette er noe som fungerer kun i tidslinje visninger"> <i class="fa fa-question-circle"></i></span>
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
        <div class="col-md-6">
            <div class="form-fields">
                <label>Annen informasjon </label>
                <select id="additional-info">
                   <option value="">Velg</option>
                   <option value="event_title">Tittel</option>
                   <option value="customer_name">Kunde</option>
                   <option value="phone_number">Kunde telefon</option>
                   <option value="age_group">Aldersgruppe</option>
                   <option value="level">Nivå</option>
                   <option value="type">Type søker</option>
                   <option value="sport">Idrett</option>
                   <option value="members">Antall medlemmer</option>
                   <option value="team_name">Lag navn</option>
                   <option value="amount_guest">Vis antall</option>
                   <option value="amount_guest">Vis antall</option>
                </select>
            </div>
        </div>
        <div class="col-md-6" <?php if(count($group_data) < 1 || empty($app_fieldss)){?>style="display:none"<?php } ?>>
            <div class="form-fields">
                <label>Vis egne felt  </label>
                <select id="fields-info">
                   <option value="">Velg</option>
                   <?php foreach($app_fieldss as $field){ ?>
                   <option value="<?php echo $field["name"];?>"><?php echo $field["label"];?></option>
                   <?php } ?>
                </select>
            </div>
        </div>
        
    </div>
    <div class="row">
    <div class="col-md-6">
            <div class="form-fields">
                <label>Kalender ikoner</label>
                <select id="admin_icon_show">
                   <!-- <option value="">Velg</option> -->
                   <option value="repeated">Repeterende booking</option>
                   <!-- <option value="not_repeated">Avkoblet repeterende </option> -->
                   <option value="linked">Sammenkoblet </option>
                  <!--  <option value="not_linked">Avkoblet sammenkoblet</option> -->
                   <option value="comment">Kommentar</option>
                   <option value="notes">Notat</option>
                   <option value="custom_field">Annen informasjon</option>
                </select>
            </div>
        </div>

        <div class="row">
        <div class="col-md-12">
            <div class="form-fields">
                <br>
                <label>
                    <input type="checkbox" mbsc-switch data-label="Vis pågående bestillinger" id="show_bk_pay_to_confirm" />
                </label>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="form-fields">
                <label>
                    <input type="checkbox" mbsc-switch data-label="Vis ufullførte eller utløpte bestillinger" id="show_bk_payment_failed" />
                </label>
            </div>
        </div>
    </div> -->
        
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-fields pdf_view" id="tv-view-btn" >
                <label>Kalender visning for TV </label>
                <button  class="mbsc-popup-button mbsc-popup-button-anchored mbsc-ltr mbsc-popup-button-primary mbsc-reset mbsc-font mbsc-button mbsc-material mbsc-ltr mbsc-button-flat"><i class="fa fa-screencast"></i></button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-fields pdf_view" id="print-calendar">
                <label>Eksporter til PDF </label>
                <button  class="mbsc-popup-button mbsc-popup-button-anchored mbsc-ltr mbsc-popup-button-primary mbsc-reset mbsc-font mbsc-button mbsc-material mbsc-ltr mbsc-button-flat"><i class="fa fa-download"></i></button>
            </div>
        </div>
    </div>
   <!--  <div class="mbsc-form-group">

        <div>
            Display days from <span class="settings-info" data-content="Display days from"> <svg class="svg-inline--fa fa-question-circle " aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>
            <span class="md-recurrence-input">
                <label>
                    <input mbsc-input id="display-days-from-input" data-dropdown="true" data-input-style="outline" />

                </label>
            </span>

            to <span class="settings-info" data-content="Display days to"> <svg class="svg-inline--fa fa-question-circle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>
            <span class="md-recurrence-input">
                <label>
                    <input mbsc-input id="display-days-to-input" data-dropdown="true" data-input-style="outline" />

                </label>
            </span>
        </div>

        <label style="display:none">
            <input type="checkbox" mbsc-switch data-label="Show a daily summary of events" id="show-daily-summary-week" />
            <span class="settings-info" data-content="Show a daily summary of events"> <svg class="svg-inline--fa fa-question-circle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>
        </label>
        <label>
            <input type="checkbox" mbsc-switch data-label="Show week numbers" id="show-week-numbers" />
            <span class="settings-info" data-content="Show week numbers"> <svg class="svg-inline--fa fa-question-circle" data-content="Show week numbers" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>
        </label>
        <div id="display-hours-container" data-content="Display hours from 5555">
            Display hours from <span class="settings-info" data-content="Display hours from dfdfdf"><svg class="svg-inline--fa fa-question-circle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>

            <span class="md-recurrence-input">
                <label>
                    <input mbsc-input id="display-hours-from-input" data-dropdown="true" data-input-style="outline" />
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

                </label>
            </span>
            to <span class="settings-info" data-content="Display hours to"><svg class="svg-inline--fa fa-question-circle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span></span>
            <span class="md-recurrence-input">
                <label>
                    <input mbsc-input id="display-hours-to-input" data-dropdown="true" data-input-style="outline" />
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

                </label>
            </span>

        </div>
        <div id="time-scale-to-container">
            Set the time scale to <span class=" settings-info" data-content="Set the time scale to"><svg class="svg-inline--fa fa-question-circle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path>
                </svg></span>

            <span class="md-recurrence-input">
                <label>
                    <input mbsc-input id="time-scale-to-input" data-dropdown="true" data-input-style="outline" />
                    <select id="time-scale-to">
                        <option value="5">5 minutes</option>
                        <option value="10">10 minutes</option>
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                        <option value="180">3 hours</option>
                        <option value="240">4 hours</option>
                        <option value="360">6 hours</option>
                        <option value="480">8 hours</option>
                        <option value="720">12 hours</option>
                        <option value="1440">24 hours</option>
                    </select>
                </label>
            </span>
        </div>

       
    </div>

    <div class="mbsc-form-group">
        <button id="print-calendar" class="mbsc-popup-button mbsc-popup-button-anchored mbsc-ltr mbsc-popup-button-primary mbsc-reset mbsc-font mbsc-button mbsc-material mbsc-ltr mbsc-button-flat">Export to PDF</button>
    </div> -->
</div>

<div id="settings-info-tooltip" >
    <div id="settings-info-tooltip-content" class="settings-info-tooltip-main"></div>
</div>