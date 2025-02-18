<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';

  // Get translated texts.
  $text = new Translation('', 'storage', '');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/common.css?v=<?= Utility::BUILD_NO ?>" />
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
  </head>
  <body>
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info(null, $text->get(0, 'Lisensen er ikke aktiv')) ?>
    <div class="content">
      <div class="form-element">
        <p>
          <?= $text->get(1, 'Vi beklager, men din lisens er ikke aktiv. Du har for &oslash;yeblikket ikke tilgang til administrasjonsgrensesnittet, og kundene dine vil f&aring; beskjed om at l&oslash;sningen er midlertidig utilgjengelig.') ?>
        </p>
        <p>
          <?= $text->get(2, 'Dette kan blant annet skyldes at:') ?>
          <ul>
            <li><?= $text->get(3, 'Lisensen er lagt inn, men ikke aktivert enn&aring;.') ?></li>
            <li><?= $text->get(4, 'Lisensen er er sagt opp.') ?></li>
            <li><?= $text->get(5, 'Lisensen er deaktivert p&aring; grunn av manglende betaling, eller av andre &aring;rsaker.') ?></li>
          </ul>
        </p>
        <p>
          <?= $text->get(6, 'Vennligst kontakt $0Gibbs kundeservice$1 for &aring; f&aring; aktivert lisensen.', array('<a href="javascript:void(0);" onclick="Utility.displaySpinnerThenGoTo(\'https://www.gibbs.no/kontakt-oss/\');">', '</a>')) ?>
        </p>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
