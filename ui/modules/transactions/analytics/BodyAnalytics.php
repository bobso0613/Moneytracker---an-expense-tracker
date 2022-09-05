<?php
date_default_timezone_set('Asia/Manila');
require_once("api/SystemConstants.php");
require_once("api/CurlAPI.php");

@session_start();

$lch_DBLocationString = DB_LOCATION;

$larr_UserDetails = array();
$larr_MSTAnalytics = array();

function add_quotes($str) {
    return sprintf("'%s'", $str);
}

/**
 * @param int $time Time in seconds
 * @return string
 * @example 3690 seconds = 1h 1m 30s 
 */

function intervalToString($time) {
    if ($time >= 0 && $time < 60)
        return $time . 's';

    if ($time >= 60 && $time < 3600)
        return (intval($time / 60)) . 'm ' . (intervalToString($time % 60));

    if ($time >=3600 && $time < 86400)
        return (intval($time / 3600)) . 'h ' . (intervalToString($time % 3600));

    if ($time >=86400 && $time < 604800)
        return (intval($time / 86400)) . 'd ' . (intervalToString($time % 86400));

    if ($time >=604800 )
        return (intval($time / 604800)) . 'w ' . (intervalToString($time % 604800));
}

// GET USER DETAILS
$larr_Params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstuser",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "code,username,whole_name" ,
    "conditions[equals][code]" => @$_SESSION["user_code"],
    "orderby" => "code ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_UserDetails = json_decode($ljson_Result,true);

// GET ACCESSIBLE ANALYTICS FOR THE USER
$larr_Params = array (
    "action" => "retrieve",
    "fileToOpen" => "default_select_query",
    "tableName" => "mstanalytics",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "*" ,
    "conditions[find_in_set][accessible_user_mst_codes]" => $larr_UserDetails[0]["code"],
    "orderby" => "type ASC, order_no ASC"
);
$ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
$larr_MSTAnalytics = json_decode($ljson_Result,true);


$larr_TypeSizeClass = array("1" => "col-lg-12 col-md-12 col-sm-12 col-sm-12",
							"2" => "col-lg-6 col-md-6 col-sm-6 col-xs-12",
							"3" => "col-lg-6 col-md-6 col-sm-6 col-xs-12");

$larr_ArrayLocation = array(0=>array(),1=>array());

if (count($larr_MSTAnalytics)>0 && $larr_MSTAnalytics[0]["result"]=="1"){
?>
	<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/echarts.min.js<?php echo VERSION_AFFIX; ?>"></script> 
	<div class="row">
	<?php
    $lin_flag = 0;
	foreach ($larr_MSTAnalytics as $lch_Key => $larr_Value) {
		$larr_ResultDataAnalytics = array();
		/* <h5><?php echo $larr_Value["analytics_name"];?></h5> */

        if ($larr_Value["type"]=="1") {
	?>
		<div class="<?php echo $larr_TypeSizeClass[$larr_Value["type"]];?>">
			<?php include($larr_Value["include_filename"].".php");?>
		</div>
	<?php
        } // if ($larr_Value["type"]=="1") {
        else {
            array_push($larr_ArrayLocation[$lin_flag], $larr_Value);
            if ($lin_flag==1) {
                $lin_flag = 0;
            } // if ($lin_flag==1) {
            else {
                $lin_flag = 1;
            } // ELSE ng if ($lin_flag==1) {
        } // ELSE ng if ($larr_Value["type"]=="1") {
	} // foreach ($larr_MSTAnalytics as $lch_Key => $larr_Value) {
    ?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <?php 
        if (count($larr_ArrayLocation[0])>0) {
            foreach ($larr_ArrayLocation[0] as $lch_Key => $larr_Value) {
            ?>
                <?php include($larr_Value["include_filename"].".php");?>
            <?php
            } // foreach ($larr_ArrayLocation[0] as $lch_Key => $larr_Value) {
        } // if (count($larr_ArrayLocation[0])>0) {
        ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <?php 
        if (count($larr_ArrayLocation[1])>0) {
            foreach ($larr_ArrayLocation[1] as $lch_Key => $larr_Value) {
            ?>
                <?php include($larr_Value["include_filename"].".php");?>
            <?php
            } // foreach ($larr_ArrayLocation[1] as $lch_Key => $larr_Value) {
        } // if (count($larr_ArrayLocation[1])>0) {
        ?>
        </div>
    <?php
	?>
	</div>
<?php
} // if (count($larr_MSTAnalytics)>0 && $larr_MSTAnalytics[0]["result"]=="1"){
else {
?>

<?php
} // ELSE ng if (count($larr_MSTAnalytics)>0 && $larr_MSTAnalytics[0]["result"]=="1"){
?>

