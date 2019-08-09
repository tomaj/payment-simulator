<?php

namespace App\Gateways\TatraPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\HmacSign;

class TatraPayHmacSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $curr;

    private $vs;

    private $cs;

    private $rurl;

    private $rem;

    private $timestamp;

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl, $rem, $timestamp)
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->rem = $rem;
        $this->timestamp = $timestamp;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->rem}{$this->timestamp}";

        $hmacSign = new HmacSign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$result}{$this->timestamp}";

        $hmacSign = new HmacSign();
        
        $sign = $hmacSign->sign($base, $this->sharedSecret);


        $params = "VS={$this->vs}&RES={$result}&AMT={$this->amt}&CURR={$this->curr}&CS={$this->cs}&TIMESTAMP={$this->timestamp}&HMAC=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
