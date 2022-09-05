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


$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);
$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);



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

?>


<div class="modal fade large-modal" id="MasterfilePrintModal" tabindex="-1" role="dialog" aria-labelledby="MasterfilePrintModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_modal_action" data-id="MasterfilePrintModal" title="Close this modal">&times;</button>
                <h4 class="modal-title" id="MasterfilePrintModalLabel">Print <?php echo $_GET["masterfilename"];?> - <?php echo strtolower(str_replace(" ", "", $_GET["masterfilename"])).date ("YmdHis",strtotime("now")).'.pdf - '. date ("M d, Y h:ia",strtotime("now"));?></h4>
            </div>
            <div class="modal-body with-iframe dont-resize">

            	<iframe id="print-frame" src="<?php echo ABSOLUTE_PATH;?>api/PrintMasterfiles.php?<?php echo $_SERVER['QUERY_STRING'];?>"  frameborder="0"></iframe>

            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn" data-id="MasterfilePrintModal" title="Save as"><i class="fa fa-floppy-o"></i> Save as</button>
                    <select class="input-sm option-color" id="saveOption" style="vertical-align:top" text="- Please select a file type -">
                        <option selected disabled class="default-option" style="display:none">- Please select a file type -</option>
                        <option value="excelnew" class="option-color">Excel</option>
                        <option value="excelold" class="option-color">Excel (Old Version)</option>
                        <option value="calc" class="option-color">Calc (Open Office)</option>
                        <option value="csv" class="option-color">CSV</option>
                        <option value="textfile" class="option-color">Text file</option>
                        <option value="pdf" class="option-color">PDF</option>
                        <option value="test" class="option-color">test</option>
                    </select>
                </div>
                <button type="button" class="btn btn-default btn-sm close_modal_action" data-id="MasterfilePrintModal" title="Close this modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>

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