<?php
header('Content-type: application/json');
date_default_timezone_set('Asia/Manila');
@include_once("../../../api/DatabaseConnect.php");
@include_once("../../../api/SanitizeField.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//$lch_dbAccess = new DatabaseAccess();
$larr_outputArray = array();
$larr_ResultQueryArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	$user_code = sanitizeField(@$_POST['user_code']);

	$date_today = date("Y-m-d");

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);

	$larr_outputArray = array();


	// CONCAT(mstreference.reference_name,' - ', MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')),' ',trnmoneytrail.booking_year) as 'reference_name'

	// from ARI entry - to add
	$lch_Query = "SELECT mstreference.reference_name, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year , SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.booking_year,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.reference_mst_code) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.money_trail_type_mst_code "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'ARI' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " GROUP BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY mstreference.reference_name,trnmoneytrail.booking_year, trnmoneytrail.booking_period ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {
		$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_receive"=>abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2)),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"],"breakdown"=>array());

		$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["reference_name"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];

		$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'ARI' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " AND trnmoneytrail.reference_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["reference_mst_code"]."' "
			. " AND trnmoneytrail.booking_year = '".$larr_ResultRow_TRNMONEYTRAIL["trail_year"]."' "
			. " AND trnmoneytrail.booking_period = '".$larr_ResultRow_TRNMONEYTRAIL["trail_month"]."' "
			. " AND trnmoneytrail.money_trail_type_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]."' "
			. " ORDER BY trnmoneytrail.money_trail_date DESC ";
		$SQL_RESULT_TRNMONEYTRAILDETAILS=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {

			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"];
			if ($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]<0.00) {
				$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = "(".number_format(abs($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]),2) . ")";
			} // if ($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]<0.00) {
			else {
				$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = number_format($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2);
			} // ELSE ng if ($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]<0.00) {
			
			
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
		} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
		ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

	// from ARI entry - to deduct
	$lch_Query = "SELECT mstreference.reference_name, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year, SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.booking_year,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.reference_mst_code) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.account_money_trail_type_mst_code "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'ARI' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " GROUP BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY mstreference.reference_name,trnmoneytrail.booking_year, trnmoneytrail.booking_period ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {
		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_receive"=>round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"]); 
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["reference_name"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];
		} // if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
		else {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_receive"] -= abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2));
		} // ELSE ng if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {

		$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'ARI' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " AND trnmoneytrail.reference_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["reference_mst_code"]."' "
			. " AND trnmoneytrail.booking_year = '".$larr_ResultRow_TRNMONEYTRAIL["trail_year"]."' "
			. " AND trnmoneytrail.booking_period = '".$larr_ResultRow_TRNMONEYTRAIL["trail_month"]."' "
			. " AND trnmoneytrail.account_money_trail_type_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]."' "
			. " ORDER BY trnmoneytrail.money_trail_date DESC ";
		$SQL_RESULT_TRNMONEYTRAILDETAILS=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] * -1;
			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = "(".number_format(abs($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]),2) . ")";

			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
		} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {

		if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_receive"]==0.00) {
			unset($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]);
		} // if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_receive"]==0.00) {
		else {
			ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);
		} // ELSE ng if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_receive"]==0.00) {
		
		
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {


	// $lch_Query = "SELECT trnmoneytrail.total_amount, trnmoneytrail.description ";
	// $lch_Query = $lch_Query . " FROM trnmoneytrail ";
	// $lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	// $lch_Query = $lch_Query . " WHERE ";
	// $lch_Query = $lch_Query . " trnmoneytrail.is_paid = '0' ";
	// $lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = '1' ";
	// $lch_Query = $lch_Query . " AND trnmoneytrail.created_user_mst_code = '$user_code' ";
	// $lch_Query = $lch_Query . " AND trnmoneytrail.total_amount < 0.00 ";
	// $lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	// $lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date ASC, trnmoneytrail.total_amount DESC ";
	// $SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	// while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {
	// 	$larr_outputArray[] =  array("to_pay"=>abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2)),"description"=>$larr_ResultRow_TRNMONEYTRAIL["description"]); 
	// } // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_array($SQL_RESULT_TRNMONEYTRAIL)) {


	
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