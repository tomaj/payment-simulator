<?php

declare(strict_types=1);

namespace App\Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Single DES (TatraPay/CardPay DES) needs the OpenSSL 3 legacy provider:
     * OPENSSL_CONF=docker/php/openssl-legacy.cnf. CI runs with --fail-on-skipped.
     */
    protected static function requireDes(): void
    {
        if (openssl_encrypt('x', 'des-ecb', '12345678', OPENSSL_RAW_DATA) === false) {
            while (openssl_error_string() !== false);
            self::markTestSkipped('Single DES is not available; run with OPENSSL_CONF=docker/php/openssl-legacy.cnf');
        }
    }
}
