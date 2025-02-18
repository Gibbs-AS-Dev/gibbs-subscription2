<?php
include('../../../../wp-load.php');
//print_r($_POST);
$fname = $_POST['firstname'];
$lname = $_POST['lastname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$bill = $_POST['billing_address_1'];
$bilcity = $_POST['billing_city'];
$billpcode = $_POST['billing_postcode'];
$uid = $_POST['uid'];

update_user_meta($uid,'billing_address_1',$bill);
update_user_meta($uid,'billing_phone',$phone);
update_user_meta($uid,'phone',$phone);
update_user_meta($uid,'billing_city',$bilcity);
update_user_meta($uid,'billing_postcode',$billpcode);