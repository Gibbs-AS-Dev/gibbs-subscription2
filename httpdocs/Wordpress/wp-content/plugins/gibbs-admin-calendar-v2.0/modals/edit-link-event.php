<div id="link-edit-mode-popup">
    <div class="row">
        <div class="col-md-12">
            <h2>Sammenkoblet bookinger</h2>
            <p>Denne hendelsen er på flere annonser samtidig. Hvordan vil du endringene skal bli lagret?</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="event-link-edit-mode-current">
                <input mbsc-radio id="event-link-edit-mode-current" class="md-link-edit-mode" value="current" name="event-link-edit-mode" type="radio" style="visibility: hidden;" checked />
                <div class="div_edited">
                    <div class="left_div">
                        <div class="dots_main">
                           <img src="<?php echo GIBBS_CALENDAR_URL;?>/assets/images/single_event_dots.jpg">
                        </div>
                        <b>Kun valgte</b>
                    </div>
                    <div class="text_div">
                        Kun denne hendelsen, ingen andre hendelser som er sammenkoblet skal bli endret.
                    </div>
                </div>
            </label>
        </div>
        <div class="col-md-12">
            <label for="event-link-edit-mode-all">
                <input mbsc-radio id="event-link-edit-mode-all" class="md-link-edit-mode"  value="all" name="event-link-edit-mode" type="radio" style="visibility: hidden;"/>
                <div class="div_edited">
                    <div class="left_div">
                        <div class="dots_main">
                            <img src="<?php echo GIBBS_CALENDAR_URL;?>/assets/images/all_evnet_dots.jpg">
                        </div>
                        <b>Alle hendelser</b>
                    </div>
                    <div class="text_div">
                        Alle hendelser som er sammenkoblet skal bli endret.
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>