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
header('Content-type: charset=iso-8859-1');
$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);
$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);
$columnsDataType = explode("|",$_GET["columnsdatatype"]);

// lookup first for the code being queried
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");
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


//echo $result;
?>


<div class="modal fade" id="MasterfileShowModal" tabindex="-1" role="dialog" aria-labelledby="MasterfileShowModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
    <input type="hidden" name="tableName" id="tableName" value="<?php echo $_GET["tablename"]; ?>"/>
        <form class="form-horizontal" method="post" id="form_masterfile_dynamic">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_modal_action" data-id="MasterfileShowModal" title="Close this modal">&times;</button>
                <h3 class="modal-title" id="MasterfileShowModalLabel">Viewing <?php echo $_GET["masterfilename"];?> - <?php echo $_GET["valueviewed"];?></h3>
            </div>
            <div class="modal-body">

                <?php
                //echo $result;
                if($output[0]["result"]==='1'){
                ?>
                
                <?php
                    foreach ($output as $key => $value){
                        /*
                        $columns = explode("|",$_GET["columnstoquery"]);
                        $columnsCaption = explode("|",$_GET["columnscaption"]);
                        $columnsDataSource = explode("|",$_GET["columnsdatasource"]);
                        $columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
                        $columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
                        $columnsDatasourceValuePair = explode("|",$_GET["columnsDatasourceValuePair"]);
                        */
                        foreach ($columns as $column_key => $column_name){

                            $valueToDisplay = "";
                            $captionToDisplay = "";

                            // static source = use value pair to determine value
                            if ($columnsDataSource[$column_key]=="1"){
                                if ($value[$column_name]!=""&&$value[$column_name]!="0"){
                                    $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                                    foreach ($value_pairs as $pair_key => $pair_value) {
                                        $static_values = explode(":",$pair_value);
                                        //if ($static_values["0"]==$value[$column_name]){
                                        if (strpos($value[$column_name],$static_values["0"]) !== false ){
                                            $valueToDisplay = $valueToDisplay . "" . $static_values["1"] . ", ";
                                            
                                        } //if ($static_values["0"]==$value[$column_name]){
                                    } // foreach ($value_pairs as $pair_key => $pair_value) {
                                    $valueToDisplay = rtrim($valueToDisplay,", ");
                                    $captionToDisplay = $valueToDisplay;
                                } // if ($value[$column_name]!=""&&$value[$column_name]!="0"){
                                
                            } //if ($columnsDataSource[$column_key]=="1"){

                            // database source - use datasource dbname and tablename to determine value
                            else if ($columnsDataSource[$column_key]=="2"){
                                if ($value[$column_name]!=""&&$value[$column_name]!="0"){
                                    // GET THE ACTUAL VALUE AND DESCRIPTION TO BE DISPLAYED (DESCRIPTION CAN BE CONCATENATION OF FIELDS (SPACE SEPARATED))
                                    $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);

                                    // IF MORE THAN 1 = CONCATENATION OF FIELDS
                                    $value_pairs_actual = explode(" ",$value_pairs["1"]);
                                    $cols = "";
                                    if (count($value_pairs_actual)>0){
                                        foreach ($value_pairs_actual as $vals){
                                            $cols = $cols . "" . $vals . ",";
                                        }
                                    } // if (count($value_pairs_actual)>0){
                                    $cols = substr($cols, 0,strlen($cols)-1);

                                    // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                                    $link = DB_LOCATION;
                                    $params = array (
                                        "action" => "retrieve",
                                        "fileToOpen" => "default_select_query",
                                        "tableName" => $columnsDataSourceTableName[$column_key],
                                        "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                                        "columns" => $value_pairs['0'].",".$cols ,
                                        "orderby" => $value_pairs['0']." ASC",
                                        "conditions[in][".$value_pairs['0']."]" => $value[$column_name]
                                    );
                                
                                    $result=processCurl($link,$params);
                                    $output2 = json_decode($result,true);
                                    if($output2[0]["result"]==='1'){

                                        // CONCATENATE DATA RETRIEVED
                                        foreach ($output2 as $data_source_key => $data_source_value){
                                            foreach ($value_pairs_actual as $vals){
                                                $valueToDisplay = $valueToDisplay . "" . $data_source_value[$vals] . ", ";
                                            }
                                            //valueToDisplay = $data_source_value[$value_pairs['1']];
                                            
                                        } // foreach ($output2 as $data_source_key => $data_source_value){
                                        $valueToDisplay = rtrim($valueToDisplay,", ");
                                        $captionToDisplay = $valueToDisplay;
                                    } // if($output[0]["result"]==='1'){
                                } // if ($value[$column_name]!=""&&$value[$column_name]!="0"){
                                
                                    

                            } //else if ($columnsDataSource[$column_key]=="2"){

                            // default source
                            else {
                                $valueToDisplay = $value[$column_name];
                                $captionToDisplay = $value[$column_name];
                            } // ELSE ng else if ($columnsDataSource[$column_key]=="2"){


                            // special conditions
                            $usesFormat = false;
                            $formatmode = "";
                            // IF MAY SPECIAL CONDITION/S = PROCESS IT -- PROGRAMMER'S USE ONLY!
                            if ($columnsSpecialConditions[$column_key]!=""&&$valueToDisplay!=""){
                                $value_pairs = explode(",",$columnsSpecialConditions[$column_key]);
                                foreach ($value_pairs as $pair_key => $pair_value){
                                    $static_values = explode("=",$pair_value);

                                    // IF USES FORMAT (SHOULD ALWAYS BE FIRST BEFORE formatmode AND format SPECIAL CONDITIONS)
                                    if ($static_values[0]=="usesformat"&&$static_values[1]=="yes"){
                                        $usesFormat=true;
                                    } // if ($static_values[0]=="usesformat"&&$static_values[1]=="yes"){

                                    // DETERMINES WHAT DATATYPE OF FORMAT
                                    else if ($static_values[0]=="formatmode"&&$static_values[1]!=""){
                                        $formatmode = $static_values[1];
                                    } // else if ($static_values[0]=="formatmode"&&$static_values[1]!=""){

                                    // FORMAT PROPER - REQUIRES formatmode AND usesformat SPECIAL CONDITIONS
                                    else if ($static_values[0]=="format"&&$static_values[1]!=""&&$usesFormat==true&&$formatmode!="") {

                                        // IF datetime = CONVERT $valueToDisplay TO DATE WITH GIVEN FORMAT
                                        if ($formatmode=="datetime"){
                                            $valueToDisplay = date ($static_values[1], strtotime($valueToDisplay));
                                        } // if ($formatmode=="datetime"){

                                        // IF number = CONVERT $valueToDisplay TO NUMBER WITH GIVEN FORMAT (either INT or DEC)
                                        else if ($formatmode=="number"){
                                            $valueToDisplay = number_format($valueToDisplay,$static_values[1]); 
                                        } // else if ($formatmode=="number"){

                                        $captionToDisplay = $valueToDisplay;
                                    } // else if ($static_values[0]=="format"&&$static_values[1]!=""&&$usesFormat==true) {
                                } // foreach ($value_pairs as $pair_key => $pair_value){
                            } // if ($columnsSpecialConditions[$column_key]!=""){

                    ?>
                       
                            <div class="form-group"  title="<?php echo $columnsCaption[$column_key] ;?>" id="<?php echo 'masterfiletransaction-name-'.$column_name?>">
                                <label for="inputEmail3" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label"><?php echo $columnsFieldName[$column_key];?></label>
                                
                                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" title="" >
                            <?php if($columnsDataType[$column_key]!="6"){ ?>
                                    <input type="text" disabled class="form-control input-sm" id="<?php echo 'masterfiletransaction-name-type-'.$column_name?>" value="<?php echo ($valueToDisplay=="")?'&nbsp;':$valueToDisplay ;?>"/>
                                        
                            <?php }//if($columnsDataType[$column_key]!="6"){
                                else{
                                 echo ($valueToDisplay=="")?'&nbsp;':$valueToDisplay;
                                     }?>
                                </div>
                            </div>
                            
                        
                    <?php
                        } // foreach ($columns as $column_name){
                    } // foreach ($output as $key => $value){
                ?>
                        
                <?php
                } // if($output[0]["result"]==='1'){
                else {
                ?>
                    <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo $output[0]["error_message"];?>
                    </div>
                <?php
                } // else ng if($output[0]["result"]==='1'){
                ?>
                <br>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm close_modal_action" data-id="MasterfileShowModal" title="Close this modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
