<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../api/DatabaseConnect.php");
date_default_timezone_set('Asia/Manila');
$dbAccess = new DatabaseAccess();
$outputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['action']) || $_POST['action']!=='') {

		$insertFields = "";
		$insertValues = "";

		$db = @$_POST['dbconnect'];
		$db = stripslashes($db);
		$access =  $dbAccess->connectDB($db);
		$db = mysql_real_escape_string($db);

		$module_code = @$_POST['module_code'];
		$module_code = stripslashes($module_code);
		$module_code = mysql_real_escape_string($module_code);

		$user_code = @$_POST['user_code'];
		$user_code = stripslashes($user_code);
		$user_code = mysql_real_escape_string($user_code);
		$ctr = 0;

		$proceedToGroupPrivilege = true;
		$alreadyAdded = false;
		$isDenied = false;

		/* RETRIEVE APPLICATION PARAMETERS FOR ADD,EDIT,DELETE,PRINT,ACCESS */
		$parameters = array();
		$query = "SELECT mstapplicationparameter.parameter_key,mstapplicationparameter.parameter_value FROM mstapplicationparameter WHERE ";
		$query = $query . "mstapplicationparameter.parameter_key IN ('module_access_code','module_action_add_code','module_action_edit_code','module_action_delete_code','module_action_print_code') ";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {
			
		} // if (mysql_num_rows($result) <= 0) { -- mstuser
		else {
			while($row=mysql_fetch_array($result)) {
				$parameters[$row["parameter_key"]] = $row["parameter_value"];
			} // while($row=mysql_fetch_array($result)) {
		} // ELSE ng if (mysql_num_rows($result) <= 0) {
		/* END - RETRIEVE APPLICATION PARAMETERS FOR ADD,EDIT,DELETE,PRINT,ACCESS */




		/* FIND GROUPS OF USER */
		$user_groups = "";
		$query = "SELECT mstuser.user_group_mst_codes FROM mstuser WHERE mstuser.code = '$user_code'; ";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {
			
		} // if (mysql_num_rows($result) <= 0) { -- mstuser
		else {
			while($row=mysql_fetch_array($result)) {
				$user_groups = $row["user_group_mst_codes"];
			} // while($row=mysql_fetch_array($result)) {
			$arr = array();
			$arr = explode(",",$user_groups);
			$user_groupsIn = "";
			if (!empty($arr)){
				$user_groupsIn = $user_groupsIn . "";
				foreach ($arr as $str){
					$user_groupsIn = $user_groupsIn . "'$str',";
				} // foreach ($arr as $str){
				$user_groupsIn = rtrim($user_groupsIn,",");
				$user_groupsIn = $user_groupsIn . "";
			} // if (!empty($arr)){
		} // ELSE ng if (mysql_num_rows($result) <= 0) {
		/* END - FIND GROUPS OF USER */




		/* RETRIEVE THE MODULE AND ITS ACTIONS */
		$module_action_codes = "";
		$query = "SELECT mstmodule.module_action_dtl_codes FROM mstmodule WHERE mstmodule.code = '$module_code'; ";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {
			
		} // if (mysql_num_rows($result) <= 0) { -- mstuser
		else {
			while($row=mysql_fetch_array($result)) {
				$module_action_codes = $row["module_action_dtl_codes"];
			} // while($row=mysql_fetch_array($result)) {
			$arr = array();
			$arr = explode(",",$module_action_codes);
			$module_actionsIn = "";
			if (!empty($arr)){
				$module_actionsIn = $module_actionsIn . "";
				foreach ($arr as $str){
					$module_actionsIn = $module_actionsIn . "'$str',";
				} // foreach ($arr as $str){
				$module_actionsIn = rtrim($module_actionsIn,",");
				$module_actionsIn = $module_actionsIn . "";
			} // if (!empty($arr)){
		} // ELSE ng if (mysql_num_rows($result) <= 0) {
		/* END - RETRIEVE THE MODULE AND ITS ACTIONS */



		$arr = array();
		/* GET THE ACTUAL PRIVILEGE - STORE TO ARRAY */
		// USER MUNA -- TO CHECK IF DENIED BA SIYA AS A USER. PRIORITY
		$query = "SELECT mstuserprivilege.type,mstuserprivilege.type_mst_code,mstuserprivilege.module_mst_code,mstuserprivilege.module_action_mst_code,mstuserprivilege.access_type ";
		$query = $query . " FROM mstuserprivilege WHERE ";
		$query = $query . " mstuserprivilege.type = '1' AND mstuserprivilege.type_mst_code = '$user_code' AND ";
		$query = $query . " mstuserprivilege.module_mst_code = '$module_code' ORDER BY mstuserprivilege.module_action_mst_code ";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {
			$proceedToGroupPrivilege = true;
			$isDenied = false;
		} // if (mysql_num_rows($result) <= 0) { -- mstuser
		else {	
			while($row=mysql_fetch_array($result)) {
				if (($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="1")&&$isDenied==false&&array_key_exists($row["module_action_mst_code"], $arr)==false){
					$arr[$row["module_action_mst_code"]] = $row["access_type"];
					$ctr++;
					$proceedToGroupPrivilege = false;

				} // if (($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="1")||$isDenied==false){
				else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"&&array_key_exists($row["module_action_mst_code"], $arr)==false){
					$arr[$row["module_action_mst_code"]] = $row["access_type"];
					$isDenied = true;
					$proceedToGroupPrivilege = false;
					$ctr++;
				} // else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"){
				else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="0"){
					$proceedToGroupPrivilege = true;
				}
				else if ($isDenied==false&&array_key_exists($row["module_action_mst_code"], $arr)==false && $row["access_type"]!="0") {
					$arr[$row["module_action_mst_code"]] = $row["access_type"];
					$ctr++;
					$proceedToGroupPrivilege = false;
				} // ELSE ng else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"){

				
			} // while($row=mysql_fetch_array($result)) {

			if (count($arr)>0){
				//array_push($outputArray,$arr);
			} // if (count($arr)>0){

		} // ELSE ng if (mysql_num_rows($result) <= 0) {

		// IF NOT USER, PROCEED TO GROUP
		//if ($proceedToGroupPrivilege==true&&$isDenied==false){
		if ($isDenied==false){
			$query = "SELECT mstuserprivilege.type,mstuserprivilege.type_mst_code,mstuserprivilege.module_mst_code,mstuserprivilege.module_action_mst_code,mstuserprivilege.access_type ";
			$query = $query . " FROM mstuserprivilege WHERE ";
			$query = $query . " mstuserprivilege.type = '2' AND mstuserprivilege.type_mst_code IN ($user_groupsIn) AND ";
			$query = $query . " mstuserprivilege.module_mst_code = '$module_code' ORDER BY  mstuserprivilege.module_action_mst_code, mstuserprivilege.type_mst_code ";
			$result=mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result) <= 0) {
			
			} // if (mysql_num_rows($result) <= 0) { -- mstuser
			else {	
				//$arr = array();
				while($row=mysql_fetch_array($result)) {
					if (($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="1")&&$isDenied==false&&array_key_exists($row["module_action_mst_code"], $arr)==false){
						$arr[$row["module_action_mst_code"]] = $row["access_type"];
						$ctr++;
					} // if (($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="1")||$isDenied==false){
					else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"&&array_key_exists($row["module_action_mst_code"], $arr)==false){
						$arr[$row["module_action_mst_code"]] = $row["access_type"];
						$isDenied = true;
						$ctr++;
					} // else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"){
					else if ($isDenied==false&&array_key_exists($row["module_action_mst_code"], $arr)==false && $row["access_type"]!="0") {
						$arr[$row["module_action_mst_code"]] = $row["access_type"];
						$ctr++;

					} // ELSE ng else if ($row["module_action_mst_code"]=="-1"&&$row["access_type"]=="2"){
					
				} // while($row=mysql_fetch_array($result)) {

				if (count($arr)>0){
					//array_push($outputArray,$arr);
				} // if (count($arr)>0){

			} // ELSE ng if (mysql_num_rows($result) <= 0) {

		} // if ($proceedToGroupPrivilege==true){


		if ($ctr<=0){
			$arr = array(
				"result" => "0"
			);
			//array_push($outputArray,$arr);
		} // if ($ctr<=0){

		$dbAccess->closeCon();
		echo json_encode($arr);
		

	} // if (isset($_POST['action']) || $_POST['action']!=='') {
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	// illegal access error
} // else ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {
?>