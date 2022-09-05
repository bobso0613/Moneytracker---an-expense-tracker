<?php
header('Content-type: application/json');
date_default_timezone_set('Asia/Manila');
@include_once("../../../api/DatabaseConnect.php");
@include_once("../../../api/SanitizeField.php");

//$lch_dbAccess = new DatabaseAccess();
$larr_outputArray = array();
$larr_ResultQueryArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	$user_code = sanitizeField(@$_POST['user_code']);

	$date_today = date("Y-m-d");

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);
	$lch_Query = "SELECT trnaudittrail.reference, trnaudittrail.module_action_mst_code, trnaudittrail.module_mst_code, trnaudittrail.created_at, mstmodule.module_name FROM trnaudittrail,mstmodule WHERE ";
	$lch_Query = $lch_Query . " trnaudittrail.action_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmodule.code = trnaudittrail.module_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnaudittrail.created_at DESC ";
	$lch_Query = $lch_Query . " LIMIT 0,10 ";
	$SQL_RESULT_TRNAUDITTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNAUDITTRAIL=mysql_fetch_array($SQL_RESULT_TRNAUDITTRAIL)) {
		array_push($larr_outputArray,array("result"=>"1",
									"reference"=>$larr_ResultRow_TRNAUDITTRAIL["reference"],
									"module_mst_code"=>$larr_ResultRow_TRNAUDITTRAIL["module_mst_code"],
									"module_action_mst_code"=>$larr_ResultRow_TRNAUDITTRAIL["module_action_mst_code"],
									"created_at"=>$larr_ResultRow_TRNAUDITTRAIL["created_at"],
									"module_name"=>$larr_ResultRow_TRNAUDITTRAIL["module_name"]));
	} // while($larr_ResultRow_TRNAUDITTRAIL=mysql_fetch_array($SQL_RESULT_TRNAUDITTRAIL)) {


	
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
else {
	// illegal access error
	$larr_ResultQueryArray = array(
		"result" => "0",	
		"error_message" => "Invalid API Access."
	);
	array_push($larr_outputArray,$larr_ResultQueryArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

// $lch_dbAccess->closeCon();
echo json_encode($larr_outputArray);
?>