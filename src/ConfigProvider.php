<?php

declare(strict_types=1);

namespace Dot\AuthSocial;

use Dot\AuthSocial\Factory\FacebookServiceFactory;
use Dot\AuthSocial\Service\AuthenticationServiceInterface;
use Dot\AuthSocial\Service\FacebookService;

class ConfigProvider
{
    /**
     * @return array{
     *     dependencies: array{
     *         aliases: array<class-string, class-string>,
     *         factories: array<class-string, class-string>
     *     }
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array{
     *     aliases: array<class-string, class-string>,
     *     factories: array<class-string, class-string>
     * }
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                AuthenticationServiceInterface::class => FacebookService::class,
            ],
            'factories' => [
                FacebookService::class => FacebookServiceFactory::class,
            ],
        ];
    }
}
