<?php

declare(strict_types=1);

namespace App\Model\Gateways\ComfortPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\HmacSign;

class ComfortPayHmacSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $curr;

    private $vs;

    private $cs;

    private $rurl;

    private $rem;

    private $ipc;

    private $name;

    private $timestamp;

    private $tid;

    private $tpay;

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $rurl, $rem, $tpay, $timestamp, $ipc = '', $name = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->rurl = $rurl;
        $this->rem = $rem;
        $this->tpay = $tpay;
        $this->timestamp = $timestamp;
        $this->ipc = $ipc;
        $this->name = $name;
        $this->ac = 111111;
        $this->cid = 111111;
        $this->cc = '1111********1111';
        $this->rc = '123';
        $this->tid = '';
    }

    public function sign()
    {
    	$base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->rurl}{$this->ipc}{$this->name}{$this->rem}{$this->tpay}{$this->timestamp}";

        $hmacSign = new HmacSign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$result}{$this->ac}{$result}{$this->cid}{$this->cc}{$this->rc}{$this->tid}{$this->timestamp}";

        $hmacSign = new HmacSign();
         
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        $params = "VS={$this->vs}&AMT={$this->amt}&CURR={$this->curr}&RES={$result}&TRES={$result}&AC={$this->ac}&CID={$this->cid}&CC={$this->cc}&RC={$this->rc}&TIMESTAMP={$this->timestamp}&HMAC=" . $sign;

         if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
