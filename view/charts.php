<?php
if (!file_exists('../videos/configuration.php')) {
    if (!file_exists('../install/index.php')) {
        die("No Configuration and no Installation");
    }
    header("Location: install/index.php");
}

require_once '../videos/configuration.php';

require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'objects/comment.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'objects/video.php';

if(!User::isLogged()){
    header("Location: ".$global['webSiteRootURL']);
}

if(empty($_POST['rowCount'])){
    $_POST['rowCount'] = 50;
}

if(User::isAdmin()){
    $videos = Video::getAllVideos("viewableNotAd", true, true, array(), true);
    $totalVideos = Video::getTotalVideos("viewableNotAd");
    $totalUsers = User::getTotalUsers();
    $totalSubscriptions = Subscribe::getTotalSubscribes();
    $totalComents = Comment::getTotalComments();
    $totalInfos = Video::getTotalVideosInfo("viewableNotAd", false, false, array(), true);
}else{
    $videos = Video::getAllVideos("viewableNotAd", true, true, array(), true);
    $totalVideos = Video::getTotalVideos("", true);
    $totalUsers = User::getTotalUsers();
    $totalSubscriptions = Subscribe::getTotalSubscribes(User::getId());
    $totalComents = Comment::getTotalComments(0, 'NULL', User::getId());
    $totalInfos = Video::getTotalVideosInfo("", true, false, array(), true);
}
$labelToday = array();
for ($i = 0; $i < 24; $i++) {
    $labelToday[] = "{$i} h";
}
$label7Days = array();
for ($i = 7; $i >= 0; $i--) {
    $label7Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$label30Days = array();
for ($i = 30; $i >= 0; $i--) {
    $label30Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$label90Days = array();
for ($i = 90; $i >= 0; $i--) {
    $label90Days[] = date("Y-m-d", strtotime("-{$i} days"));
}
$statistc_lastToday = VideoStatistic::getTotalToday("");
$statistc_last7Days = VideoStatistic::getTotalLastDays("", 7);
$statistc_last30Days = VideoStatistic::getTotalLastDays("", 30);
$statistc_last90Days = VideoStatistic::getTotalLastDays("", 90);

$bg = $bc = $labels = $labelsFull = $datas = $datas7 = $datas30 = $datasToday = $datasUnique = array();
foreach ($videos as $value) {
    $labelsFull[] = ($value["title"]);
    $labels[] = substr($value["title"], 0, 20);
    $datas[] = $value["statistc_all"];
    $datasToday[] = $value["statistc_today"];
    $datas7[] = $value["statistc_week"];
    $datas30[] = $value["statistc_month"];
    $datasUnique[] = $value["statistc_unique_user"];
    $r = rand(0, 255);
    $g = rand(0, 255);
    $b = rand(0, 255);
    $bg[] = "rgba({$r}, {$g}, {$b}, 0.5)";
    $bc[] = "rgba({$r}, {$g}, {$b}, 1)";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title>Chart - <?php echo $config->getWebSiteTitle(); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <script src="<?php echo $global['webSiteRootURL']; ?>view/js/Chart.bundle.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo $global['webSiteRootURL']; ?>view/css/DataTables/datatables.min.css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>view/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <style>
            /* Custom Colored Panels */
            .dashboard .panel-heading {
                color: #fff;
            }
            .dashboard .loading {
                color: #FFFFFF55;
            }

            .huge {
                font-size: 40px;
            }

            <?php
            $cssPanel = array(
                'green' => array('5cb85c', '3d8b3d'),
                'red' => array('d9534f', 'b52b27'),
                'yellow' => array('f0ad4e', 'df8a13'),
                'orange' => array('f26c23', 'bd4a0b'),
                'purple' => array('5133ab', '31138b'),
                'wine' => array('ac193d', '9c091d'),
                'blue' => array('2672ec', '0252ac')
            );
            foreach ($cssPanel as $key => $value) {
                ?>
                .panel-<?php echo $key; ?> {
                    border-color: #<?php echo $value[0]; ?>;
                    background-color: #<?php echo $value[0]; ?>;
                }

                .panel-<?php echo $key; ?> > a {
                    color: #<?php echo $value[0]; ?>;
                }

                .panel-<?php echo $key; ?> > a:hover {
                    color: #<?php echo $value[1]; ?>;
                }
                <?php
            }
            ?>

        </style>
    </head>
    <body>
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>
        <div class="container-fluid">
            <div class='row'>
                <div class='col-xs-12'>
                    <div class="list-group-item clear clearfix" style='background-color:#fff;border:1px solid #E1E1E1;'>
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#dashboard"><i class="fas fa-tachometer-alt"></i> <?php echo __("Dashboard"); ?></a></li>
                            <li><a data-toggle="tab" href="#menu1"><i class="fab fa-youtube"></i> <i class="fa fa-eye"></i> <?php echo __("Video views - per Channel"); ?></a></li>
                            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-comments"></i> <i class="fa fa-thumbs-up"></i> <?php echo __("Comment thumbs up - per Person"); ?></a></li>
                            <li><a data-toggle="tab" href="#menu3"><i class="fab fa-youtube"></i> <i class="fa fa-thumbs-up"></i> <?php echo __("Video thumbs up - per Channel"); ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="dashboard" class="tab-pane fade in active" style="padding: 10px;">
                                <?php
                                    include $global['systemRootPath'].'view/report0.php';
                                ?>
                            </div>
                            <div id="menu1" class="tab-pane fade" style="padding: 10px;">
                                <?php
                                    include $global['systemRootPath'].'view/report1.php';
                                ?>
                            </div>
                            <div id="menu2" class="tab-pane fade" style="padding: 10px;">
                                <?php
                                    include $global['systemRootPath'].'view/report2.php';
                                ?>
                            </div>
                            <div id="menu3" class="tab-pane fade" style="padding: 10px;">
                                <?php
                                    include $global['systemRootPath'].'view/report3.php';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo $global['webSiteRootURL']; ?>view/css/DataTables/datatables.min.js"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>view/js/jquery-ui/jquery-ui.js" type="text/javascript"></script>
        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>

        <script type="text/javascript">
            $(document).ready(function () {
                
            });
        </script>
    </body>
</html>
