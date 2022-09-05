<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("../SystemConstants.php");
require_once("../CurlAPI.php");
date_default_timezone_set('Asia/Manila');

/*
PARAMETERS:
array(75) {
  ["intermediary_rate-1-1-1-1-1-1"]=>
  string(5) "5.256"
  ["company_mst_code"]=>
  string(1) "1"
  ["branch_mst_code"]=>
  string(1) "1"
  ["line_mst_code"]=>
  string(1) "1"
  ["agent_mst_code"]=>
  string(1) "1"
}
*/

//$_SESSION["user_code"]

$larr_OutputArray = array();
$larr_ResultQueryArray = array();

// AJAX requests - start the session
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    session_start();
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$link = DB_LOCATION;
	$params = array (
        "dbconnect" => MONEYTRACKER_DB,
        "user_mst_code" => $_SESSION["user_code"],
        "fileToOpen" => "masterfiles/save_mstuserprivilege"
    );
    foreach ($_POST as $lch_key => $lch_value){
    	$params[$lch_key] = $lch_value;
    } // foreach ($_POST as $lch_key => $lch_value){

    $result=processCurl($link,$params);
    $retrievedRecordRow = json_decode($result,true);
    if (count($retrievedRecordRow)>0){

      	$larr_ResultQueryArray = array(
    			"result" => $retrievedRecordRow[0]["result"],
    			"error_message" => $retrievedRecordRow[0]["error_message"]
    		);
  		array_push($larr_OutputArray,$larr_ResultQueryArray);

      // AUDIT TRAIL PART
      $link = DB_LOCATION;
      $params = array (
          "action" => "can_edit",
          "fileToOpen" => "save_audit_trail",
          "tableName" => "trnaudittrail",
          "dbconnect" => MONEYTRACKER_DB,
          "module_mst_code" => $_POST["modulemstcode"],
          "menu_item_mst_code" => $_POST["menuitemmstcode"],
          "user_mst_code" => $_SESSION["user_code"],
          "reference" => $_POST["type_code_desc"],
          "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
      );
      $result=processCurl($link,$params);
      // no need to parse the result. error man o hindi
      // end - AUDIT TRAIL PART

    } // if (count($retrievedRecordRow)>0){
    //echo $result;
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	$larr_ResultQueryArray = array(
		"result" => "0",
		"error_message" => "Invalid Request Type."
		
	);
	array_push($larr_OutputArray,$larr_ResultQueryArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {

echo json_encode( $larr_OutputArray);




?>