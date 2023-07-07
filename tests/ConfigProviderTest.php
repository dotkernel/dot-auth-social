<?php

declare(strict_types=1);

namespace DotTest\AuthSocial;

use Dot\AuthSocial\ConfigProvider;
use Dot\AuthSocial\Service\FacebookService;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(FacebookService::class, $this->config['dependencies']['factories']);
    }
}
