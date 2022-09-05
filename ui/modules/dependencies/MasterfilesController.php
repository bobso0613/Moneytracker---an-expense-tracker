<?php
//// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//// header("Cache-Control: post-check=0, pre-check=0", false);
//// header("Pragma: no-cache");
header('Content-type: charset=iso-8859-1');

require_once("../api/Engine.php");
require_once("../api/CurlAPI.php");
require_once("../api/SystemConstants.php");
require_once("../api/UserPrivilegeFunctions.php");

class MasterfileController{
	
	protected $params = "";
	protected $BUTTON_ICONS  = array("Edit"=>"fa fa-pencil-square-o",
								"Delete"=>"fa fa-trash-o",
								"Show"=>"fa fa-eye",
								"Add"=>"fa fa-plus-circle",
								"Print"=>"fa fa-print",
								"First"=>"fa fa-angle-double-left",
								"Prev"=>"fa fa-angle-left",
								"Last"=>"fa fa-angle-double-right",
								"Next"=>"fa fa-angle-right");

	/*

	DATA TYPE
	1 - Character (one line)
	2 - Integer
	3 - Decimal
	4 - Date
	5 - Character (Paragraph)
	6 - Character (Text Editor)
	7 - Combo Box
	8 - Radio Button
	9 - Checkbox
	10 - Lookup
	11 - Parent

	*/

	protected $MASTERFILE_TEMPLATE_CODE="";

	protected $ACCESS_KEY="";
	protected $PROGRAM_NAME="";
	protected $NAME="";
	protected $DESCRIPTION="";
	protected $TYPE="";

	protected $DATABASE_NAME="";
	protected $TABLE_NAME="";

	protected $PRIMARY_CODE_FIELD_NAMES="";
	protected $MENU_ITEM_MST_CODES="";
	protected $MODULE_MST_CODE="";

	protected $SEARCH_BY_FIELDS="";
	protected $SHOW_RECORD_FIELDS="";
	protected $WHOLE_MASTERFILE_ACTIONS="";
	protected $PER_RECORD_ACTIONS="";
	protected $SPECIAL_CONDITIONS = "";

	protected $SPAN_SIZE="";

	protected $PAGE_SETTINGS = array();

	protected $UNSEARCHABLE_COLUMNS = "";

	protected $ACTION_DATA = array();
	protected $ACTION_COUNT = 0;

	protected $FILTER_DATA = array();
	protected $FILTER_COUNT = 0;
	protected $COLUMN_DATA = array();
	protected $COLUMN_COUNT = 0;
	protected $COLUMN_TABLE_DISPLAY_COUNT = 0;
	protected $AT_LEAST_ONE_RECORD_ACTION=false;
	protected $HAS_ADD=false;
	protected $HAS_SHOW=false;
	protected $HAS_EDIT=false;
	protected $HAS_DELETE=false;
	protected $HAS_PRINT=false;

	protected $HAS_ADD_PRIVILEGE=true;
	protected $HAS_SHOW_PRIVILEGE=true;
	protected $HAS_EDIT_PRIVILEGE=true;
	protected $HAS_DELETE_PRIVILEGE=true;
	protected $HAS_PRINT_PRIVILEGE=true;

	protected $HAS_ACCESS=false;

	// privilege set for MODULE_MST_CODE of the USER
	protected $PRIVILEGE_SET = array();
	protected $APP_PARAM_ACTIONS = array();

	protected $UNIQUE_DISPLAY_FILENAME = "";
	protected $UNIQUE_JAVASCRIPTS_FILENAMES = "";
	protected $CUSTOM_PHP_VALIDATE_FILENAME = "";
	protected $CUSTOM_PHP_SAVE_FILENAME = "";
	protected $CUSTOM_PHP_AFTERSAVE_FILENAME = "";

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
			"conditions[in][parameter_key]" => 'module_access_code,module_action_add_code,module_action_edit_code,module_action_delete_code,module_action_print_code',
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
			"tableName" => "_masterfiletemplates",
			"dbconnect" => MONEYTRACKER_DB,
			"columns" => "code,access_key,program_name,masterfile_name,masterfile_description,masterfile_type,".
						"database_name,table_name,primary_code_field_names,menu_item_mst_codes,module_mst_code,".
						"search_by_fields,show_record_fields,span_size,".
						"unique_display_filename,unique_javascripts_filenames,custom_php_validate_filename,".
						"custom_php_save_filename,custom_php_aftersave_filename,special_conditions",
			"conditions[equals][access_key]" => base64_encode($this->params[0]."/".$this->params[1])
		);
		$result=processCurl($link,$params);
		$output = json_decode($result,true);
		if($output[0]["result"]==='1'){
			foreach ($output as $key => $value){
				$this->MASTERFILE_TEMPLATE_CODE = $value["code"];

				$this->ACCESS_KEY = $value["access_key"];
				$this->PROGRAM_NAME = $value["program_name"];
				$this->NAME = $value["masterfile_name"];
				$this->DESCRIPTION = $value["masterfile_description"];
				$this->TYPE = $value["masterfile_type"];

				$this->PAGE_SETTINGS["menu_program_name"] = $this->PROGRAM_NAME;
				$this->PAGE_SETTINGS["PageTitle"] = $this->NAME;

				$this->DATABASE_NAME = $value["database_name"];
				$this->TABLE_NAME = $value["table_name"];

				$this->PRIMARY_CODE_FIELD_NAMES = $value["primary_code_field_names"];
				$this->MENU_ITEM_MST_CODES = $value["menu_item_mst_codes"];
				$this->MODULE_MST_CODE = $value["module_mst_code"];


				$this->SEARCH_BY_FIELDS = $value["search_by_fields"];
				$this->SHOW_RECORD_FIELDS = $value["show_record_fields"];
				//$this->WHOLE_MASTERFILE_ACTIONS = $value["whole_masterfile_actions"];
				//$this->PER_RECORD_ACTIONS = $value["per_record_actions"];

				$this->SPECIAL_CONDITIONS = $value["special_conditions"];

				$this->SPAN_SIZE = $value["span_size"];

				$this->UNIQUE_DISPLAY_FILENAME = $value["unique_display_filename"];
				$this->UNIQUE_JAVASCRIPTS_FILENAMES = $value["unique_javascripts_filenames"];
				$this->CUSTOM_PHP_VALIDATE_FILENAME = $value["custom_php_validate_filename"];
				$this->CUSTOM_PHP_SAVE_FILENAME = $value["custom_php_save_filename"];
				$this->CUSTOM_PHP_AFTERSAVE_FILENAME = $value["custom_php_aftersave_filename"];

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

				
				if ($this->TYPE!="3"){
					/* get filters if meron */
					$link = DB_LOCATION;
					$params = array (
						"action" => "retrieve-template-filters",
						"fileToOpen" => "default_select_query",
						"tableName" => "_dtlmasterfiletemplatesfilters",
						"dbconnect" => MONEYTRACKER_DB,
						"columns" => "code,master_file_templates_code,filter_name,table_field_name,filter_caption,data_type,".
									"max_length,data_source,data_source_database_name,data_source_table_name,data_source_value_pair,special_conditions,print_order",
						"conditions[equals][master_file_templates_code]" => $this->MASTERFILE_TEMPLATE_CODE,
						"orderby" => "print_order ASC"
					);
					$result=processCurl($link,$params);
					$output = json_decode($result,true);
					$ctr = 0;
					if($output[0]["result"]==='1'){
						foreach ($output as $key => $value){
							$this->FILTER_DATA[$ctr] = array();
							$this->FILTER_DATA[$ctr]["master_file_templates_code"] = $value["master_file_templates_code"];

							$this->FILTER_DATA[$ctr]["code"] = $value["code"];
							$this->FILTER_DATA[$ctr]["filter_name"] = $value["filter_name"];
							$this->FILTER_DATA[$ctr]["table_field_name"] = $value["table_field_name"];
							$this->FILTER_DATA[$ctr]["filter_caption"] = $value["filter_caption"];
							$this->FILTER_DATA[$ctr]["data_type"] = $value["data_type"];
							$this->FILTER_DATA[$ctr]["max_length"] = $value["max_length"];
							$this->FILTER_DATA[$ctr]["data_source"] = $value["data_source"];
							$this->FILTER_DATA[$ctr]["data_source_table_name"] = $value["data_source_table_name"];
							$this->FILTER_DATA[$ctr]["data_source_database_name"] = $value["data_source_database_name"];
							$this->FILTER_DATA[$ctr]["data_source_value_pair"] = $value["data_source_value_pair"];
							$this->FILTER_DATA[$ctr]["special_conditions"] = $value["special_conditions"];
							$this->FILTER_DATA[$ctr]["print_order"] = $value["print_order"];

							$ctr++;
						}
					}
					else {
						/* no filters specified, no need to error. di naman required */
					}
					$this->FILTER_COUNT = $ctr;



					/* get columns if meron */
					$link = DB_LOCATION;
					$params = array (
						"action" => "retrieve-template-columns",
						"fileToOpen" => "default_select_query",
						"tableName" => "_dtlmasterfiletemplatescolumns",
						"dbconnect" => MONEYTRACKER_DB,
						"columns" => "code,master_file_templates_code,field_name,table_field_name,field_header_caption,data_type,".
									"can_display_on_table,can_show,can_add,can_edit,is_required,is_unique,print_order,alignment,".
									"max_length,data_source,data_source_database_name,data_source_table_name,data_source_table_alias,data_source_field_alias,data_source_value_pair,special_conditions",
						"conditions[equals][master_file_templates_code]" => $this->MASTERFILE_TEMPLATE_CODE,
						"orderby" => "print_order ASC"
					);
					$result=processCurl($link,$params);
					$output = json_decode($result,true);
					$ctr = 0;
					if($output[0]["result"]==='1'){
						foreach ($output as $key => $value){
							$this->COLUMN_DATA[$ctr] = array();
							$this->COLUMN_DATA[$ctr]["master_file_templates_code"] = $value["master_file_templates_code"];

							$this->COLUMN_DATA[$ctr]["code"] = $value["code"];
							$this->COLUMN_DATA[$ctr]["field_name"] = $value["field_name"];
							$this->COLUMN_DATA[$ctr]["table_field_name"] = $value["table_field_name"];
							$this->COLUMN_DATA[$ctr]["field_header_caption"] = $value["field_header_caption"];

							$this->COLUMN_DATA[$ctr]["can_display_on_table"] = $value["can_display_on_table"];
							$this->COLUMN_DATA[$ctr]["can_show"] = $value["can_show"];
							$this->COLUMN_DATA[$ctr]["can_add"] = $value["can_add"];
							$this->COLUMN_DATA[$ctr]["can_edit"] = $value["can_edit"];
							$this->COLUMN_DATA[$ctr]["is_required"] = $value["is_required"];
							$this->COLUMN_DATA[$ctr]["is_unique"] = $value["is_unique"];

							$this->COLUMN_DATA[$ctr]["alignment"] = $value["alignment"];
							$this->COLUMN_DATA[$ctr]["data_type"] = $value["data_type"];
							$this->COLUMN_DATA[$ctr]["max_length"] = $value["max_length"];
							$this->COLUMN_DATA[$ctr]["data_source"] = $value["data_source"];
							$this->COLUMN_DATA[$ctr]["data_source_database_name"] = $value["data_source_database_name"];
							$this->COLUMN_DATA[$ctr]["data_source_table_name"] = $value["data_source_table_name"];
							$this->COLUMN_DATA[$ctr]["data_source_table_alias"] = $value["data_source_table_alias"];
							$this->COLUMN_DATA[$ctr]["data_source_field_alias"] = $value["data_source_field_alias"];
							$this->COLUMN_DATA[$ctr]["data_source_value_pair"] = $value["data_source_value_pair"];
							$this->COLUMN_DATA[$ctr]["special_conditions"] = $value["special_conditions"];
							$this->COLUMN_DATA[$ctr]["print_order"] = $value["print_order"];

							if ($value["can_display_on_table"]=="1"){
								$this->COLUMN_TABLE_DISPLAY_COUNT++;
								$this->HAS_PRINT=true;



							}

							$this->HAS_DELETE = true;
							if ($value["can_show"]=="1"||$value["can_add"]=="1"|| $value["can_edit"]=="1"){
								$this->AT_LEAST_ONE_RECORD_ACTION = true;
							}

							if ($value["can_show"]=="1"){
								$this->HAS_SHOW=true;
							}

							if ($value["can_add"]=="1"){
								$this->HAS_ADD=true;
							}


							if ($value["can_edit"]=="1"){
								$this->HAS_EDIT=true;
							}




							$ctr++;
						}
					}
					else {
						/* no columns specified, no need to error. di naman required */
					}
					$this->COLUMN_COUNT = $ctr;



					/* get ACTIONS if meron */
					$link = DB_LOCATION;
					$params = array (
						"action" => "retrieve-template-columns",
						"fileToOpen" => "default_select_query",
						"tableName" => "_dtlmasterfiletemplatesactions",
						"dbconnect" => MONEYTRACKER_DB,
						"columns" => "code,master_file_templates_code,action_type,action_name,action_shortname,action_id_prefix,module_action_mst_code,".
									"custom_php_module_include_filename,custom_php_module_inline_js_filenames,print_order",
						"conditions[equals][master_file_templates_code]" => $this->MASTERFILE_TEMPLATE_CODE,
						"orderby" => "print_order ASC"
					);
					$result=processCurl($link,$params);
					$output = json_decode($result,true);
					$ctr = 0;
					if($output[0]["result"]==='1'){
						foreach ($output as $key => $value){
							$this->ACTION_DATA[$ctr] = array();
							$this->ACTION_DATA[$ctr]["master_file_templates_code"] = $value["master_file_templates_code"];

							$this->ACTION_DATA[$ctr]["code"] = $value["code"];
							$this->ACTION_DATA[$ctr]["action_type"] = $value["action_type"];
							$this->ACTION_DATA[$ctr]["action_name"] = $value["action_name"];
							$this->ACTION_DATA[$ctr]["action_shortname"] = $value["action_shortname"];
							$this->ACTION_DATA[$ctr]["action_id_prefix"] = $value["action_id_prefix"];

							$this->ACTION_DATA[$ctr]["module_action_mst_code"] = $value["module_action_mst_code"];
							$this->ACTION_DATA[$ctr]["custom_php_module_include_filename"] = $value["custom_php_module_include_filename"];
							$this->ACTION_DATA[$ctr]["custom_php_module_inline_js_filenames"] = $value["custom_php_module_inline_js_filenames"];
							$this->ACTION_DATA[$ctr]["print_order"] = $value["print_order"];

							// check for privilege specified via $this->ACTION_DATA[$ctr]["module_action_mst_code"]
							if (!array_key_exists($this->ACTION_DATA[$ctr]["module_action_mst_code"], $this->PRIVILEGE_SET)||
								$this->PRIVILEGE_SET[$this->ACTION_DATA[$ctr]["module_action_mst_code"]]=="2"){
								$this->ACTION_DATA[$ctr]["access_privilege"] = false ;
							} // if (!array_key_exists($this->ACTION_DATA[$ctr]["module_action_mst_code"], $this->PRIVILEGE_SET)||
							else {
								$this->ACTION_DATA[$ctr]["access_privilege"] = true ;
							} // ELSE ng if (!array_key_exists($this->ACTION_DATA[$ctr]["module_action_mst_code"], $this->PRIVILEGE_SET)||
							


							$ctr++;
						} // foreach ($output as $key => $value){
					} // if($output[0]["result"]==='1'){
					else {
						/* no columns specified, no need to error. di naman required */
					} // ELSE ng if($output[0]["result"]==='1'){
					$this->ACTION_COUNT = $ctr;



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
				} // if ($this->TYPE!="3"){

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

		$this->displayCustomScripts();
		$this->displayScripts();
		$this->displayUniqueJavascripts();

		echo '</body>';
		echo '</html>';
		
	} //public function setupPage(){

	// DISPLAY CUSTOM SCRIPTS HERE
	private function displayCustomScripts(){
		
			echo '<script type="text/javascript">';
			echo 'var ABSOLUTE_PATH = "'.ABSOLUTE_PATH.'";';
			echo 'var CUSTOM_VALIDATION = "' . $this->CUSTOM_PHP_VALIDATE_FILENAME . '";' ;
			echo 'var CUSTOM_SAVE = "' . $this->CUSTOM_PHP_SAVE_FILENAME . '";' ;
			echo 'var CUSTOM_AFTERSAVE = "' . $this->CUSTOM_PHP_AFTERSAVE_FILENAME . '";' ;
			echo '</script>'; 
		
	} // private function displayCustomScripts(){

	// DISPLAY UNIQUE JAVASCRIPT
	private function displayUniqueJavascripts(){
		if ($this->UNIQUE_JAVASCRIPTS_FILENAMES!=""&&$this->UNIQUE_JAVASCRIPTS_FILENAMES!=null) {
			//realpath($this->UNIQUE_DISPLAY_FILENAME)!=false
			//include_once($this->UNIQUE_DISPLAY_FILENAME);
			// echo '<script src="../../resources/js/masterfilesfunctions.min.js"></script>';
			//echo $this->UNIQUE_JAVASCRIPTS_FILENAMES;
			$javascripts_location = explode("|",$this->UNIQUE_JAVASCRIPTS_FILENAMES);
			foreach ($javascripts_location as $js){
				if ($js!=""){
					echo '<script src="'.$js.VERSION_AFFIX.'" type="text/javascript"></script>'; 
				} // if ($js!=""&&realpath($js)!=false){
			} // foreach ($javascripts_location as $js){
		} // if ($this->UNIQUE_DISPLAY_FILENAME!="") {
		echo '<script src="../../resources/js/removeblocker.min.js'.VERSION_AFFIX.'"></script>';
	} // private function displayUniqueBody($status=""){

	// DISPLAY MAIN BODY FOR UNIQUE MASTER FILE
	private function displayUniqueBody($status=""){

		if ($this->UNIQUE_DISPLAY_FILENAME!="" && realpath($this->UNIQUE_DISPLAY_FILENAME)!=false) {
			include_once($this->UNIQUE_DISPLAY_FILENAME);
			echo '<div id="dialog-modal-wrapper" style="display:none"></div>';
			echo '<div id="modal-wrapper" style="display:none"></div>';
		} // if ($this->UNIQUE_DISPLAY_FILENAME!="" && realpath($this->UNIQUE_DISPLAY_FILENAME)!=false) {
		else {
			$this->displayErrors("error3");
		} // ELSE ng if ($this->UNIQUE_DISPLAY_FILENAME!="" && realpath($this->UNIQUE_DISPLAY_FILENAME)!=false) {
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
	        				if ($this->TYPE=="1"){
		            			$this->indexedDisplay();
		            		} //if ($this->TYPE=="1"){
		            		else if ($this->TYPE=="2"){
		            			$this->singleDisplay();
		            		} //else if ($this->TYPE=="2"){
	            			else if ($this->TYPE="3"){
	            				$this->displayUniqueBody();
	            			} // else if ($this->TYPE="3"){
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

	// DISPLAY MAIN BODY - INDEXED
	private function indexedDisplay(){
		$this->displayFilters();
		
		$this->displayIndexTable();

		echo '<div id="modal-wrapper" style="display:none"></div>';
		echo '<div id="dialog-modal-wrapper" style="display:none"></div>';
		
		
		
		
	} //private function indexedDisplay(){

	// DISPLAY MAIN BODY - SINGLE
	private function singleDisplay(){

	} //private function singleDisplay(){

	/* display pagination and actions before & after table */
	private function displayPaginationAndActions(){
	?>
		<div class="row" style="margin-bottom:5px">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:right" >
				<?php if ($this->HAS_ADD_PRIVILEGE==true&&$this->HAS_ADD==true){
				?>
					<button type="button" class="btn btn-primary btn-sm outer_actions add_action"><i class="<?php echo $this->BUTTON_ICONS["Add"];?>"></i> Add Record</button>
				<?php
				} // if ($this->HAS_ADD_PRIVILEGE==true&&$this->HAS_ADD==true){
				else if ($this->HAS_ADD_PRIVILEGE==false&&$this->HAS_ADD==true){ 
				?>	
					<div style="display:inline-block" title="You have no Add privilege. Please contact system administrator">
					<button class="btn btn-primary btn-sm outer_actions no_privilege"  disabled type="button"><i class="<?php echo $this->BUTTON_ICONS["Add"];?>"></i> Add Record</button>
					</div>
					
				<?php
				} // if ($this->HAS_ADD_PRIVILEGE==false&&$this->HAS_ADD==true){  ?>
				

				<?php if ($this->HAS_PRINT_PRIVILEGE==true&&$this->HAS_PRINT==true){
				?>
					<button type="button" class="btn btn-default btn-sm outer_actions print_action"><i class="<?php echo $this->BUTTON_ICONS["Print"];?>"></i> Print Listing</button>
				<?php
				} // if ($this->HAS_PRINT_PRIVILEGE==true&&$this->HAS_PRINT==true){
				else if ($this->HAS_PRINT_PRIVILEGE==false&&$this->HAS_PRINT==true){ 
				?>	
					<div style="display:inline-block" title="You have no Print privilege. Please contact system administrator">
					<button class="btn btn-default btn-sm outer_actions no_privilege"  disabled type="button"><i class="<?php echo $this->BUTTON_ICONS["Print"];?>"></i> Print Listing</button>
					</div>
					
				<?php
				} // ELSE ng if ($this->HAS_PRINT_PRIVILEGE==false&&$this->HAS_PRINT=?>
				
			
	<?php
		echo $this->renderCustomActions("1");
	?>
			</div>
		</div>
	<?php
	} //private function displayPaginationAndActions(){

	/* display indexed type table - separated para madali hanapin */
	private function displayIndexTable(){

		/* loop columns para malaman yung anu-ano */
	?>
		<h3 class="secondary-header">Records:</h3>
		<?php $this->displayPaginationAndActions();	?>
		<div class="row">
                <div class="col-lg-12">
		
				<div class="table-responsive">
					<table class="table table-bordered table-condensed table-hover table-large-font" id="indexedTable" style="display:none">
						<thead>
							<tr>
								<?php
								$a = 0;
								foreach ($this->COLUMN_DATA as $columnEntry){
									if ($columnEntry["can_display_on_table"]=="1") {

										if ($columnEntry["data_source"]!="0"){
											// $this->UNSEARCHABLE_COLUMNS = $this->UNSEARCHABLE_COLUMNS . "$a,";
										}
								?>
									<th class="table-header <?php echo $columnEntry["alignment"];?>" 
										<?php echo (($columnEntry["data_source"]=="1")?'data-isfromotherdata="true"':'data-isfromotherdata="false"') .' data-tablefieldname="'.$columnEntry["table_field_name"].'"'; ?>
										<?php echo ($columnEntry["field_header_caption"]!="")?"title=\"".$columnEntry["field_header_caption"]."\" data-placement=\"top\"":""; ?>
									>
										<?php echo $columnEntry["field_name"]; ?>
									</th>
								<?php
										$a++;
									} // if ($columnEntry["can_display_on_table"]=="1") {
									
								} // foreach ($this->COLUMN_DATA as $columnEntry){ style="width:300px" t
								$this->UNSEARCHABLE_COLUMNS = rtrim($this->UNSEARCHABLE_COLUMNS,",");
								?>

								<?php if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
								?>
									<th class="center table-header" title="Actions" >Actions</th>
								<?php	
								} // if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
								?>
								
							</tr>
						</thead>
					</table>
				</div>
		
		</div>
		</div>
	    <?php $this->displayPaginationAndActions();	?>

	<?php
	} //private function displayIndexTable(){

	/* display filters - separated para madali hanapin */
	private function displayFilters(){
		$classcustomformat="";
		$datacustomformat="";
		$datanumberofdec="";

		if (count($this->FILTER_DATA)>0){
			//<div style="float:right;margin-top:-3px"><button class="btn btn-default btn-xs"><i class="fa fa-caret-square-o-down"></i> Show Filters</button></div>
			

			?>
			<div class="panel panel-default">
			  <div class="panel-heading">
			  <button type="button" class="toggle_filter close " data-state="show" title="Hide Filter"><i  class="botton-icon-display fa fa-chevron-down" ></i><i  class="botton-icon-display fa fa-chevron-up " style="display:none" ></i></button>
			   
			    <h2 class="panel-title">Filters</h2>
			  </div>
			  <div class="panel-body" style="display:none">
			    <div class="row">
		    <?php

			// loop EACH filter to display each filter data
			foreach ($this->FILTER_DATA as $filter_key => $filter_value){
				?>
				<?php
				if ($filter_value["data_type"] == "10"){
				?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" <?php echo ($filter_value["filter_caption"]!=="")?'title="'.$filter_value["filter_caption"].'"':'';?> >
				<?php
				} /* if ($filter_value["data_type"] == "10"){ */
				else
				{
				?>
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" <?php echo ($filter_value["filter_caption"]!=="")?'title="'.$filter_value["filter_caption"].'"':'';?> >
				<?php
				} /* if ($filter_value["data_type"] == "10"){ ELSE */

				?>
				<span class="form-input-title"><?php echo $filter_value["filter_name"]; ?></span>
				<?php 
				switch ($filter_value["data_type"]){
					//1 - Character (one line)
					//2 - Integer
					//3 - Decimal
					//4 - Date
					case "1":
					case "2":
					case "3":
					case "4":   

					?>
					
					<?php if ($filter_value["special_conditions"]!=""){
                            
                                        $explodedSpecCon = explode("|",$filter_value["special_conditions"]);
                                        foreach ($explodedSpecCon as $spccon){
                                            $explodedPerItemSpecCon = explode("=",$spccon);
                                            
                                            if (($explodedPerItemSpecCon["0"]=="customformat")&&($explodedPerItemSpecCon["1"]=="yes")){                                                    
                                                $classcustomformat = "customformat";                                                                                                            
                                            }
                                            else if(($explodedPerItemSpecCon["0"]=="format")&&($classcustomformat=="customformat"))
                                            {
                                                $datacustomformat = $explodedPerItemSpecCon["1"];        
                                            }
                                            else if (($explodedPerItemSpecCon["0"]=="usesformat")&&($explodedPerItemSpecCon["1"]=="yes")){                                                    
                                                $classcustomformat = "usesformat";                                                                                                            
                                            }
                                            else if(($explodedPerItemSpecCon["0"]=="format")&&($classcustomformat=="usesformat"))
                                            {
                                                $datanumberofdec = $explodedPerItemSpecCon["1"];        
                                            }

                                        }
                                    }

                                    ?>
                                   
				    		<input type="text" placeholder="<?php echo $filter_value["filter_name"]; ?>"
				    			<?php if ($datanumberofdec!=""){echo 'data-numberofdec=' . '"' . $datanumberofdec . '"' ;} ?>  data-format="<?php if ($datacustomformat!=""){echo $datacustomformat;} ?>" 
				    			 class="form-control input-sm filter-fields outer_actions <?php if($classcustomformat!=""){ echo $classcustomformat;} ?> 
				    			 	<?php if ($filter_value["data_type"]=='2' && $classcustomformat=="") { echo 'integerFormat'; }
				    			 		else if ($filter_value["data_type"]=='3') { echo 'decimalFormat'; }
				    			 		else if ($filter_value["data_type"]=='4') { echo 'input-group date'; } ?>"
				    			 		<?php echo ($filter_value["max_length"]!==""&&$filter_value["max_length"]!="0"&&$filter_value["data_type"]!="4")?'maxlength="'.$filter_value["max_length"].'"':'';?> 
				    			value="<?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
				    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]!=''){
												echo $_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]];
	    									} ?>" 
				    			id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
				    			name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>" />
					<?php
					break;


					//5 - Character (Paragraph)
					//6 - Character (Text Editor)
					case "5":
					case "6":
					?>
				    		<textarea placeholder="<?php echo $filter_value["filter_name"]; ?>"
				    			id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
				    			name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
				    			 class="form-control input-sm filter-fields  outer_actions hehe <?php if ($filter_value["data_type"]=='6') { echo 'wysiwyg-editor'; }?>"
	    			 			<?php echo ($filter_value["max_length"]!==""&&$filter_value["max_length"]!="0"&&$filter_value["data_type"]!="4")?'maxlength="'.$filter_value["max_length"].'"':'';?> 
				    			 ><?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
				    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]!=''){
												echo $_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]];
	    									} ?></textarea>
					<?php
					break;



					//7 - Combo Box
					case "7":
						// FROM STATIC DATA SOURCE
						if ($filter_value["data_source"]=="1"){
							if ($filter_value["data_source_value_pair"]!=""){
								$value_pairs = explode("|",$filter_value["data_source_value_pair"]);
								
								?>
									<select class="form-control input-sm filter-fields  outer_actions "
						    			id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
						    			name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>">
					    			<option value="">- Select <?php echo $filter_value["filter_name"]; ?> -</option>
								<?php
									if (count($value_pairs)>0){
										foreach ($value_pairs as $pair_key => $pair_value) {
											$static_values = explode(":",$pair_value);
										?>
											<option value="<?php echo $static_values["0"]?>"
												<?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
					    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]==$static_values['0']){
													echo ' selected ';
		    									} ?>
											><?php echo $static_values["1"];?></option>
										<?php
										} //foreach ($value_pairs as $pair_key => $pair_value) {
									} //if (count($value_pairs)>0){
								?>
									</select>
								<?php
								
							} //if ($filter_value["data_source_value_pair"]!=""){
						} //if ($filter_value["data_source"]=="1"){
						// FROM DATABASE SOURCE
						else if ($filter_value["data_source"]=="2"){
							if ($filter_value["data_source_value_pair"]!=""){

								$value_pairs = explode(":",$filter_value["data_source_value_pair"]);
								$value_pairs_calamity = explode(" ", $value_pairs["1"]);

                                $custom_file_to_open="default_select_query";
								$chainingparentid="";
								$chainingparentfield="";
								$conditions = array();
                                $conditions_fieldname = array();
                                $conditions_fieldvalue = array();
								if ($filter_value["special_conditions"]!=""){
									$explodedSpecCon = explode("|",$filter_value["special_conditions"]);
									foreach ($explodedSpecCon as $spccon){
										$explodedPerItemSpecCon = explode("=",$spccon);
										
										if ($explodedPerItemSpecCon["0"]=="chainingparentid"){
											$chainingparentid = $explodedPerItemSpecCon["1"];
										}
										else if ($explodedPerItemSpecCon["0"]=="chainingparentfield"){
											$chainingparentfield = $explodedPerItemSpecCon["1"];
										}

										else if ($explodedPerItemSpecCon["0"]=="condition"){
                                            array_push($conditions,$explodedPerItemSpecCon["1"]);
                                        }

                                        else if ($explodedPerItemSpecCon["0"]=="condition_source_table_field"){
                                            array_push($conditions_fieldname,$explodedPerItemSpecCon["1"]);
                                        }

                                        else if ($explodedPerItemSpecCon["0"]=="condition_source_table_field_value"){
                                            array_push($conditions_fieldvalue,$explodedPerItemSpecCon["1"]);
                                        }

				                        else if ($explodedPerItemSpecCon["0"]=="custom_file_to_open"){
                                                $custom_file_to_open=str_replace("$","/",$explodedPerItemSpecCon["1"]);
                                        }
										
									}
								}
								if ($chainingparentid=="") {
					    				 	$link = DB_LOCATION;
								$params = array (
									"action" => "retrieve",
									"fileToOpen" => $custom_file_to_open,
									"tableName" => $filter_value["data_source_table_name"],
									"dbconnect" => $filter_value["data_source_database_name"],
									"columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']).(($chainingparentfield!="")?",".$chainingparentfield:"") ,
									"orderby" => $value_pairs['0']." ASC"
								);
								foreach ($conditions as $lch_key => $lch_value){
                                    $params["conditions[".$lch_value."][".$conditions_fieldname[$lch_key]."]"] = $conditions_fieldvalue[$lch_key];
                                } // foreach ($conditions as $lch_key => $lch_value){
								$result=processCurl($link,$params);
								$output = json_decode($result,true);
								$ctr = 0;
								
								?>
									<select class="form-control input-sm filter-fields  outer_actions " <?php if ($chainingparentid!=""){echo 'data-chainingparentid="'.$chainingparentid.'"';} ?> <?php if ($chainingparentfield!=""){echo ' data-chainingparentfield="'.$chainingparentfield.'"';} ?> 
							    			id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
							    			data-remotechain="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
							    			name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>">
					    			<option value="">- Select <?php echo $filter_value["filter_name"]; ?> -</option>
				    			<?php
				    				if($output[0]["result"]==='1'){
										foreach ($output as $data_source_key => $data_source_value){
								?>						
											<option value="<?php echo $data_source_value[$value_pairs['0']]?>" <?php if ($chainingparentfield!=""){echo 'class="'.$data_source_value[$chainingparentfield].'"';} ?>
													<?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
						    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]==$data_source_value[$value_pairs['0']]){
														echo ' selected ';
			    									} ?>
												><?php 
                                                        $valuepair1 = explode(" ",$value_pairs['1']);
                                                        $lch_todisplay = "";
                                                        foreach ($valuepair1 as $vp1){
                                                            $lch_todisplay = $lch_todisplay . $data_source_value[$vp1]." - ";
                                                        }
                                                        $lch_todisplay = rtrim($lch_todisplay," - ");
                                                        echo $lch_todisplay;
                                                       	
                                                    ?></option>
								<?php
										} //foreach ($output as $key => $value){
									} //if($output[0]["result"]==='1'){
								?>
								}
									</select>
									
								<?php 
					    				 } //if ($chainingparentid=="") {
					    				 else{
					    				 	?>
											<select class="form-control input-sm filter-fields  outer_actions " <?php if ($chainingparentid!=""){echo 'data-chainingparentid="'.$chainingparentid.'"';} ?> <?php if ($chainingparentfield!=""){echo ' data-chainingparentfield="'.$chainingparentfield.'"';} ?> 
										    			data-sourcetb="<?php echo $filter_value["data_source_table_name"]; ?>"
										    			disabled
										    			data-sourcedb="<?php echo $filter_value["data_source_database_name"]; ?>"
										    			data-sourcevaluepair="<?php echo $filter_value["data_source_value_pair"]; ?>"
										    			data-remotechain="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
										    			title="<?php echo $filter_value["filter_name"]; ?>"
										    			id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
										    			name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>">
								    			<option value="">- Select <?php echo $filter_value["filter_name"]; ?> -</option>
								    				</select>
					    					<?php

					    				 } // else -- if ($chainingparentid=="") {
								
								
								
							} //if ($filter_value["data_source_value_pair"]!=""){
						} //else if ($filter_value["data_source"]=="2"){

					break;



					//8 - Radio Button
					case "8":
						// FROM STATIC DATA SOURCE
						if ($filter_value["data_source"]=="1"){
							$value_pairs = explode("|",$filter_value["data_source_value_pair"]);
							if (count($value_pairs)>0){
							?>
								<div class="radio-group">
							<?php
								foreach ($value_pairs as $pair_key => $pair_value) {
									$static_values = explode(":",$pair_value);
								?>
									<label class="radio-inline">
									  <input type="radio" 
									    <?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
			    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]==$static_values['0']){
											echo ' checked ';
    									} ?>
									  	class=" outer_actions  filter-fields <?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
									  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>" 
									  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-'.$static_values["0"]; ?>" 
									  	value="<?php echo $static_values["0"];?>"> <?php echo $static_values["1"];?>
									</label>
								<?php
								} //foreach ($value_pairs as $pair_key => $pair_value) {
							?>
								</div> <!-- radio group -->
							<?php
							} //if (count($value_pairs)>0){
						} //if ($filter_value["data_source"]=="1"){
						// FROM DATABASE SOURCE
						else if ($filter_value["data_source"]=="2"){
							if ($filter_value["data_source_value_pair"]!=""){
								$value_pairs = explode(":",$filter_value["data_source_value_pair"]);
								$link = DB_LOCATION;
								$params = array (
									"action" => "retrieve",
									"fileToOpen" => "default_select_query",
									"tableName" => $filter_value["data_source_table_name"],
									"dbconnect" => $filter_value["data_source_database_name"],
									"columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']) ,
									"orderby" => $value_pairs['0']." ASC"
								);
								$result=processCurl($link,$params);
								$output = json_decode($result,true);
								$ctr = 0;
								if($output[0]["result"]==='1'){
								?>
									<div class="radio-group">
				    			<?php
									foreach ($output as $data_source_key => $data_source_value){
							?>						
										
										<label class="radio-inline">
										  <input type="radio" 
										    <?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
				    								$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]]==$data_source_value[$value_pairs['0']]){
												echo ' checked ';
	    									} ?>
										  	class=" outer_actions  filter-fields <?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
										  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>" 
										  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-'.$data_source_value[$value_pairs['0']]; ?>" 
										  	value="<?php echo $data_source_value[$value_pairs['0']];?>"><?php 
                                                        $valuepair1 = explode(" ",$value_pairs['1']);
                                                        foreach ($valuepair1 as $vp1){
                                                            echo $data_source_value[$vp1]." ";
                                                        }
                                                    ?>
										</label>

							<?php
									} //foreach ($output as $key => $value){
								?>
									</div> <!-- radio group -->
								<?php
								} //if($output[0]["result"]==='1'){
							} //if ($filter_value["data_source_value_pair"]!=""){
						} //else if ($filter_value["data_source"]=="2"){
					break;



					//9 - Checkbox
					case "9":
						// FROM STATIC DATA SOURCE
						if ($filter_value["data_source"]=="1"){
							$value_pairs = explode("|",$filter_value["data_source_value_pair"]);
							if (count($value_pairs)>0){
							?>
								<div class="checkbox-group">
									<label class="checkbox-inline">
									  <input type="checkbox" 
									  	data-children="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
									  	class=" outer_actions  filter-fields parent-checkboxes"
									  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'[]'; ?>" 
									  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-parent'; ?>" 
									  	value=""> All
									</label>
							<?php
								foreach ($value_pairs as $pair_key => $pair_value) {
									$static_values = explode(":",$pair_value);
								?>
									<label class="checkbox-inline">
									  <input type="checkbox" 
									    <?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
			    								in_array($static_values['0'],$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])){
											echo ' checked ';
    									} ?>
    									data-parent="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-parent'; ?>"
									  	class=" outer_actions  filter-fields child-checkboxes <?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
									  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'[]'; ?>" 
									  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-'.$static_values["0"]; ?>" 
									  	value="<?php echo $static_values["0"];?>"> <?php echo $static_values["1"];?>
									</label>
								<?php
								} //foreach ($value_pairs as $pair_key => $pair_value) {
							?>
								</div> <!-- checkbox group -->
							<?php
							} //if (count($value_pairs)>0){
						} //if ($filter_value["data_source"]=="1"){
						// FROM DATABASE SOURCE
						else if ($filter_value["data_source"]=="2"){
							if ($filter_value["data_source_value_pair"]!=""){
								$value_pairs = explode(":",$filter_value["data_source_value_pair"]);
								$link = DB_LOCATION;
								$params = array (
									"action" => "retrieve",
									"fileToOpen" => "default_select_query",
									"tableName" => $filter_value["data_source_table_name"],
									"dbconnect" => $filter_value["data_source_database_name"],
									"columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']) ,
									"orderby" => $value_pairs['0']." ASC"
								);
								$result=processCurl($link,$params);
								$output = json_decode($result,true);
								$ctr = 0;
								if($output[0]["result"]==='1'){
								?>
									<div class="checkbox-group">
										<label class="checkbox-inline">
										  <input type="checkbox" 
										  	data-children="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
										  	class=" outer_actions  filter-fields parent-checkboxes"
										  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'[]'; ?>" 
										  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-parent'; ?>" 
										  	value=""> All
										</label>
				    			<?php
									foreach ($output as $data_source_key => $data_source_value){
							?>						
										<label class="checkbox-inline">
										  <input type="checkbox" 
										    <?php if(isset($_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])&&
				    								in_array($data_source_value[$value_pairs['0']],$_POST['filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]])){
												echo ' checked ';
	    									} ?>
	    									data-parent="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-parent'; ?>"
										  	class=" outer_actions  filter-fields child-checkboxes <?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"]; ?>"
										  	name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'[]'; ?>" 
										  	id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'-'.$data_source_value[$value_pairs['0']]; ?>" 
										  	value="<?php echo $data_source_value[$value_pairs['0']];?>"> <?php 
                                                        $valuepair1 = explode(" ",$value_pairs['1']);
                                                        foreach ($valuepair1 as $vp1){
                                                            echo $data_source_value[$vp1]." ";
                                                        }
                                                    ?>
										</label>
									
							<?php
									} //foreach ($output as $key => $value){
								?>
									</div> <!-- checkbox group -->
								<?php
								} //if($output[0]["result"]==='1'){
							} //if ($filter_value["data_source_value_pair"]!=""){
						} //else if ($filter_value["data_source"]=="2"){
					break;

					//10 - lookup
			
					case "10":
                            	
                            // using type ahead
                            $remotelink = "";
                            $templatefileloc = "";
                            $templateItself = "";
                            if ($filter_value["special_conditions"]!=""){
                                $explodedSpecCon = explode("|",$filter_value["special_conditions"]);
                                foreach ($explodedSpecCon as $spccon){
                                    $explodedPerItemSpecCon = explode("=",$spccon);
                                    
                                    if ($explodedPerItemSpecCon["0"]=="remotelink"){
                                        $remotelink = $explodedPerItemSpecCon["1"];
                                    }
                                    else if ($explodedPerItemSpecCon["0"]=="templatefileloc"){
                                        $templatefileloc = $explodedPerItemSpecCon["1"];
                                        $templateItself = ($templatefileloc!="")?file_get_contents(ABSOLUTE_PATH.$templatefileloc):"";
                                        $templateItself = str_replace("__ABSOLUTE_PATH__", ABSOLUTE_PATH, $templateItself );
                                    }
                                    
                                } // foreach ($explodedSpecCon as $spccon){
                            } // if ($columnsSpecialConditions[$column_key]!=""){

                            $value_pairs = explode(":",$filter_value["data_source_value_pair"]); // explode(":",$columnsDatasourceValuePair[$column_key]);
                            $value_pairs_disp = explode(" ",$value_pairs["1"]);
                                
                            $firstVal = "";
                            $secondVal = "";
                            //if (($retrievedRecordRow[0][$column_name]!="0"&&$retrievedRecordRow[0][$column_name]!="")){
                              {  // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                                $link = DB_LOCATION;
                                $params = array (
                                    "action" => "retrieve",
                                    "fileToOpen" => "default_select_query",
                                    "tableName" => $filter_value["data_source_table_name"],
									"dbconnect" => $filter_value["data_source_database_name"],
									"columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']) ,
									"orderby" => $value_pairs['0']." ASC"
                                    //"conditions[equals][".$value_pairs['0']."]" => $retrievedRecordRow[0][$column_name]
                                );
                            
                                $result=processCurl($link,$params);
                                $output2 = json_decode($result,true);

                                if($output2[0]["result"]==='1'){
                                    foreach ($output2 as $data_source_key => $data_source_value){
                                        $firstVal = $data_source_value[$value_pairs_disp[0]];
                                        $secondVal = $data_source_value[$value_pairs_disp[1]];
                                    }

                                }
                            } 
                        
                        
                        ?>
					
                            <div class="row ">
                                <input type="hidden" class="filter-fields outer_actions   modal_actions" readonly
                                    data-type="<?php echo $filter_value['data_type'];?>"
                                    value=""
                                        id="<?php echo 'filter-id-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name']; ?>"
                                        name="<?php echo 'filter-name-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name']; ?>">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="padding-right:0px">
                                    <input type="text" placeholder="Type anything to search" autocomplete="off"
                                         class="form-control input-sm filter-fields outer_actions  modal_actions typeahead-fields typeahead"
                                         <?php echo ($remotelink!="")?'data-remotelink="'.$remotelink.'"':''; ?>
                                         data-sourcedb="<?php echo $filter_value["data_source_database_name"];?>"
                                         data-sourcetb="<?php echo $filter_value["data_source_table_name"];?>"
                                         data-template='<?php echo $templateItself;?>'
                                         data-sourcecols="<?php echo str_replace(" ",",",str_replace(":",",",$filter_value["data_source_value_pair"]));?>"
                                         data-fieldsearch="<?php echo str_replace(" ",",",str_replace(":",",",$filter_value["data_source_value_pair"]));?>"
                                         data-clearbutton="<?php echo 'filter-id-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-clearbutton'; ?>"
                                         data-extrareturnfieldsnames="<?php echo $value_pairs[0]."|".$value_pairs_disp[1]; ?>"
                                         data-extrareturnfields="<?php echo 'filter-id-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'] .
                                                                        '|filter-id-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-'.$value_pairs_disp[1]; ?>"
                                                <?php echo ($filter_value["max_length"]!==""&&$filter_value["max_length"]!="0"&&$filter_value["data_type"]!="4")?'maxlength="'.$filter_value["max_length"].'"':'';?> 
                                        value="" 
                                        id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-'.$value_pairs_disp[0]; ?>"
                                        name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-'.$value_pairs_disp[0]; ?>" />
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12" style="padding-left:0px;padding-right:0px">
                                    <input type="text" placeholder="Description" autocomplete="off" readonly
                                         class="form-control input-sm filter-fields outer_actions   modal_actions"
                                        value="" 
                                        id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-'.$value_pairs_disp[1]; ?>"
                                        name="<?php echo 'filter-name-'.$filter_value["data_type"].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-'.$value_pairs_disp[1]; ?>" />
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12" style="padding-left:0px;padding-right:0px">
                                    <button class="btn btn-default btn-sm outer_actions " type="button" 
                                    id="<?php echo 'filter-id-'.$filter_value["data_type"].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'].'-clearbutton'; ?>"><i class="fa fa-eraser"></i> Clear</button>
                                </div>
                            </div>
                                
                         
                        <?php
						
					break;
					//Parameters 
					case "12":
						
					break;
				} //switch ($filter_value["data_type"]){
			?>
				</div> <!-- span -->
			<?php
			} //foreach ($this->FILDER_DATA as $filter_key => $filter_value){
			?>
				</div>
			  </div> <!-- .panel-body -->
			  <div class="panel-footer" style="display:none" >
			  		<button type="button" class="btn btn-info btn-sm outer_actions " id="filter_button"><i class="fa fa-filter"></i> Filter</button>
			  		<button type="button" class="btn btn-default btn-sm outer_actions " id="clear_button"><i class="fa fa-times"></i> Clear Filters</button>
			  </div> <!-- .panel-footer -->
			</div>
			<hr>
			<?php	    
		} // if (count($this->FILTER_DATA)<=0){
	} // private function displayFilters(){

	// DISPLAY BOTTOM SCRIPTS
	private function displayScripts(){
			$recordsValues = str_replace("All", "-1", $this->SHOW_RECORD_FIELDS);
			if ($this->HAS_ACCESS==true){
				/* var ABSOLUTE_PATH = "<?php echo ABSOLUTE_PATH;?>"; */

		?>

			<script type="text/javascript">
				

				<?php if ($this->TYPE=="1"){ 
					 echo $this->generateJavascriptConstants(); 
				} // if ($this->TYPE=="1"){  ?>
	            
			    $(document).ready(function(){
			    	/* suppress keypress - para hindi mag submit yung form */
			    
		        	$('input,select,textarea').live("keypress",function(event) { return event.keyCode != 13; }); 
			    	initializeScripts("1");

                    //$('.parent-checkboxes').change(function(){
                	$('.parent-checkboxes').live("change",function(){
                        var enab = $(this).prop('checked');
                        $('.'+$(this).data('children')).each(function(idx,el){
                            $(this).prop('checked',enab);
                            $(this).change();
                        }); /* $('.'+$(this).data('children')).each(function(idx,el){ */
                    }); /* $('.parent-checkboxes').change(function(){ */

                    //$('.child-checkboxes').change(function(){
                	$('.child-checkboxes').live("change",function(){
                        if (!$(this).prop('checked')) {
                            $('#'+$(this).data('parent')).prop('checked',false);
                        } /* if (!$(this).prop('checked')) { */
                        else {
                            var enab = true;
                            $('.'+$('#'+$(this).data('parent')).data('children')).each(function(idx,el){
                                if (!$(this).prop('checked')) {
                                    enab = false;
                                    return;
                                } /* if (!$(this).prop('checked')) { */
                            }); /* $('.'+$('#'+$(this).data('parent')).data('children')).each(function(idx,el){ */
                            $('#'+$(this).data('parent')).prop('checked',enab);
                        } /* ELSE ng if (!$(this).prop('checked')) { */
                    }); /* $('.child-checkboxes').change(function(){ */
			        
			        <?php if ($this->TYPE=="1"){
		        	?>
		        		//data to pass
		        		//$this->FILTER_DATA[$ctr]["table_field_name"] = $value["table_field_name"];

		        		var indexTbl = initializeDataTables();

						

						<?php 

							/*
							*	RENDER CUSTOM INLINE SCRIPTS OF ACTIONS ADDED
							*			
							*/
							echo $this->renderCustomInlineJSActions();


							/*
	        				*
	        				*	IF THE MASTER FILE HAS AT LEAST 1 COLUMN THAT IS can_add = 1, 
	        				*	THEN WRITE THE JQUERY CODES NEEDED
	        				*
	        				*/
							if ($this->HAS_ADD_PRIVILEGE==true&&$this->HAS_ADD==true){
	        				?>
	        					$(".add_action").live("click",function(){
	        						// disable the buttons/inputs outside of modal
	        						/*$(".outer_actions").each(function(idx,el){
	        							$(this).prop("disabled",true);
	        						});*/ // $(".outer_actions").each(function(idx,el){

        							<?php
	        						//build query string to pass to the load
	        						$qstring = "?";
	        						$qstring = self::queryStringBuild(self::buildModalParameterArray("can_add"));
	        						?>
	        						// open modal here
	        						$("#loading-message").html("<b>Loading Modal...</b>");
	        						$("#blocker").fadeIn("fast",function(){
	        							$("#modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/MasterfilesAddEditModal.php<?php echo $qstring;?>",function(){
	        								initializeScripts("2");

	        								<?php if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["Chained"]) && $this->PAGE_SETTINGS["JSEnable"]["Chained"]===true) {?> 
	        								$("select.masterfiletransaction-fields").each(function(){
							        			if ($(this).attr("data-chainingparentid")!=""){
							        				$(this).chained("#"+$(this).attr("data-chainingparentid"));
							        			}
							        		});
							        		<?php } ?>

		        							$("#modal-wrapper").css("display","");
		        							$("#MasterfileAddEditModal").modal("show");
		        							$("#blocker").fadeOut("fast");
		        						}); // $("#modal-wrapper").load()
	        						}); // $("#blocker").fadeIn("fast",function(){
	        						return false;
	        					}); // $(".add_action").live("click",function(){
	        				<?php
		        			} // if ($this->HAS_ADD==true){



	        				/*
	        				*
	        				*	IF THE MASTER FILE HAS AT LEAST 1 COLUMN TO BE SHOWN, 
	        				*	THEN WRITE THE JQUERY CODES NEEDED
	        				*
	        				*/
		        			if ($this->HAS_SHOW==true){
	        				?>
	        					/*
	        					*
	        					*	SHOW BUTTON CLICK TRIGGER
	        					*		OPEN A MODAL BASED ON COLUMNS TO BE SHOWN
	        					*
	        					*/
	        					$(".show_action").live("click",function(){
	        						var data = indexTbl.row( $(this).parents('tr') ).data();
	        						// disable the buttons/inputs outside of modal
	        						/*$(".outer_actions").each(function(idx,el){
	        							$(this).prop("disabled",true);
	        						});*/ // $(".outer_actions").each(function(idx,el){

	        						<?php
	        						//build query string to pass to the load
	        						$qstring = "?";
	        						$qstring = self::queryStringBuild(self::buildModalParameterArray("can_show"));
	        						?>
	        						// open modal here
	        						$("#loading-message").html("<b>Retrieving Data...</b>");
	        						$("#blocker").fadeIn("fast",function(){
	        							$("#modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/MasterfilesShowModal.php<?php echo $qstring;?>",function(){
		        							$("#modal-wrapper").css("display","");
		        							$("#MasterfileShowModal").modal("show");
		        							$("#blocker").fadeOut("fast");
		        						}); // $("#modal-wrapper").load()
	        						}); // $("#blocker").fadeIn("fast",function(){
	        						return false;
	        					}); // $(".show_action").live("click",function(){

        						// event listener for modal hidden
        						$('#MasterfileShowModal').live('hidden.bs.modal', function (e) {
	        						$("#modal-wrapper").css("display","none");
	        						$("#MasterfileShowModal").remove();
								}); // $('#MasterfileShowModal').live('hidden.bs.modal', function (e) {

								$('#MasterfileShowModal').live('show.bs.modal', function (e) {

		        					topOffset = 200;
		        					height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
		        					height = height - topOffset;

		    						$('#MasterfileShowModal').find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
								}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {

        						
	        				<?php
		        			} // if ($this->HAS_SHOW==true){




        					/*
	        				*
	        				*	IF THE MASTER FILE HAS AT LEAST 1 COLUMN THAT IS can_edit = 1, 
	        				*	THEN WRITE THE JQUERY CODES NEEDED
	        				*
	        				*/
	        				if ($this->HAS_EDIT_PRIVILEGE==true&&$this->HAS_EDIT==true){
	        					// check for privilege here
		        			?>


		        				$(".edit_action").live("click",function(){
		        					var data = indexTbl.row( $(this).parents('tr') ).data();
		        					// disable the buttons/inputs outside of modal
	        						/*$(".outer_actions").each(function(idx,el){
	        							$(this).prop("disabled",true);
	        						});*/ // $(".outer_actions").each(function(idx,el){

        							<?php
	        						//build query string to pass to the load
	        						$qstring = "?";
	        						$qstring = self::queryStringBuild(self::buildModalParameterArray("can_edit"));
	        						?>
	        						// open modal here
	        						$("#loading-message").html("<b>Loading Modal...</b>");
	        						$("#blocker").fadeIn("fast",function(){
	        							$("#modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/MasterfilesAddEditModal.php<?php echo $qstring;?>",function(){
	        								initializeScripts("2");
	        								<?php if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["Chained"]) && $this->PAGE_SETTINGS["JSEnable"]["Chained"]===true) {?> 
	        								$("select.masterfiletransaction-fields").each(function(){
							        			if ($(this).attr("data-chainingparentid")!=""){
							        				//$(this).chained("#"+$(this).attr("data-chainingparentid"));
							        			}
							        		});
							        		<?php } ?>
		        							$("#modal-wrapper").css("display","");
		        							$("#MasterfileAddEditModal").modal("show");
		        							$("#blocker").fadeOut("fast");
		        						}); // $("#modal-wrapper").load()
	        						}); // $("#blocker").fadeIn("fast",function(){
	        						return false;
	        					}); // $(".edit_action").live("click",function(){
	        				<?php
		        			} // if ($this->HAS_EDIT_PRIVILEGE==true&&$this->HAS_EDIT==true){




	        				/*
	        				*
	        				*	IF THE MASTER FILE HAS AT LEAST 1 COLUMN THAT IS can_delete = 1, 
	        				*	THEN WRITE THE JQUERY CODES NEEDED
	        				*
	        				*/
	        				if ($this->HAS_DELETE_PRIVILEGE==true&&$this->HAS_DELETE==true){
	        					// check for privilege here
		        			?>
		        				$(".delete_action").live("click",function(){
		        					var data = indexTbl.row( $(this).parents('tr') ).data();
		        					$("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?modulemstcode=<?php echo $this->MODULE_MST_CODE;?>&menuitemmstcode=<?php echo $this->MENU_ITEM_MST_CODES;?>&valueviewed="+encodeURI(data[1])+"&primarycodefields=<?php echo $this->PRIMARY_CODE_FIELD_NAMES;?>&tablename=<?php echo $this->TABLE_NAME;?>&dbconnect=<?php echo $this->DATABASE_NAME;?>&primarycode="+encodeURI(data[0])+"&colour=danger&mode=delete&trans=can_delete&dialog_title=Delete&dialog_message=Are|you|sure|do|you|want|to|delete|record|'"+encodeURI(data[1])+"'$||This|will|include|detail|tables",function(){
	        							$("#dialog-modal-wrapper").css("display","");
	        							$("#DialogModal").modal("show");
	        						}); // $("#dialog-modal-wrapper").load()
        							return false;
	        					}); // $(".delete_action").live("click",function(){
	        				<?php
		        			} // if ($this->HAS_DELETE==true){



	        				// IF MERON ADD AND EDIT -> RENDER CANCEL AND SAVE OPTIONS
	        				if ( ($this->HAS_EDIT_PRIVILEGE==true&&$this->HAS_EDIT==true) || ($this->HAS_ADD_PRIVILEGE==true&&$this->HAS_ADD==true)){
        					?>
        						$('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {
	        						$("#modal-wrapper").css("display","none");
	        						$("#MasterfileAddEditModal").remove();
								}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {


								$('#MasterfileAddEditModal').live('show.bs.modal', function (e) {

		        					topOffset = 200;
		        					height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
		        					height = height - topOffset;

		        									$("select.masterfiletransaction-fields").each(function(){
									        			//$("select.filter-fields").live("change",function(){
									        			if ($(this).attr("data-chainingparentid")!=""&& typeof $(this).attr("data-chainingparentid")!= "undefined"){
									        				//var parentids = s
															if($(this).attr("data-sourcevaluepair")){
															var lch_columns = $(this).attr("data-sourcevaluepair").replace(':',',');	        				
										        				$("#"+$(this).attr("id")).remoteChained({
																    parents : "#"+$(this).attr("data-chainingparentid"),
																    url :  ABSOLUTE_PATH+"api/SelectRemoteChain.php?columns="+lch_columns+"&database="+$(this).attr('data-sourcedb')+ "&table="+$(this).attr('data-sourcetb')+"&chain="+$(this).attr("data-chainingparentid")+"&fieldname="+$(this).attr("title")+"&chainingparentfield="+$(this).attr("data-chainingparentfield"),
																 loading : "Loading..."
																});
															}	
									        			}
									        			// });
									        		});

		    						$('#MasterfileAddEditModal').find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
								}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {

        						// cancel action
        						$(".cancel_modal_action").live("click",function(){	
        							$("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=cancel&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){
	        							$("#dialog-modal-wrapper").css("display","");
	        							$("#DialogModal").modal("show");
	        						}); // $("#dialog-modal-wrapper").load()
        							return false;
        						}); // $(".close_modal_action").live("click",function(){

    							// SAVE ACTION
    							$(".save_modal_action").live("click",function(){	
    								// located in additionals.js
    								$(this).button("loading");
    								if (masterfileClientSideValidation()){
    									// if validation complete, 
    									$("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=save&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
		        							$("#dialog-modal-wrapper").css("display","");
		        							$("#DialogModal").modal("show");
		        						}); // $("#dialog-modal-wrapper").load()
    								}
        							return false;
        						}); // $(".close_modal_action").live("click",function(){
        					<?php
	        				} // if ($this->HAS_EDIT==true||$this->HAS_ADD==true){
						?>

						<?php
						if ($this->HAS_PRINT_PRIVILEGE==true&&$this->HAS_PRINT==true){
						?>
						/*
        				*
        				*	PRINT ACTION HERE = FOR ALL MASTERFILE, DYNAMIC
        				*	
        				*
        				*/
        				$('#MasterfilePrintModal').live('shown.bs.modal', function (e) {
        					topOffset = 200;
        					height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
        					height = height - topOffset;
    						$(this).find('iframe#print-frame').css({
					              //height:'auto', //probably not needed 
					              'height':height+'px'
					       });
						}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {
						$(".print_action").live("click",function(){
							/*
        					*
        					*	PRINT BUTTON CLICK TRIGGER
        					*		OPEN A MODAL BASED ON COLUMNS TO BE SHOWN
        					*
        					*/
        						var data = indexTbl.row( $(this).parents('tr') ).data();
        						// disable the buttons/inputs outside of modal
        						/*$(".outer_actions").each(function(idx,el){
        							$(this).prop("disabled",true);
        						});*/ // $(".outer_actions").each(function(idx,el){

        						<?php
        						//build query string to pass to the load
        						$qstring = "?";
        						$qstring = self::queryStringBuild(self::buildModalParameterArray("can_display_on_table"));
        						?>
        						// open modal here
        						$("#loading-message").html("<b>Retrieving Data...</b>");
        						$("#blocker").fadeIn("fast",function(){
        							//window.open("<?php echo ABSOLUTE_PATH;?>includes/PrintMasterfiles.php");
        							$("#modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/PrintMasterfilesModal.php<?php echo $qstring;?>&code=1",function(){
	        							$("#modal-wrapper").css("display","");
	        							$("#MasterfilePrintModal").modal("show");
	        							$("#blocker").fadeOut("fast");
	        						}); // $("#modal-wrapper").load()
        						}); // $("#blocker").fadeIn("fast",function(){
        						return false;
    					}); // $(".print_action").live("click",function(){
						<?php } // if ($this->HAS_PRINT==true){ ?>


					<?php
			        } //if ($this->TYPE=="1"){
			        ?>

			    });


			function initializeScripts(src){
				
		    	<?php if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["DatePicker"]) && $this->PAGE_SETTINGS["JSEnable"]["DatePicker"]===true) {?> 
				/* datepicker */
		        $('.input-group.date').datepicker({
		        format: "yyyy-mm-dd",
		            todayBtn: "linked",
		            orientation: "top left",
		            autoclose: true,
		            todayHighlight: true
		            //, startDate: '-0m'
		        });
		        <?php } ?>
		        <?php if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["Number"]) && $this->PAGE_SETTINGS["JSEnable"]["Number"]===true) {?> 
		    	/* decimal input field */
		    	$(".decimalFormat").each(function(idx,el){
		    		if ($(this).attr("data-numberofdec")){
		    			//$(this).number(true,$(this).attr("data-numberofdec"));	
						$(this).autoNumeric('init', 
								{aSep:',',
								dGroup:'3',
								aDec:'.',
								mDec:$(this).attr("data-numberofdec")});
		    		}
		    		else {
		    			// default 2 decimal places pag di nakaset yung data-numberofdec
		    			//$(this).number(true,2);
						$(this).autoNumeric('init', 
								{aSep:',',
								dGroup:'3',
								aDec:'.',
								mDec:'2'});
		    		}
					
				});

				// $("decimalFormat-4").autoNumeric('init', 
				// 				{aSep:',',
				// 				dGroup:'3',
				// 				aDec:'.',
				// 				mDec:'2'});
				
		        // $('.decimalFormat').number( true, 5 );
		        /* integer input field */
		        $('.integerFormat').number( true, 0 );
		        <?php } ?>
		        // $('.wysiwyg-editor').jqte({'source':false,'link':false,'unlink':false,'rule':false});
		        CKEDITOR.replaceAll('wysiwyg-editor');	
		        $(".customformat").each(function(idx,el){
					$(this).mask($(this).attr("data-format"));
				});

		        $(".typeahead-fields").each(function(idx,el){
		        	var thisfield = $(this);
		        	var fieldname = $(this).attr("name");
		        	var templatelist = $(this).attr("data-template");
		        	var remotelink = $(this).attr("data-remotelink");	
		        	var sourcedb = $(this).attr("data-sourcedb");
		        	var sourcetb = $(this).attr("data-sourcetb");
		        	var sourcecols = $(this).attr("data-sourcecols");
		        	var fieldsearch = $(this).attr("data-fieldsearch");
		        	var extrareturnfieldsnames = $(this).attr("data-extrareturnfieldsnames").split("|");
		        	var extrareturnfields = $(this).attr("data-extrareturnfields").split("|");
		        	var customsearch = (typeof $(this).attr("data-customsearch") == "undefined") ? "" : $(this).attr("data-customsearch");
		        	var clearbutton = $(this).attr("data-clearbutton");
		        	$(this).typeahead({
						name: fieldname,
						template:templatelist,
						limit:50,
						engine: Hogan,
						remote: ABSOLUTE_PATH+''+remotelink + "?searchstring=%QUERY&sourcedb="+sourcedb+"&sourcetb="+sourcetb+"&sourcecols="+sourcecols+"&fieldsearch="+fieldsearch+"&customsearch="+customsearch,
						header: '<div class="tt-header">List:</div>',
						footer: '<div class="tt-footer">*First 50 results only*</div>'
					}); // $(this).typeahead({
					$(this).on('typeahead:selected',function(e,datum){	
						for (var c = 0 ; c < extrareturnfields.length ; c ++){
							$("#"+extrareturnfields[c]).val(datum[extrareturnfieldsnames[c]]);
						} // for (var c = 0 ; c < extrareturnfields.length ; c ++){
					});
					$(this).on("blur",function(){
						if ($(this).val()==""){
							for (var c = 0 ; c < extrareturnfields.length ; c ++){
								$("#"+extrareturnfields[c]).val("");
							} // for (var c = 0 ; c < extrareturnfields.length ; c ++){
						}
					});
					$("#"+clearbutton).live("click",function(){
						for (var c = 0 ; c < extrareturnfields.length ; c ++){
							$("#"+extrareturnfields[c]).val("");
						} // for (var c = 0 ; c < extrareturnfields.length ; c ++){
						$(thisfield).val("");
						$(thisfield).typeahead('setQuery', '');
					});
		        }); // $(".typeahead-fields").each(function(idx,el){
	        	$('.typeahead.input-sm').siblings('input.tt-hint').addClass('hint-small');
				$('.typeahead.input-lg').siblings('input.tt-hint').addClass('hint-large');

		        $('.parent-checkboxes').each(function(idx,el){
                    var ctr=0;
                    var totalchild=0;
                    var enab=false;
                    $('.'+$(this).data('children')).each(function(idx,el){
                        totalchild++;
                    });
                    $('.'+$(this).data('children')).each(function(idx,el){
                        if($(this).prop('checked')){
                            ctr++;
                            enab=true;
                        } /* if($(this).prop('checked')){ */
                        else{
                            enab=false;
                            ctr=0;
                            return;
                        } /*ELSE ng if($(this).prop('checked')){ */
                    }); /* $('.'+$(this).data('children')).each(function(idx,el){ */
                    if (ctr==totalchild && ctr>0 && enab) {
                        $(this).prop('checked',true);
                    } /* if (ctr>0 && enab) { */
                }); /* $('.parent-checkboxes').each(function(idx,el){ */

            	$(".no_privilege").live("click",function(){
            		alert ('You are not supposed to be clicking this. Returning to home page for safety precaution..');
            		location.href="../../index";
            		return false;
            	}); // $(".no_privilege").live("click",function(){


        		

        		
        		<?php if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["Chained"]) && $this->PAGE_SETTINGS["JSEnable"]["Chained"]===true) {?> 
        		// chaining parent
        		if (src=="1"){
	        		$("select.filter-fields").each(function(){
	        			//$("select.filter-fields").live("change",function(){
	        			if ($(this).attr("data-chainingparentid")!=""&& typeof $(this).attr("data-chainingparentid")!= "undefined"){
	        				//var parentids = s
							if($(this).attr("data-sourcevaluepair")){
							var lch_columns = $(this).attr("data-sourcevaluepair").replace(':',',');	        				
		        				$("#"+$(this).attr("id")).remoteChained({
								    parents : "#"+$(this).attr("data-chainingparentid"),
								    url :  ABSOLUTE_PATH+"api/SelectRemoteChain.php?columns="+lch_columns+"&database="+$(this).attr('data-sourcedb')+ "&table="+$(this).attr('data-sourcetb')+"&chain="+$(this).attr("data-chainingparentid")+"&fieldname="+$(this).attr("title")+"&chainingparentfield="+$(this).attr("data-chainingparentfield"),
								 loading : "Loading..."
								});
							//	alert($(this).attr("id"));
						}
	        				//$(this).chained("#"+$(this).attr("data-chainingparentid"));
	        			}
	        			// });
	        		});
        		}
        		<?php } ?>

			}


			<?php if ($this->TYPE=="1"){ ?>

			
			function initializeDataTables(){
				<?php 
        		/*
				*
				*	SETUP DEFAULT CONTENT
				*	THESE ARE THE BUTTONS PER RECORD
				*
				*/
        			//$defaultContent = '<div class="per_record_action_cont">';
					$defaultContent = '';
        			if ($this->HAS_SHOW==true){
        				$defaultContent = $defaultContent . '<button type="button" class="btn btn-default btn-xs show_action outer_actions" title="Show details for this record"><i class="'.$this->BUTTON_ICONS["Show"].'"></i> Show</button>&nbsp;&nbsp;';
        			} // if ($this->HAS_SHOW==true){

    				if ($this->HAS_EDIT_PRIVILEGE==true&&$this->HAS_EDIT==true){
    					// check for privilege here
        				$defaultContent = $defaultContent . '<button type="button" class="btn btn-warning btn-xs edit_action outer_actions" title="Edit this record"><i class="'.$this->BUTTON_ICONS["Edit"].'"></i> Edit</button>&nbsp;&nbsp;';
        			} // if ($this->HAS_EDIT==true){
    				else if ($this->HAS_EDIT_PRIVILEGE==false&&$this->HAS_EDIT==true) {
    					$defaultContent = $defaultContent . '<div style="display:inline-block" title="You have no Edit privilege. Please contact system administrator">';
    					$defaultContent = $defaultContent . '<button type="button" class="btn btn-warning btn-xs outer_actions no_privilege" disabled><i class="'.$this->BUTTON_ICONS["Edit"].'"></i> Edit</button>';
    					$defaultContent = $defaultContent . '</div>';
    				} // else ng if ($this->HAS_EDIT==true){

    				if ($this->HAS_DELETE_PRIVILEGE==true&&$this->HAS_DELETE==true){
        				$defaultContent = $defaultContent . '<button type="button" class="btn btn-danger btn-xs delete_action outer_actions" title="Delete this record"><i class="'.$this->BUTTON_ICONS["Delete"].'"></i> Delete</button>';
        			} // if ($this->HAS_DELETE==true){
    				else if ($this->HAS_DELETE_PRIVILEGE==false&&$this->HAS_DELETE==true){
    					$defaultContent = $defaultContent . '<div style="display:inline-block" title="You have no Delete privilege. Please contact system administrator">';
    					$defaultContent = $defaultContent . '<button type="button" class="btn btn-danger btn-xs outer_actions no_privilege" disabled><i class="'.$this->BUTTON_ICONS["Delete"].'"></i> Delete</button>';
    					$defaultContent = $defaultContent . '</div>';
    				} // else ng if ($this->HAS_DELETE==true){
					//$defaultContent = $defaultContent . '</div>';


					$defaultContent = $defaultContent . $this->renderCustomActions("2");
					

    				/*
    				<div style="display:inline-block" title="You have no Print privilege. Please contact system administrator">
					<button class="btn btn-default btn-sm outer_actions no_privilege"  disabled type="button"><i class="<?php echo $this->BUTTON_ICONS["Print"];?>"></i> Print Listing</button>
					</div>
					*/


        		/*
				*
				*	SETUP COLUMNS FOR DISPLAY AND ITS ALIGNMENT TO BE DISPLAYED ON DATATABLE
				*	
				*
				*/
        		$columnsToQuery = "";
        		$centerAlignments = "";
        		$leftAlignments = "";
        		$rightAlignments = "";
        		$formattingtext = "";
        		$columnsToFormat = ""; 
				$columnsFieldDataType = "";
				$usesformat = "";
				$lch_innerjoincondition = ""; 


        		$c = 0;
				foreach ($this->COLUMN_DATA as $columnEntry){
					if ($columnEntry["can_display_on_table"]=="1") {
						
						if($columnEntry["data_source"] == "2"){
							$lch_foreign_fields =	explode(":",$columnEntry["data_source_value_pair"]);
							$lch_foreign_field = explode(" ",$lch_foreign_fields[1]) ;
									//$columnEntry["data_source_value_pair"]
							//888888888888888888888888
							//if ( $this->TABLE_NAME == $columnEntry['data_source_table_name'])
							if (isset($columnEntry['data_source_table_alias']) && $columnEntry['data_source_table_alias'] != "") 
							{								
								$lch_innerjoincondition = $lch_innerjoincondition . "`" . $columnEntry['data_source_database_name']  . "`.`" . 
								$columnEntry['data_source_table_name'] . "` as `" . $columnEntry['data_source_table_alias'] . "`," . "`" . $columnEntry['data_source_table_alias'] . "`.`" . 
								$lch_foreign_fields[0] . "`" . "=" . "`" .
								$this->TABLE_NAME . "`.`"  . $columnEntry['table_field_name'] . "`|" ;								
								$columnsToQuery = $columnsToQuery  . "`" .$columnEntry['data_source_table_alias'] . "`" . ".`" .    $lch_foreign_field[0] . "` as `" . $columnEntry['data_source_field_alias'] . "`," ;         
									// $columnsToQuery = $columnsToQuery . "". $columnEntry["table_field_name"] . ",";	
							} //88888888888888888		
							else 


							{ 
								$lch_innerjoincondition = $lch_innerjoincondition . "`" . $columnEntry['data_source_database_name']  . "`.`" .
								$columnEntry['data_source_table_name'] . "`," . "`" . 
								"`" . $columnEntry['data_source_database_name']  . "`.`" . $columnEntry['data_source_table_name'] . "`.`" . 
								$lch_foreign_fields[0] . "`" . "=" . "`" .
								$this->TABLE_NAME . "`.`"  . $columnEntry['table_field_name'] . "`|" ;
								
						

									$columnsToQuery = $columnsToQuery . "`" . $columnEntry['data_source_database_name']  . "`.`" . $columnEntry['data_source_table_name']  . "`.`" .    $lch_foreign_field[0] . "`," ;         
									// $columnsToQuery = $columnsToQuery . "". $columnEntry["table_field_name"] . ",";	

							}
						} // if($columnEntry["data_source"] == "2"){
						else 
						{
						$columnsToQuery = $columnsToQuery .  "`" . $this->DATABASE_NAME . "`.`" . $this->TABLE_NAME . "`." . "`" . $columnEntry["table_field_name"] . "`,";	
						}
						
						//$columnsToQuery = $columnsToQuery . "". $columnEntry["table_field_name"] . ",";
							$usesformat = explode("=",$columnEntry["special_conditions"]);


						if (($columnEntry["special_conditions"] !="")  && (($usesformat[0] == "usesformat") || ($usesformat[0] == "customformat") ) /* && ($usesformat[1]== "yes" )*/ ) {
						$columnsToFormat = $columnsToFormat . "". $columnEntry["special_conditions"]  . ",";
						}
						else {
						$columnsToFormat = $columnsToFormat .  "usesformat=no,";	
						}

						$columnsFieldDataType = $columnsFieldDataType . "". $columnEntry["data_type"] . ",";
						// manage alignment
						if ($columnEntry["alignment"]=="left"){
							$leftAlignments = $leftAlignments . "" . $c . ",";
						}
						else if ($columnEntry["alignment"]=="center"){
							$centerAlignments = $centerAlignments . "" . $c . ",";
						}
						else if ($columnEntry["alignment"]=="right"){
							$rightAlignments = $rightAlignments . "" . $c . ",";
						}
						
						//if (($columnEntry["data_type"]=="1") || ($columnEntry["data_type"]=="2") || ($columnEntry["data_type"]=="3") || ($columnEntry["data_type"]=="4") ){
						if ($columnEntry["data_type"]!="1"){
							$formattingtext = $formattingtext . "" . $c . ",";
						}
						/*
						else if ($columnEntry["data_type"]=="center"){
							$centerAlignments = $centerAlignments . "" . $c . ",";
						}
						else if ($columnEntry["data_type"]=="right"){
							$rightAlignments = $rightAlignments . "" . $c . ",";
						}  
						*/ /* for formatting text */
					

						$c++;
					} // if ($columnEntry["can_display_on_table"]=="1") {
				} // foreach ($this->COLUMN_DATA as $columnEntry){ 
				$columnsToQuery = rtrim($columnsToQuery,",");
				$columnsToFormat = rtrim($columnsToFormat,",");
				$lch_innerjoincondition = rtrim($lch_innerjoincondition,"|");
				$columnsFieldDataType = rtrim($columnsFieldDataType,",");
				$leftAlignments = rtrim($leftAlignments,",");
				if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
					$centerAlignments = $centerAlignments . "" . $this->COLUMN_TABLE_DISPLAY_COUNT;
				} // if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
				else {
					$centerAlignments = rtrim($centerAlignments,",");
				} // ELSE ng if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
				$rightAlignments = rtrim($rightAlignments,",");
				$formattingtext = rtrim($formattingtext,",");
				?>


				/*
				*
				*	INITIALIZE THE DataTable FOR INDEXED DISPLAY
				*	
				*
				*/
		        var indexTbl = $("#indexedTable").DataTable({
		        	<?php if ($this->AT_LEAST_ONE_RECORD_ACTION==true){
					?>
				  	"columnDefs": [{ "orderable": false, "targets": <?php echo $this->COLUMN_TABLE_DISPLAY_COUNT ;?>,
			  						"data":null,
			  						"defaultContent":'<?php echo $defaultContent;?>' },
			  						//{ "width": "19%", "targets": <?php echo $this->COLUMN_TABLE_DISPLAY_COUNT ;?>},
			  						{ "class": "column-actions center", "targets": <?php echo $this->COLUMN_TABLE_DISPLAY_COUNT ;?>},
			  						{ "class": "center", "targets": <?php echo "[".$centerAlignments."]" ;?> },
			  						{ "class": "left", "targets": <?php echo "[".$leftAlignments."]" ;?> },
			  						{ "class": "right " , "targets": <?php echo "[".$rightAlignments."]" ;?> },
			  						{"targets": [ 0 ],"visible": false,"searchable": false},
			  						//{ "className": "column-actions", "targets": <?php echo $this->COLUMN_TABLE_DISPLAY_COUNT ;?>},
			  						{ "searchable": false, "targets": [<?php echo $this->COLUMN_TABLE_DISPLAY_COUNT . (($this->UNSEARCHABLE_COLUMNS!='')?",".$this->UNSEARCHABLE_COLUMNS :"") ;?>]}],

				  	<?php } // if ($this->AT_LEAST_ONE_RECORD_ACTION==true){ ?>
		        	"processing":true,
	        		"lengthMenu": [[<?php echo $recordsValues ;?>], [<?php echo str_replace("All","'All'",$this->SHOW_RECORD_FIELDS);?>]] ,
	        		//"dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>',
	        		<?php 
	        			$true = true;
	        			$string = "\"dom\"";
	        			$string = $string . " : ";
	        			$string = $string . "\"<'row'<'col-lg-6 col-md-6 col-sm-6 col-xs-12 left'><'col-lg-6 col-md-6 col-sm-6 col-xs-12'p>>\"";
	        			$string = $string . " + ";
	        			$string = $string . "\"t\"";
	        			$string = $string . " + ";
	        			$string = $string . "\"<'row'<'col-lg-3 col-md-3 col-sm-3 col-xs-12'l><'col-lg-4 col-md-4 col-sm-4 col-xs-12 center'i><'col-lg-5 col-md-5 col-sm-5 col-xs-12'p>>\"";
	        			$string = $string . ",";
	        			$count = explode(",",$this->UNSEARCHABLE_COLUMNS);

	        			//condition to hide search
		        		if ((count($count) + 1) == $this->COLUMN_TABLE_DISPLAY_COUNT) {
		        			echo $string;
		        			// echo "alert(" . $this->UNSEARCHABLE_COLUMNS . " " .$this->COLUMN_TABLE_DISPLAY_COUNT . ")";
		           		} ?>
	        		// "dom" : "<'row'<'col-lg-6 col-md-6 col-sm-6 col-xs-12 left'><'col-lg-6 col-md-6 col-sm-6 col-xs-12'p>>" + "t" + "<'row'<'col-lg-3 col-md-3 col-sm-3 col-xs-12'l><'col-lg-4 col-md-4 col-sm-4 col-xs-12 center'i><'col-lg-5 col-md-5 col-sm-5 col-xs-12'p>>",

	        		// default = "<'row'<'col-lg-6 col-md-6 col-sm-6 col-xs-12 left'f><'col-lg-6 col-md-6 col-sm-6 col-xs-12'p>>" + "t" + "<'row'<'col-lg-3 col-md-3 col-sm-3 col-xs-12'l><'col-lg-4 col-md-4 col-sm-4 col-xs-12 center'i><'col-lg-5 col-md-5 col-sm-5 col-xs-12'p>>",
				  	"serverSide": true,
				  	"ajax": {

				  		<?php if ($this->SPECIAL_CONDITIONS!="") {
				  			$customfile = explode("=", $this->SPECIAL_CONDITIONS);
			  			?>
			  				"url": "../../api/<?php echo $customfile[1] ;?>.php",
			  			<?php
				  			} // if ($this->SPECIAL_CONDITIONS!="") {
				  			else {
			  				?>
			  					"url": "../../api/ReceiveMasterfileTables.php",
			  				<?php
			  				} // ELSE ng if ($this->SPECIAL_CONDITIONS!="") {	?>
					    

					    "type": "POST",
					    "data": function ( d ) {
					                d.dbconnect = '<?php echo $this->DATABASE_NAME;?>';
					                d.tablename = '<?php echo $this->TABLE_NAME;?>';
					                d.columnstodisplay = '<?php echo $columnsToQuery;?>';
					                d.columnsfieldformat = '<?php echo $columnsToFormat;?>';
					                d.columnsdatatype = '<?php echo $columnsFieldDataType;?>';
					            	d.innerjoinTable = '<?php echo $lch_innerjoincondition;?>'; 
					            //    d.innerjoinTable = "`mstcity`,`mstcity`.`code`= `mstbarangay`.`city_mst_code`|`mstprovince`,`mstprovince`.`code`= `mstbarangay`.`province_mst_code`|`mstregion`,`mstregion`.`code`= `mstbarangay`.`region_mst_code`|`mstcountry`,`mstcountry`.`code`= `mstbarangay`.`country_mst_code`"
					                d.filterdataname = "";
					                d.filterdata = "";
					                <?php
					                if (count($this->FILTER_DATA)>0){
					                	//echo "d.filterdata = new Array();";
					                	foreach ($this->FILTER_DATA as $filter_key => $filter_value){
				                			if ($filter_value["data_type"]=="8"||$filter_value["data_type"]=="9"){
			                				?>
			                					var selectedRadio="";
			                					$(".<?php echo 'filter-name-'.$filter_value['data_type'].'-'.$filter_value['code'].'-'.$filter_value['table_field_name'];?>").each(function(idx,el){
			                						if ($(this).prop("checked")==true){
			                							selectedRadio = selectedRadio + $(this).val() + ",";
			                						} //if ($(this).prop("checked")){
			                					});
			                					selectedRadio = selectedRadio.substring(0, selectedRadio.length - 1);
			                					d.filterdataname+= "<?php echo $filter_value['table_field_name']."-".$filter_value['data_type'];?>|";
			                					d.filterdata += selectedRadio + "|";
			                				<?php
				                				
				                			} // if ($filter_value["data_type"]=="8"){
				                			else {
				                				echo 'd.filterdataname+="'.$filter_value["table_field_name"].'-'.$filter_value["data_type"].'|";';
				                				echo 'd.filterdata += $("#filter-id-'.$filter_value["data_type"].'-'.$filter_value["code"].'-'.$filter_value["table_field_name"].'").val() +"|";';
				                			} // else ng if ($filter_value["data_type"]=="9"){
					                	} //foreach ($this->FILTER_DATA as $filter_key => $filter_value){
			                		?>
			                			d.filterdataname = d.filterdataname.substring(0,d.filterdataname.length-1);
			                			d.filterdata = d.filterdata.substring(0,d.filterdata.length-1);
			                		<?php
					                } //if (count($this->FILTER_DATA)>0){
					                ?>
					            }
					  }
				} );
				$("#indexedTable").fadeIn("fast");


				indexTbl.on( 'draw', function () {
					<?php $lin_newWidth = 17;
					$lin_newWidth = 17 + (5 * $this->ACTION_COUNT);
					?>
					var newWidth = <?php echo $lin_newWidth;?>;
					var columnname;
				    $("td.column-actions,th.column-actions").each(function(idx,el){
				    	newWidth = <?php echo $lin_newWidth;?>;
				    	$(this).find("button").each(function(idx2,el2){
				    		newWidth += $(this).outerWidth(true);
				    	});
				    	$(this).css("width",newWidth+"px");
				    	$(this).css("min-width",newWidth+"px");
				    	$(this).css("max-width",newWidth+"px");
				    	$(this).css("white-space","nowrap");
				    });
				    $("th.column-actions").each(function(idx,el){
						if(newWidth == '<?php echo $lin_newWidth;?>')
						{
						newWidth += $(this).outerWidth(false);
						}
				    	$(this).css("width",newWidth+"px");
				    	$(this).css("min-width",newWidth+"px");
				    	$(this).css("max-width",newWidth+"px");
				    	$(this).css("white-space","nowrap");

				    });

				    var ctr=1;
				    $("th.table-header").each(function(idx,el){

				    	if ($(this).attr("data-isfromotherdata")=="true"){
				    		var fieldname = $(this).attr("data-tablefieldname");
				    		$("table#indexedTable tr td:nth-child("+ctr+")").each(function(idx,el){
				    			var val = $(this).html();
				    			if (val!="There are no records found."){
				    				
			    					//$(this).html(DATA[fieldname][val][1]);
					    			for (var i = 0 ; i < DATA[fieldname].length ; i++){
					    				if (DATA[fieldname][i][0]==val){
					    					//console.log(DATA[fieldname][i][0]+"=="+val);
					    					$(this).html(DATA[fieldname][i][1]);
					    					break;
					    				} // if (DATA[fieldname][i][0]==val){
					    			} // for (var i = 0 ; i < DATA[fieldname].length ; i++){
									
				    			}
				    			
				    		});
				    	} // if ($(this).attr("data-isfromotherdata")=="true"){
				    	ctr++;
				    }); // $("th.table-header").each(function(idx,el){

			    	$(window).resize();
				    //var data = indexTbl.row( $(this).parents('tr') ).data();
				} );
				$("#indexedTable").live("resize",function(){
					var newWidth = '<?php echo $lin_newWidth;?>';
					$(this).find("tr td.column-actions,th.column-actions").each(function(idx,el){
				    	newWidth = '<?php echo $lin_newWidth;?>';
				    	$(this).find("button").each(function(idx2,el2){
				    		newWidth += $(this).outerWidth(true);
				    	});
				    	$(this).css("width",newWidth+"px");
				    	$(this).css("min-width",newWidth+"px");
				    	$(this).css("max-width",newWidth+"px");
				    	$(this).css("white-space","nowrap");
				    });
				    $(this).find("tr th.column-actions").each(function(idx,el){
						if(newWidth == '<?php echo $lin_newWidth;?>')
						{
						newWidth += $(this).width();
						}					
				    	$(this).css("width",newWidth+"px");
				    	$(this).css("min-width",newWidth+"px");
				    	$(this).css("max-width",newWidth+"px");
				    	$(this).css("white-space","nowrap");
				    });
				});

				return indexTbl;
			} // function initializeDataTables(){

			<?php } // if ($this->TYPE=="1"){ ?>

			</script>


		<?php

			if (isset($this->PAGE_SETTINGS["JSEnable"]) && isset($this->PAGE_SETTINGS["JSEnable"]["MasterfileFunctions"]) && $this->PAGE_SETTINGS["JSEnable"]["MasterfileFunctions"]===true) {
	        echo '<script src="../../resources/js/masterfilesfunctions.min.js'.VERSION_AFFIX.'"></script>';
	    	} 

    	} // if ($this->HAS_ACCESS==true){

		if (isset($this->PAGE_SETTINGS["NoNightMode"]) && $this->PAGE_SETTINGS["NoNightMode"]===false) {
			echo '<script src="../../resources/js/nightmode.min.js'.VERSION_AFFIX.'"></script>';
		}

		
	} //private function displayScripts(){

	// function to build query string based on array input
	private function queryStringBuild ($array){
		$queryString = "";

		if (count($array)>0){
			foreach ($array as $key => $value){
				$queryString = $queryString . $key."=".(str_replace(" ", "%20", $value))."&";
			}
			// remove trailing '&'
			$queryString = substr($queryString, 0,strlen($queryString)-1);
		} // if (count($array)>0){

		if ($queryString!=""){
			$queryString = "?" . $queryString;
		}
		return $queryString;
	} // private function queryStringBuild ($array){


	// function to build modal parameters based on action (Add/Edit/Show)
	// $action values = can_add , can_show , can_edit
	private function buildModalParameterArray($action){
		$columnsToQuery = "";
		$columnsFieldName = "";
		$columnsCaption = "";
		$columnsDataSource = "";
		$columnsDataSourceDatabaseName = "";
		$columnsDataSourceTableName = "";
		$columnsDataSourceValuePair = "";
		$columnsSpecialConditions="";
		$columnsIsRequired="";
		$columnsIsUnique="";
		$columnsDataType="";
		$columnsMaxLength="";
		$columnsCode="";


		// BUILD THE QUERYSTRING USING $parameterToPass array
		// common parameters
		$parameterToPass = array("usercode"=>$_SESSION["user_code"],
								"masterfilename"=>$this->NAME,
								"databasename"=>$this->DATABASE_NAME,
								"transactionmode"=>$action,
								"modulemstcode" => $this->MODULE_MST_CODE,
								"menuitemmstcode" => $this->MENU_ITEM_MST_CODES,
								"tablename"=>$this->TABLE_NAME);

		if ($action!="custom_action"&&$action!="custom_action_whole"){

			// GET ALL THE COLUMNS THAT IS SET UP TO BE SHOWN
			foreach ($this->COLUMN_DATA as $columnEntry){
				if ($columnEntry[$action]=="1") {
					$columnsToQuery = $columnsToQuery . "". $columnEntry["table_field_name"] . "|";
					$columnsFieldName = $columnsFieldName . "". $columnEntry["field_name"] . "|";
					$columnsCaption = $columnsCaption . "". $columnEntry["field_header_caption"] . "|";
					$columnsDataSource = $columnsDataSource. "". $columnEntry["data_source"] . "|";
					$columnsDataSourceDatabaseName = $columnsDataSourceDatabaseName. "".$columnEntry["data_source_database_name"]."|";
					$columnsDataSourceTableName = $columnsDataSourceTableName."".$columnEntry["data_source_table_name"]."|";
					$columnsDataSourceValuePair = $columnsDataSourceValuePair."".str_replace("|", ",", $columnEntry["data_source_value_pair"])."|";
					$columnsSpecialConditions = $columnsSpecialConditions."".str_replace("|", ",", $columnEntry["special_conditions"])."|";
					$columnsIsRequired = $columnsIsRequired."".$columnEntry["is_required"]."|";
					$columnsIsUnique = $columnsIsUnique."".$columnEntry["is_unique"]."|";
					$columnsDataType = $columnsDataType."".$columnEntry["data_type"]."|";
					$columnsMaxLength = $columnsMaxLength."".$columnEntry["max_length"]."|";
					$columnsCode = $columnsCode."".$columnEntry["code"]."|";
				} // if ($columnEntry[$action]=="1") {
			} // foreach ($this->COLUMN_DATA as $columnEntry){ 
			//used substring to maintain array count equally (remove lang yung excess sa dulo upon last loop)
			$columnsToQuery = substr($columnsToQuery, 0,strlen($columnsToQuery)-1);
			$columnsFieldName = substr($columnsFieldName, 0,strlen($columnsFieldName)-1);
			$columnsCaption = substr($columnsCaption, 0,strlen($columnsCaption)-1);
			$columnsDataSource = substr($columnsDataSource, 0,strlen($columnsDataSource)-1);
			$columnsDataSourceDatabaseName = substr($columnsDataSourceDatabaseName, 0,strlen($columnsDataSourceDatabaseName)-1);
			$columnsDataSourceTableName = substr($columnsDataSourceTableName, 0,strlen($columnsDataSourceTableName)-1);
			$columnsDataSourceValuePair = substr($columnsDataSourceValuePair, 0,strlen($columnsDataSourceValuePair)-1);
			$columnsSpecialConditions = substr($columnsSpecialConditions, 0,strlen($columnsSpecialConditions)-1);
			$columnsIsRequired = substr($columnsIsRequired, 0,strlen($columnsIsRequired)-1);
			$columnsIsUnique = substr($columnsIsUnique, 0,strlen($columnsIsUnique)-1);
			$columnsDataType = substr($columnsDataType, 0,strlen($columnsDataType)-1);
			$columnsMaxLength = substr($columnsMaxLength, 0,strlen($columnsMaxLength)-1);
			$columnsCode = substr($columnsCode, 0,strlen($columnsCode)-1);

			$parameterToPass["columnstoquery"]=$columnsToQuery;
			$parameterToPass["columnsfieldname"] = $columnsFieldName;
			$parameterToPass["columnscaption"] = $columnsCaption;
			$parameterToPass["columnsdatasource"] = $columnsDataSource;
			$parameterToPass["columnsdatasourcedatabasename"] = $columnsDataSourceDatabaseName;
			$parameterToPass["columnsdatasourcetablename"] = $columnsDataSourceTableName;
			$parameterToPass["columnsdatasourcevaluepair"] = $columnsDataSourceValuePair;
			$parameterToPass["columnsspecialconditions"] = $columnsSpecialConditions;
			$parameterToPass["columnsdatatype"] = $columnsDataType;
		} // if ($action!="custom_action"){
		

		if ($action=="can_add"||$action=="can_edit"){
			$parameterToPass["columnscode"]=$columnsCode;
			$parameterToPass["columnsisrequired"] = $columnsIsRequired;
			$parameterToPass["columnsisunique"] = $columnsIsUnique;
			$parameterToPass["columnsmaxlength"] = $columnsMaxLength;
		} // if ($action=="can_add"||$action=="can_edit"){

		if ($action=="can_edit"||$action=="can_show"||$action=="custom_action"/*||$action=="custom_action_whole"*/){
			$parameterToPass["primarycodevalue"] = '"+data[0]+"';
			$parameterToPass["primarycodefields"] = $this->PRIMARY_CODE_FIELD_NAMES;
			$parameterToPass["valueviewed"] = '"+encodeURIComponent(data[1])+"';
		} // if ($action=="can_edit"||$action=="can_show"){


		return $parameterToPass; // array
	} // private function buildModalParameterArray($action){

	private function generateJavascriptConstants(){
		$returnVar = "";

		$variableOutput = 'var DATA = new Array();';
		// GET ALL THE COLUMNS THAT IS SET UP TO BE DISPLAY ON TABLE
		foreach ($this->COLUMN_DATA as $columnEntry){

			

			if ($columnEntry["can_display_on_table"]=="1") {
				/*$columnsToQuery = $columnsToQuery . "". $columnEntry["table_field_name"] . "|";
				$columnsDataSource = $columnsDataSource. "". $columnEntry["data_source"] . "|";
				$columnsDataSourceDatabaseName = $columnsDataSourceDatabaseName. "".$columnEntry["data_source_database_name"]."|";
				$columnsDataSourceTableName = $columnsDataSourceTableName."".$columnEntry["data_source_table_name"]."|";
				$columnsDataSourceValuePair = $columnsDataSourceValuePair."".str_replace("|", ",", $columnEntry["data_source_value_pair"])."|";
				$columnsSpecialConditions = $columnsSpecialConditions."".str_replace("|", ",", $columnEntry["special_conditions"])."|";
				$columnsCode = $columnsCode."".$columnEntry["code"]."|";*/


				if ($columnEntry["data_source"]=="1"){
					
					$firstLayerArray = explode("|",$columnEntry["data_source_value_pair"]) ;
					$variableOutput = $variableOutput. 'DATA["'.str_replace("'", "\'", $columnEntry["table_field_name"]).'"] = new Array('.count($firstLayerArray).');';
					$c = 0;
					foreach ($firstLayerArray as $firstLayerValue){
						$secondLayerArray = explode (":",$firstLayerValue);
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c] = new Array(2);";
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][0] = '".str_replace("'", "\'", $secondLayerArray["0"])."';";
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][1] = '".str_replace("'", "\'", $secondLayerArray["1"])."';";
						$c++;
					}
					
					// for 0
					$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c] = new Array(2);";
					$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][0] = '0';";
					$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][1] = 'n/a';";
					$c++;
					
					$returnVar = $returnVar . "" . $variableOutput ;
				} // if ($columnEntry["data_source"]=="1"){
				/*	
				else if ($columnEntry["data_source"]=="2"){
					
					$firstLayerArray = explode(":",$columnEntry["data_source_value_pair"]);
					$variableOutput = $variableOutput. 'DATA["'.str_replace("'", "\'", $columnEntry["table_field_name"]).'"] = new Array('.count($firstLayerArray).');';
					$link = DB_LOCATION;
					$params = array (
						"action" => "retrieve",
						"fileToOpen" => "default_select_query",
						"tableName" => $columnEntry["data_source_table_name"],
						"dbconnect" => $columnEntry["data_source_database_name"],
						"columns" => $firstLayerArray['0'].",".str_replace(" ",",",$firstLayerArray['1']) ,
						"orderby" => $firstLayerArray['0']." ASC"
					);
					$result=processCurl($link,$params);
					$output = json_decode($result,true);
					$c = 0;
					if($output[0]["result"]==='1'){

						foreach ($output as $data_source_key => $data_source_value){
							$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c] = new Array(2);";
							$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][0] = '".str_replace("'", "\'", $data_source_value[$firstLayerArray["0"]])."';";
							$dval = "";
							$valuepair1 = explode(" ",$firstLayerArray['1']);
                            foreach ($valuepair1 as $vp1){
                                $dval = $dval . $data_source_value[$vp1]." - ";
                            }
                            $dval = rtrim($dval," - ");

							$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][1] = '".str_replace("'", "\'", $dval)."';";
							$c++;

						} // foreach ($output as $data_source_key => $data_source_value){


					} // if($output[0]["result"]==='1'){
					
						// for 0
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c] = new Array(2);";
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][0] = '0';";
						$variableOutput = $variableOutput. "DATA['".str_replace("'", "\'", $columnEntry["table_field_name"])."'][$c][1] = 'n/a';";
						$c++;
					
					
				} // else if ($columnEntry["data_source"]=="2"){
				*/	

			} // if ($columnEntry[$action]=="1") {
		} // foreach ($this->COLUMN_DATA as $columnEntry){ 
		$returnVar = $returnVar . "" . $variableOutput ;
		return $returnVar;
	} // private function generateJavascriptConstants(){

	// FUNCTION TO RENDER CUSTOM ACTIONS SPECIFIED IN DATABASE
	private function renderCustomActions($pinch_type=""){
		$lch_outputString = "";
		if (!empty($this->ACTION_DATA)){
			if ($pinch_type=="2"){
				$lch_outputString = "&nbsp;&nbsp;";
				foreach ($this->ACTION_DATA as $larr_Actions){
					if ($larr_Actions["action_type"]==$pinch_type){
						if ($larr_Actions["access_privilege"]==true){
							$lch_outputString = $lch_outputString . '<button type="button" class="btn btn-default btn-xs outer_actions '.$larr_Actions["action_id_prefix"].' " title="'.$larr_Actions["action_name"].'">'.$larr_Actions["action_shortname"].'</button>&nbsp;&nbsp;';
						} // if ($larr_Actions["access_privilege"]==true){
						else {
							$lch_outputString = $lch_outputString . '<div style="display:inline-block" title="You have no '.$larr_Actions["action_name"].' privilege. Please contact system administrator">';
							$lch_outputString = $lch_outputString . '<button type="button" class="btn btn-default btn-xs outer_actions no_privilege" disabled>'.$larr_Actions["action_shortname"].'</button>';
							$lch_outputString = $lch_outputString . '</div>';
						} // if ($larr_Actions["access_privilege"]==true){
					} // if ($larr_Actions["action_type"]=="2"){

				} // foreach ($this->ACTION_DATA as $larr_Actions){
			} // if ($pinch_type=="2"){
			else {
				$lch_outputString = "";
				foreach ($this->ACTION_DATA as $larr_Actions){
					if ($larr_Actions["action_type"]==$pinch_type){
						if ($larr_Actions["access_privilege"]==true){
							$lch_outputString = $lch_outputString . '<button type="button" class="btn btn-default btn-sm outer_actions '.$larr_Actions["action_id_prefix"].' " title="'.$larr_Actions["action_name"].'">'.$larr_Actions["action_shortname"].'</button>';
						} // if ($larr_Actions["access_privilege"]==true){
						else {
							$lch_outputString = $lch_outputString . '<div style="display:inline-block" title="You have no '.$larr_Actions["action_name"].' privilege. Please contact system administrator">';
							$lch_outputString = $lch_outputString . '<button type="button" class="btn btn-default btn-sm outer_actions no_privilege" disabled>'.$larr_Actions["action_shortname"].'</button>';
							$lch_outputString = $lch_outputString . '</div>';
						} // if ($larr_Actions["access_privilege"]==true){
					} // if ($larr_Actions["action_type"]=="2"){

				} // foreach ($this->ACTION_DATA as $larr_Actions){


			} // ELSE ng if ($pinch_type=="2"){
			

		} // if (!empty($this->ACTION_DATA)){
		return $lch_outputString;
	} // private function renderCustomActions(){


	// FUNCTION TO DISPLAY THE CUSTOM INLINE INCLUDE FILES SPECIFIED IN THE DATABASE
	private function renderCustomInlineJSActions(){
		if (!empty($this->ACTION_DATA)){
			foreach ($this->ACTION_DATA as $larr_Actions){
				if ($larr_Actions["custom_php_module_inline_js_filenames"]!="") {
					include_once($larr_Actions["custom_php_module_inline_js_filenames"]);
					
				} // if ($larr_Actions["custom_php_module_inline_js_filenames"]!="" && realpath($larr_Actions["custom_php_module_inline_js_filenames"])!=false) {
				
			} // foreach ($this->ACTION_DATA as $larr_Actions){
		} // if (!empty($this->ACTION_DATA)){
	} // private renderCustomInlineJSActions(){

} // class MasterfileController{
?>