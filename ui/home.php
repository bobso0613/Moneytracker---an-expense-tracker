<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

header("HTTP/1.1 200 OK");
header("Location: ./login");
exit();

/* some codes here */
/*
$PAGE_SETTINGS = array();
$PAGE_SETTINGS["CurrentDirectory"] = "./";
$PAGE_SETTINGS["PageTitle"] = "Home";

require_once($PAGE_SETTINGS["CurrentDirectory"]."api/Engine.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/CurlAPI.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."api/SystemConstants.php");
$PAGE_SETTINGS["Engine"] = new Engine();

$PAGE_SETTINGS["CssEnable"] = array();
$PAGE_SETTINGS["CssEnable"]["Cover"] = true;
$PAGE_SETTINGS["NoNightMode"] = true;

$PAGE_SETTINGS["NavigationType"] = "corporate";

require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/header_meta.php");
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_top.php");

// ---------- MAIN PAGE BODY HERE -------------- 
  require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_header_navigation.php");
  echo '<div id="announcement-container">';
  require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_announcements.php");
    echo '</div>';
?>
     
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1><?php echo SYSTEM_TITLE ?></h1>
        <p>The best non-life insurance system tailored for Philippine insurance.</p>
        <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more other features &raquo;</a></p>
      </div>
    </div>

    <div class="container">
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-md-4">
              <h2>Announcement 1</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
            </div>
            <div class="col-md-4">
              <h2>Announcement 2</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
           </div>
            <div class="col-md-4">
              <h2>Announcement 3</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
              <h2>Announcement 4</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
            </div>
            <div class="col-md-4">
              <h2>Announcement 5</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
           </div>
            <div class="col-md-4">
              <h2>Announcement 6</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
              <p><a class="btn btn-default" href="#" role="button">Read more &raquo;</a></p>
            </div>
        </div>

      <hr>

        <footer>
            <p><span>&copy; 2014 <?php echo SYSTEM_TITLE ?></span>&nbsp;
                                <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>home" class="btn-link">Home</a>&nbsp;
                                <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>features" class="btn-link">Features</a>&nbsp;
                                <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>about" class="btn-link">About</a>&nbsp;
                                <a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>contact_us" class="btn-link">Contact</a>&nbsp;</p>
        </footer>
    </div> <!-- /container -->

<?php
// ---------- END - PAGE BODY HERE -------------- /
require_once($PAGE_SETTINGS["CurrentDirectory"]."includes/body_common_bottom.php");
?>


<?php // ------------ MAIN PAGE JAVASCRIPT HERE --------------- / ?>
<script type="text/javascript">
            
    $(document).ready(function(){


    });
</script>

<script src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/js/removeblocker.min.js"></script>
<?php // ------------ END - MAIN PAGE JAVASCRIPT HERE ---------------  ?>
</body>
</html>
*/
?>