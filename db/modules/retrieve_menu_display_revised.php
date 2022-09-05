<?php
//error_reporting(0);

//error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: POST-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
date_default_timezone_set('Asia/Manila');
@include_once("../api/DatabaseConnect.php");
@include_once("../api/SanitizeField.php");


$larr_outputArray = array();
$larr_ResultQueryArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	$lch_dbAccess = new DatabaseAccess();
	$lch_dbAccess->connectDB(MONEYTRACKER_DB);

	// COMMON FOR ALL
	$lch_action = sanitizeField(@$_POST['action']);
	$menu_program_name = sanitizeField(@$_POST['menu_program_name']);
	$user_code = sanitizeField(@$_POST['user_code']);

	// APPLICATION PARAMETERS
	$larr_AppParams = array();
	
	$lch_Query = "SELECT mstapplicationparameter.* FROM mstapplicationparameter WHERE ";
	$lch_Query = $lch_Query . " mstapplicationparameter.parameter_key IN ('module_access_code')";
	$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
	while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
		$larr_AppParams [$larr_ResultRow["parameter_key"]] = $larr_ResultRow["parameter_value"];
	} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

	if ($lch_action=="retrieve_menu_per_privilege") {

		$larr_MSTUser = array();
		$larr_UserPrivilegePerModule = array();
		$larr_MSTModuleCodes = array();
		$larr_MSTModuleCodesRecords = array();

		// retrieve user groups
		$lch_dbAccess = new DatabaseAccess();
		$lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstuser.user_group_mst_codes, mstuser.code FROM mstuser WHERE ";
		$lch_Query = $lch_Query . " mstuser.code = '$user_code' ";
		$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			$larr_MSTUser = $larr_ResultRow;
		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {


		// retrieve muna lahat ng privilege para madali na.. (1 query only = 1 read only)
		$lch_dbAccess = new DatabaseAccess();
		$lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstuserprivilege.* FROM mstuserprivilege,mstmodule WHERE ";
		$lch_Query = $lch_Query . " mstuserprivilege.module_action_mst_code = '".$larr_AppParams["module_access_code"]."' AND ";
		$lch_Query = $lch_Query . " mstuserprivilege.access_type <> '0' AND ";
		$lch_Query = $lch_Query . " mstmodule.is_enabled = '1' AND ";
		if ($larr_MSTUser["user_group_mst_codes"]!="") {
			$lch_Query = $lch_Query . " ((mstuserprivilege.type = '1' AND mstuserprivilege.type_mst_code = '".$larr_MSTUser["code"]."') ";
			$lch_Query = $lch_Query . " OR (mstuserprivilege.type = '2' AND mstuserprivilege.type_mst_code IN (".$larr_MSTUser["user_group_mst_codes"].")) )";
		} // if ($larr_MSTUser["user_group_mst_codes"]!="") {
		else {
			$lch_Query = $lch_Query . " mstuserprivilege.type = '1' AND mstuserprivilege.type_mst_code = '".$larr_MSTUser["code"]."' ";
		} // else ng if ($larr_MSTUser["user_group_mst_codes"]!="") {	
		$lch_Query = $lch_Query . " ORDER BY mstuserprivilege.module_mst_code ASC, mstuserprivilege.type ASC, mstuserprivilege.type_mst_code ASC ";
		$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
			if (!array_key_exists($larr_ResultRow["module_mst_code"], $larr_UserPrivilegePerModule)) {
				$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]] = array ("access_type"=>$larr_ResultRow["access_type"],
																				"processed_user"=>false,
																				"group_already_true"=>false);
			} // if (!array_key_exists($larr_ResultRow["module_mst_code"], $larr_UserPrivilegePerModule)) {

			// pag user -> special override.. i prioritize nya yung privilege on a user level instead na group level
			if ($larr_ResultRow["type"]=="1") {
				$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["processed_user"] = true;
				$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["access_type"] = $larr_ResultRow["access_type"];
				$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["group_already_true"]=true;

				if ($larr_ResultRow["access_type"]=="1") {
					if (!in_array($larr_ResultRow["module_mst_code"], $larr_MSTModuleCodes) && $larr_ResultRow["access_type"]=="1") {
						array_push ($larr_MSTModuleCodes,$larr_ResultRow["module_mst_code"]);
					} // if (!in_array($larr_ResultRow["module_mst_code"], $larr_MSTModuleCodes)) {
				} // if ($larr_ResultRow["access_type"]=="1") {

			} // if ($larr_ResultRow["type"]=="1") {
			else if ($larr_ResultRow["type"]=="2" && !$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["processed_user"] ) {

				if (!$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["group_already_true"]) {
					$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["access_type"] = $larr_ResultRow["access_type"];
					if ($larr_ResultRow["access_type"]=="1") {
						$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["group_already_true"]=true;
					} // if ($larr_ResultRow["access_type"]=="1") {

					if (!in_array($larr_ResultRow["module_mst_code"], $larr_MSTModuleCodes) && $larr_ResultRow["access_type"]=="1") {
						array_push ($larr_MSTModuleCodes,$larr_ResultRow["module_mst_code"]);
					} // if (!in_array($larr_ResultRow["module_mst_code"], $larr_MSTModuleCodes)) {
				} // if (!$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["group_already_true"]) {
				
			} // else if ($larr_ResultRow["type"]=="2" && !$larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["processed_user"]) {

		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		// find the module table records sa lahat ng merong privilege
		if (count($larr_MSTModuleCodes)>0) {

			$lch_dbAccess = new DatabaseAccess();
			$lch_dbAccess->connectDB(MONEYTRACKER_DB);
			$lch_Query = "SELECT mstmodule.* FROM mstmodule ";
			$lch_Query = $lch_Query . " WHERE mstmodule.code IN (".implode(",", $larr_MSTModuleCodes).") ";
			$lch_Query = $lch_Query . " AND mstmodule.is_enabled = '1' ";
			$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
				$larr_MSTModuleCodesRecords[$larr_ResultRow["code"]] = $larr_ResultRow;
			} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		} // if (count($larr_MSTModuleCodes)>0) {

		// retrieve the actual menu structure - FOR ACTUAL PROGRAMS
		$larr_ActualMenuResult = array();
		$larr_ActualMenuDisplay = array();
		$lch_dbAccess = new DatabaseAccess();
		$lch_dbAccess->connectDB(MONEYTRACKER_DB);
		$lch_Query = "SELECT mstmenuitem.* FROM mstmenuitem WHERE ";
		$lch_Query = $lch_Query . " mstmenuitem.is_active = '1' ";
		$lch_Query = $lch_Query . " ORDER BY mstmenuitem.menu_item_mst_code ASC, mstmenuitem.order_no ASC ";
		$SQL_RESULT = mysql_query($lch_Query) or die(mysql_error());
		while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

			$lin_layer = 0;

			//$lin_currenparentcode = $larr_ResultRow["menu_item_mst_code"];

			// push as is
			if (!array_key_exists($larr_ResultRow["menu_item_mst_code"], $larr_ActualMenuDisplay)){
				$larr_ActualMenuDisplay[$larr_ResultRow["menu_item_mst_code"]] = array();
			} // if (!array_key_exists($larr_ResultRow["menu_item_mst_code"], $larr_ActualMenuDisplay)){

			if ($larr_ResultRow["type"]=="1") {
				$larr_ActualMenuDisplay[$larr_ResultRow["menu_item_mst_code"]][$larr_ResultRow["code"]] =
					array("type"=>"1",
						"title"=>$larr_ResultRow["menu_name"],
						"menu_name"=>$larr_ResultRow["menu_name"],
						"children"=>array());
			} // if ($larr_ResultRow["type"]=="1") {
			else {

				if (array_key_exists($larr_ResultRow["module_mst_code"], $larr_UserPrivilegePerModule) && $larr_UserPrivilegePerModule[$larr_ResultRow["module_mst_code"]]["access_type"]=="1" ) {
					$larr_ActualMenuDisplay[$larr_ResultRow["menu_item_mst_code"]][$larr_ResultRow["code"]] = 
						array("type"=>"2",
							"title"=>$larr_MSTModuleCodesRecords[$larr_ResultRow["module_mst_code"]]["module_name"],
							"menu_name"=>$larr_MSTModuleCodesRecords[$larr_ResultRow["module_mst_code"]]["short_name"],
							"program_name"=>$larr_MSTModuleCodesRecords[$larr_ResultRow["module_mst_code"]]["program_name"],
							"active"=> (($menu_program_name==$larr_MSTModuleCodesRecords[$larr_ResultRow["module_mst_code"]]["program_name"])?"1":"0") );
				} // if (array_key_exists($larr_ResultRow["module_mst_code"], $larr_UserPrivilegePerModule)) {

				
				
			} // ELSE ng if ($larr_ResultRow["type"]=="1") {
			

		} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {

		// first layer loop
		foreach ($larr_ActualMenuDisplay as $lch_Key => $larr_Value) {
			if (array_key_exists($lch_Key, $larr_ActualMenuDisplay)) {
				// 2nd layer loop
				foreach ($larr_Value as $lch_InnerKey => $larr_InnerValue) {

					if ($larr_InnerValue["type"]=="1") {
						$larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"] = $larr_ActualMenuDisplay[$lch_InnerKey];
						unset($larr_ActualMenuDisplay[$lch_InnerKey]);

						// 3rd layer loop
						if (!is_null($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"])) {
							foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"] as $lch_InnerKey02 => $larr_InnerValue02) {
								//echo json_encode($larr_ActualMenuDisplay[$lch_InnerKey02]) . " ---- " . $larr_InnerValue["type"] . "XXXXXXX \n\n" ;
								if ($larr_InnerValue02["type"]=="1") {
									$larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"] = $larr_ActualMenuDisplay[$lch_InnerKey02];
									unset($larr_ActualMenuDisplay[$lch_InnerKey02]);


									// 4th layer loop
									if (!is_null ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"])) {
										foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"] as $lch_InnerKey03 => $larr_InnerValue03) {
											if ($larr_InnerValue03["type"]=="1") {
												$larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"] = $larr_ActualMenuDisplay[$lch_InnerKey03];
												unset($larr_ActualMenuDisplay[$lch_InnerKey03]);


												// 5th layer loop
												if (!is_null($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"])) {
													foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"] as $lch_InnerKey04 => $larr_InnerValue04) {
														if ($larr_InnerValue04["type"]=="1") {
															$larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"][$lch_InnerKey04]["children"] = $larr_ActualMenuDisplay[$lch_InnerKey04];
															unset($larr_ActualMenuDisplay[$lch_InnerKey04]);

														} // if ($larr_InnerValue04["type"]=="1") {
													} // foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"] as $lch_InnerKey04 => $larr_InnerValue04) {
												} // if (!is_null($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"])) {
												
												

											} // if ($larr_InnerValue03["type"]=="1") {
										} // foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"] as $lch_InnerKey03 => $larr_InnerValue03) {
									} // if (!is_null ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"][$lch_InnerKey02]["children"])) {

								} // if ($larr_InnerValue02["type"]=="1") {
							} // foreach ($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"] as $lch_InnerKey02 => $larr_InnerValue02) {
						} // if (!is_null($larr_ActualMenuDisplay[$lch_Key][$lch_InnerKey]["children"])) {

					} // if ($larr_InnerValue["type"]=="1") {
				} // foreach ($larr_Value as $lch_InnerKey => $larr_InnerValue) {

			} // if (array_key_exists($lch_Key, $larr_ActualMenuDisplay)) {
		} // foreach ($larr_ActualMenuDisplay as $lch_Key => $larr_Value) {

		foreach ($larr_ActualMenuDisplay[0] as $lch_Key => $larr_Value) {
			if ($larr_Value["type"]=="1"){

				foreach ($larr_Value["children"] as $lch_InnerKey => $larr_InnerValue) {
					if ($larr_InnerValue["type"]=="1"){

						foreach ($larr_InnerValue["children"] as $lch_InnerKey02 => $larr_InnerValue02) {
							if ($larr_InnerValue02["type"]=="1"){

								if(!is_null($larr_InnerValue02["children"])) {
									foreach ($larr_InnerValue02["children"] as $lch_InnerKey03 => $larr_InnerValue03) {
										if ($larr_InnerValue03["type"]=="1"){


											if(!is_null($larr_InnerValue03["children"])) {
												foreach ($larr_InnerValue03["children"] as $lch_InnerKey04 => $larr_InnerValue04) {
													if ($larr_InnerValue03["type"]=="1"){

														if (count($larr_InnerValue03["children"])<=0) {
															unset($larr_ActualMenuDisplay[0][$lch_Key]["children"][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"][$lch_InnerKey04]);

															unset($larr_Value["children"][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"][$lch_InnerKey04]);
															unset($larr_InnerValue["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]["children"][$lch_InnerKey04]);
															unset($larr_InnerValue02["children"][$lch_InnerKey03]["children"][$lch_InnerKey04]);
															unset($larr_InnerValue03["children"][$lch_InnerKey04]);
															unset($larr_InnerValue04);

														} // if (count($larr_InnerValue["children"])<=0) {

													} // if ($larr_InnerValue03["type"]=="1"){
												} // foreach ($larr_InnerValue03["children"] as $lch_InnerKey04 => $larr_InnerValue04) {
											} // if(!is_null($larr_InnerValue03["children"])) {


											if (count($larr_InnerValue03["children"])<=0) {
												unset($larr_ActualMenuDisplay[0][$lch_Key]["children"][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]);

												unset($larr_Value["children"][$lch_InnerKey]["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]);
												unset($larr_InnerValue["children"][$lch_InnerKey02]["children"][$lch_InnerKey03]);
												unset($larr_InnerValue02["children"][$lch_InnerKey03]);
												unset($larr_InnerValue03);


											} // if (count($larr_InnerValue["children"])<=0) {
										} // if ($larr_InnerValue03["type"]=="1"){
									} // foreach ($larr_InnerValue02["children"] as $lch_InnerKey03 => $larr_InnerValue03) {
								} // if(!is_null($larr_InnerValue02["children"])) {


								if (count($larr_InnerValue02["children"])<=0 || is_null($larr_InnerValue02["children"])) {
									unset($larr_ActualMenuDisplay[0][$lch_Key]["children"][$lch_InnerKey]["children"][$lch_InnerKey02]);
									
									unset($larr_Value["children"][$lch_InnerKey]["children"][$lch_InnerKey02]);
									unset($larr_InnerValue["children"][$lch_InnerKey02]);
									unset($larr_InnerValue02);

								} // if (count($larr_InnerValue["children"])<=0) {
							} // if ($larr_InnerValue02["type"]=="1"){
						} // foreach ($larr_Value["children"] as $lch_InnerKey => $larr_InnerValue) {

						//echo $larr_InnerValue["menu_name"] . " - " .json_encode($larr_InnerValue["children"]) . "\n\n";
						if (count($larr_InnerValue["children"])<=0) {
							unset($larr_ActualMenuDisplay[0][$lch_Key]["children"][$lch_InnerKey]);

							unset($larr_Value["children"][$lch_InnerKey]);
							unset($larr_InnerValue);
						} // if (count($larr_InnerValue["children"])<=0) {



					} // if ($larr_InnerValue["type"]=="1"){
				} // foreach ($larr_Value["children"] as $lch_InnerKey => $larr_InnerValue) {



				if (count($larr_Value["children"])<=0) {
					unset($larr_ActualMenuDisplay[0][$lch_Key]);
					unset($larr_Value);
				} // if (count($larr_Value["children"])<=0) {
			} // if ($larr_Value["type"]=="1"){
		} // foreach ($larr_ActualMenuDisplay[0] as $lch_Key => $larr_Value) {

		if (count($larr_ActualMenuDisplay[0])>0) {
			$larr_ActualMenuDisplay["result"]=1;
			$larr_ActualMenuDisplay["data"]=$larr_ActualMenuDisplay[0];
			unset($larr_ActualMenuDisplay["0"]);
		} // if (count($larr_ActualMenuDisplay[0])>0) {
		else {
			$larr_ActualMenuDisplay["result"]=0;
		} // ELSE ng if (count($larr_ActualMenuDisplay[0])>0) {
		
		//$larr_ActualMenuDisplay["data"] = cleanArray($larr_ActualMenuDisplay["data"],);
		echo json_encode($larr_ActualMenuDisplay);
		//echo json_encode($larr_ActualMenuDisplay);

	} // else if ($lch_action=="retrieve_menu_per_privilege") {


} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
else {
	// illegal access error
	$larr_ResultQueryArray = array(
		"result" => "0",	
		"error_message" => "Invalid API Access."
	);
	array_push($larr_outputArray,$larr_ResultQueryArray);
	echo json_encode($larr_outputArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

// $lch_dbAccess->closeCon();

 ?>