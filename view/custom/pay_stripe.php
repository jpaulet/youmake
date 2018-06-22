<?php
if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../../';
}
//require_once $global['systemRootPath'] . 'objects/captcha.php';
require_once $global['systemRootPath'] . 'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

require $global['systemRootPath'] . 'plugin/Stripe/vendor/stripe/stripe-php/init.php';

header('Content-Type: application/json');
$obj = new stdClass();
$obj->error = true;
$obj->msg = "";
$obj->walletBalance = 0;

if (!User::isLogged()) {
    $obj->msg = ("Is not logged");
    die(json_encode($obj));
}
$plugin = YouPHPTubePlugin::loadPluginIfEnabled("YPTWallet");
if(empty($plugin)){
    $obj->msg = ("Plugin not enabled");
    die(json_encode($obj));
}

//$obj->walletBalance = $plugin->getBalanceFormated(User::getId());

/*
$valid = Captcha::validation($_POST['captcha']);
if (!$valid) {
    $obj->msg = ("Invalid Captcha");
    die(json_encode($obj));
}
*/

$_POST['value'] = floatval($_POST['value']);
$result = $plugin->addBalance(User::getId(),$_POST['value']);
if($result == null){
    $obj->error = false;
    $obj->walletBalance = $plugin->getBalanceFormated(User::getId());
    $obj->msg = "Added ".$_POST['value']."€ to your account.<br />You have ".$obj->walletBalance." in total.";
}else{
    $obj->msg = "We could not transfer funds, please check your credit card";
}

\Stripe\Stripe::setApiKey("sk_test_ov6iLYrsLOXTPTMNJPwadRpc");

$customer = \Stripe\Customer::create(array(
    'email' => 'blakdrak@gmail.com',
    'source'  => $_POST['stripeToken']
));

// Make the payment on Stripe
$charge = \Stripe\Charge::create(array(
    'customer' => $customer->id,
    'amount'   => $_POST['value']*100,
    'currency' => 'usd'
));

echo json_encode($obj);


