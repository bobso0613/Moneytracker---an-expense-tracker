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
	$db = @$_POST['dbconnect'];
	$db = stripslashes($db);
	$access =  $dbAccess->connectDB($db);
	$db = mysql_real_escape_string($db);
	

	$tableName = @$_POST['tableName'];
	$tableName = stripslashes($tableName);
	$tableName = mysql_real_escape_string($tableName);

	$sanitizedColumns = array();
	$sanitizedWhereEqualsConditions = array();

	/* COLUMNS TO DISPLAY */
	if (isset($_POST['columns'])){
		$sanitizedString = stripcslashes($_POST['columns']);
		$sanitizedString = mysql_real_escape_string($sanitizedString);
		$sanitizedColumns = explode(",",$sanitizedString);
	} /* if (isset($_POST['columns'])){ */
	else {
		$sanitizedColumns = array("*"); //select all sign 
	}

	/* WHERE CLAUSE NA EQUALS ANG CONDITION: */
	if (isset($_POST['conditions']['equals'])){
		foreach ($_POST['conditions']['equals'] as $keyName => $keyValue){
			if (!empty($keyValue)){
				$sanitizedWhereEqualsConditions[$keyName] = stripcslashes($keyValue);
				$sanitizedWhereEqualsConditions[$keyName] = mysql_real_escape_string($sanitizedWhereEqualsConditions[$keyName]);
			}
			
		}
	}

	// form query here
	$query = "SELECT ";
	foreach($sanitizedColumns as $keyValue){
		$query = $query . "$tableName.$keyValue,";
	}
	$query = rtrim($query,",");
	$query = $query . " FROM $tableName ";


	$equalsConditions = "";
	foreach ($sanitizedWhereEqualsConditions as $keyName => $keyValue) {
		$equalsConditions = $equalsConditions . "$tableName.$keyName='$keyValue' AND ";
	}
	$equalsConditions = rtrim($equalsConditions, "AND ");

	if ($equalsConditions!==""){
		$query = $query . " WHERE $equalsConditions ";
	}


	
	// execute query here
	$result=mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result) <= 0) {
		$arr = array(
			"result" => "0",
			"error_message" => "There are no records found."
		);
		array_push($outputArray,$arr);
	}
	else {
		/* records are here */
		$count = 0;
		$count = mysql_num_rows($result);

		while($row=mysql_fetch_array($result))
		{	
			$resultArray = array();
			foreach ($sanitizedColumns as $keyValue) {
				if ($keyValue==="*"){
					foreach ($row as $key => $value){
						if (!is_numeric($key)){
							$resultArray[$key] = ($key==='image'||$key==='uploaded_file')?base64_encode($value):$value;

						}
					}
				}
				else {
					$resultArray[$keyValue] = ($keyValue==='image'||$keyValue==='uploaded_file')?base64_encode($row[$keyValue]):$row[$keyValue];
				}
			}
			$resultArray["count"] = ($_POST['action']==='get_table_record_count')?$row["tableCount"]:$count;
			$resultArray["result"] = '1';

			array_push($outputArray,$resultArray);
		}
	}

	/*print_r($outputArray);*/
	$dbAccess->closeCon();

	echo json_encode( $outputArray);
	
}




?>