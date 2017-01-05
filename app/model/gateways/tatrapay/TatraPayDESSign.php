<?php

namespace App\Gateways\TatraPay;

use Omnipay\Core\Sign\DesSign;

class TatraPayDESSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $curr;

    private $vs;

    private $cs;

    private $rurl;

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl)
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}";

        $desSign = new DesSign();

        return $desSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}";

        $desSign = new DesSign();
        $sign = $desSign->sign($base, $this->sharedSecret);

        return $this->rurl . "?VS={$this->vs}&RES={$result}&SIGN={$sign}";
    }
}
