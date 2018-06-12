<?php
require_once '../../videos/configuration.php';
/**
 * this was made to mask the main URL
 */
if (!empty($_GET['webSiteRootURL'])) {
    $global['webSiteRootURL'] = $_GET['webSiteRootURL'];
}
require_once $global['systemRootPath'] . 'plugin/Live/Objects/LiveTransmition.php';

if (!empty($_GET['c'])) {
    $user = User::getChannelOwner($_GET['c']);
    if (!empty($user)) {
        $_GET['u'] = $user['user'];
    }
}
$customizedAdvanced = YouPHPTubePlugin::getObjectDataIfEnabled('CustomizeAdvanced');

$t = LiveTransmition::getFromDbByUserName($_GET['u']);
$uuid = $t['key'];
$p = YouPHPTubePlugin::loadPlugin("Live");
$objSecure = YouPHPTubePlugin::getObjectDataIfEnabled('SecureVideosDirectory');
if (!empty($objSecure->disableEmbedMode)) {
    die('Embed Mode disabled');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="img/favicon.ico">
        <title><?php echo $config->getWebSiteTitle(); ?> </title>
        <link href="<?php echo $global['webSiteRootURL']; ?>bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>

        <link href="<?php echo $global['webSiteRootURL']; ?>js/video.js/video-js.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>css/player.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <style>
            #chatOnline {
                width: 25vw !important;
                position: relative !important;
                margin: 0;
                padding: 0;
            }
            .container-fluid {
                padding-right: 0 !important; 
                padding-left: 0 !important; 
            }
            .liveChat .messages{
                -webkit-transition: all 1s ease; /* Safari */
                transition: all 1s ease;
            }
            #embedVideo-content .embed-responsive{
                max-height: 98vh;
            }
            body {
                padding: 0 !important;
                margin: 0 !important;
                <?php
                if(!empty($customizedAdvanced->embedBackgroundColor)){
                    echo "background-color: $customizedAdvanced->embedBackgroundColor;";
                }
                ?>
                
            }
        </style>
    </head>

    <body style="background-color: black; overflow-x: hidden;">
        <div class="container-fluid">
            <div class="col-md-9 col-sm-9 col-xs-9" style="margin: 0; padding: 0;" id="embedVideo-content">
                <?php
                echo $config->getAdsense();
                ?>
                <div class="embed-responsive  embed-responsive-16by9" >
                    <video poster="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/OnAir.jpg" controls autoplay="autoplay" 
                           class="embed-responsive-item video-js vjs-default-skin vjs-big-play-centered" 
                           id="mainVideo" data-setup='{ "aspectRatio": "16:9",  "techorder" : ["flash", "html5"] }'>
                        <source src="<?php echo $p->getPlayerServer(); ?>/<?php echo $uuid; ?>/index.m3u8" type='application/x-mpegURL'>
                    </video>
                    <div style="z-index: 999; position: absolute; top:5px; left: 5px; opacity: 0.8; filter: alpha(opacity=80);">
                        <?php
                        $streamName = $uuid;
                        include $global['systemRootPath'] . 'plugin/Live/view/onlineLabel.php';
                        include $global['systemRootPath'] . 'plugin/Live/view/onlineUsers.php';
                        ?>
                    </div>
                </div>

                <?php
                echo $config->getAdsense();
                ?>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3" style="margin: 0; padding: 0;">
                <?php
                $p->getChat($uuid);
                ?>
            </div>
        </div>

        <?php
        $liveCount = YouPHPTubePlugin::loadPluginIfEnabled('LiveCountdownEvent');
        $html = array();
        if ($liveCount) {
            $html = $liveCount->getNextLiveApplicationFromUser($user_id);
        }
        foreach ($html as $value) {
            echo $value['html'];
        };
        ?>
        <script>
            $(function () {
                $('.liveChat .messages').css({"height": ($(window).height() - 128) + "px"});
                window.addEventListener('resize', function () {
                    $('.liveChat .messages').css({"height": ($(window).height() - 128) + "px"});
                })
            });
        </script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/video.js/video.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/videojs-contrib-hls.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-persistvolume/videojs.persistvolume.js" type="text/javascript"></script>
        <script>

            $(document).ready(function () {
                player = videojs('mainVideo');
                player.ready(function () {
                    var err = this.error();
                    if (err && err.code) {
                        $('.vjs-error-display').hide();
                        $('#mainVideo').find('.vjs-poster').css({'background-image': 'url(<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/Offline.jpg)'});
<?php
if (!empty($html)) {
    echo "showCountDown();";
}
?>
                    }
<?php
if ($config->getAutoplay()) {
    echo "this.play();";
}
?>

                });
                player.persistvolume({
                    namespace: "YouPHPTube"
                });
            });
        </script>
        <?php
        require_once $global['systemRootPath'] . 'plugin/YouPHPTubePlugin.php';
        echo YouPHPTubePlugin::getFooterCode();
        ?>
    </body>
</html>