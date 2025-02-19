<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

class Header
{
  // *******************************************************************************************************************
  // *** Common methods.
  // *******************************************************************************************************************
  // Return HTML code to insert the icon with the given name. $icon could be something like 'fa-house'.
  protected static function get_icon($icon)
  {
    if (isset($icon) && !empty($icon) && is_string($icon))
      return '<i class="fa-solid ' . $icon . '"></i> ';
    return '';
  }

  // *******************************************************************************************************************

  protected static function get_headline($headline, $icon)
  {
    return '<h1>' . self::get_icon($icon) . $headline . '</h1>';
  }

  // *******************************************************************************************************************
  // *** Header without user information in the top-right corner.
  // *******************************************************************************************************************

  public static function get_simple_header($headline, $icon = '')
  {
    return '<div class="header">' . self::get_headline($headline, $icon) . '</div>';
  }

  // *******************************************************************************************************************
  // *** Header with user information in the top-right corner, and a drop-down menu.
  // *******************************************************************************************************************

  protected static function get_language_option($language_value, $language_description, $current_language)
  {
    $result  = '<option value="';
    $result .= $language_value;
    $result .= '"';
    if ($current_language === $language_value)
    {
      $result .= ' selected="selected"';
    }
    $result .= ' data-image="/subscription/resources/language/';
    $result .= $language_value;
    $result .= '.png?v=';
    $result .= Utility::BUILD_NO;
    $result .= '">';
    $result .= $language_description;
    $result .= '</option>';
    return $result;
  }

  // *******************************************************************************************************************
  // Return the description of the given role. The role in question is an array, which may have been returned by the
  // User_Data_Manager::get_user_roles method, and should have the following fields:
  //   user_id : integer
  //   role_id : integer          The database ID of the role. This can be used to switch roles.
  //   role_number : integer      Use the ROLE_NUMBER_ constants.
  //   user_group_id : integer
  //   user_group_name : string
  //   licence_status : integer   0: inactive, 1: active
  //
  // The role name will be translated using the given $text object, and will warn the user if the licence is not active.
  protected static function get_role_description($role_data, $text)
  {
    // The role is stored as a tinyint in the database, which might mean that it is returned as a string. Convert to a
    // number, just to be sure.
    $role_number = $role_data['role_number'];
    if (!isset($role_number) || !is_numeric($role_number))
    {
      return '';
    }
    $role_number = intval($role_number);
    $result = '';
    if (($role_number >= 1) && ($role_number <= 3))
    {
      // Display the role name, and a warning if the licence is inactive.
      if ($role_number === 1)
      {
        $result .= $text->get(4, 'kunde');
        if (intval($role_data['licence_status']) !== 1)
        {
          $result .= $text->get(6, '; ikke tilgjengelig');
        }
      }
      else
      {
        $result .= $text->get(5, 'administrator');
        if (intval($role_data['licence_status']) !== 1)
        {
          $result .= $text->get(7, '; lisens utl&oslash;pt');
        }
      }
    }
    return $result;
  }

  // *******************************************************************************************************************

  protected static function get_role_option($role_data, $selected, $text)
  {
    $result = '<option value="';
    $result .= $role_data['role_id'];
    $result .= '"';
    if ($selected)
    {
      $result .= ' selected="selected"';
    }
    $result .= '>';
    $result .= $role_data['user_group_name'];
    $role_description = self::get_role_description($role_data, $text);
    if (!empty($role_description))
    {
      $result .= ' (';
      $result .= $role_description;
      $result .= ')';
    }
    $result .= '</option>';
    return $result;
  }

  // *******************************************************************************************************************
  // Return true if the given $role_data represents the user's current role and user group, as stated in the given
  // $access_token.
  protected static function is_current_role($access_token, $role_data)
  {
    if (!isset($access_token))
    {
      return false;
    }
    return ($role_data['user_group_id'] === $access_token->get_user_group_id()) &&
      (Utility::role_number_to_role($role_data['role_number']) === $access_token->get_role());
  }

  // *******************************************************************************************************************
  // This assumes that the user is currently logged in. $access_token may be null, in which case no user group will be
  // displayed as selected.
  public static function get_header_with_user_info($access_token, $headline, $icon = '')
  {
    $current_user = wp_get_current_user();
    $current_language = Utility::get_current_language();
    // Get translated texts.
    $text = new Translation('header', 'storage', '');
    // Get the list of roles that this user can have.
    $roles = User_Data_Manager::get_user_roles();

    // Header information, displayed continuously. The toggleCurrentUserMenu function is found in common.js.
    $result = '<div class="header">';
    $result .= self::get_headline($headline, $icon);
    $result .= '  <div id="currentUserMenuButton" class="current-user-box" onclick="toggleCurrentUserMenu();">';
    $result .= '    <div class="user-name">';
    $result .= '      ' . $current_user->first_name . ' ' . $current_user->last_name;
    $result .= '    </div>';
    $result .= '    <div class="user-image">';
    $result .= '      <img src="' . get_avatar_url($current_user->ID) . '" width="40" height="40" alt="' . $text->get(0, 'Bilde av p&aring;logget bruker') . '" />';
    $result .= '    </div>';
    $result .= '  </div>';
    $result .= '</div>';

    // Add event handler to close the drop-down menu if the user clicks anywhere else. The clickOutsideCurrentUserMenu
    // function is found in common.js.
    $result .= '<script type="text/javascript">';
    $result .= 'document.addEventListener(\'click\', clickOutsideCurrentUserMenu);';
    $result .= '</script>';

    // Drop-down menu. The setUserGroup and submitLanguageSelection handlers are found in commmon.js.
    $result .= '<div id="currentUserMenu" class="current-user-menu" style="display: none;">';
    $result .= '  <div class="user-menu-item user-e-mail">' . $current_user->user_email . '</div>';
    $result .= '  <div class="user-menu-item"><button type="button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/html/edit_user.php\');"><i class="fa-solid fa-circle-user"></i>&nbsp;&nbsp;' . $text->get(1, 'Min profil') . '</button></div>';
    $result .= '  <div class="user-menu-item"><select onchange="setUserGroup(this.options[this.selectedIndex].value);">';
    if (!isset($access_token))
    {
      $result .= '    <option value="-1" disabled="disabled" selected="selected">' . $text->get(3, 'Velg avdeling') . '</option>';
    }
    if (!empty($roles))
    {
      foreach ($roles as $role_data)
      {
        $result .= self::get_role_option($role_data, self::is_current_role($access_token, $role_data), $text);
      }
    }
    $result .= '  </select></div>';
    $result .= '  <div class="user-menu-item">';
    $result .= '    <form id="selectLanguageForm" action="/subscription/html/set_language.php" method="post">';
    $result .= '      <input type="hidden" name="redirect_to" value="' . sanitize_text_field($_SERVER['REQUEST_URI']) . '" />';
    $result .= '      <select name="language" onchange="submitLanguageSelection();">';
    $result .= self::get_language_option(Utility::NORWEGIAN, 'Norsk (bokm&aring;l)', $current_language);
    // $result .= self::get_language_option(Utility::SWEDISH, 'Svenska', $current_language);
    $result .= self::get_language_option(Utility::ENGLISH, 'English (UK)', $current_language);
    $result .= '      </select>';
    $result .= '    </form>';
    $result .= '  </div>';
    $result .= '  <div class="user-menu-item"><button type="button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/index.php\');"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;' . $text->get(2, 'Logg ut') . '</button></div>';
    $result .= '</div>';

    return $result;
  }

  // *******************************************************************************************************************
  // This assumes that the user is currently logged in. $access_token may be null, in which case no user group will be
  // displayed as selected.
  public static function get_header_for_mobile($access_token)
  {
    $current_user = wp_get_current_user();
    $current_language = Utility::get_current_language();
    // Get translated texts.
    $text = new Translation('header', 'storage', '');
    // Get the list of roles that this user can have.
    $roles = User_Data_Manager::get_user_roles();

    // Header information, displayed continuously.
    $result =  '<div class="current-user-box">';
    $result .= '  <div class="user-name" onclick="toggleCurrentUserMenu();">';
    $result .= '    ' . $current_user->first_name . ' ' . $current_user->last_name;
    $result .= '  </div>';
    $result .= '  <div class="user-image" onclick="toggleCurrentUserMenu();">';
    $result .= '    <img src="' . get_avatar_url($current_user->ID) . '" width="40" height="40" alt="' . $text->get(0, 'Bilde av p&aring;logget bruker') . '" />';
    $result .= '  </div>';

    // Drop-down menu. The setUserGroup and submitLanguageSelection handlers are found in commmon.js.
    $result .= '  <div id="currentUserMenu" class="current-user-menu" style="display: none;">';
    $result .= '    <div class="user-menu-item user-e-mail">' . $current_user->user_email . '</div>';
    $result .= '    <div class="user-menu-item"><button type="button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/html/edit_user.php\');"><i class="fa-solid fa-circle-user"></i>&nbsp;&nbsp;' . $text->get(1, 'Min profil') . '</button></div>';
    $result .= '    <div class="user-menu-item"><select onchange="setUserGroup(this.options[this.selectedIndex].value);">';
    if (!isset($access_token))
    {
      $result .= '      <option value="-1" disabled="disabled" selected="selected">' . $text->get(3, 'Velg avdeling') . '</option>';
    }
    if (!empty($roles))
    {
      foreach ($roles as $role_data)
      {
        $result .= self::get_role_option($role_data, self::is_current_role($access_token, $role_data), $text);
      }
    }
    $result .= '    </select></div>';
    $result .= '    <div class="user-menu-item">';
    $result .= '      <form id="selectLanguageForm" action="/subscription/html/set_language.php" method="post">';
    $result .= '        <input type="hidden" name="redirect_to" value="' . sanitize_text_field($_SERVER['REQUEST_URI']) . '" />';
    $result .= '        <select name="language" onchange="submitLanguageSelection();">';
    $result .= self::get_language_option(Utility::NORWEGIAN, 'Norsk (bokm&aring;l)', $current_language);
    // $result .= self::get_language_option(Utility::SWEDISH, 'Svenska', $current_language);
    $result .= self::get_language_option(Utility::ENGLISH, 'English (UK)', $current_language);
    $result .= '        </select>';
    $result .= '      </form>';
    $result .= '    </div>';
    $result .= '    <div class="user-menu-item"><button type="button" onclick="Utility.displaySpinnerThenGoTo(\'/subscription/index.php\');"><i class="fa-solid fa-person-to-door"></i>&nbsp;&nbsp;' . $text->get(2, 'Logg ut') . '</button></div>';
    $result .= '  </div>';

    $result .= '</div>';

    return $result;
  }

  // *******************************************************************************************************************
}
?>
