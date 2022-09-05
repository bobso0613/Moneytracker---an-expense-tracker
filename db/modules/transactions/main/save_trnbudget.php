<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

header('Content-type: application/json');
date_default_timezone_set('Asia/Manila');
@include_once("../../../api/DatabaseConnect.php");
@include_once("../../../api/SanitizeField.php");

$lch_dbAccess = new DatabaseAccess();
$larr_outputArray = array();
$larr_ResultQueryArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	// COMMON FOR ALL
	$transactionmode = sanitizeField(@$_POST['transactionmode']);
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);
	$user_code = sanitizeField(@$_POST['user_code']);


	$money_trail_type_mst_code = sanitizeField(intval(@$_POST['money_trail_type_mst_code']));
	$booking_year = sanitizeField(intval(@$_POST['booking_year']));
	$booking_year_last = $booking_year - 1;
	$larr_MSTMoneyTrailType = array();

	$larr_Months = array("1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June",
		"7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");

	$larr_TrailType = array("1"=>"Expense","2"=>"Income");

	$lch_Query = "SELECT mstmoneytrailtype.* "
			. " FROM mstmoneytrailtype "
			. " WHERE "
			. " mstmoneytrailtype.code = '$money_trail_type_mst_code' ";
	$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	if($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {

		$larr_ResultRow["trail_type_description"] = $larr_TrailType[$larr_ResultRow["trail_type"]];

		$larr_MSTMoneyTrailType = $larr_ResultRow;

	} // if($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {

	if ($transactionmode=="get_budget_setup"){

		if(count($larr_MSTMoneyTrailType)>0) {

			$larr_ResultQueryArray = array("result"=>"1","error_message"=>"ok",
							"money_trail_type"=>$larr_MSTMoneyTrailType,
							"budget"=>array());

			foreach ($larr_Months as $lch_Key => $lch_Value) {
				$larr_ResultQueryArray["budget"][$lch_Key] = array("budget_trn_code"=>0,
															"name"=>$lch_Value,
															"budget_amount"=>0.00,
															"actual_amount"=>0.00,
															"diff_amount"=>0.00,
															"diff_amount_percentage"=>0.00,
															"transaction_count"=>0,
															"booking_year"=>$booking_year,
															"booking_period"=>$lch_Key,
															"remarks"=>"");
			} // foreach ($larr_Months as $lch_Key => $lch_Value) {

		} // if(count($larr_MSTMoneyTrailType)>0) {

		$lch_Query = "SELECT trnbudget.* "
				. " FROM trnbudget "
				. " WHERE "
				. " trnbudget.money_trail_type_mst_code = '$money_trail_type_mst_code' "
				. " AND trnbudget.booking_year = '$booking_year' "
				. " ORDER BY trnbudget.booking_year asc, trnbudget.booking_period ASC ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
			$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["budget_trn_code"] = $larr_ResultRow["code"];
			$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["budget_amount"] = $larr_ResultRow["budget_amount"];
			$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["remarks"] = $larr_ResultRow["remarks"];
		} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {

		// get remarks for previous year (if meron)
		$lch_Query = "SELECT trnbudget.* "
				. " FROM trnbudget "
				. " WHERE "
				. " trnbudget.money_trail_type_mst_code = '$money_trail_type_mst_code' "
				. " AND trnbudget.booking_year = '$booking_year_last' "
				. " ORDER BY trnbudget.booking_year asc, trnbudget.booking_period ASC ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
			if ($larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["remarks"]=="") {
				$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["remarks"] = $larr_ResultRow["remarks"];
			} // if ($larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["remarks"]=="") {
		} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {


		$lch_Query = "SELECT trnmoneytrail.money_trail_type_mst_code, MONTH(trnmoneytrail.money_trail_date) as 'booking_period' , "
				. " COUNT(trnmoneytrail.code) as 'transaction_count', SUM(trnmoneytrail.total_amount) as 'actual_amount' "
				. " FROM trnmoneytrail "
				. " WHERE "
				. " trnmoneytrail.money_trail_type_mst_code = '$money_trail_type_mst_code' "
				. " AND YEAR(trnmoneytrail.money_trail_date) = '$booking_year' "
				. " AND trnmoneytrail.is_paid = 1 "
				. " GROUP BY trnmoneytrail.money_trail_type_mst_code, YEAR(trnmoneytrail.money_trail_date), MONTH(trnmoneytrail.money_trail_date) "
				. " ORDER BY trnmoneytrail.money_trail_type_mst_code, YEAR(trnmoneytrail.money_trail_date), MONTH(trnmoneytrail.money_trail_date) ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
			$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["transaction_count"] += $larr_ResultRow["transaction_count"];
			$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["actual_amount"] += $larr_ResultRow["actual_amount"];
		} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {

		if ($larr_MSTMoneyTrailType["trail_type"]==2){
			// if income
			$lch_Query = "SELECT trnmoneytrail.account_money_trail_type_mst_code, MONTH(trnmoneytrail.money_trail_date) as 'booking_period' , "
					. " COUNT(trnmoneytrail.code) as 'transaction_count', SUM(trnmoneytrail.total_amount) as 'actual_amount' "
					. " FROM trnmoneytrail "
					. " WHERE "
					. " trnmoneytrail.account_money_trail_type_mst_code = '$money_trail_type_mst_code' "
					. " AND YEAR(trnmoneytrail.money_trail_date) = '$booking_year' "
					. " AND trnmoneytrail.is_paid = 1 "
					. " GROUP BY trnmoneytrail.account_money_trail_type_mst_code, YEAR(trnmoneytrail.money_trail_date), MONTH(trnmoneytrail.money_trail_date) "
					. " ORDER BY trnmoneytrail.account_money_trail_type_mst_code, YEAR(trnmoneytrail.money_trail_date), MONTH(trnmoneytrail.money_trail_date) ";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
				$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["transaction_count"] += $larr_ResultRow["transaction_count"];
				$larr_ResultQueryArray["budget"][$larr_ResultRow["booking_period"]]["actual_amount"] -= $larr_ResultRow["actual_amount"];
			} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
		} // if ($larr_MSTMoneyTrailType["trail_type"]==2){


		foreach ($larr_ResultQueryArray["budget"] as $lch_Key => $larr_Value) {

			$lde_DiffAmount = 0.00;
			$lde_DiffAmountPercentage = 0.00;


			$lde_DiffAmount = $larr_Value["budget_amount"] - $larr_Value["actual_amount"];
			if ($larr_Value["budget_amount"]==0.00 || $larr_Value["actual_amount"]==0.00) {
				$lde_DiffAmountPercentage = 0.00;
			} // if ($larr_Value["budget_amount"]==0.00 || $larr_Value["actual_amount"]==0.00) {
			else {
				$lde_DiffAmountPercentage = round($larr_Value["actual_amount"] / $larr_Value["budget_amount"],2);
			} // ELSE ng if ($larr_Value["budget_amount"]==0.00 || $larr_Value["actual_amount"]==0.00) {

			if (intval($larr_Value["booking_year"].str_pad($larr_Value["booking_period"],2,"0",STR_PAD_LEFT))<=date("Ym")) {
				$larr_ResultQueryArray["budget"][$lch_Key]["diff_amount"] = $lde_DiffAmount;
				$larr_ResultQueryArray["budget"][$lch_Key]["diff_amount_percentage"] = $lde_DiffAmountPercentage;
			} // if (intval($larr_Value["booking_year"].$larr_Value["booking_period"])<=date("Ym")) {
			
			

		} // foreach ($larr_ResultQueryArray["budget"] as $lch_Key => $larr_Value) {
		

		array_push($larr_outputArray,$larr_ResultQueryArray);
	} // if ($transactionmode=="get_budget_setups"){

	else if ($transactionmode=="save_trn_budget"){

		if (count($larr_MSTMoneyTrailType)<=0) {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Please specify a Money Trail Type"
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // if (count($larr_MSTMoneyTrailType)<=0) {

		if ($booking_year==0) {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Please select a Booking Year"
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // if ($booking_year==0) {

		if (count($larr_outputArray)<=0) {

			$larr_MonthInput = @$_POST["trnbudget_code"];

			foreach ($larr_MonthInput as $lch_Month) {

				$larr_TRNBudget = array();

				$trnbudget_budget_amount = round(floatval(str_replace(",","",@$_POST["trnbudget_budget_amount_".$lch_Month])),2);
				$trnbudget_remarks = @$_POST["trnbudget_remarks_".$lch_Month];

				// get remarks for previous year (if meron)
				$lch_Query = "SELECT trnbudget.* "
						. " FROM trnbudget "
						. " WHERE "
						. " trnbudget.money_trail_type_mst_code = '".$larr_MSTMoneyTrailType["code"]."' "
						. " AND trnbudget.booking_year = '$booking_year' "
						. " AND trnbudget.booking_period = '$lch_Month' "
						. " ORDER BY trnbudget.booking_year asc, trnbudget.booking_period ASC ";
				$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
				while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {
					$larr_TRNBudget = $larr_ResultRow;
				} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT)) {

				if (count($larr_TRNBudget)>0) {
					$lch_Query = "UPDATE trnbudget SET "
							. " trnbudget.budget_amount = '".sanitizeField($trnbudget_budget_amount)."', "
							. " trnbudget.remarks = '".sanitizeField($trnbudget_remarks)."', "
							. " trnbudget.updated_at = NOW(), trnbudget.updated_user_mst_code = '$user_code' "
							. " WHERE "
							. " trnbudget.code = '".$larr_TRNBudget["code"]."' ";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());
				} // if (count($larr_TRNBudget)>0) {
				else {
					$lch_Query = "INSERT INTO trnbudget ("
							. "money_trail_type_mst_code,booking_year,booking_period,budget_amount,"
							. "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code"
							. ") VALUES ("
							. "'".$larr_MSTMoneyTrailType["code"]."',"
							. "'$booking_year',"
							. "'$lch_Month',"
							. "'".sanitizeField($trnbudget_budget_amount)."',"
							. "'".sanitizeField($trnbudget_remarks)."',NOW(),NOW(),'$user_code','$user_code'"
							. ");";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

				} // ELSE ng if (count($larr_TRNBudget)>0) {

			} // foreach ($larr_MonthInput as $lch_Month) {

			$larr_ResultQueryArray = array(
				"result" => "1",	
				"error_message" => $larr_MSTMoneyTrailType["money_trail_name"] . " budget saved successfully. ",
				"money_trail_name"=>$larr_MSTMoneyTrailType["money_trail_name"],
				"booking_year"=>$booking_year
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);

		} // if (count($larr_outputArray)<=0) {

	} // if ($transactionmode=="save_trn_budget"){

	else {
		// NO ACTION SPECIFIED
		$larr_ResultQueryArray = array(
			"result" => "0",	
			"error_message" => "Please specify an action."
		);
		array_push($larr_outputArray,$larr_ResultQueryArray);
	} // ELSE ng else if ($transactionmode=="save_trn_budget"){


	// use this
	//array_push($larr_outputArray, $larr_ResultQueryArray);


} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
else {
	// illegal access error
	$larr_ResultQueryArray = array(
		"result" => "0",	
		"error_message" => "Invalid API Access."
	);
	array_push($larr_outputArray,$larr_ResultQueryArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

$lch_dbAccess->closeCon();
echo json_encode($larr_outputArray);
?>