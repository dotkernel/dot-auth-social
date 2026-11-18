<?php

declare(strict_types=1);

namespace DotTest\AuthSocial;

use Dot\AuthSocial\ConfigProvider;
use Dot\AuthSocial\Factory\FacebookServiceFactory;
use Dot\AuthSocial\Service\AuthenticationServiceInterface;
use Dot\AuthSocial\Service\FacebookService;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testInvokeReturnsDependencies(): void
    {
        $configProvider = new ConfigProvider();

        $this->assertSame(['dependencies' => $configProvider->getDependencies()], $configProvider());
    }

    public function testDependencies(): void
    {
        $this->assertSame([
            'aliases'   => [
                AuthenticationServiceInterface::class => FacebookService::class,
            ],
            'factories' => [
                FacebookService::class => FacebookServiceFactory::class,
            ],
        ], (new ConfigProvider())->getDependencies());
    }
}
