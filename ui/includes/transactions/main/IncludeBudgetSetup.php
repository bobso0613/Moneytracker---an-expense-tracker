<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && isset($_GET["money_trail_type_mst_code"]) && isset($_GET["booking_year"]) ) {
	@session_start();

	date_default_timezone_set('Asia/Manila');

    // lookup first for the code being queried
    require_once("../../../api/SystemConstants.php");
    require_once("../../../api/CurlAPI.php");
    $lch_DBLocationString = DB_LOCATION;

    $larr_TRNBudget = array();

    $larr_Params = array (
        "action" => "retrieve",
        "fileToOpen" => "transactions/main/save_trnbudget",
        "money_trail_type_mst_code" => @$_GET["money_trail_type_mst_code"],
        "booking_year" => @$_GET["booking_year"],
        "transactionmode"=>"get_budget_setup",
        "user_code"=>@$_SESSION['user_code']
    );
    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
    $x = $ljson_Result;
    $larr_TRNBudget = json_decode($ljson_Result,true);

    if (count($larr_TRNBudget)>0 && $larr_TRNBudget[0]["result"]=="1") {
    ?>	
    	<br><br>
    	<h4>Budget Setup for <strong><?php echo $larr_TRNBudget[0]["money_trail_type"]["money_trail_name"];?></strong> (<?php echo $larr_TRNBudget[0]["money_trail_type"]["trail_type_description"];?>)</h4>
    	<div  style="overflow-x:auto">
		    <table class="table table-bordered  compact  table-condensed " style="margin-bottom:2px" id="transaction_table_trail" >
		    	<thead>
		    		<tr>
		    			<th class="table-header center" style="border-right:1px solid white;">Month</th>
		    			<th class="table-header center" style="border-right:1px solid white;">Budget Amount</th>
		    			<th class="table-header center" style="border-right:1px solid white;">Actual Amount</th>
		    			<th class="table-header center" style="border-right:1px solid white;">Difference</th>
		    			<th class="table-header center" style="border-right:1px solid white;">Remarks</th>
		    		</tr>
		    	</thead>
		    	<tbody>
		    	<?php
		    	foreach ($larr_TRNBudget[0]["budget"] as $lch_Key => $larr_Value) {
		    	?>
		    		<input type="hidden" class="outer-fields-unique-inverted" name="trnbudget_code[]" id="trnbudget_code_<?php echo $lch_Key;?>" value="<?php echo $lch_Key;?>" readonly disabled/>
		    		<tr>
		    			<td class="center"><strong><?php echo $larr_Value["name"];?></strong></td>
		    			<td class="right"><input type="text" class="form-control outer-fields-unique-inverted  decimals right trnbudget_budget_amounts" data-code="<?php echo $lch_Key;?>" name="trnbudget_budget_amount_<?php echo $lch_Key;?>" id="trnbudget_budget_amount_<?php echo $lch_Key;?>" value="<?php echo $larr_Value["budget_amount"];?>" disabled></td>
		    			<td class="right"><input type="text" class="form-control outer-fields-unique-inverted  decimals right" name="trnbudget_actual_amount_<?php echo $lch_Key;?>" id="trnbudget_actual_amount_<?php echo $lch_Key;?>" value="<?php echo $larr_Value["actual_amount"];?>" readonly disabled></td>
		    			<td class="right"><input type="text" class="form-control outer-fields-unique-inverted  decimals right" name="trnbudget_diff_amount_<?php echo $lch_Key;?>" id="trnbudget_diff_amount_<?php echo $lch_Key;?>" value="<?php echo $larr_Value["diff_amount"];?>" readonly disabled></td>
		    			<td class=""><textarea class="form-control outer-fields-unique-inverted " name="trnbudget_remarks_<?php echo $lch_Key;?>" id="trnbudget_remarks_<?php echo $lch_Key;?>" rows="1" disabled><?php echo $larr_Value["remarks"];?></textarea></td>
		    		</tr>
		    	<?php
		    	} // foreach ($larr_TRNBudget[0]["budget"] as $lch_Key => $larr_Value) {
		    	?>
    			</tbody>
    		</table>
    	</div>
    <?php
    } // if (count($larr_TRNBudget)>0 && $larr_TRNBudget[0]["result"]=="1") {
    else {
    ?>
    	<div class="alert alert-danger prompt_containers" id="">
	        <h3 id="item_info_error_prompt_message">
	            Cannot access the API server. Please try again later.
	        </h3>
	    </div> <!-- .alert .alert-danger -->
    <?php
    } // ELSE ng  if (count($larr_TRNBudget)>0 && $larr_TRNBudget[0]["result"]=="1") {

} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && isset($_GET["money_trail_type_mst_code"]) && isset($_GET["booking_year"]) ) {
else {
?>
	<div class="alert alert-danger prompt_containers" id="">
        <h3 id="item_info_error_prompt_message">
            Cannot access the API server. Please try again later.
        </h3>
    </div> <!-- .alert .alert-danger -->
<?php
} // ELSE ng if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && isset($_GET["money_trail_type_mst_code"]) && isset($_GET["booking_year"]) ) {
?>