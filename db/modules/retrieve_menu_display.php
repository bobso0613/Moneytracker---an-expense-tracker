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

		$parent_code = @$_POST['parent_code'];
		$parent_code = stripslashes($parent_code);
		$parent_code = mysql_real_escape_string($parent_code);

		$user_code = @$_POST['user_code'];
		$user_code = stripslashes($user_code);
		$user_code = mysql_real_escape_string($user_code);
		$ctr = 0;

		$proceedToGroupPrivilege = false;
		$alreadyAdded = false;

		// find first groups of user
		$user_groups = "";
		$query = "SELECT mstuser.user_group_mst_codes FROM mstuser WHERE mstuser.code = '$user_code'; ";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {
			
		} // if (mysql_num_rows($result) <= 0) { -- mstuser
		else {
			while($row=mysql_fetch_array($result)) {
				$user_groups = $row["user_group_mst_codes"];
			}
			$arr = array();
			$arr = explode(",",$user_groups);
			$user_groupsIn = "";
			if (!empty($arr)){
				$user_groupsIn = $user_groupsIn . "";
				foreach ($arr as $str){
					$user_groupsIn = $user_groupsIn . "'$str',";
				}
				$user_groupsIn = rtrim($user_groupsIn,",");
				$user_groupsIn = $user_groupsIn . "";
			}
		}

		/* to add user privilege -- if may access */
		/*$query = "SELECT mstmenuitem.*,mstmodule.short_name,mstmodule.module_name,mstmodule.program_name FROM mstmenuitem,mstmodule WHERE ";
		$query = $query . "mstmenuitem.is_active = '1' AND mstmenuitem.menu_item_mst_code = '$parent_code' ";
		$query = $query . " AND IF ( mstmenuitem.type = '2' ,(mstmodule.code = mstmenuitem.module_mst_code AND mstmodule.is_enabled = '1'), (mstmenuitem.module_mst_code = '0'))";
		$query = $query . " ORDER BY mstmenuitem.order_no";*/
		$query = "SELECT mstmenuitem.* FROM mstmenuitem WHERE ";
		$query = $query . "mstmenuitem.is_active = '1' AND mstmenuitem.menu_item_mst_code = '$parent_code' ";
		/*$query = $query . " AND IF ( mstmenuitem.type = '2' ,(mstmodule.code = mstmenuitem.module_mst_code AND mstmodule.is_enabled = '1'), (mstmenuitem.module_mst_code = '0'))";*/
		$query = $query . " ORDER BY mstmenuitem.order_no";
		$result=mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) <= 0) {

		} // if (mysql_num_rows($result) <= 0) {
		else {
			while($row=mysql_fetch_array($result)) {	
				/* if detail = look for module mst */
				if ($row["type"]=="2" && $row["module_mst_code"]!="0") {
					$query_inner = "SELECT mstmodule.* FROM mstmodule WHERE ";
					$query_inner = $query_inner . " mstmodule.code = '".$row["module_mst_code"]."' AND mstmodule.is_enabled = '1'";
					$result_inner=mysql_query($query_inner) or die(mysql_error());
					if (mysql_num_rows($result_inner) <= 0) {

					} // if (mysql_num_rows($result_inner) <= 0) {
					else {
						
						while($row_inner=mysql_fetch_array($result_inner)) {

							$alreadyAdded = false;
							$proceedToGroupPrivilege = false;

							/* check user privilege first before adding */
							/* if it has user level access - priority/ignore group access */
							$privilege_query = "SELECT mstuserprivilege.* FROM mstuserprivilege WHERE ";
							$privilege_query = $privilege_query. "mstuserprivilege.type = '1' AND mstuserprivilege.type_mst_code = '$user_code' ";
							$privilege_query = $privilege_query. " AND mstuserprivilege.module_mst_code = '".$row_inner['code']."' AND mstuserprivilege.module_action_mst_code = '-1'";
							$privilege_result = mysql_query($privilege_query) or die(mysql_error());
							if (mysql_num_rows($privilege_result) <= 0) {

								$proceedToGroupPrivilege = true;
							} // if (mysql_num_rows($privilege_result) <= 0) {
							else {
								while ($privilege_row=mysql_fetch_array($privilege_result)) {
									/* allow access */
									if ($privilege_row["access_type"]=="1"&&!$alreadyAdded){
										$arr = array(
											"result" => "1",
											"code" => $row["code"],
											"short_name" => $row_inner["short_name"],
											"module_name" => $row_inner["module_name"],
											"program_name" => $row_inner["program_name"],
											"menu_name" => $row["menu_name"],
											"menu_item_mst_code" => $row["menu_item_mst_code"],
											"type" => $row["type"],
											"type_name" => ($row["type"]=='1')?"Parent":"Detail",
											"order_no" => $row["order_no"],
											"module_mst_code" => $row_inner["code"]
										);
										array_push($outputArray,$arr);
										$ctr++;
										$proceedToGroupPrivilege = false;
										$alreadyAdded = true;
									} // if ($privilege_row["access_type"]=="1"){

									/* if you are denied as a user = disregard group.. (think of this like a BANNED access) */
									else if ($privilege_row["access_type"]=="2"){
										$proceedToGroupPrivilege = false;
									} // else if ($privilege_row["access_type"]=="2"){

									/* if may record tapos 0 ang access type, just skip then continue to group */
									else if ($privilege_row["access_type"]=="0"){
										$proceedToGroupPrivilege = true;
									} // else if ($privilege_row["access_type"]=="0"){

								} // if ($privilege_row=mysql_fetch_array($privilege_result)) {
							} // else ng if (mysql_num_rows($privilege_result) <= 0) {

							/* if $proceedToGroupPrivilege = true : check groups of user if may access */
							if ($proceedToGroupPrivilege==true){
								$privilege_query = "SELECT mstuserprivilege.* FROM mstuserprivilege WHERE ";
								$privilege_query = $privilege_query. "mstuserprivilege.type = '2' AND mstuserprivilege.type_mst_code IN ($user_groupsIn) ";
								$privilege_query = $privilege_query. " AND mstuserprivilege.module_mst_code = '".$row_inner['code']."' AND mstuserprivilege.module_action_mst_code = '-1'";
								$privilege_query = $privilege_query. " ORDER BY mstuserprivilege.access_type";
								$privilege_result = mysql_query($privilege_query) or die(mysql_error());
								if (mysql_num_rows($privilege_result) <= 0) {
									
								} // if (mysql_num_rows($privilege_result) <= 0) {
								else {

									while ($privilege_row=mysql_fetch_array($privilege_result)/*&&!$alreadyAdded*/) {
										/* allow access */
										if ($privilege_row["access_type"]=="1" && !$alreadyAdded){
											$alreadyAdded = true;
											$arr = array(
												"result" => "1",
												"code" => $row["code"],
												"short_name" => $row_inner["short_name"],
												"module_name" => $row_inner["module_name"],
												"program_name" => $row_inner["program_name"],
												"menu_name" => $row["menu_name"],
												"menu_item_mst_code" => $row["menu_item_mst_code"],
												"type" => $row["type"],
												"type_name" => ($row["type"]=='1')?"Parent":"Detail",
												"order_no" => $row["order_no"],
												"module_mst_code" => $row_inner["code"]
											);
											array_push($outputArray,$arr);
											$ctr++;
											$proceedToGroupPrivilege = false;
											
										} // if ($privilege_row["access_type"]=="1"){

									} // while ($privilege_row=mysql_fetch_array($privilege_result)) {
								} // else ng if (mysql_num_rows($privilege_result) <= 0) {
							} // if ($proceedToGroupPrivilege==true){

							
						} //while($row_inner=mysql_fetch_array($result_inner)) {
					} // else ng if (mysql_num_rows($result_inner) <= 0) {
				} // if ($row["type"]=="2" && $row["module_mst_code"]!="0") {

				/* parent = just display menu */
				else {
					$proceedToGroupPrivilege = false;
					$arr = array(
						"result" => "1",
						"code" => $row["code"],
						"short_name" => "",
						"module_name" => "",
						"program_name" => "",
						"menu_name" => $row["menu_name"],
						"menu_item_mst_code" => $row["menu_item_mst_code"],
						"type" => $row["type"],
						"type_name" => ($row["type"]=='1')?"Parent":"Detail",
						"order_no" => $row["order_no"],
						"module_mst_code" => $row["module_mst_code"]
					);
					array_push($outputArray,$arr);
					$ctr++;
				} // else ng if ($row["type"]=="2" && $row["module_mst_code"]!="0") {
				//$query_inner = ""
				
				
			} // while($row=mysql_fetch_array($result))
		} // else ng if (mysql_num_rows($result) <= 0) {

		if ($ctr<=0){
			$arr = array(
				"result" => "0"
			);
			array_push($outputArray,$arr);
		} // if ($ctr<=0){

		$dbAccess->closeCon();
		echo json_encode( $outputArray);

	} // if (isset($_POST['action']) || $_POST['action']!=='') {
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
	// illegal access error
} // else ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {
?>