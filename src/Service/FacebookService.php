<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Service;

use Dot\AuthSocial\Result\AuthenticationResult;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Facebook as Provider;
use League\OAuth2\Client\Token\AccessToken;
use Throwable;

use function is_string;

class FacebookService implements AuthenticationServiceInterface
{
    public const string MESSAGE_FAILED   = 'Authentication with Facebook failed.';
    public const string MESSAGE_NO_EMAIL = 'The Facebook account did not share an email address.';

    public function __construct(
        protected Provider $provider,
    ) {
    }

    public function getAuthorizationUrl(): string
    {
        return $this->provider->getAuthorizationUrl();
    }

    public function getState(): string
    {
        $state = $this->provider->getState();

        return is_string($state) ? $state : '';
    }

    public function authenticate(string $code): AuthenticationResult
    {
        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $token = $this->provider->getLongLivedAccessToken($token->getToken());
            if (! $token instanceof AccessToken) {
                return AuthenticationResult::failed(self::MESSAGE_FAILED);
            }

            $user  = $this->provider->getResourceOwner($token);
            $email = $user->getEmail();
            if ($email === null || $email === '') {
                return AuthenticationResult::failed(self::MESSAGE_NO_EMAIL);
            }

            return AuthenticationResult::success(
                $email,
                $user->getFirstName(),
                $user->getLastName(),
                $token->getToken(),
                $token->getRefreshToken(),
                $token->getExpires()
            );
        } catch (IdentityProviderException $exception) {
            return AuthenticationResult::failed($exception->getMessage());
        } catch (Throwable) {
            return AuthenticationResult::failed(self::MESSAGE_FAILED);
        }
    }
}
