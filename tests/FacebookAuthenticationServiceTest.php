<?php

namespace DotTest\AuthSocial;

use Dot\AuthSocial\Result\AuthenticationResultInterface;
use Dot\AuthSocial\Service\FacebookService as Subject;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Exception;

/**
 * Class FacebookAuthenticationServiceTest
 * @package DotTest\AuthSocial
 */
class FacebookAuthenticationServiceTest extends TestCase
{
    private $accessToken;

    private $provider;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(Facebook::class);

        $this->accessToken = $this->createMock(AccessToken::class);
        $this->accessToken->method('getToken')->willReturn('access token');
        $this->accessToken->method('getRefreshToken')->willReturn(null);
        $this->accessToken->method('getExpires')->willReturn((new DateTimeImmutable())->getTimestamp());
    }

    public function testAuthorizationUri()
    {
        $this->provider
            ->expects($this->once())
            ->method('getAuthorizationUrl')
            ->willReturn('authorization_url');

        $subject = new Subject($this->provider);

        $url = $subject->getAuthorizationUrl();
        $this->assertSame('authorization_url', $url);
    }

    public function testAuthenticateThrowsException()
    {
        $exception = new Exception('error');
        $this->provider
            ->method('getAccessToken')
            ->willThrowException($exception);

        $subject = new Subject($this->provider);

        $result = $subject->authenticate('code');
        $this->assertFalse($result->isValid());
        $this->assertIsArray($result->getMessages());
        $this->assertNotEmpty($result->getMessages());
        $this->assertSame([$exception->getMessage()], $result->getMessages());
        $this->assertEmpty($result->getIdentity());
        $this->assertEmpty($result->getAccessToken());
        $this->assertEmpty($result->getRefreshToken());
        $this->assertEmpty($result->getExpiresAt());
    }

    public function testAuthenticate()
    {
        $resourceOwner = $this->createMock(FacebookUser::class);
        $resourceOwner->method('getEmail')->willReturn('test@dotkernel.com');
        $resourceOwner->method('getFirstName')->willReturn('Team');
        $resourceOwner->method('getLastName')->willReturn('DotKernel');

        $this->provider
            ->expects($this->once())
            ->method('getAccessToken')
            ->with('authorization_code', ['code' => 'code'])
            ->willReturn($this->accessToken);

        $this->provider
            ->expects($this->once())
            ->method('getLongLivedAccessToken')
            ->with($this->accessToken->getToken())
            ->willReturn($this->accessToken);

        $this->provider
            ->expects($this->once())
            ->method('getResourceOwner')
            ->willReturn($resourceOwner);

        $subject = new Subject($this->provider);

        $result = $subject->authenticate('code');
        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getMessages());
        $this->assertSame($this->accessToken->getToken(), $result->getAccessToken());
        $this->assertEmpty($result->getRefreshToken());
        $this->assertInstanceOf(DateTimeImmutable::class, $result->getExpiresAt());
        $this->assertEquals($this->accessToken->getExpires(), $result->getExpiresAt()->format('U'));
    }
}
