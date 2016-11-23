<?php

namespace App\Gateways\Tatrapay;

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
        if (strlen($sharedSecret) == 64) {
            $sharedSecret = pack('H*', $sharedSecret);
        }

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

        $sharedSecret = pack('H*', $this->sharedSecret);

        return hash_hmac('sha256', $base, $sharedSecret);

    }

    public function returnUrlSign($result)
    {
        $base = "{$this->vs}{$result}";

        $sharedSecret = pack('H*', $this->sharedSecret);

        return $this->rurl . '?VS={$this->vs}&RES={$result}&HMAC=' . hash_hmac('sha256', $base, $sharedSecret);
    }
}
