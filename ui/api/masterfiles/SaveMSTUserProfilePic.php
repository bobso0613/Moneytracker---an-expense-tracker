<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
ini_set("post_max_size", '100M');
ini_set("upload_max_filesize", '100M');
ini_set("memory_limit", '100M');
header('Content-type: application/json');
require_once("../SystemConstants.php");
require_once("../CurlAPI.php");
date_default_timezone_set('Asia/Manila');

/*
PARAMETERS:
array(75) {
  ["intermediary_rate-1-1-1-1-1-1"]=>
  string(5) "5.256"
  ["company_mst_code"]=>
  string(1) "1"
  ["branch_mst_code"]=>
  string(1) "1"
  ["line_mst_code"]=>
  string(1) "1"
  ["agent_mst_code"]=>
  string(1) "1"
}
*/

//$_SESSION["user_code"]

$larr_OutputArray = array();
$larr_ResultQueryArray = array();

// AJAX requests - start the session
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    session_start();
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $link = DB_LOCATION;
  $params = array (
        "user_mst_code" => $_SESSION["user_code"],
        "input_user_code" => $_POST["input_user_code"],
        "fileToOpen" => "masterfiles/save_mstuserprofilepic"
    );
    foreach ($_POST as $lch_key => $lch_value){
      $params[$lch_key] = $lch_value;
    } // foreach ($_POST as $lch_key => $lch_value){
      $totalsize = 0;
      //ob_start();

  //echo "huhu help";
  foreach ($_FILES as  $lch_key => $larr_Files){
    $tmpfile = $larr_Files['tmp_name'];
    //$tmpfile = 

    $filename = basename($larr_Files['name']);
    $type = explode("/",$larr_Files['type'])[1];  
    $totalsize += intval($larr_Files['size']);
    //$params[$lch_key] = file_get_contents($tmpfile);

    //echo $tmpfile;

    // $targetPath = "/var/www/tmp/".$filename; 
    //$targetPath = "/tmp/".$filename; 
    $targetPath = "./".$filename; 
    //echo $tmpfile;
    move_uploaded_file($tmpfile,$targetPath) ;
    //echo "fasasa";
    //copy($tmpfile, $targetPath);
    $imagedata = file_get_contents($targetPath);
    $base64 = base64_encode($imagedata);
    //$params[$lch_key]["data"] = urlencode($base64);
    //$params[$lch_key]["data"] = strtr($base64, '+/=', '-_~');
    $params[$lch_key]["data"] = rtrim(strtr($base64, '+/', '-_'), '=');
    $params[$lch_key]["name"] = $filename;
    $params[$lch_key]["type"] = $type;
    unlink($targetPath);



    //$params[$lch_key] = curl_file_create($tmpfile, $larr_Files['type'], $filename);//'@'.$tmpfile.';filename='.$filename;//curl_file_create($tmpfile, $larr_Files['type'], $filename); //'@'.$tmpfile.';filename='.$filename;
  } // foreach ($_FILES as $larr_Files){
  

    $result=processCurl($link,$params);

    //echo $result . " hqhqhq ";
    //$result = "";
    $retrievedRecordRow = json_decode($result,true);
    if (count($retrievedRecordRow)>0){

        $larr_ResultQueryArray = array(
          "result" => $retrievedRecordRow[0]["result"],
          "error_message" => $retrievedRecordRow[0]["error_message"] ,
          "new_image_code" => $retrievedRecordRow[0]["new_image_code"],
          "input_user_code" => $retrievedRecordRow[0]["input_user_code"]
        );
      array_push($larr_OutputArray,$larr_ResultQueryArray);

      // AUDIT TRAIL PART
      $link = DB_LOCATION;
      $params = array (
          "action" => "can_upload",
          "fileToOpen" => "save_audit_trail",
          "tableName" => "trnaudittrail",
          "dbconnect" => MONEYTRACKER_DB,
          "module_mst_code" => $_POST["modulemstcode"],
          "menu_item_mst_code" => $_POST["menuitemmstcode"],
          "user_mst_code" => $_SESSION["user_code"],
          "reference" => $_POST["valueviewed"],
          "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
      );
      $result=processCurl($link,$params);
      // no need to parse the result. error man o hindi
      // end - AUDIT TRAIL PART
    } // if (count($retrievedRecordRow)>0){

      //$result2 =  ob_get_clean();
  
} // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
else {
  $larr_ResultQueryArray = array(
    "result" => "0",
    "error_message" => "Invalid Request Type."
    
  );
  array_push($larr_OutputArray,$larr_ResultQueryArray);
} // ELSE ng if ($_SERVER['REQUEST_METHOD'] == 'POST') {

echo json_encode( $larr_OutputArray);




?>