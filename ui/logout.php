<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");



/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Logout";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("logout");

$PAGE_SETTINGS["NoNightMode"] = false;
$PAGE_SETTINGS["EnableErrorModal"] = false;
$PAGE_SETTINGS["EnableConfirmModal"] = false;
$PAGE_SETTINGS["JSEnable"] = array();
$PAGE_SETTINGS["JSEnable"]["ClientSideValidator"] = false;
if (!isset($_SESSION["username"])){
	$_SESSION['error_message'] = "Please Log in..";	
	header("Location: ".$PAGE_SETTINGS["CurrentDirectory"]."login");
	exit();
}
$PAGE_SETTINGS["Engine"]->unSetSession();

$lch_SystemVersion = "";
if (SYSTEM_VERSION=="DEV") {
    $lch_SystemVersion = "DEVELOPMENT SERVER";
} // if (SYSTEM_VERSION=="DEV") {
else if (SYSTEM_VERSION=="TRAINING") {
    $lch_SystemVersion = "TRAINING SERVER";
} // if (SYSTEM_VERSION=="TRAINING") {
else if (SYSTEM_VERSION=="PROD") {
    $lch_SystemVersion = "PRODUCTION SERVER";
} // if (SYSTEM_VERSION=="PROD") {



require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

/* ---------- MAIN PAGE BODY HERE -------------- */
?>
    <div class="container">
        <br><br>
        <h2><img class="login-image-logo" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/samplelogo.png">&nbsp;&nbsp;<?php echo META_SYSTEMNAME . " " . $lch_SystemVersion; ?></h2>
        <div class="alert alert-info">
		  <h2>You have successfully logged out.</h2>
		  <h4>You will be redirected to the login page in 10 seconds, or <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"];?>login" class="btn-link">click here</a> if it does not work.</h4>
		</div>
		<script type="text/javascript">
			setTimeout(function(){location.href="<?php echo $PAGE_SETTINGS["CurrentDirectory"];?>login"},10000);
		</script>
    </div>
<?php
/* ---------- END - PAGE BODY HERE -------------- */
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");
?>


<?php /* ------------ MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
<script type="text/javascript">
            
    $(document).ready(function(){
    	/* validator here */
    	$("#loginProcess").click(function(){
    		if (ClientSideValidate(this)){
    			/* modal are you sure */
    			/* not needed in login - rekta na agad */
    			/*$("#confirmModalBody").html("Are you sure?");
				$('#confirmModal').modal("show");
				return false;*/
    		}
    		else {
    			return false;
    			e.preventDefault();
    		}
    	});

    	<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
        /* night mode */
		if ($.cookie('nightmode-state')=='1'){
			var url = '<?php echo ABSOLUTE_PATH;?>resources/css/nightmode.min.css<?php echo VERSION_AFFIX; ?>';
	        $('#nightmodelink').attr('href',url);
			$('#nightmodetoggle').data('state','1');
	    }
		$('#nightmodetoggle').click(function(){
			if ($(this).data('state')=='0'){
	        var url = '<?php echo ABSOLUTE_PATH;?>resources/css/nightmode.min.css<?php echo VERSION_AFFIX; ?>';
	        $('#nightmodelink').attr('href',url);
			$(this).data('state','1');
			$.cookie('nightmode-state', '1', { expires: 30, path: "/"});

			}
			else {
			$('#nightmodelink').attr('href','');
			$(this).data('state','0');
			$.cookie('nightmode-state',  null, { path: "/"});
			//$.removeCookie('nightmode-state');
			//setcookie("nightmode-state", "", time()-3600, "/");
			}
			return false;
	    });
	    <?php } ?>


    });
</script>

<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>
<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>
