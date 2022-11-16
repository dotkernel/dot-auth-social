<?php

namespace Dot\AuthSocial\Service;

use Dot\AuthSocial\Result\AuthenticationResult;
use League\OAuth2\Client\Provider\Facebook as Provider;
use League\OAuth2\Client\Token\AccessToken;
use Throwable;

/**
 * Class FacebookService
 * @package Dot\AuthSocial\Service
 */
class FacebookService implements AuthenticationServiceInterface
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->provider->getAuthorizationUrl([
            'scope' => ['email'],
        ]);
    }

    public function authenticate(string $code): AuthenticationResult
    {
        try {
            /** @var AccessToken $token */
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $token = $this->provider->getLongLivedAccessToken($token->getToken());
            $user = $this->provider->getResourceOwner($token);
            return AuthenticationResult::success(
                $user->getEmail(),
                $user->getFirstName(),
                $user->getLastName(),
                $token->getToken(),
                $token->getRefreshToken(),
                $token->getExpires()
            );
        } catch (Throwable $e) {
            return AuthenticationResult::failed($e->getMessage());
        }
    }
}
