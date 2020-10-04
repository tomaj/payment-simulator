<?php

declare(strict_types=1);

namespace App\Model\Gateways\CardPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\HmacSign;

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

    private $cc;

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
        $this->cc = '1111********1111';
    }

    public function sign(): string
    {
    	$base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->rurl}{$this->ipc}{$this->name}{$this->rem}{$this->timestamp}";

        $hmacSign = new HmacSign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign(string $result): string
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$result}{$this->ac}{$result}{$this->cc}{$this->tid}{$this->timestamp}";

        $hmacSign = new HmacSign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);

        $params = "VS={$this->vs}&RES={$result}&TRES={$result}&AC={$this->ac}&AMT={$this->amt}&CURR={$this->curr}&CID=&CC={$this->cc}&AC={$this->ac}&TIMESTAMP={$this->timestamp}&HMAC=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
