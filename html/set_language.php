<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

  // Store the language.
  if(Utility::string_posted('language'))
  {
    Utility::set_current_language(Utility::read_posted_string('language'));
  }
  
  // Redirect to wherever the request wants to go.
  if (Utility::string_posted('redirect_to'))
  {
    Utility::redirect_to(Utility::read_posted_string('redirect_to'));
  }

  // The redirect_to link was not set. Redirect to the front page. This will clear the language
  // setting, so this page did nothing useful.
  Utility::redirect_to('/subscription/index.php');
?>
