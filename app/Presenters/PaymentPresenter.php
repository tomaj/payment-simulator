<?php

declare(strict_types=1);

namespace App\Presenters;

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
use App\Model\RequestParameters;
use App\Model\Responses\CancelAuthorizationResponse;
use App\Model\Sign\LibrarySign;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Random;

final class PaymentPresenter extends Presenter
{
    /** Test keys: the simulator is public, so are its keys (see README.md). */
    public const string DES_KEY = '11111111';

    public const string AES_KEY = '1111111111111111111111111111111111111111111111111111111111111111';

    public const string HMAC_KEY = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

    public const string EPLATBY_KEY = '1111111111111111111111111111111111111111111111111111111111111111';

    public const string SPOROPAY_KEY = 'Z3qY08EpvLlAAoMZdnyUdQ==';

    public function renderTatrapayAes256(): void
    {
        $this->simulate($this->queryParameters(), 'SIGN', static fn(RequestParameters $p): GatewaySign => new TatraPayAES256Sign(
            self::AES_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->optional('SS'),
        ));
    }

    public function renderTatrapayDes(): void
    {
        $this->simulate($this->queryParameters(), 'SIGN', static fn(RequestParameters $p): GatewaySign => new TatraPayDESSign(
            self::DES_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->optional('SS'),
        ));
    }

    public function renderTatrapayHmac(): void
    {
        $this->simulate($this->queryParameters(), 'HMAC', static fn(RequestParameters $p): GatewaySign => new TatraPayHmacSign(
            self::HMAC_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->optional('REM'),
            $p->required('TIMESTAMP'),
            $p->optional('SS'),
        ));
    }

    public function renderCardpayAes256(): void
    {
        $this->simulate($this->queryParameters(), 'SIGN', static fn(RequestParameters $p): GatewaySign => new CardPayAES256Sign(
            self::AES_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            '',
            $p->required('IPC'),
            $p->required('NAME'),
        ));
    }

    public function renderCardpayDes(): void
    {
        $this->simulate($this->queryParameters(), 'SIGN', static fn(RequestParameters $p): GatewaySign => new CardPayDESSign(
            self::DES_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->required('IPC'),
            $p->required('NAME'),
        ));
    }

    public function renderCardpayHmac(): void
    {
        $this->simulate($this->queryParameters(), 'HMAC', static fn(RequestParameters $p): GatewaySign => new CardPayHmacSign(
            self::HMAC_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->required('RURL'),
            $p->optional('REM'),
            $p->required('TIMESTAMP'),
            $p->required('IPC'),
            $p->required('NAME'),
        ));
    }

    public function renderComfortpayHmac(): void
    {
        $this->simulate($this->queryParameters(), 'HMAC', static fn(RequestParameters $p): GatewaySign => new ComfortPayHmacSign(
            self::HMAC_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->required('RURL'),
            $p->optional('REM'),
            $p->required('TPAY'),
            $p->required('TIMESTAMP'),
            $p->required('IPC'),
            $p->required('NAME'),
        ));
    }

    public function renderComfortpayAes256(): void
    {
        $this->simulate($this->queryParameters(), 'SIGN', static fn(RequestParameters $p): GatewaySign => new ComfortPayAes256Sign(
            self::AES_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->required('TPAY'),
            $p->required('IPC'),
            $p->required('NAME'),
        ));
    }

    public function renderEplatbyHmac(): void
    {
        // VÚB e-platby posts the form
        $this->simulate(new RequestParameters($this->getHttpRequest()->getPost()), 'SIGN', static fn(RequestParameters $p): GatewaySign => new VubEplatbyHmacSign(
            self::EPLATBY_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('VS'),
            $p->optional('CS'),
            $p->required('RURL'),
            $p->optional('SS'),
        ));
    }

    public function renderSporopayDes(): void
    {
        $this->simulate($this->queryParameters(), 'sign1', static fn(RequestParameters $p): GatewaySign => new SporoPay3DesSign(
            self::SPOROPAY_KEY,
            $p->required('pu_predcislo'),
            $p->required('pu_cislo'),
            $p->required('pu_kbanky'),
            $p->required('suma'),
            $p->required('mena'),
            $p->required('vs'),
            $p->required('ss'),
            $p->required('url'),
            $p->required('param'),
        ));
    }

    public function renderCardpayAuthorizeHmac(): void
    {
        $this->simulate($this->queryParameters(), 'HMAC', static fn(RequestParameters $p): GatewaySign => new CardPayAuthorizeHmacSign(
            self::HMAC_KEY,
            $p->required('MID'),
            $p->required('AMT'),
            $p->required('CURR'),
            $p->required('VS'),
            $p->required('RURL'),
            $p->required('IPC'),
            $p->required('NAME'),
            $p->required('TIMESTAMP'),
            $p->optional('E2E'),
            $p->optional('TXN'),
            $p->optional('REM'),
            $p->optional('TPAY'),
            $p->optional('CID'),
            $p->optional('ECID'),
            $p->optional('TDS_CARDHOLDER'),
            $p->optional('TDS_EMAIL'),
            $p->optional('TDS_MOBILE_PHONE'),
            $p->optional('TDS_BILL_CITY'),
            $p->optional('TDS_BILL_COUNTRY'),
            $p->optional('TDS_BILL_ADDRESS1'),
            $p->optional('TDS_BILL_ADDRESS2'),
            $p->optional('TDS_BILL_ZIP'),
            $p->optional('TDS_SHIP_CITY'),
            $p->optional('TDS_SHIP_COUNTRY'),
            $p->optional('TDS_SHIP_ADDRESS1'),
            $p->optional('TDS_SHIP_ADDRESS2'),
            $p->optional('TDS_SHIP_ZIP'),
            $p->optional('TDS_ADDR_MATCH'),
        ));
    }

    public function actionCardpayCancelPurchase(): void
    {
        $p = $this->queryParameters();
        // one element per line, indented like the rest of the document
        $request = implode("\n        ", [
            '<mid>' . $this->xml($p->optional('MID')) . '</mid>',
            '<amt>' . $this->xml($p->optional('AMT')) . '</amt>',
            '<tid>' . $this->xml($p->optional('TID')) . '</tid>',
            '<txn>' . $this->xml($p->optional('TXN')) . '</txn>',
            '<timestamp>' . $this->xml($p->optional('TIMESTAMP')) . '</timestamp>',
        ]);

        $successXml = <<<XML
            <?xml version="1.0" encoding="windows-1250"?>
                <cardpay>
                <request>
                    {$request}
                </request>
                <result>
                    <res>OK</res>
                </result>
            </cardpay>
            XML;

        $failXml = <<<XML
            <?xml version="1.0" encoding="windows-1250"?>
                <cardpay>
                <request>
                    {$request}
                </request>
                <result>
                    <res>FAIL</res>
                    <errorCode>10</errorCode>
                </result>
            </cardpay>
            XML;

        $input = $p->optional('MID') . $p->optional('AMT') . $p->optional('TID') . $p->optional('VS') . $p->optional('TXN') . $p->optional('REM') . $p->optional('TIMESTAMP');

        $receivedHmac = $p->all()['HMAC'] ?? null;
        if (!is_string($receivedHmac) || !hash_equals(LibrarySign::tatraHmac($input, self::HMAC_KEY), $receivedHmac)) {
            $this->sendResponse(new CancelAuthorizationResponse($failXml, []));
        }

        $this->sendResponse(new CancelAuthorizationResponse($successXml, [
            'Authorization' => 'HMAC=' . LibrarySign::tatraHmac($successXml, self::HMAC_KEY) . ',ECDSA=3046022100a4e5491ab9376f8cd9e51c176f5e75bb3664e3f852e901d36ac0a5b0f2177fad0221008794e59ab8027c68ae74ea65d0f5b5913865db9a818044d06c114f4d84346f48, ECDSA_KEY=1',
        ]));
    }

    public function renderCardPayDirectPayPostTransaction(): void
    {
        $httpRequest = $this->getHttpRequest();
        $requestBody = (string) $httpRequest->getRawBody();

        if (!$this->checkAuthorization($requestBody)) {
            $this->sendError('Invalid authorization header');
        }

        try {
            $body = Json::decode($requestBody, forceArrays: true);
        } catch (JsonException) {
            $this->sendResponse(new JsonResponse([
                'error' => 'Cannot process Google Pay token',
            ]));
        }

        if (!is_array($body) || !isset($body['amount']) || !is_numeric($body['amount'])) {
            $this->sendError('Missing or invalid amount');
        }
        $amount = (float) $body['amount'];
        $isGooglePay = isset($body['googlePayToken']);
        $isTds = false;

        $status = 'OK';
        if ($isGooglePay && $amount >= 10 * 100) {
            $status = 'TDS_AUTH_REQUIRED';
            $isTds = true;
        }
        if ($amount >= 100 * 100) {
            $status = 'FAIL';
        }

        $processingId = 10000 + random_int(0, 9999);

        $data = [
            'processingId' => $processingId,
            'status' => $status, // OK, FAIL, TDS_AUTH_REQUIRED
            'transactionId' => random_int(100000, 199999),
            'transactionData' => [
                'authorizationCode' => $this->generateAuthorizationCode(),
                'responseCode' => '00',
            ],
        ];

        if ($isTds) {
            $timestamp = date('dmYHis');
            $url = $httpRequest->getUrl();
            $action = htmlspecialchars("{$url->getScheme()}://{$url->getHost()}/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds");
            $tdsTermUrl = htmlspecialchars(is_string($body['tdsTermUrl'] ?? null) ? $body['tdsTermUrl'] : '');
            $data['tdsRedirectionFormHtml'] = "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"UTF-8\"><title>3D Secure
Processing</title></head><body><form action=\"{$action}\" method=\"POST\" name=\"redirectionForm\">
<input type=\"hidden\" name=\"tdsTermUrl\" value=\"{$tdsTermUrl}\">
<input type=\"hidden\" name=\"processingId\" value=\"{$processingId}\">
<input type=\"hidden\" name=\"timestamp\" value=\"{$timestamp}\">
<input type=\"hidden\" name=\"signature\" value=\"signature\"><noscript>
<button type=\"submit\">Continue</button></noscript><script>document.forms.redirectionForm.submit();</script>
</form></body></html>";
        }

        $this->sendSignedJson($data);
    }

    public function renderCardPayDirectPayCheckTransaction(string $processingId): void
    {
        $httpRequest = $this->getHttpRequest();
        $signString = "{$httpRequest->getHeader('X-Merchant-Id')};{$httpRequest->getHeader('X-Timestamp')};{$processingId}";

        if (!$this->checkAuthorization($signString)) {
            $this->sendError('Invalid authorization header');
        }

        $this->sendSignedJson([
            'processingId' => $processingId,
            'status' => 'OK',
            'transactionId' => random_int(100000, 199999),
            'transactionData' => [
                'authorizationCode' => $this->generateAuthorizationCode(),
                'responseCode' => '00',
            ],
        ]);
    }

    public function renderCardPayDirectPay3ds(): void
    {
        header_remove('X-Frame-Options');
        $this->template->tdsTermUrl = $this->getHttpRequest()->getPost('tdsTermUrl');
        $this->template->params = $this->getHttpRequest()->getPost();
    }

    private function queryParameters(): RequestParameters
    {
        return new RequestParameters($this->params);
    }

    /**
     * Renders the simulator page: the computed and the received signature and, when they match,
     * the signed OK/FAIL/TOUT return URLs. Lists the missing parameters instead when some are absent.
     *
     * @param \Closure(RequestParameters): GatewaySign $createSign
     */
    private function simulate(RequestParameters $params, string $signParameter, \Closure $createSign): void
    {
        $sign = $createSign($params);
        $inputSign = $params->required($signParameter);

        $this->template->params = $params->all();

        if ($params->missing() !== []) {
            $this->getHttpResponse()->setCode(IResponse::S400_BadRequest);
            $this->template->missingParameters = $params->missing();
            $this->setView('missingParameters');
            return;
        }

        $computedSign = $sign->sign();

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->signValid = hash_equals($computedSign, $inputSign);

        $this->template->okReturnUrl = $sign->returnUrlSign('OK');
        $this->template->failReturnUrl = $sign->returnUrlSign('FAIL');
        $this->template->timeoutReturnUrl = $sign->returnUrlSign('TOUT');
    }

    private function generateAuthorizationCode(): string
    {
        return Random::generate(2, 'A-Z') . Random::generate(4, '0-9');
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES);
    }

    private function checkAuthorization(string $signedContent): bool
    {
        $header = $this->getHttpRequest()->getHeader('Authorization');
        if ($header === null) {
            return false;
        }
        $parts = explode('=', $header, 2);

        return isset($parts[1]) && hash_equals(LibrarySign::tatraHmac($signedContent, self::HMAC_KEY), $parts[1]);
    }

    private function sendError(string $message): never
    {
        $this->getHttpResponse()->setCode(IResponse::S400_BadRequest);
        $this->sendResponse(new JsonResponse(['error' => $message]));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function sendSignedJson(array $data): never
    {
        $auth = LibrarySign::tatraHmac(Json::encode($data), self::HMAC_KEY);
        $this->getHttpResponse()->addHeader('Authorization', "HMAC={$auth}, ECDSA=ecdsa, ECDSA_KEY=1");
        $this->sendResponse(new JsonResponse($data));
    }
}
