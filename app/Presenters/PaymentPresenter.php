<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Gateways\ComfortPay\ComfortPayAes256Sign;
use App\Model\Gateways\TatraPay\TatraPayAES256Sign;
use App\Model\Gateways\TatraPay\TatraPayDESSign;
use App\Model\Gateways\TatraPay\TatraPayHmacSign;
use App\Model\Gateways\CardPay\CardPayAES256Sign;
use App\Model\Gateways\CardPay\CardPayDESSign;
use App\Model\Gateways\CardPay\CardPayHmacSign;
use App\Model\Gateways\ComfortPay\ComfortPayHmacSign;
use App\Model\Gateways\Eplatby\VubEplatbyHmacSign;
use App\Model\Gateways\SporoPay\SporoPay3DesSign;
use App\Model\Gateways\CardPay\CardPayAuthorizeHmacSign;
use App\Model\Responses\CancelAuthorizationResponse;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Random;
use Omnipay\Core\Sign\HmacSign;


final class PaymentPresenter extends Presenter
{
    public function renderTatrapayAes256(): void
    {
        $sharedSecret = '1111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['SIGN'];

        $tatrapaySign = new TatraPayAES256Sign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderTatrapayDes(): void
    {
        $sharedSecret = '11111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['SIGN'];

        $tatrapaySign = new TatraPayDESSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderTatrapayHmac(): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['HMAC'];

        $tatrapaySign = new TatraPayHmacSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], isset($this->params['CS']) ? $this->params['CS'] : '', $this->params['RURL'], isset($this->params['REM']) ? $this->params['REM'] : '', $this->params['TIMESTAMP']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderCardpayAes256(): void
    {
        $sharedSecret = '1111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['SIGN'];

        $tatrapaySign = new CardPayAES256Sign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], '', $this->params['IPC'], $this->params['NAME']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderCardpayDes(): void
    {
        $sharedSecret = '11111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['SIGN'];

        $tatrapaySign = new CardPayDESSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], $this->params['IPC'], $this->params['NAME']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderCardpayHmac(): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['HMAC'];

        $tatrapaySign = new CardPayHmacSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], $this->params['REM'], $this->params['TIMESTAMP'], $this->params['IPC'], $this->params['NAME']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderComfortpayHmac(): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['HMAC'];

        $tatrapaySign = new ComfortPayHmacSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['RURL'], $this->params['REM'], $this->params['TPAY'], $this->params['TIMESTAMP'], $this->params['IPC'], $this->params['NAME']);
        $computedSign = $tatrapaySign->sign();

        $okReturnUrl = $tatrapaySign->returnUrlSign('OK');
        $failReturnUrl = $tatrapaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $tatrapaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderComfortpayAes256(): void
    {
        $sharedSecret = '1111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['SIGN'];

        $comfortpaySign = new ComfortPayAes256Sign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], $this->params['REM'], $this->params['TPAY'], $this->params['IPC'], $this->params['NAME']);
        $computedSign = $comfortpaySign->sign();

        $okReturnUrl = $comfortpaySign->returnUrlSign('OK');
        $failReturnUrl = $comfortpaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $comfortpaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderEplatbyHmac(): void
    {
        $params = $this->getRequest()->getPost();
        $sharedSecret = '1111111111111111111111111111111111111111111111111111111111111111';
        $mid = $params['MID'];

        $inputSign = $params['SIGN'];

        $ss = '';
        if (isset($params['SS'])) {
            $ss = $params['SS'];
        }

        $eplatbySign = new VubEplatbyHmacSign($sharedSecret, $mid, $params['AMT'], $params['VS'], $params['CS'], $params['RURL'], $ss);
        $computedSign = $eplatbySign->sign();

        $okReturnUrl = $eplatbySign->returnUrlSign('OK');
        $failReturnUrl = $eplatbySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $eplatbySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderSporopayDes(): void
    {
        $sharedSecret = 'Z3qY08EpvLlAAoMZdnyUdQ==';

        $inputSign = $this->params['sign1'];

        $sporopaySign = new SporoPay3DesSign($sharedSecret, $this->params['pu_predcislo'], $this->params['pu_cislo'], $this->params['pu_kbanky'], $this->params['suma'], $this->params['mena'], $this->params['vs'], $this->params['ss'], $this->params['url'], $this->params['param']);
        $computedSign = $sporopaySign->sign();

        $okReturnUrl = $sporopaySign->returnUrlSign('OK');
        $failReturnUrl = $sporopaySign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $sporopaySign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function renderCardpayAuthorizeHmac(): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $inputSign = $this->params['HMAC'];

        $cardPayAuthorizeHmacSign = new CardPayAuthorizeHmacSign(
            $sharedSecret,
            $this->getParameter('MID'),
            $this->getParameter('AMT'),
            $this->getParameter('CURR'),
            $this->getParameter('VS'),
            $this->getParameter('RURL'),
            $this->getParameter('IPC'),
            $this->getParameter('NAME'),
            $this->getParameter('TIMESTAMP'),
            $this->getParameter('E2E', ''),
            $this->getParameter('TXN', ''),
            $this->getParameter('REM', ''),
            $this->getParameter('TPAY', ''),
            $this->getParameter('CID', ''),
            $this->getParameter('ECID', ''),
            $this->getParameter('TDS_CARDHOLDER', ''),
            $this->getParameter('TDS_EMAIL', ''),
            $this->getParameter('TDS_MOBILE_PHONE', ''),
            $this->getParameter('TDS_BILL_CITY', ''),
            $this->getParameter('TDS_BILL_COUNTRY', ''),
            $this->getParameter('TDS_BILL_ADDRESS1', ''),
            $this->getParameter('TDS_BILL_ADDRESS2', ''),
            $this->getParameter('TDS_BILL_ZIP', ''),
            $this->getParameter('TDS_SHIP_CITY', ''),
            $this->getParameter('TDS_SHIP_COUNTRY', ''),
            $this->getParameter('TDS_SHIP_ADDRESS1', ''),
            $this->getParameter('TDS_SHIP_ADDRESS2', ''),
            $this->getParameter('TDS_SHIP_ZIP', ''),
            $this->getParameter('TDS_ADDR_MATCH', '')
        );

        $computedSign = $cardPayAuthorizeHmacSign->sign();

        $okReturnUrl = $cardPayAuthorizeHmacSign->returnUrlSign('OK');
        $failReturnUrl = $cardPayAuthorizeHmacSign->returnUrlSign('FAIL');
        $timeoutReturnUrl = $cardPayAuthorizeHmacSign->returnUrlSign('TOUT');

        $this->template->computedSign = $computedSign;
        $this->template->inputSign = $inputSign;
        $this->template->params = $this->params;

        $this->template->okReturnUrl = $okReturnUrl;
        $this->template->failReturnUrl = $failReturnUrl;
        $this->template->timeoutReturnUrl = $timeoutReturnUrl;
    }

    public function actionCardpayCancelPurchase()
    {
        $successXml = <<<XML
<?xml version="1.0" encoding="windows-1250"?>
    <cardpay>
    <request>
        <mid>{$this->params['MID']}</mid>
        <amt>{$this->params['AMT']}</amt>
        <tid>{$this->params['TID']}</tid>
        <txn>{$this->params['TXN']}</txn>
        <timestamp>{$this->params['TIMESTAMP']}</timestamp>
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
        <mid>{$this->params['MID']}</mid>
        <amt>{$this->params['AMT']}</amt>
        <tid>{$this->params['TID']}</tid>
        <txn>{$this->params['TXN']}</txn>
        <timestamp>{$this->params['TIMESTAMP']}</timestamp>
    </request>
    <result>
        <res>FAIL</res>
        <errorCode>10</errorCode>
    </result>
</cardpay>
XML;

        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

        $hmacSign = new HmacSign();
        $input = "{$this->getParameter('MID', '')}{$this->getParameter('AMT', '')}{$this->getParameter('TID', '')}";
        $input .= "{$this->getParameter('VS', '')}{$this->getParameter('TXN', '')}{$this->getParameter('REM', '')}{$this->getParameter('TIMESTAMP', '')}";

        if ($this->params['HMAC'] !== $hmacSign->sign($input, $sharedSecret)) {
            $response = new CancelAuthorizationResponse($failXml, []);
            $this->sendResponse($response);
        }

        $response = new CancelAuthorizationResponse($successXml, [
            'Authorization' => "HMAC={$hmacSign->sign($successXml, $sharedSecret)},ECDSA=3046022100a4e5491ab9376f8cd9e51c176f5e75bb3664e3f852e901d36ac0a5b0f2177fad0221008794e59ab8027c68ae74ea65d0f5b5913865db9a818044d06c114f4d84346f48, ECDSA_KEY=1",
        ]);

        $this->sendResponse($response);
    }


    // todo move to separate presenter and add router

    private function generateAuthorizationCode($letters = 2, $numbers = 4): string
    {
        return Random::generate($letters,'A-Z') . Random::generate($numbers, '0-9');
    }

    private function checkAuthorization($body, $sharedSecret): bool
    {
        $hmacSign = new HmacSign();
        $input = $body;
        $auth = $hmacSign->sign($input, $sharedSecret);
        $header = $this->getHttpRequest()->getHeader("Authorization");
        $parts = explode('=', $header);
        return $parts[1] === $auth;
    }

    public function renderCardPayDirectPayPostTransaction(): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $httpRequest = $this->getHttpRequest();
        $requestBody = $httpRequest->getRawBody();

        if (!$this->checkAuthorization($requestBody, $sharedSecret)) {
            $response = new JsonResponse([
                "error" => "Invalid authorization header"
            ]);
            $httpResponse = $this->getHttpResponse();
            $httpResponse->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $this->sendResponse($response);
        }

        $isGooglePay = false;
//        $isApplePay = false;
        $isTds = false;

        try {
            $body = Json::decode($requestBody, Json::FORCE_ARRAY);
        } catch (JsonException $jsonException) {
            $response = new JsonResponse([
                "error" => "Cannot process Google Pay token"
            ]);
            $this->sendResponse($response);
        }

        if (isset($body['googlePayToken'])) {
            $isGooglePay = true;
        }

        $status = "OK";

        if ($isGooglePay) {
            if ($body['amount'] >= 10*100) {
                $status = "TDS_AUTH_REQUIRED";
                $isTds = true;
            }
        }
        if ($body['amount'] >= 100*100) {
            $status = "FAIL";
        }

        $processingId = 10000 + rand(0, 9999);

        $data = [
            "processingId" => $processingId,
            "status" => $status, // FAIL, TDS_AUTH_REQUIRED
            "transactionId" => rand(100000, 199999),
            "transactionData" => [
                "authorizationCode" => $this->generateAuthorizationCode(),
                "responseCode" => "00",
            ],
        ];

        if ($isTds) {
            $timestamp = date('dmYHis');
            $data['tdsRedirectionFormHtml'] = "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"UTF-8\"><title>3D Secure
Processing</title></head><body><form action=\"{$this->getHttpRequest()->getUrl()->getScheme()}://{$this->getHttpRequest()->getUrl()->getHost()}/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds\" method=\"POST\" name=\"redirectionForm\">
<input type=\"hidden\" name=\"tdsTermUrl\" value=\"{$body['tdsTermUrl']}\">
<input type=\"hidden\" name=\"processingId\" value=\"{$processingId}\">
<input type=\"hidden\" name=\"timestamp\" value=\"{$timestamp}\">
<input type=\"hidden\" name=\"signature\" value=\"signature\"><noscript>
<button type=\"submit\">Continue</button></noscript><script>document.forms.redirectionForm.submit();</script>
</form></body></html>";
        }

        $response = new JsonResponse($data);

        $this->appendResponseHmac($response, $sharedSecret);
        $this->sendResponse($response);
    }

    public function renderCardPayDirectPayCheckTransaction($processingId): void
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $httpRequest = $this->getHttpRequest();
        $signString = "{$httpRequest->getHeader("X-Merchant-Id")};{$httpRequest->getHeader("X-Timestamp")};{$processingId}";

        if (!$this->checkAuthorization($signString, $sharedSecret)) {
            $response = new JsonResponse([
                "error" => "Invalid authorization header"
            ]);
            $httpResponse = $this->getHttpResponse();
            $httpResponse->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $this->sendResponse($response);
        }

        $response = new JsonResponse([
            "processingId" => $processingId,
            "status" => "OK",
            "transactionId" => rand(100000, 199999),
            "transactionData" => [
                "authorizationCode" => $this->generateAuthorizationCode(),
                "responseCode" => "00",
            ],
        ]);

        $this->appendResponseHmac($response, $sharedSecret);
        $this->sendResponse($response);
    }

    private function appendResponseHmac(JsonResponse $response, string $sharedSecret): void
    {
        $hmacSign = new HmacSign();
        $payloadData = Json::encode($response->getPayload());
        $auth = $hmacSign->sign($payloadData, $sharedSecret);
        $this->getHttpResponse()->addHeader("Authorization", "HMAC={$auth}, ECDSA=ecdsa, ECDSA_KEY=1");
    }

    public function renderCardPayDirectPay3ds(): void
    {
        $this->template->tdsTermUrl = $this->getHttpRequest()->getPost('tdsTermUrl');
        $this->template->params = $this->getHttpRequest()->getPost();
    }
}
