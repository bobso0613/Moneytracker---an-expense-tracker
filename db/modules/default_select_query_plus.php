<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json; charset=iso-8859-1');
@include_once("../api/DatabaseConnect.php");
$dbAccess = new DatabaseAccess();
$outputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//POST_table_record_count
	if (isset($_POST['action']) && $_POST['action']!=='') {

		/*
			SELECT Columns / *
			FROM tableName
			WHERE <conditions>
			[GROUP BY <columns>]
			[HAVING <column of groupby conditions>]
			ORDER BY <columns> <asc/desc>
			LIMIT <start where>,<how many records to be retrieved>

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

		$sanitizedColumns = array();
		$sanitizedLimitStart = "";
		$sanitizedNoRecords = "";
		$sanitizedOrderBy = array();
		$sanitizedWhereNotEqualsConditions = array();
		$sanitizedWhereEqualsConditions = array();
		$sanitizedWhereLikeConditions = array();
		$sanitizedWhereInConditions = array();
		$sanitizedWhereNotInConditions = array();
		$sanitizedWhereFindInSetConditions = array();
		$sanitizedWhereLessThanConditions = array();
		$sanitizedWhereGreaterThanConditions = array();

		if (isset($_POST['action']) && $_POST['action']==='get_table_record_count') {
			$query = "SELECT COUNT(*) as tableCount FROM $tableName;";
		}
		else {


			/* COLUMNS TO DISPLAY */
			if (isset($_POST['columns'])){
				$sanitizedString = stripcslashes($_POST['columns']);
				$sanitizedString = mysql_real_escape_string($sanitizedString);
				$sanitizedColumns = explode(",",$sanitizedString);
			} /* if (isset($_POST['columns'])){ */
			else {
				$sanitizedColumns = array("*"); //select all sign
			}


			/* LIMIT START AND NO. OF RECORDS */
			if (isset($_POST['recordstart'])){
				$sanitizedLimitStart = stripcslashes($_POST['recordstart']);
				$sanitizedLimitStart = mysql_real_escape_string($sanitizedLimitStart);
			}
			if (isset($_POST['recordcount'])){
				$sanitizedNoRecords = stripcslashes($_POST['recordcount']);
				$sanitizedNoRecords = mysql_real_escape_string($sanitizedNoRecords);
			}



			/* ORDER BY

			$_POST['orderby'] = 'ColumnName ASC, ColumnName DESC'

			*/
			if (isset($_POST['orderby'])){
				$sanitizedOrderBy = stripcslashes($_POST['orderby']);
				$sanitizedOrderBy = mysql_real_escape_string($sanitizedOrderBy);
				$sanitizedOrderBy = explode(",",$sanitizedOrderBy);
			}


			/* WHERE CLAUSE NA EQUALS ANG CONDITION: */
			if (!empty($_POST['conditions']['equals'])){
				foreach ($_POST['conditions']['equals'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereEqualsConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereEqualsConditions[$keyName] = mysql_real_escape_string($sanitizedWhereEqualsConditions[$keyName]);
					}

				}
			}

			/* WHERE CLAUSE NA NOT EQUALS ANG CONDITION: */
			if (!empty($_POST['conditions']['not_equals'])){
				foreach ($_POST['conditions']['not_equals'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereNotEqualsConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereNotEqualsConditions[$keyName] = mysql_real_escape_string($sanitizedWhereNotEqualsConditions[$keyName]);
					}

				}
			}


			/* WHERE CLAUSE NA LIKE ANG CONDITION: */
			if (!empty($_POST['conditions']['like'])){
				foreach ($_POST['conditions']['like'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereLikeConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereLikeConditions[$keyName] = mysql_real_escape_string($sanitizedWhereLikeConditions[$keyName]);
						$sanitizedWhereLikeConditions[$keyName] = trim($sanitizedWhereLikeConditions[$keyName]," ");
						//$sanitizedWhereLikeConditions[$keyName] = str_replace(" ","%",$sanitizedWhereLikeConditions[$keyName]);
					}

				}
			}

			/* WHERE CLAUSE NA IN ANG CONDITION: */
			if (!empty($_POST['conditions']['in'])){
				foreach ($_POST['conditions']['in'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereInConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereInConditions[$keyName] = mysql_real_escape_string($sanitizedWhereInConditions[$keyName]);
					}

				}
			}

			/* WHERE CLAUSE NA NOT IN ANG CONDITION: */
			if (!empty($_POST['conditions']['not_in'])){
				foreach ($_POST['conditions']['not_in'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereNotInConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereNotInConditions[$keyName] = mysql_real_escape_string($sanitizedWhereNotInConditions[$keyName]);
					}

				}
			}

			/* WHERE CLAUSE NA IN ANG CONDITION: */
			if (!empty($_POST['conditions']['find_in_set'])){
				foreach ($_POST['conditions']['find_in_set'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereFindInSetConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereFindInSetConditions[$keyName] = mysql_real_escape_string($sanitizedWhereFindInSetConditions[$keyName]);
					}

				}
			}

			/* `less_than` CONDITIONS */
			if (!empty($_POST['conditions']['less_than'])){
				foreach ($_POST['conditions']['less_than'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereLessThanConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereLessThanConditions[$keyName] = mysql_real_escape_string($sanitizedWhereLessThanConditions[$keyName]);
					}

				}
			}

			/* `greater_than` CONDITIONS */
			if (!empty($_POST['conditions']['greater_than'])){
				foreach ($_POST['conditions']['greater_than'] as $keyName => $keyValue){
					if (isset($keyValue)){
						$sanitizedWhereGreaterThanConditions[$keyName] = stripcslashes($keyValue);
						$sanitizedWhereGreaterThanConditions[$keyName] = mysql_real_escape_string($sanitizedWhereGreaterThanConditions[$keyName]);
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


			$notequalsConditions = "";
			$equalsConditions = "";
			$likeConditions = "";
			$inConditions = "";
			$findInSetConditions = "";
			$notinConditions = "";
			$lessThanConditions = "";
			$greaterThanConditions = "";

			foreach ($sanitizedWhereEqualsConditions as $keyName => $keyValue) {
				$equalsConditions = $equalsConditions . "$tableName.$keyName='$keyValue' AND ";
			}
			$equalsConditions = rtrim($equalsConditions, "AND ");
			foreach ($sanitizedWhereNotEqualsConditions as $keyName => $keyValue) {
				$notequalsConditions = $notequalsConditions . "$tableName.$keyName<>'$keyValue' AND ";
			}
			$notequalsConditions = rtrim($notequalsConditions, "AND ");

			foreach ($sanitizedWhereLikeConditions as $keyName => $keyValue) {
				$likeConditions = $likeConditions . "LCASE($tableName.$keyName) LIKE '%$keyValue%' OR ";
			}
			$likeConditions = rtrim($likeConditions, "OR ");
			if ($likeConditions!=""){
				$likeConditions = " (" . $likeConditions. ") ";
			}

			foreach ($sanitizedWhereInConditions as $keyName => $keyValue) {
				$arr = array();
				$arr = explode(",",$keyValue);
				if (!empty($arr)){
					$inConditions = $inConditions . "$tableName.$keyName IN (";
					foreach ($arr as $str){
						$inConditions = $inConditions . "'$str',";
					}
					$inConditions = rtrim($inConditions,",");
					$inConditions = $inConditions . ") AND ";
				}
			}
			$inConditions = rtrim($inConditions, "AND ");

			foreach ($sanitizedWhereNotInConditions as $keyName => $keyValue) {
				$arr = array();
				$arr = explode(",",$keyValue);
				if (!empty($arr)){
					$notinConditions = $notinConditions . "NOT $tableName.$keyName IN (";
					foreach ($arr as $str){
						$notinConditions = $notinConditions . "'$str',";
					}
					$notinConditions = rtrim($notinConditions,",");
					$notinConditions = $notinConditions . ") AND ";
				}
			}
			$notinConditions = rtrim($notinConditions, "AND ");

			foreach ($sanitizedWhereFindInSetConditions as $keyName => $keyValue) {
				$findInSetConditions = $findInSetConditions . "FIND_IN_SET('$keyValue', `$keyName`) AND ";
			}
			$findInSetConditions = rtrim($findInSetConditions, " AND ");

			/* `less_than` conditions */
			foreach ($sanitizedWhereLessThanConditions as $keyName => $keyValue) {
				$lessThanConditions = $lessThanConditions . "$tableName.$keyName < '$keyValue' AND ";
			}
			$lessThanConditions = rtrim($lessThanConditions, "AND ");

			/* `greater_than` conditions */
			foreach ($sanitizedWhereGreaterThanConditions as $keyName => $keyValue) {
				$greaterThanConditions = $greaterThanConditions . "$tableName.$keyName > '$keyValue' AND ";
			}
			$greaterThanConditions = rtrim($greaterThanConditions, "AND ");

			if ($notequalsConditions!==""){
				$query = $query . " WHERE $notequalsConditions ";
				if ($equalsConditions!==""||$likeConditions!==""||$inConditions!==""||$notinConditions!==""){
					$query = $query . " AND ";
				}
			}

			if ($equalsConditions!==""){
				if ($notequalsConditions===""){
					$query = $query . " WHERE ";
				}
				$query = $query . " $equalsConditions ";
				if ($likeConditions!==""||$inConditions!==""||$notinConditions!==""){
					$query = $query . " AND ";
				}
			}


			if ($likeConditions!==""){
				if ($equalsConditions===""&&$notequalsConditions===""){
					$query = $query . " WHERE ";
				}
				$query = $query . " $likeConditions ";
				if ($inConditions!==""||$notinConditions!==""){
					$query = $query . " AND ";
				}
			}

			if ($inConditions!==""){
				if ($equalsConditions==='' && $likeConditions===''&&$notequalsConditions===""){
					$query = $query . " WHERE ";
				}
				$query = $query . " $inConditions ";
				if ($notinConditions!==""){
					$query = $query . " AND ";
				}
			}

			if ($notinConditions!==""){
				if ($equalsConditions==='' && $likeConditions===''&&$notequalsConditions===""&& $inConditions===""){
					$query = $query . " WHERE ";
				}
				$query = $query . " $notinConditions ";
			}

			if ($findInSetConditions!==""){
				if ($equalsConditions==='' && $likeConditions===''&&$notequalsConditions==="" && $inConditions===""&& $notinConditions===""){
					$query = $query . " WHERE ";
				} else {
					$query = $query . " AND ";
				}
				$query = $query . " $findInSetConditions ";
			}

			/* `less_than` conditions */
			if ($lessThanConditions) {

				if (!$equalsConditions && !$likeConditions && !$notequalsConditions  && !$inConditions
						&& !$notinConditions) {
					$query .= " WHERE";
				} else {
					$query .= " AND ";
				}

				$query .= " " . $lessThanConditions . " ";

			}

			/* `greater_than` conditions */
			if ($greaterThanConditions) {

				if (!$equalsConditions && !$likeConditions && !$notequalsConditions
						&& !$inConditions && !$notinConditions && !$lessThanConditions) {
					$query .= " WHERE";
				} else {
					$query .= " AND ";
				}

				$query .= " " . $greaterThanConditions . " ";

			}

			if (!empty($sanitizedOrderBy)){
				$query = $query . " ORDER BY ";
				foreach ($sanitizedOrderBy as $keyValue){
					$query = $query . " $tableName.$keyValue ,";
				}
				$query = rtrim($query,",");
			}

			if ($sanitizedLimitStart!==""){
				$query = $query . " LIMIT $sanitizedLimitStart";
			}

			if ($sanitizedNoRecords!==""){
				if ($sanitizedLimitStart===""){
					$query = $query . " LIMIT 0";
				}
				$query = $query . ",$sanitizedNoRecords";
			}

			$query = $query . ";";

		}


		/*if ($tableName=="trnartransaction"){
			echo $query;
		}*/


		//echo $query ."<br><br><br>";

		// execute query here
		//echo $query;
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
								$resultArray[$key] = $value;
							}

						}
					}
					else {
						$resultArray[$keyValue] = $row[$keyValue];
					}
				}
				$resultArray["count"] = ($_POST['action']==='get_table_record_count')?$row["tableCount"]:$count;
				$resultArray["result"] = '1';
				//$resultArray["query"] = $query;
				array_push($outputArray,$resultArray);
			}




		}







		/*print_r($outputArray);*/
		$dbAccess->closeCon();
		echo json_encode( $outputArray,true);


	}
}
else {
	// illegal access error
}
?>