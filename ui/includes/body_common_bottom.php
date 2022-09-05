

    <button class="btn btn-default back-to-top no-chat" type="button" data-toggle="tooltip" data-placement="left" title="Click to back to top."><i id="logo-direction" class="fa fa-chevron-up"></i></button>

    <?php if (isset($_SESSION["user_code"])){ ?>
    <!-- notification area -->
    <div id="notification-area" style="display:none">
      
    </div>
    <?php } ?>

</div>
    <!-- /#wrapper -->

    
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery-migrate-1.2.1.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.cookie.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.hotkeys.min.js<?php echo VERSION_AFFIX; ?>"></script>
   <?php /*  <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/modernizr.custom.48210.js<?php echo VERSION_AFFIX; ?>"></script> */ ?>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.maskedinput.min.js<?php echo VERSION_AFFIX; ?>" type="text/javascript"></script>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.chained.remote.min.js<?php echo VERSION_AFFIX; ?>" type="text/javascript"></script>

    <!--ckeditor-->
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/ckeditor.js<?php echo VERSION_AFFIX; ?>" type="text/javascript"></script>


    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/bootstrap.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php /* <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery-te-1.4.0.min.js<?php echo VERSION_AFFIX; ?>"></script> */?>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/metisMenu/metisMenu.min.js<?php echo VERSION_AFFIX; ?>"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/sb-admin-2.min.js<?php echo VERSION_AFFIX; ?>"></script>

    <!-- Override Tab Function for Rich Textbox and Text Editor -->
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/taboverride.min.js<?php echo VERSION_AFFIX; ?>"></script>

    
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Morris"]) && $PAGE_SETTINGS["JSEnable"]["Morris"]===true) {?> 
        <!-- Morris Charts JavaScript -->
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/morris/raphael.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/morris/morris.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/morris/morris-data.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["DatePicker"]) && $PAGE_SETTINGS["JSEnable"]["DatePicker"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/bootstrap-datepicker.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Number"]) && $PAGE_SETTINGS["JSEnable"]["Number"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.number.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["DataTables"]) && $PAGE_SETTINGS["JSEnable"]["DataTables"]===true) {?> 
        <!-- DataTables JavaScript -->
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/dataTables/jquery.dataTables.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/dataTables/dataTables.bootstrap.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Flot"]) && $PAGE_SETTINGS["JSEnable"]["Flot"]===true) {?> 
        <!-- Flot Charts JavaScript -->
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/excanvas.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/jquery.flot.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/jquery.flot.pie.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/jquery.flot.resize.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/jquery.flot.tooltip.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/plugins/flot/flot-data.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/additionals.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["ClientSideValidator"]) && $PAGE_SETTINGS["JSEnable"]["ClientSideValidator"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/client_side_validator.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>


    <?php /* if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["OnlineUsers"]) && $PAGE_SETTINGS["JSEnable"]["OnlineUsers"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/onlineusers.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php }*/ ?>



    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["Chained"]) && $PAGE_SETTINGS["JSEnable"]["Chained"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery.chained.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>

    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["TypeAhead"]) && $PAGE_SETTINGS["JSEnable"]["TypeAhead"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/typeahead.bundle.min.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/hogan.min.js<?php echo VERSION_AFFIX; ?>"></script>
        
    <?php } ?>

    <?php /* if (isset($_SESSION["user_code"])){ ?>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/notifications.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/tasks.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } */ ?>

    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/autoNumeric.min.js<?php echo VERSION_AFFIX; ?>"></script>


    <?php if (isset($PAGE_SETTINGS["JSEnable"]) && isset($PAGE_SETTINGS["JSEnable"]["LogoutCheck"]) && $PAGE_SETTINGS["JSEnable"]["LogoutCheck"]===true) {?> 
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/logoutcheck.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <?php } ?>
    
<?php if (isset($PAGE_SETTINGS["EnableErrorModal"]) && $PAGE_SETTINGS["EnableErrorModal"]===true) {?> 
<!-- error modal -->
<div class="modal span6" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:5px;padding-bottom:5px">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <span class="modal-title" style="font-size:16px" id="errorModalLabel"><strong>Error Detected:</strong></span>
      </div>
      <div class="modal-body" id="errorModalBody" style="padding-top:5px;padding-bottom:5px">

      </div>
      <div class="modal-footer" style="padding-top:5px;padding-bottom:5px">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if (isset($PAGE_SETTINGS["EnableConfirmModal"]) && $PAGE_SETTINGS["EnableConfirmModal"]===true) {?> 
<!-- confirm modal -->
<div class="modal span6" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:5px;padding-bottom:5px">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <span class="modal-title" style="font-size:16px" id="confirmModalLabel"><strong>Confirm:</strong></span>
      </div>
      <div class="modal-body" id="confirmModalBody" style="padding-top:5px;padding-bottom:5px">

      </div>
      <div class="modal-footer" style="padding-top:5px;padding-bottom:5px">
        <button type="button" id="confirmModalYes" class="btn btn-primary btn-sm" >Yes</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if (isset($PAGE_SETTINGS["NoNightMode"]) && $PAGE_SETTINGS["NoNightMode"]===false) {?> 
        <!-- NIGHT MODE CSS -->
        <link href="" id="nightmodelink" rel="stylesheet" type="text/css">
    <?php } ?>
