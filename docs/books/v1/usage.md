# Usage

In this example we will create a new controller, but you can use an existing one too.

```php
<?php

namespace Frontend\User\Controller;

use Dot\Controller\AbstractActionController;
use Dot\AuthSocial\Service\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

class FacebookController extends AbstractActionController
{
    private AuthenticationServiceInterface $service;

    public function __construct(AuthenticationServiceInterface $service)
    {
        $this->service = $service;
    }

    public function authAction(): ResponseInterface
    {
        $code = $this->request->getQueryParams()['code'] ?? false;
        if (! $code) {
            return new RedirectResponse($this->service->getAuthorizationUrl());
        }

        $result = $this->service->authenticate($code);
        if (! $result->isValid()) {
            // invalid authentication, check $result->getMessages() for errors.
        } else {
            // valid authentication, use $result->getArrayCopy() to get the user details
        }
    }
}
```

Create a factory for the controller:

```php
<?php

use Dot\AuthSocial\Service\FacebookService;
use Psr\Container\ContainerInterface;

class FacebookControllerFactory
{
    public function __invoke(ContainerInterface $container): FacebookController
    {
        return new FacebookController($container->get(FacebookService::class));
    }
}
```

Make sure to register your controller with the factory in `ConfigProvider`.
