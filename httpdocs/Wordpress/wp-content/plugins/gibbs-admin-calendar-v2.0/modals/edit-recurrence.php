<div id="recurrence-edit-mode-popup">
    <div class="row">
        <div class="col-md-12">
            <h2>Repeterende hendelse</h2>
            <p>Dette er en repeterende hendelse. Hvordan ønsker du endringene skal bli lagret? </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="recurrence-edit-mode-current">
                <input mbsc-radio id="recurrence-edit-mode-current" class="md-recurrence-edit-mode" value="current" name="recurrence-edit-mode" type="radio" style="visibility: hidden;" checked />
                <div class="div_edited">
                    <div class="left_div">
                        <div class="dots_main">
                           <img src="<?php echo GIBBS_CALENDAR_URL;?>/assets/images/single_event_dots.jpg">
                        </div>
                        <b>Kun valgte</b>
                    </div>
                    <div class="text_div">
                        Kun denne hendelsen. Ingen andre hendelser i serien blir endret.
                    </div>
                </div>
            </label>
        </div>
        <div class="col-md-12">
            <label for="recurrence-edit-mode-following">
                <input mbsc-radio id="recurrence-edit-mode-following" class="md-recurrence-edit-mode"  value="following" name="recurrence-edit-mode" type="radio" style="visibility: hidden;" />
                <div class="div_edited">
                    <div class="left_div">
                        <div class="dots_main">
                            <img src="<?php echo GIBBS_CALENDAR_URL;?>/assets/images/future_event_dots.jpg">
                        </div>
                        <b>Fremtidige</b>
                    </div>
                    <div class="text_div">
                        Denne og fremdige hendelser. Tidligere hendelser blir ikke berørt.
                    </div>
                </div>
            </label>
        </div>
        <div class="col-md-12">
            <label for="recurrence-edit-mode-all">
                <input mbsc-radio id="recurrence-edit-mode-all" class="md-recurrence-edit-mode"  value="all" name="recurrence-edit-mode" type="radio" style="visibility: hidden;"/>
                <div class="div_edited">
                    <div class="left_div">
                        <div class="dots_main">
                            <img src="<?php echo GIBBS_CALENDAR_URL;?>/assets/images/all_evnet_dots.jpg">
                        </div>
                        <b>Alle hendelser</b>
                    </div>
                    <div class="text_div">
                        Alle hendelser i serien vil bli endret.
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>