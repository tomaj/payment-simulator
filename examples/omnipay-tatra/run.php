<?php

declare(strict_types=1);

// omnipay-tatra in test mode against the simulator: TatraPay, CardPay, ComfortPay and a CardPay pre-authorization.
// For each, the simulator must accept the request signature, and the library must accept each signed answer.

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../_support/simulator.php';

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\Request;

// #region config
$config = [
    'mid' => '9999',                        // any merchant id
    'sharedSecret' => str_repeat('1', 128), // the simulator's HMAC test key
    'testMode' => true,                     // send the customer to testHost instead of the bank
    'testHost' => simulatorUrl(),           // this simulator, e.g. http://127.0.0.1:8080
];
// #endregion config
$order = ['amount' => '24.90', 'currency' => 'EUR', 'vs' => '2026001', 'rurl' => 'https://shop.example/payment/return'];
$card = ['name' => 'Jan Novak', 'ipc' => '203.0.113.10'];

$cases = [
    'TatraPay' => ['TatraPay', 'purchase', 'completePurchase', $order],
    'CardPay' => ['CardPay', 'purchase', 'completePurchase', $order + $card],
    'ComfortPay' => ['ComfortPay', 'purchase', 'completePurchase', $order + $card],
    'CardPay authorize' => ['CardPay', 'authorize', 'completeAuthorize', $order + $card + ['tpay' => 'Y']],
];

foreach ($cases as $label => [$name, $start, $complete, $parameters]) {
    // #region round-trip
    /** @var AbstractGateway $gateway */
    $gateway = Omnipay::create($name);
    $gateway->initialize($config);
    $redirect = $gateway->{$start}($parameters)->send();      // e.g. $gateway->purchase([...])->send()

    $page = openPaymentPage($redirect->getRedirectUrl());     // what the customer sees at the "bank"
    echo "{$label}: ", parse_url($redirect->getRedirectUrl(), PHP_URL_PATH), " -> {$page['verdict']}\n";

    foreach (['ok', 'fail', 'timeout'] as $answer) {
        $back = Omnipay::create($name, null, Request::create((string) $page[$answer])); // the customer's return
        $back->initialize($config);
        try {
            $result = $back->{$complete}($parameters)->send();  // e.g. $gateway->completePurchase([...])->send()
            $status = $result->isSuccessful() ? 'successful' : ($result->isPending() ? 'pending' : 'not successful');
        } catch (InvalidRequestException $e) {
            $status = 'rejected: ' . $e->getMessage();
        }
        printf("  %-8s %s\n", $answer, $status);
    }
    // #endregion round-trip
}
