<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\Gateways\CardPay\CardPayAES256Sign;
use App\Model\Gateways\CardPay\CardPayAuthorizeHmacSign;
use App\Model\Gateways\CardPay\CardPayDESSign;
use App\Model\Gateways\CardPay\CardPayHmacSign;
use App\Model\Gateways\ComfortPay\ComfortPayAes256Sign;
use App\Model\Gateways\ComfortPay\ComfortPayHmacSign;
use App\Model\Gateways\Eplatby\VubEplatbyHmacSign;
use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\SporoPay\SporoPay3DesSign;
use App\Model\Gateways\TatraPay\TatraPayAES256Sign;
use App\Model\Gateways\TatraPay\TatraPayDESSign;
use App\Model\Gateways\TatraPay\TatraPayHmacSign;
use App\Presenters\PaymentPresenter;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every expected signature was computed independently with the openssl CLI from the documented base string:
 *
 * - HMAC (Tatra banka): printf '%s' "$base" | openssl dgst -sha256 -mac HMAC -macopt hexkey:$HMAC_KEY
 * - AES (Tatra banka): printf '%s' "$base" | openssl dgst -sha1 -binary | head -c 16 \
 *       | openssl enc -aes-128-ecb -nopad -K <first 32 hex characters of the key> | xxd -p -u
 * - DES (Tatra banka): printf '%s' "$base" | openssl dgst -sha1 -binary | head -c 8 \
 *       | openssl enc -des-ecb -nopad -provider legacy -provider default -K 3131313131313131 | xxd -p -u
 * - HMAC (VÚB e-platby): printf '%s' "$base" | openssl dgst -sha256 -mac HMAC -macopt key:$EPLATBY_KEY (uppercase)
 * - 3DES (SporoPay): SHA-1 of the base padded with four 0xFF bytes, openssl enc -des-ede3-cbc -nopad -iv 0
 *       with the base64-decoded key + its first 8 bytes, base64
 */
final class GatewaySignTest extends TestCase
{
    private const string RURL = 'https://shop.example/return?x=1';

    private const string TS = '01012026101010';

    /**
     * @return iterable<string, array{\Closure(): GatewaySign, bool, string, string, string}>
     */
    public static function signatures(): iterable
    {
        $r = self::RURL;
        $ts = self::TS;

        // base 9999 10.50 978 1234567890 0308 RURL
        yield 'TatraPay AES' => [
            static fn(): GatewaySign => new TatraPayAES256Sign(PaymentPresenter::AES_KEY, '9999', '10.50', '978', '1234567890', '0308', $r),
            false,
            '71856A820413D0CAF1A95F39CE584F44',
            'OK', // base 1234567890 OK
            "{$r}&VS=1234567890&RES=OK&SIGN=2BC858513AF8471E9F64B3C614D858EE",
        ];
        yield 'TatraPay DES' => [
            static fn(): GatewaySign => new TatraPayDESSign(PaymentPresenter::DES_KEY, '9999', '10.50', '978', '1234567890', '0308', $r),
            true,
            'D7293CE4262D26BF',
            'FAIL', // base 1234567890 FAIL
            "{$r}&VS=1234567890&RES=FAIL&SIGN=424F7BF262BAAA25",
        ];
        // base 9999 10.50 978 1234567890 0308 RURL a@b.sk TIMESTAMP
        yield 'TatraPay HMAC' => [
            static fn(): GatewaySign => new TatraPayHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', '0308', $r, 'a@b.sk', $ts),
            false,
            '657c5938a3f1505129bf8407e8c8dcf4f6e829e383b1abe99745fc47769d384f',
            'OK', // base 10.50 978 1234567890 0308 OK TIMESTAMP
            "{$r}&VS=1234567890&RES=OK&AMT=10.50&CURR=978&CS=0308&TIMESTAMP={$ts}&HMAC=98f1c669e7c18f1ee98d2d2fc1cf89be3d55a9cb311d9adb27dfd56d86433ee1",
        ];
        // base 9999 10.50 978 1234567890 0308 RURL 1.2.3.4 Jan
        yield 'CardPay AES' => [
            static fn(): GatewaySign => new CardPayAES256Sign(PaymentPresenter::AES_KEY, '9999', '10.50', '978', '1234567890', '0308', $r, '', '1.2.3.4', 'Jan'),
            false,
            '1D76B8D01CD2CFCB5BEED1CC995ED8A3',
            'OK', // base 1234567890 OK 111111
            "{$r}&VS=1234567890&AC=111111&RES=OK&SIGN=1E0601B06B349F115D849BA6264ED1C5",
        ];
        yield 'CardPay DES' => [
            static fn(): GatewaySign => new CardPayDESSign(PaymentPresenter::DES_KEY, '9999', '10.50', '978', '1234567890', '0308', $r, '1.2.3.4', 'Jan'),
            true,
            'F3597C40710AD01E',
            'TOUT', // base 1234567890 TOUT 111111
            "{$r}&VS=1234567890&AC=111111&RES=TOUT&SIGN=44C66C290D1FF460",
        ];
        // base 9999 10.50 978 1234567890 PA RURL 1.2.3.4 Jan a@b.sk Y TIMESTAMP
        yield 'CardPay HMAC with card registration' => [
            static fn(): GatewaySign => new CardPayHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, 'a@b.sk', $ts, '1.2.3.4', 'Jan', 'PA', 'Y'),
            false,
            '2d170cfbc4fe5b3a32f374b8e21a56360d5bae543bea91772598f5c515fe63e0',
            'OK', // base 10.50 978 1234567890 PA OK 111111 OK 123456 1111********1111 TIMESTAMP
            "{$r}&VS=1234567890&TXN=PA&RES=OK&TRES=OK&AC=111111&AMT=10.50&CURR=978&CID=123456&CC=1111********1111&AC=111111&TIMESTAMP={$ts}&HMAC=9da8ed518e4d96c16f8df14170fe3fe6f08bc7330c61960fc1ae91c931e910c4",
        ];
        yield 'CardPay HMAC, failed payment' => [
            static fn(): GatewaySign => new CardPayHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, 'a@b.sk', $ts, '1.2.3.4', 'Jan', 'PA', 'Y'),
            false,
            '2d170cfbc4fe5b3a32f374b8e21a56360d5bae543bea91772598f5c515fe63e0',
            'FAIL', // base 10.50 978 1234567890 PA FAIL 111111 FAIL 1111********1111 TIMESTAMP (no CID)
            "{$r}&VS=1234567890&TXN=PA&RES=FAIL&TRES=FAIL&AC=111111&AMT=10.50&CURR=978&CID=&CC=1111********1111&AC=111111&TIMESTAMP={$ts}&HMAC=" . hash_hmac('sha256', "10.509781234567890PAFAIL111111FAIL1111********1111{$ts}", (string) hex2bin(PaymentPresenter::HMAC_KEY)),
        ];
        yield 'CardPay HMAC without card registration' => [
            static fn(): GatewaySign => new CardPayHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, 'a@b.sk', $ts),
            false,
            hash_hmac('sha256', "999910.509781234567890{$r}a@b.sk{$ts}", (string) hex2bin(PaymentPresenter::HMAC_KEY)),
            'FAIL', // base 10.50 978 1234567890 FAIL 111111 1111********1111 TIMESTAMP
            "{$r}&VS=1234567890&TXN=&RES=FAIL&TRES=&AC=111111&AMT=10.50&CURR=978&CID=&CC=1111********1111&AC=111111&TIMESTAMP={$ts}&HMAC=ee881f44d517ff78aed91617948e1dbf01070381a92dcb1a13d8ab6bf539e58f",
        ];
        // base 9999 10.50 978 1234567890 RURL 1.2.3.4 Jan a@b.sk Y TIMESTAMP
        yield 'ComfortPay HMAC' => [
            static fn(): GatewaySign => new ComfortPayHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, 'a@b.sk', 'Y', $ts, '1.2.3.4', 'Jan'),
            false,
            '9214c68942fe1aeb54fd8e50321b8582a4097620156f4b5f2c398928092bbc8c',
            'OK', // base 10.50 978 1234567890 OK 111111 OK 111111 1111********1111 123 TIMESTAMP
            "{$r}&VS=1234567890&AMT=10.50&CURR=978&RES=OK&TRES=OK&AC=111111&CID=111111&CC=1111********1111&RC=123&TIMESTAMP={$ts}&HMAC=15e365ba79c3c749966baee4e8ff07273394a4e20dbe151a92df6f7d23d886c9",
        ];
        // base 9999 10.50 978 1234567890 0308 RURL 1.2.3.4 Jan Y
        yield 'ComfortPay AES' => [
            static fn(): GatewaySign => new ComfortPayAes256Sign(PaymentPresenter::AES_KEY, '9999', '10.50', '978', '1234567890', '0308', $r, 'Y', '1.2.3.4', 'Jan'),
            false,
            'A9A8751CCB57D192B56CAD908FFB9AFB',
            'FAIL', // base 1234567890 FAIL 111111 111111
            "{$r}&VS=1234567890&RES=FAIL&TRES=FAIL&AC=111111&CID=111111&SIGN=821531D9EE9224B8641FEC8E75C4A829",
        ];
        // base 9999 10.50 1234567890 0308 RURL
        yield 'VUB ePlatby' => [
            static fn(): GatewaySign => new VubEplatbyHmacSign(PaymentPresenter::EPLATBY_KEY, '9999', '10.50', '1234567890', '0308', $r),
            false,
            '0E1A93FA39AC3985979DD1FA6614A558C73128CE0FF3F9C37BE98EB52DB4B2E2',
            'OK', // base 1234567890 OK
            "{$r}&VS=1234567890&RES=OK&SIGN=4D96F6255476F740F3DCC1B9D10650C7A25004DB048C058E46552E11839430E6",
        ];
        // base 000000;0013662162;0900;10.50;EUR;1234567890;77;RURL;abc=1
        yield 'SporoPay' => [
            static fn(): GatewaySign => new SporoPay3DesSign(PaymentPresenter::SPOROPAY_KEY, '000000', '0013662162', '0900', '10.50', 'EUR', '1234567890', '77', $r, 'abc=1'),
            false,
            'GITjtiThgyt+ampYH7L3fDvXNyvVNlYw',
            'OK', // base 123456;9876543210;0900;000000;0013662162;0900;10.50;EUR;1234567890;77;RURL;abc=1;OK;OK
            "{$r}&u_predcislo=123456&u_cislo=9876543210&u_kbanky=0900&pu_predcislo=000000&pu_cislo=0013662162&pu_kbanky=0900&suma=10.50&mena=EUR&vs=1234567890&ss=77&url=" . rawurlencode($r) . '&param=abc%3D1&result=OK&real=OK&SIGN2=' . rawurlencode('HjTvI/ECe2Qwx2yZ3sVYlRl0JYaDfnJ2'),
        ];
        // base 9999 10.50 978 1234567890 E2E PA RURL 1.2.3.4 Jan a@b.sk Y CID1 ECID1 (TDS_EMAIL) a@b.sk TIMESTAMP
        yield 'CardPay authorize with card registration' => [
            static fn(): GatewaySign => new CardPayAuthorizeHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, '1.2.3.4', 'Jan', $ts, 'E2E', 'PA', 'a@b.sk', 'Y', 'CID1', 'ECID1', tdsEmail: 'a@b.sk'),
            false,
            '59b2ac62ac7f3cb56c5a66de23c00d640603fb2cd7285d4d5eee009e7445d570',
            'OK', // base 10.50 978 1234567890 PA OK 31232 OK 123456 ************1234 567898 TIMESTAMP
            "{$r}&AMT=10.50&CURR=978&VS=1234567890&TXN=PA&RES=OK&AC=31232&TRES=OK&CID=123456&CC=************1234&RC=&TID=567898&TIMESTAMP={$ts}&HMAC=3cb01b02fb0f229c258409deb00bfe262184ee04dc90620f2927add3205dffe0",
        ];
        yield 'CardPay authorize, failed' => [
            static fn(): GatewaySign => new CardPayAuthorizeHmacSign(PaymentPresenter::HMAC_KEY, '9999', '10.50', '978', '1234567890', $r, '1.2.3.4', 'Jan', $ts),
            false,
            hash_hmac('sha256', "999910.509781234567890{$r}1.2.3.4Jan{$ts}", (string) hex2bin(PaymentPresenter::HMAC_KEY)),
            'FAIL', // base 10.50 978 1234567890 FAIL ************1234 01 TIMESTAMP
            "{$r}&AMT=10.50&CURR=978&VS=1234567890&TXN=&RES=FAIL&AC=&TRES=&CID=&CC=************1234&RC=01&TID=&TIMESTAMP={$ts}&HMAC=1ac7cf0fe33f55c2fa0f430afce48a9d2fb6899763ba8f6efa90b936c6734fc5",
        ];
    }

    /**
     * @param \Closure(): GatewaySign $create
     */
    #[DataProvider('signatures')]
    public function testSignatureAndReturnUrl(\Closure $create, bool $des, string $sign, string $result, string $returnUrl): void
    {
        if ($des) {
            self::requireDes();
        }
        $gateway = $create();

        self::assertSame($sign, $gateway->sign());
        self::assertSame($returnUrl, $gateway->returnUrlSign($result));
    }

    public function testReturnUrlWithoutQuery(): void
    {
        $gateway = new TatraPayAES256Sign(PaymentPresenter::AES_KEY, '9999', '10.50', '978', '1234567890', '0308', 'https://shop.example/return');

        self::assertSame('https://shop.example/return?VS=1234567890&RES=OK&SIGN=2BC858513AF8471E9F64B3C614D858EE', $gateway->returnUrlSign('OK'));
    }
}
