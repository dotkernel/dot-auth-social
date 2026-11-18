<?php

declare(strict_types=1);

namespace DotTest\AuthSocial\Service;

use DateTimeImmutable;
use Dot\AuthSocial\Service\FacebookService;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PHPUnit\Framework\TestCase;

use function parse_str;
use function parse_url;

use const PHP_URL_QUERY;

class FacebookServiceTest extends TestCase
{
    private const int EXPIRES = 1_700_000_000;

    private AccessToken $shortLivedToken;

    private AccessToken $longLivedToken;

    protected function setUp(): void
    {
        $this->shortLivedToken = new AccessToken(['access_token' => 'short lived token', 'expires' => self::EXPIRES]);
        $this->longLivedToken  = new AccessToken([
            'access_token' => 'long lived token',
            'expires'      => self::EXPIRES,
        ]);
    }

    public function testAuthorizationUrlUsesDefaultScopes(): void
    {
        $service = new FacebookService($this->realProvider());

        parse_str((string) parse_url($service->getAuthorizationUrl(), PHP_URL_QUERY), $query);

        $this->assertSame('public_profile,email', $query['scope']);
    }

    public function testGetStateIsEmptyBeforeAuthorizationUrl(): void
    {
        $this->assertSame('', (new FacebookService($this->realProvider()))->getState());
    }

    public function testGetStateReturnsStateOfAuthorizationUrl(): void
    {
        $service = new FacebookService($this->realProvider());

        parse_str((string) parse_url($service->getAuthorizationUrl(), PHP_URL_QUERY), $query);

        $this->assertIsString($query['state']);
        $this->assertNotSame('', $query['state']);
        $this->assertSame($query['state'], $service->getState());
    }

    public function testAuthenticate(): void
    {
        $provider = $this->createMock(Facebook::class);
        $provider->expects($this->once())
            ->method('getAccessToken')
            ->with('authorization_code', ['code' => 'code'])
            ->willReturn($this->shortLivedToken);
        $provider->expects($this->once())
            ->method('getLongLivedAccessToken')
            ->with('short lived token')
            ->willReturn($this->longLivedToken);
        $provider->expects($this->once())
            ->method('getResourceOwner')
            ->with($this->longLivedToken)
            ->willReturn(new FacebookUser([
                'id'         => '1',
                'email'      => 'test@dotkernel.com',
                'first_name' => 'Team',
                'last_name'  => 'Dotkernel',
            ]));

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getMessages());
        $this->assertSame('test@dotkernel.com', $result->getIdentity());
        $this->assertSame('Team', $result->getFirstName());
        $this->assertSame('Dotkernel', $result->getLastName());
        $this->assertSame('Team Dotkernel', $result->getFullName());
        $this->assertSame('long lived token', $result->getAccessToken());
        $this->assertNull($result->getRefreshToken());
        $this->assertInstanceOf(DateTimeImmutable::class, $result->getExpiresAt());
        $this->assertSame(self::EXPIRES, $result->getExpiresAt()->getTimestamp());
    }

    public function testAuthenticateWithoutNamesOrExpiry(): void
    {
        $token    = new AccessToken(['access_token' => 'long lived token']);
        $provider = $this->createStub(Facebook::class);
        $provider->method('getAccessToken')->willReturn($this->shortLivedToken);
        $provider->method('getLongLivedAccessToken')->willReturn($token);
        $provider->method('getResourceOwner')->willReturn(new FacebookUser([
            'id'    => '1',
            'email' => 'test@dotkernel.com',
        ]));

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertTrue($result->isValid());
        $this->assertNull($result->getFirstName());
        $this->assertNull($result->getLastName());
        $this->assertNull($result->getFullName());
        $this->assertNull($result->getExpiresAt());
    }

    public function testAuthenticateFailsWithoutEmail(): void
    {
        $provider = $this->createStub(Facebook::class);
        $provider->method('getAccessToken')->willReturn($this->shortLivedToken);
        $provider->method('getLongLivedAccessToken')->willReturn($this->longLivedToken);
        $provider->method('getResourceOwner')->willReturn(new FacebookUser([
            'id'         => '1',
            'first_name' => 'Team',
            'last_name'  => 'Dotkernel',
        ]));

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertFalse($result->isValid());
        $this->assertSame([FacebookService::MESSAGE_NO_EMAIL], $result->getMessages());
        $this->assertNull($result->getIdentity());
    }

    public function testAuthenticateFailsWhenLongLivedTokenIsNotAccessToken(): void
    {
        $provider = $this->createMock(Facebook::class);
        $provider->method('getAccessToken')->willReturn($this->shortLivedToken);
        $provider->method('getLongLivedAccessToken')->willReturn($this->createStub(AccessTokenInterface::class));
        $provider->expects($this->never())->method('getResourceOwner');

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertFalse($result->isValid());
        $this->assertSame([FacebookService::MESSAGE_FAILED], $result->getMessages());
    }

    public function testAuthenticateKeepsProviderErrorMessage(): void
    {
        $provider = $this->createStub(Facebook::class);
        $provider->method('getAccessToken')
            ->willThrowException(new IdentityProviderException('Invalid verification code format.', 100, []));

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertFalse($result->isValid());
        $this->assertSame(['Invalid verification code format.'], $result->getMessages());
        $this->assertNull($result->getAccessToken());
        $this->assertNull($result->getExpiresAt());
    }

    public function testAuthenticateHidesOtherErrorMessages(): void
    {
        $provider = $this->createStub(Facebook::class);
        $provider->method('getAccessToken')->willThrowException(new Exception('Internal detail in /var/www'));

        $result = (new FacebookService($provider))->authenticate('code');

        $this->assertFalse($result->isValid());
        $this->assertSame([FacebookService::MESSAGE_FAILED], $result->getMessages());
    }

    private function realProvider(): Facebook
    {
        return new Facebook([
            'clientId'        => 'client-id',
            'clientSecret'    => 'client-secret',
            'redirectUri'     => 'https://example.test/callback',
            'graphApiVersion' => 'v19.0',
        ]);
    }
}
