<?php

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_to_pay_log",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
//echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-list fa-fw"></i> To Pay as of <?php echo date("F d, Y");?>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        
		<?php
		if (count($larr_ResultDataAnalytics)>0) {
		?>
			<div class="list-group">
			<?php

			$lde_total = 0.00;

			foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
				// 
				$lch_fontsize="14px";
				$lch_displayname = $larr_Value["description"];
				if (strlen($lch_displayname)>40) {
					$lch_fontsize="10px";
				}
				else if (strlen($lch_displayname)>20) {
					$lch_fontsize="12px";
				}
				else if (strlen($lch_displayname)>10) {
					$lch_fontsize="13px";
				}

				$lde_total += floatval($larr_Value["to_pay"]);
			?>
				<div class="list-group-item" style="font-size:<?php echo $lch_fontsize;?>">
	                <?php echo $lch_displayname;?>
	                <span class="pull-right " style="font-size:14px"><strong><?php echo number_format($larr_Value["to_pay"],2) ;?></strong>
	                </span>
	            </div>
			<?php
			} // foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
			?>

				<div class="list-group-item right" style="font-size:24px">
	                <strong>TOTAL</strong>&nbsp;&nbsp;
	                <span class="pull-right " style=""><strong><u><?php echo number_format($lde_total,2) ;?></u></strong>
	                </span>
	            </div>

			</div>
			
		<?php
		} // if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {
		?>
	</div>
    <!-- /.panel-body -->
</div>