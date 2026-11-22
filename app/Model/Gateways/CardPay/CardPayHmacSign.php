<?php

declare(strict_types=1);

namespace App\Model\Gateways\CardPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class CardPayHmacSign implements GatewaySign
{
    /** Approval code the simulator returns */
    private const string AC = '111111';

    /** Masked card number the simulator returns */
    private const string CC = '1111********1111';

    /** Card id returned for a successful card registration (TPAY=Y) */
    private const string CID = '123456';

    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $rurl,
        private string $rem,
        private string $timestamp,
        private string $ipc = '',
        private string $name = '',
        private string $txn = '',
        private string $tpay = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->txn}{$this->rurl}{$this->ipc}{$this->name}{$this->rem}{$this->tpay}{$this->timestamp}";

        return LibrarySign::tatraHmac($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $cid = '';
        $tres = '';
        if ($this->tpay === 'Y') {
            $tres = $result;
            if ($result === 'OK') {
                $cid = self::CID;
            }
        }
        $ac = self::AC;
        $cc = self::CC;
        $tid = '';
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->txn}{$result}{$ac}{$tres}{$cid}{$cc}{$tid}{$this->timestamp}";
        $sign = LibrarySign::tatraHmac($base, $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&TXN={$this->txn}&RES={$result}&TRES={$tres}&AC={$ac}&AMT={$this->amt}&CURR={$this->curr}&CID={$cid}&CC={$cc}&AC={$ac}&TIMESTAMP={$this->timestamp}&HMAC={$sign}");
    }
}
