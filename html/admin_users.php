<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/test_data_manager.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/role_data_manager.php';
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
    $users = Test_Data_Manager::USERS;
  }
  else
  {
    $user_data = new Role_Data_Manager($access_token);
    $user_data->set_role_filter(Utility::ROLE_NUMBER_USER);
    $result_code = Result::NO_ACTION_TAKEN;
    // Read list of users to be displayed.
    $users = $user_data->read();
  }
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
    <script type="text/javascript" src="/subscription/js/admin_users.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var ENTITY_TYPE_TEXTS = <?= $text->get(2, "['Privatpersoner', 'Bedrifter']") ?>;
var ACTIVE_TEXTS = <?= $text->get(3, "['Kunder uten aktive abonnementer', 'Kunder med aktive abonnementer']") ?>;

// The current entity type filter, or null if all users are displayed, regardless of entity type.
// The filter is an array of integers, containing entity types that should be displayed.
var entityTypeFilter = <?= Utility::verify_filter('entity_type_filter') ?>;

// The current filter on active or inactive users, or null if all users are displayed, regardless of
// whether they are active or not. The filter is an array of integers, containing a zero if inactive
// users (users without an active subscription) should be displayed, and a 1 if active users (users
// with an active subscription) should be displayed.
var activeFilter = <?= Utility::verify_filter('active_filter') ?>;

// The current freetext filter, or an empty string if all users are displayed, regardless of what
// they contain. If a text is supplied, users will only be displayed if they contain that text, as
// part of either the name, e-mail or phone number fields.
var freetextFilter = '<?= Utility::read_passed_string('freetext_filter', '') ?>';

var TIMESTAMP = '<?= Utility::get_timestamp() ?>';
var resultCode = <?= $result_code ?>;
var users = <?= $users ?>;

    </script>
  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(0, 'Kunder'), 'fa-users') ?>
    <div class="content">
      <div class="toolbar">
        <!--a href="/subscription/html/admin_edit_user.php" class="button wide-button"><i class="fa-solid fa-user-plus"></i> <?= $text->get(1, 'Opprett ny kunde') ?></a-->
        <div id="filterToolbar" class="filter">
          &nbsp;
        </div>
      </div>
      <div id="usersBox">
        &nbsp;
      </div>
    </div>

    <?= Utility::get_spinner() ?>
    <div id="overlay" class="overlay" style="display: none;">
      &nbsp;
    </div>
    <div id="editEntityTypeFilterDialogue" class="dialogue edit-entity-type-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
    <div id="editActiveFilterDialogue" class="dialogue edit-active-filter-dialogue" style="display: none;">
      &nbsp;
    </div>
  </body>
</html>
