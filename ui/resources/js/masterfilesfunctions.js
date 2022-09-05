// ALL JAVASCRIPT/JQUERY CODES THAT IS NOT USING SERVER-SIDE CODES SHOULD BE HERE!

$(document).ready(function(){
	//$(".customformat").mask($(this).data("format"));
		$(".toggle_filter").live("click",function(){
			
				
				$(this).parents(".panel").find(".panel-body").slideToggle("fast");
				$(this).parents(".panel").find(".panel-footer").slideToggle("fast");
				$(this).find(".botton-icon-display").toggle();
				
				

			
		});


	$(".per_record_action_cont").each(function(idx,el){
		$(this).css("min-width","100px");
	})

	// dialog yes action
	$(".dialog_yes_action").live("click",function(){

		$(".dialog_yes_action").button("loading");
		$(".dialog_no_action").button("loading");

		$("#DialogModal").modal("hide");
		
		// callback function -> para nandun sa .js file ng tumawag na script yung codes para sa cancel
		if ($(this).data("trans")=="has_callback_function"&&$(this).data("mode")=="cancel"){
			cancelCallbackFunction("yes");
		} // else if ($(this).data("trans")=="has_callback_function"&&$(this).data("mode")=="cancel"){
		else if ($(this).data("trans")=="has_callback_function"&&$(this).data("mode")=="save"){
			
			saveCallbackFunction("yes");
		} // else if ($(this).data("trans")=="has_callback_function"&&$(this).data("mode")=="save"){
		else if (($(this).data("trans")=="custom_action" || $(this).data("trans")=="custom_action_whole")&&$(this).data("mode")=="save"){

			saveCallbackFunction("yes");
		} // else if ($(this).data("trans")=="custom_action"&&$(this).data("mode")=="save"){
		else if (($(this).data("trans")=="custom_action" || $(this).data("trans")=="custom_action_whole")&&$(this).data("mode")=="cancel"){
			$("#profile_pic_save").button("reset");
			
			$("#"+$(this).data("modalid")).modal("hide");

			$("#transfer_year_save").button('reset');

			/*$(".outer_actions").each(function(idx,el){
				$(this).prop("disabled",false);
			});*/ // $(".outer_actions").each(function(idx,el){
		}
		else if (($(this).data("trans")=="can_add"||$(this).data("trans")=="can_edit")&&$(this).data("mode")=="cancel"){

			$("#MasterfileAddEditModal").modal("hide");
			/*$(".outer_actions").each(function(idx,el){
				$(this).prop("disabled",false);
			});*/ // $(".outer_actions").each(function(idx,el){
		}
		else if (($(this).data("trans")=="can_add"||$(this).data("trans")=="can_edit")&&$(this).data("mode")=="save"){
			$("#loading-message").html("<b>Saving...</b>");
			$("#save_modal_action").button("loading");
			$("#blocker").fadeIn("fast",function(){
				masterfileServerSideValidation(false,ABSOLUTE_PATH+"api/ValidateMasterfiles.php");
				// masterfileSaveFunction(); -- should be called upon success of server-side validation (the only workaround because Asynchronous)
			}); // $("#blocker").fadeIn("fast",function(){
		}
		else if ($(this).data("trans")=="can_delete"&&$(this).data("mode")=="delete"){
			$("#loading-message").html("<b>Deleting...</b>");

			$("#blocker").fadeIn("fast",function(){
				
			}); // $("#blocker").fadeIn("fast",function(){

			// checking for delete -> NASA AFTERSAVE DIN YUNG GAGAWIN NG DELETE
			if (CUSTOM_AFTERSAVE!="") {
				masterfileBeforeDeleteFunction($(this).attr("data-primarycode"),$(this).attr("data-trans"),$(this).attr("data-dbconnect"),$(this).attr("data-tablename"),$(this).attr("data-primarycodefields"),$(this).attr("data-modulemstcode"),$(this).attr("data-menuitemmstcode"),$(this).attr("data-valueviewed"));
			} // if (CUSTOM_AFTERSAVE!="") {
			else {
				masterfileDeleteFunction($(this).attr("data-primarycode"),$(this).attr("data-trans"),$(this).attr("data-dbconnect"),$(this).attr("data-tablename"),$(this).attr("data-primarycodefields"),$(this).attr("data-modulemstcode"),$(this).attr("data-menuitemmstcode"),$(this).attr("data-valueviewed")); 
			} // ELSE ng if (CUSTOM_AFTERSAVE!="") {
			
		}

	});

	// dialog no action
	$(".dialog_no_action").live("click",function(){

		$(".dialog_yes_action").button("loading");
		$(".dialog_no_action").button("loading");
		$("#DialogModal").modal("hide");
		$("#save_modal_action").button("reset");
		$("#profile_pic_save").button("reset");	
		$("#new_period_save").button("reset");	
		$("#close_period_save").button("reset");	
		$("#transfer_year_save").button('reset');			
		
	});

	// filter button trigger
	$("#filter_button").live("click",function(){
		var tables = $.fn.dataTable.tables(true);
		$( tables ).DataTable().ajax.reload(null, false);
	});

	// clear button trigger
	$("#clear_button").live("click",function(){
		$(".filter-fields").each(function(idx,el){
			if ($(this).is(":checkbox")||$(this).is(":radio")){
				$(this).prop("checked",false);
			}
			else {
				$(this).val("");
			}
			$(this).change();
		});
		var tables = $.fn.dataTable.tables(true);
		$( tables ).DataTable().ajax.reload(null, false);
	});

	/*
	*
	*	CLOSE MODAL BUTTON TRIGGER
	*		- STILL NEEDS TO REMOVE THE MODAL ITSELF SO IT DOES NOT CONSUME TOO MUCH MEMORY
	*
	*/
	$(".close_modal_action").live("click",function(){
		$("#"+$(this).attr("data-id")).modal("hide");
		
		/*$(".outer_actions").each(function(idx,el){
			$(this).prop("disabled",false);
		});*/ // $(".outer_actions").each(function(idx,el){
	}); // $(".close_modal_action").live("click",function(){
	$(".modal").live('hidden.bs.modal', function (e) {
		removeWrapper($(this).parent("div"));
	}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {

	$(window).bind("load resize",function(){
		topOffset = 200;
		height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
		height = height - topOffset;
		$('.modal').each(function(idx,el){
			$(this).find('.modal-body:not(.dont-resize)').css('max-height',height+'px');
			$(this).find('.modal-body.dont-resize').css('max-height','');
		});
	});
	


});


function removeWrapper (obj){
	//alert ('1');
	$(obj).css("display","none");
	$(obj).html("");
}


// VALIDATE MASTERFILE - DYNAMIC
function masterfileClientSideValidation(){

	var errorMessage = "<ul>";
	var errorDetected = false;
	var fieldNames =  $("#columnsFieldName").val().split("|");
	var ctr = 0;
	var shouldBeError = true;
	$("form#form_masterfile_dynamic .masterfiletransaction-fields").each(function(){

		//errorMessage = errorMessage + "<li>" + $(this).data("type") + "</li>";
		// input fields required
		if (($(this).attr("data-type")!="9"&&$(this).attr("data-type")!="8"&&$(this).attr("data-type")!="7"&&$(this).attr("data-type")!="10")&&$(this).attr("data-type")!="6"&&$(this).attr("data-required")=="1"&&$(this).val()==""){
				errorMessage = errorMessage + "<li>" + fieldNames[ctr] + " must not be blank.</li>";
				errorDetected = true;
		} // if (($(this).attr("data-type")!="9"&&$(this).attr("data-type")!="8"&&$(this).attr("data-type")!="7"&&$(this).attr("data-type")!="10")&&$(this).attr("data-required")=="1"&&$(this).val()==""){

		// IF TEXT EDITOR
		else if($(this).attr("data-type")=="6"){
			textEditorID = $(this).attr("id");
			textEditorContent = encodeURIComponent(CKEDITOR.instances[textEditorID].getData());
			if(textEditorContent == ""){
				errorMessage = errorMessage + "<li>" + fieldNames[ctr] + " must not be blank.</li>";
				errorDetected = true;
			}
		}

		//	combo box && lookup required
		else if (($(this).attr("data-type")=="7"||$(this).attr("data-type")=="10")&&$(this).attr("data-required")=="1"&&$(this).val()==""){
			errorMessage = errorMessage + "<li> Please select a/n " + fieldNames[ctr] + "</li>";
			errorDetected = true;
		} // else if (($(this).attr("data-type")=="7"||$(this).attr("data-type")=="10")&&$(this).attr("data-required")=="1"&&$(this).val()==""){

		// radio required
		else if ($(this).attr("data-type")=="8"&&$(this).attr("data-required")=="1"){
			shouldBeError = true;
			$(this).find("input[type=radio]").each(function(idx,el){
				if ($(this).prop("checked")==true){
					shouldBeError = false;
				}
			});
			if (shouldBeError==true){
				errorMessage = errorMessage + "<li> Please choose a/n " + fieldNames[ctr] + "</li>";
				errorDetected = true;
			}
		} // else if ($(this).attr("data-type")=="8"&&$(this).attr("data-required")=="1"){

		// checkbox required
		else if ($(this).attr("data-type")=="9"&&$(this).attr("data-required")=="1"){
			shouldBeError = true;
			$(this).find("input[type=checkbox]").each(function(idx,el){
				if ($(this).prop("checked")==true){
					shouldBeError = false;
				}
			});
			if (shouldBeError==true){
				errorMessage = errorMessage + "<li> Please choose at least one " + fieldNames[ctr] + "</li>";
				errorDetected = true;
			}
		} // else if ($(this).attr("data-type")=="9"&&$(this).attr("data-required")=="1"){

		ctr++;
	}); // $("form#form_masterfile_dynamic .masterfiletransaction-fields").each(function(){
	errorMessage += "</ul>";

	if (errorDetected==true){
		$("#modal_error_container_content").html(errorMessage);
		$("#modal_error_container").slideDown("fast");
		$("#save_modal_action").button("reset");
		
		return false;
	} // if (errorDetected==true){
	else {
		// should be server-side validation here, but ajax call is ASYNCHRONOUS and if so, 
		// the return would be true at all times.. isabay na lang sa pag SAVE
		return true;
	} // ELSE ng if (errorDetected==true){
} // function masterfileClientSideValidation(){



function masterfileServerSideValidation(proceed,theurl){

	var textEditorID;
	$("form#form_masterfile_dynamic .masterfiletransaction-fields").each(function(){
		if($(this).attr("data-type") == "6"){
			textEditorID = $(this).attr("id"); 
			$(this).val(encodeURIComponent(CKEDITOR.instances[textEditorID].getData()));
		}//$("form#form_masterfile_dynamic .masterfiletransaction-fields").each(function(){
	}); // $("form#form_masterfile_dynamic .masterfiletransaction-fields").each(function(){


	serializedData = $("form#form_masterfile_dynamic").serialize();

	$.ajax({
        url: theurl,
        type: "post",
        data: serializedData,
        beforeSend:function(jqXHR,settings){
            
        }
    }).done(function(response, textStatus, jqXHR){
    	var isError = false;
    	var errorMessages = "";
        if (textStatus == 'success'||textStatus=="notmodified"){
        	if(response==null){
        		errorMessages = "<ul><li>Fatal Error. There is no response returned. Please try again.</li></ul>";
        		isError = true;
        	} // if(response.length<=0){
            else if(response[0].result=="1") {
            	// proceed with save
            	if (CUSTOM_VALIDATION!=""&&!proceed) {
            		masterfileServerSideValidation(true,ABSOLUTE_PATH+''+CUSTOM_VALIDATION);
            	} // if (CUSTOM_VALIDATION!="") {
            	else {
        			masterfileSaveFunction();
            	} // ELSE ng if (CUSTOM_VALIDATION!="") {
            	
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
        	errorMessages = "<ul><li>Validation Request Aborted.</li></ul>";
        	isError = true;
        } // else if (textStatus=="abort"){
        else if (textStatus=="error"||textStatus=="parsererror"){
        	errorMessages = "<ul><li>Fatal error detected upon validation request. Please try again.</li></ul>";
        	isError = true;
        } // else if (textStatus=="error"||textStatus=="parsererror"){
        else if (textStatus=="timeout"){
        	errorMessages = "<ul><li>Validation request has timed out. Please try again.</li></ul>";
        	isError = true;
        } // else if (textStatus=="timeout"){

        if (isError==true){
        	$("#modal_error_container_content").html(errorMessages);
        	$("#modal_error_container").slideDown("fast");
			$("#blocker").fadeOut("fast");
			$("#save_modal_action").button("reset");
        } // if (isError==true){

    }).fail(function (jqXHR, textStatus, errorThrown){
        $("#modal_error_container_content").html("<ul><li>Could not connect to the server or file not found.</li></ul>");
		$("#modal_error_container").slideDown("fast");
		$("#blocker").fadeOut("fast");
		$("#save_modal_action").button("reset");
    }); // $.ajax({
} // function masterfileServerSideValidation(){

// SAVE MASTERFILE - DYNAMIC
function masterfileSaveFunction(){
	var editorData;
	var editorDataVal = "&";
	// var editorDataArr = editorDataVal.split("|");
	// var editorCtr;
	// for (editorCtr in ){
	// 	if(editorDataArr[editorCtr] == 6){
	// 		editorData = 
	// 	}
	// }
	var editorID;
	$(".masterfiletransaction-fields").each(function(idx,el){
		if($(this).attr("data-type") == "6"){
			editorID = $(this).attr("id");
			editorData = CKEDITOR.instances[editorID].getData();
			editorDataVal = editorDataVal + $(this).attr("name") + "=" + encodeURIComponent(editorData);
		}
	});
	$.ajax({
        url: ABSOLUTE_PATH+ ((CUSTOM_SAVE!="")?CUSTOM_SAVE:"api/SaveMasterfiles.php"),
        type: "post",
        data:$("form#form_masterfile_dynamic").serialize() + editorDataVal,
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
            	$("#outer_success_message").html(response[0].error_message); //actually a success message

            	var last_code = (response[0].last_code!="")?response[0].last_code:"";
            	
            	if (CUSTOM_AFTERSAVE!="") {
            		masterfileCustomAfterSaveFunction(last_code);
            	} // if (CUSTOM_AFTERSAVE!="") {
        		else {
        			reShowBlocker();
        		} // ELSE ng if (CUSTOM_AFTERSAVE!="") {
            	
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
			$("#save_modal_action").button("reset");
        } // if (isError==true){

    }).fail(function (jqXHR, textStatus, errorThrown){
        $("#modal_error_container_content").html("<ul><li>Could not connect to the server or file not found.</li></ul>");
		$("#modal_error_container").slideDown("fast");
		$("#blocker").fadeOut("fast");
		$("#save_modal_action").button("reset");
    }); // $.ajax({
	
} // function masterfileSaveFunction(){

function reShowBlocker(){
	$("#blocker").fadeOut("fast",function(){
		$("#MasterfileAddEditModal").modal("hide");
		var tables = $.fn.dataTable.tables(true);
		$( tables ).DataTable().ajax.reload(null, false);
		$("#outer_success_container").slideDown("fast",function(){
			setTimeout(function(){
				$("#outer_success_container").slideUp("fast");
			},5000);
		});
	});
} // function reShowBlocker(){

// DO SOMETHING AFTER SAVE SUCCESS
function masterfileCustomAfterSaveFunction(last_code){
	$.ajax({
        url: ABSOLUTE_PATH+CUSTOM_AFTERSAVE,
        type: "post",
        data:$("form#form_masterfile_dynamic").serialize() + "&last_code=" + last_code,
        beforeSend:function(jqXHR,settings){
            
        }
    }).done(function(response, textStatus, jqXHR){
    	// doesn't matter if error or not
    	reShowBlocker();

    }).fail(function (jqXHR, textStatus, errorThrown){
        reShowBlocker();
    }); // $.ajax({
	
} // function masterfileCustomAfterSaveFunction(last_code){

// BEFORE DELETION
function masterfileBeforeDeleteFunction(primarycode,trans,dbconnect,tablename,primarycodefields,modulemstcode,menuitemmstcode,valueviewed){
	$.ajax({
        url: ABSOLUTE_PATH+CUSTOM_AFTERSAVE,
        type: "post",
        data:"last_code="+primarycode+"&transactionmode="+trans+"&dbconnect="+dbconnect+"&tableName="+tablename+"&primarycodefields="+primarycodefields+"&modulemstcode="+modulemstcode+"&menuitemmstcode="+menuitemmstcode+"&valueviewed="+valueviewed,
        beforeSend:function(jqXHR,settings){
            
        }
    }).done(function(response, textStatus, jqXHR){
    	// doesn't matter if error or not
    	masterfileDeleteFunction(primarycode,trans,dbconnect,tablename,primarycodefields,modulemstcode,menuitemmstcode,valueviewed);

    }).fail(function (jqXHR, textStatus, errorThrown){
        masterfileDeleteFunction(primarycode,trans,dbconnect,tablename,primarycodefields,modulemstcode,menuitemmstcode,valueviewed);
    }); // $.ajax({
	
} // function masterfileCustomAfterSaveFunction(last_code){

// DELETE MASTERFILE - DYNAMIC
function masterfileDeleteFunction(primarycode,trans,dbconnect,tablename,primarycodefields,modulemstcode,menuitemmstcode,valueviewed){
	$.ajax({
        url: ABSOLUTE_PATH+"api/SaveMasterfiles.php",
        type: "post",
        data:"primarycodevalue="+primarycode+"&transactionmode="+trans+"&dbconnect="+dbconnect+"&tableName="+tablename+"&primarycodefields="+primarycodefields+"&modulemstcode="+modulemstcode+"&menuitemmstcode="+menuitemmstcode+"&valueviewed="+valueviewed,
        beforeSend:function(jqXHR,settings){
            
        }
    }).done(function(response, textStatus, jqXHR){
    	var isError = false;
    	var errorMessages = "";
        if (textStatus == 'success'||textStatus=="notmodified"){
        	if(response==null){
        		errorMessages = "<ul><li>Fatal Error. There is no response returned. Please try again.</li></ul>";
        		isError = true;
        	} // if(response.length<=0){
            else if(response[0].result=="1") {
            	// save done here by now
            	$("#outer_success_message").html(response[0].error_message); //actually a success message
            	var tables = $.fn.dataTable.tables(true);
				$( tables ).DataTable().ajax.reload(null, false);
            	$("#blocker").fadeOut("fast",function(){
					$("#outer_success_container").slideDown("fast",function(){
						setTimeout(function(){
							$("#outer_success_container").slideUp("fast");
						},5000);
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
        	errorMessages = "<ul><li>Delete Request Aborted.</li></ul>";
        	isError = true;
        } // else if (textStatus=="abort"){
        else if (textStatus=="error"||textStatus=="parsererror"){
        	errorMessages = "<ul><li>Fatal error detected upon delete request. Please try again.</li></ul>";
        	isError = true;
        } // else if (textStatus=="error"||textStatus=="parsererror"){
        else if (textStatus=="timeout"){
        	errorMessages = "<ul><li>Delete request has timed out. Please try again.</li></ul>";
        	isError = true;
        } // else if (textStatus=="timeout"){

        if (isError==true){
        	$("#outer_error_container_content").html(errorMessages);
        	$("#outer_error_container").slideDown("fast",function(){
				setTimeout(function(){
					$("#outer_error_container").slideUp("fast");
				},10000);
			});
			$("#blocker").fadeOut("fast");
        } // if (isError==true){

    }).fail(function (jqXHR, textStatus, errorThrown){
        $("#outer_error_container_content").html("<ul><li>Could not connect to the server or file not found.</li></ul>");
		$("#outer_error_container").slideDown("fast",function(){
			setTimeout(function(){
				$("#outer_error_container").slideUp("fast");
			},10000);
		});
		$("#blocker").fadeOut("fast");
    }); // $.ajax({
} // function masterfileDeleteFunction(){