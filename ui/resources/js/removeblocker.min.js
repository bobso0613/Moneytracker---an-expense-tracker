$(document).ready(function(){

	if ( $( "#side-menu" ).length ) {
		$("#menu_cont").load(ABSOLUTE_PATH+"includes/body_sidebar_left.php?showmenu=1&programname="+$("#side-menu").attr("data-programname"),function(){
	        
	        $("#footer-main.toremove").fadeOut("fast",function(){
	        	$("#footer-main.toremove").remove();
	        	
	        	$("#side-menu").css("display","none");
	        	$("#side-menu").html($("#menu_cont").html());
	        	$('#side-menu').metisMenu();
	        	$(".active.menu-item").parents("li").toggleClass("active");
			    $(".active.menu-item").parents("ul").toggleClass("in");
	        	$("#side-menu").fadeIn("fast",function(){
	        		// to drop down parents of menu item selected
	        		//$("#menu_cont").unwrap();
	        	});
	        });
	    });
	} // if ( $( "#side-menu" ).length ) {

	$("#blocker").fadeOut('slow',function(){
        //$("#blocker").remove();

        //waitForNewAnnouncement();

		//$(window).resize();
    });

	
});