<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("../SystemConstants.php");
require_once("../CurlAPI.php");
date_default_timezone_set('Asia/Manila');


$larr_OutputArray = array();
$larr_ResultQueryArray = array();

// AJAX requests - start the session
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    session_start();
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


	// validate server side
	if (!isset($_POST["mstuser_first_name"]) || @$_POST["mstuser_first_name"]=="") {
    	$larr_ResultQueryArray = array(
    		"result" => "0",
    		"error_message" => "First Name should not be blank."
		);
    	array_push($larr_OutputArray,$larr_ResultQueryArray);
    } // if (!isset($_POST["mstuser_first_name"]) || @$_POST["mstuser_first_name"]=="") {

	if (!isset($_POST["mstuser_last_name"]) || @$_POST["mstuser_last_name"]=="") {
    	$larr_ResultQueryArray = array(
    		"result" => "0",
    		"error_message" => "Last Name should not be blank."
		);
    	array_push($larr_OutputArray,$larr_ResultQueryArray);
    } // if (!isset($_POST["mstuser_last_name"]) || @$_POST["mstuser_last_name"]=="") {


	if (@$_POST["mstuser_current_password"]!=""||
		@$_POST["mstuser_new_password"]!=""||
		@$_POST["mstuser_retype_new_password"]!="") {

		if (!isset($_POST["mstuser_current_password"]) || @$_POST["mstuser_current_password"]=="") {
	    	$larr_ResultQueryArray = array(
	    		"result" => "0",
	    		"error_message" => "Current Password should not be blank."
			);
	    	array_push($larr_OutputArray,$larr_ResultQueryArray);
	    } // if (!isset($_POST["mstuser_current_password"]) || @$_POST["mstuser_current_password"]=="") {


    	if (!isset($_POST["mstuser_new_password"]) || @$_POST["mstuser_new_password"]=="") {
	    	$larr_ResultQueryArray = array(
	    		"result" => "0",
	    		"error_message" => "Current Password should not be blank."
			);
	    	array_push($larr_OutputArray,$larr_ResultQueryArray);
	    } // if (!isset($_POST["mstuser_new_password"]) || @$_POST["mstuser_new_password"]=="") {


    	if (!isset($_POST["mstuser_retype_new_password"]) || @$_POST["mstuser_retype_new_password"]=="") {
	    	$larr_ResultQueryArray = array(
	    		"result" => "0",
	    		"error_message" => "Retype your new password."
			);
	    	array_push($larr_OutputArray,$larr_ResultQueryArray);
	    } // if (!isset($_POST["mstuser_retype_new_password"]) || @$_POST["mstuser_retype_new_password"]=="") {


    	if ((@$_POST["mstuser_new_password"]!=""||
			@$_POST["mstuser_retype_new_password"]!="")&&
			@$_POST["mstuser_new_password"]!=@$_POST["mstuser_retype_new_password"]) {

    		$larr_ResultQueryArray = array(
	    		"result" => "0",
	    		"error_message" => "New password and the retyped password do not match."
			);
	    	array_push($larr_OutputArray,$larr_ResultQueryArray);

    	} /* if ((@$_POST["mstuser_new_password"]!=""||
			@$_POST["mstuser_retype_new_password"]!="")&&
			@$_POST["mstuser_new_password"]!=@$_POST["mstuser_retype_new_password"]) { */



	} /* if (@$_POST["mstuser_current_password"]!=""||
		@$_POST["mstuser_new_password"]!=""||
		@$_POST["mstuser_retype_new_password"]!="") { */



	if (count($larr_OutputArray)<=0){

		// call save in db
		$link = DB_LOCATION;
		$params = array (
	        "user_code" => $_SESSION["user_code"],
	        "fileToOpen" => "masterfiles/save_mstusersettings"
	    );
	    foreach ($_POST as $lch_key => $lch_value){
	    	$params[$lch_key] = $lch_value;
	    } // foreach ($_POST as $lch_key => $lch_value){

	    $result=processCurl($link,$params);
	    //$result = "";
	    //echo $result;
	    $retrievedRecordRow = json_decode($result,true);
	    if (count($retrievedRecordRow)>0){

	      	//$larr_ResultQueryArray = $retrievedRecordRow;
	  		//array_push($larr_OutputArray,$retrievedRecordRow);
	  		$larr_OutputArray = $retrievedRecordRow;

	  		if ($retrievedRecordRow[0]["result"]=="1"){

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
		          "reference" => $_POST["valueviewed"],
		          "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
		      	);
		      	$result=processCurl($link,$params);
			    // no need to parse the result. error man o hindi
			    // end - AUDIT TRAIL PART

	  		} // if ($retrievedRecordRow[0]["result"]=="1"){

	    } // if (count($retrievedRecordRow)>0){

  	} // if (count($larr_OutputArray)<=0){
	
  
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