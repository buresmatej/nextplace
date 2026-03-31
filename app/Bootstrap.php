<?php declare(strict_types=1);

namespace App;

use Nette;
use Nette\Bootstrap\Configurator;
use Nette\Utils\FileSystem;


class Bootstrap
{
    private readonly Configurator $configurator;
    private readonly string $rootDir;


    public function __construct()
    {
        $this->rootDir = dirname(__DIR__);
        $this->configurator = new Configurator;

        $tempDir = '/tmp/nette_temp';
        $logDir = '/tmp/nette_log';

        FileSystem::createDir($tempDir);
        FileSystem::createDir($logDir);

        $this->configurator->setTempDirectory($tempDir);
        $this->configurator->enableTracy($logDir);
    }


    public function bootWebApplication(): Nette\DI\Container
    {
        $this->initializeEnvironment();
        $this->setupContainer();
        return $this->configurator->createContainer();
    }


    public function initializeEnvironment(): void
    {
        // Na školním serveru doporučuji vypnout DebugMode (false),
        // Tracy logy teď najdeš v /tmp/nette_log v kontejneru
        $this->configurator->setDebugMode(true);

        $this->configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
    }


    private function setupContainer(): void
    {
        $configDir = $this->rootDir . '/config';
        $this->configurator->addConfig($configDir . '/common.neon');
        $this->configurator->addConfig($configDir . '/services.neon');
        $this->configurator->addConfig($configDir . '/extensions.neon');
    }

    public function bootConsole(): Nette\DI\Container
    {
        // I pro konzoli musíme inicializovat loader, aby našla třídy
        $this->configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $this->setupContainer();
        return $this->configurator->createContainer();
    }
}