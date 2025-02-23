<?php
  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

  $active_tab = "all";

  if(isset($_GET['active'])){
    $active_tab = $_GET['active'];
  }

  if(isset($_POST['request_id'])){
      global $wpdb;
      $table_subscription_manual_requests = "subscription_manual_requests";
      $request_data = array(
          'status' => $_POST["status"],
      );
      $where = array("id"=>$_POST["request_id"]);

      // Insert user data into the users table
      $update_request = $wpdb->update($table_subscription_manual_requests, $request_data, $where);

      if(isset($_POST['active'])){
        $active_tab = $_POST['active'];
      }
  }

  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gibbs abonnement</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/pages/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/subscription/pages/css/booking-request-style.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.css">
  </head>
  <body>
    <div class="content-main">
        <div class="content-area"> 

        </div>
        <div class="content-inner requests">
          <div class="card shadow-sm mb-5">
                  <div class="card_body_inner">
                      <div class="content-2 booking-cl">
                           <!-- Tab links -->
                            <ul class="tab-ul">
                              <li class="tablinks <?php if($active_tab == 'all'){ echo 'active';};?>" onclick="openCity(event, 'all')">All</li>
                              <li class="tablinks <?php if($active_tab == 'need_attention'){ echo 'active';};?>" onclick="openCity(event, 'need_attention')">Need your attention</li>
                              <li class="tablinks <?php if($active_tab == 'won'){ echo 'active';};?>" onclick="openCity(event, 'won')">Won</li>
                              <li class="tablinks <?php if($active_tab == 'lost'){ echo 'active';};?>" onclick="openCity(event, 'lost')">Lost</li>
                            </ul>

                            <!-- Tab content -->
                            <div class="tab-main-content">
                              <div id="all" class="tabcontent <?php if($active_tab == 'all'){ echo 'active';}?>">
                                  <?php require("table/all.php");?>
                              </div>
                              <div id="need_attention" class="tabcontent <?php if($active_tab == 'need_attention'){ echo 'active';}?>">
                                  <?php require("table/need_attention.php");?>
                              </div>
                              <div id="won" class="tabcontent <?php if($active_tab == 'won'){ echo 'active';}?>">
                                  <?php require("table/won.php");?>
                              </div>
                              <div id="lost" class="tabcontent <?php if($active_tab == 'lost'){ echo 'active';}?>">
                                  <?php require("table/lost.php");?>
                              </div>
                            </div>  

                      </div>
                  </div>
            </div>
        </div>
        
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.dataTables.js"></script>


    <script type="text/javascript">
      var dataTb = {
          responsive: true,
          "language": {
            "sProcessing":    "behandling...",
            "sLengthMenu":    "Vis _MENU_ poster",
            "sZeroRecords":   "Ingen resultater",
            "sEmptyTable":    "Ingen data tilgjengelig i denne tabellen",
            "sInfo":          "Viser _START_ til _END_ av _TOTAL_ bookinger",
            "sInfoEmpty":     "Viser poster fra 0 til 0 av totalt 0 poster",
            "sInfoFiltered":  "(filtrerer totalt _MAX_ poster)",
            "sInfoPostFix":   "",
            "sSearch":        "Søke:",
            "sUrl":           "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Lader...",
            "oPaginate": {
                "sFirst":    "Først",
                "sLast":    "Siste",
                "sNext":    "Følgende",
                "sPrevious": "Fremre"
            },
            "oAria": {
                "sSortAscending":  ": Merk av for å sortere kolonnen i stigende rekkefølge",
                "sSortDescending": ": Merk av for å sortere kolonnen synkende"
            }
          }
      };
      let all_bk = new DataTable('#all_bk', dataTb); 
      let need_attention_bk = new DataTable('#need_attention_bk', dataTb); 
      let won_bk = new DataTable('#won_bk', dataTb); 
      let lost_bk = new DataTable('#lost_bk', dataTb); 
      jQuery('#searchAll').keyup(function(){
            all_bk.search(jQuery(this).val()).draw() ;
      }) 
      jQuery('#searchNeedAtt').keyup(function(){
            need_attention_bk.search(jQuery(this).val()).draw() ;
      }) 
      jQuery('#searchWon').keyup(function(){
            won_bk.search(jQuery(this).val()).draw() ;
      }) 

      jQuery('#searchLost').keyup(function(){
            lost_bk.search(jQuery(this).val()).draw() ;
      }) 
      
      jQuery(".main-table-bk").removeClass("vs-hidden");
      jQuery(".status_change").change(function(){
         if(this.value != ""){
            jQuery(this).parent().submit();
         }
      })
    </script>
    <script type="text/javascript">
      function openCity(evt, cityName) {
        // Declare all variables
        jQuery(".tabcontent").removeClass("active");
        jQuery("#"+cityName).addClass("active");

        jQuery(".tablinks").removeClass("active");
        jQuery(evt.currentTarget).addClass("active");
      }
    </script>

  </body>
</html>
