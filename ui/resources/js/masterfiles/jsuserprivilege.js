$(document).ready(function(){

	$("#type_code").chained("#privilege_type");

	/* FILTER THE RATES */
	$("#filter_button_unique").live("click",function(){
		var errorMessage = "";
		// check first if may laman yung required
		$(".filter-fields-unique").each(function(idx,el){
			if ($(this).attr("data-required")=="1"&&$(this).val()==""){
				errorMessage = errorMessage + "<li>Please input " + $(this).attr("data-fieldname") + ".</li>";
			} // if ($(this).attr("data-required")=="1"){
		}); // $(".filter-fields-unique").each(function(idx,el){

		if (errorMessage!=""){
			$("#outer_error_container_content").html("<ul>"+errorMessage+"</ul>");
			$("#outer_error_container").slideDown("fast");
		} // if (errorMessage!=""){
		else {
			// PROCEED TO FILTERING
			$("#outer_error_container").slideUp("fast");
			$("#outer_success_container").slideUp("fast");
			$("#error_prompt_container").fadeOut("fast");
			$("#outer_error_below_container").slideUp("fast"); 
			$("#outer_success_below_container").slideUp("fast");
			$("#newlyloaded_prompt_container").fadeOut("fast",function(){
				$("#main_result_container").fadeOut("fast",function(){
					$("#loading_message").html("Retrieving Data.. Please wait.");
					$("#loading_prompt_container").fadeIn("fast",function(){
						// AJAX CALL FOR TABLE
						loadUserPrivileges();
					}); // $("#loading_prompt_container").fadeIn("fast",function(){
				});
			}); // $("#newlyloaded_prompt_container").fadeOut("fast",function(){
		} // else ng if (errorMessage!=""){

		return false;
	}); // $("#filter_button_unique").live("click",function(){


	/* CLEAR FILTERS */
	//data_container
	$("#clear_button_unique").live("click",function(){
		$("#outer_error_container").slideUp("fast");
		$("#outer_success_container").slideUp("fast");
		$("#error_prompt_container").fadeOut("fast");
		$("#outer_error_below_container").slideUp("fast");
		$("#outer_success_below_container").slideUp("fast");
		
		$(".outer-fields-unique").each(function(idx,el){
			$(this).val("");
			$(this).change();
			$(this).typeahead('setQuery', '');
		});
		$("#main_result_container").fadeOut("fast",function(){
			$("#loading_message").html("Clearing Data.. Please wait.");
			$("#loading_prompt_container").fadeIn("fast",function(){
				$("#retreived_data_container").remove();

				$(".print_button_unique,.edit_button_unique").each(function(idx,el){
					$(this).prop("disabled",true);
				}); //$(".print_button_unique,.edit_button_unique").each(function(idx,el)){
				
				
				$("#loading_prompt_container").fadeOut("fast",function(){
					$("#newlyloaded_prompt_container").fadeIn("fast");
				});
			}); // $("#newlyloaded_prompt_container").fadeOut("fast",function(){
		}); // $("#main_result_container").fadeOut("fast",function(){
		

	}); // $("#clear_button_unique").live("click",function(){

	/* EDIT ACTION */
	$(".edit_button_unique").each(function(idx,el){
		$(this).live("click",function(){
			/*$(".outer-fields-unique").each(function(idx,el){
				$(this).prop("disabled",true);
			}); // $(".outer-fields-unique").each(function(idx,el){
			$(".outer-fields-unique-inverted").each(function(idx,el){
				$(this).prop("disabled",false);
			}); // $(".outer-fields-unique").each(function(idx,el){
			$(".edit_button_unique,.print_button_unique").each(function(idx,el){
				$(this).css("display","none");
			}); // $(".edit_button_unique").each(function(idx,el){
			$(".cancel_button_unique,.save_button_unique").each(function(idx,el){
				$(this).css("display","");
			}); // $(".edit_button_unique").each(function(idx,el){*/
			toggleFields(false);
			$(".access_type_filter").each(function(idx,el){
				$(this).change();
			});
		}); // $(this).live("click",function(){
	}); // $(".edit_button_unique").each(function(){

	/* CANCEL CHANGES ACTION */
	$(".cancel_button_unique").each(function(idx,el){
		$(this).live("click",function(){
			$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=cancel&trans=has_callback_function&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){
				$("#dialog-modal-wrapper").css("display","");
				$("#DialogModal").modal("show");
			}); // $("#dialog-modal-wrapper").load()
		}); // $(this).live("click",function(){
	}); // $(".cancel_button_unique").each(function(){


	/* SAVE CHANGES ACTION */
	$(".save_button_unique").each(function(idx,el){
		$(this).live("click",function(){
			$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=save&trans=has_callback_function&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save|changes",function(){
				$("#dialog-modal-wrapper").css("display","");
				$("#DialogModal").modal("show");
			}); // $("#dialog-modal-wrapper").load()
		}); // $(this).live("click",function(){
	}); // $(".save_button_unique").each(function(){

	/* CHANGING OF FILTER ACTION - IF PINALITAN YUNG MGA NASA TAAS, RESET THE RATES (TO AVOID INCONSISTENCIES) */
	$(".filter-fields-unique").change(function(){
		if ($("#main_result_container").css("display")=="block" && $(this).prop("disabled")==false){
			$("#main_result_container").fadeOut("fast",function(){
				$("#loading_message").html("Clearing Data.. Please wait.");
				$("#loading_prompt_container").fadeIn("fast",function(){
					$("#retreived_data_container").remove();

					$(".print_button_unique,.edit_button_unique").each(function(idx,el){
						$(this).prop("disabled",true);
					}); //$(".print_button_unique,.edit_button_unique").each(function(idx,el)){
					
					
					$("#loading_prompt_container").fadeOut("fast",function(){
						$("#newlyloaded_prompt_container").fadeIn("fast");
					}); // $("#loading_prompt_container").fadeOut("fast",function(){
				}); // $("#newlyloaded_prompt_container").fadeOut("fast",function(){
			}); // $("#main_result_container").fadeOut("fast",function(){
		} // if ($("#main_result_container").css("display")=="block" && $(this).prop("disabled")==false){
		
	}); // $(".filter-fields-unique").change(function(){

}); // $(document).ready(function(){

var firstTimeAccessType = false;
function loadUserPrivileges (){
	// RETRIVE THE DATA ITSELF - RETURNS JSON OBJECT 
	$.ajax({
        url: ABSOLUTE_PATH + "api/masterfiles/SelectMSTUserPrivilege.php",
        type: "post",
        data:$("form#form_masterfile_unique").serialize(),
        beforeSend:function(jqXHR,settings){
            
        }
    }).done(function(response, textStatus, jqXHR){
    	var isError = false;
    	var errorMessages = "";
        if (textStatus == 'success'||textStatus=="notmodified"){
        	if(response==null){
        		errorMessages = "Fatal Error. There is no response returned. Please try again.";
        		isError = true;
        	} // if(response.length<=0){
            else if(response[0].result=="1") {
            	// proceed with save
            	// RENDER RETRIEVED JSON OBJECTS INTO HTML HERE
            	renderUserPrivileges(response);
            	
            } // if(response[0].result=="1") {
            else {
            	// fail
            	errorMessages = "";
            	for (var ctr=0;ctr<response.length;ctr++) {
            		errorMessages = errorMessages + "" + response[ctr].error_message + "";
            		isError = true;
            	} // for (var ctr=0;ctr<response.length;ctr++) {      	
            	errorMessages = "" + errorMessages + "";      	
            } // else ng if(response[0].result=="1") {
        } // if (textStatus == 'success'||textStatus=="notmodified"){
        else if (textStatus=="abort"){
        	errorMessages = "Request Aborted.";
        	isError = true;
        } // else if (textStatus=="abort"){
        else if (textStatus=="error"||textStatus=="parsererror"){
        	errorMessages = "Fatal error detected upon request. Please try again.";
        	isError = true;
        } // else if (textStatus=="error"||textStatus=="parsererror"){
        else if (textStatus=="timeout"){
        	errorMessages = "Request has timed out. Please try again.";
        	isError = true;
        } // else if (textStatus=="timeout"){

        if (isError==true){
        	$("#error_prompt_message").html(errorMessages);
        	$("#loading_prompt_container").fadeOut("fast");
        	$("#error_prompt_container").fadeIn("fast");
        } // if (isError==true){

    }).fail(function (jqXHR, textStatus, errorThrown){
        $("#error_prompt_message").html("Could not connect to the server or file not found.");
        $("#loading_prompt_container").fadeOut("fast");
		$("#error_prompt_container").fadeIn("fast");
    }); // $.ajax({

} // function loadUserPrivileges (){

// make tables and put values
function renderUserPrivileges (pinarr_Result){
	var lch_htmlOutput = '';

	
	lch_htmlOutput += '';
	lch_htmlOutput += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="retreived_data_container">';
	lch_htmlOutput += '<h4>User Privileges for <strong>'+pinarr_Result[0].type_mst_details[0].whole_name+'</strong></h4>';

	// LOOP MODULES
	if (pinarr_Result[0].module_details.length>0){
		/* TABLE HEADERS */
		lch_htmlOutput += '<div style="overflow-x:auto"><table class="table table-bordered  table-condensed">';
		lch_htmlOutput += '<tr><th style="white-space:nowrap" class="table-header">MODULES/ACTIONS</th>';
		// for access
		lch_htmlOutput += '<th title="Access Type" class="center table-header">Access</th>';
		// module actions
		for (var ctr=0;ctr<pinarr_Result[0].module_action_details.length;ctr++){
			lch_htmlOutput += '<th title="'+pinarr_Result[0].module_action_details[ctr].action_name+'" class="center table-header">'+pinarr_Result[0].module_action_details[ctr].action_name+'</th>';
		} // for (var ctr=0;ctr<pinarr_Result[0].module_details.length;ctr++){
		lch_htmlOutput += '</tr>';
		/* END - TABLE HEADERS */

		/* ACTUAL TABLE RECORDS */
		for (var ctr=0;ctr<pinarr_Result[0].module_details.length;ctr++){
			// Modules
			lch_htmlOutput += '<tr class="table-hover"><td title="'+pinarr_Result[0].module_details[ctr].module_name+'" class="no-linebreak">'+pinarr_Result[0].module_details[ctr].short_name+ '</td>';

			var larr_actionlistpermodule = pinarr_Result[0].module_details[ctr].module_action_dtl_codes.split(",");
			// for access

			// name of the select
			inputNameParent = "user_privilege_"+pinarr_Result[0].type+"_"+
						pinarr_Result[0].type_mst_details[0].code+"_"+
						pinarr_Result[0].module_details[ctr].code+"_-1";
			lch_htmlOutput += '<td class="center"><select class="form-control outer-fields-unique-inverted input-sm access_type_filter" disabled name="'+inputNameParent+'">';
			lch_htmlOutput += '<option value="">- - - - -</value>';
			lch_htmlOutput += '<option value="2" ';
			if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					pinarr_Result[0].type_mst_details[0].code+"_"+
					pinarr_Result[0].module_details[ctr].code+"_-1"] != "undefined" &&
					pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					pinarr_Result[0].type_mst_details[0].code+"_"+
					pinarr_Result[0].module_details[ctr].code+"_-1"] == "2") {
				lch_htmlOutput += "selected"
			} // if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
			lch_htmlOutput += ' >Deny</option>';
			lch_htmlOutput += '<option value="1" ';
			if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					pinarr_Result[0].type_mst_details[0].code+"_"+
					pinarr_Result[0].module_details[ctr].code+"_-1"] != "undefined" &&
					pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					pinarr_Result[0].type_mst_details[0].code+"_"+
					pinarr_Result[0].module_details[ctr].code+"_-1"] == "1") {
				lch_htmlOutput += "selected"
			} // if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
			lch_htmlOutput +=' >Allow</option>';
			lch_htmlOutput += '</select></td>';
			// Module Actions loop
			for (var innerCtr=0;innerCtr<pinarr_Result[0].module_action_details.length;innerCtr++){
				if ($.inArray(pinarr_Result[0].module_action_details[innerCtr].code,larr_actionlistpermodule)>-1){
					// name of the select
					inputName = "user_privilege_"+pinarr_Result[0].type+"_"+
								pinarr_Result[0].type_mst_details[0].code+"_"+
								pinarr_Result[0].module_details[ctr].code+"_"+
								pinarr_Result[0].module_action_details[innerCtr].code;


					lch_htmlOutput += '<td class="center"><select class="form-control outer-fields-unique-inverted input-sm '+inputNameParent+'" disabled name="'+inputName+'">';
					lch_htmlOutput += '<option value="">- - - - -</value>';
					lch_htmlOutput += '<option value="2" ';
					if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
							pinarr_Result[0].type_mst_details[0].code+"_"+
							pinarr_Result[0].module_details[ctr].code+"_"+
							pinarr_Result[0].module_action_details[innerCtr].code] != "undefined" &&
							pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
							pinarr_Result[0].type_mst_details[0].code+"_"+
							pinarr_Result[0].module_details[ctr].code+"_"+
							pinarr_Result[0].module_action_details[innerCtr].code] == "2") {
						lch_htmlOutput += "selected"
					} // if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					lch_htmlOutput += ' >Deny</option>';
					lch_htmlOutput += '<option value="1" ';
					if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
							pinarr_Result[0].type_mst_details[0].code+"_"+
							pinarr_Result[0].module_details[ctr].code+"_"+
							pinarr_Result[0].module_action_details[innerCtr].code] != "undefined" &&
							pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
							pinarr_Result[0].type_mst_details[0].code+"_"+
							pinarr_Result[0].module_details[ctr].code+"_"+
							pinarr_Result[0].module_action_details[innerCtr].code] == "1") {
						lch_htmlOutput += "selected"
					} // if (typeof pinarr_Result[0].user_privilege[pinarr_Result[0].type+"_"+
					lch_htmlOutput +=' >Allow</option>';
					lch_htmlOutput += '</select></td>';
				} // if ($.inArray(pinarr_Result[0].module_action_details[innerCtr].code,larr_actionlistpermodule)>-1){
				else {
					lch_htmlOutput += '<td class="center" title="Not Applicable"><i>N/A</i></td>';
				} // if ($.inArray(pinarr_Result[0].module_action_details[innerCtr].code,larr_actionlistpermodule)>-1){

			} // for (var innerCtr=0;innerCtr<pinarr_Result[0].module_action_details.length;innerCtr++){

			lch_htmlOutput += '</tr>';
		} // for (var ctr=0;ctr<pinarr_Result[0].module_details.length;ctr++){
		/* END - ACTUAL TABLE RECORDS */



	} // if (pinarr_Result[0].module_details.length>0){
	
	
	
	lch_htmlOutput += '</table></div>';
	lch_htmlOutput += '</div>';
	

	$(".print_button_unique,.edit_button_unique").each(function(idx,el){
		$(this).prop("disabled",false);

	}); //$(".print_button_unique,.edit_button_unique").each(function(idx,el)){

	$(".cancel_button_unique,.save_button_unique").each(function(idx,el){
		$(this).css("display","none");
	}); // $(".edit_button_unique").each(function(idx,el){

	$("#data_container").html(lch_htmlOutput)
	$("#loading_prompt_container").fadeOut("fast",function(){
		$("#main_result_container").fadeIn("fast",function(){

		});

	});
	firstTimeAccessType = false;
	initializeScriptsForDynamicallyLoadedHTML();
	
} // function renderUserPrivileges (jsonResult){
// END - make tables and put values


function initializeScriptsForDynamicallyLoadedHTML(){

	/* if you want to initialize after loading dynamic data, put it here */
	//access_type_filter
	$(".access_type_filter").live("change",function(){
		if (firstTimeAccessType==false){
			firstTimeAccessType = true;
		} // if (firstTimeAccessType==false){
		else {
			if ($(this).val()==""){
				$("."+$(this).attr("name")).each(function(idx,el){
					$(this).val("");
					$(this).prop("disabled",true);
				}); // $("."+$(this).attr("name")).each(function(idx,el){
			} // if ($(this).val()==""||$(this).val()=="2"){
			else if ($(this).val()=="2"){
				$("."+$(this).attr("name")).each(function(idx,el){
					$(this).val("2");
					$(this).prop("disabled",true);
				}); // $("."+$(this).attr("name")).each(function(idx,el){
			}
			else {
				$("."+$(this).attr("name")).each(function(idx,el){
					/*if ($(this).val()!="1"){
						$(this).val("");
					}*/
					$(this).prop("disabled",false);
				}); // $("."+$(this).attr("name")).each(function(idx,el){
			} // else ng if ($(this).val()==""||$(this).val()=="2"){
		} // else ng if (firstTimeAccessType==false){
		
	}); // $(".access_type_filter").live("change",function(){

} // function initializeScriptsForDynamicallyLoadedHTML(){

// CANCEL CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR CANCEL
function cancelCallbackFunction(response){
	if (response=="yes"){
		toggleFields(true);
		

		// reset data
		$("#outer_error_container").slideUp("fast");
		$("#outer_success_container").slideUp("fast");
		$("#error_prompt_container").fadeOut("fast");
		$("#outer_error_below_container").slideUp("fast");
		$("#outer_success_below_container").slideUp("fast");
		$("#newlyloaded_prompt_container").fadeOut("fast",function(){
			$("#main_result_container").fadeOut("fast",function(){
				$("#loading_message").html("Reverting Changes.. Please wait.");
				$("#loading_prompt_container").fadeIn("fast",function(){
					// AJAX CALL FOR TABLE
					firstTimeAccessType = false;
					loadUserPrivileges();
				}); // $("#loading_prompt_container").fadeIn("fast",function(){
			});
		}); // $("#newlyloaded_prompt_container").fadeOut("fast",function(){
		
	} // if (response=="yes"){
	
} // function cancelCallbackFunction(response){

function toggleFields(state){
	$(".outer-fields-unique").each(function(idx,el){
		$(this).prop("disabled",!state);
	}); // $(".outer-fields-unique").each(function(idx,el){
	$(".outer-fields-unique-inverted").each(function(idx,el){
		$(this).prop("disabled",state);
	}); // $(".outer-fields-unique").each(function(idx,el){
	$(".edit_button_unique,.print_button_unique").each(function(idx,el){
		$(this).css("display",(state)?"":"none");
	}); // $(".edit_button_unique").each(function(idx,el){

	$(".cancel_button_unique,.save_button_unique").each(function(idx,el){
		$(this).css("display",(state)?"none":"");
	}); // $(".edit_button_unique").each(function(idx,el){

	firstTimeAccessType = false;


} // function toggleFields(state){

// SAVE CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR SAVE
function saveCallbackFunction(response){
	if (response=="yes"){
		$("#outer_error_below_container").slideUp("fast");
		$("#outer_success_below_container").slideUp("fast");
		var serializedData = $("form#form_masterfile_unique").serialize();
		var codes = "&" + "type_code_desc=" + $("#type_code option:selected").html()  +
		"&" + "privilege_type=" + $("#privilege_type").val()  +
		"&" + "type_code=" + $("#type_code").val()  +
		"&" + "menu_item_mst_code=" + $("#menu_item_mst_code").val() ; 
		serializedData = serializedData + "" + codes;
		$("#main_result_container").fadeOut("fast",function(){
			$("#loading_message").html("Saving.. Please wait.");
			$("#loading_prompt_container").fadeIn("fast",function(){
				// SAVE INTERMEDIARY RATES
				$.ajax({
			        url: ABSOLUTE_PATH + "api/masterfiles/SaveMSTUserPrivilege.php",
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
			            	
			            	/*

								DISPLAY SUCESS MESSAGE HERE AND SHOW DIALOG.. THEN MAYBE RE-QUERY

			            	*/
			            	$("#loading_prompt_container").fadeOut("fast",function(){
								$("#main_result_container").fadeIn("fast",function(){
									$("#outer_success_below_message").html(response[0].error_message);
									$("#outer_success_below_container").slideDown("fast",function(){
										setTimeout(function(){
											$("#outer_success_below_container").slideUp("fast");
										},5000);
									});
									firstTimeAccessType = false;
									toggleFields(true);
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
			        	/*$("#error_prompt_message").html(errorMessages);
			        	$("#loading_prompt_container").fadeOut("fast");
			        	$("#error_prompt_container").fadeIn("fast");*/
	
						
						$("#loading_prompt_container").fadeOut("fast",function(){
							$("#main_result_container").fadeIn("fast",function(){
								$("#outer_error_below_container_content").html("<ul>"+errorMessages+"</ul>");
								$("#outer_error_below_container").slideDown("fast");
							});
						});

			        } // if (isError==true){

			    }).fail(function (jqXHR, textStatus, errorThrown){
			        /*$("#error_prompt_message").html("Could not connect to the server or file not found.");
			        $("#loading_prompt_container").fadeOut("fast");
					$("#error_prompt_container").fadeIn("fast");*/
					errorMessages = "<li>Could not connect to the server or file not found.</li>";
					
					$("#loading_prompt_container").fadeOut("fast",function(){
						$("#main_result_container").fadeIn("fast",function(){
							$("#outer_error_below_container_content").html("<ul>"+errorMessages+"</ul>");
							$("#outer_error_below_container").slideDown("fast");
						});
					});

			    }); // $.ajax({
			});
		});
		
		
		
	} // if (response=="yes"){
	
} // function cancelCallbackFunction(response){