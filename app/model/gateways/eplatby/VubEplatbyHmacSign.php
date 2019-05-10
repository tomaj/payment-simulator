<?php

namespace App\Gateways\Eplatby;

use Omnipay\Eplatby\Sign\HmacSign;

class VubEplatbyHmacSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $vs;

    private $cs;

    private $ss;

    private $rurl;

    public function __construct($sharedSecret, $mid, $amt, $vs, $cs, $rurl, $ss = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->ss = $ss;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->vs}{$this->cs}{$this->ss}{$this->rurl}";

        $hmacSign = new HmacSign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}";

        $hmacSign = new HmacSign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        return $this->rurl . "?VS={$this->vs}&RES={$result}&SIGN=" . $sign;
    }
}
