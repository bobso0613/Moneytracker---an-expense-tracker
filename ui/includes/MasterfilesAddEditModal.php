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

//echo $_SERVER["QUERY_STRING"];

$classcustomformat="";
$datacustomformat="";
$datanumberofdec="";


date_default_timezone_set('Asia/Manila');
$columns = explode("|",$_GET["columnstoquery"]);
$columnsFieldName = explode("|",$_GET["columnsfieldname"]);
$columnsCaption = explode("|",$_GET["columnscaption"]);
$columnsDataSource = explode("|",$_GET["columnsdatasource"]);
$columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
$columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
$columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
$columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);

$columnsIsRequired = explode("|",$_GET["columnsisrequired"]);
$columnsIsUnique = explode("|",$_GET["columnsisunique"]);
$columnsDataType = explode("|",$_GET["columnsdatatype"]);
$columnsMaxLength = explode("|",$_GET["columnsmaxlength"]);

$columnsCode = explode("|",$_GET["columnscode"]);

// lookup first for the code being queried
require_once("../api/SystemConstants.php");
require_once("../api/CurlAPI.php");
if ($_GET["transactionmode"]=="can_add"){

} // if ($_GET["transactionmode"]=="can_add"){
else if ($_GET["transactionmode"]=="can_edit"){
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
    $retrievedRecordRow = json_decode($result,true);
} // else if ($_GET["transactionmode"]=="can_edit"){

$transaction = ($_GET["transactionmode"]=="can_add")?"Add":"Edit";

$notes = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 right transaction-guides">
            <span class="required" title="This field should not be blank."><strong><span class="asterisk">*</span></strong> Required Fields</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="unique" title="This field must be unique."><strong><span class="asterisk">*</span></strong> Unique Fields</span>
        </div>';

$required_tag = '<strong><span class="asterisk required">*</span></strong>';
$unique_tag = '<strong><span class="asterisk unique">*</span></strong>';

$lch_ModalClass = "";
$lch_CancelClass = "cancel_modal_action";
$lch_SaveClass = "save_modal_action";

if(isset($_GET["isFromPolicy"]) && @$_GET["isFromPolicy"] == "1"){

    if (@$_GET["modalclass"]!="") {
        $lch_ModalClass = @$_GET["modalclass"];
    } // if (@$_GET["modalclass"]!="") {
    else {
        $lch_ModalClass = "inner-modal";
    } // ELSE ng if (@$_GET["modalclass"]!="") {
     

    $lch_CancelClass = "inner_modal_cancel";
    $lch_SaveClass = "inner_modal_save";
}//if(isset(@$_GET["isFromPolicy"])){

?>


<div class="modal fade <?php echo $lch_ModalClass; ?>" id="MasterfileAddEditModal" tabindex="-1" role="dialog" aria-labelledby="MasterfileAddEditModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
        <form class="form-horizontal" method="post" id="form_masterfile_dynamic">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close <?php echo $lch_CancelClass ?>" title="Cancel and Close this modal" data-trans="<?php echo $_GET["transactionmode"];?>">&times;</button>
                <h3 class="modal-title" id="MasterfileAddEditModalLabel"><?php echo $transaction;?>&nbsp;<?php echo $_GET["masterfilename"];?><?php echo ($_GET["transactionmode"]=="can_edit")?"&nbsp;-&nbsp;".$_GET["valueviewed"]:""; ?></h3>
            </div>
            <div class="modal-body">


                <input type="hidden" name="transactionmode" id="transactionmode" value="<?php echo $_GET["transactionmode"]; ?>"/>
                <input type="hidden" name="tableName" id="tableName" value="<?php echo $_GET["tablename"]; ?>"/>
                <input type="hidden" name="dbconnect" id="dbconnect" value="<?php echo $_GET["databasename"]; ?>"/>
                <input type="hidden" name="columnsfieldname" id="columnsFieldName" value="<?php echo $_GET["columnsfieldname"]; ?>"/>
                <input type="hidden" name="columnscode" id="columnsCode" value="<?php echo $_GET["columnscode"]; ?>"/>
                <input type="hidden" name="columnstoquery" id="columns" value="<?php echo $_GET["columnstoquery"]; ?>"/>
                <input type="hidden" name="columnsisrequired" id="columnsIsRequired" value="<?php echo $_GET["columnsisrequired"]; ?>"/>
                <input type="hidden" name="columnsisunique" id="columnsIsUnique" value="<?php echo $_GET["columnsisunique"]; ?>"/>
                <input type="hidden" name="columnsdatatype" id="columnsDataType" value="<?php echo $_GET["columnsdatatype"]; ?>"/>
                <input type="hidden" name="columnsmaxlength" id="columnsMaxLength" value="<?php echo $_GET["columnsmaxlength"]; ?>"/>
                <input type="hidden" name="columnsdatasource" id="columnsDataSource" value="<?php echo $_GET["columnsdatasource"]; ?>"/>
                <input type="hidden" name="columnsdatasourcedatabasename" id="columnsDataSourceDatabaseName" value="<?php echo $_GET["columnsdatasourcedatabasename"]; ?>"/>
                <input type="hidden" name="columnsdatasourcetablename" id="columnsDataSourceTableName" value="<?php echo $_GET["columnsdatasourcetablename"]; ?>"/>
                <input type="hidden" name="columnsdatasourcevaluepair" id="columnsDatasourceValuePair" value="<?php echo $_GET["columnsdatasourcevaluepair"]; ?>"/>
                
                <input type="hidden" name="masterfilename" id="masterfilename" value="<?php echo $_GET["masterfilename"]; ?>"/>

                <input type="hidden" name="modulemstcode" id="modulemstcode" value="<?php echo $_GET["modulemstcode"]; ?>"/>
                <input type="hidden" name="menuitemmstcode" id="menuitemmstcode" value="<?php echo $_GET["menuitemmstcode"]; ?>"/>
                <input type="hidden" name="valueviewed" id="valueviewed" value="<?php echo isset($_GET["valueviewed"])? $_GET["valueviewed"]:"New Record"; ?>"/>

                <?php if ($_GET["transactionmode"]=="can_edit"){
                ?>
                    <input type="hidden" name="primarycodefields" id="primarycodefields" value="<?php echo $_GET["primarycodefields"]; ?>"/>
                    <input type="hidden" name="primarycodevalue" id="primarycodevalue" value="<?php echo $_GET["primarycodevalue"]; ?>"/>
                <?php
                } // if ($_GET["transactionmode"]=="can_edit"){
                ?>
                
                <div class="alert alert-danger" id="modal_error_container" style="display:none" >
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

                <?php
                // LOOP ALL COLUMNS - USE $column_key for index
                foreach ($columns as $column_key => $column_name){
                    $classcustomformat="";
                    $datacustomformat="";
                    $datanumberofdec = "";

                    ?>
                    <div class="form-group" title="<?php echo $columnsCaption[$column_key]; ?>" id="<?php echo 'masterfiletransaction-name-'.$columnsCode[$column_key].'-'.$column_name?>">
                    <label for="inputEmail3" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 control-label"><?php echo ($columnsIsRequired[$column_key]=="1")?$required_tag."&nbsp;":"";?><?php echo ($columnsIsUnique[$column_key]=="1")?$unique_tag."&nbsp;":"";?><?php echo $columnsFieldName[$column_key]; ?></label>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    <?php
                    // DETERMINE WHAT DATA TYPE TO RENDER CORRECT INPUT FIELD NEEDED
                    switch ($columnsDataType[$column_key]){

                        /*
                        $columns = explode("|",$_GET["columnstoquery"]);
                        $columnsFieldName = explode("|",$_GET["columnsfieldname"]);
                        $columnsCaption = explode("|",$_GET["columnscaption"]);
                        $columnsDataSource = explode("|",$_GET["columnsdatasource"]);
                        $columnsDataSourceDatabaseName = explode("|",$_GET["columnsdatasourcedatabasename"]);
                        $columnsDataSourceTableName = explode("|",$_GET["columnsdatasourcetablename"]);
                        $columnsDatasourceValuePair = explode("|",$_GET["columnsdatasourcevaluepair"]);
                        $columnsSpecialConditions = explode("|",$_GET["columnsspecialconditions"]);

                        $columnsIsRequired = explode("|",$_GET["columnsisrequired"]);
                        $columnsIsUnique = explode("|",$_GET["columnsisunique"]);
                        $columnsDataType = explode("|",$_GET["columnsdatatype"]);
                        $columnsMaxLength = explode("|",$_GET["columnsmaxlength"]);
                        */


                        //1 - Character (one line)
                        //2 - Integer
                        //3 - Decimal
                        //4 - Date
                        //11 - Password
                        case "1":
                        case "2":
                        case "3":
                        case "4": 
                        case "11":
                        ?>
                        <?php if ($columnsSpecialConditions[$column_key]!=""){
                            
                                        $explodedSpecCon = explode(",",$columnsSpecialConditions[$column_key]);
                                        $decimalplace = "";
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
                                            // else if(($explodedPerItemSpecCon["0"]=="decimal_places")&&($explodedPerItemSpecCon["1"]!=""))
                                            // {
                                            //     $decimalplace = "-" . $explodedPerItemSpecCon["1"];
                                            // }

                                        }
                                    }

                                    ?>
                            
                            <input type="<?php echo ($columnsDataType[$column_key]=='11') ? "password" :"text";?>" placeholder="<?php echo $columnsFieldName[$column_key]; ?>"
                                data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" <?php if ($datanumberofdec!=""){echo 'data-numberofdec=' . '"' . $datanumberofdec . '"' ;} ?>  data-format="<?php if ($datacustomformat!=""){echo $datacustomformat;} ?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                 class="form-control input-sm masterfiletransaction-fields modal_actions <?php if($classcustomformat!=""){ echo $classcustomformat;} ?> <?php if ($columnsDataType[$column_key]=='2' && $classcustomformat=="") { echo 'integerFormat'; }
                                        else if ($columnsDataType[$column_key]=='3') { echo 'decimalFormat'; }
                                        else if ($columnsDataType[$column_key]=='4') { echo 'input-group date'; } ?>"
                                        <?php echo ($columnsMaxLength[$column_key]!==""&&$columnsMaxLength[$column_key]!="0"&&$columnsDataType[$column_key]!="4")?'maxlength="'.$columnsMaxLength[$column_key].'"':'';?> 
                                value="<?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''){
                                                echo $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name];
                                            } 
                                            else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                    $_GET['transactionmode']=='can_edit'){
                                                echo $retrievedRecordRow[0][$column_name];
                                            } 
                                        ?>" 
                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>" />
                            
                        <?php
                        break; // case "1": case "2": case "3": case "4":



                        //5 - Character (Paragraph)
                        //6 - Character (Text Editor)
                        case "5":
                        case "6":
                        ?>
                            <textarea placeholder="<?php echo $columnsFieldName[$column_key]; ?>"
                                data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                 class="form-control input-sm masterfiletransaction-fields  modal_actions  <?php if ($columnsDataType[$column_key]=='6') { echo 'wysiwyg-editor'; }?>"
                                <?php echo ($columnsMaxLength[$column_key]!==""&&$columnsMaxLength[$column_key]!="0")?'maxlength="'.$columnsMaxLength[$column_key].'"':'';?> 
                                 ><?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''){
                                                echo $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name];
                                            }
                                            else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                    $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                    $_GET['transactionmode']=='can_edit'){
                                                echo $retrievedRecordRow[0][$column_name];
                                            } ?></textarea>
                            
                        <?php
                        break; // case "5": case "6":



                        //7 - Combo Box
                        case "7":
                            // FROM STATIC DATA SOURCE
                            if ($columnsDataSource[$column_key]=="1"){
                                if ($columnsDatasourceValuePair[$column_key]!=""){
                                    $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                                    
                                    ?>
                                        <select class="form-control input-sm masterfiletransaction-fields  modal_actions "
                                        data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                            id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                            name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>">
                                        <option value="">- Select <?php echo $columnsFieldName[$column_key]; ?> -</option>
                                    <?php
                                        if (count($value_pairs)>0){
                                            foreach ($value_pairs as $pair_key => $pair_value) {
                                                $static_values = explode(":",$pair_value);
                                            ?>
                                                <option value="<?php echo $static_values["0"]?>"
                                                    <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==$static_values['0']){
                                                        echo ' selected ';
                                                    }
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                            $_GET['transactionmode']=='can_edit'&&$static_values["0"]==$retrievedRecordRow[0][$column_name]){
                                                        echo ' selected ';
                                                    } ?>
                                                ><?php echo $static_values["1"];?></option>
                                            <?php
                                            } //foreach ($value_pairs as $pair_key => $pair_value) {
                                        } //if (count($value_pairs)>0){
                                    ?>
                                        </select>
                                    <?php
                                    
                                } //if ($columnsDatasourceValuePair[$column_key]!=""){
                            } //if ($columnsDataSource[$column_key]=="1"){
                            // FROM DATABASE SOURCE
                            else if ($columnsDataSource[$column_key]=="2"){
                                if ($columnsDatasourceValuePair[$column_key]!=""){
                                    $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);
                                    $value_pairs_calamity = explode(" ", $value_pairs['1']);

                                    $custom_file_to_open="default_select_query";
                                    $chainingparentid="";
                                    $chainingparentfield="";
                                    $conditions = array();
                                    $conditions_fieldname = array();
                                    $conditions_fieldvalue = array();
                                    if ($columnsSpecialConditions[$column_key]!=""){
                                        $explodedSpecCon = explode(",",$columnsSpecialConditions[$column_key]);
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
                                        "tableName" => $columnsDataSourceTableName[$column_key],
                                        "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                                        "columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']).(($chainingparentfield!="")?",".$chainingparentfield:"") ,
                                        "orderby" => ((array_key_exists("data_source_table_name", $columnsDataSourceTableName) && $columnsDataSourceTableName["data_source_table_name"] == "mstcalamityzone")?$value_pairs_calamity['1']:$value_pairs['0'])." ASC"
                                    );
                                    foreach ($conditions as $lch_key => $lch_value){
                                        $params["conditions[".$lch_value."][".$conditions_fieldname[$lch_key]."]"] = $conditions_fieldvalue[$lch_key];
                                    } // foreach ($conditions as $lch_key => $lch_value){
                                    $result=processCurl($link,$params);
                                    //echo $result;
                                    $output = json_decode($result,true);
                                    $ctr = 0;
                                    // echo $result;
                                    ?>


                                        <select class="form-control input-sm masterfiletransaction-fields  modal_actions " <?php if ($chainingparentid!=""){echo 'data-chainingparentid="'.$chainingparentid.'"';} ?> <?php if ($chainingparentfield!=""){echo ' data-chainingparentfield="'.$chainingparentfield.'"';} ?>
                                        data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>">
                                        <option value="">- Select <?php echo $columnsFieldName[$column_key]; ?> - </option>
                                    <?php
                                        if($output[0]["result"]==='1'){
                                            foreach ($output as $data_source_key => $data_source_value){
                                    ?>                      
                                                <option value="<?php echo $data_source_value[$value_pairs['0']]?>" <?php if ($chainingparentfield!=""){echo 'class="'.$data_source_value[$chainingparentfield].'"';} ?>
                                                        <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                                $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==$data_source_value[$value_pairs['0']]){
                                                            echo ' selected ';
                                                        }
                                                        else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                                $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                                $_GET['transactionmode']=='can_edit'&&$data_source_value[$value_pairs['0']]==$retrievedRecordRow[0][$column_name]){
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
                                        </select>
                                    <?php
                                    } // if    if ($chainingparentid=="") {
                                    else
                                    { 
                                        if ($_GET['transactionmode']=='can_edit')
                                        {
                                            $custom_file_to_open="default_select_query";       
                                            $link = DB_LOCATION;
                                            $params = array (
                                                "action" => "retrieve",
                                                "fileToOpen" => $custom_file_to_open,
                                                "tableName" => $columnsDataSourceTableName[$column_key],
                                                "dbconnect" => $columnsDataSourceDatabaseName[$column_key], 
                                                "conditions[equals][". $value_pairs['0'] ."]"  => $retrievedRecordRow[0][$column_name],
                                                //"conditions[equals][" . $lch_chainingfield . "]"  =>
                                                "columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']).(($chainingparentfield!="")?",".$chainingparentfield:"") ,
                                                "orderby" => $value_pairs['0']." ASC"
                                            );
                                            foreach ($conditions as $lch_key => $lch_value){
                                                $params["conditions[".$lch_value."][".$conditions_fieldname[$lch_key]."]"] = $conditions_fieldvalue[$lch_key];
                                            } // foreach ($conditions as $lch_key => $lch_value){
                                            $result=processCurl($link,$params);
                                            $output1 = json_decode($result,true);
                                            $ctr = 0;              


                                            //echo json_encode($output1);
                                            $output = array();
                                            if (count($output1)>0 && $output1[0]["result"]=="1") {
                                                foreach ($output1 as $data_source_key => $data_source_value){
                                            

                                                    $params = array (
                                                        "action" => "retrieve",
                                                        "fileToOpen" => $custom_file_to_open,
                                                        "tableName" => $columnsDataSourceTableName[$column_key],
                                                        "dbconnect" => $columnsDataSourceDatabaseName[$column_key], 
                                                        "conditions[equals][". $chainingparentfield ."]"  => @$data_source_value[$chainingparentfield],
                                                        //"conditions[equals][" . $lch_chainingfield . "]"  =>
                                                        "columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']).(($chainingparentfield!="")?",".$chainingparentfield:"") ,
                                                        "orderby" => $value_pairs['0']." ASC"
                                                    );
                                                    foreach ($conditions as $lch_key => $lch_value){
                                                        $params["conditions[".$lch_value."][".$conditions_fieldname[$lch_key]."]"] = $conditions_fieldvalue[$lch_key];
                                                    } // foreach ($conditions as $lch_key => $lch_value){
                                                    $result=processCurl($link,$params);
                                                        //echo json_encode($params);
                                                    //$output = array();
                                                    $output = json_decode($result,true);
                                                    $ctr = 0;           
                                                } //  foreach ($output1 as $data_source_key => $data_source_value){
                                            } // if (count($output1)>0 && $output1[0]["result"]=="1") {
                                        }//  if ($_GET['transactionmode']=='can_edit')
                                        ?>
                                        <select class="form-control input-sm masterfiletransaction-fields  modal_actions " <?php if ($chainingparentid!=""){echo 'data-chainingparentid="'.$chainingparentid.'"';} ?> <?php if ($chainingparentfield!=""){echo ' data-chainingparentfield="'.$chainingparentfield.'"';} ?>
                                        data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                        data-sourcetb="<?php echo $columnsDataSourceTableName[$column_key]; ?>"
                                                        data-sourcedb="<?php echo $columnsDataSourceDatabaseName[$column_key]; ?>"
                                                        data-sourcevaluepair="<?php echo $columnsDatasourceValuePair[$column_key]; ?>"
                                                        data-remotechain="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                        title = "<?php echo $columnsFieldName[$column_key]; ?>"
                                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>">
                                        <option value="">- Select <?php echo $columnsFieldName[$column_key]; ?> - </option>
                                          <?php
                                            if ($_GET['transactionmode']=='can_edit') {
                                                
                                                if (count($output)>0 && $output[0]["result"]=="1") { 
                                                    foreach ($output as $data_source_key => $data_source_value){

                                                    ?>          <option  value = "<?php echo $data_source_value[$value_pairs['0']] ?>"
                                                                <?php if ($data_source_value[$value_pairs['0']]==$retrievedRecordRow[0][$column_name]) {echo 'selected';} ?> 
                                                    >
                                                                            <?php echo  $data_source_value[$value_pairs['1']] ; ?> 
                                                                </option>

                                                                
                                                    <?php
                                                    } //foreach ($output as $key => $value){
                                                } //if($output[0]["result"]==='1'){
                                            } // if (count($output)>0 && $output[0]["result"]=="1") { 
                                            ?>           
                                         </select>
                                        <?php
                                    }        
                                    
                                } //if ($columnsDatasourceValuePair[$column_key]!=""){
                            } //else if ($columnsDataSource[$column_key]=="2"){
                        break; // case "7":

                        //8 - Radio Button
                        case "8":
                            // FROM STATIC DATA SOURCE
                            if ($columnsDataSource[$column_key]=="1"){
                                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                                
                                ?>
                                    <div class="radio-group masterfiletransaction-fields" data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>">
                                <?php
                                    if (count($value_pairs)>0){
                                        foreach ($value_pairs as $pair_key => $pair_value) {
                                            $static_values = explode(":",$pair_value);
                                        ?>
                                            <label class="radio-inline">
                                              <input type="radio" 
                                                <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                        $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==$static_values['0']){
                                                    echo ' checked ';
                                                } 
                                                else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                        $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                        $_GET['transactionmode']=='can_edit'&&$static_values["0"]==$retrievedRecordRow[0][$column_name]){
                                                    echo ' checked ';
                                                } ?>
                                                class=" modal_actions  <?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>" 
                                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$static_values["0"]; ?>" 
                                                value="<?php echo $static_values["0"];?>"> <?php echo $static_values["1"];?>
                                            </label>
                                        <?php
                                        } //foreach ($value_pairs as $pair_key => $pair_value) {
                                    } //if (count($value_pairs)>0){
                                ?>
                                    </div> <!-- radio group -->
                                <?php
                                
                            } //if ($columnsDataSource[$column_key]=="1"){
                            // FROM DATABASE SOURCE
                            else if ($columnsDataSource[$column_key]=="2"){
                                if ($columnsDatasourceValuePair[$column_key]!=""){
                                    $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);
                                    $link = DB_LOCATION;
                                    $params = array (
                                        "action" => "retrieve",
                                        "fileToOpen" => "default_select_query",
                                        "tableName" => $columnsDataSourceTableName[$column_key],
                                        "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                                        "columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']) ,
                                        "orderby" => $value_pairs['0']." ASC"
                                    );
                                    $result=processCurl($link,$params);
                                    $output = json_decode($result,true);
                                    $ctr = 0;
                                    
                                    ?>
                                        <div class="radio-group masterfiletransaction-fields" data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>">
                                    <?php
                                        if($output[0]["result"]==='1'){
                                            foreach ($output as $data_source_key => $data_source_value){
                                    ?>                      
                                                
                                                <label class="radio-inline">
                                                  <input type="radio" 
                                                    <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]==$data_source_value[$value_pairs['0']]){
                                                        echo ' checked ';
                                                    } 
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                                $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                                $_GET['transactionmode']=='can_edit'&&$data_source_value[$value_pairs['0']]==$retrievedRecordRow[0][$column_name]){
                                                            echo ' checked ';
                                                        }?>
                                                    class=" modal_actions  <?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                    name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>" 
                                                    id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$data_source_value[$value_pairs['0']]; ?>" 
                                                    value="<?php echo $data_source_value[$value_pairs['0']];?>"> 
                                                    <?php 
                                                        $valuepair1 = explode(" ",$value_pairs['1']);
                                                        foreach ($valuepair1 as $vp1){
                                                            echo $data_source_value[$vp1]." ";
                                                        }?>
                                                </label>

                                    <?php
                                            } //foreach ($output as $key => $value){
                                        } //if($output[0]["result"]==='1'){
                                    ?>
                                        </div> <!-- radio group -->
                                    <?php
                                    
                                } //if ($columnsDatasourceValuePair[$column_key]!=""){
                            } //else if ($columnsDataSource[$column_key]=="2"){
                        break; // case "8":


                        //9 - Checkbox
                        case "9":
                            // FROM STATIC DATA SOURCE
                            if ($columnsDataSource[$column_key]=="1"){
                                $value_pairs = explode(",",$columnsDatasourceValuePair[$column_key]);
                                
                                ?>
                                    <div class="checkbox-group  masterfiletransaction-fields" data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>">
                                        <label class="checkbox">
                                          <input type="checkbox" 
                                            data-children="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                            class="parent-checkboxes"
                                            id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-parent'; ?>" 
                                            value=""> All
                                        </label>
                                <?php
                                    if (count($value_pairs)>0){
                                        foreach ($value_pairs as $pair_key => $pair_value) {
                                            $static_values = explode(":",$pair_value);
                                        ?>
                                            <label class="checkbox">
                                              <input type="checkbox" 
                                                <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                        in_array($static_values['0'],$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])){
                                                    echo ' checked ';
                                                }
                                                else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                        $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                        $_GET['transactionmode']=='can_edit'&& /*$static_values["0"]==$retrievedRecordRow[0][$column_name]*/
                                                        strpos($retrievedRecordRow[0][$column_name],$static_values["0"]) !== false ){
                                                    echo ' checked ';
                                                } ?>
                                                class="modal_actions child-checkboxes <?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'[]'; ?>" 
                                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$static_values["0"]; ?>" 
                                                value="<?php echo $static_values["0"];?>"> <?php echo $static_values["1"];?>
                                            </label>
                                        <?php
                                        } //foreach ($value_pairs as $pair_key => $pair_value) {
                                    } //if (count($value_pairs)>0){
                                ?>
                                    </div> <!-- checkbox group -->
                                <?php
                                
                            } //if ($columnsDataSource[$column_key]=="1"){
                            // FROM DATABASE SOURCE
                            else if ($columnsDataSource[$column_key]=="2"){
                                if ($columnsDatasourceValuePair[$column_key]!=""){
                                    $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);


                                    $chainingparentid="";
                                    $chainingparentclass="";
                                    $chainingparentfield="";
                                    $conditions = array();
                                    $conditions_fieldname = array();
                                    $conditions_fieldvalue = array();


                                    /*parentclass=masterfiletransaction-name-9-941-user_line_dtl_codes|
                                    datasourcefieldtouse=line_mst_code*/
                                   
                                    if ($columnsSpecialConditions[$column_key]!=""){
                                        $explodedSpecCon = explode(",",$columnsSpecialConditions[$column_key]);
                                        foreach ($explodedSpecCon as $spccon){
                                            $explodedPerItemSpecCon = explode("=",$spccon);
                                            
                                            if ($explodedPerItemSpecCon["0"]=="chainingparentid"){
                                                $chainingparentid = $explodedPerItemSpecCon["1"];
                                            }

                                            else if ($explodedPerItemSpecCon["0"]=="chainingparentclass"){
                                                $chainingparentclass = $explodedPerItemSpecCon["1"];
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
                                            
                                        }
                                    }

                                    $link = DB_LOCATION;
                                    $params = array (
                                        "action" => "retrieve",
                                        "fileToOpen" => "default_select_query",
                                        "tableName" => $columnsDataSourceTableName[$column_key],
                                        "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                                        "columns" => $value_pairs['0'].",".str_replace(" ",",",$value_pairs['1']) ,
                                        "orderby" => $value_pairs['0']." ASC"
                                    );
                                    $result=processCurl($link,$params);
                                    $output = json_decode($result,true);
                                    $ctr = 0;
                                    
                                    ?>
                                        <div class="checkbox-group masterfiletransaction-fields" data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>">
                                            <label class="checkbox">
                                              <input type="checkbox" 
                                                data-children="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                                class="parent-checkboxes"
                                                id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-parent'; ?>" 
                                                value=""> All
                                            </label>
                                    <?php
                                        if($output[0]["result"]==='1'){
                                            foreach ($output as $data_source_key => $data_source_value){
                                    ?>                      
                                                <label class="checkbox <?php echo 'label-masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>" <?php     
                                                        if ($chainingparentfield != '')
                                                        {
                                                            $valuepair1 = explode(' ',$value_pairs['1']);
                                                            foreach ($valuepair1 as $vp1)
                                                                {
                                                                    //echo $chainingparentfield . " -- " . $vp1;
                                                                    if ($chainingparentfield == $vp1)
                                                                    {
                                                                    echo 'data-checkboxchaining="' . $data_source_value[$vp1].'" ';
                                                                    }                                                                    
                                                                }
                                                        }
                                                        ?>>
                                                  <input type="checkbox" 
                                                    <?php if(isset($retrievedRecordRow[0][$column_name])){ $exploded_values = explode(",", $retrievedRecordRow[0][$column_name]); }?>
                                                    <?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                            in_array($data_source_value[$value_pairs['0']],$_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])){
                                                        echo ' checked ';
                                                    }
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                            $_GET['transactionmode']=='can_edit' &&
                                                            /*$_GET['transactionmode']=='can_edit'&&$data_source_value[$value_pairs['0']]==$retrievedRecordRow[0][$column_name]*/
                                                            /*strpos($retrievedRecordRow[0][$column_name],$data_source_value[$value_pairs['0']]) !== false*/   
                                                            in_array($data_source_value[$value_pairs['0']],$exploded_values)){
                                                        echo ' checked ';
                                                    } ?>
                                                    data-parent="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-parent'; ?>"
                                                    <?php // if ($chainingparentid!=""){echo 'data-checkboxchaining="'.$chainingparentid.'"';} ?>
                                                    class="modal_actions child-checkboxes <?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"                                                    
                                                    name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'[]'; ?>" 
                                                    id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$data_source_value[$value_pairs['0']]; ?>" 
                                                    value="<?php echo $data_source_value[$value_pairs['0']];?>"> 
                                                    <?php 
                                                        if ($chainingparentfield != "")
                                                        {
                                                            $valuepair1 = explode(" ",$value_pairs['1']);
                                                            foreach ($valuepair1 as $vp1)
                                                                {
                                                                    if ($chainingparentfield == $vp1)
                                                                    {
                                                                    // echo $data_source_value[$vp1]." ";
                                                                    }
                                                                    else{
                                                                     echo $data_source_value[$vp1]." " ;
                                                                    }
                                                                }
                                                        }
                                                        else
                                                        {
                                                        $valuepair1 = explode(" ",$value_pairs['1']);    
                                                        foreach ($valuepair1 as $vp1){
                                                            echo $data_source_value[$vp1]." ";    

                                                        }    
                                                        }?>
                                                </label>
                                            
                                    <?php
                                            } //foreach ($output as $key => $value){
                                        } //if($output[0]["result"]==='1'){
                                    ?>
                                        </div> <!-- checkbox group -->
                                    <?php
                                    
                                } //if ($columnsDatasourceValuePair[$column_key]!=""){
                            } //else if ($columnsDataSource[$column_key]=="2"){
                        break; // case "9":

                        //10 - Lookup
                        case "10":
                            // using type ahead
                            $remotelink = "";
                            $templatefileloc = "";
                            $templateItself = "";
                            if ($columnsSpecialConditions[$column_key]!=""){
                                $explodedSpecCon = explode(",",$columnsSpecialConditions[$column_key]);
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
                            $value_pairs = explode(":",$columnsDatasourceValuePair[$column_key]);
                            $value_pairs_disp = explode(" ",$value_pairs["1"]);
                                
                            $firstVal = "";
                            $secondVal = "";
                            if ($_GET['transactionmode']=='can_edit'&&($retrievedRecordRow[0][$column_name]!="0"&&$retrievedRecordRow[0][$column_name]!="")){
                                // GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
                                $link = DB_LOCATION;
                                $params = array (
                                    "action" => "retrieve",
                                    "fileToOpen" => "default_select_query",
                                    "tableName" => $columnsDataSourceTableName[$column_key],
                                    "dbconnect" => $columnsDataSourceDatabaseName[$column_key],
                                    "columns" => str_replace(" ",",",str_replace(":",",",$columnsDatasourceValuePair[$column_key])),
                                    "orderby" => $value_pairs['0']." ASC",
                                    "conditions[equals][".$value_pairs['0']."]" => $retrievedRecordRow[0][$column_name]
                                );
                            
                                $result=processCurl($link,$params);
                                $output2 = json_decode($result,true);

                                if($output2[0]["result"]==='1'){
                                    foreach ($output2 as $data_source_key => $data_source_value){
                                        $firstVal = $data_source_value[$value_pairs_disp[0]];
                                        $secondVal = $data_source_value[$value_pairs_disp[1]];
                                    }

                                }
                            } // if ($_GET['transactionmode']=='can_edit'){
                             /*// GET THE DATA NEEDED FROM THE TABLE OF THE SPECIFIED FIELD
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
                                    if($output2[0]["result"]==='1'){*/   
                        ?>
                            <div class="row">
                                <input type="hidden" class="masterfiletransaction-fields modal_actions" readonly
                                     data-required="<?php echo $columnsIsRequired[$column_key];?>" data-unique="<?php echo $columnsIsUnique[$column_key];?>" data-type="<?php echo $columnsDataType[$column_key];?>"
                                    value="<?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])&&
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]!=''){
                                                        echo $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name];
                                                    } 
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name])||
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name]=='')&&
                                                            $_GET['transactionmode']=='can_edit'){
                                                        echo $retrievedRecordRow[0][$column_name];
                                                    } 
                                                ?>"
                                        id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>"
                                        name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name; ?>">
                                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12" style="padding-right:0px">
                                    <input type="text" placeholder="Type anything to search" autocomplete="off"
                                         class="form-control input-sm  modal_actions typeahead-fields typeahead"
                                         <?php echo ($remotelink!="")?'data-remotelink="'.$remotelink.'"':''; ?>
                                         <?php echo ($templateItself!="")?'data-template=\''.$templateItself.'\'':''; ?>
                                         data-sourcedb="<?php echo $columnsDataSourceDatabaseName[$column_key];?>"
                                         data-sourcetb="<?php echo $columnsDataSourceTableName[$column_key];?>"
                                         data-sourcecols="<?php echo str_replace(" ",",",str_replace(":",",",$columnsDatasourceValuePair[$column_key]));?>"
                                         data-fieldsearch="<?php echo str_replace(" ",",",str_replace(":",",",$columnsDatasourceValuePair[$column_key]));?>"
                                         data-clearbutton="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-clearbutton'; ?>"
                                         data-extrareturnfieldsnames="<?php echo $value_pairs[0]."|".$value_pairs_disp[1]; ?>"
                                         data-extrareturnfields="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name .
                                                                        '|masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]; ?>"
                                                <?php echo ($columnsMaxLength[$column_key]!==""&&$columnsMaxLength[$column_key]!="0"&&$columnsDataType[$column_key]!="4")?'maxlength="'.$columnsMaxLength[$column_key].'"':'';?> 
                                        value="<?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]])&&
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]]!=''){
                                                        echo $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]];
                                                    } 
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]])||
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]]=='')&&
                                                            $_GET['transactionmode']=='can_edit'){
                                                        echo $firstVal;
                                                    } 
                                                ?>" 
                                        id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]; ?>"
                                        name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[0]; ?>" />
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding-left:0px;padding-right:0px">
                                    <input type="text" placeholder="Description" autocomplete="off" readonly
                                         class="form-control input-sm  modal_actions"
                                        value="<?php if(isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]])&&
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]]!=''){
                                                        echo $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]];
                                                    } 
                                                    else if ((!isset($_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]])||
                                                            $_POST['masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]]=='')&&
                                                            $_GET['transactionmode']=='can_edit'){
                                                        echo $secondVal;
                                                    } 
                                                ?>" 
                                        id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]; ?>"
                                        name="<?php echo 'masterfiletransaction-name-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-'.$value_pairs_disp[1]; ?>" />
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12" style="padding-left:0px;padding-right:0px">
                                    <button class="btn btn-default btn-sm" type="button" 
                                    id="<?php echo 'masterfiletransaction-id-'.$columnsDataType[$column_key].'-'.$columnsCode[$column_key].'-'.$column_name.'-clearbutton'; ?>"><i class="fa fa-eraser"></i> Clear</button>
                                </div>
                            </div>
                                
                            
                        <?php
                        break; // case "10":
                        case "12":
                                        $appparamkey = '';
                                        if ($columnsSpecialConditions[$column_key]!=""){
                                        $explodedSpecCon = explode(",",$columnsSpecialConditions[$column_key]);
                                            foreach ($explodedSpecCon as $spccon){
                                                $explodedPerItemSpecCon = explode("=",$spccon);
                                                
                                                if ($explodedPerItemSpecCon["0"]=="appparamkey"){
                                                    $appparamkey = $explodedPerItemSpecCon["1"];
                                                }
                                                
                                            }        
                                        }
                                 
                                   $lch_MotorcarLineCode = "";
                                    // get parameters
                                   $lch_DBLocationString = DB_LOCATION;
                                    $larr_Params = array (
                                        "action" => "retrieve-template-columns",
                                        "fileToOpen" => "default_select_query",
                                        "tableName" => "mstapplicationparameter",
                                        "dbconnect" => MONEYTRACKER_DB,
                                        "columns" => "parameter_key,parameter_value",
                                        "conditions[equals][parameter_key]" => $appparamkey,
                                        "orderby" => "code ASC"
                                    );
                                    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
                                    $larr_OutputParams = json_decode($ljson_Result,true);
                                    if($larr_OutputParams[0]["result"]==='1'){
                                        foreach ($larr_OutputParams as $lch_Key => $larr_Value){
                                            $lch_MotorcarLineCode = $larr_Value["parameter_value"];

                                            ?> 
                                            <input type="hidden" id="<?php echo 'masterfiletransaction-id-' . $larr_Value['parameter_value']?>" disabled  
                                            value="<?php echo $larr_Value['parameter_value']?>">


                                            <?php
                                        } // foreach ($larr_OutputParams as $lch_Key => $larr_Value){
                                    } // if($larr_OutputParams[0]["result"]==='1'){        

                        break;



                    } // switch ($columnsDataType[$column_key]){
                ?>
                        </div>
                    </div>
                <?php
                } // foreach ($columns as $column_key => $column_name){
                ?>

                
                

                <?php echo $notes;?>
                <br><br>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm <?php echo $lch_SaveClass; ?>" id="save_modal_action" data-loading-text="Saving..." title="<?php echo ($_GET["transactionmode"]=="can_add")?"Save New Record":"Save Changes";?>" data-trans="<?php echo $_GET["transactionmode"];?>"><i class="fa fa-floppy-o"></i> <?php echo ($_GET["transactionmode"]=="can_add")?"Save New Record":"Save Changes";?></button>
                <button type="button" class="btn btn-default btn-sm <?php echo $lch_CancelClass ?>" title="Cancel and Close this modal" data-trans="<?php echo $_GET["transactionmode"];?>"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
