<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../api/DatabaseConnect.php");
@include_once("../api/SanitizeField.php");

$larr_OutputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	

	$lch_dbAccess = new DatabaseAccess();
	$l_access =  $lch_dbAccess->connectDB(MONEYTRACKER_DB);

	/* POST FIRST SYSTEM SETTINGS PARAMETER - TO CHECK IF NAKA ENABLE BA YUNG AUDIT TRAIL FEATURE */
	// $lch_Query = "SELECT mstsystemsettings.is_audit_trail_enabled FROM mstsystemsettings LIMIT 0,1";
	// $SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	// // assumes 1 record lang
	// $larr_SystemSettings=mysql_fetch_array($SQL_RESULT);
	// if ($larr_SystemSettings["is_audit_trail_enabled"]=="1"){

		// if pwede mag audit trail, proceed with process
		// get module action codes via parameters
		$lch_dbAccess->closeCon();
		$lch_dbAccess = new DatabaseAccess();
		$l_access =  $lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstapplicationparameter.parameter_key,mstapplicationparameter.parameter_value FROM mstapplicationparameter WHERE mstapplicationparameter.parameter_key IN ('module_access_code','module_action_add_code','module_action_edit_code','module_action_delete_code','module_action_print_code','module_upload_action_code') ORDER BY mstapplicationparameter.code ASC ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$larr_ModuleActions = array();
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$larr_ModuleActions[$larr_ResultRow["parameter_key"]] = $larr_ResultRow["parameter_value"];
		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		$lch_dbAccess->closeCon();


		/* ACTUAL ADDING OF AUDIT TRAIL ENTRY */
		
		$lch_tableName = sanitizeField($_POST["tableName"]);
		$lch_ModuleMSTCode = sanitizeField($_POST["module_mst_code"]);
		$lch_MenuItemMSTCode = sanitizeField($_POST["menu_item_mst_code"]);
		$lch_Reference = sanitizeField ($_POST["reference"]);
		$lch_DescriptionFormat = sanitizeField($_POST["description_format"]);
		$lch_Action = sanitizeField($_POST["action"]);
		$lch_UserMSTCode = sanitizeField($_POST["user_mst_code"]);
		if ($lch_Action=="can_add"){
			$lch_Action="module_action_add_code";
		} // if ($lch_Action=="can_add"){
		else if ($lch_Action=="can_edit"){
			$lch_Action="module_action_edit_code";
		} // if ($lch_Action=="can_edit"){
		else if ($lch_Action=="can_delete"){
			$lch_Action="module_action_delete_code";
		} // if ($lch_Action=="can_delete"){
		else if ($lch_Action=="can_print"){
			$lch_Action="module_action_print_code";
		} // if ($lch_Action=="can_print"){
		else if ($lch_Action=="can_upload"){
			$lch_Action="module_upload_action_code";
		} // if ($lch_Action=="can_upload"){


		// RETRIEVE MODULE
		$lch_dbAccess = new DatabaseAccess();
		$l_access =  $lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstmodule.* FROM mstmodule WHERE mstmodule.code = '$lch_ModuleMSTCode' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$larr_Module=mysql_fetch_array($SQL_RESULT);
		$lch_dbAccess->closeCon();


		// RETRIEVE USER
		$lch_dbAccess = new DatabaseAccess();
		$l_access =  $lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstuser.* FROM mstuser WHERE mstuser.code = '$lch_UserMSTCode' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$larr_User=mysql_fetch_array($SQL_RESULT);
		$lch_dbAccess->closeCon();

		// RETRIEVE MODULE ACTION
		$lch_dbAccess = new DatabaseAccess();
		$l_access =  $lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstmoduleaction.* FROM mstmoduleaction WHERE mstmoduleaction.code = '".$larr_ModuleActions[$lch_Action]."' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$larr_ModuleActionDetail=mysql_fetch_array($SQL_RESULT);
		$lch_dbAccess->closeCon();


		// replace format
		$larr_Replacement = array ("@user_whole_name"=>$larr_User["whole_name"],
							"@module_action_name"=>$larr_ModuleActionDetail["action_name"],
							"@module_name"=>$larr_Module["module_name"]);

		$lch_DescriptionFormat = strtr($lch_DescriptionFormat, $larr_Replacement);

		// INSERT AUDIT TRAIL
		$lch_dbAccess = new DatabaseAccess();
		$l_access =  $lch_dbAccess->connectDB($_POST["dbconnect"]);
		$lch_Query = "INSERT INTO $lch_tableName ($lch_tableName.module_mst_code,$lch_tableName.module_action_mst_code,$lch_tableName.menu_item_mst_code,$lch_tableName.action_user_mst_code,$lch_tableName.reference,$lch_tableName.description,$lch_tableName.created_at,$lch_tableName.updated_at,$lch_tableName.created_user_mst_code,$lch_tableName.updated_user_mst_code) VALUES (";
		$lch_Query = $lch_Query . "'".$larr_Module["code"]."',";
		$lch_Query = $lch_Query . "'".$larr_ModuleActions[$lch_Action]."',";
		$lch_Query = $lch_Query . "'".$lch_MenuItemMSTCode."',";
		$lch_Query = $lch_Query . "'".$lch_UserMSTCode."',";
		$lch_Query = $lch_Query . "'".$lch_Reference."',";
		$lch_Query = $lch_Query . "'".$lch_DescriptionFormat."',";
		$lch_Query = $lch_Query . "NOW(),NOW(),'".$lch_UserMSTCode."','".$lch_UserMSTCode."')";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$lch_dbAccess->closeCon();
		/* END - ACTUAL ADDING OF AUDIT TRAIL ENTRY */

		/*
			$link = DB_LOCATION;
			$params = array (
				"action" => $_POST["transactionmode"],
				"fileToOpen" => "save_audit_trail",
				"tableName" => "trnaudittrail",
				"dbconnect" => IISAAC_SYSTEM_DB,
				"module_mst_code" => $_POST["modulemstcode"],
				"menu_item_mst_code" => $_POST["menuitemmstcode"],
				"user_mst_code" => $_SESSION["user_code"],
				"reference" => $_POST["valueviewed"],
				"description_format" => "User @user_whole_name has @module_action_description Module @module_name."
			);
			$result=processCurl($link,$params);

		*/


	// } // if ($larr_SystemSettings["is_audit_trail_enabled"]=="1"){

} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	// illegal access error
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {
?>