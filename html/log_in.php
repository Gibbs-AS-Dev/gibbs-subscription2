<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // Set field names.
  $user_name_field = 'user_name';
  $password_field = 'password';

  // Catch wrong password. In this case, Wordpress redirects to the same page, with a custom parameter.
  if (Utility::read_passed_string('login', '') === 'incorrect_password')
  {
    $error_message = 'Feil passord.';
  }
  else
  {
    // Attempt to log in using the user name and password passed to the page, if any. If successful, the user will be
    // redirected to the dashboard. If not, an error message might need to be displayed.
    $result_code = User::log_in($user_name_field, $password_field);
    switch ($result_code)
    {
      case Result::MISSING_INPUT_FIELD:
        $error_message = 'Alle feltene m&aring; fylles inn.';
        break;
      case Result::INVALID_PASSWORD:
        $error_message = 'Passordet inneholdt ugyldige tegn. Vennligst bruk et gyldig passord.';
        break;
      case Result::WORDPRESS_ERROR:
        $error_message = 'Innlogging mislyktes. Dette kan skyldes at du har tastet feil passord.';
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

  // Get translated texts.
  $text = new Translation('', 'storage', '');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - logg inn</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript" src="/subscription/js/log_in.js"></script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_simple_header($text->get(0, 'Logg inn')) ?>
    <div class="content">
      <form action="/subscription/html/log_in.php" method="post">
        <div class="form-element">
          <label for="userNameEdit" class="standard-label"><?= $text->get(1, 'E-post:') ?></label>
          <input type="text" id="userNameEdit" name="<?= $user_name_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="form-element">
          <label for="passwordEdit" class="standard-label"><?= $text->get(2, 'Passord:') ?></label>
          <input type="password" id="passwordEdit" name="<?= $password_field ?>" class="long-text" onkeyup="enableSubmitButton();" onchange="enableSubmitButton();" />
        </div>
        <div class="button-container fixed-width-container">
          <button type="submit" id="submitButton"><i class="fa-solid fa-check"></i> <?= $text->get(3, 'Logg inn') ?></button>
        </div>
        <?= Utility::enclose_in_error_div($error_message) ?>
      </form>
    </div>
  </body>
</html>
