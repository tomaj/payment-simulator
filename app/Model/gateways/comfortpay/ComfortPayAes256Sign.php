<?php

declare(strict_types=1);

namespace App\Model\Gateways\ComfortPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\Aes256Sign;

class ComfortPayAes256Sign
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

    private $tid;

    private $tpay;

    public function __construct($sharedSecret, $mid, $amt, $curr, $vs, $cs, $rurl, $rem, $tpay, $ipc = '', $name = '')
    {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->cs = $cs;
        $this->rurl = $rurl;
        $this->rem = $rem;
        $this->tpay = $tpay;
        $this->ipc = $ipc;
        $this->name = $name;
        $this->ac = 111111;
        $this->cid = 111111;
        $this->tid = '';
    }

    public function sign(): string
    {
    	$base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->ipc}{$this->name}{$this->tpay}";

        $sign = new Aes256Sign();

        return $sign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign(string $result): string
    {
        $base = "{$this->vs}{$result}{$this->ac}{$this->cid}";

        $sign = new Aes256Sign();
        $sign = $sign->sign($base, $this->sharedSecret);

        $params = "VS={$this->vs}&RES={$result}&TRES={$result}&AC={$this->ac}&CID={$this->cid}&SIGN=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
