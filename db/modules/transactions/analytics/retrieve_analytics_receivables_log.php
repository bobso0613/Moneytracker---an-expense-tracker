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

	$larr_outputArray = array();


	$lch_Query = "SELECT trnmoneytrail.total_amount, trnmoneytrail.description ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '0' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.total_amount < 0.00 ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date ASC, trnmoneytrail.total_amount DESC ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[] =  array("to_pay"=>abs(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"])),"description"=>$larr_ResultRow_TRNMONEYTRAIL["description"]); 
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	
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