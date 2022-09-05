<?php
//// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//// header("Cache-Control: post-check=0, pre-check=0", false);
//// header("Pragma: no-cache");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/PHPTextToImage.php");

$lch_nickname = "";
$link = DB_LOCATION;
$params = array (
	"action" => "retrieve",
	"fileToOpen" => "default_select_query",
	"tableName" => "mstuser",
	"dbconnect" => MONEYTRACKER_DB	,
	"columns" => "code,first_name,username",
	"conditions[equals][code]" => @$_GET['id2'],
	"orderby" => "code ASC"
);
$result=processCurl($link,$params);
$a = json_decode($result,true);
if (count($a)>0 && $a[0]["result"]=="1") {
	$lch_nickname = $a[0]["first_name"];
	$lch_nickname = substr(strtoupper($lch_nickname),0,1);
} // if (count($a)>0 && $a[0]["result"]=="1") {



$params = array (
	"action" => "image",
	"fileToOpen" => "get_user_image",
	"tableName" => "dtluserimage",
	"dbconnect" => MONEYTRACKER_DB	,
	"columns" => "code,user_mst_code,image_name,filetype,image",
	"conditions[equals][code]" => @$_GET['id'],
	"conditions[equals][user_mst_code]" => @$_GET['id2']
);
$result=processCurl($link,$params);
$a = json_decode($result,true);
//print_r($result);
if (count($a)>0 && $a[0]["result"] ==  '1' && $a[0]["image"]!=""){


	$db_img = base64_decode($a[0]["image"]);	
	$db_img = imagecreatefromstring($db_img);

	if (isset($_GET["thumbmode"]) && $_GET["thumbmode"]==="true") {
		$width = imagesx($db_img);
		$height = imagesy($db_img);

		// calculating the part of the image to use for thumbnail
		if ($width > $height) {
		  $y = 0;
		  $x = ($width - $height) / 2;
		  $smallestSide = $height;
		} else {
		  $x = 0;
		  $y = ($height - $width) / 2;
		  $smallestSide = $width;
		}

		// copying the part into thumbnail
		$thumbSize = 200;
		$thumb = imagecreatetruecolor($thumbSize, $thumbSize);
		imagecopyresampled($thumb, $db_img, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

		$db_img = $thumb;
	}
	

	if ($db_img !== false) {	
		switch ($a[0]["filetype"]) {
			case "jpg":
			case "jpeg":
			header("Content-Type: image/jpeg");

			    imagejpeg($db_img,NULL,100);
			    break;
			case "gif":
			header("Content-Type: image/gif");
			    imagegif($db_img);
			    break;
			case "png":
			header("Content-Type: image/png");
			    imagepng($db_img,NULL,9);
			    break;
		}
		
	}
	imagedestroy($db_img);

}
else {

	if ($lch_nickname!="") {
		$thumbSize = 500;
		$fontsize = 330;
		if (isset($_GET["thumbmode"]) && $_GET["thumbmode"]==="true") {
			$thumbSize = 200;
			$fontsize = 120;
		}

		/*create class object*/
		$phptextObj = new phptextClass();
		/*phptext function to genrate image with text*/
		//echo $lch_nickname;

		header('Content-Type: image/jpeg');
		if(file_exists($PAGE_SETTINGS["CurrentDirectory"].'resources/assets/'.@$_GET['id2'].'.jpg')) {
			echo file_get_contents($PAGE_SETTINGS["CurrentDirectory"].'resources/assets/'.@$_GET['id2'].'.jpg');
		}
		else {
			 echo file_get_contents($phptextObj->phptext($lch_nickname,'#FFF','',$fontsize,$thumbSize,$thumbSize,$PAGE_SETTINGS["CurrentDirectory"].'resources/assets/', @$_GET['id2'].'.jpg'));	
		}

	   
		

	} // if ($lch_nickname!="") {
	else {

		if (isset($_GET["thumbmode"]) && $_GET["thumbmode"]==="true") {	
			$file = $PAGE_SETTINGS["CurrentDirectory"].'resources/assets/noprofilepic-min.png';
		}
		else {
			$file = $PAGE_SETTINGS["CurrentDirectory"].'resources/assets/noprofilepic.png';
		}
		
	    header('Content-Type: image/png');
	    header('Content-Length: ' . filesize($file));
	    echo file_get_contents($file);

	} // ELSE ng if ($lch_nickname!="") {

	
}

?>