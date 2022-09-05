<?php
error_reporting(0);

@include_once("DatabaseConnect.php");
function sanitizeField($pinoutName=""){
	//$lch_dbAccess = new DatabaseAccess();
	$pinoutName = stripslashes($pinoutName);
	//$pinoutName = mysql_real_escape_string($pinoutName);
	//$pinoutName = mysql_escape_string($pinoutName);
	$pinoutName = addslashes($pinoutName);
	return $pinoutName;
}

?>