<?php

namespace App\Gateways\CardPay;

use Omnipay\TatraPay\Sign\Aes256Sign;

class CardPayAES256Sign
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

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl, $rem = '', $ipc = '', $name = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->rem = $rem;
        $this->ac = 111111;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->rem}";

        $hmacSign = new Aes256Sign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}{$this->ac}";

        $hmacSign = new Aes256Sign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        return $this->rurl . "?VS={$this->vs}&AC={$this->ac}&RES={$result}&SIGN=" . $sign;
    }
}
