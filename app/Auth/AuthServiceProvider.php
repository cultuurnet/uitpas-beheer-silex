<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use Auth0\SDK\Auth0;
use Silex\Application;
use Silex\ServiceProviderInterface;

final class AuthServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
        $app[Auth0::class] = $app::share(
            function (Application $app) {
                if ($app['config']['keycloak']['enable']) {
                    return new Auth0(
                        $this->getParamsKeycloak($app['config']['keycloak'])
                    );
                }

                return new Auth0(
                    $this->getParamsKeycloak($app['config']['auth0'])
                );
            }
        );

        $app[UiTIDv1TokenService::class] = $app::share(
            function (Application $app) {
                return new UiTIDv1TokenService(
                    $app['config']['uitid']['base_url'],
                    $app['culturefeed_consumer_credentials']
                );
            }
        );
    }

    public function boot(Application $app): void
    {

    }

    private function getParamsKeycloak(array $auth) : array
    {
        return [
            'domain' => $auth['domain'],
            'clientId' => $auth['client_id'],
            'clientSecret' => $auth['client_secret'],
            'cookieSecret' => $auth['cookieSecret'],
            'redirectUri' => $auth['callback_url'],
            'scope' => [
                'openid',
                'email',
                'profile',
                'offline_access',
            ],
            'audience' => ['https://api.publiq.be'],
        ];
    }

    public function getParamsAuth0(array $auth) : array
    {
        return [
            'domain' => $auth['domain'],
            'client_id' => $auth['client_id'],
            'client_secret' => $auth['client_secret'],
            'redirect_uri' => $auth['callback_url'],
            'scope' => implode(
                ' ',
                [
                    'openid',
                    'email',
                    'profile',
                    'offline_access',
                    'https://api.publiq.be/auth/uitpas_balie',
                    'https://api.publiq.be/auth/uitpas_balie_insights',
                ]
            ),
            'audience' => 'https://api.publiq.be',
            'persist_id_token' => false,
            'id_token_leeway' => 30,
        ];
    }
}
