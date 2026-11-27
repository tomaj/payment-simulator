<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Presenters\PaymentPresenter;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Omnipay;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * The simulator accepts the requests built by the tomaj/omnipay-* gateways in test mode (the master branches of
 * omnipay-tatra 5, omnipay-vub 4 and omnipay-slsp 4), and the libraries accept the simulator's signed return URLs.
 * completePurchase() gets the order's VS, SS and amount, so the libraries check that the return belongs to it.
 *
 * DES needs the OpenSSL legacy provider (OPENSSL_CONF=docker/php/openssl-legacy.cnf, as in CI); without it the DES
 * cases are skipped.
 */
final class OmnipayLibrariesTest extends AppTestCase
{
    private const string RURL = 'https://shop.example/return';

    private const array EPLATBY = ['mid' => '9999', 'sharedSecret' => PaymentPresenter::EPLATBY_KEY, 'testHost' => self::HOST, 'testMode' => true];

    private const array EPLATBY_PAYMENT = ['amount' => '10.50', 'vs' => '1234567890', 'ss' => '77', 'cs' => '0308', 'rurl' => self::RURL];

    private const array SPOROPAY = ['sharedSecret' => PaymentPresenter::SPOROPAY_KEY, 'testHost' => self::HOST, 'testMode' => true];

    private const array SPOROPAY_PAYMENT = ['accountNumberPrefix' => '000000', 'accountNumber' => '0013662162', 'amount' => '10.50', 'vs' => '1234567890', 'ss' => '77', 'rurl' => self::RURL, 'param' => 'abc=1'];

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
        yield 'VUB ePlatby' => ['Eplatby', self::EPLATBY, 'purchase', ['amount' => '10.50', 'vs' => '1234567', 'cs' => '0308', 'rurl' => self::RURL], 'completePurchase'];
        // omnipay-vub 4 signs SS in VÚB's order (MID AMT VS SS CS RURL, return VS SS RES) and requires a 10-digit VS with SS
        yield 'VUB ePlatby with SS' => ['Eplatby', self::EPLATBY, 'purchase', self::EPLATBY_PAYMENT, 'completePurchase'];
        yield 'SporoPay' => ['SporoPay', self::SPOROPAY, 'purchase', self::SPOROPAY_PAYMENT, 'completePurchase'];
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

            // the gateways read the return from the request they are given, not from $_GET
            $result = $this->operation($this->returnGateway($name, $config, $url), $completeOperation, $parameters)->send();
            self::assertSame($successful, $result->isSuccessful(), $link);
        }
    }

    /**
     * A genuine, correctly signed return of one order must not complete another order, and a tampered return must
     * fail the signature check.
     *
     * @return iterable<string, array{0: string, 1: array<string, mixed>, 2: array<string, mixed>, 3: array<string, mixed>, 4: array<string, string>, 5: string, 6?: string}>
     */
    public static function rejectedReturns(): iterable
    {
        $eplatby = ['Eplatby', self::EPLATBY, self::EPLATBY_PAYMENT];
        $sporoPay = ['SporoPay', self::SPOROPAY, self::SPOROPAY_PAYMENT];

        yield 'VUB ePlatby: another order' => [...$eplatby, ['vs' => '1234567891'], [], 'Variable symbol mismatch'];
        yield 'VUB ePlatby: another specific symbol' => [...$eplatby, ['ss' => '78'], [], 'Specific symbol mismatch'];
        yield 'VUB ePlatby: order without a specific symbol' => [...$eplatby, ['ss' => null], [], 'Specific symbol mismatch'];
        // VÚB signs VS + SS without a separator: digits moved from SS to VS keep the signed string
        yield 'VUB ePlatby: digits moved between VS and SS' => [...$eplatby, ['vs' => '12345678907', 'ss' => '7'], ['VS' => '12345678907', 'SS' => '7'], 'The vs parameter must be exactly 10 digits when ss is set'];
        yield 'VUB ePlatby: tampered result' => [...$eplatby, [], ['RES' => 'OK'], 'incorect signature', 'continue fail'];
        yield 'SporoPay: another amount' => [...$sporoPay, ['amount' => '1.00'], [], 'Amount mismatch'];
        yield 'SporoPay: tampered amount' => [...$sporoPay, ['amount' => '1.00'], ['suma' => '1.00'], 'incorect signature'];
        yield 'SporoPay: another order' => [...$sporoPay, ['vs' => '1234567891'], [], 'Variable symbol mismatch'];
        yield 'SporoPay: another specific symbol' => [...$sporoPay, ['ss' => '78'], [], 'Specific symbol mismatch'];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payment the order paid at the simulator
     * @param array<string, mixed> $order the order completePurchase() is called for (overrides $payment)
     * @param array<string, string> $tamper query parameters changed in the simulator's signed return URL
     */
    #[DataProvider('rejectedReturns')]
    public function testReturnIsRejected(string $name, array $config, array $payment, array $order, array $tamper, string $message, string $link = 'continue ok'): void
    {
        $gateway = Omnipay::create($name);
        $gateway->initialize($config);
        $response = $gateway->purchase($payment)->send();
        self::assertInstanceOf(RedirectResponseInterface::class, $response);
        $page = $this->open($response);
        self::assertStringContainsString('Sign OK', $page->body, $page->body);

        $url = $page->link($link);
        self::assertNotNull($url);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $url = self::RURL . '?' . http_build_query(array_replace($query, $tamper));

        try {
            $this->operation($this->returnGateway($name, $config, $url), 'completePurchase', array_replace($payment, $order))->send();
            self::fail('The return was accepted');
        } catch (InvalidRequestException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function returnGateway(string $name, array $config, string $url): GatewayInterface
    {
        $gateway = Omnipay::create($name, null, Request::create($url));
        $gateway->initialize($config);

        return $gateway;
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
