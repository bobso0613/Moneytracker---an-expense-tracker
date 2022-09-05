<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
header('Content-type: application/json; charset=iso-8859-1');
require_once("../../SystemConstants.php");
require_once("../../CurlAPI.php");

$larr_resultarray = array();
$larr_InputFormatArray = array();
$larr_InputFormatArraySecond = array();
$larr_InputFormatArrayThird = array();
$lch_OutputNewValue = "";
$usesFormat = false;
$formatmode = "";

// AJAX requests - start the session
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    session_start();
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
//session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$link = DB_LOCATION;
	$params = array (
		"action" => "receive",
		"fileToOpen" => "transactions/main/receive_trnmoneytrail",
		"dbconnect" => $_POST["dbconnect"],
		"tablename" => $_POST["tablename"],
		"postvalues" => $_POST,
		"filterdata" => (isset($_POST["filterdata"]))?$_POST["filterdata"]:"",
		"filterdataname" => (isset($_POST["filterdataname"]))?$_POST["filterdataname"]:"",
		"columnstodisplay" => $_POST["columnstodisplay"],
		"user_code" => $_SESSION["user_code"],
	);
	$result=processCurl($link,$params);
	$output = json_decode($result,true);
	

	$larr_InputFormatArray = explode(",", $_POST["columnsfieldformat"]);
	$lin_counter = 0;
	if (@$output["data"]!== NULL ){
	 foreach ($output["data"] as $key => $value )
	
		{	 


			$lin_second_counter = 0;
			
			foreach ($larr_InputFormatArray as $second_key => $second_value)
			{ $usesFormat = false;		
				$valueToDisplay = $output["data"][$lin_counter][$lin_second_counter] ; 
				$larr_InputFormatArraySecond = explode("|", $second_value);
					foreach($larr_InputFormatArraySecond as $third_key => $third_value)
					{
						$larr_InputFormatArrayThird  = explode("=", $third_value);
						if (($larr_InputFormatArrayThird[0] == "usesformat" ) && ($larr_InputFormatArrayThird[1] == "yes" ))
						{
						 	$usesFormat = true;		
	  					//	$valueToDisplay = date ($static_values[1], strtotime($valueToDisplay));
		 				//$output["data"][$lin_counter][$lin_second_counter] = number_format($output["data"][$lin_counter][$lin_second_counter],2,'.',',') ;

						}
						// DETERMINES WHAT DATATYPE OF FORMAT
	                    else if ($larr_InputFormatArrayThird[0]=="formatmode"&&$larr_InputFormatArrayThird[1]!=""){
	                        $formatmode = $larr_InputFormatArrayThird[1];
	                    }
						else if ($larr_InputFormatArrayThird[0]=="format"&&$larr_InputFormatArrayThird[1]!=""&&$usesFormat==true&&$formatmode!="") {

	                                        // IF datetime = CONVERT $valueToDisplay TO DATE WITH GIVEN FORMAT
	                                        if ($formatmode=="datetime"){
	                                        	//echo "q";
	                                            $valueToDisplay = date ($larr_InputFormatArrayThird[1], strtotime($valueToDisplay));
	                                        } // if ($formatmode=="datetime"){

	                                        // IF number = CONVERT $valueToDisplay TO NUMBER WITH GIVEN FORMAT (either INT or DEC)
	                                        else if ($formatmode=="number"){
	                                            $valueToDisplay = number_format(floatval($valueToDisplay),$larr_InputFormatArrayThird[1]); 
	                                        } // else if ($formatmode=="number"){

	                                        $output["data"][$lin_counter][$lin_second_counter] = $valueToDisplay;
	                                    } // else if ($static_values[0]=="format"&&$static_values[1]!=""&&$usesFormat==true)
						
						 //  (($larr_InputFormatArraySecond[0] == "usesformat" ) && ($larr_InputFormatArraySecond[1] == "yes" ))

					} // foreach($larr_InputFormatArraySecond as $third_key => $third_value)



			$lin_second_counter = $lin_second_counter + 1 ;	
			} // foreach ($larr_InputFormatArray as $second_key => $second_value)
			$lin_counter = $lin_counter + 1;
			// $output["data"][0] = $value[2] ;
			/*
						$larr_InputFormatArraySecond = explode("=",$second_value);
			    		if($larr_InputFormatArraySecond[0] =="usesformat") 
			    		{
			    			// $output[0]["data"][$key] = number_Format ()
			    		}

			    		// usesformat
						// true = $output[0]["data"][$key] = number_Format ()

			    }			
			*/
		}
		 echo json_encode ($output);
	} //if ($output["data"]!== NULL )
	else{
		echo $result;
	}

} //foreach ($output as $key => $value ) 

?>