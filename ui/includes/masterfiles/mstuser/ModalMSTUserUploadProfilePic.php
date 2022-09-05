<?php
date_default_timezone_set('Asia/Manila');


// lookup first for the code being queried
require_once("../../../api/SystemConstants.php");
require_once("../../../api/CurlAPI.php");
$lch_DbLocationLink = DB_LOCATION;
$larr_Params = array (
    "action" => "retrieve-record-column",
    "fileToOpen" => "default_select_query",
    "tableName" => $_GET["tablename"],
    "dbconnect" => $_GET["databasename"],
    "columns" => "code,user_image_code",
    "conditions[equals][".$_GET["primarycodefields"]."]" => $_GET["primarycodevalue"],
    "orderby" => $_GET["primarycodefields"]." ASC"
);
$ljson_Result=processCurl($lch_DbLocationLink,$larr_Params);
$larr_OutputUser = json_decode($ljson_Result,true);


//echo $ljson_Result;
?>


<div class="modal fade small-modal" id="upload_profile_pic_modal" tabindex="-1" role="dialog" aria-labelledby="upload_profile_pic_modalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancel_modal_action" title="Cancel and Close this modal" data-modalid="upload_profile_pic_modal" data-trans="custom_action">&times;</button>
                <h3 class="modal-title" id="upload_profile_pic_modalLabel">Upload Profile Picture - <?php echo $_GET["valueviewed"];?> <img width="40" height="40" class="image" src="<?php echo ABSOLUTE_PATH."profilepictures.php?thumbmode=true&id=".$larr_OutputUser[0]['user_image_code']."&id2=".$larr_OutputUser[0]['code'] ?>" /></h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" id="form_upload_profile">

                <input type="hidden" name="modulemstcode" id="modulemstcode" value="<?php echo $_GET["modulemstcode"]; ?>"/>
                <input type="hidden" name="menuitemmstcode" id="menuitemmstcode" value="<?php echo $_GET["menuitemmstcode"]; ?>"/>
                <input type="hidden" name="valueviewed" id="valueviewed" value="<?php echo isset($_GET["valueviewed"])? $_GET["valueviewed"]:"New Record"; ?>"/>

                <?php
                if($larr_OutputUser[0]["result"]==='1'){
                ?>
                    <div class="alert alert-danger" id="message" style="display:none">
                               
                    </div>
                    <div class="alert alert-danger" id="modal_error_container" style="display:none" >
                        <button type="button" class="close" id="modal_error_close">&times;</button>
                        <h5 id="modal_error_message_title"><strong>Action could not be processed because of the following error/s:</strong></h5>
                        
                        <div id="modal_error_container_content">

                        </div> <!-- #modal_error_container_content -->
                        
                    </div> <!-- #modal_error_container -->
                    <div class="pull-right"><button type="button" disabled class="btn btn-default" id="remove-image">Remove Image</button></div>
                    <div class="form-group" >
                    <input type="hidden" id="input_user_code" name="input_user_code" value="<?php echo $larr_OutputUser[0]['code'];?>"/>
                        <h4 style="padding-left:40px"><strong>Preview:</strong> </h4>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center" id="image-container">
                            <img width="350" height="350" class="image img-thumbnail img-responsive" id="image-preview" src="<?php echo ABSOLUTE_PATH."resources/assets/noprofilepic.png";?>" />
                        </div>
                    </div>

                    <div class="form-group" title="Image files only">
                        <label for="profile-pic" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label">Select Image</label>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <input type="file" class="form-control" id="profile-pic" name="profile-pic" accept="image/gif, image/jpeg, image/jpg, image/png" >
                        </div>
                    </div>

                    <!-- HINDI SIYA RECOGNIZE PAG NASA INLINEJS FILE SO DITO KO NILAGAY -->
                    <script type="text/javascript">
                        function saveCallbackFunction(resp){
                            var fileData = new FormData($('form#form_upload_profile')[0]);
                            $("#loading-message").html("<b>Uploading Picture...</b>");
                            $("#blocker").fadeIn("fast",function(){
                                // save here
                                $.ajax({
                                    url: ABSOLUTE_PATH + "api/masterfiles/SaveMSTUserProfilePic.php",
                                    type: "post",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    data:fileData,
                                    beforeSend:function(jqXHR,settings){
                                        
                                    }
                                }).done(function(response, textStatus, jqXHR){
                                    var isError = false;
                                    var errorMessages = "";
                                    if (textStatus == 'success'||textStatus=="notmodified"){
                                        if(response==null||response.length<=0){
                                            errorMessages = "<ul><li>Fatal Error. There is no response returned. Please try again.</li></ul>";
                                            isError = true;
                                        } // if(response.length<=0){
                                        else if(response[0].result=="1") {
                                            // save done here by now
                                            // success here
                                            $("#outer_success_message").html(response[0].error_message); 
                                            /*$("img").each(function(idx,el){
                                                toSearch = $(this).attr("src");
                                                if (toSearch.search("profilepictures.php")>-1 && toSearch.search("id2="+response[0].input_user_code)>-1){
                                                    var reg = /id=([^"]+)/g;
                                                    var toReplace = toSearch.replace(reg,"id="+response[0].new_image_code );
                                                    $(this).attr("src",toReplace);
                                                }
                                            });*/
                                            $("#blocker").fadeOut("fast",function(){
                                                $("#upload_profile_pic_modal").modal("hide");
                                                $("#outer_success_container").slideDown("fast",function(){
                                                    setTimeout(function(){
                                                        $("#outer_success_container").slideUp("fast");
                                                    },5000);

                                                    try {
                                                        reloadPage();
                                                    } catch (e) {

                                                    }
                                                });



                                            });
                                            
                                        } // if(response[0].result=="1") {
                                        else {
                                            // fail
                                            errorMessages = "";
                                            for (var ctr=0;ctr<response.length;ctr++) {
                                                errorMessages = errorMessages + "<li>" + response[ctr].error_message + "</li>";
                                                isError = true;
                                            } // for (var ctr=0;ctr<response.length;ctr++) {        
                                            errorMessages = "<ul>" + errorMessages + "</ul>";       
                                        } // else ng if(response[0].result=="1") {
                                    } // if (textStatus == 'success'||textStatus=="notmodified"){
                                    else if (textStatus=="abort"){
                                        errorMessages = "<ul><li>Save Request Aborted.</li></ul>";
                                        isError = true;
                                    } // else if (textStatus=="abort"){
                                    else if (textStatus=="error"||textStatus=="parsererror"){
                                        errorMessages = "<ul><li>Fatal error detected upon save request. Please try again.</li></ul>";
                                        isError = true;
                                    } // else if (textStatus=="error"||textStatus=="parsererror"){
                                    else if (textStatus=="timeout"){
                                        errorMessages = "<ul><li>Save request has timed out. Please try again.</li></ul>";
                                        isError = true;
                                    } // else if (textStatus=="timeout"){

                                    if (isError==true){
                                        $("#modal_error_container_content").html(errorMessages);
                                        $("#modal_error_container").slideDown("fast");
                                        $("#blocker").fadeOut("fast");
                                        $("#profile_pic_save").button("reset");
                                    } // if (isError==true){

                                }).fail(function (jqXHR, textStatus, errorThrown){
                                    $("#modal_error_container_content").html("<ul><li>Could not connect to the server or file not found.</li></ul>");
                                    $("#modal_error_container").slideDown("fast");
                                    $("#blocker").fadeOut("fast");
                                    $("#profile_pic_save").button("reset");
                                }); // $.ajax({
                            }); // $("#blocker").fadeIn("fast",function(){

                        }

                    </script>
                        

                        
                <?php
                } // if($larr_OutputUser[0]["result"]==='1'){
                else {
                ?>
                    <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo $larr_OutputUser[0]["error_message"];?>
                    </div>
                <?php
                } // else ng if($larr_OutputUser[0]["result"]==='1'){
                ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="profile_pic_save" data-loading-text="Saving..." title="Save" data-trans="custom_action" data-modalid="upload_profile_pic_modal"><i class="fa fa-floppy-o"></i> Save</button>
                <button type="button" class="btn btn-default btn-sm cancel_modal_action" title="Cancel and Close this modal" data-modalid="upload_profile_pic_modal" data-trans="custom_action"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
