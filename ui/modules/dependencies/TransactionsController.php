<?php
//// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//// header("Cache-Control: post-check=0, pre-check=0", false);
//// header("Pragma: no-cache");

require_once("../api/Engine.php");
require_once("../api/CurlAPI.php");
require_once("../api/SystemConstants.php");
require_once("../api/UserPrivilegeFunctions.php");

class TransactionsController{
	
	protected $params = "";
	protected $BUTTON_ICONS  = array("Edit"=>"fa fa-pencil-square-o",
								"Delete"=>"fa fa-trash-o",
								"Show"=>"fa fa-eye",
								"Add"=>"fa fa-plus-circle",
								"Print"=>"fa fa-print",
								"Route"=>"fa fa-share",
								"Forward"=>"fa fa-share",
								"Upload"=>"fa fa-upload",
								"Generate"=>"fa fa-check",
								"First"=>"fa fa-angle-double-left",
								"Prev"=>"fa fa-angle-left",
								"Last"=>"fa fa-angle-double-right",
								"Next"=>"fa fa-angle-right",
								"Override"=>"fa fa-key");



	protected $TRANSACTION_TEMPLATE_CODE="";

	protected $ACCESS_KEY="";
	protected $PROGRAM_NAME="";
	protected $NAME="";
	protected $DESCRIPTION="";

	protected $MENU_ITEM_MST_CODES="";
	protected $MODULE_MST_CODE="";

	protected $PAGE_SETTINGS = array();

	protected $HAS_ADD_PRIVILEGE=true;
	protected $HAS_SHOW_PRIVILEGE=true;
	protected $HAS_EDIT_PRIVILEGE=true;
	protected $HAS_DELETE_PRIVILEGE=true;
	protected $HAS_PRINT_PRIVILEGE=true;

	protected $HAS_UPLOAD_PRIVILEGE=true;
	protected $HAS_POST_PRIVILEGE=true;

	protected $HAS_VOID_PRIVILEGE=true;


	protected $HAS_ACCESS=false;

	// privilege set for MODULE_MST_CODE of the USER
	protected $PRIVILEGE_SET = array();
	protected $APP_PARAM_ACTIONS = array();

	protected $PHP_DISPLAY_FILENAME = "";
	protected $JS_FILENAMES = "";

	public function __construct($params=""){
		$this->params = $params;
		$columnsToFormat = ""; 
		$columnsFieldDataType = ""; 
		$this->PAGE_SETTINGS["CurrentDirectory"] = "../../";

		$this->PAGE_SETTINGS["Engine"] = new Engine("refresh");
		$this->PAGE_SETTINGS["Engine"]->checkSession($this->PAGE_SETTINGS["CurrentDirectory"]);

		$this->PAGE_SETTINGS["NoNightMode"] = false;

		$this->PAGE_SETTINGS["CssEnable"] = array();
		$this->PAGE_SETTINGS["CssEnable"]["Chat"] = false;
		$this->PAGE_SETTINGS["CssEnable"]["DataTables"] = true;
		$this->PAGE_SETTINGS["CssEnable"]["OnlineUsers"] = true;
		$this->PAGE_SETTINGS["CssEnable"]["DatePicker"] = true;
		$this->PAGE_SETTINGS["CssEnable"]["TypeAhead"] = true;


		$this->PAGE_SETTINGS["JSEnable"] = array();
		$this->PAGE_SETTINGS["JSEnable"]["DatePicker"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["Chat"] = false;
		$this->PAGE_SETTINGS["JSEnable"]["DataTables"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["Number"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["OnlineUsers"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["LogoutCheck"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["MasterfileFunctions"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["Chained"] = true;
		$this->PAGE_SETTINGS["JSEnable"]["TypeAhead"] = true;

		// get parameters
		$link = DB_LOCATION;
		$params = array (
			"action" => "retrieve-template-columns",
			"fileToOpen" => "default_select_query",
			"tableName" => "mstapplicationparameter",
			"dbconnect" => MONEYTRACKER_DB,
			"columns" => "parameter_key,parameter_value",
			"conditions[in][parameter_key]" => 'module_action_add_code,module_action_edit_code,module_action_delete_code,module_action_print_code,module_access_code,module_upload_action_code,module_action_upload_code,module_action_post_code,module_action_void_code',
			"orderby" => "code ASC"
		);
		$result=processCurl($link,$params);
		$output = json_decode($result,true);
		if($output[0]["result"]==='1'){
			foreach ($output as $key => $value){
				$this->APP_PARAM_ACTIONS[$value["parameter_key"]] = $value["parameter_value"];
			} // foreach ($output as $key => $value){

		} // if($output[0]["result"]==='1'){
	} // public function __construct($params=""){

	/* MAIN FUNCTION FOR ALL MASTER FILES -> RENDER WILL DEPEND ON DATA PROVIDED */
	public function render(){




		$link = DB_LOCATION;
		$params = array (
			"action" => "retrieve-template-hdr",
			"fileToOpen" => "default_select_query",
			"tableName" => "_transactiontemplates",
			"dbconnect" => MONEYTRACKER_DB,
			"columns" => "code,access_key,program_name,transaction_name,transaction_description,".
						"menu_item_mst_codes,module_mst_code,".
						"php_display_filename,js_filenames",
			"conditions[equals][access_key]" => base64_encode($this->params[0]."/".$this->params[1])
		);
		$result=processCurl($link,$params);
		$output = json_decode($result,true);
		if($output[0]["result"]==='1'){
			foreach ($output as $key => $value){
				$this->TRANSACTION_TEMPLATE_CODE = $value["code"];

				$this->ACCESS_KEY = $value["access_key"];
				$this->PROGRAM_NAME = $value["program_name"];
				$this->NAME = $value["transaction_name"];
				$this->DESCRIPTION = $value["transaction_description"];

				$this->PAGE_SETTINGS["menu_program_name"] = $this->PROGRAM_NAME;
				$this->PAGE_SETTINGS["PageTitle"] = $this->NAME;

				$this->MENU_ITEM_MST_CODES = $value["menu_item_mst_codes"];
				$this->MODULE_MST_CODE = $value["module_mst_code"];


				$this->PHP_DISPLAY_FILENAME = $value["php_display_filename"];
				$this->JS_FILENAMES = $value["js_filenames"];

			} //foreach ($output as $key => $value){

			// get privilege
			$this->PRIVILEGE_SET = getPrivilegeSet($this->MODULE_MST_CODE,$_SESSION["user_code"]);

			//var_dump($this->PRIVILEGE_SET);

			if ((count($this->PRIVILEGE_SET)>0&&isset($this->PRIVILEGE_SET["result"])&&$this->PRIVILEGE_SET["result"]=="0")||
				count($this->PRIVILEGE_SET)<=0||
				array_key_exists("-1", $this->PRIVILEGE_SET)==false||
				(count($this->PRIVILEGE_SET)>0&&isset($this->PRIVILEGE_SET["-1"])&&$this->PRIVILEGE_SET["-1"]=="2")){
				// no access
				$this->HAS_ACCESS=false;
				$this->setupPage("error2");


			} /* if ((count($this->PRIVILEGE_SET)>0&&isset($this->PRIVILEGE_SET["result"])&&$this->PRIVILEGE_SET["result"]=="0")||
				count($this->PRIVILEGE_SET)<=0||
				(count($this->PRIVILEGE_SET)>0&&$this->PRIVILEGE_SET["-1"]=="2")){ */
			else {

				
				/* check one by one access to set variables for access */

				// check for Add Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_add_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_add_code"]]=="2"){
					$this->HAS_ADD_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_add_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_add_code"]]!="1"){ */

				// check for Edit Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_edit_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_edit_code"]]=="2"){
					$this->HAS_EDIT_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_edit_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_edit_code"]]!="1"){ */

				// check for Delete Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_delete_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_delete_code"]]=="2"){
					$this->HAS_DELETE_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_delete_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_delete_code"]]!="1"){ */

				// check for Print Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_print_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_print_code"]]=="2"){
					$this->HAS_PRINT_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_print_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_print_code"]]!="1"){ */



				// check for UPLOAD Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_upload_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_upload_code"]]=="2"){
					$this->HAS_UPLOAD_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_upload_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_upload_code"]]!="1"){ */


				// check for POST Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_post_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_post_code"]]=="2"){
					$this->HAS_POST_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_post_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_post_code"]]!="1"){ */


					// check for DISTRIBUTE Access
				if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_void_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_void_code"]]=="2"){
					$this->HAS_VOID_PRIVILEGE = false;
				} /* if (!array_key_exists($this->APP_PARAM_ACTIONS["module_action_distribute_code"], $this->PRIVILEGE_SET)||
					$this->PRIVILEGE_SET[$this->APP_PARAM_ACTIONS["module_action_distribute_code"]]!="1"){ */


				//echo json_encode($this->PRIVILEGE_SET);

				$this->HAS_ACCESS=true;
				$this->setupPage("okay");
			} /* ELSE ng if ((count($this->PRIVILEGE_SET)>0&&isset($this->PRIVILEGE_SET["result"])&&$this->PRIVILEGE_SET["result"]=="0")||
				count($this->PRIVILEGE_SET)<=0||
				(count($this->PRIVILEGE_SET)>0&&$this->PRIVILEGE_SET["-1"]=="2")){ */

			

		} //if($output[0]["result"]==='1'){
		else {
			$this->NAME = "Error Detected";
			$this->HAS_ACCESS=false;
			$this->PAGE_SETTINGS["menu_program_name"] = $this->PROGRAM_NAME;
			$this->PAGE_SETTINGS["PageTitle"] = $this->NAME;
			$this->setupPage("error");
		} // else if($output[0]["result"]==='1'){

		//var_dump($this->params);
	} //public function render(){

	public function setupPage($status=""){

		$PAGE_SETTINGS = $this->PAGE_SETTINGS;

		require_once("../includes/header_meta.php");
		require_once("../includes/body_common_top.php");
		//echo 	$PAGE_SETTINGS["menu_program_name"];
		require_once("../includes/body_header_navigation.php");
		require_once("../includes/body_sidebar_left.php");
		// form here

		
		$this->displayBody($status);


		require_once("../includes/body_common_bottom.php");

		$this->displayScripts();
		$this->displayJSFilenames();

		echo '</body>';
		echo '</html>';
		
	} //public function setupPage(){


	// DISPLAY UNIQUE JAVASCRIPT
	private function displayJSFilenames(){
		if ($this->JS_FILENAMES!=""&&$this->JS_FILENAMES!=null) {
			//realpath($this->PHP_DISPLAY_FILENAME)!=false
			//include_once($this->PHP_DISPLAY_FILENAME);
			// echo '<script src="../../resources/js/masterfilesfunctions.min.js"></script>';
			//echo $this->JS_FILENAMES;
			$javascripts_location = explode("|",$this->JS_FILENAMES);
			foreach ($javascripts_location as $js){
				if ($js!=""){
					echo '<script src="'.$js.VERSION_AFFIX.'" type="text/javascript"></script>'; 
				} // if ($js!=""&&realpath($js)!=false){
			} // foreach ($javascripts_location as $js){
		} // if ($this->PHP_DISPLAY_FILENAME!="") {
		echo '<script src="../../resources/js/removeblocker.min.js'.VERSION_AFFIX.'"></script>';
	} // private function displayUniqueBody($status=""){

	// DISPLAY MAIN BODY FOR UNIQUE MASTER FILE
	private function displayUniqueBody($status=""){

		if ($this->PHP_DISPLAY_FILENAME!="" && realpath($this->PHP_DISPLAY_FILENAME)!=false) {
			include_once($this->PHP_DISPLAY_FILENAME);
			echo '<div id="dialog-modal-wrapper" style="display:none"></div>';
			echo '<div id="modal-wrapper" style="display:none"></div>';
			echo '<div id="modal-wrapper-inner" style="display:none"></div>';
			echo '<div id="modal-wrapper-inner-inner" style="display:none"></div>';
		} // if ($this->PHP_DISPLAY_FILENAME!="" && realpath($this->PHP_DISPLAY_FILENAME)!=false) {
		else {
			$this->displayErrors("error3");
		} // ELSE ng if ($this->PHP_DISPLAY_FILENAME!="" && realpath($this->PHP_DISPLAY_FILENAME)!=false) {
	} // private function displayUniqueBody($status=""){

	// DISPLAY MAIN BODY
	private function displayBody($status=""){
		$PAGE_SETTINGS = $this->PAGE_SETTINGS;
	?>
		<!-- Page Content -->
		<div id="page-wrapper" style="">
		    <div class="fixed-toggle-button left">
		                        <button class="btn btn-default" id="sidebar-toggle-button" data-mode="showed"
		                        data-toggle="tooltip" data-placement="right" title="Click to toggle sidebar."><i id="logo-direction" class="fa fa-chevron-left"></i></button>
		                    </div>
		    <div class="row">
		        <div class="col-lg-12">
		            <h1 class="page-header with-desc"><?php echo $this->NAME; ?></h1>
		            <?php if ($this->DESCRIPTION!='') {
	            	?>
	            		<h5 class="header-desc"><?php echo $this->DESCRIPTION; ?></h5>
	            	<?php
		            } //if ($this->DESCRIPTION!='') {
	            	else {
	            		echo '<h5 class="header-desc"></h5>';
	            		
	            	} // else ng if ($this->DESCRIPTION!='') {
		            ?>
		            
		        </div>
		        <!-- /.col-lg-12 -->
		    </div>

		    <div class="row">
	        	<div class="col-lg-12 col-md-12 col-sm-12">
	        			<?php
	        			if ($status=="error"||$status=="error2"){
        					$this->displayErrors($status);
	        			} //if ($status=="error"){
	        			else{
	        				$this->displayAlertContainers();
            				$this->displayUniqueBody();
	        			} // ELSE ng if ($status=="error"){
	        			?>
	        	</div>
	    	</div>

		</div> <!-- #page-wrapper -->

	<?php
	} //private function displayBody(){

	private function displayErrors($status=""){
	?>			
		<div class="alert alert-danger">
		  <?php if ($status=="error") {
	  		?>
	  			<h3>The page you are looking for is not found.</h3>
		  		<h4>You will be redirected to the index page in 30 seconds, or <a href="../../index" class="btn-link">click here</a> if it does not work.</h4>

		  		<script type="text/javascript">
					setTimeout(function(){location.href="../../index"},30000);
				</script>
	  	<?php
		  } // if ($status=="error") {
	  	else if ($status=="error3") {
	  		?>
	  			<h3>Fatal Error!</h3>
		  		<h4>The internal files needed for this page were not found.</h4>
	  	<?php
		  } // if ($status=="error") {
		  else {
		  ?>
		  		<h3><?php echo "You are not allowed to access " . $this->NAME .  "." ?></h3>
		  		<h4>Please contact system administrator for more information.</h4>
		  		<h5>You are either:</h5>
		  		<ul><li>You have no access.</li><li>Denied Access.</li><li>Not Assigned to any group.</li><li>Wrongfully accessed this page.</li></ul>
		  <?php
		  } // else ng if ($status=="error") {
		  ?>
		  
		</div>

	<?php
	} // private function displayErrors(){

	private function displayAlertContainers(){
	?>
		<div class="alert alert-danger" id="outer_error_container" style="display:none">
            <button type="button" class="close" id="outer_error_close">&times;</button>
            <h5 id="outer_error_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>
            
            <div id="outer_error_container_content">

            </div> <!-- #outer_error_container_content -->
            
        </div> <!-- #outer_error_container -->

        <div class="alert alert-success" id="outer_success_container" style="text-align:center;display:none">
            <button type="button" class="close" id="outer_success_close">&times;</button>
            <h4 style="margin-bottom:0px"><strong id="outer_success_message">X successfully saved.</strong></h4>
        </div>
    <?php
    }  //private function displayAlertContainers(){


	// DISPLAY BOTTOM SCRIPTS
	private function displayScripts(){
			
		?>
		<script type="text/javascript">
				var ABSOLUTE_PATH = "<?php echo ABSOLUTE_PATH;?>";

				
		</script>


		<?php

		if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["MasterfileFunctions"]) && $this->PAGE_SETTINGS["JSEnable"]["MasterfileFunctions"]===true) {
        echo '<script src="../../resources/js/masterfilesfunctions.min.js'.VERSION_AFFIX.'"></script>';
    	} 

		if (isset($this->PAGE_SETTINGS["NoNightMode"]) && $this->PAGE_SETTINGS["NoNightMode"]===false) {
			echo '<script src="../../resources/js/nightmode.min.js'.VERSION_AFFIX.'"></script>';
		}

		
	} //private function displayScripts(){


} // class TransactionsController{
?>