<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");

//var_dump ( array(array("account_title"=>"yolo"),array("account_title"=>"yoleeee")) ) ;
//ABSOLUTE_PATH+''+remotelink + "?searchstring=%QUERY&sourcedb="+sourcedb+"&sourcetb="+sourcetb+"&sourcecols="+sourcecols,

$lch_tablename = $_GET["sourcetb"];
if ($lch_tablename=="mstchartofaccounts2") {
	$lch_tablename = "mstchartofaccounts";
}

$link = DB_LOCATION;
$params = array (
	"action" => "receive-masterfile-tables",
	"fileToOpen" => "default_select_query",
	"dbconnect" => $_GET["sourcedb"],
	"tableName" => $lch_tablename,
	"columns" => $_GET["sourcecols"], // code,name,address

	"recordstart"=>"0",
	"recordcount"=>"50"

	//"type="+type+"&receiver="+receiver+"&sender="+sender+"&actualVal="+actualVal
	//"columns" => "username,code,user_image_code,first_name,middle_name,last_name,user_group_mst_codes",
	//"conditions[not_equals][code]" => $_SESSION["user_code"],
	//"conditions[equals][is_active]" => "1"
);
$fieldsearch = explode(",",$_GET["fieldsearch"]);
$lch_reversedfieldsearch = "";
foreach ($fieldsearch as $val){
	//$params["conditions[like][$val]"] = strtolower($_GET["searchstring"]);
	$params["conditions[like][$val]"] = strtolower( html_entity_decode ( $_GET["searchstring"]));
	$lch_reversedfieldsearch = $val . "," . $lch_reversedfieldsearch;
}
$lch_reversedfieldsearch = trim($lch_reversedfieldsearch,",");
$params["orderby"] = $lch_reversedfieldsearch;

if (isset($_GET["customsearch"])&&$_GET["customsearch"]!=""){
	//echo $_GET["customsearch"];
	$customsearch = explode("|",$_GET["customsearch"]);
	$larr_keypair = array();
	foreach ($customsearch as $val){
		//echo json_encode($val);
		if (!empty($val)&&$val!=""){
			$larr_keypair = explode(":",$val);
			if ( strpos($larr_keypair["1"], "find_in_set=") !== false ) {
				$params["conditions[find_in_set][".$larr_keypair["0"]."]"] = ltrim(strtolower($larr_keypair["1"]), 'find_in_set=');
			}
			else if (count(explode(",", $larr_keypair["1"])) == 1) {
				$params["conditions[equals][".$larr_keypair["0"]."]"] = strtolower($larr_keypair["1"]);
			}
			else if (count(explode(",", $larr_keypair["1"])) > 1) {
				$params["conditions[in][".$larr_keypair["0"]."]"] = strtolower($larr_keypair["1"]);
			}
		}

	}
}
if ($_GET["sourcetb"]=="mstchartofaccounts") {
	$params["conditions[equals][include_in_search]"] = "1";
}

$result=processCurl($link,$params);
// echo $result;
$output = json_decode($result,true);

for ($c=0;$c<count($output);$c++){
	$output[$c]["value"] = @$output[$c][$fieldsearch["1"]];
}
//if($output[0]["result"]==='1'){
//echo json_encode($params);
echo json_encode($output);


// if ($_GET["sourcetb"]!="mstchartofaccounts") {
	
// } // if ($_GET["sourcetb"]!="mstchartofaccounts") {
// else {
// 	$larr_outputforcoa = array();
// 	for ($c=0;$c<count($output);$c++){
// 		//$output[$c]["code"]

// 		$link = DB_LOCATION;
// 		$params = array (
// 			"action" => "get_table_record_count",
// 			"fileToOpen" => "default_select_query",
// 			"dbconnect" => $_GET["sourcedb"],
// 			"tableName" => $lch_tablename,
// 			"columns" => "code",
// 			"conditions[equals][parent_chart_of_accounts_mst_code]" => $output[$c]["code"]
// 		);
// 		$result=processCurl($link,$params);
// 		$out=json_decode($result,true);
// 		if (count($out)>0 && $out[0]["result"]=="1") {

// 		} // if (count($out)>0 && $out[0]["result"]=="1") {
// 		else {
// 			$output[$c]["value"] = @$output[$c][$fieldsearch["1"]];
// 			array_push($larr_outputforcoa, $output[$c]);
// 		} // ELSE ng if (count($out)>0 && $out[0]["result"]=="1") {

// 	} // for ($c=0;$c<count($output);$c++){
// 	echo json_encode($larr_outputforcoa);
// } // ELSE ng  if ($_GET["sourcetb"]!="mstchartofaccounts") {





//echo json_encode(array(array("gl_code"=>"11111111","account_title"=>"yolo $param","value"=>"yolo"),array("gl_code"=>"11111111","account_title"=>"yoleeee","value"=>"yoleeee")))


?>