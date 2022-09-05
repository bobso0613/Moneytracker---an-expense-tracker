<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");
date_default_timezone_set('Asia/Manila');


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


	if ($_POST["transactionmode"]=="can_add"){

	} // if ($_POST["transactionmode"]=="can_add"){
	else if ($_POST["transactionmode"]=="can_edit"){
	    $link = DB_LOCATION;
	    $params = array (
	        "action" => "retrieve-record-column",
	        "fileToOpen" => "default_select_query",
	        "tableName" => $_POST["tableName"],
	        "dbconnect" => $_POST["dbconnect"],
	        "columns" => str_replace("|", ",", $_POST["columnstoquery"]),
	        "conditions[equals][".$_POST["primarycodefields"]."]" => $_POST["primarycodevalue"],
	        "orderby" => $_POST["primarycodefields"]." ASC"
	    );
	    $result=processCurl($link,$params);
	    $retrievedRecordRow = json_decode($result,true);
	} // else if ($_POST["transactionmode"]=="can_edit"){


	// LOOP ALL COLUMNS - USE $column_key for index
	foreach ($columns as $column_key => $column_name){
		// DETERMINE WHAT DATA TYPE 
	    switch ($columnsDataType[$column_key]){
	    	//1 - Character (one line)
	        //2 - Integer
	        //3 - Decimal
	        //4 - Date
	        //5 - Character (Paragraph)
	        //6 - Character (Text Editor)
	    	case "1":
	        case "2":
	        case "3":
	        case "4": 
	        case "5":
	        case "6":
	        	// required check
	        	if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){
	                $arr = array(
						"result" => "0",
						"error_message" => $columnsFieldName[$column_key]." must not be blank."
					);
					array_push($outputArray,$arr);
	            } /* if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){ */

	            // max length check
	            else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    strlen($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])>intval($columnsMaxLength[$column_key])&&
	                    intval($columnsMaxLength[$column_key])>0){
	                $arr = array(
						"result" => "0",
						"error_message" => $columnsFieldName[$column_key]." length exceeds the maximum length. (Should be at most ".intval($columnsMaxLength[$column_key])." character/s.)"
					);
					array_push($outputArray,$arr);
	            } /* else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    strlen($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])>intval($columnsMaxLength[$column_key])&&
	                    intval($columnsMaxLength[$column_key])>0){ */

	            // unique check
	            else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){

	            	$link = DB_LOCATION;
				    $params = array (
				        "action" => "retrieve-record-column",
				        "fileToOpen" => "default_select_query",
				        "tableName" => $_POST["tableName"],
				        "dbconnect" => $_POST["dbconnect"],
				        "columns" => "code",
				        "conditions[equals][".$column_name."]" => $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name],
				        "orderby" => "code ASC"
				    );
				    if ($_POST["transactionmode"]=="can_edit"){
				    	$params["conditions[not_equals][".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
				    } // if ($_POST["transactionmode"]=="can_edit"){
				    $result=processCurl($link,$params);
				    $retrievedRecordRow = json_decode($result,true);
				    if ($retrievedRecordRow[0]["result"]=="1"){
				    	$arr = array(
							"result" => "0",
							"error_message" => "'".$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name] . "' entry for ". $columnsFieldName[$column_key]." already exists (must be unique)."
						);
						array_push($outputArray,$arr);
				    } // if ($retrievedRecordRow[0]["result"]=="1"){
	                
	            } /* else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){
	    		*/

	        break;


	        //7 - Combo Box
	        //10 - Lookup
	        case "7":
	        case "10":
	        	// required check
	        	if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){
	                $arr = array(
						"result" => "0",
						"error_message" => "Please select a/n ".$columnsFieldName[$column_key]."."
					);
					array_push($outputArray,$arr);
	            } /* if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){ */

				// unique check
	            else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){

	            	$link = DB_LOCATION;
				    $params = array (
				        "action" => "retrieve-record-column",
				        "fileToOpen" => "default_select_query",
				        "tableName" => $_POST["tableName"],
				        "dbconnect" => $_POST["dbconnect"],
				        "columns" => "code",
				        "conditions[equals][".$column_name."]" => $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name],
				        "orderby" => "code ASC"
				    );
				    if ($_POST["transactionmode"]=="can_edit"){
				    	$params["conditions[not_equals][".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
				    } // if ($_POST["transactionmode"]=="can_edit"){
				    $result=processCurl($link,$params);
				    $retrievedRecordRow = json_decode($result,true);
				    if ($retrievedRecordRow[0]["result"]=="1"){
				    	$arr = array(
							"result" => "0",
							"error_message" => "'".$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name] . "' value for ". $columnsFieldName[$column_key]." already exists (must be unique)."
						);
						array_push($outputArray,$arr);
				    } // if ($retrievedRecordRow[0]["result"]=="1"){
	                
	            } /* else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){
	    		*/
	        break;

	        //8 - Radio Button
	        case "8":
	        	// required check
	        	if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){
	                $arr = array(
						"result" => "0",
						"error_message" => "Please choose a/n ".$columnsFieldName[$column_key]."."
					);
					array_push($outputArray,$arr);
	            } /* if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==''&&
	                    $columnsIsRequired[$column_key]=="1"){ */

				// unique check
	            else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){

	            	$link = DB_LOCATION;
				    $params = array (
				        "action" => "retrieve-record-column",
				        "fileToOpen" => "default_select_query",
				        "tableName" => $_POST["tableName"],
				        "dbconnect" => $_POST["dbconnect"],
				        "columns" => "code",
				        "conditions[equals][".$column_name."]" => $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name],
				        "orderby" => "code ASC"
				    );
				    if ($_POST["transactionmode"]=="can_edit"){
				    	$params["conditions[not_equals][".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
				    } // if ($_POST["transactionmode"]=="can_edit"){
				    $result=processCurl($link,$params);
				    $retrievedRecordRow = json_decode($result,true);
				    if ($retrievedRecordRow[0]["result"]=="1"){
				    	$arr = array(
							"result" => "0",
							"error_message" => "'".$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name] . "' value for ". $columnsFieldName[$column_key]." already exists (must be unique)."
						);
						array_push($outputArray,$arr);
				    } // if ($retrievedRecordRow[0]["result"]=="1"){
	                
	            } /* else if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
	                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''&&
	                    $columnsIsUnique[$column_key]=="1"){
	    		*/
	        break;


	        //9 - Checkbox
	        case "9":
	        	// required check
	        	if(empty($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]) && 
	        		$columnsIsRequired[$column_key]=="1") {
	        		$arr = array(
						"result" => "0",
						"error_message" => "Please choose at least one ".$columnsFieldName[$column_key]."."
					);
					array_push($outputArray,$arr);
	        	} /* if(empty($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]) && 
	        		$columnsIsRequired[$column_key]=="1") { */

				// unique check
				else if(!empty($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]) && 
	        		$columnsIsUnique[$column_key]=="1") {

					$checkBoxValues = implode(",",$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]);
	        		
					$link = DB_LOCATION;
				    $params = array (
				        "action" => "retrieve-record-column",
				        "fileToOpen" => "default_select_query",
				        "tableName" => $_POST["tableName"],
				        "dbconnect" => $_POST["dbconnect"],
				        "columns" => "code",
				        "conditions[equals][".$column_name."]" => $checkBoxValues,
				        "orderby" => "code ASC"
				    );
				    if ($_POST["transactionmode"]=="can_edit"){
				    	$params["conditions[not_equals][".$_POST["primarycodefields"]."]"] = $_POST["primarycodevalue"];
				    } // if ($_POST["transactionmode"]=="can_edit"){
				    $result=processCurl($link,$params);
				    $retrievedRecordRow = json_decode($result,true);
				    if ($retrievedRecordRow[0]["result"]=="1"){
				    	$arr = array(
							"result" => "0",
							"error_message" => "'". $checkBoxValues. "' value/s for ". $columnsFieldName[$column_key]." already exists (must be unique)."
						);
						array_push($outputArray,$arr);
				    } // if ($retrievedRecordRow[0]["result"]=="1"){

	        	} /* else if(!empty($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]) && 
	        		$columnsIsUnique[$column_key]=="1") { */
	        break;
	    } // switch ($columnsDataType[$column_key]){


	} // foreach ($columns as $column_key => $column_name){


	/*$arr = array(
		"result" => "0",
		"error_message" => "error eh"
	);
	array_push($outputArray,$arr);*/
	if (count($outputArray)<=0){
		$arr = array(
			"result" => "1",
			"error_message" => "Proceed"
		);
		array_push($outputArray,$arr);
	} // if (count($outputArray)<=0){
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