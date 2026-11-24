<?php

declare(strict_types=1);

// The CardPay Direct API (Apple Pay and Google Pay tokens) with plain PHP: signed JSON requests, signed JSON answers.

require __DIR__ . '/../_support/simulator.php';

// #region client
const HMAC_KEY = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

function sign(string $data): string
{
    return hash_hmac('sha256', $data, (string) hex2bin(HMAC_KEY));
}

/**
 * Sends a request and returns the decoded JSON answer, after checking the HMAC in its Authorization header.
 *
 * @param list<string> $headers
 * @return array<string, mixed>
 */
function call(string $method, string $path, array $headers, string $body = ''): array
{
    $context = stream_context_create(['http' => ['method' => $method, 'header' => $headers, 'content' => $body, 'ignore_errors' => true]]);
    $answer = (string) file_get_contents(simulatorUrl() . $path, false, $context);

    $signature = '';
    foreach (http_get_last_response_headers() ?? [] as $header) {
        if (preg_match('~^Authorization:.*HMAC=([0-9a-f]+)~i', $header, $match) === 1) {
            $signature = $match[1];
        }
    }
    if (!hash_equals(sign($answer), $signature)) {
        throw new RuntimeException("The answer is not signed correctly: {$answer}");
    }

    /** @var array<string, mixed> */
    return json_decode($answer, true, flags: JSON_THROW_ON_ERROR);
}
// #endregion client

// #region tds
/**
 * The shop shows tdsRedirectionFormHtml to the customer; the form posts to the 3-D Secure page, whose "continue" link
 * leads back to tdsTermUrl. Returns that link.
 */
function threeDSecure(string $formHtml): string
{
    preg_match('~action="([^"]+)"~', $formHtml, $action);
    preg_match_all('~name="(\w+)" value="([^"]*)"~', $formHtml, $inputs, PREG_SET_ORDER);
    $fields = [];
    foreach ($inputs as [, $name, $value]) {
        $fields[$name] = html_entity_decode($value);
    }
    $path = (string) parse_url(html_entity_decode($action[1]), PHP_URL_PATH);
    $page = (string) file_get_contents(simulatorUrl() . $path, false, stream_context_create(['http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($fields),
    ]]));
    preg_match('~<a href="([^"]+)"[^>]*>continue</a>~', $page, $continue);

    return "{$path} -> {$continue[1]}";
}
// #endregion tds

// #region check
/**
 * Asks for the result of a transaction, signed over "merchantId;timestamp;processingId".
 */
function checkTransaction(int $processingId): string
{
    $timestamp = '24112026101500'; // ddmmYYYYHHMMSS
    $result = call('GET', "/cgi-bin/e-commerce/start/api/cardpay/transaction/{$processingId}", [
        'X-Merchant-Id: 9999',
        "X-Timestamp: {$timestamp}",
        'Authorization: HMAC=' . sign("9999;{$timestamp};{$processingId}"),
    ]);

    return (string) $result['status'];
}
// #endregion check

// #region transaction
$payments = [
    'Apple Pay, 24.90' => ['amount' => 2490, 'applePayToken' => 'token-from-apple-pay-js'],   // amount in cents
    'Google Pay, 5.00' => ['amount' => 500, 'googlePayToken' => 'token-from-google-pay'],
    'Google Pay, 24.90' => ['amount' => 2490, 'googlePayToken' => 'token-from-google-pay', 'tdsTermUrl' => 'https://shop.example/3ds-done'],
    'Apple Pay, 150.00' => ['amount' => 15000, 'applePayToken' => 'token-from-apple-pay-js'],
];

foreach ($payments as $label => $payment) {
    $body = json_encode($payment, JSON_THROW_ON_ERROR);
    $answer = call('POST', '/cgi-bin/e-commerce/start/api/cardpay/transaction', [
        'Content-Type: application/json',
        'Authorization: HMAC=' . sign($body),          // HMAC of the exact request body
    ], $body);
    printf("%-18s -> %s, response code %s\n", $label, $answer['status'], $answer['transactionData']['responseCode']);

    if ($answer['status'] === 'TDS_AUTH_REQUIRED') {
        echo '  3-D Secure: ', threeDSecure($answer['tdsRedirectionFormHtml']), "\n";
        echo '  check transaction: ', checkTransaction($answer['processingId']), "\n";
    }
}
// #endregion transaction

// #region unsigned
$body = json_encode(['amount' => 2490, 'applePayToken' => 'x'], JSON_THROW_ON_ERROR);
$answer = file_get_contents(simulatorUrl() . '/cgi-bin/e-commerce/start/api/cardpay/transaction', false, stream_context_create(['http' => [
    'method' => 'POST',
    'header' => ['Content-Type: application/json', 'Authorization: HMAC=' . sign('another body')],
    'content' => $body,
    'ignore_errors' => true,
]]));
echo "wrong request signature -> " . (http_get_last_response_headers()[0] ?? "") . ": {$answer}\n";
// #endregion unsigned
