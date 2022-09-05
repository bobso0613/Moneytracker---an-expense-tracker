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

$lch_ActionsVar = $lch_ActionsVar . '</div>';

$lch_ActionsVar = $lch_ActionsVar . '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" style="text-align:right">';
// CHECK FOR EDIT PRIVILEGE
if ($this->HAS_EDIT_PRIVILEGE==true){
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-warning outer-fields-unique edit_button_unique" disabled><i class="'. $this->BUTTON_ICONS["Edit"].'"></i> Edit Budget</button>';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-success outer-fields-unique-inverted save_button_unique" data-mode="save" disabled><i class="fa fa-floppy-o"></i> Save Changes</button>';
	$lch_ActionsVar = $lch_ActionsVar . '<button type="button" class="btn btn-default outer-fields-unique-inverted cancel_button_unique" data-mode="cancel" disabled><i class="fa fa-times"></i> Cancel Changes</button>';
} // if ($this->HAS_EDIT_PRIVILEGE==true){

$lch_ActionsVar = $lch_ActionsVar . '</div></div>';
		
// DB LOCATION STRING -- http
$lch_DBLocationString = DB_LOCATION;

$larr_MSTMoneyTrailType = array();


// GET USER ACCESSIBLE BRANCHES AND LINES
$larr_Params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstmoneytrailtype",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "*" ,
    "conditions[equals][created_user_mst_code]" => $_SESSION["user_code"],
    // "conditions[equals][trail_type]" => 1,
    "conditions[equals][is_active]" => 1,
    "orderby" => "trail_type asc, order_no ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_MSTMoneyTrailType = json_decode($ljson_Result,true);

$larr_TrailType = array("1"=>"Expense","2"=>"Income");


?>
<form class="form-horizontal" method="post" id="form_masterfile_unique">

	<input type="hidden" name="modulemstcode" value="<?php echo $this->MODULE_MST_CODE; ?>">
	<input type="hidden" name="menuitemmstcode" value="<?php echo $this->MENU_ITEM_MST_CODES; ?>">

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="form-group">
				<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Money Trail</label>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	        		<select class="form-control input-sm filter-fields-unique outer-fields-unique" 
	        			data-required="1" data-fieldname="Money Trail"
	        			id="money_trail_type_mst_code" name="money_trail_type_mst_code">
	        			<option value=""> - Select a Money Trail - </option>
        				<?php
        				if (count($larr_MSTMoneyTrailType)>0 && $larr_MSTMoneyTrailType[0]["result"]=="1") {
        					foreach ($larr_MSTMoneyTrailType as $lch_Key => $larr_Value) {
        						$lch_ActiveTag = "";
        						if ($larr_Value["is_active"]!="1") {
        							$lch_ActiveTag = "(Inactive) ";
        						} // if ($larr_Value["is_active"]!="1") {
        					?>
        						<option value="<?php echo $larr_Value["code"];?>"><?php echo $lch_ActiveTag.$larr_Value["money_trail_name"] . " (".$larr_TrailType[$larr_Value["trail_type"]].")";?></option>
        					<?php
        					} // foreach ($larr_MSTMoneyTrailType as $lch_Key => $larr_Value) {
        				} // if (count($larr_MSTMoneyTrailType)>0 && $larr_MSTMoneyTrailType[0]["result"]=="1") {
        				?>
	            	</select>
	            </div> <!-- /.col-lg-10 col-md-10 col-sm-10 col-xs-10 -->
			</div> <!-- /.form-group -->

		</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="form-group">
				<label for="" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Booking Year</label>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	        		<select class="form-control input-sm filter-fields-unique outer-fields-unique" 
	        			data-required="1" data-fieldname="Booking Year"
	        			id="booking_year" name="booking_year">
	        			<option value=""> - Select a Booking Year - </option>
        				<?php
        				for ($lin_ctr=2017;$lin_ctr<=date("Y")+1;$lin_ctr++) {
        				?>
        					<option value="<?php echo $lin_ctr;?>" 
        						<?php if($lin_ctr==date("Y")) { echo ' selected ';} ?>><?php echo $lin_ctr;?></option>
        				<?php
        				} // for ($lin_ctr=2017;$lin_ctr<=date("Y")+1;$lin_ctr++) {
        				?>
	            	</select>
	            </div> <!-- /.col-lg-10 col-md-10 col-sm-10 col-xs-10 -->
			</div> <!-- /.form-group -->

		</div> <!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->


		<!-- FILTER / CLEAR BUTTONS -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center">
			<button type="button" class="btn btn-info outer-fields-unique" id="filter_button_unique"><i class="fa fa-filter"></i> Filter Budget</button>
	  		<button type="button" class="btn btn-default outer-fields-unique" id="clear_button_unique"><i class="fa fa-times"></i> Clear Filters</button>

		</div>
	</div> <!-- /.row -->


	<hr style="margin-top:5px;margin-bottom:5px">

	<!-- MAIN DISPLAY -->
	<div class="row" id="agent_rate_container">
		<div class="alert alert-info prompt_containers" id="newlyloaded_prompt_container">
            <h3>Please complete the filters above to show the Budget.</h3>
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