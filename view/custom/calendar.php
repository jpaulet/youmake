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
                	Calendar
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

        });
    </script>
</body>
</html>



