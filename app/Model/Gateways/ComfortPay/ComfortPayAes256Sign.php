<?php

declare(strict_types=1);

namespace App\Model\Gateways\ComfortPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class ComfortPayAes256Sign implements GatewaySign
{
    /** Approval code the simulator returns */
    private const string AC = '111111';

    /** Card id the simulator returns */
    private const string CID = '111111';

    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $cs,
        private string $rurl,
        private string $tpay,
        private string $ipc = '',
        private string $name = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->ipc}{$this->name}{$this->tpay}";

        return LibrarySign::tatraAes($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $ac = self::AC;
        $cid = self::CID;
        $sign = LibrarySign::tatraAes("{$this->vs}{$result}{$ac}{$cid}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&RES={$result}&TRES={$result}&AC={$ac}&CID={$cid}&SIGN={$sign}");
    }
}
