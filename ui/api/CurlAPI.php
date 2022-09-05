<?php
function processCurl($url, $params){
	 
	$urltopost = $url;
	
	$datatopost=$params;
	
	$ch = curl_init ($urltopost);
	curl_setopt ($ch, CURLOPT_POST, true);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($datatopost));
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$returndata = curl_exec ($ch); 

	return $returndata;
	
	
}

//echo 'hehe';

function processCurlWithFile($url, $params, $size=0){
	 
	$urltopost = $url;
	
	$datatopost=$params;
	$headers = array("Content-Type:multipart/form-data");
	$ch = curl_init ($urltopost);
	curl_setopt ($ch, CURLOPT_POST, true);
	//curl_setopt ($ch, CURLOPT_HEADER, 0);
	//curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt ($ch, CURLOPT_INFILESIZE, $size);
	curl_setopt ($ch, CURLOPT_BINARYTRANSFER,1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt ($ch, CURLOPT_POSTFIELDS,http_build_query($datatopost));
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$returndata = curl_exec ($ch); 
	return $returndata;
	
	
}

function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
{
  // if (empty($timeZoneSource)) {
  //   $timeZoneSource = date_default_timezone_get();
  // }
  // if (empty($timeZoneTarget)) {
  //   $timeZoneTarget = date_default_timezone_get();
  // }

	$timeZoneTarget = "Asia/Manila";
	  $timeZoneSource = "Asia/Manila";

  $dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
  $dt->setTimezone(new DateTimeZone($timeZoneTarget));

  return $dt->format("Y-m-d H:i:s");
}


?>
