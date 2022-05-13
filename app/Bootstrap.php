<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator;
        $appDir = dirname(__DIR__);

//        $configurator->setDebugMode(true);

        $configurator->enableTracy($appDir . '/log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig($appDir . '/app/config/common.neon');
        $configurator->addConfig($appDir . '/app/config/services.neon');
        $configurator->addConfig($appDir . '/app/config/local.neon');

        return $configurator;
    }
}
