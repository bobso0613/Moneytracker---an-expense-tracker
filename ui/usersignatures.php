<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$link = DB_LOCATION;
$params = array (
	"action" => "image",
	"fileToOpen" => "get_user_image",
	"tableName" => "dtlusersignature",
	"dbconnect" => MONEYTRACKER_DB	,
	"columns" => "code,user_mst_code,image_name,filetype,image",
	"conditions[equals][code]" => @$_GET['id'],
	"conditions[equals][user_mst_code]" => @$_GET['id2']
);
$result=processCurl($link,$params);
$a = json_decode($result,true);
//print_r($result);
if ($a[0]["result"] ==  '1'){
	//$this->setSession('username',$a[0]["username"]);	
	//$this->setSession('user_code',$a[0]["code"]);	
	
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
		$thumbSize = 256;
		$thumb = imagecreatetruecolor($thumbSize, $thumbSize);
		imagecopyresampled($thumb, $db_img, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

		$db_img = $thumb;
	}
	

	if ($db_img !== false) {	
		switch ($a[0]["filetype"]) {
			case "jpg":
			case "jpeg":
			header("Content-Type: image/jpeg");
			    imagejpeg($db_img);
			    break;
			case "gif":
			header("Content-Type: image/gif");
			    imagegif($db_img);
			    break;
			case "png":
			header("Content-Type: image/png");
			    imagepng($db_img);
			    break;
		}
		
	}
	/*header("Content-Type: image/jpeg");
			    imagejpeg($db_img);*/
	imagedestroy($db_img);


	/*
	$db_img = base64_decode($a[0]["image"]);	
	$db_img = imagecreatefromstring($db_img);
	if ($db_img !== false) {	
		switch ($a[0]["filetype"]) {
			case "jpg":
			header("Content-Type: image/jpeg");
			    imagejpeg($db_img);
			    break;
			case "gif":
			header("Content-Type: image/gif");
			    imagegif($db_img);
			    break;
			case "png":
			header("Content-Type: image/png");
			    imagepng($db_img);
			    break;
		}
		
	}
	imagedestroy($db_img);
	*/


}
else {
	//$this->setSession('error_message',$a[0]["error_message"]);					
	//header('Location: ./login.php');
	$file = $PAGE_SETTINGS["CurrentDirectory"].'resources/assets/nosignature.png';
    header('Content-Type: image/png');
    header('Content-Length: ' . filesize($file));
    echo file_get_contents($file);
}

?>