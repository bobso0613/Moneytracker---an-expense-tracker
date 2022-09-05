<?php
//// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//// header("Cache-Control: post-check=0, pre-check=0", false);
//// header("Pragma: no-cache");

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Dashboard";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("refresh");
$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);

$PAGE_SETTINGS["menu_program_name"] = "index";
$PAGE_SETTINGS["NoNightMode"] = false;

$PAGE_SETTINGS["CssEnable"] = array();
$PAGE_SETTINGS["CssEnable"]["DatePicker"] = true;
$PAGE_SETTINGS["CssEnable"]["Timeline"] = false;
$PAGE_SETTINGS["CssEnable"]["Morris"] = false;
$PAGE_SETTINGS["CssEnable"]["Chat"] = false;
$PAGE_SETTINGS["CssEnable"]["DataTables"] = false;
$PAGE_SETTINGS["CssEnable"]["SocialButtons"] = false;
$PAGE_SETTINGS["CssEnable"]["Calendar"] = true;
$PAGE_SETTINGS["CssEnable"]["OnlineUsers"] = true;
$PAGE_SETTINGS["CssEnable"]["TypeAhead"] = true;


$PAGE_SETTINGS["JSEnable"] = array();
$PAGE_SETTINGS["JSEnable"]["DatePicker"] = true;
$PAGE_SETTINGS["JSEnable"]["Morris"] = false;
$PAGE_SETTINGS["JSEnable"]["Chat"] = false;
$PAGE_SETTINGS["JSEnable"]["DataTables"] = false;
$PAGE_SETTINGS["JSEnable"]["Number"] = true;
$PAGE_SETTINGS["JSEnable"]["Flot"] = false;
$PAGE_SETTINGS["JSEnable"]["Calendar"] = true;
$PAGE_SETTINGS["JSEnable"]["OnlineUsers"] = true;
$PAGE_SETTINGS["JSEnable"]["LogoutCheck"] = true;
$PAGE_SETTINGS["JSEnable"]["TypeAhead"] = true;
$PAGE_SETTINGS["JSEnable"]["Chained"] = true;



require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

/* ---------- MAIN PAGE BODY HERE -------------- */
    require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_header_navigation.php");
    //echo '<div id="announcement-container">';
    //require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_announcements.php");
    //echo '</div>';
    require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_sidebar_left.php");

$PAGE_SETTINGS["PageTitle"] = "Hello ". @$output[0]["nickname"] . ".";
    
?>

<!-- Page Content -->
<div id="page-wrapper" style="">
    <div class="fixed-toggle-button left">
        <button class="btn btn-default" id="sidebar-toggle-button" data-mode="showed"
        data-toggle="tooltip" data-placement="right" title="Click to toggle sidebar."><i id="logo-direction" class="fa fa-chevron-left"></i></button>
    </div>
    <div class="row">
        <div class="col-lg-5">
            <h1 class="page-header"><?php echo $PAGE_SETTINGS["PageTitle"]; ?></h1>
        </div>
        <div class="col-lg-7" style="text-align:right">
            <h1 class="page-header"><?php echo date("l, M. d, Y"); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div id="">
                <?php include_once($PAGE_SETTINGS["CurrentDirectory"]."modules/transactions/analytics/BodyAnalytics.php"); ?>
            </div>

            
        </div>

   
    </div>
    

</div>

<?php
/* ---------- END - PAGE BODY HERE -------------- */

require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");

?>
<div id="modal-wrapper" style="display:none"></div>
<div id="dialog-modal-wrapper" style="display:none"></div>

<?php /* ------------ MAIN PAGE JAVASCRIPT HERE --------------- */ ?>

<script type="text/javascript">
     
<?php echo 'var ABSOLUTE_PATH ="' . ABSOLUTE_PATH . '";';  ?>

    
</script>


<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js<?php echo VERSION_AFFIX; ?>"></script> 
<?php } ?>
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js<?php echo VERSION_AFFIX; ?>"></script>


<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>



<?php flush(); ?>