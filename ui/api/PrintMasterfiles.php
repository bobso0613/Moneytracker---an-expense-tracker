<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2013-05-14
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**$tcpdf_include_path
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

session_start();

date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");


// Include the main TCPDF library (search for installation path).
require_once('./tcpdf/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Bob So");
$pdf->SetTitle($_GET["masterfilename"]." Listing");
$pdf->SetSubject($_GET["masterfilename"]." Listing");
$pdf->SetKeywords($_GET["masterfilename"]." Listing");
$pdf->setPageOrientation('P',true,100);

// set default header data
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

// $lch_companyName, $lch_companyAddress."\n"."Email: ".$lch_emailAddress." Phone: ".$lch_phoneNumber

 $pdf->SetHeaderData('../../../resources/assets/headerlogo2.png', "",""); // , array(0,0,0), array(255,255,255)

//$pdf->setPrintHeader(false);

$pdf->setHeaderFont(Array('anonymouspro', '', '9'));
// set header and footer fonts
// $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(10, 28, 10);
$pdf->SetHeaderMargin(10);
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

// set font
// $pdf->SetFont('monofont', '', 20);
$pdf->SetFont('anonymouspro', '', 9);

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



$link = DB_LOCATION;
$params = array (
    "action" => "retrieve-record-column",
    "fileToOpen" => "default_select_query",
    "tableName" => $_GET["tablename"],
    "dbconnect" => $_GET["databasename"],
    "columns" => ltrim(str_replace("|", ",", $_GET["columnstoquery"]),"code,"),
    "orderby" => $columns[1] . " ASC"
);
$result=processCurl($link,$params);
$output = json_decode($result,true);

//$tbl = '<br/><br/>';
$tbl = '<br/><div style="text-align:center;margin-top:10px;margin-bottom:10px;"><font size="16px">'.$_GET["masterfilename"]." Listing".'</font></div>';
$tbl = $tbl . '<br/><table class="table table-bordered"><tr nobr="true">';
// LOOP ALL COLUMNS - USE $column_key for index
foreach ($columns as $column_key => $column_name){
    if ($column_name!="code") {
        $tbl = $tbl . '<th><strong>' . $columnsFieldName[$column_key] . '</strong></th>';
    } // if ($column_name!="code") {
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
			
            if ($column_name!="code") {

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

            } // if ($column_name!="code") {


		} // foreach ($columns as $column_key => $column_name){
		$tbl = $tbl . '</tr>';
	} // foreach ($output as $key => $value){
} // if($output[0]["result"]==='1'){
$tbl = $tbl . '</table><br/>';


@$pdf->writeHTML($tbl, true, false, false, false, '');


$filename = strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.pdf';

// header('Content-type: application/pdf');
// header('Content-Disposition: attachment; filename="'.$filename.'"');

// header("Pragma: public"); // required
//     header("Expires: 0");
//     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//     header("Cache-Control: private",false); // required for certain browsers
//     header("Content-Type: $ctype"); //$ctype="application/force-download";
//     header("Content-Disposition: attachment; filename=".$Sfilename.";" );//$base="x.pdf"
//     header("Content-Transfer-Encoding: binary");
//     header("Content-Length: ".@filesize($filename));
//     @readfile($rfilename);
//     unlink($rfilename);//delete after download
// -----------------------------------------------------------------------------

//Close and output PDF document
//echo '<iframe src="" style="width:718px; height:700px;" frameborder="0"></iframe>';

$pdf->Output($filename, 'I');

//============================================================+
// END OF FILE
//============================================================+
?>