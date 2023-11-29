# dot-auth-social

This package is a wrapper for [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) social providers.
It's goal is to authenticate users though facebook and return credentials and user details.

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-auth-social)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-auth-social/1.1.2)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/blob/1.0/LICENSE.md)

[![Build Static](https://github.com/dotkernel/dot-auth-social/actions/workflows/static-analysis.yml/badge.svg?branch=1.0)](https://github.com/dotkernel/dot-auth-social/actions/workflows/static-analysis.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-auth-social/graph/badge.svg?token=VIHN1HK8DR)](https://codecov.io/gh/dotkernel/dot-auth-social)

[![SymfonyInsight](https://insight.symfony.com/projects/6919fca1-57ca-426e-add7-0c1f901efeab/big.svg)](https://insight.symfony.com/projects/6919fca1-57ca-426e-add7-0c1f901efeab)

## Installation

Run the following command in your project directory
```bash
$ composer require dotkernel/dot-auth-social
```

After installing, add the `ConfigProvider` class to your configuration aggregate.

Create a new file `social-authentication.global.php` in `config/autoload` with the following contents :

```php
return [
    'social_authentication' => [
        'facebook' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '',
            'graph_api_version' => '',
        ]
    ]
];
```

#### Note : Don't forger to put your credentials in the array.

## Usage

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

Make sure to register your controller with the factory in ``ConfigProvider``.


