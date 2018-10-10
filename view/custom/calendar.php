<?php
require_once '../../videos/configuration.php';
$makers = array();
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <title>
    <?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("Channel"); ?></title>
    <?php
    include $global['systemRootPath'] . 'view/include/head.php';
    ?>        
    <link href="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $global['webSiteRootURL']; ?>css/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script>
        /*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
        $.widget.bridge('uibutton', $.ui.button);
        $.widget.bridge('uitooltip', $.ui.tooltip);
    </script>
    <style>
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
            	<div class='panel-heading'>
                	<h2 style='font-weight:600;margin-bottom:-15px;margin-left:0px;padding-left:0px;margin-top:-20px;'> Calendar </h2>
                </div>

                <div class='panel-body' style='min-height: 60vh;'>
                	<div id='calendar' style='padding:20px 10px;'></div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include $global['systemRootPath'] . 'view/include/footer.php';
    ?>

    <link rel='stylesheet' type='text/css' href='<?php echo $global['webSiteRootURL']; ?>js/node_modules/fullcalendar/dist/fullcalendar.css' />
    <script type='text/javascript' src='<?php echo $global['webSiteRootURL']; ?>js/node_modules/moment/moment.js'></script>
    <script type='text/javascript' src='<?php echo $global['webSiteRootURL']; ?>js/node_modules/fullcalendar/dist/fullcalendar.js'></script>

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
        });
    </script>
</body>
</html>



