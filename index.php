<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/licencee_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // Ensure that the user is not logged in.
  User::log_out();
  $access_token = new Access_Token(-1, Utility::ROLE_NONE);

  // Read licencees to be displayed to the user.
  $licencee_data = new Licencee_Data_Manager($access_token);
  $licencees = $licencee_data->get_licencees();
  if ($licencees === null)
  {
    $licencees = array();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement</title>
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
  </head>
  <body>
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_simple_header('Gibbs abonnement') ?>
    <div class="content">
      <div class="form-element">
        Velkommen til Gibbs abonnement! Denne siden er midlertidig. Du vil vanligvis komme til
        linkene nedenfor via selskapets (eller Gibbs') eksisterende webside.
      </div>
      <div class="form-element">
        <a href="/subscription/html/log_in.php">Logg inn</a><br />
        <br />
        <br />
        <form action="/subscription/html/register.php" method="post">
          <div class="form-element">
            <label for="userGroupCombo">Registrer ny kunde hos selskap:</label>
            <select id="userGroupCombo" name="user_group_id" class="long-text" onchange="enableLicenceSubmitButton();">
<?php
  foreach ($licencees as $licencee)
  {
    echo('<option value="' . strval($licencee->user_group_id) . '">' . $licencee->user_group_name . '</option>');
  }
?>
            </select>
            <label for="adminCheckbox">
              <input type="checkbox" id="adminCheckbox" name="as_admin" value="true" />
              Administrator
            </label>
            <button type="submit" class="wide-button">Opprett bruker</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
