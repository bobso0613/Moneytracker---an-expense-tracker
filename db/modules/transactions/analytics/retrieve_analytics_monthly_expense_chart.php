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
	$month_today = date("n");
	$year_today = date("Y");

	// $lch_dbAccess = new DatabaseAccess();
	// $lch_dbAccess->connectDB("iisaac-insurance");
	// $lch_Query = "SELECT trnaudittrail.reference, trnaudittrail.module_action_mst_code, trnaudittrail.module_mst_code, trnaudittrail.created_at, mstmodule.module_name FROM trnaudittrail,mstmodule WHERE ";
	// $lch_Query = $lch_Query . " trnaudittrail.action_user_mst_code = '$user_code' ";
	// $lch_Query = $lch_Query . " AND mstmodule.code = trnaudittrail.module_mst_code ";
	// $lch_Query = $lch_Query . " ORDER BY trnaudittrail.created_at DESC ";
	// $SQL_RESULT_TRNAUDITTRAIL=mysql_query($lch_Query) or die(mysql_error());
	// while($larr_ResultRow_TRNAUDITTRAIL=mysql_fetch_array($SQL_RESULT_TRNAUDITTRAIL)) {
		
	// } // while($larr_ResultRow_TRNAUDITTRAIL=mysql_fetch_array($SQL_RESULT_TRNAUDITTRAIL)) {

	$larr_Months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

	$larr_PerPolicyType = array();

	$larr_outputArray ["result"] = 1;
	$larr_outputArray ["series"] = array();

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);

	$larr_Legends = array();


	$larr_MoneyTrail = array();

	$lch_Query = "SELECT mstmoneytrailtype.short_name, mstmoneytrailtype.code, mstmoneytrailtype.money_trail_name FROM mstmoneytrailtype WHERE ";
	$lch_Query = $lch_Query . " mstmoneytrailtype.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " ORDER BY mstmoneytrailtype.code ASC ";
	$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		$larr_Legends[] = $larr_ResultRow["money_trail_name"];

		$larr_MoneyTrail[$larr_ResultRow["code"]] = array("value"=>0.00,"name"=>$larr_ResultRow["money_trail_name"]);

	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {




	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.money_trail_type_mst_code, MONTH(trnmoneytrail.money_trail_date) as 'created_month'   ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND YEAR(trnmoneytrail.money_trail_date) = '$year_today' ";
	$lch_Query = $lch_Query . " AND MONTH(trnmoneytrail.money_trail_date) = '$month_today' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " GROUP BY trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_type_mst_code ASC ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {

		$larr_MoneyTrail[$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]]["value"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]));

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	$larr_outputArray["series"] = $larr_MoneyTrail;
	$larr_outputArray["legends"]= $larr_Legends;



	
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