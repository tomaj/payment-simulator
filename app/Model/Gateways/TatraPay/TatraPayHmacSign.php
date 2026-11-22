<?php

declare(strict_types=1);

namespace App\Model\Gateways\TatraPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class TatraPayHmacSign implements GatewaySign
{
    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $cs,
        private string $rurl,
        private string $rem,
        private string $timestamp,
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->rem}{$this->timestamp}";

        return LibrarySign::tatraHmac($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$result}{$this->timestamp}";
        $sign = LibrarySign::tatraHmac($base, $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&RES={$result}&AMT={$this->amt}&CURR={$this->curr}&CS={$this->cs}&TIMESTAMP={$this->timestamp}&HMAC={$sign}");
    }
}
