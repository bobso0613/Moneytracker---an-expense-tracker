<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");
date_default_timezone_set('Asia/Manila');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
session_start();
}

/*
"http://b85m-p33/abic-web/includes/MasterfilesShowModal.php?
usercode=2&
masterfilename=Module%20Actions&
databasename=iisaac_abic_system_db&
tablename=mstmoduleaction&
primarycodefields=code&
columnstoquery=remarks|code|action_name|description&
columnscaption=Remarks|Primary%20Key|Name%20of%20action|Description%20of%20the%20action&
columnsdatasource=3|0|0|0&
columnsdatasourcedatabasename=|||&
columnsdatasourcetablename=|||&
columnsdatasourcevaluepair=|||&
primarycodevalue="+data[0]+"&
valueviewed="+data[1]"+""
*/

$outputArray = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST["transactionmode"]=="can_add"||$_POST["transactionmode"]=="can_edit"){
		$columns = explode("|",$_POST["columnstoquery"]);
		$columnsFieldName = explode("|",$_POST["columnsfieldname"]);
		//$columnsCaption = explode("|",$_POST["columnscaption"]);
		$columnsDataSource = explode("|",$_POST["columnsdatasource"]);
		$columnsDataSourceDatabaseName = explode("|",$_POST["columnsdatasourcedatabasename"]);
		$columnsDataSourceTableName = explode("|",$_POST["columnsdatasourcetablename"]);
		$columnsDatasourceValuePair = explode("|",$_POST["columnsdatasourcevaluepair"]);
		//$columnsSpecialConditions = explode("|",$_POST["columnsspecialconditions"]);

		$columnsIsRequired = explode("|",$_POST["columnsisrequired"]);
		$columnsIsUnique = explode("|",$_POST["columnsisunique"]);
		$columnsDataType = explode("|",$_POST["columnsdatatype"]);
		$columnsMaxLength = explode("|",$_POST["columnsmaxlength"]);

		$columnsCode = explode("|",$_POST["columnscode"]);
	} // if ($_POST["transactionmode"]=="can_add"||$_POST["transactionmode"]=="can_edit"){

	// save proper starts here
	$link = DB_LOCATION;
	$params = array (
        "action" => "save-masterfile",
        "tableName" => $_POST["tableName"],
        "dbconnect" => $_POST["dbconnect"],
        "userid_loggedin" => $_SESSION["user_code"]
    );

	if ($_POST["transactionmode"]=="can_add"){
		$params["fileToOpen"] = "default_add_query";
	} // if ($_POST["transactionmode"]=="can_add"){
	else if ($_POST["transactionmode"]=="can_edit"){
    	$params["fileToOpen"] = "default_edit_query";
    	$params["keys[".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
	} // else if ($_POST["transactionmode"]=="can_edit"){
	else if ($_POST["transactionmode"]=="can_delete"){
    	$params["fileToOpen"] = "default_delete_query";
    	$params["keys[".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
	} // else if ($_POST["transactionmode"]=="can_delete"){


	if ($_POST["transactionmode"]=="can_add"||$_POST["transactionmode"]=="can_edit"){
		// LOOP ALL COLUMNS - USE $column_key for index
		foreach ($columns as $column_key => $column_name){
			// DETERMINE WHAT DATA TYPE 
			//9 - Checkbox
			$checkBoxValues = "";
			if ($columnsDataType[$column_key]=="9"){
				if (isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])){
					// implode $_POST variable
					$checkBoxValues = implode(",",$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]);
					//fields[fieldname]
				} // if (isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])){
				
				$params["fields[".$column_name."]"] = $checkBoxValues;
			} // if ($columnsDataType[$column_key]=="9"){

			else if ($columnsDataType[$column_key]=="2" || $columnsDataType[$column_key]=="3"){
				$params["fields[".$column_name."]"] = str_replace(',','',@$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]);
			}

//str_replace(',','',

			// OTHER
			else {
				//fields[fieldname]
				$params["fields[".$column_name."]"] = @$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name];
			} // else ng if ($columnsDataType[$column_key]=="9"){

		} // foreach ($columns as $column_key => $column_name){

		if(isset($_POST["fromInterim"]) && isset($_POST["ValuePairs"])){
			$params["fromInterim"] = @$_POST["fromInterim"];
			$params["ValuePairs"] = @$_POST["ValuePairs"];
		} //if(isset($_POST["isfromInterim"])){
	} // if ($_POST["transactionmode"]=="can_add"||$_POST["transactionmode"]=="can_edit"){

	$result=processCurl($link,$params);
	// echo $result;
    $retrievedRecordRow = json_decode($result,true);
    if (count($retrievedRecordRow)>0 && $retrievedRecordRow[0]["result"] == "1"){
    	$arr = array(
			"result" => $retrievedRecordRow[0]["result"],
			"last_code"=> (isset($retrievedRecordRow[0]["last_code"])) ? $retrievedRecordRow[0]["last_code"] : "",
			"error_message" => $retrievedRecordRow[0]["error_message"]
		);
		
	    if(isset($_POST["fromInterim"]) && isset($_POST["ValuePairs"])){

			$value_pairs = explode(":",$_POST["ValuePairs"]);
	   	 	$value_pairs_disp = explode(" ",$value_pairs["1"]);

	   	 	$arr[$value_pairs[0]] = $retrievedRecordRow[0][$value_pairs[0]];
	   	 	foreach ($value_pairs_disp as $lch_key => $lch_value) {
	   	 		$arr[$lch_value] = $retrievedRecordRow[0][$lch_value];
	   	 	}

	    	
	    	
	    	//$arr[$value_pairs_disp[1]] = $retrievedRecordRow[0][$value_pairs_disp[1]];
	    	
    	} //if(isset($_POST["isfromInterim"])){

		array_push($outputArray,$arr);
    
		// AUDIT TRAIL PART
		$link = DB_LOCATION;
		$params = array (
			"action" => $_POST["transactionmode"],
			"fileToOpen" => "save_audit_trail",
			"tableName" => "trnaudittrail",
			"dbconnect" => MONEYTRACKER_DB,
			"module_mst_code" => $_POST["modulemstcode"],
			"menu_item_mst_code" => $_POST["menuitemmstcode"],
			"user_mst_code" => $_SESSION["user_code"],
			"reference" => isset($_POST["valueviewed"]) ? $_POST["valueviewed"] : "New Record",
			"description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
		);
		$result=processCurl($link,$params);
		// no need to parse the result. error man o hindi
		// end - AUDIT TRAIL PART

	} // if (count($retrievedRecordRow)>0){
	else {

		$outputArray = $retrievedRecordRow;
		
	}
	// save proper ends here

    	

} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	$arr = array(
		"result" => "0",
		"error_message" => "Invalid Request Type."
	);
	array_push($outputArray,$arr);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {

echo json_encode( $outputArray);

?>