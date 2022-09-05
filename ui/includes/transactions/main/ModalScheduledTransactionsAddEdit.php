<?php
date_default_timezone_set('Asia/Manila');


// lookup first for the code being queried
require_once("../../../api/SystemConstants.php");
require_once("../../../api/CurlAPI.php");

$lch_modalheaderlabel = " Add Scheduled Transaction";
$lch_transactionmode = "";

// DB LOCATION STRING -- http
$lch_DBLocationString = DB_LOCATION;

session_start();

// RETRIEVE APPLICATION PARAMETERS
$larr_AppParams = array();
// get parameters
$larr_Params = array (
    "action" => "retrieve-template-columns",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstapplicationparameter",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "parameter_key,parameter_value",
    "conditions[in][parameter_key]" => 'income_accounts_mst_codes',
    "orderby" => "code ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_OutputParams = json_decode($ljson_Result,true);
if($larr_OutputParams[0]["result"]==='1'){
    foreach ($larr_OutputParams as $lch_Key => $larr_Value){
        $larr_AppParams[$larr_Value["parameter_key"]] = $larr_Value["parameter_value"];
    } // foreach ($larr_OutputParams as $lch_Key => $larr_Value){

} // if($larr_OutputParams[0]["result"]==='1'){

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

$larr_MSTScheduledTransactions = array();
$larr_MSTScheduledTransactionsDTLItems = array();
$llo_editmode = false;
if (@$_GET["transactionmode"]=="edit") {
    $llo_editmode = true;

    // get detail
    $larr_Params = array (
        "action" => "retrieve",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstscheduledtransactions",
        "dbconnect" => MONEYTRACKER_DB,
        "conditions[equals][code]" => @$_GET["scheduled_transactions_mst_code"],
        "columns" => "*" ,
        "orderby" => "code ASC"
    );
    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
    $larr_MSTScheduledTransactions = json_decode($ljson_Result,true);

    $larr_Params = array (
        "action" => "retrieve",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstscheduledtransactionsdtlitems",
        "dbconnect" => MONEYTRACKER_DB,
        "conditions[equals][scheduled_transactions_mst_code]" => $larr_MSTScheduledTransactions[0]["code"],
        "columns" => "*" ,
        "orderby" => "code ASC"
    );
    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
    $larr_MSTScheduledTransactionsDTLItems = json_decode($ljson_Result,true);

    $lch_modalheaderlabel = "Edit Scheduled Transaction - <strong>".$larr_MSTScheduledTransactions[0]["template_name"]."</strong>";
} // if (@$_GET["transactionmode"]=="edit") {






$notes = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 right transaction-guides">
            <span class="required" title="This field should not be blank."><strong><span class="asterisk">*</span></strong> Required Fields</span>
        </div>';

        /*$notes = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 right transaction-guides">
            <span class="required" title="This field should not be blank."><strong><span class="asterisk">*</span></strong> Required Fields</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="unique" title="This field must be unique."><strong><span class="asterisk">*</span></strong> Unique Fields</span>
        </div>';*/

$required_tag = '<strong><span class="asterisk required">*</span></strong>';
$unique_tag = '<strong><span class="asterisk unique">*</span></strong>';

$llo_first = true;
$lin_firstlinecode = 0;


$larr_Frequency = array(0=>"Once",
    1=>"Daily",
    2=>"Weekly",
    3=>"Bi-Monthly",
    4=>"Monthly",
    5=>"Annually");

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


<div class="modal fade medium-modal" id="AddInterimModal" tabindex="-1" role="dialog" aria-labelledby="AddInterimModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
        <form class="form-horizontal" method="post" id="form_masterfile_dynamic">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancel_modal_action" title="Cancel and Close this modal"  data-trans="has_callback_function" data-mode="cancel">&times;</button>
                <h3 class="modal-title" id="AddInterimModalLabel"><?php echo $lch_modalheaderlabel;?></h3>
            </div>
            <div class="modal-body">
                
                <div class="alert alert-danger" id="modal_error_container" style="display:none" tabindex="0" >
                    <button type="button" class="close" id="modal_error_close">&times;</button>
                    <h5 id="modal_error_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>
                    
                    <div id="modal_error_container_content">

                    </div> <!-- #modal_error_container_content -->
                    
                </div> <!-- #modal_error_container -->

                <div class="alert alert-success" id="modal_success_container" style="text-align:center;display:none" >
                    <button type="button" class="close" id="modal_success_close">&times;</button>
                    <h4 style="margin-bottom:0px"><strong id="modal_success_message"></strong></h4>
                </div>


                <?php echo $notes;?>
                <br>

                <div class="alert alert-info">
                    <strong>Scheduled Transactions are processed automatically every 9:00 am GMT+8</strong>
                </div>

                <h4>Schedule Details</h4>

                <div class="form-group">
                    <label for="mstscheduledtransactions_is_active" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Status</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_is_active_container">
                        <div class="radio-group">
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_is_active_1" class="mstscheduledtransactions_is_active" name="mstscheduledtransactions_is_active" value="1"
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["is_active"]=="1" || !$llo_editmode) { echo ' checked ';} ?>
                                   />
                                  Active
                            </label>
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_is_active_1" class="mstscheduledtransactions_is_active" name="mstscheduledtransactions_is_active" value="0" 
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["is_active"]=="0") { echo ' checked ';} ?>
                                  />
                                  Inactive
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_template_name" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Template Name</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_template_name_container">
                        <textarea class="form-control input-sm" rows="1" id="mstscheduledtransactions_template_name" maxlength="500"
                            name="mstscheduledtransactions_template_name"><?php if ($llo_editmode){echo $larr_MSTScheduledTransactions[0]["template_name"] ;}?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_start_date" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Start Date</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_start_date_container">
                        <input type="text" class="form-control input-sm <?php if (!$llo_editmode) { echo ' datepickers '; } ?>" name="mstscheduledtransactions_start_date" id="mstscheduledtransactions_start_date" <?php if ($llo_editmode) { echo ' readonly '; } ?>
                        value="<?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["start_date"]!="0000-00-00") {
                                            echo date_format(date_create($larr_MSTScheduledTransactions[0]["start_date"]),"m-d-Y");
                                    }else {echo date("m-d-Y");} ?>"/>
                    </div>
                </div>

                <?php 
                if ($llo_editmode) {
                ?>
                    <div class="form-group">
                        <label for="" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Previous Run Date</label>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="">
                            <input type="text" class="form-control input-sm" name="" id="" disabled 
                            value="<?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["previous_run_date"]!="0000-00-00") {
                                                echo date_format(date_create($larr_MSTScheduledTransactions[0]["previous_run_date"]),"m-d-Y");
                                        }else {echo "n/a";} ?>"/>
                        </div>
                    </div>
                <?php
                } //if (!$llo_editmode) {s
                ?>


                <?php 
                if ($llo_editmode) {
                ?>
                    <div class="form-group">
                        <label for="" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Frequency</label>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="">
                            <input type="text" class="form-control input-sm" name="" id="" disabled 
                            value="<?php if ($llo_editmode) {
                                                echo $larr_Frequency[$larr_MSTScheduledTransactions[0]["frequency"]];
                                        }?>"/>
                        </div>
                    </div>
                <?php
                } //if (!$llo_editmode) {s
                else {
                ?>
                    <div class="form-group">
                        <label for="mstscheduledtransactions_frequency" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Frequency</label>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_frequency_container">
                            <select class="form-control input-sm"  id="mstscheduledtransactions_frequency"
                                name="mstscheduledtransactions_frequency">
                                <option value="">- - Select a Frequency - -</option>
                                <?php foreach ($larr_Frequency as $lch_Key => $lch_Value) {
                                ?>
                                        <option value="<?php echo $lch_Key;?>" 
                                        <?php if ($llo_editmode && $lch_Key==$larr_MSTScheduledTransactions[0]["frequency"]) {
                                            echo ' selected ';
                                        } // if ($llo_editmode && $lch_Key==$larr_MSTScheduledTransactions[0]["larr_Value"]) { ?>
                                        >
                                            <?php echo $lch_Value;?>
                                        </option>
                                <?php   
                                } // foreach ($larr_Frequency as $lch_Key => $lch_Value) { ?>
                            </select>
                        </div>
                    </div>
                <?php
                } // ELSE ng if (!$llo_editmode) {s
                ?>

                <?php 
                if ($llo_editmode) {
                ?>
                    <div class="form-group">
                        <label for="" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Current Process Count</label>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="">
                            <input type="text" class="form-control input-sm" name="" id="" disabled 
                            value="<?php if ($llo_editmode) {
                                                echo $larr_MSTScheduledTransactions[0]["current_process_count"];
                                        }else {echo "0";} ?>"/>
                        </div>
                    </div>
                <?php
                } //if (!$llo_editmode) {s
                ?>

                <div class="form-group">
                    <label for="mstscheduledtransactions_maximum_process_count" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Maximum Process Count</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_maximum_process_count_container">
                        <input type="text" class="form-control input-sm integers" name="mstscheduledtransactions_maximum_process_count" id="mstscheduledtransactions_maximum_process_count"
                        <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["frequency"]=="0") { echo ' readonly ';}?>
                        value="<?php if ($llo_editmode) {
                                            echo $larr_MSTScheduledTransactions[0]["maximum_process_count"];
                                    }else {echo "1";} ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_booking_period_mode" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Booking Period Mode</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_booking_period_mode_container">
                        <div class="radio-group">
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_booking_period_mode_0" class="mstscheduledtransactions_booking_period_mode" name="mstscheduledtransactions_booking_period_mode" value="0"
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_period_mode"]=="0" || !$llo_editmode) { echo ' checked ';} ?>
                                   />
                                  As is
                            </label>
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_booking_period_mode_1" class="mstscheduledtransactions_booking_period_mode" name="mstscheduledtransactions_booking_period_mode" value="1"
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_period_mode"]=="1") { echo ' checked ';} ?>
                                   />
                                  Advanced
                            </label>
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_booking_period_mode_2" class="mstscheduledtransactions_booking_period_mode" name="mstscheduledtransactions_booking_period_mode" value="2" 
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_period_mode"]=="2") { echo ' checked ';} ?>
                                  />
                                  Late
                            </label>
                            <label class="radio-inline">
                                  <input type="radio" id="mstscheduledtransactions_booking_period_mode_3" class="mstscheduledtransactions_booking_period_mode" name="mstscheduledtransactions_booking_period_mode" value="3" 
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_period_mode"]=="3") { echo ' checked ';} ?>
                                  />
                                  Fixed Period
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_booking_year" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Fixed Booking Period</label>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="mstscheduledtransactions_booking_year_container">
                        <select class="form-control input-sm" name="mstscheduledtransactions_booking_year" id="mstscheduledtransactions_booking_year">
                            <option value="">- - -</option>
                            <?php
                            for ($ctr=$start_year;$ctr<=date("Y")+1;$ctr++) {
                            ?>
                                <option value="<?php echo $ctr;?>"
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_year"]==$ctr) { echo ' selected ';} ?>
                                ><?php echo $ctr;?></option>
                            <?php
                            } // for ($ctr=$start_year;$ctr<date("Y")+1;$ctr++) {
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="mstscheduledtransactions_booking_period_container">
                        <select class="form-control input-sm" name="mstscheduledtransactions_booking_period" id="mstscheduledtransactions_booking_period">
                            <option value="">- - -</option>
                            <?php
                            for ($ctr=1;$ctr<13;$ctr++) {
                            ?>
                                <option value="<?php echo $ctr;?>"
                                    <?php if ($llo_editmode && $larr_MSTScheduledTransactions[0]["booking_period"]==$ctr) { echo ' selected ';} ?>
                                ><?php echo $larr_PeriodMonths[$ctr];?></option>
                            <?php
                            } // for ($ctr=$start_year;$ctr<date("Y")+1;$ctr++) {
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_template_pattern" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Template Pattern</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_template_pattern_container">
                        <textarea class="form-control input-sm" rows="3" id="mstscheduledtransactions_template_pattern" maxlength="500"
                            name="mstscheduledtransactions_template_pattern"><?php if ($llo_editmode){echo $larr_MSTScheduledTransactions[0]["template_pattern"] ;}?></textarea>
                    </div>
                </div>

                <hr>

                <h4>Transaction Details</h4>

                <div class="form-group">
                    <label for="mstscheduledtransactions_reference_mst_code" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"> Reference</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_reference_mst_code_container">
                        <select class="form-control input-sm"  id="mstscheduledtransactions_reference_mst_code"
                            name="mstscheduledtransactions_reference_mst_code">
                            <option value="">- - Select a Reference - -</option>
                            <?php foreach ($larr_MSTReference as $lch_Key => $larr_Value) {
                            ?>
                                    <option value="<?php echo $larr_Value["code"];?>" 
                                    <?php if ($llo_editmode && $larr_Value["code"]==$larr_MSTScheduledTransactions[0]["reference_mst_code"]) {
                                        echo ' selected ';
                                    } // if ($llo_editmode && $lch_Key==$larr_MSTScheduledTransactions[0]["larr_Value"]) { ?>
                                    >
                                        <?php echo $larr_Value["reference_name"];?>
                                    </option>
                            <?php   
                            } // foreach ($larr_MSTReference as $lch_Key => $lch_Value) { ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_money_trail_type" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Trail Type</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_money_trail_type_container">
                        <select class="form-control input-sm"  id="mstscheduledtransactions_money_trail_type"
                            name="mstscheduledtransactions_money_trail_type">
                            <option value="">- - Select a Trail Type - -</option>
                            <?php foreach ($larr_TrailType as $lch_Key => $lch_RecordRow) {
                            ?>
                                    <option value="<?php echo $lch_Key;?>" 
                                    <?php if ($llo_editmode && $lch_Key==$larr_MSTScheduledTransactions[0]["trail_type"]) {
                                        echo ' selected ';
                                    } // if ($llo_editmode && $lch_Key==$larr_MSTScheduledTransactions[0]["trail_type"]) { ?>
                                    >
                                        <?php echo $lch_RecordRow;?>
                                    </option>
                            <?php   
                            } // foreach ($larr_TrailType as $lch_Key => $lch_Value) { ?>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="mstscheduledtransactions_money_trail_type_mst_code" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"><?php echo $required_tag; ?> Trail</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_money_trail_type_mst_code_container">
                        <select class="form-control input-sm"  id="mstscheduledtransactions_money_trail_type_mst_code"
                            name="mstscheduledtransactions_money_trail_type_mst_code">
                            <option value="">- - Select a Trail - -</option>
                            <?php foreach ($larr_MSTMoneyTrailType as $lch_Key => $lch_RecordRow) {
                            ?>
                                    <option value="<?php echo $lch_RecordRow['code'];?>" 
                                        <?php if ($llo_editmode && $lch_RecordRow['code']==$larr_MSTScheduledTransactions[0]["money_trail_type_mst_code"]) {
                                            echo ' selected ';
                                        } // if ($llo_editmode && $lch_RecordRow['code']==$larr_MSTScheduledTransactions[0]["money_trail_type_mst_code"]) { ?>
                                        class="<?php echo $lch_RecordRow['trail_type'];?>">
                                        <?php echo $lch_RecordRow['money_trail_name'];?>
                                    </option>
                            <?php   
                            } // foreach ($larr_MSTMoneyTrailType as $lch_Key => $lch_Value) { ?>
                        </select>
                    </div>
                </div>

                

                <div class="form-group">
                    <label for="mstscheduledtransactions_reference_no" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Reference no.</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_reference_no_container">
                        <textarea class="form-control input-sm" rows="1" id="mstscheduledtransactions_reference_no" maxlength="500" 
                            name="mstscheduledtransactions_reference_no"><?php if ($llo_editmode){echo $larr_MSTScheduledTransactions[0]["reference_no"] ;}?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstscheduledtransactions_account_money_trail_type_mst_code" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Account to Deduct</label>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mstscheduledtransactions_account_money_trail_type_mst_code_container">
                        <select class="form-control input-sm"  id="mstscheduledtransactions_account_money_trail_type_mst_code"
                            name="mstscheduledtransactions_account_money_trail_type_mst_code">
                            <option value="">- - Select an Account to Deduct - -</option>
                            <?php foreach ($larr_MSTMoneyTrailTypeAccount as $lch_Key => $lch_RecordRow) {
                            ?>
                                    <option value="<?php echo $lch_RecordRow['code'];?>" 
                                        <?php if ($llo_editmode && $lch_RecordRow['code']==$larr_MSTScheduledTransactions[0]["account_money_trail_type_mst_code"]) {
                                            echo ' selected ';
                                        } // if ($llo_editmode && $lch_RecordRow['code']==$larr_MSTScheduledTransactions[0]["account_money_trail_type_mst_code"]) { ?>
                                        class="<?php echo $lch_RecordRow['trail_type'];?>">
                                        <?php echo $lch_RecordRow['money_trail_name'];?>
                                    </option>
                            <?php   
                            } // foreach ($larr_MSTMoneyTrailTypeAccount as $lch_Key => $lch_Value) { ?>
                        </select>
                    </div>
                </div>


                <div class="form-group" style="margin-bottom:0">
                    <label for="mstscheduledtransactionsdtlitems" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Details:</label>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-top:7px" id="">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-top:7px;padding-left:0px;padding-right:0px" id="">
                        <strong>Amount:</strong>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:5px">
                    <label for="mstscheduledtransactionsdtlitems" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"></label>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" id="">
                        <input type="text" class="form-control input-sm" id="mstscheduledtransactionsdtlitems_description" maxlength="5000" 
                            onkeyup="checkScheduledTransactionValue()"
                            name="mstscheduledtransactionsdtlitems_description"/>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding-left:0px;padding-right:0px" id="">
                        <input type="text" class="form-control input-sm right decimals mstscheduledtransactionsdtlitems" name="mstscheduledtransactionsdtlitems_amount" id="mstscheduledtransactionsdtlitems_amount"
                            onkeyup="checkScheduledTransactionValue()"
                            value=""/>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="padding-left:5px;padding-right:0px" id="">
                        <button class="btn btn-primary btn-sm" disabled
                                type="button" title="Add Details"
                                onclick="addScheduledTransactionDetails()"
                            id="mstscheduledtransactionsdtlitems_add"><i class="fa fa-plus"></i></button>
                    </div>
                </div>

                <div id="mstscheduledtransactionsdtlitems_container">

                <?php if (count($larr_MSTScheduledTransactionsDTLItems)>0 && $larr_MSTScheduledTransactionsDTLItems[0]["result"]=="1") {
                    foreach ($larr_MSTScheduledTransactionsDTLItems as $lch_Key => $larr_Value) {
                ?>
                    <div class="form-group" style="margin-bottom:3px" id="mstscheduledtransactionsdtlitems_container_<?php echo $larr_Value["code"]; ?>">
                        <input type="hidden" name="mstscheduledtransactionsdtlitems_codes[]" class="mstscheduledtransactionsdtlitems_codes" value="<?php echo $larr_Value["code"]; ?>">
                        <label for="mstscheduledtransactionsdtlitems" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"></label>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" id="">
                            <input type="text" class="form-control input-sm" id="mstscheduledtransactionsdtlitems_description_<?php echo $larr_Value["code"];?>" maxlength="5000" 
                                name="mstscheduledtransactionsdtlitems_description_<?php echo $larr_Value["code"];?>" value="<?php echo $larr_Value["description"];?>"/>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding-left:0px;padding-right:0px" id="">
                            <input type="text" class="form-control input-sm right decimals mstscheduledtransactionsdtlitems" name="mstscheduledtransactionsdtlitems_amount_<?php echo $larr_Value["code"];?>" id="mstscheduledtransactionsdtlitems_amount_<?php echo $larr_Value["code"];?>"
                                value="<?php echo $larr_Value["amount"];?>"/>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="padding-left:5px;padding-right:0px" id="">
                            <button class="btn btn-danger btn-sm mstscheduledtransactionsdtlitems_delete" 
                                    type="button" title="Delete"
                                    onclick="deleteScheduledTransactionDetails('<?php echo $larr_Value["code"]; ?>')"
                                id="mstscheduledtransactionsdtlitems_delete_<?php echo $larr_Value["code"];?>"><i class="fa fa-trash-o"></i></button>
                        </div>
                    </div>
                <?php
                    } // foreach ($larr_MSTScheduledTransactionsDTLItems as $lch_Key => $larr_Value) {
                } // if (count($larr_MSTScheduledTransactionsDTLItems)>0 && $larr_MSTScheduledTransactionsDTLItems[0]["result"]=="1") { ?>

                </div>

                <br>

                <?php echo $notes;?>
                <br><br>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm save_add_interim" id="save_add_interim" data-loading-text="Saving..." title="" data-trans="has_callback_function" data-mode="save">Save Changes</button>
                <button type="button" class="btn btn-default btn-sm cancel_modal_action" title="Cancel and Close this modal" data-trans="has_callback_function" data-mode="cancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
