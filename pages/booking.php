<?php
  // Load WordPress core.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gibbs Booking</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/pages/css/style.css" />
  </head>
  <body>

    <div class="content-area"> 
      <h1>Choose flow</h1>
    </div>
        <div class="content-main">
          <div class="content-inner">
            <div class="card form-card shadow-sm mb-5">
                  <div class="card_body">
                      <div class="content-2">
                          <div class="top-area">
                              <h3>Manual booking</h3>
                          </div>
                          <div class="bottom-area">
                              <div class="left-area">
                                <p>Tell about your storage needs and we  will contact you</p>
                              </div>
                              <div class="right-area">
                                <button class="btn btn-primary next-step" onclick="window.location.href='/subscription/pages/manual-booking.php'">Next<i class="fa fa-chevron-right"></i></button>
                              </div>
                          </div>
                      </div>
                  </div>
            </div>

            <div class="card form-card shadow-sm mb-5">
                  <div class="card_body">
                      <div class="content-2">
                          <div class="top-area">
                              <h3>Automatic booking</h3>
                          </div>
                          <div class="bottom-area">
                              <div class="left-area">
                                <p>Book,Pay and get access to your storage.</p>
                              </div>
                              <div class="right-area">  
                                <button class="btn btn-primary next-step" onclick="window.location.href=''">Next<i class="fas fa-chevron-right"></i></button>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        
      </div>
    </div>

  </body>
</html>
