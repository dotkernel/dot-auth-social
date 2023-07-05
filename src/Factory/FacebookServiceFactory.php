<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Factory;

use Dot\AuthSocial\Service\FacebookService;
use League\OAuth2\Client\Provider\Facebook as Provider;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function sprintf;

class FacebookServiceFactory
{
    public function __invoke(ContainerInterface $container): FacebookService
    {
        $credentials = $container->get('config')['social_authentication']['facebook'] ?? [];

        $this->validateCredentials($credentials);

        $provider = new Provider([
            'clientId'        => $credentials['client_id'],
            'clientSecret'    => $credentials['client_secret'],
            'redirectUri'     => $credentials['redirect_uri'],
            'graphApiVersion' => $credentials['graph_api_version'],
        ]);

        return new FacebookService($provider);
    }

    private function validateCredentials(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            if (empty($value)) {
                throw new RuntimeException(sprintf('Value for key `%s` cannot be empty', $key));
            }
        }
    }
}
