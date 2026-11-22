<?php

declare(strict_types=1);

namespace App\Model\Gateways\CardPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class CardPayAuthorizeHmacSign implements GatewaySign
{
    /** Masked card number the simulator returns */
    private const string CC = '************1234';

    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $rurl,
        private string $ipc,
        private string $name,
        private string $timestamp,
        private string $e2e = '',
        private string $txn = '',
        private string $rem = '',
        private string $tpay = '',
        private string $cid = '',
        private string $ecid = '',
        private string $tdsCardholder = '',
        private string $tdsEmail = '',
        private string $tdsMobilePhone = '',
        private string $tdsBillCity = '',
        private string $tdsBillCountry = '',
        private string $tdsBillAddress1 = '',
        private string $tdsBillAddress2 = '',
        private string $tdsBillZip = '',
        private string $tdsShipCity = '',
        private string $tdsShipCountry = '',
        private string $tdsShipAddress1 = '',
        private string $tdsShipAddress2 = '',
        private string $tdsShipZip = '',
        private string $tdsAddrMatch = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->e2e}{$this->txn}{$this->rurl}{$this->ipc}";
        $base .= "{$this->name}{$this->rem}{$this->tpay}{$this->cid}{$this->ecid}{$this->tdsCardholder}{$this->tdsEmail}";
        $base .= "{$this->tdsMobilePhone}{$this->tdsBillCity}{$this->tdsBillCountry}{$this->tdsBillAddress1}";
        $base .= "{$this->tdsBillAddress2}{$this->tdsBillZip}{$this->tdsShipCity}{$this->tdsShipCountry}";
        $base .= "{$this->tdsShipAddress1}{$this->tdsShipAddress2}{$this->tdsShipZip}{$this->tdsAddrMatch}{$this->timestamp}";

        return LibrarySign::tatraHmac($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $cid = '';
        $tid = '';
        $tres = '';
        $cc = self::CC;
        $rc = '01';
        $ac = '';
        if ($this->tpay === 'Y') {
            $tres = $result;
            if ($result === 'OK') {
                $ac = '31232';
                $cid = '123456';
                $rc = '';
                $tid = '567898';
            }
        }
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->txn}{$result}{$ac}{$tres}{$cid}{$cc}{$rc}{$tid}{$this->timestamp}";
        $sign = LibrarySign::tatraHmac($base, $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "AMT={$this->amt}&CURR={$this->curr}&VS={$this->vs}&TXN={$this->txn}&RES={$result}&AC={$ac}&TRES={$tres}&CID={$cid}&CC={$cc}&RC={$rc}&TID={$tid}&TIMESTAMP={$this->timestamp}&HMAC={$sign}");
    }
}
