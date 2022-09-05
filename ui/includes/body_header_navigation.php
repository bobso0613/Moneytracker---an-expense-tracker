
<?php if (isset($PAGE_SETTINGS["NavigationType"]) && $PAGE_SETTINGS["NavigationType"]==="corporate") { ?>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header" id="loginbanner">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand dropdown" href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>index" title="<?php echo META_DESCRIPTION;?>"  data-placement="bottom" data-toggle="tooltip">
                <img class="" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/headerlogo2.png<?php echo VERSION_AFFIX; ?>">
            </a>
        </div>

      </div>
    </nav>

<?php }
else { ?>
    <!-- Navigation -- SYSTEM -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header" id="loginbanner">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand dropdown" href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>index" title="<?php echo META_DESCRIPTION;?>"  data-placement="bottom" data-toggle="tooltip">
                <img class="" src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>resources/assets/headerlogo2.png<?php echo VERSION_AFFIX; ?>">
                </a>


        </div>
        
        <!-- /.navbar-header -->
        <input type="hidden" id="user_code" name="user_code" value="<?php echo $_SESSION["user_code"]; ?>"/>
        <ul class="nav navbar-top-links navbar-right">

            
            <?php
            $link = DB_LOCATION;
            $params = array (
                "action" => "retrieve-user",
                "fileToOpen" => "default_select_query",
                "tableName" => "mstuser",
                "dbconnect" => MONEYTRACKER_DB,
                "columns" => "username,code,user_image_code,profile_slug_link,nickname",
                "orderby" => "whole_name ASC",
                "conditions[equals][code]" => $_SESSION["user_code"],
                "conditions[equals][is_active]" => "1"
            );
            $result=processCurl($link,$params);
            $output = json_decode($result,true);
            ?>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <img src="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."profilepictures.php?thumbmode=true&id=".$output[0]["user_image_code"]."&id2=".$output[0]["code"] ?>" 
                        class=" img-circle main-profile-picture" 
                        style="vertical-align: middle"/></i> <span id="loggedin-username" ><?php echo "<strong>".$output[0]["nickname"] . "</strong> (" . $output[0]["username"] . ")" ;?></span> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."user_profiles/".$output[0]['profile_slug_link'] ?>"><i class="fa fa-user fa-fw"></i> User Profile</a>
                    </li>
                    <li><a href="#" role="button" id="authcoderequest" onclick="alert('Feature not yet available.')"><i class="fa fa-lock fa-fw"></i> Request Auth. Code</a>
                    </li>
                    
                    <li><a href="#" role="button" id="nightmodetoggle" data-state="0"><i class="fa fa-star-half-o fa-fw"></i> Toggle Night Mode</a>
                    </li>

                    <li><a href="#" role="button" id="changelog" onclick="alert('Under construction')"><i class="fa fa-list-ol fa-fw"></i> Changelog</a>
                    </li>

                    <li class="divider"></li>
                    <li><a href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]."settings"; ?>"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li><a onclick="stopRequests();" data-islogout="1" class="chat-cancel-buttons" href="<?php echo $PAGE_SETTINGS["CurrentDirectory"]?>logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->

            
        </ul>
    </nav>
<!-- /.navbar-top-links -->
<?php } ?>

        




        

