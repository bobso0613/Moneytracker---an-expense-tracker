<?php
error_reporting(0);

// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
@include_once("../../api/DatabaseConnect.php");
$dbAccess = new DatabaseAccess();
$outputArray = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//if (isset($_POST['action']) || $_POST['action']!=='') {

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

		$codeToUpdate = @$_POST['codeToUpdate'];
		$codeToUpdate = stripslashes($codeToUpdate);
		$codeToUpdate = mysql_real_escape_string($codeToUpdate);

		if ($_POST["action"]=="can_add"||$_POST["action"]=="can_edit"){
			$lch_EncodedPassword = "";
			$lch_WholeName = "";
			$lch_SlugLink = "";
			$lch_Query = "SELECT $tableName.* FROM $tableName WHERE $tableName.code='$codeToUpdate' ORDER BY $tableName.code ASC";
			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {
				$lch_EncodedPassword = $larr_ResultRow["password"];
				if ($_POST["action"]=="can_add"){
					$lch_EncodedPassword = base64_encode(md5($lch_EncodedPassword));
				}
				$lch_WholeName = $larr_ResultRow["first_name"];
				if ($larr_ResultRow["middle_name"]!=""){
					$lch_WholeName .= " " . $larr_ResultRow["middle_name"];
				}
				$lch_WholeName .= " " . $larr_ResultRow["last_name"];
				$lch_WholeName = mysql_escape_string($lch_WholeName);

				$lch_SlugLink .= slugify($lch_WholeName." ".$larr_ResultRow["code"]);
			} // while($larr_ResultRow=mysql_fetch_array($SQL_RESULT)) {


			$lch_Query = "UPDATE $tableName SET ";
			$lch_Query = $lch_Query . "$tableName.whole_name = '$lch_WholeName', $tableName.password='$lch_EncodedPassword', $tableName.profile_slug_link = '$lch_SlugLink' WHERE $tableName.code='$codeToUpdate'";
			// model_year - manufacturer - model_type - engine series

			$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());
			if (!$SQL_RESULT){
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

				// if add - add default money trail types, add default analytics
				if ($_POST["action"]=="can_add"){
					$lch_Query = "INSERT INTO mstmoneytrailtype ( "
							. "trail_type,short_name,money_trail_name,description,reference_no,order_no,is_active,is_other_currency,show_in_account_to_deduct,is_payable_debt_account,"
							. "remarks,created_at,updated_at,created_user_mst_code,updated_user_mst_code"
							. ") VALUES ("
							. "'2','CASH','Cash on Hand','Cash on Hand','','0','1','2','1','0',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. "),"
							. "("
							. "'2','ARI','Accounts Receivable','Accounts Receivable','','5','1','2','1','0',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. "),"
							. "("
							. "'2','API','Accounts Payable','Accounts Payable','','10','1','2','1','1',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. "),"

							. "("
							. "'1','FOOD','Food','Food','','5','1','2','2','0',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. "),"

							. "("
							. "'1','TRANSPO','Transportation','Transportation','','10','1','2','2','0',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. "),"

							. "("
							. "'1','ALLOWANCE','Allowance','Allowance','','15','1','2','2','0',"
							. "'AUTO GENERATED',NOW(),NOW(),'$codeToUpdate','$codeToUpdate'"
							. ")"

							.";";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '7'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '42'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '43'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '44'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '47'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '48'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '49'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());

					$lch_Query = "UPDATE mstanalytics SET mstanalytics.accessible_user_mst_codes = CONCAT(mstanalytics.accessible_user_mst_codes,',','$codeToUpdate') WHERE mstanalytics.code = '50'; ";
					$SQL_RESULT=mysql_query($lch_Query) or die(mysql_error());



				} // if ($_POST["action"]=="can_add"){

			}
		} // if ($_POST["action"]=="can_add"||$_POST["action"]=="can_edit"){
		else {
			$arr = array(
				"result" => "1",
				"error_message" => "1"
			);
		}

		array_push($outputArray,$arr);
		/*print_r($outputArray);*/
		$dbAccess->closeCon();
		echo json_encode( $outputArray);

	//}
}
else {
	// illegal access error
}
?>