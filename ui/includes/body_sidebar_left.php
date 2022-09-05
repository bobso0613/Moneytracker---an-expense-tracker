<?php
$menuCount=0;
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && @$_GET["showmenu"]=="1") {
    $site_path = realpath(dirname(__FILE__));
    //include_once($site_path."/../api/MenuItemFunctions.php");
    require_once($site_path."/../api/SystemConstants.php");
    require_once($site_path."/../api/CurlAPI.php");
    date_default_timezone_set('Asia/Manila');
    session_start();

    
    //$curdir = $PAGE_SETTINGS["CurrentDirectory"];
    $menu_program_name = @$_GET["programname"];

    $larr_MSTMenuToDisplay = array();
    $lch_DBLocationString = DB_LOCATION;
    // RETRIEVE CHOSEN BANK RECONCILIATION HEADER
    $larr_Params = array (
        "action" => "retrieve_menu_per_privilege",
        "fileToOpen" => "retrieve_menu_display_revised",
        "menu_program_name" => $menu_program_name,
        "user_code"=>@$_SESSION["user_code"]
    );
    $ljson_Result=processCurl($lch_DBLocationString,$larr_Params);
    //echo $ljson_Result;
    $larr_MSTMenuToDisplay = json_decode($ljson_Result,true);
    // END - RETRIEVE CHOSEN BANK RECONCILIATION HEADER

    /* recursive menu listing */

    // function displayMenu ($parent_code="0",$first_time=false,$parent_name="",$prog=""){
    //     global $menuCount;
    //     global $menu_program_name;
        

    //     $noParent = true;
    //     if ($parent_code=="0"&&!$first_time){
    //         return;   
    //     }
    //     else {
    //         $menu = menuList($parent_code);
    //         $url = parse_url($_SERVER['REQUEST_URI']);
    //         if (!is_null($menu)){
                // if ($parent_code!="0"){
                //     /* actual display */
                //     echo '<li ><a href="#">';
                //     // check here if may image ba --- soon 
                //     //<i class="fa fa-bar-chart-o fa-fw"></i>
                //     echo $parent_name.'<span class="fa arrow"></span></a>';
                //     echo '<ul class="nav nav-sb nav-multilevel">';

                // } else {
                    
    //             }
    //             foreach ($menu as $key => $value){
                    // if ($value["type"]=="1"){
                    //     //echo "Parent here!! ". $value["menu_name"] . "<br>";
                        
                    //     // check first if may child ba na may ididisplay?
                    //     if (canDisplay($value["code"])){
                    //         $noParent = false;
                    //         $menuCount++;
                    //         displayMenu ($value["code"],false,$value["menu_name"],$prog);
                    //     }

                        
                    // }
                    // else {
                    //     //echo "Detail here!! ". $value["module_name"] . "<br>";
                    //     $menuCount++;
                    //     //echo $url["path"];
                    //     echo '<li title="'.$value["module_name"].'" class="actual_link" ><a '.(($value["program_name"]===$prog)?'class="active menu-item"  id="active-module"':'').' href="'. (($value["program_name"]===$prog) ? "#" : ABSOLUTE_PATH.$value["program_name"].'#active-module') .'">';
                    //     // check here if may image ba --- soon 
                    //     //<i class="fa fa-bar-chart-o fa-fw"></i>
                    //     echo $value["short_name"].'</a></li>';

                    // }
    //             }

    //             if ($parent_code!="0"){
                    // echo '</ul>';
                    // echo '</li>';
    //             }
    //         }

    //         if ($noParent==true){
    //             return;
    //         }
    //     }
    // }

    // function getMenu(){
    //     global $menuCount;
    //     return $menuCount;
    // }


    // recursive rendering of menu depending on type
    function renderMenuLayer($larr_menulayer=array()){
        global $menu_program_name;
        if (count($larr_menulayer)<=0){
            return;
        } // if (count($larr_menulayer)<=0){
        else {
            foreach ($larr_menulayer as $lch_key => $value) {
                if ($value["type"]=="1"){

                    echo '<li ><a href="#">';
                    // check here if may image ba --- soon 
                    //<i class="fa fa-bar-chart-o fa-fw"></i>
                    echo $value["menu_name"].'<span class="fa arrow"></span></a>';
                    echo '<ul class="nav nav-sb nav-multilevel">';
                        renderMenuLayer($value["children"]);
                    echo '</ul>';
                    echo '</li>';

                } // if ($value["type"]=="1"){
                else {
                    //echo "Detail here!! ". $value["module_name"] . "<br>";
                   
                    //echo $url["path"];
                    echo '<li title="'.$value["title"].'" class="actual_link" ><a '.(($value["active"]==="1")?'class="active menu-item"  id="active-module"':'').' href="'. (($value["program_name"]===$menu_program_name) ? "#" : ABSOLUTE_PATH.$value["program_name"].'#active-module') .'">';
                    // check here if may image ba --- soon 
                    //<i class="fa fa-bar-chart-o fa-fw"></i>
                    echo $value["menu_name"].'</a></li>';

                } // ELSE ng if ($value["type"]=="1"){
                
            } // foreach ($larr_MSTMenuToDisplay["data"] as $lch_key => $value) {
            return;
        } // ELSE ng if (count($larr_menulayer)<=0){
    } // function renderMenuLayer($larr_menulayer=array()){

} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

?>

<?php 
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && @$_GET["showmenu"]=="1") {
?>
    <li class="sidebar-search">
        <h3 id="title">Menu</h3>

        
    </li>

    <?php

    //echo $ljson_Result;

    if (count($larr_MSTMenuToDisplay)>0 && $larr_MSTMenuToDisplay["result"]=="1") {

        renderMenuLayer($larr_MSTMenuToDisplay["data"]);
        
    ?>

    <?php
    } // if (count($larr_MSTMenuToDisplay)>0 && $larr_MSTMenuToDisplay["result"]=="1") {
    else {
    ?>
        <li class="sidebar-search">
            <div class="alert alert-danger" role="alert" id="menu-error"><strong>Error!</strong><br>You have no access to any of the menu.<br><br>Please contact system administrator.</div>
        </li>
    <?php
    } // ELSE ng if (count($larr_MSTMenuToDisplay)>0 && $larr_MSTMenuToDisplay["result"]=="1") {

    /* initial menu parents */
    /*
    $menu = NULL;
    $menu = displayMenu("0",true,"",@$_GET["programname"]);

    if (getMenu()==0){
    ?>
        <li class="sidebar-search">
            <div class="alert alert-danger" role="alert" id="menu-error"><strong>Error!</strong><br>You have no access to any of the menu.<br><br>Please contact system administrator.</div>
        </li>
    <?php
    } // if ($menuCount>0){
    */
    ?>

    
    <li id="footer-main">
        <div id="footer-main-div">
            <span>&copy; 2017 <?php echo META_DESCRIPTION." (".SYSTEM_TITLE.")";?></span>

            <?php /*
            <a href="#">Home</a>
            <a href="#">Features</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#">Cookies</a>
            <a href="#">Privacy</a>
            */ ?>
            
        </div>
    </li>
    
    <?php 
    $lch_SystemVersion = "";
    if (SYSTEM_VERSION=="DEV") {
        $lch_SystemVersion = "DEVELOPMENT<br>SERVER";
    } // if (SYSTEM_VERSION=="DEV") {
    else if (SYSTEM_VERSION=="TRAINING") {
        $lch_SystemVersion = "TRAINING<br>SERVER";
    } // if (SYSTEM_VERSION=="TRAINING") {
    else if (SYSTEM_VERSION=="PROD") {
        $lch_SystemVersion = "PRODUCTION<BR>SERVER";
    } // if (SYSTEM_VERSION=="PROD") {
    ?>

    <?php if ($lch_SystemVersion!="") {
    ?>

        <li style="border-bottom:0">
            <div style="text-align:center;border-bottom:0;font-size:12px">
                <h4><strong><?php echo $lch_SystemVersion;?></strong></h4>
            </div>
        </li>

    <?php
    } // if ($lch_SystemVersion!="") { ?>
        
       

        
<?php
} // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
else {
?>
    <div class="sidebar-toggle nano" id="menu_container">
        <div class="navbar-default sidebar nano-content" role="navigation">
            <div id="topmost-navbar" class="sidebar-nav navbar-collapse">
                <ul class="nav nav-sb" id="side-menu" data-programname="<?php echo $PAGE_SETTINGS["menu_program_name"];?>">
                    <div id="menu_cont" style="display:none">
                    
                    </div>
                    <li id="footer-main" class="toremove">
                        <div class="prompt_containers"  id="footer-main-div" style="position: relative;top: 200px;transform: translateY(-50%);">
                            <h1><i class="fa fa-spinner fa-spin"></i></h1>
                            
                        </div>
                    </li>

                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </div><!-- /. sidebar-toggle -->

<?php // <h3 id="loading_message">Loading menu..<br>Please wait </h3>
} // ELSE ng if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
?>

