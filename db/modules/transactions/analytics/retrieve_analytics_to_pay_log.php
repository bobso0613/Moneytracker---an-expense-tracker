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


	$larr_IncomeWithBalance = array();


	// CREDIT CARD / INCOME ACCOUNTS WITH NEGATIVE VALUE BALANCES

	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.money_trail_type_mst_code, mstmoneytrailtype.money_trail_name as 'description' ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";

	$lch_Query = $lch_Query . " trnmoneytrail.trail_type = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"], $larr_IncomeWithBalance)) {
			$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]] = array("description"=>$larr_ResultRow_TRNMONEYTRAIL["description"],
				"amount"=>0.00);

		}

		$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]]["amount"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) );
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.account_money_trail_type_mst_code, mstmoneytrailtype.money_trail_name as 'description' ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.account_money_trail_type_mst_code != 0 ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.account_money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {

		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"], $larr_IncomeWithBalance)) {
			$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]] = array("description"=>$larr_ResultRow_TRNMONEYTRAIL["description"],
				"amount"=>0.00);

		}

		$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]]["amount"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) * -1);
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	// all trail started on april 23 2019 only
	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.account_money_trail_type_mst_code, mstmoneytrailtype.money_trail_name as 'description' ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.account_money_trail_type_mst_code != 0 ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_active = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_at >= '2019-04-23 00:00:00' ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.account_money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.account_money_trail_type_mst_code ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"], $larr_IncomeWithBalance)) {
			$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]] = array("description"=>$larr_ResultRow_TRNMONEYTRAIL["description"] . " - " . date("F Y"),
				"amount"=>0.00);

		}

		$larr_IncomeWithBalance[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]]["amount"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) * -1);
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {

	if (count($larr_IncomeWithBalance)>0) {
		foreach ($larr_IncomeWithBalance as $lch_Key => $larr_Value) {
			if (floatval($larr_Value["amount"])<0.00) {
				$larr_outputArray[] =  array("to_pay"=>(abs(floatval($larr_Value["amount"]))),"description"=>$larr_Value["description"] . " - " . date("F Y")); 
			} // if (floatval($larr_Value["amount"])<0.00) {
			
		} // foreach ($larr_IncomeWithBalance as $lch_Key => $larr_Value) {
	} // if (count($larr_IncomeWithBalance)>0) {

	// END - CREDIT CARD / INCOME ACCOUNTS WITH NEGATIVE VALUE BALANCES

	// MAIN EXPENSES PART
	$lch_Query = "SELECT trnmoneytrail.total_amount, trnmoneytrail.description ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '0' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.total_amount > 0.00 ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date ASC, trnmoneytrail.total_amount DESC ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[] =  array("to_pay"=>(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"])),"description"=>$larr_ResultRow_TRNMONEYTRAIL["description"]); 
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