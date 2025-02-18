
<div mbsc-page class="wpm-recurring-event-add-edit-dialog admin-calender">
    <div style="height:100%">
        <?php if(!empty($resources["listings"])){ ?>
           <div id="loader" class="loader-box-services" style="display:none"></div>
        <?php } ?>
        <div id="toast-container" class="bottom-right">
            <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-notification.php'; ?>
        </div>
        <div class="filter_overlay"></div>
        <div class="main_sch_div">
            <div id="scheduler"></div>
            <?php if(empty($resources["listings"])){
                echo "<div class='empty_calender'><div class='inner-content'>Du har ingen annonser enda. <a href='".home_url()."/my-listings'>Opprett en her :) </a></</div></div>";
            }
            ?>
        </div>
        
        <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-footer.php'; ?>

        <div style="display: none;">
            <?php
            require_once GIBBS_CALENDAR_PATH . 'modals/tv-view.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/add-edit-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/add-customer.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-recurrence.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-link-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/info-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/toast-tamplate.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-header.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-template.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-settings.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-resource-tooltip.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/google-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/outlook-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/data-content-popup.php';
            ?>
        </div>
    </div>
</div>