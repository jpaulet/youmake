<?php
return;
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/category.php';

$_GET['parentsOnly']="1";
$categories = Category::getAllCategories();
if (empty($_SESSION['language'])) {
    $lang = 'us';
} else {
    $lang = $_SESSION['language'];
}

$json_file = '{"doNotShowUploadMP4Button":true,"doNotShowImportMP4Button":false,"doNotShowImportLocalVideosButton":false,"doNotShowEncoderButton":false,"doNotShowEmbedButton":false,"doNotShowEncoderResolutionLow":false,"doNotShowEncoderResolutionSD":false,"doNotShowEncoderResolutionHD":false,"doNotShowLeftMenuAudioAndVideoButtons":false,"disableNativeSignUp":false,"disableNativeSignIn":false,"doNotShowWebsiteOnContactForm":false,"newUsersCanStream":false,"doNotIndentifyByEmail":false,"doNotIndentifyByName":false,"doNotIndentifyByUserName":false,"doNotUseXsendFile":false,"makeVideosInactiveAfterEncode":false,"usePermalinks":true,"showAdsenseBannerOnTop":false,"showAdsenseBannerOnLeft":true,"disableAnimatedGif":false,"unverifiedEmailsCanNOTLogin":false,"removeBrowserChannelLinkFromMenu":false,"uploadButtonDropdownIcon":"fas fa-video","uploadButtonDropdownText":"","EnableWavesurfer":true,"EnableMinifyJS":false,"disableShareAndPlaylist":false,"commentsMaxLength":"200","disableYoutubePlayerIntegration":false,"utf8Encode":false,"utf8Decode":false,"embedBackgroundColor":"white","userMustBeLoggedIn":false,"underMenuBarHTMLCode":{"type":"textarea","value":""},"encoderNetwork":""}';
$json_file = json_decode($json_file);
// $json_file = url_get_contents("{$global['webSiteRootURL']}plugin/CustomizeAdvanced/advancedCustom.json.php");

// convert the string to a json object
$advancedCustom = json_decode($json_file);
$thisScriptFile = pathinfo( $_SERVER["SCRIPT_FILENAME"]);
if(empty($advancedCustom->userMustBeLoggedIn) || User::isLogged()){
$updateFiles = getUpdatesFilesArray();

$plugin = YouPHPTubePlugin::loadPluginIfEnabled("YPTWallet");
$obj = $plugin->getDataObject();
?>
<nav class="navbar navbar-default navbar-fixed-top first-page-navbar" style='background-color:#F9FBFD;height:30px;'>
<div class='container-fluid' style='margin-top:0px;/*margin:0px;padding:0px;margin-left:0px !important;*/'>
    <div class='row'>
        <div class='col-xs-12'>
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
                                <img src="<?php echo $global['webSiteRootURL'], $config->getLogo(); ?>" alt="<?php echo $config->getWebSiteTitle(); ?>" class="img-responsive" style='min-width:130px;margin-left:-30px;'>
                            </a>
                        </li>
                    </ul>

                    <div class="navbar-header">
                        <button type="button" class=" navbar-toggle btn btn-default navbar-btn" data-toggle="collapse" data-target="#myNavbar" style="padding: 6px 12px;">
                            <span class="fa fa-bars"></span>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse" id="myNavbar" style='width:100%;'>
                        <ul class="right-menus" style='float:right;'>
                            
                            <?php
                            echo YouPHPTubePlugin::getHTMLMenuRight();
                            ?>
                            <?php
                            if (User::canUpload()) {
                                ?>
                                <li class="hidden-sm hidden-xs">

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle navbar-btn pull-left btn-zero-border"  data-toggle="dropdown" style='color:#470e82;background-color:transparent;'>
                                            <i class="<?php echo isset($advancedCustom->uploadButtonDropdownIcon)?$advancedCustom->uploadButtonDropdownIcon:"fas fa-video"; ?>"></i> <?php echo !empty($advancedCustom->uploadButtonDropdownText)?$advancedCustom->uploadButtonDropdownText:""; ?> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu" style="">
                                            <?php
                                            if (!empty($advancedCustom->encoderNetwork)) {
                                                ?>
                                                    <li>
                                                        <a href="<?php echo $advancedCustom->encoderNetwork, "?webSiteRootURL=", urlencode($global['webSiteRootURL']), "&user=", urlencode(User::getUserName()), "&pass=", urlencode(User::getUserPass()); ?>" target="encoder" style='width:100%;'>
                                                            <span class="fa fa-cogs"></span> <?php echo __("Encoder Network"); ?>
                                                        </a>
                                                    </li>
                                                <?php
                                            }
                                            if (empty($advancedCustom->doNotShowEncoderButton)) {
                                                if (!empty($config->getEncoderURL())) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo $config->getEncoderURL(), "?webSiteRootURL=", urlencode($global['webSiteRootURL']), "&user=", urlencode(User::getUserName()), "&pass=", urlencode(User::getUserPass()); ?>" target="encoder"  style='width:100%;'>
                                                            <span class="fa fa-cog"></span> 
                                                            <?php echo __("Encode video and audio"); ?>
                                                        </a>
                                                    </li>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo $global['webSiteRootURL']; ?>siteConfigurations" style='width:100%;'>
                                                            <span class="fa fa-cogs"></span> 
                                                            <?php echo __("Configure an Encoder URL"); ?>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            if (empty($advancedCustom->doNotShowUploadMP4Button)) {
                                                ?>
                                                <li>
                                                    <a  href="<?php echo $global['webSiteRootURL']; ?>upload"  style='width:100%;'>
                                                        <span class="fa fa-upload"></span> 
                                                        <?php echo __("Direct upload"); ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                            if (empty($advancedCustom->doNotShowImportLocalVideosButton)) {
                                                ?>
                                                <li>
                                                    <a  href="<?php echo $global['webSiteRootURL']; ?>view/import.php"  style='width:100%;'>
                                                        <span class="fas fa-hdd"></span> 
                                                        <?php echo __("Direct Import Local Videos"); ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                            if (empty($advancedCustom->doNotShowEmbedButton)) {
                                                ?>                                    
                                                <li>
                                                    <a  href="<?php echo $global['webSiteRootURL']; ?>mvideos?link=1"  style='width:100%;'>
                                                        <span class="fa fa-link"></span> 
                                                        <?php echo __("Embed a video link"); ?>
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
                            <li class="hidden-xs hidden-sm">
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
                                  <a aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown" href="#" style='position:absolute;top:13px;width:70px;color:#470e82;'>
                                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                                  </a>
                                  <ul class="dropdown-menu dropdown-user" style='left:-160px;width:200px;padding:0px 0px;'>
                                    <li style="min-height: 60px;margin-top:8px;border-bottom:1px solid #eee;margin-right:0px;">
                                        <div class="pull-left" style="margin-left: 12px;">
                                            <img src="<?php echo User::getPhoto(); ?>" style="max-width: 55px;border:0px solid #fff;"  class="img img-thumbnail img-responsive img-circle"/>
                                        </div>                        
                                        <div class='pull-right'>
                                            <h2 style='padding:0px;margin:0px;margin-top:10px;font-weight:600;'><?php echo User::getName(); ?></h2>
                                            <div><small><?php echo User::getMail(); ?></small></div>
                                        </div>
                                    </li>
                                    
                                    <li style='margin-right:0px;'>
                                        <div>
                                            <a href="<?php echo $global['webSiteRootURL']; ?>user" class="btn btn-primary btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:8px 15px;border-radius:0px;">
                                                <span class="fa fa-user-circle"></span>
                                                <?php echo __("My Account"); ?>
                                            </a>
                                        </div>
                                    </li>

                                    <li style='margin-right:0px;'>
                                        <div>
                                            <a href="<?php echo User::getChannelLink(); ?>" class="btn btn-danger btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:8px 15px;border-radius:0px;">
                                                <span class="fab fa-youtube"></span>
                                                <?php echo __("My Channel"); ?>
                                            </a>
                                        </div>
                                    </li>

                                    <?php
                                    if (User::canUpload()) {
                                        ?>
                                        <li style='margin-right:0px;'>
                                            <div>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>mvideos" class="btn btn-success btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:8px 15px;border-radius:0px;">
                                                    <span class="glyphicon glyphicon-film"></span>
                                                    <?php echo __("My videos"); ?>
                                                </a>
                                            </div>
                                        </li>
                                        <li style='margin-right:0px;'>
                                            <div>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>charts" class="btn btn-info btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:8px 15px;border-radius:0px;">
                                                    <span class="fas fa-tachometer-alt"></span>
                                                    <?php echo __("Dashboard"); ?>
                                                </a>
                                            </div>
                                        </li>
                                        <li style='margin-right:0px;'>
                                            <div>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>subscribes" class="btn btn-warning btn-block" style="background-color:#fff;color:#555;text-align:left;border-bottom:1px solid #eee;padding:8px 15px;border-radius:0px;">
                                                    <span class="fa fa-check"></span>
                                                    <?php echo __("Subscriptions"); ?>
                                                </a>
                                            </div>
                                        </li>
                                        <li style='margin-right:0px;'>
                                            <div>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>comments" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
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

                                    <li style='margin-right:0px;'>
                                        <h2 class="text-danger"><?php echo __("Admin Menu"); ?></h2>
                                        <ul  class="nav navbar" style="margin-bottom: 10px;">
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>users" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="glyphicon glyphicon-user"></span>
                                                    <?php echo __("Users"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>usersGroups" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="fa fa-users"></span>
                                                    <?php echo __("Users Groups"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>ads" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="far fa-money-bill-alt"></span>
                                                    <?php echo __("Video Advertising"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>categories" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="glyphicon glyphicon-list"></span>
                                                    <?php echo __("Categories"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>update" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="glyphicon glyphicon-refresh"></span>
                                                    <?php echo __("Update version"); ?>
                                                    <?php
                                                    if(!empty($updateFiles)){
                                                        ?><span class="label label-danger"><?php echo count($updateFiles); ?></span><?php
                                                    }
                                                    ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>siteConfigurations" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="glyphicon glyphicon-cog"></span>
                                                    <?php echo __("Site Configurations"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>locale" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
                                                    <span class="glyphicon glyphicon-flag"></span>
                                                    <?php echo __("Create more translations"); ?>
                                                </a>
                                            </li>
                                            <li style='margin-right:0px;'>
                                                <a href="<?php echo $global['webSiteRootURL']; ?>plugins" class="btn btn-default btn-block" style="background-color:#fff;color:#555;text-align:left;padding:8px 15px;border-radius:0px;border-bottom:1px solid #eee;">
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
                                        <a href="<?php echo $global['webSiteRootURL']; ?>user" class="btn btn-success btn-block youmake-button" style="color:#fff;padding:7px;margin:8px;height:35px;line-height: 22px;"> 
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
            </div>
        </div>
    </div>
</nav>

<div id="sidebar" class="list-group-item col-lg-2 col-md-2 col-sm-3 hidden-xs" style="background-color:#F9FBFD;margin-top:50px;max-width: 185px;position:absolute;top:0;max-height:92vh;">
    <div id="sideBarContainer">
        <ul style='padding:0px;'>
            <li class="nav navbar">
                <form class="navbar-form navbar-left" id="searchForm"  action="<?php echo $global['webSiteRootURL']; ?>" style='margin:0px;padding:0px 10px;'>
                    <div class="input-group" >
                        <div class="form-inline">
                            <input class="form-control" type="text" value="<?php if(!empty($_GET['search'])) { echo $_GET['search']; } ?>" name="search" placeholder="<?php echo __("Search"); ?>" style='width:75%;border: 0px;border: 0px;border: 1px solid #eee;background-color: rgba(255,255,255,0.5);border-right:0px;font-size:11px;'>
                            <button class="input-group-addon form-control hidden-xs"  style="width: 15%;border: 0px;border: 0px;border: 1px solid #eee;background-color: rgba(255,255,255,0.5);border-left:0px;" type="submit"><span class="glyphicon glyphicon-search" style='font-size:10px;margin-left:-6px;'></span></button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>

        <ul class="nav navbar" style='margin-bottom:-5px;margin-top:-20px;border-bottom:1px solid #eee;padding-bottom:20px;'>            
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>makers' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-video" style='padding-right:5px;'></i>
                    <?php echo __("Makers"); ?>
                </a>
            </li>
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>screencasts' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-desktop" style='padding-right:5px;'></i>
                    <?php echo __("ScreenCasts"); ?>
                </a>
            </li>
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>learning' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-graduation-cap" style='padding-right:5px;'></i>
                    <?php echo __("Learning"); ?>
                </a>
            </li>
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>learning' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-bullhorn" style='padding-right:5px;'></i>
                    <?php echo __("Conferences"); ?>
                </a>
            </li>
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>artists' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-street-view" style='padding-right:5px;'></i>
                    <?php echo __("Artists"); ?>
                </a>
            </li>
        </ul>

        <ul class="nav navbar" style='margin-bottom:-5px;margin-top:5px;border-bottom:1px solid #eee;padding-bottom:20px;'>            
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>calendar' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-calendar" style='padding-right:5px;'></i>
                    <?php echo __("Calendar"); ?>
                </a>
            </li>

            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>live' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-video" style='padding-right:5px;'></i>
                    <?php echo __("Live!"); ?>
                </a>
            </li>
        </ul>


        <ul class="nav navbar" style='margin-top:5px;'>            
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;font-weight:400;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>getstarted' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-bolt" style='padding-right:5px;'></i>
                    <?php echo __("Get Started"); ?>
                </a>
            </li>

            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;font-weight:400;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>how' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-microphone" style='padding-right:5px;'></i>
                    <?php echo __("How `Live Stream`"); ?>
                </a>
            </li>

            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;font-weight:400;'>
                <a href='<?php echo $global['webSiteRootURL']; ?>faq' style='margin-left:0px; margin-bottom:-20px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-info-circle" style='padding-right:5px;'></i>
                    <?php echo __("F.A.Q"); ?>
                </a>
            </li>
        </ul>

        <ul class="nav navbar" style=''>
            <?php 
            /*            
            <li class='panel-heading' style='margin-bottom:0px;color:#3c116bb3;'>
                <a href='' style='margin-left:0px; margin-bottom:0px;color:#3c116bb3;font-size:12px;'>
                    <i class="fa fa-video" style='padding-right:5px;'></i>
                    <?php echo __("Live Makers"); ?>
                </a>
            </li>

            <li style='background-color:#fff;border-radius:8px;padding:10px;'>
                <?php
                $totalUsers = array(); //User::getAllUsersByStreaming();
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
                <a href="<?php echo $global['webSiteRootURL']; ?>channels" style='font-size:12px;text-align:center;'>
                    <i class="fa fa-search"></i>
                    <?php echo __("Browse Channels"); ?>
                </a>
            </li>
            
            <?php
                }
            */                
            ?>
            
            <?php
            /*
            <!-- categories -->
            <li style=''>
                <h3 class="text-danger sidebar-title panel-heading" style='margin-bottom:0px;color:#3c116bb3;font-weight: 600;'><?php echo __("Categories"); ?></h3>
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
            <!-- categories END -->
            */
            ?>

            <?php
            //echo YouPHPTubePlugin::getHTMLMenuLeft();
            ?>            
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
