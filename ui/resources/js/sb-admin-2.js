// $(function() {

//     //$('#side-menu').metisMenu();

// });

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 52;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 104; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        /* my additional */
        var furtherMinus = 0;
        var topm  = 0;
        //console.log();
        //typeof variable_here === 'undefined'
        //console.log($("#scroller-nothing").css("margin-top"));
        /*if (!typeof $("#scroller") === 'undefined'){
            topm = parseInt($("#scroller").css("margin-top").replace(/px/gi, ''));
        }

        if (!typeof $("#scroller-nothing") === 'undefined'){
            furtherMinus = parseInt($("#scroller-nothing").css("margin-top").replace(/px/gi, ''));
        }
        else if ($("#scroller")&&topm>=0&&$("#scroller").data("state")=="1"){
            furtherMinus =  $("#scroller").outerHeight();
        }
        else {
            //furtherMinus = topm;
        }*/
        //console.log(furtherMinus);
        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset + furtherMinus;
        
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
            //if ($(this).width() < 768) {
                //$("#page-wrapper").css("margin-top", (topOffset) + "px");
            //}

            

            $(".navbar-default.sidebar").css("height", (height) + "px");
            $(".chat-sidebar").css("height", (height) + "px");
            $(".chat-sidebar").css("top", (topOffset + furtherMinus) + "px");

            // datatable stuff
            

            if ($("#page-wrapper .table-responsive .table:not(.inside-modal)").length){
                
                //var paddingOnly = $("#page-wrapper").css("padding-right");
                //var widthOnly = $("#page-wrapper").innerWidth() - (parseInt(paddingOnly.replace(/px/gi, '')) + 30);

				var widthOnly = $("#page-wrapper .table-responsive .table:not(.inside-modal)").parents(".table-responsive").innerWidth();
                $("#page-wrapper .table-responsive .table:not(.inside-modal)").css("width",((widthOnly)+"px"));

                // $(".dataTables_wrapper table").each(function(idx,el){
                //     var tablenme = "#"+$(this).attr("id");
                //     setTimeout(function(){
                //         $(tablenme).DataTable().columns.adjust().draw();
                //     }, 200);
                // });

                //$("#closespecialdiv").css("top",(topOffset-6)+"px");
            }   
			
			if ($("#page-wrapper .table-responsive .table.inside-modal").length){
				var widthOnly = $("#page-wrapper .table-responsive .table.inside-modal").parents(".table-responsive").innerWidth();
				//console.log (widthOnly);
				$("#page-wrapper .table-responsive .table.inside-modal").css("width", ((widthOnly)+"px"));
			}
			
            //$("#page-wrapper .table-responsive .table").css("width",($("#page-wrapper").innerWidth()-$(".chat-sidebar").innerWidth)+"px");

            
            //navbar-default sidebar

            //$(".chat-sidebar-contents").css("height", (height) + "px");
        }
    })
})
