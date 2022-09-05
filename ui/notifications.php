<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");


require_once("api/SystemConstants.php");

$larr_TRNNotifications = array();

/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Notifications";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");


$PAGE_SETTINGS["Engine"] = new Engine("refresh");
$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);

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


$lch_link = DB_LOCATION;
$larr_params = array (
    "action" => "retrieve-announcements",
    "fileToOpen" => "default_select_query",
    "tableName" => "trnnotification",
    "dbconnect" => MONEYTRACKER_DB,
    "columns" => "user_mst_code,notification_datetime,notification_link,description,is_active",
    "conditions[equals][user_mst_code]" => $_SESSION["user_code"],
    "orderby" => "notification_datetime DESC"
);
$ljson_ResultOutput=processCurl($lch_link,$larr_params);
$larr_TRNNotifications= json_decode($ljson_ResultOutput,true);


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
<div id="page-wrapper" style="">
    <div class="fixed-toggle-button left">
        <button class="btn btn-default" id="sidebar-toggle-button" data-mode="showed"
        data-toggle="tooltip" data-placement="right" title="Click to toggle sidebar."><i id="logo-direction" class="fa fa-chevron-left"></i></button>
    </div>
    <div class="row">
        <div class="col-lg-12"> <h1 class="page-header with-desc"><?php echo $PAGE_SETTINGS["PageTitle"]; ?></h1> <h5 class="header-desc"></h5> </div>
    </div>

    
    <div class="row" style="padding-bottom:30px">
    	<div class="col-lg-12">

    		
    		
    		<?php if (count($larr_TRNNotifications)>0 && $larr_TRNNotifications[0]["result"]=="1") {
				$lda_currentdate = "";
				$lin_ctr = 0;
				foreach ($larr_TRNNotifications as $lch_Key => $larr_Value) {
					$lda_inputdate = date_format(date_create($larr_Value["notification_datetime"]),"l, F j, Y");

					if ($lda_currentdate=="" || ($lda_currentdate!="" && $lda_currentdate!=$lda_inputdate)) {
						if ($lin_ctr>0) {
							echo "</ul>";
						}
						$lda_currentdate = $lda_inputdate;

						echo '<h3 style="margin-bottom:2px"><strong>'.$lda_currentdate.'</strong></h3>';
						echo '<ul class="list-unstyled notifications">';
						$lin_ctr++;
					}
				?>
					<li>
						<a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"] . "". $larr_Value["notification_link"]?>" class="toblock">
						<div>
						
		    				<span class="notifications-description"><?php echo $larr_Value["description"];?></span><br>
		    				<span class="notifications-time"><?php echo date_format(date_create($larr_Value["notification_datetime"]),"h:i a");?></span>
	    				
	    				</div>
	    				</a>
	    			</li>
				<?php
				} // foreach ($larr_TRNNotifications as $lch_Key => $larr_Value) {
			?>
            </ul>
			<?php
			} // if (count($larr_TRNNotifications)>0 && $larr_TRNNotifications[0]["result"]=="1") { ?>
			
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
    	

    	// JAVASCRIPTS NEEDED HERE
        

    });
</script>

<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js<?php echo VERSION_AFFIX; ?>"></script> 
<?php } ?>
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>
<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>