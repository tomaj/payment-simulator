<?php

declare(strict_types=1);

namespace App\Model\Gateways;

/**
 * Computes the signature of a payment request the way the bank does, and the bank's signed return URLs.
 */
interface GatewaySign
{
    /**
     * The signature the shop must have sent with the request.
     */
    public function sign(): string;

    /**
     * The shop's return URL with the result parameters and their signature appended, as the bank sends the customer back.
     *
     * @param string $result OK, FAIL or TOUT
     */
    public function returnUrlSign(string $result): string;
}
