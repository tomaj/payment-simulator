<?php

declare(strict_types=1);

namespace App\Model\Sign;

/**
 * Same algorithm as Omnipay\Core\Sign\Aes256Sign from tomaj/omnipay-tatra 4.x.
 *
 * The library version calls openssl_random_pseudo_bytes(0) for the (unused) ECB IV,
 * which throws a ValueError on PHP 8. Switch back to the library class once a fixed
 * omnipay-tatra release is available.
 */
final class Aes256Sign
{
    public function sign(string $input, string $secret): string
    {
        $sharedSecret = pack('H*', $secret);

        $bytesHash = substr(sha1($input, true), 0, 16);

        $encrypted = openssl_encrypt($bytesHash, 'aes-128-ecb', $sharedSecret, OPENSSL_RAW_DATA);
        if ($encrypted === false) {
            throw new \RuntimeException('Unable to compute the AES signature');
        }

        return strtoupper(substr(bin2hex($encrypted), 0, 32));
    }
}
