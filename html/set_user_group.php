<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

  // If the user is not logged in, or there is no user group ID on the session, redirect to the login page. You can not
  // switch your user group unless you have already successfully logged in.
  User::verify_logged_in();

  // A Gibbs administrator needs to pass a user group ID and a role number, since there is no Gibbs administrator role
  // in the database for every client.
  if (Utility::integers_passed(array('user_group_id', 'role')))
  {
    User::switch_to_role(User_Data_Manager::get_gibbs_admin_role(Utility::read_passed_integer('user_group_id'),
      Utility::read_passed_integer('role')));
  }

  // If the client passed a role, find the user group ID for that role, store it on the session, and redirect to the
  // appropriate initial page.
  if (Utility::integer_passed('role_id'))
  {
    User::switch_to_role_id(Utility::read_passed_integer('role_id'));
  }

  // If we get this far, we know the user is logged in, but did not pass a valid role ID. Redirect to the dashboard for
  // the user's default role.
  User::check_login_and_redirect();
?>
