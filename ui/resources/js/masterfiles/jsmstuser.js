

$(document).ready(function(){
	
	$('#MasterfileAddEditModal').live('shown.bs.modal', function (e) {
		$(".masterfiletransaction-name-9-941-user_line_dtl_codes").each(function(idx,el){

			var clickedCheckbox = $(this);

			if ($(clickedCheckbox).prop("checked")==true){
				$(".checkbox.label-masterfiletransaction-name-9-1358-applied_peril_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).show();
					}
				});
				$(".checkbox.label-masterfiletransaction-name-9-1421-user_subline_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).show();
					}
				});

				//label-masterfiletransaction-name-9-1421-user_subline_mst_codes
			} // if ($(clickedCheckbox).prop("checked")==true){
			else {
				$(".checkbox.label-masterfiletransaction-name-9-1358-applied_peril_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).find(".child-checkboxes").each(function(idx,el){
							$(this).attr("checked",false); 
						});
						$(this).hide();
					}
				});
				$(".checkbox.label-masterfiletransaction-name-9-1421-user_subline_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).find(".child-checkboxes").each(function(idx,el){
							$(this).attr("checked",false); 
						});
						$(this).hide();
					}
				});
			} // ELSE ng if ($(clickedCheckbox).prop("checked")==true){

			/*
			if( $(this).attr("data-checkboxchaining") != $("#masterfiletransaction-id-7-1351-line_mst_code").val() )
			{	
				$(".child-checkboxes").each(function(idx,el){
			//		$(this).attr("checked",false); 
				});
				$(this).hide(); 
			}
			else
			{ $(this).show();}
			*/

		});
				
		//$(".customformat").mask("999-999-999-999");


			/* DO PROCESS HERE UPON LOADING OF THE MODAL - INITIALIZE FIELDS */

		$(".masterfiletransaction-name-9-941-user_line_dtl_codes").on("change",function(){

			//masterfiletransaction-name-9-941-user_line_dtl_codes
			var clickedCheckbox = $(this);

			if ($(clickedCheckbox).prop("checked")==true){
				$(".checkbox.label-masterfiletransaction-name-9-1358-applied_peril_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).show();
					}
				});

				$(".checkbox.label-masterfiletransaction-name-9-1421-user_subline_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).show();
					}
				});

				//label-masterfiletransaction-name-9-1421-user_subline_mst_codes
			} // if ($(clickedCheckbox).prop("checked")==true){
			else {
				$(".checkbox.label-masterfiletransaction-name-9-1358-applied_peril_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).find(".child-checkboxes").each(function(idx,el){
							$(this).attr("checked",false); 
						});
						$(this).hide();
					}
				});

				$(".checkbox.label-masterfiletransaction-name-9-1421-user_subline_mst_codes").each(function(idx,el){
					if( $(this).attr("data-checkboxchaining") == $(clickedCheckbox).val() ){
						$(this).find(".child-checkboxes").each(function(idx,el){
							$(this).attr("checked",false); 
						});
						$(this).hide();
					}
				});
			} // ELSE ng if ($(clickedCheckbox).prop("checked")==true){

				/*
			$(".checkbox.label-masterfiletransaction-name-9-1358-applied_peril_mst_codes").each(function(idx,el){
				if( $(this).attr("data-checkboxchaining") != $(clickedCheckbox).val() )
				{	
					$(this).find(".child-checkboxes").each(function(idx,el){
						$(this).attr("checked",false); 
					});
					$(this).hide(); 
				}
				else
				{ $(this).show();}

			});
			*/
				
			//$("input[name~='masterfiletransaction-name-9-522-nature_of_loss_mst_codes[]']").hide();

			// alert( $("input[name~='masterfiletransaction-name-9-522-nature_of_loss_mst_codes[]']").val());



		});
	});
});