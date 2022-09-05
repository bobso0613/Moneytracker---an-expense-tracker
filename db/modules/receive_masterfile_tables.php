<?php
error_reporting(0);

// // header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// // header("Cache-Control: post-check=0, pre-check=0", false);
// // header("Pragma: no-cache");
header('Content-type: application/json; charset=iso-8859-1');
@include_once("../api/DatabaseParams.php");
date_default_timezone_set('Asia/Manila');

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = $_POST["tablename"];
//$table = ;
 
// Table's primary key
$primaryKey = 'code';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
/*$columns = array(
    array( 'db' => 'action_name', 'dt' => 0 ),
    array( 'db' => 'description',  'dt' => 1 )
);*/
$columns = array();
$columnstodisplay = explode(",",$_POST["columnstodisplay"]);
foreach ($_POST["postvalues"]["columns"] as $col_key => $col_val){
    foreach ($col_val as $key => $val){
        if ($key=="data"&&$val!=""){
            //echo  $val ."-----";
            array_push($columns,array('db'=>$columnstodisplay[$val],'dt'=>intval($val)));
        }
        
    }
}
 
// SQL server connection information
/*
$sql_details = array(
    'user' => MYSQL_USER,
    'pass' => MYSQL_PASSWORD,
    'db'   => $_POST["dbconnect"],
    'host' => MYSQL_HOST
);*/
$sql_details = array(
    'user' => $garr_DatabaseMapping[$_POST["dbconnect"]]["username"],
    'pass' => $garr_DatabaseMapping[$_POST["dbconnect"]]["password"],
    'db'   => $garr_DatabaseMapping[$_POST["dbconnect"]]["actual_tablename"],
    'host' => $garr_DatabaseMapping[$_POST["dbconnect"]]["host"]
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
//require( '../api/ssp.class.php' );
//include_once("/../api/SSP.php");
include_once(dirname(__FILE__) ."/../api/SSP.php");
 
echo json_encode(
    //SSP::simple( $_POST["postvalues"], $sql_details, $table, $primaryKey, $columns )
    SSP::customizedSimple($_POST["postvalues"], $sql_details, $table, $primaryKey, $columns,@$_POST["filterdataname"],@$_POST["filterdata"])
);
?>