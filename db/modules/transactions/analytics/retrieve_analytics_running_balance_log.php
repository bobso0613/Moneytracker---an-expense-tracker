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

	// APPLICATION PARAMETERS
	$larr_AppParams = array();
	$lch_Query = "SELECT mstapplicationparameter.* FROM mstapplicationparameter WHERE ";
	$lch_Query = $lch_Query . " mstapplicationparameter.parameter_key IN ('income_accounts_mst_codes')";
	$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		$larr_AppParams [$larr_ResultRow["parameter_key"]] = $larr_ResultRow["parameter_value"];
	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

	$lch_Query = "SELECT mstmoneytrailtype.short_name, mstmoneytrailtype.code, mstmoneytrailtype.money_trail_name FROM mstmoneytrailtype WHERE ";
	$lch_Query = $lch_Query . " mstmoneytrailtype.trail_type = '2' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_payable_debt_account != '1' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'ARI' AND mstmoneytrailtype.short_name != 'API') ";
	$lch_Query = $lch_Query . " ORDER BY mstmoneytrailtype.order_no ASC,mstmoneytrailtype.short_name ASC ";
	$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		$larr_outputArray[$larr_ResultRow["code"]] = array("result"=>"1",
									"short_name"=>$larr_ResultRow["short_name"],
									"money_trail_name"=>$larr_ResultRow["money_trail_name"],
									"running_balance"=>0.00,
									"code"=>$larr_ResultRow["code"]);

	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";

	$lch_Query = $lch_Query . " trnmoneytrail.trail_type = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_payable_debt_account != '1' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'ARI' AND mstmoneytrailtype.short_name != 'API') ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]]["running_balance"] += floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]);
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.account_money_trail_type_mst_code != 0 ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_payable_debt_account != '1' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'ARI' AND mstmoneytrailtype.short_name != 'API') ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.account_money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]]["running_balance"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) * -1);
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	// all trail started on april 23 2019 only
	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.account_money_trail_type_mst_code != 0 ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_payable_debt_account != '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_at >= '2019-04-23 00:00:00' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'ARI' AND mstmoneytrailtype.short_name != 'API') ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.account_money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]]["running_balance"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) * -1);
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