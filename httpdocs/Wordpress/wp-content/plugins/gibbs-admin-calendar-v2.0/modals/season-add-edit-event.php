<?php

$datess = array( "2021-02-01" => "Mandag", 
                 "2021-02-02" => "Tirsdag", 
                 "2021-02-03" => "Onsdag", 
                 "2021-02-04" => "Torsdag", 
                 "2021-02-05" => "Fredag",
                 "2021-02-06" => "Lørdag",
                 "2021-02-07" => "Søndag",
            );

?>
<div id="season-calendar-add-edit-popup" class="event_popup_main">

    <form id="eventForm" method="post" action="javascript:void(0)">

    <div class="top-info-div">
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields" style="display:none">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields close_popup_field">
                    <span class="close_event_popup"><i class="fa fa-times"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="middle-info-div">
        <div class="row">
            <div class="col-md-6" style="display: none;">
                <div class="form-fields">
                    <label>Annonse</label>
                    <select id="wpm-listing" class="required"  name="event-listing">
                        <option value="">Select</option>
                        <?php foreach ($listings as $listing) { ?>
                            <option value="<?php echo $listing["id"]; ?>"><?php echo $listing["name"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <?php if($type_of_form == "1"){ ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-fields">
                        <label>Dag</label>
                        <select id="week_day_start" class="required"  name="week_day_start">
                            <?php foreach ($datess as $key_d => $da) { ?>
                                <option value="<?php echo $key_d;?>"><?php echo $da;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-fields">
                        <label>Fra</label>
                        <input class="form-control required" id="add-event-start" name="event-start" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-fields">
                        <label>Til</label>
                        <input class="form-control required" id="add-event-end" name="event-end" />
                        <div id="add-event-dates"></div>
                    </div>
                </div>
            </div>    

        <?php }else{ ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-fields">
                        <label>Fra</label>
                        <input class="form-control required" id="add-event-start" name="event-start" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-fields">
                        <label>Til</label>
                        <input class="form-control required" id="add-event-end" name="event-end" />
                        <div id="add-event-dates"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        


        <div class="row">
            <div class="col-md-12">
                <div class="form-fields">
                    <label>Status</label>
                    <select  name="status_manuale" id="status_manuale" class="status_manuale required">
                       <!--  <option value="">Status</option> -->
                        <option value="1">Avslått</option>
                        <option value="0">Godkjent</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Kommentar</label>
                    <textarea class="popup-event-comment" readonly></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields">
                    <label>Notat</label>
                    <textarea  class="popup-event-description"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="cal_custom_fields">
                </div>
            </div>
        </div>
    </div>



        <div class="calendar-edit-sections"></div>
       <!--  <div class="mbsc-button-group">
            <button class="mbsc-button-block" id="popup-event-delete" mbsc-button data-color="danger" data-variant="outline">Delete event</button>
        </div> -->
        <div class="row">
            <div class="event_popup_btn_main">
                <div class="col-md-6 event_popup_right_btn">
                    <button type="button" class="btn btn-primary close_btn close_event_popup" id="popup-event-close">Lukk</button>
                    <button class="btn btn-primary submit_btn" type="submit">Lagre</button>
                </div>
            </div>
        </div>
    </form>
</div>