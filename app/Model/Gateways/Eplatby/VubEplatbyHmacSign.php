<?php

declare(strict_types=1);

namespace App\Model\Gateways\Eplatby;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class VubEplatbyHmacSign implements GatewaySign
{
    public function __construct(
        private string $sharedSecret,
        private string $mid,
        private string $amt,
        private string $vs,
        private string $cs,
        private string $rurl,
        private string $ss = '',
    ) {}

    #[\Override]
    public function sign(): string
    {
        return LibrarySign::eplatbyHmac("{$this->mid}{$this->amt}{$this->vs}{$this->cs}{$this->ss}{$this->rurl}", $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $sign = LibrarySign::eplatbyHmac("{$this->vs}{$result}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}&RES={$result}&SIGN={$sign}");
    }
}
