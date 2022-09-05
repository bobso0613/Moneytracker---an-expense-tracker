<?php
//header('Cache-Control: no-cache, no-store, must-revalidate');
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//error_reporting(E_ALL);
/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Login";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("logout");

$PAGE_SETTINGS["NoNightMode"] = false;
$PAGE_SETTINGS["EnableErrorModal"] = true;
$PAGE_SETTINGS["EnableConfirmModal"] = true;
$PAGE_SETTINGS["JSEnable"] = array();
$PAGE_SETTINGS["JSEnable"]["ClientSideValidator"] = true;

@session_start();

//echo realpath($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");

$errorMessage = '';
if (isset($_COOKIE['error_message'])){
	$errorMessage = "<li>".$_COOKIE['error_message']."</li>";
	setcookie('error_message', '', time() - 3600,"/"); 
	unset($_COOKIE['error_message']);

	$PAGE_SETTINGS["Engine"]->unSetSession();

}

if (isset($_SESSION['username']) && isset($_SESSION['user_code'])) {
	header("Location: ".$PAGE_SETTINGS["CurrentDirectory"]."index");
} /*  if (isset($_SESSION['userid'])) { */


if ( $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'login_process')){

	if(!isset($_POST['conditions']['equals']['username']) || $_POST['conditions']['equals']['username'] === ''){
		$errorMessage = $errorMessage.'<li>Username must not be blank</li>';
	} // if(!isset($_POST['conditions']['equals']['username']) || $_POST['conditions']['equals']['username'] === ''){
	if(!isset($_POST['conditions']['equals']['password']) || $_POST['conditions']['equals']['password'] === ''){
		$errorMessage = $errorMessage.'<li>Password must not be blank</li>';
	} // if(!isset($_POST['conditions']['equals']['password']) || $_POST['conditions']['equals']['password'] === ''){
	if(isset($_POST['conditions']['equals']['username']) && $_POST['conditions']['equals']['username'] !== '' && isset($_POST['conditions']['equals']['password']) && $_POST['conditions']['equals']['password'] !== ''){

		$link = DB_LOCATION;

		//echo json_encode($link) . "qwqwq";	
		$params = array (
			"action" => "login_process",
			"fileToOpen" => "default_select_query",
			"tableName" => "mstuser",
			"dbconnect" => MONEYTRACKER_DB,
			"columns" => "username,code,user_image_code,is_active",
			"conditions[equals][username]" => $_POST['conditions']['equals']['username'],
			"conditions[equals][password]" => base64_encode(md5($_POST['conditions']['equals']['password'])),
		);
		//echo json_encode($params);
		//echo http_build_query($params);
		$result=processCurl($link,$params);
		$output = json_decode($result,true);
		
		$errorMessage = "<li>".$result."</li>";
		//echo $result;
		if($output[0]["result"]==='1'){

			if (intval($output[0]["is_active"])!=1) {
				$errorMessage = "<li>This account was already inactive. Please contact system administrator.</li>";
			} // if (intval($output[0]["is_active"])!=1) {
			else {

				if (isset($_POST['remember_tag']) && $_POST['remember_tag']==='1'){
					setcookie("username",$output[0]["username"],time() + (10 * 365 * 24 * 60 * 60),"/");
					setcookie("user_code",$output[0]["user_code"],time() + (10 * 365 * 24 * 60 * 60),"/");
					setcookie("user_image_code",$output[0]["user_image_code"],time() + (10 * 365 * 24 * 60 * 60),"/");
				} // if (isset($_POST['remember_tag']) && $_POST['remember_tag']==='1'){
			
				$_SESSION['username'] = $output[0]["username"];
				$_SESSION['user_code'] = $output[0]["code"];
				$_SESSION['user_image_code'] = $output[0]["user_image_code"];



				$link = DB_LOCATION;
				$params = array (
					"action" => "update_online_state",
					"fileToOpen" => "update_online_state",
					"tableName" => "mstuser",
					"dbconnect" => MONEYTRACKER_DB,
					"username" => $output[0]["username"],
					"state" => "1"
				);
				$result=processCurl($link,$params);
				//echo $result;
				header("Location: ".$PAGE_SETTINGS["CurrentDirectory"]."index");
				exit();

			} // ELSE ng if (intval($output[0]["is_active"])!=1) {
		
			
		}
		else if($output[0]["result"]==='0'){
			if ($output[0]["error_message"]=='There are no records found.'){
				$errorMessage = "<li>Incorrect Username and/or Password.</li>";
			} // if ($output[0]["error_message"]=='There are no records found.'){
			
		} // else if($output[0]["result"]==='0'){
	
		
	} // if(isset($_POST['conditions']['equals']['username']) && $_POST['conditions']['equals']['username'] !== '' && isset($_POST['conditions']['equals']['password']) && $_POST['conditions']['equals']['password'] !== ''){
		/* $link = $this->server_link."db/systems-api.php";
		$params = array (
			"action" => "get_system_settings"
		);
		$result=processCurl($link,$params);
		$this->system_settings = json_decode($result,true);*/


} // if ( $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'login_process')){

$lch_SystemVersion = "";
if (SYSTEM_VERSION=="DEV") {
    $lch_SystemVersion = "DEV SERVER";
} // if (SYSTEM_VERSION=="DEV") {
else if (SYSTEM_VERSION=="TRAINING") {
    $lch_SystemVersion = "TRAINING SERVER";
} // if (SYSTEM_VERSION=="TRAINING") {
else if (SYSTEM_VERSION=="PROD") {
    $lch_SystemVersion = "PROD SERVER";
} // if (SYSTEM_VERSION=="PROD") {


require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

/* ---------- MAIN PAGE BODY HERE -------------- */
?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel">
                    <div class="panel-heading ">
                        <h3 class=""><strong><img class="login-image-logo" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/samplelogo.png">&nbsp;&nbsp;<?php echo META_SYSTEMNAME . " " . $lch_SystemVersion; ?> - <?php echo $PAGE_SETTINGS["PageTitle"]?></strong></h3>
                    </div>
                    <div class="panel-body">
                    	<?php if ($errorMessage !== ''){?>
							<div class="alert alert-danger alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <strong>Error Detected:</strong><Br> 
								<?php echo "<ul>".$errorMessage."</ul>";?>
							</div>
						<?php } ?>
                        <form role="form" method="post" id="form-main" action="">
                            <fieldset>

                                <div class="form-group">
                                    <input class="form-control" placeholder="Username" 
                                    name="conditions[equals][username]" type="text" autofocus autocomplete="off" 
                                    maxlength="40" data-required="true"
                                    	value="<?php echo (isset($_POST['conditions']['equals']['username']) ? $_POST['conditions']['equals']['username'] : "" )?>">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="conditions[equals][password]" type="password"  autocomplete="off" 
                                    maxlength="30" data-required="true"
                                    	value="<?php echo (isset($_POST['conditions']['equals']['password']) ? $_POST['conditions']['equals']['password'] : "" )?>">
                                </div>
                                <div class="form-group">
                            		<i style="font-size:12px">*Note: Username and Password are <strong>Case Sensitive</strong>.*</i>
                            	</div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember_tag" type="checkbox" value="Remember Me" placeholder="Remember me" data-required="false";
                                    	<?php echo (isset($_POST['remember_tag']) && $_POST['remember_tag'] === '1' ? "checked" : "" )?>
                                        >Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" id="loginProcess" name="action" value="login_process" class="btn btn-lg btn-success btn-block" data-validatingform="form-main">Login</button><br>
								<a href="#" class="btn btn-default col-md-12" role="button" id="nightmodetoggle" data-state="0"><i class="fa fa-star-half-o fa-fw"></i> Toggle Night Mode</a>
								<?php /*
								<a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>home" class="btn btn-default col-md-6">Back to Home</a>
								*/
								?>
                            </fieldset>
                        </form>
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
            
    $(document).ready(function(){
    	/* validator here */
    	$("#loginProcess").click(function(){
    		if (ClientSideValidate(this)){
    			/* modal are you sure */
    			/* not needed in login - rekta na agad */
    			/*$("#confirmModalBody").html("Are you sure?");
				$('#confirmModal').modal("show");
				return false;*/
				$("#loading-message").html("<b>Logging in...</b>");
				$("#blocker").fadeIn("fast",function(){
	                
	            });
    		}
    		else {
    			return false;
    			e.preventDefault();
    		}
    	});



    });
</script>


<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js<?php echo VERSION_AFFIX; ?>"></script> 
<?php } ?>
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>
<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>
