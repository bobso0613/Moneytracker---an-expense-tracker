<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../../api/DatabaseConnect.php");
@include_once("../../api/SanitizeField.php");
$lch_dbAccess = new DatabaseAccess();
$larr_OutputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	/* PARAMETERS HERE */
	// DATABASE NAME
	$lch_db = sanitizeField(@$_POST['dbconnect']);
	$l_access =  $lch_dbAccess->connectDB($lch_db);

	// arrays
	$larr_Modules = array();
	$larr_ModuleActionList = array ();


	// ASSIGN TABLE NAME HERE
	$lch_TableName = "mstuserprivilege";

	// USER MST CODE
	$lch_UserMSTCode = sanitizeField(@$_POST['user_mst_code']);

	$llo_UpdAddSomeRecords = false;

	// PRIMARY CODES
	$larr_PrimaryCodes = array();
	$larr_PrimaryCodes["privilege_type"] = sanitizeField(@$_POST['privilege_type']);
	$larr_PrimaryCodes["type_code"] = sanitizeField(@$_POST['type_code']);
	$lch_MenuItemCode = sanitizeField(@$_POST['menu_item_mst_code']);
	if ($lch_MenuItemCode == ""){
		$lch_MenuItemCode  = "-1";
	}

	
	/* RETRIEVE MODULES INVOLVED */
	// MENU ITEM LIST NG ININPUT NA PARENT
	// POST THE ACTUAL RECORDS FOR USER PRIVILEGE
	$lch_Query = "SELECT distinct (mstmenuitem.module_mst_code), mstmodule.module_action_dtl_codes, mstmodule.code as 'module_code' FROM mstmenuitem,mstmodule WHERE mstmenuitem.module_mst_code = mstmodule.code AND mstmenuitem.is_active = '1' ";
	$lch_Query = $lch_Query . " AND mstmenuitem.menu_item_mst_code = '$lch_MenuItemCode' AND mstmenuitem.type = '2' AND mstmodule.is_enabled = '1' AND mstmenuitem.module_mst_code <> '0'";
	$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		array_push($larr_Modules,array("module_code"=>$larr_ResultRow["module_code"],
										"module_action_dtl_codes"=>$larr_ResultRow["module_action_dtl_codes"]));

		/* check for actions */
		$larr_ModActionPerModule = explode(",", $larr_ResultRow["module_action_dtl_codes"]);
		if (!empty($larr_ModActionPerModule)){
			foreach ($larr_ModActionPerModule as $lch_action){
				if (!in_array($lch_action, $larr_ModuleActionList)){
					array_push($larr_ModuleActionList, $lch_action);
				} // if (!in_array($lch_action, $larr_ModuleActionList)){
			} // foreach ($larr_ModActionPerModule as $lch_action){
		} // if (!empty($larr_ModActionPerModule)){
	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

	// FIELDS TO ASSIGN
	$larr_FieldsToAssign = array ();
	// loop modules
	foreach ($larr_Modules as $larr_ModuleValue){
		// check first if the Access type is Blank or Denied - auto update lahat ng action

		// condition for blank input
		if (!isset($_POST["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]) ||
				(isset($_POST["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
									$larr_PrimaryCodes["type_code"]."_".
									$larr_ModuleValue["module_code"]."_-1"]) && 
				@$_POST["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
									$larr_PrimaryCodes["type_code"]."_".
									$larr_ModuleValue["module_code"]."_-1"] == "")) {

			$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"] = "0";

		} // blank input - default to 0

		else  {
			$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"] = sanitizeField(@$_POST["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]);
		} // denied or allowed


		// update first the Access record
		$lch_Query = "SELECT count($lch_TableName.code) AS 'exist_count' FROM $lch_TableName WHERE ";
		$lch_Query = $lch_Query . "$lch_TableName.type = '".$larr_PrimaryCodes["privilege_type"]."' AND ";
		$lch_Query = $lch_Query . "$lch_TableName.type_mst_code = '".$larr_PrimaryCodes["type_code"]."' AND ";
		$lch_Query = $lch_Query . "$lch_TableName.module_mst_code = '".$larr_ModuleValue["module_code"]."' AND ";
		$lch_Query = $lch_Query . "$lch_TableName.module_action_mst_code = '-1' ";
		$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		$lin_existcount = 0;
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$lin_existcount = intval($larr_ResultRow["exist_count"]);
		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		if ($lin_existcount>0){
			$lch_Query = "UPDATE $lch_TableName SET ";
			$lch_Query = $lch_Query . " $lch_TableName.access_type='".$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]."', $lch_TableName.updated_at=NOW(), $lch_TableName.updated_user_mst_code='$lch_UserMSTCode' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . "$lch_TableName.type = '".$larr_PrimaryCodes["privilege_type"]."' AND ";
			$lch_Query = $lch_Query . "$lch_TableName.type_mst_code = '".$larr_PrimaryCodes["type_code"]."' AND ";
			$lch_Query = $lch_Query . "$lch_TableName.module_mst_code = '".$larr_ModuleValue["module_code"]."' AND ";
			$lch_Query = $lch_Query . "$lch_TableName.module_action_mst_code = '-1' ";

			/* execute query here */
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			if (!$SQL_RESULT){

			} // if (!$SQL_RESULT){
			else {
				$llo_UpdAddSomeRecords = true;
			} // ELSE ng if (!$SQL_RESULT){

		} // if ($lin_existcount>0){
		else {
			$lch_Query = "INSERT INTO $lch_TableName (";
			$lch_Query = $lch_Query . "type,type_mst_code,module_mst_code,module_action_mst_code,access_type,created_at,created_user_mst_code,updated_at,updated_user_mst_code";
			$lch_Query = $lch_Query . ") VALUES (";
			$lch_Query = $lch_Query . "'".$larr_PrimaryCodes["privilege_type"]."',";
			$lch_Query = $lch_Query . "'".$larr_PrimaryCodes["type_code"]."',";
			$lch_Query = $lch_Query . "'".$larr_ModuleValue["module_code"]."',";
			$lch_Query = $lch_Query . "'-1',";
			$lch_Query = $lch_Query . "'".$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]."',";
			$lch_Query = $lch_Query . "NOW(),'$lch_UserMSTCode',NOW(),'$lch_UserMSTCode'";
			$lch_Query = $lch_Query . ")";
			
			/* execute query here */
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			if (!$SQL_RESULT){

			} // if (!$SQL_RESULT){
			else {
				$llo_UpdAddSomeRecords = true;
			} // ELSE ng if (!$SQL_RESULT){

		} // else ng if ($lin_existcount>0){
		// END - update first the Access record

		// if Blank or Denied = update all actions to its value
		if ($larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]=="0" || $larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]=="2"){

			
			$lch_Query = "UPDATE $lch_TableName SET ";
			$lch_Query = $lch_Query . " $lch_TableName.access_type='".$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_-1"]."', $lch_TableName.updated_at=NOW(), $lch_TableName.updated_user_mst_code='$lch_UserMSTCode' ";
			$lch_Query = $lch_Query . " WHERE ";
			$lch_Query = $lch_Query . "$lch_TableName.type = '".$larr_PrimaryCodes["privilege_type"]."' AND ";
			$lch_Query = $lch_Query . "$lch_TableName.type_mst_code = '".$larr_PrimaryCodes["type_code"]."' AND ";
			$lch_Query = $lch_Query . "$lch_TableName.module_mst_code = '".$larr_ModuleValue["module_code"]."'";
			
			/* execute query here */
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			if (!$SQL_RESULT){

			} // if (!$SQL_RESULT){
			else {
				$llo_UpdAddSomeRecords = true;
			} // ELSE ng if (!$SQL_RESULT){

		} // if Blank or Denied = update all actions to its value

		// if Allowed - proceed to actions
		else {

			$larr_ModActPerM = explode(",", $larr_ModuleValue["module_action_dtl_codes"]);
			foreach ($larr_ModActPerM as $lch_action){

				$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_".$lch_action] = sanitizeField(@$_POST["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
								$larr_PrimaryCodes["type_code"]."_".
								$larr_ModuleValue["module_code"]."_".$lch_action]);

				$lch_Query = "SELECT count($lch_TableName.code) AS 'exist_count' FROM $lch_TableName WHERE ";
				$lch_Query = $lch_Query . "$lch_TableName.type = '".$larr_PrimaryCodes["privilege_type"]."' AND ";
				$lch_Query = $lch_Query . "$lch_TableName.type_mst_code = '".$larr_PrimaryCodes["type_code"]."' AND ";
				$lch_Query = $lch_Query . "$lch_TableName.module_mst_code = '".$larr_ModuleValue["module_code"]."' AND ";
				$lch_Query = $lch_Query . "$lch_TableName.module_action_mst_code = '$lch_action' ";
				$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
				$lin_existcount = 0;
				while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
					$lin_existcount = intval($larr_ResultRow["exist_count"]);
				} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
				if ($lin_existcount>0){
					$lch_Query = "UPDATE $lch_TableName SET ";
					$lch_Query = $lch_Query . " $lch_TableName.access_type='".$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
										$larr_PrimaryCodes["type_code"]."_".
										$larr_ModuleValue["module_code"]."_".$lch_action]."', $lch_TableName.updated_at=NOW(), $lch_TableName.updated_user_mst_code='$lch_UserMSTCode' ";
					$lch_Query = $lch_Query . " WHERE ";
					$lch_Query = $lch_Query . "$lch_TableName.type = '".$larr_PrimaryCodes["privilege_type"]."' AND ";
					$lch_Query = $lch_Query . "$lch_TableName.type_mst_code = '".$larr_PrimaryCodes["type_code"]."' AND ";
					$lch_Query = $lch_Query . "$lch_TableName.module_mst_code = '".$larr_ModuleValue["module_code"]."' AND ";
					$lch_Query = $lch_Query . "$lch_TableName.module_action_mst_code = '$lch_action' ";

					/* execute query here */
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
					if (!$SQL_RESULT){

					} // if (!$SQL_RESULT){
					else {
						$llo_UpdAddSomeRecords = true;
					} // ELSE ng if (!$SQL_RESULT){

				} // if ($lin_existcount>0){
				else {
					$lch_Query = "INSERT INTO $lch_TableName (";
					$lch_Query = $lch_Query . "type,type_mst_code,module_mst_code,module_action_mst_code,access_type,created_at,created_user_mst_code,updated_at,updated_user_mst_code";
					$lch_Query = $lch_Query . ") VALUES (";
					$lch_Query = $lch_Query . "'".$larr_PrimaryCodes["privilege_type"]."',";
					$lch_Query = $lch_Query . "'".$larr_PrimaryCodes["type_code"]."',";
					$lch_Query = $lch_Query . "'".$larr_ModuleValue["module_code"]."',";
					$lch_Query = $lch_Query . "'$lch_action',";
					$lch_Query = $lch_Query . "'".$larr_FieldsToAssign ["user_privilege_".$larr_PrimaryCodes["privilege_type"]."_".
										$larr_PrimaryCodes["type_code"]."_".
										$larr_ModuleValue["module_code"]."_".$lch_action]."',";
					$lch_Query = $lch_Query . "NOW(),'$lch_UserMSTCode',NOW(),'$lch_UserMSTCode'";
					$lch_Query = $lch_Query . ")";

					/* execute query here */
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
					if (!$SQL_RESULT){

					} // if (!$SQL_RESULT){
					else {
						$llo_UpdAddSomeRecords = true;
					} // ELSE ng if (!$SQL_RESULT){

				} // else ng if ($lin_existcount>0){

			} // foreach ($larr_ModActPerM as $lch_action){

		} // if Allowed - proceed to actions
		
	} // foreach ($larr_Modules as $larr_ModuleValue){


	if ($llo_UpdAddSomeRecords){
		$larr_ResultQueryArray = array(
			"result" => "1",
			"error_message" => "Changes saved successfully."
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