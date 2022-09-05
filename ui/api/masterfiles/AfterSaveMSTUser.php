<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("../SystemConstants.php");
require_once("../CurlAPI.php");
date_default_timezone_set('Asia/Manila');

$outputArray = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//if ($_POST["transactionmode"]=="can_edit"){
		// save proper starts here
		$link = DB_LOCATION;
		$params = array (
	        "action" => $_POST["transactionmode"],
	        "tableName" => "mstuser", // $_POST["tableName"] -- to update vehicle details description
	        "dbconnect" => MONEYTRACKER_DB,
	        "userid_loggedin" => $_COOKIE["user_code"],
	        "fileToOpen" => "masterfiles/aftersave_mstuser",
	        "codeToUpdate" =>(isset($_POST["last_code"])&&$_POST["last_code"]!=""&&$_POST["last_code"]!="0") ? $_POST["last_code"] : $_POST["primarycodevalue"]
	    );

	    $result=processCurl($link,$params);
	    $retrievedRecordRow = json_decode($result,true);
	    if (count($retrievedRecordRow)>0){
	    	$arr = array(
				"result" => $retrievedRecordRow[0]["result"],
				"error_message" => $retrievedRecordRow[0]["error_message"]
			);
			array_push($outputArray,$arr);
	    } // if (count($retrievedRecordRow)>0){
		// save proper ends here
	//} // if ($_POST["transactionmode"]=="can_edit"){

} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	$arr = array(
		"result" => "0",
		"error_message" => "Invalid Request Type."
	);
	array_push($outputArray,$arr);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {

echo json_encode( $outputArray);

?>