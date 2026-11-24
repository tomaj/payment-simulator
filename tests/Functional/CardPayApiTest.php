<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Presenters\PaymentPresenter;
use Nette\Utils\Json;

/**
 * CardPay cancel (XML) and the CardPay Direct API (JSON, Apple Pay / Google Pay, 3-D Secure form).
 * HMACs are computed here with hash_hmac(), independently of the app's signer.
 */
final class CardPayApiTest extends AppTestCase
{
    private function hmac(string $input): string
    {
        return hash_hmac('sha256', $input, (string) hex2bin(PaymentPresenter::HMAC_KEY));
    }

    /**
     * @return array<string, string>
     */
    private function cancelQuery(): array
    {
        $q = ['MID' => '9999', 'AMT' => '10.50', 'TID' => '1', 'VS' => '123', 'TXN' => 'CA', 'REM' => '', 'TIMESTAMP' => '01012026101010'];
        $q['HMAC'] = $this->hmac(implode('', $q));

        return $q;
    }

    public function testCancelWithValidHmac(): void
    {
        $result = $this->get('/payment/cardpay-cancel-purchase', $this->cancelQuery());

        self::assertSame(200, $result->code);
        self::assertSame('text/xml', $result->header('Content-Type'));
        self::assertStringContainsString('<res>OK</res>', $result->body);
        self::assertStringContainsString("<mid>9999</mid>\n        <amt>10.50</amt>\n        <tid>1</tid>\n        <txn>CA</txn>\n        <timestamp>01012026101010</timestamp>", $result->body);
        self::assertSame('HMAC=' . $this->hmac($result->body) . ',ECDSA=3046022100a4e5491ab9376f8cd9e51c176f5e75bb3664e3f852e901d36ac0a5b0f2177fad0221008794e59ab8027c68ae74ea65d0f5b5913865db9a818044d06c114f4d84346f48, ECDSA_KEY=1', $result->header('Authorization'));
    }

    public function testCancelWithWrongHmacFails(): void
    {
        $query = $this->cancelQuery();
        $query['MID'] = '9999<x>';

        $result = $this->get('/payment/cardpay-cancel-purchase', $query);

        self::assertStringContainsString('<res>FAIL</res>', $result->body);
        self::assertStringContainsString('<errorCode>10</errorCode>', $result->body);
        self::assertStringContainsString('<mid>9999&lt;x&gt;</mid>', $result->body);
        self::assertNull($result->header('Authorization'));
    }

    public function testCancelWithoutHmacFails(): void
    {
        self::assertStringContainsString('<res>FAIL</res>', $this->get('/payment/cardpay-cancel-purchase')->body);
    }

    private function postTransaction(string $body, ?string $authorization = null): HttpResult
    {
        return $this->request(
            'POST',
            '/cgi-bin/e-commerce/start/api/cardpay/transaction',
            headers: ['Authorization' => $authorization ?? 'HMAC=' . $this->hmac($body)],
            body: $body,
        );
    }

    /**
     * @return array<mixed>
     */
    private function json(HttpResult $result): array
    {
        $data = Json::decode($result->body, forceArrays: true);
        self::assertIsArray($data);

        return $data;
    }

    private function assertSignedJson(HttpResult $result): void
    {
        self::assertSame(200, $result->code);
        self::assertSame('HMAC=' . $this->hmac($result->body) . ', ECDSA=ecdsa, ECDSA_KEY=1', $result->header('Authorization'));
    }

    public function testDirectApiApplePay(): void
    {
        $result = $this->postTransaction('{"amount":999,"applePayToken":"x"}');

        $this->assertSignedJson($result);
        $data = $this->json($result);
        self::assertSame('OK', $data['status']);
        self::assertIsInt($data['processingId']);
        self::assertGreaterThanOrEqual(10000, $data['processingId']);
        $transaction = $data['transactionData'];
        self::assertIsArray($transaction);
        self::assertSame('00', $transaction['responseCode']);
        self::assertIsString($transaction['authorizationCode']);
        self::assertMatchesRegularExpression('~^[A-Z]{2}[0-9]{4}$~', $transaction['authorizationCode']);
    }

    public function testDirectApiGooglePayAbove10RequiresTds(): void
    {
        $result = $this->postTransaction('{"amount":1000,"googlePayToken":"x","tdsTermUrl":"https://shop.example/tds?a=1&b=\"2"}');

        $this->assertSignedJson($result);
        $data = $this->json($result);
        self::assertSame('TDS_AUTH_REQUIRED', $data['status']);
        $form = $data['tdsRedirectionFormHtml'];
        self::assertIsString($form);
        self::assertIsInt($data['processingId']);
        self::assertStringContainsString('action="http://simulator.test/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds"', $form);
        self::assertStringContainsString('name="tdsTermUrl" value="https://shop.example/tds?a=1&amp;b=&quot;2"', $form);
        self::assertStringContainsString('name="processingId" value="' . $data['processingId'] . '"', $form);
    }

    public function testDirectApiTdsFormKeepsThePort(): void
    {
        $body = '{"amount":1000,"googlePayToken":"x","tdsTermUrl":"https://shop.example/tds"}';
        $result = $this->request(
            'POST',
            '/cgi-bin/e-commerce/start/api/cardpay/transaction',
            headers: ['Authorization' => 'HMAC=' . $this->hmac($body)],
            body: $body,
            host: 'http://127.0.0.1:8080',
        );

        $form = $this->json($result)['tdsRedirectionFormHtml'];
        self::assertIsString($form);
        self::assertStringContainsString('action="http://127.0.0.1:8080/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds"', $form);
    }

    public function testDirectApiGooglePayBelow10(): void
    {
        self::assertSame('OK', $this->json($this->postTransaction('{"amount":999,"googlePayToken":"x"}'))['status']);
    }

    public function testDirectApiAmountFrom100Fails(): void
    {
        self::assertSame('FAIL', $this->json($this->postTransaction('{"amount":10000,"googlePayToken":"x"}'))['status']);
        self::assertSame('FAIL', $this->json($this->postTransaction('{"amount":"10000"}'))['status']);
    }

    public function testDirectApiRejectsAWrongAuthorization(): void
    {
        foreach (['HMAC=wrong', 'nothing'] as $header) {
            $result = $this->postTransaction('{"amount":1}', $header);

            self::assertSame(400, $result->code);
            self::assertSame(['error' => 'Invalid authorization header'], $this->json($result));
        }
    }

    public function testDirectApiRejectsAMissingAuthorization(): void
    {
        $result = $this->request('POST', '/cgi-bin/e-commerce/start/api/cardpay/transaction', body: '{"amount":1}');

        self::assertSame(400, $result->code);
        self::assertSame(['error' => 'Invalid authorization header'], $this->json($result));
    }

    public function testDirectApiInvalidJson(): void
    {
        self::assertSame(['error' => 'Cannot process Google Pay token'], $this->json($this->postTransaction('{')));
    }

    public function testDirectApiMissingAmount(): void
    {
        foreach (['{}', '"text"', '{"amount":"x"}'] as $body) {
            $result = $this->postTransaction($body);

            self::assertSame(400, $result->code, $body);
            self::assertSame(['error' => 'Missing or invalid amount'], $this->json($result));
        }
    }

    public function testCheckTransaction(): void
    {
        $result = $this->request('GET', '/cgi-bin/e-commerce/start/api/cardpay/transaction/12345', headers: [
            'X-Merchant-Id' => '9999',
            'X-Timestamp' => '01012026101010',
            'Authorization' => 'HMAC=' . $this->hmac('9999;01012026101010;12345'),
        ]);

        $this->assertSignedJson($result);
        $data = $this->json($result);
        self::assertSame('12345', $data['processingId']);
        self::assertSame('OK', $data['status']);
    }

    public function testCheckTransactionWithWrongAuthorization(): void
    {
        $result = $this->request('GET', '/cgi-bin/e-commerce/start/api/cardpay/transaction/12345', headers: [
            'X-Merchant-Id' => '9999',
            'X-Timestamp' => '01012026101010',
            'Authorization' => 'HMAC=' . $this->hmac('9999;01012026101010;12346'),
        ]);

        self::assertSame(400, $result->code);
    }

    public function testTdsForm(): void
    {
        $result = $this->request('POST', '/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds', post: ['tdsTermUrl' => 'https://shop.example/tds', 'processingId' => '12345']);

        self::assertSame(200, $result->code);
        self::assertStringContainsString('https://shop.example/tds', $result->body);
    }
}
