# Usage

The login with Facebook has two steps:

1. Send the user to the authorization URL of Facebook.
2. Facebook sends the user back to your `redirect_uri` with a `code` and a `state` query parameter. Exchange the code for the user details.

## Check the state

`getAuthorizationUrl()` puts a random `state` value in the URL.
Keep this value in the session, and compare it with the `state` query parameter of the callback.
If the values are not the same, stop the login.
This check prevents login cross-site request forgery: without it, an attacker can log a victim in to the account of the attacker.

Call `getState()` after `getAuthorizationUrl()`.
Before the first call to `getAuthorizationUrl()`, `getState()` returns an empty string.

## Example handler

This example is a PSR-15 request handler.
It uses `mezzio/mezzio-session` for the session and `laminas/laminas-diactoros` for the responses.
You can use other implementations.

```php
<?php

declare(strict_types=1);

namespace Frontend\User\Handler;

use Dot\AuthSocial\Service\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function hash_equals;
use function is_string;

class GetFacebookLoginHandler implements RequestHandlerInterface
{
    private const string STATE_KEY = 'facebook_oauth_state';

    public function __construct(
        private readonly AuthenticationServiceInterface $service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $query   = $request->getQueryParams();
        $code    = $query['code'] ?? null;

        if (! is_string($code) || $code === '') {
            $url = $this->service->getAuthorizationUrl();
            $session->set(self::STATE_KEY, $this->service->getState());

            return new RedirectResponse($url);
        }

        $expectedState = $session->get(self::STATE_KEY);
        $session->unset(self::STATE_KEY);
        $state = $query['state'] ?? null;
        if (! is_string($expectedState) || ! is_string($state) || ! hash_equals($expectedState, $state)) {
            return new RedirectResponse('/user/login');
        }

        $result = $this->service->authenticate($code);
        if (! $result->isValid()) {
            // Show $result->getMessages() to the user.
            return new RedirectResponse('/user/login');
        }

        // Find or create the user with $result->getIdentity(), then log the user in.
        return new RedirectResponse('/');
    }
}
```

Create a factory for the handler:

```php
<?php

declare(strict_types=1);

namespace Frontend\User\Factory;

use Dot\AuthSocial\Service\AuthenticationServiceInterface;
use Frontend\User\Handler\GetFacebookLoginHandler;
use Psr\Container\ContainerInterface;

class GetFacebookLoginHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetFacebookLoginHandler
    {
        return new GetFacebookLoginHandler($container->get(AuthenticationServiceInterface::class));
    }
}
```

Register the handler with its factory in the `ConfigProvider` of your module, and add a route to it.
The route must be the `redirect_uri` from the configuration.

## The result

`authenticate()` returns an `AuthenticationResultInterface`.

| Method | Value |
| --- | --- |
| `isValid()` | `true` if the login succeeded |
| `getMessages()` | The error messages. An empty array after a successful login |
| `getIdentity()` | The email address of the Facebook user |
| `getFirstName()`, `getLastName()`, `getFullName()` | The name of the user, or `null` if Facebook did not return it |
| `getAccessToken()`, `getRefreshToken()` | The long-lived access token, and the refresh token or `null` |
| `getExpiresAt()` | The expiry time of the access token as `DateTimeImmutable`, or `null` if the token has no expiry time |
| `getArrayCopy()` | All values as an array. `expires_at` is in the ATOM format, for example `2026-11-18T14:00:00+00:00` |

The login fails with a message in these cases:

- Facebook returns an error. The message comes from Facebook.
- The Facebook user did not share an email address. The message is `FacebookService::MESSAGE_NO_EMAIL`.
- Any other error occurs. The message is `FacebookService::MESSAGE_FAILED`. The service does not show internal error details.
