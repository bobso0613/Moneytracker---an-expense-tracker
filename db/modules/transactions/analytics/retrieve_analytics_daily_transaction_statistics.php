<?php
header('Content-type: application/json');
date_default_timezone_set('Asia/Manila');
@include_once("../../../api/DatabaseConnect.php");
@include_once("../../../api/SanitizeField.php");


function number_format_short( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}

//$lch_dbAccess = new DatabaseAccess();
$larr_outputArray = array();
$larr_ResultQueryArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	$user_code = sanitizeField(@$_POST['user_code']);

	$date_today = date("Y-m-d");
	$month_today = date("n");
	$year_today = date("Y");
	//$date_today = "2017-12-14";

	$larr_ResultQueryArray = array(
		"result" => "1",	
		"error_message" => "Proceed"
	);

	$larr_ResultQueryArray["transaction_count"] = 0;
	$larr_ResultQueryArray["expense_amount"] = 0;
	$larr_ResultQueryArray["amount_received_month"] = 0;
	$larr_ResultQueryArray["income_amount"] = 0;

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);
	$lch_Query = "SELECT COUNT(trnmoneytrail.code) AS 'transaction_count', ";
	$lch_Query = $lch_Query . " SUM(CASE WHEN trnmoneytrail.trail_type = 1 THEN trnmoneytrail.total_amount ELSE 0 END) AS 'expense_amount', "; 
	$lch_Query = $lch_Query . " SUM(CASE WHEN trnmoneytrail.trail_type = 2 THEN trnmoneytrail.total_amount ELSE 0 END) AS 'income_amount' "; 
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND DATE(trnmoneytrail.money_trail_date) = '$date_today' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date DESC ";
	$SQL_RESULT_TRNINTPOLICY=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNINTPOLICY=mysql_fetch_array($SQL_RESULT_TRNINTPOLICY)) {
		$larr_ResultQueryArray["transaction_count"] = intval($larr_ResultRow_TRNINTPOLICY["transaction_count"]);
		$larr_ResultQueryArray["expense_amount"] = number_format_short(floatval($larr_ResultRow_TRNINTPOLICY["expense_amount"]));
		//$larr_ResultQueryArray["amount_received"] = number_format_short(floatval($larr_ResultRow_TRNINTPOLICY["amount_received"]));
		$larr_ResultQueryArray["income_amount"] = floatval($larr_ResultRow_TRNINTPOLICY["income_amount"]);
	} // while($larr_ResultRow_TRNINTPOLICY=mysql_fetch_array($SQL_RESULT_TRNINTPOLICY)) {

	// handle transfer only (income to income)
	$lch_Query = "SELECT  ";
	$lch_Query = $lch_Query . " SUM(trnmoneytrail.total_amount) AS 'income_amount' "; 
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND DATE(trnmoneytrail.money_trail_date) = '$date_today' ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = 2 ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date DESC ";
	$SQL_RESULT_TRNINTPOLICY=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNINTPOLICY=mysql_fetch_array($SQL_RESULT_TRNINTPOLICY)) {
		$larr_ResultQueryArray["income_amount"] -= floatval($larr_ResultRow_TRNINTPOLICY["income_amount"]);
	}

	$larr_ResultQueryArray["income_amount"] = number_format_short(floatval($larr_ResultQueryArray["income_amount"]));


	$lch_Query = "SELECT ";
	$lch_Query = $lch_Query . " SUM(CASE WHEN trnmoneytrail.trail_type = 1 THEN trnmoneytrail.total_amount ELSE 0 END) AS 'expense_amount', "; 
	$lch_Query = $lch_Query . " SUM(CASE WHEN trnmoneytrail.trail_type = 2 THEN trnmoneytrail.total_amount ELSE 0 END) AS 'income_amount' "; 
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND (MONTH(trnmoneytrail.money_trail_date) = '$month_today' ";
	$lch_Query = $lch_Query . " AND YEAR(trnmoneytrail.money_trail_date) = '$year_today') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date DESC ";
	$SQL_RESULT_TRNINTPOLICY=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNINTPOLICY=mysql_fetch_array($SQL_RESULT_TRNINTPOLICY)) {
		$larr_ResultQueryArray["amount_received_month"] = floatval($larr_ResultRow_TRNINTPOLICY["income_amount"]) - floatval($larr_ResultRow_TRNINTPOLICY["expense_amount"]);
	}

	// handle transfer only (income to income)
	$lch_Query = "SELECT  ";
	$lch_Query = $lch_Query . " SUM(trnmoneytrail.total_amount) AS 'income_amount' "; 
	$lch_Query = $lch_Query . " FROM trnmoneytrail ";
	$lch_Query = $lch_Query . " INNER JOIN mstmoneytrailtype ON trnmoneytrail.account_money_trail_type_mst_code = mstmoneytrailtype.code ";
	$lch_Query = $lch_Query . " WHERE ";
	$lch_Query = $lch_Query . " trnmoneytrail.created_user_mst_code = '$user_code' ";
	$lch_Query = $lch_Query . " AND (MONTH(trnmoneytrail.money_trail_date) = '$month_today' ";
	$lch_Query = $lch_Query . " AND YEAR(trnmoneytrail.money_trail_date) = '$year_today') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.short_name NOT LIKE 'AP%' ";
	$lch_Query = $lch_Query . " AND (mstmoneytrailtype.short_name != 'API' AND mstmoneytrailtype.short_name != 'ARI') ";
	$lch_Query = $lch_Query . " AND mstmoneytrailtype.is_other_currency = '2' ";
	$lch_Query = $lch_Query . " AND trnmoneytrail.trail_type = 2 ";
	$lch_Query = $lch_Query . " ORDER BY trnmoneytrail.money_trail_date DESC ";
	$SQL_RESULT_TRNINTPOLICY=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow_TRNINTPOLICY=mysql_fetch_array($SQL_RESULT_TRNINTPOLICY)) {
		$larr_ResultQueryArray["amount_received_month"] -= floatval($larr_ResultRow_TRNINTPOLICY["income_amount"]);
	}

	$larr_ResultQueryArray["amount_received_month"] = number_format_short(floatval($larr_ResultQueryArray["amount_received_month"]) );


	array_push($larr_outputArray,$larr_ResultQueryArray);
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