
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/order_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/user_data_manager.php';

$Order_Data_Manager = new Order_Data_Manager(null);
$User_Data_Manager = new User_Data_Manager;

$order_data = $Order_Data_Manager->get_orders();

foreach($order_data as $order_d){
    $order = $Order_Data_Manager->read_order($order_d["ID"]);

    $role = $User_Data_Manager->get_role($order_d["order_owner"]);

    echo "<pre>";print_r($role); die;
}

echo "<pre>";print_r($order_data); die;