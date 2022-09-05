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
	/* PARAMETERS HERE */
	// DATABASE NAME
	$lch_db = MONEYTRACKER_DB;
	$l_access =  $lch_dbAccess->connectDB($lch_db);

	// ASSIGN TABLE NAME HERE
	$lch_TableName = "mstuser";

	//ob_start();
	// USER MST CODE
	$lch_UserMSTCode = sanitizeField($_POST['user_mst_code']);
	$lch_InputUserMSTCode = sanitizeField($_POST['input_user_code']);
	$lch_PasswordToReset = sanitizeField($_POST['password_input']);
	$lch_PasswordToReset = base64_encode(md5($lch_PasswordToReset));


	$lch_Query = "UPDATE $lch_TableName SET $lch_TableName.password='$lch_PasswordToReset',$lch_TableName.updated_at=NOW(),$lch_TableName.updated_user_mst_code='$lch_UserMSTCode' WHERE $lch_TableName.code='$lch_InputUserMSTCode' ";
	$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	if (!$SQL_RESULT){

	} // if (!$SQL_RESULT){
	else {
		$llo_UpdAddSomeRecords = true;
	} // ELSE ng if (!$SQL_RESULT){
	



	if ($llo_UpdAddSomeRecords){
		$larr_ResultQueryArray = array(
			"result" => "1",
			"error_message" => "Password Successfully Reset"
		);
	} // if ($llo_UpdAddSomeRecords){
	else {
		$larr_ResultQueryArray = array(
			"result" => "0",
			"error_message" => "There are no records to update."
		);
	} // ELSE ng if ($llo_UpdAddSomeRecords){
	array_push($larr_OutputArray,$larr_ResultQueryArray);
	

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