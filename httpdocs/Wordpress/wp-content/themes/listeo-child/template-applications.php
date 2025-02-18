

get_header();
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">

<?php
    global $wpdp;
    $cuser_id = get_current_user_id();
    // $cuser_id = 1;
    $groups_table =$wpdb->prefix .'users_groups';
    $users_groups_table = $wpdb->prefix .'users_and_users_groups';
    if($cuser_id){
        $query = "SELECT id FROM $groups_table WHERE id IN (SELECT users_groups_id FROM $users_groups_table WHERE users_id = $cuser_id)";
        $group_id_data = $wpdb->get_results($query);
        $group_id_data = json_decode(json_encode($group_id_data), true);
        $groupIds = array_column($group_id_data, 'id');
        if(count($groupIds) > 0){
            $groupIdsArr = implode (", ", $groupIds);
            $querySeasons = "SELECT id,name FROM `seasons` where users_groups_id IN ($groupIdsArr)";
            $querySeasonsDropdown = "SELECT season.id,season.name, user_group.name as user_group FROM `seasons` as season
                LEFT JOIN `ptn_users_groups` as user_group ON user_group.id = season.users_groups_id 
                WHERE users_groups_id IN ($groupIdsArr) ORDER BY id DESC";
            $seasons_data = $wpdb->get_results($querySeasons);
            $seasons_data = json_decode(json_encode($seasons_data), true);
            $seasonIds = array_column($seasons_data, 'id');

            $seasons_data_dropdown = $wpdb->get_results($querySeasonsDropdown);
            $seasons_data_dropdown = json_decode(json_encode($seasons_data_dropdown), true);
        }
        if(count($seasonIds) > 0) {
            // $seasonsIdsArr = implode (", ", $seasonIds);
            $latestSeasonID = $seasonIds[count($seasonIds) -1];
            $queryApplications = "
                SELECT app.id as app_id, app.name as app_name,app.score as score,
                    app_user.display_name as app_user,
                    app.members as members, season.name as season_name,
                    team.name as team_name,
                    age_group.name as age_group_name,
                    sport.name as sport_name,
                    type.name as type,
                    team_level.name as team_level,
                    IFNULL(booking_desired.sum_desired_hours, 0) as sum_desired_hours,
                    IFNULL(booking_received.sum_received_hours, 0) as sum_received_hours
                    preferred_listing1.post_title as preferred_listing1_title,
                    preferred_listing2.post_title as preferred_listing2_title,
                    preferred_listing3.post_title as preferred_listing3_title
                    FROM `applications` as app
                    LEFT JOIN `seasons` as season ON season.id =  app.season_id
                    LEFT JOIN `ptn_team` as team ON team.id = app.team_id
                    LEFT JOIN `age_group` as age_group ON age_group.id = app.age_group_id
                    LEFT JOIN `sport` as sport ON sport.id = app.sport_id
                    LEFT JOIN `type` as type ON type.id = app.type_id
                    LEFT JOIN `team_level` as team_level ON team_level.id = app.team_level_id
                    LEFT JOIN `ptn_posts` as preferred_listing1 ON preferred_listing1.ID = app.preferred_listing1_id
                    LEFT JOIN `ptn_posts` as preferred_listing2 ON preferred_listing2.ID = app.preferred_listing2_id
                    LEFT JOIN `ptn_posts` as preferred_listing3 ON preferred_listing3.ID = app.preferred_listing3_id
                    LEFT JOIN `ptn_users` as app_user ON app_user.ID = app.user_id
                    LEFT JOIN
                        (SELECT `application_id`, ROUND(TIMESTAMPDIFF(HOUR,`date_start`,`date_end`),0) AS `sum_desired_hours`
                            FROM `ptn_bookings_calendar_raw` 
                            WHERE `fixed` = 0 GROUP BY(`application_id`)
                        )
                    AS booking_desired ON booking_desired.application_id = app.id
                    LEFT JOIN
                        (SELECT `application_id`, ROUND(TIMESTAMPDIFF(HOUR,`date_start`,`date_end`),0) AS `sum_received_hours`
                            FROM `ptn_bookings_calendar_raw` GROUP BY(`application_id`)
                        )
                    AS booking_received ON booking_received.application_id = app.id
                    WHERE app.season_id = $latestSeasonID";
                    //WHERE app.season_id IN ($seasonsIdsArr)";
            $applications_data = $wpdb->get_results($queryApplications);
            $applications_data = json_decode(json_encode($applications_data), true);
        }
    }
?>
<div id="applications" class="applications-container container">
    <div class="row">
        <div class="col-lg-12">
            <br></br>
            
            <form id="applications-ajax-form" method="post" action="#">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="season_number">Velg sesong:</label>
                                <select class="form-control" name="season_number" id="season_number">
                                    <?php
                                        foreach($seasons_data_dropdown as $season){ ?>
                                            <option value="<?= $season['id'] ?>"><?= $season['name'] ?> (<?= $season['user_group']?>)</option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="admin_url" id="admin_url" value="<?= admin_url('admin-ajax.php'); ?>">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <input id="application-submit-btn" type="submit" class="btn btn-primary btn-block" value="Bruk">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="search">Søk</label>
                                <input type="text" class="form-input" id="applications-datatable-search" class="applications-datatable-search">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div id="applications-table-block">
                <table id="applications-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Søker</th>
                            <th>Email</th>
                            <th>Tlf</th>
                            <th>Lag</th>
                            <th>Idrett</th>
                            <th>Medlemmer</th>
                            <th>Alder</th>
                            <th>Type søker</th>
                            <th>Nivå</th>
                            <th>Poeng</th>
                            <th>Ønsket timer</th>
                            <th>Forslag fra algoritme</th>
                            <th>Tildelte timer</th>
                            <th>Ønsket lokasjon 1</th>
                            <th>Ønsket lokasjon 2</th>
                            <th>Ønsket lokasjon 3</th>
                        </tr>
                    </thead>
                    <tbody id="applications-table-body">
                        <?php foreach($applications_data as $data){ 
                                $sql = "select id,date_start,date_end from $bookings_calendar_raw_table WHERE `fixed` = 0 AND application_id=".$data->app_id;

                                $bk_data = $wpdb->get_results($sql);
                 

                                $sum_desired_hours = "";
                                foreach ($bk_data as $key => $bk_da) {

                                    $date_start = $bk_da->date_start; 
                                    $date_end = $bk_da->date_end; 
                                    /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                                    $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                                    $datetime1 = new DateTime($date_start);
                                    $datetime2 = new DateTime($date_end);

                                    $interval = $datetime1->diff($datetime2);
                                    if($interval->format('%h') < 10){
                                        $hour = "0".$interval->format('%h');
                                    }else{
                                        $hour = (int) $interval->format('%h');
                                    }
                                    if($interval->format('%i') < 10){
                                        $minute = "0".$interval->format('%i');
                                    }else{
                                        $minute = (int) $interval->format('%i');
                                    }
                                    $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                                    if($sum_desired_hours != ""){
                                      $time_c = explode(":", $sum_desired_hours);  

                                      $sum_desired_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                                   }else{

                                      $sum_desired_hours = date("H:i",strtotime($dattee)); 
                                   }


                                }
                                if($sum_desired_hours == "" || $sum_desired_hours == "00:00"){
                                    $sum_desired_hours = 0;
                                }else{
                                    $detec = explode(":", $sum_desired_hours);

                                    $dddd = array("01","02","03","04","05","06","07","08","09");
                                    if(in_array($detec[0], $dddd)){
                                        $detec[0] = str_replace("0", "", $detec[0]);
                                    }

                                    $org_d = $detec[0].",".$detec[1]/60; 
                                    $sum_desired_hours = str_replace("0.","",$org_d);
                                    $sum_desired_hours = str_replace(",0","",$sum_desired_hours);
                                }
                                
                                $sql2 = "select id,date_start,date_end from $bookings_calendar_raw_approved_table WHERE  application_id=".$data->app_id;

                                $bk_data2 = $wpdb->get_results($sql2);


                                $sum_received_hours = "";


                                foreach ($bk_data2 as $key => $bk_da2) {
                                    
                                    $date_start = $bk_da2->date_start;
                                    $date_end = $bk_da2->date_end; 
                                    /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                                    $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                                    $datetime1 = new DateTime($date_start);
                                    $datetime2 = new DateTime($date_end);
                                    $interval = $datetime1->diff($datetime2);
                                    if($interval->format('%h') < 10){
                                        $hour = "0".$interval->format('%h');
                                    }else{
                                        $hour = (int) $interval->format('%h');
                                    }
                                    if($interval->format('%i') < 10){
                                        $minute = "0".$interval->format('%i');
                                    }else{
                                        $minute = (int) $interval->format('%i');
                                    }
                                    $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                                    if($sum_received_hours != ""){
                                      $time_c = explode(":", $sum_received_hours);  

                                      $sum_received_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                                   }else{

                                      $sum_received_hours = date("H:i",strtotime($dattee)); 
                                   }
                                }
                                if($sum_received_hours == "" || $sum_received_hours == "00:00"){
                                    $sum_received_hours = 0;
                                }else{
                                    $detec = explode(":", $sum_received_hours);
                                    $dddd = array("01","02","03","04","05","06","07","08","09");
                                    if(in_array($detec[0], $dddd)){
                                        $detec[0] = str_replace("0", "", $detec[0]);
                                    }

                                    $org_d = $detec[0].",".$detec[1]/60; 
                                    $sum_received_hours = str_replace("0.","",$org_d);
                                    $sum_received_hours = str_replace(",0","",$sum_received_hours);

                                }


                                $sql3 = "select id,date_start,date_end from $bookings_calendar_raw_algorithm_table WHERE  application_id=".$data->app_id;

                                $bk_data3 = $wpdb->get_results($sql3);


                                $sum_algo_hours = "";


                                foreach ($bk_data3 as $key => $bk_da3) {
                                    
                                    $date_start = $bk_da3->date_start;
                                    $date_end = $bk_da3->date_end; 
                                    /*$hour_start = date("H:i",strtotime($bk_da->date_start));
                                    $hour_end = date("H:i",strtotime($bk_da->date_end));*/
                                    $datetime1 = new DateTime($date_start);
                                    $datetime2 = new DateTime($date_end);
                                    $interval = $datetime1->diff($datetime2);
                                    if($interval->format('%h') < 10){
                                        $hour = "0".$interval->format('%h');
                                    }else{
                                        $hour = (int) $interval->format('%h');
                                    }
                                    if($interval->format('%i') < 10){
                                        $minute = "0".$interval->format('%i');
                                    }else{
                                        $minute = (int) $interval->format('%i');
                                    }
                                    $dattee = date("Y-m-d ".$hour.":".$minute.":00"); 

                                    if($sum_algo_hours != ""){
                                      $time_c = explode(":", $sum_algo_hours);  

                                      $sum_algo_hours = date("H:i",strtotime('+'.$time_c[0].' hour +'.$time_c[1].' minutes',strtotime($dattee))); 
                                   }else{

                                      $sum_algo_hours = date("H:i",strtotime($dattee)); 
                                   }
                                }
                                if($sum_algo_hours == "" || $sum_algo_hours == "00:00"){
                                    $sum_algo_hours = 0;
                                }else{
                                    $detec = explode(":", $sum_algo_hours);
                                    $dddd = array("01","02","03","04","05","06","07","08","09");
                                    if(in_array($detec[0], $dddd)){
                                        $detec[0] = str_replace("0", "", $detec[0]);
                                    }

                                    $org_d = $detec[0].",".$detec[1]/60; 
                                    $sum_algo_hours = str_replace("0.","",$org_d);
                                    $sum_algo_hours = str_replace(",0","",$sum_algo_hours);
                                }
                            ?>
                            <tr>
                                <td><?= wp_kses_post($data['app_user']);?></td>
                                <td><?= wp_kses_post($data['team_name']);?></td>
                                <td><?= wp_kses_post($data['sport_name']);?></td>
                                <td><?= wp_kses_post($data['members']);?></td>
                                <td><?= wp_kses_post($data['age_group_name']);?></td>
                                <td><?= wp_kses_post($data['type']);?></td>
                                <td><?= wp_kses_post($data['team_level']);?></td>
                                <td><?= wp_kses_post($data['score']);?></td>
                                <td><?= wp_kses_post($sum_desired_hours);?></td>
                                <td><?= wp_kses_post($sum_algo_hours);?></td>
                                <td><?= wp_kses_post($sum_received_hours);?></td>
                                <td><?= wp_kses_post($data['preferred_listing1_title']);?></td>
                                <td><?= wp_kses_post($data['preferred_listing2_title']);?></td>
                                <td><?= wp_kses_post($data['preferred_listing3_title']);?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Søker</th>
                            <th>Email</th>
                            <th>Tlf</th>
                            <th>Lag</th>
                            <th>Idrett</th>
                            <th>Medlemmer</th>
                            <th>Alder</th>
                            <th>Type søker</th>
                            <th>Nivå</th>
                            <th>Poeng</th>
                            <th>Ønsket timer</th>
                            <th>Forslag fra algoritme</th>
                            <th>Tildelte timer</th>
                            <th>Ønsket lokasjon 1</th>
                            <th>Ønsket lokasjon 2</th>
                            <th>Ønsket lokasjon 3</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var applicationsTable = jQuery('#applications-table').DataTable({
            "sDom":"ltipr",
            "bLengthChange": false,
            language: {
              'paginate': {
                'previous': 'Tidligere',
                'next': 'Neste'
              },
              "info": "Viser _START_ til _END_ av _TOTAL_ søknader",
              "infoEmpty": "Viser 0 til 0 av 0 søknader",
              "emptyTable": "Ingen data tilgjengelig i tabellen",
              "zeroRecords": "Ingen samsvarende poster funnet"
            }
        });
        jQuery('#applications-ajax-form #applications-datatable-search').keyup(function(){
            jQuery('#applications-table').DataTable().search(jQuery(this).val()).draw() ;
        });
    });
</script>

<?php

get_footer();