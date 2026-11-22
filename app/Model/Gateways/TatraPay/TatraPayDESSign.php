<?php

declare(strict_types=1);

namespace App\Model\Gateways\TatraPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\DesSign;

/**
 * TatraPay: the request is signed over MID AMT CURR VS SS CS RURL (an empty SS or CS adds nothing),
 * the return URL over VS RES.
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
        return new DesSign()->sign("{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->ss}{$this->cs}{$this->rurl}", $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $sign = new DesSign()->sign("{$this->vs}{$result}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}" . ($this->ss !== '' ? "&SS={$this->ss}" : '') . "&RES={$result}&SIGN={$sign}");
    }
}
