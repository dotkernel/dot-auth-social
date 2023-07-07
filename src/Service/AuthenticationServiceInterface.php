<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Service;

use Dot\AuthSocial\Result\AuthenticationResultInterface;

interface AuthenticationServiceInterface
{
    public function getAuthorizationUrl(): string;

    public function authenticate(string $code): AuthenticationResultInterface;
}
