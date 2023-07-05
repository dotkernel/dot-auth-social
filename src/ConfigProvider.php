<?php

declare(strict_types=1);

namespace Dot\AuthSocial;

use Dot\AuthSocial\Factory\FacebookServiceFactory;
use Dot\AuthSocial\Service\FacebookService;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                FacebookService::class => FacebookServiceFactory::class,
            ],
        ];
    }
}
