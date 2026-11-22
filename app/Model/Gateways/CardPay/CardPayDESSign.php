<?php

declare(strict_types=1);

namespace App\Model\Gateways\CardPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\DesSign;

final readonly class CardPayDESSign implements GatewaySign
{
    /** Approval code the simulator returns */
    private const string AC = '111111';

    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $cs,
        private string $rurl,
        private string $ipc = '',
        private string $name = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}{$this->ipc}{$this->name}";

        return new DesSign()->sign($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $ac = self::AC;
        $sign = new DesSign()->sign("{$this->vs}{$result}{$ac}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&AC={$ac}&RES={$result}&SIGN={$sign}");
    }
}
