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
						loadBudgetSetup();
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

function loadBudgetSetup (){

	$("#data_container").load(ABSOLUTE_PATH+"includes/transactions/main/IncludeBudgetSetup.php?money_trail_type_mst_code="+$("#money_trail_type_mst_code").val()+"&booking_year="+$("#booking_year").val(),function(){


		$(".print_button_unique,.edit_button_unique").each(function(idx,el){
			$(this).prop("disabled",false);

		}); //$(".print_button_unique,.edit_button_unique").each(function(idx,el)){

		$(".cancel_button_unique,.save_button_unique").each(function(idx,el){
			$(this).css("display","none");
		}); // $(".edit_button_unique").each(function(idx,el){

		$("#loading_prompt_container").fadeOut("fast",function(){
			$("#main_result_container").fadeIn("fast",function(){
				initializeScriptsForDynamicallyLoadedHTML();
			});

		});
		


	}); // $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/insurance/ModalInterimGenerate.php?policy_code="+gch_SelectedInterimCodeOnDispTable,function(){

} // function loadBudgetSetup (){


function initializeScriptsForDynamicallyLoadedHTML(){

    $(".trnbudget_budget_amounts").unbind();

    // js plugins
	// javascript inside modal initializations here
	$(".decimals").autoNumeric('destroy');
    $('.decimals').autoNumeric('init',
    			{aSep:',',
				dGroup:'3',
				aDec:'.',
				mDec:'2'});

    $(".trnbudget_budget_amounts").on("keyup",function(){
    	var code = $(this).attr("data-code");

    	var lde_BudgetAmount = parseNum($("#trnbudget_budget_amount_"+code).val());
    	var lde_ActualAmount = parseNum($("#trnbudget_actual_amount_"+code).val());
    	// var lde_DiffAmount = parseNum($("#trnbudget_diff_amount_"+code).val());
    	var lde_DiffAmount = 0.00;

    	lde_DiffAmount = lde_BudgetAmount - lde_ActualAmount;
    	$("#trnbudget_diff_amount_"+code).val($.number(lde_DiffAmount,2));

    }); // $(".trnbudget_budget_amounts").on("keyup",function(){

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
					loadBudgetSetup();
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
		var codes = "&" + "booking_year=" + $("#booking_year").val()  +
		"&" + "money_trail_type_mst_code=" + $("#money_trail_type_mst_code").val() +
		"&" + "transactionmode=save_trn_budget"; 
		serializedData = serializedData + "" + codes;
		$("#main_result_container").fadeOut("fast",function(){
			$("#loading_message").html("Saving.. Please wait.");
			$("#loading_prompt_container").fadeIn("fast",function(){
				// SAVE INTERMEDIARY RATES
				$.ajax({
			        url: ABSOLUTE_PATH + "api/transactions/main/SaveTRNBudget.php",
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

									$("#main_result_container").fadeOut("fast",function(){
										$("#loading_message").html("Retrieving Data.. Please wait.");
										$("#loading_prompt_container").fadeIn("fast",function(){
											// AJAX CALL FOR TABLE
											loadBudgetSetup();
										}); // $("#loading_prompt_container").fadeIn("fast",function(){
									});
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
								$("#outer_error_below_container").slideDown("fast",function(){
									initializeScriptsForDynamicallyLoadedHTML();
								});
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