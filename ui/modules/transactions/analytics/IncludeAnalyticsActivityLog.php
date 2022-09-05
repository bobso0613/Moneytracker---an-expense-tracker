<?php

$larr_AppParams = array();
// get parameters
$larr_Params = array (
    "action" => "retrieve-template-columns",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstapplicationparameter",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "parameter_key,parameter_value",
    "conditions[in][parameter_key]" => 'module_action_add_code,module_action_edit_code,module_action_delete_code,module_action_print_code,module_action_upload_code,module_action_post_code,module_action_void_code',
    "orderby" => "code ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_OutputParams = json_decode($ljson_Result,true);
if($larr_OutputParams[0]["result"]==='1'){
    foreach ($larr_OutputParams as $lch_Key => $larr_Value){
        $larr_AppParams[$larr_Value["parameter_key"]] = $larr_Value["parameter_value"];
    } // foreach ($larr_OutputParams as $lch_Key => $larr_Value){

} // if($larr_OutputParams[0]["result"]==='1'){


$larr_ButtonIconcClass  = array($larr_AppParams["module_action_edit_code"]=>"fa fa-pencil-square-o",
								$larr_AppParams["module_action_delete_code"]=>"fa fa-trash-o",
								$larr_AppParams["module_action_add_code"]=>"fa fa-plus-circle",
								$larr_AppParams["module_action_print_code"]=>"fa fa-print",
								$larr_AppParams["module_action_upload_code"]=>"fa fa-upload");

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_activity_log",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
//echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-list fa-fw"></i> Last 10 Actions
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        
		<?php
		if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {
		?>
			<div class="list-group">
			<?php

			foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
				// 
				$lch_fontsize="13px";
				$lch_displayname = $larr_Value["module_name"] . " - " .  $larr_Value["reference"];
				if (strlen($lch_displayname)>40) {
					$lch_fontsize="9px";
				}
				else if (strlen($lch_displayname)>20) {
					$lch_fontsize="10px";
				}
				else if (strlen($lch_displayname)>10) {
					$lch_fontsize="11px";
				}

			?>
				<div class="list-group-item" style="font-size:<?php echo $lch_fontsize;?>">
					<i class="<?php echo $larr_ButtonIconcClass[$larr_Value["module_action_mst_code"]] ;?> fa-fw" style="font-size:12px"></i>
	                <?php echo $lch_displayname;?>
	                <span class="pull-right text-muted small" style="font-size:12px"><em><?php echo date_format(date_create($larr_Value["created_at"]),"h:ia m/d");?></em>
	                </span>
	            </div>
			<?php
			} // foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
			?>
			</div>
		<?php
		} // if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {
		?>
	</div>
    <!-- /.panel-body -->
</div>