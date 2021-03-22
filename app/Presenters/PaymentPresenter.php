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
use Nette\Application\UI\Presenter;
use Omnipay\Core\Sign\HmacSign;



class PaymentPresenter extends Presenter
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
}
