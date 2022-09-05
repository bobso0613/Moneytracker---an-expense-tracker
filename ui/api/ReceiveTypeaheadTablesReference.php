<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
header('Content-type: application/json');
require_once("SystemConstants.php");
require_once("CurlAPI.php");

/*$lch_ReferenceTypes = ["","Clients","Licensed Intermediaries","Reinsurers","Unlicensed Intermediaries",
						"Suppliers","Third Parties","Adjusters","Repair Shops","Adjusters","Repair Shops",
						"Customers","Employees","Other vendors/Suppliers"];*/


$lch_ReferenceTypes = array(
            "1" => "Client",
            "2" => "Licensed Intermediary",
            "3" => "Reinsurers" ,
            "4" => "Unlicensed Intermediary",
            "5" => "Supplier",
            "6" => "Third Party",
            "7" => "Adjuster",
            "8" => "Repair Shop",
            "9" => "Customer",
            "10" => "Employee",
            "11" => "Other Vendor/Supplier"
        );

//var_dump ( array(array("account_title"=>"yolo"),array("account_title"=>"yoleeee")) ) ;
//ABSOLUTE_PATH+''+remotelink + "?searchstring=%QUERY&sourcedb="+sourcedb+"&sourcetb="+sourcetb+"&sourcecols="+sourcecols,

$link = DB_LOCATION;
$params = array (
	"action" => "receive-masterfile-tables",
	"fileToOpen" => "default_select_query",
	"dbconnect" => $_GET["sourcedb"],
	"tableName" => $_GET["sourcetb"],
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
// @$output[$c][$fieldsearch["1"]]
$sourcecols = explode(",",$_GET["sourcecols"]);

//echo json_encode($params);
$result=processCurl($link,$params);
$output = json_decode($result,true);
if (count($output)>0 && $output[0]["result"]=="1") {
	for ($c=0;$c<count($output);$c++){
		$output[$c]["value"] = @$output[$c][$sourcecols["1"]];
		$output[$c]["reference_type"] = $lch_ReferenceTypes[$output[$c]["reference_type"]];
	}
}


//if($output[0]["result"]==='1'){


echo json_encode($output);

//echo json_encode(array(array("gl_code"=>"11111111","account_title"=>"yolo $param","value"=>"yolo"),array("gl_code"=>"11111111","account_title"=>"yoleeee","value"=>"yoleeee")))


?>