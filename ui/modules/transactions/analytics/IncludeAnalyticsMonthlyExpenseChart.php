<?php

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_monthly_expense_chart",
    "user_code" => $larr_UserDetails[0]["code"]
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
//echo $ljson_Result;
$larr_ResultDataAnalytics = json_decode($ljson_Result,true);

$lch_series = "[]";
$lch_legends = "[]";

// formatter for JS format
if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics["result"]=="1") {
	$lch_series = "[";
	foreach ($larr_ResultDataAnalytics["series"] as $lch_Key => $larr_Value) {
		$lch_series .= "{";
		foreach ($larr_Value as $lch_SeriesKey => $larr_SeriesValue) {
			if (is_array($larr_SeriesValue)) {
				$lch_series .= $lch_SeriesKey.":[".implode(",",$larr_SeriesValue)."],";
			} // if (is_array($larr_SeriesValue)) {
			else {
				$lch_series .= $lch_SeriesKey.":'".$larr_SeriesValue."',";
			} // ELSE ng if (is_array($larr_SeriesValue)) {
			
		} // foreach ($larr_Value as $lch_SeriesKey => $larr_SeriesValue) {
		$lch_series = rtrim($lch_series,",");
		$lch_series .= "},";
	} // foreach ($larr_ResultDataAnalytics[0]["series"] as $lch_Key => $larr_Value) {
	$lch_series = rtrim($lch_series,",");
	$lch_series .= "]";

	$lch_legends = "[";
	$lch_legends .= implode(",",  array_map('add_quotes', $larr_ResultDataAnalytics["legends"]));
	$lch_legends .= "]";

} // if (count($larr_ResultDataAnalytics)>0 && $larr_ResultDataAnalytics[0]["result"]=="1") {

//echo $lch_series;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-pie-chart fa-fw"></i> Monthly Expense - <strong><?php echo date("F Y");?></strong>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body" id="monthly_expense_body" style="overflow-x:auto;padding:0px;background-color:#fff">
    	<div id="monthly_expense_chart" style="width:800px;height:400px;text-align:center">
    		
    	</div>
    </div>
    <!-- /.panel-body -->
</div>

<script type="text/javascript">
    
	$(document).ready(function(){

	    document.getElementById('monthly_expense_chart').style.width = document.getElementById('monthly_expense_body').offsetWidth + "px";
	    var monthly_expense_chart = echarts.init(document.getElementById('monthly_expense_chart'));

	    var larr_Months = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

	    // specify chart configuration item and data
	    var monthly_expense_chart_option = {
	    	color:['#ff9700', "#0800ff","#dbd711", "#a56908", "#14e5c9", "#a316e0", "#534b8e", "#758e18", "#ff0000", "#85c8ea", "#ff77ef", "#dcff9b" ],
	        title: {
	            text: ''
	        },
	        tooltip : {
		        trigger: 'item',
		        formatter: "{b}: {c} ({d}%)",
		        //extraCssText: 'text-align:right;',
		        axisPointer : {       
		            type : 'shadow'     
		        }
		    },
	        legend: {
	        	orient: 'vertical',
        		x: 'left',
	            data:<?php echo $lch_legends;?>
	            
	        },
	        toolbox: {
		        feature: {
		            saveAsImage: {title:"Save as Image",name:"Monthly Expense <?php echo date("F Y") ;?>"},
		            dataView:{title:"Data View",lang:["Data View","Turn off","Refresh"],readOnly:true},
			        restore:{title:"Restore"}
		        },
		        
		    },
	        series: {
	            name:'Monthly Expense Chart',
	            type:'pie',
	            radius: ['50%', '70%'],
	            avoidLabelOverlap: false,
	            label: {
	                normal: {
	                    show: false,
	                    position: 'center'
	                },
	                emphasis: {
	                    show: true,
	                    textStyle: {
	                        fontSize: '16',
	                        fontWeight: 'bold'
	                    }
	                }
	            },
	            labelLine: {
	                normal: {
	                    show: false
	                }
	            },
	            data:<?php echo $lch_series;?>
	        }
	    };


	    window.addEventListener('resize', function(event){
		  // do stuff here
		  document.getElementById('monthly_expense_chart').style.width = document.getElementById('monthly_expense_body').offsetWidth + "px";
		  monthly_expense_chart.resize();
		});

		// use configuration item and data specified to show chart
		monthly_expense_chart.setOption(monthly_expense_chart_option);

		$("#logo-direction2,#logo-direction").on("click",function(){
			setTimeout(function(){
			  document.getElementById('monthly_expense_chart').style.width = document.getElementById('monthly_expense_body').offsetWidth + "px";
		  		monthly_expense_chart.resize();
			}, 500);
			
		});
	
	}); // $(document).ready(function(){

</script>