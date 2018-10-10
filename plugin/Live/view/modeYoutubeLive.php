<?php
require_once '../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'plugin/Live/Objects/LiveTransmition.php';

if(!empty($_GET['c'])){
    $user = User::getChannelOwner($_GET['c']);
    if(!empty($user)){
        $_GET['u'] = $user['user'];
    }
}

$t = LiveTransmition::getFromDbByUserName($_GET['u']);
$uuid = $t['key'];

$u = new User(0, $_GET['u'], false);
$user_id = $u->getBdId();
$subscribe = Subscribe::getButton($user_id);
$name = $u->getNameIdentificationBd();

$img = "{$global['webSiteRootURL']}plugin/Live/getImage.php?u={$_GET['u']}&format=jpg";
$imgw = 640;
$imgh = 360;
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $t['title']; ?> - <?php echo __("Live Video"); ?> - <?php echo $config->getWebSiteTitle(); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/video.js/video-js.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>css/player.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        
        <meta property="fb:app_id"             content="774958212660408" />
        <meta property="og:url"                content="<?php echo $global['webSiteRootURL']; ?>plugin/Live/?u=<?php echo $_GET['u']; ?>" />
        <meta property="og:type"               content="video.other" />
        <meta property="og:title"              content="<?php echo str_replace('"', '', $t['title']); ?> - <?php echo $config->getWebSiteTitle(); ?>" />
        <meta property="og:description"        content="<?php echo str_replace('"', '', $t['title']); ?>" />
        <meta property="og:image"              content="<?php echo $img; ?>" />
        <meta property="og:image:width"        content="<?php echo $imgw; ?>" />
        <meta property="og:image:height"       content="<?php echo $imgh; ?>" />
        <style>
            .youmake-button{
                height: 35px;
                line-height: 35px;
            }
            .payment_amount_option {
                margin-bottom:2px;
                min-width:45%;
                text-align:center;
                cursor:pointer;
            }
            .payment_dropdown {
                float:left;
                border-radius:0px;
                margin-left:-2px;
                list-style-type:none;
            }

            #sidebar{
                display:none;
            }

            .container-fluid{
                padding-left: 20px !important;
            }
        </style>
    </head>

    <body>
    <?php
    include $global['systemRootPath'] . 'view/include/navbar.php';
    $lt = new LiveTransmition($t['id']);
    if($lt->userCanSeeTransmition()){
    ?>            
        <div class="container-fluid principalContainer " itemscope itemtype="http://schema.org/VideoObject" style='margin-top:10px;'>
            <div class="col-md-12">
                <?php require "{$global['systemRootPath']}plugin/Live/view/liveVideo.php"; ?>
            </div>  
        </div>
        <div class="container-fluid ">
            <div class="col-md-8 list-group-item">
                <h1 itemprop="name">
                    <i class="fas fa-video"></i> <?php echo $t['title']; ?>
                </h1>
                <p><?php echo nl2br(textToLink($t['description'])); ?></p>
                <div class="col-xs-12 col-sm-12 col-lg-12">
                    <div class="pull-left">
                        <img src="<?php echo User::getPhoto($user_id); ?>" alt="User avatar" class="img img-responsive img-circle" style="max-width: 40px;"/>
                    </div>
                    <div class="commentDetails" style="margin-left:45px;">
                        <div class="commenterName text-muted">
                            <strong style="margin-right:5px;"><?php echo $name; ?></strong>
                            <?php echo $subscribe; ?>

                            <div class="btn-group">
                                <button class="btn btn-xs youmake-button" id='transferNow'>
                                    <i class="far fa-money-bill-alt"></i> 
                                    <b class="text">Donate </b>
                                </button>

                                <li class="dropdown payment_dropdown">
                                    <a href="#" class="btn btn-default navbar-btn youmake-button" data-toggle="dropdown">
                                        <span class='payment_amount'>1$</span>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right notify-drop" style='min-width:150px;'>
                                        <li class='youmake-button payment_amount_option' style='margin-left:5px;'>1$</li>
                                        <li class='youmake-button payment_amount_option'>5$</li>
                                        <li class='youmake-button payment_amount_option' style='margin-left:5px;'>10$</li>
                                        <li class='youmake-button payment_amount_option'>20$</li>
                                    </ul>
                                </li>
                            </div>

                        </div>
                    </div>
                </div>
            </div> 
            <div class="col-md-3">
                <?php echo $config->getAdsense(); ?>
            </div>
        </div>
    <?php
    }else{
    ?>
        <h1 class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> 
            <?php echo __("You are not allowed see this streaming"); ?>
        </h1>    
    <?php
    }
    ?>

    <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $('.payment_amount_option').click(function(){
                $('.payment_amount').text($(this).text());
            });

            $('#transferNow').click(function () {
                swal({
                    title: "<?php echo __("Are you sure?"); ?>",
                    text: "<?php echo __("You will not be able to recover this action!"); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo __("Yes, transfer it!"); ?>",
                    closeOnConfirm: true
                },
                function () {
                    modal.showPleaseWait();
                    $.ajax({
                        url: '<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/transferFunds.json.php',
                        data: {
                            "value": $('.payment_amount').text().slice(0,-1),
                            "users_id": <?php echo $user_id; ?>
                        },
                        type: 'post',
                        success: function (response) {
                            $(".walletBalance").text(response.walletBalance);
                            modal.hidePleaseWait();
                            if (response.error) {
                                setTimeout(function () {
                                    swal("<?php echo __("Sorry!"); ?>", response.msg, "error");
                                }, 500);
                            } else {
                                setTimeout(function () {
                                    swal("<?php echo __("Congratulations!"); ?>", "<?php echo __("Funds successfully transferred"); ?>", "success");
                                }, 500);
                            }
                        }
                    });
                });
            });
        });
        /*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
        $.widget.bridge('uibutton', $.ui.button);
        $.widget.bridge('uitooltip', $.ui.tooltip);
    </script>  

    <script src="<?php echo $global['webSiteRootURL']; ?>js/video.js/video.js" type="text/javascript"></script>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.min.js" type="text/javascript"></script>
    <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/videojs-contrib-hls.min.js" type="text/javascript"></script>
    <?php include $global['systemRootPath'] . 'view/include/footer.php'; ?>

    <?php
    if(!empty($p)){
        $p->getChat($uuid);
    }
    ?>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-persistvolume/videojs.persistvolume.js" type="text/javascript"></script>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.js" type="text/javascript"></script>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-list-filter/bootstrap-list-filter.min.js" type="text/javascript"></script>        
    </body>
</html>

<?php include $global['systemRootPath'].'objects/include_end.php'; ?>
