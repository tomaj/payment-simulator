<?php

declare(strict_types=1);

namespace App\Model\Sign;

/**
 * Same algorithm as Omnipay\Core\Sign\DesSign from tomaj/omnipay-tatra 4.x.
 *
 * The library version calls openssl_random_pseudo_bytes(0) for the (unused) ECB IV,
 * which throws a ValueError on PHP 8. Switch back to the library class once a fixed
 * omnipay-tatra release is available.
 *
 * Single DES is only available in OpenSSL 3 when the legacy provider is enabled.
 */
final class DesSign
{
    public function sign(string $input, string $secret): string
    {
        $bytesHash = sha1($input, true);

        $encrypted = openssl_encrypt($bytesHash, 'des-ecb', $secret, OPENSSL_RAW_DATA);
        if ($encrypted === false) {
            throw new \RuntimeException('Unable to compute the DES signature (single DES needs the OpenSSL legacy provider)');
        }

        return strtoupper(substr(bin2hex($encrypted), 0, 16));
    }
}
