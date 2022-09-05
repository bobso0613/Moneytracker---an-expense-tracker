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
		$trnmoneytrail_money_trail_date = sanitizeField(@$_POST['trnmoneytrail_money_trail_date']);
		$trnmoneytrail_money_trail_type = sanitizeField(@$_POST['trnmoneytrail_money_trail_type']);
		$trnmoneytrail_money_trail_type_mst_code = sanitizeField(@$_POST['trnmoneytrail_money_trail_type_mst_code']);
		$trnmoneytrail_description = sanitizeField(@$_POST['trnmoneytrail_description']);
		$trnmoneytrail_reference_no = sanitizeField(@$_POST['trnmoneytrail_reference_no']);
		$trnmoneytrail_account_money_trail_type_mst_code = sanitizeField(@$_POST['trnmoneytrail_account_money_trail_type_mst_code']);
		$trnmoneytrail_is_paid = sanitizeField(@$_POST['trnmoneytrail_is_paid']);

		$trnmoneytrail_reference_mst_code = sanitizeField(@$_POST['trnmoneytrail_reference_mst_code']);

		$trnmoneytrail_booking_year = sanitizeField(@$_POST['trnmoneytrail_booking_year']);
		$trnmoneytrail_booking_period = sanitizeField(@$_POST['trnmoneytrail_booking_period']);

	} // if ($transactionmode=="add_interim"||$transactionmode=="process_interim"){

	if ($transactionmode=="add_interim"){

		$lin_CurrentYear = date("Y");
		$lin_CurrentMonth = date("m");
		$lin_InterimSeriesNo = 0;
		$lch_InterimNumber = "";

		$lch_Query = "SELECT mstmoneytrackersequence.* FROM mstmoneytrackersequence WHERE mstmoneytrackersequence.calendar_year = '$lin_CurrentYear' ";
		$lch_Query = $lch_Query . " AND mstmoneytrackersequence.calendar_month = '$lin_CurrentMonth' ";
		$lch_Query = $lch_Query . " AND mstmoneytrackersequence.user_mst_code = '$user_code' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lin_InterimSeriesNo = intval($larr_ResultRow["sequence_no"]);
		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		// IF NOT 0 = JUST INCREMENT IT BY ONE, ELSE CREATE A RECORD WITH 1 AS START
		if ($lin_InterimSeriesNo!=0){
			$lch_Query = "UPDATE mstmoneytrackersequence SET mstmoneytrackersequence.sequence_no = '".($lin_InterimSeriesNo+1)."', ";
			$lch_Query = $lch_Query . " mstmoneytrackersequence.updated_at = NOW() , mstmoneytrackersequence.updated_user_mst_code = '$user_code' ";
			$lch_Query = $lch_Query . " WHERE mstmoneytrackersequence.calendar_year = '$lin_CurrentYear' ";
			$lch_Query = $lch_Query . " AND mstmoneytrackersequence.calendar_month = '$lin_CurrentMonth' ";
			$lch_Query = $lch_Query . " AND mstmoneytrackersequence.user_mst_code = '$user_code' ";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		} // if ($lin_InterimSeriesNo!=0){
		else {
			$lin_InterimSeriesNo = 1;
			$lch_Query = "INSERT INTO mstmoneytrackersequence (";
			$lch_Query = $lch_Query . "calendar_year,calendar_month,user_mst_code,sequence_no,";
			$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
			$lch_Query = $lch_Query . ") VALUES (";
			$lch_Query = $lch_Query . "'$lin_CurrentYear',";
			$lch_Query = $lch_Query . "'$lin_CurrentMonth',";
			$lch_Query = $lch_Query . "'$user_code',";
			$lch_Query = $lch_Query . "'". ($lin_InterimSeriesNo+1) ."',";
			$lch_Query = $lch_Query . "'SYSTEM GENERATED',NOW(),NOW(),'$user_code','$user_code'";
			$lch_Query = $lch_Query . ")";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		} // ELSE ng if ($lin_InterimSeriesNo!=0){
		// END - INTERIM SEQUENCE

		// NEW INTERIM NUMBER HERE:
		$lch_InterimNumber = $lin_CurrentYear . "-" . str_pad($lin_CurrentMonth, 2, "0" ,STR_PAD_LEFT) . "-" . str_pad($lin_InterimSeriesNo, 4, "0" ,STR_PAD_LEFT);

		$lch_Query = "INSERT INTO trnmoneytrail (";
		$lch_Query = $lch_Query . "money_trail_no,money_trail_date,trail_type,money_trail_type_mst_code,description,reference_no,account_money_trail_type_mst_code,is_paid,paid_user_mst_code,paid_at,reference_mst_code,booking_year,booking_period,";
		$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
		$lch_Query = $lch_Query . ") VALUES (";
		$lch_Query = $lch_Query . "'".$lch_InterimNumber."',";
		$lch_Query = $lch_Query . "STR_TO_DATE('".$trnmoneytrail_money_trail_date."','%m-%d-%Y'),";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_money_trail_type."',";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_money_trail_type_mst_code."',";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_description."',";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_reference_no."',";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_account_money_trail_type_mst_code."',";
		$lch_Query = $lch_Query . "'$trnmoneytrail_is_paid',";

		if ($trnmoneytrail_is_paid=="1") {

			$lch_Query = $lch_Query . "'$user_code',";
			$lch_Query = $lch_Query . "NOW(),";

		} // if ($trnmoneytrail_is_paid=="1") {
		else {

			$lch_Query = $lch_Query . "'0',";
			$lch_Query = $lch_Query . "'0000-00-00 00:00:00',";

		} // ELSE ng if ($trnmoneytrail_is_paid=="1") {

		$lch_Query = $lch_Query . "'".$trnmoneytrail_reference_mst_code."',";

		$lch_Query = $lch_Query . "'".$trnmoneytrail_booking_year."',";
		$lch_Query = $lch_Query . "'".$trnmoneytrail_booking_period."',";

		

		$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
		$lch_Query = $lch_Query . ");";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		//array_push($larr_outputArray,error_POST_last());
		if (!$SQL_RESULT){
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Failed to add a new Money Trail."
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // if (!$SQL_RESULT){
		else {

			$lin_InterimInternalCode = mysql_insert_id();

			$larr_ResultQueryArray = array(
				"result" => "1",	
				"error_message" => "Money Trail successfully added.",
				"return_interim_code" => $lin_InterimInternalCode,
				"money_trail_no" => $lch_InterimNumber
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);

			// add details
			$larr_ItemDetailsCodes = @$_POST["trnmoneytraildtlitems_codes_toadd"];

			$lde_totalAmount = 0.00;
			$lin_NoItems = 0;

			if (count($larr_ItemDetailsCodes)>0) {

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$trnmoneytraildtlitems_amount = sanitizeField(str_replace(",","",@$_POST['trnmoneytraildtlitems_amount_'.$lch_Key]));
					$trnmoneytraildtlitems_description = sanitizeField(@$_POST['trnmoneytraildtlitems_description_'.$lch_Key]);

					$lch_Query = "INSERT INTO trnmoneytraildtlitems (";
					$lch_Query = $lch_Query . "money_trail_trn_code,description,amount,";
					$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
					$lch_Query = $lch_Query . ") VALUES (";
					$lch_Query = $lch_Query . "'$lin_InterimInternalCode',";
					$lch_Query = $lch_Query . "'$trnmoneytraildtlitems_description',";
					$lch_Query = $lch_Query . "'$trnmoneytraildtlitems_amount',";
					$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
					$lch_Query = $lch_Query . ");";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($trnmoneytraildtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {

				// update header for the new values
				$lch_Query = "UPDATE trnmoneytrail SET ";
				$lch_Query = $lch_Query . " trnmoneytrail.total_amount = '$lde_totalAmount', ";
				$lch_Query = $lch_Query . " trnmoneytrail.no_of_items = '$lin_NoItems', ";
				$lch_Query = $lch_Query . " trnmoneytrail.updated_at = NOW(), trnmoneytrail.updated_user_mst_code = '$user_code' ";
				$lch_Query = $lch_Query . " WHERE ";
				$lch_Query = $lch_Query . " trnmoneytrail.code = '$lin_InterimInternalCode' ";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			} // if (count($larr_ItemDetailsCodes)>0) {

		} // ELSE ng if (!$SQL_RESULT){


	} // if ($transactionmode=="add_interim"){

	else if ($transactionmode=="process_interim"){

		$lch_InterimNumber = "";
		$lin_InterimInternalCode = 0;

		// RETRIEVE FIRST THE INTERIM POLICY HEADER
		$lch_Query = "SELECT trnmoneytrail.code,trnmoneytrail.money_trail_no FROM trnmoneytrail WHERE trnmoneytrail.code = '".sanitizeField(@$_POST["money_trail_trn_code"])."' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lch_InterimNumber = $larr_ResultRow["money_trail_no"];
			$lin_InterimInternalCode = intval($larr_ResultRow["code"]);
		} // if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			

		if ($lch_InterimNumber!="") {

			$lde_totalAmount = 0.00;
			$lin_NoItems = 0;

			// for existing
			$larr_ItemDetailsCodes = @$_POST["trnmoneytraildtlitems_codes"];
			if (count($larr_ItemDetailsCodes)>0) {

				// delete muna yung wala sa list (baka binura na kasi)
				$lch_Query = "DELETE FROM trnmoneytraildtlitems WHERE ";
				$lch_Query = $lch_Query . " trnmoneytraildtlitems.money_trail_trn_code = '".$lin_InterimInternalCode."' ";
				$lch_Query = $lch_Query . " AND trnmoneytraildtlitems.code NOT IN (".implode(",", $larr_ItemDetailsCodes).")";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$trnmoneytraildtlitems_amount = sanitizeField(str_replace(",","",@$_POST['trnmoneytraildtlitems_amount_'.$lch_Key]));
					$trnmoneytraildtlitems_description = sanitizeField(@$_POST['trnmoneytraildtlitems_description_'.$lch_Key]);

					// edit here baka may gumalaw
					$lch_Query = "UPDATE trnmoneytraildtlitems SET ";
					$lch_Query = $lch_Query . " trnmoneytraildtlitems.description = '$trnmoneytraildtlitems_description', ";
					$lch_Query = $lch_Query . " trnmoneytraildtlitems.amount = '$trnmoneytraildtlitems_amount', ";
					$lch_Query = $lch_Query . " trnmoneytraildtlitems.updated_at = NOW(), trnmoneytraildtlitems.updated_user_mst_code = '$user_code' ";
					$lch_Query = $lch_Query . " WHERE ";
					$lch_Query = $lch_Query . " trnmoneytraildtlitems.money_trail_trn_code = '$lin_InterimInternalCode' ";
					$lch_Query = $lch_Query . " AND trnmoneytraildtlitems.code = '$lch_Key' ";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($trnmoneytraildtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {

				

			} // if (count($larr_ItemDetailsCodes)>0) {

			// if walang laman - delete yung mga laman sa ngayon
			else {

				$lch_Query = "DELETE FROM trnmoneytraildtlitems WHERE ";
				$lch_Query = $lch_Query . " trnmoneytraildtlitems.money_trail_trn_code = '".$lin_InterimInternalCode."' ";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			} // ELSE ng if (count($larr_ItemDetailsCodes)>0) {

			// add details
			$larr_ItemDetailsCodes = @$_POST["trnmoneytraildtlitems_codes_toadd"];
			if (count($larr_ItemDetailsCodes)>0) {

				foreach ($larr_ItemDetailsCodes as $lch_Key) {

					$trnmoneytraildtlitems_amount = sanitizeField(str_replace(",","",@$_POST['trnmoneytraildtlitems_amount_'.$lch_Key]));
					$trnmoneytraildtlitems_description = sanitizeField(@$_POST['trnmoneytraildtlitems_description_'.$lch_Key]);

					$lch_Query = "INSERT INTO trnmoneytraildtlitems (";
					$lch_Query = $lch_Query . "money_trail_trn_code,description,amount,";
					$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
					$lch_Query = $lch_Query . ") VALUES (";
					$lch_Query = $lch_Query . "'$lin_InterimInternalCode',";
					$lch_Query = $lch_Query . "'$trnmoneytraildtlitems_description',";
					$lch_Query = $lch_Query . "'$trnmoneytraildtlitems_amount',";
					$lch_Query = $lch_Query . "'',NOW(),NOW(),'$user_code','$user_code'";
					$lch_Query = $lch_Query . ");";
					$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

					$lin_NoItems++;
					$lde_totalAmount += floatval($trnmoneytraildtlitems_amount);

				} // foreach ($larr_ItemDetailsCodes as $lch_Key) {


			} // if (count($larr_ItemDetailsCodes)>0) {



			$lch_Query = "UPDATE trnmoneytrail SET ";
			$lch_Query = $lch_Query . " trnmoneytrail.money_trail_date = STR_TO_DATE('".$trnmoneytrail_money_trail_date."','%m-%d-%Y'), ";
			$lch_Query = $lch_Query . " trnmoneytrail.trail_type = '$trnmoneytrail_money_trail_type', ";
			$lch_Query = $lch_Query . " trnmoneytrail.money_trail_type_mst_code = '$trnmoneytrail_money_trail_type_mst_code', ";
			$lch_Query = $lch_Query . " trnmoneytrail.description = '$trnmoneytrail_description', ";
			$lch_Query = $lch_Query . " trnmoneytrail.reference_no = '$trnmoneytrail_reference_no', ";
			$lch_Query = $lch_Query . " trnmoneytrail.account_money_trail_type_mst_code = '$trnmoneytrail_account_money_trail_type_mst_code', ";
			$lch_Query = $lch_Query . " trnmoneytrail.reference_mst_code = '$trnmoneytrail_reference_mst_code', ";
			$lch_Query = $lch_Query . " trnmoneytrail.booking_year = '$trnmoneytrail_booking_year', ";
			$lch_Query = $lch_Query . " trnmoneytrail.booking_period = '$trnmoneytrail_booking_period', ";
			$lch_Query = $lch_Query . " trnmoneytrail.is_paid = '$trnmoneytrail_is_paid', ";
			if ($trnmoneytrail_is_paid=="1") {
				$lch_Query = $lch_Query . " trnmoneytrail.paid_user_mst_code = '$user_code',";
				$lch_Query = $lch_Query . " trnmoneytrail.paid_at = NOW(),";
			} // if ($trnmoneytrail_is_paid=="1") {
			else {
				$lch_Query = $lch_Query . " trnmoneytrail.paid_user_mst_code = '0',";
				$lch_Query = $lch_Query . " trnmoneytrail.paid_at = '0000-00-00 00:00:00',";
			} // ELSE ng if ($trnmoneytrail_is_paid=="1") {
			$lch_Query = $lch_Query . " trnmoneytrail.total_amount = '$lde_totalAmount', ";
			$lch_Query = $lch_Query . " trnmoneytrail.no_of_items = '$lin_NoItems', ";
			$lch_Query = $lch_Query . " trnmoneytrail.updated_at = NOW(), trnmoneytrail.updated_user_mst_code = '$user_code' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . " trnmoneytrail.code = '$lin_InterimInternalCode' ";
			$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			if (!$SQL_RESULT){
				$larr_ResultQueryArray = array(
					"result" => "0",	
					"error_message" => "Failed to update Money Trail."
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);
			} // if (!$SQL_RESULT){
			else {

				$larr_ResultQueryArray = array(
					"result" => "1",	
					"error_message" => "Money Trail successfully updated.",
					"return_interim_code" => $lin_InterimInternalCode,
					"money_trail_no" => $lch_InterimNumber
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);

			} // ELSE ng if (!$SQL_RESULT){



		} // if ($lch_InterimNumber!="") {
		else {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Fatal Error. Selected Money Trail not found. (It might be already deleted in the database by some other user.)"
			);
			array_push($larr_outputArray,$larr_ResultQueryArray);
		} // ELSE ng if ($lch_InterimNumber!="") {


	} // if ($transactionmode=="process_interim"){

	else if ($transactionmode=="delete_interim"){

		$lch_InterimNumber = "";

		// RETRIEVE FIRST THE INTERIM POLICY HEADER
		$lch_Query = "SELECT trnmoneytrail.code,trnmoneytrail.money_trail_no FROM trnmoneytrail WHERE trnmoneytrail.code = '".sanitizeField(@$_POST["money_trail_trn_code"])."' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lch_InterimNumber = $larr_ResultRow["money_trail_no"];

		} // if($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			

		if ($lch_InterimNumber!="") {

			$lch_Query = "DELETE FROM trnmoneytraildtlitems WHERE trnmoneytraildtlitems.money_trail_trn_code = '".sanitizeField(@$_POST["money_trail_trn_code"])."'";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

			$lch_Query = "DELETE FROM trnmoneytrail WHERE trnmoneytrail.code = '".sanitizeField(@$_POST["money_trail_trn_code"])."'";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

			if (!$SQL_RESULT){
				$larr_ResultQueryArray = array(
					"result" => "0",	
					"error_message" => "Failed to delete the selected money trail."
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);
			} // if (!$SQL_RESULT){
			else {

				$larr_ResultQueryArray = array(
					"result" => "1",	
					"error_message" => "Selected Money Trail successfully deleted.",
					"money_trail_no" => $lch_InterimNumber
				);
				array_push($larr_outputArray,$larr_ResultQueryArray);

			} // ELSE ng if (!$SQL_RESULT){
		} // if ($lch_InterimNumber!="") {
		else {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Fatal Error. Selected Money Trail not found. (It might be already deleted in the database by some other user.)"
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