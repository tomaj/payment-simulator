<?php

declare(strict_types=1);

namespace App\Model\Sign;

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
        return self::string(new TatraHmacSign()->sign($input, $secret));
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
