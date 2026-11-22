<?php

declare(strict_types=1);

namespace App\Model\Gateways\Eplatby;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

/**
 * VÚB e-platby: the request is signed over MID AMT VS SS CS RURL (an empty SS or CS adds nothing),
 * the return URL over VS SS RES.
 */
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
        return LibrarySign::eplatbyHmac("{$this->mid}{$this->amt}{$this->vs}{$this->ss}{$this->cs}{$this->rurl}", $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $sign = LibrarySign::eplatbyHmac("{$this->vs}{$this->ss}{$result}", $this->sharedSecret);

        return ReturnUrl::append($this->rurl, "VS={$this->vs}" . ($this->ss !== '' ? "&SS={$this->ss}" : '') . "&RES={$result}&SIGN={$sign}");
    }
}
