<?php

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_running_balance_log",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
//echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-list fa-fw"></i> Running Balance as of <?php echo date("F d, Y");?>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        
		<?php
		if (count($larr_ResultDataAnalytics)>0) {
		?>
			
			<?php
			foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
				//if (floatval(round($larr_Value["running_balance"],2))!=0.00){ 
			?>
				<div style="margin-top:0;margin-bottom:5px;font-size:24px"><?php echo $larr_Value["money_trail_name"] ;?> - 
					<div class="pull-right"><strong><?php echo number_format($larr_Value["running_balance"],2) ;?></strong></div>
				</div>
				
			<?php
				// } // if (floatval($larr_Value["running_balance")!=0.00){ 
			} // foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
			?>
			
		<?php
		} // if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {
		?>
	</div>
    <!-- /.panel-body -->
</div>