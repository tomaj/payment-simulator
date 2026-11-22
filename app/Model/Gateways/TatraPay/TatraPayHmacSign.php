<?php

declare(strict_types=1);

namespace App\Model\Gateways\TatraPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

/**
 * TatraPay HMAC: the request is signed over MID AMT CURR VS SS CS RURL REM TIMESTAMP,
 * the return URL over AMT CURR VS SS CS RES TID TIMESTAMP (the simulator sends no TID).
 */
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
        private string $ss = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->ss}{$this->cs}{$this->rurl}{$this->rem}{$this->timestamp}";

        return LibrarySign::tatraHmac($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $base = "{$this->amt}{$this->curr}{$this->vs}{$this->ss}{$this->cs}{$result}{$this->timestamp}";
        $sign = LibrarySign::tatraHmac($base, $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}" . ($this->ss !== '' ? "&SS={$this->ss}" : '') . "&RES={$result}&AMT={$this->amt}&CURR={$this->curr}&CS={$this->cs}&TIMESTAMP={$this->timestamp}&HMAC={$sign}");
    }
}
