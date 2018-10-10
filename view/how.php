<?php
require_once '../videos/configuration.php';
require_once '../plugin/YouPHPTubePlugin.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $config->getLanguage(); ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("Help"); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <style>
            .info-div{
                margin-left:20px;
                margin-bottom:20px;
                background-color:#eee;
                padding:10px;
                border-radius:4px;
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
                    <div class="panel-heading">
                        <h2 style="font-weight:600;margin-bottom:-10px;margin-left:0px;padding-left:0px;">
                            How to make a Live Stream in YouMake ? 
                        </h2>
                    </div>

                    <div class='panel-body'>
                        <h3 class="panel-heading" style="font-weight:600;">Streaming Software</h3>
                        <div class='info-div'> Download an streaming software, there are very good open source free software like:
                            <ul>
                                <li>OBS:</li>
                            </ul>
                        </div>

                        <h3 class="panel-heading" style="font-weight:600;"> Register/Log in to YouMake</h3>
                        <p class='info-div'> Once you are logged, in the top part of the menu, click on `Live` button. </p>

                        <h3 class="panel-heading" style="font-weight:600;">Connect the streaming software with YouMake</h3>
                        <p class='info-div'> In the `Live` section, you will find the necessary info to connect with YouMake from your prefered live streaming software.</p>

                        <h3 class="panel-heading" style="font-weight:600;">Start the LiveStreaming</h3>
                        <p class='info-div'> Fill the missing live streaming info to customize your streaming. It could be private (only invited people will see the streaming) or public (everybody in the portal can see your live streaming). Once it all configured, start the streaming from your software and publish the streaming. 

                        <p class='info-div' style='background-color:#ffeeee;'> Warning: it could take a while (~1min) to see it</p>

                        <h3 class="panel-heading" style="font-weight:600;"> Enjoy the live stream! </h3>                        
                    </div>
                </div>
            </div>
        </div><!--/.container-->
        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>
    </body>
</html>
