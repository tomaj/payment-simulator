<?php

declare(strict_types=1);

namespace App\Model\Sign;

use Omnipay\Core\Exception\SignatureException;
use Omnipay\Core\Sign\Aes256Sign;
use Omnipay\Core\Sign\DesSign;
use Omnipay\Core\Sign\HmacSign as TatraHmacSign;
use Omnipay\Eplatby\Sign\HmacSign as EplatbyHmacSign;
use Omnipay\SporoPay\Sign\Des3Sign;

/**
 * The signers of the tomaj/omnipay-* libraries, with native return types.
 */
final class LibrarySign
{
    /**
     * Tatra banka HMAC (TatraPay, CardPay, ComfortPay): HMAC-SHA256 with the hex-encoded key, lowercase hex.
     */
    public static function tatraHmac(string $input, string $secret): string
    {
        return new TatraHmacSign()->sign($input, $secret);
    }

    /**
     * Tatra banka AES (TatraPay, CardPay, ComfortPay): the first 16 bytes of SHA-1, AES-128-ECB with the first 16 bytes
     * of the hex-decoded key, uppercase hex.
     */
    public static function tatraAes(string $input, string $secret): string
    {
        try {
            return new Aes256Sign()->sign($input, $secret);
        } catch (SignatureException $e) {
            throw new \RuntimeException('Unable to compute the AES signature', 0, $e);
        }
    }

    /**
     * Tatra banka DES (TatraPay, CardPay): SHA-1 encrypted with DES-ECB, the first 16 hex characters, uppercase.
     * Single DES is only available in OpenSSL 3 when the legacy provider is enabled.
     */
    public static function tatraDes(string $input, string $secret): string
    {
        try {
            return new DesSign()->sign($input, $secret);
        } catch (SignatureException $e) {
            throw new \RuntimeException('Unable to compute the DES signature (single DES needs the OpenSSL legacy provider)', 0, $e);
        }
    }

    /**
     * VÚB e-platby HMAC-SHA256.
     */
    public static function eplatbyHmac(string $input, string $secret): string
    {
        return self::string(new EplatbyHmacSign()->sign($input, $secret));
    }

    /**
     * SLSP SporoPay 3DES.
     */
    public static function sporoPay3Des(string $input, string $secret): string
    {
        return self::string(new Des3Sign()->sign($input, $secret));
    }

    private static function string(mixed $signature): string
    {
        if (!is_string($signature)) {
            throw new \RuntimeException('The signer did not return a string');
        }

        return $signature;
    }
}
