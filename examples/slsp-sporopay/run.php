<?php

declare(strict_types=1);

// SLSP SporoPay with omnipay-slsp: a 3DES-signed redirect to the simulator, verified on the way back.

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../_support/simulator.php';

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\Request;

// #region purchase
$config = [
    'sharedSecret' => 'Z3qY08EpvLlAAoMZdnyUdQ==', // the simulator's SporoPay test key (base64)
    'testMode' => true,
    'testHost' => simulatorUrl(),
];
$order = [
    'accountNumberPrefix' => '000000',
    'accountNumber' => '0013662162',
    'amount' => '24.90',
    'vs' => '2026001',
    'ss' => '0000000001',
    'rurl' => 'https://shop.example/payment/return',
    'param' => 'order=2026001',
];

$gateway = Omnipay::create('SporoPay');
$gateway->initialize($config);
$redirect = $gateway->purchase($order)->send();
$page = openPaymentPage($redirect->getRedirectUrl());
// #endregion purchase
echo $redirect->getRedirectMethod(), ' ', parse_url($redirect->getRedirectUrl(), PHP_URL_PATH), " -> {$page['verdict']}\n";

// #region complete
foreach (['ok', 'fail', 'timeout'] as $answer) {
    $back = Omnipay::create('SporoPay', null, Request::create((string) $page[$answer]));
    $back->initialize($config);
    try {
        $result = $back->completePurchase(['vs' => $order['vs'], 'amount' => $order['amount']])->send();
        $status = $result->isSuccessful() ? 'paid' : 'not paid';
    } catch (InvalidRequestException $e) {
        $status = 'rejected: ' . $e->getMessage();
    }
    printf("  %-8s %s\n", $answer, $status);
}
// #endregion complete
