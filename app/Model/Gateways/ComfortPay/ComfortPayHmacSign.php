<?php

declare(strict_types=1);

namespace App\Model\Gateways\ComfortPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class ComfortPayHmacSign implements GatewaySign
{
    /** Approval code the simulator returns */
    private const string AC = '111111';

    /** Card id the simulator returns */
    private const string CID = '111111';

    /** Masked card number the simulator returns */
    private const string CC = '1111********1111';

    /** Response code the simulator returns */
    private const string RC = '123';

    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $rurl,
        private string $rem,
        private string $tpay,
        private string $timestamp,
        private string $ipc = '',
        private string $name = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->rurl}{$this->ipc}{$this->name}{$this->rem}{$this->tpay}{$this->timestamp}";

        return LibrarySign::tatraHmac($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $ac = self::AC;
        $cid = self::CID;
        $cc = self::CC;
        $rc = self::RC;
        $tid = '';
        $base = "{$this->amt}{$this->curr}{$this->vs}{$result}{$ac}{$result}{$cid}{$cc}{$rc}{$tid}{$this->timestamp}";
        $sign = LibrarySign::tatraHmac($base, $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&AMT={$this->amt}&CURR={$this->curr}&RES={$result}&TRES={$result}&AC={$ac}&CID={$cid}&CC={$cc}&RC={$rc}&TIMESTAMP={$this->timestamp}&HMAC={$sign}");
    }
}
