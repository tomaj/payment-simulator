<?php

namespace App\Presenters;

use App\Gateways\Tatrapay\TatraPayAES256Sign;
use App\Gateways\Tatrapay\TatraPayDESSign;
use App\Gateways\Tatrapay\TatraPayHmacSign;
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

        $tatrapaySign = new TatraPayHmacSign($sharedSecret, $mid, $this->params['AMT'], $this->params['CURR'], $this->params['VS'], $this->params['CS'], $this->params['RURL'], $this->params['TIMESTAMP'], isset($this->params['REM']) ? $this->params['REM'] : '', $this->params['TIMESTAMP']);
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
}
