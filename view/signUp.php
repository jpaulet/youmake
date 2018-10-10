<?php
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

$json_file = url_get_contents("{$global['webSiteRootURL']}plugin/CustomizeAdvanced/advancedCustom.json.php");
// convert the string to a json object
$advancedCustom = json_decode($json_file);
if(!empty($advancedCustom->disableNativeSignUp)){
    die("Sign Up Disabled");
}

$agreement = YouPHPTubePlugin::loadPluginIfEnabled("SignUpAgreement");
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("User"); ?></title>
        <?php include $global['systemRootPath'] . 'view/include/head.php'; ?>
    </head>

    <body>
        <?php include $global['systemRootPath'] . 'view/include/navbar.php'; ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3 col-md-offset-2 col-md-10 col-sm-12 col-xs-12">
                    <?php require $global['systemRootPath'] . 'view/signUp_container.php'; ?>
                </div>
            </div>            
        </div><!--/.container-->

        <?php include $global['systemRootPath'] . 'view/include/footer.php'; ?>
    </body>
</html>
