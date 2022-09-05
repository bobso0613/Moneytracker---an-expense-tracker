<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "User Profiles";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("refresh");
$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);

$PAGE_SETTINGS["menu_program_name"] = "user_profiles";
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

$larr_ActiveWordings = array ("0"=>"Inactive","1"=>"Active","2"=>"Inactive");

$lch_userlist = explode(",",$PAGE_SETTINGS["Engine"]->getOnlineUsers("list"));
$lch_useridlelist = explode(",",$PAGE_SETTINGS["Engine"]->getOnlineUsers("idle"));

$larr_Users  = array();
$larr_UserGroups = array();   


$llo_SlugCorrect = false; 

$lch_link = DB_LOCATION;

if (@$_GET["slug"]!=""){

     // retrieve users
    $larr_params = array (
        "action" => "retrieve-user",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstuser",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "*",
        "orderby" => "first_name ASC,middle_name ASC,last_name ASC",
        "conditions[equals][profile_slug_link]" => @$_GET["slug"]
    );
    $ljson_ResultOutput=processCurl($lch_link,$larr_params);
    $larr_Users= json_decode($ljson_ResultOutput,true);

    if ($larr_Users[0]["result"]=="1"){
        $llo_SlugCorrect = true;

        
        // get group for the user in loop
        $larr_params = array (
            "action" => "retrieve-user",
            "fileToOpen" => "default_select_query",
            "tableName" => "mstusergroup",
            "dbconnect" => MONEYTRACKER_DB,
            "columns" => "code,whole_name",
            "orderby" => "whole_name ASC",
            "conditions[in][code]" => ($larr_Users[0]["user_group_mst_codes"]!="")?$larr_Users[0]["user_group_mst_codes"]:"-1"
        );
        $ljson_ResultOutput=processCurl($lch_link,$larr_params);
        $larr_UserGroups= json_decode($ljson_ResultOutput,true);


    } // if ($larr_Users[0]["result"]=="1"){

} // if (@$_GET["slug"]!=""){
else {

    // retrieve users
    $larr_params = array (
        "action" => "retrieve-user",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstuser",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "username,code,user_image_code,first_name,middle_name,nickname,last_name,email_address,phone_number,cellphone_number,effectivity_date,user_group_mst_codes,profile_slug_link",
        "orderby" => "nickname ASC, first_name ASC,middle_name ASC,last_name ASC",
        "conditions[not_equals][code]" => $_SESSION["user_code"],
        "conditions[equals][is_active]" => "1"
    );
    $ljson_ResultOutput=processCurl($lch_link,$larr_params);
    $larr_Users= json_decode($ljson_ResultOutput,true);

    $llo_SlugCorrect = false;

} // ELSE ng if (@$_GET["slug"]!=""){





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

    <?php if (@$_GET["slug"]=="" || ($llo_SlugCorrect==true&&@$_GET["slug"]!="") ) {
    ?>
        <div class="row">
            <div class="col-lg-12 <?php if ($llo_SlugCorrect==true) { echo 'center'; } ?>">
                <?php if (@$_GET["slug"]==""  ) {
                ?>
                    <h1 class="page-header"><?php echo $PAGE_SETTINGS["PageTitle"]; ?></h1>
                <?php
                } // if (@$_GET["slug"]=="") {
                else if ($llo_SlugCorrect==true) {
                ?>
                    <h1 class="page-header"><?php echo $larr_Users[0]["first_name"] . " " . $larr_Users[0]["last_name"];?></h1>
                <?php
                } // else if ($llo_SlugCorrect==true) {
                ?>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    <?php } // if (@$_GET["slug"]=="" || ($llo_SlugCorrect==true&&@$_GET["slug"]!="") ) { ?>

    
    

        <?php 
        // USER LIST
        if (@$_GET["slug"]=="") {
        ?>
            <div class="row">

                <?php foreach ($larr_Users as $lch_Key => $larr_Value){

                        $fullname = $larr_Value["first_name"]." ".(($larr_Value["middle_name"]!=="")?$larr_Value["middle_name"]." ":"").$larr_Value["last_name"];

                        $larr_GroupsOfUser = array();   
                        // get group for the user in loop
                        $larr_params = array (
                            "action" => "retrieve-user",
                            "fileToOpen" => "default_select_query",
                            "tableName" => "mstusergroup",
                            "dbconnect" => MONEYTRACKER_DB,
                            "columns" => "code,whole_name",
                            "orderby" => "whole_name ASC",
                            "conditions[in][code]" => ($larr_Value["user_group_mst_codes"]!="")?$larr_Value["user_group_mst_codes"]:"-1"
                        );
                        $ljson_ResultOutput=processCurl($lch_link,$larr_params);
                        $larr_GroupsOfUser= json_decode($ljson_ResultOutput,true);

                        
                ?>
                    <div class="col-lg-4 col-sm-6 col-md-4 col-xs-12">
                        <div class="thumbnail" style="border:0">
                            
                          <img class="img-responsive img-circle preview_images" style="cursor:pointer" width="200" height="200" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."profilepictures.php?thumbmode=true&id=".$larr_Value['user_image_code']."&id2=".$larr_Value['code'] ?>" />
                          <div class="caption" style="text-align:center">
                            <h4 style="margin-bottom:5px"><a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."user_profiles/".$larr_Value["profile_slug_link"];?>"><?php echo $larr_Value["nickname"] ;?></a></h4>
                            
                            <?php /* $larr_Value['first_name']." ".$larr_Value['last_name']
                            <strong>E-mail</strong>: <?php echo $larr_Value["email_address"]; ?><br>
                            <strong>Cell #</strong>: <?php echo $larr_Value["cellphone_number"]; ?><br>
                            <strong>Phone #</strong>: <?php echo $larr_Value["phone_number"]; ?><br>
                            <br>
                            <strong>Active Since</strong>: <?php echo date ("M d, Y", strtotime($larr_Value["effectivity_date"]));?>
                            <br><br>
                            <strong>Part of:</strong><br>
                            <div class="same-height" style="height:40px">
                                <ul>
                                    <?php 
                                    $lin_ctr = 0;
                                    foreach ($larr_GroupsOfUser as $lch_GroupKey => $larr_GroupValue){
                                            if (array_key_exists("whole_name", $larr_GroupValue)){
                                                $lin_ctr++;
                                            ?>
                                                <li><?php echo $larr_GroupValue["whole_name"];?></li>

                                            <?php
                                            } // if (array_key_exists("whole_name", $larr_GroupValue)){
                                    } // foreach ($larr_GroupsOfUser as $lch_GroupKey => $larr_GroupValue){

                                    if ($lin_ctr==0){
                                    ?>
                                        <li>n/a</li>

                                    <?php
                                    } // if ($lin_ctr==0){
                                    ?>
                                    
                                </ul>

                            </div>
                            
                            <button type="button" class="btn btn-default" onclick="register_popup(this,'user','<?php echo $_SESSION['user_code']; ?>','<?php echo $larr_Value["code"]; ?>', '<?php echo addslashes((strlen($fullname)>33)?rtrim(substr($fullname,0,33)," ")."...":$fullname); ?>', '<?php echo $PAGE_SETTINGS["CurrentDirectory"]."profilepictures.php?thumbmode=true&id=".$larr_Value['user_image_code']."&id2=".$larr_Value['code']; ?>', '<?php echo $PAGE_SETTINGS["CurrentDirectory"]."user_profiles/".$larr_Value['profile_slug_link'] ?>','<?php echo $PAGE_SETTINGS["CurrentDirectory"]; ?>resources/assets/<?php echo $lch_path; ?>.png');return false;">
                            <i class="fa fa-envelope-o"></i> Send a Message
                            </button>
                            */ ?>

                            
                          </div>
                        </div>
                    </div>
                <?php
                } //foreach ($larr_Users as $lch_Key => $larr_Value){ 
                ?>

            </div>

        <?php
        } // if (@$_GET["slug"]=="") {

        // PROFILE NOT FOUND (SLUG WRONG)
        else if (@$_GET["slug"]!="" && $llo_SlugCorrect==false){
        ?>

            <div id="error_cont" style="position:fixed !important;position:absolute;top:0;right:0;bottom:0;left:0;">
                <div class="" style=" position: relative;top: 50%;">
                    <div class="row" style="margin-top:75px">
                        <div class="col-lg-12 center">

                            <img class="error-image-logo" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/samplelogo.png">

                        </div>
                        <!-- /.col-lg-12 -->
                    </div>

                    
                    <div class="row">

                        <div class="col-lg-12 center">
                            <h2><strong>User not found.</strong></h2>
                            <h3>The link might be broken or the user no longer exists.</h3>
                            <?php if (@$_GET["type"]!="internal_error") { ?>
                                <h4>You will be redirected to the user profile page in 30 seconds, or <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>user_profiles" class="btn-link">click here</a> if it does not work.</h4>
                                <script type="text/javascript">
                                    setTimeout(function(){location.href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>user_profiles"},30000);
                                </script>
                            <?php } // if (@$_GET["type"]!="internal_error") { ?>
                        </div>

                            
                    </div>
                </div>
            </div>

        <?php
        } // else if (@$_GET["slug"]!="" && $llo_SlugCorrect==false){

        // DISPLAY PROFILE
        else {
        ?>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center">

                    <img class="img-thumbnail preview_images" width="200px" height="auto" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."profilepictures.php?id=".$larr_Users[0]['user_image_code']."&id2=".$larr_Users[0]['code'] ?>" />
                    
                </div>
            </div>
            <hr>
            <div class="row" style="padding-bottom:5px">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 " style="padding-bottom:5px">
                    <h4 class="page-header" style="margin-top:0px">Details</h4>
                    <div>Whole Name&nbsp;&nbsp;<strong><?php echo $larr_Users[0]["whole_name"];?></strong></div>
                    <div>Nickname&nbsp;&nbsp;<strong><?php echo $larr_Users[0]["nickname"];?></strong></div>
                    <div>Last Online&nbsp;&nbsp;<strong>
                            <?php 
                            if ($larr_Users[0]["last_online_datetime"]!="0000-00-00 00:00:00") {
                                echo date_format(date_create($larr_Users[0]["last_online_datetime"]),"h:i A - F d, Y");
                            }
                            else {
                                echo 'Never';
                             
                            }?>
                        </strong></div>
                    <div>Account Status&nbsp;&nbsp;<strong><?php echo $larr_ActiveWordings[$larr_Users[0]["is_active"]];?></strong></div>
                    <?php if ($larr_Users[0]["is_active"]=="1" && $larr_Users[0]["effectivity_date"]!="0000-00-00") {
                    ?>
                        <div>Account Active Since&nbsp;&nbsp;<strong>
                            <?php echo date_format(date_create($larr_Users[0]["effectivity_date"]),"F d, Y") ?>
                        </strong></div>
                    <?php
                    } // if ($larr_Users[0]["is_active"]=="1" && $larr_Users[0]["effectivity_date"]!="0000-00-00") {
                    ?>
                    <br>
                    <div>E-mail Address&nbsp;&nbsp;<strong><?php echo $larr_Users[0]["email_address"];?></strong></div>
                    <div>Phone Number&nbsp;&nbsp;<strong><?php echo $larr_Users[0]["phone_number"];?></strong></div>
                    <div>Cellphone Number&nbsp;&nbsp;<strong><?php echo $larr_Users[0]["cellphone_number"];?></strong></div>
                    <br>
                    <div>Signature<br>
                        <img width="220" height="auto" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."usersignatures.php?thumbmode=false&id=".$larr_Users[0]['user_signature_image_code']."&id2=".$larr_Users[0]['code'] ?>" />
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 " style="padding-bottom:5px">
                    <h4 class="page-header" style="margin-top:0px">Part of</h4>
                    <ul>
                    <?php if ($larr_UserGroups[0]["result"]=="1") {
                    ?>
                        <?php foreach ($larr_UserGroups as $lch_Key => $larr_Value) {
                        ?>
                            <li><?php echo $larr_Value["whole_name"];?></li>
                        <?php
                        } // foreach ($larr_UserGroups as $lch_Key => $larr_Value) { ?>
                        
                    <?php
                    } //  if ($larr_UserGroups[0]["result"]=="1") {
                    else {
                    ?>
                        <li>n/a</li>
                    <?php
                    } //  ELSE ng if ($larr_UserGroups[0]["result"]=="1") { ?>
                    </ul>
                </div>
            </div>

 

            <div class="center">
                <h4><a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."user_profiles" ?>"?>&gt; Back to User List &lt;</a></h4>
            </div>

        <?php
        } // ELSE ng else if (@$_GET["slug"]!="" && $llo_SlugCorrect==false){
        ?>
              

    <div id="picture-modal-wrapper" style="display:none"></div>


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
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js"></script> 
<?php } ?>
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js"></script>
<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>
