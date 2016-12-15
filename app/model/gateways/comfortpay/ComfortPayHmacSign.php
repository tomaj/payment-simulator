<?php

namespace App\Gateways\ComfortPay;

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
        $this->ipc = $ipc;
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->ac = 111111;
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
    	return 'todo';
        // $base = "{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$result}{$this->tid}{$this->timestamp}";

        // $hmacSign = new HmacSign();
        // $sign = $hmacSign->sign($base, $this->sharedSecret);

        // return $this->rurl . "?VS={$this->vs}&RES={$result}&TRES={$result}&AC={$this->ac}&TIMESTAMP={$this->timestamp}&HMAC=" . $sign;
    }
}
