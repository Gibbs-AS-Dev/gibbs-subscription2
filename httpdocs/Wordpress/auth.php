<?php 

$home_url =  (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
if(isset($_GET["code"])){
 
   // $code = $_GET["code"];
   $client_id = 'urn:my:application:identifier:8382';
   $redirect_uri = $home_url.'/auth.php';
   $client_secret = '/yzeiTsOzJZyrsOo7+Apv43amf3bDEltW5jUixNnbNM=';
   $code = $_GET['code'];
   $state = $_GET['state'];
   
   $ch = curl_init();

   $headr = array();
    $headr[] = 'Content-length: 0';
    $headr[] = 'Content-type: application/x-www-form-urlencoded';
    $headr[] = 'Basic BASE64(xWwwFormUrlEncode('.$client_id.'):xWwwFormUrlEncode('.$client_secret.'))';
   
  // curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
   curl_setopt($ch, CURLOPT_URL, "https://gibbs.criipto.id/oauth2/token");
   curl_setopt($ch, CURLOPT_POST, TRUE);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
       'grant_type' => 'authorization_code',
       'code' => $code,
       'client_id' => $client_id,
       'client_secret' => $client_secret,
       'redirect_uri' => $redirect_uri,
       'scope' => 'email',
       'state' => $state,
   )));
   
   $response = curl_exec($ch);

  //  $response = '{"token_type":"Bearer","expires_in":"120","id_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IkM3RDRCQ0YzN0U5OENBMUUwOTNBQUQ5MDc4OEU4N0JENjVDRTE1MkUifQ.eyJpc3MiOiJodHRwczovL2dpYmJzLmNyaWlwdG8uaWQiLCJhdWQiOiJ1cm46bXk6YXBwbGljYXRpb246aWRlbnRpZmllcjo4MzgyIiwiaWRlbnRpdHlzY2hlbWUiOiJub2JhbmtpZC1vaWRjIiwiYXV0aGVudGljYXRpb250eXBlIjoidXJuOmdybjphdXRobjpubzpiYW5raWQiLCJhdXRoZW50aWNhdGlvbm1ldGhvZCI6InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphYzpjbGFzc2VzOlNvZnR3YXJlUEtJIiwiYXV0aGVudGljYXRpb25pbnN0YW50IjoiMjAyNC0wNy0wOFQxNjo0NDo1MS4xNjNaIiwibmFtZWlkZW50aWZpZXIiOiJhMmQwMjA3YTk4NDg0NTZiOGY4NGI3MGRlYzI0YTU3ZSIsInN1YiI6InthMmQwMjA3YS05ODQ4LTQ1NmItOGY4NC1iNzBkZWMyNGE1N2V9Iiwic2Vzc2lvbmluZGV4IjoiZDljMDRjY2ItMDgwNC00MTY3LTk0OWQtNjI1NGFhYjgxM2YyIiwidW5pcXVldXNlcmlkIjoiOTU3OC01OTk5LTQtMzA1ODkyNCIsImNlcnRpc3N1ZXIiOiJDTj1CYW5rSUQgLSBETkIgLSBCYW5rIENBIDMsT1U9OTg0ODUxMDA2LE89RE5CIEJhbmsgQVNBLEM9Tk87T3JnaW5hdG9ySWQ9NzAwMjtPcmlnaW5hdG9yTmFtZT1ETkI7T3JpZ2luYXRvcklkPTcwMDIiLCJjZXJ0c3ViamVjdCI6IkNOPUdyeWdhXFwsIEthbWlsLE89RE5CIEJhbmsgQVNBLEM9Tk8sU0VSSUFMTlVNQkVSPVVOOk5PLTk1NzgtNTk5OS00LTMwNTg5MjQsU1VSTkFNRT1HcnlnYSxHSVZFTk5BTUU9S2FtaWwiLCJhZGRyZXNzIjpudWxsLCJzdHJlZXRhZGRyZXNzIjpudWxsLCJiaXJ0aGRhdGUiOiIxOTk0LTA1LTIzIiwiZGF0ZW9mYmlydGgiOiIxOTk0LTA1LTIzIiwiZmFtaWx5X25hbWUiOiJHcnlnYSIsInN1cm5hbWUiOiJHcnlnYSIsImdpdmVuX25hbWUiOiJLYW1pbCIsImdpdmVubmFtZSI6IkthbWlsIiwibmFtZSI6IkthbWlsIEdyeWdhIiwiY291bnRyeSI6Ik5PIiwiaWF0IjoxNzIwNDU3MjI4LCJuYmYiOjE3MjA0NTcyMjgsImV4cCI6MTcyMDQ1ODQyOH0.WAaZjDckKKcYynT21ru6h-l4oecAbPme9hUey_XGs4xiBdcXPF5K5C1Fql0l6CmyrSOkmYR8wyIrZzlCJWxWvfrg5eEZgcBwDvBMIpa23Nx6v4VA5Wc5_RuUvl0yLnEzGG9QFsnKFNVfBkvIY6K285AC8kcgjvbc-KfMmMjwzpUXk6-Tvjqz5IP8xZgzo5ymABiHurSRjwqZsUViku5uIQIYWrJRRsFQKP7VJOrcTttYPgI-xtI4Mwaf7XO7WEaODio0RFAbm-0QiztFqTRpXpMxLRnxwdXywquCC8aTNXUiQWnsASsEAswSY7CUyO-9P63irTmjI06s-lK7G0Pr9w","access_token":"e1e7fd85-e61a-413f-a8d7-e0ce5e16b153"}';

   //echo "<pre>"; print_r($response); 

    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        
    } else {
      $response = json_decode($response);

 
        if(isset($response->access_token)){

            $access_token = $response->access_token; 

            ?>
            <form method="post" action="saveverify.php" id="verify">
               <input type="hidden" name="verify_token" value="<?php echo $access_token;?>">
               <input type="hidden" name="redirect_url" value="<?php echo $_GET["redirect_url"];?>">
            </form>
            <script>
              document.getElementById("verify").submit();
            </script>
            <?php
            


          //   // start
          //   $url = 'https://gibbs.criipto.id/oauth2/userinfo'; // Replace with your actual URL

          // $data = array(
          //     'access_token' => $access_token
          // );

          // $ch = curl_init();

          // curl_setopt($ch, CURLOPT_URL, $url);
          // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          // curl_setopt($ch, CURLOPT_POST, true);
          // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

          // $headers = array(
          //     'Accept: application/x-www-form-urlencoded'
          // );

          // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          // $response = curl_exec($ch);
          // if(curl_errno($ch)) {
          //     echo 'Error:' . curl_error($ch);
          // }
          // curl_close($ch);
          //   echo "<pre>"; print_r($response); die("dfjdhf");
            
         
          //    $err = curl_error($ch);
          //    curl_close($ch);

            // end

            
        }
    }
}else if(isset($_GET["redirect"]) && $_GET["redirect"] == true){

  require "wp-load.php";

  session_start();

  $redirect_auth_referer =  $_SERVER['HTTP_REFERER']; 

  $_SESSION['redirect_url_cripto'] =  $redirect_auth_referer;

  $red_uri = $home_url."/auth.php";

   $urll = "https://gibbs.criipto.id/oauth2/authorize?response_type=code&client_id=urn:my:application:identifier:8382&redirect_uri=".$red_uri."&scope=openid&state=etats";
    ?>
    <script>
      window.location.href = "<?php echo $urll;?>";
    </script>
    <?php
}else if(isset($_GET["redirect_url"]) &&  $_GET["redirect_url"] != ""){ 
  $urll =  urldecode($_GET["redirect_url"]);
  ?>
  <script>
    window.location.href = "<?php echo $urll;?>";
  </script>
<?php }else{ ?>
  <script>
    window.location.href = "/";
  </script>
<?php }
?>
