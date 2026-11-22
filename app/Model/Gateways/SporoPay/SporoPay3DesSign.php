<?php

declare(strict_types=1);

namespace App\Model\Gateways\SporoPay;

use App\Model\Gateways\GatewaySign;
use App\Model\Gateways\ReturnUrl;
use App\Model\Sign\LibrarySign;

final readonly class SporoPay3DesSign implements GatewaySign
{
    /** Payer account the simulator returns */
    private const string U_PREDCISLO = '123456';

    private const string U_CISLO = '9876543210';

    public function __construct(
        private string $sharedSecret,
        private string $puPredcislo,
        private string $puCislo,
        private string $puKbanky,
        private string $suma,
        private string $mena,
        private string $vs,
        private string $ss,
        private string $url,
        private string $param,
    ) {}

    #[\Override]
    public function sign(): string
    {
        $base = "{$this->puPredcislo};{$this->puCislo};{$this->puKbanky};{$this->suma};{$this->mena};{$this->vs};{$this->ss};{$this->url};{$this->param}";

        return LibrarySign::sporoPay3Des($base, $this->sharedSecret);
    }

    #[\Override]
    public function returnUrlSign(string $result): string
    {
        $uPredcislo = self::U_PREDCISLO;
        $uCislo = self::U_CISLO;
        $uKbanky = $this->puKbanky;
        $data = "{$uPredcislo};{$uCislo};{$uKbanky};{$this->puPredcislo};{$this->puCislo};{$this->puKbanky};{$this->suma};{$this->mena};{$this->vs};{$this->ss};{$this->url};{$this->param};{$result};{$result}";
        $sign = LibrarySign::sporoPay3Des($data, $this->sharedSecret);

        return ReturnUrl::append(
            $this->url,
            "u_predcislo={$uPredcislo}&u_cislo={$uCislo}&u_kbanky={$uKbanky}&pu_predcislo={$this->puPredcislo}&pu_cislo={$this->puCislo}&pu_kbanky={$this->puKbanky}&suma={$this->suma}&mena={$this->mena}&vs={$this->vs}&ss={$this->ss}&url=" . rawurlencode($this->url) . '&param=' . rawurlencode($this->param) . "&result={$result}&real={$result}&SIGN2=" . rawurlencode($sign),
        );
    }
}
