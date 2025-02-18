<?php



function get_messages($booking_id){

  $referral = "booking_".$booking_id;

  global $wpdb;
  /*$sql = "SELECT * FROM `" . $wpdb->prefix . "listeo_core_conversations` as con WHERE referral='".$referral."' LEFT JOIN `" . $wpdb->prefix . "listeo_core_messages` mes ON con.id = mes.conversation_id order by  created_at";*/

  $sql = "SELECT con.*,mes.* FROM `" . $wpdb->prefix . "listeo_core_messages` as mes  JOIN `" . $wpdb->prefix . "listeo_core_conversations` as con ON con.id = mes.conversation_id WHERE con.referral='".$referral."' order by  created_at";
  $messages  = $wpdb ->get_results($sql);

  //echo "<pre>"; print_r($messages); die;


  
  foreach ($messages as $key => $message) {

     $sql1 = "SELECT * FROM `" . $wpdb->prefix . "users` WHERE ID=".$message->sender_id;

     $user  = $wpdb ->get_row($sql1);

     $message->display_name = "";

     if(isset($user->display_name)){
          $message->display_name = $user->display_name;

     }

  } 
  return $messages;

}
?>