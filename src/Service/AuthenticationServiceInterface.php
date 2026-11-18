<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Service;

use Dot\AuthSocial\Result\AuthenticationResultInterface;

interface AuthenticationServiceInterface
{
    public function getAuthorizationUrl(): string;

    /**
     * Returns the state value of the last authorization URL.
     * Keep it in the session and compare it with the `state` query parameter of the callback.
     * Before the first call to getAuthorizationUrl(), the value is an empty string.
     */
    public function getState(): string;

    public function authenticate(string $code): AuthenticationResultInterface;
}
