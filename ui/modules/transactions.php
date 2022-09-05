<?php
//// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//// header("Cache-Control: post-check=0, pre-check=0", false);
//// header("Pragma: no-cache");
/*** error reporting on ***/
error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*** define the site path constant ***/
$site_path = realpath(dirname(__FILE__));
define ('SITE_PATH', $site_path);

require_once SITE_PATH.'/dependencies/TransactionsTemplate.php';

//echo SITE_PATH;

$app = new TransactionsTemplate;

?>
