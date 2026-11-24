<?php

declare(strict_types=1);

// A client without Omnipay: signs a TatraPay request by hand, opens the simulator's page, and verifies the signed
// answer by hand. Then sends a request with a wrong signature and one with a missing parameter.

require __DIR__ . '/../_support/simulator.php';

// #region request
$key = str_repeat('1', 128);                          // the HMAC test key: 128 hex characters = 64 bytes
$hmac = static fn(string $data): string => hash_hmac('sha256', $data, (string) hex2bin($key));

$request = [
    'MID' => '9999',
    'AMT' => '24.90',
    'CURR' => '978',                                  // ISO 4217 numeric code of EUR
    'VS' => '2026001',
    'RURL' => 'https://shop.example/payment/return',
    'REM' => 'orders@shop.example',
    'TIMESTAMP' => '24112026101500',                  // ddmmYYYYHHMMSS in UTC; the bank wants the current time
];
// signed: MID AMT CURR VS SS CS RURL REM TIMESTAMP, empty fields (SS, CS) add nothing
$request['HMAC'] = $hmac($request['MID'] . $request['AMT'] . $request['CURR'] . $request['VS'] . $request['RURL'] . $request['REM'] . $request['TIMESTAMP']);

$page = openPaymentPage(simulatorUrl() . '/payment/tatrapay-hmac?' . http_build_query($request));
// #endregion request
echo "signed request: HTTP {$page['status']}, {$page['verdict']}\n";

// #region verify
foreach (['ok', 'fail', 'timeout'] as $answer) {
    parse_str((string) parse_url((string) $page[$answer], PHP_URL_QUERY), $back);
    // the return is signed over AMT CURR VS SS CS RES TID TIMESTAMP (the simulator sends no TID)
    $expected = $hmac($back['AMT'] . $back['CURR'] . $back['VS'] . ($back['SS'] ?? '') . $back['CS'] . $back['RES'] . ($back['TID'] ?? '') . $back['TIMESTAMP']);
    $valid = hash_equals($expected, (string) $back['HMAC']) && $back['VS'] === $request['VS'];
    printf("  %-8s RES=%-4s signature %s\n", $answer, $back['RES'], $valid ? 'valid' : 'INVALID');
}
// #endregion verify

// #region errors
$wrong = ['HMAC' => $hmac('something else')] + $request;
$page = openPaymentPage(simulatorUrl() . '/payment/tatrapay-hmac?' . http_build_query($wrong));
echo "wrong signature: HTTP {$page['status']}, {$page['verdict']}, links back: ", $page['ok'] === null ? 'none' : 'shown', "\n";

$incomplete = $request;
unset($incomplete['AMT'], $incomplete['TIMESTAMP']);
$page = openPaymentPage(simulatorUrl() . '/payment/tatrapay-hmac?' . http_build_query($incomplete));
echo "missing AMT and TIMESTAMP: HTTP {$page['status']}, {$page['verdict']}: ", implode(', ', $page['missing']), "\n";
// #endregion errors
