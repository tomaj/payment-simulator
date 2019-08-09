<?php

namespace App\Gateways\CardPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\DesSign;

class CardPayDESSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $curr;

    private $vs;

    private $cs;

    private $rurl;

    private $ipc;

    private $name;

    private $ac;

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl, $ipc = '', $name = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->ipc = $ipc;
        $this->name = $name;
        $this->ac = 111111;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->ipc}{$this->name}";

        $desSign = new DesSign();
        return $desSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}{$this->ac}";

        $desSign = new DesSign();
        $sign = $desSign->sign($base, $this->sharedSecret);

        $params = "VS={$this->vs}&AC={$this->ac}&RES={$result}&SIGN={$sign}";
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
