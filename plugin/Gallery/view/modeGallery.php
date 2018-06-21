<?php
if (!file_exists('../videos/configuration.php')) {
    if (!file_exists('../install/index.php')) {
        die("No Configuration and no Installation");
    }
    header("Location: install/index.php");
}
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'plugin/Gallery/functions.php';
$obj = YouPHPTubePlugin::getObjectData("Gallery");
if (!empty($_GET['type'])) {
    if ($_GET['type'] == 'audio') {
        $_SESSION['type'] = 'audio';
    } else if ($_GET['type'] == 'video') {
        $_SESSION['type'] = 'video';
    } else {
        unset($_SESSION['type']);
    }
}
require_once $global['systemRootPath'] . 'objects/category.php';
$currentCat;
$currentCatType;
if (!empty($_GET['catName'])) {
    $currentCat = Category::getCategoryByName($_GET['catName']);
    $currentCatType = Category::getCategoryType($currentCat['id']);
}
if ((empty($_GET['type'])) && (!empty($currentCatType))) {
    if ($currentCatType['type'] == "1") {
        $_SESSION['type'] = "audio";
    } else if ($currentCatType['type'] == "2") {
        $_SESSION['type'] = "video";
    } else {
        unset($_SESSION['type']);
    }
}
require_once $global['systemRootPath'] . 'objects/video.php';
$orderString = "";
if ($obj->sortReverseable) {
    if (strpos($_SERVER['REQUEST_URI'], "?") != false) {
        $orderString = $_SERVER['REQUEST_URI'] . "&";
    } else {
        $orderString = $_SERVER['REQUEST_URI'] . "/?";
    }
    $orderString = str_replace("&&", "&", $orderString);
    $orderString = str_replace("//", "/", $orderString);
}
$video = Video::getVideo("", "viewableNotAd", false, false, true);
if (empty($video)) {
    $video = Video::getVideo("", "viewableNotAd");
}
if (empty($_GET['page'])) {
    $_GET['page'] = 1;
} else {
    $_GET['page'] = intval($_GET['page']);
}
$total = 0;
$totalPages = 0;
$url = '';
$args = '';
if (strpos($_SERVER['REQUEST_URI'], "?") != false) {
    $args = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?"), strlen($_SERVER['REQUEST_URI']));
}
if (strpos($_SERVER['REQUEST_URI'], "/cat/") === false) {
    $url = $global['webSiteRootURL'] . "page/";
} else {
    $url = $global['webSiteRootURL'] . "cat/" . $video['clean_category'] . "/page/";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php
            echo $config->getWebSiteTitle();
            ?></title>
        <?php include $global['systemRootPath'] . 'view/include/head.php'; ?>
    </head>

    <body>
        <?php include $global['systemRootPath'].'view/include/navbar.php'; ?>
        <div class="container-fluid gallery" itemscope itemtype="http://schema.org/VideoObject">
            <div class='row'>
                <div class='col-xs-12'>
                    
                    <div class="row text-center" style="padding: 10px;">
                        <?php echo $config->getAdsense(); ?>
                    </div>

                    <div class='col-xs-12' style='padding:0px;margin:0px;margin-bottom:40px;background-color:#4f1091;border-radius:8px;'>
                        <img src="<?php echo $global['webSiteRootURL']; ?>img/youmakelive1.jpg" alt="Show your  ♥  send some $" id="transferFundsImg" style='height:250px;border-radius:8px;' />
                        <h1 style='float:right;color:#fff;font-size:36px;letter-spacing: 0.05;padding:20px 40px;line-height:40px;'> Welcome to <span style='font-weight:600;letter-spacing:0;font-size:42px;line-height:20px;margin-left:8px;margin-right:8px;'>youMake.live</span> !</h1>
                    </div>
                    
                    <div class="col-xs-12 list-group-item" style='padding:0px;margin:0px;'>
                        
                        <?php if (User::isLogged() && !User::completeProfile()){ ?>
                            <div class='jumbotron' style='padding-top:35px;padding-bottom:25px;'>
                                <span style='position:relative;top:-20px;width:10px;float:right;'> x </span>
                                <h2 style='text-align:center;font-weight: 600;margin-bottom:10px;margin-top:-4px;'> Finish your profile </h2>
                                <div> We encourage you to complete all the user information. A good profile picture, description about you and a channel name with `hook` will help you get a bigger community.</div>
                                <p style='font-size:13px;text-align:center;'> Let's do it! <br />
                                    <a class='button button-join youmake-button youmake-button-secundary' style='background-color: #ddd !important; color:#333;margin-top:10px;margin-bottom:-40px;' href='<?php echo $global['webSiteRootURL']; ?>user'>
                                        <span class="fa fa-user-circle"></span> Profile
                                    </a> 
                                </p>
                            </div>
                        <?php } ?>
                        
                        <?php if(!User::isLogged()){ ?>
                            <div class='jumbotron' style='background-color:#dbbffc33;color:#3c116bb3;border-width: '>
                                <span style='position:relative;top:-20px;width:10px;float:right;'> x </span>
                                <h2 style='text-align:center;font-weight: 600;margin-bottom:10px;margin-top:-4px;'> Join the community of Live Makers! </h2>
                                <div> 
                                    Build your product live in YouMake, get <strong>real feedback</strong> from potentials users, 
                                    <strong>grow your community</strong> around your product before you launch
                                    and <strong>earn money</strong> while you build! 
                                </div>
                                <div> 
                                    We are X liveMakers right now! 
                                </div>
                                <p style='font-size:13px;margin-top:15px;margin-bottom:-15px;text-align:center;'> 
                                    Want more? See our features, <a class='button youmake-button' href=''>JOIN now!</a> or read the F.A.Q
                                </p>
                            </div>
                        <?php } ?>

                        <?php

                        if (!empty($currentCat)) {
                            include $global['systemRootPath'] . 'plugin/Gallery/view/Category.php';
                        }
                        if (!empty($video)) {
                            $img_portrait = ($video['rotation'] === "90" || $video['rotation'] === "270") ? "img-portrait" : "";
                            include $global['systemRootPath'] . 'plugin/Gallery/view/BigVideo.php';
                            ?>

                            <div class="row mainArea" style='padding:0px;margin:0px;'>
                                <!-- For Live Videos -->
                                <div id="liveVideos" class="clear clearfix" style="display: none;">
                                    <h3 class="galleryTitle text-danger"> <i class="fab fa-youtube"></i> <?php echo __("Live"); ?></h3>
                                    <div class="row extraVideos"></div>
                                </div>
                                <script>
                                    function afterExtraVideos($liveLi) {
                                        $liveLi.removeClass('col-lg-12 col-sm-12 col-xs-12 bottom-border');
                                        $liveLi.find('.thumbsImage').removeClass('col-lg-5 col-sm-5 col-xs-5');
                                        $liveLi.find('.videosDetails').removeClass('col-lg-7 col-sm-7 col-xs-7');
                                        $liveLi.addClass('col-lg-2 col-md-4 col-sm-4 col-xs-6 fixPadding');
                                        $('#liveVideos').slideDown();
                                        return $liveLi;
                                    }
                                </script>
                                <?php
                                echo YouPHPTubePlugin::getGallerySection();
                                ?>
                                <!-- For Live Videos End -->    
                                <?php
                                if ($obj->SortByName) {
                                    createGallery(__("Sort by name"), 'title', $obj->SortByNameRowCount, 'sortByNameOrder', "zyx", "abc", $orderString);
                                } 
                                if ($obj->DateAdded) {
                                    createGallery(__("Date added"), 'created', $obj->DateAddedRowCount, 'dateAddedOrder', __("newest"), __("oldest"), $orderString, "DESC");
                                } 
                                if ($obj->MostWatched) {
                                    createGallery(__("Most watched"), 'views_count', $obj->MostWatchedRowCount, 'mostWatchedOrder', __("Most"), __("Fewest"), $orderString, "DESC");
                                }
                                if ($obj->MostPopular) {
                                    createGallery(__("Most popular"), 'likes', $obj->MostPopularRowCount, 'mostPopularOrder', __("Most"), __("Fewest"), $orderString, "DESC");
                                }
                                if ($obj->SubscribedChannels && User::isLogged() && empty($_GET['showOnly'])) {
                                    require_once $global['systemRootPath'] . 'objects/subscribe.php';
                                    $channels = Subscribe::getSubscribedChannels(User::getId());
                                    foreach ($channels as $value) {
                                        ?>    
                                        <div class="clear clearfix">
                                            <h3 class="galleryTitle">
                                                <img src="<?php echo $value['photoURL']; ?>" class="img img-circle img-responsive pull-left" style="max-height: 20px;">
                                                <span style="margin: 0 5px;">
                                                    <?php
                                                    echo $value['identification'];
                                                    ?>
                                                </span>
                                                <a class="btn btn-xs btn-default" href="<?php echo User::getChannelLink($value['users_id']); ?>" style="margin: 0 10px;">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <?php
                                                echo Subscribe::getButton($value['users_id']);
                                                ?>
                                            </h3>
                                            <div class="row">
                                                <?php
                                                $countCols = 0;
                                                unset($_POST['sort']);
                                                $_POST['sort']['created'] = "DESC";
                                                $_POST['current'] = 1;
                                                $_POST['rowCount'] = $obj->SubscribedChannelsRowCount;
                                                $total = Video::getTotalVideos("viewableNotAd", $value['users_id']);
                                                $videos = Video::getAllVideos("viewableNotAd", $value['users_id']);
                                                createGallerySection($videos);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } else { ?>
                        
                            <div class="alert">
                                <span class="glyphicon glyphicon-facetime-video"></span>
                                <?php echo __("We have not found any videos or audios to show"); ?>.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include $global['systemRootPath'] . 'view/include/footer.php'; ?>
</body>
</html>
<?php include $global['systemRootPath'] . 'objects/include_end.php'; ?>
