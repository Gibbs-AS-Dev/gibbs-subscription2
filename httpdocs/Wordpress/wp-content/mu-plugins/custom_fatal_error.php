<?php
function custom_fatal_error_page() {
    if (defined('WP_FATAL_ERROR_HANDLER_DISPLAYING')) {
        // Det er en fatal feil, inkluder tilpasset HTML-kode her
        ?>
        <!DOCTYPE html>
        <html lang="no">
        <head>
            <title>Errrrrrror</title>
            <!-- Andre meta tags og head-elementer her -->

            <style>
                /* Din tilpassede CSS-kode her */
                body {
                    background-color: #f0f0f0;
                }
                .custom-class {
                    color: #ff0000;
                }

                .wp-die-message {
                    font-size: 14px;
                    line-height: 1.5;
                    margin: 25px 0 20px;
                    display: none;
                }
                div img {
                    clip-path: inset(0 0 10px 0);
                }
            </style>
        </head>
        <body>

        <h1>Oh my goodness!</h1>
        <p>Something went wrong! Noe gikk galt!</p>

        <p>kontakt@gibbs.no</p>

        <div>
            <img src="/wp-content/themes/gibbs_custom/error_robot.png" alt="Feilrobotbilde">
        </div>

        <!-- Resten av HTML-innholdet her -->
        </body>
        </html>
        <?php
        // Avslutt prosessen for å unngå å vise standard WordPress-feilmelding
        exit();
    }
}

add_action('template_redirect', 'custom_fatal_error_page');
?>
