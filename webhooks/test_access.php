<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/monthly_payments_utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

/*
  This file will attempt to read the config file, and log on as the Gibbs admin user found there.
  It will return a JSON object with a resultCode field, to verify that a) this file has been
  accessed successfully, and b) the credentials in the config file were valid.
*/

  // Log in as a Gibbs administrator. The credentials are found in the config file.
  $config = null;
  $access_token = null;
  $result_code = Result::NO_ACTION_TAKEN;
  Monthly_Payments_Utility::log_in_as_gibbs_admin($config, $access_token, $result_code);
  // The result code will be updated to say what happened, regardless of whether the log-in succeeded.
?>
{
  "resultCode": <?= $result_code ?>
}