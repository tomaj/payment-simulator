<?php

namespace App\Gateways\CardPay;

use Omnipay\TatraPay\Sign\HmacSign;

class CardPayHmacSign
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

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl, $rem, $timestamp, $ipc = '', $name = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->rem = $rem;
        $this->ipc = $ipc;
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->ac = 111111;
        $this->tid = '';
    }

    public function sign()
    {
    	$base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->ipc}{$this->name}{$this->rem}{$this->timestamp}";

        $hmacSign = new HmacSign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$result}{$this->tid}{$this->timestamp}";

        $hmacSign = new HmacSign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        return $this->rurl . "?VS={$this->vs}&RES={$result}&TRES={$result}&AC={$this->ac}&TIMESTAMP={$this->timestamp}&HMAC=" . $sign;
    }
}
