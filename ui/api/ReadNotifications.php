<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$link = DB_LOCATION;
	$params = array (
		"action" => $_POST["action"],
		"fileToOpen" => "read_notifications",
		//"tableName" => "mstuser",
		"dbconnect" => MONEYTRACKER_DB,
		"last_notification_code" => $_POST["last_notification_code"],
		"user_code" => $_POST["user_code"],
		"notif_code" => $_POST["notif_code"]

		//"type="+type+"&receiver="+receiver+"&sender="+sender+"&actualVal="+actualVal
		//"columns" => "username,code,user_image_code,first_name,middle_name,last_name,user_group_mst_codes",
		//"conditions[not_equals][code]" => $_SESSION["user_code"],
		//"conditions[equals][is_active]" => "1"
	);

	$result=processCurl($link,$params);
	$output = json_decode($result,true);
	//if($output[0]["result"]==='1'){


	echo $result;
}


?>