<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Presenters\PaymentPresenter;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Omnipay;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * The simulator accepts the requests built by the tomaj/omnipay-* gateways in test mode (omnipay-tatra master, the
 * released omnipay-vub and omnipay-slsp), and the libraries accept the simulator's signed return URLs.
 *
 * DES needs the OpenSSL legacy provider (OPENSSL_CONF=docker/php/openssl-legacy.cnf, as in CI); without it the DES
 * cases are skipped.
 */
final class OmnipayLibrariesTest extends AppTestCase
{
    private const string RURL = 'https://shop.example/return';

    /**
     * @return iterable<string, array{string, array<string, mixed>, string, array<string, mixed>, string}>
     */
    public static function gateways(): iterable
    {
        $tatra = ['mid' => '9999', 'sharedSecret' => PaymentPresenter::HMAC_KEY, 'testHost' => self::HOST, 'testMode' => true];
        $aes = ['sharedSecret' => PaymentPresenter::AES_KEY] + $tatra;
        $des = ['sharedSecret' => PaymentPresenter::DES_KEY] + $tatra;
        $payment = ['amount' => '10.50', 'currency' => 'EUR', 'vs' => '1234567890', 'rurl' => self::RURL, 'timestamp' => '01012026101010'];
        $card = ['ipc' => '1.2.3.4', 'name' => 'Jan'];

        yield 'TatraPay HMAC' => ['TatraPay', $tatra, 'purchase', $payment + ['cs' => '0308', 'rem' => 'a@b.sk'], 'completePurchase'];
        yield 'TatraPay AES' => ['TatraPay', $aes, 'purchase', $payment + ['cs' => '0308'], 'completePurchase'];
        yield 'TatraPay DES' => ['TatraPay', $des, 'purchase', $payment + ['cs' => '0308'], 'completePurchase'];
        // #34: the specific symbol is signed in the request and in the return of every key type
        yield 'TatraPay HMAC with SS' => ['TatraPay', $tatra, 'purchase', $payment + ['ss' => '77', 'cs' => '0308', 'rem' => 'a@b.sk'], 'completePurchase'];
        yield 'TatraPay AES with SS' => ['TatraPay', $aes, 'purchase', $payment + ['ss' => '77', 'cs' => '0308'], 'completePurchase'];
        yield 'TatraPay DES with SS' => ['TatraPay', $des, 'purchase', $payment + ['ss' => '77', 'cs' => '0308'], 'completePurchase'];
        yield 'CardPay HMAC' => ['CardPay', $tatra, 'purchase', $payment + $card + ['rem' => 'a@b.sk'], 'completePurchase'];
        yield 'CardPay AES' => ['CardPay', $aes, 'purchase', $payment + $card + ['cs' => '0308'], 'completePurchase'];
        yield 'CardPay DES' => ['CardPay', $des, 'purchase', $payment + $card + ['cs' => '0308'], 'completePurchase'];
        yield 'ComfortPay HMAC' => ['ComfortPay', $tatra, 'purchase', $payment + $card + ['rem' => 'a@b.sk'], 'completePurchase'];
        yield 'ComfortPay AES' => ['ComfortPay', $aes, 'purchase', $payment + $card + ['cs' => '0308'], 'completePurchase'];
        yield 'CardPay authorize with card registration' => ['CardPay', $tatra, 'authorize', $payment + ['ipc' => '1.2.3.4', 'name' => 'Jan', 'tpay' => 'Y', 'tdsEmail' => 'a@b.sk'], 'completeAuthorize'];
        yield 'VUB ePlatby' => ['Eplatby', ['mid' => '9999', 'sharedSecret' => PaymentPresenter::EPLATBY_KEY, 'testHost' => self::HOST, 'testMode' => true], 'purchase', ['amount' => '10.50', 'vs' => '1234567890', 'cs' => '0308', 'rurl' => self::RURL], 'completePurchase'];
        yield 'SporoPay' => ['SporoPay', ['sharedSecret' => PaymentPresenter::SPOROPAY_KEY, 'testHost' => self::HOST, 'testMode' => true], 'purchase', ['accountNumberPrefix' => '000000', 'accountNumber' => '0013662162', 'amount' => '10.50', 'vs' => '1234567890', 'ss' => '77', 'rurl' => self::RURL, 'param' => 'abc=1'], 'completePurchase'];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $parameters
     */
    #[DataProvider('gateways')]
    public function testRoundTrip(string $name, array $config, string $operation, array $parameters, string $completeOperation): void
    {
        if ($config['sharedSecret'] === PaymentPresenter::DES_KEY && @openssl_encrypt('x', 'des-ecb', '11111111') === false) {
            self::markTestSkipped('Single DES needs the OpenSSL legacy provider');
        }
        $gateway = Omnipay::create($name);
        $gateway->initialize($config);

        $request = $this->operation($gateway, $operation, $parameters);
        $response = $request->send();
        self::assertInstanceOf(RedirectResponseInterface::class, $response);

        $page = $this->open($response);
        self::assertStringContainsString('Sign OK', $page->body, $page->body);

        foreach (['continue ok' => true, 'continue fail' => false, 'continue timeout' => false] as $link => $successful) {
            $url = $page->link($link);
            self::assertNotNull($url);
            self::assertStringStartsWith(self::RURL . '?', $url);

            // omnipay-tatra reads the customer's request given to the gateway, omnipay-vub and omnipay-slsp read $_GET
            $back = Omnipay::create($name, null, Request::create($url));
            $back->initialize($config);
            $previous = $_GET;
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $_GET = $query;
            try {
                $result = $this->operation($back, $completeOperation, $parameters)->send();
            } finally {
                $_GET = $previous;
            }
            self::assertSame($successful, $result->isSuccessful(), $link);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function operation(GatewayInterface $gateway, string $operation, array $parameters): RequestInterface
    {
        return match ($operation) {
            'purchase' => $gateway->purchase($parameters),
            'completePurchase' => $gateway->completePurchase($parameters),
            'authorize' => $gateway->authorize($parameters),
            'completeAuthorize' => $gateway->completeAuthorize($parameters),
            default => throw new \LogicException("Unknown operation {$operation}"),
        };
    }

    private function open(RedirectResponseInterface $response): HttpResult
    {
        // only the path matters here: the gateways point at self::HOST, the app runs in-process
        $url = $response->getRedirectUrl();
        $path = (string) parse_url($url, PHP_URL_PATH);
        $data = $response->getRedirectData();

        if ($response->getRedirectMethod() === 'POST') {
            return $this->request('POST', $path, post: $data);
        }
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return $this->get($path, $query);
    }
}
