<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // If the user is not logged in as a Gibbs administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_gibbs_admin();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement - Gibbs administrator dashboard</title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
    <script type="text/javascript">

function initialise()
{

}

function connectUser()
{
  var userId, userGroupId, role, connectUserForm;

  userId = prompt('Enter user ID:');
  if (userId === null)
    return;
  userGroupId = prompt('Enter user group ID:', '36');
  if (userGroupId === null)
    return;
  role = prompt('Enter role number:', '1');
  if (role === null)
    return;
  
  connectUserForm = doucment.getElementById('connectUserForm');
  connectUserForm.innerHTML = '<input type="hidden" name="user_id" value="' + String(userId) +
    '" /><input type="hidden" name="user_group_id" value="' + String(userGroupId) +
    '" /><input type="hidden" name="role" value="' + String(role) + '" />';
  connectUserForm.submit();
}

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info('Gibbs administrator dashboard') ?>
    <div class="content">
      <div class="form-element">
        <a href="/subscription/html/gibbs_licencees.php">Customers and licences</a><br />
        <!--br />
        <form id="connectUserForm" action="/subscription/html/gibbs_dashboard.php" method="post">
          <button type="button" class="wide-button" onclick="connectUser();">Connect user to user group</button>
        </form-->
      </div>
    </div>
  </body>
</html>