<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: POST-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("../../SystemConstants.php");
require_once("../../CurlAPI.php");
date_default_timezone_set('Asia/Manila');


$larr_OutputArray = array();
$larr_ResultQueryArray = array();

// AJAX requests - start the session
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    session_start();
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
//session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$link = DB_LOCATION;

    // saving here depending on transactionmode
    $lch_action = "";
    switch ($_POST["transactionmode"]){

	    // ADD INTERIM PART
	    case "add_interim":
	    	$params = array (
	          "user_code" => $_SESSION["user_code"],
	          "fileToOpen" => "transactions/main/save_mstscheduledtransactions"
	      );
	      foreach ($_POST as $lch_key => $lch_value){
	        $params[$lch_key] = $lch_value;
	      } // foreach ($_POST as $lch_key => $lch_value){

	      $result=processCurl($link,$params);
	      //echo $result;
	      $retrievedRecordRow = json_decode($result,true);

	      if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="1"){

	        $larr_ResultQueryArray = array(
	          "result" => $retrievedRecordRow[0]["result"],
	          "error_message" => $retrievedRecordRow[0]["error_message"]
	        );
	        array_push($larr_OutputArray,$larr_ResultQueryArray);

	        // AUDIT TRAIL PART
	        $link = DB_LOCATION;
	        $params = array (
	            "action" => "can_add",
	            "fileToOpen" => "save_audit_trail",
	            "tableName" => "trnaudittrail",
	            "dbconnect" => MONEYTRACKER_DB,
	            "module_mst_code" => $_POST["modulemstcode"],
	            "menu_item_mst_code" => $_POST["menuitemmstcode"],
	            "user_mst_code" => $_SESSION["user_code"],
	            "reference" => "New Scheduled Transaction - " . $retrievedRecordRow[0]["template_name"],
	            "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
	        );
	        $result=processCurl($link,$params);
	        // no need to parse the result. error man o hindi
	        // end - AUDIT TRAIL PART

	      } // if (count($retrievedRecordRow)>0){
      	  else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
      	  	$larr_ResultQueryArray = array(
	          "result" => $retrievedRecordRow[0]["result"],
	          "error_message" => $retrievedRecordRow[0]["error_message"]
	        );
	        array_push($larr_OutputArray,$larr_ResultQueryArray);
      	  } // else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
	    break;
	    // END - ADD INTERIM PART


	     // EDIT INTERIM PART
	    case "process_interim":
	    	$params = array (
	          "user_code" => $_SESSION["user_code"],
	          "fileToOpen" => "transactions/main/save_mstscheduledtransactions"
	      );
	      foreach ($_POST as $lch_key => $lch_value){
	        $params[$lch_key] = $lch_value;
	      } // foreach ($_POST as $lch_key => $lch_value){

	      $result=processCurl($link,$params);
	      // echo $result;
	      $retrievedRecordRow = json_decode($result,true);

	      if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="1"){

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
	            "reference" => "Edited Scheduled Transaction - " . $retrievedRecordRow[0]["template_name"],
	            "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
	        );
	        $result=processCurl($link,$params);
	        // no need to parse the result. error man o hindi
	        // end - AUDIT TRAIL PART

	      } // if (count($retrievedRecordRow)>0){
	      else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
      	  	$larr_ResultQueryArray = array(
	          "result" => $retrievedRecordRow[0]["result"],
	          "error_message" => $retrievedRecordRow[0]["error_message"]
	        );
	        array_push($larr_OutputArray,$larr_ResultQueryArray);
      	  } // else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
	    break;
	    // END - EDIT INTERIM PART

	    // DELETE INTERIM PART
	    case "delete_interim":

	      $params = array (
	          "user_code" => $_SESSION["user_code"],
	          "fileToOpen" => "transactions/main/save_mstscheduledtransactions"
	      );
	      foreach ($_POST as $lch_key => $lch_value){
	        $params[$lch_key] = $lch_value;
	      } // foreach ($_POST as $lch_key => $lch_value){

	      $result=processCurl($link,$params);
	      // echo $result;
	      $retrievedRecordRow = json_decode($result,true);


	      if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="1"){

	        $larr_ResultQueryArray = array(
	          "result" => $retrievedRecordRow[0]["result"],
	          "error_message" => $retrievedRecordRow[0]["error_message"]
	        );
	        array_push($larr_OutputArray,$larr_ResultQueryArray);

	        // AUDIT TRAIL PART
	        $link = DB_LOCATION;
	        $params = array (
	            "action" => "can_add",
	            "fileToOpen" => "save_audit_trail",
	            "tableName" => "trnaudittrail",
	            "dbconnect" => MONEYTRACKER_DB,
	            "module_mst_code" => $_POST["modulemstcode"],
	            "menu_item_mst_code" => $_POST["menuitemmstcode"],
	            "user_mst_code" => $_SESSION["user_code"],
	            "reference" => "Deleted Scheduled Transaction - " . $retrievedRecordRow[0]["template_name"],
	            "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
	        );
	        $result=processCurl($link,$params);
	        // no need to parse the result. error man o hindi
	        // end - AUDIT TRAIL PART

	      } // if (count($retrievedRecordRow)>0){
	      else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
      	  	$larr_ResultQueryArray = array(
	          "result" => $retrievedRecordRow[0]["result"],
	          "error_message" => $retrievedRecordRow[0]["error_message"]
	        );
	        array_push($larr_OutputArray,$larr_ResultQueryArray);
      	  } // else if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"]=="0"){
	    break;
	    // END - DELETE INTERIM PART
    } // switch ($_POST["transactionmode"]){

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