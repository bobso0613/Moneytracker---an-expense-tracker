/***** JQUERY TWITCH.TV LIKE TOGGLING OF SIDEBAR MENU ****/
var currentmargin;
var toLeft;
var toRight;
var scroller = $('.scrollingtext');
var scrolling_velocity = 86;
var scrolling_from = 'right';
var offset = 200;
var duration = 500;
var clockTimerVar;
var onlineCooldown=60;
var onlineCurrentCooldown=0;
var firstTimeOnline = true;
var timeStamp = "";
var notificationRequests;
var serverDateTimeRequest;

var announcementFirstTime = true;
var last_announcement_code = 0;
var announcementRequests;

var
month = new Array();
month[0]="Jan.";
month[1]="Feb.";
month[2]="Mar.";
month[3]="Apr.";
month[4]="May";
month[5]="Jun.";
month[6]="Jul.";
month[7]="Aug.";
month[8]="Sep.";
month[9]="Oct.";
month[10]="Nov.";
month[11]="Dec.";



$(document).ready(function(){

    // Added :Mark

    // set event for keyup / shortcut keys
    document.addEventListener('keyup', new ShortcutKeysHandler().printShortcut, false);

    // Prevents modal-backdrop to still show even after modal.hide
    $('.modal').live('hidden.bs.modal', function (e) {
        //$(".modal-backdrop").remove();
        //$(".modal").removeClass("in").css("display", "none");
    });

    // End :Mark
    $('#ReportMainModal').live('show.bs.modal', function (e) {

        topOffset = 200;
        height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
        height = height - topOffset;

        $(this).find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
    }); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {
    //if (typeof $("#scroller") != 'undefined'){
    var reviewCount = 0;
    $(".scrollingtext .review").each(function(idx,el){
        reviewCount ++;
    });
    if (reviewCount>0){
        $('body').css("padding-bottom","60px");
        $('.navbar-default.sidebar').css("padding-bottom","60px");
        $('.chat-sidebar').css("padding-bottom","60px");
    }

    

    //}

    $('body').tooltip({
        selector: '[rel=tooltip]'
    });
    //startScrolling(scroller, scrolling_velocity, scrolling_from);
    try{
        calculate_popups();
    }catch (e){}

    $("#modal_error_close").live("click",function(){
        $("#modal_error_container").slideUp("fast",function(){
            $("#modal_error_container_content").html("");
        });
    });

    $("#modal_inner_error_close").live("click",function(){
        $("#modal_inner_error_container").slideUp("fast",function(){
            $("#modal_inner_error_container_content").html("");
        });
    });

    $("#modal_inner_success_close").live("click",function(){
        $("#modal_inner_success_container").slideUp("fast",function(){
            $("#modal_inner_success_message").html("");
        });
    });

    $("#modal_success_close").live("click",function(){
        $("#modal_success_container").slideUp("fast",function(){
            $("#modal_success_message").html("");
        });
    });

    $("#outer_error_close").live("click",function(){
        $("#outer_error_container").slideUp("fast",function(){
            $("#outer_error_container_content").html("");
        });
    });

    $("#outer_error_below_close").live("click",function(){
        $("#outer_error_below_container").slideUp("fast",function(){
            $("#outer_error_below_container_content").html("");
        });
    });

    $("#outer_success_close").live("click",function(){
        $("#outer_success_container").slideUp("fast",function(){
            $("#outer_success_message").html("");
        });
    });

    $("#outer_success_below_close").live("click",function(){
        $("#outer_success_below_container").slideUp("fast",function(){
            $("#outer_success_below_message").html("");
        });
    });


    clockStart();
    /*clockTimerVar = setInterval(function(){
        clockStart();

        }, 1000);*/



    //navbar-toggle collapsed
    /*$(".navbar-toggle.collapsed").click(function(){
        $("#page-wrapper").css("margin-top", "104px");
    });*/

    $("[data-toggle='tooltip']").tooltip();
    //currentmargin = $("#page-wrapper").css("margin-left");
    currentmargin="0px";
    if (currentmargin=="0px"){
        currentmargin = "250px"; // fallback static sizing

    }

    toLeft = parseInt(currentmargin.replace(/px/gi, ''));
    toRight = ($("#hasChat").val()=='true')?250:40;

    $("#sidebar-toggle-button").click(function(){
        if ($("#sidebar-toggle-button").data("mode")=="showed"){
            $("#page-wrapper").animate({'marginLeft':0},0,function(){

                $(".fixed-toggle-button.left").css("left",0+"px");
                $(".sidebar-toggle .navbar-default.sidebar").animate({opacity:0},200,function(){
                    $(".sidebar-toggle .navbar-default.sidebar").css("display","none");
                    $("#logo-direction").removeClass("fa-chevron-left");
                    $("#logo-direction").addClass("fa-chevron-right");

                    // if ($("#page-wrapper .table-responsive .table:not(.inside-modal)").length){

                    //     var paddingOnly = $("#page-wrapper").css("padding-left");
                    //     //var widthOnly = $("#page-wrapper").innerWidth() - (parseInt(paddingOnly.replace(/px/gi, '')) + 30);
                    //     var widthOnly = $("#page-wrapper .table-responsive .table:not(.inside-modal)").parents(".table-responsive").innerWidth()+16;
                    //     //alert (widthOnly);
                    //     $("#page-wrapper .table-responsive .table:not(.inside-modal)").css("width",(widthOnly+"px"));
                    // }

                    // datatable stuff
                    $(".dataTables_wrapper table").each(function(idx,el){
                        var tablenme = "#"+$(this).attr("id");
                        setTimeout(function(){
                            $(tablenme).DataTable().columns.adjust().draw();
                        }, 200);
                    });

                    //$(window).resize();
                }); //left:-toLeft}



            });
            $("#sidebar-toggle-button").data("mode","hidden");
            $.cookie('sidebar-toggle-button-data-mode', 'hidden', { expires: 30, path: "/" });
        }
        else {
            $("#page-wrapper").animate({'marginLeft':toLeft},0,function(){

                $(".fixed-toggle-button.left").css("left",toLeft+"px");
                $(".sidebar-toggle .navbar-default.sidebar").css("display","block");
                $(".sidebar-toggle .navbar-default.sidebar").animate({opacity:1},200,function(){
                    $("#logo-direction").removeClass("fa-chevron-right");
                    $("#logo-direction").addClass("fa-chevron-left");

                    // if ($("#page-wrapper .table-responsive .table:not(.inside-modal)").length){

                    //     var paddingOnly = $("#page-wrapper").css("padding-left");
                    //     //var widthOnly = $("#page-wrapper").innerWidth() - (parseInt(paddingOnly.replace(/px/gi, '')) + 30);
                    //     var widthOnly = $("#page-wrapper .table-responsive .table:not(.inside-modal)").parents(".table-responsive").innerWidth()-18;
                    //     //alert (widthOnly);
                    //     $("#page-wrapper .table-responsive .table:not(.inside-modal)").css("width",(widthOnly+"px"));
                    // }

                    // datatable stuff
                    $(".dataTables_wrapper table").each(function(idx,el){
                        var tablenme = "#"+$(this).attr("id");
                        setTimeout(function(){
                            $(tablenme).DataTable().columns.adjust().draw();
                        }, 200);
                    });

                    //$(window).resize();

                });



            });
            $("#sidebar-toggle-button").data("mode","showed");
            $.cookie('sidebar-toggle-button-data-mode',  null, { path: "/"});
            //setcookie("sidebar-toggle-button-data-mode", "", time()-3600, "/");
        }

        $(this).blur();

    });


    $("#sidebar-toggle-button-right").click(function(){
        if ($("#sidebar-toggle-button-right").data("mode")=="showed"){
            $("#page-wrapper").animate({'paddingRight':40},0,function(){
                $(".chat-sidebar").animate({opacity:0},200,function(){
                    $(".chat-sidebar").css("display","none");
                    $("#logo-direction2").removeClass("fa-chevron-right");
                    $("#logo-direction2").addClass("fa-chevron-left");
                    $(".fixed-toggle-button.right").css("right","0px");
                    $(".back-to-top").css("right","5px");
                    $(window).resize();
                }); //left:-toLeft}
            });
            $("#sidebar-toggle-button-right").data("mode","hidden");
            $.cookie('sidebar-right-toggle-button-data-mode', 'hidden', { expires: 30, path: "/" });
        }
        else {
            $("#page-wrapper").animate({'paddingRight':toRight},0,function(){
                $(".chat-sidebar").css("display","block");
                $(".chat-sidebar").animate({opacity:1},200,function(){
                    $(".fixed-toggle-button.right").css("right",(toRight-51) + "px");
                    $(".back-to-top").css("right", (toRight-45) + "px");
                    $("#logo-direction2").removeClass("fa-chevron-left");
                    $("#logo-direction2").addClass("fa-chevron-right");
                    $(window).resize();

                });

            });
            $("#sidebar-toggle-button-right").data("mode","showed");
            $.cookie('sidebar-right-toggle-button-data-mode',  null, { path: "/"});
            //setcookie("sidebar-right-toggle-button-data-mode", "", time()-3600, "/");
        }
        // if ($("#page-wrapper .table-responsive .table:not(.inside-modal)").length){

        //     var paddingOnly = $("#page-wrapper").css("padding-right");
        //     //var widthOnly = $("#page-wrapper").innerWidth() - (parseInt(paddingOnly.replace(/px/gi, '')) + 30);
        //     var widthOnly = $("#page-wrapper .table-responsive .table:not(.inside-modal)").parents(".table-responsive").innerWidth();
        //     $("#page-wrapper .table-responsive .table:not(.inside-modal)").css("width",(widthOnly+"px"));
        // }
        $(".dataTables_wrapper table").each(function(idx,el){
            var tablenme = "#"+$(this).attr("id");
            setTimeout(function(){
                $(tablenme).DataTable().columns.adjust().draw();
            }, 200);
        });
        $(this).blur();

    });

    $(window).resize(function() {
        clearInterval(clockTimerVar);
        waitForFinalEvent(function(){
        try{

        }catch (e){}
            if ($(this).width() < 800) { // 768
                calculate_popups();
                $("#page-wrapper").css("margin-left",0);
                $("#page-wrapper").css("padding-right",40);
                $(".fixed-toggle-button.right").css("right","0px");
                $(".back-to-top").css("right","5px");

                /*if ($("#scroller").css("margin-bottom")!='' && $("#scroller").css("margin-bottom")!='0px'){
                    $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight())+25);
                    //$(".navbar-toggle").click();
                    $("#topmost-navbar").removeClass("in");
                    $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight())+45);
                }*/

                //navbar-default sidebar
                //  z-index: 1200;
                //top: 50px;
                $(".navbar-default.sidebar").css("z-index","1200");
                $(".navbar-default.sidebar").css("top","50px");

                if ($.cookie('sidebar-toggle-button-data-mode')==null){
                    $(".sidebar-toggle .navbar-default.sidebar").css("display","block");
                    $(".sidebar-toggle .navbar-default.sidebar").css("opacity","1");
                    $("#logo-direction").removeClass("fa-chevron-right");
                    $("#logo-direction").addClass("fa-chevron-left");
                    $("#sidebar-toggle-button").data("mode","showed");
                    $.cookie('sidebar-toggle-button-data-mode',  null, { path: "/"});
                    //setcookie("sidebar-toggle-button-data-mode", "", time()-3600, "/");
                    //$.removeCookie('sidebar-toggle-button-data-mode');
                }


                if ($.cookie('sidebar-right-toggle-button-data-mode')==null){
                    $(".chat-sidebar").css("display","none");
                    $(".chat-sidebar").css("opacity","0");
                    $("#logo-direction2").removeClass("fa-chevron-left");
                    $("#logo-direction2").addClass("fa-chevron-right");
                    $("#sidebar-toggle-button-right").data("mode","showed");
                    $.cookie('sidebar-right-toggle-button-data-mode',  null, { path: "/"});
                    //setcookie("sidebar-right-toggle-button-data-mode", "", time()-3600, "/");
                    //$.removeCookie('sidebar-right-toggle-button-data-mode');
                }

                

          }
          else {

            /*if ($("#scroller").css("margin-bottom")!=''&& $("#scroller").css("margin-bottom")!='0px'){
                $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight()-5));
            }*/
            if ($.cookie('sidebar-toggle-button-data-mode')=='hidden'){
                $("#page-wrapper").animate({'marginLeft':0},0,function(){
                    $(".sidebar-toggle .navbar-default.sidebar").animate({opacity:0},200,function(){
                        $(".sidebar-toggle .navbar-default.sidebar").css("display","none");
                    }); //left:-toLeft}


                });
            }
            else{
                $("#page-wrapper").css("margin-left",currentmargin);

            }

            $(".navbar-default.sidebar").css("z-index","");
            $(".navbar-default.sidebar").css("top","");


            if ($.cookie('sidebar-right-toggle-button-data-mode')=='hidden' ||
            $.cookie('sidebar-right-toggle-button-data-mode')==null){

                $("#page-wrapper").animate({'paddingRight':40},0,function(){
                    $(".chat-sidebar").animate({opacity:0},200,function(){
                        $(".chat-sidebar").css("display","none");
                        $("#logo-direction2").removeClass("fa-chevron-right");
                        $("#logo-direction2").addClass("fa-chevron-left");
                        $(".fixed-toggle-button.right").css("right","0px");
                        $(".back-to-top").css("right","5px");

                    }); //left:-toLeft}
                });

            }
            else {
                $("#page-wrapper").animate({'paddingRight':toRight},0,function(){
                    $(".chat-sidebar").css("display","block");
                    $(".chat-sidebar").animate({opacity:1},200,function(){
                        $(".fixed-toggle-button.right").css("right",(toRight-51) + "px");
                        $(".back-to-top").css("right", (toRight-45) + "px");
                        $("#logo-direction2").removeClass("fa-chevron-left");
                        $("#logo-direction2").addClass("fa-chevron-right");


                    });

                });
            }



        }

            var timecont = document.getElementById('time-container');
            var datecont = document.getElementById('date-container');
            repaint(timecont);repaint(datecont);
          //clockTimerVar = setInterval(function(){clockStart();}, 1000);


          if ($("#scroller").css("display")!="none"){

            if ($("#scroller").attr("data-state")=="1"){
                $(scroller).stop();
                $(scroller).unbind("marquee");
                startScrolling(scroller, scrolling_velocity, scrolling_from);

            }

          }


        //   $(".dataTables_wrapper table").each(function(idx,el){
        //     var tablenme = "#"+$(this).attr("id");
        //     setTimeout(function(){
        //         $(tablenme).DataTable().columns.adjust().draw();
        //     }, 200);
        // });



        }, 200, "waitilldown");




    });


        //Check to see if the window is top if not then display button
        $(window).scroll(function(){
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').fadeIn();
            } else {
                $('.back-to-top').fadeOut();
            }
        });



    if ($.cookie('sidebar-toggle-button-data-mode')=='hidden'){
        $("#sidebar-toggle-button").click();
        if ($(window).width()<800){ // 768
            $("#page-wrapper").css("margin-left",0);
            $(".sidebar-toggle .navbar-default.sidebar").css("display","block");
            $(".sidebar-toggle .navbar-default.sidebar").css("opacity","1");
        }
    }

    if ($.cookie('sidebar-right-toggle-button-data-mode')=='hidden'||
    $.cookie('sidebar-right-toggle-button-data-mode')==null){
        $("#sidebar-toggle-button-right").click();
        if ($(window).width()<800){ // 768
            $("#page-wrapper").css("margin-right",0);
            $("#page-wrapper").css("padding-right",40);
            $(".fixed-toggle-button.right").css("right","0px");
            $(".back-to-top").css("right","5px");
        }
    }


    $('.back-to-top').click(function(event) {
        $(this).blur();
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, duration);
        return false;
    });


    $('#closespecial').live("click",function(){
        readAnnouncement();


    });

    /*if ($("#scroller").attr("data-state")=="0"){
        $('#closespecial').click();
    }*/




    /*$(".navbar-toggle").click(function(){
        $(".sidebar-toggle .navbar-default.sidebar").css("display","block");
        $(".sidebar-toggle .navbar-default.sidebar").css("opacity","1");
        if ($("#scroller").css("margin-bottom")=='' || $("#scroller").css("margin-bottom")=='80px'){
            $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight())+45);

        }
        //$('#page-wrapper').css('margin-top',0);

    });*/

    $("#loginbanner").mouseover(function(){
        //$(".hidable").show("slow");
        if ($(window).width() > 800) { // 768
        $(".hidable").each(function(){
            $(this).show("slow");
        });
        }
    });
    $("#loginbanner").mouseleave(function(){
        if ($(window).width() > 800) { // 768
        $(".hidable").each(function(){
            $(this).hide("slow");
        });
        }
    });


    // PREVIEW PICTURES
    $(".preview_images").live("click",function(){
        $("#picture-modal-wrapper").load(ABSOLUTE_PATH+"includes/PictureModal.php?picturelink="+encodeURIComponent($(this).attr("src")),function(){
            $("#picture-modal-wrapper").css("display","");
            $("#PictureModal").modal("show");
        }); // $("#picture-modal-wrapper").load()
        return false;
    }); // $(".preview_images").on("click",function(){
    $(".close_picture_modal").live("click",function(){
        $("#PictureModal").modal("hide");

    });

    $('#PictureModal').live('shown.bs.modal', function (e) {
        topOffset = 200;
        height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
        height = height - topOffset;

        $(this).find('.modal-body').each(function(idx,el){$(this).css('max-height',height+'px')});
    }); // $('#MasterfileAddEditModal').live('hidden.bs.modal', function (e) {

    $("#PictureModal").live("hidden.bs.modal",function(e){
       $("#picture-modal-wrapper").html("");
    });
    // END - PREVIEW PICTURES

    //jqUpdateSize();    // When the page first loads

    // show blocker upon click of menu
    $("ul#side-menu li.actual_link a,ul.dropdown-menu.dropdown-user li a,div.popup-head-left a,h4 a,li#announcement-tag a,a.navbar-brand,li.notification-item div.notification-message a,li.notification-item div.time a,div.notification-area-item-content a,span.message a,a.loadingbuttons,a.announcement-links,a.toblock").live("click",function(){
        if ($(this).attr("href")!="#") {

            if ($(this).attr("data-islogout")=="1") {
                $("#loading-message").html("<b>Logging out...</b>");
            } // if ($(this).attr("data-islogout")=="1") {
            else {
                $("#loading-message").html("<b>Loading Page...</b>");
            } // ELSE ng if ($(this).attr("data-islogout")=="1") {


            var link = $(this).attr("href");
            $("#blocker").fadeIn("fast",function(){
                location.href=link;
            });
        } // if ($(this).attr("href")!="#") {

        return false;
    }); // $("ul#side-menu li.actual_link a").live("click",function(){

    // load menu here


});

function waitForNewAnnouncement(){
    /* check for new announcements every minute */
    /*

    var announcementFirstTime = true;
    var last_announcement_code = 0;

    COMMENTED FOR NEW APPROACH .. JUST MAKE IT LIKE A REQUEST
    setInterval(function(){
        //$(scroller).stop();
        //$(scroller).unbind("marquee");
        resetCalls();
        // retrieve new announcements every minute
        $("#announcement-container").load(curDir+"includes/body_announcements.php","curdir=../",function(){

            var reviewCount = 0;
            $(".scrollingtext .review").each(function(idx,el){
                reviewCount ++;
            });
            if (reviewCount>0){
                scroller = $('.scrollingtext');
                startScrolling(scroller, scrolling_velocity, scrolling_from);
                $('body').css("padding-bottom","");
                $('.navbar-default.sidebar').css("padding-bottom","");
                $('.chat-sidebar').css("padding-bottom","");
        }

        });
    }, 60000);
    */
    // if ($("#announcement-container").length){
    //     var t;
    //     var user_code = $("#user_code").val();
    //     announcementRequests = $.ajax({
    //         url: curDir+"api/ReceiveAnnouncements.php",
    //         type: "post",
    //         cache : false,
    //         data:"last_announcement_code="+last_announcement_code+"&first_time="+announcementFirstTime+"&user_code="+user_code+"&navigationType=system",
    //         beforeSend:function(jqXHR,settings){
    //             /* clear notification popup just to be safe */

    //         },
    //         error: function(xhr, textStatus, errorThrown) {
    //             if (textStatus=="abort"){

    //             }
    //             else {

    //                 clearInterval(t);
    //                 t = setTimeout(function(){ waitForNewAnnouncement(); }, 10);
    //             }

    //         }
    //     }).done(function(response, textStatus, jqXHR){
    //         //console.log (JSON.stringify(response) + "\n" + receiver_type_word+"-"+receiver_code+"="+sender);
    //         if (textStatus == 'success'){
    //             //alert(response);
    //             if (!response.length) {
    //                 last_announcement_code = 0;
    //                 announcementFirstTime = false;

    //                 waitForNewAnnouncement();

    //             }
    //             else if (response[0].result=="0"){
    //                 //console.log("last_id=" + response[0].last_id);
    //                 if (response[0].last_announcement_code==""){
    //                     last_announcement_code = 0;
    //                     announcementFirstTime = false;
    //                     waitForNewAnnouncement();
    //                 }
    //                 else {
    //                     last_announcement_code= response[0].last_announcement_code;
    //                     announcementFirstTime = false;
    //                     waitForNewAnnouncement();
    //                 }

    //             }
    //             else if (response[0].result=="1"){

    //                 var element = "";
    //                 var lastid = 0;
    //                 var count = 0;
    //                 var code = "";

    //                 resetCalls();

    //                 for (var ctr=0;ctr<response.length;ctr++) {

    //                     if (parseInt(lastid)<parseInt(response[ctr].last_announcement_code)){
    //                         lastid = response[ctr].last_announcement_code;
    //                     }

    //                     code = response[ctr].code;
    //                     //alert ('pasok naman?');


    //                     count++;

    //                     element = element + '<span class="review" data-code="'+response[ctr].code+'">';
    //                     element = element + '<span class="message"><a href="'+ABSOLUTE_PATH+'announcements/'+response[ctr].slug_link+'">';
    //                     element = element + response[ctr].title;
    //                     element = element + '</a></span></span>';


    //                 } // for (var ctr=0;ctr<response.length;ctr++) {



    //                 var reviewCount = 0;
    //                 $(".scrollingtext .review").each(function(idx,el){
    //                     reviewCount ++;
    //                 });


    //                 if (reviewCount>0){
    //                     resetCalls();
    //                     $('.scrollingtext').prepend(element);
    //                     reviewCount = 0;
    //                     $(".scrollingtext .review").each(function(idx,el){
    //                         reviewCount ++;
    //                     });
    //                     if(reviewCount>3){
    //                         for (var c=0;c<(reviewCount-3);c++){
    //                             $('.scrollingtext .review').last().remove();
    //                         }
    //                     }

    //                     scroller = $('.scrollingtext');
    //                     startScrolling(scroller, scrolling_velocity, scrolling_from);

    //                 }
    //                 else {


    //                     element = '<div class="scrollingtext">'+element+'</div>';
    //                     $("#scroller #static-text").after(element);
    //                     $("#scroller").animate({'marginBottom':0},0,function(){
    //                             var currentMargin2 = $("#scroller").innerHeight();

    //                             $('body').css("padding-bottom",currentMargin2+"px");
    //                             $("#announcement-container").css("display","");
    //                             $("#scroller").attr("data-state","1");
    //                             $("#scroller").css("margin-bottom","");
    //                             $('.navbar-default.sidebar').css("padding-bottom",currentMargin2+"px");
    //                             $('.chat-sidebar').css("padding-bottom",currentMargin2+"px");
    //                             $("#closespecialdiv").show();

    //                             scroller = $('.scrollingtext');
    //                             resetCalls();
    //                             startScrolling(scroller, scrolling_velocity, scrolling_from);

    //                     });
    //                 }



    //                 last_announcement_code = lastid;
    //                 announcementFirstTime = false;
    //                 waitForNewAnnouncement();
    //             }


    //         }
    //         else if (textStatus == 'abort') {
    //             /* do nothing */

    //         }
    //         else {
    //             /* give breathing time before trying again */
    //             clearInterval(t);
    //             t = setTimeout(function(){ waitForNewAnnouncement(); }, 10);

    //         }


    //     }).fail(function (jqXHR, textStatus, errorThrown){
    //         /* give breathing time before trying again */
    //         if (textStatus == 'abort'){

    //         }
    //         else {
    //             clearInterval(t);
    //             t = setTimeout(function(){ waitForNewAnnouncement(); }, 10);
    //         }


    //     });
    // }
}

function readAnnouncement(){
    $.ajax({
        url: curDir+"api/ReadAnnouncement.php",
        type: "post",
        data:"announcement_code="+$(".review").data("code"),
        beforeSend:function(jqXHR,settings){

        }
    }).done(function(response, textStatus, jqXHR){

        if (textStatus == 'success'){
            if(response[0].result=="1") {
                var currentMargin2 = $("#scroller").innerHeight(); //$("#scroller").outerHeight();
                $("#scroller").animate({'marginBottom':-parseInt($("#scroller").innerHeight()+3)},0,function(){
                    /*
                    $("#closespecial").hide();
                    $(scroller).stop();
                    $(scroller).unbind("marquee");
                    $("#scroller").attr("data-state","0");
                    if ($(this).width() < 768) {
                        $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight())+25);
                    }
                    else{
                        $("#scroller").css("margin-bottom",parseInt($("#scroller").innerHeight())-7);
                    }


                    var newPageWrap = parseInt($("#page-wrapper").css("min-height").replace(/px/gi, ''));
                    $("#page-wrapper").css("min-height", (newPageWrap + currentMargin2) + "px");
                    $(".navbar-default.sidebar").css("height", (newPageWrap + currentMargin2) + "px");
                    var chatSidebarTop = parseInt($(".chat-sidebar").css("top").replace(/px/gi, ''));
                    $(".chat-sidebar").css("height", (newPageWrap + currentMargin2) + "px");
                    $(".chat-sidebar").css("top", (chatSidebarTop - currentMargin2) + "px");
                    */

                    $("#closespecialdiv").hide();
                    $(scroller).stop();
                    $(scroller).unbind("marquee");
                    $("#scroller").attr("data-state","0");
                    $("#scroller .scrollingtext").remove();
                    $('body').css("padding-bottom","");
                    $('.navbar-default.sidebar').css("padding-bottom","");
                    $('.chat-sidebar').css("padding-bottom","");
                    $("#announcement-container").css("display","none");


                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown){

    });
}

function resetCalls(){
    $(scroller).stop();
    $(scroller).finish();
    $(scroller).unbind("marquee");

}
function clockStart(){
    /*now=new Date();
    hour=now.getHours();
    min=now.getMinutes();
    sec=now.getSeconds();
    if (min<=9) { min="0"+min; }
    if (sec<=9) { sec="0"+sec; }
    if (hour>12) { hour=hour-12; add="pm"; }
    else { hour=hour; add="am"; }
    if (hour==12) { add="pm"; }
    time = ((hour<=9) ? "0"+hour : hour) + ":" + min + ":" + sec + " " + add;

    day = now.getDate();//(day[now.getDay()]);
    month2 = (month[now.getMonth()]);
    year = now.getFullYear();
    dateStr= month2 + " " + day + ", " + year;


    if (document.getElementById) {
        $("#time-container").html(time);
        $("#date-container").html(dateStr);
    }*/

    /* get from server date / time */
    /*  var firstTimeOnline = true;
        var timeStamp = ""; */
    // if ($("#datetimemain").length){
    //     serverDateTimeRequest = $.ajax({
    //         url: curDir+"api/ServerDateTime.php",
    //         type: "post",
    //         data:"first_time="+firstTimeOnline+"&timestamp="+timeStamp+"&action=get-datetime",
    //         beforeSend:function(jqXHR,settings){

    //         },
    //         error: function(xhr, textStatus, errorThrown) {
    //             if (textStatus=="abort"){

    //             }

    //         }
    //     }).done(function(response, textStatus, jqXHR){

    //         if (textStatus == "abort"){

    //         }
    //         else if (textStatus == 'success'){

    //             /* response[0].send_time */
    //             if (document.getElementById) {
    //                 $("#time-container").html(response[0].time);
    //                 $("#date-container").html(response[0].date);
    //             }
    //             timeStamp = response[0].timestamp;
    //             firstTimeOnline = false;
    //             clockStart();

    //         }

    //     }).fail(function (jqXHR, textStatus, errorThrown){
    //         firstTimeOnline = false;

    //         if (textStatus=="abort"){

    //         }
    //         else {
    //             clockStart();
    //         }

    //     });
    // }


}



function startScrolling(scroller_obj, velocity, start_from) {
    if ($("#scroller").attr("data-state")=="1"){

        scroller_obj.bind('marquee', function (event, c) {
            var ob = $(this);
            var sw = parseInt(ob.parent().width());
            var tw = parseInt(ob.width());
            tw = tw - 10;
            var tl = parseInt(ob.position().left);
            var v = velocity > 0 && velocity < 100 ? (100 - velocity) * 1000 : 5000;
            var dr = (v * tw / sw) + v;
            switch (start_from) {
                case 'right':
                    if (typeof c == 'undefined') {
                        ob.css({
                            left: (sw - 10)
                        });
                        sw = -tw;
                    } else {
                        sw = tl - (tw + sw);
                    };
                    break;
                default:
                    if (typeof c == 'undefined') {
                        ob.css({
                            left: -tw
                        });
                    } else {
                        sw += tl + tw;
                    };
            }
            ob.animate({
                left: sw
            }, {
                duration: dr,
                easing: 'linear',
                complete: function () {
                    ob.triggerHandler('marquee');
                },
                step: function () {
                    if (start_from == 'right') {
                        if (parseInt(ob.position().left) < -parseInt(ob.width())) {
                            ob.stop();
                            ob.triggerHandler('marquee');
                        };
                    } else {
                        if (parseInt(ob.position().left) > parseInt(ob.parent().width())) {
                            ob.stop();
                            ob.triggerHandler('marquee');
                        };
                    };
                }
            });
        }).triggerHandler('marquee');
        scroller_obj.mouseover(function () {
            $(this).stop();
        });
        scroller_obj.mouseout(function () {
            $(this).triggerHandler('marquee', ['resume']);
        });
    } else {
        $(scroller).stop();
        $(scroller).unbind("marquee");
    }

}



function repaint(element){

    if (!element) { return; }

    var n = document.createTextNode(' ');
    var disp = element.style.display;  // don't worry about previous display style
    var bef = element.innerHTML;
    element.appendChild(n);
    //element.style.display = 'none';

    setTimeout(function(){
            n.parentNode.removeChild(n);

    },50); // you can play with this timeout to make it as short as possible
}

var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

function serializeData(form){
    var checkboxes = $(form).find('input[type="checkbox"]');
    $.each( checkboxes, function( key, value ) {
        if (value.checked === false) {
            value.value = '';
            value.disabled = true;
        } else {
            value.value = $(value).data("origvalue");
        }
        $(value).attr('type', 'hidden');
    });
    var radios = $(form).find('input[type="radio"]');
    $.each( radios, function( key, value ) {
        if (value.checked === false) {
            value.value = '';
            value.disabled = true;
        } else {
            value.value = $(value).data("origvalue");
        }
        $(value).attr('type', 'hidden');
    });
    var dt = $(form).serialize();
    $.each( checkboxes, function( key, value ) {
        value.value = $(value).data("origvalue");
        value.disabled = false;
        $(value).attr('type', 'checkbox');
    });
    $.each( radios, function( key, value ) {
        value.disabled = false;
        value.value = $(value).data("origvalue");
        $(value).attr('type', 'radio');
    });
    return dt;

}

function stopRequests(){
    // notificationRequests.abort();
    // serverDateTimeRequest.abort();
    //for (ctr=0;ctr<waitVar.length;ctr++){
        //waitVar[$("#chat_sender_code").val()].abort();
    //}
    //waitVar[sender]
}

function parseNum(n){
    return isNum(parseFloat(removeCommas(n))) ? parseFloat(removeCommas(n)) : 0;
}

function isNum(n){
    return (isNaN(n) || !isFinite(n) || n==null ? false : true);
}

function removeCommas(str) {
    if (typeof str == "undefined"){
        return '';
    }
    else {
        return(str.replace(/,/g,''));
    }

}

function ShortcutKeysHandler() {

    this.printShortcut = function(e) {

      // F2
      if (e.keyCode == 113) {
        //if ($(".shortcut_print") != null && !$(".shortcut_print").attr("disabled")) {
        $("#loading-message").html("<b>Loading Modal...</b>");
        $("#blocker").fadeIn("fast",function(){
            $(".modal-backdrop").remove();
    /*
            $("#loading-message").html("<b>Initializing Print Window...</b>");
            $("#blocker").fadeIn("fast",function(){
                $(".shortcut_print").trigger("click");
            });
    */
            $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/insurance/ModalPrintOption.php",
                function(){
                    $("#modal-wrapper").css("display","");
                    $("#InterimMainModal").modal("show");
                    $("#blocker").fadeOut("fast");
              }); // $("#modal-wrapper").load()
        });//$("#blocker").fadeIn("fast",function(){
      }

      else  if (e.keyCode == 119) {
        //if ($(".shortcut_print") != null && !$(".shortcut_print").attr("disabled")) {
        $("#loading-message").html("<b>Loading Modal...</b>");
        $("#blocker").fadeIn("fast",function(){
            $(".modal-backdrop").remove();
    /*
            $("#loading-message").html("<b>Initializing Print Window...</b>");
            $("#blocker").fadeIn("fast",function(){
                $(".shortcut_print").trigger("click");
            });
    */
            $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/insurance/ModalPrintOptionRevised.php",
                function(){
                    $("#modal-wrapper").css("display","");
                    $("#InterimMainModal").modal("show");
                    $("#blocker").fadeOut("fast");
              }); // $("#modal-wrapper").load()
        });//$("#blocker").fadeIn("fast",function(){
      }

      else if (e.keyCode == 115){
        $("#loading-message").html("<b>Loading Modal...</b>");
        $("#blocker").fadeIn("fast",function(){
            $(".modal-backdrop").remove();
            $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/transactions/insurance/ModalPremiumCalculator.php",
                function(){
                    $("#modal-wrapper").css("display","");
                    $("#PremiumCalulatorModal").modal("show");
                    $("#blocker").fadeOut("fast");
                    gch_CurrentMode = "prem_calculator"

              }); // $("#modal-wrapper").load()
        });
      } else if (e.keyCode == 118) {
        $("#loading-message").html("<b>Loading Modal...</b>");

        $("#blocker").fadeIn("fast",function(){
            $(".modal-backdrop").remove();
            $("#modal-wrapper").load(ABSOLUTE_PATH+"includes/reports/ModalPrintReport.php",
                function(){
                    $("#modal-wrapper").css("display","");
                    $("#ReportMainModal").modal("show");
                    $("#blocker").fadeOut("fast");
              }); // $("#modal-wrapper").load()
        });
      }

  // $(".txtareatab").on('keydown', function(e) {
  //     var keyCode = e.keyCode || e.which;

  //     if (keyCode == 9) {
  //       e.preventDefault();
  //       // e.element().insert("\t");
  //       // call custom function here

  //     }
  //   });

    };

}



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



function custom_rounding (lde_inputnum, lin_precision) {

    if (typeof lin_precision === 'undefined') {
        lin_precision = 2;
    } // if (typeof lin_precision === 'undefined') { 

    var lde_newvalue = 0;
    var lch_newvaluestring = "";
    var lch_stringconvertedinput = lde_inputnum + "";
    var larr_splitdecimalwhole = lch_stringconvertedinput.split(".");

    lch_newvaluestring = larr_splitdecimalwhole[0];

    if (lin_precision>0) {
        lch_newvaluestring = lch_newvaluestring + ".";
    } // if ($lin_precision>0) {
    else {

        // whole number rounding
        larr_DecimalList = str_split(larr_splitdecimalwhole[1],1);
        lch_newvaluestring = custom_rounding_case_to_case_process (larr_splitdecimalwhole[0],larr_DecimalList,-1,0);

    } // ELSE ng if ($lin_precision>0) {


    if (larr_splitdecimalwhole.length>1) {

        larr_DecimalList = str_split(larr_splitdecimalwhole[1],1);

        // these variables are created to avoid confusion in terminologies with the logic part;
        lin_actualdecimalplacesindex = lin_precision - 1;
        lin_firstfiguredroppedindex = lin_precision ;

        // pag mas konti yung decimal place now kesa sa precision na hinihingi, pad with zeroes

        if (larr_DecimalList.length<lin_precision) {

            lch_newvaluestring = lch_newvaluestring + "" + larr_splitdecimalwhole[1];
            lch_newvaluestring = str_pad(lch_newvaluestring,lch_newvaluestring.length + (lin_precision - larr_DecimalList.length),"0",'STR_PAD_RIGHT');

        } // if (count($larr_DecimalList)<$lin_precision) {
        else if (larr_DecimalList.length==lin_precision) {

            lch_newvaluestring = lch_newvaluestring + "" + larr_splitdecimalwhole[1];

        } // else if (count($larr_DecimalList)==$lin_precision) {
        else {

            // actual case to case
            lch_newvaluestring = custom_rounding_case_to_case_process (larr_splitdecimalwhole[0],larr_DecimalList,lin_actualdecimalplacesindex,lin_firstfiguredroppedindex);

        } // ELSE ng else if (count($larr_DecimalList)==$lin_precision) {

    } // if (count($larr_splitdecimalwhole)>1) {
    else {

        lch_newvaluestring = str_pad(lch_newvaluestring,lch_newvaluestring.length + lin_precision,"0",'STR_PAD_RIGHT');

    } // ELSE ng if (count($larr_splitdecimalwhole)>1) {

    return lch_newvaluestring;

} // function custom_rounding (lde_inputnum, lin_precision) {

function str_split (string, splitLength) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/str_split/
  // original by: Martijn Wieringa
  // improved by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  //  revised by: Theriault (https://github.com/Theriault)
  //  revised by: Rafał Kukawski (http://blog.kukawski.pl)
  //    input by: Bjorn Roesbeke (http://www.bjornroesbeke.be/)
  //   example 1: str_split('Hello Friend', 3)
  //   returns 1: ['Hel', 'lo ', 'Fri', 'end']

  if (splitLength === null) {
    splitLength = 1
  } // if (splitLength === null) {
  if (string === null || splitLength < 1) {
    return false
  } // if (string === null || splitLength < 1) {

  string += ''
  var chunks = []
  var pos = 0
  var len = string.length

  while (pos < len) {
    chunks.push(string.slice(pos, pos += splitLength))
  } // while (pos < len) {

  return chunks
} // function str_split (string, splitLength) {

function str_pad (input, padLength, padString, padType) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/str_pad/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Michael White (http://getsprink.com)
  //    input by: Marco van Oort
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT')
  //   returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
  //   example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH')
  //   returns 2: '------Kevin van Zonneveld-----'

  var half = ''
  var padToGo

  var _strPadRepeater = function (s, len) {
    var collect = ''

    while (collect.length < len) {
      collect += s
    }
    collect = collect.substr(0, len)

    return collect
  }

  input += ''
  padString = padString !== undefined ? padString : ' '

  if (padType !== 'STR_PAD_LEFT' && padType !== 'STR_PAD_RIGHT' && padType !== 'STR_PAD_BOTH') {
    padType = 'STR_PAD_RIGHT'
  }
  if ((padToGo = padLength - input.length) > 0) {
    if (padType === 'STR_PAD_LEFT') {
      input = _strPadRepeater(padString, padToGo) + input
    } else if (padType === 'STR_PAD_RIGHT') {
      input = input + _strPadRepeater(padString, padToGo)
    } else if (padType === 'STR_PAD_BOTH') {
      half = _strPadRepeater(padString, Math.ceil(padToGo / 2))
      input = half + input + half
      input = input.substr(0, padLength)
    }
  }

  return input
} // function str_pad (input, padLength, padString, padType) {

function custom_rounding_case_to_case_process (lch_wholenumber, larr_DecimalList, lin_actualdecimalplacesindex , lin_firstfiguredroppedindex ) {

    var lch_newvaluestring = "";

    // if negative = whole number
    if (lin_actualdecimalplacesindex==-1) {

        // validate case A
        if (parseInt(larr_DecimalList[lin_firstfiguredroppedindex])<5) {
            lch_newvaluestring = lch_wholenumber;
        } // if (parseInt($larr_DecimalList[$lin_firstfiguredroppedindex])<5) {
        // case B
        else if (parseInt(larr_DecimalList[lin_firstfiguredroppedindex])>5) {
            lch_newvaluestring = (parseInt(lch_wholenumber) + 1);
        } // else if (parseInt($larr_DecimalList[$lin_firstfiguredroppedindex])>5) {
        // case C, D , E
        else {

            // COMMENTED CASE C-D-E FIRST KASI IT LOOKS WRONG! -- 
            // // check first the values next to the '5' first figure kept
            // // if may ibang number aside from 0 -> auto increase na agad
            // // if puro zero or wala kasunod -> check if odd/even yung last figure
            // // if last figure odd -> increase last figure
            // // if last figure even -> dont change lst figure
            // var llo_allzeroesornosuccedingdigits = true;

            // // whole number
            // larr_WholeNumberList = str_split(lch_wholenumber,1);

            // lde_oddevencase = parseFloat(larr_WholeNumberList[larr_WholeNumberList.length-1]) % 2;
            // //echo larr_WholeNumberList[count(larr_WholeNumberList)-1] + "" + "--";

            // // loop the decimal list -- starting from the figures after 5
            // for (var ctr=(lin_firstfiguredroppedindex+1);ctr<larr_DecimalList.length;ctr++) {
            //  if (parseInt(larr_DecimalList[ctr])!=0) {
            //      llo_allzeroesornosuccedingdigits = false;
            //      break;
            //  } // if (parseInt($larr_DecimalList[$ctr])!=0) {
            // } // for ($ctr=($lin_firstfiguredroppedindex+1);$ctr<count($larr_DecimalList);$ctr++) {

            // // odd OR has succeeding digits
            // if (lde_oddevencase==1 || (!llo_allzeroesornosuccedingdigits)) {

            //     lch_newvaluestring = (parseInt(lch_wholenumber) + 1);


            // } // if ($lde_oddevencase==1) {
            // // even
            // else{
            //     lch_newvaluestring = lch_wholenumber;
            // } // ELSE Ng if ($lde_oddevencase==1) {
            lch_newvaluestring = (parseInt(lch_wholenumber) + 1);


        } // ELSE Ng else if (parseInt($larr_DecimalList[$lin_firstfiguredroppedindex])>5) {

    } // if ($lin_actualdecimalplacesindex==-1) {
    else {

        var lch_DecimalListJoined = larr_DecimalList.join("").substr(0,lin_firstfiguredroppedindex);

        // validate case A
        if (parseInt(larr_DecimalList[lin_firstfiguredroppedindex])<5) {
            //lch_newvaluestring = lch_wholenumber + "" + "." + "" + substr(implode("", larr_DecimalList),0,lin_firstfiguredroppedindex);
            lch_newvaluestring = lch_wholenumber + "." + larr_DecimalList.join("").substr(0,lin_firstfiguredroppedindex);
        } // if (parseInt($larr_DecimalList[$lin_firstfiguredroppedindex])<5) {

        // case B
        else if (parseInt(larr_DecimalList[lin_firstfiguredroppedindex])>5) {
            lin_newvalue = (parseInt(lch_DecimalListJoined) + 1);
            if (lin_newvalue.length>lin_firstfiguredroppedindex) {
                lch_newvaluestring = (parseInt(lch_wholenumber) + 1) + ".";
                lch_newvaluestring = str_pad(lch_newvaluestring,lch_newvaluestring.length + lin_firstfiguredroppedindex,"0",'STR_PAD_RIGHT');
            } // if (strlen($lin_newvalue)>$lin_firstfiguredroppedindex) {s
            else {
                lch_newvaluestring =lch_wholenumber + "." + lin_newvalue;
            } // ELSE Ng if (strlen($lin_newvalue)>$lin_firstfiguredroppedindex) {
        } // else if (parseInt($larr_DecimalList[$lin_firstfiguredroppedindex])>5) {

        // case C,D,E
        else {

            // check first the values next to the '5' first figure kept
            // if may ibang number aside from 0 -> auto increase na agad
            // if puro zero or wala kasunod -> check if odd/even yung last figure
            // if last figure odd -> increase last figure
            // if last figure even -> dont change lst figure
            llo_allzeroesornosuccedingdigits = true;

            lde_oddevencase = parseFloat(larr_DecimalList[lin_firstfiguredroppedindex-1]) % 2;

            //echo $lde_oddevencase;
            
            // loop the decimal list -- starting from the figures after 5
            for (var ctr=(lin_firstfiguredroppedindex+1);ctr<larr_DecimalList.length;ctr++) {
                if (parseInt(larr_DecimalList[ctr])!=0) {
                    llo_allzeroesornosuccedingdigits = false;
                    break;
                } // if (parseInt($larr_DecimalList[$ctr])!=0) {
            } // for (ctr=($lin_firstfiguredroppedindex+1);$ctr<count($larr_DecimalList);$ctr++) {


            // odd OR has succeeding digits
            if (lde_oddevencase==1 || (!llo_allzeroesornosuccedingdigits)) {

                lin_newvalue = (parseInt(lch_DecimalListJoined) + 1);
                if (lin_newvalue>lin_firstfiguredroppedindex) {
                    lch_newvaluestring = (parseInt(lch_wholenumber) + 1) + ".";
                    lch_newvaluestring = str_pad(lch_newvaluestring,lch_newvaluestring.length + lin_firstfiguredroppedindex,"0",'STR_PAD_RIGHT');
                } // if (strlen($lin_newvalue)>$lin_firstfiguredroppedindex) {s
                else {
                    lch_newvaluestring = lch_wholenumber + "." + lin_newvalue;
                } // ELSE Ng if (strlen($lin_newvalue)>$lin_firstfiguredroppedindex) {

            } // if ($lde_oddevencase==1) {
            // even
            else{
                lch_newvaluestring = lch_wholenumber + "." + lch_DecimalListJoined;
            } // ELSE Ng if ($lde_oddevencase==1) {



        } // ELSE ng  else if (parseInt(larr_DecimalList[lin_firstfiguredroppedindex])>5) {

    } // ELSE ng if (lin_actualdecimalplacesindex==-1) {

    return lch_newvaluestring;

} // function custom_rounding_case_to_case_process (lch_wholenumber, larr_DecimalList, lin_actualdecimalplacesindex , lin_firstfiguredroppedindex ) {

