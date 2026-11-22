<?php

declare(strict_types=1);

namespace App\Model\Gateways\TatraPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\Aes256Sign;

final readonly class TatraPayAES256Sign implements GatewaySign
{
    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $curr,
        private string $vs,
        private string $cs,
        private string $rurl,
    ) {}

    #[\Override]
    public function sign(): string
    {
        return new Aes256Sign()->sign("{$this->mid}{$this->amt}{$this->curr}{$this->vs}{$this->cs}{$this->rurl}", $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $sign = new Aes256Sign()->sign("{$this->vs}{$result}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&RES={$result}&SIGN={$sign}");
    }
}
