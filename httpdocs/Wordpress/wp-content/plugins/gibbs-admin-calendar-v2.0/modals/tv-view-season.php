<?php
$active_group_id = get_user_meta( get_current_user_id(), '_gibbs_active_group_id',true );
$sqlll = "select * from ".$wpdb->prefix."users_and_users_groups_licence where users_groups_id = $active_group_id AND licence_is_active = 1";
$group_data = $wpdb->get_results($sqlll);

$app_fieldss = array();
if($active_group_id != ""){

    $app_fieldss = get_app_fields($active_group_id);

}
?>
<div id="tv-view" class="tv_view_main">
        <div class="row first">

            <div class="col-md-12">
                <div class="header-div">
                    <h2>Kalender visning for TV</h2>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-fields">
                    <label>Visning </label>
                    <select id="view" name="view">
                        <?php  if($type_of_form != "1"){ ?>
                                <option value="timeline_day">Tidslinje dag</option>
                        <?php } ?>
                        <option value="timeline_week">Tidslinje uke</option>
                        <?php if($type_of_form != "1"){ ?> 
                                <option value="timeline_month">Tidslinje måned</option>
                                <option value="schedule_day">Dag</option>
                        <?php } ?> 
                        <option value="schedule_week">Uke</option>
                        <?php if($type_of_form != "1"){ ?>     
                                
                                <option value="schedule_month">Måned</option>
                                <option value="schedule_year">År</option>
                                <?php if($type_of_form != "2"){ ?>     
                                        <option value="agenda">Agenda</option>
                                <?php } ?> 
                        <?php } ?> 
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-fields">
                    <label>Vis fra </label>
                    <select id="start_hour" name="start_hour">
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
                    <select id="end_hour" name="end_hour">
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
                        <option value="20:00" selected>20:00</option>
                        <option value="21:00">21:00</option>
                        <option value="22:00">22:00</option>
                        <option value="23:00">23:00</option>
                        <option value="24:00">24:00</option>
                    </select>
                </div>
                
            </div>

        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-fields">
                    <label>Vis dato velger</label>
                    <select id="header-div" name="header">
                       <option value="true">Ja</option>
                       <option value="false" selected>Nei</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-fields">
                    <label>Bredde</label>
                    <select id="width" name="width">
                       <option value="100%">100%</option>
                       <option value="50%">90%</option>
                       <option value="25%">80%</option>
                       <option value="20%">70%</option>
                       <option value="20%">60%</option>
                       <option value="20%">50%</option>
                       <option value="20%">40%</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-fields">
                    <label>Høyde</label>
                    <select id="height" name="height">
                        <option value="100%">100%</option>
                        <option value="50%">90%</option>
                        <option value="25%">80%</option>
                        <option value="20%">70%</option>
                        <option value="20%">60%</option>
                        <option value="20%">50%</option>
                        <option value="20%">40%</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Annonse</label>
                    <select id="tv-listing" name="listings">
                        <option value="">Velg</option>
                        <?php foreach ($listings as $listing) { ?>
                            <option value="<?php echo $listing["id"]; ?>"><?php echo $listing["name"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <!-- <div class="form-fields">
                    <label>Vis "Bestill" knapp</label>
                    <select id="show_book_now" name="show_book_now">
                        <option value="no">Nei</option>
                        <option value="yes">Ja</option>
                    </select>
                </div> -->
            </div>
            <!-- <div class="col-md-6">
                <div class="form-fields">
                    <label>Additional fields</label>
                    <select id="tv-additional-fields" name="tv-additional-fields">
                       <option value="">Velg</option>
                       <option value="event_title">Titte</option>
                       <option value="customer_name">Kunde</option>
                       <option value="age_group">Aldersgruppe</option>
                       <option value="level">Nivå</option>
                       <option value="type">Type søker</option>
                       <option value="sport">Idrett</option>
                       <option value="members">Antall medlemmer</option>
                       <option value="team_name">Lag navn</option>
                    </select>
                </div>
            </div> -->
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Annen informasjon </label>
                    <select id="additional-info-tv" name="additional-info-tv">
                        <option value="">Velg</option>
                        <option value="customer_name">Kunde</option>
                        <option value="phone_number">Kunde telefon</option>
                        <option value="age_group">Aldersgruppe</option>
                        <option value="level">Nivå</option>
                        <option value="type">Type søker</option>
                        <option value="sport">Idrett</option>
                        <option value="members">Antall medlemmer</option>
                        <option value="team_name">Lag navn</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6" <?php if(count($group_data) < 1 || empty($app_fieldss)){?>style="display:none"<?php } ?>>
                <div class="form-fields">
                    <label>Kalender ikoner </label>
                    <select id="admin_icon_show_tv" name="admin_icon_show_tv">
                        <option value="">Velg</option>
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
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Select Calender tyep</label>
                    <select id="cal-type-tv" name="cal-type-tv">
                        <option value="forespurte">Forespurte bookinger</option>
                        <option value="algoritme">Algoritme forslag</option>
                        <option value="manuelle">Manuelle endringer</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Select season</label>
                    <select id="season-type-tv" name="season-type-tv">
                        <?php foreach ($seasons_data as $seasons_d) { ?>
                            <option value="<?php echo $seasons_d->id; ?>"><?php echo $seasons_d->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12 tv-create-btn-div">
                <button  class="btn btn-primary tv-create-btn">Opprett visning</button>
            </div>
        </div>
</div>        
