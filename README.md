# dot-auth-social

Dotkernel's component, used to authenticate users through Facebook and return credentials and user details.

> dot-auth-social is a wrapper on top of [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client) social providers.

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-auth-social/.

## Versions

| Version | Branch | PHP | Status |
| --- | --- | --- | --- |
| 2.x | `2.0` | 8.3, 8.4, 8.5, 8.6 | Active |
| 1.x | `1.0` | 8.2, 8.3, 8.4, 8.5 | Security fixes only |

To upgrade from 1.x, read [Upgrading from 1.x to 2.x](https://docs.dotkernel.org/dot-auth-social/v2/upgrading/).

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-auth-social)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-auth-social/2.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-auth-social)](https://github.com/dotkernel/dot-auth-social/blob/2.0/LICENSE.md)

[![Build Static](https://github.com/dotkernel/dot-auth-social/actions/workflows/continuous-integration.yml/badge.svg?branch=2.0)](https://github.com/dotkernel/dot-auth-social/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-auth-social/graph/badge.svg?token=VIHN1HK8DR)](https://codecov.io/gh/dotkernel/dot-auth-social)
[![PHPStan](https://github.com/dotkernel/dot-auth-social/actions/workflows/static-analysis.yml/badge.svg?branch=2.0)](https://github.com/dotkernel/dot-auth-social/actions/workflows/static-analysis.yml)

## Requirements

- **PHP**: 8.3, 8.4, 8.5 or 8.6
- **Container**: a PSR-11 container that accepts one factory for each service key, for example Laminas Service Manager

If your application uses Laminas Service Manager, require `laminas/laminas-servicemanager` in your application.

## Installation

Run this command in your project directory:

```shell
composer require dotkernel/dot-auth-social
```

Then add `Dot\AuthSocial\ConfigProvider` to your configuration aggregate.

## Configuration

Create the file `config/autoload/social-authentication.local.php` with this content:

```php
<?php

declare(strict_types=1);

return [
    'social_authentication' => [
        'facebook' => [
            'client_id'         => '',
            'client_secret'     => '',
            'redirect_uri'      => '',
            'graph_api_version' => '',
        ],
    ],
];
```

Put your credentials in the array. All four keys are required.

## Usage

Inject `Dot\AuthSocial\Service\AuthenticationServiceInterface` in your handler.

1. Call `getAuthorizationUrl()`, keep `getState()` in the session, and redirect the user to the URL.
2. On the callback, compare the `state` query parameter with the value in the session. Stop the login if they are not the same.
3. Call `authenticate($code)` with the `code` query parameter.
4. Check `isValid()` on the result. Use `getIdentity()` or `getArrayCopy()` for the user details.

The [usage page](https://docs.dotkernel.org/dot-auth-social/v2/usage/) has a full example handler.
