<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: POST-check=0, pre-check=0", false);
// header("Pragma: no-cache");
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


	if ($transactionmode=="add_interim"||$transactionmode=="process_interim"){
		$mstscheduledtransactions_is_active = sanitizeField(@$_POST['mstscheduledtransactions_is_active']);
		$mstscheduledtransactions_template_name = sanitizeField(@$_POST['mstscheduledtransactions_template_name']);
		$mstscheduledtransactions_start_date = sanitizeField(@$_POST['mstscheduledtransactions_start_date']);
		$mstscheduledtransactions_frequency = sanitizeField(@$_POST['mstscheduledtransactions_frequency']);
		$mstscheduledtransactions_maximum_process_count = sanitizeField(@$_POST['mstscheduledtransactions_maximum_process_count']);
		$mstscheduledtransactions_booking_period_mode = sanitizeField(@$_POST['mstscheduledtransactions_booking_period_mode']);
		$mstscheduledtransactions_template_pattern = sanitizeField(@$_POST['mstscheduledtransactions_template_pattern']);
		$mstscheduledtransactions_reference_mst_code = sanitizeField(@$_POST['mstscheduledtransactions_reference_mst_code']);
		$mstscheduledtransactions_money_trail_type = sanitizeField(@$_POST['mstscheduledtransactions_money_trail_type']);
		$mstscheduledtransactions_money_trail_type_mst_code = sanitizeField(@$_POST['mstscheduledtransactions_money_trail_type_mst_code']);
		$mstscheduledtransactions_reference_no = sanitizeField(@$_POST['mstscheduledtransactions_reference_no']);
		$mstscheduledtransactions_account_money_trail_type_mst_code = sanitizeField(@$_POST['mstscheduledtransactions_account_money_trail_type_mst_code']);

		$mstscheduledtransactions_booking_year = sanitizeField(@$_POST['mstscheduledtransactions_booking_year']);
		$mstscheduledtransactions_booking_period = sanitizeField(@$_POST['mstscheduledtransactions_booking_period']);

	} // if ($transactionmode=="add_interim"||$transactionmode=="process_interim"){

	if ($transactionmode=="add_interim"){

		

		$lch_Query = "INSERT INTO mstscheduledtransactions (";
		$lch_Query = $lch_Query . "template_name,start_date,is_active,frequency,current_process_count,maximum_process_count,template_pattern,previous_run_date,";
		$lch_Query = $lch_Query . "booking_period_mode,trail_type,reference_no,money_trail_type_mst_code,reference_mst_code,total_amount,account_money_trail_type_mst_code,no_of_items,";
		$lch_Query = $lch_Query . "booking_year,booking_period,";
		$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
		$lch_Query = $lch_Query . ") VALUES (";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_template_name',";
		$lch_Query = $lch_Query . "STR_TO_DATE('".$mstscheduledtransactions_start_date."','%m-%d-%Y'),";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_is_active',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_frequency',";
		$lch_Query = $lch_Query . "'0',"; // current_process_count
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_maximum_process_count',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_template_pattern',";
		$lch_Query = $lch_Query . "'0000-00-00',"; // previous_run_date
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_booking_period_mode',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_money_trail_type',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_reference_no',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_money_trail_type_mst_code',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_reference_mst_code',";
		$lch_Query = $lch_Query . "'0.00',"; // total_amount
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_account_money_trail_type_mst_code',";
		$lch_Query = $lch_Query . "'0',"; // no_of_items
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_booking_year',";
		$lch_Query = $lch_Query . "'$mstscheduledtransactions_booking_period',";
		$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
		$lch_Query = $lch_Query . ");";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		//array_push($larr_outputArray,error_POST_last());
		if (!$SQL_RESULT){
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Failed to add a new Scheduled Transaction."
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // if (!$SQL_RESULT){
		else {

			$lin_InterimInternalCode = mysql_insert_id();

			$larr_ResultQueryArray = array(
				"result" => "1",	
				"error_message" => "Scheduled Transaction successfully added.",
				"return_interim_code" => $lin_InterimInternalCode,
				"template_name" => $mstscheduledtransactions_template_name
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);

			// add details
			$larr_ItemDetailsCodes = @$_POST["mstscheduledtransactionsdtlitems_codes_toadd"];

			$lde_totalAmount = 0.00;
			$lin_NoItems = 0;

			if (count($larr_ItemDetailsCodes)>0) {

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$mstscheduledtransactionsdtlitems_amount = sanitizeField(str_replace(",","",@$_POST['mstscheduledtransactionsdtlitems_amount_'.$lch_Key]));
					$mstscheduledtransactionsdtlitems_description = sanitizeField(@$_POST['mstscheduledtransactionsdtlitems_description_'.$lch_Key]);

					$lch_Query = "INSERT INTO mstscheduledtransactionsdtlitems (";
					$lch_Query = $lch_Query . "scheduled_transactions_mst_code,description,amount,";
					$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
					$lch_Query = $lch_Query . ") VALUES (";
					$lch_Query = $lch_Query . "'$lin_InterimInternalCode',";
					$lch_Query = $lch_Query . "'$mstscheduledtransactionsdtlitems_description',";
					$lch_Query = $lch_Query . "'$mstscheduledtransactionsdtlitems_amount',";
					$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
					$lch_Query = $lch_Query . ");";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($mstscheduledtransactionsdtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {

				// update header for the new values
				$lch_Query = "UPDATE mstscheduledtransactions SET ";
				$lch_Query = $lch_Query . " mstscheduledtransactions.total_amount = '$lde_totalAmount', ";
				$lch_Query = $lch_Query . " mstscheduledtransactions.no_of_items = '$lin_NoItems', ";
				$lch_Query = $lch_Query . " mstscheduledtransactions.updated_at = NOW(), mstscheduledtransactions.updated_user_mst_code = '$user_code' ";
				$lch_Query = $lch_Query . " WHERE ";
				$lch_Query = $lch_Query . " mstscheduledtransactions.code = '$lin_InterimInternalCode' ";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			} // if (count($larr_ItemDetailsCodes)>0) {

		} // ELSE ng if (!$SQL_RESULT){


	} // if ($transactionmode=="add_interim"){

	else if ($transactionmode=="process_interim"){

		$lch_InterimNumber = "";
		$lin_InterimInternalCode = 0;

		// RETRIEVE FIRST THE INTERIM POLICY HEADER
		$lch_Query = "SELECT mstscheduledtransactions.code,mstscheduledtransactions.template_name FROM mstscheduledtransactions WHERE mstscheduledtransactions.code = '".sanitizeField(@$_POST["scheduled_transactions_mst_code"])."' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lch_InterimNumber = $larr_ResultRow["template_name"];
			$lin_InterimInternalCode = intval($larr_ResultRow["code"]);
		} // if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			

		if ($lch_InterimNumber!="") {

			$lde_totalAmount = 0.00;
			$lin_NoItems = 0;

			// for existing
			$larr_ItemDetailsCodes = @$_POST["mstscheduledtransactionsdtlitems_codes"];
			if (count($larr_ItemDetailsCodes)>0) {

				// delete muna yung wala sa list (baka binura na kasi)
				$lch_Query = "DELETE FROM mstscheduledtransactionsdtlitems WHERE ";
				$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.scheduled_transactions_mst_code = '".$lin_InterimInternalCode."' ";
				$lch_Query = $lch_Query . " AND mstscheduledtransactionsdtlitems.code NOT IN (".implode(",", $larr_ItemDetailsCodes).")";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$mstscheduledtransactionsdtlitems_amount = sanitizeField(str_replace(",","",@$_POST['mstscheduledtransactionsdtlitems_amount_'.$lch_Key]));
					$mstscheduledtransactionsdtlitems_description = sanitizeField(@$_POST['mstscheduledtransactionsdtlitems_description_'.$lch_Key]);

					// edit here baka may gumalaw
					$lch_Query = "UPDATE mstscheduledtransactionsdtlitems SET ";
					$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.description = '$mstscheduledtransactionsdtlitems_description', ";
					$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.amount = '$mstscheduledtransactionsdtlitems_amount', ";
					$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.updated_at = NOW(), mstscheduledtransactionsdtlitems.updated_user_mst_code = '$user_code' ";
					$lch_Query = $lch_Query . " WHERE ";
					$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.scheduled_transactions_mst_code = '$lin_InterimInternalCode' ";
					$lch_Query = $lch_Query . " AND mstscheduledtransactionsdtlitems.code = '$lch_Key' ";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($mstscheduledtransactionsdtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {

				

			} // if (count($larr_ItemDetailsCodes)>0) {

			// if walang laman - delete yung mga laman sa ngayon
			else {

				$lch_Query = "DELETE FROM mstscheduledtransactionsdtlitems WHERE ";
				$lch_Query = $lch_Query . " mstscheduledtransactionsdtlitems.scheduled_transactions_mst_code = '".$lin_InterimInternalCode."' ";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			} // ELSE ng if (count($larr_ItemDetailsCodes)>0) {

			// add details
			$larr_ItemDetailsCodes = @$_POST["mstscheduledtransactionsdtlitems_codes_toadd"];
			if (count($larr_ItemDetailsCodes)>0) {

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$mstscheduledtransactionsdtlitems_amount = sanitizeField(str_replace(",","",@$_POST['mstscheduledtransactionsdtlitems_amount_'.$lch_Key]));
					$mstscheduledtransactionsdtlitems_description = sanitizeField(@$_POST['mstscheduledtransactionsdtlitems_description_'.$lch_Key]);

					$lch_Query = "INSERT INTO mstscheduledtransactionsdtlitems (";
					$lch_Query = $lch_Query . "scheduled_transactions_mst_code,description,amount,";
					$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
					$lch_Query = $lch_Query . ") VALUES (";
					$lch_Query = $lch_Query . "'$lin_InterimInternalCode',";
					$lch_Query = $lch_Query . "'$mstscheduledtransactionsdtlitems_description',";
					$lch_Query = $lch_Query . "'$mstscheduledtransactionsdtlitems_amount',";
					$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
					$lch_Query = $lch_Query . ");";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($mstscheduledtransactionsdtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {


			} // if (count($larr_ItemDetailsCodes)>0) {



			$lch_Query = "UPDATE mstscheduledtransactions SET ";

			$lch_Query = $lch_Query . " mstscheduledtransactions.is_active = '$mstscheduledtransactions_is_active', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.template_name = '$mstscheduledtransactions_template_name', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.maximum_process_count = '$mstscheduledtransactions_maximum_process_count', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.booking_period_mode = '$mstscheduledtransactions_booking_period_mode', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.booking_period = '$mstscheduledtransactions_booking_period', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.booking_year = '$mstscheduledtransactions_booking_year', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.template_pattern = '$mstscheduledtransactions_template_pattern', ";

			$lch_Query = $lch_Query . " mstscheduledtransactions.reference_mst_code = '$mstscheduledtransactions_reference_mst_code', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.trail_type = '$mstscheduledtransactions_money_trail_type', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.money_trail_type_mst_code = '$mstscheduledtransactions_money_trail_type_mst_code', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.reference_no = '$mstscheduledtransactions_reference_no', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.account_money_trail_type_mst_code = '$mstscheduledtransactions_account_money_trail_type_mst_code', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.total_amount = '$lde_totalAmount', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.no_of_items = '$lin_NoItems', ";

			$lch_Query = $lch_Query . " mstscheduledtransactions.updated_at = NOW(), mstscheduledtransactions.updated_user_mst_code = '$user_code' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.code = '$lin_InterimInternalCode' ";
			$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			if (!$SQL_RESULT){
				$larr_ResultQueryArray = array(
					"result" => "0",	
					"error_message" => "Failed to update Scheduled Transaction."
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);
			} // if (!$SQL_RESULT){
			else {

				$larr_ResultQueryArray = array(
					"result" => "1",	
					"error_message" => "Scheduled Transaction successfully updated.",
					"return_interim_code" => $lin_InterimInternalCode,
					"template_name" => $lch_InterimNumber
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);

			} // ELSE ng if (!$SQL_RESULT){



		} // if ($lch_InterimNumber!="") {
		else {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Fatal Error. Selected Scheduled Transaction not found. (It might be already deleted in the database by some other user.)"
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // ELSE ng if ($lch_InterimNumber!="") {


	} // if ($transactionmode=="process_interim"){

	else if ($transactionmode=="delete_interim"){

		$lch_InterimNumber = "";

		// RETRIEVE FIRST THE INTERIM POLICY HEADER
		$lch_Query = "SELECT mstscheduledtransactions.code,mstscheduledtransactions.template_name FROM mstscheduledtransactions WHERE mstscheduledtransactions.code = '".sanitizeField(@$_POST["scheduled_transactions_mst_code"])."' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lch_InterimNumber = $larr_ResultRow["template_name"];

		} // if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			

		if ($lch_InterimNumber!="") {

			$lch_Query = "DELETE FROM mstscheduledtransactionsdtlitems WHERE mstscheduledtransactionsdtlitems.scheduled_transactions_mst_code = '".sanitizeField(@$_POST["scheduled_transactions_mst_code"])."'";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

			$lch_Query = "DELETE FROM mstscheduledtransactions WHERE mstscheduledtransactions.code = '".sanitizeField(@$_POST["scheduled_transactions_mst_code"])."'";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

			if (!$SQL_RESULT){
				$larr_ResultQueryArray = array(
					"result" => "0",	
					"error_message" => "Failed to delete the selected Scheduled Transaction."
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);
			} // if (!$SQL_RESULT){
			else {

				$larr_ResultQueryArray = array(
					"result" => "1",	
					"error_message" => "Selected Scheduled Transaction successfully deleted.",
					"template_name" => $lch_InterimNumber
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);

			} // ELSE ng if (!$SQL_RESULT){
		} // if ($lch_InterimNumber!="") {
		else {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Fatal Error. Selected Scheduled Transaction not found. (It might be already deleted in the database by some other user.)"
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // ELSE ng if ($lch_InterimNumber!="") {

		

	} // else if ($transactionmode=="delete_interim"){

	else {
		// NO ACTION SPECIFIED
		$larr_ResultQueryArray = array(
			"result" => "0",	
			"error_message" => "Please specify an action."
		);
		array_push($larr_outputArray,$larr_ResultQueryArray);
	} // ELSE ng else if ($transactionmode=="can_delete"){


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