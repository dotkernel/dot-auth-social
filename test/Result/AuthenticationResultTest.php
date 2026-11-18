<?php

declare(strict_types=1);

namespace DotTest\AuthSocial\Result;

use DateTimeImmutable;
use DateTimeInterface;
use Dot\AuthSocial\Result\AuthenticationResult;
use PHPUnit\Framework\TestCase;

class AuthenticationResultTest extends TestCase
{
    private const int EXPIRES = 1_700_000_000;

    public function testSuccessIsValid(): void
    {
        $result = AuthenticationResult::success(
            'user@example.test',
            'Team',
            'Dotkernel',
            'token',
            'refresh',
            self::EXPIRES
        );

        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getMessages());
        $this->assertSame('user@example.test', $result->getIdentity());
        $this->assertSame('Team', $result->getFirstName());
        $this->assertSame('Dotkernel', $result->getLastName());
        $this->assertSame('Team Dotkernel', $result->getFullName());
        $this->assertSame('token', $result->getAccessToken());
        $this->assertSame('refresh', $result->getRefreshToken());
    }

    public function testFailedWithOneMessageIsInvalid(): void
    {
        $result = AuthenticationResult::failed('error');

        $this->assertFalse($result->isValid());
        $this->assertSame(['error'], $result->getMessages());
        $this->assertNull($result->getIdentity());
        $this->assertNull($result->getAccessToken());
        $this->assertNull($result->getRefreshToken());
        $this->assertNull($result->getExpiresAt());
    }

    public function testFailedWithManyMessagesKeepsAll(): void
    {
        $result = AuthenticationResult::failed('first', 'second');

        $this->assertFalse($result->isValid());
        $this->assertSame(['first', 'second'], $result->getMessages());
    }

    public function testGetExpiresAtReturnsTimestamp(): void
    {
        $result = AuthenticationResult::success('user@example.test', null, null, 'token', null, self::EXPIRES);

        $expiresAt = $result->getExpiresAt();
        $this->assertInstanceOf(DateTimeImmutable::class, $expiresAt);
        $this->assertSame(self::EXPIRES, $expiresAt->getTimestamp());
    }

    public function testGetExpiresAtReturnsNullWithoutExpiry(): void
    {
        $result = AuthenticationResult::success('user@example.test', null, null, 'token', null, null);

        $this->assertTrue($result->isValid());
        $this->assertNull($result->getExpiresAt());
        $this->assertNull($result->getArrayCopy()['expires_at']);
    }

    public function testGetFullNameWithOneName(): void
    {
        $this->assertSame('Team', (new AuthenticationResult(firstName: 'Team'))->getFullName());
        $this->assertSame('Dotkernel', (new AuthenticationResult(lastName: 'Dotkernel'))->getFullName());
    }

    public function testGetFullNameWithoutNamesIsNull(): void
    {
        $this->assertNull((new AuthenticationResult())->getFullName());
    }

    public function testGetArrayCopy(): void
    {
        $result = AuthenticationResult::success('user@example.test', 'Team', 'Dotkernel', 'token', null, self::EXPIRES);

        $this->assertSame([
            'identity'      => 'user@example.test',
            'first_name'    => 'Team',
            'last_name'     => 'Dotkernel',
            'access_token'  => 'token',
            'refresh_token' => null,
            'expires_at'    => (new DateTimeImmutable())->setTimestamp(self::EXPIRES)->format(DateTimeInterface::ATOM),
        ], $result->getArrayCopy());
    }
}
