<?php

declare(strict_types=1);

namespace DotTest\AuthSocial\Factory;

use Dot\AuthSocial\Factory\FacebookServiceFactory;
use DotTest\AuthSocial\Fixture\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function parse_str;
use function parse_url;

use const PHP_URL_QUERY;

class FacebookServiceFactoryTest extends TestCase
{
    private const array CREDENTIALS = [
        'client_id'         => 'client-id',
        'client_secret'     => 'client-secret',
        'redirect_uri'      => 'https://example.test/callback',
        'graph_api_version' => 'v19.0',
    ];

    public function testCreatesServiceFromConfig(): void
    {
        $url = (new FacebookServiceFactory())($this->container(self::CREDENTIALS))->getAuthorizationUrl();
        $this->assertStringStartsWith('https://www.facebook.com/v19.0/dialog/oauth', $url);

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame('client-id', $query['client_id']);
        $this->assertSame('https://example.test/callback', $query['redirect_uri']);
    }

    public function testAcceptsZeroAsValue(): void
    {
        $credentials                  = self::CREDENTIALS;
        $credentials['client_secret'] = '0';

        $url = (new FacebookServiceFactory())($this->container($credentials))->getAuthorizationUrl();

        $this->assertStringStartsWith('https://www.facebook.com/v19.0/dialog/oauth', $url);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function requiredKeyProvider(): iterable
    {
        foreach (self::CREDENTIALS as $key => $value) {
            yield $key => [$key];
        }
    }

    #[DataProvider('requiredKeyProvider')]
    public function testThrowsWhenKeyIsMissing(string $key): void
    {
        $credentials = self::CREDENTIALS;
        unset($credentials[$key]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value for key `social_authentication.facebook.' . $key . '` cannot be empty');

        (new FacebookServiceFactory())($this->container($credentials));
    }

    #[DataProvider('requiredKeyProvider')]
    public function testThrowsWhenValueIsEmpty(string $key): void
    {
        $credentials       = self::CREDENTIALS;
        $credentials[$key] = '';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value for key `social_authentication.facebook.' . $key . '` cannot be empty');

        (new FacebookServiceFactory())($this->container($credentials));
    }

    public function testThrowsWhenValueIsNotScalar(): void
    {
        $credentials              = self::CREDENTIALS;
        $credentials['client_id'] = ['client-id'];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value for key `social_authentication.facebook.client_id` cannot be empty');

        (new FacebookServiceFactory())($this->container($credentials));
    }

    public function testThrowsWhenConfigServiceIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value for key `social_authentication.facebook.client_id` cannot be empty');

        (new FacebookServiceFactory())(new InMemoryContainer());
    }

    public function testThrowsWhenConfigIsNotArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Service `config` must be an array');

        (new FacebookServiceFactory())(new InMemoryContainer(['config' => 'invalid']));
    }

    public function testThrowsWhenSocialAuthenticationIsNotArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config key `social_authentication` must be an array');

        (new FacebookServiceFactory())(new InMemoryContainer([
            'config' => ['social_authentication' => 'invalid'],
        ]));
    }

    public function testThrowsWhenFacebookConfigIsNotArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config key `social_authentication.facebook` must be an array');

        (new FacebookServiceFactory())(new InMemoryContainer([
            'config' => ['social_authentication' => ['facebook' => 'invalid']],
        ]));
    }

    /**
     * @param array<string, mixed> $credentials
     */
    private function container(array $credentials): InMemoryContainer
    {
        return new InMemoryContainer([
            'config' => ['social_authentication' => ['facebook' => $credentials]],
        ]);
    }
}
