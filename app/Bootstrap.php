<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;

final class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();
        $appDir = dirname(__DIR__);

        $configurator->enableTracy($appDir . '/log');
        self::configure($configurator);

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        return $configurator;
    }

    /**
     * Time zone, temp directory and configuration files; the tests use it with their own debug mode.
     */
    public static function configure(Configurator $configurator): Configurator
    {
        $appDir = dirname(__DIR__);

        // presenters are discovered in appDir, which otherwise defaults to the caller's directory
        $configurator->addStaticParameters(['appDir' => __DIR__]);
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->addConfig($appDir . '/app/config/common.neon');
        $configurator->addConfig($appDir . '/app/config/services.neon');
        $configurator->addConfig($appDir . '/app/config/local.neon');

        return $configurator;
    }
}
