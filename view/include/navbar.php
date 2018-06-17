<?php
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/category.php';
$_GET['parentsOnly']="1";
$categories = Category::getAllCategories();
if (empty($_SESSION['language'])) {
    $lang = 'us';
} else {
    $lang = $_SESSION['language'];
}

$json_file = url_get_contents("{$global['webSiteRootURL']}plugin/CustomizeAdvanced/advancedCustom.json.php");
// convert the string to a json object
$advancedCustom = json_decode($json_file);
$thisScriptFile = pathinfo( $_SERVER["SCRIPT_FILENAME"]);
if(empty($advancedCustom->userMustBeLoggedIn) || User::isLogged()){
$updateFiles = getUpdatesFilesArray();
?>
<nav class="navbar navbar-default navbar-fixed-top first-page-navbar" style='background-color:#F9FBFD;'>
    <ul class="items-container">
        <li>
            <ul class="left-side">
                <li>
                    <!-- <button class="btn btn-default navbar-btn pull-left" id="buttonMenu" ><span class="fa fa-bars"></span></button> -->
                    <script>
                    /*
                        $('#buttonMenu').click(function (event) {
                            event.stopPropagation();
                            $('#sidebar').fadeToggle();
                        });
                        $(document).on("click", function () {
                            $("#sidebar").fadeOut();
                        });
                        $("#sidebar").on("click", function (event) {
                            event.stopPropagation();
                        });
                    */
                    </script>
                </li>
                <li>
                    <a class="navbar-brand" href="<?php echo $global['webSiteRootURL']; ?>" >
                        <img src="<?php echo $global['webSiteRootURL'], $config->getLogo(); ?>" alt="<?php echo $config->getWebSiteTitle(); ?>" class="img-responsive ">
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="navbar-header">
                <button type="button" class=" navbar-toggle btn btn-default navbar-btn" data-toggle="collapse" data-target="#myNavbar" style="padding: 6px 12px;">
                    <span class="fa fa-bars"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="right-menus">
                    <li class="">
                        <form class="navbar-form navbar-left" id="searchForm"  action="<?php echo $global['webSiteRootURL']; ?>" >
                            <div class="input-group" >
                                <div class="form-inline">
                                    <input class="form-control" type="text" value="<?php if(!empty($_GET['search'])) { echo $_GET['search']; } ?>" name="search" placeholder="<?php echo __("Search"); ?>" style='min-width:140px;'>
                                    <button class="input-group-addon form-control hidden-xs"  style="width: 30px;" type="submit"><span class="glyphicon glyphicon-search" style='font-size:12px;margin-left:-3px;'></span></button>
                                </div>
                            </div>
                        </form>
                    </li>
                    <?php
                    echo YouPHPTubePlugin::getHTMLMenuRight();
                    ?>
                    <?php
                    if (User::canUpload()) {
                        ?>
                        <li>

                            <div class="btn-group">
                                <button type="button" class="btn btn-default  dropdown-toggle navbar-btn pull-left btn-zero-border"  data-toggle="dropdown">
                                    <i class="<?php echo isset($advancedCustom->uploadButtonDropdownIcon)?$advancedCustom->uploadButtonDropdownIcon:"fas fa-video"; ?>"></i> <?php echo !empty($advancedCustom->uploadButtonDropdownText)?$advancedCustom->uploadButtonDropdownText:""; ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu" style="">
                                    <?php
                                    if (!empty($advancedCustom->encoderNetwork)) {
                                        ?>
                                            <li>
                                                <a href="<?php echo $advancedCustom->encoderNetwork, "?webSiteRootURL=", urlencode($global['webSiteRootURL']), "&user=", urlencode(User::getUserName()), "&pass=", urlencode(User::getUserPass()); ?>" target="encoder" >
                                                    <span class="fa fa-cogs"></span> <?php echo __("Encoder Network"); ?>
                                                </a>
                                            </li>
                                        <?php
                                    }
                                    if (empty($advancedCustom->doNotShowEncoderButton)) {
                                        if (!empty($config->getEncoderURL())) {
                                            ?>
                                            <li>
                                                <a href="<?php echo $config->getEncoderURL(), "?webSiteRootURL=", urlencode($global['webSiteRootURL']), "&user=", urlencode(User::getUserName()), "&pass=", urlencode(User::getUserPass()); ?>" target="encoder" >
                                                    <span class="fa fa-cog"></span> <?php echo __("Encode video and audio"); ?>
                                                </a>
                                            </li>
                                            <?php
                                        } else {
                                            ?>
                                            <li>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>siteConfigurations" ><span class="fa fa-cogs"></span> <?php echo __("Configure an Encoder URL"); ?></a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    if (empty($advancedCustom->doNotShowUploadMP4Button)) {
                                        ?>
                                        <li>
                                            <a  href="<?php echo $global['webSiteRootURL']; ?>upload" >
                                                <span class="fa fa-upload"></span> <?php echo __("Direct upload"); ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    if (empty($advancedCustom->doNotShowImportLocalVideosButton)) {
                                        ?>
                                        <li>
                                            <a  href="<?php echo $global['webSiteRootURL']; ?>view/import.php" >
                                                <span class="fas fa-hdd"></span> <?php echo __("Direct Import Local Videos"); ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    if (empty($advancedCustom->doNotShowEmbedButton)) {
                                        ?>                                    
                                        <li>
                                            <a  href="<?php echo $global['webSiteRootURL']; ?>mvideos?link=1" >
                                                <span class="fa fa-link"></span> <?php echo __("Embed a video link"); ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>

                        </li>
                        <?php
                    }
                    ?>
                    <li>
                        <?php
                        $flags = getEnabledLangs();
                        $objFlag = new stdClass();
                        foreach ($flags as $key => $value) {
                            //$value = strtoupper($value);
                            $objFlag->$value = $value;
                        }
                        if ($lang == 'en') {
                            $lang = 'us';
                        }
                        ?>
                        <style>
                            #navBarFlag .dropdown-menu {
                                min-width: 20px;
                            }
                            .btn-gris{ background-color: #F9FBFD; }
                        </style>
                        <div id="navBarFlag" data-input-name="country" data-selected-country="<?php echo $lang; ?>"></div>
                        <script>
                            $(function () {
                                $("#navBarFlag").flagStrap({
                                    countries: <?php echo json_encode($objFlag); ?>,
                                    inputName: 'country',
                                    buttonType: "btn-default navbar-btn btn-gris",
                                    onSelect: function (value, element) {
                                        window.location.href = "<?php echo $global['webSiteRootURL']; ?>?lang=" + value;
                                    },
                                    placeholder: {
                                        value: "",
                                        text: ""
                                    }
                                });
                            });
                        </script>
                    </li>
                    
                <!-- LOGGED USER -->
                    <?php if (User::isLogged()) { ?>
                        <li class="dropdown user" style='margin-right:40px;margin-left:30px;'>
                          <a aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown" href="#" style='position:absolute;top:13px;width:70px;color:#333;'>
                            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                          </a>
                          <ul class="dropdown-menu dropdown-user" style='left:-160px;width:200px;padding:0px 0px;'>
                            <li style="min-height: 60px;">
                                <div class="pull-left" style="margin-left: 10px;">
                                    <img src="<?php echo User::getPhoto(); ?>" style="max-width: 55px;border:0px solid #fff;"  class="img img-thumbnail img-responsive img-circle"/>
                                </div>                        
                                <div>
                                    <h2><?php echo User::getName(); ?></h2>
                                    <div><small><?php echo User::getMail(); ?></small></div>
                                </div>
                            </li>
                            
                            <li>
                                <div>
                                    <a href="<?php echo $global['webSiteRootURL']; ?>user" class="btn btn-primary btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:15px;">
                                        <span class="fa fa-user-circle"></span>
                                        <?php echo __("My Account"); ?>
                                    </a>
                                </div>
                            </li>

                            <li>
                                <div>
                                    <a href="<?php echo User::getChannelLink(); ?>" class="btn btn-danger btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:15px;">
                                        <span class="fab fa-youtube"></span>
                                        <?php echo __("My Channel"); ?>
                                    </a>
                                </div>
                            </li>

                            <?php
                            if (User::canUpload()) {
                                ?>
                                <li>
                                    <div>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>mvideos" class="btn btn-success btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:15px;">
                                            <span class="glyphicon glyphicon-film"></span>
                                            <?php echo __("My videos"); ?>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>charts" class="btn btn-info btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:15px;">
                                            <span class="fas fa-tachometer-alt"></span>
                                            <?php echo __("Dashboard"); ?>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>subscribes" class="btn btn-warning btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:15px;">
                                            <span class="fa fa-check"></span>
                                            <?php echo __("Subscriptions"); ?>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>comments" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="fa fa-comment"></span>
                                            <?php echo __("Comments"); ?>
                                        </a>
                                    </div>
                                </li>                            
                                <?php
                            }
                            ?>

                            <?php
                            if (User::isAdmin()) {
                            ?>

                            <li>
                                <h2 class="text-danger"><?php echo __("Admin Menu"); ?></h2>
                                <ul  class="nav navbar" style="margin-bottom: 10px;">
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>users" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="glyphicon glyphicon-user"></span>
                                            <?php echo __("Users"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>usersGroups" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="fa fa-users"></span>
                                            <?php echo __("Users Groups"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>ads" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="far fa-money-bill-alt"></span>
                                            <?php echo __("Video Advertising"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>categories" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="glyphicon glyphicon-list"></span>
                                            <?php echo __("Categories"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>update" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                            <?php echo __("Update version"); ?>
                                            <?php
                                            if(!empty($updateFiles)){
                                                ?><span class="label label-danger"><?php echo count($updateFiles); ?></span><?php
                                            }
                                            ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>siteConfigurations" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="glyphicon glyphicon-cog"></span>
                                            <?php echo __("Site Configurations"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>locale" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="glyphicon glyphicon-flag"></span>
                                            <?php echo __("Create more translations"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $global['webSiteRootURL']; ?>plugins" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:15px;">
                                            <span class="fa fa-plug"></span> 
                                            <?php echo __("Plugins"); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <?php
                            }
                            ?>

                            <li style='margin-right:0px;'>
                                <div>
                                    <a href="<?php echo $global['webSiteRootURL']; ?>logoff" class="btn btn-default btn-block" style="background-color:transparent;color:#555;text-align:left;padding:7px;border-radius:0px;margin:8px;"> 
                                        <span class="glyphicon glyphicon-log-out"></span>
                                        <?php echo __("Logoff"); ?>
                                    </a>
                                </div>
                            </li>
                          </ul><!-- /.dropdown-user -->
                        </li><!-- /.dropdown -->                    
                <!-- END LOGGED USER -->
                    <?php
                    } else {
                    ?>
                        <li>
                            <div>
                                <a href="<?php echo $global['webSiteRootURL']; ?>user" class="btn btn-success btn-block" style="background-color:transparent;color:#555;text-align:left;padding:7px;border-radius:0px;margin:8px;"> 
                                    <span class="glyphicon glyphicon-log-in"></span>
                                    <?php echo __("Login"); ?>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <a href="<?php echo $global['webSiteRootURL']; ?>user" class="btn btn-success btn-block" style="color:#fff;padding:7px;margin:8px;"> 
                                    <?php echo __("Join Now! "); ?>
                                </a>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

        </li>
    </ul>
</nav>

<div id="sidebar" class="list-group-item" style="background-color:#F9FBFD;width:220px;position:fixed;margin-top:0px;float:left;">
    <div id="sideBarContainer">
        <ul class="nav navbar">                
            <li style='background-color:#fff;border-radius:8px;padding:10px;'>
                <a href="<?php echo $global['webSiteRootURL']; ?>makers" style='border-bottom: 1px solid #efefef;padding-bottom: 5px;margin-bottom:5px;'>
                    <i class="fa fa-search"></i>
                    <?php echo __("Live Makers"); ?>
                </a>
                <?php
                $totalUsers = User::getAllUsersByStreaming();
                foreach($totalUsers as $u){
                    echo 
                        '<div class="pull-left">
                            <img src="'.$u["photo"].'" alt="" class="img img-responsive img-circle" style="width:38px;height:38px;padding:2px;max-width: 40px;border-radius:50%;"/>
                        </div>
                        <div class="commentDetails" style="margin-left:45px;padding-top:5px;font-size:12px;">
                            <div class="commenterName text-muted">
                                <span style="min-width:5px;width:5px;height:5px;border-radius:50%;background-color:#'.($u['online'] ? "33cc33" : "FF0000").';min-height: 5px;float:left;margin-top:5px;margin-right:5px;margin-left:-5px;"></span> 
                                <strong style="margin-right:5px;"><a href="'.$u["user"].'">'.$u["name"].'</a></strong>
                                <span class="badge" style="font-size:10px;height:15px;text-align:center;line-height:12px;">'.$u["userCount"].'</span>
                                <br /><a href="'.$u["user"].'" style="font-size:11px;">@'.$u["user"].'</a>
                            </div>
                        </div>';
                }
                ?>
                <div class="pull-left">
                    <img src="<?php echo $global['webSiteRootURL']."img/userSilhouette.jpg"; ?>" alt="" class="img img-responsive img-circle" style="width:32px;height:32px;padding:4px;max-width: 40px;border-radius:50%;"/>
                </div>
                <div class="commentDetails" style="margin-left:45px;padding-top:5px;font-size:12px;">
                    <div class="commenterName text-muted">
                        <span style="min-width:5px;width:5px;height:5px;border-radius:50%;background-color:#33cc33;min-height: 5px;float:left;margin-top:5px;margin-right:5px;margin-left:-5px;"></span>
                        <strong style="margin-right:5px;"><a>Your Name</a></strong>
                        <span class="badge" style="font-size:10px;height:15px;text-align:center;line-height:12px;">0</span>
                        <br /><a href="" style="font-size:11px;">@becomeMember!</a>
                    </div>
                </div>
            </li>

            <?php
                if (empty($advancedCustom->removeBrowserChannelLinkFromMenu)) {
            ?>
            <li>
                <a href="<?php echo $global['webSiteRootURL']; ?>channels" style='font-size:12px;'>
                    <i class="fa fa-search"></i>
                    <?php echo __("Browse Channels"); ?>
                </a>
            </li>
            
            <?php
                }
            ?>
            <!-- categories -->
            <li>
                <h3 class="text-danger sidebar-title"><?php echo __("Categories"); ?></h3>
            </li>
            <?php
            
            function mkSub($catId){
                global $global;
                unset($_GET['parentsOnly']);
                $subcats = Category::getChildCategories($catId);
                if(!empty($subcats)){
                    echo "<ul style='margin-bottom: 0px; list-style-type: none;'>";
                    foreach($subcats as $subcat){
                            echo '<li class="' . ($subcat['clean_name'] == @$_GET['catName'] ? "active" : "") . '">'
                                . '<a href="' . $global['webSiteRootURL'] . 'cat/' . $subcat['clean_name'] . '" >'
                                . '<span class="' . (empty($subcat['iconClass']) ? "fa fa-folder" : $subcat['iconClass']) . '"></span>  ' . $subcat['name'] . '</a></li>'; 
                        mkSub($subcat['id']);
                    }
                    echo "</ul>";
                }
                
            }
            
            foreach ($categories as $value) {
                
                echo '<li class="' . ($value['clean_name'] == @$_GET['catName'] ? "active" : "") . '">'
                . '<a href="' . $global['webSiteRootURL'] . 'cat/' . $value['clean_name'] . '" >'
                . '<span class="' . (empty($value['iconClass']) ? "fa fa-folder" : $value['iconClass']) . '"></span>  ' . $value['name'] . '</a>'; 
                mkSub($value['id']);
                echo '</li>';
            }
            ?>

            <?php
            echo YouPHPTubePlugin::getHTMLMenuLeft();
            ?>

            <!-- categories END -->                
        </ul>

        <div style='position:fixed;bottom:5px;left:10px;'>
            <a href="<?php echo $global['webSiteRootURL']; ?>help" style='float:left; color:#555;font-size:12px;padding:0px 5px;'>
                <span class="glyphicon glyphicon-question-sign"></span>
                <?php echo __("Help"); ?>
            </a>
       
            <a href="<?php echo $global['webSiteRootURL']; ?>about" style='float:left;color:#555;font-size:12px;padding:0px 5px;'>
                <span class="glyphicon glyphicon-info-sign"></span>
                <?php echo __("About"); ?>
            </a>

            <a href="<?php echo $global['webSiteRootURL']; ?>contact" style='float:left;color:#555;font-size:12px;padding:0px 5px;'>
                <span class="glyphicon glyphicon-comment"></span>
                <?php echo __("Contact"); ?>
            </a>
        </div>
    </div>
</div>

<?php
if (!empty($advancedCustom->underMenuBarHTMLCode->value)) {
    echo $advancedCustom->underMenuBarHTMLCode->value;
}
}else if($thisScriptFile["basename"] !== 'user.php'){
    header("Location: {$global['webSiteRootURL']}user"); 
}
?>
