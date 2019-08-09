<?php

namespace App\Gateways\SporoPay;

use Nette\Utils\Strings;
use Omnipay\SporoPay\Sign\Des3Sign;

class SporoPay3DesSign
{
    private $sharedSecret;

    private $pu_predcislo;

    private $pu_cislo;

    private $pu_kbanky;

    private $suma;

    private $mena;

    private $vs;

    private $ss;

    private $url;

    private $param;

    public function __construct($sharedSecret, $pu_predcislo, $pu_cislo, $pu_kbanky, $suma, $mena, $vs, $ss, $url, $param)
    {
        $this->sharedSecret = $sharedSecret;
        $this->pu_predcislo = $pu_predcislo;
        $this->pu_cislo = $pu_cislo;
        $this->pu_kbanky = $pu_kbanky;
        $this->suma = $suma;
        $this->mena = $mena;
        $this->vs = $vs;
        $this->ss = $ss;
        $this->url = $url;
        $this->param = $param;
        $this->u_cislo = '9876543210';
        $this->u_predcislo = '123456';
        $this->u_kbanky = $pu_kbanky;
    }

    public function sign()
    {
    	$base = "{$this->pu_predcislo};{$this->pu_cislo};{$this->pu_kbanky};{$this->suma};{$this->mena};{$this->vs};{$this->ss};{$this->url};{$this->param}";

        $hmacSign = new Des3Sign();

        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign($result)
    {
        $data = "{$this->u_predcislo};{$this->u_cislo};{$this->u_kbanky};{$this->pu_predcislo};{$this->pu_cislo};{$this->pu_kbanky};{$this->suma};{$this->mena};{$this->vs};{$this->ss};{$this->url};{$this->param};{$result};{$result}";

        $hmacSign = new Des3Sign();
        $sign = $hmacSign->sign($data, $this->sharedSecret);

        $params = "u_predcislo={$this->u_predcislo}&u_cislo={$this->u_cislo}&u_kbanky={$this->u_kbanky}&pu_predcislo={$this->pu_predcislo}&pu_cislo={$this->pu_cislo}&pu_kbanky={$this->pu_kbanky}&suma={$this->suma}&mena={$this->mena}&vs={$this->vs}&ss={$this->ss}&url=".urlencode($this->url)."&param=".urlencode($this->param)."&result={$result}&real={$result}&SIGN2=" . $sign;
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }
        return $this->rurl . "?" . $params;
    }
}
