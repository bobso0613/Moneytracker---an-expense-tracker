<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../../api/DatabaseConnect.php");
@include_once("../../api/SanitizeField.php");
$lch_dbAccess = new DatabaseAccess();
$lch_dbAccess2 = new DatabaseAccess();
$larr_OutputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// COMMON FOR ALL
	$transactionmode = sanitizeField(@$_POST['transactionmode']);
	
	$user_code = sanitizeField(@$_POST['user_code']);


	if ($transactionmode=="save_mstuser"){

		//$lch_PasswordToReset = sanitizeField($_POST['password_input']);
		//$lch_PasswordToReset = base64_encode(md5($lch_PasswordToReset));

		$lch_CurrentPassword = "";
		$larr_MSTUser = array();
		$lch_NewWholeName = "";

		$mstuser_code = sanitizeField(@$_POST['mstuser_code']);

		$mstuser_current_password1 = sanitizeField(@$_POST['mstuser_current_password']);
		$mstuser_current_password = base64_encode(md5($mstuser_current_password1));

		$mstuser_new_password2 = sanitizeField(@$_POST['mstuser_new_password']);
		$mstuser_new_password = base64_encode(md5($mstuser_new_password2));

		$mstuser_retype_new_password = sanitizeField(@$_POST['mstuser_retype_new_password']);
		$mstuser_first_name = sanitizeField(@$_POST['mstuser_first_name']);
		$mstuser_middle_name = sanitizeField(@$_POST['mstuser_middle_name']);
		$mstuser_last_name = sanitizeField(@$_POST['mstuser_last_name']);
		$mstuser_email_address = sanitizeField(@$_POST['mstuser_email_address']);
		$mstuser_phone_number = sanitizeField(@$_POST['mstuser_phone_number']);
		$mstuser_cellphone_number = sanitizeField(@$_POST['mstuser_cellphone_number']);
		$mstuser_nickname = sanitizeField(@$_POST['mstuser_nickname']);


		$lch_SlugLink = "";

		$lch_username = "";

		// retrieve user records
		$lch_dbAccess = new DatabaseAccess();
		$lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstuser.* FROM mstuser WHERE mstuser.code = '$mstuser_code'";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lch_CurrentPassword = $larr_ResultRow["password"];
			//$larr_MSTUser = $larr_ResultRow;
			$lch_username = $larr_ResultRow["username"];
		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		if (($lch_CurrentPassword==$mstuser_current_password &&
			$lch_CurrentPassword!=$mstuser_new_password) || 
			($mstuser_current_password1==""&&$mstuser_new_password2==""&&$mstuser_retype_new_password=="")){
			// continue saving
			$lch_NewWholeName = $lch_NewWholeName . $mstuser_first_name;
			if ($mstuser_middle_name!=""){
				$lch_NewWholeName = $lch_NewWholeName . " " . $mstuser_middle_name;
			}
			$lch_NewWholeName = $lch_NewWholeName . " " . $mstuser_last_name;

			$lch_SlugLink .= slugify($lch_NewWholeName." ".$mstuser_code);

			// actual update
			$lch_dbAccess = new DatabaseAccess();
			$lch_dbAccess->connectDB(MONEYTRACKER_DB);
			$lch_Query = "UPDATE mstuser SET ";
			$lch_Query = $lch_Query . " mstuser.first_name = '$mstuser_first_name', ";
			$lch_Query = $lch_Query . " mstuser.middle_name = '$mstuser_middle_name', ";
			$lch_Query = $lch_Query . " mstuser.last_name = '$mstuser_last_name', ";
			$lch_Query = $lch_Query . " mstuser.whole_name = '$lch_NewWholeName', ";
			$lch_Query = $lch_Query . " mstuser.email_address = '$mstuser_email_address', ";
			$lch_Query = $lch_Query . " mstuser.phone_number = '$mstuser_phone_number', ";
			$lch_Query = $lch_Query . " mstuser.cellphone_number = '$mstuser_cellphone_number', ";
			$lch_Query = $lch_Query . " mstuser.nickname = '$mstuser_nickname', ";
			$lch_Query = $lch_Query . " mstuser.profile_slug_link = '$lch_SlugLink', ";
			if (($mstuser_current_password1!=""||$mstuser_new_password2!=""||$mstuser_retype_new_password!="")) {
				$lch_Query = $lch_Query . " mstuser.password = '$mstuser_new_password', ";
			} // if (($mstuser_current_password1!=""||$mstuser_new_password2!=""||$mstuser_retype_new_password!="")) {
			$lch_Query = $lch_Query . " mstuser.updated_at = NOW() , mstuser.updated_user_mst_code = '$user_code' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . " mstuser.code = '$mstuser_code' ";

			//echo $lch_Query;

			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

			if (!$SQL_RESULT){
				$larr_ResultQueryArray = array(
					"result" => "0",	
					"error_message" => "Failed to update user details."
				);
				array_push($larr_OutputArray,$larr_ResultQueryArray);
			} // if (!$SQL_RESULT){
			else {
				$larr_ResultQueryArray = array(
					"result" => "1",	
					"error_message" => "User details successfully updated."
				);
				array_push($larr_OutputArray,$larr_ResultQueryArray);

			
			} // ELSE ng if (!$SQL_RESULT){


		} // if ($lch_CurrentPassword==$mstuser_current_password &&
		//	$lch_CurrentPassword!=$mstuser_new_password){
		else if ($lch_CurrentPassword!=$mstuser_current_password) {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Incorrect Current Password."	
			);
			array_push($larr_OutputArray,$larr_ResultQueryArray);
		} // else if ($lch_CurrentPassword!=$mstuser_current_password) {
		else if ($lch_CurrentPassword==$mstuser_new_password) {
			$larr_ResultQueryArray = array(
				"result" => "0",	
				"error_message" => "Current Password and New Password should not be the same."
			);
			array_push($larr_OutputArray,$larr_ResultQueryArray);
		} // else if ($lch_CurrentPassword==$mstuser_new_password) {

	} // if ($transactionmode=="save_mstuser"){

	else {
		// NO ACTION SPECIFIED
		$larr_ResultQueryArray = array(
			"result" => "0",	
			"error_message" => "Please specify an action."
		);
		array_push($larr_OutputArray,$larr_ResultQueryArray);
	} // ELSE ng else if ($transactionmode=="can_delete"){
	
	

}
else {
	// illegal access error
	$larr_ResultQueryArray = array(
		"result" => "0",	
		"error_message" => "Invalid API Access."
	);
	array_push($larr_OutputArray,$larr_ResultQueryArray);
}

$lch_dbAccess->closeCon();
echo json_encode( $larr_OutputArray);
?>