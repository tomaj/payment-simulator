<?php

declare(strict_types=1);

namespace App\Model\Gateways\CardPay;

use Nette\Utils\Strings;
use Omnipay\Core\Sign\HmacSign;

class CardPayAuthorizeHmacSign
{
    private $sharedSecret;

    private $mid;

    private $amt;

    private $curr;

    private $vs;

    private $rurl;

    private $ipc;

    private $name;

    private $timestamp;

    private $e2e;

    private $txn;

    private $rem;

    private $tpay;

    private $cid;

    private $ecid;

    private $tdsCardholder;

    private $tdsEmail;

    private $tdsMobilePhone;

    private $tdsBillCity;

    private $tdsBillCountry;

    private $tdsBillAddress1;

    private $tdsBillAddress2;

    private $tdsBillZip;

    private $tdsShipCity;

    private $tdsShipCountry;

    private $tdsShipAddress1;

    private $tdsShipAddress2;

    private $tdsShipZip;

    private $tdsAddrMatch;

    public function __construct(
        $sharedSecret,
        $mid,
        $amt,
        $curr,
        $vs,
        $rurl,
        $ipc,
        $name,
        $timestamp,
        $e2e = '',
        $txn = '',
        $rem = '',
        $tpay = '',
        $cid = '',
        $ecid = '',
        $tdsCardholder = '',
        $tdsEmail = '',
        $tdsMobilePhone = '',
        $tdsBillCity = '',
        $tdsBillCountry = '',
        $tdsBillAddress1 = '',
        $tdsBillAddress2 = '',
        $tdsBillZip = '',
        $tdsShipCity = '',
        $tdsShipCountry = '',
        $tdsShipAddress1 = '',
        $tdsShipAddress2 = '',
        $tdsShipZip = '',
        $tdsAddrMatch = ''
    ) {
        $this->sharedSecret = $sharedSecret;
        $this->mid = $mid;
        $this->amt = $amt;
        $this->curr = $curr;
        $this->vs = $vs;
        $this->rurl = $rurl;
        $this->ipc = $ipc;
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->e2e = $e2e;
        $this->txn = $txn;
        $this->rem = $rem;
        $this->tpay = $tpay;
        $this->cid = $cid;
        $this->ecid = $ecid;
        $this->tdsCardholder = $tdsCardholder;
        $this->tdsEmail = $tdsEmail;
        $this->tdsMobilePhone = $tdsMobilePhone;
        $this->tdsBillCity = $tdsBillCity;
        $this->tdsBillCountry = $tdsBillCountry;
        $this->tdsBillAddress1 = $tdsBillAddress1;
        $this->tdsBillAddress2 = $tdsBillAddress2;
        $this->tdsBillZip = $tdsBillZip;
        $this->tdsShipCity = $tdsShipCity;
        $this->tdsShipCountry = $tdsShipCountry;
        $this->tdsShipAddress1 = $tdsShipAddress1;
        $this->tdsShipAddress2 = $tdsShipAddress2;
        $this->tdsShipZip = $tdsShipZip;
        $this->tdsAddrMatch = $tdsAddrMatch;
    }

    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->e2e}{$this->txn}{$this->rurl}{$this->ipc}";
        $base .= "{$this->name}{$this->rem}{$this->tpay}{$this->cid}{$this->ecid}{$this->tdsCardholder}{$this->tdsEmail}";
        $base .= "{$this->tdsMobilePhone}{$this->tdsBillCity}{$this->tdsBillCountry}{$this->tdsBillAddress1}";
        $base .= "{$this->tdsBillAddress2}{$this->tdsBillZip}{$this->tdsShipCity}{$this->tdsShipCountry}";
        $base .= "{$this->tdsShipAddress1}{$this->tdsShipAddress2}{$this->tdsShipZip}{$this->tdsAddrMatch}{$this->timestamp}";

        $hmacSign = new HmacSign();
        return $hmacSign->sign($base, $this->sharedSecret);
    }

    public function returnUrlSign(string $result): string
    {
        $cid = '';
        $tid = '';
        $tres = '';
        $cc = '************1234';
        $rc = '01';
        $ac = '';
        if ($this->tpay === 'Y') {
            $tres = $result;
            if ($result === 'OK') {
                $ac = 31232;
                $cid = 123456;
                $rc = '';
                $tid = 567898;
            }
        }
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->txn}{$result}{$ac}{$tres}{$cid}{$cc}{$rc}{$tid}{$this->timestamp}";

        $hmacSign = new HmacSign();
        $sign = $hmacSign->sign($base, $this->sharedSecret);
        $params = "AMT={$this->amt}&CURR={$this->curr}&VS={$this->vs}&TXN={$this->txn}&RES={$result}&AC={$ac}&TRES={$tres}&CID={$cid}&CC={$cc}&RC={$rc}&TID={$tid}&TIMESTAMP={$this->timestamp}&HMAC={$sign}";
        if (Strings::contains($this->rurl, '?')) {
            return $this->rurl . '&' . $params;
        }

        return $this->rurl . "?" . $params;
    }
}
