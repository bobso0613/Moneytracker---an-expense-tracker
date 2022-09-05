<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

/* some codes here */
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "User Settings";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine("refresh");
$PAGE_SETTINGS["Engine"]->checkSession($PAGE_SETTINGS["CurrentDirectory"]);

$PAGE_SETTINGS["menu_program_name"] = "settings";
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

$lch_link = DB_LOCATION;


     // retrieve users
    $larr_params = array (
        "action" => "retrieve-user",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstuser",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "*",
        "orderby" => "first_name ASC,middle_name ASC,last_name ASC",
        "conditions[equals][code]" => $_SESSION["user_code"]
    );
    $ljson_ResultOutput=processCurl($lch_link,$larr_params);
    $larr_Users= json_decode($ljson_ResultOutput,true);

   

    // RETRIEVE APPLICATION PARAMETERS
    $larr_AppParams = array();
    // get parameters
    $larr_Params = array (
        "action" => "retrieve-template-columns",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstapplicationparameter",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "parameter_key,parameter_value",
        "conditions[in][parameter_key]" => 'mstuser_menuitem_code,mstuser_module_code',
        "orderby" => "code ASC"
    );
    $ljson_Result=processCurl($lch_link,$larr_Params);
    $larr_OutputParams = json_decode($ljson_Result,true);
    if($larr_OutputParams[0]["result"]==='1'){
        foreach ($larr_OutputParams as $lch_Key => $larr_Value){
            $larr_AppParams[$larr_Value["parameter_key"]] = $larr_Value["parameter_value"];
        } // foreach ($larr_OutputParams as $lch_Key => $larr_Value){

    } // if($larr_OutputParams[0]["result"]==='1'){




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
        <div class="col-lg-12">
            <h1 class="page-header with-desc"><?php echo $PAGE_SETTINGS["PageTitle"]; ?></h1>
            <h5 class="header-desc"></h5>
        </div>
        <!-- /.col-lg-12 -->
    </div>


    
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center">

            <img class="img-thumbnail preview_images" width="150px" height="auto" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."profilepictures.php?thumbmode=true&id=".$larr_Users[0]['user_image_code']."&id2=".$larr_Users[0]['code'] ?>" />

            <br><br>

            <button type="button" class="btn btn-default" id="button_change_picture">  
                <i class="fa fa-picture-o"></i> Change Picture
            </button>

        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center">

            <img class="img-thumbnail " width="172px" height="auto" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."usersignatures.php?thumbmode=false&id=".$larr_Users[0]['user_signature_image_code']."&id2=".$larr_Users[0]['code'] ?>" />

            <br><br>

            <button type="button" class="btn btn-default" id="button_change_signature">  
                <i class="fa fa-pencil-square"></i> Change Signature
            </button>
            <br> <i>*Signature image only scaled for this preview*</i>

        </div>
    </div>
    <hr>
    <form class="form-horizontal" method="post" id="form_mstuser">

        <div class="alert alert-danger" id="outer_error_container" style="display:none">
            <button type="button" class="close" id="outer_error_close">&times;</button>
            <h5 id="outer_error_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>

            <div id="outer_error_container_content">

            </div> <!-- #outer_error_container_below_content -->

        </div> <!-- #outer_error_container -->

        <div class="alert alert-success" id="outer_success_container" style="text-align:center;display:none">
            <button type="button" class="close" id="outer_success_close">&times;</button>
            <h4 style="margin-bottom:0px"><strong id="outer_success_message"></strong></h4>
        </div>

        <div class="row" style="padding-bottom:5px">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="padding-bottom:5px">
                <h4 class="page-header" style="margin-top:0px">Change Password (Leave blank if you will not change password)</h4>

                <div class="form-group">
                    <label for="mstuser_current_password" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Current Password</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_current_password_container">
                        <input type="password" class="form-control input-sm mstuser_fields"  id="mstuser_current_password"
                          name="mstuser_current_password" placeholder="Current Password"
                        value=""/> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstuser_new_password" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">New Password</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_new_password_container">
                        <input type="password" class="form-control input-sm mstuser_fields"  id="mstuser_new_password"
                          name="mstuser_new_password" placeholder="New Password"
                        value=""/> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstuser_retype_new_password" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Retype New Password</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_retype_new_password_container">
                        <input type="password" class="form-control input-sm mstuser_fields"  id="mstuser_retype_new_password"
                          name="mstuser_retype_new_password" placeholder="Retype New Password"
                        value=""/> 
                    </div>
                </div>  
            </div>
        </div>
        <div class="row" style="padding-bottom:5px">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="padding-bottom:5px">
                <h4 class="page-header" style="margin-top:0px">Update Details</h4>
                <div class="form-group">
                    <label for="mstuser_first_name" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">First Name</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_first_name_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_first_name"
                          name="mstuser_first_name"
                        value="<?php echo $larr_Users[0]["first_name"];?>"/> 
                    </div>
                </div>
                <div class="form-group">
                    <label for="mstuser_middle_name" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Middle Name</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_middle_name_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_middle_name"
                          name="mstuser_middle_name"
                        value="<?php echo $larr_Users[0]["middle_name"];?>"/> 
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:8px">
                    <label for="mstuser_last_name" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Last Name</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_last_name_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_last_name"
                          name="mstuser_last_name"
                        value="<?php echo $larr_Users[0]["last_name"];?>"/> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstuser_nickname" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Nickname</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_nickname_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_nickname"
                          name="mstuser_nickname"
                        value="<?php echo $larr_Users[0]["nickname"];?>"/> 
                    </div>
                </div>

                

                <div class="form-group">
                    <label for="mstuser_email_address" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">E-mail Address</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_email_address_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_email_address"
                          name="mstuser_email_address"
                        value="<?php echo $larr_Users[0]["email_address"];?>"/> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstuser_phone_number" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Phone Number</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_phone_number_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_phone_number"
                          name="mstuser_phone_number"
                        value="<?php echo $larr_Users[0]["phone_number"];?>"/> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="mstuser_cellphone_number" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Cellphone Number</label>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="mstuser_cellphone_number_container">
                        <input type="text" class="form-control input-sm mstuser_fields"  id="mstuser_cellphone_number"
                          name="mstuser_cellphone_number"
                        value="<?php echo $larr_Users[0]["cellphone_number"];?>"/> 
                    </div>
                </div>
                
            </div>
        </div>
    </form>

    <div class="row" style="padding-bottom:5px">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 center" style="padding-bottom:5px">
            <button type="button" class="btn btn-success" id="button_save_mstuser">  
                <i class="fa fa-floppy-o"></i> Save Changes
            </button>

            <button type="button" class="btn btn-default" id="button_revert_changes">  
                <i class="fa fa-times"></i> Revert Changes
            </button>
        </div>
    </div>
              

    <div id="dialog-modal-wrapper" style="display:none"></div>
    <div id="modal-wrapper" style="display:none"></div>
    <div id="picture-modal-wrapper" style="display:none"></div>

</div>

<?php
/* ---------- END - PAGE BODY HERE -------------- */
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");

?>


<?php /* ------------ MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
<script type="text/javascript">
    var ABSOLUTE_PATH = '<?php echo ABSOLUTE_PATH; ?>';

    var gch_CurrentMode = "";
    $(document).ready(function(){
        /* suppress keypress - para hindi mag submit yung form */
        $('input,select').keypress(function(event) { return event.keyCode != 13; }); 


        // initializations here
        $("#button_save_mstuser").live("click",function(){
            if (validateClientSideAction()){
                gch_CurrentMode = "save_mstuser";
                $("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=save&trans=has_callback_function&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save|changes$",function(){
                    $("#dialog-modal-wrapper").css("display","");
                    $("#DialogModal").modal("show");
                }); // $("#dialog-modal-wrapper").load()
            } // if (validateClientSideAddAction()){
            return false;
        }); // $("#button_save_mstuser").live("click",function(){

        $("#button_revert_changes").live("click",function(){
            gch_CurrentMode = "revert_mstuser";
            $("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=cancel&trans=has_callback_function&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|revert|changes$<br>Unsaved|changes|will|be|lost.",function(){

                $("#dialog-modal-wrapper").css("display","");
                $("#DialogModal").modal("show");
            }); // $("#dialog-modal-wrapper").load()
            return false;
        }); // $("#button_revert_changes").live("click",function(){

        $("#button_change_picture").live("click",function(){
            // open modal here
            $("#loading-message").html("<b>Retrieving Data...</b>");
            $("#blocker").fadeIn("fast",function(){
                $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                    $(this).prop("disabled",true);
                });
                $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/masterfiles/mstuser/ModalMSTUserUploadProfilePic.php?usercode=<?php echo $larr_Users[0]["code"] ?>&masterfilename=Settings&databasename=iisaac_abic_system_db&transactionmode=custom_action&modulemstcode=<?php echo $larr_AppParams['mstuser_module_code'];?>&menuitemmstcode=<?php echo $larr_AppParams['mstuser_menuitem_code'];?>&tablename=mstuser&primarycodevalue="+<?php echo $larr_Users[0]["code"] ?>+"&primarycodefields=code&valueviewed="+encodeURIComponent("<?php echo $larr_Users[0]["username"] ?>")+"",function(){
                    $("#modal-wrapper").css("display","");
                    $("#upload_profile_pic_modal").modal("show");
                    $("#blocker").fadeOut("fast");
                }); // $("#modal-wrapper").load()
            }); // $("#blocker").fadeIn("fast",function(){
            return false;
        }); // $("#button_change_picture").live("click",function(){


        $("#button_change_signature").live("click",function(){
            // open modal here
            $("#loading-message").html("<b>Retrieving Data...</b>");
            $("#blocker").fadeIn("fast",function(){
                $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                    $(this).prop("disabled",true);
                });
                $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/masterfiles/mstuser/ModalMSTUserSignaturePic.php?usercode=<?php echo $larr_Users[0]["code"] ?>&masterfilename=Settings&databasename=iisaac_abic_system_db&transactionmode=custom_action&modulemstcode=<?php echo $larr_AppParams['mstuser_module_code'];?>&menuitemmstcode=<?php echo $larr_AppParams['mstuser_menuitem_code'];?>&tablename=mstuser&primarycodevalue="+<?php echo $larr_Users[0]["code"] ?>+"&primarycodefields=code&valueviewed="+encodeURIComponent("<?php echo $larr_Users[0]["username"] ?>")+"",function(){
                    $("#modal-wrapper").css("display","");
                    $("#upload_signature_modal").modal("show");
                    $("#blocker").fadeOut("fast");
                }); // $("#modal-wrapper").load()
            }); // $("#blocker").fadeIn("fast",function(){
            return false;
        }); // $("#button_change_signature").live("click",function(){

        // event listener for modal hidden
        $('#upload_profile_pic_modal').live('hidden.bs.modal', function (e) {
            $("#modal-wrapper").css("display","none");
            $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                $(this).prop("disabled",false);
            });
            $("#upload_profile_pic_modal").remove();
        }); // $(".upload_profile_pic").live('hidden.bs.modal', function (e) {

        $('#upload_signature_modal').live('hidden.bs.modal', function (e) {
            $("#modal-wrapper").css("display","none");
            $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                $(this).prop("disabled",false);
            });
            $("#upload_signature_modal").remove();
        }); // $(".upload_profile_pic").live('hidden.bs.modal', function (e) {

        $('#upload_profile_pic_modal,#upload_signature_modal').live('show.bs.modal', function (e) {

            topOffset = 200;
            height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
            height = height - topOffset;

            $('#upload_profile_pic_modal,#upload_signature_modal').find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
        }); // $(".upload_profile_pic").live('hidden.bs.modal', function (e) {
        
        $("#profile-pic").live("change",function() {
            $("#modal_error_container_content").empty(); // To remove the previous error message
            $("#modal_error_container").fadeOut("fast");
            $("#profile-pic").css("color","");
            var file = this.files[0];
            var imagefile = file.type;
            var match= ["image/jpeg","image/png","image/jpg","image/gif"];
            if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
            {
                $('#image-container').css("display", "none");
                $('#image-preview').attr('src',ABSOLUTE_PATH+'resources/assets/noprofilepic.png');
                $('#image-preview').attr('width', '350px');
                $('#image-preview').attr('height', '350px');
                $("#modal_error_container_content").html("<span id='error_message'>Only jpeg, jpg, gif and png Images type allowed</span>");
                $("#modal_error_container").fadeIn("fast");
                return false;
            }
            else
            {
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
            }
        });
        $("#remove-image").live("click",function(){
            $("#modal_error_container_content").empty(); // To remove the previous error message
            $("#modal_error_container").fadeOut("fast");
            $("#profile-pic").css("color","");
            $("#profile-pic").val('');
            $('#image-preview').attr('src',ABSOLUTE_PATH+'resources/assets/noprofilepic.png');
            $('#image-preview').attr('width', '350px');
            $('#image-preview').attr('height', '350px');
            $('#image-container').css("display", "block");
            $(this).prop('disabled',true);
        });

        $("#profile_pic_save").live("click",function(){ 
            $(this).button("loading");

            // if validation complete, 
            $("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=save&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
                $("#dialog-modal-wrapper").css("display","");
                $("#DialogModal").modal("show");
            }); // $("#dialog-modal-wrapper").load()
            
            return false;
        }); // $(".close_modal_action").live("click",function(){

        $("#user-signature").live("change",function() {
            $("#modal_error_container_content").empty(); // To remove the previous error message
            $("#modal_error_container").fadeOut("fast");
            $("#user-signature").css("color","");
            var file = this.files[0];
            var imagefile = file.type;
            var match= ["image/jpeg","image/png","image/jpg","image/gif"];
            if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
            {
                $('#image-container').css("display", "none");
                $('#image-preview').attr('src',ABSOLUTE_PATH+'resources/assets/nosignature.png');
                $('#image-preview').attr('width', '350px');
                $('#image-preview').attr('height', '350px');
                $("#modal_error_container_content").html("<span id='error_message'>Only jpeg, jpg, gif and png Images type allowed</span>");
                $("#modal_error_container").fadeIn("fast");
                return false;
            }
            else
            {
                var reader = new FileReader();
                reader.onload = imageIsLoaded2;
                reader.readAsDataURL(this.files[0]);
            }
        });
        $("#remove-image2").live("click",function(){
            $("#modal_error_container_content").empty(); // To remove the previous error message
            $("#modal_error_container").fadeOut("fast");
            $("#user-signature").css("color","");
            $("#user-signature").val('');
            $('#image-preview').attr('src',ABSOLUTE_PATH+'resources/assets/nosignature.png');
            $('#image-preview').attr('width', '200px');
            $('#image-preview').attr('height', '200px');
            $('#image-container').css("display", "block");
            $(this).prop('disabled',true);
        });

        $("#upload_signature_save").live("click",function(){    
            $(this).button("loading");

            // if validation complete, 
            $("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=save&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
                $("#dialog-modal-wrapper").css("display","");
                $("#DialogModal").modal("show");
            }); // $("#dialog-modal-wrapper").load()
            
            return false;
        }); // $(".close_modal_action").live("click",function(){


        // cancel action
        $(".cancel_modal_action").live("click",function(){  
            gch_CurrentMode = "cancel_upload_picture";
            $("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=cancel&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){
                $("#dialog-modal-wrapper").css("display","");
                $("#DialogModal").modal("show");
            }); // $("#dialog-modal-wrapper").load()
            return false;
        }); // $(".close_modal_action").live("click",function(){


    }); // $(document).ready(function(){

    // CANCEL CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR CANCEL
    function cancelCallbackFunction(response){
        if (response=="yes"){

            if (gch_CurrentMode=="revert_mstuser"){
                $("#form_mstuser").trigger("reset");
            } // if (gch_CurrentMode=="revert_mstuser"){
            else if (gch_CurrentMode=="cancel_upload_picture") {
                
                $('#upload_profile_pic_modal').modal("hide");
            } // else if (gch_CurrentMode=="cancel_upload_picture") {
            else if (gch_CurrentMode=="cancel_upload_signature") {
                
                $('#upload_signature_modal').modal("hide");
            } // else if (gch_CurrentMode=="cancel_upload_picture") {

        } // if (response=="yes"){
    } // function cancelCallbackFunction(response){

    // SAVE CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR SAVE
    function saveCallbackFunction(response){
        if (response=="yes"){

            if (gch_CurrentMode=="save_mstuser"){
                saveMSTUser("Saving Changes");
            } // if (gch_CurrentMode=="save_mstuser"){

        } // if (response=="yes"){
    } // function saveCallbackFunction(response){

    function validateClientSideAction(){

        var errorMessage = "<ul>";
        var errorDetected = false;
        var ctr = 0;
        var shouldBeError = true;

        $("#outer_error_container").slideUp("fast");
        $("#outer_success_container").slideUp("fast");

        if ($("#mstuser_first_name").val()==""){
            errorMessage = errorMessage + "<li> First Name should not be blank.</li>";
            errorDetected = true;
        } // if ($("#mstuser_first_name").val()==""){

        if ($("#mstuser_last_name").val()==""){
            errorMessage = errorMessage + "<li> Last Name should not be blank.</li>";
            errorDetected = true;
        } // if ($("#mstuser_last_name").val()==""){

        if ($("#mstuser_current_password").val()!=""||
            $("#mstuser_new_password").val()!=""||
            $("#mstuser_retype_new_password").val()!="") {

            if ($("#mstuser_current_password").val()==""){
                errorMessage = errorMessage + "<li> Current Password should not be blank.</li>";
                errorDetected = true;
            } // if ($("#mstuser_current_password").val()==""){


            if ($("#mstuser_new_password").val()==""){
                errorMessage = errorMessage + "<li> New Password should not be blank.</li>";
                errorDetected = true;
            } // if ($("#mstuser_new_password").val()==""){

            if ($("#mstuser_retype_new_password").val()==""){
                errorMessage = errorMessage + "<li> Retype your new password</li>";
                errorDetected = true;
            } // if ($("#mstuser_retype_new_password").val()==""){

            if (($("#mstuser_new_password").val()!=""||
                $("#mstuser_retype_new_password").val()!="")&&
                $("#mstuser_new_password").val()!=$("#mstuser_retype_new_password").val()){

                errorMessage = errorMessage + "<li> New password and the retyped password do not match.</li>";
                errorDetected = true;

            } // if (($("#mstuser_new_password").val()!=""||
               // $("#mstuser_retype_new_password").val()!="")&&
              //  $("#mstuser_new_password").val()!=$("#mstuser_retype_new_password").val()){

        } // if ($("#mstuser_current_password").val()!=""||
            //$("#mstuser_new_password").val()!=""||
            //$("#mstuser_retype_new_password").val()!="") {

        if ($("#mstuser_nickname").val()==""){
            errorMessage = errorMessage + "<li> Nickname should not be blank.</li>";
            errorDetected = true;
        } // if ($("#mstuser_nickname").val()==""){

        errorMessage += "</ul>";

        if (errorDetected==true){
            $("#outer_error_container_content").html(errorMessage);
            $("#outer_error_container").slideDown("fast");

            return false;
        } // if (errorDetected==true){
        else {
            // should be server-side validation here, but ajax call is ASYNCHRONOUS and if so,
            // the return would be true at all times.. isabay na lang sa pag SAVE
            return true;
        } // ELSE ng if (errorDetected==true){

    } // function validateClientSideAction(){


    function imageIsLoaded(e) {
        $("#profile-pic").css("color","green");
        $('#image-container').css("display", "block");
        $('#image-preview').attr('src', e.target.result);
        $('#image-preview').attr('width', '350px');
        $('#image-preview').attr('height', '350px');
        $('#remove-image').prop('disabled',false);

    } // function imageIsLoaded(e) {


    function imageIsLoaded2(e) {
        $("#user-signature").css("color","green");
        $('#image-container').css("display", "block");
        $('#image-preview').attr('src', e.target.result);
        $('#image-preview').attr('width', '200px');
        $('#image-preview').attr('height', '200px');
        $('#remove-image2').prop('disabled',false);

    }

    function reloadPage(){
        location.reload();
    } // function reloadPage(){

    // SAVE ADD ACTION FOR TRNOFFICIALRECEIPTS
    function saveMSTUser (loading_message){
        // save ajax
        var serializedData = $("form#form_mstuser").serialize();
        serializedData = serializedData + "&transactionmode="+gch_CurrentMode;
        serializedData = serializedData + "&mstuser_code=<?php echo $larr_Users[0]['code'] ?>";
        serializedData = serializedData + "&valueviewed="+encodeURIComponent("<?php echo $larr_Users[0]['username'] ?>");
        serializedData = serializedData + "&modulemstcode=<?php echo $larr_AppParams['mstuser_module_code'];?>";
        serializedData = serializedData + "&menuitemmstcode=<?php echo $larr_AppParams['mstuser_menuitem_code'];?>";

        $("#loading-message").html("<b>"+loading_message+"...</b>");
        $("#blocker").fadeIn("fast",function(){

            // DISABLE SAVE BUTTONS WHILE SAVING
            $("#outer_error_container").slideUp("fast");
            $("#outer_success_container").slideUp("fast");
            $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                $(this).prop("disabled",true);
            });

            $.ajax({
                url: ABSOLUTE_PATH + "api/masterfiles/SaveMSTUserSettings.php",
                type: "post",
                data:serializedData,
                beforeSend:function(jqXHR,settings){

                }
            }).done(function(response, textStatus, jqXHR){
                var isError = false;
                var errorMessages = "";
                if (textStatus == 'success'||textStatus=="notmodified"){
                    if(response==null||response.length<=0){
                        errorMessages = errorMessages + "<li>Fatal Error. There is no response returned. Please try again.</li>";
                        isError = true;
                    } // if(response.length<=0){
                    else if(response[0].result=="1") {

                        if (gch_CurrentMode=="save_mstuser"){

                            gch_CurrentMode = "";

                            $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                                $(this).prop("disabled",false);
                            });
                            $("#outer_success_message").html(response[0].error_message);
                            $("#blocker").fadeOut("fast",function(){
                                //alert ('1');
                                $("#outer_success_container").slideDown("fast",function(){

                                    reloadPage();
                                    setTimeout(function(){
                                        $("#outer_success_container").slideUp("fast");
                                    },5000);
                                });
                            }); // $("#blocker").fadeOut("fast",function(){
                            

                        } // if (gch_CurrentMode=="save_mstuser"){
                        

                    } // if(response[0].result=="1") {
                    else {
                        // fail
                        errorMessages = "";
                        for (var ctr=0;ctr<response.length;ctr++) {
                            errorMessages = errorMessages + "<li>" + response[ctr].error_message + "</li>";
                            isError = true;
                        } // for (var ctr=0;ctr<response.length;ctr++) {
                    } // else ng if(response[0].result=="1") {
                } // if (textStatus == 'success'||textStatus=="notmodified"){
                else if (textStatus=="abort"){
                    errorMessages = errorMessages + "<li>Request Aborted.";
                    isError = true;
                } // else if (textStatus=="abort"){
                else if (textStatus=="error"||textStatus=="parsererror"){
                    errorMessages = errorMessages + "<li>Fatal error detected upon request. Please try again.</li>";
                    isError = true;
                } // else if (textStatus=="error"||textStatus=="parsererror"){
                else if (textStatus=="timeout"){
                    errorMessages = errorMessages + "<li>Request has timed out. Please try again.</li>";
                    isError = true;
                } // else if (textStatus=="timeout"){

                if (isError==true){
                    
                    $("#blocker").fadeOut("fast",function(){
                        $("#outer_error_container_content").html("<ul>"+errorMessages+"</ul>");
                        $("#outer_error_container").slideDown("fast");
                        $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                            $(this).prop("disabled",false);
                        });
                    }); // $("#blocker").fadeOut("fast",function(){
                    
                } // if (isError==true){


            }).fail(function (jqXHR, textStatus, errorThrown){
                errorMessages = "<li>Could not connect to the server or file not found.</li>";
                $("#blocker").fadeOut("fast",function(){
                    $("#outer_error_container_content").html("<ul>"+errorMessages+"</ul>");
                    $("#outer_error_container").slideDown("fast");
                    $(".mstuser_fields,#button_save_mstuser,#button_revert_changes,#button_change_picture,#button_change_signature").each(function(){
                        $(this).prop("disabled",false);
                    });
                }); // $("#blocker").fadeOut("fast",function(){


            }); // $.ajax({

        }); // $("#blocker").fadeIn("fast",function(){
    } // function saveMSTUser (loading_message){

</script>

<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/masterfilesfunctions.min.js"></script> 
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/masterfiles/jsmstuser.js" type="text/javascript"></script>

<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/nightmode.min.js"></script> 
<?php } ?>
<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js"></script>
<?php /* ------------ END - MAIN PAGE JAVASCRIPT HERE --------------- */ ?>
</body>
</html>
