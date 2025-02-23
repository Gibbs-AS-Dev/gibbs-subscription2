<?php
   // Load WordPress core.
   session_start();
   require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
   $error_message = "";
   $success_message = "";

   if(isset($_POST['action']) && $_POST['action'] == "getEmailData"){

      $user = get_user_by("email",$_POST['email']);

      $data = array();

      if(isset($user->ID)){

         $data["email"] = $user->user_email;
         $data["phone"] = $user->phone;
         $data["name"] = $user->display_name;
         $data["address"] = $user->address;
         $data["zipcode"] = $user->zipcode;

      }

      wp_send_json($data);

      exit;


     

   }

   if(isset($_POST['send_request'])){
      global $wpdb;
      $user = get_user_by("email",$_POST['email']);

      $name = $_POST['name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];
      $address = $_POST['address'];
      $zipcode = $_POST['zipcode'];

      if(!$user){
         $username = $_POST['email'];
         $password = $_POST['email'];
         

         $user_data = array(
           'user_login' => $username,
           'user_email' => $email,
           'user_pass'  => $password,
           'display_name' => $name // Change the role as needed
         );

          // Insert the user into the database
          $user_id = wp_insert_user($user_data);

          if (!is_wp_error($user_id)) {
              update_user_meta($user_id, 'phone', $phone);
              update_user_meta($user_id, 'address', $address);
              update_user_meta($user_id, 'zipcode', $zipcode);
              $user = get_user_by("id",$user_id);
          } else {
              $error_message = $user_id->get_error_message();
          }
      }

      if(isset($user->ID)){
          $table_subscription_manual_requests = "subscription_manual_requests";

          if($user->phone != ""){
            $phone =  $user->phone;
          }

          $request_data = array(
              'user_id' => $user->ID,
              'name' => $name,
              'email' => $email,
              'phone' => $phone,
              'location' => $_POST['location'],
              'from_date' => $_POST['from_date'],
              'size' => $_POST['size'],
              'comment' => $_POST['comment'],
          );

          // Insert user data into the users table
          $insert_request = $wpdb->insert($table_subscription_manual_requests, $request_data);

          if ($insert_request) {
              $success_message =  'Data insert successfully';
          } else {
              $error_message = 'Error creating request.';
          }
      }
   }   
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Gibbs abonnement</title>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
      <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css" />
      <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css" />
      <link rel="stylesheet" type="text/css" href="/subscription/css/common.css" />
      <link rel="stylesheet" type="text/css" href="/subscription/pages/css/style.css" />
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
      <script type="text/javascript" src="/subscription/js/common.js"></script>
   </head>
   <body>
      <?php if($success_message != ""){ ?>
         <div class="success-message">
             <p><?php echo $success_message;?></p>
         </div>
      <?php } ?>
      <?php if($error_message != ""){ ?>
         <div class="error-message">
             <p><?php echo $error_message;?></p>
         </div>
      <?php } ?>
      <div class="content-area">
         <h1>Manual booking</h1>
      </div>
      <form method="post" action="">
         <div class="content-main">
            <div class=" content-inner">
               <div class="form-card card shadow-sm mb-5">
                  <div class="content-form">
                     <h3>Contact information</h3>
                     <div class="form-field">
                        <label for="emailInput" class="required">Email</label>
                        <div class="inputcontainer">
                           <input type="email" id="emailInput" name="email" placeholder="" required>
                           <div class="icon-container loader-div" style="display: none;">
                              <i class="loader"></i>
                           </div>
                        </div>
                        
                     </div>
                     <div class="form-field">
                        <label for="nameInput" class="required">Name</label>
                        <div class="inputcontainer">
                           <input type="text" id="nameInput" name="name" placeholder="" required>
                           <div class="icon-container loader-div" style="display: none;">
                              <i class="loader"></i>
                           </div>
                        </div>
                     </div>
                     <div class="form-field">
                        <label for="phoneInput"class="required">Phone</label>
                        
                        <div class="inputcontainer">
                           <input type="tel" id="phoneInput" name="phone" placeholder="" required>
                           <div class="icon-container loader-div" style="display: none;">
                              <i class="loader"></i>
                           </div>
                        </div>
                     </div>
                     <div class="form-field">
                        <label for="addressInput"class="required">Address</label>
                        
                        <div class="inputcontainer">
                           <input type="text" id="addressInput" name="address" placeholder="" required>
                           <div class="icon-container loader-div" style="display: none;">
                              <i class="loader"></i>
                           </div>
                        </div>
                     </div>
                     <div class="form-field">
                        <label for="zipcodeInput"class="required">Zipcode</label>
                        
                        <div class="inputcontainer">
                           <input type="number" id="zipcodeInput" name="zipcode" placeholder="" required>
                           <div class="icon-container loader-div" style="display: none;">
                              <i class="loader"></i>
                           </div>
                        </div>
                     </div>
                    
                  </div>
               </div>
               <div class="form-card card shadow-sm mb-5 ">
                  <div class="content-form">
                     <h3>Storage needs</h3>
                      <div class="form-field">
                     
                     <label for="location">Location <i class="icon fas fa-map-marker-alt"></i></label>
                     <input type="tel" id="location" name="location" placeholder="">
                   </div>
                     <div class="form-field">
                        <label for="sizeSelect" class="required">Size</label>
                        <div class="custom-select-wrapper">
                           <select id="sizeSelect" name="size" class="custom-select " required>
                              <option value="small">Small</option>
                              <option value="medium">Medium</option>
                              <option value="large">Large</option>
                           </select>
                           <img src="https://cdn-icons-png.flaticon.com/512/2722/2722987.png" alt="Icon" class="custom-icon">
                        </div>
                     </div>
                     <div class="form-field">
                        <label for="fromDateInput" class="required">From date</label>
                        <input type="date" id="fromDateInput" name="from_date" required>
                     </div>
                     <div class="form-field">
                        <label for="commentTextarea">Comment</label>
                        <textarea id="commentTextarea" name="comment" rows="4" cols="50"placeholder=""></textarea>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="center-btn">
            <input type="hidden" name="send_request" value="send">
            <button type="submit" >Send request</button>
         </div>
      </form>
      <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
      <script type="text/javascript">
         jQuery("#emailInput").change(function(){
            var email = this.value;
            jQuery(".loader-div").show();
            jQuery.ajax({
               url: "/subscription/pages/manual-booking.php",
               cache: false,
               type: "post",
               dataType: "json",
               data: {email: email, 'action': 'getEmailData'},
               success: function(response){
                  jQuery(".loader-div").hide();

                  if(response && response.email){
                     jQuery("#nameInput").val(response.name)
                     jQuery("#phoneInput").val(response.phone)
                     jQuery("#addressInput").val(response.address)
                     jQuery("#zipcodeInput").val(response.zipcode)
                  }else{
                     jQuery("#nameInput").val("");
                     jQuery("#phoneInput").val("");
                     jQuery("#addressInput").val("");
                     jQuery("#zipcodeInput").val("");
                  }
                
               }
            });
         })
      </script>
   </body>
</html>