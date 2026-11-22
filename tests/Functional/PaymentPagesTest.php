<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every simulated payment page: a correct signature offers the signed return links, a wrong one does not,
 * and a request without its required parameters lists them.
 */
final class PaymentPagesTest extends AppTestCase
{
    private const string RURL = 'https://shop.example/return?x=1';

    /**
     * @return iterable<string, array{string, string, array<string, string>, string, list<string>, bool}>
     */
    public static function pages(): iterable
    {
        $tatra = ['MID' => '9999', 'AMT' => '10.50', 'CURR' => '978', 'VS' => '1234567890', 'CS' => '0308', 'RURL' => self::RURL];
        $hmac = ['REM' => 'a@b.sk', 'TIMESTAMP' => '01012026101010'];
        $card = ['IPC' => '1.2.3.4', 'NAME' => 'Ján <b>Novák</b>'];

        yield 'TatraPay AES' => ['/payment/tatrapay-aes256', 'GET', $tatra, 'SIGN', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'SIGN'], false];
        yield 'TatraPay DES' => ['/payment/tatrapay-des', 'GET', $tatra, 'SIGN', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'SIGN'], true];
        yield 'TatraPay HMAC' => ['/payment/tatrapay-hmac', 'GET', $tatra + $hmac, 'HMAC', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'TIMESTAMP', 'HMAC'], false];
        yield 'CardPay AES' => ['/payment/cardpay-aes256', 'GET', $tatra + $card, 'SIGN', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'IPC', 'NAME', 'SIGN'], false];
        yield 'CardPay DES' => ['/payment/cardpay-des', 'GET', $tatra + $card, 'SIGN', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'IPC', 'NAME', 'SIGN'], true];
        yield 'CardPay HMAC' => ['/payment/cardpay-hmac', 'GET', $tatra + $hmac + $card, 'HMAC', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'TIMESTAMP', 'IPC', 'NAME', 'HMAC'], false];
        yield 'ComfortPay HMAC' => ['/payment/comfortpay-hmac', 'GET', $tatra + $hmac + $card + ['TPAY' => 'Y'], 'HMAC', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'TPAY', 'TIMESTAMP', 'IPC', 'NAME', 'HMAC'], false];
        yield 'ComfortPay AES' => ['/payment/comfortpay-aes256', 'GET', $tatra + $card + ['TPAY' => 'Y'], 'SIGN', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'TPAY', 'IPC', 'NAME', 'SIGN'], false];
        yield 'CardPay authorize' => ['/payment/cardpay-authorize-hmac', 'GET', $tatra + $hmac + $card + ['TPAY' => 'Y', 'TXN' => 'PA'], 'HMAC', ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'IPC', 'NAME', 'TIMESTAMP', 'HMAC'], false];
        yield 'VUB ePlatby' => ['/payment/eplatby-hmac', 'POST', ['MID' => '9999', 'AMT' => '10.50', 'VS' => '1234567890', 'CS' => '0308', 'RURL' => self::RURL], 'SIGN', ['MID', 'AMT', 'VS', 'RURL', 'SIGN'], false];
        yield 'SporoPay' => ['/payment/sporopay-des', 'GET', ['pu_predcislo' => '000000', 'pu_cislo' => '0013662162', 'pu_kbanky' => '0900', 'suma' => '10.50', 'mena' => 'EUR', 'vs' => '1234567890', 'ss' => '77', 'url' => self::RURL, 'param' => 'abc=1'], 'sign1', ['pu_predcislo', 'pu_cislo', 'pu_kbanky', 'suma', 'mena', 'vs', 'ss', 'url', 'param', 'sign1'], false];
    }

    /**
     * @param array<string, string> $params
     */
    private function send(string $path, string $method, array $params): HttpResult
    {
        return $method === 'POST' ? $this->request('POST', $path, post: $params) : $this->get($path, $params);
    }

    /**
     * @param array<string, string> $params
     * @param list<string> $required
     */
    #[DataProvider('pages')]
    public function testWrongSignature(string $path, string $method, array $params, string $signParameter, array $required, bool $des): void
    {
        if ($des) {
            self::requireDes();
        }
        $result = $this->send($path, $method, $params + [$signParameter => 'WRONG']);

        self::assertSame(200, $result->code);
        self::assertStringContainsString('Wrong sign', $result->body);
        self::assertStringNotContainsString('Sign OK', $result->body);
        self::assertNull($result->link('continue ok'));
        // parameters are shown escaped
        self::assertStringNotContainsString('<b>Novák</b>', $result->body);
    }

    /**
     * @param array<string, string> $params
     * @param list<string> $required
     */
    #[DataProvider('pages')]
    public function testCorrectSignature(string $path, string $method, array $params, string $signParameter, array $required, bool $des): void
    {
        if ($des) {
            self::requireDes();
        }
        $sign = $this->send($path, $method, $params + [$signParameter => 'WRONG'])->computedSign();
        self::assertNotNull($sign);
        self::assertNotSame('', $sign);

        $result = $this->send($path, $method, $params + [$signParameter => $sign]);

        self::assertSame(200, $result->code);
        self::assertStringContainsString('Sign OK', $result->body);
        foreach (['continue ok' => 'OK', 'continue fail' => 'FAIL', 'continue timeout' => 'TOUT'] as $text => $res) {
            $link = $result->link($text);
            self::assertNotNull($link, $text);
            self::assertStringStartsWith(self::RURL . '&', $link);
            self::assertMatchesRegularExpression('~[&?](RES|result)=' . $res . '(&|$)~', $link);
        }
    }

    /**
     * @param array<string, string> $params
     * @param list<string> $required
     */
    #[DataProvider('pages')]
    public function testSignatureWithAnotherCaseIsRejected(string $path, string $method, array $params, string $signParameter, array $required, bool $des): void
    {
        if ($des) {
            self::requireDes();
        }
        $sign = (string) $this->send($path, $method, $params + [$signParameter => 'WRONG'])->computedSign();
        $other = strtolower($sign) !== $sign ? strtolower($sign) : strtoupper($sign);

        self::assertStringContainsString('Wrong sign', $this->send($path, $method, $params + [$signParameter => $other])->body);
    }

    /**
     * @param array<string, string> $params
     * @param list<string> $required
     */
    #[DataProvider('pages')]
    public function testMissingParameters(string $path, string $method, array $params, string $signParameter, array $required, bool $des): void
    {
        $result = $this->send($path, $method, []);

        self::assertSame(400, $result->code);
        self::assertStringContainsString('Missing parameters', $result->body);
        preg_match_all('~<li><code>([^<]+)</code></li>~', $result->body, $m);
        self::assertSame($required, $m[1]);
    }

    /**
     * #26: the specific symbol is part of the TatraPay and VÚB signatures; signatures from the openssl CLI
     * (see Tests\Model\GatewaySignTest).
     *
     * @return iterable<string, array{string, string, array<string, string>, string}>
     */
    public static function specificSymbol(): iterable
    {
        $tatra = ['MID' => '9999', 'AMT' => '10.50', 'CURR' => '978', 'VS' => '1234567890', 'SS' => '77', 'CS' => '0308', 'RURL' => self::RURL];

        yield 'TatraPay AES' => ['/payment/tatrapay-aes256', 'GET', $tatra + ['SIGN' => '5C15143CA563C10471B0A23C652DD80F'], '&SS=77&'];
        yield 'TatraPay HMAC' => ['/payment/tatrapay-hmac', 'GET', $tatra + ['REM' => 'a@b.sk', 'TIMESTAMP' => '01012026101010', 'HMAC' => '16d7ac5df8ce67dc947a5047af91ab3b4fcdf55a79d440276328b98c6e6c9a48'], '&SS=77&'];
        yield 'VUB ePlatby' => ['/payment/eplatby-hmac', 'POST', ['MID' => '9999', 'AMT' => '10.50', 'VS' => '1234567890', 'SS' => '77', 'CS' => '0308', 'RURL' => self::RURL, 'SIGN' => '173C182CB6392A8264F8AABC33E2D1D161E2545E4F729E115099BD0212BD682C'], '&SS=77&'];
    }

    /**
     * @param array<string, string> $params
     */
    #[DataProvider('specificSymbol')]
    public function testSpecificSymbolIsSigned(string $path, string $method, array $params, string $returned): void
    {
        $result = $this->send($path, $method, $params);

        self::assertStringContainsString('Sign OK', $result->body);
        self::assertStringContainsString($returned, (string) $result->link('continue ok'));

        unset($params['SS']);
        self::assertStringContainsString('Wrong sign', $this->send($path, $method, $params)->body);
    }

    public function testArrayParameterCountsAsMissing(): void
    {
        $result = $this->get('/payment/tatrapay-aes256', ['MID' => ['9999'], 'AMT' => '1', 'CURR' => '978', 'VS' => '1', 'RURL' => self::RURL, 'SIGN' => 'x']);

        self::assertSame(400, $result->code);
        self::assertStringContainsString('<li><code>MID</code></li>', $result->body);
    }
}
