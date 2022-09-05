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

	// --------------------------------------------- API ENTRIES ---------------------------------------------

	// from API entry - to deduct
	$lch_Query = "SELECT mstreference.reference_name, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year , SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.account_money_trail_type_mst_code,'-',trnmoneytrail.reference_mst_code,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.booking_year) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.account_money_trail_type_mst_code "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'API' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " GROUP BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {
		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_pay"=>round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"]); 
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["reference_name"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];
		} // if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
		else {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"] -= abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2));
		} // ELSE ng if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {

		$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'API' "
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
			
			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"];
			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = number_format($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2);
			$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = "";

			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
		} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
		ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);
		
	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

	// from API entry - to add
	$lch_Query = "SELECT mstreference.reference_name, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year , SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.money_trail_type_mst_code,'-',trnmoneytrail.reference_mst_code,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.booking_year) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.money_trail_type_mst_code "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'API' "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " GROUP BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY trnmoneytrail.reference_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {
		if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_pay"=>round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"]); 
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["reference_name"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];
		} // if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
		else {
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"] -= abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2));
		} // ELSE ng if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {

		$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code AND mstmoneytrailtype.short_name = 'API' "
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

			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] * -1;
			$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = "(".number_format(abs($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]),2) . ")";
			$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = "";
			
			$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
		} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
		
		if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {
			unset($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]);
		} // if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {
		else {
			ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);
		} // ELSE ng if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

	// --------------------------------------------- END - API ENTRIES ---------------------------------------------


	// --------------------------------------------- API ENTRIES ---------------------------------------------
	// to add
	$lch_Query = "SELECT  mstmoneytrailtype.description, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year, SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.account_money_trail_type_mst_code,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.booking_year) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.account_money_trail_type_mst_code, mstmoneytrailtype.short_name as 'trail_short_name' "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " AND mstmoneytrailtype.is_payable_debt_account = '1' "
			// . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') "
			// . " AND trnmoneytrail.money_trail_date >= '2019-11-01' "
			. " GROUP BY trnmoneytrail.account_money_trail_type_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY trnmoneytrail.account_money_trail_type_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

		if ($larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='ARI'&&$larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='API') {


			if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_pay"=>round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"]); 
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["description"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];
			} // if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
			else {
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"] -= abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2));
			} // ELSE ng if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {

			$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date, COALESCE(reverse.short_name,'') as 'trail_short_name', COALESCE(reverse.money_trail_name,'') as 'money_trail_name' "
				. " FROM trnmoneytrail "
				. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code "
				. " LEFT JOIN mstmoneytrailtype reverse ON trnmoneytrail.money_trail_type_mst_code = reverse.code "
				. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
				. " WHERE "
				. " trnmoneytrail.is_paid = '1' "
				. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
				. " AND mstmoneytrailtype.is_other_currency = '2' "
				. " AND mstmoneytrailtype.is_payable_debt_account = '1' "
				// . " AND trnmoneytrail.money_trail_date >= '2019-11-01' "
				// . " AND trnmoneytrail.reference_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["reference_mst_code"]."' "
				. " AND trnmoneytrail.booking_year = '".$larr_ResultRow_TRNMONEYTRAIL["trail_year"]."' "
				. " AND trnmoneytrail.booking_period = '".$larr_ResultRow_TRNMONEYTRAIL["trail_month"]."' "
				. " AND trnmoneytrail.account_money_trail_type_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["account_money_trail_type_mst_code"]."' "
				// . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') "
				. " ORDER BY trnmoneytrail.money_trail_date DESC ";
			$SQL_RESULT_TRNMONEYTRAILDETAILS=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
				
				$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = round($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2);
				$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = number_format(round($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2),2);

				$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = "";
				$lch_AdditionalIdentifier = "0";
				// --------------------------------------------------------------------------
				// --------------------------------------------------------------------------
				// --------------------------------------------------------------------------
				if ($larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="API" || $larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="ARI") {
					$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_name"];
					$lch_AdditionalIdentifier = "1";
				} // if ($larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="API" || $larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="ARI") {

				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$lch_AdditionalIdentifier."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
			} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {

			if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {
				unset($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]);
			} // if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]<0.00) {
			else {
				ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);
			} // ELSE ng if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {		

		} // if ($larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='ARI'&&$larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='API') {

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

	// trnmoneytrail.booking_period
	// MONTH(DATE_SUB(trnmoneytrail.money_trail_date, INTERVAL 1 MONTH))
	// to deduct
	$lch_Query = "SELECT mstmoneytrailtype.description, MONTHNAME(STR_TO_DATE(trnmoneytrail.booking_period, '%m')) as 'month_name',trnmoneytrail.booking_year, SUM(trnmoneytrail.total_amount) as 'total_amount', trnmoneytrail.reference_mst_code, CONCAT(trnmoneytrail.money_trail_type_mst_code,'-',trnmoneytrail.booking_period,'-',trnmoneytrail.booking_year) as 'identifier', trnmoneytrail.booking_period as 'trail_month', trnmoneytrail.booking_year as 'trail_year', trnmoneytrail.money_trail_type_mst_code, mstmoneytrailtype.short_name as 'trail_short_name' "
			. " FROM trnmoneytrail "
			. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code "
			. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
			. " WHERE "
			. " trnmoneytrail.is_paid = '1' "
			. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
			. " AND mstmoneytrailtype.is_other_currency = '2' "
			. " AND mstmoneytrailtype.is_payable_debt_account = '1' "
			// . " AND trnmoneytrail.money_trail_date >= '2019-11-01' "
			// . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') "
			. " GROUP BY trnmoneytrail.money_trail_type_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  "
			. " HAVING SUM(trnmoneytrail.total_amount) != 0.00 "
			. " ORDER BY trnmoneytrail.money_trail_type_mst_code, trnmoneytrail.booking_year, trnmoneytrail.booking_period  ";
	// echo $lch_Query;
	$SQL_RESULT_TRNMONEYTRAIL=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

		if ($larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='ARI'&&$larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='API') {


			if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]] =  array("to_pay"=>round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2),"reference_name"=>$larr_ResultRow_TRNMONEYTRAIL["reference_name"]); 
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["reference_name"] = $larr_ResultRow_TRNMONEYTRAIL["description"] . " - " . $larr_ResultRow_TRNMONEYTRAIL["month_name"] . " " . $larr_ResultRow_TRNMONEYTRAIL["booking_year"];
			} // if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {
			else {
				
				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"] -= abs(round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2));
				
			} // ELSE ng if (!array_key_exists($larr_ResultRow_TRNMONEYTRAIL["identifier"], $larr_outputArray)) {

			$lch_Query = "SELECT trnmoneytrail.code, trnmoneytrail.total_amount, trnmoneytrail.description, trnmoneytrail.money_trail_date, COALESCE(reverse.short_name,'') as 'trail_short_name', COALESCE(reverse.money_trail_name,'') as 'money_trail_name' "
				. " FROM trnmoneytrail "
				. " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code "
				. " LEFT JOIN mstmoneytrailtype reverse ON trnmoneytrail.account_money_trail_type_mst_code = reverse.code "
				. " LEFT JOIN mstreference ON trnmoneytrail.reference_mst_code = mstreference.code "
				. " WHERE "
				. " trnmoneytrail.is_paid = '1' "
				. " AND trnmoneytrail.created_user_mst_code = '$user_code' "
				. " AND mstmoneytrailtype.is_other_currency = '2' "
				. " AND mstmoneytrailtype.is_payable_debt_account = '1' "
				// . " AND trnmoneytrail.money_trail_date >= '2019-11-01' "
				// . " AND trnmoneytrail.reference_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["reference_mst_code"]."' "
				. " AND trnmoneytrail.booking_year = '".$larr_ResultRow_TRNMONEYTRAIL["trail_year"]."' "
				. " AND trnmoneytrail.booking_period = '".$larr_ResultRow_TRNMONEYTRAIL["trail_month"]."' "
				. " AND trnmoneytrail.money_trail_type_mst_code = '".$larr_ResultRow_TRNMONEYTRAIL["money_trail_type_mst_code"]."' "
				// . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') "
				. " ORDER BY trnmoneytrail.money_trail_date DESC ";
			$SQL_RESULT_TRNMONEYTRAILDETAILS=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {
				
				if (round(floatval($larr_ResultRow_TRNMONEYTRAIL["total_amount"]),2)>=0.00 && $larr_ResultRow_TRNMONEYTRAILDETAILS["description"]=="refund ticket") {

					$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"] += (abs(round($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2))*2);

					$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = round($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2) ;
					$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = number_format(abs(round($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"],2)),2);
					
				}
				else {
					$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount_raw"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] * -1;
					$larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"] = "(".number_format(abs($larr_ResultRow_TRNMONEYTRAILDETAILS["total_amount"]),2) . ")";
				}

				$lch_AdditionalIdentifier = "0";

				$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = "";
				if ($larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="API" || $larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="ARI") {
					$larr_ResultRow_TRNMONEYTRAILDETAILS ["ar_or_ap"] = $larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_name"];
					$lch_AdditionalIdentifier = "1";
				} // if ($larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="API" || $larr_ResultRow_TRNMONEYTRAILDETAILS["trail_short_name"]=="ARI") {
				

				$larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"][$lch_AdditionalIdentifier."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["money_trail_date"]."-".$larr_ResultRow_TRNMONEYTRAILDETAILS["code"]] = $larr_ResultRow_TRNMONEYTRAILDETAILS;
			} // while($larr_ResultRow_TRNMONEYTRAILDETAILS=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAILDETAILS)) {

			if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {
				unset($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]);
			} // if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]<0.00) {
			else {
				ksort($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["breakdown"]);
			} // ELSE ng if ($larr_outputArray[$larr_ResultRow_TRNMONEYTRAIL["identifier"]]["to_pay"]==0.00) {		

		} // if ($larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='ARI'&&$larr_ResultRow_TRNMONEYTRAIL["trail_short_name"]!='API') {

	} // while($larr_ResultRow_TRNMONEYTRAIL=mysql_fetch_assoc($SQL_RESULT_TRNMONEYTRAIL)) {

	// --------------------------------------------- END - API ENTRIES ---------------------------------------------


	
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