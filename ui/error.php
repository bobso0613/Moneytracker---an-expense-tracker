<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Error";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("refresh");
//$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);

$PAGE_SETTINGS["menu_program_name"] = "";
$PAGE_SETTINGS["UseBaseLink"] = true;
$PAGE_SETTINGS["NoNightMode"] = false;

$PAGE_SETTINGS["CssEnable"] = array();
$PAGE_SETTINGS["CssEnable"]["Timeline"] = false;
$PAGE_SETTINGS["CssEnable"]["Morris"] = false;
$PAGE_SETTINGS["CssEnable"]["Chat"] = false;
$PAGE_SETTINGS["CssEnable"]["DataTables"] = false;
$PAGE_SETTINGS["CssEnable"]["SocialButtons"] = false;
$PAGE_SETTINGS["CssEnable"]["Calendar"] = true;
$PAGE_SETTINGS["CssEnable"]["OnlineUsers"] = true;


$PAGE_SETTINGS["JSEnable"] = array();
$PAGE_SETTINGS["JSEnable"]["DatePicker"] = false;
$PAGE_SETTINGS["JSEnable"]["Morris"] = false;
$PAGE_SETTINGS["JSEnable"]["Chat"] = false;
$PAGE_SETTINGS["JSEnable"]["DataTables"] = false;
$PAGE_SETTINGS["JSEnable"]["Number"] = true;
$PAGE_SETTINGS["JSEnable"]["Flot"] = false;
$PAGE_SETTINGS["JSEnable"]["Calendar"] = true;
$PAGE_SETTINGS["JSEnable"]["OnlineUsers"] = true;
$PAGE_SETTINGS["JSEnable"]["LogoutCheck"] = true;

$larr_Users  = array();

/*
ErrorDocument 500 /iisaac-web/error.php?type=internal_error
ErrorDocument 401 /iisaac-web/error.php?type=unauthorized
ErrorDocument 402 /iisaac-web/error.php?type=payment_required
ErrorDocument 403 /iisaac-web/error.php?type=forbidden
ErrorDocument 404 /iisaac-web/error.php?type=not_found
ErrorDocument 405 /iisaac-web/error.php?type=not_allowed
*/

$larr_ErrorTypes = array(
	"internal_error"=>array("title"=>"Internal Server Error (500)",
						"message"=>"Please contact system administrator."),
	"unauthorized"=>array("title"=>"Unauthorized Access (401)",
						"message"=>"You are unauthorized to access this page."),
	"payment_required"=>array("title"=>"Access Forbidden (402)",
						"message"=>"You are not allowed to access this page."),
	"forbidden"=>array("title"=>"Access Forbidden (403)",
						"message"=>"You are not allowed to access this page."),

	"not_found"=>array("title"=>"The page you are looking for was not found. (404)",
						"message"=>"Please double check the link you typed in the address bar."),

	"not_allowed"=>array("title"=>"Method Not Allowed (405)",
						"message"=>"Please contact system administrator for more details.")
	);

$larr_ErrorMessage = array();

if (@$_GET["type"]!=""){

	// error depending on type
	$larr_ErrorMessage = $larr_ErrorTypes[@$_GET["type"]];

} // if (@$_GET["type"]!=""){
else {

    // generic error message
    $larr_ErrorMessage = $larr_ErrorTypes["not_found"];

} // ELSE ng if (@$_GET["type"]!=""){


if ( (!isset($_SESSION['PHPSESSID']) && !(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!==''))||
	(!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')) ){


	$PAGE_SETTINGS["NavigationType"] = "corporate";

	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_header_navigation.php");
	echo '<div id="announcement-container">';
	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_announcements.php");
	echo '</div>';



	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");
	?>

	<div id="error_cont" style="position:fixed !important;position:absolute;top:0;right:0;bottom:0;left:0;">
		<div class="" style=" position: relative;top: 5%;">
			<div class="row" style="margin-top:75px">
		        <div class="col-lg-12 center">

		            <img class="error-image-logo" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/samplelogo.png">

		        </div>
		        <!-- /.col-lg-12 -->
		    </div>

		    
		    <div class="row">

		    	<div class="col-lg-12 center">
		    		<h2><strong><?php echo $larr_ErrorMessage["title"];?></strong></h2>
		    		<h3><?php echo $larr_ErrorMessage["message"];?></h3>
		    		<?php if (@$_GET["type"]!="internal_error") { ?>
			    		<h4>You will be redirected to the login page in 30 seconds, or <a href="./login" class="btn-link">click here</a> if it does not work.</h4>
			    		<script type="text/javascript">
							setTimeout(function(){location.href="./login"},30000);
						</script>
					<?php } // if (@$_GET["type"]!="internal_error") { ?>
		    	</div>

		            
		    </div>
	    </div>
	</div>
	

	<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>
	<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
	</body>
	</html>
<?php

} /* if ( (!isset($_SESSION['PHPSESSID']) && !(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!==''))||
	(!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')) ){ */
else {

	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

	/* ---------- MAIN PAGE BODY HERE -------------- */
		require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_header_navigation.php");
	    //echo '<div id="announcement-container">';
		//require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_announcements.php");
	    //echo '</div>';
		require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_sidebar_left.php");
		
	?>

	<!-- Page Content -->
	<div id="page-wrapper" style="<?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Chat"]) && $PAGE_SETTINGS["JSEnable"]["Chat"]===true) { echo 'padding-right:250px;'; }?>">
	    <div class="fixed-toggle-button left">
            <button class="btn btn-default" id="sidebar-toggle-button" data-mode="showed"
            data-toggle="tooltip" data-placement="right" title="Click to toggle sidebar."><i id="logo-direction" class="fa fa-chevron-left"></i></button>
        </div>


	    <div id="error_cont" style="position:fixed !important;position:absolute;top:0;right:0;bottom:0;left:0;">
			<div class="" style=" position: relative;top: 5%;">
				<div class="row" style="margin-top:75px">
			        <div class="col-lg-12 center">

			            <img class="error-image-logo" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/samplelogo.png">

			        </div>
			        <!-- /.col-lg-12 -->
			    </div>

			    
			    <div class="row">

			    	<div class="col-lg-12 center">
			    		<h2><strong><?php echo $larr_ErrorMessage["title"];?></strong></h2>
			    		<h3><?php echo $larr_ErrorMessage["message"];?></h3>
			    		<?php if (@$_GET["type"]!="internal_error") { ?>
				    		<h4>You will be redirected to the index page in 30 seconds, or <a href="./index" class="btn-link">click here</a> if it does not work.</h4>
				    		<script type="text/javascript">
								setTimeout(function(){location.href="./index"},30000);
							</script>
						<?php } // if (@$_GET["type"]!="internal_error") { ?>
			    	</div>

			            
			    </div>
		    </div>
		</div>


	</div>

	<?php
	/* ---------- END - PAGE BODY HERE -------------- */
	require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");

	?>


	<?php /* ------------ MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
	<script type="text/javascript">
	    var ABSOLUTE_PATH = '<?php echo ABSOLUTE_PATH; ?>';
	    $(document).ready(function(){
	    	/* suppress keypress - para hindi mag submit yung form */
	        $('input,select').keypress(function(event) { return event.keyCode != 13; }); 
	    	<?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["DatePicker"]) && $PAGE_SETTINGS["JSEnable"]["DatePicker"]===true) {?> 
			/* datepicker */
	        $('.input-group.date').datepicker({
	        format: "yyyy-mm-dd",
	            todayBtn: "linked",
	            orientation: "top left",
	            autoclose: true,
	            todayHighlight: true,
	            startDate: '-0m'
	        });
	        <?php } ?>
	        <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Number"]) && $PAGE_SETTINGS["JSEnable"]["Number"]===true) {?> 
	    	/* decimal input field */
	        //$('.decimalFormat').number( true, 2 );
			$('.decimalFormat').autoNumeric('init', 
								{aSep:',',
								dGroup:'3',
								aDec:'.',
								mDec:'2'});
	        /* integer input field */
	        $('.integerFormat').number( true, 0 );
	        <?php } ?>

	        

	    });
	</script>

	<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
	    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js<?php echo VERSION_AFFIX; ?>"></script> 
	<?php } ?>
	<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>
	<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
	</body>
	</html>

<?php
} /* if ( (!isset($_SESSION['PHPSESSID']) && !(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']!==''))||
	(!isset($_SESSION['username']) && !(isset($_COOKIE['username']) && $_COOKIE['username']!=='')) ){ */

?>

