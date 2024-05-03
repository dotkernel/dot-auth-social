# Configuration

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
