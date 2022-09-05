<?php
error_reporting(0);

// header('Cache-Control: no-cache, must-revalidate');
// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
error_reporting(0);
//echo 'weqwqwqwqwq';
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include "api/DatabaseConnect.php";
include_once("api/SanitizeField.php");
include_once("api/SlugGenerator.php");
$v = "yolo";

try{
	if (isset($_POST['fileToOpen'])) {
		include "modules/".$_POST['fileToOpen'].".php";
	}
	else {
		echo "Fatal Error";
	}
} catch (Exception $e){
	echo "Fatal Error";
}


$larr_ResultQueryArrayAPI = null;
$larr_outputArrayAPI = null;

if (isset($dbAccess)) {
	$dbAccess = null;
} // if (isset($dbAccess)) {

if (isset($lch_dbAccess)) {
	$lch_dbAccess = null;
} // if (isset($lch_dbAccess)) {

if (isset($outputArray)) {
	$outputArray = null;
} // if (isset($outputArray)) {

if (isset($arr)) {
	$arr = null;
} // if (isset($arr)) {

if (isset($larr_outputArray)) {
	$larr_outputArray = null;
} // if (isset($larr_outputArray)) {
exit;

?>
