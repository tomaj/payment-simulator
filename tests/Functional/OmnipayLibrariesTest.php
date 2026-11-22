<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Presenters\PaymentPresenter;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Omnipay;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The simulator accepts the requests built by the released tomaj/omnipay-* gateways in test mode,
 * and the libraries accept the simulator's signed return URLs.
 *
 * omnipay-tatra 4.x cannot build AES/DES requests on PHP 8 (openssl_random_pseudo_bytes(0)); those
 * algorithms are covered by the openssl CLI vectors in Tests\Model\GatewaySignTest.
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
        $payment = ['amount' => '10.50', 'currency' => 'EUR', 'vs' => '1234567890', 'rurl' => self::RURL, 'timestamp' => '01012026101010'];

        yield 'TatraPay HMAC' => ['TatraPay', $tatra, 'purchase', $payment + ['cs' => '0308', 'rem' => 'a@b.sk'], 'completePurchase'];
        yield 'CardPay HMAC' => ['CardPay', $tatra, 'purchase', $payment + ['ipc' => '1.2.3.4', 'name' => 'Jan', 'rem' => 'a@b.sk'], 'completePurchase'];
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

            $previous = $_GET;
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $_GET = $query;
            try {
                $result = $this->operation($gateway, $completeOperation, $parameters)->send();
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
        // the authorize endpoint of omnipay-tatra 4.x has the live simulator host hard-coded; only the path matters here
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
