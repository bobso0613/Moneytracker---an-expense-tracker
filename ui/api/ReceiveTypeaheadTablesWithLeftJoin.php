<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");

//var_dump ( array(array("account_title"=>"yolo"),array("account_title"=>"yoleeee")) ) ;
//ABSOLUTE_PATH+''+remotelink + "?searchstring=%QUERY&sourcedb="+sourcedb+"&sourcetb="+sourcetb+"&sourcecols="+sourcecols,

$link = DB_LOCATION;
$params = array (
	"action" => "receive-masterfile-tables",
	"fileToOpen" => "default_select_query_withleftjoin",
	"dbconnect" => $_GET["sourcedb"],
	"tableName" => $_GET["sourcetb"],
	"columns" => $_GET["sourcecols"], // code,name,address
	"innerjoinTable" => @$_GET['innerjoin'],
	"orderby" => @$_GET['orderby'],

	"recordstart"=>"0",
	"recordcount"=>"50"

	//"type="+type+"&receiver="+receiver+"&sender="+sender+"&actualVal="+actualVal
	//"columns" => "username,code,user_image_code,first_name,middle_name,last_name,user_group_mst_codes",
	//"conditions[not_equals][code]" => $_SESSION["user_code"],
	//"conditions[equals][is_active]" => "1"
);
$fieldsearch = explode(",",$_GET["fieldsearch"]);
foreach ($fieldsearch as $val){
	//$params["conditions[like][$val]"] = strtolower($_GET["searchstring"]);
	$params["conditions[like][$val]"] = strtolower( html_entity_decode ( $_GET["searchstring"]));
}

if (isset($_GET["customsearch"])&&$_GET["customsearch"]!=""){
	$customsearch = explode("|",$_GET["customsearch"]);
	$larr_keypair = array();
	foreach ($customsearch as $val){
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


// echo json_encode($_GET);

//var_dump($params);
$result=processCurl($link,$params);
// echo $result;
$output = json_decode($result,true);

if (!isset($_GET['data-display-secondary-column-name'])) {
	echo json_encode($output);
	exit;
}

if (!isset($_GET["display_column_name"])) {
	echo json_encode($output);
	exit;
}

for ($c=0;$c<count($output);$c++){
	if ( isset($output[$c][$_GET['data-display-secondary-column-name']]) &&
		$output[$c][$_GET['data-display-secondary-column-name']] != "" ) {

		$output[$c]["value"] = @$output[$c][$_GET["display_column_name"]] . '-' . @$output[$c][$_GET['data-display-secondary-column-name']];

	} else {
		$output[$c]["value"] = @$output[$c][$_GET["display_column_name"]];
	}
}

//if($output[0]["result"]==='1'){

echo json_encode($output);

//echo json_encode(array(array("gl_code"=>"11111111","account_title"=>"yolo $param","value"=>"yolo"),array("gl_code"=>"11111111","account_title"=>"yoleeee","value"=>"yoleeee")))


?>