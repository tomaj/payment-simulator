<?php

declare(strict_types=1);

namespace App\Tests\Functional;

final class SmokeTest extends AppTestCase
{
    public function testHomepage(): void
    {
        $result = $this->get('/');

        self::assertSame(200, $result->code);
        self::assertStringContainsString('<h1>Payment Simulator</h1>', $result->body);
    }

    public function testUnknownPageIs404(): void
    {
        $result = $this->get('/payment/unknown-gateway');

        self::assertSame(404, $result->code);
    }
}
