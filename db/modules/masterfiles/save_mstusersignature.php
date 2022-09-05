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
	$lch_TableName = "dtlusersignature";

	//ob_start();
	// USER MST CODE
	$lch_UserMSTCode = sanitizeField($_POST['user_mst_code']);
	$lch_InputUserMSTCode = sanitizeField($_POST['input_user_code']);

	$llo_UpdAddSomeRecords = false;

	// PROFILE PIC SECTION
	$lch_PictureData = base64_decode(str_pad(strtr($_POST["user-signature"]["data"], '-_', '+/'), strlen($_POST["user-signature"]["data"]) % 4, '=', STR_PAD_RIGHT));
	$lch_PictureData = mysql_real_escape_string ($lch_PictureData);

	$lch_PictureName = mysql_real_escape_string ($_POST["user-signature"]["name"]);
	$lch_PictureType = mysql_real_escape_string ($_POST["user-signature"]["type"]);

	
	// delete or just update blob here.. depends -- tanong muna
	$lch_Query = "DELETE FROM $lch_TableName WHERE $lch_TableName.user_mst_code = '$lch_InputUserMSTCode'";
	$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	// no need to check if success or failed

	// SA NGAYON, PURO ADD IMAGE MUNA, I-UPDATE NA LANG YUNG CODE SA USERMST PARA SA MAIN PICTURE.
	$lch_Query = "INSERT INTO $lch_TableName  (image,image_name,filetype,user_mst_code,uploaded_at,uploaded_user_mst_code,created_at,updated_at,created_user_mst_code,updated_user_mst_code) VALUES ";
	$lch_Query = $lch_Query . " ('$lch_PictureData','$lch_PictureName','$lch_PictureType','$lch_InputUserMSTCode',NOW(),'$lch_UserMSTCode',NOW(),NOW(),'$lch_UserMSTCode','$lch_UserMSTCode') ";
	$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	if (!$SQL_RESULT){

	} // if (!$SQL_RESULT){
	else {
		$lin_imagecode = mysql_insert_id();
		
		$l_access2 =  $lch_dbAccess2->connectDB(MONEYTRACKER_DB);

		$lch_Query = "UPDATE mstuser SET mstuser.user_signature_image_code='$lin_imagecode',updated_at=NOW(),updated_user_mst_code='$lch_UserMSTCode' WHERE mstuser.code='$lch_InputUserMSTCode' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());


		$llo_UpdAddSomeRecords = true;
	} // ELSE ng if (!$SQL_RESULT){
	

	//$result2 =  ob_get_clean();

	if ($llo_UpdAddSomeRecords){
		$larr_ResultQueryArray = array(
			"result" => "1",
			"error_message" => "Changes saved successfully. Please refresh/reload the page if it did not update."
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
$lch_dbAccess2->closeCon();
echo json_encode( $larr_OutputArray);
?>