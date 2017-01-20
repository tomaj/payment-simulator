<?php

namespace App\Presenters;

use App\Gateways\ComfortPay\ComfortPayAes256Sign;
use App\Gateways\TatraPay\TatraPayAES256Sign;
use App\Gateways\TatraPay\TatraPayDESSign;
use App\Gateways\TatraPay\TatraPayHmacSign;
use App\Gateways\CardPay\CardPayAES256Sign;
use App\Gateways\CardPay\CardPayDESSign;
use App\Gateways\CardPay\CardPayHmacSign;
use App\Gateways\ComfortPay\ComfortPayHmacSign;
use App\Gateways\Eplatby\VubEplatbyHmacSign;
use App\Gateways\SporoPay\SporoPay3DesSign;
use Nette\Application\UI\Presenter;

class PaymentPresenter extends Presenter
{
    public function renderTatrapayAes256()
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

    public function renderTatrapayDes()
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

    public function renderTatrapayHmac()
    {
        $sharedSecret = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
        $mid = $this->params['MID'];

        $inputSign = $this->params['HMAC'];

        $tatrapaySign = new TatraPayHmacSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], isset($this->params['REM']) ? $this->params['REM'] : '', $this->params['TIMESTAMP']);
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

    public function renderCardpayAes256()
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

    public function renderCardpayDes()
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

    public function renderCardpayHmac()
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

    public function renderComfortpayHmac()
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

    public function renderComfortpayAes256()
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

    public function renderEplatbyHmac()
    {
        $params = $this->getRequest()->getPost();
        $sharedSecret = '1111111111111111111111111111111111111111111111111111111111111111';
        $mid = $params['MID'];

        $inputSign = $params['SIGN'];

        $eplatbySign = new VubEplatbyHmacSign($sharedSecret, $mid, $params['AMT'], $params['VS'], $params['CS'], $params['RURL']);
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

    public function renderSporopayDes()
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
}
