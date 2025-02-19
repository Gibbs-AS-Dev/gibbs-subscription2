<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

  // Log out, and redirect to the login page.
  User::log_out();
  Utility::redirect_to('/subscription/html/log_in_to_dashboard.php');
?>