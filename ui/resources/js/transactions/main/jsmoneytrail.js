var gch_CurrentMode = "";
var gch_SelectedInterimCode = "";
var gch_SelectedInterimCodeOnDispTable = "";

$(document).ready(function(){

	var indexTbl = $("#transaction_table").DataTable({
						"columnDefs" : [
				  						{"targets": [ 0 ],"visible": false,"searchable": false},
				  						{"targets": [ 9,10,11 ],"visible": false,"searchable": false},
				  						{"targets": [ 1,6,7 ],"visible": true,"searchable": false}
										],
						"order": [[ 2, "desc" ]],
						"processing":true,
						"serverSide": true,
						"ajax": {
						    "url": "../../api/transactions/main/ReceiveInterimTable.php",
						    "type": "POST",
					    	"data": function ( d ) {
				                d.dbconnect = 'bd926c5_moneytracker';
				                d.tablename = 'trnmoneytrail';
				                d.columnstodisplay = '`bd926c5_moneytracker`.`trnmoneytrail`.`code`,`bd926c5_moneytracker`.`trnmoneytrail`.`money_trail_date`,`bd926c5_moneytracker`.`trnmoneytrail`.`money_trail_no`,`bd926c5_moneytracker`.`385`.`money_trail_name` as `money_trail_name`,`bd926c5_moneytracker`.`trnmoneytrail`.`description`,`bd926c5_moneytracker`.`387`.`reference_name`,`bd926c5_moneytracker`.`trnmoneytrail`.`total_amount`,`bd926c5_moneytracker`.`trnmoneytrail`.`no_of_items`,`bd926c5_moneytracker`.`386`.`money_trail_name` as `deduct_money_trail_name`,`bd926c5_moneytracker`.`trnmoneytrail`.`is_paid`,`bd926c5_moneytracker`.`trnmoneytrail`.`paid_user_mst_code`,`bd926c5_moneytracker`.`trnmoneytrail`.`paid_at`';

				                d.columnsfieldformat = 'usesformat=no,usesformat=yes|formatmode=datetime|format=m/d/Y,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=yes|formatmode=number|format=2,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=yes|formatmode=datetime|format=m/d/Y';

				                // usesformat=no,usesformat=yes|formatmode=datetime|format=m/d/Y h:i a,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no,usesformat=no

				                d.columnsdatatype = '1,1,1,1,1,1,1,1,1,1,1,1';
				            	d.innerjoinTable = '`bd926c5_moneytracker`.`mstmoneytrailtype` as `385`,`385`.`code`=`trnmoneytrail`.`money_trail_type_mst_code`';
				            	d.leftjoinTable = '`bd926c5_moneytracker`.`mstmoneytrailtype` as `386`,`386`.`code`=`trnmoneytrail`.`account_money_trail_type_mst_code`|`bd926c5_moneytracker`.`mstreference` as `387`,`387`.`code`=`trnmoneytrail`.`reference_mst_code`';
				            	d.filterdataname = "created_user_mst_code-7"; // | separated
				            	d.filterdata = $("#created_user_mst_code").val() ;

				    			// <th class="table-header" title="">Code</th>
								// <th class="table-header" title="">Trail Date</th>
								// <th class="table-header" title="Trail No"  style="white-space:nowrap" >Trail no.</th>
								// <th class="table-header" title="Trail Type"  style="" >Trail Type</th>
								// <th class="table-header" title="Description"  style="" >Description</th>
								// <th class="table-header" title="Reference" style="">Reference</th>
								// <th class="table-header">Total Amount</th>
								// <th class="table-header">No. of Items</th>
								// <th class="table-header">Account to Deduct</th>
								// <th class="table-header">is_paid</th>
								// <th class="table-header">paid_user_mst_code</th>
								// <th class="table-header">paid_at</th>


				            }
					  	}
				  	});

	indexTbl.on( 'draw', function () {
		    var ctr=1;
		    $("#transaction_table tbody tr").each(function(idx,el){

		    	$(this).find("td:nth-child(1)").css("white-space","nowrap");
		    	$(this).find("td:nth-child(2)").css("white-space","nowrap");
		    	$(this).find("td:nth-child(6)").css("text-align","right");

		    	var data = indexTbl.row( $(this) ).data();


		    	$(this).attr("data-selected","0");
		    	$(this).attr("data-state","1");
		    	$(this).addClass("transaction_table_rows");
		    	$(this).on("click",function(){
		    		selectIndexRow(this);
		    		//alert ('clicked');
		    	}); // $(this).on("click",function(){

		    	//if (!typeof data == "undefined"){

		    		try {

		    			if (gch_SelectedInterimCodeOnDispTable!="" &&
			    			gch_SelectedInterimCodeOnDispTable==data[0]){
		    				selectIndexRow(this);
			    		}

		    			var is_paid = data[9].trim();

			    		var classToAdd = "";
						if (is_paid=="1"){
				    		classToAdd = "blue-hover";
				    	} // else if (is_paid=="1"){
			    		else {
			    			classToAdd = "yellow-hover";
			    		}
				    	$(this).addClass(classToAdd);

		    		} catch(err) {

		    		}

		    	//}



		    }); // $("th.table-header").each(function(idx,el){
	    	$(window).resize();
	    	$("#blocker").fadeOut("fast");
		});


	/* FILTER THE RATES */
	$("#filter_button_unique").live("click",function(){

		$("#loading-message").html("<b>Filtering Data...</b>");
		$("#blocker").fadeIn("fast",function(){
			var tables = $.fn.dataTable.tables(true);
			$( tables ).DataTable().ajax.reload(null, false);
		});
		return false;
	}); // $("#filter_button_unique").live("click",function(){


	/* CLEAR FILTERS */
	//data_container
	$("#clear_button_unique").live("click",function(){

		$("#loading-message").html("<b>Clearing Filter Values...</b>");
		$("#blocker").fadeIn("fast",function(){

			$(".filter-fields").each(function(idx,el){

				if ($(this).is('select')) {
					$(this).val($(this).find("option:first-child").attr("value"));
					$(this).change();
				} // if ($(this).is('select')) {
				else {
					$(this).val("");
					$(this).typeahead("setQuery","");
				} // ELSE ng if ($(this).is('select')) {

				
			});
			var tables = $.fn.dataTable.tables(true);
			$( tables ).DataTable().ajax.reload(null, false);
		});
		return false;
	}); // $("#clear_button_unique").live("click",function(){


	// FOR MODALS
	$('#PrintOutModal').live('shown.bs.modal', function (e) {
		topOffset = 200;
		height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
		height = height - topOffset;
		$(this).find('iframe#print-frame').css({
              //height:'auto', //probably not needed
              'height':height+'px'
       });
	}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {

	$('#AddInterimModal').live('show.bs.modal', function (e) {

		topOffset = 200;
		height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
		height = height - topOffset;

		$(this).find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
	}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {
	
	$('#AddInterimModal').live('hidden.bs.modal', function (e) {
		$("#loading-message").html("<b>Refreshing table records...</b>");
		$("#blocker").fadeIn("fast",function(){
			var tables = $.fn.dataTable.tables(true);
			$( tables ).DataTable().ajax.reload(null, true);
		});

	}); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {


	/* CANCEL CHANGES ACTION */
	$(".cancel_button_unique").each(function(idx,el){
		$(this).live("click",function(){
			var btn = $(this).button('loading');
			$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=cancel&trans=has_callback_function&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){
				$("#dialog-modal-wrapper").css("display","");
				$("#DialogModal").modal("show");
				btn.button('reset');
			}); // $("#dialog-modal-wrapper").load()
		}); // $(this).live("click",function(){
	}); // $(".cancel_button_unique").each(function(){


	/* SAVE CHANGES ACTION */
	$(".save_button_unique").each(function(idx,el){
		$(this).live("click",function(){
			var btn = $(this).button('loading');
			$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=save&trans=has_callback_function&dialog_title=Save&dialog_message=Are|you|sure|do|you|want|to|save|changes",function(){
				$("#dialog-modal-wrapper").css("display","");
				$("#DialogModal").modal("show");
				btn.button('reset');
			}); // $("#dialog-modal-wrapper").load()
		}); // $(this).live("click",function(){
	}); // $(".save_button_unique").each(function(){


	/* ------- ADD NEW INTERIM ------- */
	$(".add_interim").live("click",function(){
		$(".add_interim").button('loading');
		$("#loading-message").html("<b>Loading Modal...</b>");
		$("#blocker").fadeIn("fast",function(){

			
			deselectAllTransactionTableRows();

			$("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/main/ModalMoneyTrailAddEdit.php?transactionmode=add",function(){

				gch_CurrentMode = "add_interim";
				$("#modal-wrapper").css("display","");
				$("#AddInterimModal").modal("show");
				$("#blocker").fadeOut("fast");
				$(".add_interim").button('reset');


				/* CANCEL CHANGES ACTION */
				$(".cancel_modal_action").each(function(idx,el){
					$(this).live("click",function(){
						var btn = $(this).button('loading');
						gch_CurrentMode = "add_interim";
						$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=cancel&trans=has_callback_function&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){

							$("#dialog-modal-wrapper").css("display","");
							$("#DialogModal").modal("show");
							btn.button('reset');
						}); // $("#dialog-modal-wrapper").load()
					}); // $(this).live("click",function(){
				}); // $(".cancel_modal_action").each(function(){
				$("#save_add_interim").on("click",function(){
					if (validateClientSideAddInterim()){
						var btn = $(this).button('loading');
						gch_CurrentMode = "add_interim";
						$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=save&trans=has_callback_function&dialog_title=Proceed&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
							$("#dialog-modal-wrapper").css("display","");
							$("#DialogModal").modal("show");
							btn.button('reset');
						}); // $("#dialog-modal-wrapper").load()
					} // if (validateClientSideAddInterim()){
					return false;
				}); // $("#save_add_interim").on("click",function(){

				$("#trnmoneytrail_money_trail_type_mst_code").chained("#trnmoneytrail_money_trail_type");

				$('body').unbind("focus");
			    $('body').on('focus',".datepickers", function(){
			    	var datepickerObj = $(this);
			    	$(this).datepicker({
				    	format: "mm-dd-yyyy",
				        todayBtn: "linked",
				        orientation: "top left",
				        autoclose: true,
				        todayHighlight: true

						  /*,
				        startDate: '-0m'*/
				    }).on("changeDate",function(e){
				    	var splitFirstDate = $("#trnmoneytrail_money_trail_date").val().split("-");
				    	$("#trnmoneytrail_booking_year").val(splitFirstDate[2]);
				    	$("#trnmoneytrail_booking_period").val(parseInt(splitFirstDate[0]));
				    }); // }).on("changeDate",function(e){
			    }); // $('body').on('focus',".datepickers", function(){

		    	$(".trnmoneytraildtlitems.decimals").autoNumeric('destroy');
			    $('.trnmoneytraildtlitems.decimals').autoNumeric('init',
			    			{aSep:',',
							dGroup:'3',
							aDec:'.',
							mDec:'2'});


			}); // $("#modal-wrapper").load()
		}); // $("#blocker").fadeIn("fast",function(){
		return false;
	}); // $(".add_interim").live("click",function(){
	/* ------- END - ADD NEW INTERIM ------- */

	//  PROCESS BUTTON -> OPENS TRANSACTION WINDOW
	$(".process_interim").live('click',function(){
		$(".process_interim").button('loading');
		$("#loading-message").html("<b>Loading Modal...</b>");
		$("#blocker").fadeIn("fast",function(){
			
			$("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/main/ModalMoneyTrailAddEdit.php?transactionmode=edit&money_trail_trn_code="+gch_SelectedInterimCodeOnDispTable,function(){

				gch_CurrentMode = "process_interim";
				$("#modal-wrapper").css("display","");
				$("#AddInterimModal").modal("show");
				$("#blocker").fadeOut("fast");
				$(".process_interim").button('reset');


				/* CANCEL CHANGES ACTION */
				$(".cancel_modal_action").each(function(idx,el){
					$(this).live("click",function(){
						var btn = $(this).button('loading');
						gch_CurrentMode = "process_interim";
						$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=cancel&trans=has_callback_function&dialog_title=Cancel&dialog_message=Are|you|sure|do|you|want|to|cancel$<br>Unsaved|changes|will|be|lost.",function(){

							$("#dialog-modal-wrapper").css("display","");
							$("#DialogModal").modal("show");
							btn.button('reset');
						}); // $("#dialog-modal-wrapper").load()
					}); // $(this).live("click",function(){
				}); // $(".cancel_modal_action").each(function(){
				$("#save_add_interim").on("click",function(){
					if (validateClientSideAddInterim()){
						var btn = $(this).button('loading');
						gch_CurrentMode = "process_interim";
						$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=success&mode=save&trans=has_callback_function&dialog_title=Proceed&dialog_message=Are|you|sure|do|you|want|to|save$",function(){
							$("#dialog-modal-wrapper").css("display","");
							$("#DialogModal").modal("show");
							btn.button('reset');
						}); // $("#dialog-modal-wrapper").load()
					} // if (validateClientSideAddInterim()){
					return false;
				}); // $("#save_add_interim").on("click",function(){

				$("#trnmoneytrail_money_trail_type_mst_code").chained("#trnmoneytrail_money_trail_type");

				$('body').unbind("focus");
			    $('body').on('focus',".datepickers", function(){
			    	var datepickerObj = $(this);
			    	$(this).datepicker({
				    	format: "mm-dd-yyyy",
				        todayBtn: "linked",
				        orientation: "top left",
				        autoclose: true,
				        todayHighlight: true

						  /*,
				        startDate: '-0m'*/
				    }).on("changeDate",function(e){
				    	
				    }); // }).on("changeDate",function(e){
			    }); // $('body').on('focus',".datepickers", function(){

		    	$(".trnmoneytraildtlitems.decimals").autoNumeric('destroy');
			    $('.trnmoneytraildtlitems.decimals').autoNumeric('init',
			    			{aSep:',',
							dGroup:'3',
							aDec:'.',
							mDec:'2'});


			}); // $("#modal-wrapper").load()
		}); // $("#blocker").fadeIn("fast",function(){
		return false;
	});

	//  DELETE INTERIM BUTTON
	$(".delete_interim").live('click',function(){
		gch_CurrentMode = "delete_interim";
		$(".delete_interim").button('loading');
		// every validation sa server side na, mas madali i-validate
		$("#dialog-modal-wrapper").load(ABSOLUTE_PATH+"includes/DialogModal.php?colour=danger&mode=save&trans=has_callback_function&dialog_title=Delete&dialog_message=Are|you|sure|do|you|want|to|delete|the|selected|money|trail$",function(){
			$("#dialog-modal-wrapper").css("display","");
			$("#DialogModal").modal("show");
			$(".delete_interim").button('reset');
		}); // $("#dialog-modal-wrapper").load()
		return false;
	});


	CKEDITOR.cleanWord = function( data, editor ){
		return data;
	} // CKEDITOR.cleanWord = function( data, editor ){

}); // $(document).ready(function(){


// CANCEL CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR CANCEL
function cancelCallbackFunction(response){
	if (response=="yes"){
		
		if (gch_CurrentMode=="add_interim"||gch_CurrentMode=="process_interim"){
		
			$("#AddInterimModal").modal("hide");
		
		} // if (gch_CurrentMode=="add_interim"||gch_CurrentMode=="process_interim"){

	} // if (response=="yes"){
	gch_CurrentMode = "";
} // function cancelCallbackFunction(response){


// SAVE CALLBACK FUNCTION -> TINAWAG TO SA PAG CLICK NG 'YES' SA DIALOG NG ARE YOU SURE FOR SAVE
function saveCallbackFunction(response){
	if (response=="yes"){
		// alert(gch_CurrentMode);
		if (gch_CurrentMode=="add_interim"||gch_CurrentMode=="process_interim"){

			saveAddInterim();

		} // if (gch_CurrentMode=="add_interim"||gch_CurrentMode=="process_interim"){


		else if (gch_CurrentMode=="delete_interim"){

			deleteInterim();

		} // else if (gch_CurrentMode=="delete_interim"){


	} // if (response=="yes"){
	//gch_CurrentMode = "";
} // function saveCallbackFunction(response){

// SAVE ADD INTERIM THEN PROCEED TO MAIN TRANSACTION PAGE IF SUCCESS
function saveAddInterim (){
	// save ajax
	var serializedData = $("form#form_masterfile_dynamic").serialize();
	serializedData = serializedData + "&transactionmode="+gch_CurrentMode;
	if (gch_SelectedInterimCodeOnDispTable!=""){
		serializedData = serializedData + "&money_trail_trn_code="+gch_SelectedInterimCodeOnDispTable;
	} // if (gch_SelectedInterimCodeOnDispTable!=""){
	serializedData = serializedData + "&" + $("form#form_masterfile_unique").serialize();

	// DISABLE SAVE BUTTONS WHILE SAVING
	$("#save_add_interim").button("loading");
	$(".cancel_modal_action").each(function(idx,el){
		$(this).prop("disabled",true);
	});

	$("#modal_error_container").slideUp("fast");
	$("#outer_success_below_container").slideUp("fast");

	$("#loading-message").html("<b>Processing action...</b>");
	$("#blocker").fadeIn("fast",function(){

		$.ajax({
	        url: ABSOLUTE_PATH + "api/transactions/main/SaveMoneyTrailTable.php",
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


	            	$("#outer_success_below_message").html(response[0].error_message); //actually a success message
					$("#outer_success_below_container").slideDown("fast",function(){
						setTimeout(function(){
							$("#outer_success_below_container").slideUp("fast");
						},5000);
					});
					gch_CurrentMode = "";
					$("#AddInterimModal").modal("hide");

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
				$("#modal_error_container_content").html("<ul>"+errorMessages+"</ul>");
				$("#modal_error_container").slideDown("fast");
	        } // if (isError==true){


        	$("#blocker").fadeOut("fast");
			$("#save_add_interim").button("reset");
			$(".cancel_modal_action").each(function(idx,el){
				$(this).prop("disabled",false);
			});

	    }).fail(function (jqXHR, textStatus, errorThrown){
			errorMessages = "<li>Could not connect to the server or file not found.</li>";
			$("#modal_error_container_content").html("<ul>"+errorMessages+"</ul>");
			$("#modal_error_container").slideDown("fast");

			$("#blocker").fadeOut("fast");
			$("#save_add_interim").button("reset");
			$(".cancel_modal_action").each(function(idx,el){
				$(this).prop("disabled",false);
			});
	    }); // $.ajax({

	});

} // function saveAddInterim (){

// DELETE INTERIM

function deleteInterim (){
	// save ajax
	var serializedData = $("#form_masterfile_unique").serialize();
	serializedData = serializedData + "&transactionmode="+gch_CurrentMode;
	serializedData = serializedData + "&money_trail_trn_code=" + gch_SelectedInterimCodeOnDispTable


	$("#outer_error_below_container").slideUp("fast");
	$("#outer_success_below_container").slideUp("fast");
	$("#loading-message").html("<b>Processing action...</b>");
		$("#blocker").fadeIn("fast",function(){
		$.ajax({
	        url: ABSOLUTE_PATH + "api/transactions/main/SaveMoneyTrailTable.php",
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

					// success here
					gch_CurrentMode = "";
					gch_SelectedInterimCodeOnDispTable = "";

					$("#outer_success_below_message").html(response[0].error_message);
					$("#outer_success_below_container").slideDown("fast");

					deselectAllTransactionTableRows();

					var tables = $.fn.dataTable.tables(true);
					$( tables ).DataTable().ajax.reload(null, false);

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
				$("#outer_error_below_container_content").html("<ul>"+errorMessages+"</ul>");
				$("#outer_error_below_container").slideDown("fast");
	        } // if (isError==true){


        	$("#blocker").fadeOut("fast");


	    }).fail(function (jqXHR, textStatus, errorThrown){
			errorMessages = "<li>Could not connect to the server or file not found.</li>";
			$("#outer_error_below_container_content").html("<ul>"+errorMessages+"</ul>");
			$("#outer_error_below_container").slideDown("fast");
			$("#blocker").fadeOut("fast");
	    }); // $.ajax({
	});
} // function deleteInterim (){


// CLIENT SIDE VALIDATION FOR ADD INTERIM
function validateClientSideAddInterim (){
	var errorMessage = "<ul>";
	var errorDetected = false;
	var ctr = 0;
	var shouldBeError = true;

	$("#modal_error_container").slideUp("fast");

	if ($("#trnmoneytrail_money_trail_date").val()==""){
		errorMessage = errorMessage + "<li> Date should not be blank.</li>";
		errorDetected = true;
	} // if ($("#trnmoneytrail_money_trail_date").val()==""){


	if ($("#trnmoneytrail_money_trail_type").val()==""){
		errorMessage = errorMessage + "<li> Please select a Trail Type </li>";
		errorDetected = true;
	} // if ($("#trnmoneytrail_money_trail_type").val()==""){

	if ($("#trnmoneytrail_money_trail_type_mst_code").val()==""){
		errorMessage = errorMessage + "<li> Please select a Trail </li>";
		errorDetected = true;
	} // if ($("#trnmoneytrail_money_trail_type_mst_code").val()==""){

	if ($("#trnmoneytrail_description").val()==""){
		errorMessage = errorMessage + "<li> Description should not be blank.</li>";
		errorDetected = true;
	} // if ($("#trnmoneytrail_description").val()==""){

	var llo_hasItems = false;
	var lin_ctr = 1;
	$(".trnmoneytraildtlitems_codes").each(function(idx,el){
		llo_hasItems = true;

		var lch_value = $(el).val();

		if ($("#trnmoneytraildtlitems_description_"+lch_value).val()=="") {
			errorMessage = errorMessage + "<li> Description on item no. "+lin_ctr+" should not be blank.</li>";
			errorDetected = true;
		} // if ($("#trnmoneytraildtlitems_description_"+lch_value).val()=="") {

		if (parseNum($("#trnmoneytraildtlitems_amount_"+lch_value).val())==0.00 || $("#trnmoneytraildtlitems_amount_"+lch_value).val()=="") {
			errorMessage = errorMessage + "<li> Amount on item no. "+lin_ctr+" should not be 0.00 or blank.</li>";
			errorDetected = true;
		} // if (parseNum($("#trnmoneytraildtlitems_amount_"+lch_value).val())==0.00 || $("#trnmoneytraildtlitems_amount_"+lch_value).val()=="") {

		lin_ctr++;

	}); // $(".trnmoneytraildtlitems_codes").each(function(idx,el){
	if (!llo_hasItems) {
		errorMessage = errorMessage + "<li> You should have at least 1 item detail.</li>";
		errorDetected = true;
	} // if (!llo_hasItems) {

	errorMessage += "</ul>";

	if (errorDetected==true){
		$("#modal_error_container_content").html(errorMessage);
		$("#modal_error_container").slideDown("fast",function(){
			$("#modal_error_container").focus();
		});
		$("#save_modal_action").button("reset");

		return false;
	} // if (errorDetected==true){
	else {
		// should be server-side validation here, but ajax call is ASYNCHRONOUS and if so,
		// the return would be true at all times.. isabay na lang sa pag SAVE
		return true;
	} // ELSE ng if (errorDetected==true){
} // function validateClientSideAddInterim (){

// DESELECT ALL INDEX
function deselectAllTransactionTableRows(){

	// dont forget to reset data-state to 1 pag tapos ng edit/add
	$(".transaction_table_rows").each(function(idx,el){

		$(this).attr("data-selected","0");
		$(this).attr("data-state","0");
		$(this).removeClass("selected");

	});

	$(".process_interim,.delete_interim,.print_interim").each(function(idx,el){
		$(this).prop("disabled",true);
	});


} // function deselectAllTransactionTableRows(){


// FUNCTION TO SELECT ROW - INDEX TABLE
function selectIndexRow(obj){
	//alert ('edit mode');
	var data = $("#transaction_table").DataTable().row( $(obj) ).data();

	var is_paid = data[9].trim();	

	var classToAdd = "";
	if (is_paid=="1"){
		classToAdd = "blue-selected";
	} // else if (is_paid=="1"){\
	else {
		classToAdd = "yellow-selected";
	}
	//$(this).addClass();

	if ($(obj).attr("data-state")=="1"){
		if ($(obj).attr("data-selected")=="1"){
			// de-select
			$(obj).attr("data-selected","0");
			$(obj).toggleClass(classToAdd);

			gch_SelectedInterimCodeOnDispTable = "";

			// reset all fields value function here
			// disable all per record buttons
			$(".process_interim,.delete_interim,.print_interim").each(function(idx,el){
				$(this).prop("disabled",true);
			}); // $(".process_interim,.delete_interim,.route_interim,,.print_interim").each(function(idx,el){

		} // if ($(obj).attr("data-selected")=="1"){
		else if ($(obj).attr("data-selected")=="0"){
			// select
			$("tr.transaction_table_rows").each(function(idx,el){

				var perRow = $("#transaction_table").DataTable().row( $(this) ).data();
				var is_paid2 = perRow[9].trim();
				$(this).attr("data-selected","0");

				var classToAdd2 = "";
				if (is_paid2=="1"){
					classToAdd2 = "blue-selected";
				} // else if (is_paid=="1"){
				else {
					classToAdd2 = "yellow-selected";
				}

				$(this).removeClass(classToAdd2);
			});

			$(obj).attr("data-selected","1");
			$(obj).toggleClass(classToAdd);

			gch_SelectedInterimCodeOnDispTable = data[0];

			$(".process_interim,.delete_interim,.print_interim").each(function(idx,el){
				$(this).prop("disabled",false);
			}); // $(".process_interim,.delete_interim,.print_interim").each(function(idx,el){

			if (is_paid=="1"){
				$(".delete_interim").each(function(idx,el){
					$(this).prop("disabled",true);
				}); // $(".process_interim,.delete_interim").each(function(idx,el){
			}


		} // else if ($(obj).attr("data-selected")=="0"){
	} // if ($(obj).attr("data-state")=="1"){
} // function selectIndexRow(obj){


function checkMoneyTrailValue(){

	if (parseNum($("#trnmoneytraildtlitems_amount").val())!=0.00 && $("#trnmoneytraildtlitems_description").val().trim()!="") {
		$("#trnmoneytraildtlitems_add").prop("disabled",false);
	} // if (parseNum($("#trnmoneytraildtlitems_amount").val())!=0.00 && $("#trnmoneytraildtlitems_description").val().trim()!="") {
	else {
		$("#trnmoneytraildtlitems_add").prop("disabled",true);
	} // ELSE ng if (parseNum($("#trnmoneytraildtlitems_amount").val())!=0.00 && $("#trnmoneytraildtlitems_description").val().trim()!="") {

} // function checkMoneyTrailValue(){

function addMoneyTrailDetails(){

	var lch_htmlOutput = "";
	var lch_textslugify = slugify($("#trnmoneytraildtlitems_description").val().trim());

	var lde_amount = parseNum($("#trnmoneytraildtlitems_amount").val());
	var lch_desc = $("#trnmoneytraildtlitems_description").val().trim();

	lch_htmlOutput = '<div class="form-group" style="margin-bottom:3px" id="trnmoneytraildtlitems_container_'+lch_textslugify+'">';
	lch_htmlOutput += '<input type="hidden" name="trnmoneytraildtlitems_codes_toadd[]" class="trnmoneytraildtlitems_codes" value="'+lch_textslugify+'">';
	lch_htmlOutput += '<label for="trnmoneytraildtlitems" class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label"></label>';
	lch_htmlOutput += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" id="">';
	lch_htmlOutput += '<input type="text" class="form-control input-sm" id="trnmoneytraildtlitems_description_'+lch_textslugify+'" maxlength="5000" name="trnmoneytraildtlitems_description_'+lch_textslugify+'" value="'+lch_desc+'"/>';
	lch_htmlOutput += '</div>';

	lch_htmlOutput += '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding-left:0px;padding-right:0px" id="">';
	lch_htmlOutput += '<input type="text" class="form-control input-sm right decimals trnmoneytraildtlitems" name="trnmoneytraildtlitems_amount_'+lch_textslugify+'" id="trnmoneytraildtlitems_amount_'+lch_textslugify+'" value="'+lde_amount+'"/>';
	lch_htmlOutput += '</div>';

	lch_htmlOutput += '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="padding-left:5px;padding-right:0px" id="">';
	lch_htmlOutput += '<button class="btn btn-danger btn-sm trnmoneytraildtlitems_delete" type="button" title="Delete" onclick="deleteMoneyTrailDetails(\''+lch_textslugify+'\')" id="trnmoneytraildtlitems_delete_'+lch_textslugify+'"><i class="fa fa-trash-o"></i></button>';
	lch_htmlOutput += '</div>';
	lch_htmlOutput += '</div>';

	$("#trnmoneytraildtlitems_container").append(lch_htmlOutput);

	$(".trnmoneytraildtlitems.decimals").autoNumeric('destroy');
    $('.trnmoneytraildtlitems.decimals').autoNumeric('init',
    			{aSep:',',
				dGroup:'3',
				aDec:'.',
				mDec:'2'});

    $("#trnmoneytraildtlitems_add").prop("disabled",true);
    $("#trnmoneytraildtlitems_amount").val("");
    $("#trnmoneytraildtlitems_description").val("");


} // function addMoneyTrailDetails(){

function deleteMoneyTrailDetails(code) {
	$("#trnmoneytraildtlitems_container_"+code).remove();

	$("#trnmoneytraildtlitems_add").prop("disabled",true);
    $("#trnmoneytraildtlitems_amount").val("");
    $("#trnmoneytraildtlitems_description").val("");
} // function deleteMoneyTrailDetails(code) {


function slugify(string) {
    return string.trim() // Remove surrounding whitespace.
    .toLowerCase() // Lowercase.
    .replace(/[^a-z0-9]+/g,'_') // Find everything that is not a lowercase letter or number, one or more times, globally, and replace it with a dash.
    .replace(/^-+/, '') // Remove all dashes from the beginning of the string.
    .replace(/-+$/, ''); // Remove all dashes from the end of the string.
} // function slugify(string) {

function formatDate(date, format, utc) {
    var MMMM = ["\x00", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var MMM = ["\x01", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var dddd = ["\x02", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var ddd = ["\x03", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    function ii(i, len) {
        var s = i + "";
        len = len || 2;
        while (s.length < len) s = "0" + s;
        return s;
    }

    var y = utc ? date.getUTCFullYear() : date.getFullYear();
    format = format.replace(/(^|[^\\])yyyy+/g, "$1" + y);
    format = format.replace(/(^|[^\\])yy/g, "$1" + y.toString().substr(2, 2));
    format = format.replace(/(^|[^\\])y/g, "$1" + y);

    var M = (utc ? date.getUTCMonth() : date.getMonth()) + 1;
    format = format.replace(/(^|[^\\])MMMM+/g, "$1" + MMMM[0]);
    format = format.replace(/(^|[^\\])MMM/g, "$1" + MMM[0]);
    format = format.replace(/(^|[^\\])MM/g, "$1" + ii(M));
    format = format.replace(/(^|[^\\])M/g, "$1" + M);

    var d = utc ? date.getUTCDate() : date.getDate();
    format = format.replace(/(^|[^\\])dddd+/g, "$1" + dddd[0]);
    format = format.replace(/(^|[^\\])ddd/g, "$1" + ddd[0]);
    format = format.replace(/(^|[^\\])dd/g, "$1" + ii(d));
    format = format.replace(/(^|[^\\])d/g, "$1" + d);

    var H = utc ? date.getUTCHours() : date.getHours();
    format = format.replace(/(^|[^\\])HH+/g, "$1" + ii(H));
    format = format.replace(/(^|[^\\])H/g, "$1" + H);

    var h = H > 12 ? H - 12 : H == 0 ? 12 : H;
    format = format.replace(/(^|[^\\])hh+/g, "$1" + ii(h));
    format = format.replace(/(^|[^\\])h/g, "$1" + h);

    var m = utc ? date.getUTCMinutes() : date.getMinutes();
    format = format.replace(/(^|[^\\])mm+/g, "$1" + ii(m));
    format = format.replace(/(^|[^\\])m/g, "$1" + m);

    var s = utc ? date.getUTCSeconds() : date.getSeconds();
    format = format.replace(/(^|[^\\])ss+/g, "$1" + ii(s));
    format = format.replace(/(^|[^\\])s/g, "$1" + s);

    var f = utc ? date.getUTCMilliseconds() : date.getMilliseconds();
    format = format.replace(/(^|[^\\])fff+/g, "$1" + ii(f, 3));
    f = Math.round(f / 10);
    format = format.replace(/(^|[^\\])ff/g, "$1" + ii(f));
    f = Math.round(f / 10);
    format = format.replace(/(^|[^\\])f/g, "$1" + f);

    var T = H < 12 ? "AM" : "PM";
    format = format.replace(/(^|[^\\])TT+/g, "$1" + T);
    format = format.replace(/(^|[^\\])T/g, "$1" + T.charAt(0));

    var t = T.toLowerCase();
    format = format.replace(/(^|[^\\])tt+/g, "$1" + t);
    format = format.replace(/(^|[^\\])t/g, "$1" + t.charAt(0));

    var tz = -date.getTimezoneOffset();
    var K = utc || !tz ? "Z" : tz > 0 ? "+" : "-";
    if (!utc) {
        tz = Math.abs(tz);
        var tzHrs = Math.floor(tz / 60);
        var tzMin = tz % 60;
        K += ii(tzHrs) + ":" + ii(tzMin);
    }
    format = format.replace(/(^|[^\\])K/g, "$1" + K);

    var day = (utc ? date.getUTCDay() : date.getDay()) + 1;
    format = format.replace(new RegExp(dddd[0], "g"), dddd[day]);
    format = format.replace(new RegExp(ddd[0], "g"), ddd[day]);

    format = format.replace(new RegExp(MMMM[0], "g"), MMMM[M]);
    format = format.replace(new RegExp(MMM[0], "g"), MMM[M]);

    format = format.replace(/\\(.)/g, "$1");

    return format;
}