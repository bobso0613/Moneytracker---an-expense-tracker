<?php
date_default_timezone_set('Asia/Manila');
// lookup first for the code being queried
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

@session_start();

// ACTIONS - nilagay sa function para pwede doblehin
$lch_ActionsVar = '';

// LEFT SIDE
$lch_ActionsVar = $lch_ActionsVar . '<div class="row"><div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:left;">';
// CHECK FOR ADD PRIVILEGE
if ($this->HAS_ADD_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" data-loading-text="Loading..." autocomplete="off" class="btn btn-primary btn-sm outer-fields-unique add_interim"><i class="'. $this->BUTTON_ICONS["Add"].'"></i> Add Trail</button>';

} // if ($this->HAS_ADD_PRIVILEGE==true){
else if ($this->HAS_ADD_PRIVILEGE==false) {


} // else ng if ($this->HAS_ADD_PRIVILEGE==true){


// CHECK FOR EDIT PRIVILEGE
if ($this->HAS_EDIT_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<button type="button" data-loading-text="Loading..." autocomplete="off" disabled class="btn btn-warning btn-sm outer-fields-unique process_interim"><i class="'. $this->BUTTON_ICONS["Edit"].'"></i> Edit </button>';
} // if ($this->HAS_EDIT_PRIVILEGE==true){
else if ($this->HAS_EDIT_PRIVILEGE==false) {


} // else ng if ($this->HAS_EDIT_PRIVILEGE==true){

// CHECK FOR DELETE PRIVILEGE
if ($this->HAS_DELETE_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<button type="button" data-loading-text="Loading..." autocomplete="off" disabled class="btn btn-danger btn-sm outer-fields-unique delete_interim"><i class="'. $this->BUTTON_ICONS["Delete"].'"></i> Delete </button>';
} // if ($this->HAS_DELETE_PRIVILEGE==true){
else if ($this->HAS_DELETE_PRIVILEGE==false) {


} // else ng if ($this->HAS_DELETE_PRIVILEGE==true){


// // CHECK FOR ROUTE PRIVILEGE
// if ($this->HAS_ROUTE_PRIVILEGE==true){
// 	// ROUTE (UNDER CONSTRUCTION)
// 	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<button type="button" data-loading-text="Loading..." autocomplete="off" disabled class="btn btn-default btn-sm outer-fields-unique route_interim"><i class="'.$this->BUTTON_ICONS["Route"].'"></i> Route </button>';
// } // if ($this->HAS_ROUTE_PRIVILEGE==true){
// else if ($this->HAS_ROUTE_PRIVILEGE==false) {

// 	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<div style="display:inline-block" title="You have no Route privilege. Please contact system administrator">';
// 	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-default btn-sm no_privilege" disabled><i class="'.$this->BUTTON_ICONS["Route"].'"></i> Route </button>';
// 	$lch_ActionsVar = $lch_ActionsVar . '</div>';

// } // else ng if ($this->HAS_ROUTE_PRIVILEGE==true){



$lch_ActionsVar = $lch_ActionsVar . '</div>';
// END - LEFT SIDE

// RIGHT SIDE
$lch_ActionsVar = $lch_ActionsVar . '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:right;">';



// CHECK FOR PRINT PRIVILEGE
if ($this->HAS_PRINT_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<button type="button" data-loading-text="Loading..." autocomplete="off" disabled class="btn btn-default btn-sm outer-fields-unique print_interim shortcut_print"><i class="'. $this->BUTTON_ICONS["Print"].'"></i> Preview </button>';
} // if ($this->HAS_PRINT_PRIVILEGE==true){
else if ($this->HAS_PRINT_PRIVILEGE==false) {

	$lch_ActionsVar = $lch_ActionsVar . '&nbsp;&nbsp;<div style="display:inline-block" title="You have no Print privilege. Please contact system administrator">';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-default btn-sm no_privilege" disabled><i class="'.$this->BUTTON_ICONS["Print"].'"></i> Preview </button>';
	$lch_ActionsVar = $lch_ActionsVar . '</div>';

} // else ng if ($this->HAS_PRINT_PRIVILEGE==true){

$lch_ActionsVar = $lch_ActionsVar . '</div></div><br>';
// END - RIGHT SIDE





$lch_RecordsButtons = '';

// DB LOCATION STRING -- http
$lch_DBLocationString = DB_LOCATION;

/*
*	GET FILTER DATA
*/

	// GET USER ACCESSIBLE BRANCHES AND LINES
	$larr_Params = array (
	    "action" => "retrieve",
	    "fileToOpen" => "default_select_query",
	    "tableName" => "mstuser",
	    "dbconnect" => MONEYTRACKER_DB,
	    "columns" => "code" ,
	    "conditions[equals][code]" => $_SESSION["user_code"],
	    "orderby" => "code ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_UserDetails = json_decode($ljson_Result,true);


?>
<form class="form-horizontal" method="post" id="form_masterfile_unique">

	<input type="hidden" name="modulemstcode" value="<?php echo $this->MODULE_MST_CODE; ?>">
	<input type="hidden" name="menuitemmstcode" value="<?php echo $this->MENU_ITEM_MST_CODES; ?>">
	<input type="hidden" id="created_user_mst_code" value="<?php echo $larr_UserDetails[0]["code"] ;?>" name="created_user_mst_code"/>

	<div class="alert alert-danger prompt_containers" id="error_prompt_container" style="display:none">
        <h3 id="error_prompt_message"></h3>
    </div> <!-- #modal_error_container -->

    <div class="prompt_containers" id="loading_prompt_container" style="display:none">
    	<h1><i class="fa fa-spinner fa-spin"></i></h1>
    	<h3 id="loading_message">Loading.. Please wait </h3>
    </div> <!-- #loading_prompt_container -->

    
    <!-- MAIN INDEX TABLE (TABLE,ACTIONS, FILTERS) - (TO BE HIDDEN ON TRANSACTION PROCESS) -->
	<div id="index_container">
		<!-- FILTER PANEL -->
		<div class="panel panel-default">
			  <div class="panel-heading">
			  	<button type="button" class="toggle_filter close " data-state="show" title="Hide Filter"><i  class="botton-icon-display fa fa-chevron-down" ></i><i  class="botton-icon-display fa fa-chevron-up " style="display:none" ></i></button>

			    <h2 class="panel-title">Filters</h2>
			  </div>
			  <div class="panel-body" style="display:none">

				<div class="row">

					


				</div> <!-- /.row -->


			</div> <!-- .panel-body -->
			<div class="panel-footer" style="display:none" >
			  		<button type="button" data-loading-text="Loading..." autocomplete="off" class="btn btn-info outer-fields-unique" id="filter_button_unique"><i class="fa fa-filter"></i> Filter </button>
	  				<button type="button" data-loading-text="Loading..." autocomplete="off" class="btn btn-default outer-fields-unique" id="clear_button_unique"><i class="fa fa-times"></i> Clear Filters</button>
			</div> <!-- .panel-footer -->
		</div>
		<!-- END - FILTER PANEL -->


		<hr style="margin-top:5px;margin-bottom:5px">

		<div class="alert alert-danger" id="outer_error_below_container" style="display:none">
	        <button type="button" class="close" id="outer_error_below_close">&times;</button>
	        <h5 id="outer_error_below_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>

	        <div id="outer_error_below_container_content">

	        </div> <!-- #outer_error_container_below_content -->

	    </div> <!-- #outer_error_container -->

		<div class="alert alert-success" id="outer_success_below_container" style="text-align:center;display:none">
	        <button type="button" class="close" id="outer_success_below_close">&times;</button>
	        <h4 style="margin-bottom:0px"><strong id="outer_success_below_message"></strong></h4>
	    </div>


		<!-- MAIN DISPLAY -->
		<div class="row" id="interim_container">
	    	<!-- TABLES AND OUTSIDE ACTIONS - WILL BE DISPLAY ON PRE-LOAD (DEFAULT IS INDEX) -->
			<div id="data_container" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php echo $lch_ActionsVar; ?>
				<div class="table-responsive" style="">
					<table class="table table-bordered table-large-font compact  table-condensed table-small-font" id="transaction_table" >
						<thead>
							<tr>
								<th class="table-header" title="">Code</th>
								<th class="table-header" title="">Trail Date</th>
								<th class="table-header" title="Trail No"  style="white-space:nowrap" >Trail no.</th>
								<th class="table-header" title="Trail Type"  style="" >Trail Type</th>
								<th class="table-header" title="Description"  style="" >Description</th>
								<th class="table-header" title="Reference" style="">Reference</th>
								<th class="table-header">Total Amount</th>
								<th class="table-header">No. of Items</th>
								<th class="table-header">Account to Deduct</th>
								<th class="table-header">is_paid</th>
								<th class="table-header">paid_user_mst_code</th>
								<th class="table-header">paid_at</th>

							</tr>
						</thead>
					</table>
				</div>
				 <?php echo $lch_ActionsVar; ?>
	        </div> <!-- #data_container -->
	        <!-- END - TABLES AND OUTSIDE ACTIONS -->
		</div> <!-- /.row #interim_container -->


	</div> <!-- #index_container -->
	<!-- END - MAIN INDEX TABLE (TABLE,ACTIONS, FILTERS) - (TO BE HIDDEN ON TRANSACTION PROCESS) -->


 	<!-- ACTUAL TRANSACTION FIELDS  (DYNAMICALLY LOADED - ON A SEPARATE FILE) -->
   <div id="transaction_container" class="row" style="display:none">
   </div> <!-- #transaction_container -->
   <!-- END - ACTUAL TRANSACTION FIELDS -->



</form> <!-- /#form_masterfile_unique -->