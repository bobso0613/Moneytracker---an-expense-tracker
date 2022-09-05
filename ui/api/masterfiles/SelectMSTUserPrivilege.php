<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("../SystemConstants.php");
require_once("../CurlAPI.php");
date_default_timezone_set('Asia/Manila');

$larr_OutputArray = array();
$larr_OutputLines = array();
$larr_OutputTypeMstDetails = array();
$larr_OutputSublines = array();
$larr_OutputPerils = array();
$larr_OutputAgent = array();
$larr_OutputUserPrivilegesBeforeFormatting = array();
$larr_OutputIntermediaryRates = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// DB LOCATION STRING -- http
	$lch_DBLocationString = DB_LOCATION;

	$privilege_type = @$_POST["privilege_type"];
	$type_code = @$_POST["type_code"];
	$menu_item_mst_code = @$_POST["menu_item_mst_code"];
	if ($menu_item_mst_code==""){
		$menu_item_mst_code = "-1";
	}

	// get user if type == 1
	if ($privilege_type=="1"){
		// POST THE USER SELECTED
		$larr_Params = array (
		    "action" => "retrieve",
		    "fileToOpen" => "default_select_query",
		    "tableName" => "mstuser",
		    "dbconnect" => MONEYTRACKER_DB,
		    "columns" => "code,username,whole_name" ,
		    "conditions[equals][code]" => $type_code,
		    "orderby" => "code ASC"
		);
	} // if ($privilege_type=="1"){

	// get user if type == 2
	else {
		// POST THE GROUP SELECTED
		$larr_Params = array (
		    "action" => "retrieve",
		    "fileToOpen" => "default_select_query",
		    "tableName" => "mstusergroup",
		    "dbconnect" => MONEYTRACKER_DB,
		    "columns" => "code,short_name,whole_name" ,
		    "conditions[equals][code]" => $type_code,
		    "orderby" => "code ASC"
		);
	} // else ng if ($privilege_type=="1"){
	
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_OutputTypeMstDetails = json_decode($ljson_Result,true);


	// MENU ITEM LIST NG ININPUT NA PARENT
	// POST THE ACTUAL RECORDS FOR USER PRIVILEGE
	$larr_Params = array (
	    "action" => "retrieve",
	    "fileToOpen" => "default_select_query",
	    "tableName" => "mstmenuitem",
	    "dbconnect" => MONEYTRACKER_DB,
	    "columns" => "code,module_mst_code,menu_item_mst_code,is_active" ,
	    "conditions[not_equals][module_mst_code]" => "0",
	    "conditions[equals][menu_item_mst_code]" => $menu_item_mst_code,
	    "conditions[equals][is_active]" => "1",
	    "conditions[equals][type]" => "2",
	    "orderby" => "order_no ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_OutputMenuItemList = json_decode($ljson_Result,true);
	$larr_ModuleItemList = array();
	if ($larr_OutputMenuItemList[0]["result"]=="1"){
		foreach ($larr_OutputMenuItemList as $lch_Key => $lch_ValueArray){
			// POST ALL MODULES TO POST THE DETAILS
			array_push($larr_ModuleItemList, $lch_ValueArray["module_mst_code"]);
		} // foreach ($larr_OutputMenuItemList as $lch_Key => $lch_ValueArray){
		// end - reformat
	} // 	if (count($larr_OutputMenuItemList)>0){
	$lch_ModuleItemListString = implode(",", $larr_ModuleItemList);

	$larr_ResultQueryArray = array(
		"result" => "1",
		"error_message" => "Proceed.",
		"type" => $privilege_type,
		//"yolo" => $lch_ModuleItemListString,
		"type_mst_details" => $larr_OutputTypeMstDetails
		//"user_privileges_list" => $larr_OutputIntermediaryRates

	);

	// POST THE MODULES NAMAN -> FILTER PER MENU PARENT
	//$lch_ModuleListString = implode(",", $larr_ModuleList);
	//$larr_OutputModuleDetails = array();
	if ($lch_ModuleItemListString!=""){
		$larr_Params = array (
		    "action" => "retrieve",
		    "fileToOpen" => "default_select_query",
		    "tableName" => "mstmodule",
		    "dbconnect" => MONEYTRACKER_DB,
		    "columns" => "code,short_name,module_name,module_action_dtl_codes" ,
		    "conditions[equals][is_enabled]" => "1",
		    "conditions[in][code]" => $lch_ModuleItemListString,
		    "orderby" => "short_name ASC, module_name ASC"
		);
		$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
		$larr_OutputModuleDetails = json_decode($ljson_Result,true);
	} // if ($lch_ModuleListString!=""){
	$larr_ResultQueryArray["module_details"] = $larr_OutputModuleDetails;


	// POST THE ACTUAL RECORDS FOR USER PRIVILEGE
	$larr_Params = array (
	    "action" => "retrieve",
	    "fileToOpen" => "default_select_query",
	    "tableName" => "mstuserprivilege",
	    "dbconnect" => MONEYTRACKER_DB,
	    "columns" => "code,type,type_mst_code,module_mst_code,module_action_mst_code,access_type" ,
	    "conditions[equals][type_mst_code]" => $type_code,
	    "conditions[equals][type]" => $privilege_type,
	    "conditions[in][module_mst_code]" => $lch_ModuleItemListString,
	    "orderby" => "type ASC, type_mst_code ASC, module_mst_code ASC, module_action_mst_code ASC, access_type ASC, code ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_OutputUserPrivilegesBeforeFormatting = json_decode($ljson_Result,true);

	// re-format result of intermediary rates for easier retrieval in the caller javascript
	// new format index name = 'user_privilege-$type-$type_mst_code-$module_mst_code-$module_action_mst_code'
	// sample = $larr_ResultQueryArray["user_privilege"][$lch_ValueArray["type"]."_".$lch_ValueArray["type_mst_code"]."_".$lch_ValueArray["module_mst_code"]."_".$lch_ValueArray["module_action_mst_code"]] = $lch_ValueArray["access_type"]
	$larr_ResultQueryArray["user_privilege"] = array();
	//$larr_ModuleList = array();
	if ($larr_OutputUserPrivilegesBeforeFormatting[0]["result"]=="1"){
		
		foreach ($larr_OutputUserPrivilegesBeforeFormatting as $lch_Key => $lch_ValueArray){

			// POST ALL MODULES TO POST THE DETAILS
			/* not needed 
			if (!in_array($lch_ValueArray["module_mst_code"],$larr_ModuleList)){
				array_push($larr_ModuleList, $lch_ValueArray["module_mst_code"]);
			} // if (!in_array($lch_ValueArray["module_mst_code"],$larr_ModuleList)){
			*/

			$larr_ResultQueryArray["user_privilege"][$lch_ValueArray["type"]."_".$lch_ValueArray["type_mst_code"]."_".$lch_ValueArray["module_mst_code"]."_".$lch_ValueArray["module_action_mst_code"]] = $lch_ValueArray["access_type"];
		} // foreach ($larr_OutputUserPrivilegesBeforeFormatting as $lch_Key => $lch_ValueArray){
		// end - reformat
	} // 	if (count($larr_OutputUserPrivilegesBeforeFormatting)>0){


	

	// loop $larr_OutputModuleDetails to get the actions na meron for all modules
	$larr_ModuleActionList = array();
	foreach ($larr_OutputModuleDetails as $lch_Key => $lch_ValueArray){
		$larr_ModActListPerModule = explode(",", $lch_ValueArray["module_action_dtl_codes"]);
		foreach ($larr_ModActListPerModule as $lch_Value){
			if (!in_array($lch_Value,$larr_ModuleActionList)){
				array_push($larr_ModuleActionList, $lch_Value);
			} // if (!in_array($lch_ValueArray["module_mst_code"],$larr_ModuleList)){
		}
	} // foreach ($larr_OutputModuleDetails as $lch_Key => $lch_ValueArray){

	// POST MODULE ACTIONS
	$lch_ModuleActionList = implode(",", $larr_ModuleActionList);
	$larr_OutputModuleActionDetails = array ();
	if ($lch_ModuleActionList!=""){
		$larr_Params = array (
		    "action" => "retrieve",
		    "fileToOpen" => "default_select_query",
		    "tableName" => "mstmoduleaction",
		    "dbconnect" => MONEYTRACKER_DB,
		    "columns" => "code,action_name" ,
		    "conditions[in][code]" => $lch_ModuleActionList,
		    "orderby" => "code ASC"
		);
		$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
		$larr_OutputModuleActionDetails = json_decode($ljson_Result,true);
	} // if ($lch_ModuleActionList!=""){
	$larr_ResultQueryArray["module_action_details"] = $larr_OutputModuleActionDetails;


	array_push($larr_OutputArray,$larr_ResultQueryArray);
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	$larr_ResultQueryArray = array(
		"result" => "0",
		"error_message" => "Invalid Request Type."
		
	);
	array_push($larr_OutputArray,$larr_ResultQueryArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {

echo json_encode( $larr_OutputArray);

?>