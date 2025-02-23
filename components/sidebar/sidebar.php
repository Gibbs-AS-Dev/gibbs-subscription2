<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';

class Sidebar
{
  // *******************************************************************************************************************
  // *** Simple sidebar.
  // *******************************************************************************************************************

  public static function get_simple_sidebar()
  {
    return file_get_contents(dirname(__FILE__) . '/simple_sidebar.html');
  }

  // *******************************************************************************************************************
  // *** Expanding sidebar.
  // *******************************************************************************************************************

  protected const MENU =
  [
    '' =>
    [
      'path' => '/dashbord/',
      'icon' => 'fa-person-to-door',
      'text_index' => 1,
      'default_text' => 'Gibbs booking',
      'enabled' => true,
      'subitems' => []
    ],
    'null' =>
    [
      'path' => '',
      'icon' => '',
      'text_index' => 0,
      'default_text' => '&nbsp;',
      'enabled' => false,
      'subitems' => []
    ],
    'admin_dashboard.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-house',
      'text_index' => 2,
      'default_text' => 'Gibbs minilager',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_product_types.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-box-check',
      'text_index' => 7,
      'default_text' => 'Bodtyper',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_locations.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-location-dot',
      'text_index' => 3,
      'default_text' => 'Lager',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_products.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-boxes-stacked',
      'text_index' => 4,
      'default_text' => 'Lagerboder',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_prices.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-square-dollar',
      'text_index' => 12,
      'default_text' => 'Priser',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_users.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-users',
      'text_index' => 5,
      'default_text' => 'Brukere',
      'enabled' => true,
      'subitems' =>
      [
        'admin_edit_user.php' =>
        [
          'path' => '/subscription/html/',
          'icon' => 'fa-repeat',
          'text_index' => 6,
          'default_text' => 'Abonnementer',
          'enabled' => false
        ]
      ]
    ],
    'admin_blocked_users.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-ban',
      'text_index' => 11,
      'default_text' => 'Blokkerte brukere',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_insurance.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-fire',
      'text_index' => 9,
      'default_text' => 'Forsikringer',
      'enabled' => true,
      'subitems' => []
    ],
    'admin_settings.php' =>
    [
      'path' => '/subscription/html/',
      'icon' => 'fa-gear',
      'text_index' => 10,
      'default_text' => 'Innstillinger',
      'enabled' => true,
      'subitems' => []
    ]
  ];

  // *******************************************************************************************************************

  public static function get_expanding_sidebar()
  {
    // Get translated texts.
    $text = new Translation('sidebar', 'storage', '');

    $result  = '<div class="sidebar expanding-sidebar">';
    $result .= '  <div class="logo">';
    $result .= '    <a href="/subscription/index.php"><img src="/subscription/resources/logo.png" alt="' . $text->get(8, 'Gibbs logo') . '" class="logo" /></a><a href="/subscription/index.php" class="logo">gibbs.no</a>';
    $result .= '  </div>';
    $result .= '  <div class="sidebar-menu">';
    foreach (self::MENU as $file_name => $menu_item)
    {
      $result .= self::get_menu_item($text, $file_name, $menu_item);
    }
    $result .= '';
    $result .= '  </div>';
    $result .= '</div>';
    return $result;
  }

  // *******************************************************************************************************************

  protected static function get_menu_item($text, $file_name, $menu_item)
  {
    $result = '';
    if (self::is_current_file($file_name))
    {
      // Write a selected menu item.
      $result .= '<span class="sidebar-item sidebar-item-selected"><i class="fa-solid ' . $menu_item['icon'] . ' sidebar-icon" style="color: #fff;"></i> ' . $text->get($menu_item['text_index'], $menu_item['default_text']) . '</span><br />';
    }
    elseif ($menu_item['enabled'])
    {
      // Write an enabled menu item.
      $result .= '<a href="' . $menu_item['path'] . $file_name . '" class="sidebar-item sidebar-item-enabled"><i class="fa-solid ' . $menu_item['icon'] . ' sidebar-icon"></i> ' . $text->get($menu_item['text_index'], $menu_item['default_text']) . '</a><br />';
    }
    else
    {
      // Write a disabled menu item.
      $result .= '<span class="sidebar-item sidebar-item-disabled">' . $menu_item['text'] . '</span><br />';
    }

    foreach ($menu_item['subitems'] as $subitem_file_name => $subitem)
    {
      $result .= self::get_subitem($text, $subitem_file_name, $subitem);
    }
    return $result;
  }

  // *******************************************************************************************************************

  protected static function get_subitem($text, $file_name, $menu_item)
  {
    $result = '';
    if (self::is_current_file($file_name))
    {
      // Write a selected submenu item.
      $result .= '<span class="sidebar-item sidebar-item-selected"><i class="fa-solid ' . $menu_item['icon'] . ' sidebar-icon sidebar-icon-indent-1" style="color: #fff;"></i> ' . $text->get($menu_item['text_index'], $menu_item['default_text']) . '</span><br />';
    }
    else
    {
      // TODO: Write an enabled submenu item.
      // Write a disabled submenu item.
      $result .= '<span class="sidebar-item sidebar-item-disabled"><i class="fa-solid ' . $menu_item['icon'] . ' sidebar-icon sidebar-icon-indent-1"></i> ' . $text->get($menu_item['text_index'], $menu_item['default_text']) . '</span><br />';
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return true if the given $file_name is the same as the name of the currently displayed file. $file_name should not
  // include a path.
  protected static function is_current_file($file_name)
  {
    // The REQUEST_URI field includes query parameters. Use parse_url to get rid of them, and then remove the path using
    // the basename function.
    return $file_name === basename(parse_url($_SERVER['REQUEST_URI'])['path']);
  }

  // *******************************************************************************************************************

}
?>
