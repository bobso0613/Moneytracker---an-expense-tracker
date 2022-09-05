<?php
/**
*	FUNCTION TO GET USER PRIVILEGE SET  FOR MODULE_CODE PER USER_CODE
*	@param String $module_code  - The module involved
*	@param String $user_code 	- The user involved
*	@return Array $privilege_set	- set of privilege
*		sample structure:
*			array["0"] = 1
*				"0" -> module action code - should be specified in application parameter to get specific value
*					if module_action_code == -1 -> the actual access
*				 1  -> access_type - 1:allow ; 2:deny ; 
*
*/
function getPrivilegeSet($module_code="",$user_code=""){
	$privilege_set = array();

	if ($module_code!="" && $user_code!=""){

		$link = DB_LOCATION;
	    $params = array (
	        "action" => "retrieve-privilege",
	        "fileToOpen" => "retrieve_privilege_menu_user",
	        "module_code" => $module_code,
	        "user_code" => $user_code,
	        "dbconnect" => MONEYTRACKER_DB
	    );
	    $result=processCurl($link,$params);

	    $output = json_decode($result,true);
	    

	    $privilege_set = $output;

	} // if ($module_code!="" && $user_code!=""){
	


	return $privilege_set;
} // function getPrivilegeSet($module_code="",$user_code=""){

?>