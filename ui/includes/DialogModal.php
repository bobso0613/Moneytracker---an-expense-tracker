<div class="modal fade" id="DialogModal" tabindex="2" role="dialog" aria-labelledby="DialogModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close dialog_no_action" data-trans="<?php echo $_GET["trans"]; ?>" data-mode="<?php echo $_GET["mode"]; ?>">&times;</button>
                <h5 class="modal-title" id="DialogModalLabel"><?php echo str_replace("$", "?",str_replace("|", " ", $_GET["dialog_title"]));?></h5>
            </div>
            <div class="modal-body">
        		<span><?php echo str_replace("$", "?",str_replace("|", " ", $_GET["dialog_message"]));?></span>

            </div>
            <div class="modal-footer">
                <?php
                if (!isset($_GET["onebuttonmode"])) {
                ?>
                <button type="button" class="btn btn-<?php echo $_GET["colour"];?> btn-sm dialog_yes_action" 
                        <?php echo (isset($_GET["modulemstcode"]))?'data-modulemstcode="'.$_GET["modulemstcode"].'"':"";?>
                        <?php echo (isset($_GET["menuitemmstcode"]))?'data-menuitemmstcode="'.$_GET["menuitemmstcode"].'"':"";?>
                        <?php echo (isset($_GET["valueviewed"]))?'data-valueviewed="'.$_GET["valueviewed"].'"':"";?>
                        <?php echo (isset($_GET["primarycode"]))?'data-primarycode="'.$_GET["primarycode"].'"':"";?>
                        <?php echo (isset($_GET["dbconnect"]))?'data-dbconnect="'.$_GET["dbconnect"].'"':"";?>
                        <?php echo (isset($_GET["primarycodefields"]))?'data-primarycodefields="'.$_GET["primarycodefields"].'"':"";?>
                        <?php echo (isset($_GET["tablename"]))?'data-tablename="'.$_GET["tablename"].'"':"";?>
                        data-trans="<?php echo $_GET["trans"]; ?>" data-mode="<?php echo $_GET["mode"]; ?>" 
                        data-modalid="<?php echo isset($_GET["modalid"])?$_GET["modalid"]:""; ?>">Yes</button>
                <?php
                }
                ?>
                <button type="button" class="btn btn-default btn-sm dialog_no_action" data-trans="<?php echo $_GET["trans"]; ?>" data-mode="<?php echo $_GET["mode"]; ?>" data-modalid="<?php echo @$_GET["modalid"]; ?>"> 
                    <?php echo (isset($_GET["onebuttonmode"]) ? "Okay" : "No"); ?>
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>