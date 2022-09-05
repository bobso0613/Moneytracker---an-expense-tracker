<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

if (PHP_SAPI == 'cli')
    die('This should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

// Include the main TCPDF library (search for installation path).
require_once('./tcpdf/tcpdf_include.php');

$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);

$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);

$larr_Letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$larr_Letters2 = array('AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

$lin_letterCount = count($larr_Letters);
$lin_letterCount2 = count($larr_Letters2);

$link = DB_LOCATION;
$params = array (
    "action" => "retrieve-record-column",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstcompany",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "company_name,short_address,email_address,phone_number",
    "conditions[equals][code]" => "1"
);
$result=processCurl($link,$params);
$output3 = json_decode($result,true);

$lch_companyName = $output3[0]['company_name'];
$lch_companyAddress = $output3[0]['short_address'];
$lch_emailAddress = $output3[0]['email_address'];
$lch_phoneNumber = $output3[0]['phone_number'];

// $link = DB_LOCATION;
$params = array (
    "action" => "retrieve-record-column",
    "fileToOpen" => "default_select_query",
    "tableName" => $_GET["tablename"],
    "dbconnect" => $_GET["databasename"],
    "columns" => str_replace("|", ",", $_GET["columnstoquery"])
);
$result=processCurl($link,$params);
$output = json_decode($result,true);


$SaveAsType = $_GET['type'];

if($SaveAsType == "calc"){

		saveAsCalc($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output);

}else if($SaveAsType == "excelnew"){

			saveAsExcelNew($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output);

}else if($SaveAsType == "excelold"){

			saveAsExcelOld($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output);

}else if($SaveAsType == "textfile"){

				saveAsTextFile($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output);

}else if($SaveAsType == "pdf"){

				saveAsPdf($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output);
} else if($SaveAsType == "test"){



}else{

		echo "Error";

}	

function testAsCalc(){

	$objPHPExcel = new PHPExcel();

	for($i=0; 0<100; $i++){
	    $objPHPExcel->setActiveSheetIndex(0)
	                ->setCellValue("A1",$lch_companyName)
	                ->setCellValue("A2",$lch_companyAddress)
	                ->setCellValue("A3","Email: ".$lch_emailAddress." Phone: ".$lch_phoneNumber." ")
	                ->setCellValue("A4",$_GET["masterfilename"]." Listing");
	}//for($i=0; 0<100; $i++){

}//function testAsCalc(){


function saveAsCalc($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output){

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

	//set header
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A1",$lch_companyName)
                ->setCellValue("A2",$lch_companyAddress)
                ->setCellValue("A3","Email: ".$lch_emailAddress." Phone: ".$lch_phoneNumber." ")
                ->setCellValue("A4",$_GET["masterfilename"]." Listing");
	$ctr = 6;

	// LOOP ALL COLUMNS - USE $column_key for index
	foreach ($columns as $column_key => $column_name){
	    $objPHPExcel->setActiveSheetIndex(0)
	                ->setCellValue($larr_Letters[$column_key] . $ctr,$columnsFieldName[$column_key]);
	} // foreach ($columns as $column_key => $column_name){

	$ctr++; 
	$rowctr=0;

	    foreach ($output as $key => $value){
	        foreach ($columns as $column_key => $column_name) {

	            if ($columnsDataSource[$column_key]=="1"){
	                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
	                foreach ($value_pairs as $pair_key => $pair_value) {
	                    $static_values = explode(":",$pair_value);
	                    if ($static_values["0"]==$value[$column_name]){
	                        // $tbl = $tbl . '<td>' . $static_values["1"] . '</td>';
	                    $objPHPExcel->setActiveSheetIndex(0)
	                                ->setCellValue($larr_Letters[$rowctr] . $ctr,$static_values["1"]);
	                    } //if ($static_values["0"]==$value[$column_name]){
	                } // foreach ($value_pairs as $pair_key => $pair_value) {
	            } //if ($columnsDataSource[$column_key]=="1"){
	            else if ($columnsDataSource[$column_key]=="2"){
	                // GET THE ACTUAL VALUE AND DESCRIPTION TO BE DISPLAYED (DESCRIPTION CAN BE CONCATENATION OF FIELDS (SPACE SEPARATED))
	                $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);

	                // IF MORE THAN 1 = CONCATENATION OF FIELDS
	                $value_pairs_actual = explode(" ",$value_pairs["1"]);
	                $cols = "";
	                if (count($value_pairs_actual)>0){
	                    foreach ($value_pairs_actual as $vals){
	                        $cols = $cols . "" . $vals . ",";
	                    }
	                } // if (count($value_pairs_actual)>0){
	                $cols = substr($cols, 0,strlen($cols)-1);

	                // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
	                $link = DB_LOCATION;
	                $params = array (
	                    "action" => "retrieve",
	                    "fileToOpen" => "default_select_query",
	                    "tableName" => $columnsDataSourceTableName[$column_key],
	                    "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
	                    "columns" => $value_pairs['0'].",".$cols ,
	                    "orderby" => $value_pairs['0']." ASC",
	                    "conditions[equals][".$value_pairs['0']."]" => $value[$column_name]
	                );
	                $result=processCurl($link,$params);
	                $output2 = json_decode($result,true);
	                if($output2[0]["result"]==='1'){

	                    // CONCATENATE DATA RETRIEVED
	                    foreach ($output2 as $data_source_key => $data_source_value){
	                        $concatval = "";
	                        foreach ($value_pairs_actual as $vals){
	                            $concatval = $concatval . $data_source_value[$vals] . ' ';
	                        }
	                           $objPHPExcel->setActiveSheetIndex(0)
	                                       ->setCellValue($larr_Letters[$rowctr] . $ctr,$concatval);
	                        //valueToDisplay = $data_source_value[$value_pairs['1']];
	                        
	                    } // foreach ($output2 as $data_source_key => $data_source_value){
	                } // if($output[0]["result"]==='1'){
	            }//else if ($columnsDataSource[$column_key]=="2"){
	            else{
	                $objPHPExcel->setActiveSheetIndex(0)
	                            ->setCellValue($larr_Letters[$rowctr] . $ctr,$value[$column_name]);
	            }//else
	            $rowctr++;
	        }//foreach ($columns as $column_key => $column_name)
	        $ctr++;
	        $rowctr=0;
	    }//foreach ($output as $key => $value){

	$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."1:".$larr_Letters[count($columns)-1]."1");
	$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."2:".$larr_Letters[count($columns)-1]."2");
	$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."3:".$larr_Letters[count($columns)-1]."3");
	$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."4:".$larr_Letters[count($columns)-1]."4");


	 $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
	 $objPHPExcel->getActiveSheet()->getStyle($larr_Letters[0]."6:".$larr_Letters[count($columns)-1]."6")->getFont()->setBold(true);

	//loop to autosize each cell
	for($c=0;$c<$lin_letterCount;$c++){

	    $objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters[$c])->setAutoSize(true);
	    $objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters2[$c])->setAutoSize(true);

	}



	//generate filename
	$filename = strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.ods';

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle(substr($_GET["masterfilename"],0,31));


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;

}//function saveAsCalc(){

function saveAsExcelNew($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output){

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';



$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);

$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);

$larr_Letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$larr_Letters2 = array('AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

$lin_letterCount = count($larr_Letters);
$lin_letterCount2 = count($larr_Letters2);


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
// $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
// 							 ->setLastModifiedBy("Maarten Balliauw")
// 							 ->setTitle("Office 2007 XLSX Test Document")
// 							 ->setSubject("Office 2007 XLSX Test Document")
// 							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
// 							 ->setKeywords("office 2007 openxml php")
// 							 ->setCategory("Test result file");

//set header
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A1",$lch_companyName)
                ->setCellValue("A2",$lch_companyAddress)
                ->setCellValue("A3","Email: ".$lch_emailAddress." Phone: ".$lch_phoneNumber." ")
                ->setCellValue("A4",$_GET["masterfilename"]." Listing");

$ctr = 6;

// LOOP ALL COLUMNS - USE $column_key for index
foreach ($columns as $column_key => $column_name){
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($larr_Letters[$column_key] . $ctr,$columnsFieldName[$column_key]);
} // foreach ($columns as $column_key => $column_name){

$ctr++;	
$rowctr=0;

	foreach ($output as $key => $value){
		foreach ($columns as $column_key => $column_name) {

            if ($columnsDataSource[$column_key]=="1"){
                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                foreach ($value_pairs as $pair_key => $pair_value) {
                    $static_values = explode(":",$pair_value);
                    if ($static_values["0"]==$value[$column_name]){
                        // $tbl = $tbl . '<td>' . $static_values["1"] . '</td>';
					$objPHPExcel->setActiveSheetIndex(0)
						        ->setCellValue($larr_Letters[$rowctr] . $ctr,$static_values["1"]);
                    } //if ($static_values["0"]==$value[$column_name]){
                } // foreach ($value_pairs as $pair_key => $pair_value) {
            } //if ($columnsDataSource[$column_key]=="1"){
            else if ($columnsDataSource[$column_key]=="2"){
                // GET THE ACTUAL VALUE AND DESCRIPTION TO BE DISPLAYED (DESCRIPTION CAN BE CONCATENATION OF FIELDS (SPACE SEPARATED))
                $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);

                // IF MORE THAN 1 = CONCATENATION OF FIELDS
                $value_pairs_actual = explode(" ",$value_pairs["1"]);
                $cols = "";
                if (count($value_pairs_actual)>0){
                    foreach ($value_pairs_actual as $vals){
                        $cols = $cols . "" . $vals . ",";
                    }
                } // if (count($value_pairs_actual)>0){
                $cols = substr($cols, 0,strlen($cols)-1);

                // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                $link = DB_LOCATION;
                $params = array (
                    "action" => "retrieve",
                    "fileToOpen" => "default_select_query",
                    "tableName" => $columnsDataSourceTableName[$column_key],
                    "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                    "columns" => $value_pairs['0'].",".$cols ,
                    "orderby" => $value_pairs['0']." ASC",
                    "conditions[equals][".$value_pairs['0']."]" => $value[$column_name]
                );
                $result=processCurl($link,$params);
                $output2 = json_decode($result,true);
                if($output2[0]["result"]==='1'){

                    // CONCATENATE DATA RETRIEVED
                    foreach ($output2 as $data_source_key => $data_source_value){
                        $concatval = "";
                        foreach ($value_pairs_actual as $vals){
                            $concatval = $concatval . $data_source_value[$vals] . ' ';
                        }
					       $objPHPExcel->setActiveSheetIndex(0)
							    	   ->setCellValue($larr_Letters[$rowctr] . $ctr,$concatval);
                        //valueToDisplay = $data_source_value[$value_pairs['1']];
                        
                    } // foreach ($output2 as $data_source_key => $data_source_value){
                } // if($output[0]["result"]==='1'){
            }//else if ($columnsDataSource[$column_key]=="2"){
        	else{
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($larr_Letters[$rowctr] . $ctr,$value[$column_name]);
        	}//else
	        $rowctr++;
		}//foreach ($columns as $column_key => $column_name)
		$ctr++;
		$rowctr=0;
	}//foreach ($output as $key => $value){


$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."1:".$larr_Letters[count($columns)-1]."1");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."2:".$larr_Letters[count($columns)-1]."2");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."3:".$larr_Letters[count($columns)-1]."3");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."4:".$larr_Letters[count($columns)-1]."4");


 $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
 $objPHPExcel->getActiveSheet()->getStyle($larr_Letters[0]."6:".$larr_Letters[count($columns)-1]."6")->getFont()->setBold(true);

//loop to autosize each cell
for($c=0;$c<$lin_letterCount;$c++){

	$objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters[$c])->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters2[$c])->setAutoSize(true);



}

//generate filename
$filename = strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.xlsx';

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(substr($_GET["masterfilename"],0,31));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

} //function saveAsExcelNew(){

function saveAsExcelOld($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output){

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';



$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);

$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);

$larr_Letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$larr_Letters2 = array('AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

$lin_letterCount = count($larr_Letters);
$lin_letterCount2 = count($larr_Letters2);


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
// $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
// 							 ->setLastModifiedBy("Maarten Balliauw")
// 							 ->setTitle("Office 2007 XLSX Test Document")
// 							 ->setSubject("Office 2007 XLSX Test Document")
// 							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
// 							 ->setKeywords("office 2007 openxml php")
// 							 ->setCategory("Test result file");

//header
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A1",$lch_companyName)
                ->setCellValue("A2",$lch_companyAddress)
                ->setCellValue("A3","Email: ".$lch_emailAddress." Phone: ".$lch_phoneNumber." ")
                ->setCellValue("A4",$_GET["masterfilename"]." Listing");

$ctr = 6;

// LOOP ALL COLUMNS - USE $column_key for index
foreach ($columns as $column_key => $column_name){
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($larr_Letters[$column_key] . $ctr,$columnsFieldName[$column_key]);
} // foreach ($columns as $column_key => $column_name){

$ctr++;	
$rowctr=0;

	foreach ($output as $key => $value){
		foreach ($columns as $column_key => $column_name) {

            if ($columnsDataSource[$column_key]=="1"){
                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                foreach ($value_pairs as $pair_key => $pair_value) {
                    $static_values = explode(":",$pair_value);
                    if ($static_values["0"]==$value[$column_name]){
                        // $tbl = $tbl . '<td>' . $static_values["1"] . '</td>';
					$objPHPExcel->setActiveSheetIndex(0)
						        ->setCellValue($larr_Letters[$rowctr] . $ctr,$static_values["1"]);
                    } //if ($static_values["0"]==$value[$column_name]){
                } // foreach ($value_pairs as $pair_key => $pair_value) {
            } //if ($columnsDataSource[$column_key]=="1"){
            else if ($columnsDataSource[$column_key]=="2"){
                // GET THE ACTUAL VALUE AND DESCRIPTION TO BE DISPLAYED (DESCRIPTION CAN BE CONCATENATION OF FIELDS (SPACE SEPARATED))
                $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);

                // IF MORE THAN 1 = CONCATENATION OF FIELDS
                $value_pairs_actual = explode(" ",$value_pairs["1"]);
                $cols = "";
                if (count($value_pairs_actual)>0){
                    foreach ($value_pairs_actual as $vals){
                        $cols = $cols . "" . $vals . ",";
                    }
                } // if (count($value_pairs_actual)>0){
                $cols = substr($cols, 0,strlen($cols)-1);

                // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                $link = DB_LOCATION;
                $params = array (
                    "action" => "retrieve",
                    "fileToOpen" => "default_select_query",
                    "tableName" => $columnsDataSourceTableName[$column_key],
                    "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                    "columns" => $value_pairs['0'].",".$cols ,
                    "orderby" => $value_pairs['0']." ASC",
                    "conditions[equals][".$value_pairs['0']."]" => $value[$column_name]
                );
                $result=processCurl($link,$params);
                $output2 = json_decode($result,true);
                if($output2[0]["result"]==='1'){

                    // CONCATENATE DATA RETRIEVED
                    foreach ($output2 as $data_source_key => $data_source_value){
                        $concatval = "";
                        foreach ($value_pairs_actual as $vals){
                            $concatval = $concatval . $data_source_value[$vals] . ' ';
                        }
					       $objPHPExcel->setActiveSheetIndex(0)
							    	   ->setCellValue($larr_Letters[$rowctr] . $ctr,$concatval);
                        //valueToDisplay = $data_source_value[$value_pairs['1']];
                        
                    } // foreach ($output2 as $data_source_key => $data_source_value){
                } // if($output[0]["result"]==='1'){
            }//else if ($columnsDataSource[$column_key]=="2"){
        	else{
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($larr_Letters[$rowctr] . $ctr,$value[$column_name]);
        	}//else
	        $rowctr++;
		}//foreach ($columns as $column_key => $column_name)
		$ctr++;
		$rowctr=0;
	}//foreach ($output as $key => $value){

$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."1:".$larr_Letters[count($columns)-1]."1");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."2:".$larr_Letters[count($columns)-1]."2");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."3:".$larr_Letters[count($columns)-1]."3");
$objPHPExcel->getActiveSheet()->mergeCells($larr_Letters[0]."4:".$larr_Letters[count($columns)-1]."4");


 $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
 $objPHPExcel->getActiveSheet()->getStyle($larr_Letters[0]."6:".$larr_Letters[count($columns)-1]."6")->getFont()->setBold(true);

//loop to autosize each cell
for($c=0;$c<$lin_letterCount;$c++){


    // $objPHPExcel->getActiveSheet()->getStyle($larr_Letters2[$c] . "1")->getFont()->setBold(true);

    $objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters[$c])->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension($larr_Letters2[$c])->setAutoSize(true);

}

//generate filename
$filename = strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.xls';

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(substr($_GET["masterfilename"],0,31));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

} //function saveAsExcelOld(){

function saveAsTextFile($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output){


}//function saveAsTextFile(){


function saveAsPdf($columns,
				   $columnsFieldName,
				   $columnsCaption,
				   $columnsDataSource,
				   $columnsDataSourceDatabaseName,
				   $columnsDataSourceTableName,
				   $columnsDatasourceValuePair,
				   $columnsSpecialConditions,
				   $larr_Letters,
				   $larr_Letters2,
				   $lin_letterCount,
				   $lin_letterCount2,
				   $lch_companyName,
				   $lch_companyAddress,
				   $lch_emailAddress,
				   $lch_phoneNumber,
				   $output){

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 048');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->setPageOrientation('P',true,100);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, $lch_companyAddress."\n"."Email:".$lch_emailAddress." Phone:".$lch_phoneNumber."\n".$_GET["masterfilename"]." Listing");
$pdf->setHeaderFont(Array('freemono', '', '10'));
// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

session_start();

date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");
// set font

$pdf->SetFont('freemono', '', 12);


// add a page
$pdf->AddPage();


$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);

$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);


$tbl = '<br/><br/>';
$tbl = $tbl . '<table class="table table-bordered"><tr nobr="true">';
// LOOP ALL COLUMNS - USE $column_key for index
foreach ($columns as $column_key => $column_name){
$tbl = $tbl . '<th><strong>' . $columnsFieldName[$column_key] . '</strong></th>';
} // foreach ($columns as $column_key => $column_name){
$tbl = $tbl . '</tr>';

// AUDIT TRAIL PART
$link = DB_LOCATION;
$params = array (
    "action" => "can_print",
    "fileToOpen" => "save_audit_trail",
    "tableName" => "trnaudittrail",
    "dbconnect" => MONEYTRACKER_DB,
    "module_mst_code" => $_GET["modulemstcode"],
    "menu_item_mst_code" => $_GET["menuitemmstcode"],
    "user_mst_code" => $_SESSION["user_code"],
    "reference" => strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.pdf',
    "description_format" => "User @user_whole_name has @module_action_name in Module @module_name."
);
$result=processCurl($link,$params);
// no need to parse the result. error man o hindi
// end - AUDIT TRAIL PART

// actual records
if($output[0]["result"]==='1'){
	foreach ($output as $key => $value){
		$tbl = $tbl . '<tr>';
		foreach ($columns as $column_key => $column_name){
			

			// static source = use value pair to determine value
            if ($columnsDataSource[$column_key]=="1"){
                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                foreach ($value_pairs as $pair_key => $pair_value) {
                    $static_values = explode(":",$pair_value);
                    if ($static_values["0"]==$value[$column_name]){
                        $tbl = $tbl . '<td>' . $static_values["1"] . '</td>';
                    } //if ($static_values["0"]==$value[$column_name]){
                } // foreach ($value_pairs as $pair_key => $pair_value) {
            } //if ($columnsDataSource[$column_key]=="1"){

            // database source - use datasource dbname and tablename to determine value
            else if ($columnsDataSource[$column_key]=="2"){
                // GET THE ACTUAL VALUE AND DESCRIPTION TO BE DISPLAYED (DESCRIPTION CAN BE CONCATENATION OF FIELDS (SPACE SEPARATED))
                $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);

                // IF MORE THAN 1 = CONCATENATION OF FIELDS
                $value_pairs_actual = explode(" ",$value_pairs["1"]);
                $cols = "";
                if (count($value_pairs_actual)>0){
                    foreach ($value_pairs_actual as $vals){
                        $cols = $cols . "" . $vals . ",";
                    }
                } // if (count($value_pairs_actual)>0){
                $cols = substr($cols, 0,strlen($cols)-1);

                // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                $link = DB_LOCATION;
                $params = array (
                    "action" => "retrieve",
                    "fileToOpen" => "default_select_query",
                    "tableName" => $columnsDataSourceTableName[$column_key],
                    "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                    "columns" => $value_pairs['0'].",".$cols ,
                    "orderby" => $value_pairs['0']." ASC",
                    "conditions[equals][".$value_pairs['0']."]" => $value[$column_name]
                );
                $result=processCurl($link,$params);
                $output2 = json_decode($result,true);
                if($output2[0]["result"]==='1'){
                    // CONCATENATE DATA RETRIEVED
                    foreach ($output2 as $data_source_key => $data_source_value){
                        $concatval = "";
                        foreach ($value_pairs_actual as $vals){
                            $concatval = $concatval . $data_source_value[$vals] . ' ';
                        }
                        $tbl = $tbl . '<td>' . $concatval . '</td>';
                        //valueToDisplay = $data_source_value[$value_pairs['1']];
                        
                    } // foreach ($output2 as $data_source_key => $data_source_value){
                } // if($output[0]["result"]==='1'){
                    

            } //else if ($columnsDataSource[$column_key]=="2"){

            // default source
            else {
                $tbl = $tbl . '<td>' . $value[$column_name] . '</td>';
            } // ELSE ng else if ($columnsDataSource[$column_key]=="2"){


		} // foreach ($columns as $column_key => $column_name){
		$tbl = $tbl . '</tr>';
	} // foreach ($output as $key => $value){
} // if($output[0]["result"]==='1'){
$tbl = $tbl . '</table><br/>';


$pdf->writeHTML($tbl, true, false, false, false, '');


$filename = strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.pdf';

$pdf->Output($filename, 'D');


}//function saveAsPdf(){

 ?>
