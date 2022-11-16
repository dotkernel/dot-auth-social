<?php

namespace Dot\AuthSocial;

use Dot\AuthSocial\Factory\FacebookServiceFactory;
use Dot\AuthSocial\Service\FacebookService;

/**
 * Class ConfigProvider
 * @package Dot\AuthSocial
 */
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
