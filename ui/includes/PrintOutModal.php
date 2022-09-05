<?php
/*
"http://b85m-p33/abic-web/includes/MasterfilesShowModal.php?
usercode=2&
masterfilename=Module%20Actions&
databasename=iisaac_abic_system_db&
tablename=mstmoduleaction&
primarycodefields=code&
columnstoquery=remarks|code|action_name|description&
columnscaption=Remarks|Primary%20Key|Name%20of%20action|Description%20of%20the%20action&
columnsdatasource=3|0|0|0&
columnsdatasourcedatabasename=|||&
columnsdatasourcetablename=|||&
columnsdatasourcevaluepair=|||&
primarycodevalue="+data[0]+"&
valueviewed="+data[1]"+""
*/
date_default_timezone_set('Asia/Manila');
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");

/*
$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);
$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);
*/


// lookup first for the code being queried
/*
$link = DB_LOCATION;
$params = array (
    "action" => "retrieve-record-column",
    "fileToOpen" => "default_select_query",
    "tableName" => $_GET["tablename"],
    "dbconnect" => $_GET["databasename"],
    "columns" => str_replace("|", ",", $_GET["columnstoquery"]),
    "conditions[equals][".$_GET["primarycodefields"]."]" => $_GET["primarycodevalue"],
    "orderby" => $_GET["primarycodefields"]." ASC"
);
$result=processCurl($link,$params);
$output = json_decode($result,true);
*/

$lch_DBLocationString = DB_LOCATION;

// RETRIEVE APPLICATION PARAMETERS
$larr_AppParams = array();
// get parameters
$larr_Params = array (
    "action" => "retrieve-template-columns",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstapplicationparameter",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "parameter_key,parameter_value",
    "conditions[in][parameter_key]" => 'user_group_claims_mst_code,trnclaims_module_code,module_action_add_code',
    "orderby" => "code ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_OutputParams = json_decode($ljson_Result,true);
if($larr_OutputParams[0]["result"]==='1'){
    foreach ($larr_OutputParams as $lch_Key => $larr_Value){
        $larr_AppParams[$larr_Value["parameter_key"]] = $larr_Value["parameter_value"];
    } // foreach ($larr_OutputParams as $lch_Key => $larr_Value){

} // if($larr_OutputParams[0]["result"]==='1'){

$EXCEL_SUPPORTED_REPORTS = ['EDSTReport','FaculHoldCover','BankRecon'];
$CSV_SUPPORTED_REPORTS = ['PDFAutoLinkBatch','EDSTReport'];

//if (isset($_GET["openclaimbutton"]) && @$_GET["openclaimbutton"]=="true") {

    require_once("../api/UserPrivilegeFunctions.php");

    session_start();

    $larr_Params = array (
        "action" => "retrieve-template-columns",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstuser",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "*",
        "conditions[equals][code]" => $_SESSION["user_code"],
        "orderby" => "code ASC"
    );
    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
    $larr_MSTUser = json_decode($ljson_Result,true);

    // get privilege of user for claims
    $larr_ClaimsPrivileges = array();
    $larr_ClaimsPrivileges = getPrivilegeSet($larr_AppParams["trnclaims_module_code"],$_SESSION["user_code"]);
    $larr_MSTUserGroups = explode(",", $larr_MSTUser[0]["user_group_mst_codes"]);
    $llo_IncludedInClaimGroup = false;
    if (in_array($larr_AppParams["user_group_claims_mst_code"], $larr_MSTUserGroups)){
        $llo_IncludedInClaimGroup = true;
    } // if (in_array($larr_AppParams["user_group_claims_mst_code"], $larr_MSTUserGroups)){
    //module_action_add_code
    $llo_CanAddClaimsPrivilege = true;
    if (!array_key_exists($larr_AppParams["module_action_add_code"], $larr_ClaimsPrivileges)||
        $larr_ClaimsPrivileges[$larr_AppParams["module_action_add_code"]]=="2"){
        $llo_CanAddClaimsPrivilege = false;
    } /* if (!array_key_exists($larr_AppParams["module_action_add_code"], $larr_ClaimsPrivileges)||
        $larr_ClaimsPrivileges[$larr_AppParams["module_action_add_code"]]=="2"){ */

// } // if (isset($_GET["openclaimbutton"]) && @$_GET["openclaimbutton"]) {


?>
<style>
    .MasterfilePrintModal{

    z-index: 1900;
    }

    #loading-prompt {
        text-align: center;
        width: 100%;
    }

    /*table tbody tr td {
        white-space:nowrap;
    }*/

    /*#report_table{
    
        background: #fff;
        padding: 10px;
        margin: 5px;
        border: 2px solid rgba(0,0,0,0.7);
        font-family: sans-serif;

    }*/
</style>

<script type="text/javascript">

    var SUPPORTED_DOWNLOADABLE_PRINTOUTS = ["insurance/PDFAutoLinkBatch", "insurance/PDFOriginalPolicy", "insurance/PDFInvoice", "insurance/PDFInterimPreview", "insurance/PDFPolicy","claims/PDFClaimInfoSheet", "claims/PDFAutoHubConfirmationOfCover"];

    $(".close_modal_action_printoutmodal").on("click",function(){
        $("#PrintOutModal").modal("hide");
    });

    $('#PrintOutModal').find('#print-frame').load(function() {
        $(this).siblings("#loading-prompt").css("display", "none");
        $(this).css("display", "");
    });

    $('#PrintOutModal').live('hidden.bs.modal', function (e) {
       $(this).siblings("#loading-prompt").css("display", "");
        $(this).find("#print-frame").css("display", "none");
        window.top.$('#print-frame').attr('src','about:blank');
    });

    $("#print-option").on("change",function(){
        if($(this).attr("data-optiontype") == "claim_preview"){

            var lch_FilePath = $(this).val();
            var lch_Code = "<?php echo @$_GET['trnclaims_code']; ?>";
            var lch_NewQueryString = "<?php echo ABSOLUTE_PATH;?>/api/transactions/" + lch_FilePath + ".php?trnclaims_code=" + lch_Code + "&policy_codes=" + lch_Code +"#zoom=156";
            $("#loading-message").html("<b>Loading file...</b>");
            $("#blocker").fadeIn("fast",function(){
                $('#print-frame').attr('src', lch_NewQueryString);
                $("#blocker").fadeOut("fast");
            });

        } else if ($(this).attr("data-optiontype") == "interim_preview"){
            
            var lch_FilePath = $(this).val();
            var lch_Code = "<?php echo @$_GET['interim_codes']; ?>";
            var lch_NewQueryString = "<?php echo ABSOLUTE_PATH;?>/api/transactions/" + lch_FilePath + ".php?<?php echo $_SERVER['QUERY_STRING'];?>#zoom=156";
            $("#loading-message").html("<b>Loading file...</b>");
            $("#blocker").fadeIn("fast",function(){
                $('#print-frame').attr('src', lch_NewQueryString);
                $("#blocker").fadeOut("fast");
            });


        }// if($(this).attr("data-optiontype") == "claim_preview"){
    });

    <?php if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") {
    ?>
        $('#save-btn').unbind();
        $('#save-btn').on('click', function() {
            $("#loading-message").html("<b>Generating Excel file..</b>");
            $("#blocker").fadeIn("fast",function(){
                $("#print-frame").contents().find("#report_table").table2excel({
                    exclude: ".noExl",
                    name: '<?php echo $_GET["printdesc"];?>',
                    filename: '<?php echo strtolower(str_replace(' ','',html_entity_decode($_GET["printdesc"]))) . "-".date("Ymd-Hia") ;?>', //do not include extension
                    fileext: ".xlsx",
                    exclude_img: true,
                    exclude_links: true,
                    exclude_inputs: true
                },function(){
                    $("#blocker").fadeOut("fast");
                });
            });
            
            //$('#save-btn').button("reset");

        }); // $('#save-btn').on('click', function() {

    <?php } // if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") {
    else { ?>

        $('#save-btn').on('click', function() {
            var src = $('#print-frame').attr('src');

            if (src.indexOf('ReportBootstrap') > -1) {
                var saveOptionVal = $("#save-option").val();

                if (!saveOptionVal) {
                    alert('Please select a file format');
                    return;
                }

                src = src.replace('#zoom=156', '');
                src = src + '&save_as=' + saveOptionVal;
                window.open(src, "_blank");
                return;
            }

            var policy_codes = $('#lin_policy_codes').val();
            var interim_codes = $('#lin_interim_codes').val();
            var current_codes = $('#lin_current_codes').val();
            var program_name = $('#lin_program_name').val();
            // alert(program_name + " -- 1");
            var include_warranty_and_clauses = $('#lin_include_warranty_and_clauses').val();
            var include_header = $('#lin_include_header').val();

            var selected_option = $('#save-option').val();
            var modal_printout_branch_mst_code = $('#modal_printout_branch_mst_code').val();
            var modal_printout_line_mst_code = $('#modal_printout_line_mst_code').val();
            var modal_printout_subline_mst_code = $('#modal_printout_subline_mst_code').val();
            var modal_printout_low_scope = $('#modal_printout_low_scope').val();
            var modal_printout_high_scope = $('#modal_printout_high_scope').val();
            var modal_printout_policy_sources = $('#modal_printout_policy_sources').val();
            var modal_printout_policy_types = $('#modal_printout_policy_types').val();
            var modal_printout_series = $('#modal_printout_series').val();
            //alert(selected_option + ", (" + policy_codes + "), " + program_name);

            //alert("policy = " + policy_codes + ", interim = " + interim_codes + ", current = " + current_codes);
            downloadPrintout(policy_codes, interim_codes, current_codes, program_name, selected_option, modal_printout_branch_mst_code, modal_printout_line_mst_code, modal_printout_subline_mst_code, modal_printout_low_scope, modal_printout_high_scope, modal_printout_policy_sources, modal_printout_policy_types, modal_printout_series);
        });

        function downloadPrintout(policy_codes, interim_codes, current_codes, program_name, mode, modal_printout_branch_mst_code, modal_printout_line_mst_code, modal_printout_subline_mst_code, modal_printout_low_scope, modal_printout_high_scope, modal_printout_policy_sources, modal_printout_policy_types, modal_printout_series) {
            var include_warranty_and_clauses = $('#lin_include_warranty_and_clauses').val();
            var include_header = $('#lin_include_header').val();

            if (SUPPORTED_DOWNLOADABLE_PRINTOUTS.indexOf(program_name) == -1) {
                alert("Downloading this print out is not yet available.");
                // alert(program_name);
                return;
            }

            var d = new Date();

            var month = d.getMonth()+1;
            var day = d.getDate();

            var date = d.getFullYear() +
            ((''+month).length<2 ? '0' : '') + month +
            ((''+day).length<2 ? '0' : '') + day;

            var absolute_path = "<?php echo ABSOLUTE_PATH;?>";

            var code = "";

            //alert(policy_codes);
            if (policy_codes != "") {
                code = "policy_codes=" + policy_codes;
            }

            if (interim_codes != "") {
                code = "interim_codes=" + interim_codes;
            }

            if (current_codes != "") {
                code = "current_codes=" + current_codes;
            }

            var url = absolute_path + "api/transactions/" + program_name + ".php?" + code +
            "&mode=save" + "&date=" + date +
            "&include_header=" + include_header + "&include_warranty_and_clauses=" + include_warranty_and_clauses +
            "&modal_printout_branch_mst_code=" + modal_printout_branch_mst_code + "&modal_printout_line_mst_code=" + modal_printout_line_mst_code +
            "&modal_printout_subline_mst_code=" + modal_printout_subline_mst_code + "&modal_printout_low_scope=" + modal_printout_low_scope +
            "&modal_printout_high_scope=" + modal_printout_high_scope + "&modal_printout_policy_sources=" + modal_printout_policy_sources +
            "&modal_printout_policy_types=" + modal_printout_policy_types + "&modal_printout_series=" + modal_printout_series;

            // alert(url);
            window.open(url, "_blank");
        }

    <?php } // ELSE ng if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") { ?>

    

</script>

<div class="modal fade large-modal <?php if(@$_GET["is_inner_modal"]==1){ echo "inner-modal"; } ?>" id="PrintOutModal" tabindex="-1" role="dialog" aria-labelledby="MasterfilePrintModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <?php $search = array("insurance/", "collection/", "financials/") ?>
                <button type="button" class="close close_modal_action_printoutmodal" data-id="PrintOutModal" title="Close this modal">&times;</button>
                <input type="hidden" id="lin_policy_codes" name="lin_policy_codes" value="<?php echo @$_GET['policy_codes']; ?>"/>
                <input type="hidden" id="lin_current_codes" name="lin_current_codes" value="<?php echo @$_GET['current_codes']; ?>"/>
                <input type="hidden" id="lin_interim_codes" name="lin_interim_codes" value="<?php echo @$_GET['interim_codes']; ?>"/>
                <input type="hidden" id="lin_program_name" name="lin_program_name" value="<?php echo @$_GET['programname']; ?>"/>
                <input type="hidden" id="lin_interim_code" name="lin_interim_code" value="<?php echo @$_GET['interim_code']; ?>"/>
                <input type="hidden" id="lin_include_warranty_and_clauses" name="lin_include_warranty_and_clauses" value="<?php echo @$_GET['include_warranty_and_clauses']; ?>"/>
                <input type="hidden" id="lin_include_header" name="lin_include_header" value="<?php echo @$_GET['include_header']; ?>"/>

                <h4 class="modal-title" id="MasterfilePrintModalLabel">Print <?php  echo $_GET["printdesc"];?></h4>
            </div>
            <div class="modal-body with-iframe dont-resize">

                <div class="alert alert-danger" id="modal_error_container" style="display:none" >
                    <button type="button" class="close error_container_buttons" id="modal_error_close">&times;</button>
                    <h5 id="modal_error_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>
                    <div id="modal_error_container_content">

                    </div> <!-- #modal_error_container_content -->

                </div> <!-- #modal_error_container -->

                <!-- prompt container-->
                <div class="prompt_containers" id="loading-prompt">
                    <h1><i class="fa fa-spinner fa-spin"></i></h1>
                    <h3>Loading.. Please wait </h3>
                </div>

            	<iframe id="print-frame" style="display: none;" src="<?php echo ABSOLUTE_PATH;?>api/transactions/<?php echo $_GET['programname'];?>.php?<?php echo $_SERVER['QUERY_STRING'] . "&user_code=" . $larr_MSTUser[0]["code"] ;?>#zoom=156"  frameborder="0"></iframe>

            </div>
            <div class="modal-footer">
                <div class="pull-left">
                

                <?php if(isset($_GET["claim_options"]) && @$_GET["claim_options"] != ""){ ?>

                    <?php
                        $larr_ClaimOptions = explode(",", $_GET["claim_options"]);
                    ?>
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn" data-id="PrintOutModal" title="Save as"><i class="fa fa-floppy-o"></i> Save as</button>
                    <select class="input-sm option-color" id="save-option" style="vertical-align:top" text="- Please select a file type -">
                        <option selected disabled class="default-option" style="display:none">- Please select a file type -</option>
                        <?php
                        $lch_ProgramName = str_replace($search, "", @$_GET['programname']);
                        if(in_array($lch_ProgramName, $CSV_SUPPORTED_REPORTS) || in_array(@$_GET['reportname'], $CSV_SUPPORTED_REPORTS)) {
                            ?>
                            <option value="csv" class="option-color">CSV</option>
                            <?php
                        }
                        if(in_array($lch_ProgramName, $EXCEL_SUPPORTED_REPORTS) || in_array(@$_GET['reportname'], $EXCEL_SUPPORTED_REPORTS)) {
                            ?>
                            <option value="excel" class="option-color">EXCEL</option>
                            <?php
                        }
                        ?>
                        <!-- <option value="textfile" class="option-color">Text file</option> -->
                        <option value="pdf" class="option-color">PDF</option>
                    </select>

                    <select class="input-sm option-color" id="print-option" style="vertical-align:top" data-optiontype="claim_preview" text="- Please select a print option -">
                        <?php if($_GET["openclaimbutton"] == "true"){ ?>
                          <option selected value="<?php $_GET["programname"]?>" class="default-option">Policy Preview</option>
                        <?php } else { ?>
                            <option selected value="claims/PDFClaimInfoSheet" class="default-option">Info Sheet</option>
                        <?php } ?>
                        <?php foreach($larr_ClaimOptions as $lch_Key => $lch_Value){ 
                                $larr_ExplodedClaimOption = explode(":", $lch_Value);
                            ?>
                            <option value="<?php echo $larr_ExplodedClaimOption[1]; ?>"><?php echo $larr_ExplodedClaimOption[0]; ?></option>
                        <?php } ?>
                    </select>

                <?php } // if(isset($_GET["claim_options"]) && @$_GET["claim_options"] != ""){
                else if(isset($_GET["preview_options"]) && @$_GET["preview_options"] != "") {
                ?>
                    <?php
                        $larr_PreviewOption = explode(",", $_GET["preview_options"]);
                    ?>
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn" data-id="PrintOutModal" title="Save as"><i class="fa fa-floppy-o"></i> Save as</button>
                    <select class="input-sm option-color" id="save-option" style="vertical-align:top" text="- Please select a file type -">
                        <option selected disabled class="default-option" style="display:none">- Please select a file type -</option>
                        <?php
                        $lch_ProgramName = str_replace($search, "", @$_GET['programname']);
                        if(in_array($lch_ProgramName, $CSV_SUPPORTED_REPORTS) || in_array(@$_GET['reportname'], $CSV_SUPPORTED_REPORTS)) {
                            ?>
                            <option value="csv" class="option-color">CSV</option>
                            <?php
                        }
                        if(in_array($lch_ProgramName, $EXCEL_SUPPORTED_REPORTS) || in_array(@$_GET['reportname'], $EXCEL_SUPPORTED_REPORTS)) {
                            ?>
                            <option value="excel" class="option-color">EXCEL</option>
                            <?php
                        }
                        ?>
                        <!-- <option value="textfile" class="option-color">Text file</option> -->
                        <option value="pdf" class="option-color">PDF</option>
                    </select>

                    <select class="input-sm option-color" id="print-option" style="vertical-align:top" data-optiontype="interim_preview" text="- Please select a print option -">
                        <?php foreach($larr_PreviewOption as $lch_Key => $lch_Value){ 
                                $larr_ExplodedPreviewOption = explode(":", $lch_Value);
                            ?>
                            <?php if($larr_ExplodedPreviewOption[2] == 1){?>

                                <option selected value="<?php echo $larr_ExplodedPreviewOption[1]; ?>"><?php echo $larr_ExplodedPreviewOption[0]; ?></option>

                            <?php } else {?>

                                <option  value="<?php echo $larr_ExplodedPreviewOption[1]; ?>"><?php echo $larr_ExplodedPreviewOption[0]; ?></option>

                            <?php } // ELSE NG if($larr_ExplodedPreviewOption[2] == 1){ ?>

                        <?php } // foreach($larr_PreviewOption as $lch_Key => $lch_Value){ ?>

                    </select>
                    
                <?php
                } // else if(isset($_GET["preview_options"]) && @$_GET["preview_options"] != "") {
                else if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") {
                ?>
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn" data-loading-text="Generating Excel file.." data-id="PrintOutModal" title="Save as"><i class="fa fa-floppy-o"></i> Save as Excel</button>
                <?php
                } // else if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") { ?>
                

                </div>

                <?php if (isset($_GET["openclaimbutton"]) && @$_GET["openclaimbutton"]=="true") {
                        if ($llo_IncludedInClaimGroup==true && $llo_CanAddClaimsPrivilege==true){
                ?>
                            <button type="button" class="btn btn-primary btn-sm add_claim_action" title="Open a Claim for this Policy/Binder" data-trans="has_callback_function" data-mode="save"><i class="fa fa-plus-circle"></i> Open Claim</button>
                <?php
                        } // if ($llo_IncludedInClaimGroup==true && $llo_CanAddClaimsPrivilege==true){
                } // if (isset($_GET["openclaimbutton"]) && @$_GET["openclaimbutton"]) { ?>
                
                <button type="button" class="btn btn-default btn-sm close_modal_action_printoutmodal" data-id="PrintOutModal" title="Close this modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
<?php if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") {

} else { ?>
<script type="text/javascript">


$("#save-btn").live("click", function(){

    saveAs($("#saveOption").val());
    // alert($("#saveOption").val());
});//$("#save-btn").live("click", function(){

function saveAs(path){

    if(path!=null){

        location.href = "<?php echo ABSOLUTE_PATH ?>api/SaveAs.php?<?php echo $_SERVER["QUERY_STRING"]; ?>&type=" + path + "&code=1";

    }

}//function saveAs(path){


</script>
<?php } // if(isset($_GET["fromreport"]) && @$_GET["fromreport"] == "1") { ?>