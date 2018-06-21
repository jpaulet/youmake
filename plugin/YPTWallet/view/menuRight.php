<?php
if (!User::isLogged()) {
    return;
}
$plugin = YouPHPTubePlugin::loadPluginIfEnabled("YPTWallet");
$obj = $plugin->getDataObject();
$balance = $plugin->getBalance(User::getId());
?>
<style>
</style>
<li>
    <div class="btn-group">
        <button type="button" class="btn btn-default  dropdown-toggle navbar-btn pull-left" data-toggle="dropdown" style='border: transparent; background-color:transparent;'>
            <?php echo $obj->wallet_button_title; ?> 
            <span class="badge" style='padding:8px 14px;margin-top:-2px;background-color:#370b65;'>
                <?php echo $obj->currency_symbol; ?> 
                <span class="walletBalance">
                    <?php echo number_format($balance, $obj->decimalPrecision); ?>
                </span> 
                <?php echo $obj->currency; ?>
            </span>
            </span> 
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu"> 
            <?php
            if($obj->enableAutomaticAddFundsPage){
            ?>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/addFunds.php" style='width:100%;'>
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo __("Add Funds"); ?>
                </a>
            </li>
            <?php
            }
            if($obj->enableManualAddFundsPage){
            ?>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/manualAddFunds.php" style='width:100%;'>
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo $obj->manualAddFundsMenuTitle; ?>
                </a>
            </li>
            <?php
            }
            if($obj->enableManualWithdrawFundsPage){
            ?>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/manualWithdrawFunds.php" style='width:100%;'>
                    <i class="far fa-money-bill-alt" aria-hidden="true"></i>
                    <?php echo $obj->manualWithdrawFundsMenuTitle; ?>
                </a>
            </li>
            <?php
            }
            ?>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/transferFunds.php" style='width:100%;'>
                    <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                    <?php echo __("Transfer Funds"); ?>
                </a>
            </li> 
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/history.php" style='width:100%;'>
                    <i class="fa fa-history" aria-hidden="true"></i>
                    <?php echo __("History"); ?>
                </a>
            </li> 
            <li class="dropdown-submenu">
                <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/configuration.php" style='width:100%;'>
                    <i class="fas fa-cog" aria-hidden="true"></i>
                    <?php echo __("Configuration"); ?>
                </a>
            </li> 
            <?php
            if (User::isAdmin()) {
                $total = WalletLog::getTotalFromWallet(0,true,'pending');
                ?>
                <li class="dropdown-header">Admin Menu</li>
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/adminManageWallets.php" style='width:100%;'>
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <?php echo __("Manage Wallets"); ?>
                    </a>
                </li> 
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/pendingRequests.php" style='width:100%;'>
                        <i class="far fa-clock" aria-hidden="true"></i>
                        <?php echo __("Pending Requests"); ?> <span class="badge"><?php echo $total; ?></span>
                    </a>
                </li> 
                <?php
            }
            ?>
        </ul>
    </div>

</li>

<script>
    $(document).ready(function () {
    });
</script>