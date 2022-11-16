<?php

namespace Dot\AuthSocial\Service;

use Dot\AuthSocial\Result\AuthenticationResultInterface;

/**
 * Interface AuthenticationServiceInterface
 * @package Dot\AuthSocial\Service
 */
interface AuthenticationServiceInterface
{
    public function getAuthorizationUrl(): string;

    public function authenticate(string $code): AuthenticationResultInterface;
}
