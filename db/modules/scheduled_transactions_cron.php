<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// $lch_mode = "dev";
$lch_mode = "production";

date_default_timezone_set('Asia/Manila');


function logMe($path,$filename,$data){
    
    $file = $path.$filename;
    $string = "";
    
    /** Check if File Exist then copy existing data except the current user data **/
    if(file_exists($file)){
        
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $string.=$line;
            }
            fclose($handle);
            $string.="\n";
        } 
    } 
    
    /** Create new file **/
    $myfile = fopen($file, "w")or die("Unable to open file!");
    
    /** Write data **/
    fwrite($myfile, $string.$data);
    
    fclose($myfile);
} // function logMe($path,$filename,$data){

// $lda_diff = date("Y-m-d h:i:s");
// $lda_diff2 = date_format(date_create("2017-07-02 23:35:22"),"Y-m-d");

// echo get_date_difference($lda_diff,$lda_diff2);

if (php_sapi_name() == "cli") {

	@include_once("/home/bd926c5/public_html/moneytracker.22infinity.com.ph/db/api/DatabaseConnect.php");
	@include_once("/home/bd926c5/public_html/moneytracker.22infinity.com.ph/db/api/SanitizeField.php");
	@include_once("/home/bd926c5/public_html/moneytracker.22infinity.com.ph/db/api/SlugGenerator.php");
	// @include_once("../api/DatabaseConnect.php");
	// @include_once("../api/SanitizeField.php");
	// @include_once("../api/SlugGenerator.php");
	

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);

	// file name
	// $lch_filename = $argv[0];
	$lch_filename = "scheduled_transaction_cron.php";


	$lch_FilePath = "/home/bd926c5/public_html/moneytracker.22infinity.com.ph/";
	$lch_LogFileName = "scheduled_transaction_log".date("Y-m-F").".txt";
	$lch_FileLog = "";

	$larr_Months = array("1"=>"January",
		"2"=>"February",
		"3"=>"March",
		"4"=>"April",
		"5"=>"May",
		"6"=>"June",
		"7"=>"July",
		"8"=>"August",
		"9"=>"September",
		"10"=>"October",
		"11"=>"November",
		"12"=>"December");

	$larr_Frequency = array("0"=>"Once",
		"1"=>"Daily",
		"2"=>"Weekly",
		"3"=>"Bi-Monthly",
		"4"=>"Monthly",
		"5"=>"Annually");

	$larr_FrequencyNoOfDays = array("0"=>0,
		"1"=>1,
		"2"=>7,
		"3"=>15,
		"4"=>30,
		"5"=>365);


	$from_date_cron = time();

	$lch_FileLog .= "=====================================\n";
	$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] CRON Job " . $lch_filename . " started.\n";

	$lda_DateNow = date("Y-m-d");

	$larr_ToProcess = array();

	
	$lch_Query = "SELECT mstscheduledtransactions.* "
			. " FROM mstscheduledtransactions "
			. " INNER JOIN mstmoneytrailtype trailtype ON mstscheduledtransactions.money_trail_type_mst_code = trailtype.code "
			. " LEFT JOIN mstmoneytrailtype accountdeduct ON mstscheduledtransactions.account_money_trail_type_mst_code = accountdeduct.code "
			. " LEFT JOIN mstreference refe ON mstscheduledtransactions.reference_mst_code = refe.code "
			. " WHERE "
			. " mstscheduledtransactions.is_active = 1 "
			. " AND mstscheduledtransactions.current_process_count < mstscheduledtransactions.maximum_process_count "
			. " AND mstscheduledtransactions.start_date <= '$lda_DateNow' "
			. " ORDER BY mstscheduledtransactions.start_date ASC " ;
	// echo $lch_Query;
	$SQL_RESULT_SCHEDULEDTRANS=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT_SCHEDULEDTRANS)) {

		$lda_DateNowPlusDays = "";

		if($larr_ResultRow["previous_run_date"]=="0000-00-00") {
			$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " (".number_format($larr_ResultRow["total_amount"],2).")" . " No previous run yet. Process this\n";
			array_push($larr_ToProcess,$larr_ResultRow);
		} // if($larr_ResultRow["previous_run_date"]=="0000-00-00") {
		else {
			if ($larr_ResultRow["frequency"]==0&&$larr_ResultRow["current_process_count"]==0) {
				$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " (".number_format($larr_ResultRow["total_amount"],2).")" . " Frequency 'Once' and no process yet. Process this\n";
				array_push($larr_ToProcess,$larr_ResultRow);
			} // if ($larr_ResultRow["frequency"]==0&&$larr_ResultRow["current_process_count"]==0) {
			else {
				$lda_DateNowPlusDays = date('Y-m-d', strtotime($larr_ResultRow["previous_run_date"]. ' + '.$larr_FrequencyNoOfDays[$larr_ResultRow["frequency"]].' days'));

				$lda_DateNowTime = strtotime($lda_DateNow);
				$lda_DateNowPlusDaysTime = strtotime($lda_DateNowPlusDays);
				$lin_DateGap = ceil(abs($lda_DateNowTime - $lda_DateNowPlusDaysTime) / 86400);
				if ($lda_DateNowTime>=$lda_DateNowPlusDaysTime) {
					$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " (".number_format($larr_ResultRow["total_amount"],2).")" . " Frequency '".$larr_Frequency[$larr_ResultRow["frequency"]]."' Today's date and Previous date gap is already qualified for processing ($lin_DateGap days) ".$lda_DateNow." - ".$lda_DateNowPlusDays." . Process this\n";
					array_push($larr_ToProcess,$larr_ResultRow);
				} // if ($lda_DateNowTime>=$lda_DateNowPlusDaysTime) {
				else {
					// $lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " Frequency '".$larr_Frequency[$larr_ResultRow["frequency"]]."'. Today's date and Previous date gap is not yet ".$larr_FrequencyNoOfDays[$larr_ResultRow["frequency"]]." days ($lin_DateGap days more). ".$lda_DateNow." - ".$lda_DateNowPlusDays.". Dont Process this \n";
				} // ELSE ng if ($lda_DateNowTime>=$lda_DateNowPlusDaysTime) {

				// $lch_FileLog .= $larr_ResultRow["template_name"] . " - " . $lda_DateNow . " - " . $lda_DateNowPlusDays . " - "  .$lda_DateNowTime . " - " . $lda_DateNowPlusDaysTime . "\n";
			} // ELSE ng if ($larr_ResultRow["frequency"]==0&&$larr_ResultRow["current_process_count"]==0) {
			
		} // ELSE ng if($larr_ResultRow["previous_run_date"]=="0000-00-00") {

	} // while($larr_ResultRow=mysql_fetch_assoc($SQL_RESULT_SCHEDULEDTRANS)) {

	if (count($larr_ToProcess)>0) {
		foreach ($larr_ToProcess as $larr_ResultRow) {
			$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " (".number_format($larr_ResultRow["total_amount"],2).")" . " Make Auto Entry \n";

			$user_code = $larr_ResultRow["created_user_mst_code"];

			$lin_CurrentYear = date("Y");
			$lin_CurrentMonth = date("m");

			$trnmoneytrail_booking_year = 0;
			$trnmoneytrail_booking_period = 0;

			if ($larr_ResultRow["booking_period_mode"]=="0") {

				$trnmoneytrail_booking_year = date("Y");
				$trnmoneytrail_booking_period = date("m");
				
			} // if ($larr_ResultRow["booking_period_mode"]=="0") {
			else if ($larr_ResultRow["booking_period_mode"]=="1") {

				$trnmoneytrail_booking_year = date('Y', strtotime(date("Y-m-d").' + 1 month'));
				$trnmoneytrail_booking_period = date('m', strtotime(date("Y-m-d").' + 1 month'));

			} // else if ($larr_ResultRow["booking_period_mode"]=="1") {
			else if ($larr_ResultRow["booking_period_mode"]=="2") {

				$trnmoneytrail_booking_year = date('Y', strtotime(date("Y-m-d").' - 1 month'));
				$trnmoneytrail_booking_period = date('m', strtotime(date("Y-m-d").' - 1 month'));

			} // else if ($larr_ResultRow["booking_period_mode"]=="2") {
			else if ($larr_ResultRow["booking_period_mode"]=="3") {
				$trnmoneytrail_booking_year = $larr_ResultRow["booking_year"];
				$trnmoneytrail_booking_period = $larr_ResultRow["booking_period"];
			} // else if ($larr_ResultRow["booking_period_mode"]=="3") {

			if ($larr_ResultRow["previous_run_date"]!="0000-00-00") {
				$lda_DateNowPlusDays = date('Y-m-d', strtotime($larr_ResultRow["previous_run_date"]. ' + '.$larr_FrequencyNoOfDays[$larr_ResultRow["frequency"]].' days'));
			} // if ($larr_ResultRow["previous_run_date"]!="0000-00-00") {
			else {
				$lda_DateNowPlusDays = $larr_ResultRow["start_date"];
			} // ELSE ng if ($larr_ResultRow["previous_run_date"]!="0000-00-00") {
			


			$lin_currentprocesscountadd = $larr_ResultRow["current_process_count"] + 1;

			/*
			@current_process_count
			@maximum_process_count 

			*/
			$larr_ResultRow["template_pattern_formatted"] = $larr_ResultRow["template_pattern"];
			$larr_ResultRow["template_pattern_formatted"] = str_replace("@current_process_count",$lin_currentprocesscountadd,$larr_ResultRow["template_pattern_formatted"]);
			$larr_ResultRow["template_pattern_formatted"] = str_replace("@maximum_process_count",$larr_ResultRow["maximum_process_count"],$larr_ResultRow["template_pattern_formatted"]);
			$larr_ResultRow["template_pattern_formatted"] = str_replace("@month_name",$larr_Months[intval($lin_CurrentMonth).""],$larr_ResultRow["template_pattern_formatted"]);
			$larr_ResultRow["template_pattern_formatted"] = str_replace("@year",$lin_CurrentYear,$larr_ResultRow["template_pattern_formatted"]);

			$lin_InterimSeriesNo = 0;
			$lch_InterimNumber = "";

			$lch_Query = "SELECT mstmoneytrackersequence.* FROM mstmoneytrackersequence WHERE mstmoneytrackersequence.calendar_year = '$lin_CurrentYear' ";
			$lch_Query = $lch_Query . " AND mstmoneytrackersequence.calendar_month = '$lin_CurrentMonth' ";
			$lch_Query = $lch_Query . " AND mstmoneytrackersequence.user_mst_code = '$user_code' ";
			$SQL_RESULT_UPDATESERIES=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRowSeries=mysql_fetch_array($SQL_RESULT_UPDATESERIES)) {
				$lin_InterimSeriesNo = intval($larr_ResultRowSeries["sequence_no"]);
			} // while($larr_ResultRowSeries=mysql_fetch_array($SQL_RESULT_UPDATE)) {
			// IF NOT 0 = JUST INCREMENT IT BY ONE, ELSE CREATE A RECORD WITH 1 AS START
			if ($lin_InterimSeriesNo!=0){
				$lch_Query = "UPDATE mstmoneytrackersequence SET mstmoneytrackersequence.sequence_no = '".($lin_InterimSeriesNo+1)."', ";
				$lch_Query = $lch_Query . " mstmoneytrackersequence.updated_at = NOW() , mstmoneytrackersequence.updated_user_mst_code = '$user_code' ";
				$lch_Query = $lch_Query . " WHERE mstmoneytrackersequence.calendar_year = '$lin_CurrentYear' ";
				$lch_Query = $lch_Query . " AND mstmoneytrackersequence.calendar_month = '$lin_CurrentMonth' ";
				$lch_Query = $lch_Query . " AND mstmoneytrackersequence.user_mst_code = '$user_code' ";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());
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
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());
			} // ELSE ng if ($lin_InterimSeriesNo!=0){
			// END - INTERIM SEQUENCE

			// NEW INTERIM NUMBER HERE:
			$lch_InterimNumber = $lin_CurrentYear . "-" . str_pad($lin_CurrentMonth, 2, "0" ,STR_PAD_LEFT) . "-" . str_pad($lin_InterimSeriesNo, 4, "0" ,STR_PAD_LEFT);

			$lch_Query = "INSERT INTO trnmoneytrail (";
			$lch_Query = $lch_Query . "money_trail_no,money_trail_date,trail_type,money_trail_type_mst_code,description,reference_no,account_money_trail_type_mst_code,is_paid,paid_user_mst_code,paid_at,reference_mst_code,booking_year,booking_period,";
			$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
			$lch_Query = $lch_Query . ") VALUES (";
			$lch_Query = $lch_Query . "'".$lch_InterimNumber."',";
			$lch_Query = $lch_Query . "'".$lda_DateNowPlusDays."',";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["trail_type"])."',";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["money_trail_type_mst_code"])."',";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["template_pattern_formatted"])."',";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["reference_no"])."',";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["account_money_trail_type_mst_code"])."',";
			$lch_Query = $lch_Query . "'1',";
			$lch_Query = $lch_Query . "'$user_code',";
			$lch_Query = $lch_Query . "NOW(),";
			$lch_Query = $lch_Query . "'".sanitizeField($larr_ResultRow["reference_mst_code"])."',";
			$lch_Query = $lch_Query . "'".$trnmoneytrail_booking_year."',";
			$lch_Query = $lch_Query . "'".$trnmoneytrail_booking_period."',";
			$lch_Query = $lch_Query . "'From Scheduled Transactions CRON Job ".date("h:i:s a m/d/y")." - ".sanitizeField($larr_ResultRow["template_name"])."',NOW(),NOW(),'$user_code','$user_code'";
			$lch_Query = $lch_Query . ");";
			$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());


			$lin_InterimInternalCode = mysql_insert_id();

			$lde_totalAmount = 0.00;
			$lin_NoItems = 0;

			$lch_Query = "SELECT mstscheduledtransactionsdtlitems.* "
					. " FROM mstscheduledtransactionsdtlitems "
					. " WHERE mstscheduledtransactionsdtlitems.scheduled_transactions_mst_code = '".$larr_ResultRow["code"]."' "
					. " ORDER BY mstscheduledtransactionsdtlitems.code ASC ";
			$SQL_RESULT_SCHEDULEDTRANSITEMS=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRowTRANSITEMS=mysql_fetch_assoc($SQL_RESULT_SCHEDULEDTRANSITEMS)) {

				$lch_Query = "INSERT INTO trnmoneytraildtlitems (";
				$lch_Query = $lch_Query . "money_trail_trn_code,description,amount,";
				$lch_Query = $lch_Query . "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code";
				$lch_Query = $lch_Query . ") VALUES (";
				$lch_Query = $lch_Query . "'$lin_InterimInternalCode',";
				$lch_Query = $lch_Query . "'".$larr_ResultRowTRANSITEMS["description"]."',";
				$lch_Query = $lch_Query . "'".$larr_ResultRowTRANSITEMS["amount"]."',";
				$lch_Query = $lch_Query . "'From Scheduled Transactions CRON Job ".date("h:i:s a m/d/y")." - ".sanitizeField($larr_ResultRow["template_name"])."',NOW(),NOW(),'$user_code','$user_code'";
				$lch_Query = $lch_Query . ");";
				$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

				$lin_NoItems++;
				$lde_totalAmount += floatval($larr_ResultRowTRANSITEMS["amount"]);

			} // while($larr_ResultRowTRANSITEMS=mysql_fetch_assoc($SQL_RESULT_SCHEDULEDTRANSITEMS)) {

			// update header for the new values
			$lch_Query = "UPDATE trnmoneytrail SET ";
			$lch_Query = $lch_Query . " trnmoneytrail.total_amount = '$lde_totalAmount', ";
			$lch_Query = $lch_Query . " trnmoneytrail.no_of_items = '$lin_NoItems', ";
			$lch_Query = $lch_Query . " trnmoneytrail.updated_at = NOW(), trnmoneytrail.updated_user_mst_code = '$user_code' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . " trnmoneytrail.code = '$lin_InterimInternalCode' ";
			$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			// update scheduled transaction
			$lch_Query = "UPDATE mstscheduledtransactions SET ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.current_process_count = '$lin_currentprocesscountadd', ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.previous_run_date = '$lda_DateNowPlusDays', ";

			// if equal na, tag as inactive na
			if ($lin_currentprocesscountadd==$larr_ResultRow["maximum_process_count"]) {
				$lch_Query = $lch_Query . " mstscheduledtransactions.is_active = '0', ";
			} // if ($lin_currentprocesscountadd==$larr_ResultRow["maximum_process_count"]) {

			$lch_Query = $lch_Query . " mstscheduledtransactions.updated_at = NOW(), mstscheduledtransactions.updated_user_mst_code = '2' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . " mstscheduledtransactions.code = '".$larr_ResultRow["code"]."'; ";
			$SQL_RESULT_UPDATE=mysql_query($lch_Query) or die(mysql_error());

			$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] ".$larr_ResultRow["template_name"] . " (".number_format($larr_ResultRow["total_amount"],2).")" . " Done - Transaction number ".$lch_InterimNumber." \n";


		} // foreach ($larr_ToProcess as $larr_ResultRow) {
	} // if (count($larr_ToProcess)>0) {
	else {
		$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] There are no scheduled transactions to process today. \n";
	} // ELSE ng if (count($larr_ToProcess)>0) {



	$to_date_cron = time();
	$seconds_cron = round(($to_date_cron - $from_date_cron) ,5);

	$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] CRON Job " . $lch_filename . " finished.\n";
	$lch_FileLog .= "[".date("h:i:s a m/d/y") . "] Duration: ".number_format($seconds_cron,5,'.','')." seconds.\n";
	$lch_FileLog .= "=====================================";

	echo $lch_FileLog;
	if ($lch_mode!="dev"){
		logMe($lch_FilePath,$lch_LogFileName,$lch_FileLog);
	} // if ($lch_mode!="dev"){
	


	$lch_dbAccess->closeCon();
	$lch_dbAccess=null;
	exit;

} // if (php_sapi_name() == "cli") {
else {
	echo 'This script not allowed for browser usage.';
	exit;
} // ELSE ng if (php_sapi_name() == "cli") {

?>