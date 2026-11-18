# Configuration

Add `Dot\AuthSocial\ConfigProvider` to your configuration aggregate.
It registers these services:

| Service | Type |
| --- | --- |
| `Dot\AuthSocial\Service\FacebookService` | Factory `Dot\AuthSocial\Factory\FacebookServiceFactory` |
| `Dot\AuthSocial\Service\AuthenticationServiceInterface` | Alias of `FacebookService` |

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

Put your credentials in the array.
Do not commit this file, because it contains the client secret.

## Keys

All four keys are required.

| Key | Value |
| --- | --- |
| `client_id` | The App ID of your Facebook app |
| `client_secret` | The App Secret of your Facebook app |
| `redirect_uri` | The URL of your callback route. It must be a valid OAuth redirect URI in the settings of your Facebook app |
| `graph_api_version` | The Graph API version, in the format `v19.0`. The provider rejects values that do not start with `v` and a version number |

## Errors

If a key is missing or empty, the factory throws a `RuntimeException` with the full path of the key:

```text
Value for key `social_authentication.facebook.client_secret` cannot be empty
```

The value `0` is a valid value.
