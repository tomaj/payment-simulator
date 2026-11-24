<?php

declare(strict_types=1);

// VÚB e-platby with omnipay-vub: the shop posts a signed form to the simulator, which answers like the bank.

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../_support/simulator.php';

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\Request;

// #region purchase
$config = [
    'mid' => '9999',
    'sharedSecret' => str_repeat('1', 64), // the simulator's e-platby test key (used as text)
    'testMode' => true,
    'testHost' => simulatorUrl(),
];
$order = ['amount' => '24.90', 'vs' => '2026001', 'cs' => '0308', 'rurl' => 'https://shop.example/payment/return'];

$gateway = Omnipay::create('Eplatby');
$gateway->initialize($config);
$redirect = $gateway->purchase($order)->send();

// e-platby is a form POST: $redirect->redirect() renders an auto-submitting form
$page = openPaymentPage($redirect->getRedirectUrl(), $redirect->getRedirectData());
// #endregion purchase
echo $redirect->getRedirectMethod(), ' ', parse_url($redirect->getRedirectUrl(), PHP_URL_PATH), " -> {$page['verdict']}\n";

// #region complete
foreach (['ok', 'fail', 'timeout'] as $answer) {
    $back = Omnipay::create('Eplatby', null, Request::create((string) $page[$answer]));
    $back->initialize($config);
    try {
        $result = $back->completePurchase(['vs' => $order['vs']])->send();
        $status = $result->isSuccessful() ? 'paid' : 'not paid';
    } catch (InvalidRequestException $e) {
        $status = 'rejected: ' . $e->getMessage();
    }
    printf("  %-8s %s\n", $answer, $status);
}
// #endregion complete
