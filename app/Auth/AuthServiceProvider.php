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
                return new Auth0(
                    [
                        'domain' => $app['config']['auth0']['domain'],
                        'client_id' => $app['config']['auth0']['client_id'],
                        'client_secret' => $app['config']['auth0']['client_secret'],
                        'redirect_uri' => $app['config']['auth0']['callback_url'],
                        'scope' => implode(
                            ' ',
                            [
                                'openid',
                                'email',
                                'profile',
                                'offline_access',
                                'https://api.publiq.be/auth/uitpas_balie',
                            ]
                        ),
                        'audience' => 'https://api.publiq.be',
                        'persist_id_token' => false,
                        'id_token_leeway' => 30,
                    ]
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
}
