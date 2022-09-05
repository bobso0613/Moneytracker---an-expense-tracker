<?php

/*define("META_DESCRIPTION","An Extensive General Insurance System");
define("META_CHARSET","ISO-8859-1");
define("META_AUTHOR","Bob Elridge Franz L. So");
define("META_SYSTEMNAME","AEGIS-Web");
define("SYSTEM_TITLE","AEGIS-Web");
define("HTML_LANG","en");

$PAGE_SETTINGS["CssEnable"] = array();
$PAGE_SETTINGS["CssEnable"]["Timeline"] = false;
$PAGE_SETTINGS["CssEnable"]["Morris"] = true;
$PAGE_SETTINGS["CssEnable"]["Chat"] = true;
$PAGE_SETTINGS["CssEnable"]["DataTables"] = true;
$PAGE_SETTINGS["CssEnable"]["SocialButtons"] = true;

*/

?>
<!DOCTYPE html>
<html lang="<?php echo HTML_LANG ?>">

<head>

	<meta charset="<?php echo META_CHARSET ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="<?php echo META_SYSTEMNAME ?>">
    <meta name="description" content="<?php echo META_DESCRIPTION ?>">
    <meta name="author" content="<?php echo META_AUTHOR ?>">
    <meta name="keywords" content="<?php echo SEO_KEYWORDS ?>,<?php echo str_replace(' ',',',META_SYSTEMNAME) ?>,<?php echo str_replace(' ',',',META_AUTHOR) ?>,<?php echo str_replace(' ',',',META_DESCRIPTION) ?>">
    <?php if (isset($PAGE_SETTINGS["UseBaseLink"]) && $PAGE_SETTINGS["UseBaseLink"]==true){
    ?>
        <base href="<?php echo ABSOLUTE_PATH;?>">
    <?php } ?>

    <title><?php echo $PAGE_SETTINGS["PageTitle"]?> - <?php echo META_SYSTEMNAME ?></title>
	<link rel="shortcut icon" href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?><?php echo ICON_LINK ?>" />
	
    <script type="text/javascript">
       var curDir = '<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>';
       var VERSION_AFFIX = '<?php echo VERSION_AFFIX; ?>';
    </script>

	<!-- DEFAULT CSS -->
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/bootstrap.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php /*
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/jquery-te-1.4.0.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet"></link>
    */ ?>
    <!-- Custom CSS -->
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/sb-admin-2.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/font-awesome-4.5.0/css/font-awesome.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet" type="text/css">
    <!-- MetisMenu CSS -->
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/plugins/metisMenu/metisMenu.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">

    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["Timeline"]) && $PAGE_SETTINGS["CssEnable"]["Timeline"]===true) {?> 
	    <!-- Timeline CSS -->
	    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/plugins/timeline.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["Morris"]) && $PAGE_SETTINGS["CssEnable"]["Morris"]===true) {?> 
	    <!-- Morris Charts CSS -->
    	<link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/plugins/morris.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["DataTables"]) && $PAGE_SETTINGS["CssEnable"]["DataTables"]===true) {?> 
	    <!-- DataTables CSS -->
    	<link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/plugins/dataTables.bootstrap.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["SocialButtons"]) && $PAGE_SETTINGS["CssEnable"]["SocialButtons"]===true) {?> 
	    <!-- Social Buttons CSS -->
    	<link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/plugins/social-buttons.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>

    
    <!-- CUSTOM CSS -->
    <!-- Additional CSS -->
    
    
    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["DatePicker"]) && $PAGE_SETTINGS["CssEnable"]["DatePicker"]===true) {?> 
        <!-- Social Buttons CSS -->
        <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/datepicker.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>
    <?php if (isset($PAGE_SETTINGS["CssEnable"]) && isset($PAGE_SETTINGS["CssEnable"]["TypeAhead"]) && $PAGE_SETTINGS["CssEnable"]["TypeAhead"]===true) {?> 
        <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/typeahead.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    <?php } ?>
    <link href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/css/additionals.min.css<?php echo VERSION_AFFIX; ?>" rel="stylesheet">
    

    <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/jquery-2.0.3.min.js<?php echo VERSION_AFFIX; ?>"></script>
	

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>js/html5shiv.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>js/html5shiv-printshiv.js<?php echo VERSION_AFFIX; ?>"></script>
        <script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>js/respond.min.js<?php echo VERSION_AFFIX; ?>"></script>
    <![endif]-->

</head>