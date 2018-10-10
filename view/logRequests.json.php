<?php
if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../';
}
require_once $global['systemRootPath'] . 'videos/configuration.php';
if (!User::isLogged()) {
    header("Location: {$global['webSiteRootURL']}?error=" . __("You can not do this"));
    exit;
}

if(!empty($_GET['users_id']) && User::isAdmin()){
    $users_id = $_GET['users_id'];
}else{
    $users_id = User::getId();
}

header('Content-Type: application/json');

if(!empty($_POST['sort']['valueText'])){
    $_POST['sort']['value'] = $_POST['sort']['valueText'];
    unset($_POST['sort']['valueText']);
}

$row = null; // WalletLog::getAllFromUser($users_id);
$total = 1;  // WalletLog::getTotalFromUser($users_id);
echo '{  "current": '.$_POST['current'].',"rowCount": '.$_POST['rowCount'].', "total": '.$total.', "rows":'. json_encode($row).'}';