<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
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
    <title>Gibbs abonnement - inaktiv lisens</title>
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <script type="text/javascript" src="/subscription/js/common.js"></script>
  </head>
  <body>
    <?= Sidebar::get_simple_sidebar() ?>
    <?= Header::get_header_with_user_info($text->get(0, 'Lisensen er ikke aktiv')) ?>
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
          <?= $text->get(6, 'Vennligst kontakt <a href="https://www.gibbs.no/kontakt-oss/">Gibbs kundeservice</a> for &aring; f&aring; aktivert lisensen.') ?>
        </p>
      </div>
    </div>
  </body>
</html>
