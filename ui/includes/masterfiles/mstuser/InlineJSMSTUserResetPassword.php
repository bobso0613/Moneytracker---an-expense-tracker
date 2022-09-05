<?php
/*
*
*		INLINE JAVASCRIPT FOR UPLOADING OF USER PROFILE PICTURE
*
*/
//<script>
?>


$(".<?php echo $larr_Actions["action_id_prefix"]; ?>").live("click",function(){
	var data = indexTbl.row( $(this).parents('tr') ).data();

	<?php
	//build query string to pass to the load
	$qstring = "?";
	$qstring = self::queryStringBuild(self::buildModalParameterArray("custom_action"));
	?>
	// open modal here
	$("#loading-message").html("<b>Retrieving Data...</b>");
	$("#blocker").fadeIn("fast",function(){
		$("#modal-wrapper").load("<?php echo  ABSOLUTE_PATH . "" . $larr_Actions["custom_php_module_include_filename"] . "" . $qstring;?>",function(){
			$("#modal-wrapper").css("display","");
			$("#<?php echo $larr_Actions["action_id_prefix"]; ?>_modal").modal("show");
			$("#blocker").fadeOut("fast");
		}); // $("#modal-wrapper").load()
	}); // $("#blocker").fadeIn("fast",function(){
	return false;
}); // $(".<?php echo $larr_Actions["action_id_prefix"]; ?>").live("click",function(){

// event listener for modal hidden
$('#<?php echo $larr_Actions["action_id_prefix"]; ?>_modal').live('hidden.bs.modal', function (e) {
	$("#modal-wrapper").css("display","none");
	$("#<?php echo $larr_Actions["action_id_prefix"]; ?>_modal").remove();
}); // $(".<?php echo $larr_Actions["action_id_prefix"]; ?>").live('hidden.bs.modal', function (e) {

$('#<?php echo $larr_Actions["action_id_prefix"]; ?>_modal').live('show.bs.modal', function (e) {

	topOffset = 200;
	height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
	height = height - topOffset;

	$('#<?php echo $larr_Actions["action_id_prefix"]; ?>_modal').find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
}); // $(".<?php echo $larr_Actions["action_id_prefix"]; ?>").live('hidden.bs.modal', function (e) {


$("#password_save").live("click",function(){	

	if ($("#password_input").val()==""){

		$("#modal_error_container_content").html("<ul><li>Please input new password</li></ul>");
        $("#modal_error_container").slideDown("fast");
	}
	else {
		$(this).button("loading");

		// if validation complete, 
		$("#dialog-modal-wrapper").load("<?php echo ABSOLUTE_PATH;?>includes/DialogModal.php?colour=success&mode=save&trans="+$(this).data("trans")+"&modalid="+$(this).data("modalid")+"&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
			$("#dialog-modal-wrapper").css("display","");
			$("#DialogModal").modal("show");
		}); // $("#dialog-modal-wrapper").load()
	}

	
	
	return false;
}); // $(".close_modal_action").live("click",function(){

<?php
//</script>
?>