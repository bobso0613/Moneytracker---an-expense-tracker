<?php
/*
define("MYSQL_HOST","localhost");
define("MYSQL_USER","root");
define("MYSQL_PASSWORD","");
*/

define("MONEYTRACKER_DB","bd926c5_moneytracker");

// THIS IS CREATED BECAUSE STATIC YUNG DATABASE NAME SA MGA SOURCE CODES -> PARA DI NA PAPALITAN ISA ISA ULIT.
$garr_DatabaseMapping = array(MONEYTRACKER_DB=>array("host"=>"localhost","username"=>"root","password"=>"abc123","actual_tablename"=>MONEYTRACKER_DB));

//echo json_encode ($garr_DatabaseMapping);

/*$mysql_host = "localhost";
$mysql_database = "aegis-system";
$mysql_user = "root";
$mysql_password = "";*/
?>
