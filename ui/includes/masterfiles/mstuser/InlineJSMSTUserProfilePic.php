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

function imageIsLoaded(e) {
	$("#profile-pic").css("color","green");
	$('#image-container').css("display", "block");
	$('#image-preview').attr('src', e.target.result);
	$('#image-preview').attr('width', '350px');
	$('#image-preview').attr('height', '350px');
	$('#remove-image').prop('disabled',false);

}



<?php
//</script>
?>