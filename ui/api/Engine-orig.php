<?php
/*
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
*/

ini_set('session.cookie_lifetime', 172800);
ini_set('session.gc_maxlifetime', 172800);
date_default_timezone_set('Asia/Manila');


class Engine {

	private $mode;
	private $state;

	public function __construct($mode="",$state="") {
		$this->state = $state;
		$this->mode = $mode;

		$this->startSession();
		
		$this->renewSession();
		//$this->checkAccountActive();

	} // public function __construct($mode="",$state="") {


	public function startSession(){
		if(!isset($_SESSION)){
			@session_destroy();
		    session_start();
		} // if(!isset($_SESSION)){
	} // public function startSession(){

	public function checkSession ($dir=""){
		if (!isset($_SESSION['PHPSESSID']) && !(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!=='')){
			setcookie("error_message",'Please Log in.',time() + (10 * 365 * 24 * 60 * 60),"/");
			header("HTTP/1.1 200 OK");
			header("Location: ".$dir."login");
			exit();
		} // if (!isset($_SESSION['PHPSESSID']) && !(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!=='')){
		else if (!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')){
			//$this->setSession('error_message','Please Log in.');
			
			setcookie("error_message",'Please Log in.',time() + (10 * 365 * 24 * 60 * 60),"/");
			header("HTTP/1.1 200 OK");
			header("Location: ".$dir."login");
			exit();
		}  // else if (!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')){
		else {
			if ($this->mode==="refresh"){
				$this->updateSession();
			} // if ($this->mode==="refresh"){
		} // ELSE ng else if (!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')){
	} // public function checkSession ($dir=""){

	public function setSession ($sessionName='',$sessionValue=''){
		setcookie($sessionName,$sessionValue,time() + (10 * 365 * 24 * 60 * 60),"/");
		$_SESSION[$sessionName] = $sessionValue;
	} // public function setSession ($sessionName='',$sessionValue=''){

	public function unSetSession (){

		if (isset($_COOKIE['username']) && @$_COOKIE['username']!=""){
			$link = DB_LOCATION;
			$params = array (
				"action" => "update_online_state",
				"fileToOpen" => "update_online_state",
				"tableName" => "mstuser",
				"dbconnect" => MONEYTRACKER_DB,
				"username" => $_COOKIE['username'],
				"state" => "0"
			);
			$result=processCurl($link,$params);
		} // if (isset($_COOKIE['username']) && @$_COOKIE['username']!=""){
		

		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if(isset($_COOKIE['username'])) {
		  unset($_COOKIE['username']);
		  setcookie('username', '', time() - 3600,"/"); // empty value and old timestamp
		} // if(isset($_COOKIE['username'])) {

		if(isset($_COOKIE['user_code'])) {
		  unset($_COOKIE['user_code']);
		  setcookie('user_code', '', time() - 3600,"/"); // empty value and old timestamp
		} // if(isset($_COOKIE['user_code'])) {

		if(isset($_COOKIE['user_image_code'])) {
		  unset($_COOKIE['user_image_code']);
		  setcookie('user_image_code', '', time() - 3600,"/"); // empty value and old timestamp
		} // if(isset($_COOKIE['user_image_code'])) {
		// Finally, destroy the session.
		

		unset($_SESSION['username']);
		unset($_SESSION['user_code']);
		unset($_SESSION['user_image_code']);

		session_unset();
		session_regenerate_id(); 

		$this->stopSession();
		setcookie('PHPSESSID', '', time() - 3600,"/");
	} // public function unSetSession (){

	public function stopSession(){
		session_destroy(); 

	} // public function stopSession(){

	public function updateSession(){
		if (isset($_SESSION['username']) && isset($_SESSION['user_code'])) {
			$link = DB_LOCATION;
			$params = array (
				"action" => "get_user_details",
				"fileToOpen" => "default_select_query",
				"tableName" => "mstuser",
				"dbconnect" => MONEYTRACKER_DB,
				"columns" => "username,code,user_image_code,is_active",
				"conditions[equals][username]" => $_SESSION['username'],
				"username" => $_SESSION['username']
			);
			$result=processCurl($link,$params);
			$a = json_decode($result,true);
			if ($a[0]["result"] ==  '1'){

				if (intval($a[0]["is_active"])!=1) {

					setcookie("error_message",'This account was already inactive. Please contact system administrator.',time() + (10 * 365 * 24 * 60 * 60),"/");
					$this->unSetSession();
					header("HTTP/1.1 200 OK");
					header("Location: ".$dir."login");
					exit();

				} // if (intval($a[0]["is_active"])!=1) {
				else {
					$this->setSession('username',$a[0]["username"]);	
					$this->setSession('user_code',$a[0]["code"]);	
					$this->setSession('user_image_code',$a[0]["user_image_code"]);	
				} // ELSE ng if (intval($a[0]["is_active"])!=1) {

			} // if ($a[0]["result"] ==  '1'){
			else {
				$this->setSession('error_message',$a[0]["error_message"]);					
				//header('Location: ./login.php');
			} // ELSE ng if ($a[0]["result"] ==  '1'){
		} // if (isset($_SESSION['username']) && isset($_SESSION['user_code'])) {
	} // public function updateSession(){

	public function renewSession(){
		if (!isset($_SESSION['username']) && !isset($_SESSION['user_code'])) {
			if ((isset($_COOKIE['username']) && $_COOKIE['username']!=='') && !isset($_SESSION['username'])){
				
					$link = DB_LOCATION;
					$params = array (
						"action" => "get_user_details",
						"fileToOpen" => "default_select_query",
						"tableName" => "mstuser",
						"dbconnect" => MONEYTRACKER_DB,
						"columns" => "username,code,user_image_code,is_active",
						"conditions[equals][username]" => $_COOKIE['username'],
						"username" => $_COOKIE['username']
					);
					$result=processCurl($link,$params);
					$a = json_decode($result,true);
					if ($a[0]["result"] ==  '1'){

						if (intval($a[0]["is_active"])!=1) {

							setcookie("error_message",'This account was already inactive. Please contact system administrator.',time() + (10 * 365 * 24 * 60 * 60),"/");
							$this->unSetSession();
							header("HTTP/1.1 200 OK");
							header("Location: ".$dir."login");
							exit();

						} // if (intval($a[0]["is_active"])!=1) {
						else {

							$this->setSession('username',$a[0]["username"]);	
							$this->setSession('user_code',$a[0]["code"]);	
							$this->setSession('user_image_code',$a[0]["user_image_code"]);	
							$link = DB_LOCATION;
							$params = array (
								"action" => "update_online_state",
								"fileToOpen" => "update_online_state",
								"tableName" => "mstuser",
								"dbconnect" => MONEYTRACKER_DB,
								"username" => $_COOKIE['username'],
								"state" => "1"
							);
							$result=processCurl($link,$params);

						} // ELSE ng if (intval($a[0]["is_active"])!=1) {

						
						
					} // if ($a[0]["result"] ==  '1'){
					else {
						$this->setSession('error_message',$a[0]["error_message"]);					
						//header('Location: ./login.php');
					} // ELSE ng if ($a[0]["result"] ==  '1'){
					
					
			} // if ((isset($_COOKIE['username']) && $_COOKIE['username']!=='') && !isset($_SESSION['username'])){
			
		} // if (!isset($_SESSION['username']) && !isset($_SESSION['user_code'])) {
		else {
			//$this->updateSession();
			if ($this->mode!=="logout") {

				if ($this->mode=="idle") {
					$link = DB_LOCATION;
					$params = array (
						"action" => "update_online_state",
						"fileToOpen" => "update_online_state",
						"tableName" => "mstuser",
						"dbconnect" => MONEYTRACKER_DB,
						"username" => $_COOKIE['username'],
						"state" => "2"
					);
					$result=processCurl($link,$params);
				} /* if ($this->mode=="idle") { */
				else {

					$link = DB_LOCATION;
					$params = array (
						"action" => "update_online_state",
						"fileToOpen" => "update_online_state",
						"tableName" => "mstuser",
						"dbconnect" => MONEYTRACKER_DB,
						"username" => $_SESSION['username'],
						"state" => "1"
					);
					if ($this->state == "true"){
						$params['state'] = "2" ;
					} // if ($this->state == "true"){
					$result=processCurl($link,$params);
				} /*else */
				

			} // if ($this->mode!=="logout") {
			
		} // else {
		
						
		return;
	} // public function renewSession(){

	function getOnlineUsers($lch_param ){ 

		$link = DB_LOCATION;
		$params = array (
			"action" => "get_online_users",
			"fileToOpen" => "retrieve_online_users",
			"tableName" => "mstuser",
			"dbconnect" => MONEYTRACKER_DB
		);
		$jsonResult=processCurl($link,$params);
		$arrOnlineUsers = json_decode($jsonResult,true);

		if ($arrOnlineUsers[0]["result"] ==  '1'){
			if ($lch_param == 'list')
			{
			return $arrOnlineUsers[0]["online_list"];
			}
			else if ($lch_param == 'idle')
			{
			return $arrOnlineUsers[0]["online_idle"];
			}
			
			else {
				return $arrOnlineUsers[0]["online_count"];
			}
		} // if ($arrOnlineUsers[0]["result"] ==  '1'){

		else {

		} // else ng if ($arrOnlineUsers[0]["result"] ==  '1'){		

		return false;

	} // function getOnlineUsers($lch_param ){ 

} // class Engine {

?>