<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bootstrap;
use Nette\Application\Application;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class BootstrapTest extends TestCase
{
    /**
     * www/index.php: boot() enables Tracy, so it runs in its own process.
     */
    #[RunInSeparateProcess]
    public function testBootCreatesTheApplication(): void
    {
        $container = Bootstrap::boot()->createContainer();
        $container->getByType(Application::class);

        self::assertSame('Europe/Prague', date_default_timezone_get());
    }
}
