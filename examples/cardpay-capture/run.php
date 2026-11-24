<?php

declare(strict_types=1);

// CardPay capture, release and refund of a pre-authorization (the bank's txn_process endpoint), in plain PHP:
// a signed GET request, an XML answer signed in the Authorization header.

require __DIR__ . '/../_support/simulator.php';

// #region request
const HMAC_KEY = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

function hmac(string $data): string
{
    return hash_hmac('sha256', $data, (string) hex2bin(HMAC_KEY));
}

/**
 * Sends the signed request and returns the XML answer and its Authorization header.
 *
 * @param array<string, string> $request MID AMT TID VS TXN REM TIMESTAMP
 * @return array{xml: string, authorization: ?string}
 */
function cancelAuthorize(array $request, ?string $signature = null): array
{
    // signed: MID AMT TID VS TXN REM TIMESTAMP, empty fields add nothing
    $request['HMAC'] = $signature ?? hmac(implode('', $request));
    $xml = (string) file_get_contents(simulatorUrl() . '/payment/cardpay-cancel-purchase?' . http_build_query($request));

    $authorization = null;
    foreach (http_get_last_response_headers() ?? [] as $header) {
        if (stripos($header, 'Authorization:') === 0) {
            $authorization = trim(substr($header, 14));
        }
    }

    return ['xml' => $xml, 'authorization' => $authorization];
}
// #endregion request

// #region verify
/**
 * Authorization: HMAC=<hex>,ECDSA=...,ECDSA_KEY=1 - the HMAC covers the XML body exactly as received.
 */
function bodyIsSigned(array $answer): bool
{
    preg_match('~HMAC=([0-9a-f]+)~', (string) $answer['authorization'], $signature);

    return isset($signature[1]) && hash_equals(hmac($answer['xml']), $signature[1]);
}
// #endregion verify

// #region operations
foreach (['CPA' => 'capture', 'SPA' => 'release', 'CB' => 'refund'] as $txn => $meaning) {
    $answer = cancelAuthorize(['MID' => '9999', 'AMT' => '110.00', 'TID' => '567898', 'VS' => '2026020', 'TXN' => $txn, 'TIMESTAMP' => '24112026101500']);
    $result = simplexml_load_string($answer['xml'])->result;
    echo "{$txn} ({$meaning}): res {$result->res}, body signature ", bodyIsSigned($answer) ? 'valid' : 'INVALID', "\n";
}
// #endregion operations

// #region wrong
$answer = cancelAuthorize(['MID' => '9999', 'AMT' => '110.00', 'TID' => '567898', 'VS' => '2026020', 'TXN' => 'CPA', 'TIMESTAMP' => '24112026101500'], hmac('something else'));
$result = simplexml_load_string($answer['xml'])->result;
echo "wrong request signature: res {$result->res}, errorCode {$result->errorCode}, Authorization header: ", $answer['authorization'] ?? 'none', "\n";
// #endregion wrong
