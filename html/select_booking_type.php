<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  // See if the user is already logged in as an ordinary user, or else try to let him use the booking process
  // anonymously.
  $access_token = User::verify_is_user_or_anonymous();

  // Read location ID parameter, and compose parameter strings. Note that the two files to which we redirect require
  // parameters with different names, so two different strings must be composed.
  if (Utility::integer_passed('location_id'))
  {
    $location_id = Utility::read_passed_integer('location_id');
    $initial_location_id = '&initial_location_id=' . strval($location_id);
    $location_id = '&location_id=' . strval($location_id);
  }
  else
  {
    $initial_location_id = '';
    $location_id = '';
  }

  // Read booking type. If the settings specify a single type of booking, redirect there. Otherwise, give the user a
  // choice. Note that, if the booking type is BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS, we won't be able to tell what
  // kind of booking type is appropriate until the user has selected a location. Send him to the self service booking,
  // so he can select a location. Note that, if a location_id parameter is passed, the location is already selected;
  // even so, we'll let the self service booking file handle it and redirect if required.
  $settings = Settings_Manager::read_settings($access_token);
  $booking_type = $settings->get_booking_type();

  if (($booking_type === Settings::BOOKING_TYPE_SELF_SERVICE) ||
    ($booking_type === Settings::BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS))
  {
    Utility::redirect_to('/subscription/html/book_subscription.php?user_group_id=' .
      $access_token->get_user_group_id() . $initial_location_id);
  }

  if ($booking_type === Settings::BOOKING_TYPE_REQUEST)
  {
    Utility::redirect_to('/subscription/html/submit_request.php?user_group_id=' .
      $access_token->get_user_group_id() . $location_id);
  }

  // Settings specify that the user should have a choice. Give him the alternatives.

  // Get translated texts.
  $text = new Translation('', 'storage', '');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/user.css?v=<?= Utility::BUILD_NO ?>" />
    <style>
<?= Dynamic_Styles::get_user_styles($settings) ?>

.booking-type-left
{
  width: calc(100% - 160px);
}

.booking-type-right
{
  width: 140px;
}

    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************

function sendRequest()
{
  Utility.displaySpinnerThenGoTo('/subscription/html/submit_request.php?user_group_id=<?= $access_token->get_user_group_id() ?><?= $location_id ?>');
}

// *************************************************************************************************

function bookSubscription()
{
  Utility.displaySpinnerThenGoTo('/subscription/html/book_subscription.php?user_group_id=<?= $access_token->get_user_group_id() ?><?= $initial_location_id ?>');
}

// *************************************************************************************************

    </script>
  </head>
  <body>
    <div class="content-area">
      <div class="toolbar">
        <div class="back-button-box">
          <button type="button" class="back-button" onclick="window.history.back();"><?= $text->get(0, 'Tilbake') ?></button>
        </div>
      </div>
      <div class="tab">
        <h1><?= $text->get(1, 'Bestill lagerbod') ?></h1>
        <div class="button-box">
          <div class="button-box-left booking-type-left">
            <h3><?= $text->get(2, 'Send forespørsel') ?></h3>
            <p>
              <?= $text->get(3, 'Fortell oss hva du trenger, så kontakter vi deg og finner riktig lagerbod sammen.') ?>
            </p>
          </div>
          <div class="button-box-right booking-type-right">
            <button type="button" onclick="sendRequest();"><?= $text->get(6, 'Velg') ?>&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
        <div class="button-box">
          <div class="button-box-left booking-type-left">
            <h3><?= $text->get(4, 'Bestill selv') ?></h3>
            <p>
              <?= $text->get(5, 'Bruk vår selvbetjeningsløsning til å bestille lagerbod med en gang.') ?>
            </p>
          </div>
          <div class="button-box-right booking-type-right">
            <button type="button" onclick="bookSubscription();"><?= $text->get(6, 'Velg') ?>&nbsp;&nbsp;<i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
