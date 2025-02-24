<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/dynamic_styles.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // If a user group ID is passed to the page, store it on the session so that the user will be directed to the
  // dashboard for that user group when he logs in.
  if (Utility::integer_passed('user_group_id'))
  {
    User::set_user_group_id(Utility::read_passed_integer('user_group_id'));
  }

  // If a user group ID is available on the session, read the settings for that user group, in order to get the colour
  // profile right. Otherwise, use a new Settings object with the default colour profile.
  $settings = new Settings();
  $user_group_id = User::get_user_group_id();
  if ($user_group_id >= 0)
  {
    $access_token = User::use_anonymously($user_group_id, false);
    if (!$access_token->is_error())
    {
      $settings = Settings_Manager::read_settings($access_token);
    }
  }

  // Catch wrong password. In this case, Wordpress redirects to the same page, with a custom parameter.
  if (Utility::read_passed_string('login', '') === 'incorrect_password')
  {
    $error_message = $text->get(3, 'Feil passord.');
  }
  else
  {
    // Attempt to log in using the user name and password passed to the page, if any. If successful, the user will be
    // redirected to the dashboard. If not, an error message might need to be displayed.
    $result_code = User::log_in();
    switch ($result_code)
    {
      case Result::MISSING_INPUT_FIELD:
        $error_message = $text->get(4, 'Alle feltene m&aring; fylles inn.');
        break;
      case Result::INVALID_PASSWORD:
        $error_message = $text->get(5, 'Passordet inneholdt ugyldige tegn. Vennligst bruk et gyldig passord.');
        break;
      case Result::WORDPRESS_ERROR:
        $error_message = $text->get(6, 'Innlogging mislyktes. Dette kan skyldes at du har tastet feil passord.');
        break;
      case Result::NO_ACTION_TAKEN:
        // The user did not post any credentials, so the page is being displayed for the first time.
        $error_message = '';
        break;
      default:
        // The login method returned an unknown result. Log the error, but display the login page normally.
        error_log('User::log_in returned an unknown result: ' . strval($result_code));
        $error_message = '';
    }
  }

  // Read selected language from the session.
  $current_language = Utility::get_current_language();
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
    </style>
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/log_in_to_dashboard.js?v=<?= Utility::BUILD_NO ?>"></script>
  </head>
  <body onload="initialise();">
    <div class="content-area">
      <div class="toolbar user-dashboard-toolbar">
        <div class="form-element">
<?php
  // Link to register.php. Not required for now.
  //        < ?= $text->get(7, 'Ikke kunde? <a href="/subscription/html/register.php?user_group_id=$0">Registrer deg her</a>', array(User::get_user_group_id())) ? >
?>
          <div class="language-selector-box">
            <form id="selectLanguageForm" action="/subscription/html/set_language.php" method="post">
              <input type="hidden" name="redirect_to" value="<?= sanitize_text_field($_SERVER['REQUEST_URI']) ?>" />
              <select name="language" onchange="submitLanguageSelection();">
                <option value="<?= Utility::NORWEGIAN ?>" <?= ($current_language === Utility::NORWEGIAN ? 'selected="selected"' : '') ?>>Norsk (bokm&aring;l)</option>
                <option value="<?= Utility::ENGLISH ?>" <?= ($current_language === Utility::ENGLISH ? 'selected="selected"' : '') ?>>English (UK)</option>
              </select>
            </form>
          </div>
        </div>
      </div>
      <div class="tab">
        <form id="loginForm" action="/subscription/html/log_in_to_dashboard.php" method="post">
          <div class="area-box user-info-box login-box">
            <div class="separator-box">
              <h2><?= $text->get(0, 'Logg inn som administrator') ?></h2>
            </div>

            <div class="centered">
              <button type="button" class="wide-button" onclick="window.location.href = '<?= Utility::get_login_url() ?>';"><?= $text->get(9, 'Gå til min side') ?></button>
            </div>

            <label for="userNameEdit"><?= $text->get(1, 'E-post:') ?> <span class="mandatory">*</span></label>
            <input type="email" id="userNameEdit" name="user_name" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />

            <label for="passwordEdit"><?= $text->get(2, 'Passord:') ?> <span class="mandatory">*</span></label>
            <input type="password" id="passwordEdit" name="password" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
          </div>
<?php
  if ($error_message !== '')
  {
?>
          <div class="error-message"><?= $error_message ?></div>
<?php
  }
?>
          <div class="submit-box">
            <button type="button" id="submitButton" class="wide-button" onclick="Utility.displaySpinnerThenSubmit(loginForm);">
              <i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?= $text->get(8, 'Logg inn') ?>
            </button>
          </div>
        </form>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
