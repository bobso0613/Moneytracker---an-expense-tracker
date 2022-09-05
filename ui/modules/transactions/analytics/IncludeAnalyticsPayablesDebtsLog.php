<?php

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_payables_debts_log",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
// echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-list fa-fw"></i> Payables/Debts as of <?php echo date("F d, Y");?>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        
		<?php
		if (count($larr_ResultDataAnalytics)>0) {
			$lin_ctr = 1;
			$lin_TotalPayables = 0.00;
		?>
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php
				foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {

					$lch_fontsize="14px";
					$lch_displayname = $larr_Value["reference_name"];
					if (strlen($lch_displayname)>40) {
						$lch_fontsize="10px";
					} // if (strlen($lch_displayname)>40) {
					else if (strlen($lch_displayname)>20) {
						$lch_fontsize="12px";
					} // else if (strlen($lch_displayname)>20) {
					else if (strlen($lch_displayname)>10) {
						$lch_fontsize="13px";
					} // else if (strlen($lch_displayname)>10) {

					$lde_total = 0.00;
					$lde_totalotherpeople = 0.00;
					

				?>
					<div class="list-group" style="margin:0px">
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="heading_debts_<?php echo $lin_ctr;?>" style="padding:0px">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_debts_<?php echo $lin_ctr;?>" aria-expanded="true" aria-controls="collapse_debts_<?php echo $lin_ctr;?>">
									
										<div class="list-group-item" style="font-size:<?php echo $lch_fontsize;?>">
							                <?php echo $lch_displayname;?>
							                <span class="pull-right " style="font-size:14px"><strong><?php echo number_format($larr_Value["to_pay"],2) ;?></strong>
							                </span>
							            </div>
									
								</a>
							</div>
							<div id="collapse_debts_<?php echo $lin_ctr;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_debts_<?php echo $lin_ctr;?>">
								<div class="panel-body" style="font-size:<?php echo $lch_fontsize;?>;padding-left:40px">
									<table class="table table-condensed table-small-font table-bordered" style="margin:0px;padding:0px">
										<thead>
											<tr>
												<th class="table-header">Date</th>
												<th class="table-header">Transaction</th>
												<th class="table-header right">Amount</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// echo $lch_Key."<br>";
											foreach ($larr_Value["breakdown"] as $lch_InnerKey => $larr_InnerValue) {
												$lde_total += floatval($larr_InnerValue["total_amount_raw"]);
												$lin_TotalPayables += floatval($larr_InnerValue["total_amount_raw"]);

												if($larr_InnerValue["ar_or_ap"]!="") {
													$lde_totalotherpeople += floatval($larr_InnerValue["total_amount_raw"]);
												} // if($larr_InnerValue["ar_or_ap"]!="") {

											?>
												<tr>
													<td style="width:7%"><?php echo date_format(date_create($larr_InnerValue["money_trail_date"]),"m/d");?></td>
													<td style="width:68%"><div style="overflow-x:auto;overflow-y:auto;max-height:100px"><?php if($larr_InnerValue["ar_or_ap"]!="") { echo '<i>'.$larr_InnerValue["ar_or_ap"].'</i> - ';} ?><?php echo $larr_InnerValue["description"];?></div></td>
													<td class="right" style="width:25%"><?php echo $larr_InnerValue["total_amount"];?></td>
												</tr>
											<?php
											} // foreach ($larr_Value["breakdown"] as $lch_InnerKey => $larr_InnerValue) {
											?>
											<tr>
												<td colspan="2" class="right"><strong>TOTAL</strong></td>
												<td class="right" style="width:25%"><strong><?php echo number_format($lde_total,2);?></strong></td>
											</tr>

											<?php
											if ($lde_totalotherpeople>=0.00) {
											?>
												<tr>
													<td colspan="2" class="right"><strong>LESS - AR/AP of others</strong></td>
													<td class="right" style="width:25%"><strong>(<?php echo number_format($lde_totalotherpeople,2);?>)</strong></td>
												</tr>
												<tr>
													<td colspan="2" class="right"><strong>NET AMOUNT</strong></td>
													<td class="right" style="width:25%"><strong><?php echo number_format($lde_total-$lde_totalotherpeople,2);?></strong></td>
												</tr>
											<?php
											} // if ($lde_totalotherpeople>=0.00) {
											?>

										</tbody>
									</table>
									
								</div>
							</div>
						</div>
					</div>
				<?php
					$lin_ctr++;
				} // foreach ($larr_ResultDataAnalytics as $lch_Key => $larr_Value) {
				?>
			</div>

			<div class="list-group" style="margin:0px">
				<div class="list-group-item right" style="font-size:24px">
	                <strong>TOTAL</strong>&nbsp;&nbsp;
	                <span class="pull-right " style=""><strong><u><?php echo number_format($lin_TotalPayables,2) ;?></u></strong>
	                </span>
	            </div>
            </div>
			
		<?php
		} // if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {
		?>
	</div>
    <!-- /.panel-body -->
</div>