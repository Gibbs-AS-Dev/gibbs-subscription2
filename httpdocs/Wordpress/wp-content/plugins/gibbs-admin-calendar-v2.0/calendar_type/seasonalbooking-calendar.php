<div mbsc-page class="wpm-recurring-event-add-edit-dialog season-calender">
    <div style="height:100%">
        <div id="loader" class="loader-box-services" style="display:none"></div>
        <div id="toast-container" class="bottom-right">
            <?php require_once GIBBS_CALENDAR_PATH . 'components/calendar-notification.php'; ?>
        </div>
        <div class="filter_overlay"></div>
        <div id="scheduler"></div>
        <?php require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-footer.php'; ?>

        <div style="display: none;">
            <?php
            require_once GIBBS_CALENDAR_PATH . 'modals/tv-view-season.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/season-add-edit-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/add-customer.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-event.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/edit-recurrence.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/season-calendar-event-tooltip-popup.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/toast-tamplate.php';
            require_once GIBBS_CALENDAR_PATH . 'modals/algo-popup.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-header.php';
            require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-template-season.php';
            require_once GIBBS_CALENDAR_PATH . 'components/season/calendar-settings-season.php';
            require_once GIBBS_CALENDAR_PATH . 'components/calendar-resource-tooltip.php';
            ?>
        </div>
    </div>
</div>