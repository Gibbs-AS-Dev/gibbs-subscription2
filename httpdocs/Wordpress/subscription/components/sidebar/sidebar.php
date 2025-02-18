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
  // *** Company administrator sidebar.
  // *******************************************************************************************************************

  protected const ADMIN_MENU =
  [
    [
      'file_name' => '',
      'path' => '/dashbord/',
      'icon' => 'fa-person-to-door',
      'text_index' => 1,
      'default_text' => 'Gibbs booking',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => '[separator]'
    ],
    [
      'file_name' => 'admin_dashboard.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-gauge-simple-high',
      'text_index' => 2,
      'default_text' => 'Dashbord',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'admin_rental_overview.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-boxes-stacked',
      'text_index' => 15,
      'default_text' => 'Utleieoversikt',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'admin_orders.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-file-invoice-dollar',
      'text_index' => 16,
      'default_text' => 'Ordre',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'admin_subscriptions.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-repeat',
      'text_index' => 6,
      'default_text' => 'Abonnementer',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'admin_requests.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-message-question',
      'text_index' => 13,
      'default_text' => 'Foresp&oslash;rsler',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'admin_users.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-users',
      'text_index' => 5,
      'default_text' => 'Kunder',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => '[drawer]',
      'path' => '',
      'text_index' => 14,
      'default_text' => 'Lageradministrasjon',
      'enabled' => true,
      'subitems' =>
      [
        [
          'file_name' => 'admin_products.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-boxes-stacked',
          'text_index' => 4,
          'default_text' => 'Lagerboder',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_locations.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-location-dot',
          'text_index' => 3,
          'default_text' => 'Lager',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_product_types.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-box-check',
          'text_index' => 7,
          'default_text' => 'Bodtyper',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_categories.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-box-check',
          'text_index' => 21,
          'default_text' => 'Kategorier',
          'enabled' => true,
          'subitems' => []
        ]
      ]
    ],
    [
      'file_name' => '[drawer]',
      'path' => '',
      'text_index' => 19,
      'default_text' => 'SMS og e-post',
      'enabled' => true,
      'subitems' =>
      [
        [
          'file_name' => 'admin_email_sms_log.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-envelopes',
          'text_index' => 18,
          'default_text' => 'Logg',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_email_sms_templates.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-envelopes-bulk',
          'text_index' => 17,
          'default_text' => 'Maler',
          'enabled' => true,
          'subitems' => []
        ]
      ]
    ],
    [
      'file_name' => '[drawer]',
      'path' => '',
      'text_index' => 22,
      'default_text' => 'Konfigurasjon',
      'enabled' => true,
      'subitems' =>
      [
        [
          'file_name' => 'admin_settings.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-gear',
          'text_index' => 10,
          'default_text' => 'Innstillinger',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_price_rules.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-square-dollar',
          'text_index' => 12,
          'default_text' => 'Prisregler',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'admin_insurance.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-fire',
          'text_index' => 9,
          'default_text' => 'Forsikringer',
          'enabled' => true,
          'subitems' => []
        ]
      ]
    ]
/*
    [
      'file_name' => 'admin_blocked_users.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-ban',
      'text_index' => 11,
      'default_text' => 'Blokkerte kunder',
      'enabled' => true,
      'subitems' => []
    ],
*/
  ];

  // *******************************************************************************************************************
  // Return HTML code for the company administrator sidebar, including Javascript to display it once the page has
  // loaded.
  public static function get_admin_sidebar()
  {
    return self::get_sidebar(self::ADMIN_MENU);
  }

  // *******************************************************************************************************************
  // *** Gibbs administrator sidebar.
  // *******************************************************************************************************************

  protected const GIBBS_ADMIN_MENU =
  [
    [
      'file_name' => '',
      'path' => '/dashbord/',
      'icon' => 'fa-person-to-door',
      'text_index' => 1,
      'default_text' => 'Gibbs booking',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => '[separator]'
    ],
    [
      'file_name' => 'gibbs_dashboard.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-house',
      'text_index' => -1,
      'default_text' => 'Gibbs administrator',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'gibbs_licencees.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-users',
      'text_index' => -1,
      'default_text' => 'User groups and licences',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => 'gibbs_register.php',
      'path' => '/subscription/html/',
      'icon' => 'fa-user-plus',
      'text_index' => -1,
      'default_text' => 'Register new user',
      'enabled' => true,
      'subitems' => []
    ],
    [
      'file_name' => '[drawer]',
      'path' => '',
      'text_index' => -1,
      'default_text' => 'Nets',
      'enabled' => true,
      'subitems' =>
      [
        [
          'file_name' => 'gibbs_monthly_orders.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-file-invoice-dollar',
          'text_index' => -1,
          'default_text' => 'Monthly orders',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'gibbs_monthly_payments.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-square-dollar',
          'text_index' => -1,
          'default_text' => 'Monthly payments',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'gibbs_payment_status.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-message-question',
          'text_index' => -1,
          'default_text' => 'Payment status',
          'enabled' => true,
          'subitems' => []
        ],
        [
          'file_name' => 'gibbs_test_webhook_access.php',
          'path' => '/subscription/html/',
          'icon' => 'fa-check-double',
          'text_index' => -1,
          'default_text' => 'Test webhook access',
          'enabled' => true,
          'subitems' => []
        ]
      ]
    ]
  ];

  // *******************************************************************************************************************
  // Return HTML code for the Gibbs administrator sidebar, including Javascript to display it once the page has loaded.
  public static function get_gibbs_admin_sidebar()
  {
    return self::get_sidebar(self::GIBBS_ADMIN_MENU);
  }

  // *******************************************************************************************************************
  // *** Expanding sidebar.
  // *******************************************************************************************************************
  // Return HTML code for a sidebar with the given $menu, including Javascript to display it once the page has loaded.
  public static function get_sidebar($menu)
  {
    // Get translated texts.
    $text = new Translation('sidebar', 'storage', '');

    // Write sidebar HTML elements.
    $result  = '<div class="sidebar expanding-sidebar"><div class="logo"><a href="/subscription/index.php"><img src="/subscription/resources/logo.png?v=';
    $result .= Utility::BUILD_NO;
    $result .= '" alt="';
    $result .= $text->get(8, 'Gibbs logo');
    $result .= '" class="logo" /></a><a href="/subscription/index.php" class="logo"><span class="gibbs">gibbs</span><br />';
    $result .= $text->get(20, 'minilager');
    $result .= '</a></div><div id="sidebarMenu" class="sidebar-menu"></div></div>';

    // Write script to generate the menu items.
    $result .= "<script type=\"text/javascript\">\n";
    $result .= "var sidebarMenu = new SidebarMenu('sidebarMenu',\n";
    $result .= Sidebar::get_sidebar_menu_data($text, $menu);
    $result .= "  );\n";
    $result .= "</script>";

    return $result;
  }

  // *******************************************************************************************************************
  // Return a string that holds a Javascript table declaration that can be used to generate the contents of the sidebar
  // menu. The table declaration is based on the data in the given $menu. $text is a Translation instance that provides
  // translated texts for the menu.
  protected static function get_sidebar_menu_data($text, $menu)
  {
    $result  = "  [";
    if (count($menu) > 0) 
    {
      foreach ($menu as $menu_item)
      {
        $result .= self::get_menu_item_data($text, $menu_item, '      ');
      }
      $result = Utility::remove_final_comma($result);
      $result .= "\n";
    }
    $result .= "  ]\n";
    return $result;
  }

  // *******************************************************************************************************************
  // Return a string that holds a Javascript array declaration with data for the given $menu_item. $text is a
  // Translation instance that provides translated texts for the menu.
  protected static function get_menu_item_data($text, $menu_item, $indent)
  {
    $result  = "\n";
    if ($menu_item['file_name'] === '[separator]')
    {
      $result .= $indent . "[\n";
      $result .= $indent . "  '[separator]'\n";
      $result .= $indent . "],";
    }
    else
    {
      $result .= $indent . "[\n";
      // URL.
      $result .= $indent . "  '" . $menu_item['path'] . $menu_item['file_name'] . "',\n";
      if ($menu_item['file_name'] === '[drawer]')
      {
        // Open flag. A drawer is open by default if a subitem within the drawer is selected.
        if (self::holds_current_file($menu_item['subitems']))
        {
          $result .= $indent . "  true,\n";
        }
        else
        {
          $result .= $indent . "  false,\n";
        }
        // Text.
        $result .= $indent . "  '" . $text->get($menu_item['text_index'], $menu_item['default_text']) . "',\n";
        // Subitems.
        $result .= $indent . "  [";
        if (isset($menu_item['subitems']) && (count($menu_item['subitems']) > 0))
        {
          foreach ($menu_item['subitems'] as $submenu_item)
          {
            $result .= self::get_menu_item_data($text, $submenu_item, $indent . '    ');
          }
          $result = Utility::remove_final_comma($result);
          $result .= "\n";
          $result .= $indent . "  ";
        }
        $result .= "]\n";
      }
      else
      {
        // Icon.
        $result .= $indent . "  '" . $menu_item['icon'] . "',\n";
        // Text.
        $result .= $indent . "  '" . $text->get($menu_item['text_index'], $menu_item['default_text']) . "',\n";
        // Enabled.
        $result .= $indent . "  " . ($menu_item['enabled'] ? "true" : "false") . ",\n";
        // Selected.
        $result .= $indent . "  " . (self::is_current_file($menu_item['file_name']) ? "true" : "false") . "\n";
      }
      $result .= $indent . "],";
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return true if the given $menu includes the current file name.
  protected static function holds_current_file($menu)
  {
    if (!isset($menu))
    {
      return false;
    }
    $current_file = self::get_current_file();

    foreach ($menu as $menu_item)
    {
      // This menu item holds the current file if the file name is the same, or its subitems (if there are any) hold the
      // current file.
      if (($menu_item['file_name'] === $current_file) ||
        ((count($menu_item['subitems']) > 0) && (self::holds_current_file($menu_item['subitems']))))
      {
        return true;
      }
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the given $file_name is the same as the name of the currently displayed file. $file_name should not
  // include a path.
  protected static function is_current_file($file_name)
  {
    return $file_name === self::get_current_file();
  }

  // *******************************************************************************************************************
  // Return the name of the file in the current URL, for instance "user_dashboard.php", without any path or parameter
  // information.
  protected static function get_current_file()
  {
    // The REQUEST_URI field includes query parameters. Use parse_url to get rid of them, and then remove the path using
    // the basename function.
    return basename(parse_url($_SERVER['REQUEST_URI'])['path']);
  }

  // *******************************************************************************************************************

}
?>
