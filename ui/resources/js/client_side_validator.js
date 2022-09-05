function ClientSideValidate (obj){
	var validatingFormID = $(obj).data("validatingform");
	var errMsg ='';
	if (validatingFormID==''){
		errMsg+='Validating Form not set.';
	}
	else {
		$("#"+validatingFormID+" input[type=hidden]").each(function(){
			if ($(this).val().trim()=='' && $(this).data("required")==true){
				errMsg=errMsg+"- Please choose a/an "+$(this).attr("placeholder")+".<br>";
			}
		});
		$("#"+validatingFormID+" input[type=text], #"+validatingFormID+" input[type=password], #"+validatingFormID+" select, #"+validatingFormID+" textarea").each(function(){
			if ($(this).val().trim()=='' && $(this).data("required")==true){
				if ($(this).attr("type")=="select"){
					errMsg=errMsg+"- Please select a/an "+$(this).attr("placeholder")+".<br>";
				} else {
					errMsg=errMsg+"- "+$(this).attr("placeholder")+" should not be blank.<br>";
				}
				
			}
		});
		var currentid = '';
		var placeholder = '';
		var required = false;
		var ctr = 0;
		$("#"+validatingFormID+" input[type=checkbox],#"+validatingFormID+" input[type=radio]").each(function(){
			if (currentid!=$(this).attr("name")){
				if (currentid!='' && ctr==0 && required){
					errMsg=errMsg+"- Please select a/an "+placeholder+".<br>";
				}
				currentid = $(this).attr("name");
				required = $(this).data("required");
				placeholder = $(this).attr("placeholder");
				ctr = 0;
			}
			if ($(this).prop("checked")==true && $(this).data("required")==true){
				ctr++;
			}
		});
		if (currentid!='' && ctr==0 && required){
			errMsg=errMsg+"- Please select a/an "+placeholder+".<br>";
		}
	}

	if (errMsg!=''){
		$("#errorModalBody").html(errMsg);
		$('#errorModal').modal("show");
		return false;
	}
	return true;
}