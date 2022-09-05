<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../api/DatabaseConnect.php");
$dbAccess = new DatabaseAccess();
$outputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['action']) || $_POST['action']!=='') {

		/*
			<input type="text" value="val" name="fields[fieldname]" /> = $_POST['fields']['fieldname'] = 'val';

			<input type="checkbox" name="fields_checkboxes[fieldname][]" value="val"/> $_POST['fields_checkboxes']['fieldname'][] = 'val' - array;

			<input type="radio" name="fields_radio[fieldname]" value="val"/> $_POST['fields_radio']['fieldname'] = 'val';

		*/

		$insertFields = "";
		$insertValues = "";

		$db = @$_POST['dbconnect'];
		$db = stripslashes($db);
		$access =  $dbAccess->connectDB($db);
		$db = mysql_real_escape_string($db);

		$tableName = @$_POST['tableName'];
		$tableName = stripslashes($tableName);
		$tableName = mysql_real_escape_string($tableName);

		$userid = @$_POST['userid_loggedin'];
		$userid = stripslashes($userid);
		$userid = mysql_real_escape_string($userid);

		$sanitizedFields = array();

		/* CHECKBOXES */
		if (isset($_POST['fields_checkboxes'])){
			foreach ($_POST['fields_checkboxes'] as $fieldName => $fieldValue ){
				if (!empty($fieldValue)){
					$sanitizedFields[$fieldName] = implode(',',(array)$fieldValue); 
					$sanitizedFields[$fieldName] = mysql_real_escape_string($sanitizedFields[$fieldName]);
				}else {
					$sanitizedFields[$fieldName] = '';
					$sanitizedFields[$fieldName] = mysql_real_escape_string($sanitizedFields[$fieldName]);
				}
			}
		}

		/* RADIO BUTTONS */
		if (isset($_POST['fields_radio'])){
			foreach ($_POST['fields_radio'] as $fieldName => $fieldValue){
				$fieldValue = (isset($fieldValue)) ? $fieldValue : '0';
				$sanitizedFields[$fieldName] = stripcslashes($fieldValue);
				$sanitizedFields[$fieldName] = mysql_real_escape_string($sanitizedFields[$fieldName]);
			}
		}

		/* OTHER FIELDS */
		if (isset($_POST['fields'])){
			foreach ($_POST['fields'] as $fieldName => $fieldValue){
				if (!empty($fieldValue)){
					$sanitizedFields[$fieldName] = stripcslashes($fieldValue);
					$sanitizedFields[$fieldName] = mysql_real_escape_string($sanitizedFields[$fieldName]);
				}
				
			}
		}

		// check if na-add na, anti refresh / multiple clicks
		// $lch_Query = "SELECT * FROM $tableName WHERE ";
		// $updateKeyValues = "";
		// foreach ($sanitizedFields as $keyName => $keyValue) {
		// 	$updateKeyValues = $updateKeyValues . "$tableName.$keyName='$keyValue' AND ";
		// }
		// $updateKeyValues = rtrim($updateKeyValues, "AND ");
		// $lch_Query = $lch_Query . "$updateKeyValues";
		// $SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
		// if (mysql_num_rows($SQL_RESULT) > 0) {
		// 	$arr = array(
		// 		"result" => "0",
		// 		"error_message" => "The record cannot be added because of possible duplication of record."
		// 	);
		// }
		// else {

			$lch_Query = "INSERT INTO $tableName ";
			$insertValues = "(";
			$insertFields = "(";
			foreach ($sanitizedFields as $fieldName => $fieldValue) {
				$insertFields = $insertFields . "$tableName.$fieldName,";
				$insertValues = $insertValues . "'$fieldValue',"; 


			}
			$insertFields = $insertFields . "$tableName.created_at,$tableName.created_user_mst_code,$tableName.updated_at,$tableName.updated_user_mst_code)";
			$insertValues = $insertValues . "NOW(),'$userid',NOW(),'$userid')"; 
			$lch_Query = $lch_Query . "$insertFields VALUES $insertValues;";

			//echo $query;

			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			if (!$SQL_RESULT){
				$arr = array(
					"result" => "0",	
					"error_message" => "Failed to perform the selected action."
				);
			}
			else {
				$last_codes = mysql_insert_id();
				$arr = array(
					"result" => "1",
					"last_code"=> $last_codes,
					"error_message" => "Record successfully saved."
				);
				if(isset($_POST["fromInterim"]) && @$_POST["fromInterim"] == "1"){

					if(isset($_POST["ValuePairs"]) && @$_POST["ValuePairs"] != ""){

						$value_pairs = explode(":",$_POST["ValuePairs"]);
						$value_pairs_left = explode(" ",$value_pairs["0"]);
                   	 	$value_pairs_disp = explode(" ",$value_pairs["1"]);
                   	 	$queryFields = "";

                   	 	foreach ($value_pairs_left as $value) {
                   	 		$queryFields .= $value . ",";
                   	 	}//foreach ($value_pairs_left as $value) {
               	 		foreach ($value_pairs_disp as $value) {
                   	 		$queryFields .= $value . ",";
                   	 	}//foreach ($value_pairs as $value) {

                   	 	$queryFields = rtrim($queryFields,",");

						$lch_Query = "SELECT $queryFields FROM $tableName WHERE $tableName.code = '$last_codes'";
						$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
						//$arr["pasok"]=$lch_Query;
						while($larr_ResultRow = mysql_fetch_array($SQL_RESULT)){

							foreach ($larr_ResultRow as $lch_key => $lch_value){
								if (!is_numeric($lch_key)){
									$arr[$lch_key] = $lch_value;
								}//if (!is_numeric($lch_key)){
							}//foreach ($larr_ResultRow as $lch_key => $lch_value){

							
						}//while($larr_ResultRow = mysql_fetch_array($SQL_RESULT)){
					}//if(isset($_POST["ValuePairs"]) && @$_POST["ValuePairs"] != ""){
				}//if(isset($_POST["fromInterim"]) && @$_POST["fromInterim"] == "1"){
			}

		//}

		
		array_push($outputArray,$arr);
		/*print_r($outputArray);*/
		$dbAccess->closeCon();
		echo json_encode( $outputArray);

	}
}
else {
	// illegal access error
}
?>