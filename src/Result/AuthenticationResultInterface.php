<?php

namespace Dot\AuthSocial\Result;

use DateTimeImmutable;

/**
 * Interface AuthenticationResultInterface
 * @package Dot\AuthSocial\Result
 */
interface AuthenticationResultInterface
{
    public function getMessages(): ?array;

    public function isValid(): bool;

    public function getIdentity(): ?string;

    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresAt(): ?DateTimeImmutable;

    public function getArrayCopy(): array;
}
