<?php

declare(strict_types=1);

namespace DotTest\AuthSocial\Factory;

use Dot\AuthSocial\Factory\FacebookServiceFactory;
use Dot\AuthSocial\Service\FacebookService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

class FacebookServiceFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCanCreateFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $credentials = [
            'social_authentication' => [
                'facebook' => [
                    'client_id'         => 1,
                    'client_secret'     => 123,
                    'redirect_uri'      => 'testUri',
                    'graph_api_version' => 'v2.4',
                ],
            ],
        ];

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($credentials);

        $result = (new FacebookServiceFactory())($container);

        $this->assertInstanceOf(FacebookService::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testFactoryWillThrowException(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $credentials = [
            'social_authentication' => [
                'facebook' => [
                    'client_id'         => 1,
                    'client_secret'     => '',
                    'redirect_uri'      => '',
                    'graph_api_version' => '',
                ],
            ],
        ];

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($credentials);

        $this->expectException(RuntimeException::class);
        (new FacebookServiceFactory())($container);
    }
}
