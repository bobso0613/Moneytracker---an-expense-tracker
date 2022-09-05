/* check logged out */
    //$.cookie('sidebar-right-toggle-button-data-mode')==null
    /*setInterval(function(){
        if ($.cookie('PHPSESSID')==null||$.cookie('PHPSESSID')==""){
            $.cookie('error_message', 'You have been logged out.', { expires: 30, path: "/" });
            location.href = curDir + "login";
        }
    },1000);*/
$(document).ready(function(){
	setInterval(function(){
        if ($.cookie('PHPSESSID')==null||$.cookie('PHPSESSID')==""){
            $.cookie('error_message', 'You have been logged out.', { expires: 30, path: "/" });
            location.href = curDir + "login";
        }
    },1000);
});