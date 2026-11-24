<?php

declare(strict_types=1);

// Plays the customer at the simulator: opens the payment page a shop redirected to (GET) or posted a form to (POST)
// and reads what the page shows. Shared by the examples.

/**
 * The simulator the examples talk to (SIMULATOR_URL, default http://127.0.0.1:8080).
 */
function simulatorUrl(): string
{
    $url = getenv('SIMULATOR_URL');

    return rtrim(is_string($url) && $url !== '' ? $url : 'http://127.0.0.1:8080', '/');
}

// #region open
/**
 * Opens a payment page and returns the HTTP status, the verdict (Sign OK, Wrong sign or Missing parameters), the
 * missing parameters it lists, and the three signed links back to the shop.
 *
 * @param array<string, mixed> $form POST fields; a GET request when empty
 * @return array{status: int, verdict: string, missing: list<string>, ok: ?string, fail: ?string, timeout: ?string}
 */
function openPaymentPage(string $url, array $form = []): array
{
    $http = ['ignore_errors' => true];
    if ($form !== []) {
        $http += ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencoded', 'content' => http_build_query($form)];
    }
    $html = @file_get_contents($url, false, stream_context_create(['http' => $http]));
    if ($html === false) {
        throw new RuntimeException("Cannot open {$url}: is the simulator running at " . simulatorUrl() . '?');
    }
    preg_match('~^HTTP/\S+ (\d{3})~', $http_response_header[0] ?? '', $status);

    $verdict = 'no verdict';
    foreach (['Sign OK', 'Wrong sign', 'Missing parameters'] as $candidate) {
        if (str_contains($html, $candidate)) {
            $verdict = $candidate;
            break;
        }
    }
    preg_match_all('~<li><code>([^<]+)</code></li>~', $html, $missing);

    $links = ['ok' => null, 'fail' => null, 'timeout' => null];
    preg_match_all('~<a href="([^"]+)"[^>]*>continue (ok|fail|timeout)</a>~', $html, $matches, PREG_SET_ORDER);
    foreach ($matches as [, $href, $result]) {
        $links[$result] = html_entity_decode($href, ENT_QUOTES | ENT_HTML5);
    }

    return ['status' => (int) ($status[1] ?? 0), 'verdict' => $verdict, 'missing' => $missing[1]] + $links;
}
// #endregion open

/**
 * The query string of a URL as an array, like PHP fills $_GET.
 *
 * @return array<string, mixed>
 */
function queryOf(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

    /** @var array<string, mixed> $query */
    return $query;
}
