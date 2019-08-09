<?php

namespace App\Gateways\Eplatby;

use Nette\Utils\Strings;
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

        $params = "VS={$this->vs}&RES={$result}&SIGN=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
