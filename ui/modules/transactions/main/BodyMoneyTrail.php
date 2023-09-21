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

	$larr_TrailType = array ("1"=>"Expense","2"=>"Income");

	$larr_PaidStatus = array ("1"=>"Paid","0"=>"Unpaid");

	$larr_MSTMoneyTrailTypeAccount = array();

	$larr_Params = array (
		"action" => "retrieve",
		"fileToOpen" => "default_select_query",
		"tableName" => "mstmoneytrailtype",
		"dbconnect" => MONEYTRACKER_DB,
		"columns" => "code,trail_type,short_name,money_trail_name,description,reference_no" ,
		"conditions[equals][created_user_mst_code]" => $_SESSION["user_code"],
		"conditions[equals][is_active]"=>1,
		"orderby" => "order_no, trail_type asc, short_name ASC, code ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_MSTMoneyTrailType = json_decode($ljson_Result,true);

	$larr_Params = array (
		"action" => "retrieve",
		"fileToOpen" => "default_select_query",
		"tableName" => "mstmoneytrailtype",
		"dbconnect" => MONEYTRACKER_DB,
		"columns" => "code,trail_type,short_name,money_trail_name,description,reference_no" ,
		"conditions[equals][trail_type]" => "2",
		//"conditions[in][code]" => $larr_AppParams["income_accounts_mst_codes"],
		"conditions[equals][created_user_mst_code]" => $_SESSION["user_code"],
		"conditions[equals][is_active]"=>1,
		"conditions[equals][show_in_account_to_deduct]"=>1,
		"orderby" => "order_no asc, trail_type asc, short_name ASC, code ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_MSTMoneyTrailTypeAccount = json_decode($ljson_Result,true);

	$larr_MSTReference = array();
	$larr_Params = array (
		"action" => "retrieve",
		"fileToOpen" => "default_select_query",
		"tableName" => "mstreference",
		"dbconnect" => MONEYTRACKER_DB,
		"columns" => "code,reference_name" ,
		"conditions[equals][created_user_mst_code]" => $_SESSION["user_code"],
		"conditions[equals][is_active]"=>1,
		"orderby" => "reference_name ASC"
	);
	$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
	$larr_MSTReference = json_decode($ljson_Result,true);

	$start_year=2017;

	$larr_PeriodMonths = array(1=>"January",
		2=>"February",
		3=>"March",
		4=>"April",
		5=>"May",
		6=>"June",
		7=>"July",
		8=>"August",
		9=>"September",
		10=>"October",
		11=>"November",
		12=>"December");


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
			  	<button type="button" class="toggle_filter close " data-state="show" title="Hide Filter"><i  class="botton-icon-display fa fa-chevron-down" style="display:none" ></i><i  class="botton-icon-display fa fa-chevron-up " ></i></button>

			    <h2 class="panel-title">Filters</h2>
			  </div>
			  <div class="panel-body">

				<div class="row">

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Payment Status</label>
				            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
				        		<select class="form-control input-sm outer-fields-unique-inverted filter-fields"
				        			data-required="0" data-fieldname="Payment Status"
				        			id="payment_status" name="payment_status">
				        			<option value=""> - All Payment Status - </option>
				        			<?php foreach ($larr_PaidStatus as $lch_Key => $lch_RecordRow) {
				        			?>
					        				<option value="<?php echo $lch_Key;?>" class="">
					        					<?php echo $lch_RecordRow;?>
				        					</option>
				        			<?php
									} // foreach ($larr_PaidStatus as $lch_Key => $lch_RecordRow) { ?>
				            	</select>
				            </div> <!-- /.col-lg-9 col-md-9 col-sm-9 col-xs-9 -->
						</div> <!-- /.form-group -->
					</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->
					

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="booking_year" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Booking Period</label>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="booking_year_container">
								<select class="form-control input-sm outer-fields-unique-inverted filter-fields" name="booking_year" id="booking_year">
									<option value=""> - Any Year - </option>
									<?php
									for ($ctr=$start_year;$ctr<=date("Y")+1;$ctr++) {
									?>
										<option value="<?php echo $ctr;?>"
										><?php echo $ctr;?></option>
									<?php
									} // for ($ctr=$start_year;$ctr<date("Y")+1;$ctr++) {
									?>
								</select>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="booking_period_container">
								<select class="form-control input-sm outer-fields-unique-inverted filter-fields" name="booking_period" id="booking_period">
									<option value=""> - Any Month - </option>
									<?php
									for ($ctr=1;$ctr<13;$ctr++) {
									?>
										<option value="<?php echo $ctr;?>"
										><?php echo $larr_PeriodMonths[$ctr];?></option>
									<?php
									} // for ($ctr=$start_year;$ctr<date("Y")+1;$ctr++) {
									?>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Reference</label>
				            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
				        		<select class="form-control input-sm outer-fields-unique-inverted filter-fields"
				        			data-required="0" data-fieldname="Reference"
				        			id="reference_mst_code" name="reference_mst_code">
				        			<option value=""> - All Reference - </option>
				        			<?php foreach ($larr_MSTReference as $lch_Key => $lch_RecordRow) {
				        			?>
					        				<option value="<?php echo $lch_RecordRow["code"];?>" class="">
					        					<?php echo $lch_RecordRow["reference_name"];?>
				        					</option>
				        			<?php
									} // foreach ($larr_MSTReference as $lch_Key => $lch_RecordRow) { ?>
				            	</select>
				            </div> <!-- /.col-lg-9 col-md-9 col-sm-9 col-xs-9 -->
						</div> <!-- /.form-group -->

					</div>

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="money_trail_type" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Trail</label>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="money_trail_type_container">
								<select class="form-control input-sm outer-fields-unique-inverted filter-fields" name="money_trail_type" id="money_trail_type">
									<option value=""> - All Trail Type - </option>
									<?php foreach ($larr_TrailType as $lch_Key => $lch_RecordRow) {
									?>
											<option value="<?php echo $lch_Key;?>" 
											>
												<?php echo $lch_RecordRow;?>
											</option>
									<?php   
									} // foreach ($larr_TrailType as $lch_Key => $lch_Value) { ?>
								</select>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="money_trail_type_mst_code_container">
								<select class="form-control input-sm outer-fields-unique-inverted filter-fields" name="money_trail_type_mst_code" id="money_trail_type_mst_code">
									<option value=""> - All Trail - </option>
									<?php foreach ($larr_MSTMoneyTrailType as $lch_Key => $lch_RecordRow) {
									?>
											<option value="<?php echo $lch_RecordRow['code'];?>" 
												class="<?php echo $lch_RecordRow['trail_type'];?>">
												<?php echo $lch_RecordRow['money_trail_name'];?>
											</option>
									<?php   
									} // foreach ($larr_MSTMoneyTrailType as $lch_Key => $lch_Value) { ?>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Account to Deduct</label>
				            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
				        		<select class="form-control input-sm outer-fields-unique-inverted filter-fields"
				        			data-required="0" data-fieldname="Account to Deduct"
				        			id="account_money_trail_type_mst_code" name="account_money_trail_type_mst_code">
				        				<option value=""> - All Account to Deduct - </option>
										<?php foreach ($larr_MSTMoneyTrailTypeAccount as $lch_Key => $lch_RecordRow) {
										?>
												<option value="<?php echo $lch_RecordRow['code'];?>" 
													class="<?php echo $lch_RecordRow['trail_type'];?>">
													<?php echo $lch_RecordRow['money_trail_name'];?>
												</option>
										<?php   
										} // foreach ($larr_MSTMoneyTrailTypeAccount as $lch_Key => $lch_Value) { ?>
				            	</select>
				            </div> <!-- /.col-lg-9 col-md-9 col-sm-9 col-xs-9 -->
						</div> <!-- /.form-group -->

					</div>

				</div> <!-- /.row -->


			</div> <!-- .panel-body -->
			<div class="panel-footer" >
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