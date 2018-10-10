<?php
require_once '../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

$p = YouPHPTubePlugin::loadPlugin("Live");

if(!empty($_GET['c'])){
    $user = User::getChannelOwner($_GET['c']);
    if(!empty($user)){
        $_GET['u'] = $user['user'];
    }
}

if (!empty($_GET['u']) && !empty($_GET['embedv2'])) {
    include $global['systemRootPath'].'plugin/Live/view/videoEmbededV2.php';
    exit;
} else if (!empty($_GET['u']) && !empty($_GET['embed'])) {
    include $global['systemRootPath'].'plugin/Live/view/videoEmbeded.php';
    exit;
} else if (!empty($_GET['u'])) {
    include $global['systemRootPath'].'plugin/Live/view/modeYoutubeLive.php';
    exit;
} else if (!User::canStream()) {
    header("Location: {$global['webSiteRootURL']}?error=" . __("You can not stream live videos"));
    exit;
}

require_once $global['systemRootPath'] . 'objects/userGroups.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'plugin/Live/Objects/LiveTransmition.php';

// if user already have a key
$trasnmition = LiveTransmition::createTransmitionIfNeed(User::getId());
if(!empty($_GET['resetKey'])){
    LiveTransmition::resetTransmitionKey(User::getId());
    header("Location: {$global['webSiteRootURL']}plugin/Live/");
    exit;
}

$aspectRatio = "16:9";
$vjsClass = "vjs-16-9";

$trans = new LiveTransmition($trasnmition['id']);
$groups = $trans->getGroups();
$obj = $p->getDataObject();

//check if channel name exists
$channelName = User::getUserChannelName();
$user = new User(User::getId());
if(empty($channelName)){
    $channelName = uniqid();
    $user->setChannelName($channelName);
    $user->save();    
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <title><?php echo __("Live"); ?> - <?php echo $config->getWebSiteTitle(); ?></title>
    <?php
    include $global['systemRootPath'] . 'view/include/head.php';
    ?>
    <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/swfobject.js" type="text/javascript"></script>
    <link href="<?php echo $global['webSiteRootURL']; ?>js/video.js/video-js.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $global['webSiteRootURL']; ?>css/player.css" rel="stylesheet" type="text/css"/>
    
    <!-- <script src="<?php echo $global['webSiteRootURL']; ?>js/video.js/video.js" type="text/javascript"></script> -->
    <!-- <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/videojs-contrib-hls.min.js" type="text/javascript"></script> -->
    
    <link href="<?php echo $global['webSiteRootURL']; ?>js/Croppie/croppie.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/Croppie/croppie.min.js" type="text/javascript"></script>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery.countdown.min.js" type="text/javascript"></script>
    <link href="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
    <style>
        .container{
            width: 100% !important;
        } 
        .previewPage{
            display:none;
        }

        .nav-tabs { 
            padding:20px 20px 0px 0px;
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
            border:0px;
            border-bottom:2px solid #3f0c74;
            background-color: transparent !important;
        }

        .nav-tabs > li.active {
            background-color: #f3f1f380;
        }

        .nav-tabs > li > a{
            color: #3f0c74b3 !important;
            padding: 10px 15px;
        }

        .nav-tabs > li > a:hover{
            color: #3f0c74 !important;
        }

        .nav-tabs > li.active > a{
            color: #3f0c74 !important;
        }

        .sectionTitle {
            font-weight: 600;
            margin-bottom:15px;
            margin-left:-5px;
        }

        .jumbotron-info{
            margin-top:20px; 
            padding:20px 0px;
            font-size:12px;
            margin-bottom:20px;
            text-align:justify;
            color:#888;
            background-color:#eee2;
        }
        .bgWhite{
            background-color: #fff;
        }   

        .pickColor{
            cursor:pointer;
            width:14px;
            height:14px;
            float:left;
            margin-left:3px;
            margin-top:3px;
        }

        .defaultColor{
            border:1px solid #000;
        }

        .drop-file-zone {
            background-color:#eee2;
            border: 1px dimgray dashed;
        }

        .rrssb-buttons {
            box-sizing: border-box;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 12px;
            height: 36px;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        
        .rrssb-buttons li a {
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
        }

        .rrssb-buttons li {
            box-sizing: border-box;
            float: left;
            height: 100%;
            line-height: 13px;
            list-style: none;
            margin: 0;
            padding: 0 2px;
            /*min-width:120px;*/
            margin-bottom:-5px;
        }
        .rrssb-buttons li.rrssb-linkedin a {
            background-color: #007bb6;
        }
        .rrssb-buttons li.rrssb-twitter a {
            background-color: #26c4f1;
        }
        .rrssb-buttons li.rrssb-googleplus a {
            background-color: #e93f2e;
        }
        .rrssb-buttons li.rrssb-whatsapp a {
            background-color: #43d854;
        }
        .rrssb-buttons li.rrssb-email a {
            background-color: #0a88ff;
        }
        .rrssb-buttons li.rrssb-facebook a:hover {
            background-color: #244872;
        }
        .rrssb-buttons li.rrssb-facebook a {
            background-color: #306199;
        }
        .rrssb-buttons li a {
            background-color: #ccc;
            border-radius: 2px;
            box-sizing: border-box;
            display: block;
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            font-weight: 700;
            height: 100%;
            padding: 11px 7px 12px 27px;
            position: relative;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            -webkit-transition: background-color .2s ease-in-out;
            transition: background-color .2s ease-in-out;
            width: 100%;
            color:#fff;
            font-size:0.8em;
        }
        .rrssb-buttons li a .rrssb-icon {
            display: block;
            left: 10px;
            padding-top: 9px;
            position: absolute;
            top: 0;
            width: 10%;
        }
        .rrssb-buttons li a .rrssb-icon svg path {
            fill: #fff;
        }
        .rrssb-buttons li a .rrssb-icon svg {
            height: 17px;
            width: 17px;
        }

        .section_head {
            border: none !important;
            font-size: 22px !important;
            text-align: center;
            margin: 0;
            margin-bottom: 0px;
            margin-bottom: 20px;
            letter-spacing: .2em;
            font-weight: 200;
        }
        #event_page_wrap #details .event_poster img {
            border: 4px solid #f6f6f6;
            max-width: 100%;
            min-width: 100%;
        }
        section.container {
            padding: 40px;
            background-color: #ffffff;
            margin-bottom: 25px;
        }
        
        #event_page_wrap #intro h1 {
            position: relative;
            padding: 10px 10px;
            margin: 0;
            margin-bottom: 0px;
            font-weight: 400;
            font-size: 40px;
            margin-bottom: 10px;
        }

        .site-footer {
            padding: 140px 0 40px;
            padding: 80px 0 20px;
            width: 100%;
            background: #1a1d1e;
        }
        .bg-constellation {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .constellation-shapes {
            display:block;
            position:absolute;
            width:100%;
            height:95%;
            top:0;
            left:0;
            z-index:-1;
            background:#1a1d1e url(<?php echo $global['webSiteRootURL']; ?>view/img/constellation.svg) center/cover no-repeat
        }

        .bg-constellation::before {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            bottom: 0;
            content: '';
            background: url(<?php echo $global['webSiteRootURL']; ?>view/img/constellation.svg) center/cover no-repeat;
            display: block;
            z-index: -1;
        }

        .site-footer::after {
            width: 102%;
            height: 80px;
            left: -1%;
            top: -1px;
            content: '';
            background: url(<?php echo $global['webSiteRootURL']; ?>view/img/constellation.svg) bottom center/102% auto no-repeat;
            display: block;
            position: absolute;
            -ms-transform: scale(1,-1);
            transform: scale(1,-1);

        }
        .bg-black {
            background:#1a1d1e
        }
        .constellation-shape-wrap {
            position:relative;
            overflow:hidden;
            z-index:2;
            padding-top:50px
        }
        
        .clock-div {
            text-align: center;
        }

        .clock-div div {
            text-align: center;
            display: block;
            line-height: 2em;
            font-size: 2em;
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.2);
            color:#555;
        }

        .clock-div label{
            text-align: center;
            color:#999;
        }
        
        .photo-list ul {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .photo-list ul li.circle {
            width: 60px;
            height: 60px;
            margin: 10px;
            display: inline-block;
            overflow: hidden;
            background: #f3f6f9;
            border-radius: 50%;
        }

        .photo-list ul li.circle img {
            width: 100%;
            min-height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .eventTxt {
            display: block;
            font-family: futura-pt-condensed,"Arial Narrow","Trebuchet MS",sans-serif;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 16px;
            font-size: 1.6rem;
            line-height: 1.55;
            letter-spacing: 2px;
        }

        figure {
          width: 128px;
          height: 111px;
          background-color: #ffffff;
          border-radius: 10px;
          position: relative;
          min-width:40px;
        }
        figure:before {
          content: '';
          display: block;
          width: 100%;
          height: 69px;
          border-radius: 10px 10px 0 0;
          background-image: -webkit-linear-gradient(white 0%, #edeeef 100%);
          background-image: -moz-linear-gradient(white 0%, #edeeef 100%);
          background-image: -o-linear-gradient(white 0%, #edeeef 100%);
          background-image: linear-gradient(white 0%, #edeeef 100%);
          min-width:40px;
        }
        figure header {
          width: 100%;
          height: 27px;
          position: absolute;
          top: -1px;
          background-color: #fa565a;
          border-radius: 10px 10px 0 0;
          border-bottom: 3px solid #e5e5e5;
          font: 400 15px/27px Arial, Helvetica, Geneva, sans-serif;
          letter-spacing: 0.5px;
          color: #fff;
          text-align: center;
          min-width:40px;
        }
        figure section {
          width: 100%;
          height: 80px;
          position: absolute;
          top: 28px;
          font: 400 55px/75px "Helvetica Neue", Arial, Helvetica, Geneva, sans-serif;
          letter-spacing: -2px;
          color: #4c566b;
          text-align: center;
          z-index: 10;
          min-width:40px;
        }
        figure section:before {
          content: '';
          display: block;
          position: absolute;
          top: 35px;
          width: 3px;
          height: 10px;
          background-image: -webkit-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: -moz-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: -o-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
        }
        figure section:after {
          content: '';
          display: block;
          position: absolute;
          top: 35px;
          right: 0;
          width: 3px;
          height: 10px;
          background-image: -webkit-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: -moz-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: -o-linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
          background-image: linear-gradient(#b5bdc5 0%, #e5e5e5 100%);
        }
        /* @end */

    </style>
</head>
<body>
    <?php
    include $global['systemRootPath'] . 'view/include/navbar.php';

    if(false && YPTWallet::getTotalBalance() == 0 && !User::isAdmin()){ 
    ?>
        <div class="container-fluid">
            <div class='row'>
                <div class='col-xs-12'>
                    Add funds
                </div>
            </div>
        </div>
    <?php
        return;
    }
    ?>        

    <div class="container-fluid">
        <div class="row">
        
            <ul class="col-xs-12 nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#streamNow">
                        <i class="fa fa-plug"></i> Stream Now!
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#futureEvent">
                        <i class="fa fa-inbox"></i> Future Event
                    </a>
                </li>
                <!--
                <li>
                    <a data-toggle="tab" href="#masterClass">
                        <i class="fa fa-plus"></i> MasterClass
                    </a>
                </li>
                -->
            </ul>

            <div class="tab-content">
                
                <div id="streamNow" class="tab-pane fade in active">
                    <div class='col-xs-12 bgWhite'>
                    
                        <div class='jumbotron jumbotron-info'>
                            Publish a public (or private) stream right now. If the streaming is marked as <i>`Public Transmition`</i> it will be visible for all users in the platform. Below you must set the visible <i>`Title`</i> and <i>`Description`</i>. You can find the streaming share info and the server information in order to set in your broadcast software. Have fun! 
                        </div>

                        <!-- Left -->
                        <div class="col-md-6">
                            <div class="">
                                <div class="panel-heading">
                                    <?php
                                    $streamName = $trasnmition['key'];
                                    include $global['systemRootPath'].'plugin/Live/view/onlineLabel.php';
                                    ?>
                                </div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'>          
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video poster="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/OnAir.jpg" controls 
                                         class="embed-responsive-item video-js vjs-default-skin <?php echo $vjsClass; ?> vjs-big-play-centered" 
                                         id="mainVideo" data-setup='{ aspectRatio: "<?php echo $aspectRatio; ?>",  "techorder" : ["flash", "html5"] }'>
                                         <source src="<?php echo $p->getPlayerServer(); ?>/<?php echo $trasnmition['key']; ?>/index.m3u8" type='application/x-mpegURL'>
                                         </video>
                                     </div>
                                 </div>
                             </div>
                             <div class="">
                                <div class="panel-heading" style='font-weight:600;margin-top:20px;'><?php echo __("Stream Settings"); ?></div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'> 
                                    <div class="form-group">
                                        <label for="title"><?php echo __("Title"); ?>:</label>
                                        <input type="text" class="form-control" id="title" value="<?php echo $trasnmition['title'] ?>">
                                    </div>    
                                    <div class="form-group">
                                        <label for="description"><?php echo __("Description"); ?>:</label>
                                        <textarea class="form-control" id="description"><?php echo $trasnmition['description'] ?></textarea>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <span class="fa fa-globe"></span> <?php echo __("Public Transmition"); ?> 
                                        <div class="material-switch pull-right">
                                            <input id="listed" type="checkbox" value="1" <?php echo!empty($trasnmition['public']) ? "checked" : ""; ?>/>
                                            <label for="listed" class="label-success"></label> 
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right -->
                        <div class="col-md-6">
                            <?php
                            if(!empty($obj->experimentalWebcam)){
                                ?>
                                <div class="">
                                    <div class="panel-heading" style='font-weight:600;'><?php echo __("WebCam Streaming"); ?></div>
                                    <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <div class="embed-responsive-item"  id="webcam">
                                                <button class="btn btn-primary btn-block" id="enableWebCam">
                                                    <i class="fa fa-camera"></i> <?php echo __("Enable WebCam Stream"); ?>
                                                </button>
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-warning"><?php echo __("We will check if there is a stream conflict before stream"); ?></i>
                                                </div>
                                                
                                                <div class="alert alert-info">
                                                    <?php echo __("This is an experimental resource"); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="">
                                <div class="panel-heading" style='font-weight:600;'><i class="fa fa-share"></i> <?php echo __("Share Info"); ?></div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;max-height:240px;'>          
                                    <div class="form-group">
                                        <label for="playerURL"><i class="fa fa-play-circle"></i> <?php echo __("Player URL"); ?>:</label>
                                        <input type="text" class="form-control" id="playerURL" value="<?php echo $p->getPlayerServer(); ?>/<?php echo $trasnmition['key']; ?>/index.m3u8"  readonly="readonly">
                                    </div>       
                                    <div class="form-group">
                                        <label for="youphptubeURL"><i class="fa fa-circle"></i> <?php echo __("Live URL"); ?>:</label>
                                        <input type="text" class="form-control" id="youphptubeURL" value="<?php echo $global['webSiteRootURL']; ?>plugin/Live/?c=<?php echo urlencode($channelName); ?>"  readonly="readonly">
                                    </div>   
                                    <div class="form-group">
                                        <label for="embedStream"><i class="fa fa-code"></i> <?php echo __("Embed Stream"); ?>:</label>
                                        <input type="text" class="form-control" id="embedStream" value='<iframe width="640" height="480" style="max-width: 100%;max-height: 100%;" src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/?c=<?php echo urlencode($channelName); ?>&embed=1" frameborder="0" allowfullscreen="allowfullscreen" class="YouPHPTubeIframe"></iframe>'  readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="panel-heading" style='font-weight:600;margin-top:20px;'><i class="fa fa-hdd-o"></i> <?php echo __("Devices Stream Info"); ?></div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;max-height:200px;'>
                                    <div class="form-group">
                                        <label for="server"><i class="fa fa-server"></i> <?php echo __("Server URL"); ?>:</label>
                                        <input type="text" class="form-control" id="server" value="<?php echo $p->getServer(); ?>?p=<?php echo User::getUserPass(); ?>" readonly="readonly">
                                        <small class="label label-info"><i class="fa fa-warning"></i> <?php echo __("If you change your password the Server URL parameters will be changed too."); ?></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="streamkey"><i class="fa fa-key"></i> <?php echo __("Stream name/key"); ?>:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="streamkey" value="<?php echo $trasnmition['key']; ?>" readonly="readonly">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default" href="<?php echo $global['webSiteRootURL']; ?>plugin/Live/?resetKey=1"><i class="fa fa-refresh"></i> <?php echo __("Reset Key"); ?></a>
                                            </span>
                                        </div>
                                        <span class="label label-warning"><i class="fa fa-warning"></i> <?php echo __("Anyone with this key can watch your live stream."); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php YouPHPTubePlugin::getLivePanel(); ?>

                            <div class="">
                                <div class="panel-heading" style='font-weight:600;margin-top:20px;'><?php echo __("Groups That Can See This Stream"); ?><br><small><?php echo __("Uncheck all to make it public"); ?></small></div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'> 
                                    <?php
                                    $ug = UserGroups::getAllUsersGroups();
                                    foreach ($ug as $value) {
                                        ?>
                                        <div class="form-group">
                                            <span class="fa fa-users"></span> <?php echo $value['group_name']; ?>
                                            <div class="material-switch pull-right">
                                                <input id="group<?php echo $value['id']; ?>" type="checkbox" value="<?php echo $value['id']; ?>" class="userGroups" <?php echo (in_array($value['id'], $groups) ? "checked" : "") ?>/>
                                                <label for="group<?php echo $value['id']; ?>" class="label-success"></label>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <a href="<?php echo $global['webSiteRootURL']; ?>usersGroups" class="btn btn-primary"><span class="fa fa-users"></span> <?php echo __("Add more user Groups"); ?></a>
                                </div>
                            </div>
                        </div>
                    
                        <div class='col-xs-12' style='text-align:center;margin-bottom:20px;margin-top:25px;'>
                            <button type="button" class="btn btn-success youmake-button" id="btnSaveStream"><?php echo __("Save Stream"); ?></button>                        
                        </div>
                    </div>
                </div>
            
                <!--
                <div id="futureEvent" class="tab-pane fade">

                    <div class='col-xs-12 bgWhite'>

                        <div class='jumbotron jumbotron-info'>
                            Publish a public (or private) stream in the future. This enables you more control, a public event page (where people can subscribe) and a ticketing system to earn money while you are streaming. If the streaming is marked as <i>`Public Transmition`</i> it will be visible for all users in the platform. Below you must set the visible <i>`Title`</i>, <i>`Description`</i> and <i>the event time</i>. You can find the streaming share info and the server information in order to set in your broadcast software. Have fun! 
                        </div>
                        
                        <div class="col-sm-12 col-md-6">
                            <div class="">
                                <div class="panel-heading">
                                    Event Info:
                                </div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                                    <div class="form-group">
                                        <label for="titleEvent"><?php echo __("Title"); ?>:</label>
                                        <input type="text" class="form-control" id="titleEvent" value="<?php echo $event['title'] ?>">
                                        <div class='titleColor' style='line-height:20px;height:20px;'>
                                            <label style='float:left;font-size:11px;font-weight:200;color:#888;'> Color: </label>
                                            <p class='pickColor defaultColor' style='background-color: #fff;'></p>
                                            <p class='pickColor' style='background-color: #000;'></p>
                                            <p class='pickColor' style='background-color:#ff0000;'></p>
                                            <p class='pickColor' style='background-color:#00ff00;'></p>
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label for="descriptionEvent"><?php echo __("Description"); ?>:</label>
                                        <textarea class="form-control" id="descriptionEvent"><?php echo $event['description'] ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="descriptionEvent"><?php echo __("Tags"); ?>:</label>
                                        <input class="form-control" id="tagsEvent" value="<?php echo $event['tags'] ?>" placeholder='Comma-separated event princpials tags'>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputLinkStarts"><?php echo __("Starts on"); ?>:</label>
                                        <input type="text" id="inputLinkStarts" name="start_date" class="form-control datepickerLink" placeholder="<?php echo __("Starts on"); ?>" required >
                                    </div>
                                    <div class="form-group">
                                        <label for="inputLinkEnd"><?php echo __("End on"); ?>:</label>
                                        <input type="text" id="inputLinkEnd" name="end_date" class="form-control datepickerLink" placeholder="<?php echo __("End on"); ?>" required>
                                    </div>
                                    <hr>
                                    <div class="form-group" style='min-height:340px;'>
                                        <label for="upload"><?php echo __("Event Thumbnail"); ?></label>
                                        <div class="col-xs-8" style='float:right;'>
                                            <div id="croppie"></div>
                                            <center>
                                                <a id="upload-btn" class="btn btn-primary youmake-button"><i class="fa fa-upload"></i> <?php echo __("Upload a Photo"); ?></a>
                                            </center>
                                        </div>
                                        <input type="file" id="upload" value="Choose a file" accept="image/*" style="display: none;" />
                                    </div>                                    
                                    <hr>
                                    <div class="form-group" style='margin-top:30px;'>
                                        <span class="fa fa-globe"></span> <?php echo __("Public Transmition"); ?> 
                                        <div class="material-switch pull-right">
                                            <input id="listedEvent" type="checkbox" value="1" <?php echo!empty($event['public']) ? "checked" : ""; ?>/>
                                            <label for="listedEvent" class="label-success"></label> 
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="">
                                <div class="panel-heading">
                                    Preview
                                </div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'>          
                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="">
                                <div class="panel-heading">
                                    Tickets
                                </div>
                                <div class="panel-body" style='background-color:#fff;border-radius:8px;'>          
                                    
                                </div>
                            </div>
                        </div>

                        <div class='col-xs-12' style='text-align:center;margin-bottom:20px;margin-top:25px;'>
                            <button type="button" class="btn btn-success youmake-button" id="btnSaveEvent"><?php echo __("Save Event"); ?></button>                        
                        </div>
                    </div>
                </div>  
                -->
            
                <div id='futureEvent' class='tab-pane fade'>
                    <div class='col-xs-12 bgWhite' style='padding:0px 40px;'>
                                            
                        <div class="page-header">
                            <h1 style='padding-left:0px;margin-left:0px;'>Create new Event</h1>
                            <div class='jumbotron jumbotron-info' style='padding-left:10px;margin-top:0px;margin-bottom:0px;'>
                                Publish a public (or private) stream in the future. This enables you more control, a public event page (where people can subscribe) and a ticketing system to earn money while you are streaming. If the streaming is marked as <i>`Public Transmition`</i> it will be visible for all users in the platform. Below you must set the visible <i>`Title`</i>, <i>`Description`</i> and <i>the event time</i>. You can find the streaming share info and the server information in order to set in your broadcast software. Have fun! 
                            </div>
                        </div>
                        <form role="form" name="editEvent" data-ng-submit="save(editEvent, event)" data-error-sensitive="editEventHeader,editPrices" novalidate="" class="ng-dirty ng-valid-parse ng-invalid ng-invalid-required ng-valid-pattern ng-valid-minlength ng-valid-url ng-valid-min ng-valid-maxlength">

                            <div class="row">
                                <div class="col-sm-8 col-md-8">
                                    <div class="form-group ng-scope" bs-form-error="editEventHeader.displayName">
                                        <label for="displayName">Title</label>
                                        <input data-ng-model="obj.displayName" name="displayName" data-grab-focus="" id="displayName" class="form-control" required="" type="text" maxlength="50">                                        
                                    </div>
                                    <div class="form-group ng-scope" bs-form-error="editEventHeader.shortName">

                                        <label for="shortName">URL</label>
                                        <div class="input-group ng-scope">
                                            <span class="input-group-addon">
                                                <span>/event/</span>
                                            </span>
                                            <input id="shortName" data-ng-model="obj.shortName" class="form-control" required="" pattern="^[A-Za-z0-9]{1,}([-_]*[A-Za-z0-9]+)+$" name="shortName" type="text">
                                            <span class="input-group-addon">
                                                <span>/</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8 col-md-6">
                                    <div class="form-group">
                                        <label for="descriptionEvent"><?php echo __("Short Description"); ?>:</label>
                                        <input class="form-control" id="tagsEvent" value="<?php echo $event['tags'] ?>" placeholder='Max 50 chars - Description in event card' maxlength='50'>
                                    </div>
                                    <div class="form-group">
                                        <label for="descriptionEvent"><?php echo __("Tags"); ?>:</label>
                                        <input class="form-control" id="tagsEvent" value="<?php echo $event['tags'] ?>" placeholder='Comma-separated event princpials tags' maxlength='70'>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="inputLinkStarts"><?php echo __("Starts on"); ?>:</label>
                                        <input type="text" id="inputLinkStarts" name="start_date" class="form-control datepickerLink" placeholder="<?php echo __("Starts on"); ?>" required >
                                    </div>
                                    <div class="form-group">
                                        <label for="inputLinkEnd"><?php echo __("Estimated Duration"); ?>:</label>
                                        <input type="number" min='0' step='0.5' id="inputLinkEnd" name="end_date" class="form-control datepickerLink" placeholder="<?php echo __("Estimated Duration in hours"); ?>" required>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group ng-scope" bs-form-error="editEventHeader.timeZone">
                                        <label for="timeZone">Event time zone</label>
                                        <select class="form-control ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required" id="timeZone" name="timeZone" data-ng-model="obj.geolocation.timeZone" required="" ng-options="tz as tz for tz in timezones">
                                            <option value="?" selected="selected"></option>
                                            <option label="Africa/Abidjan" value="string:Africa/Abidjan">Africa/Abidjan</option>
                                            <option label="Africa/Accra" value="string:Africa/Accra">Africa/Accra</option>
                                            <option label="Africa/Addis_Ababa" value="string:Africa/Addis_Ababa">Africa/Addis_Ababa</option>
                                            <option label="Africa/Algiers" value="string:Africa/Algiers">Africa/Algiers</option>
                                            <option label="Africa/Asmara" value="string:Africa/Asmara">Africa/Asmara</option>
                                            <option label="Africa/Asmera" value="string:Africa/Asmera">Africa/Asmera</option>
                                            <option label="Africa/Bamako" value="string:Africa/Bamako">Africa/Bamako</option>
                                            <option label="Africa/Bangui" value="string:Africa/Bangui">Africa/Bangui</option>
                                            <option label="Africa/Banjul" value="string:Africa/Banjul">Africa/Banjul</option>
                                            <option label="Africa/Bissau" value="string:Africa/Bissau">Africa/Bissau</option>
                                            <option label="Africa/Blantyre" value="string:Africa/Blantyre">Africa/Blantyre</option>
                                            <option label="Africa/Brazzaville" value="string:Africa/Brazzaville">Africa/Brazzaville</option>
                                            <option label="Africa/Bujumbura" value="string:Africa/Bujumbura">Africa/Bujumbura</option>
                                            <option label="Africa/Cairo" value="string:Africa/Cairo">Africa/Cairo</option>
                                            <option label="Africa/Casablanca" value="string:Africa/Casablanca">Africa/Casablanca</option>
                                            <option label="Africa/Ceuta" value="string:Africa/Ceuta">Africa/Ceuta</option>
                                            <option label="Africa/Conakry" value="string:Africa/Conakry">Africa/Conakry</option>
                                            <option label="Africa/Dakar" value="string:Africa/Dakar">Africa/Dakar</option>
                                            <option label="Africa/Dar_es_Salaam" value="string:Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option>
                                            <option label="Africa/Djibouti" value="string:Africa/Djibouti">Africa/Djibouti</option>
                                            <option label="Africa/Douala" value="string:Africa/Douala">Africa/Douala</option>
                                            <option label="Africa/El_Aaiun" value="string:Africa/El_Aaiun">Africa/El_Aaiun</option>
                                            <option label="Africa/Freetown" value="string:Africa/Freetown">Africa/Freetown</option>
                                            <option label="Africa/Gaborone" value="string:Africa/Gaborone">Africa/Gaborone</option>
                                            <option label="Africa/Harare" value="string:Africa/Harare">Africa/Harare</option>
                                            <option label="Africa/Johannesburg" value="string:Africa/Johannesburg">Africa/Johannesburg</option>
                                            <option label="Africa/Juba" value="string:Africa/Juba">Africa/Juba</option>
                                            <option label="Africa/Kampala" value="string:Africa/Kampala">Africa/Kampala</option>
                                            <option label="Africa/Khartoum" value="string:Africa/Khartoum">Africa/Khartoum</option>
                                            <option label="Africa/Kigali" value="string:Africa/Kigali">Africa/Kigali</option>
                                            <option label="Africa/Kinshasa" value="string:Africa/Kinshasa">Africa/Kinshasa</option>
                                            <option label="Africa/Lagos" value="string:Africa/Lagos">Africa/Lagos</option>
                                            <option label="Africa/Libreville" value="string:Africa/Libreville">Africa/Libreville</option>
                                            <option label="Africa/Lome" value="string:Africa/Lome">Africa/Lome</option>
                                            <option label="Africa/Luanda" value="string:Africa/Luanda">Africa/Luanda</option>
                                            <option label="Africa/Lubumbashi" value="string:Africa/Lubumbashi">Africa/Lubumbashi</option>
                                            <option label="Africa/Lusaka" value="string:Africa/Lusaka">Africa/Lusaka</option>
                                            <option label="Africa/Malabo" value="string:Africa/Malabo">Africa/Malabo</option>
                                            <option label="Africa/Maputo" value="string:Africa/Maputo">Africa/Maputo</option>
                                            <option label="Africa/Maseru" value="string:Africa/Maseru">Africa/Maseru</option>
                                            <option label="Africa/Mbabane" value="string:Africa/Mbabane">Africa/Mbabane</option>
                                            <option label="Africa/Mogadishu" value="string:Africa/Mogadishu">Africa/Mogadishu</option>
                                            <option label="Africa/Monrovia" value="string:Africa/Monrovia">Africa/Monrovia</option>
                                            <option label="Africa/Nairobi" value="string:Africa/Nairobi">Africa/Nairobi</option>
                                            <option label="Africa/Ndjamena" value="string:Africa/Ndjamena">Africa/Ndjamena</option>
                                            <option label="Africa/Niamey" value="string:Africa/Niamey">Africa/Niamey</option>
                                            <option label="Africa/Nouakchott" value="string:Africa/Nouakchott">Africa/Nouakchott</option>
                                            <option label="Africa/Ouagadougou" value="string:Africa/Ouagadougou">Africa/Ouagadougou</option>
                                            <option label="Africa/Porto-Novo" value="string:Africa/Porto-Novo">Africa/Porto-Novo</option>
                                            <option label="Africa/Sao_Tome" value="string:Africa/Sao_Tome">Africa/Sao_Tome</option>
                                            <option label="Africa/Timbuktu" value="string:Africa/Timbuktu">Africa/Timbuktu</option>
                                            <option label="Africa/Tripoli" value="string:Africa/Tripoli">Africa/Tripoli</option>
                                            <option label="Africa/Tunis" value="string:Africa/Tunis">Africa/Tunis</option>
                                            <option label="Africa/Windhoek" value="string:Africa/Windhoek">Africa/Windhoek</option>
                                            <option label="America/Adak" value="string:America/Adak">America/Adak</option>
                                            <option label="America/Anchorage" value="string:America/Anchorage">America/Anchorage</option>
                                            <option label="America/Anguilla" value="string:America/Anguilla">America/Anguilla</option>
                                            <option label="America/Antigua" value="string:America/Antigua">America/Antigua</option>
                                            <option label="America/Araguaina" value="string:America/Araguaina">America/Araguaina</option>
                                            <option label="America/Argentina/Buenos_Aires" value="string:America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>
                                            <option label="America/Argentina/Catamarca" value="string:America/Argentina/Catamarca">America/Argentina/Catamarca</option>
                                            <option label="America/Argentina/ComodRivadavia" value="string:America/Argentina/ComodRivadavia">America/Argentina/ComodRivadavia</option>
                                            <option label="America/Argentina/Cordoba" value="string:America/Argentina/Cordoba">America/Argentina/Cordoba</option>
                                            <option label="America/Argentina/Jujuy" value="string:America/Argentina/Jujuy">America/Argentina/Jujuy</option>
                                            <option label="America/Argentina/La_Rioja" value="string:America/Argentina/La_Rioja">America/Argentina/La_Rioja</option>
                                            <option label="America/Argentina/Mendoza" value="string:America/Argentina/Mendoza">America/Argentina/Mendoza</option>
                                            <option label="America/Argentina/Rio_Gallegos" value="string:America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option>
                                            <option label="America/Argentina/Salta" value="string:America/Argentina/Salta">America/Argentina/Salta</option>
                                            <option label="America/Argentina/San_Juan" value="string:America/Argentina/San_Juan">America/Argentina/San_Juan</option>
                                            <option label="America/Argentina/San_Luis" value="string:America/Argentina/San_Luis">America/Argentina/San_Luis</option>
                                            <option label="America/Argentina/Tucuman" value="string:America/Argentina/Tucuman">America/Argentina/Tucuman</option>
                                            <option label="America/Argentina/Ushuaia" value="string:America/Argentina/Ushuaia">America/Argentina/Ushuaia</option>
                                            <option label="America/Aruba" value="string:America/Aruba">America/Aruba</option>
                                            <option label="America/Asuncion" value="string:America/Asuncion">America/Asuncion</option>
                                            <option label="America/Atikokan" value="string:America/Atikokan">America/Atikokan</option>
                                            <option label="America/Atka" value="string:America/Atka">America/Atka</option>
                                            <option label="America/Bahia" value="string:America/Bahia">America/Bahia</option>
                                            <option label="America/Bahia_Banderas" value="string:America/Bahia_Banderas">America/Bahia_Banderas</option>
                                            <option label="America/Barbados" value="string:America/Barbados">America/Barbados</option>
                                            <option label="America/Belem" value="string:America/Belem">America/Belem</option>
                                            <option label="America/Belize" value="string:America/Belize">America/Belize</option>
                                            <option label="America/Blanc-Sablon" value="string:America/Blanc-Sablon">America/Blanc-Sablon</option>
                                            <option label="America/Boa_Vista" value="string:America/Boa_Vista">America/Boa_Vista</option>
                                            <option label="America/Bogota" value="string:America/Bogota">America/Bogota</option>
                                            <option label="America/Boise" value="string:America/Boise">America/Boise</option>
                                            <option label="America/Buenos_Aires" value="string:America/Buenos_Aires">America/Buenos_Aires</option>
                                            <option label="America/Cambridge_Bay" value="string:America/Cambridge_Bay">America/Cambridge_Bay</option>
                                            <option label="America/Campo_Grande" value="string:America/Campo_Grande">America/Campo_Grande</option>
                                            <option label="America/Cancun" value="string:America/Cancun">America/Cancun</option>
                                            <option label="America/Caracas" value="string:America/Caracas">America/Caracas</option>
                                            <option label="America/Catamarca" value="string:America/Catamarca">America/Catamarca</option>
                                            <option label="America/Cayenne" value="string:America/Cayenne">America/Cayenne</option>
                                            <option label="America/Cayman" value="string:America/Cayman">America/Cayman</option>
                                            <option label="America/Chicago" value="string:America/Chicago">America/Chicago</option>
                                            <option label="America/Chihuahua" value="string:America/Chihuahua">America/Chihuahua</option>
                                            <option label="America/Coral_Harbour" value="string:America/Coral_Harbour">America/Coral_Harbour</option>
                                            <option label="America/Cordoba" value="string:America/Cordoba">America/Cordoba</option>
                                            <option label="America/Costa_Rica" value="string:America/Costa_Rica">America/Costa_Rica</option>
                                            <option label="America/Creston" value="string:America/Creston">America/Creston</option>
                                            <option label="America/Cuiaba" value="string:America/Cuiaba">America/Cuiaba</option>
                                            <option label="America/Curacao" value="string:America/Curacao">America/Curacao</option>
                                            <option label="America/Danmarkshavn" value="string:America/Danmarkshavn">America/Danmarkshavn</option>
                                            <option label="America/Dawson" value="string:America/Dawson">America/Dawson</option>
                                            <option label="America/Dawson_Creek" value="string:America/Dawson_Creek">America/Dawson_Creek</option>
                                            <option label="America/Denver" value="string:America/Denver">America/Denver</option>
                                            <option label="America/Detroit" value="string:America/Detroit">America/Detroit</option>
                                            <option label="America/Dominica" value="string:America/Dominica">America/Dominica</option>
                                            <option label="America/Edmonton" value="string:America/Edmonton">America/Edmonton</option>
                                            <option label="America/Eirunepe" value="string:America/Eirunepe">America/Eirunepe</option>
                                            <option label="America/El_Salvador" value="string:America/El_Salvador">America/El_Salvador</option>
                                            <option label="America/Ensenada" value="string:America/Ensenada">America/Ensenada</option>
                                            <option label="America/Fort_Nelson" value="string:America/Fort_Nelson">America/Fort_Nelson</option>
                                            <option label="America/Fort_Wayne" value="string:America/Fort_Wayne">America/Fort_Wayne</option>
                                            <option label="America/Fortaleza" value="string:America/Fortaleza">America/Fortaleza</option>
                                            <option label="America/Glace_Bay" value="string:America/Glace_Bay">America/Glace_Bay</option>
                                            <option label="America/Godthab" value="string:America/Godthab">America/Godthab</option>
                                            <option label="America/Goose_Bay" value="string:America/Goose_Bay">America/Goose_Bay</option>
                                            <option label="America/Grand_Turk" value="string:America/Grand_Turk">America/Grand_Turk</option>
                                            <option label="America/Grenada" value="string:America/Grenada">America/Grenada</option>
                                            <option label="America/Guadeloupe" value="string:America/Guadeloupe">America/Guadeloupe</option>
                                            <option label="America/Guatemala" value="string:America/Guatemala">America/Guatemala</option>
                                            <option label="America/Guayaquil" value="string:America/Guayaquil">America/Guayaquil</option>
                                            <option label="America/Guyana" value="string:America/Guyana">America/Guyana</option>
                                            <option label="America/Halifax" value="string:America/Halifax">America/Halifax</option>
                                            <option label="America/Havana" value="string:America/Havana">America/Havana</option>
                                            <option label="America/Hermosillo" value="string:America/Hermosillo">America/Hermosillo</option>
                                            <option label="America/Indiana/Indianapolis" value="string:America/Indiana/Indianapolis">America/Indiana/Indianapolis</option>
                                            <option label="America/Indiana/Knox" value="string:America/Indiana/Knox">America/Indiana/Knox</option>
                                            <option label="America/Indiana/Marengo" value="string:America/Indiana/Marengo">America/Indiana/Marengo</option>
                                            <option label="America/Indiana/Petersburg" value="string:America/Indiana/Petersburg">America/Indiana/Petersburg</option>
                                            <option label="America/Indiana/Tell_City" value="string:America/Indiana/Tell_City">America/Indiana/Tell_City</option>
                                            <option label="America/Indiana/Vevay" value="string:America/Indiana/Vevay">America/Indiana/Vevay</option>
                                            <option label="America/Indiana/Vincennes" value="string:America/Indiana/Vincennes">America/Indiana/Vincennes</option>
                                            <option label="America/Indiana/Winamac" value="string:America/Indiana/Winamac">America/Indiana/Winamac</option>
                                            <option label="America/Indianapolis" value="string:America/Indianapolis">America/Indianapolis</option>
                                            <option label="America/Inuvik" value="string:America/Inuvik">America/Inuvik</option>
                                            <option label="America/Iqaluit" value="string:America/Iqaluit">America/Iqaluit</option>
                                            <option label="America/Jamaica" value="string:America/Jamaica">America/Jamaica</option>
                                            <option label="America/Jujuy" value="string:America/Jujuy">America/Jujuy</option>
                                            <option label="America/Juneau" value="string:America/Juneau">America/Juneau</option>
                                            <option label="America/Kentucky/Louisville" value="string:America/Kentucky/Louisville">America/Kentucky/Louisville</option>
                                            <option label="America/Kentucky/Monticello" value="string:America/Kentucky/Monticello">America/Kentucky/Monticello</option>
                                            <option label="America/Knox_IN" value="string:America/Knox_IN">America/Knox_IN</option>
                                            <option label="America/Kralendijk" value="string:America/Kralendijk">America/Kralendijk</option>
                                            <option label="America/La_Paz" value="string:America/La_Paz">America/La_Paz</option>
                                            <option label="America/Lima" value="string:America/Lima">America/Lima</option>
                                            <option label="America/Los_Angeles" value="string:America/Los_Angeles">America/Los_Angeles</option>
                                            <option label="America/Louisville" value="string:America/Louisville">America/Louisville</option>
                                            <option label="America/Lower_Princes" value="string:America/Lower_Princes">America/Lower_Princes</option>
                                            <option label="America/Maceio" value="string:America/Maceio">America/Maceio</option>
                                            <option label="America/Managua" value="string:America/Managua">America/Managua</option>
                                            <option label="America/Manaus" value="string:America/Manaus">America/Manaus</option>
                                            <option label="America/Marigot" value="string:America/Marigot">America/Marigot</option>
                                            <option label="America/Martinique" value="string:America/Martinique">America/Martinique</option>
                                            <option label="America/Matamoros" value="string:America/Matamoros">America/Matamoros</option>
                                            <option label="America/Mazatlan" value="string:America/Mazatlan">America/Mazatlan</option>
                                            <option label="America/Mendoza" value="string:America/Mendoza">America/Mendoza</option>
                                            <option label="America/Menominee" value="string:America/Menominee">America/Menominee</option>
                                            <option label="America/Merida" value="string:America/Merida">America/Merida</option>
                                            <option label="America/Metlakatla" value="string:America/Metlakatla">America/Metlakatla</option>
                                            <option label="America/Mexico_City" value="string:America/Mexico_City">America/Mexico_City</option>
                                            <option label="America/Miquelon" value="string:America/Miquelon">America/Miquelon</option>
                                            <option label="America/Moncton" value="string:America/Moncton">America/Moncton</option>
                                            <option label="America/Monterrey" value="string:America/Monterrey">America/Monterrey</option>
                                            <option label="America/Montevideo" value="string:America/Montevideo">America/Montevideo</option>
                                            <option label="America/Montreal" value="string:America/Montreal">America/Montreal</option>
                                            <option label="America/Montserrat" value="string:America/Montserrat">America/Montserrat</option>
                                            <option label="America/Nassau" value="string:America/Nassau">America/Nassau</option>
                                            <option label="America/New_York" value="string:America/New_York">America/New_York</option>
                                            <option label="America/Nipigon" value="string:America/Nipigon">America/Nipigon</option>
                                            <option label="America/Nome" value="string:America/Nome">America/Nome</option>
                                            <option label="America/Noronha" value="string:America/Noronha">America/Noronha</option>
                                            <option label="America/North_Dakota/Beulah" value="string:America/North_Dakota/Beulah">America/North_Dakota/Beulah</option>
                                            <option label="America/North_Dakota/Center" value="string:America/North_Dakota/Center">America/North_Dakota/Center</option>
                                            <option label="America/North_Dakota/New_Salem" value="string:America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option>
                                            <option label="America/Ojinaga" value="string:America/Ojinaga">America/Ojinaga</option>
                                            <option label="America/Panama" value="string:America/Panama">America/Panama</option>
                                            <option label="America/Pangnirtung" value="string:America/Pangnirtung">America/Pangnirtung</option>
                                            <option label="America/Paramaribo" value="string:America/Paramaribo">America/Paramaribo</option>
                                            <option label="America/Phoenix" value="string:America/Phoenix">America/Phoenix</option>
                                            <option label="America/Port-au-Prince" value="string:America/Port-au-Prince">America/Port-au-Prince</option>
                                            <option label="America/Port_of_Spain" value="string:America/Port_of_Spain">America/Port_of_Spain</option>
                                            <option label="America/Porto_Acre" value="string:America/Porto_Acre">America/Porto_Acre</option>
                                            <option label="America/Porto_Velho" value="string:America/Porto_Velho">America/Porto_Velho</option>
                                            <option label="America/Puerto_Rico" value="string:America/Puerto_Rico">America/Puerto_Rico</option>
                                            <option label="America/Punta_Arenas" value="string:America/Punta_Arenas">America/Punta_Arenas</option>
                                            <option label="America/Rainy_River" value="string:America/Rainy_River">America/Rainy_River</option>
                                            <option label="America/Rankin_Inlet" value="string:America/Rankin_Inlet">America/Rankin_Inlet</option>
                                            <option label="America/Recife" value="string:America/Recife">America/Recife</option>
                                            <option label="America/Regina" value="string:America/Regina">America/Regina</option>
                                            <option label="America/Resolute" value="string:America/Resolute">America/Resolute</option>
                                            <option label="America/Rio_Branco" value="string:America/Rio_Branco">America/Rio_Branco</option>
                                            <option label="America/Rosario" value="string:America/Rosario">America/Rosario</option>
                                            <option label="America/Santa_Isabel" value="string:America/Santa_Isabel">America/Santa_Isabel</option>
                                            <option label="America/Santarem" value="string:America/Santarem">America/Santarem</option>
                                            <option label="America/Santiago" value="string:America/Santiago">America/Santiago</option>
                                            <option label="America/Santo_Domingo" value="string:America/Santo_Domingo">America/Santo_Domingo</option>
                                            <option label="America/Sao_Paulo" value="string:America/Sao_Paulo">America/Sao_Paulo</option>
                                            <option label="America/Scoresbysund" value="string:America/Scoresbysund">America/Scoresbysund</option>
                                            <option label="America/Shiprock" value="string:America/Shiprock">America/Shiprock</option>
                                            <option label="America/Sitka" value="string:America/Sitka">America/Sitka</option>
                                            <option label="America/St_Barthelemy" value="string:America/St_Barthelemy">America/St_Barthelemy</option>
                                            <option label="America/St_Johns" value="string:America/St_Johns">America/St_Johns</option>
                                            <option label="America/St_Kitts" value="string:America/St_Kitts">America/St_Kitts</option>
                                            <option label="America/St_Lucia" value="string:America/St_Lucia">America/St_Lucia</option>
                                            <option label="America/St_Thomas" value="string:America/St_Thomas">America/St_Thomas</option>
                                            <option label="America/St_Vincent" value="string:America/St_Vincent">America/St_Vincent</option>
                                            <option label="America/Swift_Current" value="string:America/Swift_Current">America/Swift_Current</option>
                                            <option label="America/Tegucigalpa" value="string:America/Tegucigalpa">America/Tegucigalpa</option>
                                            <option label="America/Thule" value="string:America/Thule">America/Thule</option>
                                            <option label="America/Thunder_Bay" value="string:America/Thunder_Bay">America/Thunder_Bay</option>
                                            <option label="America/Tijuana" value="string:America/Tijuana">America/Tijuana</option>
                                            <option label="America/Toronto" value="string:America/Toronto">America/Toronto</option>
                                            <option label="America/Tortola" value="string:America/Tortola">America/Tortola</option>
                                            <option label="America/Vancouver" value="string:America/Vancouver">America/Vancouver</option>
                                            <option label="America/Virgin" value="string:America/Virgin">America/Virgin</option>
                                            <option label="America/Whitehorse" value="string:America/Whitehorse">America/Whitehorse</option>
                                            <option label="America/Winnipeg" value="string:America/Winnipeg">America/Winnipeg</option>
                                            <option label="America/Yakutat" value="string:America/Yakutat">America/Yakutat</option>
                                            <option label="America/Yellowknife" value="string:America/Yellowknife">America/Yellowknife</option>
                                            <option label="Antarctica/Casey" value="string:Antarctica/Casey">Antarctica/Casey</option>
                                            <option label="Antarctica/Davis" value="string:Antarctica/Davis">Antarctica/Davis</option>
                                            <option label="Antarctica/DumontDUrville" value="string:Antarctica/DumontDUrville">Antarctica/DumontDUrville</option>
                                            <option label="Antarctica/Macquarie" value="string:Antarctica/Macquarie">Antarctica/Macquarie</option>
                                            <option label="Antarctica/Mawson" value="string:Antarctica/Mawson">Antarctica/Mawson</option>
                                            <option label="Antarctica/McMurdo" value="string:Antarctica/McMurdo">Antarctica/McMurdo</option>
                                            <option label="Antarctica/Palmer" value="string:Antarctica/Palmer">Antarctica/Palmer</option>
                                            <option label="Antarctica/Rothera" value="string:Antarctica/Rothera">Antarctica/Rothera</option>
                                            <option label="Antarctica/South_Pole" value="string:Antarctica/South_Pole">Antarctica/South_Pole</option>
                                            <option label="Antarctica/Syowa" value="string:Antarctica/Syowa">Antarctica/Syowa</option>
                                            <option label="Antarctica/Troll" value="string:Antarctica/Troll">Antarctica/Troll</option>
                                            <option label="Antarctica/Vostok" value="string:Antarctica/Vostok">Antarctica/Vostok</option>
                                            <option label="Arctic/Longyearbyen" value="string:Arctic/Longyearbyen">Arctic/Longyearbyen</option>
                                            <option label="Asia/Aden" value="string:Asia/Aden">Asia/Aden</option>
                                            <option label="Asia/Almaty" value="string:Asia/Almaty">Asia/Almaty</option>
                                            <option label="Asia/Amman" value="string:Asia/Amman">Asia/Amman</option>
                                            <option label="Asia/Anadyr" value="string:Asia/Anadyr">Asia/Anadyr</option>
                                            <option label="Asia/Aqtau" value="string:Asia/Aqtau">Asia/Aqtau</option>
                                            <option label="Asia/Aqtobe" value="string:Asia/Aqtobe">Asia/Aqtobe</option>
                                            <option label="Asia/Ashgabat" value="string:Asia/Ashgabat">Asia/Ashgabat</option>
                                            <option label="Asia/Ashkhabad" value="string:Asia/Ashkhabad">Asia/Ashkhabad</option>
                                            <option label="Asia/Atyrau" value="string:Asia/Atyrau">Asia/Atyrau</option>
                                            <option label="Asia/Baghdad" value="string:Asia/Baghdad">Asia/Baghdad</option>
                                            <option label="Asia/Bahrain" value="string:Asia/Bahrain">Asia/Bahrain</option>
                                            <option label="Asia/Baku" value="string:Asia/Baku">Asia/Baku</option>
                                            <option label="Asia/Bangkok" value="string:Asia/Bangkok">Asia/Bangkok</option>
                                            <option label="Asia/Barnaul" value="string:Asia/Barnaul">Asia/Barnaul</option>
                                            <option label="Asia/Beirut" value="string:Asia/Beirut">Asia/Beirut</option>
                                            <option label="Asia/Bishkek" value="string:Asia/Bishkek">Asia/Bishkek</option>
                                            <option label="Asia/Brunei" value="string:Asia/Brunei">Asia/Brunei</option>
                                            <option label="Asia/Calcutta" value="string:Asia/Calcutta">Asia/Calcutta</option>
                                            <option label="Asia/Chita" value="string:Asia/Chita">Asia/Chita</option>
                                            <option label="Asia/Choibalsan" value="string:Asia/Choibalsan">Asia/Choibalsan</option>
                                            <option label="Asia/Chongqing" value="string:Asia/Chongqing">Asia/Chongqing</option>
                                            <option label="Asia/Chungking" value="string:Asia/Chungking">Asia/Chungking</option>
                                            <option label="Asia/Colombo" value="string:Asia/Colombo">Asia/Colombo</option>
                                            <option label="Asia/Dacca" value="string:Asia/Dacca">Asia/Dacca</option>
                                            <option label="Asia/Damascus" value="string:Asia/Damascus">Asia/Damascus</option>
                                            <option label="Asia/Dhaka" value="string:Asia/Dhaka">Asia/Dhaka</option>
                                            <option label="Asia/Dili" value="string:Asia/Dili">Asia/Dili</option>
                                            <option label="Asia/Dubai" value="string:Asia/Dubai">Asia/Dubai</option>
                                            <option label="Asia/Dushanbe" value="string:Asia/Dushanbe">Asia/Dushanbe</option>
                                            <option label="Asia/Famagusta" value="string:Asia/Famagusta">Asia/Famagusta</option>
                                            <option label="Asia/Gaza" value="string:Asia/Gaza">Asia/Gaza</option>
                                            <option label="Asia/Harbin" value="string:Asia/Harbin">Asia/Harbin</option>
                                            <option label="Asia/Hebron" value="string:Asia/Hebron">Asia/Hebron</option>
                                            <option label="Asia/Ho_Chi_Minh" value="string:Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option>
                                            <option label="Asia/Hong_Kong" value="string:Asia/Hong_Kong">Asia/Hong_Kong</option>
                                            <option label="Asia/Hovd" value="string:Asia/Hovd">Asia/Hovd</option>
                                            <option label="Asia/Irkutsk" value="string:Asia/Irkutsk">Asia/Irkutsk</option>
                                            <option label="Asia/Istanbul" value="string:Asia/Istanbul">Asia/Istanbul</option>
                                            <option label="Asia/Jakarta" value="string:Asia/Jakarta">Asia/Jakarta</option>
                                            <option label="Asia/Jayapura" value="string:Asia/Jayapura">Asia/Jayapura</option>
                                            <option label="Asia/Jerusalem" value="string:Asia/Jerusalem">Asia/Jerusalem</option>
                                            <option label="Asia/Kabul" value="string:Asia/Kabul">Asia/Kabul</option>
                                            <option label="Asia/Kamchatka" value="string:Asia/Kamchatka">Asia/Kamchatka</option>
                                            <option label="Asia/Karachi" value="string:Asia/Karachi">Asia/Karachi</option>
                                            <option label="Asia/Kashgar" value="string:Asia/Kashgar">Asia/Kashgar</option>
                                            <option label="Asia/Kathmandu" value="string:Asia/Kathmandu">Asia/Kathmandu</option>
                                            <option label="Asia/Katmandu" value="string:Asia/Katmandu">Asia/Katmandu</option>
                                            <option label="Asia/Khandyga" value="string:Asia/Khandyga">Asia/Khandyga</option>
                                            <option label="Asia/Kolkata" value="string:Asia/Kolkata">Asia/Kolkata</option>
                                            <option label="Asia/Krasnoyarsk" value="string:Asia/Krasnoyarsk">Asia/Krasnoyarsk</option>
                                            <option label="Asia/Kuala_Lumpur" value="string:Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option>
                                            <option label="Asia/Kuching" value="string:Asia/Kuching">Asia/Kuching</option>
                                            <option label="Asia/Kuwait" value="string:Asia/Kuwait">Asia/Kuwait</option>
                                            <option label="Asia/Macao" value="string:Asia/Macao">Asia/Macao</option>
                                            <option label="Asia/Macau" value="string:Asia/Macau">Asia/Macau</option>
                                            <option label="Asia/Magadan" value="string:Asia/Magadan">Asia/Magadan</option>
                                            <option label="Asia/Makassar" value="string:Asia/Makassar">Asia/Makassar</option>
                                            <option label="Asia/Manila" value="string:Asia/Manila">Asia/Manila</option>
                                            <option label="Asia/Muscat" value="string:Asia/Muscat">Asia/Muscat</option>
                                            <option label="Asia/Nicosia" value="string:Asia/Nicosia">Asia/Nicosia</option>
                                            <option label="Asia/Novokuznetsk" value="string:Asia/Novokuznetsk">Asia/Novokuznetsk</option>
                                            <option label="Asia/Novosibirsk" value="string:Asia/Novosibirsk">Asia/Novosibirsk</option>
                                            <option label="Asia/Omsk" value="string:Asia/Omsk">Asia/Omsk</option>
                                            <option label="Asia/Oral" value="string:Asia/Oral">Asia/Oral</option>
                                            <option label="Asia/Phnom_Penh" value="string:Asia/Phnom_Penh">Asia/Phnom_Penh</option>
                                            <option label="Asia/Pontianak" value="string:Asia/Pontianak">Asia/Pontianak</option>
                                            <option label="Asia/Pyongyang" value="string:Asia/Pyongyang">Asia/Pyongyang</option>
                                            <option label="Asia/Qatar" value="string:Asia/Qatar">Asia/Qatar</option>
                                            <option label="Asia/Qyzylorda" value="string:Asia/Qyzylorda">Asia/Qyzylorda</option>
                                            <option label="Asia/Rangoon" value="string:Asia/Rangoon">Asia/Rangoon</option>
                                            <option label="Asia/Riyadh" value="string:Asia/Riyadh">Asia/Riyadh</option>
                                            <option label="Asia/Saigon" value="string:Asia/Saigon">Asia/Saigon</option>
                                            <option label="Asia/Sakhalin" value="string:Asia/Sakhalin">Asia/Sakhalin</option>
                                            <option label="Asia/Samarkand" value="string:Asia/Samarkand">Asia/Samarkand</option>
                                            <option label="Asia/Seoul" value="string:Asia/Seoul">Asia/Seoul</option>
                                            <option label="Asia/Shanghai" value="string:Asia/Shanghai">Asia/Shanghai</option>
                                            <option label="Asia/Singapore" value="string:Asia/Singapore">Asia/Singapore</option>
                                            <option label="Asia/Srednekolymsk" value="string:Asia/Srednekolymsk">Asia/Srednekolymsk</option>
                                            <option label="Asia/Taipei" value="string:Asia/Taipei">Asia/Taipei</option>
                                            <option label="Asia/Tashkent" value="string:Asia/Tashkent">Asia/Tashkent</option>
                                            <option label="Asia/Tbilisi" value="string:Asia/Tbilisi">Asia/Tbilisi</option>
                                            <option label="Asia/Tehran" value="string:Asia/Tehran">Asia/Tehran</option>
                                            <option label="Asia/Tel_Aviv" value="string:Asia/Tel_Aviv">Asia/Tel_Aviv</option>
                                            <option label="Asia/Thimbu" value="string:Asia/Thimbu">Asia/Thimbu</option>
                                            <option label="Asia/Thimphu" value="string:Asia/Thimphu">Asia/Thimphu</option>
                                            <option label="Asia/Tokyo" value="string:Asia/Tokyo">Asia/Tokyo</option>
                                            <option label="Asia/Tomsk" value="string:Asia/Tomsk">Asia/Tomsk</option>
                                            <option label="Asia/Ujung_Pandang" value="string:Asia/Ujung_Pandang">Asia/Ujung_Pandang</option>
                                            <option label="Asia/Ulaanbaatar" value="string:Asia/Ulaanbaatar">Asia/Ulaanbaatar</option>
                                            <option label="Asia/Ulan_Bator" value="string:Asia/Ulan_Bator">Asia/Ulan_Bator</option>
                                            <option label="Asia/Urumqi" value="string:Asia/Urumqi">Asia/Urumqi</option>
                                            <option label="Asia/Ust-Nera" value="string:Asia/Ust-Nera">Asia/Ust-Nera</option>
                                            <option label="Asia/Vientiane" value="string:Asia/Vientiane">Asia/Vientiane</option>
                                            <option label="Asia/Vladivostok" value="string:Asia/Vladivostok">Asia/Vladivostok</option>
                                            <option label="Asia/Yakutsk" value="string:Asia/Yakutsk">Asia/Yakutsk</option>
                                            <option label="Asia/Yangon" value="string:Asia/Yangon">Asia/Yangon</option>
                                            <option label="Asia/Yekaterinburg" value="string:Asia/Yekaterinburg">Asia/Yekaterinburg</option>
                                            <option label="Asia/Yerevan" value="string:Asia/Yerevan">Asia/Yerevan</option>
                                            <option label="Atlantic/Azores" value="string:Atlantic/Azores">Atlantic/Azores</option>
                                            <option label="Atlantic/Bermuda" value="string:Atlantic/Bermuda">Atlantic/Bermuda</option>
                                            <option label="Atlantic/Canary" value="string:Atlantic/Canary">Atlantic/Canary</option>
                                            <option label="Atlantic/Cape_Verde" value="string:Atlantic/Cape_Verde">Atlantic/Cape_Verde</option>
                                            <option label="Atlantic/Faeroe" value="string:Atlantic/Faeroe">Atlantic/Faeroe</option>
                                            <option label="Atlantic/Faroe" value="string:Atlantic/Faroe">Atlantic/Faroe</option>
                                            <option label="Atlantic/Jan_Mayen" value="string:Atlantic/Jan_Mayen">Atlantic/Jan_Mayen</option>
                                            <option label="Atlantic/Madeira" value="string:Atlantic/Madeira">Atlantic/Madeira</option>
                                            <option label="Atlantic/Reykjavik" value="string:Atlantic/Reykjavik">Atlantic/Reykjavik</option>
                                            <option label="Atlantic/South_Georgia" value="string:Atlantic/South_Georgia">Atlantic/South_Georgia</option>
                                            <option label="Atlantic/St_Helena" value="string:Atlantic/St_Helena">Atlantic/St_Helena</option>
                                            <option label="Atlantic/Stanley" value="string:Atlantic/Stanley">Atlantic/Stanley</option>
                                            <option label="Australia/ACT" value="string:Australia/ACT">Australia/ACT</option>
                                            <option label="Australia/Adelaide" value="string:Australia/Adelaide">Australia/Adelaide</option>
                                            <option label="Australia/Brisbane" value="string:Australia/Brisbane">Australia/Brisbane</option>
                                            <option label="Australia/Broken_Hill" value="string:Australia/Broken_Hill">Australia/Broken_Hill</option>
                                            <option label="Australia/Canberra" value="string:Australia/Canberra">Australia/Canberra</option>
                                            <option label="Australia/Currie" value="string:Australia/Currie">Australia/Currie</option>
                                            <option label="Australia/Darwin" value="string:Australia/Darwin">Australia/Darwin</option>
                                            <option label="Australia/Eucla" value="string:Australia/Eucla">Australia/Eucla</option>
                                            <option label="Australia/Hobart" value="string:Australia/Hobart">Australia/Hobart</option>
                                            <option label="Australia/LHI" value="string:Australia/LHI">Australia/LHI</option>
                                            <option label="Australia/Lindeman" value="string:Australia/Lindeman">Australia/Lindeman</option>
                                            <option label="Australia/Lord_Howe" value="string:Australia/Lord_Howe">Australia/Lord_Howe</option>
                                            <option label="Australia/Melbourne" value="string:Australia/Melbourne">Australia/Melbourne</option>
                                            <option label="Australia/NSW" value="string:Australia/NSW">Australia/NSW</option>
                                            <option label="Australia/North" value="string:Australia/North">Australia/North</option>
                                            <option label="Australia/Perth" value="string:Australia/Perth">Australia/Perth</option>
                                            <option label="Australia/Queensland" value="string:Australia/Queensland">Australia/Queensland</option>
                                            <option label="Australia/South" value="string:Australia/South">Australia/South</option>
                                            <option label="Australia/Sydney" value="string:Australia/Sydney">Australia/Sydney</option>
                                            <option label="Australia/Tasmania" value="string:Australia/Tasmania">Australia/Tasmania</option>
                                            <option label="Australia/Victoria" value="string:Australia/Victoria">Australia/Victoria</option>
                                            <option label="Australia/West" value="string:Australia/West">Australia/West</option>
                                            <option label="Australia/Yancowinna" value="string:Australia/Yancowinna">Australia/Yancowinna</option>
                                            <option label="Brazil/Acre" value="string:Brazil/Acre">Brazil/Acre</option>
                                            <option label="Brazil/DeNoronha" value="string:Brazil/DeNoronha">Brazil/DeNoronha</option>
                                            <option label="Brazil/East" value="string:Brazil/East">Brazil/East</option>
                                            <option label="Brazil/West" value="string:Brazil/West">Brazil/West</option>
                                            <option label="CET" value="string:CET">CET</option>
                                            <option label="CST6CDT" value="string:CST6CDT">CST6CDT</option>
                                            <option label="Canada/Atlantic" value="string:Canada/Atlantic">Canada/Atlantic</option>
                                            <option label="Canada/Central" value="string:Canada/Central">Canada/Central</option>
                                            <option label="Canada/Eastern" value="string:Canada/Eastern">Canada/Eastern</option>
                                            <option label="Canada/Mountain" value="string:Canada/Mountain">Canada/Mountain</option>
                                            <option label="Canada/Newfoundland" value="string:Canada/Newfoundland">Canada/Newfoundland</option>
                                            <option label="Canada/Pacific" value="string:Canada/Pacific">Canada/Pacific</option>
                                            <option label="Canada/Saskatchewan" value="string:Canada/Saskatchewan">Canada/Saskatchewan</option>
                                            <option label="Canada/Yukon" value="string:Canada/Yukon">Canada/Yukon</option>
                                            <option label="Chile/Continental" value="string:Chile/Continental">Chile/Continental</option>
                                            <option label="Chile/EasterIsland" value="string:Chile/EasterIsland">Chile/EasterIsland</option>
                                            <option label="Cuba" value="string:Cuba">Cuba</option>
                                            <option label="EET" value="string:EET">EET</option>
                                            <option label="EST5EDT" value="string:EST5EDT">EST5EDT</option>
                                            <option label="Egypt" value="string:Egypt">Egypt</option>
                                            <option label="Eire" value="string:Eire">Eire</option>
                                            <option label="Etc/GMT" value="string:Etc/GMT">Etc/GMT</option>
                                            <option label="Etc/GMT+0" value="string:Etc/GMT+0">Etc/GMT+0</option>
                                            <option label="Etc/GMT+1" value="string:Etc/GMT+1">Etc/GMT+1</option>
                                            <option label="Etc/GMT+10" value="string:Etc/GMT+10">Etc/GMT+10</option>
                                            <option label="Etc/GMT+11" value="string:Etc/GMT+11">Etc/GMT+11</option>
                                            <option label="Etc/GMT+12" value="string:Etc/GMT+12">Etc/GMT+12</option>
                                            <option label="Etc/GMT+2" value="string:Etc/GMT+2">Etc/GMT+2</option>
                                            <option label="Etc/GMT+3" value="string:Etc/GMT+3">Etc/GMT+3</option>
                                            <option label="Etc/GMT+4" value="string:Etc/GMT+4">Etc/GMT+4</option>
                                            <option label="Etc/GMT+5" value="string:Etc/GMT+5">Etc/GMT+5</option>
                                            <option label="Etc/GMT+6" value="string:Etc/GMT+6">Etc/GMT+6</option>
                                            <option label="Etc/GMT+7" value="string:Etc/GMT+7">Etc/GMT+7</option>
                                            <option label="Etc/GMT+8" value="string:Etc/GMT+8">Etc/GMT+8</option>
                                            <option label="Etc/GMT+9" value="string:Etc/GMT+9">Etc/GMT+9</option>
                                            <option label="Etc/GMT-0" value="string:Etc/GMT-0">Etc/GMT-0</option>
                                            <option label="Etc/GMT-1" value="string:Etc/GMT-1">Etc/GMT-1</option>
                                            <option label="Etc/GMT-10" value="string:Etc/GMT-10">Etc/GMT-10</option>
                                            <option label="Etc/GMT-11" value="string:Etc/GMT-11">Etc/GMT-11</option>
                                            <option label="Etc/GMT-12" value="string:Etc/GMT-12">Etc/GMT-12</option>
                                            <option label="Etc/GMT-13" value="string:Etc/GMT-13">Etc/GMT-13</option>
                                            <option label="Etc/GMT-14" value="string:Etc/GMT-14">Etc/GMT-14</option>
                                            <option label="Etc/GMT-2" value="string:Etc/GMT-2">Etc/GMT-2</option>
                                            <option label="Etc/GMT-3" value="string:Etc/GMT-3">Etc/GMT-3</option>
                                            <option label="Etc/GMT-4" value="string:Etc/GMT-4">Etc/GMT-4</option>
                                            <option label="Etc/GMT-5" value="string:Etc/GMT-5">Etc/GMT-5</option>
                                            <option label="Etc/GMT-6" value="string:Etc/GMT-6">Etc/GMT-6</option>
                                            <option label="Etc/GMT-7" value="string:Etc/GMT-7">Etc/GMT-7</option>
                                            <option label="Etc/GMT-8" value="string:Etc/GMT-8">Etc/GMT-8</option>
                                            <option label="Etc/GMT-9" value="string:Etc/GMT-9">Etc/GMT-9</option>
                                            <option label="Etc/GMT0" value="string:Etc/GMT0">Etc/GMT0</option>
                                            <option label="Etc/Greenwich" value="string:Etc/Greenwich">Etc/Greenwich</option>
                                            <option label="Etc/UCT" value="string:Etc/UCT">Etc/UCT</option>
                                            <option label="Etc/UTC" value="string:Etc/UTC">Etc/UTC</option>
                                            <option label="Etc/Universal" value="string:Etc/Universal">Etc/Universal</option>
                                            <option label="Etc/Zulu" value="string:Etc/Zulu">Etc/Zulu</option>
                                            <option label="Europe/Amsterdam" value="string:Europe/Amsterdam">Europe/Amsterdam</option>
                                            <option label="Europe/Andorra" value="string:Europe/Andorra">Europe/Andorra</option>
                                            <option label="Europe/Astrakhan" value="string:Europe/Astrakhan">Europe/Astrakhan</option>
                                            <option label="Europe/Athens" value="string:Europe/Athens">Europe/Athens</option>
                                            <option label="Europe/Belfast" value="string:Europe/Belfast">Europe/Belfast</option>
                                            <option label="Europe/Belgrade" value="string:Europe/Belgrade">Europe/Belgrade</option>
                                            <option label="Europe/Berlin" value="string:Europe/Berlin">Europe/Berlin</option>
                                            <option label="Europe/Bratislava" value="string:Europe/Bratislava">Europe/Bratislava</option>
                                            <option label="Europe/Brussels" value="string:Europe/Brussels">Europe/Brussels</option>
                                            <option label="Europe/Bucharest" value="string:Europe/Bucharest">Europe/Bucharest</option>
                                            <option label="Europe/Budapest" value="string:Europe/Budapest">Europe/Budapest</option>
                                            <option label="Europe/Busingen" value="string:Europe/Busingen">Europe/Busingen</option>
                                            <option label="Europe/Chisinau" value="string:Europe/Chisinau">Europe/Chisinau</option>
                                            <option label="Europe/Copenhagen" value="string:Europe/Copenhagen">Europe/Copenhagen</option>
                                            <option label="Europe/Dublin" value="string:Europe/Dublin">Europe/Dublin</option>
                                            <option label="Europe/Gibraltar" value="string:Europe/Gibraltar">Europe/Gibraltar</option>
                                            <option label="Europe/Guernsey" value="string:Europe/Guernsey">Europe/Guernsey</option>
                                            <option label="Europe/Helsinki" value="string:Europe/Helsinki">Europe/Helsinki</option>
                                            <option label="Europe/Isle_of_Man" value="string:Europe/Isle_of_Man">Europe/Isle_of_Man</option>
                                            <option label="Europe/Istanbul" value="string:Europe/Istanbul">Europe/Istanbul</option>
                                            <option label="Europe/Jersey" value="string:Europe/Jersey">Europe/Jersey</option>
                                            <option label="Europe/Kaliningrad" value="string:Europe/Kaliningrad">Europe/Kaliningrad</option>
                                            <option label="Europe/Kiev" value="string:Europe/Kiev">Europe/Kiev</option>
                                            <option label="Europe/Kirov" value="string:Europe/Kirov">Europe/Kirov</option>
                                            <option label="Europe/Lisbon" value="string:Europe/Lisbon">Europe/Lisbon</option>
                                            <option label="Europe/Ljubljana" value="string:Europe/Ljubljana">Europe/Ljubljana</option>
                                            <option label="Europe/London" value="string:Europe/London">Europe/London</option>
                                            <option label="Europe/Luxembourg" value="string:Europe/Luxembourg">Europe/Luxembourg</option>
                                            <option label="Europe/Madrid" value="string:Europe/Madrid">Europe/Madrid</option>
                                            <option label="Europe/Malta" value="string:Europe/Malta">Europe/Malta</option>
                                            <option label="Europe/Mariehamn" value="string:Europe/Mariehamn">Europe/Mariehamn</option>
                                            <option label="Europe/Minsk" value="string:Europe/Minsk">Europe/Minsk</option>
                                            <option label="Europe/Monaco" value="string:Europe/Monaco">Europe/Monaco</option>
                                            <option label="Europe/Moscow" value="string:Europe/Moscow">Europe/Moscow</option>
                                            <option label="Europe/Nicosia" value="string:Europe/Nicosia">Europe/Nicosia</option>
                                            <option label="Europe/Oslo" value="string:Europe/Oslo">Europe/Oslo</option>
                                            <option label="Europe/Paris" value="string:Europe/Paris">Europe/Paris</option>
                                            <option label="Europe/Podgorica" value="string:Europe/Podgorica">Europe/Podgorica</option>
                                            <option label="Europe/Prague" value="string:Europe/Prague">Europe/Prague</option>
                                            <option label="Europe/Riga" value="string:Europe/Riga">Europe/Riga</option>
                                            <option label="Europe/Rome" value="string:Europe/Rome">Europe/Rome</option>
                                            <option label="Europe/Samara" value="string:Europe/Samara">Europe/Samara</option>
                                            <option label="Europe/San_Marino" value="string:Europe/San_Marino">Europe/San_Marino</option>
                                            <option label="Europe/Sarajevo" value="string:Europe/Sarajevo">Europe/Sarajevo</option>
                                            <option label="Europe/Saratov" value="string:Europe/Saratov">Europe/Saratov</option>
                                            <option label="Europe/Simferopol" value="string:Europe/Simferopol">Europe/Simferopol</option>
                                            <option label="Europe/Skopje" value="string:Europe/Skopje">Europe/Skopje</option>
                                            <option label="Europe/Sofia" value="string:Europe/Sofia">Europe/Sofia</option>
                                            <option label="Europe/Stockholm" value="string:Europe/Stockholm">Europe/Stockholm</option>
                                            <option label="Europe/Tallinn" value="string:Europe/Tallinn">Europe/Tallinn</option>
                                            <option label="Europe/Tirane" value="string:Europe/Tirane">Europe/Tirane</option>
                                            <option label="Europe/Tiraspol" value="string:Europe/Tiraspol">Europe/Tiraspol</option>
                                            <option label="Europe/Ulyanovsk" value="string:Europe/Ulyanovsk">Europe/Ulyanovsk</option>
                                            <option label="Europe/Uzhgorod" value="string:Europe/Uzhgorod">Europe/Uzhgorod</option>
                                            <option label="Europe/Vaduz" value="string:Europe/Vaduz">Europe/Vaduz</option>
                                            <option label="Europe/Vatican" value="string:Europe/Vatican">Europe/Vatican</option>
                                            <option label="Europe/Vienna" value="string:Europe/Vienna">Europe/Vienna</option>
                                            <option label="Europe/Vilnius" value="string:Europe/Vilnius">Europe/Vilnius</option>
                                            <option label="Europe/Volgograd" value="string:Europe/Volgograd">Europe/Volgograd</option>
                                            <option label="Europe/Warsaw" value="string:Europe/Warsaw">Europe/Warsaw</option>
                                            <option label="Europe/Zagreb" value="string:Europe/Zagreb">Europe/Zagreb</option>
                                            <option label="Europe/Zaporozhye" value="string:Europe/Zaporozhye">Europe/Zaporozhye</option>
                                            <option label="Europe/Zurich" value="string:Europe/Zurich">Europe/Zurich</option>
                                            <option label="GB" value="string:GB">GB</option>
                                            <option label="GB-Eire" value="string:GB-Eire">GB-Eire</option>
                                            <option label="GMT" value="string:GMT">GMT</option>
                                            <option label="GMT0" value="string:GMT0">GMT0</option>
                                            <option label="Greenwich" value="string:Greenwich">Greenwich</option>
                                            <option label="Hongkong" value="string:Hongkong">Hongkong</option>
                                            <option label="Iceland" value="string:Iceland">Iceland</option>
                                            <option label="Indian/Antananarivo" value="string:Indian/Antananarivo">Indian/Antananarivo</option>
                                            <option label="Indian/Chagos" value="string:Indian/Chagos">Indian/Chagos</option>
                                            <option label="Indian/Christmas" value="string:Indian/Christmas">Indian/Christmas</option>
                                            <option label="Indian/Cocos" value="string:Indian/Cocos">Indian/Cocos</option>
                                            <option label="Indian/Comoro" value="string:Indian/Comoro">Indian/Comoro</option>
                                            <option label="Indian/Kerguelen" value="string:Indian/Kerguelen">Indian/Kerguelen</option>
                                            <option label="Indian/Mahe" value="string:Indian/Mahe">Indian/Mahe</option>
                                            <option label="Indian/Maldives" value="string:Indian/Maldives">Indian/Maldives</option>
                                            <option label="Indian/Mauritius" value="string:Indian/Mauritius">Indian/Mauritius</option>
                                            <option label="Indian/Mayotte" value="string:Indian/Mayotte">Indian/Mayotte</option>
                                            <option label="Indian/Reunion" value="string:Indian/Reunion">Indian/Reunion</option>
                                            <option label="Iran" value="string:Iran">Iran</option>
                                            <option label="Israel" value="string:Israel">Israel</option>
                                            <option label="Jamaica" value="string:Jamaica">Jamaica</option>
                                            <option label="Japan" value="string:Japan">Japan</option>
                                            <option label="Kwajalein" value="string:Kwajalein">Kwajalein</option>
                                            <option label="Libya" value="string:Libya">Libya</option>
                                            <option label="MET" value="string:MET">MET</option>
                                            <option label="MST7MDT" value="string:MST7MDT">MST7MDT</option>
                                            <option label="Mexico/BajaNorte" value="string:Mexico/BajaNorte">Mexico/BajaNorte</option>
                                            <option label="Mexico/BajaSur" value="string:Mexico/BajaSur">Mexico/BajaSur</option>
                                            <option label="Mexico/General" value="string:Mexico/General">Mexico/General</option>
                                            <option label="NZ" value="string:NZ">NZ</option>
                                            <option label="NZ-CHAT" value="string:NZ-CHAT">NZ-CHAT</option>
                                            <option label="Navajo" value="string:Navajo">Navajo</option>
                                            <option label="PRC" value="string:PRC">PRC</option>
                                            <option label="PST8PDT" value="string:PST8PDT">PST8PDT</option>
                                            <option label="Pacific/Apia" value="string:Pacific/Apia">Pacific/Apia</option>
                                            <option label="Pacific/Auckland" value="string:Pacific/Auckland">Pacific/Auckland</option>
                                            <option label="Pacific/Bougainville" value="string:Pacific/Bougainville">Pacific/Bougainville</option>
                                            <option label="Pacific/Chatham" value="string:Pacific/Chatham">Pacific/Chatham</option>
                                            <option label="Pacific/Chuuk" value="string:Pacific/Chuuk">Pacific/Chuuk</option>
                                            <option label="Pacific/Easter" value="string:Pacific/Easter">Pacific/Easter</option>
                                            <option label="Pacific/Efate" value="string:Pacific/Efate">Pacific/Efate</option>
                                            <option label="Pacific/Enderbury" value="string:Pacific/Enderbury">Pacific/Enderbury</option>
                                            <option label="Pacific/Fakaofo" value="string:Pacific/Fakaofo">Pacific/Fakaofo</option>
                                            <option label="Pacific/Fiji" value="string:Pacific/Fiji">Pacific/Fiji</option>
                                            <option label="Pacific/Funafuti" value="string:Pacific/Funafuti">Pacific/Funafuti</option>
                                            <option label="Pacific/Galapagos" value="string:Pacific/Galapagos">Pacific/Galapagos</option>
                                            <option label="Pacific/Gambier" value="string:Pacific/Gambier">Pacific/Gambier</option>
                                            <option label="Pacific/Guadalcanal" value="string:Pacific/Guadalcanal">Pacific/Guadalcanal</option>
                                            <option label="Pacific/Guam" value="string:Pacific/Guam">Pacific/Guam</option>
                                            <option label="Pacific/Honolulu" value="string:Pacific/Honolulu">Pacific/Honolulu</option>
                                            <option label="Pacific/Johnston" value="string:Pacific/Johnston">Pacific/Johnston</option>
                                            <option label="Pacific/Kiritimati" value="string:Pacific/Kiritimati">Pacific/Kiritimati</option>
                                            <option label="Pacific/Kosrae" value="string:Pacific/Kosrae">Pacific/Kosrae</option>
                                            <option label="Pacific/Kwajalein" value="string:Pacific/Kwajalein">Pacific/Kwajalein</option>
                                            <option label="Pacific/Majuro" value="string:Pacific/Majuro">Pacific/Majuro</option>
                                            <option label="Pacific/Marquesas" value="string:Pacific/Marquesas">Pacific/Marquesas</option>
                                            <option label="Pacific/Midway" value="string:Pacific/Midway">Pacific/Midway</option>
                                            <option label="Pacific/Nauru" value="string:Pacific/Nauru">Pacific/Nauru</option>
                                            <option label="Pacific/Niue" value="string:Pacific/Niue">Pacific/Niue</option>
                                            <option label="Pacific/Norfolk" value="string:Pacific/Norfolk">Pacific/Norfolk</option>
                                            <option label="Pacific/Noumea" value="string:Pacific/Noumea">Pacific/Noumea</option>
                                            <option label="Pacific/Pago_Pago" value="string:Pacific/Pago_Pago">Pacific/Pago_Pago</option>
                                            <option label="Pacific/Palau" value="string:Pacific/Palau">Pacific/Palau</option>
                                            <option label="Pacific/Pitcairn" value="string:Pacific/Pitcairn">Pacific/Pitcairn</option>
                                            <option label="Pacific/Pohnpei" value="string:Pacific/Pohnpei">Pacific/Pohnpei</option>
                                            <option label="Pacific/Ponape" value="string:Pacific/Ponape">Pacific/Ponape</option>
                                            <option label="Pacific/Port_Moresby" value="string:Pacific/Port_Moresby">Pacific/Port_Moresby</option>
                                            <option label="Pacific/Rarotonga" value="string:Pacific/Rarotonga">Pacific/Rarotonga</option>
                                            <option label="Pacific/Saipan" value="string:Pacific/Saipan">Pacific/Saipan</option>
                                            <option label="Pacific/Samoa" value="string:Pacific/Samoa">Pacific/Samoa</option>
                                            <option label="Pacific/Tahiti" value="string:Pacific/Tahiti">Pacific/Tahiti</option>
                                            <option label="Pacific/Tarawa" value="string:Pacific/Tarawa">Pacific/Tarawa</option>
                                            <option label="Pacific/Tongatapu" value="string:Pacific/Tongatapu">Pacific/Tongatapu</option>
                                            <option label="Pacific/Truk" value="string:Pacific/Truk">Pacific/Truk</option>
                                            <option label="Pacific/Wake" value="string:Pacific/Wake">Pacific/Wake</option>
                                            <option label="Pacific/Wallis" value="string:Pacific/Wallis">Pacific/Wallis</option>
                                            <option label="Pacific/Yap" value="string:Pacific/Yap">Pacific/Yap</option>
                                            <option label="Poland" value="string:Poland">Poland</option>
                                            <option label="Portugal" value="string:Portugal">Portugal</option>
                                            <option label="ROK" value="string:ROK">ROK</option>
                                            <option label="Singapore" value="string:Singapore">Singapore</option>
                                            <option label="SystemV/AST4" value="string:SystemV/AST4">SystemV/AST4</option>
                                            <option label="SystemV/AST4ADT" value="string:SystemV/AST4ADT">SystemV/AST4ADT</option>
                                            <option label="SystemV/CST6" value="string:SystemV/CST6">SystemV/CST6</option>
                                            <option label="SystemV/CST6CDT" value="string:SystemV/CST6CDT">SystemV/CST6CDT</option>
                                            <option label="SystemV/EST5" value="string:SystemV/EST5">SystemV/EST5</option>
                                            <option label="SystemV/EST5EDT" value="string:SystemV/EST5EDT">SystemV/EST5EDT</option>
                                            <option label="SystemV/HST10" value="string:SystemV/HST10">SystemV/HST10</option>
                                            <option label="SystemV/MST7" value="string:SystemV/MST7">SystemV/MST7</option>
                                            <option label="SystemV/MST7MDT" value="string:SystemV/MST7MDT">SystemV/MST7MDT</option>
                                            <option label="SystemV/PST8" value="string:SystemV/PST8">SystemV/PST8</option>
                                            <option label="SystemV/PST8PDT" value="string:SystemV/PST8PDT">SystemV/PST8PDT</option>
                                            <option label="SystemV/YST9" value="string:SystemV/YST9">SystemV/YST9</option>
                                            <option label="SystemV/YST9YDT" value="string:SystemV/YST9YDT">SystemV/YST9YDT</option>
                                            <option label="Turkey" value="string:Turkey">Turkey</option>
                                            <option label="UCT" value="string:UCT">UCT</option>
                                            <option label="US/Alaska" value="string:US/Alaska">US/Alaska</option>
                                            <option label="US/Aleutian" value="string:US/Aleutian">US/Aleutian</option>
                                            <option label="US/Arizona" value="string:US/Arizona">US/Arizona</option>
                                            <option label="US/Central" value="string:US/Central">US/Central</option>
                                            <option label="US/East-Indiana" value="string:US/East-Indiana">US/East-Indiana</option>
                                            <option label="US/Eastern" value="string:US/Eastern">US/Eastern</option>
                                            <option label="US/Hawaii" value="string:US/Hawaii">US/Hawaii</option>
                                            <option label="US/Indiana-Starke" value="string:US/Indiana-Starke">US/Indiana-Starke</option>
                                            <option label="US/Michigan" value="string:US/Michigan">US/Michigan</option>
                                            <option label="US/Mountain" value="string:US/Mountain">US/Mountain</option>
                                            <option label="US/Pacific" value="string:US/Pacific">US/Pacific</option>
                                            <option label="US/Pacific-New" value="string:US/Pacific-New">US/Pacific-New</option>
                                            <option label="US/Samoa" value="string:US/Samoa">US/Samoa</option>
                                            <option label="UTC" value="string:UTC">UTC</option>
                                            <option label="Universal" value="string:Universal">Universal</option>
                                            <option label="W-SU" value="string:W-SU">W-SU</option>
                                            <option label="WET" value="string:WET">WET</option>
                                            <option label="Zulu" value="string:Zulu">Zulu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-md-8">
                                    <div class="form-group ng-scope" bs-form-error="editEventHeader.websiteUrl">
                                        <label for="websiteUrl">Description</label>
                                        <textarea class="form-control" id="description"><?php echo $event['description'] ?></textarea>
                                        <div class="markdown-help text-right">
                                            <img class="markdown-logo" src="<?php echo $global['webSiteRootURL'].'view/img/markdown-logo.svg'; ?>" style='width:15px;'> 
                                            <a href="http://commonmark.org/help/" target="_blank" style='font-size:12px;'>Markdown (CommonMark) supported</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group ng-scope" bs-form-error="editEventHeader.websiteUrl">
                                        <label for="websiteUrl">Website URL</label>
                                        <input data-ng-model="obj.websiteUrl" name="websiteUrl" id="websiteUrl" required="" class="form-control ng-pristine" type="url">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="imageFile">Image</label>
                                        <div id="imageFile" class="drop-file-zone wMarginBottom well" data-accept="image/*" data-ngf-pattern="'image/*'" data-ng-model="droppedFile">
                                            Drop image here or click to upload (Maximum size : 1MB)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <div class="alert alert-danger alert-form ng-invalid ng-scope" style='margin-top:30px;'><i class="fa fa-warning"></i> Event logo is missing!</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style='margin-top:30px;'>
                                        <span class="fa fa-globe"></span> <?php echo __("Public Transmition"); ?> 
                                        <div class="material-switch pull-right">
                                            <input id="listed" type="checkbox" value="1" <?php echo!empty($trasnmition['public']) ? "checked" : ""; ?>/>
                                            <label for="listed" class="label-success"></label> 
                                        </div>                                        
                                    </div>
                                    <span style='font-size:12px;color:#555;'> If private, only people with live transmition link could see the event. </span>
                                </div>
                            </div>

                        <div data-ng-if="isInternal(event)" class="ng-scope">
                            <div class="page-header">
                                <h3><i class="fa fa-ticket-alt" style="padding-right:5px;"></i>Seats and payment info<i class="fa fa-info-circle payment-info-button" style='float:right;'></i></h3>
                            </div>
                            <div class='payment-info' style='font-size:12px;font-weight:300;color:#888;padding:10px;margin-top:10px;margin-bottom:20px;display:none'>
                                <b>How it works?</b>
                                Every time someone buy a ticket (either the predefined ones or the one you speciefied), the amount will be charged to your <i>`youMake`</i> wallet. In both cases, a 15% tax will be charged before.<br />
                                <b>Why this tax</b>
                                This tax is billed in order to be able to run this platform (indie maker). <i>`Premium`</i> users can create <b>masterclasses</b> with only 5% of tax.
                            </div>
                            <div class="form-group" style='padding:0px 30px;'>
                                <label>Ticket price model</label>
                                <div class="col-xs-12">
                                    <div class="radio-inline">
                                        <label>
                                            <input class='hideTickets' name="freeOfCharge" data-ng-model="obj.freeOfCharge" value="0" type="radio" checked> Free of charge*
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input class='showTickets' name="freeOfCharge" data-ng-model="obj.freeOfCharge"  value="1" type="radio"> Entry fee requested*
                                        </label>
                                    </div>
                                    <label class='pull-right' style='font-size:12px;color:#888;font-weight:300;'> *All events has predefined optionals tickets for sell (1$, 5$, 10$ and 20$). If you choose <i>`Entry fee requested`</i> all users must buy your ticket (defined below). You can limit the number of free viewers by creating one ticket to 0$ (i.e the first 100 subscribers free, then <i>X</i>$).</label>
                                </div>
                            </div>
                            <div class="row ticketsRow" style='display:none;clear:both;padding:0px 30px;margin-top:90px;'>
                                <div class="col-sm-3">
                                    <div class="form-group ng-scope" bs-form-error="editEvent.availableSeats">
                                        <label for="availableSeats">Max tickets <span style='font-size:10px;color:#888;font-weight: 300;'>(0 for unlimited)</label>
                                        <input min="0" value='0' data-ng-model="obj.availableSeats" name="availableSeats" id="availableSeats" class="form-control ng-pristine ng-untouched ng-empty ng-valid-min ng-invalid ng-invalid-required" required="" type="number">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ng-scope" bs-form-error="editEvent.regularPrice">
                                        <label for="regularPrice">Regular Price</label>
                                        <input min="0" data-ng-model="obj.regularPrice" name="regularPrice" id="regularPrice" class="form-control ng-pristine ng-untouched ng-empty ng-valid-min ng-invalid ng-invalid-required" required="" type="number">
                                    </div>
                                </div>
                                <div class="col-sm-2 ng-scope">
                                    <div class="form-group ng-scope" bs-form-error="editEvent.currency">
                                        <label for="currency">Currency <i class="fa fa-info-circle" data-uib-tooltip="Some payment options don't support all currencies. Please double check that before activate them"></i></label>
                                        <input value='$' disabled='disabled' autocomplete="off" data-ng-model="obj.currency" name="currency" id="currency" class="form-control ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required ng-valid-pattern" type="text">
                                    </div>
                                </div>
                                <div class="col-sm-3 ng-scope">
                                    <div class="form-group ng-scope" bs-form-error="editEvent.currency">
                                        <label for="endbuying">End Buying Date (early bids)</label>
                                        <input autocomplete="off" data-ng-model="obj.endbuyingdate" name="endbuying" id="endbuying" class="form-control ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required ng-valid-pattern datepickerLink" type="text">
                                    </div>
                                </div>
                                <div class='col-sm-1'>
                                    <button class='youmake-button createNewTicket' style='margin-top:18px;'>+</button>
                                </div>
                            </div>

                            <br class="clearfix" style='margin-bottom:20px;'>

                            <hr>
                            <h3 style='font-size:13px;margin-left:0px;margin-bottom:-30px;'> Cooming soon: </h3>
                            <div style='opacity:0.25;margin-top:-10px;'>
                                <div class="page-header">
                                    <h3><i class="fa fa-info-circle"></i> Attendees' data to collect</h3>
                                    <h5 class="text-muted">Attendees' full name, e-mail and language are collected by default. What else do you need to know about your attendees?</h5>
                                </div>

                                <div ng-init="dropdownOpen = false">
                                    <div class="btn-group dropdown" uib-dropdown="" is-open="dropdownOpen">
                                        <button id="single-button" type="button" class="btn btn-success dropdown-toggle" uib-dropdown-toggle="" aria-haspopup="true" aria-expanded="false">
                                            Add new <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" uib-dropdown-menu="" role="menu" aria-labelledby="single-button">
                                            <li role="menuitem">
                                                <a ng-click="addNewTicketField(event)"><i class="fa fa-file-o"></i> Custom</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> jobTitle</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> phoneNumber</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> company</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> address</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> country</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> notes</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> gender</a>
                                            </li>
                                            <li role="menuitem" ng-repeat="template in dynamicFieldTemplates" class="ng-scope">
                                                <a ng-click="addTicketFieldFromTemplate(event, template)"><i class="fa fa-clone"></i> tShirtSize</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>


                            <!-- start preview -->
                            <h1 class='previewPage' style='margin-top:30px;'> Preview: </h1>
                            <div class='previewPage' id="event_page_wrap" vocab="http://schema.org/" typeof="Event" style='border:1px solid #eee;border-radius:8px;margin:30px;padding:30px;border-radius:3px;'>
                                <?php require_once $global['systemRootPath'] . 'view/custom/eventPage.php'; ?>
                            </div>
                            <!-- end preview -->

                            <div class='row'>
                                <h2> Chat </h2>
                                <app-root preChatForm tenant='1' recipientId='1' projectid='1' userId='1' userEmail='a@a.com' userPassword='1' userFullname='a'></app-root>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/inline.bundle.js"></script>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/polyfills.bundle.js"></script>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/scripts.bundle.js"></script>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/styles.bundle.js"></script>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/vendor.bundle.js"></script>
                                <script type="text/javascript" src="https://chat21-web.firebaseapp.com/main.bundle.js"></script>
                            </div>

                            <hr class="wMarginTop30px">
                            <div class="row">
                                <div class="col-xs-12 col-md-9 col-md-push-3">
                                    <div class="row">
                                        <div class="col-md-4 col-md-push-8 col-xs-12">
                                            <button class="btn btn-lg btn-warning btn-block ng-binding youmake-button" style="margin-bottom: 10px">Save</button>
                                        </div>
                                        <div class="col-md-4 col-md-pull-4 col-xs-12">
                                            <button type="button" class="btn btn-lg btn-default btn-block" style="margin-bottom: 10px">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-md-pull-9">
                                    <button class="btn btn-lg btn-block btn-success previewPageButton" type="button">Preview Event</button>
                                </div>
                            </div>
                        </form>        
                    </div>
                </div><!-- MasterClass -->

            </div><!-- tab Content -->
        </div>
    </div><!-- Container fluid -->


<?php $p->getChat($trasnmition['key']); ?>
<?php 
include $global['systemRootPath'] . 'view/include/footer.php'; 
?>

<script src="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

<script>
    var uploadCrop;    
    var flashvars = {server: "<?php echo $p->getServer(); ?>?p=<?php echo User::getUserPass(); ?>", stream: "<?php echo $trasnmition['key']; ?>"};
    var params = {};
    var attributes = {};

    function readFile(input, crop) {
        if ($(input)[0].files && $(input)[0].files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                crop.croppie('bind', {
                    url: e.target.result
                }).then(function () {
                    console.log('jQuery bind complete');
                });
            }

            reader.readAsDataURL($(input)[0].files[0]);
        } else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }    

    function amIOnline() {
        $.ajax({
            url: '<?php echo $global['webSiteRootURL']; ?>plugin/Live/stats.json.php?checkIfYouOnline',
            data: {"name": "<?php echo $streamName; ?>"},
            type: 'post',
            success: function (response) {
                offLine = true;
                for (i = 0; i < response.applications.length; i++) {
                    if (response.applications[i].key === "<?php echo $trasnmition['key']; ?>") {
                        offLine = false;
                        break;
                    }
                }
                // you online do not show webcam
                if (!offLine) {
                    $('#webcam').find('.alert').text("<?php echo __("You are online now, web cam is disabled"); ?>");
                } else {
                    $('#webcam').find('.alert').text("<?php echo __("You are not online, loading webcam..."); ?>");
                    swfobject.embedSWF("<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/webcam.swf", "webcam", "100%", "100%", "9.0.0", "expressInstall.swf", flashvars, params, attributes);
                }
            }
        });
    }

    function saveStream() {
        modal.showPleaseWait();

        var selectedUserGroups = [];
        $('.userGroups:checked').each(function () {
            selectedUserGroups.push($(this).val());
        });

        $.ajax({
            url: 'saveLive.php',
            data: {
                "title": $('#title').val(),
                "description": $('#description').val(),
                "key": "<?php echo $trasnmition['key']; ?>",
                "listed": $('#listed').is(":checked"),
                "userGroups": selectedUserGroups
            },
            type: 'post',
            success: function (response) {
                modal.hidePleaseWait();
            }
        });
    }

    function saveEvent() {
        console.log('save event!!!');
        /*

        modal.showPleaseWait();

        var selectedUserGroups = [];
        $('.userGroups:checked').each(function () {
            selectedUserGroups.push($(this).val());
        });

        $.ajax({
            url: 'saveLive.php',
            data: {
                "title": $('#title').val(),
                "description": $('#description').val(),
                "key": "<?php echo $trasnmition['key']; ?>",
                "listed": $('#listed').is(":checked"),
                "userGroups": selectedUserGroups
            },
            type: 'post',
            success: function (response) {
                modal.hidePleaseWait();
            }
        });
        */
    }

    $(document).ready(function () {
        $('#upload').on('change', function () {
            readFile(this, uploadCrop);
        });
        $('#upload-btn').on('click', function (ev) {
            $('#upload').trigger("click");
        });
        
        uploadCrop = $('#croppie').croppie({
            url: "",
            enableExif: true,
            enforceBoundary: false,
            mouseWheelZoom: false,
            viewport: {
                width: 150,
                height: 150
            },
            boundary: {
                width: 200,
                height: 200
            }
        });
        setTimeout(function () {
            uploadCrop.croppie('setZoom', 1);
        }, 1000);

        $('#btnSaveEvent').click(function () {
            saveEvent();
        });

        $('.datepickerLink').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true
        });

        $('#btnSaveStream').click(function () {
            saveStream();
        });
        $('#enableWebCam').click(function () {
            amIOnline();
        });

        $('.showTickets').click(function(){
            $('.ticketsRow').fadeIn();
        });

        $('.hideTickets').click(function(){
            $('.ticketsRow').fadeOut();
        });

        $('.payment-info-button').click(function(){
            $('.payment-info').fadeToggle();
        });

        $('#clock').countdown("2020/10/10", function(event) {
          var totalHours = event.offset.totalDays * 24 + event.offset.hours;
          $(this).html(event.strftime(totalHours + ' hr %M min %S sec'));
        });

        $('.previewPageButton').click(function(){
            $('.previewPage').fadeToggle();
        });
    });
</script>

    

</body>
</html>
