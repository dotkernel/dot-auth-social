<?php

declare(strict_types=1);

namespace Dot\AuthSocial\Factory;

use Dot\AuthSocial\Service\FacebookService;
use League\OAuth2\Client\Provider\Facebook as Provider;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function is_array;
use function is_scalar;
use function sprintf;

class FacebookServiceFactory
{
    /** @var list<string> */
    private const array REQUIRED_KEYS = ['client_id', 'client_secret', 'redirect_uri', 'graph_api_version'];

    public function __invoke(ContainerInterface $container): FacebookService
    {
        $config = $container->has('config') ? $container->get('config') : [];
        if (! is_array($config)) {
            throw new RuntimeException('Service `config` must be an array');
        }

        $socialConfig = $config['social_authentication'] ?? [];
        if (! is_array($socialConfig)) {
            throw new RuntimeException('Config key `social_authentication` must be an array');
        }

        $credentials = $socialConfig['facebook'] ?? [];
        if (! is_array($credentials)) {
            throw new RuntimeException('Config key `social_authentication.facebook` must be an array');
        }

        $options = $this->getOptions($credentials);

        return new FacebookService(new Provider([
            'clientId'        => $options['client_id'],
            'clientSecret'    => $options['client_secret'],
            'redirectUri'     => $options['redirect_uri'],
            'graphApiVersion' => $options['graph_api_version'],
        ]));
    }

    /**
     * @param array<mixed> $credentials
     * @return array<string, string>
     */
    private function getOptions(array $credentials): array
    {
        $options = [];
        foreach (self::REQUIRED_KEYS as $key) {
            $value = $credentials[$key] ?? null;
            if (! is_scalar($value) || (string) $value === '') {
                throw new RuntimeException(
                    sprintf('Value for key `social_authentication.facebook.%s` cannot be empty', $key)
                );
            }

            $options[$key] = (string) $value;
        }

        return $options;
    }
}
