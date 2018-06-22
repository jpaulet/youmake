<?php
require_once '../../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';

if(!User::isLogged()){
    header('Location: '.$global['webSiteRootURL'].'User');
}

$plugin = YouPHPTubePlugin::loadPluginIfEnabled("YPTWallet");
$paypal = YouPHPTubePlugin::loadPluginIfEnabled("PayPalYPT");
$obj = $plugin->getDataObject();
if (!empty($paypal)) {
    $paypalObj = $paypal->getDataObject();
}
$options = json_decode($obj->addFundsOptions);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title>Add Funds</title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <style>
            .addFunds-row {
                margin-bottom: 30px;
            }
            .addFunds-row h3{
                font-weight: 600;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>
        <div class="container-fluid">
            <div class="row ">
                <div class="addFunds-row col-xs-12">
                    <h3><?php echo __("Become Member"); ?></h3>
                    <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                        <div class="col-sm-6">
                            <?php echo "Benefits of being `YouMake` premium member" ?>
                        </div>
                        <div class="col-sm-6" style='text-align:center;'>
                            <button class='youmake-button'> Become Member </button>
                        </div>
                    </div>
                </div>

                <div class="addFunds-row col-xs-12">
                    <h3><?php echo __("Add Funds"); ?> Paypal</h3>
                    <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                        <div class="col-sm-6">
                            <?php echo $obj->add_funds_text ?>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            if (!empty($_GET['status'])) {
                                $text = "unknow";
                                $class = "danger";
                                switch ($_GET['status']) {
                                    case "fail":
                                        $text = $obj->add_funds_success_fail;
                                        break;
                                    case "success":
                                        $text = $obj->add_funds_success_success;
                                        $class = "success";
                                        break;
                                    case "cancel":
                                        $text = $obj->add_funds_success_cancel;
                                        $class = "warning";
                                        break;
                                }
                                ?>
                                <div class="alert alert-<?php echo $class; ?>">
                                    <?php echo $text; ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="form-group">
                                <label for="value"><?php echo __("Add Funds"); ?> <?php echo $obj->currency_symbol; ?> <?php echo $obj->currency; ?></label>
                                <select class="form-control" id="value" >
                                    <?php
                                    foreach ($options as $value) {
                                        ?>
                                        <option value="<?php echo $value; ?>">
                                            <?php echo $obj->currency_symbol; ?> 
                                            <?php echo $value; ?> 
                                            <?php echo $obj->currency; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $plugin->getAvailablePayments();
                            ?>
                        </div>  
                    </div>
                </div>

                <div class="addFunds-row col-xs-12">
                    <h3><?php echo __("Add Funds"); ?> Stripe</h3>
                    <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                        <div class="col-sm-6">
                            <?php echo $obj->add_funds_text ?>
                        </div>
                        <div class="col-sm-6">
                            <?php 
                            include $global['systemRootPath'] . 'view/custom/stripe.php';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>
        <script>
            $(document).ready(function () {

            });
        </script>
    </body>
</html>
