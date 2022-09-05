<?php

// GET ACTUAL ANALYTICS FOR THE DATA
$larr_Params = array (
    "fileToOpen" => "transactions/analytics/retrieve_analytics_yearly_earnings_chart",
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
				if (count($larr_SeriesValue)>0) {

					if (array_key_exists("normal", $larr_SeriesValue)){
						$lch_series .= $lch_SeriesKey.":{normal:".implode(",",$larr_SeriesValue)."},";
					} // if (array_key_exists("normal", $larr_SeriesValue)){
					else {
						$lch_series .= $lch_SeriesKey.":[".implode(",",$larr_SeriesValue)."],";
					} // ELSE ng if (array_key_exists("normal", $larr_SeriesValue)){

					
				}
				else {
					$lch_series .= $lch_SeriesKey.":{},";
				}
				
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
        <i class="fa fa-line-chart fa-fw"></i> Yearly Earnings - <strong><?php echo date("Y");?></strong>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body" id="yearly_earnings_body" style="overflow-x:auto;padding:0px;background-color:#fff">
    	<div id="yearly_earnings_chart" style="width:800px;height:600px;text-align:center">
    		
    	</div>
    </div>
    <!-- /.panel-body -->
</div>

<script type="text/javascript">
    
	$(document).ready(function(){

	    document.getElementById('yearly_earnings_chart').style.width = document.getElementById('yearly_earnings_body').offsetWidth + "px";
	    var yearly_earnings_chart = echarts.init(document.getElementById('yearly_earnings_chart'));

	    var larr_Months = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

	    // specify chart configuration item and data
	    var yearly_earnings_chart_option = {
	    	color:['#5cb85c','#d9534f', '#ff9700', "#0800ff","#dbd711", "#a56908", "#14e5c9", "#a316e0", "#534b8e", "#758e18", "#ff0000", "#d63bb7", "#ff77ef", "#dcff9b" ],
	        title: {
	            text: ''
	        },
	        tooltip : {
		        trigger: 'axis',
		        extraCssText: 'text-align:right;',
		        axisPointer : {       
		            type : 'shadow'     
		        }
		    },
	        legend: {
	            data:<?php echo $lch_legends;?>,
	            bottom:0,
	            
	        },
	        toolbox: {
		        feature: {
		            saveAsImage: {title:"Save as Image",name:"Yearly Earnings Chart as of <?php echo date("m/d/Y") ;?>"},
		            dataView:{title:"Data View",lang:["Data View","Turn off","Refresh"],readOnly:true},
			        dataZoom:{title:"Data Zoom"},
			        restore:{title:"Restore"}
		        },
		        
		    },
	        grid:{
	        	top:25,
	        	bottom:80,
		        containLabel: true
	        },
	        xAxis: {
	        	type : 'category',
            	boundaryGap : false,
	            data: larr_Months
	        },
	        yAxis: {},
	        series: <?php echo $lch_series;?>
	    };


	    window.addEventListener('resize', function(event){
		  // do stuff here
		  document.getElementById('yearly_earnings_chart').style.width = document.getElementById('yearly_earnings_body').offsetWidth + "px";
		  yearly_earnings_chart.resize();
		});

		// use configuration item and data specified to show chart
		yearly_earnings_chart.setOption(yearly_earnings_chart_option);

		$("#logo-direction2,#logo-direction").on("click",function(){
			setTimeout(function(){
				document.getElementById('yearly_earnings_chart').style.width = document.getElementById('yearly_earnings_body').offsetWidth + "px";
		  		yearly_earnings_chart.resize();
			}, 500);
			
		});
	
	}); // $(document).ready(function(){

</script>