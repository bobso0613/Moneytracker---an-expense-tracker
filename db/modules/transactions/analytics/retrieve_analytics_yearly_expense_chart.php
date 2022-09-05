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

	$larr_Legends = array("Expenses For the month");

	$larr_Data = array("1"=>0,
					"2"=>0,
					"3"=>0,
					"4"=>0,
					"5"=>0,
					"6"=>0,
					"7"=>0,
					"8"=>0,
					"9"=>0,
					"10"=>0,
					"11"=>0,
					"12"=>0);

	$larr_MoneyTrail = array();

	$lch_Query = "SELECT mstmoneytrailtype.short_name, mstmoneytrailtype.code, mstmoneytrailtype.money_trail_name FROM mstmoneytrailtype WHERE ";
	$lch_Query = $lch_Query . " mstmoneytrailtype.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " ORDER BY mstmoneytrailtype.code ASC ";
	$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		$larr_Legends[] = $larr_ResultRow["money_trail_name"];

		$larr_MoneyTrail[$larr_ResultRow["code"]] = array("1"=>0,
					"2"=>0,
					"3"=>0,
					"4"=>0,
					"5"=>0,
					"6"=>0,
					"7"=>0,
					"8"=>0,
					"9"=>0,
					"10"=>0,
					"11"=>0,
					"12"=>0);

	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {




	$lch_Query = "SELECT SUM(trnmoneytrail.total_amount) AS 'total_amount', trnmoneytrail.money_trail_type_mst_code, MONTH(trnmoneytrail.money_trail_date) as 'created_month'   ";
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.trail_type = '1' ";
	$lch_Query = $lch_Query . " AND YEAR(trnmoneytrail.money_trail_date) = '$year_today' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " GROUP BY MONTH(trnmoneytrail.money_trail_date), trnmoneytrail.money_trail_type_mst_code ";
	$lch_Query = $lch_Query . " ORDER BY MONTH(trnmoneytrail.money_trail_date), trnmoneytrail.money_trail_type_mst_code ASC ";
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
		//$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]]["running_balance"] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]) * -1);

		$larr_Data[intval($larr_ResultRow_TRNMONEYTRAIL["created_month"])] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]));

		$larr_MoneyTrail[$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]][intval($larr_ResultRow_TRNMONEYTRAIL["created_month"])] += (floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]));

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	$larr_outputArray ["series"][] = array("name"=>$larr_Legends[0],"type"=>"line",  "sampling"=>"average", "itemStyle" => array("normal"=> "{color:'#04a8f4'}") , "areaStyle" => array("normal"=> "{color:'#04a8f4'}"),"smooth"=>true,"data"=>$larr_Data);

	$lin_ctr = 1;
	foreach ($larr_MoneyTrail as $lch_MoneyType => $larr_MoneyTypeValue) {
		$larr_outputArray ["series"][] = array("name"=>$larr_Legends[$lin_ctr],"type"=>"bar","stack"=>"x",  "data"=>$larr_MoneyTypeValue);
		$lin_ctr++;

	} // foreach ($larr_MoneyTrail as $lch_MoneyType => $larr_MoneyTypeValue) {



	



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