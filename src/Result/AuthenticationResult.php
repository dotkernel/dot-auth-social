<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Result;

use DateTimeImmutable;
use DateTimeInterface;

use function array_values;
use function trim;

class AuthenticationResult implements AuthenticationResultInterface
{
    /**
     * @param list<string>|null $messages
     * @param int|null $expires Unix timestamp when the access token expires.
     */
    public function __construct(
        private readonly ?array $messages = null,
        private readonly ?string $identity = null,
        private readonly ?string $firstName = null,
        private readonly ?string $lastName = null,
        private readonly ?string $accessToken = null,
        private readonly ?string $refreshToken = null,
        private readonly ?int $expires = null,
    ) {
    }

    public function isValid(): bool
    {
        return empty($this->messages);
    }

    /**
     * @return list<string>|null
     */
    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): ?string
    {
        $fullName = trim($this->firstName . ' ' . $this->lastName);

        return $fullName === '' ? null : $fullName;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        if ($this->expires === null) {
            return null;
        }

        return (new DateTimeImmutable())->setTimestamp($this->expires);
    }

    public static function success(
        string $identity,
        ?string $firstName,
        ?string $lastName,
        string $accessToken,
        ?string $refreshToken,
        ?int $expires
    ): self {
        return new self([], $identity, $firstName, $lastName, $accessToken, $refreshToken, $expires);
    }

    public static function failed(string $message, string ...$messages): self
    {
        return new self([$message, ...array_values($messages)]);
    }

    /**
     * @return array{
     *     identity: string|null,
     *     first_name: string|null,
     *     last_name: string|null,
     *     access_token: string|null,
     *     refresh_token: string|null,
     *     expires_at: string|null
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'identity'      => $this->getIdentity(),
            'first_name'    => $this->getFirstName(),
            'last_name'     => $this->getLastName(),
            'access_token'  => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
            'expires_at'    => $this->getExpiresAt()?->format(DateTimeInterface::ATOM),
        ];
    }
}
