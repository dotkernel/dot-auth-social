<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Result;

use DateTimeImmutable;

interface AuthenticationResultInterface
{
    /**
     * @return list<string>|null
     */
    public function getMessages(): ?array;

    public function isValid(): bool;

    public function getIdentity(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getFullName(): ?string;

    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresAt(): ?DateTimeImmutable;

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
    public function getArrayCopy(): array;
}
