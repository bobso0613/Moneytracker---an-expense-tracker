$(document).ready(function(){
	var notFirstTime = false;

	$("#filter-id-7-112-menu_item_mst_code option").each(function(idx,el){
		if ($(this).html().trim()==""){
			$(this).remove();
		}
	});
	
	$('#MasterfileAddEditModal').live('show.bs.modal', function (e) {


		/* DO PROCESS HERE UPON LOADING OF THE MODAL - INITIALIZE FIELDS */
		notFirstTime = false;
		// upon load of modal, remove options with no names (No name = Module)
		//alert ('1');
		$("#masterfiletransaction-id-7-1016-menu_item_mst_code option").each(function(idx,el){
			
			if ($(this).html().trim()==""){
				$(this).remove();
			}
		});
		//alert ('2');

		// upon load of modal, set menu parent
		// edit mode
		if ($("#save_modal_action").attr("data-trans")=="can_edit"){
			if($("#masterfiletransaction-id-8-1017-type-1").prop("checked")){
				$("#masterfiletransaction-id-8-1017-type-1").change();
			}
			else {
				$("#masterfiletransaction-id-8-1017-type-2").change();
			}
			
		} // if ($("#save_modal_action").attr("data-trans")=="can_edit"){
		// add mode
		else if ($("#save_modal_action").attr("data-trans")=="can_add"){
			$("#masterfiletransaction-id-8-1017-type-1").prop("checked",true);
			$("#masterfiletransaction-id-8-1017-type-1").change();
			
		} // else if ($("#save_modal_action").attr("data-trans")=="can_add"){
		

	

	}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {


	$('#MasterfileShowModal').live('show.bs.modal', function (e) {

		/* DO PROCESS HERE UPON LOADING OF THE MODAL - INITIALIZE FIELDS */

	}); // $('#MasterfileShowModal').live('hidden.bs.modal', function (e) {

	/* DO PROCESS HERE UPON CLICK OF THE SELECTED FIELD */
	$(".masterfiletransaction-name-8-1017-type").live("change",function(){

		if (notFirstTime){
			$("#masterfiletransaction-id-1-1015-menu_name").val("");
			$("#masterfiletransaction-id-7-1018-module_mst_code").val("");
		}
		notFirstTime = true;
		
		if ($(this).val()=="1"){
			$("#masterfiletransaction-id-1-1015-menu_name").prop("readonly",false);
			$("#masterfiletransaction-name-1018-module_mst_code").css("display","none");
			$("#masterfiletransaction-name-1015-menu_name").css("display","");
			$("#masterfiletransaction-id-1-1015-menu_name").attr("data-required","1");
		}
		else {
			
			$("#masterfiletransaction-id-1-1015-menu_name").prop("readonly",true);
			$("#masterfiletransaction-name-1018-module_mst_code").css("display","");
			$("#masterfiletransaction-name-1015-menu_name").css("display","none");
			$("#masterfiletransaction-id-1-1015-menu_name").attr("data-required","0");
		}

		var newDataRequired = "";
		$(".modal-body .form-group  *[data-required=0], .modal-body .form-group  *[data-required=1]").each(function(){
			newDataRequired = newDataRequired + $(this).attr('data-required') + "|";
		});
		newDataRequired = newDataRequired.substring(0, newDataRequired.length - 1);

		$("#columnsIsRequired").val(newDataRequired);
	}); // $(".masterfiletransaction-name-8-1017-type").live("change",function(){






	// ......
});

