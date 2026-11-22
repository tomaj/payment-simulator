<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\RequestParameters;
use App\Tests\TestCase;

final class RequestParametersTest extends TestCase
{
    public function testRequiredAndOptional(): void
    {
        $params = new RequestParameters(['MID' => '9999', 'CS' => '', 'ARR' => ['x'], 'NUM' => 5]);

        self::assertSame('9999', $params->required('MID'));
        self::assertSame('', $params->required('CS'));
        self::assertSame('', $params->required('VS'));
        self::assertSame('', $params->required('ARR'));
        self::assertSame('', $params->required('NUM'));
        self::assertSame('', $params->optional('REM'));
        self::assertSame('9999', $params->optional('MID'));
        self::assertSame('', $params->optional('ARR'));

        self::assertSame(['VS', 'ARR', 'NUM'], $params->missing());
        self::assertSame(['MID' => '9999', 'CS' => '', 'ARR' => ['x'], 'NUM' => 5], $params->all());
    }

    public function testNothingMissing(): void
    {
        $params = new RequestParameters(['MID' => '1']);
        $params->required('MID');

        self::assertSame([], $params->missing());
    }
}
