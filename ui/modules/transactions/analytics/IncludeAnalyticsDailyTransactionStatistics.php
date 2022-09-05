<?php
// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_daily_transaction_statistics",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
//echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);
?>
<div class="row">
	<div class="col-lg-3 col-md-6">
	    <div class="panel panel-primary"  data-toggle="tooltip" data-placement="bottom" title="Total Trails you processed today">
	        <div class="panel-heading">
	            <div class="row">
	                <div class="col-xs-3">
	                <i class="fa fa-file-text fa-3x"></i>
	                </div>
	                <div class="col-xs-9 text-right">
	                    <div class="huge"><?php echo $larr_ResultDataAnalytics[0]["transaction_count"]; ?></div>
	                    <div><font style="font-size:11px">Trails Dated Today</font></div>
	                </div>
	            </div>
	        </div>
	        <?php /*
	        <a href="#">
	            <div class="panel-footer">
	                <span class="pull-left">View Details</span>
	                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                <div class="clearfix"></div>
	            </div>
	        </a>
	        */ ?>
	    </div>
	</div>
	<div class="col-lg-3 col-md-6">
	    
        <div class="panel panel-red"  data-toggle="tooltip" data-placement="bottom" title="Total Expenses for today">
	        <div class="panel-heading">
	            <div class="row">
	                <div class="col-xs-3">
	                    <i class="fa fa-arrow-down fa-3x"></i>
	                </div>
	                <div class="col-xs-9 text-right">
	                    <div class="huge"><?php echo $larr_ResultDataAnalytics[0]["expense_amount"]; ?></div>
	                    <div><font style="font-size:11px">Expenses for today</font></div>
	                </div>
	            </div>
	        </div>
	        <?php /*
	        <a href="#">
	            <div class="panel-footer">
	                <span class="pull-left">View Details</span>
	                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                <div class="clearfix"></div>
	            </div>
	        </a>
	        */ ?>
	    </div>
	</div>
	<div class="col-lg-3 col-md-6">
	    <div class="panel panel-green"  data-toggle="tooltip" data-placement="bottom" title="Total Income for today">
	        <div class="panel-heading">
	            <div class="row">
	                <div class="col-xs-3">
	                    <i class="fa fa-arrow-up fa-3x"></i>
	                </div>
	                <div class="col-xs-9 text-right">
	                    <div class="huge"><?php echo $larr_ResultDataAnalytics[0]["income_amount"]; ?></div>
	                    <div><font style="font-size:11px">Income for today</font></div>
	                </div>
	            </div>
	        </div>
	        <?php /*
	        <a href="#">
	            <div class="panel-footer">
	                <span class="pull-left">View Details</span>
	                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                <div class="clearfix"></div>
	            </div>
	        </a>
	        */ ?>
	    </div>
	</div>
	<div class="col-lg-3 col-md-6">
	    <div class="panel panel-info"  data-toggle="tooltip" data-placement="bottom" title="Net Income/Expense for the month">
	        <div class="panel-heading">
	            <div class="row">
	                <div class="col-xs-3">
	                    <i class="fa fa-money fa-3x"></i>
	                </div>
	                <div class="col-xs-9 text-right">
	                    <div class="huge"><font style="font-size:75%;"><?php echo $larr_ResultDataAnalytics[0]["amount_received_month"]; ?></font></div>
	                    <div><font style="font-size:11px">Net Gain/Loss for <?php echo date("M Y"); ?></font></div>
	                </div>
	            </div>
	        </div>
	        <?php /*
	        <a href="#">
	            <div class="panel-footer">
	                <span class="pull-left">View Details</span>
	                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                <div class="clearfix"></div>
	            </div>
	        </a>
	        */ ?>
	    </div>
	</div>
</div>