<div class="modal fade medium-modal" id="PictureModal" tabindex="-1" role="dialog" aria-labelledby="PictureModalLabel" 
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <!-- Modal -->
    <div class="modal-dialog">
        <form class="form-horizontal" method="post" id="form_add_trncommissionchit_dynamic">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_picture_modal" title="Close this modal" >&times;</button>
                <h3 class="modal-title" id="PictureModalLabel">Picture</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-lg-12 center" style="text-align:center">
                        <img width="70%" height="auto" src="<?php echo str_replace("thumbmode=true","thumbmode=false",$_GET['picturelink']);?>" />
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm close_picture_modal" title="Close this modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
        <!-- /.modal -->
</div>
