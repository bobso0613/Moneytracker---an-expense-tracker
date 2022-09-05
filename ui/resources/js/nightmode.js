$(document).ready(function(){
	/* night mode */
    if ($.cookie('nightmode-state')=='1'){
        var url = curDir+'resources/css/nightmode.min.css'+VERSION_AFFIX;
        $('#nightmodelink').attr('href',url);
        $('#nightmodetoggle').data('state','1');
    }
    $('#nightmodetoggle').click(function(){
        if ($(this).data('state')=='0'){
        var url = curDir+'resources/css/nightmode.min.css'+VERSION_AFFIX;
        $('#nightmodelink').attr('href',url);
        $(this).data('state','1');
        $.cookie('nightmode-state', '1', { expires: 30, path: "/"});

        }
        else {
        $('#nightmodelink').attr('href','');
        $(this).data('state','0');
        $.cookie('nightmode-state',  null, { path: "/"});
        //$.removeCookie('nightmode-state');
        //setcookie("nightmode-state", "", time()-3600, "/");
        }
        return false;
    });
});