<?php
require_once("CurlAPI.php");
require_once("SystemConstants.php");
$larr = array();
$lch_database =  $_GET['database'];
$lch_table  =  $_GET['table'];
$lch_columns = $_GET['columns'] ;
$lch_fieldname = $_GET['fieldname'];

//$larr = explode('-', $lch_filter);

// $lch_chainingfield = $_GET['chainingparentfield'];
// $lch_condition = "conditions[equals][" . $lch_chainingfield . "]" ;
// $lch_filter = str_replace("-id-","-name-",$_GET['chain']);

$larr_ChainingFields = explode("|", @$_GET['chainingparentfield']);
$larr_ChainingFieldNames = explode("|", str_replace("-id-","-name-",@$_GET['chain']));

$lch_sort = "";
$lch_sortmode = "";

$lch_sort = "code";
if (@$_GET['sorting']!="") {
    $lch_sort = @$_GET['sorting'];
}


$linsample =count($larr);
 
$link = DB_LOCATION;
$params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query_chain",
    "tableName" => $lch_table,
    "dbconnect" => $lch_database,
    "columns" => $lch_columns,
     //$lch_condition => $_GET[$lch_filter] ,
     "fieldname" => $lch_fieldname,
    "orderby" => $lch_sort . " ASC"
);

foreach ($larr_ChainingFields as $lch_key => $lch_Value) {
    $params["conditions[equals][".$lch_Value."]"] = (@$_GET[$larr_ChainingFieldNames[$lch_key]]=="") ? "0" : @$_GET[$larr_ChainingFieldNames[$lch_key]];
}

//echo json_encode($params);

$result=processCurl($link,$params);
$output = json_decode($result,true);
/*
$arr = array("-1"=>"UNBLOCKED");
$new = array();
if ($lch_table=="mstblock") {
    $new = array_splice($output, 1,0 ,$arr);
}
*/

//echo  $lch_condition;
echo json_encode($output);


?>