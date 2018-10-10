<?php
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/video.php';
require_once $global['systemRootPath'] . 'objects/playlist.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'plugin/Gallery/functions.php';

if (empty($_GET['channelName'])) {
    if (User::isLogged()) {
        $_GET['user_id'] = User::getId();
    } else {
        return false;
    }
}else{
    $user = User::getChannelOwner($_GET['channelName']);
    if(!empty($user)){
        $_GET['user_id'] = $user['id'];
    }else{
        $_GET['user_id'] = $_GET['channelName'];
    }    
}
$user_id = $_GET['user_id'];


$isMyChannel = false;
if (User::isLogged() && $user_id == User::getId()) {
    $isMyChannel = true;
}

$user = new User($user_id);
$_POST['sort']['created'] = "DESC";
$uploadedVideos = Video::getAllVideos("a", $user_id);
unset($_POST['sort']);
$publicOnly = true;
if (User::isLogged() && $user_id == User::getId()) {
    $publicOnly = false;
}
$playlists = PlayList::getAllFromUser($user_id, $publicOnly);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("Channel"); ?></title>
    <?php
    include $global['systemRootPath'] . 'view/include/head.php';
    ?>        
    <link href="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    
    <link rel='stylesheet' type='text/css' href='<?php echo $global['webSiteRootURL']; ?>js/node_modules/fullcalendar/dist/fullcalendar.css' />
    <script type='text/javascript' src='<?php echo $global['webSiteRootURL']; ?>js/node_modules/moment/moment.js'></script>
    <script type='text/javascript' src='<?php echo $global['webSiteRootURL']; ?>js/node_modules/fullcalendar/dist/fullcalendar.js'></script>

    <script>
        /*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
        $.widget.bridge('uibutton', $.ui.button);
        $.widget.bridge('uitooltip', $.ui.tooltip);
    </script>
    <!-- users_id = <?php echo $user_id; ?> -->
    <link href="<?php echo $global['webSiteRootURL']; ?>/plugin/Gallery/style.css" rel="stylesheet" type="text/css"/>
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        .galleryVideo {
            padding-bottom: 10px;
        }
        .badge_xs{
            margin-top:10px;
            padding:3px 6px 3px 6px;
        }
        .badge_xs a{
            color: #fff;
        }
        .badge_twitter{
            background-color: #1DA1F2;
        }
        .badge_facebook{
            background-color: #3B5998;
        }
        .badge_instagram{
            background-color: #262626;
        }
        .badge_website{
            background-color: #bbb;
        }

        .nav-tabs { 
            padding:20px 20px 0px 20px;
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
            border:0px;
            border-bottom:2px solid #3f0c74;
        }

        .nav-tabs > li > a{
            color: #3f0c74b3 !important;
        }

        .nav-tabs > li > a:hover{
            color: #3f0c74 !important;
        }

        .nav-tabs > li.active > a{
            color: #3f0c74 !important;
        }

        .card {
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            border-radius: 5px; /* 5px rounded corners */
        }
        .card:hover {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }
        .card-title{
            font-weight: 600;
            line-height:25px;
            font-size: 13px;
        }
        .card-text{
            font-size: 11px;
        }
        .card-img-top{
            height: 150px;
        }
    </style>
</head>

<body>
    <?php
    include $global['systemRootPath'] . 'view/include/navbar.php';
    ?>

    <div class="container-fluid">
        <div class='row'>
            <div class='col-xs-12'>
                <div class="bgWhite list-group-item gallery clear clearfix" >
                    <div class="row bg-info profileBg" style="background-image: url('<?php echo $global['webSiteRootURL'], $user->getBackgroundURL(); ?>')">
                        <img src="<?php echo User::getPhoto($user_id); ?>" alt="<?php echo $user->_getName(); ?>" class="img img-responsive img-thumbnail" style="max-width: 100px;"/>
                    </div>
                    <div class="col-md-12" style='padding:0px;'>
                        <h1 class="pull-left" style='padding-left:0px;'> <?php echo $user->getNameIdentificationBd(); ?> </h1>
                        <div class='pull-left'>
                            <?php 
                            if($user->getTwitter() !== '') { ?>
                                <span class='badge badge_twitter badge_xs'> 
                                    <i class='fab fa-twitter'></i>
                                    <a href='http://www.twitter.com/<?php echo $user->getTwitter(); ?>' target='_blank'>
                                        <?php echo $user->getTwitter(); ?>
                                    </a>
                                </span>
                            
                            <?php 
                            }
                            if($user->getFacebook() !== '') { ?>
                                <span class='badge badge_facebook badge_xs'> 
                                    <i class='fab fa-facebook'></i>
                                    <a href='http://www.facebook.com/<?php echo $user->getFacebook(); ?>' target='_blank'>
                                        <?php echo $user->getFacebook(); ?>
                                    </a>
                                </span>
                            <?php 
                            }
                            if($user->getInstagram() !== '') { ?>
                                <span class='badge badge_instagram badge_xs'> 
                                    <i class='fab fa-instagram'></i>
                                    <a href='http://www.instagram.com/<?php echo $user->getInstagram(); ?>' target='_blank'>
                                        <?php echo $user->getInstagram(); ?>
                                    </a>
                                </span>                            
                            <?php 
                            }
                            if($user->getWebsite() !== '') { ?>
                                <span class='badge badge_website badge_xs'> 
                                    <i class='fas fa-external-link-alt'></i>
                                    <a href='<?php echo $user->getWebsite(); ?>' target='_blank'>
                                        <?php echo $user->getWebsite(); ?>
                                    </a>
                                </span>
                            <?php } ?>
                        </div>
                        <span class="pull-right">
                            <?php
                            echo Subscribe::getButton($user_id);
                            ?>

                            <div style='background-color: transparent;border:0px;float:left;margin-right:10px;margin-top:1px;'>
                                <?php
                                if ($isMyChannel) {
                                ?>
                                    <a href="<?php echo $global['webSiteRootURL']; ?>mvideos" class="btn btn-success">
                                        <span class="glyphicon glyphicon-film"></span>
                                        <?php echo __("My videos"); ?>
                                    </a>
                                <?php
                                }
                                echo YouPHPTubePlugin::getChannelButton();
                                ?>
                            </div>
                        </span>
                    </div>

                    <div class="col-md-12" style='clear:both;'>
                        <div> <?php echo nl2br(htmlentities($user->getAbout())); ?> </div>
                    </div>

                    <ul class="col-xs-12 nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#myVideos">
                                <i class="fa fa-plug"></i> My Videos
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#calendarTab">
                                <i class="fa fa-calendar"></i> Calendar
                            </a>
                        </li>
                        <!--
                        <li>
                            <a data-toggle="tab" href="#menu2">
                                <i class="fa fa-cart-plus"></i> Upcoming `Live`
                            </a>
                        </li>
                        -->
                        <li>
                            <a data-toggle="tab" href="#requestTab">
                                <i class="fa fa-inbox"></i> Make a Request
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#subscribersTab">
                                <i class="fa fa-plus"></i> Subscribers
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="myVideos" class="tab-pane fade in active">

                            <div class="col-md-12">
                                <div class="panel-default">
                                    <div class="panel-body">
                                            <?php
                                            if(!empty($uploadedVideos[0])){
                                                $video = $uploadedVideos[0];
                                                $obj = new stdClass();
                                                $obj->BigVideo = true;
                                                $obj->Description = false;
                                                include $global['systemRootPath'] . 'plugin/Gallery/view/BigVideo.php';
                                                unset($uploadedVideos[0]);
                                            }
                                            ?>
                                        <div class="row mainArea">
                                            <?php
                                            createGallerySection($uploadedVideos);
                                            ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>

                            <div class="col-md-12">
                                <?php
                                foreach ($playlists as $playlist) {
                                    $videosArrayId = PlayList::getVideosIdFromPlaylist($playlist['id']);
                                    if (empty($videosArrayId)) {
                                        continue;
                                    }
                                    $videos = Video::getAllVideos("a", false, false, $videosArrayId);
                                    $videos = PlayList::sortVideos($videos, $videosArrayId);
                                    ?>

                                    <div class="panel-default">
                                        <div class="panel-heading">

                                            <strong style="font-size: 1em;" class="playlistName"><?php echo $playlist['name']; ?> </strong>
                                            <a href="<?php echo $global['webSiteRootURL']; ?>playlist/<?php echo $playlist['id']; ?>" class="btn btn-xs btn-default playAll"><span class="fa fa-play"></span> <?php echo __("Play All"); ?></a>
                                            <?php
                                            if ($isMyChannel) {
                                                ?>     
                                                <script>
                                                    $(function () {
                                                        $("#sortable<?php echo $playlist['id']; ?>").sortable({
                                                            stop: function (event, ui) {
                                                                modal.showPleaseWait();
                                                                var list = $(this).sortable("toArray");
                                                                $.ajax({
                                                                    url: '<?php echo $global['webSiteRootURL']; ?>sortPlaylist',
                                                                    data: {
                                                                        "list": list,
                                                                        "playlist_id": <?php echo $playlist['id']; ?>
                                                                    },
                                                                    type: 'post',
                                                                    success: function (response) {
                                                                        modal.hidePleaseWait();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                        $("#sortable<?php echo $playlist['id']; ?>").disableSelection();
                                                    });
                                                </script>  
                                                <div class="pull-right btn-group">
                                                    <button class="btn btn-xs btn-info" ><i class="fa fa-info-circle"></i> <?php echo __("Drag and drop to sort"); ?></button>
                                                    <button class="btn btn-xs btn-danger deletePlaylist" playlist_id="<?php echo $playlist['id']; ?>" ><span class="fa fa-trash-o"></span> <?php echo __("Delete"); ?></button>
                                                    <button class="btn btn-xs btn-primary renamePlaylist" playlist_id="<?php echo $playlist['id']; ?>" ><span class="fa fa-pencil"></span> <?php echo __("Rename"); ?></button>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="panel-body">

                                            <div id="sortable<?php echo $playlist['id']; ?>" style="list-style: none;">
                                                <?php
                                                foreach ($videos as $value) {
                                                    $img_portrait = ($value['rotation'] === "90" || $value['rotation'] === "270") ? "img-portrait" : "";
                                                    $name = User::getNameIdentificationById($value['users_id']);

                                                    $images = Video::getImageFromFilename($value['filename'], $value['type']);
                                                    $imgGif = $images->thumbsGif;
                                                    $poster = $images->thumbsJpg;
                                                    ?>
                                                    <li class="col-lg-2 col-md-4 col-sm-4 col-xs-6 galleryVideo " id="<?php echo $value['id']; ?>">
                                                        <a class="aspectRatio16_9" href="<?php echo $global['webSiteRootURL']; ?>video/<?php echo $value['clean_title']; ?>" title="<?php echo $value['title']; ?>" style="margin: 0;" >
                                                            <img src="<?php echo $poster; ?>" alt="<?php echo $value['title']; ?>" class="img img-responsive <?php echo $img_portrait; ?>  rotate<?php echo $value['rotation']; ?>" />
                                                            <span class="duration"><?php echo Video::getCleanDuration($value['duration']); ?></span>
                                                        </a>
                                                        <a href="<?php echo $global['webSiteRootURL']; ?>video/<?php echo $value['clean_title']; ?>" title="<?php echo $value['title']; ?>">
                                                            <h2><?php echo $value['title']; ?></h2>
                                                        </a>
                                                        <?php
                                                        if ($isMyChannel) {
                                                            ?>
                                                            <button class="btn btn-xs btn-default btn-block removeVideo" playlist_id="<?php echo $playlist['id']; ?>" video_id="<?php echo $value['id']; ?>">
                                                                <span class="fa fa-trash-o"></span> <?php echo __("Remove"); ?>
                                                            </button>
                                                            <?php
                                                        }
                                                        ?>
                                                        <div class="text-muted galeryDetails">
                                                            <div>
                                                                <?php
                                                                $value['tags'] = Video::getTags($value['id']);
                                                                foreach ($value['tags'] as $value2) {
                                                                    if ($value2->label === __("Group")) {
                                                                        ?>
                                                                        <span class="label label-<?php echo $value2->type; ?>"><?php echo $value2->text; ?></span>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <div>
                                                                <i class="fa fa-eye"></i>
                                                                <span itemprop="interactionCount">
                                                                    <?php echo number_format($value['views_count'], 0); ?> <?php echo __("Views"); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <i class="fa fa-clock-o"></i>
                                                                <?php
                                                                echo humanTiming(strtotime($value['videoCreation'])), " ", __('ago');
                                                                ?>
                                                            </div>
                                                            <div>
                                                                <i class="fa fa-user"></i>
                                                                <?php
                                                                echo $name;
                                                                ?>
                                                            </div>
                                                            <?php
                                                            if (Video::canEdit($value['id'])) {
                                                                ?>
                                                                <div>
                                                                    <a href="<?php echo $global['webSiteRootURL']; ?>mvideos?video_id=<?php echo $value['id']; ?>" class="text-primary"><i class="fa fa-edit"></i> <?php echo __("Edit Video"); ?></a>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Menu 1 -->
                        <div id="calendarTab" class="tab-pane fade">
                            <div class="col-md-12">
                                <div class='calendar_text' style='padding:20px 10px;'>
                                    Here you can view the next planned `Live Sessions` by <?php echo $user->getNameIdentificationBd(); ?>. You can request specific sessions in the `Make a Request` tab.
                                </div>
                                <div id='calendar' style='padding:20px 10px;'></div>
                            </div>
                        </div>

                        <!-- Menu 2 -->
                        <div id="requestTab" class="tab-pane fade">
                            <div class="col-md-12">

                                <div style='position:absolute;text-align:center;font-size: 36px;top:40%;left:30%;color:#888;'> Coming soon...! </div>
                                
                                <div style='opacity:0.25;'>
                                    <div class='row' style='margin-top:30px;margin-bottom:30px;background-color:#eee;padding:10px;'>
                                        <div class='col-xs-4' style='font-size:12px;color:#888;line-height:30px;'> Here you can request for a specific video. </div>
                                        <div class='col-xs-8'> <input type='text' class='form-control input-sm' name='requests' placeholder='Your request' style='width:100%; '/> </div>
                                    </div>

                                    <table id="grid" class="table table-condensed table-hover table-striped" style='margin-top: 20px;'>
                                        <thead>
                                            <tr>
                                                <th data-column-id="valueText"  data-width="150px"><?php echo __("User"); ?></th>
                                                <th data-column-id="description" ><?php echo __("Request"); ?></th>
                                                <th data-column-id="status" data-formatter="status"  data-width="250px"><?php echo __("Status"); ?></th>
                                                <th data-column-id="created" data-order="desc" data-width="150px"><?php echo __("Date"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Menu 3 -->
                        <div id="subscribersTab" class="tab-pane fade">
                            <div class="col-md-12">
                                <?php 
                                $subscribers =  Subscribe::getAllSubscribes($user_id); 
                                foreach($subscribers as $s){
                                    //echo "<pre>"; var_dump($s); echo "</pre><br />";
                                ?>
                                    <div class="col-xs-3" style="width:20%;height:250px;margin-top:20px;">
                                        <div class='card' style='padding:10px 5px 20px 5px;text-align:center;'>
                                          <img class="card-img-top" src="<?php echo $s['photoURL']; ?>" alt="User photo" style='border-radius:8px;'>
                                          <div class="card-body">
                                            <h5 class="card-title">
                                                <?php echo $s['identification']; ?>
                                            </h5>                                                                                 
                                          </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Menu 4 
                        <div id="menu4" class="tab-pane fade">
                            <div class="col-md-12">
                                Menu 1
                            </div>
                        </div>
                        -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include $global['systemRootPath'] . 'view/include/footer.php';
    ?>
    <script>
        var currentObject;
        $(function () {
            $('#calendar').fullCalendar({
              header: {
                left: 'prev,next today',
                center: 'title',
                right: 'listDay,listWeek,month'
              },

              // customize the button names,
              // otherwise they'd all just say "list"
              views: {
                listDay: { buttonText: 'List day' },
                listWeek: { buttonText: 'List week' },
                month: { buttonText: 'Month' }
              },

              defaultView: 'month',
              defaultDate: '<?php echo date("Y-m-d"); ?>',
              navLinks: true, // can click day/week names to navigate views
              eventLimit: true, // allow "more" link when too many events
              events: [
                {
                  title: 'All Day Event',
                  start: '2018-03-01'
                },
                {
                  title: 'Long Event',
                  start: '2018-03-07',
                  end: '2018-03-10'
                },
                {
                  id: 999,
                  title: 'Repeating Event',
                  start: '2018-03-09T16:00:00'
                },
                {
                  id: 999,
                  title: 'Repeating Event',
                  start: '2018-03-16T16:00:00'
                },
                {
                  title: 'Conference',
                  start: '2018-03-11',
                  end: '2018-03-13'
                },
                {
                  title: 'Meeting',
                  start: '2018-03-12T10:30:00',
                  end: '2018-03-12T12:30:00'
                },
                {
                  title: 'Lunch',
                  start: '2018-03-12T12:00:00'
                },
                {
                  title: 'Meeting',
                  start: '2018-03-12T14:30:00'
                },
                {
                  title: 'Happy Hour',
                  start: '2018-03-12T17:30:00'
                },
                {
                  title: 'Dinner',
                  start: '2018-03-12T20:00:00'
                },
                {
                  title: 'Birthday Party',
                  start: '2018-03-13T07:00:00'
                },
                {
                  title: 'Click for Google',
                  url: 'http://google.com/',
                  start: '2018-03-28'
                }
              ]
            });

            $('.removeVideo').click(function () {
                currentObject = this;
                swal({
                    title: "<?php echo __("Are you sure?"); ?>",
                    text: "<?php echo __("You will not be able to recover this action!"); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo __("Yes, delete it!"); ?>",
                    closeOnConfirm: true
                },
                function () {
                    modal.showPleaseWait();
                    var playlist_id = $(currentObject).attr('playlist_id');
                    var video_id = $(currentObject).attr('video_id');
                    $.ajax({
                        url: '<?php echo $global['webSiteRootURL']; ?>removeVideoFromPlaylist',
                        data: {
                            "playlist_id": playlist_id,
                            "video_id": video_id
                        },
                        type: 'post',
                        success: function (response) {
                            $(currentObject).closest('.galleryVideo').fadeOut();
                            modal.hidePleaseWait();
                        }
                    });
                });
            });

            $('.deletePlaylist').click(function () {
                currentObject = this;
                swal({
                    title: "<?php echo __("Are you sure?"); ?>",
                    text: "<?php echo __("You will not be able to recover this action!"); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo __("Yes, delete it!"); ?>",
                    closeOnConfirm: true
                },
                function () {
                    modal.showPleaseWait();
                    var playlist_id = $(currentObject).attr('playlist_id');
                    console.log(playlist_id);
                    $.ajax({
                        url: '<?php echo $global['webSiteRootURL']; ?>removePlaylist',
                        data: {
                            "playlist_id": playlist_id
                        },
                        type: 'post',
                        success: function (response) {
                            $(currentObject).closest('.playList').slideUp();
                            modal.hidePleaseWait();
                        }
                    });
                });
            });

            $('.renamePlaylist').click(function () {
                currentObject = this;
                swal({
                    title: "<?php echo __("Change Playlist Name"); ?>!",
                    text: "<?php echo __("What is the new name?"); ?>",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    inputPlaceholder: "<?php echo __("Playlist name?"); ?>"
                },
                function (inputValue) {
                    if (inputValue === false)
                        return false;

                    if (inputValue === "") {
                        swal.showInputError("<?php echo __("You need to tell us the new name?"); ?>");
                        return false
                    }

                    modal.showPleaseWait();
                    var playlist_id = $(currentObject).attr('playlist_id');
                    console.log(playlist_id);
                    $.ajax({
                        url: '<?php echo $global['webSiteRootURL']; ?>renamePlaylist',
                        data: {
                            "playlist_id": playlist_id,
                            "name": inputValue
                        },
                        type: 'post',
                        success: function (response) {
                            $(currentObject).closest('.playList').find('.playlistName').text(inputValue);
                            modal.hidePleaseWait();
                        }
                    });
                    return false;
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {

            var grid = $("#grid").bootgrid({
                labels: {
                    noResults: "<?php echo __("No results found!"); ?>",
                    all: "<?php echo __("All"); ?>",
                    infos: "<?php echo __("Showing {{ctx.start}} to {{ctx.end}} of {{ctx.total}} entries"); ?>",
                    loading: "<?php echo __("Loading..."); ?>",
                    refresh: "<?php echo __("Refresh"); ?>",
                    search: "<?php echo __("Search"); ?>",
                },
                ajax: true,
                url: "<?php echo $global['webSiteRootURL']; ?>/view/logRequests.json.php?users_id=<?php echo $users_id; ?>",
                formatters: {
                    "status": function (column, row) {
                        var status = "<span class='label label-success'>Done</span>";
                        if (row.status == 'pending') {
                            status = "<span class='label label-warning'>Pending</span>";
                        } else if (row.status == 'canceled') {
                            status = "<span class='label label-danger'>Canceled</span>";
                        }
                        
                        <?php if ($isMyChannel) { ?>
                            status += "<br><br><div class=\"btn-group\"><button class='btn btn-default btn-xs command-status-success'>Done</button>";
                            status += "<button class='btn btn-default btn-xs command-status-pending'>Pending</button>";
                            status += "<button class='btn btn-default btn-xs command-status-canceled'>Canceled</button><div>";
                        <?php } ?>

                        return status;
                    }
                }
            }).on("loaded.rs.jquery.bootgrid", function () {
                
                <?php if ($isMyChannel) { ?>
                    /* Executes after data is loaded and rendered */
                    grid.find(".command-status-success").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        setStatus("success", row.id);
                    });

                    grid.find(".command-status-pending").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        setStatus("pending", row.id);
                    });

                    grid.find(".command-status-canceled").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        setStatus("canceled", row.id);
                    });
                <?php } ?>
            });
        });
        
        <?php if ($isMyChannel) { ?>
            function setStatus(status, wallet_log_id) {
                modal.showPleaseWait();
                $.ajax({
                    url: '<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/changeLogStatus.json.php',
                    type: "POST",
                    data: {
                        status: status,
                        wallet_log_id: wallet_log_id
                    },
                    success: function (response) {
                        $(".walletBalance").text(response.walletBalance);
                        modal.hidePleaseWait();
                        if (response.error) {
                            setTimeout(function () {
                                swal("<?php echo __("Sorry!"); ?>", response.msg, "error");
                            }, 500);
                        } else {
                            $("#grid").bootgrid("reload");
                        }
                    }
                });
            }
        <?php } ?>
    </script>
</body>
</html>



