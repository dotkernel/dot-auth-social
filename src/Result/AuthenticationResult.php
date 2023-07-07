<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Result;

use DateTimeImmutable;
use Throwable;

use function is_string;
use function strval;
use function trim;

class AuthenticationResult implements AuthenticationResultInterface
{
    private ?array $messages;

    private ?string $identity;

    private ?string $firstName;

    private ?string $lastName;

    private ?string $accessToken;

    private ?string $refreshToken;

    private ?int $expiresIn;

    public function __construct(
        ?array $messages = null,
        ?string $identity = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $accessToken = null,
        ?string $refreshToken = null,
        ?int $expiresIn = null
    ) {
        $this->messages     = $messages;
        $this->identity     = $identity;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->accessToken  = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn    = $expiresIn;
    }

    public function isValid(): bool
    {
        return empty($this->messages);
    }

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
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): DateTimeImmutable|false
    {
        try {
            return DateTimeImmutable::createFromFormat('U', strval($this->expiresIn));
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function success(
        string $identity,
        string $firstName,
        string $lastName,
        string $accessToken,
        ?string $refreshToken,
        int $expiresIn
    ): self {
        return new self([], $identity, $firstName, $lastName, $accessToken, $refreshToken, $expiresIn);
    }

    public static function failed(?string $messages): self
    {
        if (is_string($messages)) {
            return new self([$messages]);
        }

        return new self($messages);
    }

    public function getArrayCopy(): array
    {
        return [
            'identity'      => $this->getIdentity(),
            'first_name'    => $this->getFirstName(),
            'last_name'     => $this->getLastName(),
            'access_token'  => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
            'expires_at'    => $this->getExpiresAt() instanceof DateTimeImmutable ?
                $this->getExpiresAt()->format('Y-m-d H:i:s') : null,
        ];
    }
}
