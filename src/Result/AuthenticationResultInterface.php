<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Result;

use DateTimeImmutable;

interface AuthenticationResultInterface
{
    public function getMessages(): ?array;

    public function isValid(): bool;

    public function getIdentity(): ?string;

    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresAt(): DateTimeImmutable|false;

    public function getArrayCopy(): array;
}
