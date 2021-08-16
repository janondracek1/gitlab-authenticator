<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Nette\Http\Helpers;

class Bootstrap
{

    public static function boot(): Configurator
    {
        $appDir = dirname(__DIR__);
        $configurator = new Configurator;
        $configurator->setDebugMode(
            php_sapi_name() === 'cli' || Helpers::ipMatch($_SERVER['REMOTE_ADDR'], '127.0.0.1/8')
        );
        $configurator->enableTracy($appDir . '/log');
        $configurator->setTempDirectory($appDir . '/temp');
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator->addConfig($appDir . '/config/common.neon');
        $configurator->addConfig($appDir . '/config/services.neon');
        $configurator->addConfig($appDir . '/config/gitlab-config.neon');

        return $configurator;
    }

}