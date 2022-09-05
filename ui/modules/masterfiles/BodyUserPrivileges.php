<?php
date_default_timezone_set('Asia/Manila');
// lookup first for the code being queried
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

// ACTIONS - nilagay sa function para pwede doblehin
$lch_ActionsVar = '';
$lch_ActionsVar = $lch_ActionsVar . '<div class="row"><div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="text-align:left">';
// CHECK FOR PRINT PRIVILEGE
if ($this->HAS_PRINT_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-default outer-fields-unique print_button_unique" disabled><i class="'.$this->BUTTON_ICONS["Print"].'"></i> Print Listing</button>';
} // if ($this->HAS_PRINT_PRIVILEGE==true){
else if ($this->HAS_PRINT_PRIVILEGE==false) {

	$lch_ActionsVar = $lch_ActionsVar . '<div style="display:inline-block" title="You have no Print privilege. Please contact system administrator">';
	$lch_ActionsVar = $lch_ActionsVar . '<button class="btn btn-default no_privilege"  disabled type="button"><i class="'.$this->BUTTON_ICONS["Print"].'"></i> Print Listing</button>';
	$lch_ActionsVar = $lch_ActionsVar . '</div>';

} // else ng if ($this->HAS_PRINT_PRIVILEGE==true){ 
$lch_ActionsVar = $lch_ActionsVar . '</div>';

$lch_ActionsVar = $lch_ActionsVar . '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" style="text-align:right">';
// CHECK FOR EDIT PRIVILEGE
if ($this->HAS_EDIT_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-warning outer-fields-unique edit_button_unique" disabled><i class="'. $this->BUTTON_ICONS["Edit"].'"></i> Edit User Privileges</button>';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-success outer-fields-unique-inverted save_button_unique" data-mode="save" disabled><i class="fa fa-floppy-o"></i> Save Changes</button>';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-default outer-fields-unique-inverted cancel_button_unique" data-mode="cancel" disabled><i class="fa fa-times"></i> Cancel Changes</button>';
} // if ($this->HAS_EDIT_PRIVILEGE==true){
else if ($this->HAS_EDIT_PRIVILEGE==false) {

	$lch_ActionsVar = $lch_ActionsVar . '<div style="display:inline-block" title="You have no Edit privilege. Please contact system administrator">';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-warning no_privilege" disabled><i class="'.$this->BUTTON_ICONS["Edit"].'"></i> Edit User Privileges</button>';
	$lch_ActionsVar = $lch_ActionsVar . '</div>';

} // else ng if ($this->HAS_EDIT==true){ 
$lch_ActionsVar = $lch_ActionsVar . '</div></div>';
		
  		


// DB LOCATION STRING -- http
$lch_DBLocationString = DB_LOCATION;


// GET USERS
$larr_Params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstuser",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "code,username,whole_name" ,
    "orderby" => "username ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_UserList = json_decode($ljson_Result,true);

// GET USER GROUPS
$larr_Params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstusergroup",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "code,short_name,whole_name" ,
    "orderby" => "whole_name ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_UserGroupList = json_decode($ljson_Result,true);


/* ----------------- MENU ITEM RELATED  --------------------------- */
include_once("../api/MenuItemFunctions.php");

global $larr_MenuItemList;
$larr_MenuItemList = array();

/* recursive menu listing */
function displayMenu2 ($parent_code="0",$first_time=false,$parent_name="",$prog=""){
    global $menuCount;
    global $larr_MenuItemList;

    $noParent = true;
    if ($parent_code=="0"&&!$first_time){
        return;   
    } // if ($parent_code=="0"&&!$first_time){
    else {
        $menu = menuListAll($parent_code);
        if (!is_null($menu)){
            if ($parent_code=="0"){
                array_push($larr_MenuItemList,array("code"=>$parent_code,"menu_name"=>"Menu"));
                $parent_name = "Menu";
            } // if ($parent_code=="0"){
            foreach ($menu as $key => $value){
                if ($value["type"]=="1"){
                    if (canDisplayAll($value["code"])){
                        $noParent = false;
                        if (canDisplayNonRecursiveAll($value["code"])){
                        	array_push($larr_MenuItemList,array("code"=>$value["code"],"menu_name"=>$parent_name . " / " .$value["menu_name"]));
                    	} // if (canDisplayNonRecursive($value["code"])){
                        displayMenu2 ($value["code"],false,$parent_name . " / " .$value["menu_name"],$prog);
                    } // if (canDisplay($value["code"])){
                } // if ($value["type"]=="1"){
            } // foreach ($menu as $key => $value){
        } // if (!is_null($menu)){

        if ($noParent==true){
            return;
        } // if ($noParent==true){
    } // else ng if ($parent_code=="0"&&!$first_time){
} // function displayMenu2 ($parent_code="0",$first_time=false,$parent_name="",$prog=""){

function getMenuArray(){
    global $larr_MenuItemList;
    return $larr_MenuItemList;
}
displayMenu2("0",true,"","");
$larr_MenuItems = getMenuArray();
/* ----------------- END - MENU ITEM RELATED  --------------------------- */


?>
<form class="form-horizontal" method="post" id="form_masterfile_unique">

	<input type="hidden" name="modulemstcode" value="<?php echo $this->MODULE_MST_CODE; ?>">
	<input type="hidden" name="menuitemmstcode" value="<?php echo $this->MENU_ITEM_MST_CODES; ?>">

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="form-group">
				<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Type</label>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	        		<select class="form-control input-sm filter-fields-unique outer-fields-unique" 
	        			data-required="1" data-fieldname="Type"
	        			id="privilege_type" name="privilege_type">
	        			<option value=""> - Select a Type - </option>
        				<option value="1"> User </option>
        				<option value="2"> Groups </option>
	            	</select>
	            </div> <!-- /.col-lg-10 col-md-10 col-sm-10 col-xs-10 -->
			</div> <!-- /.form-group -->

		</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="form-group">
				<?php /*
				<label for="" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 control-label">Type Code</label>
				*/ ?>
	            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	        		<select class="form-control input-sm filter-fields filter-fields-unique outer-fields-unique" 
	        			data-required="1" data-fieldname="Type Code" id="type_code" name="type_code" data-chainingparentid="privilege_type">
	        			<option value="" class=""> - - - - - </option>
	        			<?php foreach ($larr_UserGroupList as $lch_Key => $lch_RecordRow) {
	        			?>
	        				<option value="<?php echo $lch_RecordRow['code'];?>" class="2">
	        					<?php echo $lch_RecordRow['short_name'] . ' - ' .$lch_RecordRow['whole_name'];?>
        					</option>
	        			<?php	
        				} // foreach ($larr_UserGroupList as $lch_Key => $lch_Value) { ?>

        				<?php foreach ($larr_UserList as $lch_Key => $lch_RecordRow) {
	        			?>
	        				<option value="<?php echo $lch_RecordRow['code'];?>" class="1">
	        					<?php echo $lch_RecordRow['username'] . ' - ' .$lch_RecordRow['whole_name'];?>
        					</option>
	        			<?php	
        				} // foreach ($larr_UserList as $lch_Key => $lch_Value) { ?>
	            	</select>
	            </div> <!-- /.col-lg-10 col-md-10 col-sm-10 col-xs-10 -->
			</div> <!-- /.form-group -->

		</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="form-group">
				<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label" style="padding-left:0px">Parent Menu</label>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	        		<select class="form-control input-sm filter-fields filter-fields-unique outer-fields-unique" 
	        			data-required="1" data-fieldname="Parent Menu" id="menu_item_mst_code" name="menu_item_mst_code">
	        				<option value="">- - - - -</option>
	        			<?php foreach ($larr_MenuItems as $lch_Key => $lch_RecordRow) {
	        			?>
	        				<option value="<?php echo $lch_RecordRow['code'];?>"><?php echo $lch_RecordRow['menu_name'];?></option>
	        			<?php	
        				} // foreach ($larr_UserGroupList as $lch_Key => $lch_Value) { ?>
	            	</select>
	            </div> <!-- /.col-lg-9 col-md-9 col-sm-9 col-xs-9 -->
			</div> <!-- /.form-group -->

		</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->

		

		<!-- FILTER / CLEAR BUTTONS -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center">
			<button type="button" class="btn btn-info outer-fields-unique" id="filter_button_unique"><i class="fa fa-filter"></i> Filter User Privileges</button>
	  		<button type="button" class="btn btn-default outer-fields-unique" id="clear_button_unique"><i class="fa fa-times"></i> Clear Filters</button>

		</div>
	</div> <!-- /.row -->


	<hr style="margin-top:5px;margin-bottom:5px">

	<!-- MAIN DISPLAY -->
	<div class="row" id="agent_rate_container">
		<div class="alert alert-info prompt_containers" id="newlyloaded_prompt_container">
            <h3>Please complete the filters above to show the User Privileges.</h3>
        </div> <!-- #modal_error_container -->

        <div class="alert alert-danger prompt_containers" id="error_prompt_container" style="display:none">
            <h3 id="error_prompt_message"></h3>
        </div> <!-- #modal_error_container -->

        <div class="prompt_containers" id="loading_prompt_container" style="display:none">
        	<h1><i class="fa fa-spinner fa-spin"></i></h1>
        	<h3 id="loading_message">Loading.. Please wait </h3>
        </div> <!-- #loading_prompt_container -->

        <div class="alert alert-danger" id="outer_error_below_container" style="display:none">
            <button type="button" class="close" id="outer_error_below_close">&times;</button>
            <h5 id="outer_error_below_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>
            
            <div id="outer_error_below_container_content">

            </div> <!-- #outer_error_container_below_content -->
            
        </div> <!-- #outer_error_container -->

        <div class="alert alert-success" id="outer_success_below_container" style="text-align:center;display:none">
            <button type="button" class="close" id="outer_success_below_close">&times;</button>
            <h4 style="margin-bottom:0px"><strong id="outer_success_below_message">X successfully saved.</strong></h4>
        </div>

        <div id="main_result_container" style="display:none">
        	<?php echo $lch_ActionsVar; ?>
			<div id="data_container">

	        </div> <!-- col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
	        <br><br><Br><br>
	        <?php echo $lch_ActionsVar; ?>
        </div>


	</div> <!-- /.row -->


</form> <!-- /#form_masterfile_unique -->