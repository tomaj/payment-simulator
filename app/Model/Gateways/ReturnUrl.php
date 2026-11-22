<?php

declare(strict_types=1);

namespace App\Model\Gateways;

final class ReturnUrl
{
    /**
     * Appends an already encoded query string to the shop's return URL.
     */
    public static function append(string $url, string $query): string
    {
        return $url . (str_contains($url, '?') ? '&' : '?') . $query;
    }
}
