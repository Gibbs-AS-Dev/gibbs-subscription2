<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/email_sms_template_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read data.
  $settings = Settings_Manager::read_settings($access_token);
  if ($settings->get_use_test_data())
  {
    $result_code = Result::NO_ACTION_TAKEN;
    $template_data = Test_Data_Manager::TEMPLATES;
  }
  else
  {
    $template_data = new Email_Sms_Template_Data_Manager($access_token);
    // Handle create, update and delete operations.
    $result_code = $template_data->perform_action();
    // Read templates to be displayed to the user.
    $templates = $template_data->read();
  }

/*
var TRIGGER_HEADLINES = <?= $text->get(2, "['Ny bruker', 'Glemt passord', 'Eksisterende kunde bestilte abonnement', 'Ny kunde bestilte abonnement', 'Ugyldig abonnement', 'Abonnement utløper', 'M&aring;nedlig betaling vellykket', 'M&aring;nedlig betaling mislyktes', 'Betalingsp&aring;minnelse', 'Inkassovarsel', 'Abonnement sagt opp', 'Brukervilk&aring;r endret', 'Pris endret', 'Nyhetsbrev', 'Vedlikehold', 'Endret adgangskode', 'Slettet konto']") ?>;
var TRIGGER_DESCRIPTIONS = <?= $text->get(3, "['N&aring;r en bruker registrerer seg, uten &aring; bestille et abonnement.', 'N&aring;r en bruker klikker p&aring; &quot;Glemt passord&quot;-knappen.', 'N&aring;r en eksisterende kunde bestiller et abonnement. Hvis han betalte med kort, var betalingen vellykket.', 'N&aring;r en ny kunde registrerer seg og bestiller et abonnement. Hvis han betalte med kort, var betalingen vellykket.', 'N&aring;r et Nets-abonnement ikke lenger er gyldig, for eksempel fordi kredittkortet er g&aring;tt ut p&aring; dato. Dette vil oppdages omtrent to uker f&oslash;r forfall.', 'N&aring;r et Nets-abonnement er utl&oslash;pt, eller snart utl&oslash;per. Dette vil oppdages omtrent to uker f&oslash;r forfall.', 'N&aring;r en m&aring;nedlig betaling via Nets var vellykket. Det er ikke sikkert du trenger &aring; fortelle kunden om dette, men du kan gj&oslash;re det.', 'N&aring;r en m&aring;nedlig betaling via Nets mislyktes, for eksempel fordi kredittgrensen var overtrukket.', 'N&aring;r en faktura er forfalt, og du vil sende en betalingsp&aring;minnelse.', 'N&aring;r en faktura er forfalt, og du vil sende et inkassovarsel.', 'N&aring;r en kunde avbestiller et abonnement.', 'N&aring;r brukervilk&aring;rene endres.', 'N&aring;r prisen for et abonnement endres.', 'N&aring;r du vil sende et nyhetsbrev eller kampanjeinformasjon.', 'N&aring;r du vil gj&oslash;re oppmerksom p&aring; vedlikehold eller stengning p&aring; et lager.', 'N&aring;r kundens adgangskode endres.', 'N&aring;r en kunde sletter kontoen sin.']") ?>;

      "['Ny bruker', 'Glemt passord', 'Eksisterende kunde bestilte abonnement', 'Ny kunde bestilte abonnement', 'Ugyldig abonnement', 'Abonnement utløper', 'M&aring;nedlig betaling vellykket', 'M&aring;nedlig betaling mislyktes', 'Betalingsp&aring;minnelse', 'Inkassovarsel', 'Abonnement sagt opp', 'Brukervilk&aring;r endret', 'Pris endret', 'Nyhetsbrev', 'Vedlikehold', 'Endret adgangskode', 'Slettet konto']",
      "['N&aring;r en bruker registrerer seg, uten &aring; bestille et abonnement.', 'N&aring;r en bruker klikker p&aring; &quot;Glemt passord&quot;-knappen.', 'N&aring;r en eksisterende kunde bestiller et abonnement. Hvis han betalte med kort, var betalingen vellykket.', 'N&aring;r en ny kunde registrerer seg og bestiller et abonnement. Hvis han betalte med kort, var betalingen vellykket.', 'N&aring;r et Nets-abonnement ikke lenger er gyldig, for eksempel fordi kredittkortet er g&aring;tt ut p&aring; dato. Dette vil oppdages omtrent to uker f&oslash;r forfall.', 'N&aring;r et Nets-abonnement er utl&oslash;pt, eller snart utl&oslash;per. Dette vil oppdages omtrent to uker f&oslash;r forfall.', 'N&aring;r en m&aring;nedlig betaling via Nets var vellykket. Det er ikke sikkert du trenger &aring; fortelle kunden om dette, men du kan gj&oslash;re det.', 'N&aring;r en m&aring;nedlig betaling via Nets mislyktes, for eksempel fordi kredittgrensen var overtrukket.', 'N&aring;r en faktura er forfalt, og du vil sende en betalingsp&aring;minnelse.', 'N&aring;r en faktura er forfalt, og du vil sende et inkassovarsel.', 'N&aring;r en kunde avbestiller et abonnement.', 'N&aring;r brukervilk&aring;rene endres.', 'N&aring;r prisen for et abonnement endres.', 'N&aring;r du vil sende et nyhetsbrev eller kampanjeinformasjon.', 'N&aring;r du vil gj&oslash;re oppmerksom p&aring; vedlikehold eller stengning p&aring; et lager.', 'N&aring;r kundens adgangskode endres.', 'N&aring;r en kunde sletter kontoen sin.']",

      "['Registered', 'Forgot password', 'Existing user bought subscription', 'New user bought subscription', 'Subscription invalid', 'Before expiry', 'Monthly payment success', 'Monthly payment failure', 'Invoice first reminder', 'Invoice second reminder', 'Subscription cancelled', 'Terms changed', 'Price changed', 'Newsletter', 'Maintenance', 'Access code modified', 'Account deleted']",
      "['When a new user has registered, but not bought a subscription.', 'When a user clicks the &quot;forgot password&quot; button.', 'When an existing user buys a subscription. If he paid with a credit card, the payment succeeded.', 'When a new user registers and buys a subscription. If he paid with a credit card, the payment succeeded.', 'When a Nets subscription validation failed, for instance because the buyer's credit card has expired. This will trigger around two weeks before the next payment is due.', 'When a Nets subscription has expired, or is about to expire. This will trigger around two weeks before the next payment is due.', 'When a Nets subscription was charged successfully. You may not need to tell the customer about this, but you can.', 'When a Nets subscription was not charged successfully. The payment failed for some reason, for instance if the customer had exceeded his credit limit.', 'When an invoice is overdue, and you need to let him know about it.', 'When an invoice is overdue, and you need to let him know the claim may be referred to a collection agency.', 'When a customer cancels a subscription.', 'When the terms and conditions change.', 'When the price of a subscription changes.', 'When you want to send a newsletter or special offer.', 'When you want to send a notification about maintenance or closures at a particular location.', 'When the customer's access code is modified.', 'When a customer deletes his account.']",
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/common.css?v=<?= Utility::BUILD_NO ?>" />
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/sorting/sorting.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/components/menu/popup_menu.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_email_sms_templates.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var MESSAGE_TYPE_TEXTS = <?= $text->get(4, "['SMS', 'E-post']") ?>;
var TRIGGER_HEADLINES = <?= $text->get(2, "['Nytt abonnement opprettet', 'Ny ordre opprettet', 'Betaling feilet', 'Ikke i bruk', 'Abonnement har blitt inaktivt', 'Abonnement har f&aring;tt sluttdato']") ?>;
var TRIGGER_DESCRIPTIONS = <?= $text->get(3, "['Beskrivelse mangler', 'Beskrivelse mangler', 'Beskrivelse mangler', 'Beskrivelse mangler', 'Beskrivelse mangler', 'Beskrivelse mangler']") ?>;

// The current message type filter, or null if all message types are displayed. The filter is an
// array of integers, containing the message types that should be displayed. Use the MESSAGE_TYPE_
// constants.
var messageTypeFilter = <?= Utility::verify_filter('message_type_filter') ?>;

// The current trigger type filter, or null if all templates are displayed, regardless of trigger.
// The filter is an array of integers, containing the trigger types that should be displayed. Use
// the TRIGGER_TYPE_ constants.
var triggerTypeFilter = <?= Utility::verify_filter('trigger_type_filter') ?>;

// The current freetext filter, or an empty string if all templates are displayed, regardless of
// what they contain. If a text is supplied, templates will only be displayed if they contain that
// text, as part of either the name, header or content fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

<?= Utility::write_initial_sorting() ?>

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var templates = <?= $templates ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'SMS- og e-postmaler'), 'fa-envelopes-bulk') ?>
    <div class="content">
      <div class="toolbar">
        <button type="button" class="wide-button" onclick="displayEditTemplateDialogue(-1);"><i class="fa-solid fa-envelopes-bulk"></i> <?= $text->get(1, 'Opprett mal') ?></button>
        <div id="filterToolbar" class="filter filter-next-to-buttons">
          &nbsp;
        </div>
      </div>
      <div id="templateBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editTemplateDialogue" class="dialogue edit-template-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editMessageTypeFilterDialogue" class="dialogue edit-message-type-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editTriggerTypeFilterDialogue" class="dialogue edit-trigger-type-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
