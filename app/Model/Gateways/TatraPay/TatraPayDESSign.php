<?php

declare(strict_types=1);

namespace App\Model\Gateways\TatraPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

/**
 * TatraPay: the request is signed over MID AMT CURR VS SS CS RURL (an empty SS or CS adds nothing),
 * the return URL over VS SS RES (Tatra banka, TatraPay technical manual 3.3).
 * Without SS the base strings are VS RES.
 */
final readonly class TatraPayDESSign implements GatewaySign
{
    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $cs,
        private string $rurl,
        private string $ss = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        return LibrarySign::tatraDes("{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->ss}{$this->cs}{$this->rurl}", $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $sign = LibrarySign::tatraDes("{$this->vs}{$this->ss}{$result}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}" . ($this->ss !== '' ? "&SS={$this->ss}" : '') . "&RES={$result}&SIGN={$sign}");
    }
}
