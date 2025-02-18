<?php
$active_group_id = get_user_meta( get_current_user_ID(), '_gibbs_active_group_id',true );
?>
<form id="eventForm" method="post" action="javascript:void(0)">
    <div class="row">
        <div class="k-tooltip-content">
            
        </div>
       <!--  <div class="event_popup_btn_main">
            <div class="col-md-6 event_popup_left_btn">
                <button type="button" class="btn btn-primary" id="popup-event-delete">Slett</button>
            </div>
            <div class="col-md-6 event_popup_right_btn">
                <button type="button" class="btn btn-primary close_btn close_event_popup" id="popup-event-close">Lukk</button>
                <button class="btn btn-primary submit_btn" type="submit">Lagre</button>
            </div>
        </div> -->
    </div>

    <div class="top-info-div">
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields ">
                    <input  class="form-control event-title" id="event-title" name="event-title" placeholder="Tittel" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="topAction2 event_popup_btn_main1">
                    <div class="event_popup_left_btn" >
                        <button type="button" class="btn btn-primary" id="popup-event-delete"><i class="fa fa-trash"></i></button>
                    </div>
                    <button class="btn btn-primary submit_btn" type="submit"><i class="fa fa-floppy-disk view-more"></i></button>
                    <button type="button" class="btn btn-primary close_btn close_event_popup" id="popup-event-close"><i class="fa fa-close close_event_popup"></i></button>
                    
                    
                </div>
            </div>
        </div>
    </div>

    <div class="tabs-event" style="display: none;">
        <div class="tab-event active" data-tab="eventForm">Booking</div>
        <div class="tab-event" data-tab="user-info">Kunde detaljer</div>
        <div class="tab-event" data-tab="sms-email-info">Sms/Email Log</div>
    </div>
    <div id="user-info" class="user-info-event" style="display: none;">
        
    </div>
    <div id="sms-email-info" class="sms-email-event" style="display: none;">
        
    </div>

    <div class="middle-info-div event-info_main">
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields dropdown_chevron">
                    <label class="event_none">Kunde <?php if($active_group_id != ""){ ?>
                        <?php if($is_group_active){ ?>
                           <span class="openCustomer"><i class="fa fa-plus"></i><?php } ?></span>
                        <?php }else{ ?>
                            <span class="group_infoo tooltip_info" data-content="For å opprette en ny kunde manuelt, trenger du mer tilgang. Spør om mer tilgang på kontakt@gibbs.no"><i class="fa fa-plus-circle" ></i></span>
                        <?php } ?>   
                    </label>
                    <select id="wpm-client" class="required client_dropdown" name="event-customer">
                        <option value="">Select</option>
                        <?php foreach ($wpm_user_list as $user) { ?>
                            <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields dropdown_chevron tooltip_div">
                    <label class="event_none">Annonse <span class="settings-info" data-content="Kan ikke endre på annonse etter at hendelsen har blitt opprettet"> <i class="fa fa-question-circle"></i></span></label>
                    <select id="wpm-listing" class="required listing_dropdown"  name="event-listing">
                        <option value="">Select</option>
                        <?php foreach ($listings as $listing) { ?>
                            <option value="<?php echo $listing["id"]; ?>"><?php echo $listing["name"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-fields dropdown_chevron from_dropdown">
                    <label>Fra</label>
                    <input class="form-control required" id="add-event-start" name="event-start" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields dropdown_chevron to_dropdown">
                    <label>Til</label>
                    <input class="form-control required" id="add-event-end" name="event-end" />
                    <div id="add-event-dates"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 smaller_switch">
                <div class="form-fields">
                    <label class="repeter_switch_label">
                        <span class="labell bigger_font">Repeter</span>
                        <input type="checkbox" mbsc-switch id="repeter_switch" />
                    </label>
                    <div class="dropdown_chevron rec_divv">
                       <input data-dropdown="true" class="popup-event-recurrence" />
                    </div>
                </div>

                <div class="popup-event-recurrence-editor">
                    <div mbsc-segmented-group>
                        <label>
                            Daglig
                            <input mbsc-segmented class="md-recurrence-repeat recurrence-repeat-daily" name="recurrence-repeat" type="radio" value="daily" checked />
                        </label>
                        <label>
                            Ukentlig
                            <input mbsc-segmented class="md-recurrence-repeat recurrence-repeat-weekly" name="recurrence-repeat" type="radio" value="weekly" />
                        </label>
                        <label>
                            Månedlig
                            <input mbsc-segmented class="md-recurrence-repeat recurrence-repeat-monthly " name="recurrence-repeat" type="radio" value="monthly" />
                        </label>
                        <label>
                            Årlig
                            <input mbsc-segmented class="md-recurrence-repeat recurrence-repeat-yearly" name="recurrence-repeat" type="radio" value="yearly" />
                        </label>
                    </div>

                    <div class="md-recurrence-options">
                        
                        <div class="repeat_days_div">
                            <span class="rp-every"><span class="rp-inner">Repeter hver</span></span>
                            <span class="md-recurrence-input md-recurrence-input-nr">
                                <label>
                                    <input class="recurrence-interval" mbsc-input data-input-style="outline" type="number" min="1" />
                                </label>
                            </span>

                            <span class="md-recurrence-text md-recurrence-daily"><span class="md-recurrence-inner-text">dag(er)</span></span>
                            <span class="md-recurrence-text md-recurrence-weekly"><span class="md-recurrence-inner-text">uke(er)</span></span>
                            <span class="md-recurrence-text md-recurrence-monthly">
                                <span class="md-recurrence-inner-text">måned(er) på dag</span>
                                <span class="md-recurrence-input md-recurrence-input-nr">
                                    <label>
                                        <input class="recurrence-day" mbsc-input data-dropdown="true" data-input-style="outline" />
                                    </label>
                                </span>
                            </span>
                            <span class="md-recurrence-text md-recurrence-yearly">
                                <span class="md-recurrence-inner-text">år</span>
                            </span>
                           
                        </div>
                        <div class="d-flex yearly_div">
                            <span class="md-recurrence-text md-recurrence-yearly">
                                <span class="md-recurrence-inner-text">på den</span>
                                <span class="md-recurrence-input md-recurrence-input-nr">
                                    <label>
                                        <input class="recurrence-month-day" mbsc-input data-dropdown="true" data-input-style="outline" />
                                    </label>
                                </span>
                                <span class="md-recurrence-inner-text">av</span>
                                <span class="md-recurrence-input">
                                    <label>
                                        <input class="recurrence-month" mbsc-input data-dropdown="true" data-input-style="outline" />
                                    </label>
                                </span>
                            </span>
                        </div>


                        <div>
                            <p class="md-recurrence-desc md-recurrence-text md-recurrence-daily">Hendelsen vil bli repetert hver dag eller evert x dager</p>
                            <p class="md-recurrence-desc md-recurrence-text md-recurrence-weekly">Hendelsen vil bli repertert hver x uke på valgte ukedager</p>
                            <p class="md-recurrence-desc md-recurrence-text md-recurrence-monthly">Hendelsen vil bli repetert hver x måned på valgt dag i måneden</p>
                            <p class="md-recurrence-desc md-recurrence-text md-recurrence-yearly">Hendelsen vil bli repetert hver x år på valgt dag og valgt måned</p>
                        </div>
                    </div>

                    <div class="md-recurrence-text md-recurrence-weekly" mbsc-segmented-group>
                       
                        <label>
                            Man
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="MO" />
                        </label>
                        <label>
                            Tir
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="TU" />
                        </label>
                        <label>
                            Ons
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="WE" />
                        </label>
                        <label>
                            Tor
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="TH" />
                        </label>
                        <label>
                            Fre
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="FR" />
                        </label>
                        <label>
                            Lør
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="SA" />
                        </label>
                        <label>
                            Søn
                            <input mbsc-segmented class="md-recurrence-weekdays" type="checkbox" value="SU" />
                        </label>
                    </div>

                    <div class="md-recurrence-ends">Slutt repeteringen </div>

                    <div class="mbsc-form-group">
                        <label>
                            <input mbsc-radio  id="recurrence-condition-never" class="md-recurrence-condition recurrence-condition-never" type="radio" name="recurrence-condition" value="never" data-label="Aldri" data-position="start" data-description="Hendelsen vil repeteres for alltid" checked />
                        </label>
                        <label class="until rec-divs">
                            <input mbsc-radio id="recurrence-condition-until" class="md-recurrence-condition recurrence-condition-until" type="radio" name="recurrence-condition" value="until"  data-position="start" data-description="Hendelsen vil repeteres til den valgte datoen" />
                            <span class="rec-divs-inner">
                                <span>På dato</span>
                                <span class="md-recurrence-input">
                                    <label>
                                        <input class="recurrence-until" mbsc-input data-input-style="outline" placeholder="Velg" />
                                    </label>
                                </span>
                            </span>
                        </label>
                        <label class="condition-count rec-divs">
                            <input mbsc-radio id="recurrence-condition-count" class="md-recurrence-condition recurrence-condition-count" type="radio" name="recurrence-condition" value="count" data-position="start" data-description="Hendelsen vil repeteres til den blir gjentatt valgt antall ganger" />
                            <span class="rec-divs-inner">
                                <span>Etter</span>

                                <span class="md-recurrence-input md-recurrence-input-nr">
                                    <label>
                                        <input class="recurrence-count" mbsc-input data-input-style="outline" type="number" min="1" />
                                    </label> 
                                </span> <span>gjentakelser </span> 
                            </span>    
                           
                        </label>
                    </div>
                </div>
            </div>
        </div>

       

        <div class="row price_divv">
            <div class="col-md-6">
                <div class="form-fields price_dropdown">
                    <label class="event_none">Pris <span class="price_plus"><i class="fa fa-plus"></i></span></label>
                    <input type="number" name="bk_price" id="bk_price" min="0">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields paylink_main tooltip_div">
                    <label class="event_none">Betalingslenke <span class="settings-info" data-content="Du kan sende en betalingslenke bare når statusen er godkjent, slik at kundene kan betale gjennom lenken"> <i class="fa fa-question-circle"></i></span></label>
                    <span class="payment_plus"><i class="fa fa-plus"></i></span>
                    <div class="paylink_main_inner">
                        <span class="link_gen_span">Blir opprettet etter lagring</span>
                        <span class="payment_url"></span>
                    </div>
                    
                </div>
                <div class="form-fields refund_main tooltip_div">
                        <label class="event_none">Refunder <span class="settings-info" data-content="For å opprette en refusjon, trykk på 'Refunder beløp' og deretter på 'Lagre'. Husk å endre statusen hvis det er nødvendig "> <i class="fa fa-question-circle"></i></span></label>
                        <span class="refund_plus"><i class="fa fa-plus"></i> Refunder beløp</span>
                        <div class="refund_main_inner" style="display:none">
                           <input type="number" name="refund_price" id="refund_price" min="0">
                        </div>
                        <div class="refund_main_inner_refunded" style="display:none">
                           <input type="number" id="refund_price_used" min="0" readonly>
                        </div>
                        <div class="all_refund_data"></div>
                
                </div>
            </div>
            <!-- <div class="col-md-6">
                <div class="form-fields paylink_main tooltip_div">
                    <label class="event_none">Refund <span class="settings-info" data-content="Refund tooltip"> <i class="fa fa-question-circle"></i></span></label>
                    <span class="payment_plus">Create a refund<i class="fa fa-plus"></i></span>
                    <div class="paylink_main_inner">
                        <span class="link_gen_span">Refund is coming</span>
                        <span class="payment_url"></span>
                    </div>
                    
                </div>
            </div> -->
        </div>
        <div class="row mobile_flex_row">
            <div class="col-md-9 small_mobile">
                <div class="form-fields dropdown_chevron">
                    <label>Status</label>
                    <select  name="status" id="wpm-status" class="wpm-status required">
                        <option>Select status</option>
                        <option value="waiting">Reservasjon</option>
                        <option value="paid">Betalt </option>
                        <option value="sesongbooking">Sesongbooking </option>
                        <option value="manual_invoice">Faktura </option>
                        <option value="closed">Stengt</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 small_mobile_status">
                <div class="form-fields tooltip_div">
                    <label class="send_mail_switch_label event_none">
                        <span class="labell" >Send epost <span class="settings-info" data-content="Huk av om du ønsker å sende epost til valgt kunde"> <i class="fa fa-question-circle"></i></span></span>
                        <input type="checkbox" id="sendmail" />
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-fields mobile_comment tooltip_div">
                    <label class="event_none">Kommentar <span class="settings-info" data-content="Når en hendelse har en kommentar fra kunde, vil det vises her"> <i class="fa fa-question-circle"></i></span></label>
                    <textarea class="popup-event-comment" readonly></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-fields mobile_note tooltip_div">
                    <label class="event_none">Notat <span class="settings-info" data-content="Dine notater til hendelsen. Kun utleier som ser disse notatene"> <i class="fa fa-question-circle"></i></span></label>
                    <textarea  class="popup-event-description"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-fields adult_div tooltip_div">
                    <label class="event_none">Antall <span class="settings-info" data-content="Antallet som er booket"> <i class="fa fa-question-circle"></i></span></label>
                    <input type="number" class="popup-event-guest">
                </div>
            </div>
            <div class="col-md-6 access_code_div" style="display:none">
                <div class="form-fields  tooltip_div">
                    <label class="event_none">Adgangskode <span class="settings-info" data-content="Koden for å åpne"> <i class="fa fa-question-circle"></i></span></label>
                    <input type="text" class="access_code" readonly>
                </div>
            </div>
        </div>
        
    </div>



        <div class="calendar-edit-sections"></div>
       <!--  <div class="mbsc-button-group">
            <button class="mbsc-button-block" id="popup-event-delete" mbsc-button data-color="danger" data-variant="outline">Delete event</button>
        </div> -->
        
    </form>
<div class="row">
    <div class="col-md-12">
        <div class="cal_custom_fields">
        </div>
    </div>
</div>    
