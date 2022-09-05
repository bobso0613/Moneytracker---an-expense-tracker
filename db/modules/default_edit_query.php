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
			<input type="hidden" name="keys[keyname]" value="value"/> = $_POST['keys']['keyname'] = 'value';

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
		$sanitizedKeys = array();

		/* PRIMARY KEYS */
		if (isset($_POST['keys'])){
			foreach ($_POST['keys'] as $keyName => $keyValue){
				if (!empty($keyValue)){
					$sanitizedKeys[$keyName] = stripcslashes($keyValue);
					$sanitizedKeys[$keyName] = mysql_real_escape_string($sanitizedKeys[$keyName]);
				}
				
			}
		}

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
				if (isset($fieldValue)){
					$sanitizedFields[$fieldName] = stripcslashes($fieldValue);
					$sanitizedFields[$fieldName] = mysql_real_escape_string($sanitizedFields[$fieldName]);
				}
				
			}
		}

		$query = "UPDATE $tableName SET ";
		$updateValues = "";
		$updateKeyValues = "";
		foreach ($sanitizedFields as $fieldName => $fieldValue) {
			$updateValues = $updateValues . "$tableName.$fieldName='$fieldValue',";
		}
		foreach ($sanitizedKeys as $keyName => $keyValue) {
			$updateKeyValues = $updateKeyValues . "$tableName.$keyName='$keyValue',";
		}
		$updateKeyValues = rtrim($updateKeyValues, ",");
		$updateValues = $updateValues . "$tableName.updated_at=NOW(),$tableName.updated_user_mst_code='$userid'";
		$query = $query . "$updateValues WHERE $updateKeyValues";
		

		//echo $query;

		$result=mysql_query($query) or die(mysql_error());
		if (!$result){
			$arr = array(
				"result" => "0",
				"error_message" => "Failed to perform the selected action."
			);
		}
		else {
			$arr = array(
				"result" => "1",
				"error_message" => "Record successfully updated."
			);
		}
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