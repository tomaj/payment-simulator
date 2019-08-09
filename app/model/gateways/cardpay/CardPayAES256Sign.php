<?php

namespace App\Gateways\CardPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\Aes256Sign;

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
        $this->ipc = $ipc;
        $this->name = $name;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->rem}{$this->ipc}{$this->name}";

        $hmacSign = new Aes256Sign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}{$this->ac}";

        $hmacSign = new Aes256Sign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        $params = "VS={$this->vs}&AC={$this->ac}&RES={$result}&SIGN=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
