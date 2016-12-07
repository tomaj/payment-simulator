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

    private $rurl;

    public function __construct($sharedSecret, $mid, $amt, $vs, $cs, $rurl)
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
    }

    public function sign()
    {
        $base = "{$this->mid}{$this->amt}{$this->vs}{$this->cs}{$this->rurl}";
        // var_dump($base);

        $hmacSign = new HmacSign();
        // var_dump($hmacSign->sign($base, $this->sharedSecret));
        // var_dump($hmacSign->sign('1111111110.001234560321http://localhost:4444/testserver.php', $this->sharedSecret));

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
